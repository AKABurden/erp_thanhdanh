<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .table-return_suppliers img {
    height: 20px;
    width: 20px;
}
    .table-return_suppliers tr td:nth-child(2){
        text-align: center;
        min-width: 100px;
    }    
    .table-return_suppliers tr td:nth-child(4){
        text-align: center;
        min-width: 90px;
    }
    .table-return_suppliers tr td:nth-child(6){
        text-align: center;
        min-width: 125px;
    }
    .table-return_suppliers tbody tr td:nth-child(9) {
      white-space: inherit;
      min-width: 160px;
    }
    .table-return_suppliers tbody .dropdown {
      text-align: center;
    }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <!-- <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <li>   
                        <a href="<?=admin_url('option_pdf')?>" class="" target="_blank"><i class="fa fa-file-pdf-o"></i>  
                         <?php echo _l('option_pdf'); ?></a>
                    </li>
                    <li>    
                        <a class="btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><i class="fa fa-filter"></i>  <?= lang('ch_seach_statistical') ?></a>
                    </li>
                </ul>
            </div> -->
            <?php if (has_permission('return_suppliers','','create')) { ?>
            <div class="pull-right mright5 H_border">
              <a href="<?=admin_url('return_suppliers/detail')?>"  class="btn btn-info test H_action_button">
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
            <div id="search-tnh"  aria-expanded="true" style="">
               <div class="col-md-3">
                    <?php echo render_select('search_code',array(),array('id','company'),'ch_code_p');?>
                </div>
                <div class="col-md-3">
                    <?php echo render_select('search_staff[]',$dataStaff,array('staffid','name'),'Người tạo','',array('data-actions-box'=>1,'multiple'=>true),array(),'','',false);?>
                </div>
                <div class="col-md-3">
                    <?php echo render_select('search_id_suppliers[]',$dataSupplier,array('id','company','code'),'ch_name_suppliers','',array('data-actions-box'=>1,'multiple'=>true),array(),'','',false);?>
                </div>
                <div class="col-md-3">
                     <div class="form-group">
                        <label for="search_date" class="control-label"><?=_l('ch_date_p')?></label>
                        <div class="input-group">
                            <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                <div class="clearfix"></div>
                  <div class="horizontal-scrollable-tabs">
                      <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                      <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                      <div class="horizontal-tabs">
                          <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                            <li class="active">
                                <a class="H_filter" data-id="all">
                                  <?=_l('leads_all')?> (<span class="all">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="1">
                                  <?=_l('ch_confirm_22')?> (<span class="ch_confirm_22">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="2">
                                  <?=_l('dont_approve')?> (<span class="dont_approve">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="3">
                                  <?=_l('ch_warehouse_d')?> (<span class="ch_warehouse_d">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="4">
                                  <?=_l('ch_warehouse_nd')?> (<span class="ch_warehouse_nd">0</span>)
                                </a>
                            </li>
                          </ul>
                      </div>
                  </div>
                  <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                  <input type="hidden" id="suppliers_id" name="suppliers_id" value=""/>
                  <div class="clearfix"></div>
                  <?php $table_data = array(
                      _l('ch_date_p'),
                      _l('ch_code_p'),
                      _l('supplier'),
                      _l('total_price'),
                      _l('ch_addedfrom'),
                      _l('ch_treatment_methods'),
                      _l('invoice_dt_table_heading_status'),
                      _l('ch_warehoues_app'),
                    );
                    array_push($table_data,_l('ch_option'));
                    render_datatable($table_data,'return_suppliers');
                  ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/popup_purchase_order/manage')?>
<div id="return_suppliers_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
function view_return_suppliers(id=null,edit = false) 
{
    $('#return_suppliers_data').html('');
    $.get(admin_url + 'return_suppliers/int_return_suppliers_view/'+ id).done(function(response) {
    $('#return_suppliers_data').html(response);
    $('#return_suppliers').modal({show:true,backdrop:'static'});
    init_selectpicker();
    init_datepicker();
    changeRowNew('tblreturn_suppliers', id);
    }).fail(function(error) {
    var response = JSON.parse(error.responseText);
    alert_float('danger', response.message);
    });    
}
$('body').on('hidden.bs.modal', '#return_suppliers', function() {
    $('#return_suppliers_data').html('');
    tAPI.draw('page');
});
$('.H_filter').click(function(e) {
  var target = $(e.currentTarget);
  var value = target.attr('data-id');
  target.parent().parent().find('li').removeClass('active');
  target.parent().addClass('active');
  $('input[name="filterStatus"]').val(value);
  $('input[name="filterStatus"]').change();
});
var tAPI;
$(function(){
    var CustomersServerParams = {
      'filterStatus' : '[name="filterStatus"]',
      'suppliers_id' : '[name="suppliers_id"]',
      'search_code' : '[name="search_code"]',
      'search_staff' : '[name="search_staff[]"]',
      'search_id_suppliers' : '[name="search_id_suppliers[]"]',
      'search_date' : '[name="search_date"]',
    };
    tAPI = initDataTableCustom('.table-return_suppliers', admin_url+'return_suppliers/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>, fixedColumns = {leftColumns: 3, rightColumns: 1});
    // var tAPI = initDataTable('.table-return_suppliers', admin_url+'return_suppliers/table', [0], [0], CustomersServerParams,[1, 'desc']);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
      $('' + filterItem).on('change', function(){
        tAPI.draw('page');
      });
    });

    $('.table-return_suppliers').on('draw.dt', function() {
      checkNoDrop();
      get_total_limit();
    });
});
$('body').on('hidden.bs.modal', '#views_return_suppliers', function() {
    $('#return_suppliers_data').html('');
    tAPI.draw('page');
});
$(document).on('click', '.delete-reminds', function() {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            tAPI.draw('page');
        }, 'json');
    }
    return false;
});
$(document).on('click', '.change_type', function() {
  setTimeout(function(){
    tAPI.draw('page');
    }, 500);
});
function confirm_warehous(id,warehouseman_id) {
    {
        dataString={id:id,warehouseman_id:warehouseman_id,[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({   
            type: "post",
            url:"<?=admin_url()?>return_suppliers/confirm_warehous",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                alert_float(response.alert_type, response.message);
            }
        });
        return false;
    }
}
function var_status(status,id) {
    {
        dataString={id:id,status:status,[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({   
            type: "post",
            url:"<?=admin_url()?>return_suppliers/update_status",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    tAPI.draw('page');
                    alert_float('success', response.message);
                }
            }
        });
        return false;
    }
}
    function init_ajax_searchs(e, t, a, i) {
       var n = $("body").find(t);
       var h = t;
       if (n.length) {
          var s = {
             ajax: {
                 url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                 data: function() {
                     var t = {[csrfData.token_name] : csrfData.hash};
                     return t.type = e, t.rel_id = "", t.q = "{{{q}}}", void 0 !== a && jQuery.extend(t, a), t
                 }
             },
             locale: {
                 emptyTitle: app.lang.search_ajax_empty,
                 statusInitialized: app.lang.search_ajax_initialized,
                 statusSearching: app.lang.search_ajax_searching,
                 statusNoResults: app.lang.not_results_found,
                 searchPlaceholder: app.lang.search_ajax_placeholder,
                 currentlySelected: app.lang.currently_selected
             },
             requestDelay: 500,
             cache: !1,
             preprocessData: function(e) {
                 var t = [];
                 var _temp_all = {
                    'value': 'all',
                    'text': 'Tất cả',
                 };
                 t.push(_temp_all);
                 for (var a = e.length, i = 0; i < a; i++) {
                     var n = {
                         value: e[i].id,
                         text: e[i].name
                     }; t.push(n)
                 }
                 return t;
             },
             preserveSelectedPosition: "after",
             preserveSelected: !0
          };
          n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s);
       }
    }

    var search_daterangepicker = () => {
       $('input[name="search_date"]').daterangepicker({
          opens: 'left',
          autoUpdateInput: false, 
          isInvalidDate: false,
          "locale": {
             "format": "DD/MM/YYYY",
             "separator": " - ",
             "applyLabel": lang_daterangepicker.applyLabel,
             "cancelLabel": lang_daterangepicker.cancelLabel,
             "fromLabel": lang_daterangepicker.fromLabel,
             "toLabel": lang_daterangepicker.toLabel,
             "customRangeLabel": lang_daterangepicker.customRangeLabel,
             "daysOfWeek": lang_daterangepicker.daysOfWeek,
             "monthNames": lang_daterangepicker.monthNames
          },
       }, function(start, end, label) {
       });
       $('input[name="search_date"]').val('').datepicker("refresh");
       $('input[name="search_date"]').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
          $( "#search_date" ).trigger( "change" );
       });
       $('input[name="search_date"]').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
          $( "#search_date" ).trigger( "change" );
       });
    };

    // $(document).on('change','#search_code',function(e){
    //    tAPI.draw('page');
    // });
    // $(document).on('change','select[name="search_staff[]"]',function(e){
    //    tAPI.draw('page');
    // });
    // $(document).on('change','select[name="search_id_suppliers[]"]',function(e){
    //    tAPI.draw('page');
    // });
    // $(document).on('change','#search_date',function(e){
    //    tAPI.draw('page');
    // });
    //end
     function get_total_limit() {
          dataString = {[csrfData['token_name']] : csrfData['hash']};
            jQuery.ajax({
                type: "post",
                url: "<?=admin_url()?>return_suppliers/count_all/",
                data: dataString,
                cache: false,
                success: function (data) {
                  data = JSON.parse(data);
                  $('.all').html(data.all);
                  $('.ch_confirm_22').html(data.ch_confirm_22);
                  $('.dont_approve').html(data.dont_approve);
                  $('.ch_warehouse_d').html(data.ch_warehouse_d);
                  $('.ch_warehouse_nd').html(data.ch_warehouse_nd);


                  }
            });
      }
init_ajax_searchs('return_supplier','#search_code');
search_daterangepicker();
</script>
