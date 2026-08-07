<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
    <?php init_head(); ?>
    <style type="text/css">

    .table-advance_payment img{
        height: 20px;
        width: 20px;
    }
    .table-advance_payment thead tr th{
       text-align: center;
    }
    .table-advance_payment tr td:nth-child(2)
    {
                min-width: 90px;
                white-space: unset;
                text-align: center;
    }
    .table-advance_payment tr td:nth-child(3)
    {
                min-width: 110px;
                white-space: unset;
                text-align: center;

    }
    .table-advance_payment tr td:nth-child(4)
    {
                min-width: 110px;
                white-space: unset;
                text-align: center;
    }
    .table-advance_payment tr td:nth-child(5)
    {
                min-width: 200px;
                white-space: unset;

    }
    .table-advance_payment tr td:nth-child(6)
    {
                min-width: 200px;
                white-space: unset;
    }
    .table-advance_payment tr td:nth-child(7)
    {
                min-width: 100px;
                white-space: unset;
                text-align: center;
    }
    .table-advance_payment tr td:nth-child(8)
    {
                min-width: 120px;
                white-space: unset;
                text-align: center;
    }
    .table-advance_payment tr td:nth-child(9)
    {
                min-width: 110px;
                white-space: unset;
                text-align: right; 
    }
    .table-advance_payment tr td:nth-child(11)
    {
                min-width: 120px;
                white-space: unset;
    }
    .table-advance_payment tr td:nth-child(10)
    {
                min-width: 120px;
                white-space: unset;
                text-align: center;
    }
    .table-advance_payment tr td:nth-child(12)
    {
                min-width: 150px;
                white-space: unset;
                text-align: center;
    }
    .table-advance_payment tr td:nth-child(14)
    {
                min-width: 150px;
                white-space: unset;
    }
    .popover{
        max-width:2500px;
        height:140px;    
    }
    </style>
        <div id="wrapper">
           <div class="panel_s mbot10 H_scroll" id="H_scroll">
              <div class="panel-body ">
                 <div class="_buttons">
                    <span class="bold uppercase fsize18 H_title"><?=$title?></span>
                    <a class=" btn btn-info pull-right H_action_button " data-toggle="collapse" data-target="#search-tnh" aria-expanded="true">
                      <span style="margin-bottom: 3px;" class="lnr lnr-funnel"></span>   
                      <?php echo _l('ch_seach_statistical'); ?>
                    </a>
                    <?php if (has_permission('advance_payment','','create')) { ?>
                    <div class="line-sp"></div>
                    <a href="" onclick="new_advance_payment(); return false;" class="btn btn-info mright5 test pull-right H_action_button">
                       <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                       <?php echo _l('create_add_new'); ?></a>
                    <?php } ?>
                  </div>
                  <div class="clearfix"></div>
              </div>
           </div>
           <div class="content">
              <div class="row">
                <div class="col-md-12">
                <div id="search-tnh" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?php echo render_select('search_staff[]', $dataStaff, array('staffid', 'name'), 'Nhân viên', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_select('paymode_c',$payment_modes,array('id','name'),'Phương thức thanh toán chuyển'); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_select('paymode_n',$payment_modes,array('id','name'),'Phương thức thanh toán nhận'); ?>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>
                            <div class="input-group">
                                <input type="text" id="search_date" name="search_date" class="form-control search_date"
                                       aria-invalid="false">
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
                        <div class="horizontal-scrollable-tabs">
                              <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                              <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                              <div class="horizontal-tabs">
                                  <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                          <?=_l('leads_all')?>(<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                          <?=_l('Chưa duyệt')?>(<span class="no_pay">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                          <?=_l('Đã duyệt')?>(<span class="pay">0</span>)
                                        </a>
                                    </li>
                                  </ul>
                              </div>
                          </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                          <div class="clearfix mtop20"></div>
                          <?php $table_data = array(
                              _l('#'),
                              _l('ch_code_number'),
                              _l('ch_date_p'),
                              _l('als_staff'),
                              _l('acs_sales_payment_modes_submenu_c'),
                              _l('acs_sales_payment_modes_submenu_n'),
                              _l('ch_costs'),
                              _l('ticket_dt_status'),
                              _l('expense_add_edit_amount'),
                              _l('ch_addedfrom'),
                              _l('ch_note_pay_slips'),
                              _l('ch_option'),
                            );
                            render_datatable_tfoot_ch($table_data,'advance_payment');
                          ?>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
    <?php init_tail(); ?>
    <div id="view_advance_payment"></div>
    <div id="view_advance_payment_data"></div>
    <div id="export_different_data"></div>

    <div id="return_suppliers_data"></div>
    <link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
    <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
    <script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
    <script>
        function new_advance_payment() {
            $('#view_advance_payment').html('');
            $.get(admin_url + 'advance_payment/advance_payment/').done(function (response) {
                $('#view_advance_payment').html(response);
                $('#advance_payment').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }
        $('body').on('hidden.bs.modal', '#advance_payment', function() {
            $('#view_advance_payment').html('');
        });
        function edit_advance_payment(id) {
            $('#view_advance_payment').html('');
            $.get(admin_url + 'advance_payment/advance_payment/'+id).done(function (response) {
                $('#view_advance_payment').html(response);
                $('#advance_payment').modal('show');
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }        
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
              'search_staff' : '[name="search_staff[]"]',
              'paymode_c' : '[name="paymode_c"]',
              'paymode_n' : '[name="paymode_n"]',
              'search_date' : '[name="search_date"]',
            };
            tAPI = initDataTableCustom('.table-advance_payment', admin_url+'advance_payment/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>, fixedColumns = {leftColumns: 3, rightColumns: 1});
            $.each(CustomersServerParams, function(filterIndex, filterItem){
              $('' + filterItem).on('change', function(){
                tAPI.draw('page');
              });
            });
            $('.table-advance_payment').on('draw.dt', function() {
               var invoiceReportsTable = $(this).DataTable();
               var sums = invoiceReportsTable.ajax.json().sums;
               $('.text-muted.all_orther').text(sums.all);
               $('.text-muted.payment').text(sums.payment);
               $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
               $('.DTFC_LeftFootWrapper').css("background","#ffff");
               $('.dataTables_scrollFoot').find('tfoot td').eq(10).html('<div class="text-right">'+sums.payment+'</div>');   
              get_total_limit();
            });
        });
        function var_status(status,id) {
            var r = confirm("<?php echo _l('confirm_action_prompt');?>");
            if (r == false) {
                return false;
            } else {
                dataString={id:id,status:status,[csrfData['token_name']] : csrfData['hash']};
                jQuery.ajax({   
                    type: "post",
                    url:"<?=admin_url()?>advance_payment/update_status",
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
        function update_status_not(status,id) {
            var r = confirm("<?php echo _l('confirm_action_prompt');?>");
            if (r == false) {
                return false;
            } else {
                dataString={id:id,status:status,[csrfData['token_name']] : csrfData['hash']};
                jQuery.ajax({   
                    type: "post",
                    url:"<?=admin_url()?>advance_payment/update_status_not",
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
        $(document).on('click', '.delete-remind', function() {
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
        function view_pay_slip(id) {
            $('#view_pay_slip_data').html('');
            $.get(admin_url + 'pay_slip/electronic_bill/'+id).done(function(response) {
            $('#view_pay_slip_data').html(response);
            $('#view_pay_slip').modal({show:true,backdrop:'static'});
            init_selectpicker();
            init_datepicker();
            }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
            }); 
        }
        $('body').on('hidden.bs.modal', '#advance_payment', function() {
            $('#view_advance_payment').html('');
        });
        $('body').on('hidden.bs.modal', '#views_import', function() {
            $('#import_data').html('');
            $('.table-import').DataTable().ajax.reload();
        });
        function get_total_limit() {
              dataString = {[csrfData['token_name']] : csrfData['hash']};
                      jQuery.ajax({
                          type: "post",
                          url: "<?=admin_url()?>advance_payment/count_all/",
                          data: dataString,
                          cache: false,
                          success: function (data) {
                            data = JSON.parse(data);
                            $('.all').html(data.all);
                            $('.pay').html(data.pay);
                            $('.no_pay').html(data.no_pay);
                            $('.pay_client').html(data.pay_client);
                            $('.pay_suppliers').html(data.pay_suppliers);
                            $('.pay_staff').html(data.pay_staff);
                            $('.pay_other').html(data.pay_other);
                          }
                      });
        }
    function view_advance_payment(id) {
        $('#view_advance_payment_data').html('');
        $.get(admin_url + 'advance_payment/view_modal/'+id).done(function(response) {
        $('#view_advance_payment_data').html(response);
        $('#view_advance_payment_view').modal({show:true,backdrop:'static'});
        init_selectpicker();
        init_datepicker();
        }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
        }); 
    }
    $('body').on('hidden.bs.modal', '#view_advance_payment_view', function() {
        $('#view_advance_payment_data').html('');
    });  
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
        }, function (start, end, label) {
        });
        $('input[name="search_date"]').val('').datepicker("refresh");
        $('input[name="search_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date").trigger("change");
        });
        $('input[name="search_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            $("#search_date").trigger("change");
        });
    };
    search_daterangepicker();
    </script>
