<style>
  .tab-pane{
      display: none;
  }
  .tab-pane.active{
      display: block;
  }

  .progressbar:not(.hoang) {
      margin: 0;
      padding: 0;
      counter-reset: step;
  }
  .progressbar li span{
    font-size: 11px;
  }    
  .progressbar li:not(.hoang) {
      list-style-type: none;
      width: 22%;
      float: left;
      font-size: 12px;
      position: relative;
      text-align: center;
      /*text-transform: uppercase;*/
      color: #7d7d7d;
      z-index: 0;
  }
  .progressbar li:not(.hoang):before {
      width: 10px;
      height: 10px;
      content: ' ';
      counter-increment: step;
      line-height: 51px;
      border: 5px solid #7d7d7d;
      display: block;
      text-align: center;
      margin: 0 auto 10px auto;
      border-radius: 50%;
      background-color: white;
  }
  .progressbar li:not(.hoang):after {
      width: 100%!important;
      height: 2px!important;
      content: ''!important;
      position: absolute!important;
      background-color: #7d7d7d!important;
      top: 4px!important;
      left: -50%!important;
      z-index: -1!important;
  }
  .progressbar li:first-child:after {
      content: none;
      display: none;
  }
  .progressbar li.active:not(.hoang) {
      color: green;
  }
  .progressbar li.active:not(.hoang):before {
      border-color: #55b776;
  }
  .progressbar li.cancel:before {
      border-color: red;
  }   
  .progressbar li.active + li:after {
      background-color: #55b776!important;
  }
  .font11
  {
      font-size: 11px;
  }
  .progressbar_img{
      text-align: center!important;
      display: flex;
      flex-direction: row;
      justify-content: center;
  }
  .progressbar_img img{
    height: 35px;
    width: 35px;
  }
  ul.progressbar_img li.active_img img{
    border: 2px solid #00ff50;
  }
  ul.progressbar_img li.cancel img{
    border: 2px solid red;
  }
  ul.progressbar_img li.cancel_all img{
    border: 2px solid blue;
  }
  ul.progressbar_img li {
      width: 22%;
      float: left;
  }
</style>
<div class="modal fade in" id="view_export_supplies" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('detail_export_supplies'); ?> </span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="lead-view" id="leadViewWrapper">
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= _dt($dataMain->date) ?></span>
              </div>
              <div class="wap-content second">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Mã Bảo hành') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $dataSub->code ?></span>
              </div>
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Mã phiếu xuất') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $dataMain->code ?></span>
              </div>
              <div class="wap-content second">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_export_name') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $dataMain->name ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="lead-view" id="leadViewWrapper">
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('ch_catestaff_create') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= get_staff_full_name($dataMain->staff_create); ?></span>
              </div>
              <div class="wap-content second">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date_created') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= _dt($dataMain->date_create); ?></span>
              </div>
              <div class="wap-content firt">
                  <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                  <span class="bold font-medium-xs lead-name"><?= $dataMain->note ?></span>
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
          <hr>
          <div class="col-md-12">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#detail" aria-controls="detail" role="tab" data-toggle="tab"><?=_l('ch_information')?></a>
                </li>
                <li role="presentation">
                    <a href="#info" aria-controls="info" role="tab" data-toggle="tab"><?=_l('status_purchases_by_warranty')?></a>
                </li>
            </ul>
            <div role="tabpanel" class="tab-pane active" id="detail">
              <div class="table-responsive">
                <table id="table-export-supplies" class="dt-table table table-bordered table-hover dont-responsive-table">
                  <thead>
                      <tr>
                          <th style="width: 5%;" class="text-center">
                              STT
                          </th>
                          <th style="width: 5%;" class="text-center"><?=_l('image')?></th>
                          <th style="width: 15%;" class="text-center"><?=_l('code_supplies')?></th>
                          <th style="width: 15%;" class="text-center"><?=_l('name_supplies')?></th>
                          <th style="width: 10%;" class="text-center"><?=_l('unit')?></th>
                          <th style="width: 15%;" class="text-center"><?=_l('quantity_export_supplies')?></th>
                          <th style="width: 15%;" class="text-center"><?=_l('tnh_quantity_warehouses')?></th>
                          <th style="width: 20%;" class="text-center"><?=_l('warning')?></th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($supplies as $key => $value) { ?>
                        <?php
                            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
                            if($value['type_item'] == 'materials') {
                                $getDetail = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                                $name = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_item_materials').'</span>';
                                $unit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                                if($getDetail && !empty($getDetail->images)) {
                                    $img = '<img width="50" src="'.base_url('uploads/materials/'.$getDetail->images).'">';
                                }

                                $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouse');
                                $this->db->where('tblwarehouse_items.id_items', $value['id_item']);
                                $this->db->where('tblwarehouse_items.type_items', 'nvl');
                                $quantity_warehouse = $this->db->get('tblwarehouse_items')->row();
                            }
                            else if($value['type_item'] == 'supplies') {
                                $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                                $name = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_tools_supplies').'</span>';
                                $unit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                                if($getDetail && !empty($getDetail->images)) {
                                    $img = '<img width="50" src="'.base_url('uploads/tools_supplies/'.$getDetail->images).'">';
                                }

                                $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouse');
                                $this->db->where('tblwarehouse_items.id_items', $value['id_item']);
                                $this->db->where('tblwarehouse_items.type_items', 'tools');
                                $quantity_warehouse = $this->db->get('tblwarehouse_items')->row();
                            }
                            //không tính sl đã xuất
                            $quantity_rest = $value['quantity'] - $value['export_warehouse'];
                            //end
                        ?>
                        <tr>
                            <td class="text-center"><?=++$key?></td>
                            <td class="text-center"><?=$img?></td>
                            <td class="text-left">
                                <?=$getDetail->code?>
                                <?php if($value['additional_supplies'] == 1) { ?>
                                  <br><span class="label label-primary">Vật tư bổ sung</span>
                                <?php } ?>
                            </td>
                            <td class="text-left">
                                <?=$name?>
                            </td>
                            <td class="text-center">
                                <?=$unit->unit?>
                            </td>
                            <td class="text-center">
                                <?=$value['quantity']?>
                                <div class="text-danger">* Đã xuất: <?= $value['export_warehouse'] ?></div>
                            </td>
                            <td class="text-right">
                                <?=$quantity_warehouse->quantity_warehouse?>
                            </td>
                            <td class="text-left text-danger bold">
                                <?= ($quantity_warehouse->quantity_warehouse < $quantity_rest) ? 'SL hiện tại không đủ' : '' ?>
                            </td>
                        </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="info">
              <?php if(!empty($dataMain->id_purchases)) { ?>
                <div class="table-responsive">
                  <table id="table-status-purchases" class="dt-table table table-bordered table-hover dont-responsive-table">
                    <thead>
                      <tr>
                        <th style="width: 5%;" class="text-center">
                            STT
                        </th>
                        <th style="width: 95%;" class="text-center"><?=_l('tnh_procedure')?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $arrTostr = explode(",", $dataMain->id_purchases); ?>
                      <?php foreach ($arrTostr as $key => $value) { ?>
                        <?php $dataPurchases = get_table_where('tblpurchases',array('id'=>$value),'','row'); ?>
                        <?php $dataRfq = get_table_where('tblrfq_ask_price',array('id_purchases'=>$dataPurchases->id),'','row'); ?>
                        <?php if($dataPurchases) { ?>
                          <tr>
                            <td class="text-center">
                              <?= ++$key ?>
                            </td>
                            <td class="text-center">
                              <?php echo process_purchases_by_warranty($dataPurchases->id) ?>
                            </td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                  </table>
                  
                </div>
              <?php } else { ?>
                <div class="panel panel-danger">
                  <div class="panel-body text-center">
                    <?= _l('not_purchases') ?>
                  </div>
                </div>
              <?php } ?>
            </div>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $( document ).ready(function() {
    var opt = {
        format: 'd/m/Y H:i:s',
        timepicker: true,
        scrollInput: false,
        lazyInit: true,
        dayOfWeekStart: 0,
    };
    $('#date_export_supplies').datetimepicker(opt);

    $('#table-export-supplies').DataTable({
      "bLengthChange" : true,
      "language": app.lang.datatables,
      "searching": false,
      "pageLength": app.options.tables_pagination_limit,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "initComplete": function(settings, json) {
          var t = this;
          t.parents('.table-loading').removeClass('table-loading');
          t.removeClass('dt-table-loading');
      },
      "footerCallback": function ( row, data, start, end, display ) {
      }
    });

    $('#table-status-purchases').DataTable({
      "bLengthChange" : true,
      "language": app.lang.datatables,
      "searching": false,
      "pageLength": app.options.tables_pagination_limit,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
      "initComplete": function(settings, json) {
          var t = this;
          t.parents('.table-loading').removeClass('table-loading');
          t.removeClass('dt-table-loading');
      },
      "footerCallback": function ( row, data, start, end, display ) {
      }
    });
  });

  appValidateForm($('#export-supplies-form'), {date_export_supplies: 'required', name_export_supplies: 'required'}, manage_export_supplies);
  function manage_export_supplies(form) {
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
          response = JSON.parse(response);
          if (response.success == true) {
              alert_float(response.alert_type, response.message);
              $('.table-warranty').DataTable().ajax.reload();
              $('#add_export_supplies').modal('hide');
          }
          else if (response.success == false) {
              alert_float(response.alert_type, response.message);
          }
      });
      return false;
  }
</script>