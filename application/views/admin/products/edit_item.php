<?php echo form_open('admin/products/edit_product/'.$product['id'].'/'.$actions, array('id'=>'edit-product')); ?>
<div class="modal-dialog" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $actions == 'edit' ? _l('tnh_edit_product') : _l('tnh_copy_product'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-left: 15px;">
                        <li role="presentation" class="active">
                            <a href="#home1" aria-controls="home" role="tab" data-toggle="tab"><?= lang('info') ?></a>
                        </li>
                        <li role="presentation">
                            <a href="#tab4" class="tabs-bom" aria-controls="tab4" role="tab" data-toggle="tab"><?= lang('tnh_bom_edit') ?></a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home1">
                            <div class="col-md-4 col-cs-1">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('category', 'category') ?>
                                        <select name="category" id="category" data-placeholder="<?= lang('tnh_category_product') ?>" class="modal-select2" style="width: 100%;">
                                            <option value=""></option>
                                            <?= recursiveCategoryProducts() ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_branch', 'id_branch') ?>
                                        <select name="id_branch" id="id_branch" class="id_branch" required="required" style="width: 100%;"  data-placeholder="<?= lang('tnh_branch') ?>">
                                            <option value=""></option>
                                            <?php if(!empty($branch)): ?>
                                                <?php foreach($branch as $key => $value): ?>
                                                    <option <?= $value['id'] == $product['id_branch'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_species', 'species') ?>
                                        <select name="species" id="species" data-placeholder="<?= lang('tnh_species') ?>" class="modal-select2" style="width: 100%;">
                                            <option value=""></option>
                                            <?php if(!empty($species)): ?>
                                                <?php foreach($species as $key => $value): ?>
                                                    <option <?= $value['id'] == $product['species'] ? 'selected' : '' ?> data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_type_print', 'type_print') ?>
                                        <select name="type_print" id="type_print" data-placeholder="<?= lang('tnh_type_print') ?>" class="modal-select2" style="width: 100%;">
                                            <option value=""></option>
                                            <?php if(!empty($type_print)): ?>
                                                <?php foreach($type_print as $key => $value): ?>
                                                    <option <?= $value['id'] == $product['type_print'] ? 'selected' : '' ?> data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_customer', 'customer') ?>
                                        <input type="text" name="customer" id="customer" data-placeholder="<?= lang('tnh_customer') ?>" class="customer modal-select2" style="width: 100%;" value="<?= $product['customer'] ?>" title="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_type_products', 'type_products') ?>
                                        <select name="type_products" id="type_products" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_type_products') ?>" required="required">
                                            <option value=""></option>
                                            <?php foreach (type_products() as $key => $value): ?>
                                                <option <?= $product['type_products'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_product_code', 'code') ?>
                                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : $product['code']), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                                        <div class="checkbox checkbox-info">
                                            <input type="checkbox" name="hand_input_code" id="hand_input_code" <?= $product['hand_input_code'] ? 'checked' : '' ?> value="1">
                                            <label for="hand_input_code"><?= lang('tnh_hand_input') ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_code_bom', 'code_bom') ?>
                                        <?php echo form_input('code_bom', (isset($_POST['code_bom']) ? $_POST['code_bom'] : $product['code_bom']), 'placeholder="'.lang('tnh_code_bom').'" id="code_bom" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_product_name', 'name') ?>
                                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : $product['name']), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_product_code_customer', 'product_code_customer') ?>
                                        <?php echo form_input('product_code_customer', (isset($_POST['product_code_customer']) ? $_POST['product_code_customer'] : $product['product_code_customer']), 'placeholder="'.lang('tnh_product_code_customer').'" id="product_code_customer" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_product_name_customer', 'product_name_customer') ?>
                                        <?php echo form_input('product_name_customer', (isset($_POST['product_name_customer']) ? $_POST['product_name_customer'] : $product['product_name_customer']), 'placeholder="'.lang('tnh_product_name_customer').'" id="product_name_customer" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 hide">
                                    <div class="form-group">
                                        <?= lang('tnh_sample_cover_code', 'sample_cover_code') ?>
                                        <?php echo form_input('sample_cover_code', (isset($_POST['sample_cover_code']) ? $_POST['sample_cover_code'] : $product['sample_cover_code']), 'placeholder="'.lang('tnh_sample_cover_code').'" id="sample_cover_code" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_mold_code', 'mold_code') ?>
                                        <?php echo form_input('mold_code', (isset($_POST['mold_code']) ? $_POST['mold_code'] : $product['mold_code']), 'placeholder="'.lang('tnh_mold_code').'" id="mold_code" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_price_import', 'price_import') ?>
                                        <?php echo form_input('price_import', (isset($_POST['price_import']) ? $_POST['price_import'] : formatNumber($product['price_import'])), 'placeholder="'.lang('tnh_price_import').'" onkeyup="formatNumBerKeyUpCus(this)" id="price_import" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_price_sell', 'price_sell') ?>
                                        <?php echo form_input('price_sell', (isset($_POST['price_sell']) ? $_POST['price_sell'] : formatNumber($product['price_sell'])), 'placeholder="'.lang('tnh_price_sell').'" onkeyup="formatNumBerKeyUpCus(this)" id="price_sell" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_price_processing', 'price_processing') ?>
                                        <?php echo form_input('price_processing', (isset($_POST['price_processing']) ? $_POST['price_processing'] : formatNumber($product['price_processing'])), 'placeholder="'.lang('tnh_price_processing').'" onkeyup="formatNumBerKeyUpCus(this)" id="price_sell" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantity_minimum', 'quantity_minimum') ?>
                                        <?php echo form_input('quantity_minimum', (isset($_POST['quantity_minimum']) ? $_POST['quantity_minimum'] : formatNumber($product['quantity_minimum'])), 'placeholder="'.lang('tnh_quantity_minimum').'" onkeyup="formatNumBerKeyUpCus(this)" id="quantity_minimum" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantity_max', 'quantity_max') ?>
                                        <?php echo form_input('quantity_max', (isset($_POST['quantity_max']) ? $_POST['quantity_max'] : formatNumber($product['quantity_max'])), 'placeholder="'.lang('tnh_quantity_max').'" onkeyup="formatNumBerKeyUpCus(this)" id="quantity_max" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('size', 'c_size') ?>
                                        <select name="size" id="size" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('c_size') ?>">
                                            <option value=""></option>
                                            <?php if(!empty($size)) : ?>
                                                <?php foreach ($size as $key => $value): ?>
                                                    <option <?= $product['size'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php endforeach ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('c_number_day_sx', 'number_day') ?>
                                        <?php echo form_input('number_day', (isset($_POST['number_day']) ? $_POST['number_day'] : $product['number_day']), 'placeholder="'.lang('c_number_day_sx').'" id="number_day" type="number" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_incident_record', 'incident_record') ?>
                                        <?php echo form_input('incident_record', (isset($_POST['incident_record']) ? $_POST['incident_record'] : $product['incident_record']), 'placeholder="'.lang('tnh_incident_record').'" id="incident_record" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_operating_procedure', 'operating_procedure') ?>
                                        <?php echo form_input('operating_procedure', (isset($_POST['operating_procedure']) ? $_POST['operating_procedure'] : $product['operating_procedure']), 'placeholder="'.lang('tnh_operating_procedure').'" id="operating_procedure" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_withdraw_check_procedure', 'withdraw_check_procedure') ?>
                                        <?php echo form_input('withdraw_check_procedure', (isset($_POST['withdraw_check_procedure']) ? $_POST['withdraw_check_procedure'] : $product['withdraw_check_procedure']), 'placeholder="'.lang('tnh_withdraw_check_procedure').'" id="withdraw_check_procedure" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_prevent_procedure', 'prevent_procedure') ?>
                                        <?php echo form_input('prevent_procedure', (isset($_POST['prevent_procedure']) ? $_POST['prevent_procedure'] : $product['prevent_procedure']), 'placeholder="'.lang('tnh_prevent_procedure').'" id="prevent_procedure" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_time_inventory', 'time_inventory') ?>
                                        <?php echo form_input('time_inventory', (isset($_POST['time_inventory']) ? $_POST['time_inventory'] : $product['time_inventory']), 'placeholder="'.lang('tnh_time_inventory').'" id="time_inventory" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_delivery_norms', 'delivery_norms') ?>
                                        <?php echo form_input('delivery_norms', (isset($_POST['delivery_norms']) ? $_POST['delivery_norms'] : $product['delivery_norms']), 'placeholder="'.lang('tnh_delivery_norms').'" id="delivery_norms" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-cs-2">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('Brand', 'brand_id') ?>
                                        <select name="brand_id" id="brand_id" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('Brand') ?>">
                                            <option value=""></option>
                                            <?php foreach ($brand as $key => $value): ?>
                                                <option <?= $product['brand_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('Phân loại', 'classify') ?>
                                        <?php echo form_input('classify', (isset($_POST['classify']) ? $_POST['classify'] : $product['classify']), 'placeholder="'.lang('Phân loại').'" id="classify" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('ĐV Đo SP', 'unit_measure') ?>
                                        <select name="unit_measure" id="unit_measure" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('ĐV Đo SP') ?>">
                                            <option value=""></option>
                                            <?php foreach ($units as $key => $value): ?>
                                                <option <?= $product['unit_measure'] == $value['unitid'] ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <?php
                                        $arrColumns = [];
                                        $dtColumns = $this->products_model->getProductsColumnsMul($id);
                                        if (!empty($dtColumns)) {
                                            foreach ($dtColumns as $key => $value) {
                                                $arrColumns[] = $value['columns_id'];
                                            }
                                        }
                                    ?>
                                    <div class="form-group">
                                        <?= lang('columns', 'columns') ?>
                                        <select name="columns_id[]" multiple class="columns modal-select2" data-placeholder="<?= lang('columns') ?>" style="width: 100%;">
                                            <option value=""></option>
                                            <?php if(!empty($columns)): ?>
                                                <?php foreach($columns as $key => $value): ?>
                                                    <option <?= in_array($value['id'], $arrColumns) ? 'selected' : '' ?> data-subtext="<?=$value['name_detail']?>"  data-text="<?=$value['code']?>"  value="<?= $value['id'] ?>"><?= $value['code'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_standard_colors', 'standard_colors') ?>
                                        <?php echo form_input('standard_colors', (isset($_POST['standard_colors']) ? $_POST['standard_colors'] : $product['standard_colors']), 'placeholder="'.lang('tnh_standard_colors').'" id="standard_colors" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_pp_check', 'pp_check') ?>
                                        <?php echo form_input('pp_check', (isset($_POST['pp_check']) ? $_POST['pp_check'] : $product['pp_check']), 'placeholder="'.lang('tnh_pp_check').'" id="pp_check" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 hide">
                                    <div class="form-group">
                                        <?= lang('tnh_number_child_sue', 'number_child_sue') ?>
                                        <?php echo form_input('number_child_sue', (isset($_POST['number_child_sue']) ? $_POST['number_child_sue'] : $product['number_child_sue']), 'placeholder="'.lang('tnh_number_child_sue').'" id="number_child_sue" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_packing', 'packing') ?>
                                        <?php echo form_input('packing', (isset($_POST['packing']) ? $_POST['packing'] : $product['packing']), 'placeholder="'.lang('tnh_packing').'" id="packing" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_qr', 'qr') ?>
                                        <?php echo form_input('qr', (isset($_POST['qr']) ? $_POST['qr'] : $product['qr']), 'placeholder="'.lang('tnh_qr').'" id="qr" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_time_stock', 'time_stock') ?>
                                        <?php echo form_input('time_stock', (isset($_POST['time_stock']) ? $_POST['time_stock'] : $product['time_stock']), 'placeholder="'.lang('tnh_time_stock').'" id="time_stock" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('unit', 'unit') ?>
                                        <select name="unit" id="unit" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('unit') ?>" required="required">
                                            <option value=""></option>
                                            <?php foreach ($units as $key => $value): ?>
                                                <option <?= $product['unit_id'] == $value['unitid'] ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('tnh_conversion_unit', 'conversion_unit') ?>
                                        <select name="conversion_unit" id="conversion_unit" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_conversion_unit') ?>" required="required">
                                            <option value=""></option>
                                            <?php foreach ($units as $key => $value): ?>
                                                <option <?= $product['conversion_unit'] == $value['unitid'] ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('tnh_conversion_quantity_unit', 'conversion_quantity_unit') ?>
                                        <input type="text" name="conversion_quantity_unit" id="conversion_quantity_unit" class="form-control number-format" value="<?= $product['conversion_quantity_unit'] ?>" required="required">
                                    </div>
                                </div>
                                <div class="col-md-12 hide">
                                    <table class="tnh-tb table-exchange table table-bordered table-hover">
                                        <thead>
                                            <tr class="primary-table">
                                                <th colspan="4"><?= lang('tnh_exchange') ?></th>
                                            </tr>
                                            <tr>
                                                <th style="width: 80px; text-align: center;">
                                                    <div class="text-center">#
                                                        <!-- <button type="button" class="btn btn-warning btn-icon btn-add-itemss"><i class="fa fa-plus"></i></button> -->
                                                    </div>
                                                </th>
                                                <th>
                                                    <div><?= lang('unit') ?></div>
                                                    <div class="text-danger">VD: 1 thùng : 30 cái</div>
                                                </th>
                                                <th style="width: 150px;"><?= lang('quantity') ?></th>
                                                <!-- <th style="width: 80px; text-align: center;"><i class="fa fa-trash-o"></i></th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($exchanges)): ?>
                                            <?php foreach ($exchanges as $key => $value): ?>
                                                <tr>
                                                    <td class="stt text-center"><?= ++$key ?></td>
                                                    <td>
                                                        <select name="unit_exchange[]"  data-live-search="true" id="unit_exchange" class="form-control unit_exchange selectpicker">
                                                            <option value="0"></option>
                                                            <?php foreach ($units as $k => $val): ?>
                                                                <option <?= $val['unitid'] == $value['unit_id'] ? 'selected' : '' ?> value="<?= $val['unitid'] ?>"><?= $val['unit'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="number_exchange[]" id="number_exchange[]" class="form-control" value="<?= $value['number_exchange'] ?>">
                                                    </td>
                                                    <!-- <td>
                                                        <div class="text-center"><i class="btn btn-danger fa fa-remove remove-exchange"></i></div>
                                                    </td> -->
                                                </tr>
                                            <?php endforeach ?>
                                            <?php else: ?>
                                                <td class="stt text-center">1</td>
                                                <td>
                                                    <select name="unit_exchange[]" data-none-selected-text="<?= lang('unit') ?>"  data-live-search="true" id="unit_exchange" class="form-control unit_exchange selectpicker">
                                                        <option value=""></option>
                                                        <?php foreach ($units as $key => $value): ?>
                                                            <option <?= $value['unit'] == 'thùng' ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="number_exchange[]" id="number_exchange[]" class="form-control" value="0">
                                                </td>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('colors', 'colors') ?>
                                        <select name="colors[]" id="colors" class="form-control selectpicker ajax-select" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('colors') ?>" >
                                            <option value=""></option>
                                            <?php foreach ($colors as $key => $value): ?>
                                                <option value="<?= $value['id'] ?>" selected><?= $value['color_name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_mode', 'mode') ?>
                                        <?php echo form_input('mode', (isset($_POST['mode']) ? $_POST['mode'] : $product['mode']), 'placeholder="'.lang('tnh_mode').'" id="mode" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_brand', 'brand') ?>
                                        <?php echo form_input('brand', (isset($_POST['brand']) ? $_POST['brand'] : $product['brand']), 'placeholder="'.lang('tnh_brand').'" id="brand" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 hide">
                                    <div class="form-group">
                                        <?= lang('tnh_number_labor', 'number_labor') ?>
                                        <?php echo form_input('number_labor', (isset($_POST['number_labor']) ? $_POST['number_labor'] : formatNumber($product['number_labor'])), 'placeholder="'.lang('tnh_number_labor').'" id="mode" onkeyup="formatNumBerKeyUpCus(this)" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_images_represent', 'image') ?>
                                        <input type="file" name="image" id="image" class="form-control" value="" title="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_images_multiple', 'images_multiple') ?>
                                        <input type="file" name="images_multiple[]" id="images_multiple" multiple class="form-control" value="" title="">
                                    </div>
                                    <?php if (!empty($product['images_multiple']) && $actions == 'edit'): ?>
                                    <div class="preview_image" id="avatar_view" style="width: auto;">
                                        <div class="display-block contract-attachment-wrapper img-1">
                                            <?php $images_multiple = explode('||', $product['images_multiple']); ?>
                                            <?php foreach ($images_multiple as $key => $value): ?>
                                            <div class="col-md-2">
                                                <input type="hidden" name="images_old[]" id="images_old[]" class="form-control" value="<?= $value ?>">
                                                <button type="button" class="close remove-image" data-id="50" data-src="uploads/items/50/tru_ringlock.jpg" style="color:red;" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <a href="<?= pathProduct($value) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                    <div class="">
                                                        <img style="max-width: 200px;max-height: 300px;" src="<?= pathProduct($value) ?>">
                                                    </div>
                                                </a>
                                            </div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                    <?php endif ?>
                                    <input type="hidden" name="remove_image" id="remove_image" class="form-control" value="0">
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('note', 'note') ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $product['note']), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-cs-3" <?= !empty($product) && $product['type_products'] != "products" ? '' : '' ?>>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_longs', 'longs') ?>
                                        <?php echo form_input('longs', (isset($_POST['longs']) ? $_POST['longs'] : $product['longs']), 'placeholder="'.lang('tnh_longs').'" id="longs" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_wide', 'wide') ?>
                                        <?php echo form_input('wide', (isset($_POST['wide']) ? $_POST['wide'] : $product['wide']), 'placeholder="'.lang('tnh_wide').'" id="wide" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_height', 'height') ?>
                                        <?php echo form_input('height', (isset($_POST['height']) ? $_POST['height'] : $product['height']), 'placeholder="'.lang('tnh_height').'" id="height" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_mode_product', 'mode_product') ?>
                                        <?php echo form_input('mode_product', (isset($_POST['mode_product']) ? $_POST['mode_product'] : $product['mode_product']), 'placeholder="'.lang('tnh_mode_product').'" id="mode_product" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_stage_mode', 'stage_mode') ?>
                                        <?php echo form_input('stage_mode', (isset($_POST['stage_mode']) ? $_POST['stage_mode'] : $product['stage_mode']), 'placeholder="'.lang('tnh_stage_mode').'" id="stage_mode" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_stage_standard', 'stage_standard') ?>
                                        <?php echo form_input('stage_standard', (isset($_POST['stage_standard']) ? $_POST['stage_standard'] : $product['stage_standard']), 'placeholder="'.lang('tnh_stage_standard').'" id="stage_standard" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_operating_gauge', 'operating_gauge') ?>
                                        <?php echo form_input('operating_gauge', (isset($_POST['operating_gauge']) ? $_POST['operating_gauge'] : $product['operating_gauge']), 'placeholder="'.lang('tnh_operating_gauge').'" id="operating_gauge" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quota_productivity_h', 'quota_productivity_h') ?>
                                        <?php echo form_input('quota_productivity_h', (isset($_POST['quota_productivity_h']) ? $_POST['quota_productivity_h'] : $product['quota_productivity_h']), 'placeholder="'.lang('tnh_quota_productivity_h').'" id="quota_productivity_h" class="form-control number-format input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quota_power_consumption_h', 'quota_power_consumption_h') ?>
                                        <?php echo form_input('quota_power_consumption_h', (isset($_POST['quota_power_consumption_h']) ? $_POST['quota_power_consumption_h'] : $product['quota_power_consumption_h']), 'placeholder="'.lang('tnh_quota_power_consumption_h').'" id="quota_power_consumption_h" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quota_material_replace_t', 'quota_material_replace_t') ?>
                                        <?php echo form_input('quota_material_replace_t', (isset($_POST['quota_material_replace_t']) ? $_POST['quota_material_replace_t'] : $product['quota_material_replace_t']), 'placeholder="'.lang('tnh_quota_material_replace_t').'" id="quota_material_replace_t" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quota_depreciation_ts_date', 'quota_depreciation_ts_date') ?>
                                        <?php echo form_input('quota_depreciation_ts_date', (isset($_POST['quota_depreciation_ts_date']) ? $_POST['quota_depreciation_ts_date'] : $product['quota_depreciation_ts_date']), 'placeholder="'.lang('tnh_quota_depreciation_ts_date').'" id="quota_material_replace_t" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quota_npl_consumption_one', 'quota_npl_consumption_one') ?>
                                        <?php echo form_input('quota_npl_consumption_one', (isset($_POST['quota_npl_consumption_one']) ? $_POST['quota_npl_consumption_one'] : $product['quota_npl_consumption_one']), 'placeholder="'.lang('tnh_quota_npl_consumption_one').'" id="quota_npl_consumption_one" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quota_time_change_one', 'quota_time_change_one') ?>
                                        <?php echo form_input('quota_time_change_one', (isset($_POST['quota_time_change_one']) ? $_POST['tnh_quota_time_change_one'] : $product['quota_time_change_one']), 'placeholder="'.lang('tnh_quota_time_change_one').'" id="quota_time_change_one" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_person_charge', 'person_charge') ?>
                                        <?php echo form_input('person_charge', (isset($_POST['person_charge']) ? $_POST['person_charge'] : $product['person_charge']), 'placeholder="'.lang('tnh_person_charge').'" id="person_charge" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_property_grant', 'property_grant') ?>
                                        <?php echo form_input('property_grant', (isset($_POST['property_grant']) ? $_POST['property_grant'] : $product['property_grant']), 'placeholder="'.lang('tnh_property_grant').'" id="property_grant" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_completion_standard', 'completion_standard') ?>
                                        <?php echo form_input('completion_standard', (isset($_POST['completion_standard']) ? $_POST['completion_standard'] : $product['completion_standard']), 'placeholder="'.lang('tnh_completion_standard').'" id="completion_standard" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_control_criteria', 'control_criteria') ?>
                                        <?php echo form_input('control_criteria', (isset($_POST['control_criteria']) ? $_POST['control_criteria'] : $product['control_criteria']), 'placeholder="'.lang('tnh_control_criteria').'" id="control_criteria" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_productivity_m_w_n', 'productivity_m_w_n') ?>
                                        <?php echo form_input('productivity_m_w_n', (isset($_POST['productivity_m_w_n']) ? $_POST['productivity_m_w_n'] : $product['productivity_m_w_n']), 'placeholder="'.lang('tnh_productivity_m_w_n').'" id="productivity_m_w_n" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quality_problem', 'quality_problem') ?>
                                        <?php echo form_input('quality_problem', (isset($_POST['quality_problem']) ? $_POST['quality_problem'] : $product['quality_problem']), 'placeholder="'.lang('tnh_quality_problem').'" id="quality_problem" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_loss', 'loss') ?>
                                        <?php echo form_input('loss', (isset($_POST['loss']) ? $_POST['loss'] : $product['loss']), 'placeholder="'.lang('tnh_loss').'" id="loss" class="form-control input-tip loss number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantity_child_sheet', 'quantity_child_sheet') ?>
                                        <?php echo form_input('quantity_child_sheet', (isset($_POST['quantity_child_sheet']) ? $_POST['quantity_child_sheet'] : formatNumber($product['quantity_child_sheet'])), 'placeholder="'.lang('tnh_quantity_child_sheet').'" id="quantity_child_sheet" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantity_sheet_bale', 'quantity_sheet_bale') ?>
                                        <?php echo form_input('quantity_sheet_bale', (isset($_POST['quantity_sheet_bale']) ? $_POST['quantity_sheet_bale'] : formatNumber($product['quantity_sheet_bale'])), 'placeholder="'.lang('tnh_quantity_sheet_bale').'" id="quantity_sheet_bale" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('dt_quantity_child_molds', 'quantity_child_molds') ?>
                                        <?php echo form_input('quantity_child_molds', (isset($_POST['quantity_child_molds']) ? $_POST['quantity_child_molds'] : formatNumber($product['quantity_child_molds'])), 'placeholder="'.lang('dt_quantity_child_molds').'" id="quantity_child_molds" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantity_child_molds_offset', 'quantity_child_molds_offset') ?>
                                        <?php echo form_input('quantity_child_molds_offset', (isset($_POST['quantity_child_molds_offset']) ? $_POST['quantity_child_molds_offset'] : formatNumber($product['quantity_child_molds_offset'])), 'placeholder="'.lang('tnh_quantity_child_molds_offset').'" id="quantity_child_molds_offset" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantity_child_molds_flexo', 'quantity_child_molds_flexo') ?>
                                        <?php echo form_input('quantity_child_molds_flexo', (isset($_POST['quantity_child_molds_flexo']) ? $_POST['quantity_child_molds_flexo'] : formatNumber($product['quantity_child_molds_flexo'])), 'placeholder="'.lang('tnh_quantity_child_molds_flexo').'" id="quantity_child_molds_flexo" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('Tồn Cho Phép', 'allowable') ?>
                                        <?php echo form_input('allowable', (isset($_POST['allowable']) ? $_POST['allowable'] : formatNumber($product['allowable'])), 'placeholder="'.lang('Tồn Cho Phép').'" id="allowable" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('Định Mức SP', 'quota') ?>
                                        <?php echo form_input('quota', (isset($_POST['quota']) ? $_POST['quota'] : formatNumber($product['quota'])), 'placeholder="'.lang('Định Mức SP').'" id="quota" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('Định Mức Thùng', 'barrel_size') ?>
                                        <?php echo form_input('barrel_size', (isset($_POST['barrel_size']) ? $_POST['barrel_size'] : formatNumber($product['barrel_size'])), 'placeholder="'.lang('Định Mức Thùng').'" id="barrel_size" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <?php
                                    $dtCustomer = $this->site_model->rowCustomer($product['customer']);
                                    $is_separate_guest = $dtCustomer['is_separate_guest'];
                                    $styleGuest = '';
                                    if (empty($is_separate_guest)) {
                                        $styleGuest = 'style="display: none;"';
                                    }
                                ?>
                                <div class="col-md-12 div-separate-guest" <?= $styleGuest ?>>
                                    <div class="form-group">
                                        <?= lang('tnh_color_size', 'color_size') ?>
                                        <?php echo form_input('color_size', (isset($_POST['color_size']) ? $_POST['color_size'] : $product['color_size']), 'placeholder="'.lang('tnh_color_size').'" id="color_size" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 div-separate-guest" <?= $styleGuest ?>>
                                    <div class="form-group">
                                        <?= lang('tnh_gw', 'gw') ?>
                                        <?php echo form_input('gw', (isset($_POST['gw']) ? $_POST['gw'] : $product['gw']), 'placeholder="'.lang('tnh_gw').'" id="gw" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 div-separate-guest" <?= $styleGuest ?>>
                                    <div class="form-group">
                                        <?= lang('tnh_carton_size', 'carton_size') ?>
                                        <?php echo form_input('carton_size', (isset($_POST['carton_size']) ? $_POST['carton_size'] : $product['carton_size']), 'placeholder="'.lang('tnh_carton_size').'" id="carton_size" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($custom_fields)): ?>
                                <div class="col-md-12">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title"><?= lang('tnh_custom_fields') ?></h3>
                                        </div>
                                        <div class="panel-body">
                                            <?= render_custom_fields('products', $product['id']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                            <div class="col-md-12">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            DANH SÁCH TIÊU CHUẨN
                                        </h3>
                                    </div>
                                    <div class="panel-body">
										<?php
											foreach($this->list_standard as $kfield => $vfield) {?>
                                                <div class="col-md-4">
													<?php $value = !empty($product[$vfield['id_key']]) ? $product[$vfield['id_key']] : '';?>
													<?php
                                                        $this->db->group_start();
                                                        $this->db->where('id_product', $product['id']);
                                                        $this->db->or_where('id_product IS NULL', false, false);
                                                        $this->db->group_end();
                                                        $this->db->where('standard != ""', false, false);
                                                        $data_field = $this->db->get_where('tbllist_other', [
                                                            'type' => $vfield['id'],
                                                        ])->result_array(); ?>
													<?php echo render_select($vfield['id_key'], $data_field, ['id', 'standard'], $vfield['name'] , $value)?>
                                                </div>
											<?php }
										?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab1">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="tb-suppliers" class="tnh-table table-hover table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;"><i class="fa fa-plus btn btn-primary add-supplies"></i></th>
                                                <th style="width: 200px;"><?= lang('tnh_supplies') ?></th>
                                                <th><?= lang('tnh_leadtime') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="t-body">
                                            <?php $counter = 0; ?>
                                        <?php if (!empty($product_suppliers)): ?>
                                            <?php foreach ($product_suppliers as $key => $value): ?>
                                                <?php $leadtimes = $this->products_model->getProductSuppliersByProductAndSupplier($id, $value['supplier_id']) ?>
                                                <tr>
                                                    <td class="td-number text-center"></td>
                                                    <td class="td-suppliers ">
                                                        <input type="hidden" name="counter[]" id="counter" class="form-control counter" value="<?= $counter ?>">
                                                        <input type="text" name="suppliers[<?= $counter ?>]" data-placeholder="<?= lang('choose') ?>" id="suppliers_<?= $counter ?>" class="suppliers modal-select2" style="width: 100%;" value="<?= $value['supplier_id'] ?>">
                                                    </td>
                                                    <td class="td-leadtime">
                                                        <table class="tnh-table tb-subb">
                                                            <thead>
                                                                <th style="width: 50px;" class="text-center">
                                                                    <i onclick="addStageSub(this, <?= $counter ?>)" class="fa fa-plus btn btn-success add-sub-st"></i>
                                                                </th>
                                                                <th style="width: 150px;"><?= lang('tnh_stage') ?></th>
                                                                <th style="width: 150px;"><?= lang('tnh_sequence') ?></th>
                                                                <th><?= lang('tnh_number_date') ?></th>
                                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash" class="remove-sub"></i></th>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (!empty($leadtimes)): ?>
                                                                    <?php foreach ($leadtimes as $k => $val): ?>
                                                                        <tr>
                                                                            <td class="td-number-sub text-center"></td>
                                                                            <td class="td-stage-sub ">
                                                                                <select name="procedure[<?= $counter ?>][]" id="procedure" data-placeholder="<?= lang('choose') ?>" class="procedure modal-select2" style="width: 100%;">
                                                                                    <?php foreach ($procedure_detail as $e => $v): ?>
                                                                                        <option <?= $v['id'] == $val['procedure_id'] ? 'selected' : '' ?> value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
                                                                                    <?php endforeach ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="sequence[<?= $counter ?>][]" id="sequence" class="form-control sequence" value="<?= $val['sequence'] ?>">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="number_date[<?= $counter ?>][]" id="number_date" class="form-control number_date" value="<?= $val['number_date'] ?>">
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <a class="fa fa-remove" onclick="removeStageSubb(this)"></a>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach ?>
                                                                <?php endif ?>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td class="td-actions text-center"><i class="btn btn-danger fa fa-remove remove-suppliers" onclick="removeSuppliers(this)"></i></td>
                                                </tr>
                                                <?php $counter++; ?>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="tb-warehouse" class="tnh-table table-bordered table-condensed table-hover table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;"><i class="fa fa-plus btn btn-primary add-warehouse" onclick="addTbWarehouse(this)"></i></th>
                                                <th style="width: 200px;"><?= lang('tnh_warehouses') ?></th>
                                                <th><?= lang('tnh_vt') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($product_warehouse)): ?>
                                            <?php foreach ($product_warehouse as $k => $val): ?>
                                            <tr>
                                                <td class="td-number-ws text-center"></td>
                                                <td>
                                                    <select onchange="changeWarehouse(this)" name="warehouses[]" id="warehouses" data-placeholder="<?= lang('choose') ?>" class="warehouses modal-select2" style="width: 100%;">
                                                        <?php foreach ($warehouses as $key => $value): ?>
                                                            <option <?= $val['warehouse_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <?php $options = '<option></option>'.recursiveLocationWarehouses($val['warehouse_id']);
                                                    ?>
                                                    <select name="location[]" id="location" data-placeholder="<?= lang('choose') ?>" class="location modal-select2 sl<?= $val['id'] ?>" style="width: 100%;">
                                                        <?= $options ?>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <a class="fa fa-remove" onclick="removeWarehouse(this)"></a>
                                                </td>
                                            </tr>
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    $('select.sl<?= $val['id'] ?>').select2();
                                                    $('select.sl<?= $val['id'] ?>').select2().val(<?= $val['location_id'] ?>).trigger('change');
                                                });
                                            </script>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab4">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="tnh-table table-bordered table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;" class="text-center"><a href="javascript:void(0)" onclick="aBOM(this)"><i class="fa fa-plus"></i></a></th>
                                                <th><?= lang('tnh_list_bom') ?></th>
                                                <th style="width: 200px;"><?= lang('tnh_using') ?></th>
                                                <th style="width: 100px;" class="text-center"><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $counter_bom = 0; ?>
                                            <?php if (!empty($boms_product)): ?>
                                                <?php foreach ($boms_product as $key => $value): ?>
                                                    <tr>
                                                        <input type="hidden" name="product_versions[]" id="inputProduct_versions[]" class="form-control" value="<?= $value['versions'] ?>">
                                                        <td></td>
                                                        <td><?= $value['versions'] ?></td>
                                                        <td>
                                                            <div class="radio radio-info no-mtop no-mbot cbobox"><input type="radio" class="rel_type" <?= $value['versions'] == $product['versions'] ? 'checked' : '' ?> name="using" value="<?= $counter_bom ?>" id="radio_<?= $key ?>"><label for="radio_<?= $key ?>"><?= lang('choose') ?></label></div>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0)" onclick="revBom(this)"><i class="fa fa-remove"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php $counter_bom++; ?>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary edit"><?= $actions == 'edit' ? _l('edit') : _l('copy') ?></button>
        </div>
    </div>
</div>

<?php echo form_close(); ?>
<script type="text/javascript">
    var counter = <?= $counter ?>;
    var counter_bom = <?= $counter_bom ?>;
    var units = <?= !empty($units) ? json_encode($units) : '{}' ?>;
    var procedure_detail = <?= !empty($procedure_detail) ? json_encode($procedure_detail) : '{}' ?>;
    var warehouses = <?= !empty($warehouses) ? json_encode($warehouses) : '{}' ?>;
    var boms = <?= !empty($boms) ? json_encode($boms) : '{}' ?>;
    var edit = 1;
</script>
<script type="text/javascript" src="<?= js('products.js?vs=2.0') ?>"></script>
<script>

    function getBOM() {
        var options = '<option></option>';
        $.each(boms, function(index, el) {
            options+= '<option value="'+ el.id +'">'+el.versions+'</option>';
        });
        return options;
    }

    function aBOM(el)
    {
        tbB = $(el).closest('table');
        tdNB = '<td class="td-number-b text-center"></td>';
        tdBoms = '<td class="td-bs">'+
            '<select name="bs[]" id="bs" data-placeholder="'+lang_core['choose']+'" class="bs modal-select2" style="width: 100%;">'+getBOM()+'</select>'+
        '</td>';
        tdRadio = '<td class="td-radio">'+
            '<div class="radio radio-info no-mtop no-mbot cbobox"><input type="radio" class="rel_type" name="using" value="'+counter_bom+'" id="radio_'+counter_bom+'"><label for="radio_'+counter_bom+'"><?= lang('choose') ?></label></div>'
        '</td>';

        tdRemove = '<td class="text-center">'+
            '<a class="fa fa-remove" onclick="revBom(this)"></a>'+
        '</td>';

        trBm = '<tr>'+
            tdNB+
            tdBoms+
            tdRadio+
            tdRemove+
        '</tr>';

        tbB.find('tbody').append(trBm);
        counter_bom++;
        $('select.bs').select2();
    }

    function revBom(el) {
        $(el).closest('tr').remove();
    }
    ajaxSelectParams('#customer', 'admin/clients/searchOnlyCustomers', 0, true, true);
    function formatss(item) {
        var originalOption = item.element;
        return "<b>" + $(originalOption).data('text') + "</b>"+
        "<br><span class='text-muted-chose' style='font-style: italic;font-size: 10px;'>"+$(originalOption).data('subtext')+"</span>"
    }
    $(function(){
        ajaxSelectParams('#customer', 'admin/clients/searchOnlyCustomers', $('#customer').val(), true, true);
        // selectAjax('#category', false, 'admin/products/searchCategory');
        $('#category').select2({'allowClear': true});
        $('#species').select2({'allowClear': true});
        $('#type_print').select2({'allowClear': true});
        $('#id_branch').select2();
        $('select.columns').select2(
            {
            'allowClear': true,
            formatSelection: formatss,
            formatResult: formatss,
            escapeMarkup: function (m) {
                return m;
            },
        });

        $(document).ready(function () {
            $('#category').val(<?= $product['category_id'] ?>).trigger('change');
        });


        type_products = '<?= $product['type_products'] ?>';
        if (type_products == "semi_products_outside") {
            $('.tabs-bom').hide();
        }
        $('#type_products').change(function(event) {
            type_products = $(this).val();
            if (type_products == "semi_products_outside") {
                $('.tabs-bom').hide();
            } else {
                $('.tabs-bom').show();
            }
        });

        selectAjax('#colors', false, 'admin/products/searchColors');
        appValidateForm($('#edit-product'), {
           category: 'required',
           type_products: 'required',
           unit: 'required',
           code: 'required',
           name: 'required',
           conversion_unit: 'required',
           conversion_quantity_unit: 'required',
           id_branch: 'required',
        }, editproduct);

        $('.remove-image').click(function(event) {
            $(this).closest('.col-md-2').remove();
            $('#remove_image').val(1);
        });

        function editproduct(form) {
            $('.edit').attr('disabled', 'disabled');
            tinymce.get('note').save();
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

            var url = form.action;
            $.ajax({
                url : site.base_url+'admin/products/edit_product/<?= $product['id'] ?>'+'/<?= $actions ?>',
                type : 'POST',
                dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable != 'undefined' && oTable != '') {
                        oTable.draw('page');
                    }
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.edit').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.edit').removeAttr('disabled', 'disabled');
            });
            return false;
        }
        init_editor('textarea[name="note"]');
        init_selectpicker();
    })
</script>