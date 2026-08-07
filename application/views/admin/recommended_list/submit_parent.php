<div class="modal-dialog" role="document">
    <div class="modal-content">
        <?php echo form_open(admin_url('recommended_list/submit_parent/' . ($id ?? '')), array('id' => 'submit_form')); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                <?php echo ($title); ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= render_input('code', 'tnh_code_recommended_list', (!empty($value['code']) ? ($value['code']) : '')) ?>
                </div>
                <div class="col-md-12">
                    <?= render_input('name', 'tnh_name_recommended_list', (!empty($value['name']) ? ($value['name']) : '')) ?>
                </div>
                <div class="col-md-12">
                    <?= render_select('type', $arrType, ['code', 'name'], 'type', ($value['type'] ?? '')) ?>
                </div>
                <div class="col-md-12">
                    <?= render_textarea('note', 'Tên công việc', (!empty($value['note']) ? $value['note'] : '')) ?>
                </div>
                <div class="col-md-12">
                    <div class="form-group" app-field-wrapper="type_plan_propose">
                        <label for="type_plan_propose" class="control-label">
                            <?php echo _l('Loại kế hoạch'); ?>
                        </label>
                        <select name="type_plan_propose" id="type_plan_propose" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Mã công việc') ?>" class="selectpicker">
                            <option></option>
                            <?php if (!empty($type_plan_propose)) : ?>
                                <?php foreach ($type_plan_propose as $key => $k) : ?>
                                    <option <?= ((!empty($value['type_plan_propose']) && $value['type_plan_propose'] == $k['id']) ? 'selected' : '') ?> value="<?= $k['id'] ?>"><?= $k['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-info p7" type="submit"><?php echo _l('submit'); ?></button>
            <button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script>
    init_selectpicker();

    _validate_form($('#submit_form'), {
        code: 'required',
        name: 'required',
        type_plan_propose: 'required',
    }, submit);

    function submit(form) {
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        // $.each(form.find('input[type="file"]'), function(i, tag) {
        //     $.each($(tag)[0].files, function(i, file) {
        //         formData.append(tag.name, file);
        //     });
        // });

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });
        // var url = form.action;
        var url = site.base_url + 'admin/recommended_list/submit/<?= $id ?? '' ?>';
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
                if (data.isSuccess) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message);
                }

                if (typeof oTable != 'undefined' && oTable != '') {
                    oTable.draw();
                }
                $('.modal-dialog .close').trigger('click');
            })
            .fail(function() {
                alert_float('danger', 'error');
            });
        return false;
    }
</script>