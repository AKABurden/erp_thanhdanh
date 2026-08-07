<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style class="">
table tr td {
    vertical-align: middle !important;
}

.progressbar_img li.active>img {
    color: green;
    border: 1px solid green;
}

.active_poin {
    font-size: smaller !important;
    font-style: italic !important;
    color: #da8227 !important;
}
tr._bg-danger {
    background: #fff3f3;
}
</style>
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button"
                    id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 220px;">
                    <li>
                        <a class="test btn-search-tnh" target="_blank"
                            href="<?= base_url('admin/orders/gannt_orders') ?>"><i class="fa fa-file-text-o"></i>
                            <?= lang('tnh_diagram_gantt_orders') ?></a>
                    </li>
                    <li>
                        <a class="test btn-search-tnh" target="_blank"
                            href="<?= base_url('admin/orders/delivery_schedules') ?>"><i class="fa fa-calendar"></i>
                            <?= lang('tnh_delivery_schedules') ?></a>
                    </li>
                    <li>
                        <a class="test btn-search-tnh" data-toggle="collapse" data-target="#search-tnh"
                            aria-expanded="true"><i class="fa fa-filter"></i> <?= lang('tnh_seach_statistical') ?></a>
                    </li>
                    <li>
                        <a class="test btn-search-tnh" onclick="exportExcel()"
                           aria-expanded="true"><i class="fa fa-file-excel-o"></i> <?= lang('c_export_excel') ?></a>
                    </li>
                    <li>
                        <a class="test btn-search-tnh" onclick="excelOrdersInformationDetail()"
                           aria-expanded="true"><i class="fa fa-file-excel-o"></i> <?= lang('Xuất excel chi tiết đơn hàng') ?></a>
                    </li>
                    <?php if ($this->perAddOrders): ?>
                    <li>
                        <a href="<?= base_url('admin/orders/import_orders') ?>" class="">
                            <i class="fa fa-upload"></i>
                            <?php echo _l('tnh_import_orders'). ' cố định'; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/orders/import_orders_change') ?>" class="">
                            <i class="fa fa-upload"></i>
                            <?php echo _l('tnh_import_orders'). ' thay đổi'; ?>
                        </a>
                    </li>
                    <?php endif ?>
                </ul>
            </div>
            <?php if ($this->perAddOrders): ?>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/orders/add') ?>" class="btn btn-info H_action_button">
                    <?php echo _l('add'); ?>
                </a>
            </div>
            <?php endif ?>
            <div class="text-center pull-right mright20 count-grand-total">
                <h4 class="bold text-muted" style="margin: 0">0</h4>
                <span class="text-primary"><?= lang('tnh_total_amount') ?></span>
            </div>
            <div class="text-center pull-right mright20 count-orders">
                <h4 class="bold text-muted" style="margin: 0">0</h4>
                <span class="text-danger"><?= lang('tnh_numbers_orders') ?></span>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <?= lang('tnh_reference_orders', 'orders_search') ?>
                        <input type="text" name="orders_search" id="orders_search" style="width: 100%;"
                            data-placeholder="<?= lang('tnh_reference_orders') ?>"
                            value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>"
                            id="customer_search" class="customer_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                            id="start_date_search" class="start_date_search datepicker form-control"
                            style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                            id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                            value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('tnh_type_orders', 'type_orders_search') ?>
                        <select name="type_orders_search" id="type_orders_search" data-placeholder="<?= lang('tnh_type_orders') ?>" class="type_orders" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($type_orders as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <?= lang('tnh_status_orders', 'status_orders_search') ?>
                        <select name="status_orders_search" id="status_orders_search" data-placeholder="<?= lang('tnh_status_orders') ?>" class="status_orders" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($status_orders as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <?= lang('Thành phẩm', 'items_search') ?>
                        <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="Thành phẩm" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <?php foreach (workflowOrders() as $key => $value): ?>
                                    <li role="presentation">
                                        <a href="#<?= $key ?>" aria-controls="<?= $key ?>" role="tab"
                                            value="<?= $key ?>"
                                            data-toggle="tab"><?= $value ?>(<span><?= $status[$key] ?></span>)</a>
                                    </li>
                                    <?php endforeach ?>
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab"
                                            value="un_approved"
                                            data-toggle="tab"><?= lang('tnh_un_approved') ?>(<span><?= $un_approved ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="approved"
                                            data-toggle="tab"><?= lang('tnh_approved') ?>(<span><?= $approved ?></span>)</a>
                                    </li>
                                    <li class="bg-danger" role="presentation">
                                        <a href="#out_time_ship" aria-controls="out_time_ship" role="tab" value="out_time_ship"
                                            data-toggle="tab"><?= lang('Quá hạn ngày giao hàng dự kiến') ?>(<span><?=!empty($out_time_ship) ? $out_time_ship : 0?></span>)</a>
                                    </li>
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all"
                                            data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table"
                                    class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="">
                            <table id="table-orders" class="table dt-tnh table-hover table-orders-new"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-right">
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="orders-new"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('Ngày lập đơn') ?></th>
                                        <th class="text-center"><?= lang('tnh_type_orders') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_orders') ?></th>
                                        <th class="text-center"><?= lang('customers') ?></th>
                                        <th class="text-center"><?= lang('tnh_address') ?></th>
                                        <th class="text-center"><?= lang('Tổng tiền') ?></th>
                                        <th class="text-center"><?= lang('Tổng tiền(VND)') ?></th>
                                        <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                        <th class="text-center"><?= lang('tnh_agree') ?></th>
                                        <th class="text-center"><?= lang('tnh_status_orders') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th class="text-center"><?= lang('tnh_wordflow_orders') ?></th>
                                        <th class="text-center"><?= lang('tnh_type_bills') ?></th>
                                        <th class="text-center"><?= lang('tnh_status_contract') ?></th>
                                        <th class="text-center"><?= lang('tnh_status_payment') ?></th>
                                        <th class="text-center"><?= lang('h_branch') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                        <th class="text-center"><?= lang('id') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bold">
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
var lang_orders =
    <?= json_encode(array('tnh_quantity_delivery_less' => lang('tnh_quantity_delivery_less'), 'tnh_check_quantity_delivery' => lang('tnh_check_quantity_delivery'))) ?>;
var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var fnserverparams = {
    customer_search: "#customer_search",
    status_table: '#status_table',
    orders_search: '#orders_search',
    start_date_search: '#start_date_search',
    end_date_search: '#end_date_search',
    type_orders_search: '#type_orders_search',
    status_orders_search: '#status_orders_search',
    items_search: '#items_search',
};
var oTable = '';
var statusOrders = <?= json_encode(statusOrders()) ?>;

function calOrders() {
    order_id = $('#orders_search').val();
    customer_id = $('#customer_search').val();
    start_date = $('#start_date_search').val();
    end_date = $('#end_date_search').val();
    type_orders = $('#type_orders_search').val();
    status_orders = $('#status_orders_search').val();

    $.ajax({
            url: site.base_url + 'admin/orders/calOrders',
            type: 'POST',
            dataType: 'json',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                order_id: order_id,
                customer_id: customer_id,
                start_date: start_date,
                end_date: end_date,
                type_orders: type_orders,
                status_orders: status_orders,
            },
        })
        .done(function(data) {
            $('.count-orders h4').html(tnhFormatZero(data.count_orders));
            $('.count-grand-total h4').html(tnhFormatMoney(data.sum_grand_total));
            $('.count-total-shopee h4').html(tnhFormatMoney(data.sum_total_shopee));
        })
        .fail(function() {
            console.log("error");
        });
}

$(document).ready(function() {
    $('#type_orders_search').select2({allowClear: true});
    $('#status_orders_search').select2({allowClear: true});
    ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
    ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
    ajaxSelectParams($('#items_search'), 'admin/products/searchProductAndGoodsMaterials', 0,true,true);

    oTable = tnhInitDataTable('#table-orders', '<?= site_url('admin/orders/getOrders') ?>', {
        'order': [
            [18, 'desc']
        ],
        'fixedHeader': {
            header: true,
        },
        'responsive': true,
        "ajax": {
            "url": '<?= site_url('admin/orders/getOrders') ?>',
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if (table.attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = table.attr('data-last-order-identifier');
                }
            },
            "dataSrc": function(json) {
                $('#table-orders tfoot tr td:nth-child(7)').html('<div class="text-right">'+tnhFormatMoney(json.grand_total)+'</div>');
                $('#table-orders tfoot tr td:nth-child(8)').html('<div class="text-right">'+tnhFormatMoney(json.grand_total_vnd)+'</div>');
                return json.aaData;
            }
        },
        "columnDefs": [{
                "render": function(data, type, row) {
                    return '<div class="checkbox"><input type="checkbox" name="order_id[]" id="check-item' +
                        data + '" value="' + data + '"><label for="check-item' + data +
                        '"></label></div>';
                },
                "targets": 0,
                "name": 'id',
                'orderable': false,
                'width': '40px'
            },
            {
                "render": function(data, type, row) {
                    return fld(data) + '<div>' + row[17] + '</div>';
                },
                "targets": 1,
                "name": 'type_orders',
                'width': '100px',
            },
            {
                "targets": 2,
                "name": 'customer_name',
                'width': '150px'
            },
            {
                "render": function(data, type, row) {
                    var str = '';
                    // return '<div style="min-width: 150px;" class="">\
                    //         <a data-tnh="modal" class="tnh-modal" href="' + site.base_url +
                    //     'admin/orders/view_order/' + row[0] +
                    //     '" data-toggle="modal" data-target="#myModal">' + data + '</a>\
                    //         </div>' + str + '<div class="td-reference-no"></div>';
                    return '<div style="min-width: 150px;" class="">'+data+'</div>';
                },
                "targets": 3,
                "name": 'reference_no',
                'width': '150px'
            },
            {
                "targets": 4,
                "name": 'customer_name',
                'width': '150px'
            },
            {
                "targets": 5,
                "name": 'address_delivery',
                'width': '150px'
            },
            {
                "render": function(data, type, row) {
                    return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                },
                "targets": 6,
                "name": 'grand_total',
                'width': '100px'
            },
            {
                "render": function(data, type, row) {
                    return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                },
                "targets": 7,
                "name": 'grand_total_vnd',
                'width': '100px'
            },
            {
                "targets": 8,
                "name": 'created_by',
                'width': '100px'
            },
            {
                "render": function(data, type, row) {
                    str = '';
                    order_id = row[0];
                    if (!data) return '';
                    data = data.split('__');
                    if (data[0] == "approved") {
                        user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: ' +
                            data[1] + '</div>';
                    } else {
                        user_status = '';
                    }
                    if (data[0] == "un_approved") {
                        str =
                            '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>" data-content="<p><a id=\'agree\' order_id=\'' +
                            order_id +
                            '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' +
                            user_status;
                    } else if (data[0] == "approved") {
                        str =
                            '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>\" data-content="<p><a id=\'agree\' order_id=\'' +
                            order_id +
                            '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' +
                            user_status;
                    }

                    return str;
                },
                "targets": 9,
                "name": 'status',
                'width': '100px'
            },
            {
                "targets": 10,
                "name": 'status_orders',
                'width': '100px',
                'sortable': false,
                'searchable': false
            },
            {
                "targets": 11,
                "name": 'note',
                'width': '100px'
            },
            {
                "render": function(data, type, row) {
                    return data;
                },
                "targets": 12,
                "name": 'workflow_orders',
                'width': '400px',
                'visible': true,
                'searchable': false,
                'sortable': false
            },
            {
                "render": function(data, type, row) {
                    bill = '';
                    if (data == 0) {
                        bill =
                            '<span class="label btn-success"><?= lang('tnh_retail_bill') ?></span>';
                    } else if (data == 1) {
                        bill =
                            '<span class="label btn-danger"><?= lang('tnh_tax_bill') ?></span>';
                    }
                    return bill;
                },
                "targets": 13,
                "name": 'type_bills',
                'width': '80px',
                'visible': false
            },
            {
                "render": function(data, type, row) {
                    str = '';
                    if (data) {
                        str =
                            '<div class="label label-success"><?= lang('ch__status_contract') ?></div><div class="mtop10">' +
                            data + '</div>';
                    } else {
                        str =
                            '<div class="label label-danger"><?= lang('ch__not_status_contract') ?></div>';
                    }
                    return str;
                },
                "targets": 14,
                "name": 'status_contracts',
                'width': '100px'
            },
            {
                "render": function(data, type, row) {
                    var str = '';
                    if (data == 1) {
                        str =
                            '<span class="label btn-warning"><?= lang('invoice_status_not_paid_completely') ?></span>';
                    } else if (data == 2) {
                        str =
                            '<span class="label btn-primary"><?= lang('invoice_status_paid') ?></span>';
                    } else {
                        str =
                            '<span class="label btn-danger"><?= lang('invoice_status_unpaid') ?></span>';
                    }
                    return str;
                },
                "targets": 15,
                "name": 'status_payment',
                'width': '100px',
                'visible': false
            },
            {
                "targets": 16,
                "name": 'branch',
                'width': '150px'
            },
            {
                "targets": 17,
                "name": 'actions',
                'sortable': false,
                'searchable': false,
                'width': '160px',
                'visible': false
            },
            {
                "targets": 18,
                "name": 'id_sort',
                'visible': false
            },
        ],
    });

    calOrders();
    $(document).on('change', '#customer_search, #orders_search, #start_date_search, #end_date_search, #type_orders_search, #status_orders_search, #items_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
        calOrders();
    });

    $('#table-tools_supplies').on('draw.dt', function() {})

    $(document).on('click', '.btn-dt-reload', function(event) {
        // oTable.draw();
    });

    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });

    $(document).on('click', '#agree', function(event) {
        event.preventDefault();
        index = this;
        order_id = $(this).attr('order_id');
        status = $(this).attr('value');
        $(index).attr('disabled', 'disabled');
        $('.po').popover('hide');
        if (order_id) {
            $.ajax({
                    url: site.base_url + 'admin/orders/agree',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        order_id: order_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        if (status == "approved") {
                            user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?></div>';
                        } else {
                            user_status = '';
                        }
                        if (status == "un_approved") {
                            str =
                                '<div style="margin-right: 10px;margin-top: 5px;" class="pull-right mbot5  text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>" data-content="<p><a id=\'agree\' order_id=\'' +
                                order_id +
                                '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' +
                                user_status;
                        } else if (status == "approved") {
                            str =
                                '<div style="margin-right: 10px;margin-top: -3px;" class="pull-right mbot5  text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>\" data-content="<p><a id=\'agree\' order_id=\'' +
                                order_id +
                                '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' +
                                user_status;
                        }
                        var html = str;
                        $('.status_ch').html(html);
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

    $(document).on('click', '.status-custom', function(event) {
        event.preventDefault();
        index = this;
        // divParent = $(this).parents('div');
        // arrStatus = [];
        // listCheck = $(divParent).find('input[name="status_custom[]"]');
        // $.each(listCheck, function(index, el) {
        //     if ($(el).is(":checked"))
        //     {
        //         arrStatus.push($(el).val());
        //     }
        // });
        order_id = $(this).attr('order_id');
        status = $(this).attr('value');
        $(index).attr('disabled', 'disabled');
        $('.po').popover('hide');
        if (order_id) {
            $.ajax({
                    url: site.base_url + 'admin/orders/agreeStatusCustom',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        order_id: order_id,
                        status: status,
                        // arrStatus: arrStatus
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw('false');
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw('false');
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
        }
    });

    $(document).on('click', '.tick-chonse', function(event) {
        divClick = $(this).closest('div');
        divClick.find('input[name="status_custom[]"]').trigger('click');
    });
});
</script>



<script>
function printDiv() {
    var printContents = document.getElementById('cong_modal').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function print_qr_code(id) {
    $.get(admin_url + 'orders/get_modal_orders/' + id, function(data) {
        $('#cong_modal').html(data);
        $('#view_print_qr').modal('show');
        // printDiv();
    })
}
function excelOrdersInformationDetail() {
    customer_search = $('#customer_search').val();
    start_date_search = $('#start_date_search').val();
    end_date_search = $('#end_date_search').val();
    type_orders_search = $('#type_orders_search').val();
    status_orders_search = $('#status_orders_search').val();

    var url_print = site.base_url+'admin/orders/excel_orders_information_detail?customer_search='+customer_search+'&start_date_search='+start_date_search+'&end_date_search='+end_date_search+'&type_orders_search='+type_orders_search+'&status_orders_search='+status_orders_search;
    window.open(url_print, "_blank");
}

function print_pdf_detail(id) {
    url = admin_url + 'orders/print_orders_detail_html/' + id;
    var iframe = document.createElement('iframe');
    // iframe.id = 'pdfIframe'
    iframe.className='pdfIframe'
    document.body.appendChild(iframe);
    iframe.style.display = 'none';
    iframe.onload = function () {
        setTimeout(function () {
            iframe.focus();
            iframe.contentWindow.print();
            URL.revokeObjectURL(url)
            // document.body.removeChild(iframe)
        }, 1);
    };
    iframe.src = url;
}

function exportExcel() {
    customer_search = $("#customer_search").val();
    status_table = $("#status_table").val();
    orders_search = $("#orders_search").val();
    start_date_search = $("#start_date_search").val();
    end_date_search = $("#end_date_search").val();
    type_orders_search = $("#type_orders_search").val();
    status_orders_search = $("#status_orders_search").val();
    items_search = $("#items_search").val();

    $.ajax({
        type: "POST",
        url: site.base_url + 'admin/orders/export_excel',
        data: {
            csrf_token_name: hash,
            customer_search: customer_search,
            status_table: status_table,
            orders_search: orders_search,
            start_date_search: start_date_search,
            end_date_search: end_date_search,
            type_orders_search: type_orders_search,
            status_orders_search: status_orders_search,
            items_search: items_search,
        },
        dataType: "json",
        success: function(response) {
            console.log(response);
            if (response.result) {
                alert_float('success', response.message);
                download(response.filename, response.file);
            } else {
                alert_float('danger', response.message);
            }
        }
    });
}


function exportExcelColumsDetail(id) {
    $.ajax({
        type: "POST",
        url: site.base_url + 'admin/orders/export_excel_colums_detail',
        data: {
            csrf_token_name: hash,
            id: id,
        },
        dataType: "json",
        success: function(response) {
            console.log(response);
            if (response.result) {
                alert_float('success', response.message);
                download(response.filename, response.file);
            } else {
                alert_float('danger', response.message);
            }
        }
    });
}
</script>