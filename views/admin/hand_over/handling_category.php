<?php echo form_open_multipart('admin/hand_over/handling_category/'.$id, array('id' => 'add-hand_over', 'class' => '', 'enctype' => 'multipart/form-data',)); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_code_category_hand_over', 'code_category_hand_over') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($category_hand_over) ? $category_hand_over['code'] : '')), 'placeholder="' . lang('tnh_code_category_hand_over') . '" id="code_category_hand_over" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_name_category_hand_over', 'name_category_hand_over') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($category_hand_over) ? $category_hand_over['name'] : '')), 'placeholder="' . lang('tnh_code_category_hand_over') . '" id="code_category_hand_over" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12 hide">
                    <div class="form-group">
                        <?= lang('tnh_module_category_hand_over', 'module_category_hand_over') ?>
                        <select name="type" id="module_category_hand_over" data-placeholder="<?= lang('tnh_module_category_hand_over') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if(!empty($module_hand_over)): ?>
                                <?php foreach($module_hand_over as $key => $value): ?>
                                    <option <?= (!empty($category_hand_over) && $category_hand_over['type'] == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
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
        $('#module_category_hand_over').select2();
        appValidateForm($('#add-hand_over'), {
            code_category_hand_over: 'required',
            name_category_hand_over: 'required',
            // module_category_hand_over: 'required',
        }, handlingCategory);

        function handlingCategory(form) {
            $('.add').attr('disabled', 'disabled');
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
                url: site.base_url + 'admin/hand_over/handling_category/<?= $id ?>',
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
        init_selectpicker();
        init_datepicker();
    })
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>