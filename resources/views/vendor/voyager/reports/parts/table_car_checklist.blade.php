<div class="container-table-reports-car-checklist">
  @if($reports_car_checklist != null)
  <div class="container-tabele-rapoarte">
    <div class="tabel-produse-raport"></div>
    <div class="tabel-categorii-raport"></div>
    <div class="tabel-vanzari-raport">
      <div class="container-tabel-raport" style="max-height: 300px; overflow-y: scroll;">
          <table id='dataTableCarChecklist' class='table table-hover dataTable no-footer' role='grid' aria-describedby='dataTable_info'>
              <thead>
                  <tr role='row'>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>#</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Operator</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Car brand</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Licence plate</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>ITP expiration date</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>RCA expiration date</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>First AID box exp date </th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Fire extinquisher exp date</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Warning triangle</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Warning vest</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Spare tire</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Front tire mm</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Back tire mm</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Jack</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Tyre wrench</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Snow chains</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Windshield wipers</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Oil level</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Antifreeze level</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Issues of the car body</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Light status</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Board warning light</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Demage report open</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Electrical and other issues</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Break fluid level</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Need repairs</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Kilometers</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Last revision checkpoint</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Next revision date</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Other observations</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Details</th>
                  </tr>
              </thead>
              <tbody>
                  @php $id_curent = 1; @endphp
                  @foreach($reports_car_checklist as $key => $report)
                     <tr role='row'>
                          <td><p>{{$id_curent}}</p></td>
                          <td><p>{{$report->name}}</p></td>
                          <td><p>{{$report->car_brand}}</p></td>
                          <td><p>{{$report->licence_plate}}</p></td>
                          <td><p>{{$report->itp_exp_date}}</p></td>
                          <td><p>{{$report->rca_exp_date}}</p></td>
                          <td><p>{{$report->first_aid_box_exp_date}}</p></td>
                          <td><p>{{$report->fire_extinquisher_exp_date}}</p></td>
                          <td><p>{{$report->warning_triangle}}</p></td>
                          <td><p>{{$report->warning_vest}}</p></td>                                          
                          <td><p>{{$report->spare_tire}}</p></td>                                          
                          <td><p>{{$report->front_tire_mm}}</p></td>                                          
                          <td><p>{{$report->back_tire_mm}}</p></td>                                          
                          <td><p>{{$report->radio_jack}}</p></td>                                          
                          <td><p>{{$report->radio_snow_chains}}</p></td>                                          
                          <td><p>{{$report->radio_tyre_wrench}}</p></td>                                          
                          <td><p>{{$report->radio_windshield_wipers}}</p></td>                                          
                          <td><p>{{$report->radio_oil_level}}</p></td>                                          
                          <td><p>{{$report->radio_antifreeze_level}}</p></td>                                          
                          <td><p>{{$report->radio_issues_of_the_car_body}}</p></td>                                          
                          <td><p>{{$report->light_status}}</p></td>                                          
                          <td><p>{{$report->board_warning_light}}</p></td>                                          
                          <td><p>{{$report->radio_demage_report_open}}</p></td>                                          
                          <td><p>{{$report->radio_electrical_and_other_issues}}</p></td>                          
                          <td><p>{{$report->radio_break_fluid_level}}</p></td>                                          
                          <td><p>{{$report->need_repairs}}</p></td>                                          
                          <td><p>{{$report->kilometers}}</p></td>                                          
                          <td><p>{{$report->last_revision_checkpoint_at}}</p></td>                                          
                          <td><p>{{$report->next_revision_date}}</p></td>                                          
                          <td><p>{{$report->other_observations}}</p></td>                                          
                          <td><p>{{$report->details}}</p></td>                                                          
                      </tr>
                     @php $id_curent++;  @endphp
                  @endforeach

              </tbody>
          </table>
        </div>
    </div>

  </div>
  @else
    <label style="width: 100%; padding-left: 40px;" class="control-label" for="name">No report available</label>
  @endif
</div>