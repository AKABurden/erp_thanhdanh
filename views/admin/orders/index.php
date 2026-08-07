<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
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
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
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
                        <a class="test btn-search-tnh" onclick="excelOrdersInformationDetail()"
                            aria-expanded="true"><i class="fa fa-file-excel-o"></i> <?= lang('Xuất excel') ?></a>
                    </li>
                    <?php if ($this->perAddOrders): ?>
                    <!-- <li>
                        <a href="<?= ''//base_url('admin/orders/import_orders') ?>" class="">
                            <i class="fa fa-upload"></i>
                            <?php ''//echo _l('tnh_import_orders'); ?>
                        </a>
                    </li> -->
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
                    <div class="col-md-3">
                        <?= lang('tnh_reference_orders', 'orders_search') ?>
                        <input type="text" name="orders_search" id="orders_search" style="width: 100%;"
                            data-placeholder="<?= lang('tnh_reference_orders') ?>" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>"
                            id="customer_search" class="customer_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                            id="start_date_search" class="start_date_search datepicker form-control"
                            style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                            id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                            value="">
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
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all"
                                            data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                    </li>
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
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="orders-new"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_orders') ?></th>
                                        <th><?= lang('customers') ?></th>
                                        <th><?= lang('tnh_address') ?></th>
                                        <th><?= lang('tnh_grand_total') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_agree') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('tnh_view') ?></th>
                                        <th><?= lang('tnh_status_warehouse') ?></th>
                                        <th><?= lang('tnh_type_bills') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_wordflow_orders') ?></th>
                                        <th><?= lang('tnh_status_contract') ?></th>
                                        <th><?= lang('tnh_status_payment') ?></th>
                                        <th><?= lang('h_branch') ?></th>
                                        <th><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
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
    end_date_search: '#end_date_search'
};
var oTable = '';
var statusOrders = <?= json_encode(statusOrders()) ?>;

function calOrders() {
    order_id = $('#orders_search').val();
    customer_id = $('#customer_search').val();
    start_date = $('#start_date_search').val();
    end_date = $('#end_date_search').val();

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
    ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
    ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);

    oTable = tnhDatatable(
        '#table-orders', {
            'order': [
                [1, 'desc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'fixedHeader': {
                header: true,
            },
            "responsive": true,
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/orders/getOrders') ?>',
            'fnServerData': function(sSource, aoData, fnCallback) {
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
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "rowCallback": function(row, data) {
                ckV = data[8];
                if (ckV > 0) {
                    $(row).find('.td-reference-no').html('<span class="wap-new">new</span>');
                }
                st = data[7];
                if (st != 'approved') {
                    $(row).find('.tnh-bill').addClass('tnh-disabled');
                    $(row).find('.cvc').addClass('tnh-disabled');
                    $(row).find('.tnh-convert-delivery').addClass('tnh-disabled');
                    $(row).find('.tnh-add-payment').addClass('tnh-disabled');
                }
                taxBill = data[11];
                if (taxBill == 1) {
                    $(row).find('.tnh-bill').addClass('tnh-disabled');
                }
                contract = data[14];
                if (contract) {
                    $(row).find('.cvc').addClass('tnh-disabled');
                }

                workflow_orders = data[13];
                arrData = workflow_orders.split('||');
                if (arrData[0]) {
                    $(row).find('.tnh-add-production-plan').addClass('hide');
                }
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
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
                    "name": 'date',
                    'width': '100px',
                    'searchable': false
                },
                {
                    "render": function(data, type, row) {
                        var str = '';
                        data = data.split('___');
                        if (data[1]) {
                            str =
                                '<div class="mbot5"><span class="label btn-danger"><?= lang('tnh_hold_the_goods') ?>: ' +
                                data[1] + '</span></div>'
                        }
                        return '<div style="min-width: 150px;" class="">\
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url +
                            'admin/orders/view_order/' + row[0] +
                            '" data-toggle="modal" data-target="#myModal">' + data[0] + '</a>\
                            </div>' + str + '<div class="td-reference-no"></div>';
                    },
                    "targets": 2,
                    "name": 'reference_no',
                    'width': '150px'
                },
                {
                    "targets": 3,
                    "name": 'customer_name',
                    'width': '150px'
                },
                {
                    "targets": 4,
                    "name": 'address_delivery',
                    'width': '150px'
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 5,
                    "name": 'grand_total',
                    'width': '100px'
                },
                {
                    "targets": 6,
                    "name": 'created_by',
                    'width': '100px'
                },
                {
                    "render": function(data, type, row) {
                        str = '';
                        order_id = row[0];
                        if (data == "approved") {
                            user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: ' +
                                row[8] + '</div>';
                        } else {
                            user_status = '';
                        }
                        if (data == "un_approved") {
                            str =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>" data-content="<p><a id=\'agree\' order_id=\'' +
                                order_id +
                                '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' +
                                user_status;
                        } else if (data == "approved") {
                            str =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>\" data-content="<p><a id=\'agree\' order_id=\'' +
                                order_id +
                                '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' +
                                user_status;
                        }

                        return str;
                    },
                    "targets": 7,
                    "name": 'status',
                    'width': '100px'
                },
                {
                    "targets": 8,
                    "name": 'user_status',
                    'visible': false
                },
                {
                    "targets": 9,
                    "name": 'list_users',
                    'visible': false,
                    'searchable': false
                },
                {
                    "render": function(data, type, row) {
                        var str = '';
                        if (data > 0) {
                            str =
                                '<span class="label label-danger"><?= lang('tnh_not_stock') ?></span>';
                        } else {
                            str =
                                '<span class="label label-success"><?= lang('tnh_ok_stock') ?></span>';
                        }
                        return '<div class="text-center">' + str + '</div>';
                    },
                    "targets": 10,
                    "name": 'status_warehouses',
                    'width': '100px',
                    'sortable': false,
                    'searchable': false
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
                    "targets": 11,
                    "name": 'type_bills',
                    'width': '80px',
                    'visible': false
                },
                {
                    "targets": 12,
                    "name": 'note',
                    'width': '100px'
                },
                {
                    "render": function(data, type, row) {
                        var lkhsx = "";
                        var dsxtcty = "";
                        var sxx = "";
                        var gh = "";
                        var xkgh = "";
                        var xgc = "";
                        var ngc = "";

                        var linkLkhsx = '';
                        var linkDsxtcty = '';
                        var linkSxx = '';
                        var linkSkgh = '';
                        var linkXgc = '';
                        var linkNgc = '';
                        var linkGh = '';
                        if (data) {
                            arrData = data.split('||');
                            if (arrData[0]) {
                                lkhsx = "active";
                                linkLkhsx = arrData[0];
                            }
                            if (arrData[1]) {
                                dsxtcty = "active";
                                linkDsxtcty = arrData[1];
                            }
                            if (arrData[2]) {
                                sxx = "active";
                                linkSxx = arrData[2];
                            }
                            if (arrData[6] == '-1') {
                                linkGh =
                                    '<span class="inline-block label label-danger"><?= lang('tnh_cg') ?></span>';
                            }
                            if (arrData[6] >= 0) {
                                gh = "active";
                                if (arrData[6] == 0) {
                                    linkGh =
                                        '<span class="inline-block label label-primary"><?= lang('tnh_dgd') ?></span>';
                                } else if (arrData[6] > 0) {
                                    linkGh =
                                        '<span class="inline-block label label-warning"><?= lang('tnh_g1p') ?></span>';
                                }
                                // linkGh = arrData[6];
                            }
                            // if (arrData[3]) {
                            //     xkgh = "active";
                            //     linkSkgh = arrData[3];
                            // }
                            if (arrData[4]) {
                                xgc = "active";
                                linkXgc = arrData[4];
                            }
                            if (arrData[5]) {
                                ngc = "active";
                                linkNgc = arrData[5];
                            }
                        }
                        var str = '<ul class="progressbar" style="display: flex;">' +
                            '<li class="' + lkhsx + '">' +
                            '<div><?= lang('tnh_lkhsx') ?></div>' +
                            '<div>' + linkLkhsx + '</div>' +
                            '</li>' +
                            '<li class="' + dsxtcty + '">' +
                            '<div><?= lang('tnh_dsxtcty') ?></div>' +
                            '<div>' + linkDsxtcty + '</div>' +
                            '</li>' +
                            '<li class="' + sxx + '">' +
                            '<div><?= lang('tnh_sxx') ?></div>' +
                            '<div>' + linkSxx + '</div>' +
                            '</li>' +
                            '<li class="' + xgc + '">' +
                            '<div><?= lang('tnh_xgcn') ?></div>' +
                            '<div>' + linkXgc + '</div>' +
                            '</li>' +
                            '<li class="' + ngc + '">' +
                            '<div><?= lang('tnh_ngcn') ?></div>' +
                            '<div>' + linkNgc + '</div>' +
                            '</li>' +
                            '<li class="' + gh + '">' +
                            '<div><?= lang('tnh_gh') ?></div>' +
                            '<div>' + linkGh + '</div>' +
                            '</li>' +
                            '</ul>';
                        return str;
                    },
                    "targets": 13,
                    "name": 'workflow_orders',
                    'width': '400px',
                    'visible': true,
                    'searchable': false,
                    'sortable': false
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
                    'width': '100px'
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
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                var grand_total = 0;
                for (var i = 0; i < aaData.length; i++) {
                    grand_total += intVal(aaData[i][5]);
                }
                var nCells = nRow.getElementsByTagName('td');
                nCells[5].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(grand_total) +
                    '</div>';
            }
        }
    );

    calOrders();
    $(document).on('change', '#customer_search, #orders_search, #start_date_search, #end_date_search', function(
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
</script>