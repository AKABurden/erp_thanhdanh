<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .wap-compare-container {
    position: relative;
  }
  .wap-compare {
    cursor: pointer;
    position: absolute;
    width: 35px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: bold;
    background: #e0e0e0;
    border: 1px solid #bfbfbf;
  }
  .wap-compare-container .wap-compare:nth-child(2) {
    border-left: 0;
    border-right: 0;
    left: 35px;
  }
  .wap-compare-container .wap-compare:nth-child(3) {
    left: 70px;
  }
  .wap-compare.active {
    background: #3e99d7;
    color: #fff;
  }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
              <a class="btn btn-info H_action_button" onclick="add(); return false;">
                  <?php echo _l('create_add_new'); ?>
              </a>
            </div>
         </div>
      </div>
   </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <?php render_datatable(array(
                      _l('#'),
                      _l('Mã phân loại NCC'),
                      _l('name_supplier_classify'),
                      _l('percent_classify'),
                      _l('result_warning'),
                      _l('options')
                  ),'supplier_classify'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="supplier_classify_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('add_supplier_classify'); ?></span>
                    <span class="edit-title"><?php echo _l('edit_supplier_classify'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/supplier_classify/add',array('id'=>'form_supplier_classify')); ?>
            <div class="modal-body">
              <div class="panel_s">
                <div class="panel-body">
                  <?php echo render_input('code_supplier_classify','Mã phân loại NCC'); ?>
                  <?php echo render_input('name','name_supplier_classify'); ?>
                  <div class="form-group" app-field-wrapper="percent">
                    <label for="percent" class="control-label"><?=_l('percent_classify')?> (%/Tổng)</label>
                    <div class="wap-compare-container">
                      <div class="wap-compare" data-content=">">
                        <span>></span>
                      </div>
                      <div class="wap-compare" data-content="<">
                        <span><</span>
                      </div>
                      <div class="wap-compare" data-content="=">
                        <span>=</span>
                      </div>
                      <input type="hidden" name="compare" class="compare">
                      <input type="number" id="percent" name="percent" class="form-control" value="" style="padding-left: 110px;">
                    </div>
                  </div>
                  <?php echo render_textarea('result_warning','result_warning'); ?>
                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
  var tAPI = initDataTableCustom('.table-supplier_classify', admin_url+'supplier_classify/table_supplier_classify', [0], [0], [],<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
  $(function(){
    var CustomersServerParams = {
          'filterStatus' : '[name="filterStatus"]',
       };
    // $.each(CustomersServerParams, function(filterIndex, filterItem){
    //    $('' + filterItem).on('change', function(){
    //        tAPI.draw('page');
    //    });
    // });
  });

  $(document).on('click','.wap-compare', function (e) {
    var target = $(e.currentTarget);
    $('.wap-compare').removeClass('active');
    target.addClass('active');
    var compare = target.attr('data-content');
    $('.compare').val(compare);
  });

  function add() {
      $('.add-title').removeClass('hide');
      $('.edit-title').addClass('hide');

      $('#form_supplier_classify').attr("action","<?=admin_url('supplier_classify/add')?>");
      $('.wap-compare').removeClass('active');
      $('#code_supplier_classify').val('');
      $('#name').val('');
      $('.compare').val('');
      $('#percent').val(0);
      $('#result_warning').val('');
      $('#supplier_classify_modal').modal({backdrop: 'static', keyboard: false});
  }

  function edit(id) {
      $('.add-title').addClass('hide');
      $('.edit-title').removeClass('hide');
      $('#form_supplier_classify').attr("action","<?=admin_url('supplier_classify/edit/')?>"+id);

      var data = {};
      if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
      }
      $.post(admin_url+'supplier_classify/getData/'+id, data).done(function(response){
        response = JSON.parse(response);
        $('.wap-compare').removeClass('active');
        $('div[data-content="'+response.compare+'"]').addClass('active');
        $('#code_supplier_classify').val(response.code_supplier_classify);
        $('#name').val(response.name);
        $('.compare').val(response.compare);
        $('#percent').val(response.percent);
        $('#result_warning').val(response.result_warning);
        $('#supplier_classify_modal').modal({backdrop: 'static', keyboard: false});
      });
  }

  function delete_supplier_classify(id) {
      var data = {};
      if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
      }
      $.post(admin_url+'supplier_classify/delete_supplier_classify/'+id, data).done(function(response){
        response = JSON.parse(response);
        alert_float(response.alert_type, response.message);
        if(response.success) {
          tAPI.draw('page');
        }
      });
  }

  //load trang voi action
  _validate_form($('#form_supplier_classify'),{code_supplier_classify:'required', name:'required'},add_supplier_classify_s);
  function add_supplier_classify_s(form) {
    if(!$('.compare').val() || $('.compare').val() == "") {
      alert_float('danger','Vui lòng nhập dữ liệu cho điều kiện!');
      return  false;
    }
    var data = $(form).serialize(),
        action = form.action;
    return $.post(action, data).done(function(form) {
        form = JSON.parse(form),
        alert_float(form.alert_type, form.message);
        if(form.success) {
            tAPI.draw('page');
            $('#supplier_classify_modal').modal('hide');
        }
    }), !1
  }
  //end
</script>