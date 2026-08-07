<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$checklist_data = json_decode($checklist->checklist_data ?? '{}', true) ?: [];
?>

<style>
    /* Modal Setup */
    .checklist-modal .modal-dialog {
        width: 90%;
        max-width: 1400px;
        height: 95vh;
        margin: 2.5vh auto;
    }

    .checklist-modal .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        border-radius: 12px;
        overflow: hidden;
    }

    .checklist-modal .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        background: #f9fafb;
    }

    /* Breadcrumb - Simple Style */
    .progress-breadcrumb {
        background: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 16px 24px;
    }

    .breadcrumb-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 900px;
        margin: 0 auto;
    }

    .breadcrumb-step {
        display: flex;
        align-items: center;
    }

    .step-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 11px;
        border: 1px solid #d1d5db;
        background: white;
        color: #9ca3af;
    }

    .step-circle.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .step-circle.completed {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }

    .step-label {
        margin-left: 8px;
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }

    .step-label.active {
        color: #1f2937;
        font-weight: 700;
    }

    .step-connector {
        width: 32px;
        height: 2px;
        background: #e5e7eb;
        margin: 0 8px;
    }

    .step-connector.completed {
        background: #10b981;
    }

    /* Avatar Preview */
    .avatar-preview-box {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }

    .avatar-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-preview-box .no-avatar {
        text-align: center;
        color: #9ca3af;
    }

    .avatar-preview-box .no-avatar i {
        font-size: 36px;
        margin-bottom: 8px;
    }

    /* File Preview List */
    .file-attachments {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .file-attachments-title {
        font-weight: 600;
        font-size: 13px;
        color: #374151;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .file-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 6px;
        transition: all 0.2s;
    }

    .file-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .file-item-icon {
        font-size: 20px;
        margin-right: 10px;
    }

    .file-item-info {
        flex: 1;
        min-width: 0;
    }

    .file-item-name {
        font-size: 13px;
        font-weight: 500;
        color: #2563eb;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-decoration: none;
        display: block;
    }

    .file-item-name:hover {
        text-decoration: underline;
    }

    .file-item-meta {
        font-size: 11px;
        color: #6b7280;
    }

    /* Content */
    .checklist-content {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Info Card */
    .info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .info-card-header {
        background: #f9fafb;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        border-bottom: 1px solid #e5e7eb;
    }

    .info-card-header:hover {
        background: #f3f4f6;
    }

    .info-card-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #374151;
    }

    .info-card-title i {
        color: #6b7280;
    }

    .status-tag {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        background: #e5e7eb;
        color: #6b7280;
        margin-left: 8px;
    }

    .status-tag.success {
        background: #d1fae5;
        color: #065f46;
    }

    .info-card-body {
        padding: 16px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        font-size: 13px;
    }

    .info-item label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    .info-item div {
        font-weight: 600;
        color: #1f2937;
        margin-top: 2px;
    }

    .btn-supplement {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 12px;
    }

    .btn-supplement:hover {
        background: #dbeafe;
    }

    /* Gate Cards */
    .gate-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-top: 24px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .gate-card.locked {
        opacity: 0.7;
    }

    .gate-card.locked .gate-header,
    .gate-card.locked .gate-body {
        filter: blur(1px);
    }

    .lock-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .lock-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .gate-header {
        padding: 12px 16px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
    }

    .gate-header.gate-1 {
        background: #068b2e;
    }

    .gate-header.gate-1.completed {
        background: #059669;
    }

    .gate-header.gate-2 {
        background: #6b21a8;
    }

    .gate-header.gate-3 {
        background: #047857;
    }

    .gate-header>div {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .gate-badge {
        font-size: 11px;
        padding: 4px 8px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }

    .gate-body {
        padding: 16px;
    }

    /* Checklist Items */
    .checklist-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .checklist-item:hover {
        background: #f9fafb;
    }

    .checklist-item.checked {
        background: #ecfdf5;
        border-color: #6ee7b7;
    }

    .checklist-item.checked .checklist-label {
        color: #047857;
        font-weight: 600;
    }

    .checklist-item.disabled-item {
        background: #fef2f2;
        border-color: #fca5a5;
        opacity: 0.8;
    }

    .checklist-item.disabled-item:hover {
        background: #fee2e2;
    }

    .checklist-label {
        font-size: 13px;
        color: #4b5563;
    }

    /* Buttons */
    .btn-gate {
        width: 100%;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-gate:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-gate:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-gate-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .btn-gate-purple {
        background: linear-gradient(135deg, #7c3aed, #6b21a8);
        color: white;
    }

    .btn-gate-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    /* Form Controls */
    .form-control-modern {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .employee-id-display {
        background: #f5f3ff;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #c4b5fd;
        text-align: center;
    }

    .employee-id-display .label {
        font-size: 11px;
        color: #6b21a8;
        font-weight: 700;
        text-transform: uppercase;
    }

    .employee-id-display .value {
        font-size: 20px;
        font-weight: 700;
        color: #6b21a8;
        margin: 4px 0;
    }

    .employee-id-display .sub {
        font-size: 11px;
        color: #7c3aed;
    }

    /* Completed State */
    .completed-box {
        text-align: center;
        padding: 20px;
        color: #059669;
    }

    .completed-box i {
        font-size: 48px;
        margin-bottom: 8px;
    }

    .completed-box .title {
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .completed-box .date {
        font-size: 12px;
        color: #6b7280;
    }

    /* Sidebar */
    .sidebar-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .sidebar-title {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sidebar-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 12px;
    }

    .sidebar-item:last-child {
        border-bottom: none;
    }

    .sidebar-item .label {
        color: #6b7280;
    }

    .sidebar-item .value {
        font-weight: 600;
        color: #1f2937;
    }

    /* Missing Info Alert */
    .missing-info {
        background: #fef3c7;
        border: 1px solid #fbbf24;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
        font-size: 12px;
        color: #92400e;
    }

    .missing-info i {
        color: #f59e0b;
        margin-right: 6px;
    }

    /* Highlight animation */
    .info-item.highlight-updated {
        animation: highlightPulse 2s ease-in-out;
    }

    @keyframes highlightPulse {
        0% {
            background-color: transparent;
        }

        50% {
            background-color: #dbeafe;
        }

        100% {
            background-color: transparent;
        }
    }

    /* Supplement Modal */
    #supplement-modal .modal-dialog {
        max-width: 700px;
        margin: 50px auto;
    }

    #supplement-modal .modal-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 16px 20px;
    }

    #supplement-modal .modal-title {
        font-weight: 700;
        font-size: 16px;
    }

    #supplement-modal .form-group {
        margin-bottom: 16px;
    }

    #supplement-modal .form-group label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        color: #374151;
        margin-bottom: 6px;
    }

    #supplement-modal .form-group label .required {
        color: #dc3545;
    }

    #supplement-modal .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
    }

    #supplement-modal .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<div class="modal-dialog" style="width: 70%; max-width: 1800px; height: 95vh; margin: 2.5vh auto;">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header" style="color: white; padding: 16px 20px;">
            <h4 class="modal-title"
                style="font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-shield"></i>
                Chi tiết: <?= $checklist->ho_ten ?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">
                <span>&times;</span>
            </button>
        </div>

        <!-- Breadcrumb -->
        <div class="progress-breadcrumb">
            <div class="breadcrumb-steps">
                <?php
                $current_status = $checklist->status;
                $steps = [
                    ['code' => 'S6', 'label' => 'S6: Đang Đối Chiếu'],
                    ['code' => 'S7', 'label' => 'S7: Đã Check'],
                    ['code' => 'S8', 'label' => 'S8: Thử Việc'],
                    ['code' => 'S9', 'label' => 'S9: Chính Thức']
                ];
                $current_index = array_search($current_status, array_column($steps, 'code'));

                foreach ($steps as $index => $step):
                    $is_completed = $index < $current_index;
                    $is_active = $index == $current_index;
                ?>
                    <div class="breadcrumb-step">
                        <div class="step-circle <?= $is_active ? 'active' : ($is_completed ? 'completed' : '') ?>">
                            <?= $index + 1 ?>
                        </div>
                        <div class="step-label <?= $is_active ? 'active' : '' ?>">
                            <?= $step['label'] ?>
                        </div>
                        <?php if ($index < count($steps) - 1): ?>
                            <div class="step-connector <?= $is_completed ? 'completed' : '' ?>"></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Body -->
        <div class="modal-body">
            <div class="checklist-content">
                <div style="display: grid; grid-template-columns: 1fr 420px; gap: 24px;">
                    <!-- Left: Main Content -->
                    <div>
                        <div style="display: flex; justify-between; align-items: center; margin-bottom: 16px;">
                            <h2 style="font-size: 20px; font-weight: 700; color: #1f2937;"><?= $checklist->ho_ten ?>
                            </h2>
                            <span style="font-size: 13px; color: #6b7280;"><?= $checklist->position ?></span>
                        </div>

                        <!-- Candidate Info -->
                        <div class="info-card" id="info-card">
                            <div class="info-card-header" onclick="toggleInfo()">
                                <div class="info-card-title">
                                    <i class="fa fa-user"></i>
                                    Thông tin Hồ Sơ (S6)
                                    <?php if ($current_status != 'S6'): ?>
                                        <span class="status-tag success"><i class="fa fa-check-circle"></i> Đã lưu</span>
                                    <?php endif; ?>
                                </div>
                                <i class="fa fa-chevron-down" id="info-icon"></i>
                            </div>
                            <div class="info-card-body" id="info-body"
                                style="<?= $current_status != 'S6' ? 'display:none;' : '' ?>">
                                <?php
                                // Check missing info
                                $missing = [];
                                if (empty($candidate_data['phone_number']))
                                    $missing[] = 'Số điện thoại';
                                if (empty($candidate_data['email']))
                                    $missing[] = 'Email';
                                if (empty($candidate_data['date_of_birth']))
                                    $missing[] = 'Ngày sinh';
                                if (empty($candidate_data['id_card']))
                                    $missing[] = 'CMND/CCCD';

                                if (!empty($missing)):
                                ?>
                                    <div class="missing-info">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Thiếu thông tin:</strong> <?= implode(', ', $missing) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Avatar Preview -->
                                <?php if (!empty($candidate_data['avatar'])): ?>
                                    <div style="margin-bottom: 16px;">
                                        <div class="avatar-preview-box">
                                            <img src="<?= base_url($candidate_data['avatar']) ?>" alt="Avatar">
                                        </div>
                                        <div style="text-align: center; font-size: 11px; color: #6b7280;">
                                            <i class="fa fa-user-circle"></i> Ảnh đại diện
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Họ tên</label>
                                        <div><?= $candidate_data['full_name'] ?? $checklist->ho_ten ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày sinh</label>
                                        <div><?= $candidate_data['date_of_birth'] ? date('d/m/Y', strtotime($candidate_data['date_of_birth'])) : '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Email</label>
                                        <div><?= $candidate_data['email'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>SĐT</label>
                                        <div><?= $candidate_data['phone_number'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>CMND/CCCD</label>
                                        <div><?= $candidate_data['id_card'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày cấp</label>
                                        <div><?= $candidate_data['date_of_issue'] ? date('d/m/Y', strtotime($candidate_data['date_of_issue'])) : '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Giới tính</label>
                                        <div><?= $candidate_data['gender'] == 'male' ? 'Nam' : ($candidate_data['gender'] == 'female' ? 'Nữ' : '—') ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Tình trạng hôn nhân</label>
                                        <div><?= $candidate_data['marital_status'] == 'marriage' ? 'Đã kết hôn' : ($candidate_data['marital_status'] == 'single' ? 'Độc thân' : '—') ?></div>
                                    </div>
                                    <div class="info-item" style="grid-column: span 2;">
                                        <label>Địa chỉ hiện tại</label>
                                        <div><?= $candidate_data['current_address'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item" style="grid-column: span 2;">
                                        <label>Địa chỉ thường trú</label>
                                        <div><?= $candidate_data['permanent_address'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Trường đào tạo</label>
                                        <div><?= $candidate_data['training_school'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Xếp loại</label>
                                        <div><?php
                                                $ranking = $candidate_data['academic_ranking'] ?? '';
                                                echo $ranking == 'excellent' ? 'Xuất sắc' : ($ranking == 'good' ? 'Giỏi' : ($ranking == 'fair' ? 'Khá' : ($ranking == 'average' ? 'Trung bình' : '—')));
                                                ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Kinh nghiệm</label>
                                        <div><?= ($candidate_data['years_of_experience'] ?? 0) . ' năm' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Công ty cũ</label>
                                        <div><?= $candidate_data['the_company_did'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item" style="grid-column: span 2;">
                                        <label>Vị trí ứng tuyển</label>
                                        <div><?= $candidate_data['job_title'] ?? '—' ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Lương mong muốn</label>
                                        <div style="color: #d97706; font-weight: 700;">
                                            <?= !empty($candidate_data['expected_salary']) ? number_format($candidate_data['expected_salary']) . ' VNĐ' : '—' ?>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <label>Lương Offer (P1 + P2 + P3 + Phụ Cấp P3)</label>
                                        <div style="color: #059669; font-weight: 700;">
                                            <?php
                                            $total_offer = ($offer_data['luong_p1'] ?? 0) + ($offer_data['luong_p2'] ?? 0) + ($offer_data['luong_p3'] ?? 0) + ($offer_data['phu_cap'] ?? 0);
                                            echo $total_offer > 0 ? number_format($total_offer) . ' VNĐ' : '—';
                                            ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($candidate_data['achievements'])): ?>
                                        <div class="info-item" style="grid-column: span 2; padding-top: 8px; border-top: 1px solid #f3f4f6; margin-top: 8px;">
                                            <label>Thành tựu</label>
                                            <div><?= $candidate_data['achievements'] ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($candidate_data['info_other'])): ?>
                                        <div class="info-item" style="grid-column: span 2;">
                                            <label>Thông tin khác</label>
                                            <div><?= $candidate_data['info_other'] ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($candidate_data['cv_link'])): ?>
                                        <div class="info-item" style="grid-column: span 2;">
                                            <label>CV Link</label>
                                            <div><a href="<?= $candidate_data['cv_link'] ?>" target="_blank" style="color: #2563eb;"><i class="fa fa-external-link"></i> Xem CV</a></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- File Attachments -->
                                <?php if (!empty($attachments) && is_array($attachments)): ?>
                                    <div class="file-attachments">
                                        <div class="file-attachments-title">
                                            <i class="fa fa-paperclip"></i>
                                            Hồ sơ đính kèm (<?= count($attachments) ?> file)
                                        </div>
                                        <?php foreach ($attachments as $file):
                                            $ext = strtolower($file['external'] ?? '');
                                            $icon = '📄';
                                            switch ($ext) {
                                                case 'pdf':
                                                    $icon = '📕';
                                                    break;
                                                case 'doc':
                                                case 'docx':
                                                    $icon = '📘';
                                                    break;
                                                case 'xls':
                                                case 'xlsx':
                                                    $icon = '📗';
                                                    break;
                                                case 'png':
                                                case 'jpg':
                                                case 'jpeg':
                                                case 'gif':
                                                    $icon = '🖼️';
                                                    break;
                                            }
                                        ?>
                                            <div class="file-item">
                                                <div class="file-item-icon"><?= $icon ?></div>
                                                <div class="file-item-info">
                                                    <a href="<?= base_url($file['external_link']) ?>" target="_blank" class="file-item-name">
                                                        <?= htmlspecialchars($file['file_name']) ?>
                                                    </a>
                                                    <div class="file-item-meta">
                                                        <?= strtoupper($ext) ?>
                                                        <?php if (!empty($file['filetype'])): ?>
                                                            • <?= $file['filetype'] ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <a href="<?= base_url($file['external_link']) ?>" download class="btn btn-sm" style="padding: 4px 8px; font-size: 11px;">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($missing)): ?>
                                    <button type="button" class="btn-supplement" onclick="openSupplementForm()">
                                        <i class="fa fa-plus-circle"></i>
                                        Bổ sung thông tin còn thiếu
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Gate 2: Employee Creation -->
                        <div id="gate-2" class="gate-card <?= $current_status == 'S6' ? 'locked' : '' ?>">
                            <?php if ($current_status == 'S6'): ?>
                                <div class="lock-overlay">
                                    <div class="lock-badge">
                                        <i class="fa fa-lock"></i>
                                        Hoàn thành Checklist để mở khóa
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div
                                class="gate-header gate-2 <?= in_array($current_status, ['S8', 'S9']) ? 'completed' : '' ?>">
                                <div>
                                    <i class="fa fa-user-plus"></i>
                                    <span>Tạo Nhân Sự (S7 → S8)</span>
                                </div>
                                <span class="gate-badge">Gate 2</span>
                            </div>
                            <div class="gate-body">
                                <?php if (in_array($current_status, ['S8', 'S9'])): ?>
                                    <div class="employee-id-display">
                                        <div class="label">Mã Nhân Viên</div>
                                        <div class="value"><?= $checklist->employee_id ?></div>
                                        <div class="sub">
                                            <?= $current_status == 'S9' ? 'Nhân sự chính thức' : 'Đang thử việc' ?></div>
                                    </div>
                                <?php elseif ($current_status == 'S7'): ?>
                                    <div style="margin-bottom: 12px;">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #6b7280; display: block; margin-bottom: 6px;">Mã
                                            NV dự kiến</label>
                                        <input type="text" id="employee_id_input" class="form-control-modern"
                                            value="NV<?= date('Y') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) ?>">
                                    </div>
                                    <button type="button" class="btn-gate btn-gate-purple" onclick="createEmployee()">
                                        <i class="fa fa-plus-circle"></i>
                                        Tạo Hồ Sơ Thử Việc
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Gate 3: Probation -->
                        <div id="gate-3" class="gate-card <?= !in_array($current_status, ['S8', 'S9']) ? 'locked' : '' ?>">
                            <?php if (!in_array($current_status, ['S8', 'S9'])): ?>
                                <div class="lock-overlay">
                                    <div class="lock-badge">
                                        <i class="fa fa-lock"></i>
                                        Chưa đến thời hạn đánh giá
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="gate-header gate-3 <?= $current_status == 'S9' ? 'completed' : '' ?>">
                                <div>
                                    <i class="fa fa-trophy"></i>
                                    <span>Đánh Giá Thử Việc (Gate 3)</span>
                                </div>
                            </div>
                            <div class="gate-body">
                                <?php if ($current_status == 'S9'): ?>
                                    <div class="completed-box">
                                        <i class="fa fa-trophy"></i>
                                        <div class="title">Nhân sự Chính thức</div>
                                        <div class="date">Hoàn thành:
                                            <?= date('d/m/Y H:i', strtotime($checklist->finalized_date ?? $checklist->date_update)) ?>
                                        </div>
                                    </div>
                                <?php elseif ($current_status == 'S8'): ?>
                                    <?php
                                    $start_date = $checklist->created_employee_date ?? $checklist->date_update;
                                    $days_count = floor((time() - strtotime($start_date)) / 86400);
                                    ?>
                                    <div class="probation-info" style="margin-bottom: 15px; padding: 12px; background: #f0f9ff; border-radius: 6px; border-left: 3px solid #3b82f6;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                            <i class="fa fa-clock-o" style="color: #3b82f6; font-size: 16px;"></i>
                                            <span style="font-weight: 600; color: #1e40af;">Thời gian thử việc</span>
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: #3b82f6; margin-bottom: 4px;">
                                            <?= $days_count ?> ngày
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">
                                            Bắt đầu: <?= date('d/m/Y', strtotime($start_date)) ?>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-gate btn-gate-success" onclick="convertToFullTime()" style="width: 100%;">
                                        <i class="fa fa-check-circle"></i>
                                        Chuyển Nhân Viên Chính Thức
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Sidebar -->
                    <div>
                        <div class="sidebar-card">
                            <div class="sidebar-title">
                                <i class="fa fa-info-circle"></i>
                                Thông tin hệ thống
                            </div>
                            <div class="sidebar-item">
                                <span class="label">Mã Checklist</span>
                                <span class="value"
                                    style="color: #2563eb; font-family: monospace;"><?= $checklist->ma_checklist ?></span>
                            </div>
                            <div class="sidebar-item">
                                <span class="label">Mã Offer</span>
                                <span class="value"
                                    style="color: #2563eb; font-family: monospace;"><?= $checklist->ma_offer ?></span>
                            </div>
                            <div class="sidebar-item">
                                <span class="label">Người tạo</span>
                                <span class="value"><?= $checklist->nguoi_tao ?></span>
                            </div>
                            <div class="sidebar-item">
                                <span class="label">Ngày tạo</span>
                                <span class="value"><?= date('d/m/Y', strtotime($checklist->ngay_tao)) ?></span>
                            </div>
                        </div>
                        <!-- Gate 1: HR Checklist -->
                        <div class="gate-card" id="gate-1" style="margin-top: 0;">
                            <div
                                class="gate-header gate-1 <?= in_array($current_status, ['S7', 'S8', 'S9']) ? 'completed' : '' ?>">
                                <div>
                                    <i class="fa fa-shield"></i>
                                    <span>Checklist</span>
                                </div>
                                <span class="gate-badge">Gate 1</span>
                            </div>
                            <div class="gate-body">
                                <?php if (in_array($current_status, ['S7', 'S8', 'S9'])): ?>
                                    <div class="completed-box">
                                        <i class="fa fa-check-circle"></i>
                                        <div class="title">Đã hoàn thành</div>
                                        <div class="date">Duyệt lúc:
                                            <?= date('d/m/Y H:i', strtotime($checklist->approved_s7_date ?? $checklist->date_update)) ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php
                                    $items = [
                                        'ho_so_phap_ly_day_du' => '1. Hồ sơ pháp lý (CMND/CCCD - d93)',
                                        'bang_cap_cong_chung' => '2. Bằng cấp công chứng',
                                        'bhxh_detail_valid' => '3. Thông tin BHXH',
                                        'tai_khoan_ngan_hang_exist' => '4. TK Ngân hàng',
                                        'luong_p1_p2_valid' => '5. Lương đúng Offer'
                                    ];

                                    // Kiểm tra đã điền CMND/CCCD chưa
                                    $has_id_card = !empty($candidate_data['id_card']) && !empty($candidate_data['date_of_issue']);
                                    ?>
                                    <?php foreach ($items as $key => $label): ?>
                                        <div class="checklist-item <?= ($checklist_data[$key] ?? false) ? 'checked' : '' ?> <?= ($key === 'ho_so_phap_ly_day_du' && !$has_id_card) ? 'disabled-item' : '' ?>"
                                            onclick="toggleItem('<?= $key ?>')">
                                            <span class="checklist-label"><?= $label ?></span>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <?php if ($key === 'ho_so_phap_ly_day_du' && !$has_id_card): ?>
                                                    <span style="font-size: 11px; color: #dc2626; background: #fee2e2; padding: 2px 8px; border-radius: 4px;">
                                                        <i class="fa fa-exclamation-triangle"></i> Chưa điền CMND
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($checklist_data[$key] ?? false): ?>
                                                    <i class="fa fa-check-circle" style="color: #10b981; font-size: 16px;"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="button" class="btn-gate btn-gate-primary" id="btn-approve-s7"
                                        onclick="approveS7()" disabled>
                                        <i class="fa fa-check-circle"></i>
                                        Xác nhận ĐẠT (Sang S7)
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>



                        <div class="sidebar-card" style="margin-top: 10px;">
                            <div class="sidebar-title">
                                <i class="fa fa-clock-o"></i>
                                Tiến độ xử lý
                            </div>
                            <div
                                style="position: relative; padding-left: 24px; border-left: 2px solid #e5e7eb; margin-left: 8px;">
                                <?php
                                $timeline = [
                                    ['status' => 'S6', 'label' => 'Tạo Checklist', 'date' => $checklist->ngay_tao],
                                    ['status' => 'S7', 'label' => 'Duyệt Checklist', 'date' => $checklist->approved_s7_date],
                                    ['status' => 'S8', 'label' => 'Tạo Nhân Sự', 'date' => $checklist->created_employee_date],
                                    ['status' => 'S9', 'label' => 'Chính Thức', 'date' => $checklist->finalized_date]
                                ];

                                foreach ($timeline as $item):
                                    $is_done = array_search($item['status'], array_column($steps, 'code')) < $current_index || $item['status'] == $current_status;
                                ?>
                                    <div style="margin-bottom: 20px; position: relative;">
                                        <div style="position: absolute; left: -32px; width: 16px; height: 16px; border-radius: 50%; 
                                                    border: 3px solid <?= $is_done ? '#10b981' : '#e5e7eb' ?>; 
                                                    background: <?= $is_done ? '#10b981' : 'white' ?>;"></div>
                                        <div
                                            style="font-size: 12px; font-weight: 700; color: <?= $is_done ? '#059669' : '#9ca3af' ?>;">
                                            <?= $item['label'] ?>
                                        </div>
                                        <div style="font-size: 11px; color: #6b7280;">
                                            <?= $item['date'] ? date('d/m/Y H:i', strtotime($item['date'])) : '—' ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer" style="background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 12px 16px;">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> Đóng
            </button>
        </div>
    </div>
</div>

<!-- Supplement Info Modal -->
<div class="modal fade" id="supplement-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-edit"></i> Bổ sung thông tin ứng viên
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <form id="supplement-form">
                    <!-- I. Thông tin cơ bản -->
                    <div style="background: #f8f9fa; padding: 12px 16px; border-left: 4px solid #3b82f6; margin-bottom: 16px;">
                        <strong style="color: #1f2937;">I. Thông tin cơ bản</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Họ và tên <span class="required">*</span></label>
                                <input type="text" class="form-control" name="full_name" id="sup_full_name" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                                <small class="text-muted">Không thể sửa tên từ đây</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ngày sinh <span class="required">*</span></label>
                                <input type="date" class="form-control" name="date_of_birth" id="sup_date_of_birth" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Số điện thoại <span class="required">*</span></label>
                                <input type="tel" class="form-control" name="phone_number" id="sup_phone_number" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                                <small class="text-muted">Không thể sửa SDT từ đây</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" class="form-control" name="email" id="sup_email" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Giới tính</label>
                                <div style="padding-top: 7px;">
                                    <label style="margin-right: 20px; font-weight: normal; cursor: pointer;">
                                        <input type="radio" name="gender" value="male" id="sup_gender_male" checked style="margin-right: 5px;"> Nam
                                    </label>
                                    <label style="font-weight: normal; cursor: pointer;">
                                        <input type="radio" name="gender" value="female" id="sup_gender_female" style="margin-right: 5px;"> Nữ
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tình trạng hôn nhân</label>
                                <select class="form-control" name="marital_status" id="sup_marital_status">
                                    <option value="">-- Chọn --</option>
                                    <option value="single">Độc thân</option>
                                    <option value="marriage">Đã kết hôn</option>
                                    <option value="divorced">Ly hôn</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ hiện tại</label>
                        <textarea class="form-control" name="current_address" id="sup_current_address" rows="2" placeholder="Nhập địa chỉ hiện tại"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ thường trú</label>
                        <textarea class="form-control" name="permanent_address" id="sup_permanent_address" rows="2" placeholder="Nhập địa chỉ thường trú"></textarea>
                    </div>

                    <!-- II. Thông tin giấy tờ -->
                    <div style="background: #f8f9fa; padding: 12px 16px; border-left: 4px solid #3b82f6; margin-bottom: 16px; margin-top: 20px;">
                        <strong style="color: #1f2937;">II. Thông tin giấy tờ</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>CMND/CCCD <span class="required">*</span></label>
                                <input type="text" class="form-control" name="id_card" id="sup_id_card" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ngày cấp</label>
                                <input type="date" class="form-control" name="date_of_issue" id="sup_date_of_issue">
                            </div>
                        </div>
                    </div>

                    <!-- III. Trình độ học vấn -->
                    <div style="background: #f8f9fa; padding: 12px 16px; border-left: 4px solid #3b82f6; margin-bottom: 16px; margin-top: 20px;">
                        <strong style="color: #1f2937;">III. Trình độ học vấn</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Trình độ học vấn</label>
                                <select class="form-control" name="educational" id="sup_educational">
                                    <option value="">-- Chọn trình độ --</option>
                                    <option value="thpt">THPT</option>
                                    <option value="trung_cap">Trung cấp</option>
                                    <option value="cao_dang">Cao đẳng</option>
                                    <option value="dai_hoc">Đại học</option>
                                    <option value="thac_si">Thạc sĩ</option>
                                    <option value="tien_si">Tiến sĩ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Trường đào tạo</label>
                                <input type="text" class="form-control" name="training_school" id="sup_training_school" placeholder="Tên trường">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Xếp loại</label>
                        <select class="form-control" name="academic_ranking" id="sup_academic_ranking">
                            <option value="">-- Chọn xếp loại --</option>
                            <option value="excellent">Xuất sắc</option>
                            <option value="good">Giỏi</option>
                            <option value="fair">Khá</option>
                            <option value="average">Trung bình</option>
                        </select>
                    </div>

                    <!-- IV. Kinh nghiệm làm việc -->
                    <div style="background: #f8f9fa; padding: 12px 16px; border-left: 4px solid #3b82f6; margin-bottom: 16px; margin-top: 20px;">
                        <strong style="color: #1f2937;">IV. Kinh nghiệm làm việc</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Số năm kinh nghiệm</label>
                                <input type="number" class="form-control" name="years_of_experience" id="sup_years_of_experience" min="0" step="0.5" placeholder="Ví dụ: 2.5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Công ty đã làm</label>
                                <input type="text" class="form-control" name="the_company_did" id="sup_the_company_did" placeholder="Tên công ty gần nhất">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Chức danh/Vị trí ứng tuyển</label>
                        <input type="text" class="form-control" name="job_title" id="sup_job_title" placeholder="Ví dụ: Nhân viên kế toán">
                    </div>

                    <div class="form-group">
                        <label>Thành tựu nổi bật</label>
                        <textarea class="form-control" name="achievements" id="sup_achievements" rows="2" placeholder="Các thành tích đáng chú ý"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Thông tin khác</label>
                        <textarea class="form-control" name="info_other" id="sup_info_other" rows="2" placeholder="Các thông tin bổ sung khác"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Đóng
                </button>
                <button type="button" class="btn btn-primary" onclick="submitSupplementForm()">
                    <i class="fa fa-save"></i> Lưu thông tin
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var checklistId = <?= $checklist->id ?>;
    var checklistData = <?= json_encode($checklist_data) ?>;
    var currentStatus = '<?= $checklist->status ?>';
    var candidateData = <?= json_encode($candidate_data) ?>;
    var offerData = <?= json_encode($offer_data ?? []) ?>;

    function toggleInfo() {
        $('#info-body').slideToggle();
        $('#info-icon').toggleClass('fa-chevron-down fa-chevron-up');
    }

    // Kiểm tra đã điền CMND/CCCD chưa
    function checkIdCardComplete() {
        return !!(candidateData.id_card && candidateData.date_of_issue);
    }

    function toggleItem(key) {
        if (currentStatus !== 'S6') return;

        // Validation cho mục hồ sơ pháp lý
        if (key === 'ho_so_phap_ly_day_du' && !checklistData[key]) {
            // Nếu đang muốn check (từ unchecked -> checked)
            if (!checkIdCardComplete()) {
                alert('⚠️ Chưa điền đầy đủ thông tin CMND/CCCD!\n\nVui lòng bổ sung thông tin CMND/CCCD và Ngày cấp trước khi check mục này.');
                return; // Không cho phép check
            }
        }

        // Validation cho mục lương đúng offer
        if (key === 'luong_p1_p2_valid' && !checklistData[key]) {
            var expectedSalary = parseFloat(candidateData.expected_salary) || 0;
            var offerP1 = parseFloat(offerData.luong_p1) || 0;
            var offerP2 = parseFloat(offerData.luong_p2) || 0;
            var totalOffer = offerP1 + offerP2;

            if (expectedSalary > totalOffer && totalOffer > 0) {
                var difference = expectedSalary - totalOffer;
                var confirm = window.confirm(
                    '⚠️ CẢNH BÁO: Lương mong muốn cao hơn Lương Offer!\n\n' +
                    '• Lương mong muốn: ' + expectedSalary.toLocaleString('vi-VN') + ' VNĐ\n' +
                    '• Lương Offer (P1+P2): ' + totalOffer.toLocaleString('vi-VN') + ' VNĐ\n' +
                    '• Chênh lệch: ' + difference.toLocaleString('vi-VN') + ' VNĐ\n\n' +
                    'Bạn có chắc chắn muốn check mục này?'
                );
                if (!confirm) {
                    return; // Không check nếu user chọn Cancel
                }
            }
        }

        checklistData[key] = !checklistData[key];

        var item = $('[onclick="toggleItem(\'' + key + '\')"]');
        if (checklistData[key]) {
            item.addClass('checked');
            if (!item.find('.fa-check-circle').length) {
                item.find('.checklist-label').after('<i class="fa fa-check-circle" style="color: #10b981; font-size: 16px;"></i>');
            }
        } else {
            item.removeClass('checked');
            item.find('.fa-check-circle').remove();
        }

        var allChecked = Object.values(checklistData).every(v => v === true);
        $('#btn-approve-s7').prop('disabled', !allChecked);
    }

    function openSupplementForm() {
        // Fill existing data
        var candidateData = <?= json_encode($candidate_data) ?>;

        // Thông tin cơ bản
        $('#sup_full_name').val(candidateData.full_name || '');
        $('#sup_date_of_birth').val(candidateData.date_of_birth || '');
        $('#sup_phone_number').val(candidateData.phone_number || '');
        $('#sup_email').val(candidateData.email || '');

        // Giới tính
        if (candidateData.gender == 'female') {
            $('#sup_gender_female').prop('checked', true);
        } else {
            $('#sup_gender_male').prop('checked', true);
        }

        // Tình trạng hôn nhân
        $('#sup_marital_status').val(candidateData.marital_status || '');

        // Địa chỉ
        $('#sup_current_address').val(candidateData.current_address || '');
        $('#sup_permanent_address').val(candidateData.permanent_address || '');

        // Giấy tờ
        $('#sup_id_card').val(candidateData.id_card || '');
        $('#sup_date_of_issue').val(candidateData.date_of_issue || '');

        // Trình độ
        $('#sup_educational').val(candidateData.educational || '');
        $('#sup_training_school').val(candidateData.training_school || '');
        $('#sup_academic_ranking').val(candidateData.academic_ranking || '');

        // Kinh nghiệm
        $('#sup_years_of_experience').val(candidateData.years_of_experience || '');
        $('#sup_the_company_did').val(candidateData.the_company_did || '');
        $('#sup_job_title').val(candidateData.job_title || '');
        $('#sup_achievements').val(candidateData.achievements || '');
        $('#sup_info_other').val(candidateData.info_other || '');

        // Mở modal và append vào body để tránh conflict
        $('#supplement-modal').appendTo('body').modal('show');
    }

    // Hàm reload thông tin candidate
    function reloadCandidateInfo() {
        $.ajax({
            url: admin_url + 'checklist_profile/getCandidateData/' + checklistId,
            type: 'GET',
            dataType: 'JSON',
            success: function(response) {
                if (response.result == 1 && response.data) {
                    updateCandidateInfoUI(response.data);
                }
            }
        });
    }

    // Hàm cập nhật UI thông tin candidate
    function updateCandidateInfoUI(data) {
        // Cập nhật các trường thông tin trong info-grid
        var infoItems = $('.info-item');

        // Helper function để format date
        function formatDate(dateStr) {
            if (!dateStr) return '—';
            var date = new Date(dateStr);
            return date.toLocaleDateString('vi-VN');
        }

        // Helper function để format gender
        function formatGender(gender) {
            if (gender == 'male') return 'Nam';
            if (gender == 'female') return 'Nữ';
            return '—';
        }

        // Helper function để format marital status
        function formatMaritalStatus(status) {
            if (status == 'marriage') return 'Đã kết hôn';
            if (status == 'single') return 'Độc thân';
            if (status == 'divorced') return 'Ly hôn';
            return '—';
        }

        // Helper function để format academic ranking
        function formatRanking(ranking) {
            if (ranking == 'excellent') return 'Xuất sắc';
            if (ranking == 'good') return 'Giỏi';
            if (ranking == 'fair') return 'Khá';
            if (ranking == 'average') return 'Trung bình';
            return '—';
        }

        // Cập nhật từng trường trong info-grid
        infoItems.each(function() {
            var label = $(this).find('label').text().trim();
            var valueDiv = $(this).find('div').last();

            if (label.includes('Họ tên')) {
                valueDiv.text(data.full_name || '—');
            } else if (label.includes('Ngày sinh')) {
                valueDiv.text(formatDate(data.date_of_birth));
            } else if (label.includes('Email')) {
                valueDiv.text(data.email || '—');
            } else if (label.includes('SĐT')) {
                valueDiv.text(data.phone_number || '—');
            } else if (label.includes('CMND/CCCD')) {
                valueDiv.text(data.id_card || '—');
            } else if (label.includes('Ngày cấp')) {
                valueDiv.text(formatDate(data.date_of_issue));
            } else if (label.includes('Giới tính')) {
                valueDiv.text(formatGender(data.gender));
            } else if (label.includes('Tình trạng hôn nhân')) {
                valueDiv.text(formatMaritalStatus(data.marital_status));
            } else if (label.includes('Địa chỉ hiện tại')) {
                valueDiv.text(data.current_address || '—');
            } else if (label.includes('Địa chỉ thường trú')) {
                valueDiv.text(data.permanent_address || '—');
            } else if (label.includes('Trường đào tạo')) {
                valueDiv.text(data.training_school || '—');
            } else if (label.includes('Xếp loại')) {
                valueDiv.text(formatRanking(data.academic_ranking));
            } else if (label.includes('Kinh nghiệm')) {
                valueDiv.text((data.years_of_experience || 0) + ' năm');
            } else if (label.includes('Công ty cũ')) {
                valueDiv.text(data.the_company_did || '—');
            } else if (label.includes('Vị trí ứng tuyển')) {
                valueDiv.text(data.job_title || '—');
            } else if (label.includes('Thành tựu')) {
                valueDiv.text(data.achievements || '—');
            } else if (label.includes('Thông tin khác')) {
                valueDiv.text(data.info_other || '—');
            }
        });

        // Ẩn/hiện warning nếu còn thiếu thông tin
        var hasMissing = !data.phone_number || !data.email || !data.date_of_birth || !data.id_card;
        if (!hasMissing) {
            $('.missing-info').fadeOut();
            $('.btn-supplement').fadeOut();
        }

        // Cập nhật avatar nếu có
        if (data.avatar) {
            var avatarHtml = '<div style="margin-bottom: 16px;">' +
                '<div class="avatar-preview-box">' +
                '<img src="' + site_url + data.avatar + '" alt="Avatar">' +
                '</div>' +
                '<div style="text-align: center; font-size: 11px; color: #6b7280;">' +
                '<i class="fa fa-user-circle"></i> Ảnh đại diện' +
                '</div>' +
                '</div>';

            if ($('.avatar-preview-box').length == 0) {
                $('.info-grid').before(avatarHtml);
            }
        }

        // Hiệu ứng highlight các trường đã cập nhật
        infoItems.addClass('highlight-updated');
        setTimeout(function() {
            infoItems.removeClass('highlight-updated');
        }, 2000);
    }

    // Hàm cập nhật UI sau khi thay đổi trạng thái
    function updateUIAfterStatusChange(newStatus, employeeId) {
        // Cập nhật breadcrumb
        var steps = ['S6', 'S7', 'S8', 'S9'];
        var currentIndex = steps.indexOf(newStatus);

        $('.breadcrumb-step').each(function(index) {
            var circle = $(this).find('.step-circle');
            var label = $(this).find('.step-label');
            var connector = $(this).find('.step-connector');

            circle.removeClass('active completed');
            label.removeClass('active');
            connector.removeClass('completed');

            if (index < currentIndex) {
                circle.addClass('completed');
                connector.addClass('completed');
            } else if (index == currentIndex) {
                circle.addClass('active');
                label.addClass('active');
            }
        });

        // Cập nhật Gate 1 (Checklist)
        if (newStatus == 'S7' || newStatus == 'S8' || newStatus == 'S9') {
            $('#gate-1 .gate-header').addClass('completed');
            $('#gate-1 .gate-body').html(
                '<div class="completed-box">' +
                '<i class="fa fa-check-circle"></i>' +
                '<div class="title">Đã hoàn thành</div>' +
                '<div class="date">Duyệt lúc: ' + new Date().toLocaleString('vi-VN') + '</div>' +
                '</div>'
            );
            $('.info-card-title .status-tag').remove();
            $('.info-card-title').append('<span class="status-tag success"><i class="fa fa-check-circle"></i> Đã lưu</span>');
            $('#info-body').slideUp();
        }

        // Cập nhật Gate 2 (Tạo nhân sự)
        if (newStatus == 'S7') {
            // Mở lock Gate 2 và hiển thị form tạo nhân sự
            var gate2 = $('#gate-2');
            console.log('Gate 2 found:', gate2.length);
            gate2.removeClass('locked').find('.lock-overlay').remove();
            gate2.find('.gate-body').html(
                '<div style="margin-bottom: 12px;">' +
                '<label style="font-size: 11px; font-weight: 700; color: #6b7280; display: block; margin-bottom: 6px;">Mã NV dự kiến</label>' +
                '<input type="text" id="employee_id_input" class="form-control-modern" value="NV<?= date('Y') ?>' + String(Math.floor(Math.random() * 900) + 100) + '">' +
                '</div>' +
                '<button type="button" class="btn-gate btn-gate-purple" onclick="createEmployee()">' +
                '<i class="fa fa-plus-circle"></i> Tạo Hồ Sơ Thử Việc' +
                '</button>'
            );
            console.log('Gate 2 updated for S7');
        } else if (newStatus == 'S8' || newStatus == 'S9') {
            var gate2 = $('#gate-2');
            gate2.removeClass('locked').find('.lock-overlay').remove();
            gate2.find('.gate-header').addClass('completed');

            if (employeeId) {
                gate2.find('.gate-body').html(
                    '<div class="employee-id-display">' +
                    '<div class="label">Mã Nhân Viên</div>' +
                    '<div class="value">' + employeeId + '</div>' +
                    '<div class="sub">' + (newStatus == 'S9' ? 'Nhân sự chính thức' : 'Đang thử việc') + '</div>' +
                    '</div>'
                );
            }
        }

        // Cập nhật Gate 3 (Đánh giá)
        if (newStatus == 'S8') {
            var gate3 = $('#gate-3');
            gate3.removeClass('locked').find('.lock-overlay').remove();

            // Hiển thị thời gian thử việc và nút chuyển chính thức
            var startDate = new Date();
            var daysCount = 0;
            var probationHtml = '<div class="probation-info" style="margin-bottom: 15px; padding: 12px; background: #f0f9ff; border-radius: 6px; border-left: 3px solid #3b82f6;">' +
                '<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">' +
                '<i class="fa fa-clock-o" style="color: #3b82f6; font-size: 16px;"></i>' +
                '<span style="font-weight: 600; color: #1e40af;">Thời gian thử việc</span>' +
                '</div>' +
                '<div style="font-size: 24px; font-weight: 700; color: #3b82f6; margin-bottom: 4px;">' +
                daysCount + ' ngày</div>' +
                '<div style="font-size: 12px; color: #64748b;">Bắt đầu: ' + startDate.toLocaleDateString('vi-VN') + '</div>' +
                '</div>' +
                '<button type="button" class="btn-gate btn-gate-success" onclick="convertToFullTime()" style="width: 100%;">' +
                '<i class="fa fa-check-circle"></i> Chuyển Nhân Viên Chính Thức</button>';

            gate3.find('.gate-body').html(probationHtml);
        }

        if (newStatus == 'S9') {
            var gate3 = $('#gate-3');
            gate3.removeClass('locked').find('.lock-overlay').remove();
            gate3.find('.gate-header').addClass('completed');
            gate3.find('.gate-body').html(
                '<div class="completed-box">' +
                '<i class="fa fa-trophy"></i>' +
                '<div class="title">Nhân sự Chính thức</div>' +
                '<div class="date">Hoàn thành: ' + new Date().toLocaleString('vi-VN') + '</div>' +
                '</div>'
            );
        }
    }

    function submitSupplementForm() {
        var form = $('#supplement-form');

        // Validate - bỏ qua các trường readonly
        var requiredFields = form.find('input[required]:not([readonly]), select[required], textarea[required]');
        var isValid = true;

        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).focus();
                return false;
            }
        });

        if (!isValid) {
            alert_float('warning', 'Vui lòng điền đầy đủ thông tin bắt buộc');
            return;
        }

        var formData = form.serializeArray();
        var data = {
            checklist_id: checklistId,
            <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
        };

        formData.forEach(function(item) {
            data[item.name] = item.value;
        });

        $.ajax({
            url: admin_url + 'checklist_profile/supplementInfo',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: function() {
                $('.modal-footer .btn-primary').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    $('#supplement-modal').modal('hide');

                    // Reload thông tin candidate
                    reloadCandidateInfo();

                    // Reload table nếu có
                    if (typeof oTable !== 'undefined') {
                        oTable.draw();
                    }
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra, vui lòng thử lại');
            },
            complete: function() {
                $('.modal-footer .btn-primary').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu thông tin');
            }
        });
    }

    function approveS7() {
        if (!confirm('Xác nhận đã kiểm tra đầy đủ và chuyển sang S7?')) return;

        $.ajax({
            url: admin_url + 'checklist_profile/updateStatus/' + checklistId,
            type: 'POST',
            dataType: 'JSON',
            data: {
                status: 'S7',
                checklist_data: JSON.stringify(checklistData),
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);

                    // Cập nhật UI
                    currentStatus = 'S7';
                    updateUIAfterStatusChange('S7');

                    // Reload table nếu có
                    if (typeof oTable !== 'undefined') {
                        oTable.draw();
                    }

                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    function createEmployee() {
        var empId = $('#employee_id_input').val().trim();
        if (!empId) {
            alert_float('warning', 'Vui lòng nhập mã nhân viên');
            return;
        }

        if (!confirm('Tạo hồ sơ với mã: ' + empId + '?')) return;

        // Mở cửa sổ trước khi AJAX để tránh bị chặn popup
        var newWindow = window.open('', '_blank');
        newWindow.document.write('<html><head><title>Đang tải...</title></head><body style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:Arial;"><div style="text-align:center;"><div style="font-size:48px;margin-bottom:20px;">⏳</div><div style="font-size:18px;color:#666;">Đang tạo hồ sơ nhân sự...</div></div></body></html>');

        $.ajax({
            url: admin_url + 'checklist_profile/updateStatus/' + checklistId,
            type: 'POST',
            dataType: 'JSON',
            data: {
                status: 'S8',
                employee_id: empId,
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);

                    // Cập nhật UI
                    currentStatus = 'S8';
                    updateUIAfterStatusChange('S8', empId);

                    // Reload candidate info để cập nhật breadcrumb
                    reloadCandidateInfo();

                    // Chuyển hướng cửa sổ đã mở đến trang staff
                    if (response.staff_id) {
                        newWindow.location.href = admin_url + 'staff/member/' + response.staff_id;
                    } else {
                        newWindow.close();
                    }

                    // Reload table nếu có
                    if (typeof oTable !== 'undefined') {
                        oTable.draw();
                    }
                } else {
                    alert_float('danger', response.message);
                    // Đóng cửa sổ nếu lỗi
                    newWindow.close();
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra khi tạo hồ sơ nhân sự');
                // Đóng cửa sổ nếu lỗi
                newWindow.close();
            }
        });
    }

    function convertToFullTime() {
        if (!confirm('Xác nhận chuyển thành nhân viên chính thức?')) return;

        $.ajax({
            url: admin_url + 'checklist_profile/updateStatus/' + checklistId,
            type: 'POST',
            dataType: 'JSON',
            data: {
                status: 'S9',
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);

                    // Cập nhật UI
                    currentStatus = 'S9';
                    updateUIAfterStatusChange('S9');

                    // Reload candidate info
                    reloadCandidateInfo();

                    // Reload table nếu có
                    if (typeof oTable !== 'undefined') {
                        oTable.draw();
                    }
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra');
            }
        });
    }

    // Hàm cũ - giữ lại nếu cần
    function finalizeEmployee() {
        convertToFullTime();
    }


    $(document).ready(function() {
        // Check initial state
        if (currentStatus === 'S6') {
            var allChecked = Object.values(checklistData).every(v => v === true);
            $('#btn-approve-s7').prop('disabled', !allChecked);
        }

        // Ngăn modal bổ sung đóng modal chính
        $('#supplement-modal').on('show.bs.modal', function(e) {
            e.stopPropagation();
        }).on('hide.bs.modal', function(e) {
            e.stopPropagation();
        }).on('hidden.bs.modal', function(e) {
            e.stopPropagation();
        });

        // Xử lý nút đóng modal bổ sung
        $('#supplement-modal .close, #supplement-modal .btn-default').on('click', function(e) {
            e.stopPropagation();
            $('#supplement-modal').modal('hide');
        });
    });
</script>