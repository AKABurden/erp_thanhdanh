<?php echo form_open('admin/organization/detail/'.$id.'', array('id' => 'detail-organization')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title; ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Mã tổ chức', 'code') ?>
                        <?php echo form_input('code', ($organization['code'] ?? ''), 'placeholder="' . lang('code') . '" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tên tổ chức', 'name') ?>
                        <?php echo form_input('name', ($organization['name'] ?? ''), 'placeholder="' . lang('name') . '" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Loại', 'type') ?>
                        <select name="type" id="type" onchange="changeType(this)" data-placeholder="<?= lang('Loại') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach (getTypeOrganization() as $key => $value){ ?>
                                <option <?= $organization['object_type'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Danh sách liên quan', 'object_id') ?>
                        <select name="object_id" id="object_id" class="modal-select2" data-placeholder="<?= lang('Danh sách liên quan') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($dtObject)){ ?>
                                <?php foreach ($dtObject as $key => $value){ ?>
                                    <option <?= $organization['object_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 hide">
                    <div class="form-group">
                        <?= lang('tnh_group_parent', 'parent_id') ?>
                        <select name="parent_id" id="parent_id" data-placeholder="<?= lang('tnh_group_parent') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?= recursiveOrganization() ?>
                        </select>
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
<script>
    $(function() {
        $('#parent_id').val(<?= $parent_id ?>).trigger('change');
        init_selectpicker();
        $('#parent_id').select2({
            allowClear: true
        });
        $('#type').select2({
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
                url: site.base_url + 'admin/organization/detail/<?= $id ?>',
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        loadData();
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

    function changeType(_this){
        object_type = $(_this).val();
        $.ajax({
            type: 'POST',
            url: admin_url+'organization/getObjectByType',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                'object_type': object_type
            },
            dataType: "JSON",
            success: function (response) {
                let html = '<option value=""></option>';
                response.data.forEach(function(item){
                    html += `<option value="${item.id}">${item.name}</option>`;
                });
                $('#object_id').html(html);
                $('#object_id').val('').trigger('change');
            }
        });
    }
</script>