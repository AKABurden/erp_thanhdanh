<?php echo form_open('admin/quotes/convert_order/' . $quote['id'].'/'.$ptm, array('id' => 'add-order')); ?>
<style>
    .table-child {
        margin: 5px !important;
    }

    .table-child tr th {
        background: #0e306340 !important;
        border: 1px solid #0e306340 !important;
        color: black !important;
    }

    .table-child-size {
        margin: 5px !important;
    }

    .table-child-size tr th {
        background: #0e306340 !important;
        border: 1px solid #0e306340 !important;
        color: black !important;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_convert_order'); ?> <?= $ptm ? 'PTM' : '' ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y H:i:s')), 'placeholder="' . lang('date') . '" id="date" required class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('customers', 'customers') ?>
                        <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : (!empty($customer) ? ($quote['type_customer'] == "customers" ? $customer['company_short'] : $customer['name']) : '')), 'placeholder="' . lang('customers') . '" id="customers" required class="form-control input-tip" readonly'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_address_delivery', 'address_delivery') ?>
                        <div class="input-group">
                            <input type="tel" name="address_delivery" id="address_delivery" data-placeholder="<?= lang('tnh_address_delivery') ?>" class="modal-select2" style="width: 100%;" value="">
                            <span class="input-group-addon">
                                <a href="<?= base_url('admin/clients/addShipping/' . $customer_id) ?>" class="tnh-modal2" data-tnh="modal" data-toggle="modal" data-target="#myModal2"><i class="fa fa-plus"></i></a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('staff_admin', 'staff_admin') ?>
                        <select name="staff_admin" id="staff_admin" data-placeholder="<?= lang('staff_admin') ?>" style="width: 100%;" class="" required="required">
                            <option value=""></option>
                            <?php foreach ($staff as $key => $value) : ?>
                                <option value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="' . lang('note') . '" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_status_orders', 'status_orders') ?>
                        <select name="status_orders" id="status_orders" data-placeholder="<?= lang('tnh_status_orders') ?>" class="status_orders" style="width: 100%;" required>
                            <option value=""></option>
                            <?php foreach ($status_orders as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_currencies', 'currencies') ?>
                        <select name="currencies" id="currencies" data-placeholder="<?= lang('tnh_currencies') ?>" class="currencies" style="width: 100%;" required>
                            <option value=""></option>
                            <?php foreach ($currencies as $key => $value) : ?>
                                <option <?= $quote['currencies'] == $value['id'] ? 'selected' : '' ?> data-amount_to_vnd="<?= $value['amount_to_vnd'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('amount_to_vnd', 'amount_to_vnd') ?>
                        <input type="text" name="amount_to_vnd" id="amount_to_vnd" placeholder="<?= lang('amount_to_vnd') ?>" class="form-control money-format" value="<?= formatMoney($quote['amount_to_vnd']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_type_orders', 'type_orders') ?>
                        <select name="type_orders" id="type_orders" data-placeholder="<?= lang('tnh_type_orders') ?>" class="type_orders modal-select2" style="width: 100%;" required>
                            <option value=""></option>
                            <?php foreach ($type_orders as $key => $value) : ?>
                                <option <?= $value['id'] == TYPE_SAMPLE_ORDER ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="col-md-12 mbot10">
                    <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('c_type_items', 'type_items') ?>
                            <select name="type_items" id="type_items" data-placeholder="<?= lang('c_type_items') ?>" class="type_items modal-select2" style="width: 100%;">
                                <option value=""></option>
                                <?php foreach ($type_items as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mbot10">
                    <div class="row">
                        <div class="col-md-4">
                            <?= lang('File nhập columns', 'file_import') ?>
                            <input type="file" name="file_import" id="file_import" class="form-control file_import" value="">
                        </div>
                        <div class="col-md-2">
                            <?= lang('Mã thành phẩm import', 'code_import') ?>
                            <input type="text" name="code_import" placeholder="<?= lang('Mã thành phẩm import') ?>" id="code_import" class="form-control code_import" value="">
                        </div>
                        <div class="col-md-3">
                            <!-- <a href="<?= base_url('file/orders/import_columns_orders.xlsx?vs=1.1') ?>" target="_blank" class="btn btn-success" style="margin-top: 27px;"><?= lang('File mẫu') ?></a> -->
                            <a href="javascript:void(0)" onclick="exportExcelTemplate()" class="btn btn-success" style="margin-top: 27px;"><?= lang('File mẫu') ?></a>
                            <a href="javascript:void(0)" onclick="addImportColumns()" class="btn btn-primary" style="margin-top: 27px;"><?= lang('import') ?></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="preview-order" class="dt-tnh tnh-table table-bordered table-hover dont-responsive-table dataTable" style="min-width: 1700px; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th style="width: 120px;" class="text-center"><?= lang('tnh_product_code') ?></th>
                                    <th style="width: 150px;" class="text-center"><?= lang('tnh_product_name_customer') ?></th>
                                    <!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_mode_product') ?></th> -->
                                    <th style="width: 50px;" class="text-center"><?= lang('tnh_dvt') ?></th>
                                    <th style="width: 100px;" class="text-center"><?= lang('tnh_sample_quantity') ?></th>
                                    <th style="width: 100px;" class="text-center"><?= lang('tnh_total_quantity') ?></th>
                                    <th style="width: 100px;" class="text-center"><?= lang('tnh_unit_price') ?></th>
                                    <th style="width: 100px;" class="text-center"><?= lang('tnh_total_amount') ?></th>
                                    <!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_discount_percent') ?></th> -->
                                    <!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_grand_total') ?></th> -->
                                    <!-- <th style="width: 100px;" class="text-center"><?= lang('tnh_size') ?></th> -->
                                    <th style="width: 80px;" class="text-center"><?= lang('tnh_loss') ?></th>
                                    <th style="width: 120px;" class="text-center"><?= lang('cong_shipment_date') ?></th>
                                    <th style="width: 100px;" class="text-center"><?= lang('note') ?></th>
                                    <th style="width: 80px;" class="text-center"><?= lang('actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 0; ?>
                                <?php foreach ($items as $key => $value) : ?>
                                    <?php
                                    $type_item = $value['type_item'];
                                    $item_id = $value['item_id'];
                                    $strMoreProduct = '';
                                    $size_name = '';
                                    $loss = '';
                                    $mode_product = '';
                                    $unit = null;
                                    if ($type_item == "products") {
                                        $info = $this->products_model->rowProduct($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                        $strMoreProduct = '
                                                <div>SL con/tờ: <span class="quantity_child_sheet">' . $info['quantity_child_sheet'] . '</span></div>
                                                <div>SL tờ/kiện: <span class="quantity_sheet_bale">' . $info['quantity_sheet_bale'] . '</div>
                                            ';
                                        $dtSize = $this->products_model->getSizeById($info['size']);
                                        $size_name = !empty($dtSize) ? $dtSize['name'] : '';
                                        $loss = $info['loss'];
                                        $mode_product = $info['mode_product'];
                                    } else if ($type_item == "materials") {
                                        $info = $this->items_model->rowMaterial($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                    }

                                    $htmlExchange = '';
                                    if ($value['type_item'] == "products") {
                                        $exchange = $this->site_model->getExchangeProducts($value['item_id']);
                                        if (!empty($exchange)) {
                                            foreach ($exchange as $k => $val) {
                                                $htmlExchange .= '<div class="list-exchange">
                                                    <input type="hidden" class="form-control number-exchange" value="' . $val['number_exchange'] . '">
                                                    <span>' . $val['unit_name'] . ': </span>
                                                    <span class="text-number-exchange">' . formatNumber($value['quantity'] / $val['number_exchange']) . '</span>
                                                </div>';
                                            }
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <!-- <td class="text-center td-number"><?= '' //(++$key) 
                                                                                ?></td> -->
                                        <td>
                                            <div class="text-right checkbox checkbox-info">
                                                <input type="checkbox" name="checkbox_item" id="checkbox_item_<?= $counter ?>" class="checkbox_item" value="">
                                                <label for="checkbox_item_<?= $counter ?>"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="items_id[<?= $counter ?>]" class="form-control items_id" value="<?= $value['item_id'].'__'.$value['type_item'] ?>">
                                            <span class="bold div-code"><?= $info['code'] ?></span>
                                            <?= $strMoreProduct ?>
                                        </td>
                                        <td>
                                            <input type="text" placeholder="<?= lang('tnh_product_name_customer') ?>" name="product_name_customer[<?= $counter ?>]" class="form-control product_name_customer" value="">
                                        </td>
                                        <!-- <td class="text-center">
                                            <?= $mode_product ?>
                                        </td> -->
                                        <td class="text-center"><?= $unit['unit'] ?></td>
                                        <td class="text-center hide">
                                            <input type="hidden" name="counter[<?= $counter ?>]" class="form-control counter" value="<?= $counter ?>">
                                            <input type="hidden" name="quote_item_id[<?= $counter ?>]" id="input" class="form-control" value="<?= $value['id'] ?>">
                                            <input type="hidden" name="quantity[<?= $counter ?>]" id="quantity" class="form-control quantity number-format" value="<?= $value['quantity'] ?>">
                                            <div class="show-exchange text-primary mtop5 text-left hide"><?= $htmlExchange ?></div>
                                        </td>
                                        <td>
                                            <input type="text" name="sample_quantity[<?= $counter ?>]" class="form-control sample_quantity" value="0" readonly>
                                        </td>
                                        <td class="td-total-quantity text-center">

                                        </td>
                                        <td class="text-right">
                                            <input type="hidden" name="price" id="price" class="form-control price" value="<?= $value['unit_price'] ?>">
                                            <?= formatMoney($value['unit_price']) ?>
                                        </td>
                                        <td class="text-right td-total-amount">
                                            <?= formatMoney($value['total_amount']) ?>
                                        </td>
                                        <!-- <td>
                                            <input type="number" name="discount_percent_item[<?= $counter ?>]" id="discount_percent_item" class="form-control discount_percent_item" value="<?= $value['discount_precent_item'] ?>">
                                        </td>
                                        <td class="td-grand-total text-right">
                                            <?= formatMoney($value['total_amount']) ?>
                                        </td> -->
                                        <!-- <td>
                                            <div class="text-center td-size"><?= $size_name ?></div>
                                        </td> -->
                                        <td>
                                            <div class="text-center td-loss"><?= $loss ?></div>
                                        </td>
                                        <td>
                                            <div class="td-date">
                                                <div class="sub">
                                                    <div class="sb">
                                                        <div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub[<?= $counter ?>][]" autocomplete="off" class="form-control datepicker date_sub" placeholder="Ngày" value="" style="width: 100%;" title=""></div>
                                                    </div>
                                                </div>
                                                <div class="text-danger show-errors"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="note_items[<?= $counter ?>]" id="note_items[]" class="form-control" rows="3"></textarea>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" onclick="removeTr(this)" class="text-danger"><i class="fa fa-remove"></i></a>
                                        </td>
                                    </tr>
                                    <tr id="tr-child-<?= $counter ?>" class="not-tr tr-child-columns">
                                        <td colspan="20">
                                            <?php
                                            $bodyItems = '';
                                            if ($type_item == "products") {
                                                $productsColumns = $this->products_model->getProductsColumns($item_id);

                                                $trHtmlChild = '';

                                                $thSub = '';
                                                $trAddChild = '';
                                                $html_sub = '';
                                                $trHtmlChild = '';
                                                $trHtmlColumns = '';
                                                if (!empty($productsColumns)) {
                                                    foreach ($productsColumns as $k => $v) {
                                                        $thSub .= '<th class="text-center" style="width:130px;">' . $v['name'] . '</th>';
                                                        $trAddChild .= '
                                                                    <td>
                                                                        <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id][]" class="form-control" value="' . $v['id'] . '">
                                                                        <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value][]" class="form-control" value="' . $v['name'] . '">
                                                                        <input type="text" placeholder="' . $v['name'] . '" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_name][]" class="form-control" value="">
                                                                    </td>
                                                                ';
                                                    }
                                                }


                                                $html_sub .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
                                                        <thead>
                                                            <tr class="not-tr">
                                                                <th class="text-center" style="width: 50px;">
                                                                    <a href="javascript:void(0)" onclick="addChild' . $counter . '(this, ' . $counter . ')"><i class="fa fa-plus"></i></a>
                                                                </th>
                                                                <th class="text-center" style="width: 150px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                                                                <th class="text-center" style="width: 150px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                                                                <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                                                                <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                                                                <th class="text-center" style="width: 100px;">' . lang('tnh_sample_quantity') . '</th>
                                                                ' . $thSub . '
                                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                                            </tr>
                                                        </thead>
                                                            <tbody class="child">
                                                                ' . $trHtmlChild . '
                                                            </tbody>
                                                        </table>
                                                        <script>

                                                            function addChild' . $counter . '(_this, temp_counter) {
                                                                trChild = $(_this).parents("tr");
                                                                tdNumberChild = `<td></td>`;
                                                                tdActionsChild = `<td class="text-center">
                                                                    <a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
                                                                </td>`;

                                                                tdOrderCode = `<td>
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_order_code]" class="form-control" value="0">
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_order_code]" class="form-control" value="order_code">
                                                                    <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][order_code]" placeholder="Mã đơn đặt" class="form-control order_code" value="">
                                                                </td>`;

                                                                tdCommand = `<td>
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_command]" class="form-control" value="0">
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_command]" class="form-control" value="command">
                                                                    <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][command]" placeholder="Chỉ lệnh" class="form-control command" value="">
                                                                </td>`;

                                                                tdQuantityPut = `<td>
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_quantity_put]" class="form-control" value="0">
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_quantity_put]" class="form-control" value="quantity_put">
                                                                    <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][quantity_put]" class="form-control quantity_put number-format" style="width: 100%;" value="0">
                                                                </td>`;

                                                                tdQuantityLoss = `<td>
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_quantity_loss]" class="form-control" value="0">
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_quantity_loss]" class="form-control" value="quantity_loss">
                                                                    <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][quantity_loss]" class="form-control quantity_loss number-format" readonly style="width: 100%;" value="0">
                                                                </td>`;

                                                                tdSampleQuantityItem = `<td>
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_sample_quantity_item]" class="form-control" value="0">
                                                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_sample_quantity_item]" class="form-control" value="sample_quantity_item">
                                                                    <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][sample_quantity_item]" class="form-control sample_quantity_item number-format" style="width: 100%;" value="0">
                                                                </td>`;

                                                                trHtmlChild = `<tr class="not-tr tr-sub-items">
                                                                    ${tdNumberChild}
                                                                    ${tdOrderCode}
                                                                    ${tdCommand}
                                                                    ${tdQuantityPut}
                                                                    ${tdQuantityLoss}
                                                                    ${tdSampleQuantityItem}
                                                                    ' . $trAddChild . '
                                                                    ${tdActionsChild}
                                                                </tr>`;
                                                                trChild.find(".table-child tbody").append(trHtmlChild);
                                                                counter_child++;
                                                            }

                                                            $(document).ready(function () {

                                                            });
                                                        </script>
                                                        ';

                                                $bodyItems .= $html_sub;
                                            }
                                            echo $bodyItems;
                                            ?>
                                        </td>
                                    </tr>
                                    <!-- <tr id="tr-child-<?= $counter ?>" class="not-tr tr-child-size" style="display: none;">
                                        <td colspan="20">
                                            <table class="table table-child" style="width: 50%; margin-left: 50px !important;">
                                                <thead>
                                                    <tr class="not-tr">
                                                        <th class="text-center" style="width: 50px;">
                                                            <a href="javascript:void(0)" onclick="addChild(this, <?= $counter ?>)"><i class="fa fa-plus"></i></a>
                                                        </th>
                                                        <th class="text-center" style="width: 120px;">Size SP</th>
                                                        <th class="text-center" style="width: 120px;">Size ĐC</th>
                                                        <th class="text-center" style="width: 120px;">Style Number</th>
                                                        <th class="text-center" style="width: 120px;">Color</th>
                                                        <th class="text-center" style="width: 100px;">Số lượng</th>
                                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="child">
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr id="tr-child-change-size-<?= $counter ?>" class="not-tr tr-child-change-size" style="display: none;">
                                        <td colspan="20">
                                            <table class="table table-child-size table-child-size-<?= $counter ?>" style="width: 50%; margin-left: 50px !important;">
                                                <thead>
                                                    <tr class="not-tr">
                                                        <th class="text-center" style="width: 50px;">
                                                            <a href="javascript:void(0)" onclick="addChildChangeSize(this, <?= $counter ?>)"><i class="fa fa-plus"></i></a>
                                                        </th>
                                                        <th class="text-center" style="width: 120px;">Số Size</th>
                                                        <th class="text-center" style="width: 120px;">Số lượng</th>
                                                        <th class="text-center" style="width: 120px;">Tờ chẵn</th>
                                                        <th class="text-center" style="width: 120px;">Tờ lẻ</th>
                                                        <th class="text-center" style="width: 100px;">Kiện chẵn</th>
                                                        <th class="text-center" style="width: 100px;">Kiện lẻ</th>
                                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="child">
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr> -->
                                    <?php $counter++; ?>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot>
                                <tr class="bold hide">
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                    <td></td>
                                    <td class="text-center th-total-quantity"><?= formatNumber($quote['total_quantity']) ?></td>
                                    <td></td>
                                    <td class="text-right th-total"><?= formatMoney($quote['total']) ?></td>
                                    <td class="text-right"></td>
                                    <td class="text-right th-grand-total"><?= formatMoney($quote['total']) ?></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table tnh-tb table-bordered table-hover">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><?= lang('tax', 'tax') ?></td>
                                <td>
                                    <select name="tax_id" id="tax_id" class="tax_id" data-placeholder="<?= lang('tax') ?>" style="width: 100%;">
                                        <option value="0"><?= lang('0%') ?></option>
                                        <?php foreach ($taxs as $key => $value) : ?>
                                            <option data-rate="<?= $value['taxrate'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr class="success" style="font-weight: 700;">
                                <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                <td class="td-grand-total-all text-right"><?= formatMoney($quote['total']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<div id="show-form-detail-1"></div>
<script>
    counter = <?= $counter ?>;
    var count_errors = 0;
    var counter_child = 0;
    var ORDER_DEFAULT = <?= ORDER_DEFAULT ?>;
    var ORDER_CHANGE = <?= ORDER_CHANGE ?>;
    var ORDER_CHANGE_SIZE = <?= ORDER_CHANGE_SIZE ?>;
    var size = <?= !empty($size) ? json_encode($size) : '{}' ?>;
    var colors = <?= !empty($colors) ? json_encode($colors) : '{}' ?>;

    function removeTr(_this) {
        tr = $(_this).closest('tr');
        $(_this).closest('tr').remove();
        counter_index = tr.find('.counter').val();
        $('#tr-child-' + counter_index).remove();
        totalOrder();
    }

    function getSize(select_id) {
        var option = '<option value=""></option>';
        option += '<option value="0"></option>';
        $.each(size, function(index, el) {
            selected = select_id == el.id ? 'selected' : '';
            option += '<option value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function getColors(select_id) {
        var option = '<option value=""></option>';
        option += '<option value="0"></option>';
        $.each(colors, function(index, el) {
            selected = select_id == el.id ? 'selected' : '';
            option += '<option value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function addImportColumns() {
        var form = $('#add-order'),
        formData = new FormData(),
        formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        formData.append('counter_index', counter);
        formData.append('counter_child', counter_child);
        $.ajax({
            url : site.base_url+'admin/orders/import_columns',
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
        .done(function(data) {
            if (data.result) {
                counter_child = intVal(data.counter_child);
                cur_counter_index = data.counter_index;
                $('#tr-child-'+cur_counter_index+' tbody').html(data.trHtmlChild);
                alert_float('success', data.message);
                totalOrder();
            } else {
                alert_float('danger', data.message);
            }
        })
        .fail(function() {
            alert_float('danger', 'Vui lòng xóa file chọn lại');
        });
    }

    function exportExcelTemplate() {
        code_import = $('#code_import').val();
        if (!code_import) {
            bootbox.alert('Xin vui lòng nhập mã thành phẩm import');
            return;
        }

        if (code_import) {

            var dataPOST = {};
            dataPOST['code_import'] = code_import;
            dataPOST[csrfData['token_name']] = csrfData['hash'];
            dataPOST['export_excel'] = 1;

            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/orders/exportExcelTemplate',
                data: dataPOST,
                dataType: "json",
                success: function(response) {
                    if (response.result) {
                        alert_float('success', response.message);
                        download(response.filename, response.file);
                    } else {
                        alert_float('danger', response.message);
                    }
                }
            });
        }
    }

    function totalOrder() {
        tb = '#preview-order tbody tr:not("[class^=not-tr]")';
        var table = $(tb).length;
        var total_quantity = 0;
        var total_amount = 0;
        var total_tax_item = 0;
        var total_discount_percent_item = 0;
        var total_discount_direct_item = 0;
        var total_grand_total_item = 0;
        var stt = 1;
        count_errors = 0;
        for (ii = 0; ii < table; ii++) {
            element = $(tb)[ii];

            $(element).find('.td-number').html(stt);
            stt++;
            // quantity = intVal($(element).find('.quantity').val());
            quantity = 0;
            // sample_quantity = intVal($(element).find('.sample_quantity').val());
            price = intVal($(element).find('.price').val());
            loss_item = intVal($(element).find('.td-loss').html()); 

            total_quantity_item = 0;
            cTempCounter = $(element).find('.counter').val();
            elementSub = $('#tr-child-' + cTempCounter + ' tbody tr.tr-sub-items');
            total_quantity_put = 0;
            total_quantity_sample = 0;
            if (typeof elementSub !== "undefined" && elementSub.length > 0) {
                $.each(elementSub, function(index, value) {
                    quantity_put = intVal($(value).find('.quantity_put').val());
                    sample_quantity_item = intVal($(value).find('.sample_quantity_item').val());
                    // quantity_loss = intVal($(value).find('.quantity_loss').val());
                    // quantity_loss = quantity_put * 1/100;
                    quantity_loss = tnhToFixedNumber(quantity_put * loss_item/100, 0);
                    $(value).find('.quantity_loss').val(tnhFormatNumber(quantity_loss));
                    
                    total_quantity_put += quantity_put;
                    total_quantity_item += quantity_put;
                    total_quantity_item += quantity_loss;
                    total_quantity_sample+= sample_quantity_item;
                });
            }

            $(element).find('.sample_quantity').val(tnhFormatNumber(total_quantity_sample));
            total_quantity_item = total_quantity_item + total_quantity_sample;
            quantity = total_quantity_item;
            amount = total_quantity_put * price;
            $(element).find('.td-total-quantity').html(tnhFormatNumber(total_quantity_item));
            shipping = intVal($(element).find('.shipping').val());

            //sub date delivery
            quantity_sub = 0;
            $.each($(element).find('.quantity_sub'), function(index, el) {
                quantity_sub += intVal($(el).val());
            });

            if (quantity_sub > quantity) {
                $(element).find('.show-errors').html(lang_core['total_quantity_less'] + formatNumberTnh(total_quantity_item));
                count_errors++;
            } else {
                $(element).find('.show-errors').html('');
            }
            //end sub date delivery

            discount = intVal($(element).find('.discount').val());
            c_type_discount = intVal($(element).find('.c_type_discount:checked').val());
            dc = 0;
            if (c_type_discount == 1) {
                dc = amount * (discount / 100);
            } else if (c_type_discount == 2) {
                dc = discount;
            }

            $(element).find('.td-total-amount').html(tnhFormatMoney(amount));
            total_quantity += quantity;
            total_amount += amount;

            grand_total_item = amount;
            tax_rate_item = intVal($(element).find('select.tax_item').select2().find(":selected").data('rate'));
            tax_item_amount = 0;
            if (tax_rate_item > 0) {
                tax_item_amount = total_amount * (tax_rate_item / 100);
                total_tax_item += tax_item_amount;
                grand_total_item += tax_item_amount;
            }

            discount_percent_item = intVal($(element).find('.discount_percent_item').val());
            discount_percent_item_amount = 0;
            if (discount_percent_item > 0) {
                discount_percent_item_amount = grand_total_item * (discount_percent_item / 100);
                total_discount_percent_item += discount_percent_item_amount;
                grand_total_item -= discount_percent_item_amount;
            }

            discount_direct_item_amount = intVal($(element).find('.discount_direct_item').val());
            total_discount_direct_item += discount_direct_item_amount;
            grand_total_item -= discount_direct_item_amount;

            $(element).find('.td-grand-total').html(tnhFormatMoney(grand_total_item));
            total_grand_total_item += grand_total_item;

            showExchange = $(element).find('.list-exchange');
            nShowExchange = showExchange.length;
            for (jj = 0; jj < nShowExchange; jj++) {
                elementShowExchange = $(showExchange)[jj];
                numberExchange = intVal($(elementShowExchange).find('.number-exchange').val());
                totalQuantityExchange = quantity / numberExchange;
                $(elementShowExchange).find('.text-number-exchange').html(tnhFormatNumber(totalQuantityExchange));
            }
        }

        $('.th-total-quantity').html(tnhFormatNumber(total_quantity));
        $('.th-total').html(tnhFormatMoney(total_amount));
        $('.th-grand-total').html(tnhFormatMoney(total_grand_total_item));

        grand_total_all = total_grand_total_item;
        tax_rate = intVal($('select.tax_id').select2().find(":selected").data('rate'));
        tax_amount = 0;
        if (tax_rate > 0) {
            tax_amount = total_grand_total_item * (tax_rate / 100);
        }
        grand_total_all += tax_amount;

        discount_percent = intVal($('#discount_percent').val());
        discount_percent_amount = 0;
        if (discount_percent > 0) {
            discount_percent_amount = grand_total_all * (discount_percent / 100);
        }
        grand_total_all -= discount_percent_amount;

        discount_direct = intVal($('#discount_direct').val());
        grand_total_all -= discount_direct;
        $('.td-grand-total-all').html(tnhFormatMoney(grand_total_all));
    }

    function addRowShipping(counter, _this) {
        var div = $(_this).closest('.td-date');

        html = '<div class="sb">' +
            '<div class="col-md-7" style="padding: 0px;"><input type="text" name="date_sub[' + counter + '][]" id="input" class="form-control datepicker date_sub" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>' +
            '<div class="col-md-4" style="padding: 0px;"><input type="text" style="width: 100%;" name="quantity_sub[' + counter + '][]" id="input" class="form-control quantity_sub number-format" value="0" title=""></div>' +
            '<div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>' +
            '</div>';
        div.find('.sub').append(html);
        totalOrder();
        formatNumberPlugin();
        init_datepicker();
    }

    function removeChildSize(_this) {
        cTr = $(_this).closest('tr');
        cTr.remove();
        totalOrder();
    }

    function addChild(_this, temp_counter) {
        trChild = $(_this).parents('tr');

        tdNumberChild = `<td></td>`;
        tdSizeSPChild = `<td>
            <select name="itemsChild[${temp_counter}][${counter_child}][size]" data-placeholder="Size SP" id="size-child-${counter_child}" style="width: 100%;" class="size_sp">
                ${getSize(0)}
            </select>
        </td>`;
        tdSizeDCChild = `<td>
            <input type="text" name="itemsChild[${temp_counter}][${counter_child}][size_dc]" placeholder="Size ĐC" class="form-control size_dc" value="">
        </td>`;
        tdSizeNumberChild = `<td>
            <input type="text" name="itemsChild[${temp_counter}][${counter_child}][style_number]" placeholder="Style Number" class="form-control style_number" value="">
        </td>`;
        tdColorChild = `<td>
            <select name="itemsChild[${temp_counter}][${counter_child}][color]" data-placeholder="Color" id="color-${counter_child}" style="width: 100%;" class="color">
                ${getColors(0)}
            </select>
        </td>`;
        tdQuantityChild = `<td>
            <input type="text" name="itemsChild[${temp_counter}][${counter_child}][quantity]" class="form-control number-format" value="1">
        </td>`;
        tdActionsChild = `<td class="text-center">
            <a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
        </td>`;

        trHtmlChild = `<tr class="not-tr">
            ${tdNumberChild}
            ${tdSizeSPChild}
            ${tdSizeDCChild}
            ${tdSizeNumberChild}
            ${tdColorChild}
            ${tdQuantityChild}
            ${tdActionsChild}
        </tr>`;
        trChild.find('.table-child tbody').append(trHtmlChild);
        $('#size-child-' + counter_child).select2();
        $('#color-' + counter_child).select2();
        counter_child++;
    }


    function totalChildChangeSize(t_temp_counter) {
        tbchangeSize = '.table-child-size-' + t_temp_counter + ' tbody tr';
        var nChangeSize = $(tbchangeSize).length;

        cTrSize = $('.counter[value="' + t_temp_counter + '"]').closest('tr');
        quantity_child_sheet = intVal(cTrSize.find('.quantity_child_sheet').html());
        quantity_sheet_bale = intVal(cTrSize.find('.quantity_sheet_bale').html());
        console.log(nChangeSize);
        for (ii = 0; ii < nChangeSize; ii++) {
            element = $(tbchangeSize)[ii];
            quantity_child = intVal($(element).find('.quantity').val());
            if (quantity_child_sheet > 0) {
                quantity_sheet = quantity_child / quantity_child_sheet;
                even_quantity = Math.floor(quantity_sheet);
                quantity_ceil = Math.ceil(quantity_sheet);
                odd_quantity = quantity_ceil - even_quantity;

                $(element).find('.even-sheet').html(even_quantity);
                $(element).find('.odd-sheet').html(odd_quantity);
            }

            if (quantity_sheet_bale > 0) {
                quantity_bale = quantity_child / quantity_sheet_bale;
                even_quantity_bale = Math.floor(quantity_bale);
                quantity_ceil_bale = Math.ceil(quantity_bale);
                odd_quantity_bale = quantity_ceil_bale - even_quantity_bale;

                $(element).find('.even-bale').html(even_quantity_bale);
                $(element).find('.odd-bale').html(odd_quantity_bale);
            }
        }
    }

    function removeChildChangeSize(_this) {
        cTr = $(_this).closest('tr');
        cTr.remove();
        totalOrder();
    }

    function addChildChangeSize(_this, temp_counter) {
        trChild = $(_this).parents('tr');

        tdNumberChild = `<td></td>`;

        tdNumberSizeChild = `<td>
            <input type="text" name="itemsChildSize[${temp_counter}][${counter_child}][number_size]" placeholder="Số Size" class="form-control" value="">
        </td>`;

        tdQuantityChild = `<td>
            <input type="text" name="itemsChildSize[${temp_counter}][${counter_child}][quantity]" onchange="totalChildChangeSize(${temp_counter})" class="form-control number-format quantity" placeholder="Số lượng" value="">
        </td>`;

        tdEvenSheetChild = `<td class="even-sheet text-center">
        </td>`;

        tdOddSheetChild = `<td class="odd-sheet text-center">
        </td>`;

        tdEvenBaleChild = `<td class="even-bale text-center">
        </td>`;

        tdOddBaleChild = `<td class="odd-bale text-center">
        </td>`;

        tdActionsChild = `<td class="text-center">
            <a href="javascript:void(0)" class="text-danger" onClick="removeChildChangeSize(this)"><i class="fa fa-remove"></i><a/>
        </td>`;

        trHtmlChild = `<tr class="not-tr">
            ${tdNumberChild}
            ${tdNumberSizeChild}
            ${tdQuantityChild}
            ${tdEvenSheetChild}
            ${tdOddSheetChild}
            ${tdEvenBaleChild}
            ${tdOddBaleChild}
            ${tdActionsChild}
        </tr>`;
        trChild.find('.table-child-size tbody').append(trHtmlChild);
        $('#size-child-' + counter_child).select2();
        $('#color-' + counter_child).select2();
        counter_child++;
    }


    $(function() {
        init_datepicker();
        formatNumberPlugin();
        formatMoneyPlugin();
        $('select.currencies').select2();
        $('select.type_orders').select2();
        $('select.type_orders').select2('readonly', true);
        $('select.status_orders').select2({
            'allowClear': true
        });
        $('select.type_items').select2({
            'allowClear': true
        });
        $('#currencies').change(function(event) {
            amount_to_vnd = $(this).select2().find(":selected").data("amount_to_vnd");
            $('#amount_to_vnd').val(tnhFormatMoney(amount_to_vnd));
        });

        $('#staff_admin').select2({
            allowClear: true
        });
        $('.tax_item').select2();
        $('.tax_id').select2();

        $('.checkbox_item').change(function(event) {
            cTr = $(this).closest('tr');
            $('#code_import').val('');
            isChecked = $(this).prop('checked');
            $('input.checkbox_item').prop('checked', false);
            if (isChecked) {
                $(this).prop('checked', true);
                item_code = $('.div-code').html();
                $('#code_import').val(item_code);
            }
        });

        $('#type_orders').on('change', function(event) {
            // type_orders = $('#type_orders').val();
            // if (type_orders == ORDER_DEFAULT || type_orders == ORDER_CHANGE) {
            //     $('.tr-child-columns').attr('style', 'display: block;');
            //     $('.tr-child-size').attr('style', 'display: none;');
            //     $('.tr-child-change-size').attr('style', 'display: none;');
            // } else if (type_orders == ORDER_DEFAULT) {
            //     $('.tr-child-size').attr('style', 'display: none;');
            //     $('.tr-child-change-size').attr('style', 'display: none;');
            //     $('.tr-child-columns').attr('style', 'display: none;');
            // } else if (type_orders == ORDER_CHANGE) {
            //     $('.tr-child-size').removeAttr('style');
            //     $('.tr-child-change-size').attr('style', 'display: none;');
            //     $('.tr-child-columns').attr('style', 'display: none;');
            // } else if (type_orders == ORDER_CHANGE_SIZE) {
            //     $('.tr-child-size').attr('style', 'display: none;');
            //     $('.tr-child-change-size').removeAttr('style');
            //     $('.tr-child-columns').attr('style', 'display: none;');
            // }

            // $('.tr-child-size').find('tbody').html();
            // $('.tr-child-change-size').find('tbody').html();
        });

        ajaxSelectParams('#address_delivery', 'admin/clients/searchAddressDelivery', 0, {
            'customer_id': '<?= $quote['type_customer'] . '__' . $quote['customer_id'] ?>'
        });

        for (var i = 0; i < counter; i++) {
            ajaxSelectParams('#customer_item_id' + i, 'admin/clients/searchCustomers', 0);
        }

        $('.quantity, .price, .quantity_sub, .tax_item, .discount_percent_item, .discount_direct_item, .tax_id, #discount_percent, #discount_direct, .sample_quantity, .sample_quantity_item').change(function(event) {
            totalOrder();
        });

        appValidateForm($('#add-order'), {
            'date': 'required',
            'customers': 'required',
            'staff_admin': 'required',
            'currencies': 'required',
            'amount_to_vnd': 'required',
            'type_orders': 'required',
            'status_orders': 'required'
        }, convert);

        function convert(form) {
            if (count_errors > 0) {
                alert_float('danger', lang_core['check_date_enter']);
                return;
            }
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            // var data = $(form).serialize();
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw();
                        }

                        <?php if (!empty($ptm)): ?>
                            var url = site.base_url + 'admin/manufactures/add_productions_plan';
                            var inputs = '';
                            inputs += `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
                            inputs += `<input type="hidden" name="p_id" value="${data.arrProductId}">`;
                            inputs += `<input type="hidden" name="arrObjecOrderstId" value="${data.arrObjecOrderstId}">`;
                            inputs += `<input type="hidden" name="arrObjecBusinesstId" value="">`;
                            inputs += `<input type="hidden" name="cs_id" value="">`;
                            inputs += `<input type="hidden" name="_id_branch" value="${data.id_branch}">`;
                            inputs += `<input type="hidden" name="start_date" value="<?= _d(minusMonth(date('Y-m-d'), 6)) ?>">`;
                            inputs += `<input type="hidden" name="end_date" value="<?= _d(plusMonth(date('Y-m-d'), 12)) ?>">`;
                            inputs += `<input type="hidden" name="is_type" value="quotes">`;
                            $("#show-form-detail-1").html('<form target="_blank" action="' + url + '" method="post" id="poster-detail-1">' + inputs + '</form>');
                            $("#poster-detail-1").submit();
                        <?php endif; ?>

                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>