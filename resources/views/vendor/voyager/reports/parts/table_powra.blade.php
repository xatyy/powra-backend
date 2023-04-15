<div class="container-table-reports-powra">
  @if($reports_powra != null)
  <div class="container-tabele-rapoarte">
    <div class="tabel-produse-raport"></div>
    <div class="tabel-categorii-raport"></div>
    <div class="tabel-vanzari-raport">
      <div class="container-tabel-raport" style="max-height: 300px; overflow-y: scroll;">
          <table id='dataTablePowra' class='table table-hover dataTable no-footer' role='grid' aria-describedby='dataTable_info'>
              <thead>
                  <tr role='row'>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>#</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Data</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Project</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Operator</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Country</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Site Book In</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Site Book Out</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Team(all team operators)</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Work insert</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Work steps</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Other visitors</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Emergency plans</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Wellfare</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Engineer provide</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA1</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA2</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA3</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA4</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA5</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA6</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA7</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA8</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>SHA9</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>POWRA</th>
                  </tr>
              </thead>
              <tbody>
                  @php $id_curent = 1; @endphp
                  @foreach($reports_powra as $key => $report)
                     @php
                      $report->other_visitors = json_decode($report->other_visitors, true);
                     @endphp
                     <tr role='row'>
                          <td><p>{{$id_curent}}</p></td>
                          <td><p>{{$report->date}}</p></td>                                                         
                          <td><p>{{$report->project}}</p></td>                                                         
                          <td><p>{{$report->user->name}}</p></td>                                                         
                          <td><p>{{$report->country}}</p></td>                                                         
                          <td><p>{{$report->site_book_in}}</p></td>                                                         
                          <td><p>{{$report->site_book_out}}</p></td>                                                         
                          <td><p>{{$report->team_operatives_identification1 != null ? $report->team_operatives_identification1 : ''}}{{$report->team_operatives_identification2 != null ? ', '.$report->team_operatives_identification2 : ''}}{{$report->team_operatives_identification3 != null ? ', '.$report->team_operatives_identification3 : ''}}</p></td>                                                         
                          <td><p>{{$report->site_scope_work_insert}}</p></td>                                                         
                          <td><p>{{$report->site_major_work_steps}}</p></td>                                                         
                          <td>
                            @if(count($report->other_visitors) > 0)
                              @foreach($report->other_visitors as $visitor)
                                <p>{{$visitor}}</p>
                              @endforeach
                            @else
                              <p>-</p>
                            @endif
                          </td>                                                         
                          <td><p>{{$report->awarness_emergency_plans ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->awarness_wellfare ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->lead_engineer_provide != null ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha1 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha2 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha3 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha4 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha5 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha6 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha7 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha8 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->sha9 ? 'YES' : 'NO'}}</p></td>                                                         
                          <td><p>{{$report->powra}}</p></td>                                                         
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