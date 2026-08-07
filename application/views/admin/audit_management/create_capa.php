<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-exclamation-triangle"></i> <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        
                        <div class="alert alert-info">
                            <strong>Audit:</strong> <?php echo $audit->department; ?> - <?php echo _d($audit->audit_date); ?><br>
                            <strong>Tiêu chí không đạt:</strong> <?php echo htmlspecialchars($item->item_text); ?>
                            <?php if ($item->critical): ?>
                                <span class="label label-danger">CRITICAL</span>
                            <?php endif; ?>
                        </div>

                        <?php echo form_open(admin_url('audit_management/saveCapa')); ?>
                        
                        <input type="hidden" name="item_id" value="<?php echo $item->id; ?>">
                        <input type="hidden" name="audit_id" value="<?php echo $audit->id; ?>">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="issue_description" class="control-label">
                                        <span class="text-danger">*</span> Mô tả vấn đề
                                    </label>
                                    <textarea name="issue_description" id="issue_description" rows="3" 
                                        class="form-control" required><?php echo htmlspecialchars($item->item_text); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="root_cause" class="control-label">
                                        <span class="text-danger">*</span> Nguyên nhân gốc rễ
                                    </label>
                                    <textarea name="root_cause" id="root_cause" rows="3" 
                                        class="form-control" required 
                                        placeholder="Phân tích nguyên nhân gốc rễ của vấn đề..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="corrective_action" class="control-label">
                                        <span class="text-danger">*</span> Hành động khắc phục (Corrective Action)
                                    </label>
                                    <textarea name="corrective_action" id="corrective_action" rows="3" 
                                        class="form-control" required
                                        placeholder="Các hành động khắc phục ngay lập tức..."></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preventive_action" class="control-label">
                                        <span class="text-danger">*</span> Hành động phòng ngừa (Preventive Action)
                                    </label>
                                    <textarea name="preventive_action" id="preventive_action" rows="3" 
                                        class="form-control" required
                                        placeholder="Các hành động phòng ngừa lặp lại..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="assigned_to" class="control-label">
                                        <span class="text-danger">*</span> Người thực hiện
                                    </label>
                                    <select name="assigned_to" id="assigned_to" class="selectpicker form-control" 
                                        data-live-search="true" required>
                                        <option value="">-- Chọn người thực hiện --</option>
                                        <?php
                                        $staff_members = $this->db->where('active', 1)->get('tblstaff')->result();
                                        foreach ($staff_members as $staff) {
                                            echo '<option value="' . $staff->staffid . '">' . $staff->firstname . ' ' . $staff->lastname . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date" class="control-label">
                                        <span class="text-danger">*</span> Hạn hoàn thành
                                    </label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" required
                                        min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="btn-bottom-toolbar text-right">
                            <button type="button" class="btn btn-default" onclick="window.close();">
                                <i class="fa fa-times"></i> Đóng
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Tạo phiếu CAPA
                            </button>
                        </div>
                        
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    appValidateForm($('form'), {
        issue_description: 'required',
        root_cause: 'required',
        corrective_action: 'required',
        preventive_action: 'required',
        assigned_to: 'required',
        due_date: 'required'
    });
});
</script>
</body>
</html>
