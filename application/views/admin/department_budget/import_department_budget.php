<?php echo form_open('admin/department_budget/import_excel', ['id' => 'importDepartmentBudget']); ?>
<div class="modal-dialog" style="width: 600px; max-width: 95vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?? 'Import KPI Ngân Sách Phòng Ban' ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                    <a target="_blank" href="<?= base_url('uploads/import_dt/KPI_NganSach_PhongBan.xlsx?vs=1.1') ?>">
                        <i class="fa fa-download"></i> Download file mẫu (KPI_NganSach_PhongBan.xlsx)
                    </a>
                </div>
            </div>

            <!-- Hướng dẫn cột -->
            <div class="row mbot10">
                <div class="col-md-12">
                    <div class="alert alert-info" style="padding: 8px 12px; font-size: 12px;">
                        <strong><i class="fa fa-info-circle"></i> Cấu trúc file Excel (bắt đầu từ dòng 3):</strong>
                        <table class="table table-bordered table-condensed mtop5" style="font-size:12px; margin-bottom:0;">
                            <thead>
                                <tr class="info">
                                    <th>Cột</th>
                                    <th>Tên trường</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>A</td>
                                    <td>STT</td>
                                    <td>Bỏ qua</td>
                                </tr>
                                <tr>
                                    <td>B</td>
                                    <td>Mã Phòng Ban</td>
                                    <td class="text-danger">Bắt buộc - Mã phòng ban trong hệ thống</td>
                                </tr>
                                <tr>
                                    <td>C</td>
                                    <td>Tên Phòng Ban</td>
                                    <td>Bỏ qua (lấy từ hệ thống)</td>
                                </tr>
                                <tr>
                                    <td>D</td>
                                    <td>Mã Chi Phí</td>
                                    <td class="text-danger">Bắt buộc - Mã chi phí thuộc phòng ban</td>
                                </tr>
                                <tr>
                                    <td>E</td>
                                    <td>Ngân Sách Được Cấp</td>
                                    <td class="text-danger">Bắt buộc - Nhập số tiền</td>
                                </tr>
                                <tr>
                                    <td>F</td>
                                    <td>Chi Phí Thực Tế</td>
                                    <td class="text-muted">Sẽ lấy từ phần mềm Tự tính (bỏ qua)</td>
                                </tr>
                                <tr>
                                    <td>G-J</td>
                                    <td>Chênh Lệch, Tỷ Lệ, Trạng Thái, Điểm KPI</td>
                                    <td class="text-muted">Tự tính (bỏ qua)</td>
                                </tr>
                                <tr>
                                    <td>K</td>
                                    <td>Ghi Chú</td>
                                    <td>Tuỳ chọn</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="fileinput fileinput-new mtop10 mbot10" data-provides="fileinput">
                        <span class="btn btn-default btn-file col-md-12 mbot20">
                            <span>Chọn file Excel</span>
                            <input type="file" name="file" class="mbot10 btn" style="width:100%"
                                id="file_import_dept_budget" accept=".xls,.xlsx" required="">
                        </span>
                    </div>
                    <div class="show-errors text-danger" style="max-height: 200px; "></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add">
                <i class="fa fa-upload"></i> Import
            </button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function() {
        appValidateForm($('#importDepartmentBudget'), {
            file: 'required'
        }, handling);

        function handling(form) {
            var url = form.action;
            var $form = $(form),
                formData = new FormData(),
                formParams = $form.serializeArray();

            $.each($form.find('input[type="file"]'), function(i, tag) {
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
            }).done(function(data) {
                if (data.success) {
                    alert_float('success', data.message);
                    if (!data.errors) {
                        $('.modal-dialog .close').trigger('click');
                    }
                    if (data.errors) {
                        $('.show-errors').html('<strong>Các dòng lỗi:</strong><br>' + data.errors);
                    }
                    if (typeof oTable != 'undefined') {
                        oTable.draw();
                    }
                } else {
                    $('.show-errors').html(data.errors);
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled');
                }
            }).fail(function() {
                alert_float('danger', 'Có lỗi xảy ra, vui lòng thử lại.');
            });
            return false;
        }
    });
</script>