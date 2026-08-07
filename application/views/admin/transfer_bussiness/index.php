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
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3" style="margin-bottom: 10px">
                                    <label for="product_search" class="control-label"><?= _l('Thành phẩm') ?></label>
                                    <input type="text" name="product_search" id="product_search" class="product_search" style="width: 100%;" data-placeholder="thành phẩm" value="">
                                </div>
                                <div class="col-md-3" style="margin-bottom: 10px">
                                    <label for="orders_search" class="control-label"><?= _l('Đơn hàng') ?></label>
                                    <input type="text" name="orders_search" id="orders_search" class="orders_search" style="width: 100%;" data-placeholder="đơn hàng" value="">
                                </div>
                                <div class="col-md-2" style="margin-bottom: 10px">
                                    <label for="business_search" class="control-label"><?= _l('Kế hoạch thành phẩm') ?></label>
                                    <input type="text" name="business_search" id="business_search" class="business_search" style="width: 100%;" data-placeholder="Kế hoạch thành phẩm" value="">
                                </div>
                                <div class="col-md-2" style="margin-bottom: 10px">
                                    <?= lang('Chi nhánh', 'branch_search') ?>
                                    <select name="branch_search" id="branch_search" class="branch_search"  data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                        <option value=""></option>
                                        <?php if (!empty($branch)) { ?>
                                            <?php foreach ($branch as $key => $value) { ?>
                                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                                    <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-hold">
                                <table id="table-tranfer-business" class="table dt-tnh table-tranfer-business-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Ngày giữ') ?></th>
                                        <th class="text-center"><?= lang('Mã phiếu') ?></th>
                                        <th class="text-center"><?= lang('Đơn hàng') ?></th>
                                        <th class="text-center"><?= lang('Nguời tạo') ?></th>
                                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                                        <th class="text-center"><?= lang('Số LSX') ?></th>
                                        <th class="text-center"><?= lang('Tác vụ') ?></th>
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
    var oTable = '';

    var fnserverparamsitems = {
        product_search: '#product_search',
        orders_search: '#orders_search',
        business_search: '#business_search',
        branch_search: '#branch_search',
        productions_orders_search: '#productions_orders_search'
    };

    function loadTable() {
        oTable = tnhInitDataTable('#table-tranfer-business',
            '<?= site_url('admin/transfer_bussiness/getTranferBusiness') ?>', {
                'order': [
                    [0, 'desc'],
                ],
                'fixedHeader': {
                    header: true,
                },
                "ajax": {
                    "url": '<?= site_url('admin/transfer_bussiness/getTranferBusiness') ?>',
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
                        "name": 'date',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data + '</div>';
                        },
                        "targets": 2,
                        "name": 'code',
                        'width': '100px',
                    },
                    {
                        "targets": 3,
                        "name": 'order',
                        'width': '120px'
                    },
                    {
                        "targets": 4,
                        "name": 'created_by',
                        'width': '120px'
                    },
                    {
                        "targets": 5,
                        "name": 'status',
                        'width': '100px'
                    },
                    {
                        "targets": 6,
                        "name": 'note',
                        'width': '150px'
                    },
                    {
                        "targets": 7,
                        'width': '120px',
                        "name": 'reference_po',
                        'sortable': false,
                        'searchable': false,
                    },
                    {
                        "targets": 8,
                        'width': '120px',
                        'sortable': false,
                        'searchable': false,
                    },
                ],
            });
    }

    $(document).ready(function() {
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        $('#type_items').select2({
            'allowClear': true
        });
        $('#branch_search').select2({
            'allowClear': true
        });
        ajaxSelectParams($('#product_search'), 'admin/products/searchProductAndGoodsMaterials', 0,true,true);
        ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        ajaxSelectParams('#business_search', 'admin/transfer_bussiness/searchBusiness', 0, true, true);
        loadTable();
    });

    $(document).on('change',
        '#product_search, #orders_search, #business_search, #end_date_search, #start_date_search, #branch_search, #productions_orders_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        if (status_table == 1){
            $(".table-hold-plan").addClass('hide');
            $(".table-hold").removeClass('hide');
            if (typeof oTableItems != 'undefined' && oTable != '') {
                oTable.draw();
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
        oTable.draw();
    });

</script>