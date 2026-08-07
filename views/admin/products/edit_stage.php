<?php echo form_open('admin/advisory_lead/detail',array('id'=>'edit-stage')); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $actions == 'copy' ? _l('tnh_copy_stage') : _l('tnh_edit_stage'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_category_stages', 'category_stages') ?>
                        <select name="category_stages" id="category_stages" class="form-control selectpicker" data-language="vi_VN" data-live-search="true" data-none-selected-text="<?= lang('tnh_category_stages') ?>">
                            <option value=""></option>
                            <?php if(!empty($categoryStages)) : ?>
                                <?php foreach ($categoryStages as $key => $value): ?>
                                    <option <?= $stage['category_stages'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_stage_code', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : $stage['code']), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_stage_name', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : $stage['name']), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <?= ''//lang('tnh_name_category_stage', 'name_category_stage') ?>
                        <?php //echo form_input('name_category_stage', (isset($_POST['name_category_stage']) ? $_POST['name_category_stage'] : $stage['name_category_stage']), 'placeholder="' . lang('tnh_name_category_stage') . '" id="name_category_stage" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= ''//lang('tnh_code_category_stage', 'code_category_stage') ?>
                        <?php //echo form_input('code_category_stage', (isset($_POST['code_category_stage']) ? $_POST['code_category_stage'] : $stage['code_category_stage']), 'placeholder="' . lang('tnh_code_category_stage') . '" id="code_category_stage" class="form-control input-tip"'); ?>
                    </div>
                </div> -->
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_stage_price_gauge', 'stage_price_gauge') ?>
                        <?php echo form_input('stage_price_gauge', (isset($_POST['stage_price_gauge']) ? $_POST['stage_price_gauge'] : formatMoney($stage['stage_price_gauge'])), 'placeholder="' . lang('tnh_stage_price_gauge') . '" id="stage_price_gauge" class="form-control input-tip money-format"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_operating_gauge', 'operating_gauge') ?>
                        <?php echo form_input('operating_gauge', (isset($_POST['operating_gauge']) ? $_POST['operating_gauge'] : $stage['operating_gauge']), 'placeholder="' . lang('tnh_operating_gauge') . '" id="operating_gauge" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_completion_standard', 'completion_standard') ?>
                        <?php echo form_input('completion_standard', (isset($_POST['completion_standard']) ? $_POST['completion_standard'] : $stage['completion_standard']), 'placeholder="' . lang('tnh_completion_standard') . '" id="completion_standard" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_control_criteria', 'control_criteria') ?>
                        <?php echo form_input('control_criteria', (isset($_POST['control_criteria']) ? $_POST['control_criteria'] : $stage['control_criteria']), 'placeholder="' . lang('tnh_control_criteria') . '" id="control_criteria" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_quota_productivity_h', 'quota_productivity_h') ?>
                        <?php echo form_input('quota_productivity_h', (isset($_POST['quota_productivity_h']) ? $_POST['quota_productivity_h'] : $stage['quota_productivity_h']), 'placeholder="' . lang('tnh_quota_productivity_h') . '" id="quota_productivity_h" class="form-control input-tip number-format"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_quota_power_consumption_h', 'quota_power_consumption_h') ?>
                        <?php echo form_input('quota_power_consumption_h', (isset($_POST['quota_power_consumption_h']) ? $_POST['quota_power_consumption_h'] : $stage['quota_power_consumption_h']), 'placeholder="' . lang('tnh_quota_power_consumption_h') . '" id="quota_power_consumption_h" class="form-control input-tip number-format"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_quota_material_replace_t', 'quota_material_replace_t') ?>
                        <?php echo form_input('quota_material_replace_t', (isset($_POST['quota_material_replace_t']) ? $_POST['quota_material_replace_t'] : $stage['quota_material_replace_t']), 'placeholder="' . lang('tnh_quota_material_replace_t') . '" id="quota_material_replace_t" class="form-control input-tip number-format"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_quota_depreciation_ts_date', 'quota_depreciation_ts_date') ?>
                        <?php echo form_input('quota_depreciation_ts_date', (isset($_POST['quota_depreciation_ts_date']) ? $_POST['quota_depreciation_ts_date'] : $stage['quota_depreciation_ts_date']), 'placeholder="' . lang('tnh_quota_depreciation_ts_date') . '" id="quota_depreciation_ts_date" class="form-control input-tip number-format"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_quota_npl_consumption_one', 'quota_npl_consumption_one') ?>
                        <?php echo form_input('quota_npl_consumption_one', (isset($_POST['quota_npl_consumption_one']) ? $_POST['quota_npl_consumption_one'] : $stage['quota_npl_consumption_one']), 'placeholder="' . lang('tnh_quota_npl_consumption_one') . '" id="quota_npl_consumption_one" class="form-control input-tip number-format"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="formula_m2" id="formula_m2" <?= $stage['formula_m2'] == 1 ? 'checked' : '' ?> value="1">
                            <label for="formula_m2"><?= lang('Công thức m2') ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="checkbox checkbox-info">
                            <input <?= (!empty($stage['status_qc']) ? 'checked' : '') ?> type="checkbox"
                                name="status_qc" id="status_qc" value="1">
                            <label for="status_qc"><?= lang('tnh_status_qc') ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="checkbox checkbox-info">
                            <input <?= (!empty($stage['status_default_outsource']) ? 'checked' : '') ?> type="checkbox"
                                name="status_default_outsource" class="status_default_outsource"
                                id="status_default_outsource" value="1">
                            <label for="status_default_outsource"><?= lang('Mặc định gia công') ?></label>
                        </div>
                        <div class="error_outsource text-danger"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="checkbox checkbox-info">
                            <input <?= (!empty($stage['stage_import_outsource']) ? 'checked' : '') ?> type="checkbox"
                                name="stage_import_outsource" class="stage_import_outsource" id="stage_import_outsource"
                                value="1">
                            <label for="stage_import_outsource"><?= lang('Mặc định nhập gia công') ?></label>
                        </div>
                        <div class="error_import text-danger"></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_output', 'output') ?>
                        <div class="flex pannel-custom">
                            <div class="checkbox checkbox-info mright10">
                                <input <?= ($stage['type'] == 1 ? 'checked' : '') ?> type="checkbox" name="type"
                                    id="semi_finished_product" value="1">
                                <label for="semi_finished_product"><?= lang('tnh_semi_finished_product') ?></label>
                            </div>
                            <div class="checkbox checkbox-info" style="margin-top: 10px;">
                                <input <?= ($stage['type'] == 2 ? 'checked' : '') ?> type="checkbox" name="type"
                                    id="unfinished_product" value="2">
                                <label for="unfinished_product"><?= lang('tnh_unfinished_product') ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?php $stages = get_table_where('tbl_stages',[],'','result_array'); ?>
                        <?= lang('Công đoạn chọn lại', 'stage_again') ?>
                        <select name="stage_again" id="stage_again" class="modal-select2" data-language="vi_VN"
                            data-placeholder="<?= lang('Công đoạn chọn lại') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if(!empty($stages)){ ?>
                            <?php foreach ($stages as $key => $value){ ?>
                            <option <?= $value['id'] == $stage['stage_again'] ? 'selected' : '' ?>
                                value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 mbot10">
                    <table id="tb-criteria" class="table table-hover table-cs dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">
                                    <a class="hover-svg dropdown-toggle add-row" onclick="addCriteria()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                            <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                        </svg>
                                    </a>
                                </th>
                                <th><?= lang('tnh_withdraw_check') ?><span class="text-danger">*</span></th>
                                <th><?= lang('tnh_test_standards') ?><span class="text-danger">*</span></th>
                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($stage_criteria): ?>
                                <?php foreach($stage_criteria as $key => $value): ?>
                                    <?php
                                        $tdNumbers = '<td class="text-center td-numbers">'.(++$key).'</td>';
                                        $tdWithdrawCheck = '<td>
                                            <input type="hidden" name="stage_criteria_id[]" class="form-control stage_criteria_id" value="'.$value['id'].'">
                                            <input type="text" name="withdraw_check[]" class="form-control withdraw_check" value="'.$value['withdraw_check'].'" placeholder="'.lang('tnh_withdraw_check').'">
                                        </td>';
                                
                                        $tdTestStandards = '<td>
                                            <input type="text" name="test_standards[]" class="form-control test_standards" value="'.$value['test_standards'].'" placeholder="'.lang('tnh_test_standards').'">
                                        </td>';

                                        $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeCriteria(this)"></i></td>';
                                
                                        $trCriteria = '<tr>
                                            '.$tdNumbers.'
                                            '.$tdWithdrawCheck.'
                                            '.$tdTestStandards.'
                                            '.$tdActions.'
                                        </tr>';   
                                        echo $trCriteria;
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 mbot10">
                    <?php $group_customer = get_table_where('tblcustomers_groups'); ?>
                    <div class="form-group">
                        <?= lang('Nhóm khách hàng', 'group_customer') ?>
                        <select name="group_customer" id="group_customer" data-placeholder="<?= lang('Nhóm khách hàng') ?>" class="modal-select2 group_customer" style="width: 100%;">
                            <option value=""></option>
                            <?php if(!empty($group_customer)): ?>
                                <?php foreach($group_customer as $key => $value): ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <table id="tb-group-customer" class="table table-hover table-group-customer dataTable">
                        <thead>
                            <tr>
                                <th style="width: 150px;"><?= lang('Nhóm khách hàng') ?><span class="text-danger">*</span></th>
                                <th style="width: 150px;"><?= lang('tnh_prices') ?><span class="text-danger">*</span></th>
                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $stagesCustomerPrices = $this->products_model->getStagesCustomerPrices($stage['id']);
                            ?>
                            <?php if(!empty($stagesCustomerPrices)): ?>
                                <?php foreach($stagesCustomerPrices as $key => $value): ?>
                                    <?php
                                        $tdGroupCustomer = '<td class="text-center">
                                            <input type="hidden" name="stages_customer_prices[]" class="form-control" value="'.$value['id'].'">
                                            <input type="hidden" name="customers_groups_id[]" class="form-control customers_groups_id" value="'.$value['customers_groups_id'].'">
                                            '.$value['customers_groups_name'].'
                                        </td>';

                                        $tdPriceGroupCustomer = '<td>
                                            <input type="text" name="price_group_customer[]" class="form-control price_group_customer number-format" value="'.formatMoney($value['price_group_customer']).'" placeholder="">
                                        </td>';

                                        $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeGroupCustomer(this)"></i></td>';

                                        echo '<tr>
                                            '.$tdGroupCustomer.'
                                            '.$tdPriceGroupCustomer.'
                                            '.$tdActions.'
                                        </tr>';
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $stage['note']), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="id" class="id" value="<?= $stage['id'] ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
$(function() {
    $("#stage_again").select2();
    $('#group_customer').select2();
    appValidateForm($('#edit-stage'), {
        category_stages: 'required',
        code: 'required',
        name: 'required'
    }, addstage);

    function addstage(form) {
        $('.add').attr('disabled', 'disabled');
        tinymce.get('note').save();
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
                url: site.base_url + 'admin/products/edit_stage/<?= $stage['id'] ?>/<?= $actions ?>',
                type: 'POST', 
                dataType: 'JSON',
                data: data,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable != 'undefined') {
                        oTable.draw('page');
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