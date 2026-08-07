<?php init_head(); ?>
<style type="text/css">
    .select2-choice {
        height: 100% !important;
    }

    .select2-choice {
        min-height: 35px !important;
        height: 100% !important;
    }

    .new_contact_form {
        border: 1px solid #bfcbd9;
    }

    .new_contact_form:hover i {
        color: #afafaf;
    }

    .btn_add_contact {
        border: 1px dotted;
    }

    .remove_contact_panel {
        text-align: right;
    }

    .remove_contact_panel i {
        color: #fff;
        font-size: 18px;
    }

    .remove_contact_panel i:hover {
        cursor: pointer;
    }

    .btn_add_contact i {
        font-size: 5em;
        color: #adadad;
    }

    .add_new_contact p {
        margin: 0;
        color: #adadad;
    }

    .add_new_contact {
        text-align: center;
        padding: 10px 0;
    }

    .add_new_contact:hover p,
    .add_new_contact:hover i {
        color: #7b7b7b;
        cursor: pointer;
    }

    .append_html>div {
        padding: 0 0 15px 5px;
    }

    .item-purchase .ui-sortable tr td input {
        width: 80px;
    }

    .classify {
        position: fixed;
        z-index: 999;
        height: 100px;
        width: 300px;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 3px 6px -1px rgba(0, 0, 0, .12), 0 10px 36px -4px rgba(77, 96, 232, .3) !important;
        bottom: -105px;
        left: 20px;
        transition: all 0.5s;
    }

    .classify.active {
        transition: all 0.5s;
        bottom: 15px;
    }

    .classify-html {
        color: #aba6a6;
        text-align: center;
        font-style: italic;
    }

    .classify-title {
        text-align: center;
        padding: 5px;
    }

    .classify img {
        float: left;
        width: 85px;
    }

    .classify-content {
        float: left;
        width: calc(96% - 85px);
        height: 85px;
    }
    .price_suppliers{
        pointer-events: none;
        background-color: #e5e5e5 !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), array('id' => 'supplier_quotes-form', 'class' => '_transaction_form invoice-form'));
            if (isset($items)) {
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
                                $status = format_purchase_status($items->status, '', false);
                            } else {
                                $status = format_purchase_status(-1, '', false);
                            }
                            ?>
                            <span><?= $status ?></span>
                        </div>
                        <?php if (isset($items)) { ?>
                        <?php } ?>
                        <h4 class=" bold no-margin font-medium">
                            <?php echo _l('ch_po_t'); ?>
                        </h4>
                        <hr />
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="alert alert-warning text-center total_debt hide"></div>
                            <div class="panel panel-primary">
                                <div class="panel-heading"><?= _l('lead_general_info') ?></div>
                                <div class="panel-body">
                                    <?php if (isset($items)) {
                                        echo format_purchase_order_father($items->id);
                                    ?>
                                        <br>
                                        <br>
                                    <?php } ?>
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
                                                                <?php echo (isset($items) ? ($items->prefix) : get_option('prefix_purchase_order')); ?>-</span>
                                                            <?php
                                                            $number = sprintf('%06d', ch_getMaxID('id', 'tblpurchase_order') + 1);
                                                            $value = (isset($items) ? ($items->code) : $number);
                                                            ?>
                                                            <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
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
                                                    <?php echo render_date_input('date', '', $value); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="suppliers_id" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('supplier'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="form-group">
                                                            <?php
                                                            $value = (isset($items) ? $items->suppliers_id : '');
                                                            echo render_select('suppliers_id', $suppliers, array('id', 'company'), '', $value);
                                                            ?>
                                                        </div>
                                                        <?php if (has_permission('suppliers', '', 'create')) { ?>
                                                            <span class="input-group-addon">
                                                                <a href="javascript:void(0)" onclick="int_suppliers_view('','true'); return false;"><i class="fa fa-plus"></i></a>
                                                            </span>
                                                        <?php } ?>
                                                    </div>

                                                </td>
                                                <td>
                                                    <label for="id_staff" class="control-label">
                                                        <?php echo _l('als_staff'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value = (isset($items) ? $items->staff_create : get_staff_user_id()); ?>
                                                    <?php echo render_select('id_staff', $staff, array('staffid', array('firstname', 'lastname')), '', $value, array('disabled' => true)); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="currency" class="control-label">
                                                        <?php echo _l('ch_currency'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value = (isset($items) ? $items->currency : CURRENCY_VND); ?>
                                                    <div class="input-group">
                                                        <div class="form-group">
                                                            <input type="text" id="ch_currency" class="hide" value="<?= $value ?>" name="currency_ch">
                                                            <select required="true" class="currency selectpicker" disabled id="currency" style="width: 200px;" name="currency" data-width="100%" data-none-selected-text="<?php echo _l('ch_currency'); ?>">
                                                                <option></option>
                                                                <?php foreach ($currency as $k => $v) { ?>
                                                                    <option data-name="<?= $v['name'] ?>" data-total="<?= $v['amount_to_vnd'] ?>" data-subtext="<?= formatNumber($v['amount_to_vnd']) ?><?= $v['symbol'] ?>" <?= (($v['id'] == $value) ? 'selected' : '') ?> value="<?= $v['id'] ?>"><?= $v['name'] ?> </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <span class="input-group-addon">
                                                            <a href="javascript:void(0)" onclick="edit_currencies(); return false;"><i class="fa fa-pencil"></i></a>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <label for="amount_to_vnd" class="control-label">
                                                        <?php echo _l('rate_currency'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $value = (isset($items) ? formatNumber($items->amount_to_vnd) : 0);
                                                    echo render_input('amount_to_vnd', '', $value, 'text', array('onchange' => "formatNumBerKeyUpCus(this)"));
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="type_items" class="control-label">
                                                        <?php echo _l('type'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $type_order = array(
                                                        array(
                                                            'id' => 1,
                                                            'name' => 'Trong nước',
                                                        ),
                                                        array(
                                                            'id' => 2,
                                                            'name' => 'Nhập khẩu',
                                                        ),
                                                    );
                                                    ?>
                                                    <?php $value_type_order = (isset($items) ? ($items->type_order) : ''); ?>
                                                    <?php
                                                    echo render_select('type_order', $type_order, array('id', 'name'), '', $value_type_order);
                                                    ?>
                                                </td>
                                                <td class="hide">
                                                    <label for="type_items" class="control-label">
                                                        <?php echo _l('ch_type'); ?>
                                                    </label>
                                                </td>
                                                <td class="hide">
                                                    <?php
                                                    echo render_select('type_items', $type_items, array('type', 'name'), '', -1);
                                                    ?>
                                                </td>
                                                <td>
                                                    <label for="type_items" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_delivery_date'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $delivery_date = (isset($items) ? _d($items->delivery_date) : ''); ?>
                                                    <?php echo render_date_input('delivery_date', '', $delivery_date); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2">
                                                    <label for="note" class="control-label">
                                                        <?php echo _l('ch_note_t'); ?>
                                                    </label>
                                                </td>
                                                <td rowspan="2">
                                                    <?php
                                                    $value = (isset($items) ? $items->note : "");
                                                    echo render_textarea('note', '', $value);
                                                    ?>
                                                </td>
                                                <td>
                                                    <label for="delivery_cost" class="control-label">
                                                        <?php echo _l('ch_delivery_cost'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $value = (isset($items) ? formatNumber($items->delivery_cost) : 0);
                                                    echo render_input('delivery_cost', '', $value);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="reduce_cost" class="control-label">
                                                        <?php echo _l('ch_reduce_cost'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    $value = (isset($items) ? formatNumber($items->reduce_cost) : 0);
                                                    echo render_input('reduce_cost', '', $value);
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div id="top_search_new" style="width: 100%;">
                                <div>
                                    <input style="float: right; height: 60px" type="search" id="SearchQR" class="form-control" placeholder="<?php echo _l('Quét mã qr sản phẩm...'); ?>">
                                </div>
                                <div id="top_search_button" class="top_search_button_scan" style="top: 15px">
                                    <button type="button" class="btn"><i class="fa fa-barcode" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <?php
                        $customer_custom_fields = false;
                        if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'purchase_order', 'active' => 1)) > 0) {
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
                                            <?php echo render_custom_fields('purchase_order', $value_id); ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-12" style="margin-bottom: 60px">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <?= lang('tnh_info_items') ?>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table style="table-layout: fixed;" class="dt-tnh table item-supplier_quotes table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 100px;" class="center"><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                                    <th style="width: 300px;" class="text-left"><?php echo _l('ch_items_name_t'); ?></th>
                                                    <th class="hide"><?= _l('code_item_in_invoice') ?></th>
                                                    <th class="hide"><?php echo _l('status_item_in_internal'); ?></th>
                                                    <th class="hide"><?php echo _l('status_item_in_suppliers'); ?></th>
                                                    <th style="width: 100px;" class="text-center hide"><?php echo _l('item_quantity'); ?>
                                                    <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_standard'); ?></th>
                                                    <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_stock'); ?></th>
                                                    <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_payment'); ?></th>
                                                    <th style="width: 100px;" class="text-center hide"><?php echo _l('quantity_inventory_missing'); ?></th>
                                                    <th style="width: 100px;" class="text-left hide"><?php echo _l('item_unit'); ?></th>
                                                    <th style="width: 100px;" class="text-right hide"><?php echo _l('price_expected'); ?></th>
                                                    <th style="width: 100px;" class="text-right"><?php echo _l('ch_price_ncc'); ?></th>
                                                    <th style="width: 100px;" class="text-center"><?php echo _l('tax'); ?></th>
                                                    <th style="width: 100px;" class="text-right hide"><?php echo _l('amount_expected_vnd'); ?></th>
                                                    <th style="width: 100px;" class="text-right"><?php echo _l('promotion_suppliers'); ?></th>
                                                    <th style="width: 100px;" class="text-right"><?php echo _l('amount_suppliers_vnd'); ?></th>
                                                    <th style="width: 160px;" class="text-left"><?php echo _l('note'); ?></th>
                                                    <th style="width: 30px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 0;
                                                $totalQuantity_approve = 0;
                                                $totalQuantity = 0;
                                                $vat = 0;
                                                if (isset($items) && count($items->items) > 0) {
                                                    foreach ($items->items as $value) {
                                                        $total_novat = $value['quantity'] * $value['unit_cost'];
                                                        $total = $value['quantity'] * $value['unit_cost'] * (1 + ($value['tax_rate'] / 100));
                                                ?>
                                                        <tr class="sortable item">
                                                            <td class="dragger" style="text-align: center;">
                                                                <img style="border-radius: 50%;width: 4em;height: 4em;" src="<?= $value['avatar'] ?>">
                                                                <?= format_item_purchases($value['type']) ?>
                                                                <input type="hidden" name="items[<?php echo $i; ?>][type]" id="type" value="<?php echo $value['type']; ?>" />
                                                                <input type="hidden" id="product_id" name="items[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>">
                                                                <input type="hidden" class="recipe" id="recipe" name="items[<?php echo $i; ?>][recipe]" value="<?php echo $value['recipe']; ?>" />
                                                                <input type="hidden" class="paper" id="paper" name="items[<?php echo $i; ?>][paper]" value="<?php echo $value['paper']; ?>" />
                                                                <input type="hidden" class="longs" id="longs" name="items[<?php echo $i; ?>][longs]" value="<?php echo $value['longs']; ?>" />
                                                                <input type="hidden" class="wide" id="wide" name="items[<?php echo $i; ?>][wide]" value="<?php echo $value['wide']; ?>" />
                                                            </td>
                                                            <td>

                                                                <select class="custom_item_select" id="custom_item_select_<?= $i ?>" style="width: 100%;" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                    <?php foreach (get_options_search_cbo('items', $value['product_id'], $value['type']) as $t) { ?>
                                                                        <option value="<?php echo $t['id']; ?>" data-idd="<?= $t['quantity_warehoue'] ?>" <?php echo ($t['id'] == $value['id'] ? 'selected' : '') ?>> <?= $t['name'] ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                                <br><br>
                                                                <div class="color"><?= format_item_color($value['product_id'], $value['type']) ?></div>
                                                            </td>
                                                            <td class="hide">???</td>
                                                            <td class="hide">???</td>
                                                            <td class="hide">???</td>

                                                            <td class="hide"><input style="width: 100px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity" type="text" name="items[<?php echo $i; ?>][quantity]" value="<?= formatNumber($value['quantity']) ?>" /></td>
                                                            <td><input style="width: 50px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity_suppliers" type="text" name="items[<?php echo $i; ?>][quantity_suppliers]" value="<?= $value['quantity_suppliers'] ?>" /><input style="width: 50px" class="hide height_auto H_input exchange_standard_unit" type="text" name="items[<?php echo $i; ?>][exchange_standard_unit]" value="<?= formatNumber($value['exchange_unit']) ?>" />/<?= $value['unit'] ?></td>
                                                            <td class="text-center"><span class="text_mainquantity_stock text-center">0</span><input style="width: 50px" class="hide height_auto H_input mainquantity_stock" type="text" name="items[<?php echo $i; ?>][quantity_stock]" value="<?= formatNumber($value['quantity_stock']) ?>" /><input style="width: 50px" class=" hide height_auto H_input exchange_stock" type="text" name="items[<?php echo $i; ?>][exchange_stock]" value="<?= formatNumber($value['exchange_stock']) ?>" />/<?= $value['unit_name_stock'] ?></td>
                                                            <td class="text-center"><span class="text_mainquantity_payment">0</span><input style="width: 50px" class="hide height_auto H_input mainquantity_payment" type="text" name="items[<?php echo $i; ?>][quantity_payment]" value="<?= formatNumber($value['quantity_payment']) ?>" /><input style="width: 50px" class="hide height_auto H_input exchange_payment" type="text" name="items[<?php echo $i; ?>][exchange_payment]" value="<?= formatNumber($value['exchange_payment']) ?>" />/<?= $value['unit_name_payment'] ?></td>

                                                            <td class="hide center quantity_warehoue"></td>
                                                            <td class="hide"><?= $value['unit'] ?></td>
                                                            <td class="hide"><input style="width: 100px" onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_expected" type="text" name="items[<?php echo $i; ?>][price_expected]" value="<?= formatNumber($value['price_expected']) ?>" /></td>
                                                            <td><input style="width: 100px" onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_suppliers"  type="text" name="items[<?php echo $i; ?>][price_suppliers]" value="<?= formatNumber($value['price_suppliers']) ?>" /></td>
                                                            <td>
                                                                <select class="selectpicker tax" name="items[<?php echo $i; ?>][tax_id]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                    <option value data-taxrate="0"><?= _l('no_tax') ?></option>
                                                                    <?php foreach ($tax as $t) { ?>
                                                                        <option value="<?php echo $t['id']; ?>" data-taxrate="<?= $t['taxrate'] ?>" <?php echo ($t['id'] == $value['tax_id'] ? 'selected' : '') ?>> <?= $t['name'] ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                                <input type="hidden" class="tax_rate" name="items[<?php echo $i; ?>][tax_rate]" value="<?= $value['tax_rate'] ?>">
                                                            </td>
                                                            <td class="hide align_right total_expected"><?= formatNumber($value['total_expected']) ?></td>
                                                            <td><input style="width: 100px" onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right promotion_expected" type="text" name="items[<?php echo $i; ?>][promotion_expected]" value="<?= formatNumber($value['promotion_expected']) ?>" /></td>

                                                            <td class="align_right total_suppliers"><?= formatNumber($value['total_suppliers']) ?></td>
                                                            <td><textarea class="note" name="items[<?php echo $i; ?>][note]" value="<?= $value['note'] ?>"><?= $value['note'] ?></textarea></td>
                                                            <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                                        </tr>
                                                    <?php
                                                        $i++;
                                                        $totalQuantity += $value['quantity'];
                                                    } ?>
                                                <?php } ?>
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
                <div style="width: 80%" class=" pull-left">
                    <table class="table tnh-tb noMargin table-color_sum dont-responsive-table" style="table-layout: fixed;">
                        <tbody>
                            <tr>
                                <!-- thống kê ncc -->
                                <td style="width: 8%;">
                                    <span class="bold"><?php echo _l('quantity'); ?>:</span><br>
                                    <span class="bold"><?php echo _l('amount'); ?>:</span>
                                </td>
                                <td style="width: 6%;">
                                    <span class="total_quantity_suppliers"><?php echo $totalQuantity ?></span><br>
                                    <span class="total_sub_suppliers">0</span>
                                </td>
                                <td style="width: 6%;">
                                    <span class="bold"><?php echo _l('ch_tax_all'); ?> :</span>
                                </td>
                                <td style="width: 10%;">
                                    <?php $tax_all = (isset($items) ? $items->tax_all : ''); ?>
                                    <?= str_replace(' tax', ' tax_all', get_taxes_dropdown_template('tax_all', $tax_all)) ?>

                                </td>
                                <td style="width: 13%;">
                                    <div>
                                        <div>
                                            <input type="text" name="valtype_check_suppliers" value="<?= ((isset($items)) ? $items->valtype_check_suppliers : 1) ?>" class="hide" id="valtype_check_suppliers">
                                            <div style="float: left;" class="radio radio-primary radio-inline"><input type="radio" class="type_check_suppliers" name="type_check_suppliers" value="1" <?= (isset($items) ? ((isset($items) && $items->valtype_check_suppliers == 1) ? 'checked' : '') : 'checked') ?>><label>%</label></div>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="type_check_suppliers" class="type_check_suppliers" value="2" <?= ((isset($items) && $items->valtype_check_suppliers == 2) ? 'checked' : '') ?>>
                                                <label>Tiền</label>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; float: left;">
                                            <span style="float: left;width: 45%;" class="bold"><?php echo _l('Chiết khấu'); ?>:&nbsp;&nbsp;</span>
                                            <input placeholder="Chiết khấu" class="form-control" id="discount_percent_suppliers" value="<?= (isset($items) ? $items->discount_percent_suppliers : 0) ?>" name="discount_percent_suppliers" style="width: 100%;float: left;" onchange="formatNumBerKeyUp(this)">
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </td>
                                <td class="discount_percent_total_suppliers" style="width: 6%;">
                                    0
                                </td>

                                <td class="" style="width: 8%;">
                                    <span style="color:blue" class="bold"><?php echo _l('payment_total_amount'); ?> (<span id="name_currency"></span>):</span>
                                    <br>
                                    <span style="color:red" class="bold"><?php echo _l('payment_total_amount'); ?> (<?php echo _l('VNĐ'); ?>):</span>
                                </td>
                                <td class="" style="width: 7%;">
                                    <span style="color:blue;" class="totalPrice_suppliers_cqd">0</span>
                                    <br>
                                    <span style="color:red;" class="totalPrice_suppliers">0</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-info pull-right form-submitersss" style="padding: 28px;"><?= _l('submit') ?>
                </button>
                <a href="<?= admin_url('purchase_order'); ?>" class="btn btn-default pull-right" style="padding: 28px;margin-right: 10px;"><?= _l('go_back') ?>
                </a>
            </div>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
</div>
<div id="view_currencies_modal"></div>
<!-- popup thông báo đánh giá ncc -->
<div class="classify">
    <img width="85" src="https://i.gifer.com/XlQO.gif">
    <div class="classify-content">
        <div class="classify-title bold uppercase"><?= _l('supplier_classify_full') ?></div>
        <div class="text-center">
            <span class="inline-block label label-warning classify_title"></span>
        </div>
        <div class="classify-html"></div>
    </div>
</div>
<!-- end -->

<?php init_tail(); ?>
<script>
    $('#currency').on('change', (e) => {
        $('#name_currency').html($('#currency').find('option:selected').attr('data-name'));
        var currents = Number($('#currency').find('option:selected').attr('data-total'));
        $('#amount_to_vnd').val(tnhFormatNumber(currents));
        getTotalPrice();
    });
    $('#amount_to_vnd').on('change', (e) => {
        getTotalPrice();
    });
    $('#name_currency').html($('#currency').find('option:selected').attr('data-name'));
    $('#reduce_cost').on('change', (e) => {
        key = "";
        money = $('#reduce_cost').val().replace(/[^\-\d\.]/g, '');
        a = money.split(".");
        $.each(a, function(index, value) {
            key = key + value;
        });
        if (key == '') {
            key = 0;
        }
        $('#reduce_cost').val(tnhFormatNumber(key, '.', ','));
        getTotalPrice();
    });
    $('#delivery_cost').on('change', (e) => {
        key = "";
        money = $('#delivery_cost').val().replace(/[^\-\d\.]/g, '');
        a = money.split(".");
        $.each(a, function(index, value) {
            key = key + value;
        });
        if (key == '') {
            key = 0;
        }
        $('#delivery_cost').val(tnhFormatNumber(key, '.', ','));
        getTotalPrice();
    });

    var dem_temp_delay = 10;
    var table_price = {};
    $(document).on('change', '#suppliers_id', (e) => {
        $('.classify').removeClass('active');
        var currentQuantityInput = $(e.currentTarget);

        var id = $(currentQuantityInput).val();
        if (id == '') {
            $('.total_debt').addClass('hide');
        } else {
            $.post(admin_url + 'suppliers/get_debt/' + id, {
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                data = JSON.parse(data);
                if (data.default_currency != "0") {
                    $('#currency').val(data.default_currency).selectpicker('refresh');
                    $('#ch_currency').val(data.default_currency);
                } else {
                    $('#currency').val('<?= CURRENCY_VND ?>').selectpicker('refresh');
                    $('#ch_currency').val('<?= CURRENCY_VND ?>');
                }
                $('#currency').trigger('change');
                if (data.success == true) {
                    $('.total_debt').removeClass('hide');
                    $('.total_debt').html('<?= _l('ch_wanring_debt_limit') ?><b>' + data.total + '</b>');
                } else {
                    $('.total_debt').addClass('hide');
                }
                table_price = data.table_price;
                <?php if (empty($items)) { ?>
                    var items = $('table.item-supplier_quotes tbody').find('tr.item');
                    $.each(items, (index, value) => {
                        $(value).find('td:nth-child(13)').find(
                                'input.price_suppliers').val(0)
                            .change();
                    });
                    $.each(items, (index, value) => {
                        if (table_price.length > 0) {
                            $.each(table_price, (i, v) => {
                                var product_id = $(value).find('td:nth-child(1)').find(
                                    'input#product_id').val();
                                var type = $(value).find('td:nth-child(1)').find('input#type')
                                    .val();
                                if (v.product_id == product_id && v.product_type == type) {
                                    $(value).find('td:nth-child(13)').find(
                                            'input.price_suppliers').val(formatNumber(v.price))
                                        .change();
                                }
                            });
                        } else {
                            $(value).find('td:nth-child(13)').find('input.price_suppliers').val(
                                formatNumber(0)).change();
                        }
                    });
                <?php } ?>
            });

            $.post(admin_url + 'suppliers/get_classify/' + id, {
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                data = JSON.parse(data);
                if (data.result_warning && data.result_warning != '') {
                    $('.classify').addClass('active');
                    $('.classify').find('.classify-html').html(data.result_warning);
                    $('.classify').find('.classify_title').html(data.classify_title);

                    dem_temp_delay = 10;
                } else {
                    $('.classify').removeClass('active');
                }
            });
        }
    });
    // $('#suppliers_id').change();

    setInterval(sub_delay, 1000);

    function sub_delay() {
        dem_temp_delay--;
        if (dem_temp_delay == 0) {
            hide_classify();
        }
    }

    function hide_classify() {
        $('.classify').removeClass('active');
    }

    $(function() {
        createTrItemfist();
        var dt = $('.item-supplier_quotes').DataTable({
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
        _validate_form($('#supplier_quotes-form'), {
            date: "required",
            number: "required",
            type_order: "required",
            suppliers_id: "required",
            delivery_date: "required",
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
                        var t = {
                            [csrfData.token_name]: csrfData.hash
                        };
                        return t.type = e, t.rel_id = "", t.q = "{{{q}}}", t.type_items = type, void 0 !== a && jQuery.extend(t, a), t
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
                            type_items: e[i].type_items,
                            quantity_warehoue: e[i].quantity_warehoue
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
                    html += '<option data-idd="' + value.quantity_warehoue + '" data-id=' + value.type_items + ' value="' + value.value + '">' + value.text + '</option>';
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
    var itemList = <?php echo json_encode($type_items); ?>;
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
    countrow();

    function countrowfist() {
        if (!$('table.item-supplier_quotes tbody tr.item').find('input[value=hau]').length) {
            createTrItemfist();
        }
    }
    // function countrow()
    // {
    //     var items = $('table.item-purchases tbody').find('tr.item');
    //     console.log(123);
    //     $.each(items, (index,value)=>{
    //     	var type = $(value).find('td:nth-child(1)').find('input#type').val();
    //     	var name_type = '<span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +findItem(type)+ '</span>';
    //         $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);
    //         $(value).find('td:nth-child(6) input.mainQuantity').change();

    //     });
    // }
    $('#type_items').on('change', function(e) {
        var type = $('#type_items').val();
        loadItems(type);
    });

    function loadItems(type) {
        var custom_item_select = $('#custom_item_select');
        custom_item_select.find('option:gt(0)').remove();
        custom_item_select.selectpicker('refresh');
        if (custom_item_select.length) {
            $.ajax({
                    url: admin_url + 'invoice_items/items/' + type,
                    dataType: 'json',
                })
                .done(function(data) {
                    $.each(data, function(key, value) {
                        custom_item_select.append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    custom_item_select.selectpicker('refresh');
                });
        }
    }
    $(document).on('change', '.custom_item_select', (e) => {
        var currentQuantityInput = $(e.currentTarget);

        var id = $(currentQuantityInput).val();
        if (id == '') {} else {
            // var type = $('option:selected', currentQuantityInput).attr('data-id');
            var type = currentQuantityInput.select2('data').type;
            var id_order = $('#id_order').val();
            var id_supplier = $('#suppliers_id').val();
            $.post(admin_url + 'supplier_quotes/get_items/' + id + '/' + type + '/0/' + id_supplier, {
                [csrfData['token_name']]: csrfData['hash']
            }, function(item) {
                var item = JSON.parse(item);
                createTrItem(item, currentQuantityInput, type);
                console.log(item);
                console.log(currentQuantityInput);
                console.log(type);
            });
        }
    });
    var uniqueArray = <?= $i ?>;
    var taxes_dropdown_template = <?= json_encode($taxes) ?>;
    var createTrItemfist = (item) => {
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?= base_url('assets/images/preview-not-available.jpg') ?>">';
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatar" style="text-align: center;">' + name_type + '<input type="hidden" name="items[' + uniqueArray + '][type]" id="type" value="hau" /></td>');
        var td2 = $('<td ><input type="hidden" class="count" value="' + uniqueArray + '" /><input style="width:100%" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select_' + uniqueArray + '" name="custom_item_select_' + uniqueArray + '" style="width: 100%">\
								        <br><br><div class="color"><div></td>');
        var td3 = $('<td class="hide">???</td>');
        var td4 = $('<td class="hide">???</td>');
        var td5 = $('<td class="hide">???</td>');
        var td7 = $('<td class="hide"><input style="width: 100px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity" type="text" name="items[' + uniqueArray + '][quantity]" value="1" /></td>');
        var td8 = $('<td><input style="width: 50px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity_suppliers" type="text" name="items[' + uniqueArray + '][quantity_suppliers]" value="1" /><input style="width: 100px"  class="hide height_auto H_input exchange_standard_unit" type="text" name="items[' + uniqueArray + '][exchange_standard_unit]" value="1" /><span class="unit_name"></span></td>');
        var td20 = $('<td class="text-center"><span class="text_mainquantity_stock text-center">0</span><span class="unit_name_stock"></span><input style="width: 50px"  class="hide height_auto H_input mainquantity_stock" type="text" name="items[' + uniqueArray + '][quantity_stock]" value="1" /><input style="width: 100px"  class=" hide height_auto H_input exchange_stock" type="text" name="items[' + uniqueArray + '][exchange_stock]" value="1" /></td>');
        var td21 = $('<td class="text-center"><span class="text_mainquantity_payment">0</span><span class="unit_name_payment"></span><input style="width: 50px"  class="hide height_auto H_input mainquantity_payment" type="text" name="items[' + uniqueArray + '][quantity_payment]" value="1" /><input style="width: 100px"  class="hide height_auto H_input exchange_payment" type="text" name="items[' + uniqueArray + '][exchange_payment]" value="1" /></td>');

        var td9 = $('<td class="hide center quantity_warehoue"></td>');
        var td10 = $('<td class="unit_name hide"></td>');
        var td11 = $('<td class="hide" ><input style="width: 100px" onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_expected" type="text" name="items[' + uniqueArray + '][price_expected]" value="0" /></td>');
        var td12 = $('<td ><input style="width: 100px"  onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_suppliers" type="text" name="items[' + uniqueArray + '][price_suppliers]" value="0" /></td>');
        var taxTemplate = taxes_dropdown_template;
        taxTemplate = taxTemplate.replace('name=""', 'name="items[' + uniqueArray + '][tax_id]"');
        var td13 = $('<td>' + taxTemplate + '<input type="hidden" class="tax_rate" name="items[' + uniqueArray + '][tax_rate]" value="0"></td>');

        var td14 = $('<td class="align_right total_expected hide">0</td>');
        var td15 = $('<td ><input style="width: 100px" onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right promotion_expected" type="text" name="items[' + uniqueArray + '][promotion_expected]" value="0" /></td>');
        var td16 = $('<td class="align_right total_suppliers">0</td>');
        var td17 = $('<td><textarea class="note" name="items[' + uniqueArray + '][note]"></textarea></td>');


        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td20);
        newTr.append(td21);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        newTr.append(td12);
        newTr.append(td13);
        newTr.append(td14);
        newTr.append(td15);
        newTr.append(td16);
        newTr.append(td17);
        newTr.append('<td class="delete"></td');
        $('table.item-supplier_quotes tbody').append(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        ajaxSelectCallBack($('#custom_item_select_' + uniqueArray), "<?= admin_url('purchases/SearchItems') ?>", 0);
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        uniqueArray++;
    }
    var createTrItem = (item, currentQuantityInput, type) => {
        var price_suppliers = 0;
        $.each(table_price, (index, value) => {
            if (value.product_id == item.id && value.product_type == type) {
                price_suppliers = value.price;
            }
        });
        if (typeof(item) == 'undefined' || item.length == 0) return;
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;" src="' + item.avatar_1 + '"><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(type) + '</span>';
        var new_tr = currentQuantityInput.parents('tr');
        var count = new_tr.find('td > input.count').val();
        new_tr.find('td.avatar').html(name_type + '\
            <input type="hidden" id="type" name="items[' + count + '][type]" value="' + type + '" />\
            <input type="hidden" class="id" id="product_id" name="items[' + count + '][id]" value="' + item.id + '" />\
            <input type="hidden" class="recipe" id="recipe" name="items[' + count + '][recipe]" value="' + item.recipe + '" />\
            <input type="hidden" class="paper" id="paper" name="items[' + count + '][paper]" value="' + item.paper + '" />\
            <input type="hidden" class="longs" id="longs" name="items[' + count + '][longs]" value="' + item.longs + '" />\
            <input type="hidden" class="wide" id="wide" name="items[' + count + '][wide]" value="' + item.wide + '" />\
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
        new_tr.find('.color').html(item.color);
        new_tr.find('td > input.price_expected').val(tnhFormatNumber(price_suppliers));
        new_tr.find('td > input.price_suppliers').val(tnhFormatNumber(price_suppliers));
        new_tr.find('span.unit_name').html('/' + unit_name);
        new_tr.find('span.unit_name_payment').html('/' + unit_name_payment);
        new_tr.find('span.unit_name_stock').html('/' + unit_name_stock);

        new_tr.find('td.delete').html('<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a>');

        new_tr.find('td > input.exchange_standard_unit').val((item.exchange_unit));
        new_tr.find('td > input.exchange_stock').val((item.exchange_standard_unit));
        new_tr.find('td > input.exchange_payment').val((item.exchange_unit_payment));

        var tax_id = $('select.tax_all').val();
        new_tr.find('select.tax').selectpicker('val', tax_id);
        uniqueArray++;
        new_tr.find('select.tax').change();
        getTotalPrice();
        countrowfist();
        new_tr.find('td  > input.mainQuantity').change();
    }
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };

    function getTotalPrice() {
        var items = $('table.item-supplier_quotes tbody').find('tr.item');
        var total_quantity_expected = 0;
        var total_sub_expected = 0;
        var total_quantity_suppliers = 0;
        var total_sub_suppliers = 0;
        $.each(items, (index, value) => {
            if (!empty($(value).find('#type').val())) {
                total_quantity_expected += parseFloat($(value).find('.mainQuantity').val().replace(/\,/g, ''));
                total_sub_expected += parseFloat($(value).find('.total_expected').text().replace(/\,/g, ''));
                total_quantity_suppliers += parseFloat($(value).find('.mainQuantity_suppliers').val().replace(/\,/g, ''));
                total_sub_suppliers += parseFloat($(value).find('.total_suppliers').text().replace(/\,/g, ''));
            }
        });

        var reduce_cost = unformat_number($('#reduce_cost').val());
        var delivery_cost = unformat_number($('#delivery_cost').val());

        $('.total_quantity_expected').text(tnhFormatNumber(total_quantity_expected));
        $('.total_sub_expected').text(tnhFormatNumber(total_sub_expected));
        $('.total_quantity_suppliers').text(tnhFormatNumber(total_quantity_suppliers));
        $('.total_sub_suppliers').text(tnhFormatNumber(total_sub_suppliers));

        var discount_percent_expected = $('#discount_percent_expected').val();
        var valtype_check_expected = $('#valtype_check_expected').val();
        var discount_percent_suppliers = $('#discount_percent_suppliers').val();
        var valtype_check_suppliers = $('#valtype_check_suppliers').val();


        //thống kê NCC
        var discount_percent_suppliers_total = 0;
        var totalAll_suppliers = 0;
        if (valtype_check_suppliers == 1) {
            discount_percent_suppliers_total = total_sub_suppliers * discount_percent_suppliers / 100;
            totalAll_suppliers = total_sub_suppliers - discount_percent_suppliers_total;
        } else if (valtype_check_suppliers == 2) {
            discount_percent_suppliers_total = unformat_number(discount_percent_suppliers);
            if (discount_percent_suppliers_total >= total_sub_suppliers) {
                $('#discount_percent_suppliers').val(tnhFormatNumber(total_sub_suppliers));
                discount_percent_suppliers_total = total_sub_suppliers;
            }
            totalAll_suppliers = total_sub_suppliers - discount_percent_suppliers_total;
        }
        $('.discount_percent_total_suppliers').text(tnhFormatNumber(discount_percent_suppliers_total));

        var currents = Number($('#amount_to_vnd').val().replace(/\,/g, ''));

        $('.totalPrice_suppliers_cqd').text(tnhFormatNumber((Number(totalAll_suppliers) + Number(delivery_cost) - Number(reduce_cost))));
        $('.totalPrice_suppliers').text(tnhFormatNumber((Number(totalAll_suppliers) + Number(delivery_cost) - Number(reduce_cost)) * currents));
    }
    $('#items-form').on('submit', (e) => {
        if ($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', '<?= _l('ch_invalid_value') ?>');
        }
    });
    $(document).on('change', 'select.tax_all', function(e) {
        var tax_id = $(this).val();
        var items = $('table.item-supplier_quotes tbody').find('tr.item');
        $.each(items, (index, value) => {
            if ($(value).find('td:nth-child(1)').find('input#type').val() != 'hau') {
                $(value).find('select.tax').selectpicker('val', tax_id);
                $(value).find('select.tax').change();
            }
        });
    });
    $(document).on('change', 'select.tax', function(e) {
        var tax_id = $(this).val();
        var tax_rate = parseInt($(this).find('option:selected').attr('data-taxrate'));
        var current_row = $(this).parents('tr');
        if (isNaN(tax_rate)) tax_rate = 0;
        $(this).parents('tr').find('input.tax_rate').val(tax_rate);
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '.type_check_expected', function(e) {
        var val = $(this).val();
        $('#valtype_check_expected').val(val);
        $('#discount_percent_expected').val(0);
    });
    $(document).on('change', '.type_check_suppliers', function(e) {
        var val = $(this).val();
        $('#valtype_check_suppliers').val(val);
        $('#discount_percent_suppliers').val(0);
    });
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);
        var current_row = currentInput.parents('tr');
        let recipe = (current_row.find('.recipe').val());
        let paper = (current_row.find('.paper').val());
        let longs = (current_row.find('.longs').val());
        let wide = (current_row.find('.wide').val());

        let mainQuantity = unformat_number(current_row.find('.mainQuantity').val());
        let mainQuantity_suppliers = unformat_number(current_row.find('.mainQuantity_suppliers').val());

        let exchange_standard_unit = unformat_number(current_row.find('.exchange_standard_unit').val());
        let exchange_stock = unformat_number(current_row.find('.exchange_stock').val());
        let exchange_payment = unformat_number(current_row.find('.exchange_payment').val());

        var quantity_stock = Math.round((mainQuantity_suppliers / exchange_stock) * exchange_standard_unit);
        current_row.find('.text_mainquantity_stock').text(tnhFormatNumber(quantity_stock));
        current_row.find('.mainquantity_stock').val((quantity_stock));
        if (recipe == 1) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * exchange_standard_unit);
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 2) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * paper / 100);
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 3) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * (longs * wide) / 10000);
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        }

        let price_expected = unformat_number(current_row.find('.price_expected').val());
        let price_suppliers = unformat_number(current_row.find('.price_suppliers').val());
        let promotion_expected = unformat_number(current_row.find('.promotion_expected').val());
        let tax = current_row.find('.tax_rate').val();

        var total_expected = quantity_payment * price_expected * (1 + tax / 100);
        var total_suppliers = (quantity_payment * price_suppliers * (1 + tax / 100)) - promotion_expected;
        current_row.find('.total_expected').text(tnhFormatNumber(total_expected));
        current_row.find('.total_suppliers').text(tnhFormatNumber(total_suppliers));
        getTotalPrice();

    };

    // function fsormatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
    //     nStr += '';
    //     x = nStr.split(decSeperate);
    //     x1 = x[0];
    //     x2 = x.length > 1 ? '.' + x[1] : '';
    //     x2 = x2.substr(0, 2);
    //     var rgx = /(\d+)(\d{3})/;
    //     while (rgx.test(x1)) {
    //         x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    //     }
    //     return x1 + x2;
    // };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    $(document).on('keyup', '#discount_percent_expected', (e) => {
        var currentDiscountPercentInput = $(e.currentTarget);
        var valtype_check = $('#valtype_check_expected').val();
        if (valtype_check == 1) {
            if (unformat_number(currentDiscountPercentInput.val()) < 0) {
                currentDiscountPercentInput.val(0);
            }
            if (unformat_number(currentDiscountPercentInput.val()) > 100) {
                currentDiscountPercentInput.val(100);
            }
        }
        getTotalPrice();
    });
    $(document).on('keyup', '#discount_percent_suppliers', (e) => {
        var currentDiscountPercentInput = $(e.currentTarget);
        var valtype_check = $('#valtype_check_suppliers').val();
        if (valtype_check == 1) {
            if (unformat_number(currentDiscountPercentInput.val()) < 0) {
                currentDiscountPercentInput.val(0);
            }
            if (unformat_number(currentDiscountPercentInput.val()) > 100) {
                currentDiscountPercentInput.val(100);
            }
        }
        getTotalPrice();
    });
    $(document).on('keyup', '.unit_cost', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.mainQuantity', (e) => {

        var currentQuantityInput = $(e.currentTarget);
        var quantity_warehoue = currentQuantityInput.parent().parent('tr').find('td select.custom_item_select  option:selected').attr('data-idd');

        quantity_warehoues = quantity_warehoue - parseInt(unformat_number(currentQuantityInput.val()));
        if (quantity_warehoues < 0) {
            currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text(Math.abs(quantity_warehoues));
        }
        if (quantity_warehoues >= 0) {
            currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text('');
        }
        calculateTotal(e.currentTarget);
    });
    $(document).on('click', '.mainQuantity', (e) => {

        var currentQuantityInput = $(e.currentTarget);
        var quantity_warehoue = currentQuantityInput.parent().parent('tr').find('td select.custom_item_select  option:selected').attr('data-idd');
        quantity_warehoues = quantity_warehoue - parseInt(unformat_number(currentQuantityInput.val()));
        console.log(quantity_warehoues)
        if (quantity_warehoues < 0) {
            currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text(Math.abs(quantity_warehoues));
        }
        if (quantity_warehoues >= 0) {
            currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text('');
        }
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '.mainQuantity', (e) => {

        var currentQuantityInput = $(e.currentTarget);
        var quantity_warehoue = currentQuantityInput.parent().parent('tr').find('td select.custom_item_select  option:selected').attr('data-idd');
        quantity_warehoues = quantity_warehoue - parseInt(unformat_number(currentQuantityInput.val()));
        console.log(quantity_warehoues)
        if (quantity_warehoues < 0) {
            currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text(Math.abs(quantity_warehoues));
        }
        if (quantity_warehoues >= 0) {
            currentQuantityInput.parent().parent('tr').find('td.quantity_warehoue').text('');
        }
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.mainQuantity_suppliers', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.price_expected', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.price_suppliers', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '.price_suppliers', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.promotion_expected', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('click', '.type_check', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        $('#discount_percent').val(0);
        $('#valtype_check').val(currentQuantityInput.val());
        getTotalPrice();
    });
    countrow();
    getTotalPrice();

    function countrow() {
        var items = $('table.item-supplier_quotes tbody').find('tr.item');
        $.each(items, (index, value) => {
            var type = $(value).find('td:nth-child(1)').find('input#type').val();
            var name_type = '<span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(type) + '</span>';
            $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);
            $(value).find('td:nth-child(6) input.mainQuantity').change();
            $('#custom_item_select_' + index).select2();


        });
    }
    $(document).on('show.bs.dropdown', '.custom_item_select', (e) => {
        $('.table-responsive').css("overflow", "inherit");
    });

    $(document).on('hide.bs.dropdown', '.custom_item_select', (e) => {
        $('.table-responsive').css("overflow", "auto");
    });

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
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
                allowClear: false,
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

    function repoFormatSelection(state) {
        if (!state.id) return state.text;

        return state.text + ' - ' + '(' + state.code + ')';
    }

    function int_suppliers_view(id = null, edit) {
        $('#suppliers_view_data').html('');
        $.get(admin_url + 'suppliers/int_suppliers_add/' + edit + '/' + id).done(function(response) {
            $('#suppliers_view_data').html(response);
            add_html_evaluate(id);
            $('#suppliers_add').modal({
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

    function new_suppliers_source_inline(e = 'groups_in') {
        var t = "";
        ($("body").hasClass("suppliers-email-integration") || $("body").hasClass("web-to-suppliers-form")) && (e = "suppliers_" + e), t = '<div id="new_suppliers_' + e + '_inline" class="form-group"><label for="new_' + e + '_name">' + $('label[for="' + e + '"]').html() + '</label><div class="input-group"><input type="text" id="new_' + e + '_name" name="new_' + e + '_name" class="form-control"><div class="input-group-addon"><a href="#" onclick="suppliers_add_inline_select_submit(\'' + e + '\'); return false;" class="suppliers-add-inline-submit-' + e + '"><i class="fa fa-check"></i></a></div></div></div>', $(".form-group-select-input-" + e).after(t), $("body").find("#new_" + e + "_name").focus(), $('.suppliers-save-btn,#form_info button[type="submit"],#suppliers-email-integration button[type="submit"],.btn-import-submit').prop("disabled", !0), $(".suppliers-field-new").addClass("disabled").css("opacity", .5), $(".form-group-select-input-" + e).addClass("hide")
    }

    function suppliers_add_inline_select_submit(e) {
        var t = $("#new_" + e + "_name").val();
        if ("" !== t) {
            var a = 'group';
            e.indexOf("suppliers_") > -1 && (a = a.replace("suppliers_", ""));
            var i = {
                [csrfData['token_name']]: csrfData['hash']
            };
            i.name = t, i.inline = !0, $.post(admin_url + "suppliers/" + a, i).done(function(a) {
                if (!0 === (a = JSON.parse(a)).success || "true" == a.success) {
                    var i = $("body").find("select#" + e);
                    i.append('<option value="' + a.id + '">' + t + "</option>"), i.selectpicker("val", a.id), i.selectpicker("refresh"), i.parents(".form-group").removeClass("has-error")
                }
            })
        }
        $("#new_suppliers_" + e + "_inline").remove(), $(".form-group-select-input-" + e).removeClass("hide"), $('.suppliers-save-btn,#form_info button[type="submit"],#suppliers-email-integration button[type="submit"],.btn-import-submit').prop("disabled", !1), $(".suppliers-field-new").removeClass("disabled").removeAttr("style")
    }
    //Hoàng CRM
    function add_contact_view() {
        var length = $('.new_contact_form').length;
        console.log(length);
        var opt = {
            format: "<?= substr(get_option('dateformat'), 0, 5); ?>",
            timepicker: false,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 0,
        };
        var html = '<div class="col-md-6">\
                    <div class="col-md-12 new_contact_form">\
                      <div class="remove_contact_panel">\
                        <a class="remove_html" title="Xóa"><i class="fa fa-trash"></i></a>\
                      </div>\
                      <div class="col-md-12">\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][name]"><label for="alo" class="control-label"><?= _l('clients_list_full_name') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][name]" name="contact[' + (length + 1) + '][name]" class="form-control" value="">\
                          </div>\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][phone]"><label for="alo" class="control-label"><?= _l('leads_dt_phonenumber') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][phone]" name="contact[' + (length + 1) + '][phone]" class="form-control" value="">\
                          </div>\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][email]"><label for="alo" class="control-label"><?= _l('client_email') ?></label>\
                          <input type="email" id="contact[' + (length + 1) + '][email]" name="contact[' + (length + 1) + '][email]" class="form-control" value="">\
                          </div>\
                      </div>\
                      <div class="col-md-12">\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][address]"><label for="alo" class="control-label"><?= _l('settings_sales_address') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][address]" name="contact[' + (length + 1) + '][address]" class="form-control" value="">\
                          </div>\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][birthday]"><label for="alo" class="control-label"><?= _l('birthday') ?></label>\
                                <div class="input-group date">\
                                    <input type="text" class="form-control datepicker" id="date_dk" name="contact[' + (length + 1) + '][birthday]">\
                                    <div class="input-group-addon">\
                                    <i class="fa fa-calendar calendar-icon"></i>\
                                    </div>\
                                </div>\
                            </div>\
                           <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][sex]"><label for="contact[' + (length + 1) + '][sex]" class="control-label"><?= _l('sex') ?></label><div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="contact[' + (length + 1) + '][sex]" name="contact[' + (length + 1) + '][sex]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98"><option value=""></option><option value="1">Nam</option><option value="2">Nữ</option><option value="3">Khác</option></select><div class="dropdown-menu open" role="combobox"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off" role="textbox" aria-label="Search"></div><div class="inner open" role="listbox" aria-expanded="false" tabindex="-1"><ul class="dropdown-menu inner "></ul></div></div></div></div>\
                        </div>\
                      <div class="col-md-12">\
                          <div class="form-group" app-field-wrapper="contact[' + (length + 1) + '][note]"><label for="alo" class="control-label"><?= _l('note') ?></label>\
                          <input type="text" id="contact[' + (length + 1) + '][note]" name="contact[' + (length + 1) + '][note]" class="form-control" value="">\
                          </div>\
                      </div>\
                      <div class="col-md-6 form-group">\
                          <input type="checkbox" value="1" name="contact[' + (length + 1) + '][receive_email]"><?= _l('get_email') ?>\
                          </div> \
                          <div class="col-md-6 form-group">\
                        <input type = "checkbox"value = "1" name = "contact[' + (length + 1) + '][main_contact]" > <?= _l('key_contact') ?></div>\ < /div > \ </div>';
        $('.append_html').append(html);
        $('#date_dk').datetimepicker(opt);
        init_selectpicker();
        init_datepicker();
    }
    $(document).on('click', '.remove_html', (e) => {
        $(e.currentTarget).parent().parent().parent().remove();
    });

    function edit_currencies() {
        var currents = $('#currency').val();
        $('#view_currencies_modal').html('');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        if (currents != "") {
            data['id'] = currents;
        }
        $.post(admin_url + 'currencies/get_currencies_v2', data, function(data) {
            data = JSON.parse(data);
            $('#view_currencies_modal').html(data.data);
        })
    }

    $('body').on('change', '#SearchQR', function (e) {
        var code = $(this).val();
        if (code) {
            $.ajax({
                url: "<?= base_url() ?>" + 'admin/purchase_order/searchQR',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    code: code,
                    csrf_token_name: hash,
                },
            }).done(function (data) {
                if (data.result){
                    alert_float('success',data.message);
                    createTrItemAuto(data.items);
                } else {
                    alert_float('danger',data.message);
                }
            }).fail(function () {
                alert_float('danger','Vui lòng thử lại');
            });
        }
        $('#SearchQR').val('');
    })

    var createTrItemAuto = (item) => {
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="'+item.avatar+'"><br><span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #e30000">'+findItem(item.type)+'</span>';
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatar" style="text-align: center;">' + name_type + '<input type="hidden" name="items[' + uniqueArray + '][type]" id="type" value="'+item.type+'" />\
            <input type="hidden" class="id" id="product_id" name="items[' + uniqueArray + '][id]" value="' + item.id + '" />\
            <input type="hidden" class="recipe" id="recipe" name="items[' + uniqueArray + '][recipe]" value="' + item.recipe + '" />\
            <input type="hidden" class="paper" id="paper" name="items[' + uniqueArray + '][paper]" value="' + item.paper + '" />\
            <input type="hidden" class="longs" id="longs" name="items[' + uniqueArray + '][longs]" value="' + item.longs + '" />\
            <input type="hidden" class="wide" id="wide" name="items[' + uniqueArray + '][wide]" value="' + item.wide + '" />\
        </td>');
        var td2 = $('<td ><input type="hidden" class="count" value="' + uniqueArray + '" /><input style="width:100%" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select_' + uniqueArray + '" name="custom_item_select_' + uniqueArray + '" style="width: 100%">\
								        <br><br><div class="color">'+item.html+'<div></td>');
        var td3 = $('<td class="hide">???</td>');
        var td4 = $('<td class="hide">???</td>');
        var td5 = $('<td class="hide">???</td>');
        var td7 = $('<td class="hide"><input style="width: 100px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity" type="text" name="items[' + uniqueArray + '][quantity]" value="1" /></td>');
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
        var td8 = $('<td><input style="width: 50px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity_suppliers" type="text" name="items[' + uniqueArray + '][quantity_suppliers]" value="1" /><input style="width: 100px"  class="hide height_auto H_input exchange_standard_unit" type="text" name="items[' + uniqueArray + '][exchange_standard_unit]" value="1" /><span class="unit_name">/'+unit_name+'</span></td>');
        var td20 = $('<td class="text-center"><span class="text_mainquantity_stock text-center">0</span><span class="unit_name_stock">/'+unit_name_stock+'</span><input style="width: 50px"  class="hide height_auto H_input mainquantity_stock" type="text" name="items[' + uniqueArray + '][quantity_stock]" value="1" /><input style="width: 100px"  class=" hide height_auto H_input exchange_stock" type="text" name="items[' + uniqueArray + '][exchange_stock]" value="1" /></td>');
        var td21 = $('<td class="text-center"><span class="text_mainquantity_payment">0</span><span class="unit_name_payment">/'+unit_name_payment+'</span><input style="width: 50px"  class="hide height_auto H_input mainquantity_payment" type="text" name="items[' + uniqueArray + '][quantity_payment]" value="1" /><input style="width: 100px"  class="hide height_auto H_input exchange_payment" type="text" name="items[' + uniqueArray + '][exchange_payment]" value="1" /></td>');

        var td9 = $('<td class="hide center quantity_warehoue"></td>');
        var td10 = $('<td class="unit_name hide"></td>');
        var td11 = $('<td class="hide" ><input style="width: 100px" onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_expected" type="text" name="items[' + uniqueArray + '][price_expected]" value="0" /></td>');
        var td12 = $('<td ><input style="width: 100px"  onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right price_suppliers" type="text" name="items[' + uniqueArray + '][price_suppliers]" value="0" /></td>');
        var taxTemplate = taxes_dropdown_template;
        taxTemplate = taxTemplate.replace('name=""', 'name="items[' + uniqueArray + '][tax_id]"');
        var td13 = $('<td>' + taxTemplate + '<input type="hidden" class="tax_rate" name="items[' + uniqueArray + '][tax_rate]" value="0"></td>');

        var td14 = $('<td class="align_right total_expected hide">0</td>');
        var td15 = $('<td ><input style="width: 100px" onchange="formatNumBerKeyUp(this)" class="height_auto H_input align_right promotion_expected" type="text" name="items[' + uniqueArray + '][promotion_expected]" value="0" /></td>');
        var td16 = $('<td class="align_right total_suppliers">0</td>');
        var td17 = $('<td><textarea class="note" name="items[' + uniqueArray + '][note]"></textarea></td>');

        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td20);
        newTr.append(td21);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        newTr.append(td12);
        newTr.append(td13);
        newTr.append(td14);
        newTr.append(td15);
        newTr.append(td16);
        newTr.append(td17);
        newTr.append('<td class="delete"></td');
        $('table.item-supplier_quotes tbody').prepend(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        ajaxSelectCallBack($('#custom_item_select_' + uniqueArray), "<?= admin_url('purchases/SearchItems') ?>", item.id,item.type);
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        uniqueArray++;
        // console.log(item);

        var tax_id = $('select.tax_all').val();
        newTr.find('select.tax').selectpicker('val', tax_id);
        uniqueArray++;
        newTr.find('select.tax').change();
        getTotalPrice();
        countrowfist();
        newTr.find('td  > input.mainQuantity').change();
    }
</script>