<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
    <?php init_head(); ?>
    <style type="text/css">
    .table-purchase_invoice tr td:nth-child(1){
       text-align: center;
    }
    .table-purchase_invoice tr td:nth-child(2){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase_invoice tr td:nth-child(3){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase_invoice tr td:nth-child(4){
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase_invoice tr td:nth-child(5){
        min-width: 200px;
        white-space: unset;
    }
    .table-purchase_invoice tr td:nth-child(6){
        min-width: 110px;
        white-space: unset;
    }
    .table-purchase_invoice tr td:nth-child(7){
        min-width: 110px;
        white-space: unset;
    }
    .table-purchase_invoice tr td:nth-child(8){
        min-width: 110px;
        white-space: unset;
    }
    .table-purchase_invoice tr td:nth-child(9){
        min-width: 110px;
        white-space: unset;
    }
    .table-purchase_invoice tr td:nth-child(10){
        min-width: 110px;
        white-space: unset;
    }
    .table-purchase_invoice tr td:nth-child(11){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase_invoice tr td:nth-child(12){
        min-width: 150px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase_invoice tbody tr td:nth-child(13) {
        white-space: inherit;
        min-width: 100px;
    }
    .table-purchase_invoice tbody tr td:nth-child(14) {
        white-space: inherit;
        min-width: 160px;
    }
    .table-purchase_invoice tbody .dropdown {
        text-align: center;
    }
    .table-purchase_invoice img{
        height: 20px;
        width: 20px;
    }
    .table-purchase_invoice thead tr th{
       text-align: center;
    }
    </style>
        <div id="wrapper">
           <div class="panel_s mbot10 H_scroll" id="H_scroll">
              <div class="panel-body _buttons">
                 <div class="_buttons">
                    <span class="bold uppercase fsize18 H_title"><?=$title?></span>
                    <!-- yct start -->
                    <!-- create new -->
                    <?php if (has_permission('pay_slip', '', 'create')){ ?>
                    <div class="pull-right mright5 H_border mleft5">
                        <a href="<?= base_url('admin/purchase_invoice/modalAdd') ?>" class="btn btn-info H_action_button tnh-modal">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
                    <?php } ?>
                    <!-- yct end -->
                    <?php if(has_permission('pay_slip', '', 'create')){ ?>
                    <a  class="add_contact_person btn btn-info pull-right mleft5 H_action_button" >
                    <?php echo _l('ch_pay_slip_total'); ?></a>
                    <?php }?>
                    <!-- sum note -->
                    <a class="btn btn-info pull-right mleft5 H_action_button option_barcode" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><i class="fa fa-filter"></i> <?= lang('tnh_seach_statistical') ?></a>
                    <!-- ./sum note -->
                    <div class="clearfix"></div>

                 </div>
              </div>
           </div>
           <div class="content">
              <div class="row">
              <!-- sum note -->
              <div class="col-md-12">
                <div id="search-tnh" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?= lang('supplier', 'business_plan_search') ?>
                        <input type="text" name="suppliers_id" data-placeholder="<?= lang('supplier') ?>" id="suppliers_id" class="business_plan_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
             <!-- ./sum note -->
                 <div class="col-md-12">
                    <div class="panel_s">
                       <div class="panel-body">
                        <div class="clearfix"></div>
                          <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                          <input type="hidden" id="suppliers_id" name="suppliers_id" value=""/>
                          <div class="clearfix mtop20"></div>
                           <?php $table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="purchase_invoice"><label></label></div>';
                           ?>
                          <?php $table_data = array_merge($table_data, array(
                              _l('ch_date_invoice'),
                              _l('ch_code_invoice'),
                              _l('ch_importss'),
                              _l('supplier'),
                              _l('total_price_befor_vat'),
                              _l('total_price_vat'),
                            //   _l('ch_ncc_promotion'),
                              _l('total_price_affter_vat'),
                            //   _l('ch_other_expenses'),
                            //   _l('leads_dt_status'),
                              _l('leads_dt_assigned'),
                            //   _l('Link'),
                              _l('ch_option'),
                            ));
                            render_datatable_tfoot_ch($table_data,'purchase_invoice');
                          ?>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
    <div id="electronic_bill_data"></div>
    <div id="payment_data"></div>
    <?php init_tail(); ?>
    <link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
    <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
    <script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
    <!-- sum note -->
    <script type="text/javascript">
        var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
        var fnserverparams = {status_table: '#status_table', business_plan_search: '#business_plan_search', start_date: '#start_date', end_date: '#end_date'};
        var oTable = '';
    </script>
    <script type="text/javascript">
    function ajaxSelectParamsv1(element, url, id, params = false, clearSl2 = false)
    {
    console.log(clearSl2);
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: true,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                        if (data.row) {
                            if (data.row.id === 0) {
                                $(element).val(0);
                            }
                        }
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        params: params,
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: true,
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        params: params,
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    }
}
	$(document).ready(function() {
        ajaxSelectParamsv1('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        ajaxSelectParamsv1('#suppliers_id', 'admin/suppliers/searchSuppliers', 0, true, true);

		oTable = tnhDatatable(
            '#table-productions-plan',
            {
                'order': [[2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                scrollY: height_body,
                scrollX: true,
                // fixedColumns:   {
                //     leftColumns: 4,
                //     rightColumns: 1
                // },
                // stateSave: true,
                autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getProductionsPlan') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "drawCallback": function(settings, nRow) {
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    stProductionPlan = aData[9];
                    if (stProductionPlan != 'approved') {
                        $(nRow).find('.tnh-created-productions-capacity').addClass('tnh-disabled');
                    }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function( tfoot, data, start, end, display ) {
                },
                "columnDefs": [
                    {"targets": 0, "name": 'id', 'visible': false},
                    {"targets": 1, "name": 'number_records', 'className': 'text-center', 'sortable': false, 'width': '50px'},
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2, "name": 'date', 'searchable': false, 'width': '80px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<a class="tnh-modal" data-tnh="modal" href="'+site.base_url+'admin/manufactures/view_productions_plan/'+row[0]+'" data-toggle="modal" data-target="#myModal">'+data+'</a>';
                        },
                        "targets": 3, "name": 'reference_no', 'width': '140px'
                    },
                    {"targets": 4, "name": 'planning_cycle', 'width': '150px'},
                    {
                        "render": function(data, type, row) {
                            return data;
                        },
                        "targets": 5, "name": 'pb', 'width': '250px'
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            if (data) {
                                data = data.split('-');
                                if (data[0] == 1)
                                {
                                    str+= '<span class="label label-primary"><?= lang('tnh_sales_orders') ?></span>';
                                }
                                if (data[0] == 1 && data[1] == 1) {
                                    str+= '</br></br>';
                                }
                                if (data[1] == 1)
                                {
                                    str+= '<span class="label label-warning"><?= lang('tnh_business_plan') ?></span>';
                                }
                            }
                            return str;
                        },
                        "targets": 6, "name": 'options', 'width': '130px'
                    },
                    {"targets": 7, "name": 'note', 'width': '150px'},
                    {"targets": 8, "name": 'created_by', 'width': '120px'},
                    {
                        "render": function(data, type, row) {
                            str = '';

                            productions_plan_id = row[0];
                            if (data == "approved" || data == "capacity") {
                                user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: '+row[10]+'</div>';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' productions_plan_id=\''+productions_plan_id+'\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>'+user_status;
                            } else if (data == "approved") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' productions_plan_id=\''+productions_plan_id+'\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>'+user_status;
                            } else if (data == "capacity") {
                                str = '<div class="text-left"><span class="label label-primary"><?= lang('tnh_capacity') ?></span></div>'+user_status;
                            } else {
                                str = '';
                            }
                            return str;
                        },
                        "targets": 9, "name": 'status', 'width': '120px'
                    },
                    {"targets": 10, "name": 'user_status', 'visible': false},
                    {"targets": 11, "name": 'reference_orders', 'visible': false},
                    {
                        "render": function(data, type, row) {
                            str = '';
                            productions_plan_id = row[0];

                            if (data == 2) {
                                str = '<div class="text-center"><span class="label label-success"><?= lang('tnh_order_finised') ?></span></div>';
                            } else if (data == 0 || data == 1) {
                                title = '<?= lang('tnh_not_produced') ?>';
                                // btnType = "warning";
                                str = '<div class="text-center"><span class="label label-warning">'+title+'</span></div>';
                                if (data == 1) {
                                    title = '<?= lang('tnh_apart_producing') ?>';
                                    // btnType = "primary";
                                    str = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_procedure') ?>\" data-content="<p><a id=\'agree-procedure\' productions_plan_id=\''+productions_plan_id+'\' value=\'2\' class=\'btn btn-danger\'><?= lang('tnh_order_finised') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-primary po">'+title+'</span></div>';
                                }
                            }
                            return str;
                        },
                        "targets": 12, "name": 'wprocedure', 'width': '120px'
                    },
                    {"targets": 13, "name": 'actions', 'searchable': false, 'sortable': false, 'width': '160px'}
                ]
            }
        );

        $(document).on('click', '#table-productions-plan_wrapper .btn-dt-reload', function(event) {
            oTable.draw();
        });

        $(document).on('change', '#orders_search, #business_plan_search, #start_date, #end_date', function(event) {
            oTable.draw();
        });

        $(document).on('click', '#table-view-plan_wrapper .btn-dt-reload', function(event) {
            oTableProductionsPlan.draw('page');
        });

        $(document).on('click', '#table-view-procedure_wrapper .btn-dt-reload', function(event) {
            oTableProductionsPlanProceduce.draw('page');
        });

        $('#table-productions-plan').on('draw.dt', function(e, settings) {
        })

        $(document).on('click', '.export-excel', function(event) {
            event.preventDefault();
            productions_plan_id = $(this).attr('value');
            bootbox.confirm({
                message: '<?= lang('tnh_you_want_to_export_excel') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        if (productions_plan_id) {
                            $.ajax({
                                url: site.base_url+'admin/manufactures/export_excel_production_plan',
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    productions_plan_id: productions_plan_id,
                                    export_excel: 1,
                                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                                },
                            })
                            .done(function(data) {
                                if (data.result) {
                                    alert_float('success', data.message);
                                    download(data.filename, data.file);
                                } else {
                                    alert_float('danger', data.message);
                                }
                            })
                            .fail(function() {
                                alert_float('danger', 'errors');
                            });
                        }
                    }
                }
            });
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            productions_plan_id = $(this).attr('productions_plan_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_plan_id) {
                $.ajax({
                    url: site.base_url+'admin/manufactures/agreeProductionsPlan',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        productions_plan_id: productions_plan_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw('page');
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw('page');
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
            }
        });

        $(document).on('click', '#agree-procedure', function(event) {
            event.preventDefault();
            index = this;
            productions_plan_id = $(this).attr('productions_plan_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_plan_id) {
                $.ajax({
                    url: site.base_url+'admin/manufactures/agreeProductionsPlanProcedure',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        productions_plan_id: productions_plan_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw('page');
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw('page');
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
            }
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
	});
</script>
    <!-- ./sum note -->
    <script>
        $(document).on('change', '#id_suppliers', function() {
        $('#suppliers_id').val($('#id_suppliers').val());
        $('#suppliers_id').change();
        });
        var tAPI;
        $(function(){

            var CustomersServerParams = {
              'suppliers_id' : '[name="suppliers_id"]',
              'start_date_search' : '[name="start_date_search"]',
              'end_date_search' : '[name="end_date_search"]',
            };
            tAPI = initDataTableCustom('.table-purchase_invoice', admin_url+'purchase_invoice/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1,'desc'))); ?>, /*fixedColumns = {leftColumns: 3, rightColumns: 1}*/);
            $.each(CustomersServerParams, function(filterIndex, filterItem){
              $('' + filterItem).on('change', function(){
                tAPI.draw('page');
              });
            });
        });
        $('.table-purchase_invoice').on('draw.dt', function() {
            var itemsTable = $(this).DataTable();
            var sums = itemsTable.ajax.json().sums;
            $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
            $('.DTFC_LeftFootWrapper').css("background","#ffff");
            $('.dataTables_scrollFoot').find('tfoot td').eq(5).html('<div class="text-right">'+sums.no_vat+'</div>');   
            $('.dataTables_scrollFoot').find('tfoot td').eq(6).html('<div class="text-right">'+sums.vat+'</div>');   
            // $('.dataTables_scrollFoot').find('tfoot td').eq(7).html('<div class="text-right">'+sums.km+'</div>');   
            $('.dataTables_scrollFoot').find('tfoot td').eq(7).html('<div class="text-right">'+sums.co_vat+'</div>');   
            // $('.dataTables_scrollFoot').find('tfoot td').eq(9).html('<div class="text-right">'+sums.pay+'</div>');   
        });
        function var_status(status,id) {
            {
                dataString={id:id,status:status,[csrfData['token_name']] : csrfData['hash']};
                jQuery.ajax({   
                    type: "post",
                    url:"<?=admin_url()?>purchase_invoice/update_status",
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
        function send_quote_suppliers(supplier_id,ask_price) {
            $('#send_quote_suppliers').html('');
            $.get(admin_url + 'RFQ/send_quote_suppliers/' + supplier_id + '/' +ask_price).done(function (response) {
                $('#send_quote_suppliers').html(response);
                $('#send_quote').modal('show');
                init_editor();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }
        var inner_popover_template = '<div class="popover" style="width:400px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
          $(document).on('click','.add_contact_person',function(e){
            $('#suppliers_id').val('');
            $('#suppliers_id').change();
            $('.add_contact_person_invoice').popover('hide');
            var id=$(this).attr('data-id');
            var dropdown_menu='\
            <?php
                echo render_select('id_suppliers',$suppliers,array('id','company'),'ch_chose_suppliers');
            ?>
            <button type="button" onclick="payment_all();return false;" class="btn btn-info btn-block mtop15"><?php echo _l('ch_submit_import'); ?></button>\
            </div>';
            $('select[name="id_suppliers"]').selectpicker('refresh');
            $(this).popover({
              html: true,
              container: 'body',
              placement: "bottom",
              trigger: 'click focus',
              // trigger: 'focus',
              title:'<?=_l('ch_pay_slip_total')?><button type="button" class="close close_pay">&times;</button>',
              content: function() {
                return dropdown_menu;
              },
              template: inner_popover_template
            });
            $('#suppliers_id').selectpicker('refresh');
          });
          function save_contact_person(id) {
            var note_cancel = $('#note_cancel').val();
            dataString={note_cancel:note_cancel,[csrfData['token_name']] : csrfData['hash']};
            jQuery.ajax({
              type: "post",
              url:"<?=admin_url()?>purchases/note_cancel/"+id,
              data: dataString,
              cache: false,
              success: function (data) {
                    // itemList = data;
                    $('.add_contact_person').popover('hide');
                    tAPI.draw('page');
                    // table_api.ajax.reload();
                    
                    data = JSON.parse(data);
                    alert_float(data.alert_type,data.message)
                  }
                });
          }
        $(document).on('click','.close',function(e){
          $('.add_contact_person').popover('hide');
          $('#suppliers_id').val('');
          $('#suppliers_id').change();
        }); 
        $('body').on('hidden.bs.modal', '#views_import', function() {
            $('#import_data').html('');
            $('.table-import').DataTable().ajax.reload();
        });      
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
        function electronic_bill(id) {
          $('#electronic_bill_data').html('');
          $.get(admin_url + 'purchase_invoice/electronic_bill/'+id).done(function(response) {
          $('#electronic_bill_data').html(response);
          $('#electronic_bill').modal({show:true,backdrop:'static'});
          init_selectpicker();
          init_datepicker();
          }).fail(function(error) {
          var response = JSON.parse(error.responseText);
          alert_float('danger', response.message);
          }); 
        }
        $('body').on('hidden.bs.modal', '#electronic_bill', function() {
            $('#electronic_bill_data').html('');
        });
        function payment(id) {
          $('#payment_data').html('');
          $.get(admin_url + 'purchase_invoice/payment/'+id).done(function(response) {
          $('#payment_data').html(response);
          $('#payment').modal({show:true,backdrop:'static'});
          init_selectpicker();
          init_datepicker();
          }).fail(function(error) {
          var response = JSON.parse(error.responseText);
          alert_float('danger', response.message);
          }); 
        }
        $('body').on('hidden.bs.modal', '#payment', function() {
            $('#payment_data').html('');
        });
        function payment_all()
        {
                var ids = '';
                var rows = $('.DTFC_LeftBodyWrapper .table-purchase_invoice').find('tbody tr');
                $.each(rows, function() {
                    var checkbox = $($(this).find('td').eq(0)).find('input');
                    if (checkbox.prop('checked') == true) {
                        ids+=checkbox.val()+',';
                    }
                });
                if(empty(ids))
                {
                  alert("<?=_l('ch_pay_total_all')?>");
                  return;
                }
                else
                {
                    $('#payment_data').html('');
                    dataString={ids:ids,[csrfData['token_name']] : csrfData['hash']};
                    jQuery.ajax({   
                        type: "post",
                        url:"<?=admin_url()?>purchase_invoice/payment_all",
                        data: dataString,
                        cache: false,
                        success: function (response) {
                            $('#payment_data').html(response);
                            $('#payment').modal({show:true,backdrop:'static'});
                            init_selectpicker();
                            init_datepicker();
                            $('#id_supplierss').val($('#suppliers_id').val());
                        }
                    });
                    return false;
                }
        } 
    </script>




