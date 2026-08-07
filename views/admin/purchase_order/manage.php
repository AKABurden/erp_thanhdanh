<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .progressbar_img {
        text-align: center !important;
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .progressbar_img img {
        height: 35px;
        width: 35px;
    }

    ul.progressbar_img li.active_img img {
        border: 2px solid #00ff50;
    }

    ul.progressbar_img li.cancel img {
        border: 2px solid red;
    }

    ul.progressbar_img li.cancel_all img {
        border: 2px solid blue;
    }

    ul.progressbar_img li {
        width: 87px;
        float: left;
    }

    .progressbar:not(.hoang) {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }

    .progressbar li span {
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
        width: 100% !important;
        height: 2px !important;
        content: '' !important;
        position: absolute !important;
        background-color: #7d7d7d !important;
        top: 4px !important;
        left: -50% !important;
        z-index: -1 !important;
    }

    .progressbar li:first-child:after {
        content: none;
        display: none;
    }

    .progressbar li.active_ch:before {
        border-color: red;
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

    .progressbar li.active+li:after {
        background-color: #55b776 !important;
    }

    .step-status {
        float: left;
        width: 20%;
        text-align: center;
        padding: 0 10px;
    }

    .step-status img {
        position: relative;
        cursor: pointer;
        z-index: 0;
        height: 30px;
        width: 30px;
        top: 5px;

    }

    .step-status .active img {
        border: 3px solid #4ab138;
    }

    .step-status .cancel img {
        border: 3px solid #f00;
    }

    .line {
        border: 1px solid #7d7d7d;
        position: relative;
        height: 1px;
        width: 100%;
        top: 40px;
        z-index: 0;
    }

    .line10:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 10%;
        height: 1px;
        border: 1px solid #4ab138;
    }

    .line30:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 30%;
        height: 1px;
        border: 1px solid #4ab138;
    }

    .line50:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 50%;
        height: 1px;
        border: 1px solid #4ab138;
    }

    .line70:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 70%;
        height: 1px;
        border: 1px solid #4ab138;
    }

    .no-drop img {
        cursor: no-drop;
    }

    .table-purchase-order tr th:nth-child(1) {
        min-width: 40px;
        white-space: unset;
        text-align: center;
        display: none;
    }

    .table-purchase-order tr td:nth-child(1) {
        min-width: 20px;
        display: none;
    }



    .table-purchase-order tr th:nth-child(2) {
        min-width: 140px;
        white-space: unset;
        text-align: center;
    }

    .table-purchase-order tr td:nth-child(2) {
        white-space: unset;
        text-align: center;
    }

    .table-purchase-order tr td:nth-child(3) {
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-purchase-order tr th:nth-child(4) {
        /* max-width: 190px; */
        width: 190px;
        white-space: unset;
        text-align: center;
    }

    .table-purchase-order tr td:nth-child(4) {
        /* max-width: 190px; */
        width: 190px;
        white-space: unset;
    }

    .table-purchase-order tr td:nth-child(5) {
        min-width: 90px;
        white-space: unset;
        text-align: right;
    }

    .table-purchase-order tr td:nth-child(6) {
        min-width: 90px;
        white-space: unset;
        text-align: right;
    }

    .table-purchase-order tr td:nth-child(7) {
        min-width: 90px;
        white-space: unset;
        text-align: center;
    }

    .table-purchase-order tr td:nth-child(8) {
        min-width: 90px;
        white-space: unset;
        text-align: right;
    }

    .table-purchase-order tr td:nth-child(9) {
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-purchase-order tr td:nth-child(10) {
        min-width: 100px;
        white-space: unset;
        text-align: left;
    }

    .table-purchase-order tr td:nth-child(11) {
        min-width: 100px;
        white-space: unset;
    }

    .table-purchase-order tr td:nth-child(12) {
        min-width: 100px;
        white-space: unset;
    }

    .table-purchase-order tbody tr td:nth-child(14) {
        white-space: inherit;
        min-width: 160px;
    }

    .table-purchase-order tbody .dropdown {
        text-align: center;
    }

    .table-purchase-order thead tr th {
        text-align: center;
    }

    .wap-icon {
        float: left;
        width: 20%;
    }

    .wap-icon img {
        cursor: pointer;
        position: relative;
    }

    .wap-icon img:hover {
        top: -5px;
        transition: all 0.5s;
    }

    .wap-icon.active .wap-title span {
        color: #2887d4;
        border: 2px solid #2887d46b;
        padding: 5px 25px;
    }

    .wap-icon.active .wap-title span::before {
        content: "✔";
        margin-right: 5px;
    }

    .wap-title {
        margin-top: 10px;
    }

    .wap-title-status {
        margin-top: 20px;
    }

    .wap-title-status {
        position: relative;
    }

    .wap-title-status::before {
        content: "";
        width: 10px;
        height: 10px;
        position: absolute;
        background: #7d7d7d;
        border-radius: 50%;
        top: -14px;
        left: calc(50% - 5px);
    }

    .wap-title-status.success::before {
        background: #4ab138;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="dropdown pull-right">
                    <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        <?= lang('actions') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                        <!-- <li>
                            <a href="<?= admin_url('option_pdf') ?>" class="" target="_blank"><i class="fa fa-file-pdf-o"></i>
                                <?php echo _l('option_pdf'); ?></a>
                        </li>
                        <li>
                            <a class="btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><i class="fa fa-filter"></i>
                                <?= lang('ch_seach_statistical') ?></a>
                        </li> -->
                        <?php if (has_permission('purchase_order', '', 'create')) { ?>
                            <li>
                                <a href="<?= base_url('admin/purchase_order/import_orders') ?>" class="">
                                    <i class="fa fa-upload"></i>
                                    <?php echo _l('tnh_import_orders'); ?>
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
                <?php if (has_permission('pay_slip', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a class="add_contact_person btn btn-info H_action_button option_barcode">
                            <?php echo _l('ch_pay_slip_total'); ?></a>
                    </div>
                <?php } ?>
                <?php if (has_permission('purchase_invoice', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a class="add_contact_person_invoice btn btn-info H_action_button option_barcode">
                            <?php echo _l('ch_red_invoice_all'); ?></a>
                    </div>
                <?php } ?>
                <?php if (has_permission('purchase_order', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= admin_url('purchase_order/detail') ?>" class="btn btn-info test H_action_button">
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
                <div id="search-tnh" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?php echo render_select('search_code', array(), array('id', 'company'), 'ch_code_p'); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_select('search_priorities[]', $dataPriorities, array('priorityid', 'name'), 'status_order', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
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
                                    <li class="hide">
                                        <a class="H_filter" data-id="1">
                                            <?= _l('dont_confirm') ?> (<span class="status0">0</span>)
                                        </a>
                                    </li>
                                    <li class="hide">
                                        <a class="H_filter" data-id="2">
                                            <?= _l('do_confirm') ?> (<span class="status1">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                            <?= _l('do_approve') ?> (<span class="status2">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="9">
                                            <?= _l('Chưa nhập') ?> (<span class="import">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="10">
                                            <?= _l('ch_imports_part') ?> (<span class="import2">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="11">
                                            <?= _l('ch_imports_full') ?> (<span class="import1">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                            <?= _l('ch_invoice_tax') ?> (<span class="red_invoice">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="5">
                                            <?= _l('ch_retail_invoice') ?> (<span class="red_invoice_no">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="6">
                                            <?= _l('ch_status_pays_slip') ?> (<span class="status_pay">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="7">
                                            <?= _l('ch_status_pays_slip_part') ?> (<span class="status_pay1">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="8">
                                            <?= _l('ch_status_pays_slip_no') ?> (<span class="status_pay0">0</span>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <input type="hidden" id="suppliers_id" name="suppliers_id" value="" />
                        <?php
                        $table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="purchase-order"><label></label></div>';
                        ?>
                        <?php $table_data = array_merge($table_data, array(
                            _l('ch_date_p'),
                            _l('ch_code_p'),
                            _l('supplier'),
                            _l('total_price'),
                            _l('total_d_exchange'),
                            _l('ch_total_expenses'),
                            _l('ch_type_invoice'),
                            '<div class="center">' . _l('Trạng thái nhập hàng') . '</div>',
                            _l('note'),
                        ));
                        $custom_fields = get_custom_fields('purchase_order', array('show_on_table' => 1));
                        foreach ($custom_fields as $field) {
                            array_push($table_data, $field['name']);
                        }
                        render_datatable_tfoot_ch($table_data, 'purchase-order');
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

<!-- modal evaluate -->
<div id="evaluate_modal"></div>
<!-- end -->
<?php init_tail(); ?>
<?php $this->load->view('admin/popup_purchase_order/manage') ?>
<input type="text" name="type_suppliert" class="hide" id="type_suppliert">
<div id="payment_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    function red_invoice_all() {
        $('#id_supplier').val('');
        $('#id_import_all').val('');
        $('#code_invoice_all').val('');
        $('#date_invoice_all').val('<?= _d(date('Y-m-d')) ?>');
        $('#note_all').val('');
        var ids = '';
        var rows = $('.DTFC_LeftBodyWrapper .table-purchase-order').find('tbody tr');

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
            $('.table-import').DataTable().ajax.reload();
            $('#suppliers_id').val('');
            $('#suppliers_id').change();
            $('.add_contact_person_invoice').popover('hide');
            if (form.success != false) {
                window.open('<?= admin_url('purchase_invoice') ?>', "_blank");
            }
        }), !1
    }
    $(document).on('change', '#id_suppliers', function() {
        $('#type_suppliert').val(1);
        $('#suppliers_id').val($('#id_suppliers').val());
        $('#suppliers_id').change();

    });
    $(document).on('change', '#id_suppliers_invoice', function() {
        $('#type_suppliert').val(2);
        $('#suppliers_id').val($('#id_suppliers_invoice').val());
        $('#suppliers_id').change();
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
    var arr = [];
    $(function() {
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'search_code': '[name="search_code"]',
            'suppliers_id': '[name="suppliers_id"]',
            'search_staff': '[name="search_staff[]"]',
            'search_id_suppliers': '[name="search_id_suppliers[]"]',
            'search_priorities': '[name="search_priorities[]"]',
            'search_date': '[name="search_date"]',
            'type_items': '[name="type_items"]',
            'custom_item_select': '[name="custom_item_select"]',
            'type_suppliert': '[name="type_suppliert"]',
        };

        tAPI = initDataTableCustom('.table-purchase-order', admin_url + 'purchase_order/table', [0], [0],
            CustomersServerParams,
            <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(3, 'desc'))); ?>
        );
        <?php if (!has_permission('purchase_order', '', 'view_price')) { ?>
            tAPI.columns(4).visible(false, false);
            tAPI.columns(5).visible(false, false);
        <?php } ?>
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page')
            });
        });
    });

    $('.table-purchase-order').on('draw.dt', function() {
        get_total_limit();
    });
    //dt
    $('.table-purchase-order').on('draw.dt', function() {
        var itemsTable = $(this).DataTable();
        var sums = itemsTable.ajax.json().sums;
        $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
        $('.DTFC_LeftFootWrapper').css("background", "#ffff");
        $('.dataTables_scrollFoot').find('tfoot td').eq(5).html('<div class="text-right">' + sums.total + '</div>');
        $('.dataTables_scrollFoot').find('tfoot td').eq(6).html('<div class="text-right">' + sums.pay + '</div>');
    });
    $('.table-purchase-order').on('draw.dt', function(e, settings) {
        if (arr.length > 0) {
            $.each(arr, function(index, el) {
                $('input[name="idd"][value="' + el + '"]').closest('tr').find('td:nth-child(1)').trigger(
                    'click');
            });
        }
    })

    $('.table-purchase-order tbody').on('click', 'td:nth-child(1)', function() {
        var tr = $(this).closest('tr');
        var order_id = tr.find('.idd').val();
        var row = tAPI.row(tr);
        if (row.child.isShown()) {
            // var index=arr.indexOf(order_id);
            // arr.splice(index,1);
            arr = removeArray(arr, order_id);
        } else {
            if (!arr.includes(order_id)) {
                arr.push(order_id);
            }
            tr.addClass('shown');
        }
    });
    //
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

    function var_status(status, id) {
        {
            dataString = {
                id: id,
                status: status,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>purchase_order/update_status",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    alert_float(response.alert_type, response.message);
                    tAPI.draw('page');
                }
            });
            return false;
        }
    }

    function cancel_status(id) {
        {
            var r = confirm("<?php echo _l('Bạn có muốn kết thúc phiếu'); ?>");
            if (r == false) {
                tAPI.draw('page');
                return false;
            } else {
                dataString = {
                    id: id,
                    [csrfData['token_name']]: csrfData['hash']
                };
                jQuery.ajax({
                    type: "post",
                    url: "<?= admin_url() ?>purchase_order/cancel_status",
                    data: dataString,
                    cache: false,
                    success: function(response) {
                        response = JSON.parse(response);
                        alert_float('success', response.message);
                        if (response.success == true) {
                            tAPI.draw('page');
                            add_evaluate(id);
                        }
                    }
                });
                return false;
            }
        }
    }

    function no_cancel_status(id) {
        {
            var r = confirm("<?php echo _l('Bạn có muốn bỏ kết thúc phiếu'); ?>");
            if (r == false) {
                tAPI.draw('page');
                return false;
            } else {
                dataString = {
                    id: id,
                    [csrfData['token_name']]: csrfData['hash']
                };
                jQuery.ajax({
                    type: "post",
                    url: "<?= admin_url() ?>purchase_order/cancel_status",
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
    }


    //hoàng crm bổ xung search
    var inner_popover_templates =
        '<div class="popover" style="width:1000px;max-width: 2000px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.search_person', function(e) {
        var dropdown_menu = '\
      <div class="col-md-3">\
        <?php echo render_select('search_code', array(), array('id', 'company'), 'ch_code_p'); ?>\
      </div>\
      <div class="col-md-3">\
        <?php echo render_select('search_priorities[]', $dataPriorities, array('priorityid', 'name'), 'status_order', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>\
      </div>\
      <div class="col-md-3">\
        <?php echo render_select('search_staff[]', $dataStaff, array('staffid', 'name'), 'ch_staff_p', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>\
      </div>\
      <div class="col-md-3">\
        <?php echo render_select('search_id_suppliers[]', $dataSupplier, array('id', 'company', 'code'), 'ch_name_suppliers', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>\
      </div>\
      <div class="col-md-3">\
         <div class="form-group">\
            <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>\
            <div class="input-group">\
               <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">\
               <div class="input-group-addon">\
                  <i class="fa fa-calendar calendar-icon"></i>\
               </div>\
            </div>\
         </div>\
      </div>\
   ';
        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            title: '<?= _l('ch_seach_statistical') ?><button type="button" class="close">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_templates
        });
        init_selectpicker();
        init_ajax_searchs('purchase_order', '#search_code');
        search_daterangepicker();
    });
    $(document).on('click', '.close', function(e) {
        $('.search_person').popover('hide');
    });
    init_ajax_searchs('purchase_order', '#search_code');

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
    $(document).on('change', 'select[name="search_priorities[]"]', function(e) {
        tAPI.draw('page');
    });
    $(document).on('change', '#search_date', function(e) {
        tAPI.draw('page');
    });
    //end
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
        $.get(admin_url + 'purchase_order/payment/' + id).done(function(response) {
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
        var rows = $('.table-purchase-order').find('tbody tr');
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
                url: "<?= admin_url() ?>purchase_order/payment_all",
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

    function get_total_limit() {
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>purchase_order/count_all/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.status0').html(data.status0);
                $('.status1').html(data.status1);
                $('.status2').html(data.status2);
                $('.red_invoice').html(data.red_invoice);
                $('.red_invoice_no').html(data.red_invoice_no);
                $('.status_pay').html(data.status_pay);
                $('.status_pay0').html(data.status_pay0);
                $('.status_pay1').html(data.status_pay1);
                $('.import1').html(data.import1);
                $('.import').html(data.import);
                $('.import2').html(data.import2);
            }
        });
    }

    $(document).on('click', '.wap-icon', function(e) {
        var target = $(e.currentTarget);
        $('.wap-icon').removeClass('active');
        target.addClass('active');
        var points = target.attr('data-points');
        $('.points').val(points);
    });

    function add_evaluate(id) {
        $('#evaluate_modal').html('');
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>purchase_order/evaluate_modal",
            data: dataString,
            cache: false,
            success: function(data) {
                $('#evaluate_modal').html(data);

                $('.add_title_evaluate').removeClass('hide');
                $('.edit_title_evaluate').addClass('hide');
                $('#evaluate_form').attr("action", "<?= admin_url('purchase_order/add_evaluate/') ?>" + id);
                $('#evaluate_modal_data').modal({
                    show: true,
                    backdrop: 'static'
                });
            }
        });
    }

    function edit_evaluate(id) {
        $('#evaluate_modal').html('');
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>purchase_order/evaluate_modal/" + id,
            data: dataString,
            cache: false,
            success: function(data) {
                $('#evaluate_modal').html(data);

                $('.add_title_evaluate').addClass('hide');
                $('.edit_title_evaluate').removeClass('hide');
                $('#evaluate_form').attr("action", "<?= admin_url('purchase_order/edit_evaluate/') ?>" + id);
                $('#evaluate_modal_data').modal({
                    show: true,
                    backdrop: 'static'
                });
            }
        });
    }
    <?php
    if (!empty($this->input->get('zalo_purchase'))) {
    ?>
        view_purchase_order(<?= $this->input->get('zalo_purchase') ?>);
    <?php
    }
    ?>
    search_daterangepicker();


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