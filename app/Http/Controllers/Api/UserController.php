<?php
/**
 * @group  User management
 *
 * APIs for managing users
 */
namespace App\Http\Controllers\Api;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ResetPassword;
use App\Models\User;
use Socialite;
use Validator;
use Auth;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    
    public function login(Request $request)
    {
        if (!$request->email){
            return ['success' => false, 'error' => 'Adresa de email este obligatorie!'];
        }
        
        if (!$request->password){
            return ['success' => false, 'error' => 'Parola este obligatorie!'];
        }
        
        $user = User::where('email', $request->email)->first();
        if (!$user){
            return ['success' => false, 'error' => 'Nu exista utilizator asociat acestei adrese de email!'];
        }
        if ($user->password == 'oauth'){
          $pass_modified =  Hash::make($request->password);
          User::where('id', $user->id)->update(['password' => $pass_modified]);
        } else{
          if (!Hash::check($request->password, $user->password)){
              return ['success' => false, 'error' => 'Parola este incorecta! Reincercati!'];
          }
        }
        
        $token = $this->generateToken($user->email, $request->password);
      
        if (!$token){
            return ['success' => false, 'error' => 'S-a produs o eroare. Reincercati! :)'];
        }
        
      
        return [
            'success' => true,
            'user' => $user,
            'token' => $token,
        ];
    }
  
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user){
            return ['success' => false, 'error' => 'No user found!'];
        }
      
        if (!$request->email){
            return ['success' => false, 'error' => 'Please enter your email address!'];
        }
        
        
        $recover_token = $this->generateRandomString(25);
//         try {
            $user->recover_token = sha1($recover_token);
            $user->save();
            $url_link = URL::temporarySignedRoute(
                'recover-password', now()->addMinutes(5), ['hash' => $user->recover_token]
            );
            Mail::to($user->email)->send(new ResetPassword($url_link));
            return [
                'success' => true,
                'msg' => "An email with your new password was sent to your email address. Please check your email.",
            ];
//         } catch (\Exception $e) {
//             return [
//                 'success' => false,
//                 'error' => 'Could not recover the password. Please try again later!',
//             ];
//         }
    }
  
  public function forgot_password_form(){
    $token = $this->check_for_token();
    if($token != null){
      $user = User::where('recover_token', $token)->first();
    } else{
      return redirect("404");
    }
    if($user && $user != null){
      return view('forgot_password_form', ['token' => $token]);
    } else{
      return redirect("404");
    }
  }
  
    public function check_for_token(){
      $url = url()->full();
      if(preg_match("/\/(\d+)$/",$url,$matches))
      {
        $end=$matches[1];
      }
      $url = explode("/", $url);
      $token = $url[count($url) - 1];
      $token = explode("?", $token)[0];
      return $token;
    }
    
    public function forgotPasswordVerify(Request $request)
    {
        $token = $request->token;
        if($token != null){
          $user = User::where('recover_token', $token)->first();
        } else{
          return ['success' => false, 'msg' => 'Din pacate url-ul a expirat sau user-ul nu exista!'];
        }
        if (!$request->parola1){
            return ['success' => false, 'msg' => 'Ambele parole trebuie completate!'];
        }
        if (!$request->parola2){
            return ['success' => false, 'msg' => 'Ambele parole trebuie completate!'];
        }
        if ($request->parola1 != $request->parola2){
            return ['success' => false, 'msg' => 'Parolele nu se potrivesc!'];
        }
        
        if (!$user){
            return ['success' => false, 'msg' => 'Nu s-a gasit niciun cont cu aceasta adresa de email!'];
        }
       
        try {
            $user->recover_token = null;
            $user->password = Hash::make($request->parola1);
            $user->save();
            return [
                'success' => true,
                'msg' => 'Parola a fost modificata cu succes!',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => 'Nu s-a putut modifica parola. Reincercati!'];
        }
    }
    
  
  public function get_updated_user (Request $request) {
    $user = Auth::guard('api')->user();
    return ['success' => true, 'user' => $user];
    
  }
  
    public function facebook(Request $request)
    {
        return $this->socialiteProvider('facebook', $request);
    }

    public function appleLogin(Request $request)
    {
        return $this->socialiteProvider('sign-in-with-apple', $request);
    }
  
    public function socialiteProvider($provider, $request)
    {
        $token = $request->access_token;
        if (!$token)
            return ['success' => false, 'error' => 'unknown'];
        
        try {
            $oauthUser = Socialite::driver($provider)->userFromToken($token);
        }
        catch (\Exception $e) {
            return ['success' => false, 'error' => 'wrong-token'];
        }
        if(!$oauthUser->getEmail())
            return ['success' => false, 'error' => 'Este o problema cu autentificarea folosind acest cont de facebook! Reincercati!'];
        
        $user = User::where('email', $oauthUser->getEmail())->first();
        
        if (!$user) {
            $user = new Account;
            $user->email     = $oauthUser->getEmail();
            $user->password    = 'oauth';
            $user->name      = $oauthUser->getName();
            if (!$user->save())
                return ['success' => false, 'error' => 'Unknown error'];
        }
        
        $token = $this->generateToken($user->email, encrypt('oauth'));
        if (!$token)
            return ['success' => false, 'error' => 'unknown'];
        
        return [
            'success' => true,
            'user' => $user,
            'token' => $token,
        ];
    }
    
    private function generateToken($username, $password)
    {
        $http = new GuzzleClient;
        $response = $http->request('POST', url('/oauth/token'), [
            'allow_redirects' => true,
            'http_errors' => false,
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => env("OAUTH_PASSWORD_CLIENT_ID"),
                'client_secret' => env("OAUTH_PASSWORD_CLIENT_SECRET"),
                'username'      => $username,
                'password'      => $password,
                'scope'         => '*',
            ],
        ]);
        return json_decode((string) $response->getBody(), true);
    }

    public function checkToken(Request $request)
    {
        $guard = Auth::guard('api');
        $logged = $guard->check();
        $user = false;
        if ($logged) {
            $user = $guard->user();
		
			
			if(!$user){
				   $logged = false; 
				   $user = false; 
			}
        }
        return [
            'logged'  => $logged,
            'user'    => $user,
        ];
    }
    
    public function refreshToken(Request $request)
    {
        if (!$request->has('refresh_token'))
            return ['success' => false, 'error' => 'no-token'];
        
        $refresh_token = $request->refresh_token;
        $http = new GuzzleClient;
        $responseObj = $http->request('POST', url('/oauth/token'), [
            'allow_redirects' => true,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'client_id'     => env('OAUTH_PASSWORD_CLIENT_ID'),
                'client_secret' => env('OAUTH_PASSWORD_CLIENT_SECRET'),
                'refresh_token' => $refresh_token,
                'scope'         => '*',
            ],
        ]);
        $response = json_decode((string) $responseObj->getBody(), true);
        if (!$response) return ['success' => false, 'error' => 'unknown'];
        
        if (isset($response['error'])) {
            $return = ['success' => false, 'error' => 'unknown'];
            
            if ($response['error'] === 'invalid_request')
                $return['error'] = 'expired-token';
            
            if ($response['error'] === 'invalid_client') {
                // Sentry::captureException(new Exception('Internal oauth2 server, invalid client error.'), [
                //     'extra' => ['Response' => $response],
                // ]);
            }
            
            return $return;
        }
        
        // Note: action() will return the latest url with this action assigned
//         $checkResponse = $http->request('GET', action('Api\UserController@checkToken'), [
        $checkResponse = $http->request('GET', action('/user/check'), [
            'allow_redirects' => true,
            'http_errors' => false,
            'headers' => [
                'Content-Type'   => 'application/json',
                'Accept'         => 'application/json',
                'Authorization'  => $response['token_type'].' '.$response['access_token'],
            ],
        ]);
        $check = json_decode((string) $checkResponse->getBody(), true);
        
        return [
            'success' => true,
            'token'   => $response,
            'user'    => $check['user'],
        ];
    }
  
    public function edit(Request $request){
        $user = Auth::guard('api')->user();
        $form_data = $request->only(['email', 'name', 'password']);
        $validationRules = [
            'email'      => ['required', 'email'],
            'name'    => ['required', 'min:6'],
        ];
        $validationMessages = [
            'email.email'      => "Trebuie sa introduci o adresa de :attribute valida!\n",
            'email.unique'     => "Exista un cont asociat acestei adrese de email!\n",
            'email.required'   => "Campul email este obligatoriu!\n",
            'name.required' => "Campul nume este obligatoriu!\n",
            'name.min' => "Campul nume trebuie sa aiba minim 6 caractere!\n",
        ];
        $validator = Validator::make($form_data, $validationRules, $validationMessages);
        if(!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)){
           return ['success' => false, 'msg' => 'Adresa de email nu respecta formatul standard! Ex: email@email.com'];
        }
        if ($validator->fails())
            return ['success' => false, 'msg' => $validator->errors()->all()];
        
        if ($request->email && $user->email != $request->email){
            if (User::where('id', '!=', $user->id)->where('email', $request->email)->count() > 0)
                return ['success' => false, 'msg' => 'Acest email deja se afla in baza noastra de date'];
            $user->email = $request->email;
        }
      
        if ($request->password && !Hash::check($user->password, $request->password)){
            $user->password = Hash::make($request->password);
        }
        
        if ($request->name && $user->name != $request->name){
            $user->name = $request->name;
        }
      
        if ($request->email && $user->email != $request->email){
            $user->email = $request->email;
        }
        
        if ($user->isDirty()) {
            if (!$user->save())
                return ['success' => false, 'msg' => 'Datele nu au putut fi salvate!'];
        }
        return [
            'success' => true,
            'msg'     => 'Datele au fost modificate cu succes!',
            'user' => $user,
        ];
    }
    public static function generateRandomString($length = 90) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
}
