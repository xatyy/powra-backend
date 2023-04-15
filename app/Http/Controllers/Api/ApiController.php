<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Storage;
use Auth;
use Validator;
use App\Car;
use App\CustodyCar;
use App\DeliveryReport;
use App\CarChecklist;
use App\PowraReport;
use App\Mail\Powra;
use App\Country;
use App\Project;
use App\Notification;
use App\Models\User;
use OneSignal;

class ApiController extends Controller
{
  public function getStatics(){
    $user = Auth::guard('api')->user();
    if(!$user){
      return ['success' => true];
    }
    // all operators has role_id = 4
    $operators = User::select('id', 'name', 'email', 'phone')->whereNotIn('role_id', [1,6])->whereNotIn('id', [$user->id])->get();
    $cars = Car::get();
    $countries = Country::get();
    $projects = Project::get();
    $custody_car = CustodyCar::with('car')->where('user_id', $user->id)->first();
    try {
      return [
        'success' => true, 
        'address'         => setting('app.address'),
        'email_admin'     => setting('app.email_admin'),
        'national_phone'  => setting('app.national_phone'),
        'international_phone_number' => setting('app.international_phone_number'),
        'cars' => $cars,
        'countries' => $countries,
        'operators' => $operators,
        'projects' => $projects,
        'custody_car' => $custody_car,
      ];
    } catch(Exception $e){
      return ['success' => true, 'msg' => 'Din pacate s-a produs o eroare la preluarea datelor.'];
    }
  }
  
  public function getCustodyCars(){
    $user = Auth::guard('api')->user();
    if(!$user){
      return [
        'success' => false,
        'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
      ];
    } else{
      if($user->role_id == 5){
        $custody_car = CustodyCar::with('car')->where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
      } else{
        $custody_car = CustodyCar::with('car')->where('user_id', $user->id)->where('status', 'receipt')->orderBy('created_at', 'DESC')->get();
      }
      if(count($custody_car) > 0){
        $custody_car = $custody_car[0];
      }
      $delivery_receipt_notifications = $this->getDeliveryCheck($user->id)['delivery_receipt_notifications'];
      $car_checklist = $this->getDeliveryCheck($user->id)['car_checklist'];
      return [
          'success' => true,
          'custody_car' => $custody_car,
          'delivery_receipt_notifications' => $delivery_receipt_notifications,
          'car_checklist' => $car_checklist,
      ];
    }
  }
  public function getDeliveryCheck($user_id){
    $new_delivery_receipt = DeliveryReport::where('recipient_id', $user_id)->where('status_receipt', null)->get();
    $new_car_checklist = DeliveryReport::with('custody_car.car')->where('recipient_id', $user_id)
      ->where('status_receipt', '!=', 'notif_canceled')
      ->where(function($query){
          $query
            ->whereDate('last_checklist', '<', Carbon::today()->subWeek())
            ->orWhere('last_checklist', null);
      })
      ->get();
    $car_checklist = [];
    $delivery_receipt_notifications = [];
    if(count($new_delivery_receipt) > 0){
      foreach($new_delivery_receipt as $rec){
        array_push($delivery_receipt_notifications, $rec);
      }
    }
    if(count($new_car_checklist) > 0){
      foreach($new_car_checklist as &$rec){
        if($rec->custody_car != null){
          if($rec->custody_car->status_returned == null){
            if($rec->last_checklist == null && $rec->receipt_at < Carbon::today()->subWeek()){
              $rec->car = $rec->custody_car->car;
              array_push($car_checklist, $rec);
            }
          }
        }
      }
    }
    return [
      'delivery_receipt_notifications' => $delivery_receipt_notifications,
      'car_checklist' => $car_checklist,
    ];
  }
  public function getNotifications(){
    $user = Auth::guard('api')->user();
    if(!$user){
      return [
        'success' => false,
        'notifications' => [],
        'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
      ];
    }
    $notifications = Notification::where('user_id', $user->id)->whereNotIn('status', ['canceled', 'shown'])->get();
    if(count($notifications) > 0){
      foreach($notifications as &$notification){
        if($notification->type == 'car_checklist'){
          $car_checklist = CarChecklist::find($notification->field_id);
          $notification->car_checklist = $car_checklist;
        } else{
          $not = Notification::find($notification->id); 
          $not->status = 'shown';
          $not->save();
          $delivery_deport = DeliveryReport::find($notification->field_id);
          $notification->delivery_report = $delivery_deport;
        }
      }
    }
    return [
      'success' => true,
      'notifications' => $notifications
    ];
  }
  
  public function rejectNotification(Request $request){
     $user = Auth::guard('api')->user();
      if(!$user){
        return [
          'success' => false,
          'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
        ];
      }
      if($request->input('id') != null){
        $elem_id = $request->input('id');
        try{
          if($user->role_id != 6){
            if($request->input('type') == 'delivery'){
              $delivery_report = DeliveryReport::find($elem_id);
              $delivery_report->status_receipt = 'canceled';
              $delivery_report->save();
              try{
                $user_id = $delivery_report->user_id;
                $title = "Delivery - receipt";
                $message = 'Delivery - receipt report rejected';
                $this->send_notification($user_id, $title, $message);
              } catch(\Exception $e){}
            }
            if($request->input('type') == 'car_checklist'){
              $delivery_report = DeliveryReport::find($elem_id);
              $delivery_report->status_receipt = 'notif_canceled';
              $delivery_report->save();
            }
            $delivery_receipt_notifications = $this->getDeliveryCheck($user->id)['delivery_receipt_notifications'];
            $car_checklist = $this->getDeliveryCheck($user->id)['car_checklist'];
            return [
                'success' => true,
                'delivery_receipt_notifications' => $delivery_receipt_notifications,
                'car_checklist' => $car_checklist,
                'msg' => '[Success deleted] - The notification has been removed'
            ];
          } else{
            $notif_id = $request->input('id');
            Notification::where('id', $notif_id)->where('user_id', $user->id)->delete();
            $powra_notifs = [];
            $powra_notifications = Notification::with('powra')->where('status', '!=', 'shown')->where('user_id', $user->id)->get();
            if(count($powra_notifications)){
              foreach($powra_notifications as &$pn){
                if($pn->powra != null){
                  $powra = new PowraReport;
                  $powra->id = $pn->powra->id;
                  $powra->country = $pn->powra->country;
                  $powra->project = $pn->powra->project;
                  $powra->other = $pn->powra->other;
                  $powra->site_id = $pn->powra->site_id;
                  $powra->site_book_in = $pn->powra->site_book_in;
                  $powra->site_book_out = $pn->powra->site_book_out;
                  $powra->date = $pn->powra->date;
                  $powra->team_operatives_identification1 = $pn->powra->team_operatives_identification1;
                  $powra->team_operatives_identification2 = $pn->powra->team_operatives_identification2;
                  $powra->team_operatives_identification3 = $pn->powra->team_operatives_identification3;
                  $powra->site_scope_work_insert = $pn->powra->site_scope_work_insert;
                  $powra->site_major_work_steps = $pn->powra->site_major_work_steps;
                  $powra->other_visitors = json_decode($pn->powra->other_visitors, true);
                  $powra->awarness_emergency_plans = $pn->powra->awarness_emergency_plans;
                  $powra->awarness_wellfare = $pn->powra->awarness_wellfare;
                  $powra->lead_engineer_provide = $pn->powra->lead_engineer_provide;
                  $powra->sha1 = $pn->powra->sha1;
                  $powra->sha2 = $pn->powra->sha2;
                  $powra->sha3 = $pn->powra->sha3;
                  $powra->sha4 = $pn->powra->sha4;
                  $powra->sha5 = $pn->powra->sha5;
                  $powra->sha6 = $pn->powra->sha6;
                  $powra->sha7 = $pn->powra->sha7;
                  $powra->sha8 = $pn->powra->sha8;
                  $powra->sha8 = $pn->powra->sha8;
                  $powra->sha9 = $pn->powra->sha9;
                  $powra->powra_elements = json_decode($pn->powra->powra_elements, true);
                  $powra->ppe_elements = json_decode($pn->powra->ppe_elements, true);
                  $powra->user_id = $pn->powra->user_id;
                  $powra->created_at = $pn->powra->user_id;
                  $powra->updated_at = $pn->powra->user_id;
                  $powra->country_retrieved = Country::where('name', $pn->powra->country)->first();
                  $powra->date_show = Carbon::createFromFormat('d/m/Y', $pn->powra->date)->format("Y-m-d");
                  $powra->notif_id = $pn->id;
                  array_push($powra_notifs, $powra);
                }
              }
            }
            return [
                'success' => true,
                'powra_notifications' => $powra_notifs,
                'msg' => '[Success deleted] - The notification has been removed'
            ];
          }
        } catch(\Exception $e){
          return ['success' => false, 'msg' => '[Delete] - There was a problem trying to remove the notification'];
        }
      } else{
        return ['success' => false, 'msg' => '[Error remove] - The notification has not been removed'];
      }
  }
  
  public function removeNotification(Request $request){
     $user = Auth::guard('api')->user();
    if(!$user){
      return [
        'success' => false,
        'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
      ];
    }
    if($request->input('notification_id') != null){
      $notif_id = $request->input('notification_id');
      try{
        Notification::find($notif_id)->delete();
        return ['success' => true, 'msg' => '[Success deleted] - The notification has been removed'];
      } catch(\Exception $e){
        return ['success' => false, 'msg' => '[Delete] - There was a problem trying to remove the notification'];
      }
    }
  }
  public function setNotifications($user_id){
    // expiration of car checklist in 3 days
    $new_delivery_receipt = DeliveryReport::where('recipient_id', $user_id)->where('status_receipt', '!=', null)->where('receipt_at', '!=', null)->whereDate('last_checklist', '<', Carbon::today()->subDays(3))->orWhere('last_checklist', null)->get();
    if($new_delivery_receipt != null){
      $notification = new Notification;
      $notification->user_id = $user_id;
      $notification->type = 'car_checklist';
      $notification->status = 'expires in 3 days'; // cand expira in 3 zile
      $notification->field_id = $new_delivery_receipt->id;
      $notification->created_at = date("Y-m-d H:i:s");
      $notification->updated_at = date("Y-m-d H:i:s");
      $notification->save();
    }
  }
  public function send_delivery_report(Request $request) {
    $user = Auth::guard('api')->user();
    if(!$user){
        return ['success' => false, 'error' => 'User not found'];
    }
    $recipient = User::find($request->input('recipient'));
    
    $imgBack = $this->saveFile($request->file('imgBack'));
    $imgBord = $this->saveFile($request->file('imgBord'));
    $imgFront = $this->saveFile($request->file('imgFront'));
    $imgLeft = $this->saveFile($request->file('imgLeft'));
    $imgRight = $this->saveFile($request->file('imgRight'));
    
    $delivery_report = new DeliveryReport;
    $delivery_report->imgBack = $imgBack;
    $delivery_report->imgBord = $imgBord;
    $delivery_report->imgFront = $imgFront;
    $delivery_report->imgLeft = $imgLeft;
    $delivery_report->imgRight = $imgRight;
    
    $delivery_report->car_brand = $request->input('car_brand');
    $delivery_report->licence_plate = $request->input('licence_plate');
    $delivery_report->delivery_time = $request->input('delivery_time');
    $delivery_report->deliverer = $request->input('deliverer');
    $delivery_report->recipient = $recipient->name;
    $delivery_report->itp_exp_date = $request->input('itp_exp_date');
    $delivery_report->rca_exp_date = $request->input('rca_exp_date');
    $delivery_report->first_aid_box_exp_date = $request->input('first_aid_box_exp_date');
    $delivery_report->fire_extinquisher_exp_date = $request->input('fire_extinquisher_exp_date');
    $delivery_report->warning_triangle = $request->input('warning_triangle');
    $delivery_report->warning_vest = $request->input('warning_vest');
    $delivery_report->spare_tire = $request->input('spare_tire');
    $delivery_report->front_tire_mm = $request->input('front_tire_mm');
    $delivery_report->back_tire_mm = $request->input('back_tire_mm');
    $delivery_report->radio_jack = $request->input('radio_jack');
    $delivery_report->radio_tyre_wrench = $request->input('radio_tyre_wrench');
    $delivery_report->radio_snow_chains = $request->input('radio_snow_chains');
    $delivery_report->radio_windshield_wipers = $request->input('radio_windshield_wipers');
    $delivery_report->radio_clean_car = $request->input('radio_clean_car');
    $delivery_report->radio_full_gas_tank = $request->input('radio_full_gas_tank');
    $delivery_report->radio_oil_level = $request->input('radio_oil_level');
    $delivery_report->radio_antifreeze_level = $request->input('radio_antifreeze_level');
    $delivery_report->radio_break_fluid_level = $request->input('radio_break_fluid_level');
    $delivery_report->radio_issues_of_the_car_body = $request->input('radio_issues_of_the_car_body');
    $delivery_report->details = $request->input('details');
    $delivery_report->radio_demage_report_open = $request->input('radio_demage_report_open');
    $delivery_report->radio_electrical_and_other_issues = $request->input('radio_electrical_and_other_issues');
    $delivery_report->need_repairs = $request->input('need_repairs');
    $delivery_report->kilometers = $request->input('kilometers');
    $delivery_report->last_revision_checkpoint_at = $request->input('last_revision_checkpoint_at');
    $delivery_report->next_revision_date = $request->input('next_revision_date');
    $delivery_report->other_observations = $request->input('other_observations');
    $delivery_report->user_id = $user->id;
    $delivery_report->recipient_id = $recipient->id;
    $delivery_report->status_delivery = 'delivery';
    $delivery_report->status_receipt = null;
    if($request->input('custory_car_id') != null && $request->input('custory_car_id') != 'null'){
      // if i have a custody car, i get the delivery_report_id and put it on the next delivery report for evidence
      $delivery_report->custory_car_id = $request->input('custory_car_id');
    }
    
    if (!$delivery_report->save()) {
        return ['success' => false, 'error' => 'Cannot save the report to database'];
    }
    // add car to custody
    $car = Car::where('licence_plate', $delivery_report->licence_plate)->first();
    if($request->input('custory_car_id') == null){
      $custody_car = new CustodyCar;
      $custody_car->car_id = $car->id;
      $custody_car->created_at = date("Y-m-d H:i:s");
      $custody_car->user_id = $recipient->id;
      $custody_car->delivery_report_id = $delivery_report->id;
      $custody_car->status = 'delivery';
      $custody_car->updated_at = date("Y-m-d H:i:s");
      $custody_car->save();
    } 
    
    
    $notification = new Notification;
    $notification->user_id = $user->id;
    $notification->type = 'delivery_receipt';
    $notification->status = 'new'; // new cand se adauga un nou delivery_receipt, receipt cand se accepta de catre op, canceled cand se sterge notificarea
    $notification->field_id = $delivery_report->id;
    $notification->created_at = date("Y-m-d H:i:s");
    $notification->updated_at = date("Y-m-d H:i:s");
    $notification->save();
    $delivery_report->status = 'delivery';
    $delivery_report->date_show = Carbon::createFromFormat('d/m/Y H:i', $delivery_report->delivery_time)->format("Y-m-d");
    
    try{
      $user_id = $delivery_report->recipient_id;
      $title = "Delivery - receipt";
      $message = 'New delivery - receipt report for '.$delivery_report->licence_plate;
      $this->send_notification($user_id, $title, $message);
    } catch(\Exception $e){}
    return [
        'success' => true,
        'msg'     => 'Successfully sent',
        'delivery_report' => $delivery_report,
    ];
  }
  
  public function send_car_checklist_report(Request $request) {
    $user = Auth::guard('api')->user();
    if(!$user){
        return ['success' => false, 'error' => 'User not found'];
    }
    $recipient = User::find($request->input('recipient'));
    
    $imgBack = $this->saveFile($request->file('imgBack'));
    $imgBord = $this->saveFile($request->file('imgBord'));
    $imgFront = $this->saveFile($request->file('imgFront'));
    $imgLeft = $this->saveFile($request->file('imgLeft'));
    $imgRight = $this->saveFile($request->file('imgRight'));
    
    $car_checklist_report = new CarChecklist;
    $car_checklist_report->imgBack = $imgBack;
    $car_checklist_report->imgBord = $imgBord;
    $car_checklist_report->imgFront = $imgFront;
    $car_checklist_report->imgLeft = $imgLeft;
    $car_checklist_report->imgRight = $imgRight;
    
    $car_checklist_report->car_brand = $request->input('car_brand');
    $car_checklist_report->licence_plate = $request->input('licence_plate');
    $car_checklist_report->name = $request->input('name');
    $car_checklist_report->itp_exp_date = $request->input('itp_exp_date');
    $car_checklist_report->rca_exp_date = $request->input('rca_exp_date');
    $car_checklist_report->first_aid_box_exp_date = $request->input('first_aid_box_exp_date');
    $car_checklist_report->fire_extinquisher_exp_date = $request->input('fire_extinquisher_exp_date');
    $car_checklist_report->warning_triangle = $request->input('warning_triangle');
    $car_checklist_report->warning_vest = $request->input('warning_vest');
    $car_checklist_report->spare_tire = $request->input('spare_tire');
    $car_checklist_report->front_tire_mm = $request->input('front_tire_mm');
    $car_checklist_report->back_tire_mm = $request->input('back_tire_mm');
    $car_checklist_report->radio_jack = $request->input('radio_jack');
    $car_checklist_report->radio_tyre_wrench = $request->input('radio_tyre_wrench');
    $car_checklist_report->radio_snow_chains = $request->input('radio_snow_chains');
    $car_checklist_report->radio_windshield_wipers = $request->input('radio_windshield_wipers');
    $car_checklist_report->radio_oil_level = $request->input('radio_oil_level');
    $car_checklist_report->radio_antifreeze_level = $request->input('radio_antifreeze_level');
    $car_checklist_report->radio_break_fluid_level = $request->input('radio_break_fluid_level');
    $car_checklist_report->radio_issues_of_the_car_body = $request->input('radio_issues_of_the_car_body');
    $car_checklist_report->light_status = $request->input('light_status');
    $car_checklist_report->board_warning_light = $request->input('board_warning_light');
    $car_checklist_report->radio_demage_report_open = $request->input('radio_demage_report_open');
    $car_checklist_report->radio_electrical_and_other_issues = $request->input('radio_electrical_and_other_issues');
    $car_checklist_report->need_repairs = $request->input('need_repairs');
    $car_checklist_report->kilometers = $request->input('kilometers');
    $car_checklist_report->last_revision_checkpoint_at = $request->input('last_revision_checkpoint_at');
    $car_checklist_report->next_revision_date = $request->input('next_revision_date');
    $car_checklist_report->other_observations = $request->input('other_observations');
    $car_checklist_report->details = $request->input('details');
    $car_checklist_report->user_id = $user->id;
    
    if (!$car_checklist_report->save()) {
        return ['success' => false, 'error' => 'Cannot save the report to database'];
    }
    if($request->input('delivery_report_id') != null && $request->input('delivery_report_id') != 'null'){
      $delivery_report = DeliveryReport::find($request->input('delivery_report_id'));
      $delivery_report->last_checklist = date("Y-m-d H:i:s");
      $delivery_report->save();
    }
    try{
      $sefi_pa = User::where('role_id', 5)->get();
      if(count($sefi_pa) > 0){
        foreach($sefi_pa as $pa){
          $user_id = $pa->id;
          $title = "Car checklist";
          $message = 'New car checklist for '.$car_checklist_report->licence_plate.'. Check it on admin panel';
          $this->send_notification($user_id, $title, $message);
        }
      }
    } catch(\Exception $e){}
    return [
        'success' => true,
        'msg'     => 'Successfully sent',
        'car_checklist_report' => $car_checklist_report,
    ];
  }
  
  public function send_powra_report(Request $request) {
    $user = Auth::guard('api')->user();
    if(!$user){
        return ['success' => false, 'error' => 'User not found'];
    }
    $recipient = User::find($request->input('recipient'));
    if($request->input('is_editable') && $request->input('id_report') != null){
      $powra_report = PowraReport::find($request->input('id_report'));
    } else{
      $powra_report = new PowraReport;
    }
    
    $powra_report->country = $request->input('country');
    $powra_report->project = $request->input('project_name');
    $powra_report->other = null;
    $powra_report->site_id = $request->input('site_id');
    $powra_report->site_book_in = $request->input('site_book_in');
    $powra_report->site_book_out = $request->input('site_book_out');
    $powra_report->date = $request->input('date');
    $powra_report->team_operatives_identification1 = $request->input('team_operatives_identification1');
    $powra_report->team_operatives_identification2 = $request->input('team_operatives_identification2');
    $powra_report->team_operatives_identification3 = $request->input('team_operatives_identification3');
    $powra_report->site_scope_work_insert = $request->input('site_scope_work_insert');
    $powra_report->site_major_work_steps = $request->input('site_major_work_steps');
    $powra_report->other_visitors = $request->input('other_visitors');
    $powra_report->awarness_emergency_plans = $request->input('awarness_emergency_plans');
    $powra_report->awarness_wellfare = $request->input('awarness_wellfare');
    $powra_report->sha1 = $request->input('sha1');
    $powra_report->sha2 = $request->input('sha2');
    $powra_report->sha3 = $request->input('sha3');
    $powra_report->sha4 = $request->input('sha4');
    $powra_report->sha5 = $request->input('sha5');
    $powra_report->sha6 = $request->input('sha6');
    $powra_report->sha7 = $request->input('sha7');
    $powra_report->sha8 = $request->input('sha8');
    $powra_report->sha9 = $request->input('sha9');
    $powra_report->powra_elements = $request->input('powra_elements');
    $powra_report->ppe_elements = $request->input('ppe_elements');
    $powra_report->user_id = $user->id;
    
    if (!$powra_report->save()) {
        return ['success' => false, 'error' => 'Cannot save the report to database'];
    }
//     if($powra_report->other != null && $powra_report->other != 'null'){
//       $other_project = Project::where('title', $powra_report->other)->first();
//       if($other_project == null){
//         $project = Project::insert(['title' => $powra_report->other, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")]);
//       }
//     }
    $report_id = $powra_report->id;
    $projects = Project::get();
    $powra_report->powra_elements = json_decode($powra_report->powra_elements, true);
    $powra_report->ppe_elements = json_decode($powra_report->ppe_elements, true);
    $powra_report->other_visitors = json_decode($powra_report->other_visitors, true);
    $powra_report->user = $user;
    
    try{
      if($powra_report->country == 'Romania'){
        $nat_int = 'national';
      } else{
        $nat_int = 'international';
      }
      $powra_users = User::where('role_id', 6)->where('national_international', $nat_int)->where('id', '!=', $user->id)->get();
      if(count($powra_users) > 0){
        foreach($powra_users as $pr){
          $user_id = $pr->id;
          $title = "POWRA";
          if($request->input('is_editable') && $request->input('id_report') != null){
            $message = 'POWRA for project '.$powra_report->project.' was modified';
          } else{
            $message = 'New POWRA for project '.$powra_report->project;
          }
          $notification = new Notification;
          $notification->user_id = $user_id;
          $notification->type = 'powra';
          $notification->status = 'new';
          $notification->field_id = $report_id;
          $notification->save();
          $this->send_notification($user_id, $title, $message);
          Mail::to($pr->email)->send(new Powra($powra_report));
        }
      }
    } catch(\Exception $e){}
    
    return [
        'success' => true,
        'msg'     => $request->input('is_editable') ? 'Successfully edited' : 'Successfully sent',
        'powra_report' => $powra_report,
        'projects' => $projects,
    ];
  }
  
  public function get_delivery_receipt() {
    $user = Auth::guard('api')->user();
    if(!$user){
      return [
        'success' => false,
        'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
      ];
    } else{
      if($user->role_id == 5){
        $retrieved_car_list = DeliveryReport::get();
      } else{
        $retrieved_car_list = DeliveryReport::where(['user_id' => $user->id])->get();
      }
      $car_list = [];
      if(count($retrieved_car_list) > 0){
        foreach($retrieved_car_list as &$car){
          if($user->role_id != 5){
            $new_data = clone($car);
            if($new_data->status_receipt != null){
              $new_data->status = 'receipt';
              $new_data->date_show = $new_data->receipt_at;
              array_push($car_list, $new_data);
            }
          }
          $car->status = 'delivery';
          $car->date_show = Carbon::createFromFormat('d/m/Y H:i', $car->delivery_time)->format("Y-m-d");
          array_push($car_list, $car);
          
        }
      }
      return [
        'success' => true,
        'car_list' => $car_list
      ];
    }
  }
  
  public function get_car_checklist(){
    $user = Auth::guard('api')->user();
    if(!$user){
      return [
        'success' => false,
        'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
      ];
    } else{
      $retrieved_car_list = CarChecklist::where('user_id', $user->id)->get();
      if(count($retrieved_car_list) > 0){
        foreach($retrieved_car_list as &$item){
          $item->status = 'completed';
        }
      }
      return [
        'success' => true,
        'car_list' => $retrieved_car_list
      ];
    }
  }
  
  public function getPowraData(){
    $user = Auth::guard('api')->user();
    if(!$user){
      return [
        'success' => false,
        'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
      ];
    } else{
      $powra_notifs = [];
      $powra_notifications = Notification::with('powra')->where('status', '!=', 'shown')->where('user_id', $user->id)->get();
      if(count($powra_notifications) > 0){
        foreach($powra_notifications as &$pn){
          if($pn->powra != null){
            $powra = new PowraReport;
            $powra->id = $pn->powra->id;
            $powra->country = $pn->powra->country;
            $powra->project = $pn->powra->project;
            $powra->other = $pn->powra->other;
            $powra->site_id = $pn->powra->site_id;
            $powra->site_book_in = $pn->powra->site_book_in;
            $powra->site_book_out = $pn->powra->site_book_out;
            $powra->date = $pn->powra->date;
            $powra->team_operatives_identification1 = $pn->powra->team_operatives_identification1;
            $powra->team_operatives_identification2 = $pn->powra->team_operatives_identification2;
            $powra->team_operatives_identification3 = $pn->powra->team_operatives_identification3;
            $powra->site_scope_work_insert = $pn->powra->site_scope_work_insert;
            $powra->site_major_work_steps = $pn->powra->site_major_work_steps;
            $powra->other_visitors = json_decode($pn->powra->other_visitors, true);
            $powra->awarness_emergency_plans = $pn->powra->awarness_emergency_plans;
            $powra->awarness_wellfare = $pn->powra->awarness_wellfare;
            $powra->lead_engineer_provide = $pn->powra->lead_engineer_provide;
            $powra->sha1 = $pn->powra->sha1;
            $powra->sha2 = $pn->powra->sha2;
            $powra->sha3 = $pn->powra->sha3;
            $powra->sha4 = $pn->powra->sha4;
            $powra->sha5 = $pn->powra->sha5;
            $powra->sha6 = $pn->powra->sha6;
            $powra->sha7 = $pn->powra->sha7;
            $powra->sha8 = $pn->powra->sha8;
            $powra->sha8 = $pn->powra->sha8;
            $powra->sha9 = $pn->powra->sha9;
            $powra->powra_elements = json_decode($pn->powra->powra_elements, true);
            $powra->ppe_elements = json_decode($pn->powra->ppe_elements, true);
            $powra->user_id = $pn->powra->user_id;
            $powra->created_at = $pn->powra->user_id;
            $powra->updated_at = $pn->powra->user_id;
            $powra->country_retrieved = Country::where('name', $pn->powra->country)->first();
            $powra->date_show = Carbon::createFromFormat('d/m/Y', $pn->powra->date)->format("Y-m-d");
            $powra->notif_id = $pn->id;
            array_push($powra_notifs, $powra);
          }
        }
      }
      return ['success' => true, 'powra_notifications' => $powra_notifs];
    }
  }
  
  public function get_powra_reports(){
    $user = Auth::guard('api')->user();
    if(!$user){
      return [
        'success' => false,
        'msg'     => 'Trebuie sa te autentifici pentru a acea acces la acest modul!',
      ];
    } else{
      if($user->role_id != 6){
          $powra_reports = PowraReport::where('user_id', $user->id)->get();
      } else{
        if($user->national_international == 'national'){
          $powra_reports = PowraReport::where(['country' => 'Romania'])->get();
        } else{
          $powra_reports = PowraReport::whereNotIn('country', ['Romania'])->get();
        }
      }
      if(count($powra_reports) > 0){
        foreach($powra_reports as &$item){
          $item->country_retrieved = Country::where('name', $item->country)->first();
          $item->other_visitors = json_decode($item->other_visitors, true);
          $item->powra_elements = json_decode($item->powra_elements, true);
          $item->ppe_elements = json_decode($item->ppe_elements, true);
          $item->date_show = Carbon::createFromFormat('d/m/Y', $item->date)->format("Y-m-d");
        }
      }
      return [
        'success' => true,
        'powra_reports' => $powra_reports
      ];
    }
  }
  
  public function accept_report(Request $request) {
    $user = Auth::guard('api')->user();
    if(!$user){
        return ['success' => false, 'error' => 'User not found'];
    }
    $rep = DeliveryReport::find($request->input('id'));
    $rep->status_receipt = 'receipt';
    $rep->receipt_at = date("Y-m-d H:i:s");
    try{
      $rep->save();

      if($rep->custory_car_id != null){  
        $custody = CustodyCar::find($rep->custory_car_id);
        if($custody != null){
          $custody->status = 'receipt';
          $custody->user_id = $user->id;
          $custody->save();
        }
      } else{
        $car = Car::where('car_brand', $rep->car_brand)->where('licence_plate', $rep->licence_plate)->first();
        $custody = new CustodyCar;
        $custody->user_id = $user->id;
        $custody->car_id = $car->id;
        $custody->status = 'receipt';
        $custody->delivery_report_id = $rep->id;
        $custody->status_returned = null;
        $custody->save();
      }
      $delivery_receipt_notifications = $this->getDeliveryCheck($user->id)['delivery_receipt_notifications'];
      $car_checklist = $this->getDeliveryCheck($user->id)['car_checklist'];
      $notification = Notification::where(['field_id' => $rep->id, 'type' => 'delivery_receipt', 'user_id' => $user->id])->first();
      if($notification != null){
        $notification->status = 'receipt'; // new cand se adauga un nou delivery_receipt, receipt cand se accepta de catre op, canceled cand se sterge notificarea
        $notification->updated_at = date("Y-m-d H:i:s");
        $notification->save();
      }
      try{
        $user_id = $rep->user_id;
        $title = "Accepted";
        $message = 'Delivery - receipt report for '.$rep->licence_plate. ' was accepted';
        $this->send_notification($user_id, $title, $message);
      } catch(\Exception $e){}
      return [
        'success' => true, 
        'msg' => '[Accepted] The receipt has been accepted',
        'delivery_receipt_notifications' => $delivery_receipt_notifications,
        'car_checklist' => $car_checklist,
      ];
    } catch(\Exception $e){
      return ['success' => false, 'msg' => '[Accept - Error] An error occured. Please try again later'];
    }
  }
  
  public function saveFile($file){
    $disk = "public";
    $destination_path = "uploads/delivery_reports";
    $new_file_name = md5($file->getClientOriginalName().time()).'.'.$file->getClientOriginalExtension();
    $file_path = $file->storeAs($destination_path, $new_file_name, $disk);
    return $file_path;
  }
  
  public static function onesignal_params($user_id, $title, $message) {
      $params = [];
      $params['headings'] = ['en' => $title];
      $params['contents'] = [
          'en' => $message
      ];
      $params['filters'] = [
        ['field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $user_id],
      ];
      return $params;
  }
  public function send_notification($user_id, $title, $message){
    $notification = (new self())->onesignal_params($user_id, $title, $message);
    $onesignal_response = OneSignal::sendNotificationCustom($notification);
    if (!$onesignal_response) {
//       $this->info('Rezultat trimitere notificare: false');
    }
    if (!$onesignal_parsed = json_decode($onesignal_response->getBody()->getContents(), true)) {
//       $this->info('Rezultat trimitere notificare: false');
    }
//     $this->info('Rezultat trimitere notificare: true');
  }
}