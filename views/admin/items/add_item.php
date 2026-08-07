<?php echo form_open('admin/items/add_item',array('id'=>'add-item')); ?>
<div class="modal-dialog modal-lg" style="width: 80%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_add_item'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div role="tabpanel">
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
                                        <select name="id_branch" id="id_branch" class="id_branch" required="required" style="width: 100%;"  data-placeholder="<?= lang('tnh_branch') ?>">
                                            <option value=""></option>
                                            <?php if(!empty($branch)): ?>
                                                <?php foreach($branch as $key => $value): ?>
                                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
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
                                                    <option data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_suppliers', 'suppliers') ?>
                                        <input type="text" name="suppliers" id="suppliers" data-placeholder="<?= lang('tnh_suppliers') ?>" class="suppliers modal-select2" value="" style="width: 100%;">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_material_code', 'code') ?>
                                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : ''), 'placeholder="'.lang('tnh_material_code').'" id="code" required class="form-control input-tip"'); ?>
                                        <div class="checkbox checkbox-info">
                                            <input type="checkbox" name="hand_input_code" id="hand_input_code" value="1">
                                            <label for="hand_input_code"><?= lang('tnh_hand_input') ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_material_name', 'name') ?>
                                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ''), 'placeholder="'.lang('tnh_material_name').'" id="name" required class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_material_name_customer', 'name_customer') ?>
                                        <?php echo form_input('name_customer', (isset($_POST['name_customer']) ? $_POST['name_customer'] : ''), 'placeholder="'.lang('tnh_material_name_customer').'" id="name_customer" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_material_code_supplier', 'material_code_supplier') ?>
                                        <?php echo form_input('material_code_supplier', (isset($_POST['material_code_supplier']) ? $_POST['material_code_supplier'] : ''), 'placeholder="'.lang('tnh_material_code_supplier').'" id="material_code_supplier" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_material_name_supplier', 'name_supplier') ?>
                                        <?php echo form_input('name_supplier', (isset($_POST['name_supplier']) ? $_POST['name_supplier'] : ''), 'placeholder="'.lang('tnh_material_name_supplier').'" id="name_supplier" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_price_import', 'price_import') ?>
                                        <?php echo form_input('price_import', (isset($_POST['price_import']) ? $_POST['price_import'] : ''), 'placeholder="'.lang('tnh_price_import').'" onkeyup="formatNumBerKeyUpCus(this)" id="price_import" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_price_sell', 'price_sell') ?>
                                        <?php echo form_input('price_sell', (isset($_POST['price_sell']) ? $_POST['price_sell'] : ''), 'placeholder="'.lang('tnh_price_sell').'" onkeyup="formatNumBerKeyUpCus(this)" id="price_sell" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantity_minimum', 'quantity_minimum') ?>
                                        <?php echo form_input('quantity_minimum', (isset($_POST['quantity_minimum']) ? $_POST['quantity_minimum'] : ''), 'placeholder="'.lang('tnh_quantity_minimum').'" onkeyup="formatNumBerKeyUpCus(this)" id="quantity_minimum" class="form-control input-tip"'); ?>
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
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quality_standard', 'quality_standard') ?>
                                        <?php echo form_input('quality_standard', (isset($_POST['quality_standard']) ? $_POST['quality_standard'] : ''), 'placeholder="'.lang('tnh_quality_standard').'" id="quality_standard" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_mode_id', 'mode') ?>
                                        <?php //echo form_input('mode', (isset($_POST['mode']) ? $_POST['mode'] : ''), 'placeholder="'.lang('tnh_mode').'" id="mode" class="form-control input-tip"'); ?>
                                        <select name="mode_id" id="mode_id" data-live-search="true" data-none-selected-text="<?= lang('tnh_mode_id') ?>" class="form-control selectpicker">
                                            <option value=""></option>
                                            <?php if(!empty($mode_materials)): ?>
                                                <?php foreach($mode_materials as $key => $value): ?>
                                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
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
                                            <?php foreach ($units as $key => $value): ?>
                                                <option value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('tnh_exchange', 'exchange_unit') ?>
                                        <input type="text" name="exchange_unit" id="exchange_unit" class="form-control exchange_unit number-format" required value="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('tnh_standard_unit', 'standard_unit') ?>
                                        <select name="standard_unit" id="standard_unit" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" required data-none-selected-text="<?= lang('tnh_standard_unit') ?>">
                                            <option value=""></option>
                                            <?php foreach ($units as $key => $value): ?>
                                                <option value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('tnh_exchange', 'exchange_standard_unit') ?>
                                        <input type="text" name="exchange_standard_unit" id="exchange_standard_unit" class="form-control exchange_standard_unit number-format" required value="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('tnh_unit_payment', 'unit_payment') ?>
                                        <select name="unit_payment" id="unit_payment" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" required data-none-selected-text="<?= lang('tnh_unit_payment') ?>">
                                            <option value=""></option>
                                            <?php foreach ($units as $key => $value): ?>
                                                <option value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('tnh_exchange', 'exchange_unit_payment') ?>
                                        <input type="text" name="exchange_unit_payment" id="exchange_unit_payment" class="form-control exchange_unit_payment number-format" required value="1">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="is_single_use" id="single_use" value="1">
                                        <label for="single_use"><?= lang('tnh_single_use') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="tnh-tb table-exchange table table-bordered table-hover">
                                        <thead>
                                            <tr class="primary-table">
                                                <th colspan="4"><?= lang('Đơn vị sản xuất') ?></th>
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
                                            <tr>
                                                <td class="stt text-center">1</td>
                                                <td>
                                                    <select name="unit_exchange[]" data-none-selected-text="<?= lang('unit') ?>"  data-live-search="true" id="unit_exchange" class="form-control unit_exchange selectpicker">
                                                        <option value=""></option>
                                                        <?php foreach ($units as $key => $value): ?>
                                                            <option value="<?= $value['unitid'] ?>"><?= $value['unit'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="number_exchange[]" id="number_exchange[]" class="form-control" value="1">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('note', 'note') ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
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
                                                <input type="radio" name="recipe" id="recipe_default" value="1" checked>
                                                <label for="recipe_default"><?= lang('tnh_default') ?></label>
                                            </div>
                                            <div class="radio radio-info" style="margin: 0px; width: 70px;">
                                                <input type="radio" name="recipe" id="recipe_paper" value="2">
                                                <label for="recipe_paper"><?= lang('tnh_paper') ?></label>
                                            </div>
                                            <div class="radio radio-info" style="margin: 0px; width: 100px;">
                                                <input type="radio" name="recipe" id="recipe_long_wide" value="3">
                                                <label for="recipe_long_wide"><?= lang('tnh_long_wide') ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('tnh_longs', 'longs') ?>
                                        <input type="text" placeholder="<?= lang('tnh_longs') ?>" name="longs" id="longs" class="form-control longs number-format" value="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('tnh_wide', 'wide') ?>
                                        <input type="text" placeholder="<?= lang('tnh_wide') ?>" name="wide" id="wide" class="form-control wide number-format" value="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('tnh_height', 'height') ?>
                                        <input type="text" placeholder="<?= lang('tnh_height') ?>" name="height" id="height" class="form-control height number-format" value="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_paper', 'paper') ?>
                                        <input type="text" name="paper" placeholder="<?= lang('tnh_paper') ?>" id="paper" class="form-control paper number-format" value="" title="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_quantitative', 'quantitative') ?>
                                        <?php echo form_input('quantitative', (isset($_POST['quantitative']) ? $_POST['quantitative'] : ''), 'placeholder="'.lang('tnh_quantitative').'" id="quantitative" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_standard_cl', 'standard_cl') ?>
                                        <?php echo form_input('standard_cl', (isset($_POST['standard_cl']) ? $_POST['standard_cl'] : ''), 'placeholder="'.lang('tnh_standard_cl').'" id="standard_cl" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_time_payment', 'time_payment') ?>
                                        <?php echo form_input('time_payment', (isset($_POST['time_payment']) ? $_POST['time_payment'] : ''), 'placeholder="'.lang('tnh_time_payment').'" id="time_payment" class="form-control input-tip number-format"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_pp_check', 'pp_check') ?>
                                        <?php echo form_input('pp_check', (isset($_POST['pp_check']) ? $_POST['pp_check'] : ''), 'placeholder="'.lang('tnh_pp_check').'" id="pp_check" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_name_account', 'name_account') ?>
                                        <?php echo form_input('name_account', (isset($_POST['name_account']) ? $_POST['name_account'] : ''), 'placeholder="'.lang('tnh_name_account').'" id="name_account" class="form-control input-tip"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang('tnh_time_stock', 'time_stock') ?>
                                        <?php echo form_input('time_stock', (isset($_POST['time_stock']) ? $_POST['time_stock'] : ''), 'placeholder="'.lang('tnh_time_stock').'" id="time_stock" class="form-control input-tip number-format"'); ?>
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
                                            <?= render_custom_fields('materials') ?>
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
                                                <!-- style="width: 200px;" <th><?//= lang('tnh_leadtime') ?></th> -->
                                                <!-- <th style="width: 70px;" class="text-center"><?//= lang('actions') ?></th> -->
                                            </tr>
                                        </thead>
                                        <tbody class="t-body">
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
			<button type="submit" class="btn btn-primary add"><?= _l('add') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    var counter = 0;
    var units = <?= !empty($units) ? json_encode($units) : '{}' ?>;
    var procedure_detail = <?= !empty($procedure_detail) ? json_encode($procedure_detail) : '{}' ?>;
    var warehouses = <?= !empty($warehouses) ? json_encode($warehouses) : '{}' ?>;
    var edit = 0;
    var counter_suppliers = 0;
</script>
<script type="text/javascript" src="<?= js('items.js?vs=1.8') ?>"></script>
<script>
    $(function(){
        // selectAjax('#category', false, 'admin/items/searchCategory');
        $("#id_branch").select2();
        $('#category').select2({
            'allowClear': true,
            // escapeMarkup: function(m) {
            //     return $.trim(m);
            // },
        });
        ajaxSelectParamsCallback('#suppliers', 'admin/orders/searchSuppliers', $('#suppliers').val(), {type: 0}, true);
        $('#species').select2({'allowClear': true});

        for (var iS = 0; iS < 3; iS++) {
            $('.add-supplies').click();
        }

       	appValidateForm($('#add-item'), {
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
        }, additem);

        function additem(form) {
        	$('.add').attr('disabled', 'disabled');
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
            	url : site.base_url+'admin/items/add_item',
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
            			oTable.draw();
            		}
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
        init_editor('textarea[name="note"]');
        init_selectpicker();
    })
</script>