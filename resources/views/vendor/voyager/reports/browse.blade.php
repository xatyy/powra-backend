@php
	$reports = \App\DeliveryReport::get();
	$reports_car_checklist = \App\CarChecklist::orderBy('name', 'ASC')->get();
  $total_cars = \App\Car::count();
  $total_operators = \App\Models\User::where('role_id', 4)->count();
  $total_cars = \App\Car::count();
  $custody_cars = \App\CustodyCar::select('car_id')->groupBy('car_id')->count();
  $powra_reports = \App\PowraReport::count();
  $operators = \App\Models\User::select('id', 'name', 'email', 'phone')->where('role_id', 4)->get();
  $reports_powra = \App\PowraReport::orderBy('project', 'ASC')->get();
  $is_pa = Auth::user()->role['name'] == 'responsabil_pa' ? true : false;
  if($reports_powra){
    foreach($reports_powra as $report){
      if(\App\Http\Controllers\ReportsController::checkPowra($report->powra_elements)){
        $report->powra = 'LOW RISK';
      } else{
        $report->powra = 'HIGH RISK';
      }
    }
  }
@endphp

@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))


@section('page_header')

    <div class="container-fluid">

        <h1 class="page-title">

            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}

        </h1>
        @include('voyager::multilingual.language-selector')

    </div>

@stop

@section('content')

  <div class="page-content browse container-fluid">

    <div class="row reports-container">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total cars</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$total_cars}}</div>
                        </div>
                        <div class="col-auto">
                            <span class="icon voyager-truck"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total operators</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$total_operators}}</div>
                        </div>
                        <div class="col-auto">
                            <span class="icon voyager-group"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Custody cars</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$custody_cars}}</div>
                        </div>
                        <div class="col-auto">
                             <span class="icon voyager-truck"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!$is_pa)
          <!-- Pending Requests Card Example -->
          <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                      <div class="row no-gutters align-items-center">
                          <div class="col mr-2">
                              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                  POWRA reports</div>
                              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$powra_reports}}</div>
                          </div>
                          <div class="col-auto">
                               <span class="icon voyager-calendar"></span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        @endif
    </div>
  </div>
  @include('voyager::alerts')

  <div class="analytics-container">
    <h1 style="padding-left: 0;" class="page-title" id="rapoarte-content-car-history"><i class=""></i>Car History (select start date and end date to filter results)</h1>
    <form class="formular-rapoarte" action='get-rapoarte-data' method="POST" enctype="multipart/form-data">
     {{ csrf_field() }}
      <div class="input-group input-daterange">
          <input type="text" name="data_inceput" class="datepicker form-control" placeholder="Data inceput">
          <div class="input-group-addon">-</div>
          <input type="text" name="data_sfarsit" class="datepicker form-control" placeholder="Data sfarsit">
      </div>
      <button type="button" class="btn btn-primary btnGetRapoarte" rep="delivery">Generate report</button>
      <a class="btn btn-success btn-add-new btn-export">
          <i class="voyager-plus"></i> <span>Export data</span>
      </a>
    </form>
    @include('vendor.voyager.reports.parts.table')
    <h1 style="padding-left: 0;" class="page-title" id="rapoarte-content-car-checklist"><i class=""></i>Car checklist (select start date and end date to filter results or select the operator)</h1>
    <form class="formular-rapoarte-car-checklist" action='get-rapoarte-data-carchecklist' method="POST" enctype="multipart/form-data">
     {{ csrf_field() }}
      <div class="input-group input-daterange">
          <input type="text" name="data_inceput" class="datepicker form-control" placeholder="Data inceput">
          <div class="input-group-addon">-</div>
          <input type="text" name="data_sfarsit" class="datepicker form-control" placeholder="Data sfarsit">
      </div>
      <select name="user_id" class="form-control" style="width: 25%;">
        <option selected disabled>Select operator for filtering</option>
        @foreach($operators as $operator)
          <option value="{{$operator->id}}">{{$operator->name}}</option>
        @endforeach
      </select>
      <button type="button" class="btn btn-primary btnGetRapoarte" rep="car-checklist">Generate report</button>
      <a class="btn btn-success btn-add-new btn-export-car-checklist">
          <i class="voyager-plus"></i> <span>Export data</span>
      </a>
    </form>
    @include('vendor.voyager.reports.parts.table_car_checklist')
    @if(!$is_pa)
      <h1 style="padding-left: 0;" class="page-title" id="rapoarte-content-powra"><i class=""></i>POWRA reports (select start date and end date to filter results or select any filter)</h1>
      <form class="formular-rapoarte-car-checklist" action='get-rapoarte-data-powra' method="POST" enctype="multipart/form-data">
       {{ csrf_field() }}
        <div class="input-group input-daterange">
            <input type="text" name="data_inceput" class="datepicker form-control" placeholder="Data inceput">
            <div class="input-group-addon">-</div>
            <input type="text" name="data_sfarsit" class="datepicker form-control" placeholder="Data sfarsit">
        </div>
        <select name="user_id" class="form-control" style="width: 25%;">
          <option selected disabled>Select filter</option>
          <option value="country">Country</option>
          <option value="project">Project</option>
        </select>
        <button type="button" class="btn btn-primary btnGetRapoarte" rep="powra">Generate report</button>
        <a class="btn btn-success btn-add-new btn-export-powra">
            <i class="voyager-plus"></i> <span>Export data</span>
        </a>
      </form>
      @include('vendor.voyager.reports.parts.table_powra')
    @endif
  </div>
  
@stop

@section('css')
<style>
	.mb-4, .my-4 {
	    margin-bottom: 1.5rem!important;
	}
	.card {
	    position: relative;
	    display: flex;
	    flex-direction: column;
	    min-width: 0;
	    word-wrap: break-word;
	    background-color: #fff;
	    background-clip: border-box;
	    border: 1px solid #e3e6f0;
	    border-radius: .35rem;
	}
	.shadow {
	    box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15)!important;
	}
	.border-left-primary {
	    border-left: .25rem solid #4e73df!important;
	}

	.pb-2, .py-2 {
	    padding-bottom: .5rem!important;
	}
	.pt-2, .py-2 {
	    padding-top: .5rem!important;
	}
	.h-100 {
	    height: 100%!important;
	}
	.card-body {
	    flex: 1 1 auto;
	    min-height: 1px;
	    padding: 1.25rem;
	}
	.align-items-center {
	    align-items: center!important;
	}

	.no-gutters {
	    margin-right: 0;
	    margin-left: 0;
	}
	.reports-container .no-gutters>.col, .reports-container .no-gutters>[class*=col-] {
	    padding-right: 0;
	    padding-left: 0;
	    margin-bottom: 0;
	}
	.font-weight-bold {
	    font-weight: 700!important;
	}

	.dropdown .dropdown-menu .dropdown-header, .sidebar .sidebar-heading, .text-uppercase {
	    text-transform: uppercase!important;
	}
	.mb-1, .my-1 {
	    margin-bottom: .25rem!important;
	}
	.mb-0, .my-0 {
	    margin-bottom: 0!important;
	}

	.h5, h5 {
	    font-size: 1.25rem;
	}
	.col-auto {
	    flex: 0 0 auto;
	    width: auto;
	    max-width: 100%;
	}
	.text-gray-300 {
	    color: #dddfeb!important;
	}
	.reports-container .row {
	    display: flex;
	    flex-wrap: wrap;
	    margin-right: -.75rem;
	    margin-left: -.75rem;
	}
	.text-success {
	    color: #1cc88a!important;
	}
	.text-info {
	    color: #36b9cc!important;
	}
	.text-warning {
	    color: #f6c23e!important;
	}
	.border-left-success {
	    border-left: .25rem solid #1cc88a!important;
	}
	.border-left-info {
	    border-left: .25rem solid #36b9cc!important;
	}
	.border-left-warning {
	    border-left: .25rem solid #f6c23e!important;
	}
	.col {
	    flex-basis: 0;
	    flex-grow: 1;
	    max-width: 100%;
	}
	span.icon{
		font-size: 32px;
	}
	.progress-sm {
	    height: .5rem;
	}

	.mr-2, .mx-2 {
	    margin-right: .5rem!important;
	}
	.progress {
	    display: flex !important;
	    height: 1rem !important;
	    overflow: hidden !important;
	    line-height: 0 !important;
	    font-size: .75rem !important;
	    background-color: #eaecf4 !important;
	    border-radius: .35rem !important;
	    margin-bottom: 0;
	    margin-top: 10px;
	    margin-left: 10px;
	}
	.bg-info {
	    background-color: #36b9cc!important;
	}

	.progress-bar {
	    display: flex !important;
	    flex-direction: column !important;
	    justify-content: center !important;
	    overflow: hidden !important;
	    color: #fff !important;
	    text-align: center !important;
	    white-space: nowrap !important;
	    transition: width .6s ease !important;
	}
  .formular-rapoarte, .formular-rapoarte-car-checklist, .formular-rapoarte-powra{
    display: flex;
    align-items: center;
    justify-content: flex-start;
  }
  .btnGetRapoarte{
    margin-top: 0;
    margin-bottom: 0;
    margin-left: 10px;
    margin-right: 10px;
  }
  .btn-export,.btn-export-car-checklist, .btn-export-powra{
    margin-top: 0;
    margin-bottom: 0;
  }
</style>
<style>
	@media (min-width: 768px){
		.col-md-6 {
		    flex: 0 0 50%;
		    max-width: 50%;
		}
	}
	@media (min-width: 1200px){
		.col-xl-3 {
		    flex: 0 0 25%;
		    max-width: 25%;
		}
	}
</style>
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))

    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">

@endif

@stop



@section('javascript')

    <!-- DataTables -->

    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))

        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>

    @endif

   <script>
      $(".btnGetRapoarte").click(function() {
        var which_rep = $(this).attr('rep');
        $.ajax({
            method: 'POST',
            url: $(this).closest("form").attr("action"),
            data: $(this).closest("form").serializeArray(),
            context: this, 
            async: true, 
            cache: false, 
            dataType: 'json'
        }).done(function(res) {
            if (res.success == true) {
                toastr.success(res.msg, 'Success');
                if(which_rep == 'car-checklist'){
                  $(".container-table-reports-car-checklist").html(res.result_table);
                }
                if(which_rep == 'delivery'){
                  $(".container-table-reports").html(res.result_table);
                }
                if(which_rep == 'powra'){
                  $(".container-table-reports-powra").html(res.result_table);
                }
            } else { 
              toastr.error(res.error, 'Error');
            }
        })
        .fail(function(xhr, status, error) {
          if(xhr && xhr.responseJSON && xhr.responseJSON.message && xhr.responseJSON.message.indexOf("CSRF token mismatch") >= 0){
            window.location.reload();
          }
        });
        return;
      });
      $(".btn-export").click(function(){
          var today = new Date();
          var dd = String(today.getDate()).padStart(2, '0');
          var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
          var yyyy = today.getFullYear();

          today = mm + '_' + dd + '_' + yyyy;
          $("#dataTable").table2excel({
              exclude: ".noExl",
              name: today+"_report",
              filename: today+"_report",
              fileext:".xlsx",
              preserveColors:false
          }); 
      });
      $(".btn-export-car-checklist").click(function(){
          var today = new Date();
          var dd = String(today.getDate()).padStart(2, '0');
          var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
          var yyyy = today.getFullYear();

          today = mm + '_' + dd + '_' + yyyy;
          $("#dataTableCarChecklist").table2excel({
              exclude: ".noExl",
              name: today+"_report_car_checklist",
              filename: today+"_report_car_checklist",
              fileext:".xlsx",
              preserveColors:false
          }); 
      });
      $(".btn-export-powra").click(function(){
          var today = new Date();
          var dd = String(today.getDate()).padStart(2, '0');
          var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
          var yyyy = today.getFullYear();

          today = mm + '_' + dd + '_' + yyyy;
          $("#dataTablePowra").table2excel({
              exclude: ".noExl",
              name: today+"_report_powra",
              filename: today+"_report_powra",
              fileext:".xlsx",
              preserveColors:false
          }); 
      });
     
    </script>

@stop
