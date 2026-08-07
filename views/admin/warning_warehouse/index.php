<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
.bg-group {
    background: #daeaf9;
}
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            </div>
<!--            <div class="pull-right mright5 H_border">-->
<!--                <a onclick="createPurchaseOrder(this)" class="btn btn-info test H_action_button">-->
<!--                    --><?php //echo _l('Đặt hàng'); ?><!--</a>-->
<!--            </div>-->
            <div class="pull-right mright5 H_border">
                <a onclick="createPurchase(this)" class="btn btn-info test H_action_button">
                    <?php echo _l('Tạo YCMH'); ?></a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?= lang('Loại Hàng', 'type_item') ?>
                                <?php $array_type = [
                                    [
                                        'type'=>'materials',
                                        'name'=>'Nguyên vật liệu',
                                    ],
                                    [
                                        'type'=>'semi_products',
                                        'name'=>'Bán thành phẩm(SX)',
                                    ],
                                    [
                                        'type'=>'semi_products_outside',
                                        'name'=>'Bán thành phẩm(MN)',
                                    ],
                                    [
                                        'type'=>'tools_supplies',
                                        'name'=>'Công cụ vật tư',
                                    ]
                            ]; ?>
                                <select name="type_item" id="type_item" data-placeholder="<?= lang('Loại hàng') ?>"
                                    class="modal-select2" style="width: 100%;">
                                    <option value=""></option>
                                    <?php foreach($array_type as $key => $value){?>
                                    <option value="<?= $value['type'] ?>"><?= $value['name'] ?></option>
                                    <?php }?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <?= lang('Tên Hàng', 'name_items') ?>
                                <input type="text" name="name_items" placeholder="<?= lang('Tên hàng') ?>"
                                    id="name_items" class="name_items form-control" style="width: 100%;" value="">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation" class="hide">
                                        <a href="#manu" aria-controls="manu" role="tab" value="manu"
                                            data-toggle="tab"><?= lang('Cần Sản Xuất') ?><span class="manu"></span></a>
                                    </li>
                                    <li role="presentation" class="active">
                                        <a href="#purchase" aria-controls="purchase" role="tab" value="purchase"
                                            data-toggle="tab"><?= lang('Cần Thu Mua') ?><span
                                                class="purchase"></span></a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table"
                                    class="form-control status_table" value="purchase">
                            </div>
                        </div>
                        <div class="table-responsive mtop5">
                            <table id="tb-warning-warehouse"
                                class="table dt-tnh dont-responsive-table mtop0 table-warning-warehouse"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="warning-warehouse"><label
                                                    for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('image') ?></th>
                                        <th class="text-center"><?= lang('name') ?> - <?= lang('code') ?></th>
                                        <th class="text-center"><?= lang('tnh_unit') ?></th>
                                        <th class="text-center"><?= lang('Kế hoạch đơn mới') ?></th>
                                        <th class="text-center"><?= lang('Số lượng tối thiểu') ?></th>
                                        <th class="text-center"><?= lang('Tồn kho') ?></th>
                                        <th class="text-center"><?= lang('Cần mua/sx thêm') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="bold">
                                    <tr>
                                        <td></td>
                                        <td class="text-center"><?= lang('tnh_grand_total') ?></td>
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
<div id="create_purchase_data"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script>
var oTable = '';
var fnserverparams = {
    type_item: '#type_item',
    name_items: '#name_items',
    status_table: '#status_table'
};
$(document).ready(function() {
    $('#type_item').select2({
        'allowClear': true
    });
    oTable = tnhInitDataTable('#tb-warning-warehouse',
        '<?= site_url('admin/warning_warehouse/getItemsWarehouse') ?>', {
            'ordering': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/warning_warehouse/getItemsWarehouse') ?>',
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
                    // $('#tb-warning-warehouse tfoot tr td:nth-child(5)').html(
                    //     `<div class="text-center">${tnhFormatNumber(json.totalQuantityBom)}</div>`);
                    $('#tb-warning-warehouse tfoot tr td:nth-child(5)').html(
                        `<div class="text-center">${tnhFormatNumber(json.totalQuantity)}</div>`);
                    $('#tb-warning-warehouse tfoot tr td:nth-child(6)').html(
                        `<div class="text-center">${tnhFormatNumber(json.totalQuantityInventory)}</div>`
                    );
                    $('#tb-warning-warehouse tfoot tr td:nth-child(7)').html(
                        `<div class="text-center">${tnhFormatNumber(json.totalQuantityPurchase)}</div>`
                    );
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
                if (data[0] === 'group') {
                    $('td:eq(0)', row).attr('colspan', 8);
                    $('td:eq(1)', row).css('display', 'none');
                    $('td:eq(2)', row).css('display', 'none');
                    $('td:eq(3)', row).css('display', 'none');
                    $('td:eq(4)', row).css('display', 'none');
                    $('td:eq(5)', row).css('display', 'none');
                    $('td:eq(6)', row).css('display', 'none');
                    $('td:eq(7)', row).css('display', 'none');
                    this.api().cell($('td:eq(0)', row)).data(data[1]);
                    $(row).addClass('bg-group bold');
                }
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 0,
                    "name": 'id',
                    'width': '50px'
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 1,
                    "name": 'images',
                    'width': '100px'
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 4,
                    "name": 'quantity_bom',
                    'width': '120px',
                    "visible": false
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 5,
                    "name": 'quantity',
                    'width': '120px'
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 7,
                    "name": 'quantity_purchase',
                    'width': '120px'
                },
            ]
        });
});
$(document).on('change', '#type_item, #name_items', function() {
    oTable.draw();
    // total_limit();
});
$(document).on('keyup', '#name_items', function() {
    oTable.draw();
    // total_limit();
});
$(document).on('click', '.status-table li a', function(event) {
    status_table = $(this).attr('value');
    $('#status_table').val(status_table);
    oTable.draw();
    // total_limit();
});
// total_limit();

function total_limit() {
    dataString = {
        [csrfData['token_name']]: csrfData['hash'],
        type_item: $("#type_item").val(),
        name_items: $("#name_items").val(),
    };
    jQuery.ajax({
        type: "post",
        url: "<?=admin_url()?>warning_warehouse/count_all/",
        data: dataString,
        cache: false,
        success: function(data) {
            data = JSON.parse(data);
            $('.manu').html(tnhFormatNumber(data.manu));
            $('.purchase').html(tnhFormatNumber(data.purchase));
        }
    });
}

function createPurchase() {
    var ids = '';

    var rows = $('.table-warning-warehouse').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') == true) {
            ids += checkbox.val() + ',';
        }
    });
    if (!ids) {
        bootbox.alert('Xin vui lòng mặt hàng cần tạo YCMH');
        return;
    }
    if (ids) {
        $("#create_purchase_data").html('');
        $.ajax({
                url: site.base_url + 'admin/warning_warehouse/createPurchase',
                type: 'POST',
                dataType: 'html',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    ids: ids,
                },
            })
            .done(function(data) {
                $("#create_purchase_data").html(data);
                $('#create_purchase_modal').modal('show');
            })
            .fail(function(data) {
                alert_float('danger', 'errors');
            })
    }
}

function createPurchaseOrder() {
    var ids = '';

    var rows = $('.table-warning-warehouse').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') == true) {
            ids += checkbox.val() + ',';
        }
    });
    if (!ids) {
        bootbox.alert('Xin vui lòng mặt hàng cần tạo đặt hàng');
        return;
    }
    if (ids) {
        $("#create_purchase_data").html('');
        $.ajax({
                url: site.base_url + 'admin/warning_warehouse/createPurchaseOrder',
                type: 'POST',
                dataType: 'html',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    ids: ids,
                },
            })
            .done(function(data) {
                $("#create_purchase_data").html(data);
                $('#create_purchase_order_modal').modal('show');
            })
            .fail(function(data) {
                alert_float('danger', 'errors');
            })
    }
}
</script>