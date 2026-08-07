<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    .history-timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 50px;
        padding-bottom: 30px;
        border-left: 2px solid #e2e8f0;
        margin-left: 20px;
    }

    .timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }

    .timeline-icon {
        position: absolute;
        left: -11px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .timeline-icon.status-change {
        background: linear-gradient(135deg, #3182ce 0%, #2c5aa0 100%);
    }

    .timeline-icon.image-upload {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .timeline-icon.image-delete {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .timeline-icon.audit-create {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .timeline-icon.audit-complete {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .timeline-content {
        background: white;
        border-radius: 8px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .timeline-title {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .timeline-time {
        font-size: 12px;
        color: #718096;
        white-space: nowrap;
        margin-left: 12px;
    }

    .timeline-description {
        font-size: 13px;
        color: #4a5568;
        line-height: 1.6;
        margin-bottom: 8px;
    }

    .timeline-user {
        font-size: 12px;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .timeline-user i {
        color: #a0aec0;
    }

    .timeline-details {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
    }

    .timeline-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-right: 6px;
    }

    .badge-yes {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-no {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-unchanged {
        background: #e5e7eb;
        color: #4b5563;
    }

    .modal-header-custom {
        background: linear-gradient(135deg, #542901 0%, #8b4513 100%);
        color: white;
        padding: 20px 24px;
        border-radius: 10px 10px 0 0;
    }

    .modal-header-custom h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-header-custom .close {
        color: white;
        opacity: 0.8;
        font-size: 28px;
    }

    .modal-header-custom .close:hover {
        opacity: 1;
    }

    .audit-info-bar {
        background: #f8fafc;
        padding: 12px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        gap: 20px;
        font-size: 13px;
        color: #4a5568;
    }

    .audit-info-bar i {
        color: #718096;
        margin-right: 6px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 16px;
    }

    .empty-state h5 {
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #9ca3af;
        font-size: 14px;
    }
</style>

<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
        <!-- Modal Header -->
        <div class="modal-header-custom">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <h4>
                    <i class="fa fa-history"></i>
                    <?php echo $title; ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>

        <!-- Audit Info Bar -->
        <div class="audit-info-bar">
            <span><i class="fa fa-building"></i> <?php echo $audit->department; ?></span>
            <span><i class="fa fa-calendar"></i> <?php echo _d($audit->audit_date); ?></span>
            <span><i class="fa fa-user"></i> <?php echo $audit->team_leader; ?></span>
            <span>
                <i class="fa fa-info-circle"></i>
                <?php
                if ($audit->status == 'COMPLETED') {
                    echo '<span class="label label-success">Đã hoàn thành</span>';
                } elseif ($audit->status == 'IN_PROGRESS') {
                    echo '<span class="label label-info">Đang thực hiện</span>';
                } else {
                    echo '<span class="label label-default">' . $audit->status . '</span>';
                }
                ?>
            </span>
        </div>

        <!-- Modal Body -->
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding: 24px;">
            <?php if (empty($logs)): ?>
                <div class="empty-state">
                    <i class="fa fa-inbox"></i>
                    <h5>Chưa có lịch sử thao tác</h5>
                    <p>Các thao tác trên phiếu audit này sẽ được ghi lại tại đây</p>
                </div>
            <?php else: ?>
                <div class="history-timeline">
                    <?php foreach ($logs as $log): ?>
                        <div class="timeline-item">
                            <!-- Timeline Icon -->
                            <div class="timeline-icon <?php echo strtolower(str_replace('_', '-', $log->action_type)); ?>">
                                <?php
                                switch ($log->action_type) {
                                    case 'STATUS_CHANGE':
                                        echo '<i class="fa fa-exchange"></i>';
                                        break;
                                    case 'IMAGE_UPLOAD':
                                        echo '<i class="fa fa-upload"></i>';
                                        break;
                                    case 'IMAGE_DELETE':
                                        echo '<i class="fa fa-trash"></i>';
                                        break;
                                    case 'AUDIT_CREATE':
                                        echo '<i class="fa fa-plus-circle"></i>';
                                        break;
                                    case 'AUDIT_COMPLETE':
                                        echo '<i class="fa fa-check-circle"></i>';
                                        break;
                                    default:
                                        echo '<i class="fa fa-circle"></i>';
                                }
                                ?>
                            </div>

                            <!-- Timeline Content -->
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <h6 class="timeline-title">
                                        <?php
                                        switch ($log->action_type) {
                                            case 'STATUS_CHANGE':
                                                echo 'Thay đổi trạng thái';
                                                break;
                                            case 'IMAGE_UPLOAD':
                                                echo 'Upload ảnh';
                                                break;
                                            case 'IMAGE_DELETE':
                                                echo 'Xóa ảnh';
                                                break;
                                            case 'AUDIT_CREATE':
                                                echo 'Tạo phiếu audit';
                                                break;
                                            case 'AUDIT_COMPLETE':
                                                echo 'Hoàn thành audit';
                                                break;
                                            default:
                                                echo $log->action_type;
                                        }
                                        ?>
                                    </h6>
                                    <span class="timeline-time">
                                        <i class="fa fa-clock-o"></i>
                                        <?php echo date('d/m/Y H:i:s', strtotime($log->created_at)); ?>
                                    </span>
                                </div>

                                <div class="timeline-description">
                                    <?php echo htmlspecialchars($log->action_description); ?>
                                </div>

                                <?php if ($log->action_type == 'STATUS_CHANGE' && $log->old_value && $log->new_value): ?>
                                    <div class="timeline-details">
                                        <?php
                                        $old_data = json_decode($log->old_value, true);
                                        $new_data = json_decode($log->new_value, true);
                                        ?>
                                        <span class="timeline-badge <?php echo $old_data['status'] ? 'badge-' . $old_data['status'] : 'badge-unchanged'; ?>">
                                            Cũ: <?php echo $old_data['status'] ? strtoupper($old_data['status']) : 'CHƯA CHECK'; ?>
                                        </span>
                                        <i class="fa fa-arrow-right" style="color: #9ca3af;"></i>
                                        <span class="timeline-badge badge-<?php echo $new_data['status']; ?>">
                                            Mới: <?php echo strtoupper($new_data['status']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if (($log->action_type == 'IMAGE_UPLOAD' || $log->action_type == 'IMAGE_DELETE') && $log->image_filename): ?>
                                    <div class="timeline-details">
                                        <span style="font-size: 12px; color: #718096;">
                                            <i class="fa fa-file-image-o"></i>
                                            <?php echo htmlspecialchars($log->image_filename); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="timeline-user">
                                    <i class="fa fa-user-circle"></i>
                                    <span><?php echo htmlspecialchars($log->staff_name); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer" style="border-top: 2px solid #e2e8f0; background: #f8fafc;">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> Đóng
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Modal is already shown by tnhModal
    });
</script>
