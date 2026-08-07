<?php echo form_open('admin/kpi_equipment_stage/detail/' . $id, ['id' => 'formKpiEquipmentStage']); ?>
<div class="modal-dialog" style="width: 900px; max-width: 95vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?? '' ?></h4>
        </div>
        <div class="modal-body">

            <!-- ===== THÔNG TIN CÔNG ĐOẠN & THIẾT BỊ ===== -->
            <fieldset>
                <legend class="text-primary"><i class="fa fa-cogs"></i> Thông tin công đoạn &amp; thiết bị</legend>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nhóm công đoạn</label>
                            <input type="text" name="group_stage" class="form-control"
                                value="<?= isset($dtData) ? $dtData['group_stage'] : '' ?>"
                                placeholder="e.g. Cutting, Sewing, Ironing">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mã công đoạn <span class="text-danger">*</span></label>
                            <input type="text" name="stage_code" class="form-control" required
                                value="<?= isset($dtData) ? $dtData['stage_code'] : '' ?>"
                                placeholder="e.g. CD01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tên công đoạn</label>
                            <input type="text" name="stage_name" class="form-control"
                                value="<?= isset($dtData) ? $dtData['stage_name'] : '' ?>"
                                placeholder="e.g. Cutting Stage">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mã thiết bị <span class="text-danger">*</span></label>
                            <input type="text" name="equipment_code" class="form-control" required
                                value="<?= isset($dtData) ? $dtData['equipment_code'] : '' ?>"
                                placeholder="e.g. TB01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tên thiết bị <span class="text-danger">*</span></label>
                            <input type="text" name="equipment_name" class="form-control" required
                                value="<?= isset($dtData) ? $dtData['equipment_name'] : '' ?>"
                                placeholder="e.g. CNC Cutting Machine">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Trạng thái thiết bị</label>
                            <select name="equipment_status" class="form-control">
                                <?php
                                $statusOptions = ['Active' => 'Active (Hoạt động)', 'Stopped' => 'Stopped (Ngừng máy)', 'Maintenance' => 'Maintenance (Bảo trì)', 'Broken' => 'Broken (Hỏng)'];
                                $currentStatus = isset($dtData) ? $dtData['equipment_status'] : '';
                                foreach ($statusOptions as $val => $label):
                                ?>
                                    <option value="<?= $val ?>" <?= $currentStatus == $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ===== NGỪNG MÁY / SỬA CHỮA ===== -->
            <fieldset class="mtop10">
                <legend class="text-warning"><i class="fa fa-wrench"></i> Ngừng máy &amp; Sửa chữa</legend>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Thời gian ngừng máy (phút)</label>
                            <input type="number" step="0.01" name="downtime_minutes" class="form-control"
                                value="<?= isset($dtData) ? $dtData['downtime_minutes'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Số lần sửa chữa</label>
                            <input type="number" step="1" name="repair_count" class="form-control"
                                value="<?= isset($dtData) ? $dtData['repair_count'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Thời gian sửa chữa (phút)</label>
                            <input type="number" step="0.01" name="repair_minutes" class="form-control"
                                value="<?= isset($dtData) ? $dtData['repair_minutes'] : '0' ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nguyên nhân ngừng máy</label>
                            <textarea name="downtime_reason" class="form-control" rows="2"><?= isset($dtData) ? $dtData['downtime_reason'] : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ===== BẢO TRÌ / HIỆU CHUẨN ===== -->
            <fieldset class="mtop10">
                <legend class="text-info"><i class="fa fa-calendar-check-o"></i> Bảo trì &amp; Hiệu chuẩn</legend>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="periodic_maintenance" value="1"
                                        <?= (isset($dtData) && $dtData['periodic_maintenance']) ? 'checked' : '' ?>>
                                    Bảo trì định kỳ (Y/N)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lần bảo trì gần nhất</label>
                            <div class="input-group date">
                                <input type="text" name="last_maintenance_date" class="form-control datepicker"
                                    value="<?= isset($dtData) && !empty($dtData['last_maintenance_date']) ? _d($dtData['last_maintenance_date']) : '' ?>">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="calibration" value="1"
                                        <?= (isset($dtData) && $dtData['calibration']) ? 'checked' : '' ?>>
                                    Hiệu chuẩn (Y/N)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lần hiệu chuẩn gần nhất</label>
                            <div class="input-group date">
                                <input type="text" name="last_calibration_date" class="form-control datepicker"
                                    value="<?= isset($dtData) && !empty($dtData['last_calibration_date']) ? _d($dtData['last_calibration_date']) : '' ?>">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ===== CHẤT LƯỢNG / NĂNG SUẤT ===== -->
            <fieldset class="mtop10">
                <legend class="text-success"><i class="fa fa-bar-chart"></i> Chất lượng &amp; Năng suất</legend>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>NPL cảnh báo (%)</label>
                            <input type="number" step="0.01" name="npl_warning_pct" class="form-control"
                                value="<?= isset($dtData) ? $dtData['npl_warning_pct'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Số lỗi</label>
                            <input type="number" step="1" name="defect_count" class="form-control"
                                value="<?= isset($dtData) ? $dtData['defect_count'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tỷ lệ lỗi (%)</label>
                            <input type="number" step="0.01" name="defect_rate_pct" class="form-control"
                                value="<?= isset($dtData) ? $dtData['defect_rate_pct'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tỷ lệ đạt KH (%)</label>
                            <input type="number" step="0.01" name="target_achievement_pct" class="form-control"
                                value="<?= isset($dtData) ? $dtData['target_achievement_pct'] : '0' ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Năng suất định mức</label>
                            <input type="number" step="0.01" name="planned_output" class="form-control"
                                value="<?= isset($dtData) ? $dtData['planned_output'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sản lượng thực tế</label>
                            <input type="number" step="0.01" name="actual_output" class="form-control"
                                value="<?= isset($dtData) ? $dtData['actual_output'] : '0' ?>">
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ===== CHI PHÍ & NGÂN SÁCH ===== -->
            <fieldset class="mtop10">
                <legend class="text-danger"><i class="fa fa-money"></i> Chi phí &amp; Ngân sách</legend>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ngân sách thiết bị</label>
                            <input type="text" name="equipment_budget" class="form-control number-format"
                                value="<?= isset($dtData) ? $dtData['equipment_budget'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Chi phí sửa chữa</label>
                            <input type="text" name="repair_cost" class="form-control number-format"
                                value="<?= isset($dtData) ? $dtData['repair_cost'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Chi phí bảo trì</label>
                            <input type="text" name="maintenance_cost" class="form-control number-format"
                                value="<?= isset($dtData) ? $dtData['maintenance_cost'] : '0' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tổng chi phí</label>
                            <input type="text" name="total_cost" class="form-control number-format"
                                value="<?= isset($dtData) ? $dtData['total_cost'] : '0' ?>">
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ===== TRẠNG THÁI CẢNH BÁO & GHI CHÚ ===== -->
            <fieldset class="mtop10">
                <legend><i class="fa fa-info-circle"></i> Trạng thái cảnh báo &amp; Ghi chú</legend>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Trạng thái cảnh báo</label>
                            <input type="text" name="warning_status" class="form-control"
                                value="<?= isset($dtData) ? $dtData['warning_status'] : '' ?>"
                                placeholder="e.g. Danger, Warning, Normal">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2"><?= isset($dtData) ? $dtData['note'] : '' ?></textarea>
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
        appValidateForm($('#formKpiEquipmentStage'), {
            stage_code: 'required',
            equipment_code: 'required',
            equipment_name: 'required',
        }, handling);

        function handling(form) {
            var url = form.action;
            var $form = $(form),
                formData = new FormData(),
                formParams = $form.serializeArray();

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            if (!$form.find('[name=periodic_maintenance]').is(':checked')) {
                formData.set('periodic_maintenance', '0');
            }
            if (!$form.find('[name=calibration]').is(':checked')) {
                formData.set('calibration', '0');
            }

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
                alert_float('danger', 'An error occurred.');
            });
            return false;
        }
    });
</script>