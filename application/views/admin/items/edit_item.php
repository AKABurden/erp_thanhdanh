<?php echo form_open('admin/items/edit_item' . $material['id'] . '/' . $actions, array('id' => 'edit-item')); ?>
<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $actions == 'edit' ? _l('tnh_edit_item') : _l('tnh_copy_item'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <ul class="nav nav-tabs" role="tablist" style="margin-left: 15px;">
                    <li role="presentation" class="active">
                        <a href="#home1" aria-controls="home1" role="tab" data-toggle="tab"><?= lang('info') ?></a>
                    </li>
                    <li role="presentation">
                        <a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab"><?= lang('tnh_supplies') ?></a>
                    </li>
                    <li role="presentation">
                        <a href="#tab3" aria-controls="tab2" role="tab" data-toggle="tab"><?= lang('tnh_warehouses') ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home1">
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_item_materials_category', 'category') ?>
                                    <select name="category" id="category" data-placeholder="<?= lang('tnh_item_materials_category') ?>" class="modal-select2" style="width: 100%;">
                                        <option value=""></option>
                                        <?= recursiveCategoryItems() ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_branch', 'id_branch') ?>
                                    <select name="id_branch" id="id_branch" class="id_branch" required="required" style="width: 100%;" data-placeholder="<?= lang('tnh_branch') ?>">
                                        <option value=""></option>
                                        <?php if (!empty($branch)) : ?>
                                            <?php foreach ($branch as $key => $value) : ?>
                                                <option <?= $material['id_branch'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
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
                                        <?php if (!empty($species)) : ?>
                                            <?php foreach ($species as $key => $value) : ?>
                                                <option <?= $value['id'] == $material['species'] ? 'selected' : '' ?> data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_suppliers', 'suppliers') ?>
                                    <input type="text" name="suppliers" id="suppliers" data-placeholder="<?= lang('tnh_suppliers') ?>" class="suppliers modal-select2" value="<?= $material['suppliers'] ?>" style="width: 100%;">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_material_code', 'code') ?>
                                    <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : $material['code']), 'placeholder="' . lang('tnh_material_code') . '" id="code" required class="form-control input-tip"'); ?>
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="hand_input_code" id="hand_input_code" <?= $material['hand_input_code'] == 1 ? 'checked' : '' ?> value="1">
                                        <label for="hand_input_code"><?= lang('tnh_hand_input') ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_material_name', 'name') ?>
                                    <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : $material['name']), 'placeholder="' . lang('tnh_material_name') . '" id="name" required class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_material_name_customer', 'name_customer') ?>
                                    <?php echo form_input('name_customer', (isset($_POST['name_customer']) ? $_POST['name_customer'] : $material['name_customer']), 'placeholder="' . lang('tnh_material_name_customer') . '" id="name_customer" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_material_code_supplier', 'material_code_supplier') ?>
                                    <?php echo form_input('material_code_supplier', (isset($_POST['material_code_supplier']) ? $_POST['material_code_supplier'] : $material['material_code_supplier']), 'placeholder="' . lang('tnh_material_code_supplier') . '" id="material_code_supplier" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_material_name_supplier', 'name_supplier') ?>
                                    <?php echo form_input('name_supplier', (isset($_POST['name_supplier']) ? $_POST['name_supplier'] : $material['name_supplier']), 'placeholder="' . lang('tnh_material_name_supplier') . '" id="name_supplier" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_quantity_begin', 'quantity_begin') ?>
                                    <?php echo form_input('quantity_begin', (isset($_POST['quantity_begin']) ? $_POST['quantity_begin'] : ''), 'placeholder="' . lang('tnh_quantity_begin') . '" onkeyup="formatNumBerKeyUpCus(this)" id="quantity_begin" class="form-control input-tip"'); ?>
                                </div>
                            </div> -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_price_import', 'price_import') ?>
                                    <?php echo form_input('price_import', (isset($_POST['price_import']) ? $_POST['price_import'] : formatMoney($material['price_import'])), 'placeholder="' . lang('tnh_price_import') . '" onkeyup="formatNumBerKeyUpCus(this)" id="price_import" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_price_sell', 'price_sell') ?>
                                    <?php echo form_input('price_sell', (isset($_POST['price_sell']) ? $_POST['price_sell'] : formatMoney($material['price_sell'])), 'placeholder="' . lang('tnh_price_sell') . '" onkeyup="formatNumBerKeyUpCus(this)" id="price_sell" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_quantity_minimum', 'quantity_minimum') ?>
                                    <?php echo form_input('quantity_minimum', (isset($_POST['quantity_minimum']) ? $_POST['quantity_minimum'] : formatNumber($material['quantity_minimum'])), 'placeholder="' . lang('tnh_quantity_minimum') . '" onkeyup="formatNumBerKeyUpCus(this)" id="quantity_minimum" class="form-control input-tip"'); ?>
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
                                <?php if (!empty($material['images_multiple']) && $actions == 'edit') : ?>
                                    <div class="preview_image" id="avatar_view" style="width: auto;">
                                        <div class="display-block contract-attachment-wrapper img-1">
                                            <?php $images_multiple = explode('||', $material['images_multiple']); ?>
                                            <?php foreach ($images_multiple as $key => $value) : ?>
                                                <div class="col-md-2">
                                                    <input type="hidden" name="images_old[]" id="images_old[]" class="form-control" value="<?= $value ?>">
                                                    <button type="button" class="close remove-image" data-id="50" data-src="uploads/items/50/tru_ringlock.jpg" style="color:red;" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                    <a href="<?= pathMaterial($value) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                        <div class="">
                                                            <img style="max-width: 200px;max-height: 300px;" src="<?= pathMaterial($value) ?>">
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                <?php endif ?>
                                <input type="hidden" name="remove_image" id="remove_image" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_quality_standard', 'quality_standard') ?>
                                    <?php echo form_input('quality_standard', (isset($_POST['quality_standard']) ? $_POST['quality_standard'] : $material['quality_standard']), 'placeholder="' . lang('tnh_quality_standard') . '" id="quality_standard" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_mode_id', 'mode_id') ?>
                                    <?php //echo form_input('mode', (isset($_POST['mode']) ? $_POST['mode'] : $material['mode']), 'placeholder="' . lang('tnh_mode') . '" id="mode" class="form-control input-tip"'); 
                                    ?>
                                    <select name="mode_id" id="mode_id" data-live-search="true" data-none-selected-text="<?= lang('tnh_mode_id') ?>" class="form-control selectpicker">
                                        <option value=""></option>
                                        <?php if (!empty($mode_materials)) : ?>
                                            <?php foreach ($mode_materials as $key => $value) : ?>
                                                <option <?= $value['id'] == $material['mode_id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('tnh_unit_stock', 'unit') ?>
                                    <select name="unit" id="unit" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_unit_stock') ?>" required="required">
                                        <option value=""></option>
                                        <?php foreach ($units as $key => $value) : ?>
                                            <option <?= $value['unitid'] == $material['unit_id'] ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('tnh_exchange', 'exchange_unit') ?>
                                    <input type="text" name="exchange_unit" id="exchange_unit" class="form-control exchange_unit number-format" value="<?= $material['exchange_unit'] ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('tnh_standard_unit', 'standard_unit') ?>
                                    <select name="standard_unit" id="standard_unit" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_standard_unit') ?>">
                                        <option value=""></option>
                                        <?php foreach ($units as $key => $value) : ?>
                                            <option <?= $value['unitid'] == $material['standard_unit'] ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('tnh_exchange', 'exchange_standard_unit') ?>
                                    <input type="text" name="exchange_standard_unit" id="exchange_standard_unit" class="form-control exchange_standard_unit number-format" value="<?= $material['exchange_standard_unit'] ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('tnh_unit_payment', 'unit_payment') ?>
                                    <select name="unit_payment" id="unit_payment" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_unit_payment') ?>">
                                        <option value=""></option>
                                        <?php foreach ($units as $key => $value) : ?>
                                            <option <?= $value['unitid'] == $material['unit_payment'] ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('tnh_exchange', 'exchange_unit_payment') ?>
                                    <input type="text" name="exchange_unit_payment" id="exchange_unit_payment" class="form-control exchange_unit_payment number-format" value="<?= $material['exchange_unit_payment'] ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox checkbox-info">
                                    <input type="checkbox" name="is_single_use" <?= !empty($material['is_single_use']) ? 'checked' : '' ?> id="single_use" value="1">
                                    <label for="single_use"><?= lang('tnh_single_use') ?></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('Tồn Cho Phép', 'allowable') ?>
                                    <?php echo form_input('allowable', (isset($_POST['allowable']) ? $_POST['allowable'] : formatNumber($material['allowable'])), 'placeholder="'.lang('Tồn Cho Phép').'" id="allowable" class="form-control input-tip number-format"'); ?>
                                </div>
                            </div>
                            <?php $titleBan = ''; ?>
                            <?php if (!empty($isMaterial)) : ?>
                                <?php
                                $titleBan = '<div class="text-danger">Đã sử dụng trong BOM</div>';
                                ?>
                            <?php endif ?>
                            <div class="col-md-12">
                                <table class="tnh-tb table-exchange table table-bordered table-hover">
                                    <thead>
                                        <tr class="primary-table">
                                            <th colspan="4">
                                                <?= lang('Đơn vị sản xuất') ?>
                                                <?= $titleBan ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="width: 80px; text-align: center;">
                                                <div class="text-center">
                                                    <?= lang('tnh_numbers') ?>
                                                    <!-- <button type="button" class="btn btn-warning btn-icon btn-add-items"><i class="fa fa-plus"></i></button> -->
                                                </div>
                                            </th>
                                            <th><?= lang('unit') ?></th>
                                            <th style="width: 150px;"><?= lang('quantity') ?></th>
                                            <!-- <th style="width: 80px; text-align: center;"><i class="fa fa-trash-o"></i></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($exchanges)) : ?>
                                            <?php foreach ($exchanges as $key => $value) : ?>
                                                <tr>
                                                    <td class="stt text-center"><?= ++$key ?></td>
                                                    <td <?= !empty($titleBan) ? 'style="pointer-events: none;"' : '' ?>>
                                                        <select name="unit_exchange[]" data-live-search="true" id="unit_exchange" class="form-control unit_exchange">
                                                            <option value="0"></option>
                                                            <?php foreach ($units as $k => $val) : ?>
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
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('note', 'note') ?>
                                    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $material['note']), 'placeholder="' . lang('note') . '" id="note" class="form-control input-tip tinymce"'); ?>
                                </div>
                            </div>
                        </div>
                        <!-- col-4 3 -->
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_recipe', 'recipe') ?>
                                    <div class="flex-center">
                                        <div class="radio radio-info" style="margin: 0px; width: 100px;">
                                            <input type="radio" name="recipe" id="recipe_default" value="1" <?= $material['recipe'] == 1 ? 'checked' : '' ?>>
                                            <label for="recipe_default"><?= lang('tnh_default') ?></label>
                                        </div>
                                        <div class="radio radio-info" style="margin: 0px; width: 70px;">
                                            <input type="radio" name="recipe" id="recipe_paper" value="2" <?= $material['recipe'] == 2 ? 'checked' : '' ?>>
                                            <label for="recipe_paper"><?= lang('tnh_paper') ?></label>
                                        </div>
                                        <div class="radio radio-info" style="margin: 0px; width: 100px;">
                                            <input type="radio" name="recipe" id="recipe_long_wide" value="3" <?= $material['recipe'] == 3 ? 'checked' : '' ?>>
                                            <label for="recipe_long_wide"><?= lang('tnh_long_wide') ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('tnh_longs', 'longs') ?>
                                    <input type="text" placeholder="<?= lang('tnh_longs') ?>" name="longs" id="longs" class="form-control longs number-format" value="<?= $material['longs'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('tnh_wide', 'wide') ?>
                                    <input type="text" placeholder="<?= lang('tnh_wide') ?>" name="wide" id="wide" class="form-control wide number-format" value="<?= $material['wide'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('tnh_height', 'height') ?>
                                    <input type="text" placeholder="<?= lang('tnh_height') ?>" name="height" id="height" class="form-control height number-format" value="<?= $material['height'] ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_paper', 'paper') ?>
                                    <input type="text" name="paper" placeholder="<?= lang('tnh_paper') ?>" id="paper" class="form-control paper number-format" value="<?= $material['paper'] ?>" title="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_quantitative', 'quantitative') ?>
                                    <?php echo form_input('quantitative', (isset($_POST['quantitative']) ? $_POST['quantitative'] : $material['quantitative']), 'placeholder="' . lang('tnh_quantitative') . '" id="quantitative" class="form-control input-tip number-format"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_standard_cl', 'standard_cl') ?>
                                    <?php echo form_input('standard_cl', (isset($_POST['standard_cl']) ? $_POST['standard_cl'] : $material['standard_cl']), 'placeholder="' . lang('tnh_standard_cl') . '" id="standard_cl" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_time_payment', 'time_payment') ?>
                                    <?php echo form_input('time_payment', (isset($_POST['time_payment']) ? $_POST['time_payment'] : $material['time_payment']), 'placeholder="' . lang('tnh_time_payment') . '" id="time_payment" class="form-control input-tip number-format"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_pp_check', 'pp_check') ?>
                                    <?php echo form_input('pp_check', (isset($_POST['pp_check']) ? $_POST['pp_check'] : $material['pp_check']), 'placeholder="' . lang('tnh_pp_check') . '" id="pp_check" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_name_account', 'name_account') ?>
                                    <?php echo form_input('name_account', (isset($_POST['name_account']) ? $_POST['name_account'] : $material['name_account']), 'placeholder="' . lang('tnh_name_account') . '" id="name_account" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_time_stock', 'time_stock') ?>
                                    <?php echo form_input('time_stock', (isset($_POST['time_stock']) ? $_POST['time_stock'] : $material['time_stock']), 'placeholder="' . lang('tnh_time_stock') . '" id="time_stock" class="form-control input-tip number-format"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_unit_of_measure', 'unit_of_measure') ?>
                                    <select name="unit_of_measure" id="unit_of_measure" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_unit_of_measure') ?>">
                                        <option value=""></option>
                                        <?php foreach ($units as $key => $value) : ?>
                                            <option  <?= $material['unit_of_measure'] == $value['unitid'] ? 'selected' : '' ?> value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_packaging_standard', 'packaging_standard') ?>
                                    <?php echo form_input('packaging_standard', (isset($_POST['packaging_standard']) ? $_POST['packaging_standard'] : $material['packaging_standard']), 'placeholder="' . lang('tnh_packaging_standard') . '" id="packaging_standard" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?= lang('tnh_npl_standard', 'npl_standard') ?>
                                    <?php echo form_input('npl_standard', (isset($_POST['npl_standard']) ? $_POST['npl_standard'] : $material['npl_standard']), 'placeholder="' . lang('tnh_npl_standard') . '" id="npl_standard" class="form-control input-tip"'); ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($custom_fields)) : ?>
                            <div class="col-md-12">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><?= lang('tnh_custom_fields') ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <?= render_custom_fields('materials', $material['id']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab1">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tb-suppliers" class="tnh-table table-hover table-condensed table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;"><i class="fa fa-plus btn btn-primary add-supplies hide"></i></th>
                                            <th><?= lang('tnh_supplies') ?></th>
                                            <!-- <th><? //= lang('tnh_leadtime') 
                                                        ?></th> -->
                                            <!-- <th style="width: 70px;" class="text-center"><? //= lang('actions') 
                                                                                                ?></th> -->
                                        </tr>
                                    </thead>
                                    <tbody class="t-body">
                                        <?php
                                        $counter = 0;
                                        $counter_suppliers = 0;
                                        ?>
                                        <?php if (!empty($material_suppliers)) : ?>
                                            <?php foreach ($material_suppliers as $key => $value) : ?>
                                                <?php $leadtimes = $this->items_model->getMaterialSuppliersByMaterialAndSupplier($id, $value['supplier_id']) ?>
                                                <tr>
                                                    <td class="td-number text-center"><?= ++$counter_suppliers ?></td>
                                                    <td class="td-suppliers ">
                                                        <input type="hidden" name="counter[]" id="counter" class="form-control counter" value="<?= $counter ?>">
                                                        <input type="text" name="suppliers_arr[<?= $counter ?>]" data-placeholder="<?= lang('choose') ?>" id="suppliers_arr<?= $counter ?>" class="suppliers modal-select2" style="width: 100%;" value="<?= $value['supplier_id'] ?>">
                                                    </td>
                                                    <!-- <td class="td-leadtime">
                                                        <table class="tnh-table tb-subb">
                                                            <thead>
                                                                <th style="width: 50px;" class="text-center">
                                                                    <i onclick="addStageSub(this, <? //= $counter 
                                                                                                    ?>)" class="fa fa-plus btn btn-success add-sub-st"></i>
                                                                </th>
                                                                <th style="width: 150px;"><? //= lang('tnh_stage') 
                                                                                            ?></th>
                                                                <th style="width: 150px;"><? //= lang('tnh_sequence') 
                                                                                            ?></th>
                                                                <th><? //= lang('tnh_number_date') 
                                                                    ?></th>
                                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash" class="remove-sub"></i></th>
                                                            </thead>
                                                            <tbody>
                                                                <?php //if (!empty($leadtimes)) : 
                                                                ?>
                                                                    <?php //foreach ($leadtimes as $k => $val) : 
                                                                    ?>
                                                                        <tr>
                                                                            <td class="td-number-sub text-center"></td>
                                                                            <td class="td-stage-sub ">
                                                                                <select name="procedure[<? //= $counter 
                                                                                                        ?>][]" id="procedure" data-placeholder="<? //= lang('choose') 
                                                                                                                                                ?>" class="procedure modal-select2" style="width: 100%;">
                                                                                    <?php //foreach ($procedure_detail as $e => $v) : 
                                                                                    ?>
                                                                                        <option <? //= $v['id'] == $val['procedure_id'] ? 'selected' : '' 
                                                                                                ?> value="<? //= $v['id'] 
                                                                                                            ?>"><? //= $v['name'] 
                                                                                                                                                                        ?></option>
                                                                                    <?php //endforeach 
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="sequence[<? //= $counter 
                                                                                                                    ?>][]" id="sequence" class="form-control sequence" value="<? //= $val['sequence'] 
                                                                                                                                                                                ?>">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" name="number_date[<? //= $counter 
                                                                                                                        ?>][]" id="number_date" class="form-control number_date" value="<? //= $val['number_date'] 
                                                                                                                                                                                        ?>">
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <a class="fa fa-remove" onclick="removeStageSubb(this)"></a>
                                                                            </td>
                                                                        </tr>
                                                                    <?php //endforeach 
                                                                    ?>
                                                                <?php //endif 
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </td> -->
                                                    <!-- <td class="td-actions text-center"><i class="btn btn-danger fa fa-remove remove-suppliers" onclick="removeSuppliers(this)"></i></td> -->
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
                                        <?php if (!empty($material_warehouse)) : ?>
                                            <?php foreach ($material_warehouse as $k => $val) : ?>
                                                <tr>
                                                    <td class="td-number-ws text-center"></td>
                                                    <td>
                                                        <select onchange="changeWarehouse(this)" name="warehouses[]" id="warehouses" data-placeholder="<?= lang('choose') ?>" class="warehouses modal-select2" style="width: 100%;">
                                                            <?php foreach ($warehouses as $key => $value) : ?>
                                                                <option <?= $val['warehouse_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <?php $options = '<option></option>' . recursiveLocationWarehouses($val['warehouse_id']);
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
    var units = <?= !empty($units) ? json_encode($units) : '{}' ?>;
    var procedure_detail = <?= !empty($procedure_detail) ? json_encode($procedure_detail) : '{}' ?>;
    var warehouses = <?= !empty($warehouses) ? json_encode($warehouses) : '{}' ?>;
    var edit = 1;
    var counter_suppliers = <?= $counter_suppliers ?>;;
</script>
<script type="text/javascript" src="<?= js('items.js?vs=1.8') ?>"></script>
<script>
    $(function() {
        // selectAjax('#category', false, 'admin/items/searchCategory');
        $('#id_branch').select2({
            'allowClear': true
        });
        $('#species').select2({
            'allowClear': true
        });
        ajaxSelectParamsCallback('#suppliers', 'admin/orders/searchSuppliers', $('#suppliers').val(), {
            type: 0
        }, true);
        $('#category').select2({
            'allowClear': true
        });

        $(document).ready(function() {
            $('#category').val(<?= $material['category_id'] ?>).trigger('change');
        });

        $('.unit_exchange').selectpicker();

        $('.remove-image').click(function(event) {
            $(this).closest('.col-md-2').remove();
            $('#remove_image').val(1);
        });

        for (var iS = counter_suppliers; iS < 3; iS++) {
            $('.add-supplies').click();
        }

        appValidateForm($('#edit-item'), {
            category: 'required',
            unit: 'required',
            code: 'required',
            name: 'required',
            standard_unit: 'required',
            exchange_unit: 'required',
            exchange_standard_unit: 'required',
            unit_payment: 'required',
            exchange_unit_payment: 'required',
            id_branch: 'required',
        }, edititem);

        function edititem(form) {
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
            //
            var url = form.action;
            $.ajax({
                    url: site.base_url + 'admin/items/edit_item/<?= $material['id'] ?>' + '/<?= $actions ?>',
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
                            oTable.draw('page');
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.edit').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    console.log("error");
                });
            return false;
        }
        init_editor('textarea[name="note"]');
        init_selectpicker();
    })
</script>