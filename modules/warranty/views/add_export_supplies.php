<style>
</style>
<div class="modal fade in" id="add_export_supplies" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <?php echo form_open('admin/warranty/add_export_supplies_form/'.$id, array('id'=>'export-supplies-form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="book-title"><?php echo _l('add_export_supplies'); ?> </span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-primary">
              <div class="panel-heading"><?=_l('lead_general_info')?></div>
              <div class="panel-body">
                <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                  <tbody>
                    <tr>
                      <td>
                        <label for="date_export_supplies" class="control-label">
                          <small class="req text-danger">* </small>
                          <?php echo _l('date'); ?>
                        </label>
                      </td>
                      <td>
                        <?php echo render_datetime_input('date_export_supplies','', $now); ?>
                      </td>
                      <td>
                        <label for="name_export_supplies" class="control-label">
                          <small class="req text-danger">* </small>
                          <?php echo _l('tnh_export_name'); ?>
                        </label>
                      </td>
                      <td>
                        <?php echo render_input('name_export_supplies',''); ?>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <label for="date_deadline" class="control-label">
                          <span><?= _l('date_deadline') ?></span>
                          <br><span class="bold">(Trống: không giới hạn)</span>
                        </label>
                      </td>
                      <td>
                        <?php echo render_date_input('date_deadline',''); ?>
                      </td>
                      <td>
                        <label for="note_export_supplies" class="control-label">
                          <?php echo _l('note'); ?>
                        </label>
                      </td>
                      <td>
                        <?php echo render_textarea('note_export_supplies',''); ?>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <hr>
          <div class="col-md-12">
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
                      ?>
                      <tr>
                          <td class="text-center"><?=++$key?></td>
                          <td class="text-center"><?=$img?></td>
                          <td class="text-left">
                              <?=$getDetail->code?>
                          </td>
                          <td class="text-left">
                              <?=$name?>
                          </td>
                          <td class="text-center">
                              <?=$unit->unit?>
                          </td>
                          <td class="text-center">
                              <?=$value['quantity']?>
                          </td>
                          <td class="text-right">
                              <?=$quantity_warehouse->quantity_warehouse?>
                          </td>
                          <td class="text-left text-danger bold">
                              <?= ($quantity_warehouse->quantity_warehouse < $value['quantity'] || $quantity_warehouse->quantity_warehouse == 0) ? 'SL hiện tại không đủ' : '' ?>
                          </td>
                      </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
    </div>
    <?php echo form_close(); ?>
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

    var op = {
        format: 'd/m/Y',
        timepicker: false,
        scrollInput: false,
        lazyInit: true,
        dayOfWeekStart: 0,
    };
    $('#date_deadline').datetimepicker(op);

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
  });

  appValidateForm($('#export-supplies-form'), {date_export_supplies: 'required', name_export_supplies: 'required'}, manage_export_supplies);
  function manage_export_supplies(form) {
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
          response = JSON.parse(response);
          if (response.success == true) {
              alert_float(response.alert_type, response.message);
              tAPI.draw('page');
              $('#add_export_supplies').modal('hide');
          }
          else if (response.success == false) {
              alert_float(response.alert_type, response.message);
          }
      });
      return false;
  }
</script>