<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .wap-btn:hover {
    cursor: pointer;
    background: #ffffff42;
    border-radius: 50%;
  }
  .wap-btn {
    margin-left: 10px;
    background: #fff0;
    border: 0;
    outline: 0;
  }
  td.sorting_1.one-control {
      background: url(<?=base_url('assets/images/details_open.png')?>) no-repeat center center;
      cursor: pointer;
  }

  tr.shown td.sorting_1.one-control {
      background: url(<?=base_url('assets/images/details_close.png')?>) no-repeat center center;
  }
  td.two-control {
      background: url(<?=base_url('assets/images/details_open.png')?>) no-repeat center left;
      cursor: pointer;
  }

  tr.shown td.two-control.two-control {
      background: url(<?=base_url('assets/images/details_close.png')?>) no-repeat center left;
  }
  .treegrid-indent {
      width: 16px;
      height: 16px;
      display: inline-block;
      position: relative;
  }
  .treegrid-expander {
      width: 16px;
      height: 16px;
      display: inline-block;
      position: relative;
      cursor: pointer;
  }
  .buttons-collection.btn-default-dt-options{
        display: none;
    }
    .not-control{
        background: none!important;
    }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if (has_permission('suppliers','','create')) { ?>
              <div class="pull-right mright5 H_border">
                <a class="btn btn-info test H_action_button">
                  <?php echo _l('Export excel'); ?></a>
              </div>
              <div class="pull-right mright5 H_border">
                <a href="#" id="add_new" class="btn btn-info H_action_button" data-toggle="modal" data-target="#evaluation_criteria">
                  <?php echo _l('create_add_new'); ?></a>
              </div>
            <?php } ?>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <input type="text" name="view" value="," class="hide">
                <input type="text" name="view_children" value="," class="hide">

                    <?php render_datatable(array(
                    _l('ID'),  
                    _l('Tên'),
                    _l('Màu'),
                    _l('Loại'),
                    _l('Bắt buộc'),
                    _l('ch_option'),
                    ),
                    'info_suppliers'); ?>
               </div>
            </div>
         </div>
      </div>
</div>
<div class="modal fade" id="evaluation_criteria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('ch_evaluation_criteria'); ?>
        </h4>
      </div>
      <?php echo form_open_multipart(admin_url('suppliers/add_info_suppliers'), array('class'=>'info_suppliers-from','autocomplete'=>'off')); ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <input type="text" name="id" class="hide" id="id_info_suppliers">
                    <?php echo render_input('name','name'); ?>
                    <label class="bold mbot10 inline-block"><?=_l('kb_group_color')?></label>
                    <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                        <input type="text" value="" name="color" id="color" class="form-control colorpicker">
                        <span class="input-group-addon">
                            <i class="i_color"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-info" ><?php echo _l('submit'); ?></button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<div class="modal fade" id="evaluation_criteria_children" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('Thêm thông tin con'); ?>
        </h4>
      </div>
      <?php echo form_open_multipart(admin_url('suppliers/add_info_suppliers_datail'), array('class'=>'evaluation_criteria_children-form','autocomplete'=>'off')); ?>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <input type="text" class="hide" name="id_info_suppliers" id="id_info_supplierss">
              <input type="text" class="hide" name="id_info_suppliers_datail" id="id_info_suppliers_datail">
              <?php echo render_select('type_form', $type_form, array('name', 'name'), 'cong_type_form'); ?>
              <?php echo render_input('name_detail','name'); ?>
              <div class="checkbox">
                  <input type="checkbox" id="is_required" name="is_required" value="1">
                  <label for="is_required"><?=_l('cong_is_required')?></label>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-info" ><?php echo _l('submit'); ?></button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<div class="modal fade" id="suppliers_info_detail_value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('Thêm thông tin con'); ?>
        </h4>
      </div>
      <?php echo form_open_multipart(admin_url('suppliers/add_suppliers_info_detail_value'), array('class'=>'suppliers_info_detail_value-form','autocomplete'=>'off')); ?>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <input type="text" class="hide" name="id_suppliers_info_detail_value" id="id_suppliers_info_detail_value">
              <input type="text" class="hide" name="id_info_suppliers_datails" id="id_info_suppliers_datails">
              <?php echo render_input('name_detail_value','name'); ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-info" ><?php echo _l('submit'); ?></button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script>
  $('#add_new').click(function(e) {
    $('#name').val('');
    $('#id_info_suppliers').val('');
    $('#color').colorpicker();
            $('#color').val('');
            $(".i_color").attr("style", "");
    });
  appValidateForm($('.evaluation_criteria_children-form'), {
        name_children: 'required'
  }, manage_evaluation_criteria_children);
    function manage_evaluation_criteria_children(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
             if (response.alert_type == "success") {
                if($.fn.DataTable.isDataTable('.table-info_suppliers')){
                    $('.table-info_suppliers').DataTable().ajax.reload();
                }
                alert_float('success', response.message);
            }
            $('#evaluation_criteria_children').modal('hide');
        });
        return false;
    }
$('#evaluation_criteria_children').on('hidden.bs.modal', function () {
          $('#id_evaluation').val('');
            $('#id_evaluation_children').val('');
            $('#name_children').val('');
});
$('#evaluation_criteria').on('hidden.bs.modal', function () {
          $('#id').val('');
            $('#name').val('');
});
function add_evaluation_criteria_children(id) {

  $('#evaluation_criteria_children').modal('show');
  $('#id_info_supplierss').val(id);
            $('#id_info_suppliers_datail').val('');
            $('#name_detail').val('');
            $('#type_form').selectpicker('val','');
            $('#is_required').prop('checked', false);
}
function edit_evaluation_criteria_children(id)
    {
        jQuery.ajax({
            type: "post",
            url:admin_url+"suppliers/get_info_suppliers_datail/"+id,
            data: {[csrfData['token_name']] : csrfData['hash']},
            cache: false,
            success: function (data) {
            var json = JSON.parse(data);
            {
            $('#id_info_supplierss').val(json.id_suppliers_info);
            $('#id_info_suppliers_datail').val(id);
            $('#name_detail').val(json.name);
            $('#type_form').selectpicker('val',json.type_form);
            if(json.is_required == 1)
            {
                $('#is_required').prop('checked', true);
            }
            else
            {
                $('#is_required').prop('checked', false);
            }
            $('#evaluation_criteria_children').modal('show');
          }
        }
        });
    }
function edit_suppliers_info_detail_value(id)
    {
        jQuery.ajax({
            type: "post",
            url:admin_url+"suppliers/get_suppliers_info_detail_value/"+id,
            data: {[csrfData['token_name']] : csrfData['hash']},
            cache: false,
            success: function (data) {
            var json = JSON.parse(data);
            {
            $('#id_info_suppliers_datails').val(json.id_info_detail);
            $('#id_suppliers_info_detail_value').val(id);
            $('#name_detail_value').val(json.name);
            $('#suppliers_info_detail_value').modal('show');
          }
        }
        });
    }    
function add_suppliers_info_detail_value(id) {
    $('#id_info_suppliers_datails').val(id);
    $('#name_detail_value').val('');
    $('#id_suppliers_info_detail_value').val('');
    $('#suppliers_info_detail_value').modal('show');
}
  appValidateForm($('.suppliers_info_detail_value-form'), {
        name_detail_value: 'required'
  }, suppliers_info_detail_value);
    function suppliers_info_detail_value(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
             if (response.alert_type == "success") {
                if($.fn.DataTable.isDataTable('.table-info_suppliers')){
                    $('.table-info_suppliers').DataTable().ajax.reload();
                    $('#type_form').selectpicker('val','');
                }
                alert_float('success', response.message);
            }
            $('#suppliers_info_detail_value').modal('hide');
        });
        return false;
    }
function edit_evaluation_criteria(id)
    {
        jQuery.ajax({
            type: "post",
            url:admin_url+"suppliers/get_info_suppliers/"+id,
            data: {[csrfData['token_name']] : csrfData['hash']},
            cache: false,
            success: function (data) {
            var json = JSON.parse(data);
            {
            $('#id_info_suppliers').val(id);
            $('#name').val(json.name);
            $('#color').val(json.color);
            $(".i_color").attr("style", "background: " + json.color);
            $('#evaluation_criteria').modal('show');
            $('#color').colorpicker();
          }
        }
        });
    }    
  appValidateForm($('.info_suppliers-from'), {
        name: 'required'
  }, manage_evaluation_criteria);
    function manage_evaluation_criteria(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
             if (response.alert_type == "success") {
                if($.fn.DataTable.isDataTable('.table-info_suppliers')){
                    $('.table-info_suppliers').DataTable().ajax.reload();
                }
                alert_float('success', response.message);
            }
            $('#evaluation_criteria').modal('hide');
        });
        return false;
    }
  $(document).on('click', '.delete-remind', function() {
      var r = confirm("<?php echo _l('confirm_action_prompt');?>");
      if (r == false) {
        return false;
      } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            $('.table-info_suppliers').DataTable().ajax.reload();
          }, 'json');
      }
      return false;
    });
  $(function(){
  var fnServerParams = {
   "view": '[name="view"]',
   "view_children": '[name="view_children"]',
  };
  var notSortableAndSearchableItemColumns = [];
    initDataTable('.table-info_suppliers','<?=admin_url('suppliers/table_info_suppliers/')?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,fnServerParams,'');
  });
  function view(id) {
    var view = $('[name="view"]').val();
    view = view+id+',';
    $('[name="view"]').val(view);
    $('.table-info_suppliers').DataTable().ajax.reload();
  }
  function no_view(id) {
    var view = $('[name="view"]').val();
    view = view.replace(','+id+',',',');
    $('[name="view"]').val(view);
    $('.table-info_suppliers').DataTable().ajax.reload();
  }
  function view_children(id) {
    var view = $('[name="view_children"]').val();
    view = view+id+',';
    $('[name="view_children"]').val(view);
    $('.table-info_suppliers').DataTable().ajax.reload();
  }
  function no_view_children(id) {
    var view = $('[name="view_children"]').val();
    view = view.replace(','+id+',',',');
    $('[name="view_children"]').val(view);
    $('.table-info_suppliers').DataTable().ajax.reload();
  }  
</script>
</body>
</html>
