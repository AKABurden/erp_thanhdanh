<?php echo form_open('admin/manufactures_temp/moderation_criteria/' . $po_id, array('id' => 'handlingModerationCriteria')); ?>
<div class="modal-dialog modal-md modal-semi">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Tiêu chí điều độ</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= lang('tnh_reference_productions_orders', 'reference_productions_orders') ?>
                    <?= $production_order['reference_no'] ?>
                </div>
                <div class="col-md-12">
                    
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 300px;">Nội dung</th>
                                <th>Kết quả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Phiếu PTM</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_ptm'] ? 'checked' : '' ?> name="is_ptm" id="is_ptm" value="1">
                                        <label for="is_ptm">Chọn</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Mẫu Sản Xuất</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_color'] ? 'checked' : '' ?> name="is_color" id="is_color" value="1">
                                        <label for="is_color">Chọn</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Layout Ghép</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_layout'] ? 'checked' : '' ?> name="is_layout" id="is_layout" value="1">
                                        <label for="is_layout">Chọn</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Khuân Bế</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_sewing'] ? 'checked' : '' ?> name="is_sewing" id="is_sewing" value="1">
                                        <label for="is_sewing">Chọn</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>NPL</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_npl'] ? 'checked' : '' ?> name="is_npl" id="is_npl" value="1">
                                        <label for="is_npl">Chọn</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Vật Tư</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_material'] ? 'checked' : '' ?> name="is_material" id="is_material" value="1">
                                        <label for="is_material">Chọn</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Phiếu Cắt Giấy</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_cutting'] ? 'checked' : '' ?> name="is_cutting" id="is_cutting" value="1">
                                        <label for="is_cutting">Chọn</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Ngày Về NPL</td>
                                <td>
                                    <input type="text" name="date_npl" id="date_npl" autocomplete="off" class="form-control datepicker" placeholder="<?= lang('Ngày Về NPL') ?>" value="<?= !empty($production_order['date_npl']) ? _d($production_order['date_npl']) : '' ?>">
                                </td>
                            </tr>
                            <tr>
                                <td>Số tờ in offset</td>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" <?= $production_order['is_number_printed'] ? 'checked' : '' ?> name="is_number_printed" id="is_number_printed" value="1">
                                        <label for="is_number_printed">Chọn NVL</label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="m_productions_orders_id" id="m_productions_orders_id" class="form-control" value="<?= $po_id ?>">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="submit" class="btn btn-primary add-moderation-criteria pull-right mleft10"><?= _l('save') ?></button>
            <button type="button" class="btn btn-default pull-right mleft5" data-dismiss="modal"><?= _l('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>

    $(function() {
        init_datepicker();
        appValidateForm($('#handlingModerationCriteria'), {
        }, handlingModerationCriteria);

        function handlingModerationCriteria(form) {
            $('.add-moderation-criteria').attr('disabled', 'disabled');
            var url = form.action;
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
                    } else {
                        alert_float('danger', data.message);
                    }

                    $('.add-moderation-criteria').removeAttr('disabled', 'disabled');
                    $('.modal-dialog .close').click();
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add-moderation-criteria').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>