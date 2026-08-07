<?php echo form_open('admin/room/update_room/'.$id.'', array('id' => 'detail-organization')); ?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title; ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã phòng ban', 'code') ?>
                            <?php echo form_input('code', ($dtData['code'] ?? ''), 'placeholder="' . lang('code') . '" id="code" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tên phòng ban', 'name') ?>
                            <?php echo form_input('name', ($dtData['name'] ?? ''), 'placeholder="' . lang('name') . '" id="name" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Khối', 'block_id') ?>
                            <select name="block_id" id="block_id" data-placeholder="<?= lang('Khối') ?>" class="modal-select2" style="width: 100%;">
                                <option value=""></option>
                                <?php foreach ($dtBlock as $key => $value){ ?>
                                    <option <?= !empty($dtData) && $dtData['block_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Email', 'email') ?>
                            <?php echo form_input('email', ($dtData['email'] ?? ''), 'placeholder="' . lang('Email') . '" id="email" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngân sách', 'budget') ?>
                            <?php echo form_input('budget', ( !empty($dtData['budget']) ? formatMoney($dtData['budget']) : 0), 'placeholder="' . lang('budget') . '" id="budget" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="room_goals"><?= lang('Mục tiêu') ?></label>
                            <textarea class="form-control room_goals" name="room_goals" cols="3" rows="3"><?= $dtData['room_goals'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="effective_from"><?= lang('Thời gian hiệu lực từ') ?></label>
                            <input class="form-control effective_from datepicker" name="effective_from" value="<?= !empty($dtData['effective_from']) ? _dhau($dtData['effective_from']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="effective_to"><?= lang('Thời gian hiệu lực đến') ?></label>
                            <input class="form-control effective_to datepicker" name="effective_to" value="<?= !empty($dtData['effective_to']) ? _dhau($dtData['effective_to']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="policy_link"><?= lang('Link quy định') ?></label>
                            <input class="form-control policy_link" name="policy_link" value="<?= !empty($dtData['policy_link']) ? ($dtData['policy_link']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="workspace_link"><?= lang('Link làm việc') ?></label>
                            <input class="form-control workspace_link" name="workspace_link" value="<?= !empty($dtData['workspace_link']) ? ($dtData['workspace_link']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="note"><?= lang('Ghi chú') ?></label>
                            <textarea class="form-control note" name="note" cols="3" rows="3"><?= $dtData['note'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') : _l('edit') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function() {
        init_selectpicker();
        init_datepicker();
        $('#parent_id').select2({
            allowClear: true
        });
        $('#block_id').select2({
            allowClear: true
        });
        $('#object_id').select2({
            allowClear: true
        });
        appValidateForm($('#detail-organization'), {
            code: 'required',
            name: 'required'
        }, addCategory);

        function addCategory(form) {
            $('.add').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/room/update_room/<?= $id ?>',
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        $('.modal-dialog .close').trigger('click');
                        tAPI.draw();
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