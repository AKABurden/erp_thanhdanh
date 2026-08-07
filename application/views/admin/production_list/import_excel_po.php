<?php echo form_open('admin/production_list/import_excel_po', array('id' => 'import_moderation_plan')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Import update'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= lang('note', 'note') ?>:
                    <div class="text-danger">
                        <div>
                            - Trường dữ liệu phải dưới dạng text không dùng công thức hay định dạng.</br>
                            - Vui lòng chọn đầy đủ các trường dữ liệu.
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_row"><?= lang('Dòng bắt đầu') ?></label>
                        <input type="number" min="1" class="form-control" id="start_row" name="start_row" value="1" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_row"><?= lang('Dòng kết thúc') ?></label>
                        <input type="number" min="1" class="form-control" id="end_row" name="end_row" value="1" required>
                    </div>
                </div>
                <div class="col-md-12 mbot10">
                    <div class="table-responsive">
                        <table class="table table-bordered dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('Tên dữ liệu') ?></th>
                                    <th><?= lang('Cột') ?></th>
                                    <th><?= lang('Tên dữ liệu') ?></th>
                                    <th><?= lang('Cột') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $fields = [
                                    ['label' => lang('ID'), 'name' => 'col_id', 'value' => 'B'],
                                    ['label' => lang('Ngày bắt đầu dự kiến'), 'name' => 'col_ngay_bat_dau_du_kien', 'value' => 'AG'],
                                    ['label' => lang('Ngày hoàn thành in'), 'name' => 'col_ngay_hoan_thanh_in', 'value' => 'AH'],
                                    ['label' => lang('Ngày bắt đầu kế hoạch'), 'name' => 'col_ngay_bat_dau_ke_hoach', 'value' => 'AI'],
                                    ['label' => lang('Ngày kết thúc kế hoạch'), 'name' => 'col_ngay_ket_thuc_ke_hoach', 'value' => 'AJ'],
                                    ['label' => lang('Ngày bắt đầu thực tế'), 'name' => 'col_ngay_bat_dau_thuc_te', 'value' => 'AK'],
                                    ['label' => lang('Ngày kết thúc thực tế'), 'name' => 'col_ngay_ket_thuc_thuc_te', 'value' => 'AL'],
                                    ['label' => lang('Thời gian canh bài thực tế'), 'name' => 'col_thoi_gian_canh_bai_thuc_te', 'value' => 'AM'],
                                    ['label' => lang('NPL canh bài thực tế'), 'name' => 'col_npl_canh_bai_thuc_te', 'value' => 'AN'],
                                    ['label' => lang('Số lượng thực tế'), 'name' => 'col_so_luong_thuc_te', 'value' => 'AO'],
                                ];
                                for ($i = 0; $i < count($fields); $i += 2): ?>
                                    <tr>
                                        <td><?= $fields[$i]['label'] ?></td>
                                        <td>
                                            <input type="text" class="form-control" name="<?= $fields[$i]['name'] ?>" value="<?= $fields[$i]['value'] ?>">
                                        </td>
                                        <?php if (isset($fields[$i + 1])): ?>
                                            <td><?= $fields[$i + 1]['label'] ?></td>
                                            <td>
                                                <input type="text" class="form-control" name="<?= $fields[$i + 1]['name'] ?>" value="<?= $fields[$i + 1]['value'] ?>">
                                            </td>
                                        <?php else: ?>
                                            <td></td>
                                            <td></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mbot10">
                        <div class="">
                            <div class="input-group input-file" name="file">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-choose" style="height: 36px;" type="button"><?= lang('file') ?></button>
                                </span>
                                <input type="text" name="text_file" class="form-control" placeholder='<?= lang('choose') ?>' />
                                <span class="input-group-btn">
                                    <button class="btn btn-warning btn-reset" style="height: 36px;" type="button"><?= lang('tnh_reset') ?></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="show-errors text-danger" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap; word-break: break-word;"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
        bs_input_file();
        init_datepicker();

        appValidateForm($('#import_moderation_plan'), {
                text_file: {
                    required: true,
                    extension: "xlsx,xls"
                },
                actual_date: 'required',
                start_row: 'required',
                end_row: 'required',
                col_id: 'required',
                col_ngay_bat_dau_du_kien: 'required',
                col_ngay_hoan_thanh_in: 'required',
                col_ngay_bat_dau_ke_hoach: 'required',
                col_ngay_ket_thuc_ke_hoach: 'required',
                col_ngay_bat_dau_thuc_te: 'required',
                col_ngay_ket_thuc_thuc_te: 'required',
                col_so_luong_thuc_te: 'required',
            },
            importStages, {
                text_file: '<?= lang('Vui lòng chọn excel') ?>'
            }
        );

        function importStages(form) {
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
            //
            var url = form.action;
            $.ajax({
                    url: site.base_url + 'admin/production_list/import_excel_po',
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(data) {
                    console.log(data);
                    $('.add').removeAttr('disabled', 'disabled');
                    if (data.result) {
                        alert_float('success', data.message);
                        loadDataPlan();
                    } else {
                        alert_float('danger', data.message);
                    }
                    $('.show-errors').html(data.errors);
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    });
</script>