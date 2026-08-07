<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=2.5') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a href="javascript:void(0)" onclick="addPurchasePlan()" class="btn btn-info pull-right mright5 H_action_button" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('Tạo mua hàng'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="from-group">
                        <?= lang('productions_plan', 'productions_plan_search') ?>
                        <select name="productions_plan_search[]" id="productions_plan_search"  data-live-search="true" data-none-selected-text="<?= lang('productions_plan') ?>" data-actions-box="true" class="form-control ajax-search" multiple>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="from-group">
                        <?= lang('materials', 'materials_search') ?>
                        <input type="text" name="materials_search" id="materials_search" class="form-control materials_search" placeholder="<?= lang('materials') ?>" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="from-group">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" autocomplete="off" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="from-group">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" autocomplete="off" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel-body">
                    <?php echo $this->load->view('admin/alert') ?>
                    <div class="">
                        <table id="table-productions-plan-purchases" class="table dt-tnh table-hover table-condensed table-productions-plan-purchases" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-left" style="width: 20px;">
                                        <div class="">
                                            <input type="checkbox" id="check-all" onchange="checkALLItems(this)" value="1">
                                            <label for="check-all"></label>
                                        </div>
                                    </th>
                                    <th class="text-center"><?= lang('tnh_materials_code') ?></th>
                                    <th class="text-center"><?= lang('tnh_materials_name') ?></th>
                                    <th class="text-center"><?= lang('tnh_standard_unit') ?></th>
                                    <th class="text-center"><?= lang('Số lượng kế hoạch') ?></th>
                                    <th class="text-center"><?= lang('Số lượng kho') ?></th>
                                    <th class="text-center"><?= lang('Số lượng đã giữ') ?></th>
                                    <th class="text-center"><?= lang('Số lượng đã YC') ?></th>
                                    <th class="text-center"><?= lang('Số lượng đã nhập') ?></th>
                                    <th class="text-center"><?= lang('Số lượng còn lại') ?></th>
                                    <th class="text-center"><?= lang('Tồn tổng') ?></th>
                                    <th class="text-center"><?= lang('SL tồn thực tế') ?></th>
                                    <th class="text-center"><?= lang('SL tồn cho phép') ?></th>
                                    <th class="text-center"><?= lang('SL tồn đã mua<br>(chưa nhập kho)') ?></th>
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
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script>
    var oTable = '';
    var fnserverparams = {
        productions_plan_search: '#productions_plan_search',
        materials_search: '#materials_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };

    var arrItemId = {};

    function checkALLItems(_this) {
        iLength = $('input[name="item_id[]"]').length;
        cCheck = $(_this).prop('checked');
        $('input[name="item_id[]"]').prop('checked', cCheck);
        if (iLength) {
            $.each($('input[name="item_id[]"]'), function(index, value) {
                isCheck = $(value).prop('checked');
                item_id = $(value).val();
                if (isCheck) {
                    arrItemId[item_id] = 1;
                } else {
                    arrItemId[item_id] = 0;
                }
            });
        }
    }

    function changeCheckboxItem(_this) {
        isCheck = $(_this).prop('checked');
        item_id = $(_this).val();
        if (isCheck) {
            arrItemId[item_id] = 1;
        } else {
            arrItemId[item_id] = 0;
        }
    }

    function addPurchasePlan() {
        dataItem = [];
        $.each(arrItemId, function (index, value) { 
            if (value == 1) {
                dataItem.push(index);
            }
        });

        if (dataItem.length == 0) {
            alert_float('danger', 'Vui lòng chọn mặt hàng để tạo mua hàng');
            return;
        }

        var dataPOST = {};
        dataPOST['productions_plan_id'] = $('#productions_plan_search').val();
        dataPOST['dataItem'] = dataItem;
        dataPOST[csrfData['token_name']] = csrfData['hash'];

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufactures_temp/addPurchasePlan',
            data: dataPOST,
            dataType: "html",
            success: function (response) {
                $('#tnhModal').html(response);
                $('#tnhModal').modal({backdrop: 'static', keyboard: true});
            }
        });
    }

    $(document).ready(function() {
        selectAjax($('select#productions_plan_search'), false, 'admin/manufactures_temp/searchProductionsPlan');

        oTable = tnhInitDataTable('#table-productions-plan-purchases', '', {
            'ordering': false,
            // 'fixedHeader': {
            //     header: true,
            // },
            'searching': false,
            "ajax": {
                "url": '<?= site_url('admin/manufactures_temp/getPurchasePlan') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(5)').html('<div class="text-center">' + tnhFormatMoney(json.totalQuantityPlan) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(6)').html('<div class="text-center">' + tnhFormatMoney(json.totalQuantityWarehouse) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(7)').html('<div class="text-center">' + tnhFormatMoney(json.totalQuantitytransfer) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(8)').html('<div class="text-center">' + tnhFormatMoney(json.totalQuantityPurchase) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(9)').html('<div class="text-center">' + tnhFormatMoney(json.totalQuantityimport) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(10)').html('<div class="text-center">' + tnhFormatMoney(json.totalQuantityRest) + '</div>');

                    $('#table-productions-plan-purchases tfoot tr td:nth-child(11)').html('<div class="text-center">' + tnhFormatMoney(json.totalTonTong) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(12)').html('<div class="text-center">' + tnhFormatMoney(json.totalTonThucTe) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(13)').html('<div class="text-center">' + tnhFormatMoney(json.totalTonChoPhep) + '</div>');
                    $('#table-productions-plan-purchases tfoot tr td:nth-child(14)').html('<div class="text-center">' + tnhFormatMoney(json.totalTonDaMua) + '</div>');
                    return json.aaData;
                }
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                cur_item_id = $(nRow).find('input[name="item_id[]"]').val();
                if (typeof arrItemId[cur_item_id] !== 'undefined' && arrItemId[cur_item_id] === 1) {
                    $(nRow).find('input[name="item_id[]"]').prop('checked', true);
                }
                return nRow;
            },
            "columnDefs": [
                {
                    "targets": 3,
                    "name": 'quantity_primary',
                    'searchable': false,
                },
                {
                    "targets": 4,
                    "name": 'quantity_inventory',
                    'searchable': false,
                },
                {
                    "targets": 5,
                    "name": 'quantity_purchase',
                    'searchable': false,
                },
                {
                    "targets": 6,
                    "name": 'quantity_rest',
                    'searchable': false,
                },
            ],
        });

        $('#productions_plan_search, #materials_search, #start_date_search, #end_date_search').change(function(event) {
            oTable.draw();
        })
    });
</script>