<div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">
        <?php echo form_open(admin_url('recommended_list/submit/' . ($id ?? '')), array('id' => 'submit_form')); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                <?php echo ($title); ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= render_select('parent_id', $arrRecommendedList, ['id', 'code', 'name'], 'Danh mục cha', ($value['parent_id'] ?? ''), ['disabled' => true]) ?>
                    <input name="parent_id" type="hidden" value="<?= ($value['parent_id'] ?? '') ?>">
                </div>
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
                    <?= render_textarea('note', 'note', (!empty($value['note']) ? $value['note'] : '')) ?>
                </div>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="is_excel" id="is_excel" <?= (!empty($value['is_excel']) && $value['is_excel'] == 1 ? 'checked' : '') ?>>
                        <label for="is_excel">Danh sách có thể import excel</label>
                    </div>
                </div>
                <div class="col-md-12 div_excel">
                    <label for="excel_file">File Excel</label>
                    <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx, .xls">
                    <?php if (!empty($value['excel'])): ?>
                        <?php 
                            $saved_headers = json_decode($value['excel'], true);
                            if (is_array($saved_headers)) {
                                echo '<div class="table-responsive mtop10" style="margin-top: 10px;">';
                                echo '<table class="table table-bordered table-striped" style="margin-bottom: 0;">';
                                echo '<thead><tr class="success">';
                                foreach ($saved_headers as $header) {
                                    echo '<th class="text-center bold" style="min-width: 150px; white-space: nowrap;">' . htmlspecialchars($header) . '</th>';
                                }
                                echo '</tr></thead>';
                                echo '<tbody><tr>';
                                foreach ($saved_headers as $header) {
                                    echo '<td>&nbsp;</td>';
                                }
                                echo '</tr></tbody>';
                                echo '</table>';
                                echo '</div>';
                            } else {
                                echo '<p class="text-info mtop10" style="margin-top: 10px;"><strong>FILE hiện tại:</strong> ' . htmlspecialchars($value['excel']) . '</p>';
                            }
                        ?>
                    <?php endif; ?>
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

    function toggleExcelInput() {
        if ($('#is_excel').is(':checked')) {
            $('.div_excel').show();
        } else {
            $('.div_excel').hide();
        }
    }
    $('#is_excel').on('change', function() {
        toggleExcelInput();
    });
    toggleExcelInput();

    _validate_form($('#submit_form'), {
        code: 'required',
        name: 'required',
    }, submit);

    function submit(form) {
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