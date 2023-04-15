<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\CarChecklist;
use App\DeliveryReport;
use Carbon\Carbon;
use OneSignal;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check notifications for onesignal';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data_curenta = Carbon::now();
        $last_week = Carbon::now()->subWeek()->format('Y-m-d H:i:s');
        $car_checklists = CarChecklist::where('created_at', '>=', $last_week)->get();
        if(count($car_checklists) > 0){
          $sending_notif = [];
          foreach($car_checklists as &$car_checklist){
              $known_date =  new Carbon($car_checklist->created_at);
              $diff = $known_date->diffInDays($data_curenta);
              if(in_array($diff, [-1,1,3,7])){
                $params = (new self())->onesignal_params($diff, $car_checklist->licence_plate, $car_checklist->user_id);
                array_push($sending_notif, $params);
              }
            // trimit notificarea prin onesignal
          }
          foreach($sending_notif as $notification) {
            $onesignal_response = OneSignal::sendNotificationCustom($notification);
            if (!$onesignal_response) {
              $this->info('Rezultat trimitere notificare: false');
            }
            if (!$onesignal_parsed = json_decode($onesignal_response->getBody()->getContents(), true)) {
              $this->info('Rezultat trimitere notificare: false');
            }
            $this->info('Rezultat trimitere notificare: true');
          }
        }
    }
  
    public static function onesignal_params($zile, $car_nr, $user_id) {
      $params = [];
      $params['headings'] = ['en' => 'Car checklist'];
      if($zile == -1){
        $params['contents'] = [
          'en' => 'Checklist for '.$car_nr.' expired yesterday! Please do it urgently!'
        ];
      }else if($zile != 1){
        $params['contents'] = [
          'en' => 'Checklist for '.$car_nr.' expires over '.$zile.' days!'
        ];
      } else{
        $params['contents'] = [
          'en' => 'Checklist for '.$car_nr.' expires tommorrow!'
        ];
      }
      $params['filters'] = [
        ['field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $user_id],
      ];
      return $params;
    }
}
