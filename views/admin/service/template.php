<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .mtop54 {
        <?php if (!isset($invoice)) { ?>margin-top: 54px;
        <?php } else { ?>margin-top: 24px;
        <?php } ?>
    }

    legend {
        font-size: 15px;
        font-weight: 500;
        width: auto !important;
    }

    fieldset {
        padding: .35em .625em .75em !important;
        margin: 0 2px !important;
        border: 1px solid #19a9ea !important;
    }



    .can-toggle {
        position: relative;
    }

    .can-toggle *,
    .can-toggle *:before,
    .can-toggle *:after {
        box-sizing: border-box;
    }

    .can-toggle input[type=checkbox] {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
    }

    .can-toggle input[type=checkbox][disabled]~label {
        pointer-events: none;
    }

    .can-toggle input[type=checkbox][disabled]~label .can-toggle__switch {
        opacity: 0.4;
    }

    .can-toggle input[type=checkbox]:checked~label .can-toggle__switch:before {
        content: attr(data-unchecked);
        left: 0;
    }

    .can-toggle input[type=checkbox]:checked~label .can-toggle__switch:after {
        content: attr(data-checked);
    }

    .can-toggle label {
        user-select: none;
        position: relative;
        display: flex;
        align-items: center;
    }



    .can-toggle label .can-toggle__switch {
        position: relative;
    }

    .can-toggle label .can-toggle__switch:before {
        content: attr(data-checked);
        position: absolute;
        top: 0;
        text-transform: uppercase;
        text-align: center;
    }

    .can-toggle label .can-toggle__switch:after {
        content: attr(data-unchecked);
        position: absolute;
        z-index: 5;
        text-transform: uppercase;
        text-align: center;
        background: white;
        transform: translate3d(0, 0, 0);
    }

    .can-toggle input[type=checkbox][disabled]~label {
        color: rgba(119, 119, 119, 0.5);
    }

    .can-toggle input[type=checkbox]:focus~label .can-toggle__switch,
    .can-toggle input[type=checkbox]:hover~label .can-toggle__switch {
        background-color: #777;
    }

    .can-toggle input[type=checkbox]:focus~label .can-toggle__switch:after,
    .can-toggle input[type=checkbox]:hover~label .can-toggle__switch:after {
        color: #5e5e5e;
    }

    .can-toggle input[type=checkbox]:hover~label {
        color: #6a6a6a;
    }

    .can-toggle input[type=checkbox]:checked~label:hover {
        color: #55bc49;
    }

    .can-toggle input[type=checkbox]:checked~label .can-toggle__switch {
        background-color: #70c767;
    }

    .can-toggle input[type=checkbox]:checked~label .can-toggle__switch:after {
        color: #4fb743;
    }

    .can-toggle input[type=checkbox]:checked:focus~label .can-toggle__switch,
    .can-toggle input[type=checkbox]:checked:hover~label .can-toggle__switch {
        background-color: #5fc054;
    }

    .can-toggle input[type=checkbox]:checked:focus~label .can-toggle__switch:after,
    .can-toggle input[type=checkbox]:checked:hover~label .can-toggle__switch:after {
        color: #47a43d;
    }



    .can-toggle label .can-toggle__switch {
        transition: background-color 0.3s cubic-bezier(0, 1, 0.5, 1);
        background: #848484;
    }

    .can-toggle label .can-toggle__switch:before {
        color: rgba(255, 255, 255, 0.5);
    }

    .can-toggle label .can-toggle__switch:after {
        -webkit-transition: -webkit-transform 0.3s cubic-bezier(0, 1, 0.5, 1);
        transition: transform 0.3s cubic-bezier(0, 1, 0.5, 1);
        color: #777;
    }

    .can-toggle input[type=checkbox]:focus~label .can-toggle__switch:after,
    .can-toggle input[type=checkbox]:hover~label .can-toggle__switch:after {
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.4);
    }

    .can-toggle input[type=checkbox]:checked~label .can-toggle__switch:after {
        transform: translate3d(42px, 0, 0);
    }

    .can-toggle input[type=checkbox]:checked:focus~label .can-toggle__switch:after,
    .can-toggle input[type=checkbox]:checked:hover~label .can-toggle__switch:after {
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.4);
    }

    .can-toggle label {
        font-size: 14px;
    }

    .can-toggle label .can-toggle__switch {
        height: 26px;
        flex: 0 0 88px;
        border-radius: 4px;
    }

    .can-toggle label .can-toggle__switch:before {
        left: 42px;
        font-size: 12px;
        line-height: 26px;
        width: 47px;
        padding: 0 12px;
    }
    .can-toggle label .can-toggle__switch:after {
        top: 2px;
        left: 2px;
        border-radius: 2px;
        width: 42px;
        line-height: 22px;
        font-size: 12px;
    }

    

    .can-toggle.demo-rebrand-1 input[type=checkbox][disabled]~label {
        color: rgba(181, 62, 116, 0.5);
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:focus~label .can-toggle__switch,
    .can-toggle.demo-rebrand-1 input[type=checkbox]:hover~label .can-toggle__switch {
        background-color: #b53e74;
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:focus~label .can-toggle__switch:after,
    .can-toggle.demo-rebrand-1 input[type=checkbox]:hover~label .can-toggle__switch:after {
        color: #8f315c;
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:hover~label {
        color: #a23768;
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:checked~label:hover {
        color: #39916a;
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:checked~label .can-toggle__switch {
        background-color: #44ae7f;
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:checked~label .can-toggle__switch:after {
        color: #368a65;
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:checked:focus~label .can-toggle__switch,
    .can-toggle.demo-rebrand-1 input[type=checkbox]:checked:hover~label .can-toggle__switch {
        background-color: #3d9c72;
    }

    .can-toggle.demo-rebrand-1 input[type=checkbox]:checked:focus~label .can-toggle__switch:after,
    .can-toggle.demo-rebrand-1 input[type=checkbox]:checked:hover~label .can-toggle__switch:after {
        color: #2f7757;
    }



    .can-toggle.demo-rebrand-1 label .can-toggle__switch {
        transition: background-color 0.3s ease-in-out;
        background: #c14b81;
    }

    .can-toggle.demo-rebrand-1 label .can-toggle__switch:before {
        color: rgba(255, 255, 255, 0.6);
    }

    .can-toggle.demo-rebrand-1 label .can-toggle__switch:after {
        -webkit-transition: -webkit-transform 0.3s ease-in-out;
        transition: transform 0.3s ease-in-out;
        color: #b53e74;
    }
</style>
<div class="panel_s invoice accounting-template">
    <div class="additional"></div>
    <div class="panel-body">
        <?php if (isset($invoice)) { ?>
            <?php echo format_invoice_status($invoice->status); ?>
            <hr class="hr-panel-heading" />
        <?php } ?>
        <?php hooks()->do_action('before_render_invoice_template'); ?>
        <?php if (isset($invoice)) {
            echo form_hidden('merge_current_invoice', $invoice->id);
        } ?>
        <div class="row">
            <div class="col-md-6">
                <fieldset>
                    <legend><?= _l('ch_service_info') ?></legend>
                    <?php
                    $events = '';
                    if (isset($invoice)) {
                        $events = 'disabled';
                    } ?>
                    <div class="col-md-6">
                        <?php $value = (isset($invoice) ? _d($invoice->date) : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('date', 'ch_service_date', $value); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number" class="control-label"><?php echo _l('ch_service_code'); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?php echo (isset($invoice) ? ($invoice->prefix) : get_option('service_prefix')); ?></span>
                                <?php
                                $number = sprintf('%06d', ch_getMaxID('id', 'tbl_services') + 1);
                                $value = (isset($invoice) ? ($invoice->code) : $number);
                                ?>
                                <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" >
                        <label for="suppliertid" class="control-label"><?php echo _l('ch_service_suppliers'); ?></label>
                        <?php
                        $suppliertid = (isset($invoice) ? ($invoice->suppliers_id) : 0);
                        ?>
                        <input data-placeholder="<?= _l('ch_service_suppliers') ?>" value="<?=$suppliertid?>" name="suppliertid" style="width: 100%" id="suppliertid">
                    </div>
                   
                    <div class="col-md-12" style="margin-top: 10px;">
                        <label for="costs_id" class="control-label"><?php echo _l('ch_costs'); ?></label>
                         <?php $id_costs = (isset($invoice) ? $invoice->type_service : ''); ?>
                        <?php echo render_select('id_costs', $costs, array('id', 'name'), '', $id_costs); ?>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset>
                    <legend>THÔNG TIN KHÁC</legend>
                    <div class="hide">
                        <?php
                        $currency_attr = array('disabled' => true, 'data-show-subtext' => true);
                        $currency_attr = apply_filters_deprecated('invoice_currency_disabled', [$currency_attr], '2.3.0', 'invoice_currency_attributes');

                        foreach ($currencies as $currency) {
                            if ($currency['isdefault'] == 1) {
                                $currency_attr['data-base'] = $currency['id'];
                            }
                            if (isset($invoice)) {
                                if ($currency['id'] == $invoice->currency) {
                                    $selected = $currency['id'];
                                }
                            } else {
                                if ($currency['isdefault'] == 1) {
                                    $selected = $currency['id'];
                                }
                            }
                        }
                        $currency_attr = hooks()->apply_filters('invoice_currency_attributes', $currency_attr);
                        ?>
                        <?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
                    </div>

                    <?php $rel_id = (isset($invoice) ? $invoice->id : false); ?>
                    <?php
                    if (isset($custom_fields_rel_transfer)) {
                        $rel_id = $custom_fields_rel_transfer;
                    }
                    ?>
                    <?php echo render_custom_fields('invoice', $rel_id); ?>
                    <?php $value = (isset($invoice) ? $invoice->note : ''); ?>
                    <div class="col-md-12">
                        <?php echo render_textarea('note', 'invoice_note', $value, array('rows' => 6)); ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="panel-body mtop10">
        <?php if (isset($invoice_from_project)) {
            echo '<hr class="no-mtop" />';
        } ?>
        <div class="table-responsive s_table">
            <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                    <tr>
                        <th width="5%" align="center"><a onclick="change_item_load_v3()" class="btn btn-info btn-icon"><?=_l('ch_service_add_colum')?></a></th>
                        <th width="30%" align="center"><?php echo _l('ch_service_items_in'); ?></th>
                        <th width="10%" align="center"><?php echo _l('ch_service_quanliti_in'); ?></th>
                        <th width="25%" align="center"><?php echo _l('ch_service_price_in'); ?></th>
                        <th width="25%" align="center"><?php echo _l('ch_service_toal_in'); ?></th>
                        <th align="center"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    if (isset($invoice)) {
                        foreach ($invoice->items as $item) {
                            $manual    = false;
                            $table_row = '<tr class="sortable">';
                            $table_row .= '<td class="text-center"></td>';
                            $table_row .= '<td class="text-right"><input  id="name" name="items[' . $i . '][name]" class="form-control" placeholder="NỘI DUNG PHÁT SINH" value="' . ($item['name']) . '"><input class="hide" id="id" name="items[' . $i . '][id]" class="form-control" value="' . ($item['id']) . '"></td>';
                            $table_row .= '<td class="text-right"><input  id="quanliti" name="items[' . $i . '][quanliti]" onchange="formatNumBerKeyUp(this)" class="form-control text-center quanliti" placeholder="Số lượng" value="' . number_format($item['quanliti']) . '"></td>';
                            $table_row .= '<td class="text-right"><input  id="price" name="items[' . $i . '][price]" onchange="formatNumBerKeyUp(this)" class="form-control text-right price" placeholder="Đơn giá" value="' . number_format($item['price']) . '"></td>';
                            $table_row .= '</td><td class="text-right subtotalss">' . number_format($item['total']) . '</td><td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>';
                            $table_row .= '</tr>';
                            echo $table_row;
                            $i++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-8 col-md-offset-4">
            <table class="table text-right">
                <tbody>
                    <tr>
                        <td><span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
                        </td>
                        <td class="quantili_all">0
                        </td>
                    </tr>
                    <tr>
                        <td><span class="bold"><?php echo _l('Service_total_app_vat'); ?> :</span>
                        </td>
                        <td class="total_novat">0
                        </td>
                    </tr>
                    <tr>
                        <td><div class="bold"><?php echo _l('Chiết khấu'); ?> :
                                <div style="width: 79px;float: right;" class="can-toggle demo-rebrand-1">
                                    <input <?=(isset($invoice)&&($invoice->type_discount == 1) ? 'checked' : '')?> id="d" name="type_discount" type="checkbox">
                                    <label for="d">
                                        <div class="can-toggle__switch" data-checked="TM" data-unchecked="%"></div>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                        <input id="discount" name="discount" class="form-control text-right discount" onchange="formatNumBerKeyUp(this)" placeholder="<?=_l('estimate_discount')?>" value="<?=(isset($invoice) ? formatNumber($invoice->detail_discount) : 0)?>" aria-invalid="false">
                        </td>
                    </tr>
                    <tr>
                        <td><span class="bold"><?php echo _l('Tiền thuế VAT'); ?> :<?php
                                                                                    $default_tax = unserialize(get_option('default_tax'));
                                                                                    $select = '<input type="hidden" class="tax_rate" name="tax_rate" value=""><select class="selectpicker tax_ch main-tax" data-width="250px" name="tax_id" data-none-selected-text="' . _l('no_tax') . '">';
                                                                                    foreach ($taxes as $tax) {
                                                                                        $selected = '';
                                                                                        if (empty($invoice)) {
                                                                                            if (is_array($default_tax)) {
                                                                                                if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                                                                                    $selected = ' selected ';
                                                                                                }
                                                                                            }
                                                                                        } else {
                                                                                            if ($tax['id'] == $invoice->tax_id) {
                                                                                                $selected = ' selected ';
                                                                                            }
                                                                                        }
                                                                                        $select .= '<option value="' . $tax['id'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['name'] . '</option>';
                                                                                    }
                                                                                    $select .= '</select>';
                                                                                    echo $select;
                                                                                    ?></span>
                        </td>
                        <td class="vat">0
                        </td>
                    </tr>
                    <tr>
                        <td><span class="bold"><?php echo _l('Tổng thanh toán'); ?> :</span>
                        </td>
                        <td class="total_all">0
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="removed-items"></div>
        <div id="billed-tasks"></div>
        <div id="billed-expenses"></div>
        <?php echo form_hidden('task_id'); ?>
        <?php echo form_hidden('expense_id'); ?>
    </div>
    <div class="row">
        <div class="col-md-12 mtop15">
            <div class="panel-body bottom-transaction">
                <div class="btn-bottom-toolbar text-right">
                    <div class="btn-group dropup">
                        <button class="btn-tr btn btn-info invoice-form-submit "><?php echo _l('Lưu phiếu'); ?></button>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-pusher"></div>
        </div>
    </div>
</div>
<script>
    <?php if (!empty($invoice)) { ?>
        setTimeout(function() {
            $('table.invoice-items-table tbody').find('.selectpicker').selectpicker('refresh').change();
        }, 700);
    <?php } ?>

    function se_contract() {
        var id = jQuery('#clientid').val()

        var dataString = {
            id: id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>invoices/load_bill_client",
            data: dataString,
            cache: false,
            success: function(data) {
                if (data == "<option></option>") {
                    if (id != "") {
                        alert_float('danger', "<?php echo _l('Không tìm thấy phiếu xuất bill nào') ?>");
                        $('#id_contract').html('');
                    } else {
                        $('#id_contract').html('');
                    }
                } else {
                    $('#select_bill').html(data).selectpicker('refresh');
                    alert_float('success', "<?php echo _l('Tìm thấy hợp đồng chưa xuất hóa đơn') ?>");
                }
            }
        });
    }
    var i = <?= $i ?>;

    function change_item_load_v3() {
        i++;
        var _td = _td + '<td class="text-center count"></td>';
        _td = _td + '<td class="text-right"><input  id="name" name="items[' + i + '][name]" class="form-control" placeholder="<?=_l('ch_service_items')?>" value=""></td>';
        _td = _td + '<td class="text-right"><input  id="quanliti" name="items[' + i + '][quanliti]" class="form-control text-center quanliti" onchange="formatNumBerKeyUp(this)" placeholder="Số lượng" value="0"></td>';
        _td = _td + '<td class="text-right"><input  id="price" name="items[' + i + '][price]" class="form-control text-right price" onchange="formatNumBerKeyUp(this)" placeholder="Đơn giá" value="0"></td>';
        _td = _td + '<td class="text-right subtotalss">0</td><td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>';

        $('table.invoice-items-table tbody').prepend('<tr class="tr_">' + _td + '</tr>');
        // $(".ui-sortable").html('<tr class="tr_'+data.id+'">' + _td + '</tr>');
        // tinh_tien(data.id);
        $('table.invoice-items-table tbody').find('.selectpicker').selectpicker('refresh');
        $('select.tax_ch').change();
        countrow();
    }
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };
    countrow();

    function countrow() {
        var items = $('table.invoice-items-table tbody').find('tr');
        var dem = items.length;
        $.each(items, (index, value) => {
            console.log($(value).find('td:nth-child(1)'));

            $(value).find('td:nth-child(1)').text(dem);
            dem--;
        });
    }

    function change_item_load_v2() {
        $(".ui-sortable").html('');
        var _td = _td + '<td class="text-right"><input  id="price" name="items[0][price]" class="form-control text-right" onchange="formatNumBerKeyUp(this)" placeholder="Đơn giá" value="0"></td>';
        _td = _td + '<td><?php
                            $default_tax = unserialize(get_option('default_tax'));
                            $select = '<input type="hidden" class="tax_rate" name="items[0][tax_rate]" value=""><select class="selectpicker tax_ch main-tax" data-width="100%" name="items[0][tax_id]" data-none-selected-text="' . _l('no_tax') . '">';
                            foreach ($taxes as $tax) {
                                $selected = '';
                                if (is_array($default_tax)) {
                                    if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                        $selected = ' selected ';
                                    }
                                }
                                $select .= '<option value="' . $tax['id'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
                            }
                            $select .= '</select>';
                            echo $select;
                            ?></td>';
        _td = _td + '<td class="text-right subtotalss">0</td><td></td>';
        $('table.invoice-items-table tbody').append('<tr class="tr_">' + _td + '</tr>');
        // $(".ui-sortable").html('<tr class="tr_'+data.id+'">' + _td + '</tr>');
        // tinh_tien(data.id);
        $('table.invoice-items-table tbody').find('.selectpicker').selectpicker('refresh');
        $('select.tax_ch').change();
    }

    function change_item_load(id) {
        if (!empty(id)) {
            var dataString = {
                id: id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>invoices/load_bill_invoice",
                data: dataString,
                cache: false,
                success: function(data) {
                    data = JSON.parse(data);
                    if (!empty(data)) {
                        $(".ui-sortable").html('');
                        $('#datestart').val(data.date_start);
                        $('#dateend').val(data.date_end);
                        $('#type_contract').val(data.type_contract);
                        var _td = _td + '<td></td><td class="heading">' +
                            '<div class="input-group">' +
                            '<span class="input-group-addon">' + data.prefix + '- <input name="items[0][id_bill]" style="display: none;" value="' + data.id + '"></span>' +
                            '<input type="text" readonly  class="form-control" data-isedit="" data-original-number="" aria-required="true" value="' + data.code + '"> ' +
                            '</div>'

                            +
                            '</td>';
                        _td = _td + '<td class="text-right"><input  id="price" name="items[0][price]" class="form-control hide" placeholder="Đơn giá" value="' + Number(data.total) + '">' + data.total_for + '</td>';
                        _td = _td + '<td><?php
                                            $default_tax = unserialize(get_option('default_tax'));
                                            $select = '<input type="hidden" class="tax_rate" name="items[0][tax_rate]" value=""><select class="selectpicker tax_ch main-tax" data-width="100%" name="items[0][tax_id]" data-none-selected-text="' . _l('no_tax') . '">';
                                            foreach ($taxes as $tax) {
                                                $selected = '';
                                                if (is_array($default_tax)) {
                                                    if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                                        $selected = ' selected ';
                                                    }
                                                }
                                                $select .= '<option value="' . $tax['id'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
                                            }
                                            $select .= '</select>';
                                            echo $select;
                                            ?></td>';
                        _td = _td + '<td class="text-right subtotalss">' + data.total_for + '</td><td></td>';
                        $('table.invoice-items-table tbody').append('<tr class="tr_' + data.id + '">' + _td + '</tr>');
                        // $(".ui-sortable").html('<tr class="tr_'+data.id+'">' + _td + '</tr>');
                        // tinh_tien(data.id);
                        $('table.invoice-items-table tbody').find('.selectpicker').selectpicker('refresh');
                        $('select.tax_ch').change();
                    } else {
                        $('#datestart').val('');
                        $('#dateend').val('');
                        $('#type_contract').val('');
                        $(".ui-sortable").html('');
                    }

                }
            });
        } else {
            $('#datestart').val('');
            $('#dateend').val('');
            $('#type_contract').val('');
            $(".ui-sortable").html('');
        }
    }

    function delete_colum(id) {
        $(".tr_" + id).remove();
        $('#select_bill').val('');
    }
    $('.invoice-form-submit').on('click', (e) => {
        var items = $('table.invoice-items-table tbody tr');
        if (items.length == 0) {
            alert_float('danger', '<?= _l('Hàng hóa - dịch vụ không được để rỗng') ?>');
            return;
        }
        if ($('input.error').length) {
            e.preventDefault();
            alert('<?= _l('Hàng hóa - dịch vụ không được để rỗng') ?>');
            return;
        } else {
            $('#invoicesch-form').submit();
        }
    });

    function ajaxSelectCallBack_hau(element, url, id, types = '')
            {
                if (id > 0)
                {
                    $(element).val(id).select2({
                        width: 'resolve',
                        allowClear: false,
                        initSelection: function (element, callback) {
                            $.ajax({
                                type: "get", async: false,
                                url: site.base_url + url + '/' + $(element).val(),
                                dataType: "json",
                                success: function (data) {
                                    callback(data.results[0]);
                                }
                            });
                        },
                        ajax: {
                            url: url,
                            dataType: 'json',
                            quietMillis: 15,
                            data: function (term, page) {
                                return {
                                    type:$('#type_items').val(),
                                    types: types,
                                    term: term,
                                    limit: 50
                                };
                            },
                            results: function (data, page) {
                                if (data.results != null) {
                                    return {results: data.results};
                                } else {
                                    return {results: [{id: '', text: 'No Match Found'}]};
                                }
                            }
                        },
                            dropdownCssClass: "bigdrop",
                            escapeMarkup: function (m) { return m; }
                    });
                } else {
                    $(element).select2({
                        // minimumInputLength: 1,
                        width: 'resolve',
                        allowClear: false,
                        ajax: {
                            url: site.base_url + url + '/' + $(element).val(),
                            dataType: 'json',
                            quietMillis: 15,
                            data: function (term, page) {
                                return {
                                    type:$('#type_items').val(),
                                    types: types,
                                    term: term,
                                    limit: 50
                                };
                            },
                            results: function (data, page) {
                                if(data.results != null) {
                                    return { results: data.results };
                                } else {
                                    return { results: [{code_client:'',id: '', text: 'No Match Found'}]};
                                }
                            }
                        },
                        dropdownCssClass: "bigdrop",
                        escapeMarkup: function (m) { return m; }
                    });
                }
            }
    $(function(e) {
        ajaxSelectCallBack_hau($('#suppliertid'), "admin/service/SearchSuppliert", $('#suppliertid').val());
    });
</script>