<?php init_head(); ?>
<style type="text/css">
    .item-items .ui-sortable tr td input {
        width: 80px;
    }

    .select2-chosen {
        word-wrap: break-word !important;
        text-overflow: inherit !important;
        white-space: normal !important;
    }

    .select2-choice {
        height: 100% !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), array('id' => 'inventory-form', 'class' => '_transaction_form invoice-form'));
            if (isset($invoice)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="additional"></div>
                    <div class="panel-body">
                        <?php
                        $type = '';
                        if (!isset($items))
                            $type = 'warning';
                        elseif ($items->status == 0)
                            $type = 'warning';
                        elseif ($items->status == 1)
                            $type = 'info';
                        elseif ($items->status == 2)
                            $type = 'success';

                        ?>
                        <div class="ribbon <?= $type ?>" project-status-ribbon-2="">
                            <?php
                            if (isset($items)) {
                                $status = format_import_status($items->status, '', false);
                            } else {
                                $status = format_import_status(-1, '', false);
                            }
                            ?>
                            <span><?= $status ?></span>
                        </div>
                        <?php
                        $disabled = array();
                        $readonly = array();
                        if (isset($items)) {
                            $disabled = array('disabled' => true);
                            $readonly = array('readonly' => true);
                        } ?>
                        <h4 class="bold no-margin font-medium">
                            <?php echo $title; ?>
                        </h4>
                        <hr />
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="alert alert-warning text-center total_debt hide"></div>
                            <div class="panel panel-primary">
                                <div class="panel-heading"><?= _l('lead_general_info') ?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_code_p'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <?php echo (isset($items) ? ($items->prefix) : get_option('prefix_inventory')); ?>-</span>
                                                            <?php
                                                            $number = sprintf('%06d', ch_getMaxID('id', 'tblinventory') + 1);
                                                            $value = (isset($items) ? ($items->code) : $number);
                                                            ?>
                                                            <input type="text" name="code" class="form-control" value="<?= $value ?>" readonly>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <label for="date" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_date_p'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                                                    <?php echo render_date_input('date', '', $value, $readonly); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="suppliers_id" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('warehouse'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $value = (isset($items) ? $items->warehouse_id : '');
                                                    echo render_select('warehouse_id', $warehouse, array('id', 'name', 'code'), '', $value, $disabled);
                                                    ?>
                                                    <input type="hidden" id="warehouse_idd" name="warehouse_idd" value="" />
                                                </td>
                                                <td>
                                                    <label for="type_items" class="control-label">
                                                        <?php echo _l('ch_type'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $type_items_new = array(
                                                        array(
                                                            'type' => '-1',
                                                            'name' => 'Tất cả',
                                                        ),
                                                        array(
                                                            'type' => 'product',
                                                            'name' => 'Thành phẩm',
                                                        ),
                                                        array(
                                                            'type' => 'semi_products',
                                                            'name' => 'Bán thành phẩm(SX)',
                                                        ),
                                                        array(
                                                            'type' => 'nvl',
                                                            'name' => 'Nguyên Vật Liệu',
                                                        ),
                                                        array(
                                                            'type' => 'tools',
                                                            'name' => 'Công cụ - Vật tư',
                                                        )
                                                    );
                                                    echo render_select('type_items', $type_items_new, array('type', 'name'), '', -1);
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mbot50">
                            <div class="panel panel-warning">
                                <div class="panel-heading"><?= _l('Mặt hàng cần kiểm kê') ?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 10%;">
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('tnh_items'); ?>
                                                    </label>
                                                </td>
                                                <td style="width: 40%;">
                                                    <input data-placeholder="<?= _l('Danh sách mặt hàng') ?>" id="custom_item_select" class="custom_item_select" style="width: 100%">
                                                </td>
                                                <td style="width: 10%;">
                                                    <label for="reason" class="control-label">
                                                        <?php echo _l('ch_note_t'); ?>
                                                    </label>
                                                </td>
                                                <td style="width: 40%;">
                                                    <?php $value = (isset($items) ? $items->note : ""); ?>
                                                    <?php echo render_textarea('note', '', $value, array('rows' => 2)); ?>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_info_items'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div style="width: 20%;float: left;" class="form-group" app-field-wrapper="lot_code">
                                                        <label for="number" class="control-label">
                                                            <?php echo _l('Lot'); ?>
                                                        </label>
                                                        <input class="lot_code form-control height_auto" type="text" value="" />
                                                    </div>
                                                    <div style="width: 70%;float: left;margin-left: 30px;">
                                                        <div class="thongso hide" style="width: 60%;float: left;">
                                                            <div class="form-group" app-field-wrapper="date">
                                                                <label for="date_sx" class="control-label"><?= _l('ch_date_of_manufacture') ?></label>
                                                                <div class="input-group date"><input type="text" id="date_sx" class="form-control datepicker date_sx maindate_sx" value="" autocomplete="off">
                                                                    <div class="input-group-addon">
                                                                        <i class="fa fa-calendar calendar-icon"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" app-field-wrapper="date"><label for="date_sd" class="control-label">Ngày sử dụng</label>
                                                                <div class="input-group date"><input type="text" id="date_sd" class="form-control datepicker date_sd maindate_sd" value="" autocomplete="off">
                                                                    <div class="input-group-addon">
                                                                        <i class="fa fa-calendar calendar-icon"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="width: 30%;float: left;margin-left: 30px;">
                                                            <div class="form-group thongso hide" app-field-wrapper="date_use">
                                                                <label for="date_use" class="control-label"><?= _l('ch_items_date_use') ?></label>
                                                                <input type="number" id="date_use" class="form-control maindateuse" value="" aria-invalid="false">
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- <select style="width: 100%;" id="localtion_id" class="localtion_id" data-live-search="true" data-width="100%" data-placeholder="<?= _l('Danh sách vị trí') ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    </select> -->
                                                </td>
                                                <td style="width: 10%;" rowspan="2" colspan="2">
                                                    <?php if (empty($items)) { ?>
                                                        <table style="width: 100%;float: right;table-layout: fixed;" class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 25%">
                                                                        <a type="submit" href="<?= base_url('uploads/import_inventory.xlsx?vs=1.0') ?>" class="btn btn-success">Tải mẫu import</a>
                                                                    </td>
                                                                    <td style="width: 50%">
                                                                        <?php echo render_input('file_csv', '', '', 'file'); ?>
                                                                    </td>
                                                                    <td style="width: 25%">
                                                                        <a href="#" onclick="import_export_client(this);return false;" id="import_export_client" class="btn btn-warning btn-icon" style="float: right;"><?= _l('import mặt hàng') ?></a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('warehouse_localtion'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <select style="width: 80%;float: left;" id="localtion_id" class="localtion_id" data-live-search="true" data-width="100%" data-placeholder="<?= _l('Danh sách vị trí') ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    </select>
                                                    <div style="float: left;margin-left: 20%;">
                                                        <label for="date_use" class="control-label"></label>
                                                        <a onclick="chose_items()" class="btn btn-success">Chọn</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
                            <ul class="nav nav-tabs hide ch_tab" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#money_goods" class="money_goods" onclick="" aria-controls="money_goods" role="tab" data-toggle="tab">
                                        <?php echo _l('Mặt hàng'); ?>
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#import_error" style="color: red;" class="count_error" onclick="" aria-controls="import_error" role="tab" data-toggle="tab">
                                        <?php echo _l('Import lỗi'); ?>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="money_goods">
                                    <div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
                                        <div class="panel-heading">
                                            <?= lang('tnh_info_items') ?>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive" style="max-height: 310px;">
                                                <table class="dt-tnh table item-inventory table-bordered table-hover mtop0 mbot0" style="width: 100%;table-layout: fixed;">
                                                    <!-- <table class="table items item-inventory no-mtop dont-responsive-table"> -->
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 150px"></th>
                                                            <th style="width: 200px" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('ch_info_items'); ?></th>
                                                            <th style="width: 100px" class="text-center"><?php echo _l('item_unit'); ?></th>
                                                            <th style="width: 100px" class="text-center"><?php echo _l('cong_price_thinh'); ?></th>
                                                            <th style="width: 100px" class="text-center"><?php echo _l('item_quantity_inwarehouse'); ?></th>
                                                            <th style="width: 120px" class="text-center"><?php echo _l('ch_quantity_time_inwarehouse'); ?></th>
                                                            <th style="width: 100px" class="text-center"><?php echo _l('ch_difference'); ?></th>
                                                            <th style="width: 100px" class="text-center"><?php echo _l('amount_suppliers_vnd'); ?></th>
                                                            <th style="width: 170px" class="text-center"><?php echo _l('ch_handling'); ?></th>
                                                            <th style="width: 50px"><i class="fa fa-trash-o" aria-hidden="true"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 0;
                                                        $totalQuantity_approve = 0;
                                                        $totalQuantity = 0;
                                                        if (isset($items) && count($items->items) > 0) {
                                                            foreach ($items->items as $value) {
                                                        ?>
                                                                <tr class="sortable item">
                                                                    <td class="text-center"><input class="hide idd" id="idd" name="items[<?php echo $i; ?>][idd]" value="<?php echo $value['id']; ?>" /><input class="hide type" id="type" name="items[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>" /><input class="hide id" id="id" name="items[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>" /><input class="hide localtion" id="localtion" name="items[<?php echo $i; ?>][localtion]" value="<?php echo $value['localtion']; ?>" /><img style="border-radius: 50%;width: 2em;height: 2em;" src="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg')) ?>"><br><?= format_item_purchases($value['type']) ?>
                                                                    </td>
                                                                    <td class="dragger">
                                                                        <?= $value['name_item'] ?><?= (isset($value['mode']) ? '<br><span style="font-size: 10px;font-style: italic;">' . _l('ch_items_specification') . ': ' . $value['mode'] . '</span>' : '') ?><br><?= $value['localtion_name_id'] ?>

                                                                    </td>
                                                                    <td><?= $value['unit'] ?></td>
                                                                    <td><input onchange="formatNumBerKeyUp(this)" style="width:100%" class="price H_input align_right height_auto" type="text" name="items[<?= $i ?>][price]" value="<?= number_format($value['price']) ?>" />
                                                                    </td>
                                                                    <td>
                                                                        <input onchange="formatNumBerKeyUpCus(this)" class="mainQuantity H_input height_auto" type="text" readonly name="items[<?= $i ?>][quantity]" value="<?= formatNumber($value['quantity']) ?>" />
                                                                    </td>
                                                                    <td>
                                                                        <input onchange="formatNumBerKeyUpCus(this)" class="mainQuantityNet H_input height_auto" type="text" name="items[<?= $i ?>][quantity_net]" value="<?= formatNumber($value['quantity_net']) ?>" />
                                                                    </td>
                                                                    <td>
                                                                        <input class="mainQuantityDiff H_input height_auto" type="text" readonly name="items[<?= $i ?>][quantity_diff]" value="<?= formatNumber($value['quantity_diff']) ?>" />
                                                                    </td>
                                                                    <td class="amount text-right"><?= number_format($value['amount']) ?></td>
                                                                    <td><?= $value['handling'] ?><input class="handling" type="hidden" name="items[<?= $i ?>][handling]" value="<?= $value['handling'] ?>"></td>
                                                                    <td>
                                                                        <a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;">
                                                                            <i class="fa fa-times"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                        <?php
                                                                $i++;
                                                            }
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="import_error">
                                    <div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
                                        <div class="panel-heading">
                                            <?= lang('Các dòng bị lỗi') ?>
                                        </div>
                                        <div class="panel-body">
                                            <table class="dt-tnh table  table-bordered table-hover mtop0 mbot0" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 100px"><?php echo _l('import_count'); ?></th>
                                                        <th style="width: 300px" class="text-center"><?php echo _l('import_error'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="import_error">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <div class="width90 pull-left">
                    <table class="table tnh-tb noMargin table-color_sum dont-responsive-table">
                        <tbody>
                            <tr>
                                <td>
                                    <span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
                                </td>
                                <td class="total_all">
                                    <?php echo $totalQuantity ?>
                                </td>
                                <td>
                                    <span class="bold"><?php echo _l('ch_quantity_time_total'); ?> :</span>
                                </td>
                                <td class="total_net">
                                    <?php echo $totalQuantity_approve ?>
                                </td>
                                <td>
                                    <span class="bold"><?php echo _l('ch_difference_total'); ?> :</span>
                                </td>
                                <td class="total_diff">
                                    <?php echo $totalQuantity_approve ?>
                                </td>
                                <td>
                                    <span class="bold"><?php echo _l('cong_info_money'); ?> :</span>
                                </td>
                                <td class="total_amount">
                                    <?php echo $totalQuantity_approve ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="btn btn-info pull-right custom-form-submit">
                    <?php echo _l('submit'); ?>
                </a>
            </div>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    $('.custom-form-submit').on('click', (e) => {
        if ($('input.error').length) {
            e.preventDefault();
            alert('<?= _l('ch_invalid_value') ?>');
            return;
        }
        var a = confirm('<?= _l('ch_you_want_update') ?>');
        if (a === false) {
            e.preventDefault();
        } else {
            $('#inventory-form').submit();
        }
    });

    function import_export_client() {
        $('.ch_tab').addClass('hide');
        var warehouse_id = $('#warehouse_id').val();
        if (empty(warehouse_id)) {
            alert('<?= _l('alert_warehouse') ?>');
            return;
        }
        var file_datas = $('input#file_csv').val();
        if (empty(file_datas)) {
            alert('<?= _l('alert_file') ?>');
            return;
        }
        if ($('table.item-inventory tbody').find('tr').length) {
            var r = confirm("<?php echo _l('ch_note_load'); ?>");
            if (r == false) {
                return false;
            }
        }
        $('table.item-inventory tbody').html('');
        var file_data = $('input#file_csv').prop('files')[0];
        var button = $('#import_export_client');
        // button.button({loadingText: 'please wait'});
        // button.button('loading');

        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('warehouse_id', warehouse_id);
        form_data.append('csrf_token_name', csrfData.hash);
        $.ajax({
            url: "<?= admin_url() ?>inventory/import_items/",
            type: 'POST',
            data: form_data,
            async: false,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,

        }).done(function(data) {
            data = JSON.parse(data);
            if (data.success) {
                $.each(data.list_data, (index, value) => {
                    createTrItemfist_load(value);
                });
                if (!empty(data.list_data_orr)) {
                    $('.ch_tab').removeClass('hide');
                    createTrItemfist_error(data.list_data_orr);
                    $('.count_error').html('<?= _l('Import lỗi') ?> (' + (data.list_data_orr).length + ')')
                } else {
                    $('.money_goods').click();
                }
                button.button('reset');
                $('input#file_csv').val('');
            }
        }).fail(function() {
            alert_float('danger', 'err');
        }).always(function() {
            $('input#file_csv').val('');

        });
        return false;
    }

    function createTrItemfist_error(data) {
        $('.import_error').html('');
        var html = '';
        $.each(data, function(key, value) {
            html += '<tr>';
            html += '<td>Dòng ' + value.count + '</td>';
            html += '<td>' + value.title + '</td>';
            html += '/<tr>';
        });
        $('.import_error').html(html);
    }

    function createTrItemfist_load(data) {
        var warehouses = $('#warehouse_id').val();
        var date = $('#date').val();
        var id = data.id_items;
        var type = data.type;
        var localtion = data.id_localtion;
        var lot_code = data.lot_code;
        var date_sx = data.date_sx;
        var date_sd = data.date_sd;
        var date_use = data.date_use;
        dataString = {
            type: type,
            id: id,
            warehouses: warehouses,
            date: date,
            localtion: localtion,
            lot_code: lot_code,
            date_sx: date_sx,
            date_sd: date_sd,
            date_use: date_use,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>warehouse/get_localtion/",
            data: dataString,
            cache: false,
            success: function(_data) {
                _data = JSON.parse(_data);
                createTrItem_load(_data[0], data);
            }
        });
    }

    function createTrItem_load(data, _data) {
        if (!$('div #warehouse_id option:selected').length || $('div #warehouse_id option:selected').val() == '') {
            alert_float('danger', "<?= _l('ch_not_warehouse') ?>");
            return;
        }
        // if ($('table.item-inventory tbody tr').find('input[value=' + data.items.id + '].id ').length > 0) {
        //     var parents = $('table.item-inventory tbody tr').find('input[value=' + data.items.id + '].id ').parents('tr');
        //     if ((parents.find('input[value=' + _data.type + '].type ').length > 0) && (parents.find('input[value=' + data.localtion + '].localtion ').length > 0)) {
        //         alert_float('warning', "<?= _l('ch_exsit_items_rfq') ?>");
        //         return;
        //     }
        // }
        var trungss = data.items.id + _data.type + data.localtion + data.lot_code + data.date_sx + data.date_sd + data.date_use;
        if ($('table.item-inventory tbody tr').find('input[value="' + trungss + '"].trungss ').length > 0) {
            var parents = $('table.item-inventory tbody tr').find('input[value=' + data.items.id + '].id ').parents('tr');
            if ((parents.length > 0)) {
                alert_float('warning', "<?= _l('ch_exsit_items_rfq') ?>");
                return false;
            }
        }
        var newTr = $('<tr class="sortable item"></tr>');
        if (data.lot_code == null) {
            data.lot_code = '';
        }
        if (data.date_sx == null) {
            data.date_sx = '';
        }
        if (data.date_sd == null) {
            data.date_sd = '';
        }
        if (data.date_use == null) {
            data.date_use = '';
        }
        // var td1 = $('<td class="text-center"><img style="border-radius: 50%;width: 2em;height: 2em;" src="' + data.items.avatar_1 + '"><br><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(_data.type) + '</span><input class="hide id"  name="items[' + uniqueArray + '][id]" value="' + data.items.id + '" />' +
        //     '<input class="hide localtion" name="items[' + uniqueArray + '][localtion]" value="' + data.localtion + '" /><input class="hide type" name="items[' + uniqueArray + '][type]" value="' + _data.type + '" /></td>');
        var td1 = $('<td class="text-center"><img style="border-radius: 50%;width: 2em;height: 2em;" src="' + data.items.avatar_1 + '"><br><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(_data.type) + '</span><input class="hide id"  name="items[' + uniqueArray + '][id]" value="' + data.items.id + '" />' +
            '<input class="hide localtion" name="items[' + uniqueArray + '][localtion]" value="' + data.localtion + '" /><input class="hide type" name="items[' + uniqueArray + '][type]" value="' + _data.type + '" />\
            <input class="hide date_sx" name="items[' + uniqueArray + '][date_sx]" value="' + data.date_sx + '" />\
            <input class="hide lot_code_new" name="items[' + uniqueArray + '][lot_code]" value="' + data.lot_code + '" />\
            <input class="hide date_sd" name="items[' + uniqueArray + '][date_sd]" value="' + data.date_sd + '" />\
            <input class="hide date_use" name="items[' + uniqueArray + '][date_use]" value="' + data.date_use + '" />\
            <input class="hide trungss"  value="' + trungss + '" />\
            </td>');

        var td2 = $('<td class="dragger">' + data.items.name + '<br>' + data.name_localtion + '</td>');
        var thongso = ' <div style="font-size: 11px;font-style: italic;" >\
                            <?= _l('Lot') ?>:' + data.lot_code;
        if (_data.type != 'tools') {
            thongso += '<br><?= _l('ch_date_of_manufacture_m') ?>: ' + data.date_sx + '\
                    <br><?= _l('ch_items_dateed_m') ?>: ' + data.date_sd + '\
                    <br><?= _l('ch_items_date_use_m') ?>: ' + data.date_use;
        }
        thongso += '</div>';
        var td10 = $('<td>' + thongso + '</td>');
        var td3 = $('<td>' + data.items.unit_name_stock + '</td>');
        var td8 = $('<td><input onchange="formatNumBerKeyUp(this)" style="width:100%" class="height_auto price H_input align_right" type="text" name="items[' + uniqueArray + '][price]" id="price_' + uniqueArray + '" value="' + formatNumber(data.items.price_import) + '" /></td>');
        var td4 = $('<td><input readonly style="width:100%" class="height_auto mainQuantity H_input" type="text" onchange="formatNumBerKeyUpCus(this)" name="items[' + uniqueArray + '][quantity]" value="' + data.get_quantity_import + '" /></td>');
        var td5 = $('<td><input id="mainQuantityNet_' + uniqueArray + '" style="width:100%" class="height_auto mainQuantityNet H_input" type="text" onchange="formatNumBerKeyUpCus(this)" name="items[' + uniqueArray + '][quantity_net]" value="' + _data.quantity + '" /></td>');
        var td6 = $('<td><input readonly style="width:100%" class="height_auto mainQuantityDiff H_input" type="text" name="items[' + uniqueArray + '][quantity_diff]" value="" /></td>');
        var td9 = $('<td class="amount text-right"></td>');
        var td7 = $('<td><input class="handling" type="hidden" name="items[' + uniqueArray + '][handling]" value=""></td>');


        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td10);
        newTr.append(td3);
        newTr.append(td8);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td9);
        newTr.append(td7);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-inventory tbody').append(newTr);
        $('#mainQuantityNet_' + uniqueArray).change();
        total += parseFloat($('tr.main').find('td:nth-child(4) > input').val());
        $('#price_' + uniqueArray).change();

        uniqueArray++;
        // $('#custom_item_select').val('').selectpicker('render'); 
        refreshQuantity();
    };
    $(function() {
        var dt = $('.item-purchases').DataTable({
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fixedHeader': true,
            // scrollY: true,
            // scrollY: '150px',
            // scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });
    });
    $(function() {
        _validate_form($('#inventory-form'), {
            date: "required",
            number: "required",
            warehouse_id: "required",
        });
    });
    $(document).on('change', '#warehouse_id', (e) => {

        loadLocaltion_warehouses();
        var warehouse = $('#warehouse_id').val();
        $('#warehouse_idd').val(warehouse);
    })
    $('#warehouse_id').change();
    refreshQuantity();

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
                        url: url + '/' + id,
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0]);
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
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
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
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    var base_url = '<?= base_url() ?>';
    // function repoFormatSelection(state) {
    //     if (!state.id) return state.text;
    //     if(state.img)
    //     {
    //         var img = '<img class="img_option" src="'+base_url+state.img+'"/> ';
    //     }
    //     else
    //     {
    //         var img = '<img class="img_option" src="'+base_url+'download/preview_image"/> ';
    //     }
    //     return  img + ' (' +state.code+ ') ' +state.text ;
    // }
    function repoFormatSelection(result) {
        if (!result.id) return result.text; // optgroup
        tr = '';
        if (result) {
            if (result.img) {
                var img = '<img class="img_option" src="' + base_url + result.img + '"/> ';
            } else {
                var img = '<img class="img_option" src="' + base_url + 'download/preview_image"/> ';
            }
            if (result.type == 'tools') {
                tr += '<td style="width: 100%;border:0 !important;padding:0 !important">' +
                    '<div class="bold" style="font-size: 14px;">' + img + result.text + ' (' + result.code + ')</div>' +
                    '</td>';
            } else {
                tr += '<td style="width: 100%;border:0 !important;padding:0 !important">' +
                    '<div class="bold" style="font-size: 14px;">' + img + result.text + ' (' + result.code + ')</div>' +
                    '<div style="font-style: italic;"><?= _l('item_specification') ?>: ' + result.mode + '</div>' +
                    '</td>';
            }

            // tr+= '<td style="width: 15%;">'+result.name_color+'</td>';
            // tr+= '<td style="width: 15%;">'+result.mode+'</td>';
            // tr+= '<td style="width: 15%;">'+result.mt+'</td>';
            // tr+= '<td style="width: 10%;" class="text-center">'+result.qty_warehouse+'</td>';
        }
        tableSelect = '<table class="tnh-table-bottom dont-responsive-table">' + '<tbody>' + tr + '</tbody>' + '</table>';
        return tableSelect;
    }
    ajaxSelectCallBack($('.custom_item_select'), "<?= admin_url('inventory/SearchItems_new') ?>", 0);
    $('#localtion_id').select2();

    function loadLocaltion_warehouses() {
        var warehouse = $('#warehouse_id').val();
        $('#localtion_id').select2();
        $('#localtion_id').find('option:gt(0)').remove();
        if ($('#localtion_id').length) {
            $.post(admin_url + "warehouse/list_localtion_kk", {
                warehouse: warehouse,
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                $('#localtion_id').html(data).find('option').attr('disabled', 'disabled').parents('#localtion_id').find('option[child="1"]').removeAttr('disabled').selectpicker('render');
                $('#localtion_id').find('option:nth-child(1)').removeAttr('disabled');
            })
        }
        $('#custom_item_select').select2('val', '');
    }
    $('#custom_item_select').change((e) => {
        if (!$('div #warehouse_id option:selected').length || $('div #warehouse_id option:selected').val() == '') {
            alert_float('danger', "<?= _l('ch_not_warehouse') ?>");
            return;
        }
        var id = $(e.currentTarget).val();
        var warehouses = $('#warehouse_id').val();
        var date = $('#date').val();
        var type = $(e.currentTarget).select2('data').type;
        var type_v1 = '';
        if (type == 'product') {
            type_v1 = $(e.currentTarget).select2('data').type_v1;

        }
        $('.thongso').addClass('hide');
        if (type == 'nvl' || type == 'product') {
            $('.thongso').removeClass('hide');
        }
        if (typeof(id) != 'undefined') {
            dataString1 = {
                type: type,
                id: id,
                warehouses: warehouses,
                date: date,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>warehouse/get_localtion/",
                data: dataString1,
                cache: false,
                success: function(data) {
                    data = JSON.parse(data);
                    if (!empty(data)) {
                        $.each(data, function(key, value) {
                            createTrItem(value, type, type_v1);
                        });
                    } else {
                        // alert_float('danger', "<?= _l('ch_not_items_warehouse_localtion') ?>");
                    }
                }
            });
        } else {
            alert('<?= _l('alert_choses_items') ?>');
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    var itemList = <?php echo json_encode($type_items); ?>;
    var findItem = (type, type_v1) => {
        var itemResult;
        $.each(itemList, (index, value) => {
            if (value.type == type) {
                itemResult = value.name;
                if (type == 'product') {
                    if (type_v1 == 'semi_products') {
                        itemResult = 'Bán thành phẩm(SX)';
                    }
                }
                return false;
            }
        });
        itemResult = '';
        return itemResult;
    };
    var uniqueArray = <?= $i ?>;
    var total = 0;

    function createTrItem(data, type, type_v1) {
        if (!$('div #warehouse_id option:selected').length || $('div #warehouse_id option:selected').val() == '') {
            alert_float('danger', "<?= _l('ch_not_warehouse') ?>");
            return;
        }
        // if ($('table.item-inventory tbody tr').find('input[value=' + data.items.id + '].id ').length > 0) {
        //     var parents = $('table.item-inventory tbody tr').find('input[value=' + data.items.id + '].id ').parents('tr');
        //     if ((parents.find('input[value=' + type + '].type ').length > 0) && (parents.find('input[value=' + data.localtion + '].localtion ').length > 0) && (parents.find('input[value="' + data.lot_code + '"].lot_code ').length > 0) && (parents.find('input[value="' + data.date_sx + '"].date_sx ').length > 0) && (parents.find('input[value="' + data.date_sd + '"].date_sd ').length > 0) && (parents.find('input[value="' + data.date_use + '"].date_use ').length > 0)) {
        //         alert_float('warning', "<?= _l('ch_exsit_items_rfq') ?>");
        //         return false;
        //     }
        // }
        var trungss = data.items.id + type + data.localtion + data.lot_code + data.date_sx + data.date_sd + data.date_use;
        if ($('table.item-inventory tbody tr').find('input[value="' + trungss + '"].trungss ').length > 0) {
            var parents = $('table.item-inventory tbody tr').find('input[value=' + data.items.id + '].id ').parents('tr');
            if ((parents.length > 0)) {
                alert_float('warning', "<?= _l('ch_exsit_items_rfq') ?>");
                return false;
            }
        }
        var newTr = $('<tr class="sortable item"></tr>');
        if (data.lot_code == null) {
            data.lot_code = '';
        }
        if (data.date_sx == null) {
            data.date_sx = '';
        }
        if (data.date_sd == null) {
            data.date_sd = '';
        }
        if (data.date_use == null) {
            data.date_use = '';
        }
        var td1 = $('<td class="text-center"><img style="border-radius: 50%;width: 2em;height: 2em;" src="' + data.items.avatar_1 + '"><br><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(type, type_v1) + '</span><input class="hide id"  name="items[' + uniqueArray + '][id]" value="' + data.items.id + '" />' +
            '<input class="hide localtion" name="items[' + uniqueArray + '][localtion]" value="' + data.localtion + '" /><input class="hide type" name="items[' + uniqueArray + '][type]" value="' + type + '" />\
            <input class="hide date_sx" name="items[' + uniqueArray + '][date_sx]" value="' + data.date_sx + '" />\
            <input class="hide lot_code_new" name="items[' + uniqueArray + '][lot_code]" value="' + data.lot_code + '" />\
            <input class="hide date_sd" name="items[' + uniqueArray + '][date_sd]" value="' + data.date_sd + '" />\
            <input class="hide date_use" name="items[' + uniqueArray + '][date_use]" value="' + data.date_use + '" />\
            <input class="hide trungss"  value="' + trungss + '" />\
            </td>');
        var td2 = $('<td class="dragger">' + data.items.name + '<span style="font-size: 10px;font-style: italic;">' + data.mode + '</span><br>' + data.name_localtion + '</td>');
        var thongso = ' <div style="font-size: 11px;font-style: italic;" >\
                            <?= _l('Lot') ?>:' + data.lot_code;
        if (type != 'tools') {
            thongso += '<br><?= _l('ch_date_of_manufacture_m') ?>: ' + data.date_sx + '\
                    <br><?= _l('ch_items_dateed_m') ?>: ' + data.date_sd + '\
                    <br><?= _l('ch_items_date_use_m') ?>: ' + data.date_use;
        }
        thongso += '</div>';
        var td10 = $('<td>' + thongso + '</td>');
        var td3 = $('<td>' + data.items.unit_name_stock + '</td>');
        var td8 = $('<td><input style="width:100%" onchange="formatNumBerKeyUp(this)" id="price_' + uniqueArray + '" class="height_auto price H_input align_right" type="text" name="items[' + uniqueArray + '][price]" value="' + formatNumber(data.items.price_import) + '" /></td>');
        var td4 = $('<td><input readonly style="width:100%" class="height_auto mainQuantity H_input" type="text" onchange="formatNumBerKeyUpCus(this)" name="items[' + uniqueArray + '][quantity]" value="' + data.get_quantity_import + '" /></td>');
        var td5 = $('<td><input style="width:100%"  class="height_auto mainQuantityNet H_input" type="text" onchange="formatNumBerKeyUpCus(this)" name="items[' + uniqueArray + '][quantity_net]" value="" /></td>');
        var td9 = $('<td class="amount text-right">0</td>');
        var td6 = $('<td><input readonly style="width:100%" class="height_auto mainQuantityDiff H_input" type="text" name="items[' + uniqueArray + '][quantity_diff]" value="" /></td>');

        var td7 = $('<td><input class="handling" type="hidden" name="items[' + uniqueArray + '][handling]" value=""></td>');


        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td10);
        newTr.append(td3);
        newTr.append(td8);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td9);
        newTr.append(td7);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-inventory tbody').append(newTr);
        total += parseFloat($('tr.main').find('td:nth-child(4) > input').val());
        $('#price_' + uniqueArray).change();
        uniqueArray++;
        // $('#custom_item_select').val('').selectpicker('render'); 

        refreshQuantity();
    };
    var deleteTrItem = (trItem) => {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            var current = $(trItem).parent().parent();
            $(trItem).parent().parent().remove();
            refreshQuantity();
        }
    };
    $(document).on('keyup', '.price', (e) => {
        var currentQuantityInput = $(e.currentTarget).parents('tr').find('input.mainQuantityNet');
        quantity = $(e.currentTarget).parents('tr').find('input.mainQuantity');
        quantityDiff = $(e.currentTarget).parents('tr').find('input.mainQuantityDiff');
        handlingInput = $(e.currentTarget).parents('tr').find('input.handling');
        handlingTd = handlingInput.parent();
        var diff = Number(currentQuantityInput.val()) - Number(quantity.val());
        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
        $(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total * diff)));
        refreshQuantity();
    });
    $(document).on('change', '.price', (e) => {
        var currentQuantityInput = $(e.currentTarget).parents('tr').find('input.mainQuantityNet');
        quantity = $(e.currentTarget).parents('tr').find('input.mainQuantity');
        quantityDiff = $(e.currentTarget).parents('tr').find('input.mainQuantityDiff');
        handlingInput = $(e.currentTarget).parents('tr').find('input.handling');
        handlingTd = handlingInput.parent();
        var diff = Number(currentQuantityInput.val()) - Number(quantity.val());
        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
        $(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total * diff)));
        refreshQuantity();
    });
    $(document).on('keyup', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        quantity = $(e.currentTarget).parents('tr').find('input.mainQuantity');
        quantityDiff = $(e.currentTarget).parents('tr').find('input.mainQuantityDiff');
        handlingInput = $(e.currentTarget).parents('tr').find('input.handling');
        handlingTd = handlingInput.parent();
        var diff = Number(unformat_number(currentQuantityInput.val())) - Number(quantity.val());
        var handling = '';
        if (diff > 0) handling = '<?= _l('ch_handling_up') ?> ' + tnhFormatNumber(Math.abs(diff));
        if (diff < 0) handling = '<?= _l('ch_handling_down') ?> ' + tnhFormatNumber(Math.abs(diff));
        handlingTd.text(handling);
        handlingInput.val(handling);
        handlingTd.append(handlingInput);
        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
        $(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total * diff)));
        quantityDiff.val(tnhFormatNumber(Number(unformat_number(currentQuantityInput.val())) - Number(quantity.val())));
        refreshQuantity();
    });
    $(document).on('change', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        quantity = $(e.currentTarget).parents('tr').find('input.mainQuantity');
        quantityDiff = $(e.currentTarget).parents('tr').find('input.mainQuantityDiff');
        handlingInput = $(e.currentTarget).parents('tr').find('input.handling');
        handlingTd = handlingInput.parent();
        var diff = Number(unformat_number(currentQuantityInput.val())) - Number(quantity.val());
        var handling = '';
        if (diff > 0) handling = '<?= _l('ch_handling_up') ?> ' + tnhFormatNumber(Math.abs(diff));
        if (diff < 0) handling = '<?= _l('ch_handling_down') ?> ' + tnhFormatNumber(Math.abs(diff));
        handlingTd.text(handling);
        handlingInput.val(handling);
        handlingTd.append(handlingInput);
        var total = unformat_number($(e.currentTarget).parents('tr').find('input.price').val());
        $(e.currentTarget).parents('tr').find('td.amount').text(formatNumber(Math.abs(total * diff)));
        quantityDiff.val(tnhFormatNumber(Number(unformat_number(currentQuantityInput.val())) - Number(quantity.val())));
        refreshQuantity();
    });

    function refreshQuantity() {
        var items = $('table.item-inventory tbody tr');
        total = 0;
        total_net = 0;
        total_diff = 0;
        total_amount = 0;
        $.each(items, (index, value) => {
            let temp = parseFloat(unformat_number($(value).find('input.mainQuantity').val()));
            total += (isNaN(temp) ? 0 : temp);
            temp = parseFloat(unformat_number($(value).find('input.mainQuantityNet').val()));
            total_net += (isNaN(temp) ? 0 : temp);
            temp = parseFloat(unformat_number($(value).find('input.mainQuantityDiff').val()));
            total_diff += (isNaN(temp) ? 0 : temp);
            temp = parseFloat(unformat_number($(value).find('td.amount').text()));
            total_amount += (isNaN(temp) ? 0 : temp);
        });
        $('.total_all').text(tnhFormatNumber(total));
        $('.total_net').text(tnhFormatNumber(total_net));
        $('.total_diff').text(tnhFormatNumber(total_diff));
        $('.total_amount').text(formatNumber(total_amount));
        if (items.length > 0) {
            $('#date').attr('disabled', true);
            $('#warehouse_id').prop('disabled', 'disabled');
        } else {
            $('#date').attr('disabled', false);
            $('#warehouse_id').prop('disabled', false);
        }
    };

    function chose_items() {
        var localtion = $('#localtion_id').val();
        var warehouses = $('#warehouse_id').val();
        var date = $('#date').val();
        var id = $('#custom_item_select').val();
        var lot_code = $('.lot_code').val();
        var date_sx = $('#date_sx').val();
        var date_sd = $('#date_sd').val();
        var date_use = $('#date_use').val();
        if (empty(id)) {
            alert('Bạn chọn lại sản phẩm!');
            $('#localtion_id').select2('val', '');
            return;
        }
        if (empty(localtion)) {
            alert('Bạn chưa chọn vị trí!');
            $('#localtion_id').select2('val', '');
            return;
        }
        var type = $('#custom_item_select').select2('data').type;
        var type_v1 = '';
        if (type == 'product') {
            type_v1 = $('#custom_item_select').select2('data').type_v1;

        }
        dataString = {
            type: type,
            id: id,
            lot_code: lot_code,
            date_sx: date_sx,
            date_sd: date_sd,
            date_use: date_use,
            warehouses: warehouses,
            date: date,
            localtion: localtion,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>warehouse/get_localtion/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                createTrItem(data[0], type, type_v1);
                $('#localtion_id').select2('val', '');
                $('.lot_code').val('');
                $('#date_sx').val('');
                $('#date_sd').val('');
                $('#date_use').val('');
            }
        });
    }
    // $('#localtion_id').change((e) => {
    //     var localtion = $(e.currentTarget).val();
    //     var warehouses = $('#warehouse_id').val();
    //     var date = $('#date').val();
    //     var id = $('#custom_item_select').val();
    //     if (empty(id)) {
    //         alert('Bạn chọn lại sản phẩm!');
    //         $(e.currentTarget).select2('val', '');
    //         return;
    //     }
    //     var type = $('#custom_item_select').select2('data').type;
    //     dataString = {
    //         type: type,
    //         id: id,
    //         warehouses: warehouses,
    //         date: date,
    //         localtion: localtion,
    //         [csrfData['token_name']]: csrfData['hash']
    //     };
    //     jQuery.ajax({
    //         type: "post",
    //         url: "<?= admin_url() ?>warehouse/get_localtion/",
    //         data: dataString,
    //         cache: false,
    //         success: function(data) {
    //             data = JSON.parse(data);
    //             createTrItem(data[0], type);
    //             $(e.currentTarget).select2('val', '');
    //         }
    //     });
    // });

    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2 = x2.substr(0, 2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };

    function convertToDate(dateString) {
        //  Convert a "dd/MM/yyyy" string into a Date object
        let d = dateString.split("/");
        let dat = new Date(d[2] + '/' + d[1] + '/' + d[0]);
        return dat;
    }

    function convertToDatev2(dateString) {
        //  Convert a "dd/MM/yyyy" string into a Date object
        let d = dateString.split("/");
        let dat = new Date(d[2] + '-' + d[1] + '-' + d[0]);
        return dat;
    }

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [day, month, year].join('/');
    }
    $(document).on('change', '.maindate_sx', (e) => {
        var currentQuantityInput = $(e.currentTarget);

        if (($('.maindate_sd').val() != '') && ($('.date_sx').val() != '')) {
            date_sx = convertToDate($('.date_sx').val());
            date_sd = convertToDate($('.maindate_sd').val());
            var diff = Math.abs(date_sd - date_sx); // difference in milliseconds
            var dateOffset = (Number(diff) / (24 * 60 * 60 * 1000)) * 1; //5 days
        } else {
            dateOffset = 0;
        }
        $('input.maindateuse').val(dateOffset);
    })
    $(document).on('change', '.maindate_sd', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (($('.maindate_sd').val() != '') && ($('.date_sx').val() != '')) {
            date_sx = convertToDate($('.date_sx').val());
            date_sd = convertToDate($('.maindate_sd').val());
            var diff = Math.abs(date_sd - date_sx); // difference in milliseconds
            var dateOffset = (Number(diff) / (24 * 60 * 60 * 1000)) * 1; //5 days
        } else {
            dateOffset = 0;
        }
        $('input.maindateuse').val(dateOffset);
    })
    $(document).on('change', '.maindateuse', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (($('.date_sx').val() != '')) {
            date_sx = convertToDatev2($('.date_sx').val());
            date_sd = $('input.maindateuse').val();
            // var diff = Math.abs(date_sd + (date_sx*(24 * 60 * 60 * 1000))); // difference in milliseconds
            const dates = new Date(date_sx);
            var dateOffset = dates.setTime(dates.getTime() + ((24 * 60 * 60 * 1000) * date_sd));

        } else {
            dateOffset = '';
        }
        $('input.maindate_sd').val(formatDate(dateOffset));
    })
    $(document).on('change', '.price', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(0);
        }
        type = $(e.currentTarget).parents('tr').find('input.type').val();
        console.log(type)
        if (intVal(currentQuantityInput.val()) == 0 && type == 'nvl') {
            currentQuantityInput.attr("style", "border: 1px solid red !important");
            currentQuantityInput.attr('data-toggle', 'tooltip');
            currentQuantityInput.attr('data-trigger', 'manual');
            currentQuantityInput.attr('title', '<?= _l('Đơn giá phải lớn hơn 0') ?>');
            currentQuantityInput.off('focus', '**').off('hover', '**');
            currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this).tooltip('show'));
            currentQuantityInput.addClass('error');
        } else {
            currentQuantityInput.attr("style", "");
            currentQuantityInput.removeClass('error');
        }
    });
</script>