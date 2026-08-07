<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    /* Reset & Base */
    * { box-sizing: border-box; }

    /* Modal Full Height */
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
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .checklist-modal .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        background: #f9fafb;
    }

    /* Breadcrumb Progress - Simple như React */
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
        position: relative;
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
        transition: all 0.2s;
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
        margin-left: 14px;
        font-size: 13px;
        font-weight: 600;
        color: #94a3b8;
        transition: all 0.3s;
    }

    .step-label.active {
        color: #1e293b;
        font-weight: 700;
        font-size: 14px;
    }

    .step-connector {
        flex: 1;
        height: 3px;
        background: #e2e8f0;
        margin: 0 20px;
        position: relative;
        overflow: hidden;
        border-radius: 999px;
    }

    .step-connector.completed {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        box-shadow: 0 1px 3px rgba(16, 185, 129, 0.3);
    }

    /* Content Area - Enhanced */
    .checklist-content {
        padding: 32px 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Info Card - Modern Design */
    .info-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        transition: all 0.3s;
    }

    .info-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .info-card.collapsed {
        padding: 18px 24px;
    }

    .info-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }

    .info-card-header:hover {
        background: #f8fafc;
        margin: -18px -24px 18px;
        padding: 18px 24px;
        border-radius: 16px 16px 0 0;
    }

    .info-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 14px;
        color: #1e293b;
    }

    .info-card-title i {
        color: #3b82f6;
        font-size: 16px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .info-item label {
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 6px;
    }

    .info-item div {
        font-size: 15px;
        color: #1e293b;
        font-weight: 600;
    }

    /* Gate Cards */
    .gate-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .gate-card.locked {
        opacity: 0.6;
        pointer-events: none;
        position: relative;
    }

    .gate-card.locked::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(248, 250, 252, 0.9) 100%);
        backdrop-filter: blur(2px);
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
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .gate-header {
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        font-weight: 700;
    }

    .gate-header.gate-1 {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
    }

    .gate-header.gate-2 {
        background: linear-gradient(135deg, #7c3aed 0%, #6b21a8 100%);
    }

    .gate-header.gate-3 {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .gate-header.completed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .gate-body {
        padding: 20px;
    }

    .gate-badge {
        font-size: 10px;
        padding: 4px 12px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Checklist Items - Enhanced */
    .checklist-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .checklist-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #e2e8f0;
        transition: all 0.25s;
    }

    .checklist-item:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateX(4px);
    }

    .checklist-item.checked {
        background: linear-gradient(to right, #ecfdf5 0%, #d1fae5 100%);
        border-color: #6ee7b7;
    }

    .checklist-item.checked::before {
        background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        width: 5px;
    }

    .checklist-item.checked:hover {
        background: linear-gradient(to right, #d1fae5 0%, #a7f3d0 100%);
        border-color: #34d399;
    }

    .checklist-label {
        font-size: 14px;
        color: #475569;
        font-weight: 500;
        transition: all 0.2s;
    }

    .checklist-item.checked .checklist-label {
        color: #047857;
        font-weight: 600;
    }

    /* Form Controls */
    .form-control-modern {
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-control-modern:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Buttons - Enhanced Premium */
    .btn-gate {
        width: 100%;
        padding: 14px 24px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .btn-gate::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-gate:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-gate:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-gate:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-gate:disabled::before {
        display: none;
    }

    .btn-gate-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .btn-gate-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .btn-gate-purple {
        background: linear-gradient(135deg, #7c3aed 0%, #6b21a8 100%);
        color: white;
    }

    /* Employee ID Display */
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
        margin-bottom: 4px;
    }

    .employee-id-display .value {
        font-size: 20px;
        color: #5b21b6;
        font-weight: 900;
        font-family: 'Courier New', monospace;
    }

    .employee-id-display .sub {
        font-size: 12px;
        color: #7c3aed;
        margin-top: 4px;
    }

    /* Status Badge in Content */
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-indicator.success {
        background: #ecfdf5;
        color: #047857;
    }
    .offer-form-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 20px 24px;
        border-radius: 6px 6px 0 0;
        margin: 0;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
    }
</style>

<!-- <div class="modal-dialog checklist-modal"> -->
<div class="modal-dialog" style="width: 70%; max-width: 1800px; height: 95vh; margin: 2.5vh auto;">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header offer-form-header">
            <h4 class="modal-title" style="font-weight: 700; display: flex; align-items: center; gap: 12px;">
                <i class="fa fa-shield"></i>
                Chi tiết Checklist: <?= $checklist->ho_ten ?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">
                <span>&times;</span>
            </button>
        </div>

        <!-- Breadcrumb Progress -->
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
                            <?= $is_completed ? '<i class="fa fa-check"></i>' : ($index + 1) ?>
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
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Left Column: Information & Gates -->
                    <div>
                        <!-- Candidate Info -->
                        <div class="info-card <?= $current_status != 'S6' ? 'collapsed' : '' ?>"
                            id="candidate-info-card">
                            <div class="info-card-header" onclick="toggleCandidateInfo()">
                                <div class="info-card-title">
                                    <i class="fa fa-user"></i>
                                    Thông tin Hồ Sơ (S6)
                                    <?php if ($current_status != 'S6'): ?>
                                        <span class="status-indicator success">
                                            <i class="fa fa-check-circle"></i> Đã lưu
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <i class="fa fa-chevron-down" id="candidate-info-icon"></i>
                            </div>
                            <div id="candidate-info-content"
                                style="<?= $current_status != 'S6' ? 'display:none;' : '' ?>">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Họ tên</label>
                                        <div><?= $candidate_data['ho_ten'] ?? $checklist->ho_ten ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Vị trí</label>
                                        <div><?= $candidate_data['vi_tri'] ?? $checklist->position ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Phòng ban</label>
                                        <div><?= $candidate_data['phong_ban'] ?? $checklist->department ?></div>
                                    </div>
                                    <div class="info-item">
                                        <label>Lương P1</label>
                                        <div><?= number_format($candidate_data['luong_p1'] ?? 0) ?> VNĐ</div>
                                    </div>
                                    <div class="info-item">
                                        <label>Lương P2</label>
                                        <div><?= number_format($candidate_data['luong_p2'] ?? 0) ?> VNĐ</div>
                                    </div>
                                    <div class="info-item">
                                        <label>Tổng thu nhập</label>
                                        <div style="color: #059669; font-size: 16px;">
                                            <?= number_format(($candidate_data['luong_p1'] ?? 0) + ($candidate_data['luong_p2'] ?? 0) + ($candidate_data['phu_cap'] ?? 0)) ?>
                                            VNĐ
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gate 1: HR Checklist (S6 -> S7) -->
                        <div class="gate-card" id="gate-1">
                            <div
                                class="gate-header gate-1 <?= in_array($current_status, ['S7', 'S8', 'S9']) ? 'completed' : '' ?>">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa fa-shield"></i>
                                    <span>Gate 1: HR Checklist (S6 → S7)</span>
                                </div>
                                <span class="gate-badge">Đối chiếu hồ sơ</span>
                            </div>
                            <div class="gate-body">
                                <?php if (in_array($current_status, ['S7', 'S8', 'S9'])): ?>
                                    <div style="text-align: center; padding: 20px; color: #059669;">
                                        <i class="fa fa-check-circle" style="font-size: 48px; margin-bottom: 12px;"></i>
                                        <div style="font-weight: 700; font-size: 16px;">Đã hoàn thành</div>
                                        <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                                            Duyệt lúc:
                                            <?= date('d/m/Y H:i', strtotime($checklist->approved_s7_date ?? $checklist->date_update)) ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php
                                    $checklist_items = [
                                        'ho_so_phap_ly_day_du' => '1. Hồ sơ pháp lý (CMND, Giấy khám sức khỏe...)',
                                        'bang_cap_cong_chung' => '2. Bằng cấp công chứng',
                                        'bhxh_detail_valid' => '3. Thông tin BHXH hợp lệ',
                                        'tai_khoan_ngan_hang_exist' => '4. Tài khoản ngân hàng',
                                        'luong_p1_p2_valid' => '5. Lương đúng với Offer (P1 + P2)'
                                    ];
                                    ?>
                                    <?php foreach ($checklist_items as $key => $label): ?>
                                        <div class="checklist-item <?= $checklist_data[$key] ? 'checked' : '' ?>"
                                            onclick="toggleChecklistItem('<?= $key ?>')">
                                            <span class="checklist-label"><?= $label ?></span>
                                            <?php if ($checklist_data[$key]): ?>
                                                <i class="fa fa-check-circle" style="color: #10b981; font-size: 18px;"></i>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="button" class="btn-gate btn-gate-primary" id="btn-approve-s7"
                                        onclick="approveS7()" disabled>
                                        <i class="fa fa-check-circle"></i>
                                        Xác nhận ĐẠT (Chuyển sang S7)
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Gate 2: Employee Creation (S7 -> S8) -->
                        <div class="gate-card <?= $current_status == 'S6' ? 'locked' : '' ?>" id="gate-2">
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
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa fa-user-plus"></i>
                                    <span>Gate 2: Tạo Nhân Sự (S7 → S8)</span>
                                </div>
                                <span class="gate-badge">Hồ sơ thử việc</span>
                            </div>
                            <div class="gate-body">
                                <?php if (in_array($current_status, ['S8', 'S9'])): ?>
                                    <div class="employee-id-display">
                                        <div class="label">Mã Nhân Viên</div>
                                        <div class="value"><?= $checklist->employee_id ?></div>
                                        <div class="sub">
                                            <?= $current_status == 'S9' ? 'Nhân sự chính thức' : 'Đang trong giai đoạn thử việc' ?>
                                        </div>
                                    </div>
                                <?php elseif ($current_status == 'S7'): ?>
                                    <div style="margin-bottom: 16px;">
                                        <label
                                            style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block;">
                                            Mã NV dự kiến
                                        </label>
                                        <input type="text" id="employee_id_input" class="form-control-modern"
                                            value="NV<?= date('Y') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) ?>"
                                            placeholder="Nhập mã nhân viên...">
                                    </div>
                                    <button type="button" class="btn-gate btn-gate-purple" onclick="createEmployee()">
                                        <i class="fa fa-plus-circle"></i>
                                        Tạo Hồ Sơ Thử Việc
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Gate 3: Probation Evaluation (S8 -> S9) -->
                        <div class="gate-card <?= !in_array($current_status, ['S8', 'S9']) ? 'locked' : '' ?>"
                            id="gate-3">
                            <?php if (!in_array($current_status, ['S8', 'S9'])): ?>
                                <div class="lock-overlay">
                                    <div class="lock-badge">
                                        <i class="fa fa-lock"></i>
                                        Chưa đến thời hạn đánh giá
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="gate-header gate-3 <?= $current_status == 'S9' ? 'completed' : '' ?>">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa fa-trophy"></i>
                                    <span>Gate 3: Đánh Giá Thử Việc (S8 → S9)</span>
                                </div>
                                <span class="gate-badge">Phê duyệt chính thức</span>
                            </div>
                            <div class="gate-body">
                                <?php if ($current_status == 'S9'): ?>
                                    <div style="text-align: center; padding: 20px; color: #059669;">
                                        <i class="fa fa-trophy" style="font-size: 48px; margin-bottom: 12px;"></i>
                                        <div style="font-weight: 700; font-size: 16px;">Nhân sự Chính thức</div>
                                        <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                                            Hoàn thành:
                                            <?= date('d/m/Y H:i', strtotime($checklist->finalized_date ?? $checklist->date_update)) ?>
                                        </div>
                                    </div>
                                <?php elseif ($current_status == 'S8'): ?>
                                    <div
                                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                        <input type="number" id="kpi_score" class="form-control-modern"
                                            placeholder="KPI (%)" min="0" max="100">
                                        <input type="number" id="culture_score" class="form-control-modern"
                                            placeholder="Điểm Văn hóa" min="0" max="100">
                                    </div>
                                    <textarea id="evaluation_note" class="form-control-modern" rows="3"
                                        placeholder="Nhận xét đánh giá..." style="margin-bottom: 12px;"></textarea>
                                    <button type="button" class="btn-gate btn-gate-success" onclick="finalizeEmployee()">
                                        <i class="fa fa-check-circle"></i>
                                        Phê duyệt Chính thức (S9)
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar Info -->
                    <div>
                        <!-- System Info -->
                        <div class="info-card">
                            <div class="info-card-title" style="margin-bottom: 16px;">
                                <i class="fa fa-info-circle"></i>
                                Thông tin hệ thống
                            </div>
                            <div style="font-size: 13px;">
                                <div
                                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                                    <span style="color: #64748b;">Mã Checklist</span>
                                    <span
                                        style="font-weight: 700; color: #2563eb; font-family: 'Courier New', monospace;">
                                        <?= $checklist->ma_checklist ?>
                                    </span>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                                    <span style="color: #64748b;">Mã Offer</span>
                                    <span
                                        style="font-weight: 700; color: #2563eb; font-family: 'Courier New', monospace;">
                                        <?= $checklist->ma_offer ?>
                                    </span>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                                    <span style="color: #64748b;">Người tạo</span>
                                    <span style="font-weight: 600;"><?= $checklist->nguoi_tao ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                                    <span style="color: #64748b;">Ngày tạo</span>
                                    <span
                                        style="font-weight: 600;"><?= date('d/m/Y', strtotime($checklist->ngay_tao)) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="info-card">
                            <div class="info-card-title" style="margin-bottom: 16px;">
                                <i class="fa fa-clock-o"></i>
                                Tiến độ xử lý
                            </div>
                            <div
                                style="position: relative; padding-left: 24px; border-left: 2px solid #e2e8f0; margin-left: 8px;">
                                <?php
                                $timeline = [
                                    ['status' => 'S6', 'label' => 'Tạo Checklist', 'date' => $checklist->ngay_tao, 'icon' => 'fa-file-text'],
                                    ['status' => 'S7', 'label' => 'Duyệt Checklist', 'date' => $checklist->approved_s7_date, 'icon' => 'fa-check-circle'],
                                    ['status' => 'S8', 'label' => 'Tạo Nhân Sự', 'date' => $checklist->created_employee_date, 'icon' => 'fa-user-plus'],
                                    ['status' => 'S9', 'label' => 'Chính Thức', 'date' => $checklist->finalized_date, 'icon' => 'fa-trophy']
                                ];

                                foreach ($timeline as $item):
                                    $is_completed = array_search($item['status'], array_column($steps, 'code')) < $current_index;
                                    $is_current = $item['status'] == $current_status;
                                    ?>
                                    <div style="margin-bottom: 24px; position: relative;">
                                        <div
                                            style="position: absolute; left: -32px; width: 16px; height: 16px; border-radius: 50%; 
                                                    border: 3px solid <?= $is_completed || $is_current ? '#10b981' : '#e2e8f0' ?>; 
                                                    background: <?= $is_completed || $is_current ? '#10b981' : 'white' ?>;">
                                        </div>
                                        <div
                                            style="font-size: 12px; font-weight: 700; color: <?= $is_completed || $is_current ? '#059669' : '#94a3b8' ?>; margin-bottom: 2px;">
                                            <?= $item['label'] ?>
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">
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
        <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px;">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> Đóng
            </button>
        </div>
    </div>
</div>

<script>
    var checklistId = <?= $checklist->id ?>;
    var checklistData = <?= json_encode($checklist_data) ?>;
    var currentStatus = '<?= $checklist->status ?>';

    function toggleCandidateInfo() {
        var content = $('#candidate-info-content');
        var icon = $('#candidate-info-icon');
        content.slideToggle();
        icon.toggleClass('fa-chevron-down fa-chevron-up');
    }

    function toggleChecklistItem(key) {
        if (currentStatus !== 'S6') return;

        checklistData[key] = !checklistData[key];

        // Update UI
        var item = $('[onclick="toggleChecklistItem(\'' + key + '\')"]');
        if (checklistData[key]) {
            item.addClass('checked');
            item.find('.checklist-label').after('<i class="fa fa-check-circle" style="color: #10b981; font-size: 18px;"></i>');
        } else {
            item.removeClass('checked');
            item.find('.fa-check-circle').remove();
        }

        // Check if all items are checked
        var allChecked = Object.values(checklistData).every(v => v === true);
        $('#btn-approve-s7').prop('disabled', !allChecked);
    }

    function approveS7() {
        if (!confirm('Xác nhận đã kiểm tra đầy đủ hồ sơ và chuyển sang S7?')) return;

        $.ajax({
            url: admin_url + 'checklist_profile/updateStatus/' + checklistId,
            type: 'POST',
            dataType: 'JSON',
            data: {
                status: 'S7',
                checklist_data: JSON.stringify(checklistData),
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function (response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function () {
                alert_float('danger', 'Có lỗi xảy ra');
            }
        });
    }

    function createEmployee() {
        var employeeId = $('#employee_id_input').val().trim();
        if (!employeeId) {
            alert_float('warning', 'Vui lòng nhập mã nhân viên');
            return;
        }

        if (!confirm('Tạo hồ sơ nhân viên với mã: ' + employeeId + '?')) return;

        $.ajax({
            url: admin_url + 'checklist_profile/updateStatus/' + checklistId,
            type: 'POST',
            dataType: 'JSON',
            data: {
                status: 'S8',
                employee_id: employeeId,
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function (response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function () {
                alert_float('danger', 'Có lỗi xảy ra');
            }
        });
    }

    function finalizeEmployee() {
        var kpi = $('#kpi_score').val();
        var culture = $('#culture_score').val();
        var note = $('#evaluation_note').val();

        if (!kpi || !culture) {
            alert_float('warning', 'Vui lòng nhập đầy đủ điểm đánh giá');
            return;
        }

        if (!confirm('Phê duyệt nhân sự chính thức?')) return;

        $.ajax({
            url: admin_url + 'checklist_profile/updateStatus/' + checklistId,
            type: 'POST',
            dataType: 'JSON',
            data: {
                status: 'S9',
                evaluation_data: JSON.stringify({ kpi: kpi, culture: culture, note: note }),
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function (response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function () {
                alert_float('danger', 'Có lỗi xảy ra');
            }
        });
    }

    $(document).ready(function () {
        // Check initial state for S7 button
        if (currentStatus === 'S6') {
            var allChecked = Object.values(checklistData).every(v => v === true);
            $('#btn-approve-s7').prop('disabled', !allChecked);
        }
    });
</script>