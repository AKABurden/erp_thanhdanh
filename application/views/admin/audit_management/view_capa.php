<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-file-text-o"></i> <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Mã CAPA:</strong> <?php echo $capa->capa_code; ?></p>
                                <p><strong>Ngày tạo:</strong> <?php echo _dt($capa->created_at); ?></p>
                                <p><strong>Trạng thái:</strong> 
                                    <?php if ($capa->status == 'COMPLETED'): ?>
                                        <span class="label label-success">Hoàn thành</span>
                                    <?php elseif ($capa->status == 'IN_PROGRESS'): ?>
                                        <span class="label label-info">Đang thực hiện</span>
                                    <?php else: ?>
                                        <span class="label label-warning">Mới tạo</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Người thực hiện:</strong> 
                                    <?php echo get_staff_full_name($capa->assigned_to); ?>
                                </p>
                                <p><strong>Hạn hoàn thành:</strong> 
                                    <?php echo _d($capa->due_date); ?>
                                    <?php if (strtotime($capa->due_date) < time() && $capa->status != 'COMPLETED'): ?>
                                        <span class="label label-danger">Quá hạn</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h4>Mô tả vấn đề</h4>
                        <p><?php echo nl2br(htmlspecialchars($capa->issue_description)); ?></p>
                        
                        <h4>Nguyên nhân gốc rễ</h4>
                        <p><?php echo nl2br(htmlspecialchars($capa->root_cause)); ?></p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Hành động khắc phục</h4>
                                <p><?php echo nl2br(htmlspecialchars($capa->corrective_action)); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h4>Hành động phòng ngừa</h4>
                                <p><?php echo nl2br(htmlspecialchars($capa->preventive_action)); ?></p>
                            </div>
                        </div>
                        
                        <?php if ($capa->status != 'COMPLETED'): ?>
                        <hr>
                        <div class="text-right">
                            <button type="button" class="btn btn-success" onclick="markAsCompleted()">
                                <i class="fa fa-check"></i> Đánh dấu hoàn thành
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <div class="btn-bottom-toolbar text-right mtop15">
                            <button type="button" class="btn btn-default" onclick="window.close();">
                                <i class="fa fa-times"></i> Đóng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
function markAsCompleted() {
    if (!confirm('Xác nhận đánh dấu CAPA này đã hoàn thành?')) {
        return;
    }
    
    $.post('<?php echo admin_url("audit_management/completeCapaAjax"); ?>', {
        capa_id: <?php echo $capa->id; ?>,
        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function(response) {
        if (response.success) {
            alert_float('success', 'Đã đánh dấu hoàn thành!');
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        } else {
            alert_float('danger', 'Có lỗi xảy ra!');
        }
    }, 'json');
}
</script>
</body>
</html>
