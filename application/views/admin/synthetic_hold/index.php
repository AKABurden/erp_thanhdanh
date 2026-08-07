<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .bg-group {
        background: #daeaf9;
    }

    .table-view_warehouse_machine_import tr td:nth-child(3) {
        width: 100px;
        white-space: unset;
        text-align: center;
    }

    .tag-cs-red {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid red;
        color: red;
    }

    .tag-cs-color {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #2886e7;
        color: #2886e7;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <a onclick="prin_pdf()" class="btn btn-info pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('In phiếu'); ?>
            </a>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3" style="margin-bottom: 10px">
                                    </div>

                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                        <?php foreach ($arrTab as $key => $value): ?>
                                            <li role="presentation" class="<?= $key == 0 ? 'active' : '' ?>">
                                                <a href="#<?= $value['id'] ?>" aria-controls="<?= $value['id'] ?>" role="tab"
                                                   value="<?= $value['id'] ?>"
                                                   data-toggle="tab"><?= $value['name'] ?></a>
                                            </li>
                                        <?php endforeach ?>
                                    <input type="hidden" name="status_table" id="status_table"
                                           class="form-control status_table" value="<?= $arrTab[0]['id'] ?>">
                                </div>
                            </div>
                            <div class="table-hold">
                                <div class="row">
                                    <div class="col-md-3" style="margin-bottom: 10px">
                                        <label for="product_search" class="control-label"><?= _l('Thành phẩm') ?></label>
                                        <input type="text" name="product_search" id="product_search" class="product_search" style="width: 100%;" data-placeholder="thành phẩm" value="">
                                    </div>
                                    <div class="col-md-3" style="margin-bottom: 10px">
                                        <label for="orders_search" class="control-label"><?= _l('Đơn hàng') ?></label>
                                        <input type="text" name="orders_search" id="orders_search" class="orders_search" style="width: 100%;" data-placeholder="đơn hàng" value="">
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
                            <table id="table-synthetic-hold" class="table dt-tnh table-synthetic-hold-new" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th class="text-center"><?= lang('STT') ?></th>
                                    <th class="text-center"><?= lang('Ngày giữ') ?></th>
                                    <th class="text-center"><?= lang('Mã phiếu') ?></th>
                                    <th class="text-center"><?= lang('dt_product_code') ?></th>
                                    <th class="text-center"><?= lang('dt_product_name') ?></th>
                                    <th class="text-center"><?= lang('Kho hàng') ?></th>
                                    <th class="text-center"><?= lang('Khách hàng') ?></th>
                                    <th class="text-center"><?= lang('Số đơn hàng') ?></th>
                                    <th class="text-center"><?= lang('Số lượng giữ') ?></th>
                                    <th class="text-center"><?= lang('Số lượng giữ còn trong kho') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="99"></td>
                                </tr>
                                </tbody>
                            </table>
                            </div>
                            <div class="table-hold-plan hide">
                                <div class="row">
                                    <div class="col-md-2" style="margin-bottom: 10px">
                                        <label for="items_search" class="control-label"><?= _l('Nguyên phụ liệu') ?></label>
                                        <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="Nguyên phụ liệu" value="">
                                    </div>
                                    <div class="col-md-2" style="margin-bottom: 10px">
                                        <label for="plan_search" class="control-label"><?= _l('Kế hoạch NPL') ?></label>
                                        <input type="text" name="plan_search" id="plan_search" class="plan_search" style="width: 100%;" data-placeholder="Kế hoạch NPL" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('productions_orders', 'productions_orders_search') ?>
                                        <input type="text" name="productions_orders_search" id="productions_orders_search" class="productions_orders_search" value="" style="width: 100%;" data-placeholder="<?= lang('productions_orders') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('start_date', 'start_date_search_new') ?>
                                        <input type="text" name="start_date_search_new" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search_new" class="start_date_search_new datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search_new') ?>
                                        <input type="text" name="end_date_search_new" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search_new" class="end_date_search_new datepicker form-control" style="width: 100%;"
                                               value="">
                                    </div>
                                </div>
                                <table id="table-synthetic-hold-plan" class="table dt-tnh table-synthetic-hold-plan-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Ngày giữ') ?></th>
                                        <th class="text-center"><?= lang('Mã phiếu') ?></th>
                                        <th class="text-center"><?= lang('dt_nvl_code') ?></th>
                                        <th class="text-center"><?= lang('dt_nvl_name') ?></th>
                                        <th class="text-center"><?= lang('Kho hàng') ?></th>
                                        <th class="text-center"><?= lang('Số kế hoạch NPL') ?></th>
                                        <th class="text-center"><?= lang('Lệnh sản xuất tổng') ?></th>
                                        <th class="text-center"><?= lang('Số lượng giữ') ?></th>
                                        <th class="text-center"><?= lang('Số lượng giữ còn trong kho') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTableItems = '';
    var oTableItemsPlan = '';

    var fnserverparamsitems = {
        status_table: '#status_table',
        product_search: '#product_search',
        orders_search: '#orders_search',
        plan_search: '#plan_search',
        items_search: '#items_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        start_date_search_new: '#start_date_search_new',
        end_date_search_new: '#end_date_search_new',
        productions_orders_search: '#productions_orders_search',
    };

    function loadTable() {
        oTableItems = tnhInitDataTable('#table-synthetic-hold',
            '<?= site_url('admin/synthetic_hold/getSyntheticHolds') ?>', {
                'order': [
                    [0, 'asc'],
                ],
                'fixedHeader': {
                    header: true,
                },
                "ajax": {
                    "url": '<?= site_url('admin/synthetic_hold/getSyntheticHolds') ?>',
                    "type": "POST",
                    "data": function(d) {
                        if (typeof(csrfData) !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in fnserverparamsitems) {
                            d[key] = $(fnserverparamsitems[key]).val();
                        }
                        if (table.attr('data-last-order-identifier')) {
                            d['last_order_identifier'] = table.attr('data-last-order-identifier');
                        }
                    },
                    "dataSrc": function(json) {
                        return json.aaData;
                    }
                },
                "createdRow": function(row, data, index) {
                },
                "columnDefs": [
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:left">${data}</div>`;
                        },
                        "targets": 0,
                        "name": 'stt',
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:left">${data}</div>`;
                        },
                        "targets": 1,
                        "name": 'code',
                        'width': '80px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data + '</div>';
                        },
                        "targets": 2,
                        "name": 'name',
                        'width': '80px',
                    },
                    {
                        "targets": 3,
                        "name": 'customer_id',
                        'width': '120px'
                    },
                    {
                        "targets": 4,
                        'width': '100px'
                    },
                    {
                        "targets": 5,
                        'width': '120px'
                    },
                    {
                        "targets": 6,
                        'width': '120px'
                    },
                    {
                        "targets": 7,
                        'width': '80px'
                    },
                    {
                        "targets": 8,
                        'width': '50px'
                    },
                    {
                        "targets": 9,
                        'width': '50px'
                    },
                ],
            });
    }


    function loadTablePlan() {
        oTableItemsPlan = tnhInitDataTable('#table-synthetic-hold-plan',
            '<?= site_url('admin/synthetic_hold/getSyntheticHoldPlan') ?>', {
                'order': [
                    [0, 'asc'],
                ],
                'fixedHeader': {
                    header: true,
                },
                "ajax": {
                    "url": '<?= site_url('admin/synthetic_hold/getSyntheticHoldPlan') ?>',
                    "type": "POST",
                    "data": function(d) {
                        if (typeof(csrfData) !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in fnserverparamsitems) {
                            d[key] = $(fnserverparamsitems[key]).val();
                        }
                        if (table.attr('data-last-order-identifier')) {
                            d['last_order_identifier'] = table.attr('data-last-order-identifier');
                        }
                    },
                    "dataSrc": function(json) {
                        return json.aaData;
                    }
                },
                "createdRow": function(row, data, index) {
                },
                "columnDefs": [
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:left">${data}</div>`;
                        },
                        "targets": 0,
                        "name": 'stt',
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:left">${data}</div>`;
                        },
                        "targets": 1,
                        "name": 'code',
                        'width': '80px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data + '</div>';
                        },
                        "targets": 2,
                        "name": 'name',
                        'width': '80px',
                    },
                    {
                        "targets": 3,
                        "name": 'order',
                        'width': '120px'
                    },
                    {
                        "targets": 4,
                        'width': '120px'
                    },
                    {
                        "targets": 5,
                        'width': '120px'
                    },
                    {
                        "targets": 6,
                        'width': '70px'
                    },
                    {
                        "targets": 7,
                        'width': '70px'
                    },
                    {
                        "targets": 8,
                        'width': '50px'
                    },
                    {
                        "targets": 9,
                        'width': '70px'
                    },
                ],
            });
    }

    $(document).ready(function() {
        $('#type').select2({
            'allowClear': true
        });
        $('#type_items').select2({
            'allowClear': true
        });
        ajaxSelectParams($('#product_search'), 'admin/products/searchProductAndGoodsMaterials', 0,true,true);
        ajaxSelectParams($('#items_search'), 'admin/synthetic_hold/searchItemsNew', 0,true,true);
        ajaxSelectParams($('#plan_search'), 'admin/synthetic_hold/searchProductionsPlanNew', 0,true,true);
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true)
        loadTable();
        ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
    });

    $(document).on('change',
        '#product_search,#orders_search,#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTableItems.draw();
        });
    $(document).on('change',
        '#plan_search,#items_search,#end_date_search_new,#start_date_search_new,#productions_orders_search',
        function(
            event) {
            event.preventDefault();
            oTableItemsPlan.draw();
        });
    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        if (status_table == 1){
            $(".table-hold-plan").addClass('hide');
            $(".table-hold").removeClass('hide');
            if (typeof oTableItems != 'undefined' && oTableItems != '') {
                oTableItems.draw();
            } else {
                loadTable();
            }
        } else {
            $(".table-hold-plan").removeClass('hide');
            $(".table-hold").addClass('hide');
            if (typeof oTableItemsPlan != 'undefined' && oTableItemsPlan != '') {
                oTableItemsPlan.draw();
            } else {
                loadTablePlan();
            }
        }
    });
    $('.btn-search').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-value');
        $('input[name="status_table"]').val(value);
        $('input[name="status_table"]').change();
        oTableItems.draw();
    });

    $('#table-synthetic-order').on('draw.dt', function() {
    });

    function prin_pdf(){
        status_table = $("#status_table").val();
        if (status_table == 1){
            product_search = $("#product_search").val();
            orders_search = $("#orders_search").val();
            start_date_search = $("#start_date_search").val();
            end_date_search = $("#end_date_search").val();
            window.open(site.base_url + 'admin/synthetic_hold/print_pdf?type=' + status_table + '&product_search=' + product_search +
                '&orders_search=' + orders_search + '&start_date_search=' + start_date_search + '&end_date_search=' + end_date_search, "_blank");
        } else {
            product_search = $("#items_search").val();
            plan_search = $("#plan_search").val();
            start_date_search_new = $("#start_date_search_new").val();
            end_date_search_new = $("#end_date_search_new").val();
            productions_orders_search = $("#productions_orders_search").val();
            window.open(site.base_url + 'admin/synthetic_hold/print_pdf_plan?type=' + status_table + '&product_search=' + product_search +
                '&plan_search=' + plan_search + '&start_date_search_new=' + start_date_search_new + '&end_date_search_new=' + end_date_search_new + '&productions_orders_search=' + productions_orders_search, "_blank");
        }
    }
</script>