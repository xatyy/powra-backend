<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DeliveryReport;
use App\CarChecklist;
use App\PowraReport;
use Validator;
use Carbon\Carbon;

class ReportsController extends Controller
{
   public function generate_report(Request $request){
     $form_data = $request->only(['data_inceput','data_sfarsit']);
      $validationRules = [
          'data_inceput'    => ['required'],
          'data_sfarsit'    => ['required'],
      ];

      $validationMessages = [
          'data_inceput.required'    => "You must select the start date!",
          'data_sfarsit.required'    => "You must select the end date!",
      ];
      $validator = Validator::make($form_data, $validationRules, $validationMessages);
      if ($validator->fails())
          return ['success' => false, 'error' => $validator->errors()->all()];  
      else{
        if($request->input('data_inceput') > $request->input('data_sfarsit')){
          return ['success' => false, 'error' => [0 => 'Start date must be less than end date!']];
        }
        $data_inceput = explode(" ",$request->input('data_inceput'))[0];
        $data_sfarsit = explode(" ",$request->input('data_sfarsit'))[0];

        $start_date  = Carbon::parse($request->input('data_inceput'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('data_sfarsit'))->format('Y-m-d');
        $start_date .= " 00:00:00";
        $end_date   .= " 23:59:59";
        $reports = DeliveryReport::whereBetween('created_at', [$start_date, $end_date])->get();
        return [
          'success' => true, 
          'result_table' => (string)view('vendor.voyager.reports.parts.table', ['reports' => $reports]),
        ];
      }
   } 
   public function generate_report_car_checklist(Request $request){
     $form_data = $request->only(['data_inceput','data_sfarsit', 'user_id']);
      if($request->input('data_inceput') != null && $request->input('data_sfarsit') != null){
        if($request->input('data_inceput') > $request->input('data_sfarsit')){
          return ['success' => false, 'error' => [0 => 'Start date must be less than end date!']];
        }
        $data_inceput = explode(" ",$request->input('data_inceput'))[0];
        $data_sfarsit = explode(" ",$request->input('data_sfarsit'))[0];

        $start_date  = Carbon::parse($request->input('data_inceput'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('data_sfarsit'))->format('Y-m-d');
        $start_date .= " 00:00:00";
        $end_date   .= " 23:59:59";
        if($request->input('user_id') != null){
          $reports = CarChecklist::whereBetween('created_at', [$start_date, $end_date])->where('user_id', $request->input('user_id'))->orderBy('name', 'ASC')->get();
        } else{
          $reports = CarChecklist::whereBetween('created_at', [$start_date, $end_date])->orderBy('name', 'ASC')->get();
        }
        return [
          'success' => true, 
          'result_table' => (string)view('vendor.voyager.reports.parts.table_car_checklist', ['reports_car_checklist' => $reports]),
        ];
    } else{
      if($request->input('user_id') != null){
        $reports = CarChecklist::where('user_id', $request->input('user_id'))->orderBy('name', 'ASC')->get();
      } else{
        $reports = CarChecklist::orderBy('name', 'ASC')->get();
      }
      return [
        'success' => true, 
        'result_table' => (string)view('vendor.voyager.reports.parts.table_car_checklist', ['reports_car_checklist' => $reports]),
      ];
    }
   }
  
  public function generate_report_powra(Request $request){
     $form_data = $request->only(['data_inceput','data_sfarsit', 'filter']);
      if($request->input('data_inceput') != null && $request->input('data_sfarsit') != null){
        if($request->input('data_inceput') > $request->input('data_sfarsit')){
          return ['success' => false, 'error' => [0 => 'Start date must be less than end date!']];
        }
        $data_inceput = explode(" ",$request->input('data_inceput'))[0];
        $data_sfarsit = explode(" ",$request->input('data_sfarsit'))[0];

        $start_date  = Carbon::parse($request->input('data_inceput'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('data_sfarsit'))->format('Y-m-d');
        $start_date .= " 00:00:00";
        $end_date   .= " 23:59:59";
        if($request->input('filter') != null){
          $filter = $request->input('filter');
          if($filter == 'country'){
            $reports = PowraReport::whereBetween('created_at', [$start_date, $end_date])->where('country', $filter)->orderBy('country', 'ASC')->get();
          } else{
            $reports = PowraReport::whereBetween('created_at', [$start_date, $end_date])->where('project', $filter)->orderBy('project', 'ASC')->get();
          }
        } else{
          $reports = PowraReport::whereBetween('created_at', [$start_date, $end_date])->orderBy('project', 'ASC')->orderBy('project', 'ASC')->get();
        }
        if($reports){
          foreach($reports as $report){
            if((new self())->checkPowra($report->powra_elements)){
              $report->powra = 'LOW RISK';
            } else{
              $report->powra = 'HIGH RISK';
            }
          }
        }
        return [
          'success' => true, 
          'result_table' => (string)view('vendor.voyager.reports.parts.table_powra', ['reports_powra' => $reports]),
        ];
    } else{
      if($request->input('filter') != null){
        $filter = $request->input('filter');
        if($filter == 'country'){
          $reports = PowraReport::where('country', $filter)->orderBy('country', 'ASC')->get();
        } else{
          $reports = PowraReport::where('project', $filter)->orderBy('project', 'ASC')->get();
        }
      } else{
        $reports = PowraReport::orderBy('project', 'ASC')->get();
      }
      if($reports){
        foreach($reports as $report){
          if((new self())->checkPowra($report->powra_elements)){
            $report->powra = 'LOW RISK';
          } else{
            $report->powra = 'HIGH RISK';
          }
        }
      }
      return [
        'success' => true, 
        'result_table' => (string)view('vendor.voyager.reports.parts.table_powra', ['reports_powra' => $reports]),
      ];
    }
   }
  
  public static function checkPowra($powra){
    $result = false;
    $powra = json_decode($powra, true);
    foreach($powra as $pw){
      $counter_resp = 0;
      $counter_hazard = 0;
      foreach($pw['select_options'] as $key => $opt){
        if((array_key_exists($key, $pw['options']) && $pw['options'][$key] == 'N/A' && $opt)){
          $counter_resp++;
        }
        if($pw['hazard_identification'][0]){
          $counter_hazard++;
        }
      }
    }
    if($counter_resp+$counter_hazard == 10){
      $result = true;
    }
    return $result;
  }
}
