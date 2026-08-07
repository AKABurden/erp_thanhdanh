<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-import img {
        height: 20px;
        width: 20px;
    }

    .table-import tr td:nth-child(1) {
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }

    .table-import tr td:nth-child(2) {
        min-width: 108px;
        white-space: unset;
        text-align: center;
    }

    .table-import tr td:nth-child(3) {
        min-width: 200px;
        white-space: unset;
        text-align: left;
    }

    .table-import tr td:nth-child(4) {
        min-width: 100px;
        white-space: unset;
    }

    .table-import tr td:nth-child(5) {
        min-width: 100px;
        white-space: unset;
        text-align: right;
    }

    /*  .table-import tr td:nth-child(6) {
        min-width: 130px;
        white-space: unset;
        text-align: center;
        display: none;
    }*/

    /*.table-import tr th:nth-child(6) {
        display: none;
    }*/

    .table-import tr td:nth-child(7) {
        min-width: 130px;
        white-space: unset;
        text-align: center;
    }

    .table-import tr td:nth-child(8) {
        min-width: 120px;
        white-space: unset;
        text-align: center;
    }

    .table-import tr td:nth-child(9) {
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-import tbody tr td:nth-child(10) {
        white-space: inherit;
        width: 150px;
        text-align: center;
    }

    .table-import tbody .dropdown {
        text-align: center;
    }

    .table-import thead tr th {
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="dropdown pull-right">


                </div>
                <?php if (has_permission('import', '', 'create')) { ?>
                    <!--  <a href="<?= admin_url('import/detail') ?>"  class="btn btn-info mright5 test pull-right H_action_button">
               <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('create_add_new'); ?></a> -->
                <?php } ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?php echo render_select('search_code', array(), array('id', 'company'), 'ch_code_p'); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_select('search_staff[]', $dataStaff, array('staffid', 'name'), 'ch_staff_p', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_select('search_id_suppliers[]', $dataSupplier, array('id', 'company', 'code'), 'ch_name_suppliers', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>
                            <div class="input-group">
                                <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="items">
                            <input type="text" name="type_items" class="hide" id="type_items">
                            <label for="months-report"><?php echo _l('tnh_items'); ?></label><br />
                            <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select" name="custom_item_select" style="width: 100%">
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
                                            <?= _l('leads_all') ?> (<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                            <?= _l('ch_confirm_22') ?> QA (<span class="dont_approve">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                            <?= _l('dont_approve') ?> QA (<span class="ch_confirm_22">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="5">
                                            <?= _l('ch_warehouse_d') ?> (<span class="ch_warehouse_d">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="6">
                                            <?= _l('ch_warehouse_nd') ?> (<span class="ch_warehouse_nd">0</span>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <input type="hidden" id="suppliers_id" name="suppliers_id" value="" />
                        <div class="clearfix"></div>
                        <?php $table_data = array(
                            _l('ch_date_p'),
                            _l('ch_code_p'),
                            _l('supplier'),
                            _l('ch_code_old'),
                            _l('total_price'),
                            _l('ch_addedfrom'),
                            //   _l('invoice_dt_table_heading_status'),
                            _l('ch_check_qc'),
                            _l('ch_warehoues_app'),
                            _l('tnh_reference_bill'),
                            _l('Công Việc'),
                        );
                        $custom_fields = get_custom_fields('imports', array('show_on_table' => 1));
                        foreach ($custom_fields as $field) {
                            array_push($table_data, $field['name']);
                        }
                        array_push($table_data, _l('ch_option'));
                        render_datatable_tfoot_ch($table_data, 'import');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="red_invoice_all" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('ch_red_invoice_all'); ?>
                </h4>
            </div>
            <?php echo form_open('admin/purchase_invoice/add_all', array('id' => 'invoice_all-form')); ?>
            <div class="modal-body" style="background: #f1f1f1">
                <div class="panel_s panel_box">
                    <div class="panel-body">
                        <input id="id_import_all" class="id_import_all hide" name="id_import_all">
                        <input id="id_supplier" class="id_supplier hide" name="id_supplier">
                        <?php echo render_input('code_invoice_all', 'ch_code_invoice'); ?>
                        <?php echo render_date_input('date_invoice_all', 'ch_date_invoice'); ?>
                        <?php echo render_textarea('note_all', 'ch_note') ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer" style="background: #f1f1f1">
                <button type="submit" class="btn btn-info" target="_blank"><?php echo _l('create_add_new'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php
if (!empty($id_modal)) {
    echo '<a class="hide btn btn-info btn-icon mbot5 tao_new_task" href="javascript:void(0)" onclick="new_task(\'' . admin_url('tasks/task?import_id=' . $id_modal) . '\'); return false;">Tạo công việc</a>';
}
?>
<?php init_tail(); ?>
<?php $this->load->view('admin/popup_purchase_order/manage') ?>
<div id="payment_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    <?php
    if (!empty($id_modal)) { ?>
        $('.tao_new_task').click();
    <?php }
    ?>
    $(document).on('change', '#id_suppliers', function() {
        $('#suppliers_id').val($('#id_suppliers').val());
        $('#suppliers_id').change();
    });
    $(document).on('change', '#id_suppliers_invoice', function() {
        $('#suppliers_id').val($('#id_suppliers_invoice').val());
        $('#suppliers_id').change();
    });

    function red_invoice_all() {
        $('#id_supplier').val('');
        $('#id_import_all').val('');
        var ids = '';
        var rows = $('.table-import').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        if (empty(ids)) {
            alert("<?= _l('ch_null_invoice_all') ?>");
            return;
        } else {
            $('#id_supplier').val($('#suppliers_id').val());
            $('#id_import_all').val(ids);
        }
        $('#red_invoice_all').modal('show');
    }
    _validate_form($('#invoice_all-form'), {
        code_invoice_all: 'required',
        date_invoice_all: 'required'
    }, purchase_invoice_all);

    function purchase_invoice_all(form) {
        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form);
            alert_float(form.alert_type, form.message);
            $('#red_invoice_all').modal('hide');
            tAPI.draw('page');
            $('#suppliers_id').val('');
            $('#suppliers_id').change();
            $('.add_contact_person_invoice').popover('hide');
            window.open('<?= admin_url('purchase_invoice') ?>', "_blank");
        }), !1
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
    $(function() {
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'suppliers_id': '[name="suppliers_id"]',
            'search_code': '[name="search_code"]',
            'search_staff': '[name="search_staff[]"]',
            'search_id_suppliers': '[name="search_id_suppliers[]"]',
            'search_date': '[name="search_date"]',
            'type_items': '[name="type_items"]',
            'custom_item_select': '[name="custom_item_select"]',
        };
        tAPI = initDataTableCustom('.table-import', admin_url + 'import/table', [0], [0], CustomersServerParams,
            <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>,
            fixedColumns = {
                leftColumns: 0,
                rightColumns: 0
            });
        // var tAPI = initDataTable('.table-import', admin_url+'import/table', [0], [0], CustomersServerParams,[1, 'desc']);
        <?php if (!has_permission('import', '', 'view_price')) { ?>
            tAPI.columns(4).visible(false, false);
        <?php } ?>
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });

        $('.table-import').on('draw.dt', function() {
            checkNoDrop();
            get_total_limit();
        });
        $('.table-import').on('draw.dt', function() {
            <?php if (has_permission('import', '', 'view_price')) { ?>
                var itemsTable = $(this).DataTable();
                var sums = itemsTable.ajax.json().sums;
                $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
                $('.DTFC_LeftFootWrapper').css("background", "#ffff");
                $('.dataTables_scrollFoot').find('tfoot td').eq(1).html('<div class="text-center">Tổng</div>');
                $('.dataTables_scrollFoot').find('tfoot td').eq(4).html('<div class="text-right">' + sums.total + '</div>');
            <?php } ?>

        });
    });


    function view_supplier_quotes(id) {
        $('#view_supplier_quotes').html('');
        $.get(admin_url + 'supplier_quotes/view_supplier_quotes/' + id).done(function(response) {
            $('#view_supplier_quotes').html(response);
            $('#views_items').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#views_import', function() {
        $('#import_data').html('');
        tAPI.draw('page');
    });
    $('body').on('hidden.bs.modal', '#views_items', function() {
        $('#view_supplier_quotes').html('');
    });

    function view_purchase_order(id) {
        $('#purchase_order_data').html('');
        $.get(admin_url + 'purchase_order/view_purchase_order/' + id).done(function(response) {
            $('#purchase_order_data').html(response);
            $('#view_purchase_order').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_purchase_order', function() {
        $('#purchase_order_data').html('');
    });

    function view_purchases(id) {
        $('#purchases_data').html('');
        $.get(admin_url + 'purchases/views_purchases/' + id).done(function(response) {
            $('#purchases_data').html(response);
            $('#views_purchases').modal('show');
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    $(document).on('click', '.delete-remind', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
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
        setTimeout(function() {
            tAPI.draw('page');
        }, 500);
    });

    function confirm_warehous(id, warehouseman_id) {
        {
            dataString = {
                id: id,
                warehouseman_id: warehouseman_id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>import/confirm_warehous",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    tAPI.draw('page');
                    alert_float(response.alert_type, response.message);
                }
            });
            return false;
        }
    }

    function var_status(status, id) {
        {
            dataString = {
                id: id,
                status: status,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>import/update_status",
                data: dataString,
                cache: false,
                success: function(response) {
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
    $(document).on('click', '.invoice_button', (e) => {
        $('#purchase_invoice-form').attr('action', '<?= admin_url("purchase_invoice/add") ?>');
        $('#code_invoice').val('');
        $('#note').val('');
        $('#date_invoice').val("<?= _d(date('Y-m-d')); ?>");
    });

    function send_quote_suppliers(supplier_id, ask_price) {
        $('#send_quote_suppliers').html('');
        $.get(admin_url + 'RFQ/send_quote_suppliers/' + supplier_id + '/' + ask_price).done(function(response) {
            $('#send_quote_suppliers').html(response);
            $('#send_quote').modal('show');
            init_editor();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    var inner_popover_template =
        '<div class="popover" style="width:400px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.add_contact_person', function(e) {
        $('#suppliers_id').val('');
        $('#suppliers_id').change();
        $('.add_contact_person_invoice').popover('hide');
        var id = $(this).attr('data-id');
        var dropdown_menu = '\
    <?php
    echo render_select('id_suppliers', $suppliers, array('id', 'company'), 'ch_chose_suppliers');
    ?>\
    <button type="button" onclick="payment_all();return false;" class="btn btn-info btn-block mtop15"><?php echo _l('ch_submit_import'); ?></button>\
    </div>';
        $('select[name="id_suppliers"]').selectpicker('refresh');
        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            // trigger: 'focus',
            title: '<?= _l('ch_pay_slip_total') ?><button type="button" class="close close_pay">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_template
        });
        $('#suppliers_id').selectpicker('refresh');
    });
    var inner_popover_template_invoice =
        '<div class="popover" style="width:400px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.add_contact_person_invoice', function(e) {
        $('#suppliers_id').val('');
        $('#suppliers_id').change();
        $('.add_contact_person').popover('hide');
        var id = $(this).attr('data-id');
        var dropdown_menu = '\
    <?php
    echo render_select('id_suppliers_invoice', $suppliers, array('id', 'company'), 'ch_chose_suppliers');
    ?>\
    <button type="button" onclick="red_invoice_all();return false;" class="btn btn-info btn-block mtop15"><?php echo _l('ch_submit_import'); ?></button>\
    </div>';
        $('select[name="id_suppliers"]').selectpicker('refresh');
        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            // trigger: 'focus',
            title: '<?= _l('ch_red_invoice_all') ?><button type="button" class="close close_invoice">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_template_invoice
        });
        $('#id_suppliers_invoice').selectpicker('refresh');
    });

    function save_contact_person(id) {
        var note_cancel = $('#note_cancel').val();
        dataString = {
            note_cancel: note_cancel,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>purchases/note_cancel/" + id,
            data: dataString,
            cache: false,
            success: function(data) {
                // itemList = data;
                $('.add_contact_person').popover('hide');
                tAPI.draw('page');
                // table_api.ajax.reload();

                data = JSON.parse(data);
                alert_float(data.alert_type, data.message)
            }
        });
    }
    $(document).on('click', '.close_pay', function(e) {
        $('.add_contact_person').popover('hide');
        $('#suppliers_id').val('');
        $('#suppliers_id').change();
    });
    $(document).on('click', '.close_invoice', function(e) {
        $('.add_contact_person_invoice').popover('hide');
        $('#suppliers_id').val('');
        $('#suppliers_id').change();
    });
    $(document).on('click', '.po-close', function(e) {
        $('.popover').popover('hide');
    });

    function payment(id) {
        // id_supplierss

        $('#payment_data').html('');
        $.get(admin_url + 'import/payment/' + id).done(function(response) {
            $('#payment_data').html(response);
            $('#payment').modal({
                show: true,
                backdrop: 'static'
            });
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

    function payment_all() {

        var ids = '';
        var rows = $('.table-import').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        if (empty(ids)) {
            alert("<?= _l('ch_pay_total_all') ?>");
            return;
        } else {
            $('#payment_data').html('');
            dataString = {
                ids: ids,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>import/payment_all",
                data: dataString,
                cache: false,
                success: function(response) {
                    $('#payment_data').html(response);
                    $('#payment').modal({
                        show: true,
                        backdrop: 'static'
                    });

                    init_selectpicker();
                    init_datepicker();
                    $('#id_supplierss').val($('#suppliers_id').val());
                }
            });
            return false;
        }
    }


    //hoàng crm bổ xung search
    var inner_popover_template =
        '<div class="popover" style="width:1000px;max-width: 2000px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.search_person', function(e) {
        var dropdown_menu = `
          <div class="col-md-3">
            <?php echo render_select('search_code', array(), array('id', 'company'), 'ch_code_p'); ?>
          </div>
          <div class="col-md-3">
            <?php echo render_select('search_staff[]', $dataStaff, array('staffid', 'name'), 'ch_staff_p', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
          </div>
          <div class="col-md-3">
            <?php echo render_select('search_id_suppliers[]', $dataSupplier, array('id', 'company', 'code'), 'ch_name_suppliers', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
          </div>
          <div class="col-md-3">
            <div class="form-group">
               <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>
               <div class="input-group">
                  <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">
                  <div class="input-group-addon">
                     <i class="fa fa-calendar calendar-icon"></i>
                  </div>
               </div>
            </div>
          </div>`;

        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            title: '<?= _l('ch_seach_statistical') ?><button type="button" class="close">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_template
        });
        init_selectpicker();
        init_ajax_searchs('import', '#search_code');
        search_daterangepicker();
    });
    $(document).on('click', '.close', function(e) {
        $('.search_person').popover('hide');
    });

    function init_ajax_searchs(e, t, a, i) {
        var n = $("body").find(t);
        var h = t;
        if (n.length) {
            var s = {
                ajax: {
                    url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                    data: function() {
                        var t = {
                            [csrfData.token_name]: csrfData.hash
                        };
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
                        };
                        t.push(n)
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
        }, function(start, end, label) {});
        $('input[name="search_date"]').val('').datepicker("refresh");
        $('input[name="search_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date").trigger("change");
        });
        $('input[name="search_date"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#search_date").trigger("change");
        });
    };

    $(document).on('change', '#search_code', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', 'select[name="search_staff[]"]', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', 'select[name="search_id_suppliers[]"]', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', '#search_date', function(e) {
        tAPI.draw('page');
    });

    function get_total_limit() {
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>import/count_all/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.ch_confirm_22').html(data.ch_confirm_22);
                $('.dont_approve').html(data.dont_approve);
                $('.ch_warehouse_d').html(data.ch_warehouse_d);
                $('.ch_warehouse_nd').html(data.ch_warehouse_nd);


            }
        });
    }
    //end
    <?php
    if (!empty($this->input->get('zalo_purchase'))) {
    ?>
        view_import(<?= $this->input->get('zalo_purchase') ?>);
    <?php
    }
    ?>
    init_ajax_searchs('import', '#search_code');
    search_daterangepicker();

    function barcode(id, id_items) {
        var url = "<?= admin_url('import/print_pdf_code_v2/') ?>" + id + '/' + id_items;
        printPdf(url);
    }

    function printPdf(url) {
        var iframe = document.createElement('iframe');
        // iframe.id = 'pdfIframe'
        iframe.className = 'pdfIframe'
        document.body.appendChild(iframe);
        iframe.style.display = 'none';
        iframe.onload = function() {
            setTimeout(function() {
                iframe.focus();
                iframe.contentWindow.print();
                URL.revokeObjectURL(url)
                // document.body.removeChild(iframe)
            }, 1);
        };
        iframe.src = url;
    }

    $('#custom_item_select').on('change', function(e) {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() != '') {
            var type = currentQuantityInput.select2('data').type;
            $('#type_items').val(type);
        } else {
            $('#type_items').val('');
        }
        $('#type_items').change();
    });

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    var base_url = '<?= base_url() ?>';

    function repoFormatSelection(state) {
        if (!state.id) return state.text;

        return state.text + ' - ' + '(' + state.code + ')';
    }
    ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('inventory/SearchItems_new') ?>", 0);
</script>