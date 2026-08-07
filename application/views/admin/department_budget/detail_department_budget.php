<?php echo form_open('admin/department_budget/detail/' . $id, ['id' => 'formDepartmentBudget']); ?>
<div class="modal-dialog" style="width: 700px; max-width: 95vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?? '' ?></h4>
        </div>
        <div class="modal-body">

            <!-- ===== PHÒNG BAN & LOẠI CHI PHÍ ===== -->
            <fieldset>
                <legend class="text-primary"><i class="fa fa-building"></i> Phòng ban & Loại chi phí</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phòng ban <span class="text-danger">*</span></label>
                            <select name="department_id" id="sel-department" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">-- Chọn phòng ban --</option>
                                <?php foreach ($dtDepartments as $dep): ?>
                                    <option value="<?= $dep['departmentid'] ?>"
                                        <?= (isset($dtData) && $dtData['department_id'] == $dep['departmentid']) ? 'selected' : '' ?>>
                                        <?= $dep['code'] ?> - <?= $dep['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Loại chi phí <span class="text-danger">*</span></label>
                            <select name="cost_id" id="sel-cost" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">-- Chọn loại chi phí --</option>
                                <?php foreach ($dtCosts as $cost): ?>
                                    <option value="<?= $cost['id'] ?>"
                                        <?= (isset($dtData) && $dtData['cost_id'] == $cost['id']) ? 'selected' : '' ?>>
                                        <?= $cost['code'] ?> - <?= $cost['name'] ?>
                                        <?= !empty($cost['ten_cha']) ? ' (' . $cost['ten_cha'] . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ===== NGÂN SÁCH & CHI PHÍ ===== -->
            <fieldset class="mtop10">
                <legend class="text-danger"><i class="fa fa-money"></i> Ngân sách & Chi phí</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ngân sách được cấp <span class="text-danger">*</span></label>
                            <input type="text" name="ngan_sach_duoc_cap" id="ngan_sach_duoc_cap"
                                class="form-control number-format" required
                                value="<?= isset($dtData) ? $dtData['ngan_sach_duoc_cap'] : '0' ?>"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chi phí thực tế</label>
                            <input type="text" name="chi_phi_thuc_te" id="chi_phi_thuc_te"
                                class="form-control number-format"
                                value="<?= isset($dtData) ? $dtData['chi_phi_thuc_te'] : '0' ?>"
                                placeholder="0">
                            <small class="text-muted">Hệ thống sẽ tự tính KPI sau khi lưu</small>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ===== KẾT QUẢ KPI (chỉ đọc khi sửa) ===== -->
            <?php if (!empty($dtData)): ?>
                <fieldset class="mtop10">
                    <legend class="text-success"><i class="fa fa-bar-chart"></i> Kết quả KPI (tự tính)</legend>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Chênh lệch</label>
                                <?php
                                $cl = $dtData['chenh_lech'];
                                $clClass = $cl > 0 ? 'text-danger' : 'text-success';
                                ?>
                                <p class="form-control-static bold <?= $clClass ?>"><?= formatMoney($cl) ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tỷ lệ sử dụng</label>
                                <p class="form-control-static bold"><?= number_format($dtData['ty_le_su_dung'], 2) ?>%</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Trạng thái NS</label>
                                <?php
                                $tt = $dtData['trang_thai_ngan_sach'];
                                $ttMap = ['Tốt' => 'label-success', 'Đạt' => 'label-info', 'Cảnh báo' => 'label-warning', 'Vượt' => 'label-danger'];
                                $ttClass = $ttMap[$tt] ?? 'label-default';
                                ?>
                                <p class="form-control-static"><span class="label <?= $ttClass ?>"><?= $tt ?></span></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Điểm KPI</label>
                                <?php
                                $diem = (int)$dtData['diem_kpi'];
                                $diemClass = $diem >= 100 ? 'label-success' : ($diem >= 90 ? 'label-info' : ($diem >= 70 ? 'label-warning' : 'label-danger'));
                                ?>
                                <p class="form-control-static"><span class="label <?= $diemClass ?> fsize14"><?= $diem ?> điểm</span></p>
                            </div>
                        </div>
                    </div>
                </fieldset>
            <?php endif; ?>

            <!-- ===== GHI CHÚ ===== -->
            <fieldset class="mtop10">
                <legend><i class="fa fa-comment"></i> Ghi chú</legend>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea name="ghi_chu" class="form-control" rows="2"
                                placeholder="Ghi chú thêm nếu có..."><?= isset($dtData) ? $dtData['ghi_chu'] : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </fieldset>

        </div><!-- /.modal-body -->
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary submit-btn">
                <i class="fa fa-save"></i> <?= _l('submit') ?>
            </button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function() {
        init_selectpicker();
        appValidateForm($('#formDepartmentBudget'), {
            department_id: 'required',
            cost_id: 'required',
            ngan_sach_duoc_cap: 'required',
        }, handling);

        function handling(form) {
            var url = form.action;
            var $form = $(form),
                formData = new FormData(),
                formParams = $form.serializeArray();

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
                if (data.result == 1) {
                    alert_float('success', data.message);
                    $('.modal-dialog .close').trigger('click');
                    if (typeof oTable != 'undefined') {
                        oTable.draw();
                    }
                } else {
                    alert_float('danger', data.message);
                    $('.submit-btn').removeAttr('disabled');
                }
            }).fail(function() {
                alert_float('danger', 'Có lỗi xảy ra.');
            });
            return false;
        }
    });
</script>