<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <?php echo form_open(admin_url('entrance_ticket/location_detail/' . $id), ['id' => 'location-form']); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= empty($id) ? lang('Thêm vị trí mới') : lang('Sửa vị trí') ?></h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="name"><span class="text-danger">*</span><?= lang('Tên vị trí (VD: QA, BV...)') ?></label>
                <input type="text" name="name" id="name" class="form-control" readonly value="<?= !empty($location) ? $location['name'] : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="role_ids"><span class="text-danger">*</span><?= lang('Chọn các vai trò (Roles)') ?></label>
                <select name="role_ids[]" id="role_ids" class="form-control selectpicker" multiple data-actions-box="true" data-live-search="true" required>
                    <?php
                    $selected_roles = !empty($location['role_ids']) ? $location['role_ids'] : [];
                    foreach ($roles as $role): ?>
                        <option value="<?= $role['roleid'] ?>" <?= in_array($role['roleid'], $selected_roles) ? 'selected' : '' ?>>
                            <?= $role['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" class="btn btn-primary"><?= lang('submit') ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
    init_selectpicker();
</script>
