<?php echo form_open_multipart('admin/categories_other/handlingMaterialsEquipment/'.$id, array('id' => 'handling-materials-equipment')); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Mã vị trí', 'role') ?>
                        <select name="role" id="role" data-none-selected-text="<?= lang('Mã vị trí') ?>" data-live-search="true" class="form-control role selectpicker" required="required">
                            <option value=""></option>
                            <?php if(!empty($roles)): ?>
                                <?php foreach($roles as $key => $value): ?>
                                    <option <?= !empty($materials_equipment) && $materials_equipment['role_id'] == $value['roleid'] ? 'selected' : '' ?> data-subtext="<?= $value['code'] ?>" value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Vật tư', 'supplies') ?>
                        <input type="text" name="supplies" id="supplies" placeholder="<?= lang('Vật tư') ?>" class="form-control supplies" value="<?= !empty($materials_equipment) ? $materials_equipment['supplies'] : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Số lượng', 'quantity') ?>
                        <input type="text" name="quantity" id="quantity" placeholder="<?= lang('Số lượng') ?>" class="form-control quantity number-format" value="<?= !empty($materials_equipment) ? $materials_equipment['quantity'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Chất lượng', 'quality') ?>
                        <input type="text" name="quality" id="quality" placeholder="<?= lang('Chất lượng') ?>" class="form-control quality" value="<?= !empty($materials_equipment) ? $materials_equipment['quality'] : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Thiết bị', 'machine') ?>
                        <input type="text" name="machine" id="machine" placeholder="<?= lang('Thiết bị') ?>" class="form-control machine" value="<?= !empty($materials_equipment) ? $materials_equipment['machine'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-6 hide">
                    <div class="form-group">
                        <?= lang('Số thứ tự mã TT', 'number') ?>
                        <input type="text" name="number" id="number" placeholder="<?= lang('Số thứ tự mã TT') ?>" class="form-control" value="<?= !empty($materials_equipment) ? $materials_equipment['number'] : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Số lượng', 'quantity_1') ?>
                        <input type="text" name="quantity_1" id="quantity_1" placeholder="<?= lang('Số lượng') ?>" class="form-control number-format quantity_1" value="<?= !empty($materials_equipment) ? $materials_equipment['quantity_1'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Chất lượng', 'quality_1') ?>
                        <input type="text" name="quality_1" id="quality_1" placeholder="<?= lang('Chất lượng') ?>" class="form-control quality_1" value="<?= !empty($materials_equipment) ? $materials_equipment['quality_1'] : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Chi tiết cấu hình máy', 'detail_machine') ?>
                        <textarea name="detail_machine" id="detail_machine" placeholder="<?= lang('Chi tiết cấu hình máy') ?>" class="form-control detail_machine" rows="6"><?= !empty($materials_equipment) ? $materials_equipment['detail_machine'] : '' ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Tên phần mềm cài đặt', 'software') ?>
                        <textarea name="software" id="software" class="form-control software" placeholder="<?= lang('Tên phần mềm cài đặt') ?>" rows="6"><?= !empty($materials_equipment) ? $materials_equipment['software'] : '' ?></textarea>
                    </div>
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
<script>
    $(function() {
        init_selectpicker();
        appValidateForm($('#handling-materials-equipment'), {
            role: 'required',
        }, handlingData);

        function handlingData(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

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