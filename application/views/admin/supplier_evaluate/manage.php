<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .tab-pane{
    display: none;
  }
  .tab-pane.active{
    display: block;
  }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <ul class="nav nav-tabs" role="tablist">
                     <li role="presentation" class="active">
                        <a href="#tab_evaluate" aria-controls="tab_evaluate" role="tab" data-toggle="tab">
                           <?php echo _l('evaluate_order'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a class="reload_table" href="#tab_result" aria-controls="tab_result" role="tab" data-toggle="tab">
                           <?php echo _l('evaluate_result'); ?>
                        </a>
                     </li>
                  </ul>
                  <div role="tabpanel" class="tab-pane active" id="tab_evaluate">
                     <?php render_datatable(array(
                        _l('#'),
                        _l('ch_code_p'),
                        _l('ch_date_p'),
                        _l('tnh_supplies'),
                        _l('evaluate'),
                        _l('staff_evaluate'),
                        _l('date_evaluate'),
                        _l('note')
                     ),'evaluate'); ?>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="tab_result">
                     <?php render_datatable(array(
                        _l('#'),
                        _l('evaluate_cycle'),
                        _l('supplier'),
                        _l('% đánh giá'),
                        _l('evaluate_time'),
                        _l('evaluate_result')
                     ),'evaluate_result'); ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
  var tAPI = initDataTableCustom('.table-evaluate', admin_url+'supplier_evaluate/table_evaluate', [0], [0], [],<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
  var tAPI_result = initDataTableCustom('.table-evaluate_result', admin_url+'supplier_evaluate/table_evaluate_result', [0], [0], [],<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
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

  function view_purchase_order(id) {
    $('#purchase_order_data').html('');
    $.get(admin_url + 'purchase_order/view_purchase_order/' + id).done(function (response) {
        $('#purchase_order_data').html(response);
        changeRowNew('tblpurchase_order', id);
        $('#view_purchase_order').modal('show');
    }).fail(function (error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
  }

  function int_suppliers_view(id = null, edit = false) 
  {
    $('#suppliers_view_data').html('');
    $.get(admin_url + 'suppliers/int_suppliers_view/' + edit + '/' + id+'/1').done(function(response) {
    $('#suppliers_view_data').html(response);
    $('#suppliers_add').modal({show:true,backdrop:'static'});
    init_selectpicker();
    init_datepicker();
    add_html_evaluate(id);
    }).fail(function(error) {
    var response = JSON.parse(error.responseText);
    alert_float('danger', response.message);
    });    
  }

  var dem_evaluate_result = 0;
  $(document).on('click','.reload_table', function (e) {
    if(dem_evaluate_result == 0) {
      tAPI_result.draw('page');
      dem_evaluate_result++;
    }
  });
</script>