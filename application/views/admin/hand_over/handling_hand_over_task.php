<?php echo form_open_multipart('admin/hand_over/handling_hand_over_task/'.$id, array('id' => 'add-hand_over', 'class' => '', 'enctype' => 'multipart/form-data',)); ?>
<div class="modal-dialog" style="min-width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_category_hand_over', 'category_hand_over') ?>
                        <select name="category_hand_over_id" id="category_hand_over" data-placeholder="<?= lang('tnh_category_hand_over') ?>" class="modal-select2" style="width: 100%;" required="required">
                            <option value=""></option>
                            <?php if(!empty($category_hand_over)): ?>
                                <?php foreach($category_hand_over as $key => $value): ?>
                                    <option <?= (!empty($hand_over_task) && $hand_over_task['category_hand_over_id'] == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
<!--                <div class="col-md-12">-->
<!--                    <div class="form-group">-->
<!--                        --><?//= lang('tnh_code_handover_task', 'code_handover_task') ?>
<!--                        --><?php //echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($hand_over_task) ? $hand_over_task['code'] : '')), 'placeholder="' . lang('tnh_code_handover_task') . '" id="code_handover_task" required class="form-control input-tip"'); ?>
<!--                    </div>-->
<!--                </div>-->
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_name_handover_task', 'name_handover_task') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($hand_over_task) ? $hand_over_task['name'] : '')), 'placeholder="' . lang('tnh_name_handover_task') . '" id="name_handover_task" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php
                        $value = !empty($hand_over_task) ? $hand_over_task['id_stage'] : '';
                        echo render_select('id_stage', (!empty($stage) ? $stage : ''), ['id', 'code', 'name'], 'Công đoạn', $value)
                    ?>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
						<?php
                            $value = (isset($_POST['standard']) ? $_POST['standard'] : (!empty($hand_over_task) ? $hand_over_task['standard'] : ''));
                            echo render_select('standard', (!empty($standard) ? $standard : ''), ['id', 'code', 'name'], 'c_tieu_chuan', $value)
						?>

<!--                        --><?//= lang('c_tieu_chuan', 'standard') ?>
<!--                        --><?php //echo form_input('standard', (isset($_POST['standard']) ? $_POST['standard'] : (!empty($hand_over_task) ? $hand_over_task['standard'] : '')), 'placeholder="' . lang('c_tieu_chuan') . '" id="standard"  class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('c_phuong_phap', 'method') ?>
                        <?php echo form_input('method', (isset($_POST['method']) ? $_POST['method'] : (!empty($hand_over_task) ? $hand_over_task['method'] : '')), 'placeholder="' . lang('c_phuong_phap') . '" id="method"  class="form-control input-tip"'); ?>
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
        $('#standard').change(function() {
            var method = $(this).find('option:selected').data('subtext');
            $('#method').val(method);
        })
        $('#category_hand_over').select2();
        appValidateForm($('#add-hand_over'), {
            code_handover_task: 'required',
            name_handover_task: 'required',
            category_hand_over: 'required',
        }, handlingHandOverTask);

        function handlingHandOverTask(form) {
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
                url: site.base_url + 'admin/hand_over/handling_hand_over_task/<?= $id ?>',
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