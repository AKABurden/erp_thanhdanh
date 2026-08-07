<?php init_head(); ?>
<style type="text/css">
    .select2-choice {
        min-height: 35px !important;
        height: 100% !important;
    }

    .item-items .ui-sortable tr td input {
        width: 80px;
    }

    .select2-search-choice-close {
        display: none !important;
    }

    .bootstrap-select .filter-option .text-muted {
        display: none;
    }
</style>
<?php
$disabled = array();
$view_price = '';
if (!has_permission('import', '', 'view_price')) {
    $view_price = 'hide';
} ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), array('id' => 'import-form', 'class' => '_transaction_form invoice-form'));
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
                        <?php if (isset($items)) { ?>
                        <?php } ?>
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
                                                        <?php echo _l('ch_code_old'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <input type="text" id="id_order" class="hide" name="id_order" value="<?php echo (isset($purchase_order) ? ($purchase_order->id) : '') ?>">
                                                            <?php echo (isset($purchase_order) ? ($purchase_order->prefix) : get_option('prefix_import')); ?>-</span>
                                                        <?php
                                                        $value = (isset($purchase_order) ? ($purchase_order->code) : '');
                                                        ?>
                                                        <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_code_p'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <?php echo (isset($items) ? ($items->prefix) : get_option('prefix_import')); ?>-</span>
                                                        <?php
                                                        $number = sprintf('%06d', ch_getMaxID('id', 'tblimport') + 1);
                                                        $value = (isset($items) ? ($items->code) : $number);
                                                        ?>
                                                        <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="date" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_date_p'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                                                    <?php echo render_date_input('date', '', $value); ?>
                                                </td>
                                                <td>
                                                    <label for="suppliers_id" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('supplier'); ?>
                                                    </label>
                                                </td>
                                                <td class="none-event">
                                                    <?php
                                                    $value = (isset($purchase_order) ? $purchase_order->suppliers_id : '');
                                                    echo render_select('suppliers_id', $suppliers, array('id', 'company'), '', $value);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="warehouse_id" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('warehouse'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $warehouse_id_default = '';
                                                    if ($purchase_order->type_plan != null) {
                                                        $warehouse_id_default = WAREHOUSES_CAPACITY;
                                                        // $disabled = array('disabled'=>true);
                                                    }
                                                    $value = (isset($items) ? $items->warehouse_id : $warehouse_id_default);
                                                    echo render_select('warehouse_id', $warehouse, array('id', 'name', 'code'), '', $value, $disabled);
                                                    ?>
                                                </td>
                                                <td>
                                                    <label for="type_items" class="control-label">
                                                        <?php echo _l('ch_type'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo render_select('type_items', $type_items, array('type', 'name'), '', -1);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="delivery_supplier_code" class="control-label">
                                                        <?php echo _l('Phiếu giao hàng'); ?>
                                                    </label>
                                                </td>
                                                <td colspan="3">
                                                    <?php $value = (isset($items) ? $items->delivery_supplier_code : ""); ?>
                                                    <input type="text" name="delivery_supplier_code" class="form-control delivery_supplier_code" value="<?= $value ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="reason" class="control-label">
                                                        <?php echo _l('ch_note_t'); ?>
                                                    </label>
                                                </td>
                                                <td colspan="3">
                                                    <?php $value = (isset($items) ? $items->note : ""); ?>
                                                    <?php echo render_textarea('reason', '', $value); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php
                        $customer_custom_fields = false;
                        if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'imports', 'active' => 1)) > 0) {
                            $customer_custom_fields = true;
                        ?>
                        <?php } ?>
                        <?php if ($customer_custom_fields) { ?>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading"><?= _l('custom_fields') ?></div>
                                    <div class="panel-body">

                                        <div role="tabpanel" class="tab-pane" id="custom_fields">
                                            <?php $value_id = (isset($items) ? $items->id : ''); ?>
                                            <?php echo render_custom_fields('imports', $value_id); ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mbot30">
                            <div class="row">
                                <div class="col-md-12">
                                    <table style="width: 50%;float: right;table-layout: fixed;" class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                        <tbody>
                                            <tr>
                                                <td style="width: 25%">
                                                    <label for="number" class="control-label">
                                                        <?php echo _l('Chọn nhanh mặt hàng'); ?>
                                                    </label>
                                                </td>
                                                <td style="width: 55%">
                                                    <select id="type_items_ch[]" class="selectpicker type_items_ch[]" data-width="100%" data-actions-box="1" data-live-search="true" multiple="true">
                                                        <?= $html1 ?>
                                                    </select>
                                                </td>
                                                <td style="width: 20%">
                                                    <a href="#" onclick="load();return false;" class="btn btn-warning btn-icon" style="float: right;"><?= _l('load_item') ?></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="hide" style="width: 40%">
                                        <div class="form-group ">
                                            <label for="reason" class="control-label">
                                                <?php echo _l('Chọn mặt hàng cân ký'); ?>
                                            </label>
                                            <select style="width: 100%" class="custom_item_select_all" id="custom_item_select_all" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
                                                <?php echo $html; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <span class="hide" style="color:red"><b>&ensp;Phím tắt: F1 - LẤY SỐ KG (Lưu ý: Con trỏ chuột
                                    phải ngay ô
                                    số lượng)<br>
                                    &emsp;&emsp;&emsp;&ensp;&ensp;&ensp; F2 - IN TEM
                                </b></span>
                            <br>

                            <div class="clearfix"></div>

                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <?= lang('tnh_info_items') ?>
                                </div>

                                <div class="panel-body ">

                                    <div class="table-responsive" style="min-height: 300px">
                                        <!-- <table class="table items item-import no-mtop dont-responsive-table"> -->
                                        <table class="dt-tnh table item-import table-bordered table-hover" style="table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 100px" class="text-center"><a onclick="button_create()" class="btn btn-info btn-icon">+</a><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                                    <th style="width: 200px" class="text-center">
                                                        <?php echo _l('ch_items_name_t'); ?></th>
                                                    <th style="width: 100px" class="text-center hide">
                                                        <?php echo _l('Lot'); ?></th>
                                                    <th style="width: 200px" class="text-center ">
                                                        <?php echo _l('warehouse_localtion'); ?></th>
                                                    <th style="width: 100px" class="text-center hide">
                                                        <?php echo _l('item_unit'); ?></th>
                                                    <th style="width: 160px;" class="text-center "><?php echo _l('ch_date_use'); ?></th>
                                                    <th style="width: 100px" class="text-center hide">
                                                        <?php echo _l('item_quantity'); ?></th>
                                                    <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_standard'); ?></th>
                                                    <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_stock'); ?></th>
                                                    <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_payment'); ?></th>
                                                    <th style="width: 100px" class="text-center <?= $view_price ?>">
                                                        <?php echo _l('tnh_price_import'); ?></th>
                                                    <th style="width: 100px" class="text-center <?= $view_price ?>">
                                                        <?php echo _l('promotion_suppliers'); ?></th>
                                                    <th style="width: 100px" class="text-center hide <?= $view_price ?>">
                                                        <?php echo _l('tax'); ?></th>
                                                    <th style="width: 100px" class="text-center <?= $view_price ?>">
                                                        <?php echo _l('invoice_total'); ?></th>
                                                    <th style="width: 100px" class="text-center">
                                                        <?php echo _l('note'); ?></th>
                                                    <th style="width: 100px"></th>
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
                                                            <td class="dragger avatart" style="text-align: center;"><img style="border-radius: 50%;width: 4em;height: 4em;" src="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg')) ?>"><br><input type="hidden" id="type" name="items[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>" /><input type="hidden" class="id" id="product_id" name="items[<?php echo $i; ?>][id]" value="<?= $value['product_id'] ?>" /><input type="hidden" class="plan_id" id="plan_id" name="items[<?php echo $i; ?>][plan_id]" value="<?= $value['plan_id'] ?>" /><input type="hidden" class="id" id="id_import_items" value="<?= $value['id'] ?>" />
                                                                <div id="type_name"></div>
                                                                <input type="hidden" class="idd" id="idd" name="items[<?php echo $i; ?>][idd]" value="<?= $value['id_purchase_order_items'] ?>" />
                                                                <input type="hidden" class="id" name="items[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>">
                                                                <input type="hidden" class="idd" id="idd" name="items[<?php echo $i; ?>][idd]" value="<?= $value['id_purchase_order_items'] ?>" />
                                                            </td>
                                                            <td><input type="hidden" id="type" value="<?php echo $value['type']; ?>" />
                                                                <select style="width: 200px" class=" custom_item_select" id="custom_item_select_<?= $i ?>" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                    <?php foreach (get_options_search_cbo('items', $value['product_id'], $value['type']) as $t) { ?>
                                                                        <option data-qc="<?= $t['mode'] ?>" data-id="<?= $value['type'] ?>" data-text="<?= $t['name'] ?>" value="<?php echo $t['id']; ?>" <?php echo ($t['id'] == $value['id'] ? 'selected' : '') ?>>
                                                                            <?= $t['name'] ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                                <br><br>
                                                                <div class="color">
                                                                    <?= format_item_color($value['product_id'], $value['type']) ?>
                                                                </div>
                                                                <div class="code_plan">
                                                                    <?php $code_plan = '';
                                                                    $plan = get_table_where('tbl_productions_plan', ['id' => $value['plan_id']], '', 'row_array');
                                                                    if (!empty($plan)) {
                                                                        $code_plan = $plan['reference_no'];
                                                                    }
                                                                    ?>
                                                                    <div class="label label-success"><?= $code_plan ?></div>
                                                                </div>
                                                            </td>
                                                            <td class="hide"><input style="width: 100px" class="lot_code form-control height_auto" type="text" name="items[<?= $i ?>][lot_code]" value="<?= $value['lot_code'] ?>" /></td>
                                                            <td class="td_location" style="text-align:center">
                                                                <?php if (!empty($value['plan_id'])) { ?>
                                                                    <div class="label label-danger">Vị trí tự động theo KHSX</div>
                                                                    <input type="hidden" name="items[<?= $i ?>][localtion_warehouses_id]" value="<?= $value['localtion_warehouses_id'] ?>">
                                                                <?php } else { ?>
                                                                    <div class="form-group ">
                                                                        <select data-id="<?= $value['localtion_warehouses_id'] ?>" class="localtion_warehouses_id  " style="width: 200px;" id="localtion_warehouses_id_<?= $i ?>" name="items[<?= $i ?>][localtion_warehouses_id]" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                        </select>
                                                                    </div>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?= $value['unit'] ?>
                                                            </td>
                                                            <td>
                                                                <div class="<?= ($value['type'] == 'tools' ? 'hide' : '') ?>">
                                                                    <div class="form-group" app-field-wrapper="date"><label for="date_sx" class="control-label"><?= _l('ch_date_of_manufacture') ?></label>
                                                                        <div class="input-group date"><input type="text" id="date_sx_<?= $i ?>" name="items[<?= $i ?>][date_sx]" class="form-control datepicker date_sx maindate_sx" value="<?= _d($value['date_sx']) ?>" autocomplete="off">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar calendar-icon"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    <!--  -->
                                                                    <div class="form-group" app-field-wrapper="date"><label for="date_sd" class="control-label">Ngày sử dụng</label>
                                                                        <div class="input-group date"><input type="text" id="date_sd_<?= $i ?>" name="items[<?= $i ?>][date_sd]" class="form-control datepicker date_sd maindate_sd" value="<?= _d($value['date_sd']) ?>" autocomplete="off">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar calendar-icon"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    <div class="form-group" app-field-wrapper="date_use">
                                                                        <label for="date_use" class="control-label"><?= _l('ch_items_date_use') ?></label>
                                                                        <input style="width: 160px;" type="number" id="date_use" name="items[<?= $i ?>][date_use]" class="form-control maindateuse" value="<?= ($value['date_use']) ?>" aria-invalid="false">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="hide">
                                                                <input onchange="formatNumBerKeyUpCus(this)" readonly class="mainQuantity H_input height_auto" type="text" name="items[<?= $i ?>][quantity]" max="<?= $value['quantity'] ?>" value="<?= formatNumber($value['quantity']) ?>" />
                                                            </td>
                                                            <td>
                                                                <input onchange="formatNumBerKeyUpCus(this)" class="mainQuantityNet H_input height_auto" min="1" type="text" name="items[<?= $i ?>][quantity_net]" value="<?= formatNumber($value['quantity_net']) ?>" />
                                                            </td>
                                                            <td class="<?= $view_price ?>">
                                                                <input onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price no-drop" readonly    type="text" name="items[<?= $i ?>][price]" value="<?= number_format($value['price']) ?>" />
                                                            </td>
                                                            <td class="align_right promotion_suppliers" <?= $view_price ?>><input class="hide" name="items[<?= $i ?>][promotion_suppliers_1]" id="promotion_suppliers_1" value="<?= $value['promotion_suppliers_1'] ?>"><input class="hide" id="promotion_suppliers" name="items[<?= $i ?>][promotion_suppliers]" value="<?= $value['promotion_suppliers'] ?>">
                                                                <p><?= number_format($value['promotion_suppliers']) ?></p>
                                                            </td>
                                                            <td class="<?= $view_price ?> hide">
                                                                <select class="selectpicker tax" name="items[<?php echo $i; ?>][tax_id]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                    <option value data-taxrate="0"><?= _l('no_tax') ?></option>
                                                                    <?php foreach ($tax as $t) { ?>
                                                                        <option value="<?php echo $t['id']; ?>" data-taxrate="<?= $t['taxrate'] ?>" <?php echo ($t['id'] == $value['tax_id'] ? 'selected' : '') ?>>
                                                                            <?= $t['name'] ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                                <input type="hidden" class="tax_rate" name="items[<?php echo $i; ?>][tax_rate]" value="<?= $value['tax_rate'] ?>">
                                                            </td>
                                                            <td class="align_right amount <?= $view_price ?>">
                                                                <?= number_format($value['amount']) ?></td>
                                                            <td><textarea style="width: 100%;" class="note" name="items[<?php echo $i; ?>][note]"><?= $value['note'] ?></textarea>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="barcode hide" name="items[<?php echo $i; ?>][barcode]" class="type" value="" /><a href="#" class="btn btn-danger pull-right" style="margin-left: 12px;" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a><a href="#" class="btn btn-success pull-right" onclick="barcode(this); return false;"><i class="fa fa-barcode"></i></a>
                                                            </td>
                                                        </tr>
                                                <?php
                                                        $i++;
                                                        $totalQuantity += $value['quantity'];
                                                        $totalQuantity_approve += $value['quantity_net'];
                                                    }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <div style="width: 85%;" class=" pull-left">
                    <table class="table tnh-tb noMargin table-color_sum dont-responsive-table">
                        <tbody>
                            <tr>
                                <td>
                                    <span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
                                </td>
                                <td class="total_quantity_all">
                                    <?php echo $totalQuantity ?>
                                </td>
                                <td>
                                    <span class="bold"><?php echo _l('item_quantity_approve'); ?> :</span>
                                </td>
                                <td class="total_quantity_approve">
                                    <?php echo $totalQuantity_approve ?>
                                </td>
                                <td>
                                    <span class="bold"><?php echo _l('total_price'); ?> :</span>
                                </td>
                                <td class="total_price <?= $view_price ?>">
                                    <?php echo $totalQuantity_approve ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="btn btn-info pull-right form-submitersss"><?= _l('submit') ?>
                </a>
                <a style="margin-right: 10px;" href="<?= admin_url('purchase_order') ?>" class="btn btn-default pull-right"><?= _l('go_back') ?>
                </a>
            </div>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    $(document).on('change', '#suppliers_id', (e) => {
        var currentQuantityInput = $(e.currentTarget);

        var id = $(currentQuantityInput).val();
        if (id == '') {
            $('.total_debt').addClass('hide');
        } else {
            $.post(admin_url + 'suppliers/get_debt/' + id, {
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                3
                data = JSON.parse(data);
                if (data.success == true) {
                    $('.total_debt').removeClass('hide');
                    $('.total_debt').html('<?= _l('ch_wanring_debt_limit') ?><b>' + data.total + '</b>');
                } else {
                    $('.total_debt').addClass('hide');
                }
            });
        }
    });
    $(function() {
        $('#suppliers_id').change();
        // $('#warehouse_id').change();
    });
    var warehouse_old;
    $(document).on('change', '#warehouse_id', (e) => {
        var warehouse_id = $('#warehouse_id').val();
        if (warehouse_id != '') {
            if (!$('table.item-import tbody tr.item').find('input[value=hau]').length) {
                createTrItemfist();
            }
        }
        var items = $('table.item-import tbody').find('tr.item');
        if (items.length > 1) {
            var r = confirm("<?php echo _l('ch_alert_change_items'); ?>");
            if (r == false) {
                $('#warehouse_id').selectpicker('val', warehouse_old);
                warehouse_id = warehouse_old;
                return false;
            } else {
                $.each(items, (index, value) => {
                    if ($(value).find('td:nth-child(1)').find('input.type').val() != 'hau') {
                        var indexs = $(value).find('td:nth-child(4)').find(
                            'select.localtion_warehouses_id');
                        var plan_id = $(value).find('input#plan_id').val();
                        loadLocaltion_warehouses_change(warehouse_id, indexs, plan_id);
                    }
                });
            }
            return false;
        }
        warehouse_old = warehouse_id;
    })
    var button_create = () => {
        var warehouse_id = $('#warehouse_id').val();
        if ((warehouse_id != '')) {
            if (!$('table.item-import tbody tr.item').find('input[value=hau]').length) {
                createTrItemfist();
            }
        } else {
            alert_float('warning', '<?= _l('alert_warehouse') ?>');
            return;
        }
    }
    var type_of_document = <?php echo $type_of_document; ?>;
    var idd = <?php echo $id; ?>;
    var id_import = <?php echo $id_import; ?>;
    $('.form-submitersss').on('click', (e) => {
        var product_id = '';
        var test_quantity = 0;
        var items = $('table.item-import tbody tr');
        if (items.length == 0) {
            alert_float('danger', '<?= _l('ch_not_items') ?>');
            return;
        }
        if (items.length == 1) {
            if (($('table.item-import tbody tr').find('input[value="hau"].type').length > 0)) {
                alert_float('danger', '<?= _l('ch_not_items') ?>');
                return;
            }
        }

        $.each(items, (index, value) => {
            if ($(value).find('td:nth-child(1)').find('input.type').val() != 'hau') {
                var quantitys = 0;
                $.each(items, (is, vs) => {
                    if ($(vs).find('td:nth-child(1)').find('input#product_id').val() == $(value)
                        .find('td:nth-child(1)').find('input#product_id').val() && $(value).find(
                            'input#plan_id').val() == $(vs).find('input#plan_id').val()) {
                        quantitys = Number(quantitys) + Number(unformat_number($(value).find('td:nth-child(8)')
                            .find('input').val()));
                    }
                });
                var type = $(value).find('td:nth-child(1)').find('input').val() + '|' + $(value).find(
                        'td:nth-child(1)').find('input#product_id').val() + '|' + quantitys + '|' + $(value)
                    .find('input#plan_id').val() + '|' + $(value)
                    .find('input#idd').val();

                if (product_id.indexOf(type) == -1) {
                    product_id += type + ',';
                }
            }
        });
        dataString = {
            id_import: id_import,
            product_id: product_id,
            type_of_document: type_of_document,
            id: idd,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>import/test_quantity/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                if (data.test_quantity > 0) {
                    alert('<?= _l('ch_limit_items') ?>');

                    $.each(items, (index, value) => {
                        if ($(value).find('td:nth-child(1)').find('input.type').val() != 'hau') {
                            if (($(value).find('td:nth-child(1)').find('input.type').val() == data.items[index].type) && $(value).find('td:nth-child(1)').find('input#product_id').val() == data.items[index].id_product) {
                                $(value).find('td:nth-child(5)').find('input.mainQuantity').val(data.items[index].quantity);
                                $(value).find('td:nth-child(6)').find('input.mainQuantityNet').keyup();
                            }
                        }
                    });
                    return;
                } else {
                    if ($('input.error').length) {
                        e.preventDefault();
                        alert('<?= _l('ch_invalid_value') ?>');
                        return;
                    }
                    var a = confirm('<?= _l('ch_you_want_update') ?>');
                    if (a === false) {
                        e.preventDefault();
                    } else {
                        $('#import-form').submit();
                    }
                }
            }
        });

    });

    function init_ajax_searchs(e, t, a, i) {
        var n = $("body").find(t);
        var h = t;
        if (n.length) {
            var s = {
                ajax: {
                    url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                    data: function() {
                        var type = $('#type_items').val();
                        var id_order = $('#id_order').val();
                        var t = {
                            [csrfData.token_name]: csrfData.hash
                        };
                        return t.type = e, t.rel_id = "", t.q = "{{{q}}}", t.type_items = type, t.id_order =
                            id_order, void 0 !== a && jQuery.extend(t, a), t
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
                    for (var t = [], a = e.length, i = 0; i < a; i++) {
                        var n = {
                            value: e[i].id,
                            text: e[i].name,
                            type_items: e[i].type_items
                        };
                        t.push(n)
                    }
                    findItemasdsad(t, h);
                },
                preserveSelectedPosition: "after",
                preserveSelected: !0
            };
            n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s);

        }
    }

    $(function() {
        // validate_invoice_form();
        _validate_form($('#import-form'), {
            date: "required",
            suppliers_id: "required",
            number: "required",
            warehouse_id: "required",
            localtion_warehouses_id: "required"
        });
    });
    var itemList = <?php echo json_encode($type_items); ?>;
    var findItemasdsad = (data, h) => {
        setTimeout(function() {
            $(h).find('option:gt(0)').remove();
            $(h).selectpicker('refresh');
            var count = data.length;
            var html = '';
            $.each(data, function(key, value) {
                if (key == 0) {
                    html += '<optgroup label="' + value.text + '">';
                } else if (value.value == 'h') {
                    html += '</optgroup>';
                    html += '<optgroup label="' + value.text + '">';
                } else {
                    html += '<option data-id=' + value.type_items + ' value="' + value.value + '">' +
                        value.text + '</option>';
                }
            });
            html += '</optgroup>';
            $(h).html(html);
            $(h).selectpicker('refresh');
            if (count > 3) {
                $(h).parents().find('.status').addClass('hide');
            } else {
                $(h).parents().find('.status').removeClass('hide');
            }
        }, 1);
    };
    var findItem = (type) => {
        var itemResult;
        $.each(itemList, (index, value) => {
            if (value.type == type) {
                itemResult = value.name;
                return false;
            }
        });
        return itemResult;
    };
    appendtype();

    function appendtype() {

        var items = $('table.item-import tbody').find('tr.item');
        $.each(items, (index, value) => {
            // $('#custom_item_select_' + index).select2();
            $('#custom_item_select_' + index).select2({
                formatResult: repoFormatHtml,
                formatSelection: repoFormatHtml,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
            var type = $(value).find('td:nth-child(2)').find('input#type').val();
            var name_type =
                '<span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +
                findItem(type) + '</span>';
            $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);
            var warehouse_id = $('#warehouse_id').val();
            $('#localtion_warehouses_id_' + index).select2();
            var plan_id = $(value).find('input#plan_id').val();
            loadLocaltion_warehouses(warehouse_id, index, plan_id);
        });
    }

    function countrow() {
        if ($('table.item-import tbody tr.item').find('input[value=hau]').length == 0) {
            createTrItemfist();
        }
    }

    $(document).on('change', '.custom_item_select', (e) => {
        var warehouse_id = $('#warehouse_id').val();
        if (empty(warehouse_id)) {
            alert_float('warning', '<?= _l('alert_warehouse') ?>');
            return;
        }
        var currentQuantityInput = $(e.currentTarget);

        var id = $(currentQuantityInput).val();
        if (id == '') {} else {
            id = id.split("__");
            item_id = id[0];
            var type = $('option:selected', currentQuantityInput).attr('data-id');
            var plan_id = $('option:selected', currentQuantityInput).attr('data-plan');
            var idd = $('option:selected', currentQuantityInput).attr('data-idd');
            var id_order = $('#id_order').val();
            var new_tr = currentQuantityInput.parents('tr');
            // var plan_id = new_tr.find('.plan_id').val();
            $.post(admin_url + 'import/get_items_order/' + item_id + '/' + type + '/' + id_order + '/' + plan_id + '/' + idd, {
                [csrfData['token_name']]: csrfData['hash']
            }, function(item) {
                var item = JSON.parse(item);
                createTrItem(item, currentQuantityInput, type);
            });
        }
    });
    var uniqueArray = <?= $i ?>;
    var taxes_dropdown_template = <?= json_encode($taxes) ?>;
    var createTrItem = (item, currentQuantityInput, type) => {
        if (typeof(item) == 'undefined' || item.length == 0) return;

        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;" src="' + item.avatar +
            '"><br><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +
            findItem(type) + '</span>';
        var new_tr = currentQuantityInput.parents('tr');
        new_tr.find('.text_date').addClass('hide');
        if (type == 'nvl') {
            new_tr.find('.text_date').removeClass('hide');
        }
        if (type == 'product') {
            new_tr.find('.text_date').removeClass('hide');
        }
        var count = new_tr.find('td > input.count').val();
        new_tr.find('td.avatart').html(name_type + '\
        <input type="hidden" id="type" name="items[' + count +'][type]" value="' + type + '" />\
        <input type="hidden" class="id" id="product_id" name="items[' + count +'][id]" value="' + item.id + '" />\
        <input type="hidden" class="plan_id" id="plan_id" name="items[' +count +'][plan_id]" value="' + item.type_plan + '" />\
        <input type="hidden" class="idd" id="idd" name="items[' + count + '][idd]" value="' + item.idd + '" />\
        <input type="hidden" class="recipe" id="recipe" name="items[' + count + '][recipe]" value="' + item.info_items.recipe + '" />\
        <input type="hidden" class="paper" id="paper" name="items[' + count + '][paper]" value="' + item.info_items.paper + '" />\
        <input type="hidden" class="longs" id="longs" name="items[' + count + '][longs]" value="' + item.info_items.longs + '" />\
        <input type="hidden" class="wide" id="wide" name="items[' + count + '][wide]" value="' + item.info_items.wide + '" />\
        ');
        var unit_name = item.unit_name;
        if (item.unit_name == null) {
            unit_name = '';
        }
        var unit_name_payment = item.unit_name_payment;
        if (item.unit_name_payment == null) {
            unit_name_payment = '';
        }
        var unit_name_stock = item.unit_name_stock;
        if (item.unit_name_stock == null) {
            unit_name_stock = '';
        }
        new_tr.find('td > input.exchange_standard_unit').val((item.exchange_unit));
        new_tr.find('td > input.exchange_stock').val((item.exchange_standard_unit));
        new_tr.find('td > input.exchange_payment').val((item.exchange_unit_payment));
        new_tr.find('span.unit_name').html('/' + unit_name);
        new_tr.find('span.unit_name_payment').html('/' + unit_name_payment);
        new_tr.find('span.unit_name_stock').html('/' + unit_name_stock);
        new_tr.find('.color').html(item.color);
        if (item.code_plan != null) {
            new_tr.find('.code_plan').html('<div class="label label-success">' + item.code_plan + '</div>');
            new_tr.find('.td_location').html(
                '<div class="label label-danger">Vị trí tự động theo KHSX</div><input type="hidden"  name="items[' +
                count + '][localtion_warehouses_id]" value="0">')
        }
        new_tr.find('td > input.mainQuantity').val(tnhFormatNumber(item.quantity_suppliers));
        new_tr.find('td > input.mainQuantity').attr('data-max', item.quantity_suppliers);
        check = new_tr.find('td > input.mainQuantityNet').attr('data-check');
        if (check == 1) {

        } else {
            new_tr.find('td > input.mainQuantityNet').val(tnhFormatNumber(item.quantity_suppliers));
        }
        new_tr.find('td > input.mainQuantityNet').focus();
        new_tr.find('td > input.price').val(tnhFormatNumber(item.price_suppliers));
        new_tr.find('td > input.lot_code').val((item.lot_code));
        new_tr.find('select.tax').selectpicker('val', item.tax_id);
        new_tr.find('input.tax_rate').val(item.tax_rate);
        new_tr.find('td.unit_name').html(unit_name);
        var promotion_suppliers = item.promotion_expected / item.quantity_purchase_order;

        new_tr.find('td.promotion_suppliers').find('p').text(tnhFormatNumber(promotion_suppliers * item
            .quantity_suppliers));
        //giá trị trên 1 sản phẩm!
        new_tr.find('td.promotion_suppliers').find('> input#promotion_suppliers_1').val(promotion_suppliers);
        new_tr.find('td.promotion_suppliers').find('> input#promotion_suppliers').val(promotion_suppliers * item
            .quantity_suppliers);
        new_tr.find('td.delete').html('<input type="text" class="barcode hide" name="items[' + count +
            '][barcode]" class="type" value="" /><a href="#" class="btn btn-danger pull-right" style="margin-left: 12px;" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a><a href="#" class="btn btn-success pull-right" onclick="barcode(this); return false;"><i class="fa fa-barcode"></i></a>'
        );
        var warehouse_id = $('#warehouse_id').val();
        loadLocaltion_warehouses(warehouse_id, count, item.type_plan);
        countrow();
        calculateTotal(currentQuantityInput);
        new_tr.find('td > input.mainQuantityNet').change();
    }
    var createTrItemfist = () => {

        if ($('.dataTables_empty').length) {
            $('.dataTables_empty').parents('tr').remove();
        }
        var name_type =
            '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?= base_url('assets/images/preview-not-available.jpg') ?>">';
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatart" style="text-align: center;">' + name_type +
            '<input type="hidden" name="items[' + uniqueArray + '][type]" class="type" value="hau" /></td>');

        var td2 = $('<td><input type="hidden" class="count" value="' + uniqueArray + '" /><div class="form-group ">\
								             <select style="width: 200px" class="custom_item_select" id="custom_item_select_' + uniqueArray +
            '" name="items[' + uniqueArray + '][id_item]" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             	<?php echo $html; ?>\
								             </select>\
								        </div><br><br><div class="color"></div><div style="margin-top: 5px;" class="code_plan"></div></td>');
        var td12 = $('<td class="hide"><input style="width: 100px" class="lot_code form-control height_auto " type="text" name="items[' + uniqueArray + '][lot_code]" value="" /></td>');
        var td3 = $('<td class="td_location" style="text-align:center"><div class="form-group ">\
								             <select class="localtion_warehouses_id" id="localtion_warehouses_id_' + uniqueArray +
            '" name="items[' + uniqueArray + '][localtion_warehouses_id]" style="width: 200px;" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             </select>\
								        </div></td>');
        var td4 = $('<td class="unit_name text-center hide"></td>');
        const date_sx = '<div class="form-group" app-field-wrapper="date"><label for="date_sx" class="control-label"><?= _l('ch_date_of_manufacture') ?></label>\
                        <div class="input-group date"><input  type="text" id="date_sx_' + uniqueArray + '" name="items[' + uniqueArray + '][date_sx]" class="form-control datepicker date_sx maindate_sx" value="<?= _d(date('Y-m-d')) ?>" autocomplete="off">\
                            <div class="input-group-addon">\
                                <i class="fa fa-calendar calendar-icon"></i>\
                            </div>\
                        </div>\
                    </div>';
        const date_sd = '<br><div class="form-group" app-field-wrapper="date"><label for="date_sd" class="control-label"><?= _l('ch_items_dateed') ?></label>\
                        <div class="input-group date"><input  type="text" id="date_sd_' + uniqueArray + '" name="items[' + uniqueArray + '][date_sd]" class="form-control datepicker date_sd maindate_sd" value="" autocomplete="off">\
                            <div class="input-group-addon">\
                                <i class="fa fa-calendar calendar-icon"></i>\
                            </div>\
                        </div>\
                    </div>';
        const date_use = '<br><div class="form-group" app-field-wrapper="date_use">\
                                <label for="date_use" class="control-label"><?= _l('ch_items_date_use') ?></label>\
                                <input style="width: 160px;" type="number" id="date_use" name="items[' + uniqueArray + '][date_use]" class="form-control maindateuse" value="0" aria-invalid="false">\
                            </div>';
        var td19 = $('<td class=""><div class="text_date hide">' + date_sx + date_sd + date_use + '</div></td>');
        var td5 = $(
            '<td class="hide"><input onchange="formatNumBerKeyUpCus(this)" readonly class="mainQuantity H_input height_auto" type="text" name="items[' +
            uniqueArray + '][quantity]" value="1" /></td>');
        var td6 = $(
            '<td ><input onchange="formatNumBerKeyUpCus(this)" style="width: 50px" class="mainQuantityNet H_input height_auto"  type="text" name="items[' +
            uniqueArray + '][quantity_net]" value="" /><input style="width: 100px"  class="hide height_auto H_input exchange_standard_unit" type="text" name="items[' + uniqueArray + '][exchange_standard_unit]" value="1" /><span class="unit_name"></span></td>');
        var td20 = $('<td class="text-center"><span class="text_mainquantity_stock text-center">0</span><span class="unit_name_stock"></span><input style="width: 100px"  class="hide height_auto H_input mainquantity_stock" type="text" name="items[' + uniqueArray + '][quantity_stock]" value="1" /><input style="width: 100px"  class=" hide height_auto H_input exchange_stock" type="text" name="items[' + uniqueArray + '][exchange_stock]" value="1" /></td>');
        var td21 = $('<td class="text-center"><span class="text_mainquantity_payment">0</span><span class="unit_name_payment"></span><input style="width: 100px" class="hide height_auto H_input mainquantity_payment" type="text" name="items[' + uniqueArray + '][quantity_payment]" value="1" /><input style="width: 100px"  class="hide height_auto H_input exchange_payment" type="text" name="items[' + uniqueArray + '][exchange_payment]" value="1" /></td>');





        var td7 = $(
            '<td class="<?= $view_price ?>"><input onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price no-drop" readonly   type="text" name="items[' +
            uniqueArray + '][price]" value="0" /></td>');
        var td8 = $('<td class="align_right promotion_suppliers <?= $view_price ?>"><input class="hide" name="items[' +
            uniqueArray +
            '][promotion_suppliers_1]" id="promotion_suppliers_1"  value="0"><input class="hide" id="promotion_suppliers" name="items[' +
            uniqueArray + '][promotion_suppliers]" value="0"><p>0</p></td>');
        var taxTemplate = taxes_dropdown_template;
        taxTemplate = taxTemplate.replace('name=""', 'name="items[' + uniqueArray + '][tax_id]"');
        var td9 = $('<td class="<?= $view_price ?> hide">' + taxTemplate +
            '<input type="hidden" class="tax_rate" name="items[' + uniqueArray + '][tax_rate]" value="0"></td>');
        var td10 = $('<td class="align_right amount <?= $view_price ?>">0</td>');
        var td11 = $('<td><textarea style="width: 100%;" class="note" name="items[' + uniqueArray +
            '][note]"></textarea></td>');
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td12);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td19);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td20);
        newTr.append(td21);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        newTr.append('<td class="delete"></td>');
        $('table.item-import tbody').append(newTr);
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        // $('#custom_item_select_' + uniqueArray).select2();
        $('#custom_item_select_' + uniqueArray).select2({
            formatResult: repoFormatHtml,
            formatSelection: repoFormatHtml,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function(m) {
                return m;
            }
        });
        var opt = {
            format: 'd/m/Y',
            timepicker: false,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 0,
        };
        $('#date_sx_' + uniqueArray).datetimepicker(opt);
        $('#date_sd_' + uniqueArray).datetimepicker(opt);
        $('#localtion_warehouses_id_' + uniqueArray).select2();
        uniqueArray++;
        getTotalPrice();
    }

    function repoFormatHtml(item) {
        var originalOption = item.element;

        if ($(originalOption).data('id') == 'nvl' || $(originalOption).data('id') == 'product') {
            return "<b>" + $(originalOption).data('text') + "</b>"
        } else {
            return "<b>" + $(originalOption).data('text') + "</b>"
        }
    }

    var createTrItemfist_ch = (key, idd) => {
        item_id = idd;
        idd = idd.split('__');
        plan_id = idd[1];
        if ($('.dataTables_empty').length) {
            $('.dataTables_empty').parents('tr').remove();
        }
        var name_type =
            '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?= base_url('assets/images/preview-not-available.jpg') ?>">';
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatart" style="text-align: center;">' + name_type +
            '<input type="hidden" name="items[' + uniqueArray +
            '][type]" class="type" value="hau" /><input type="hidden" name="items[' + uniqueArray +
            '][plan_id]" class="plan_id_check" id="plan_id_check" value="' + plan_id + '" /></td>');

        var td2 = $('<td><input type="hidden" class="count" value="' + uniqueArray + '" /><div class="form-group ">\
								             <select style="width: 200px" class="custom_item_select" id="custom_item_select_' + uniqueArray +
            '" name="items[' + uniqueArray + '][id_item]" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             	<?php echo $html; ?>\
								             </select>\
								        </div><div class="color"></div><div style="margin-top: 5px;" class="code_plan"></div></td>');
        var td12 = $('<td  class="hide"><input  style="width: 100px" class="lot_code form-control height_auto" type="text" name="items[' + uniqueArray + '][lot_code]" value="" /></td>');
        var td3 = $('<td class="td_location" style="text-align:center"><div class="form-group">\
								             <select class="localtion_warehouses_id" id="localtion_warehouses_id_' + uniqueArray +
            '" name="items[' + uniqueArray + '][localtion_warehouses_id]" style="width: 200px;" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             </select>\
								        </div></td>');
        var td4 = $('<td class="unit_name text-center hide"></td>');
        const date_sx = '<div class="form-group" app-field-wrapper="date"><label for="date_sx" class="control-label"><?= _l('ch_date_of_manufacture') ?></label>\
                        <div class="input-group date"><input  type="text" id="date_sx_' + uniqueArray + '" name="items[' + uniqueArray + '][date_sx]" class="form-control datepicker date_sx maindate_sx" value="<?= _d(date('Y-m-d')) ?>" autocomplete="off">\
                            <div class="input-group-addon">\
                                <i class="fa fa-calendar calendar-icon"></i>\
                            </div>\
                        </div>\
                    </div>';
        const date_sd = '<br><div class="form-group" app-field-wrapper="date"><label for="date_sd" class="control-label"><?= _l('ch_items_dateed') ?></label>\
                        <div class="input-group date"><input  type="text" id="date_sd_' + uniqueArray + '" name="items[' + uniqueArray + '][date_sd]" class="form-control datepicker date_sd maindate_sd" value="" autocomplete="off">\
                            <div class="input-group-addon">\
                                <i class="fa fa-calendar calendar-icon"></i>\
                            </div>\
                        </div>\
                    </div>';
        const date_use = '<br><div class="form-group" app-field-wrapper="date_use">\
                                <label for="date_use" class="control-label"><?= _l('ch_items_date_use') ?></label>\
                                <input style="width: 160px;" type="number" id="date_use" name="items[' + uniqueArray + '][date_use]" class="form-control maindateuse" value="0" aria-invalid="false">\
                            </div>';
        var td19 = $('<td class=""><div class="text_date hide">' + date_sx + date_sd + date_use + '</div></td>');
        var td5 = $(
            '<td class="hide"><input onchange="formatNumBerKeyUpCus(this)" readonly class="mainQuantity H_input height_auto" type="text" name="items[' +
            uniqueArray + '][quantity]" value="1" /></td>');
        // var td6 = $(
        //     '<td><input onchange="formatNumBerKeyUpCus(this)" class="mainQuantityNet H_input height_auto"  type="text" name="items[' +
        //     uniqueArray + '][quantity_net]" value="1" /></td>');
        var td6 = $(
            '<td ><input onchange="formatNumBerKeyUpCus(this)" style="width: 50px" class="mainQuantityNet H_input height_auto"  type="text" name="items[' +
            uniqueArray + '][quantity_net]" value="" /><input style="width: 100px"  class="hide height_auto H_input exchange_standard_unit" type="text" name="items[' + uniqueArray + '][exchange_standard_unit]" value="1" /><span class="unit_name"></span></td>');
        var td20 = $('<td class="text-center"><span class="text_mainquantity_stock text-center">0</span><span class="unit_name_stock"></span><input style="width: 100px"  class="hide height_auto H_input mainquantity_stock" type="text" name="items[' + uniqueArray + '][quantity_stock]" value="1" /><input style="width: 100px"  class=" hide height_auto H_input exchange_stock" type="text" name="items[' + uniqueArray + '][exchange_stock]" value="1" /></td>');
        var td21 = $('<td class="text-center"><span class="text_mainquantity_payment">0</span><span class="unit_name_payment"></span><input style="width: 100px" class="hide height_auto H_input mainquantity_payment" type="text" name="items[' + uniqueArray + '][quantity_payment]" value="1" /><input style="width: 100px"  class="hide height_auto H_input exchange_payment" type="text" name="items[' + uniqueArray + '][exchange_payment]" value="1" /></td>');

        var td7 = $(
            '<td class="<?= $view_price ?>"><input onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price no-drop" readonly    type="text" name="items[' +
            uniqueArray + '][price]" value="0" /></td>');
        var td8 = $('<td class="align_right promotion_suppliers <?= $view_price ?>"><input class="hide" name="items[' +
            uniqueArray +
            '][promotion_suppliers_1]" id="promotion_suppliers_1"  value="0"><input class="hide" id="promotion_suppliers" name="items[' +
            uniqueArray + '][promotion_suppliers]" value="0"><p>0</p></td>');
        var taxTemplate = taxes_dropdown_template;
        taxTemplate = taxTemplate.replace('name=""', 'name="items[' + uniqueArray + '][tax_id]"');
        var td9 = $('<td class="<?= $view_price ?> hide">' + taxTemplate +
            '<input type="hidden" class="tax_rate" name="items[' + uniqueArray + '][tax_rate]" value="0"></td>');
        var td10 = $('<td class="align_right amount <?= $view_price ?>">0</td>');
        var td11 = $('<td><textarea style="width: 100%;" class="note" name="items[' + uniqueArray +
            '][note]"></textarea></td>');
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td12);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td19);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td20);
        newTr.append(td21);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        newTr.append('<td class="delete"></td>');
        $('table.item-import tbody').prepend(newTr);
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        // $('#custom_item_select_' + uniqueArray).select2();
        $('#custom_item_select_' + uniqueArray).select2({
            formatResult: repoFormatHtml,
            formatSelection: repoFormatHtml,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('#custom_item_select_' + uniqueArray).val(item_id);
        $('#custom_item_select_' + uniqueArray).trigger('change');
        $('#localtion_warehouses_id_' + uniqueArray).select2();
        var opt = {
            format: 'd/m/Y',
            timepicker: false,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 0,
        };
        $('#date_sx_' + uniqueArray).datetimepicker(opt);
        $('#date_sd_' + uniqueArray).datetimepicker(opt);
        uniqueArray++;
        getTotalPrice();
    }
    var createTrItemfist_ch_v2 = (key, idd) => {
        if ($('.dataTables_empty').length) {
            $('.dataTables_empty').parents('tr').remove();
        }
        var name_type =
            '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?= base_url('assets/images/preview-not-available.jpg') ?>">';
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatart" style="text-align: center;">' + name_type +
            '<input type="hidden" name="items[' + uniqueArray + '][type]" class="type" value="hau" /></td>');

        var td2 = $('<td><input type="hidden" class="count" value="' + uniqueArray + '" /><div class="form-group ">\
								             <select style="width: 200px" class="custom_item_select" id="custom_item_select_' + uniqueArray +
            '" name="items[' + uniqueArray + '][id_item]" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             	<?php echo $html; ?>\
								             </select>\
								        </div><div class="color"></div></td>');
        var td3 = $('<td class="td_location" style="text-align:center"><div class="form-group">\
								             <select class="localtion_warehouses_id" id="localtion_warehouses_id_' + uniqueArray +
            '" name="items[' + uniqueArray + '][localtion_warehouses_id]" style="width: 200px;" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             </select>\
								        </div></td>');
        var td4 = $('<td class="unit_name text-center"></td>');
        var td5 = $(
            '<td class="hide"><input onchange="formatNumBerKeyUpCus(this)" readonly class="mainQuantity H_input height_auto" type="text" name="items[' +
            uniqueArray + '][quantity]" value="1" /></td>');
        var td6 = $(
            '<td><input onchange="formatNumBerKeyUpCus(this)" class="mainQuantityNet H_input height_auto"  type="text" name="items[' +
            uniqueArray + '][quantity_net]" data-check="1" value="" /></td>');
        var td7 = $(
            '<td class="<?= $view_price ?>"><input onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price no-drop" readonly   type="text" name="items[' +
            uniqueArray + '][price]" value="0" /></td>');
        var td8 = $('<td class="align_right promotion_suppliers <?= $view_price ?>"><input class="hide" name="items[' +
            uniqueArray +
            '][promotion_suppliers_1]" id="promotion_suppliers_1"  value="0"><input class="hide" id="promotion_suppliers" name="items[' +
            uniqueArray + '][promotion_suppliers]" value="0"><p>0</p></td>');
        var taxTemplate = taxes_dropdown_template;
        taxTemplate = taxTemplate.replace('name=""', 'name="items[' + uniqueArray + '][tax_id]"');
        var td9 = $('<td class="<?= $view_price ?>">' + taxTemplate +
            '<input type="hidden" class="tax_rate" name="items[' + uniqueArray + '][tax_rate]" value="0"></td>');
        var td10 = $('<td class="align_right amount <?= $view_price ?>">0</td>');
        var td11 = $('<td><textarea style="width: 100%;" class="note" name="items[' + uniqueArray +
            '][note]"></textarea></td>');
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        newTr.append('<td class="delete"></td>');
        $('table.item-import tbody').prepend(newTr);
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        // $('#custom_item_select_' + uniqueArray).select2();
        $('#custom_item_select_' + uniqueArray).select2({
            formatResult: repoFormatHtml,
            formatSelection: repoFormatHtml,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('#custom_item_select_' + uniqueArray).val(idd);
        $('#custom_item_select_' + uniqueArray).trigger('change');
        $('#localtion_warehouses_id_' + uniqueArray).select2();
        uniqueArray++;
        getTotalPrice();
    }

    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
    };
    $(document).on('change', 'select.tax', function(e) {
        var tax_id = $(this).val();
        var tax_rate = parseInt($(this).find('option:selected').attr('data-taxrate'));
        var current_row = $(this).parents('tr');
        if (isNaN(tax_rate)) tax_rate = 0;
        $(this).parents('tr').find('input.tax_rate').val(tax_rate);
        calculateTotal(e.currentTarget);
    });
    // $(document).on('change', '.mainQuantity', (e) => {
    //     var currentQuantityInput = $(e.currentTarget);
    //     mainQuantity = currentQuantityInput.attr('data-max');
    //     if (Number(unformat_number(currentQuantityInput.val())) > Number(parseInt(mainQuantity))) {
    //         currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
    //         currentQuantityInput.attr('data-toggle', 'tooltip');
    //         currentQuantityInput.attr('data-trigger', 'manual');
    //         currentQuantityInput.attr('title', 'Số lượng vượt mức cho phép!');
    //         currentQuantityInput.off('focus', '**').off('hover', '**');
    //         currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this)
    //             .tooltip('show'));
    //         currentQuantityInput.addClass('error');
    //         currentQuantityInput.focus();
    //     } else {
    //         currentQuantityInput.attr('data-toggle', 'tooltip');
    //         currentQuantityInput.attr('data-trigger', 'manual');
    //         currentQuantityInput.attr('title', ' ');
    //         currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this)
    //             .tooltip('show'));
    //         currentQuantityInput.removeClass('error');
    //         currentQuantityInput.attr("style", "");
    //         currentQuantityInput.focus();
    //         calculateTotal(e.currentTarget);
    //     }
    // });
    // $(document).on('keyup', '.mainQuantity', (e) => {
    //     var currentQuantityInput = $(e.currentTarget);
    //     mainQuantity = currentQuantityInput.attr('data-max');
    //     if (Number(unformat_number(currentQuantityInput.val())) > Number(parseInt(mainQuantity))) {
    //         currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
    //         currentQuantityInput.attr('data-toggle', 'tooltip');
    //         currentQuantityInput.attr('data-trigger', 'manual');
    //         currentQuantityInput.attr('title', 'Số lượng vượt mức cho phép!');
    //         currentQuantityInput.off('focus', '**').off('hover', '**');
    //         currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this)
    //             .tooltip('show'));
    //         currentQuantityInput.addClass('error');
    //         currentQuantityInput.focus();
    //     } else {
    //         currentQuantityInput.attr('data-toggle', 'tooltip');
    //         currentQuantityInput.attr('data-trigger', 'manual');
    //         currentQuantityInput.attr('title', ' ');
    //         currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this)
    //             .tooltip('show'));
    //         currentQuantityInput.removeClass('error');
    //         currentQuantityInput.attr("style", "");
    //         currentQuantityInput.focus();
    //         calculateTotal(e.currentTarget);
    //     }
    // });
    // $(document).on('change', '.mainQuantityNet', (e)=>{
    //     var currentQuantityInput = $(e.currentTarget);
    //     var items_detail = currentQuantityInput.parent().parent().find('input#product_id').val();
    //     var items = $('table.item-import tbody').find('tr.item');
    //     var mainQuantityNet = 0;
    //     $.each(items, (index,value)=>{
    //     	if($(value).find('input#product_id').val() == items_detail)
    //     	{
    //         mainQuantityNet += parseFloat($(value).find('.mainQuantityNet').val().replace(/\,/g, ''));
    //     	}
    //     });
    //     mainQuantity=unformat_number(currentQuantityInput.parents('tr').find('.mainQuantity').val());
    // 	if(Number((mainQuantityNet)) > Number(mainQuantity)){
    // 		$.each(items, (index,value)=>{
    // 		if($(value).find('input#product_id').val() == items_detail)
    //     	{
    //             $(value).find('input.mainQuantityNet').attr("style", "width: 100px;border: 1px solid red !important");
    //             $(value).find('input.mainQuantityNet').attr('data-toggle', 'tooltip');
    //             $(value).find('input.mainQuantityNet').attr('data-trigger', 'manual');
    //             $(value).find('input.mainQuantityNet').attr('title', '<?= _l('ch_limit_items') ?>').tooltip('fixTitle').tooltip('show');
    //             $(value).find('input.mainQuantityNet').off('focus', '**').off('hover', '**');
    //             $(value).find('input.mainQuantityNet').tooltip('fixTitle').focus(()=>$(this).tooltip('show')).hover(()=>$(this).tooltip('show'));
    //             $(value).find('input.mainQuantityNet').addClass('error');
    //         }
    //         });
    //     }
    //     else {
    //     	$.each(items, (index,value)=>{
    //     		if($(value).find('input#product_id').val() == items_detail)
    //         	{
    //         		$(value).find('input.mainQuantityNet').attr("style", "");
    // 	            $(value).find('input.mainQuantityNet').attr('title', 'OK!').tooltip('fixTitle').tooltip('show');
    // 	            $(value).find('input.mainQuantityNet').removeClass('error');
    // 	            calculateTotal(e.currentTarget);
    //             }
    //         });
    //     }
    // });
    // $(document).on('keyup', '.mainQuantityNet', (e)=>{
    //     var currentQuantityInput = $(e.currentTarget);
    //     var items_detail = currentQuantityInput.parent().parent().find('input#product_id').val();
    //     var items = $('table.item-import tbody').find('tr.item');
    //     var mainQuantityNet = 0;
    //     $.each(items, (index,value)=>{
    //     	if($(value).find('input#product_id').val() == items_detail)
    //     	{
    //         mainQuantityNet += parseFloat($(value).find('.mainQuantityNet').val().replace(/\,/g, ''));
    //     	}
    //     });
    //     mainQuantity=unformat_number(currentQuantityInput.parents('tr').find('.mainQuantity').val());
    // 	if(Number((mainQuantityNet)) > Number(mainQuantity)){
    // 		$.each(items, (index,value)=>{
    // 		if($(value).find('input#product_id').val() == items_detail)
    //     	{
    //             $(value).find('input.mainQuantityNet').attr("style", "width: 100px;border: 1px solid red !important");
    //             $(value).find('input.mainQuantityNet').attr('data-toggle', 'tooltip');
    //             $(value).find('input.mainQuantityNet').attr('data-trigger', 'manual');
    //             $(value).find('input.mainQuantityNet').attr('title', '<?= _l('ch_limit_items') ?>').tooltip('fixTitle').tooltip('show');
    //             $(value).find('input.mainQuantityNet').off('focus', '**').off('hover', '**');
    //             $(value).find('input.mainQuantityNet').tooltip('fixTitle').focus(()=>$(this).tooltip('show')).hover(()=>$(this).tooltip('show'));
    //             $(value).find('input.mainQuantityNet').addClass('error');
    //         }
    //         });
    //     }
    //     else {
    //     	$.each(items, (index,value)=>{
    //     		if($(value).find('input#product_id').val() == items_detail)
    //         	{
    //         		$(value).find('input.mainQuantityNet').attr("style", "");
    // 	            $(value).find('input.mainQuantityNet').attr('title', 'OK!').tooltip('fixTitle').tooltip('show');
    // 	            $(value).find('input.mainQuantityNet').removeClass('error');
    // 	            calculateTotal(e.currentTarget);
    //             }
    //         });
    //     }
    // });
    $(document).on('keyup', '.price', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);
        var current_row = currentInput.parents('tr');
        let recipe = (current_row.find('.recipe').val());
        let paper = (current_row.find('.paper').val());
        let longs = (current_row.find('.longs').val());
        let wide = (current_row.find('.wide').val());
        let mainQuantity = unformat_number(current_row.find('.mainQuantity').val());
        let mainQuantityNet = unformat_number(current_row.find('.mainQuantityNet').val());
        let promotion_suppliers = current_row.find('#promotion_suppliers_1').val();
        let price = unformat_number(current_row.find('.price').val());
        let tax = current_row.find('.tax_rate').val();

        let exchange_standard_unit = unformat_number(current_row.find('.exchange_standard_unit').val());
        let exchange_stock = unformat_number(current_row.find('.exchange_stock').val());
        let exchange_payment = unformat_number(current_row.find('.exchange_payment').val());

        var quantity_stock = (mainQuantityNet / exchange_stock) * exchange_standard_unit
        current_row.find('.text_mainquantity_stock').text(tnhFormatNumber(quantity_stock));
        current_row.find('.mainquantity_stock').val((quantity_stock));
      
        if (recipe == 1) {
            var quantity_payment = (mainQuantityNet / exchange_payment) * exchange_standard_unit
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 2) {
            var quantity_payment = (mainQuantityNet / exchange_payment) * paper / 100
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 3) {
            var quantity_payment = (mainQuantityNet / exchange_payment) * (longs * wide) / 10000
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        }
        var total = ((quantity_payment * price) - (Number(promotion_suppliers) * quantity_payment)) * (1 + tax / 100);
        current_row.find('.amount').text(tnhFormatNumber(total));
        getTotalPrice();
    };

    function getTotalPrice() {
        var items = $('table.item-import tbody').find('tr.item');
        var totalQuantity = 0;
        var totalQuantityNet = 0;
        var totalPrice = 0;
        $.each(items, (index, value) => {
            if (!empty($(value).find('#type').val())) {
                totalQuantity += parseFloat($(value).find('.mainQuantity').val().replace(/\,/g, ''));
                totalQuantityNet += parseFloat($(value).find('.mainQuantityNet').val().replace(/\,/g, ''));
                totalPrice += parseFloat($(value).find('.amount').text().replace(/\,/g, ''));
            }
        });
        $('.total_quantity_all').text(tnhFormatNumber(totalQuantity));
        $('.total_quantity_approve').text(tnhFormatNumber(totalQuantityNet));
        $('.total_price').text(tnhFormatNumber(totalPrice));
    }
    $('#items-form').on('submit', (e) => {
        if ($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', '<?= _l('ch_invalid_value') ?>');
        }
    });

    function loadLocaltion_warehouses(warehouse_id, id, plan_id = null) {
        var localtion_warehouses = $('#localtion_warehouses_id_' + id);
        var checked = localtion_warehouses.attr('data-id');
        localtion_warehouses.attr('required', true);

        // if (plan_id == null || plan_id == 0) {
        //     localtion_warehouses.attr('required', true);
        // } else {
        //     localtion_warehouses.attr('required', false);
        // }
        if (localtion_warehouses.length) {
            $.post(admin_url + "warehouse/list_localtion_new", {
                warehouse: warehouse_id,
                checked: checked,
                plan_id: plan_id,
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                data = JSON.parse(data);
                localtion_warehouses.html(data.html).find('option').attr('disabled', 'disabled').parents(
                    '#localtion_warehouses_id_' + id).find('option[child="1"]').removeAttr('disabled');
                localtion_warehouses.find('option:nth-child(1)').removeAttr('disabled');
                if (checked == undefined || checked == null) {
                    checked = data.checked;
                }
                localtion_warehouses.select2('val', checked);
            })
        }
    }

    function loadLocaltion_warehouses_change(warehouse_id, indexs, plan_id = null) {
        localtion_warehouses.attr('required', true);
        var localtion_warehouses = indexs;
        var checked = localtion_warehouses.attr('data-id');
        // if (plan_id == null || plan_id == 0) {
        //     localtion_warehouses.attr('required', true);
        // } else {
        //     localtion_warehouses.attr('required', false);
        // }
        localtion_warehouses.find('option:gt(0)').remove();
        if (localtion_warehouses.length) {
            $.post(admin_url + "warehouse/list_localtion_new", {
                warehouse: warehouse_id,
                checked: checked,
                plan_id: plan_id,
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                data = JSON.parse(data);
                console.log(data.checked);
                localtion_warehouses.html(data.html).find('option').attr('disabled', 'disabled').parents(indexs)
                    .find(
                        'option[child="1"]').removeAttr('disabled');
                localtion_warehouses.find('option:nth-child(1)').removeAttr('disabled');
                if (checked == undefined || checked == null) {
                    checked = data.checked;
                }
                localtion_warehouses.select2('val', checked);
            })
        }
    }

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

    $(document).ready(function() {
        $('.table-responsive').on('show.bs.dropdown', function() {
            $('.table-responsive').css("overflow", "inherit");
        });

        $('.table-responsive').on('hide.bs.dropdown', function() {
            $('.table-responsive').css("overflow", "auto");
        })
    });

    function load() {
        var warehouse_id = $('#warehouse_id').val();
        if ((warehouse_id == '')) {
            alert_float('warning', "<?= _l('alert_warehouse') ?>");
            return;
        }
        var id_array = $('[id="type_items_ch[]"]').val();
        if (empty(id_array)) {
            alert_float('warning', '<?= _l('ch_alert_not_chose_items') ?>');
            return;
        }
        $('table.item-import tbody').find('tr.item').remove();
        $.each(id_array, function(key, value) {
            createTrItemfist_ch(key, value);
        });

    }
    $(function() {
        var dt = $('.item-import').DataTable({
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
    setTimeout(function() {
        $('[id="type_items_ch[]"]').parent().find('.bs-select-all').click();
    }, 200);
    $('#custom_item_select_all').select2();
    $(document).on('change', '#custom_item_select_all', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var id = $(currentQuantityInput).val();
        if (id != '') {

            var warehouse_id = $('#warehouse_id').val();
            if ((warehouse_id == '')) {
                alert_float('warning', "<?= _l('alert_warehouse') ?>");
                $('#custom_item_select_all').select2('val', '').trigger('change');
                return;
            } else {
                createTrItemfist_ch_v2(0, id);
                $('#custom_item_select_all').select2('val', '').trigger('change');

            }
        }
    });

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

    function barcode(trItem) {
        var current = $(trItem).parents('tr');
        var items = current.find('input#product_id').val();
        var suppliers_id = $('#suppliers_id').val();

        var quantity = current.find('input.mainQuantityNet').val();
        var type = current.find('input#type').val();
        var text = items + 'F' + type + 'F' + unformat_number(quantity) + 'F' + Date.now() + 'F' + suppliers_id;
        current.find('input.barcode').val(text);
        var url = "<?= admin_url('import/print_pdf_code/') ?>" + text;
        printPdf(url);
    }
    // $(this).caret().start
    $(document).on('keyup', function(e) {
        if (e.keyCode == 113) {
            var current = $(e.target).parents('tr');

            var items = current.find('input#product_id').val();

            if ((typeof items == 'undefined')) {
                alert('Không tìm thấy dữ liệu để in');
                return;
            }
            var suppliers_id = $('#suppliers_id').val();
            var quantity = current.find('input.mainQuantityNet').val();
            var type = current.find('input#type').val();
            var text = items + 'F' + type + 'F' + unformat_number(quantity) + 'F' + Date.now() + 'F' + suppliers_id;
            current.find('input.barcode').val(text);
            var url = "<?= admin_url('import/print_pdf_code/') ?>" + text;
            printPdf(url);
        }
    });

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
        if ((currentQuantityInput.parents('tr').find('.maindate_sd').val() != '') && (currentQuantityInput.parents('tr').find('.date_sx').val() != '')) {
            date_sx = convertToDate(currentQuantityInput.parents('tr').find('.date_sx').val());
            date_sd = convertToDate(currentQuantityInput.parents('tr').find('.maindate_sd').val());
            var diff = Math.abs(date_sd - date_sx); // difference in milliseconds
            var dateOffset = (Number(diff) / (24 * 60 * 60 * 1000)) * 1; //5 days
        } else {
            dateOffset = 0;
        }
        currentQuantityInput.parents('tr').find('input.maindateuse').val(dateOffset);
    })
    $(document).on('change', '.maindate_sd', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if ((currentQuantityInput.parents('tr').find('.maindate_sd').val() != '') && (currentQuantityInput.parents('tr').find('.date_sx').val() != '')) {
            date_sx = convertToDate(currentQuantityInput.parents('tr').find('.date_sx').val());
            date_sd = convertToDate(currentQuantityInput.parents('tr').find('.maindate_sd').val());
            var diff = Math.abs(date_sd - date_sx); // difference in milliseconds
            var dateOffset = (Number(diff) / (24 * 60 * 60 * 1000)) * 1; //5 days
        } else {
            dateOffset = 0;
        }
        currentQuantityInput.parents('tr').find('input.maindateuse').val(dateOffset);
    })
    $(document).on('change', '.maindateuse', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if ((currentQuantityInput.parents('tr').find('.date_sx').val() != '')) {
            date_sx = convertToDatev2(currentQuantityInput.parents('tr').find('.date_sx').val());
            date_sd = currentQuantityInput.parents('tr').find('input.maindateuse').val();
            // var diff = Math.abs(date_sd + (date_sx*(24 * 60 * 60 * 1000))); // difference in milliseconds
            const dates = new Date(date_sx);
            var dateOffset = dates.setTime(dates.getTime() + ((24 * 60 * 60 * 1000) * date_sd));

        } else {
            dateOffset = '';
        }
        currentQuantityInput.parents('tr').find('input.maindate_sd').val(formatDate(dateOffset));
    })
    $(document).on('change', '#date', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var items = $('table.item-import tbody').find('tr.item');
        $.each(items, (index, value) => {
            $(value).find('.maindate_sx').val(currentQuantityInput.val()).trigger('change');
        });
    })
</script>