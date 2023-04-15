<div class="container-table-reports">
  @if($reports != null)
  <div class="container-tabele-rapoarte">
    <div class="tabel-produse-raport"></div>
    <div class="tabel-categorii-raport"></div>
    <div class="tabel-vanzari-raport">
      <div class="container-tabel-raport" style="max-height: 300px; overflow-y: scroll;">
          <table id='dataTable' class='table table-hover dataTable no-footer' role='grid' aria-describedby='dataTable_info'>
              <thead>
                  <tr role='row'>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>#</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Car brand</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Licence plate</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Deliverer</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Recipient</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Kilometers</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Last Revision Checkpoint km</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Next Revision date</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Last Checklist date</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Delivered at</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1' tabindex='0' aria-controls='dataTable' rowspan='1'>Received at</th>
                  </tr>
              </thead>
              <tbody>
                  @php $id_curent = 1; @endphp
                  @foreach($reports as $key => $report)
                     <tr role='row'>
                          <td><p>{{$id_curent}}</p></td>
                          <td><p>{{$report->car_brand}}</p></td>
                          <td><p>{{$report->licence_plate}}</p></td>
                          <td><p><a href='/admin/users/{{$report->user_id}}' target='_blank'>{{$report->deliverer}}</a></p></td>
                          <td><p><a href='/admin/users/{{$report->recipient_id}}' target='_blank'>{{$report->recipient}}</a></p></td>
                          <td><p>{{$report->kilometers}} km</p></td>
                          <td><p>{{$report->last_revision_checkpoint_at}}</p></td>
                          <td><p>{{$report->next_revision_date}}</p></td>
                          <td><p>{{$report->last_checklist != null ? $report->last_checklist : 'Unchecked'}}</p></td>
                          <td><p>{{$report->delivery_time}}</p></td>
                          <td><p>{{$report->receipt_at}}</p></td>                                          
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