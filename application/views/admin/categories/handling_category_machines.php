<?php echo form_open('admin/categories/handlingCategoryMachines/'.$id.'/'.$actions, array('id'=>'handling-category-machines')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $actions == "add" ? _l('dt_add_category_machines') : _l('dt_edit_category_machines'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('code', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtCategoryMachines) ? $dtCategoryMachines['code'] : '')), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('name', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtCategoryMachines) ? $dtCategoryMachines['name'] : '')), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Mã chủng loại', 'code_species') ?>
                        <?php echo form_input('code_species', (isset($_POST['code_species']) ? $_POST['code_species'] : (!empty($dtCategoryMachines) ? $dtCategoryMachines['code_species'] : '')), 'placeholder="'.lang('Mã chủng loạ').'" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tên chủng loại', 'name_species') ?>
                        <?php echo form_input('name_species', (isset($_POST['name_species']) ? $_POST['name_species'] : (!empty($dtCategoryMachines) ? $dtCategoryMachines['name_species'] : '')), 'placeholder="'.lang('Tên chủng loại').'" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Phân loại thiết bị', 'is_type') ?>
                        <select name="is_type"
                                id="is_type"
                                class="c_select2"
                                data-placeholder="<?= lang('Chi nhánh') ?>"
                                style="width: 100%;">
                            <option value=""></option>
                            <?php if(!empty($listTypeCategoryMachines)) {?>
                                <?php $dataVal = (isset($_POST['is_type']) ? $_POST['is_type'] : (!empty($dtCategoryMachines) ? $dtCategoryMachines['is_type'] : ''))?>
                                <?php foreach($listTypeCategoryMachines as $key => $value) {?>
                                    <option value="<?=$key?>" <?=($dataVal == $key ? 'selected' : '')?>><?=$value?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : (!empty($dtCategoryMachines) ? $dtCategoryMachines['note'] : '')), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= $actions == "add" ? _l('add') : _l('edit'); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        $('.c_select2').select2();
        appValidateForm($('#handling-category-machines'), {
            code: 'required',
            name: 'required',
            is_type: 'required',
        }, handlingCategoryMachines);

        function handlingCategoryMachines(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            tinymce.get('note').save();
            var data = $(form).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    console.log("error");
                });
            return false;
        }
        init_editor('textarea[name="note"]');
    })
</script>