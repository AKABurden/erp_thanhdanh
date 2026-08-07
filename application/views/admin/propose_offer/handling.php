    <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

    <style>
        /* Modal Styles */
        .offer-form-modal .modal-dialog {
            max-width: 1800px;
            height: 95vh;
            margin: 2.5vh auto;
        }

        .offer-form-modal .modal-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            border-radius: 10px !important;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .offer-form-modal .modal-body {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .offer-form-modal .modal-footer {
            flex-shrink: 0;
        }

        /* Header */
        .offer-form-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 6px 6px 0 0;
            margin: 0;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
        }

        .offer-form-header h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .offer-form-header h4 i {
            font-size: 24px;
        }

        .offer-form-subtitle {
            margin: 8px 0 0 36px;
            font-size: 13px;
            opacity: 0.9;
        }

        /* Form Sections */
        .form-section-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .form-section-header {
            background: linear-gradient(to right, #eff6ff, white);
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-section-icon {
            background: #dbeafe;
            padding: 8px;
            border-radius: 6px;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }

        .form-section-icon i {
            font-size: 16px;
        }

        .form-section-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 15px;
            margin: 0;
        }

        .form-section-body {
            padding: 24px;
        }

        /* Salary Section (Highlight) */
        .salary-section-header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            padding: 16px 20px;
            border-bottom: 1px solid #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        .salary-section-header .form-section-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .salary-section-header .form-section-title {
            color: white;
        }

        .salary-highlight-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Custom AJAX Search Box */
        .ajax-search-wrapper {
            position: relative;
        }

        .ajax-search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .ajax-search-input {
            width: 100%;
            padding: 10px 45px 10px 42px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            transition: all 0.2s;
            background: white;
        }

        .ajax-search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .ajax-search-input.has-value {
            background: #f0f9ff;
            border-color: #3b82f6;
            font-weight: 600;
            color: #1e40af;
        }

        .ajax-search-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        .ajax-search-input:focus~.ajax-search-icon {
            color: #3b82f6;
        }

        .ajax-search-arrow {
            position: absolute;
            right: 14px;
            color: #94a3b8;
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
            transition: all 0.2s;
        }

        .ajax-search-arrow:hover {
            color: #475569;
        }

        .ajax-search-arrow.rotate {
            transform: rotate(180deg);
        }

        .ajax-search-clear {
            position: absolute;
            right: 40px;
            color: #94a3b8;
            cursor: pointer;
            font-size: 14px;
            display: none;
            padding: 4px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .ajax-search-clear:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .ajax-search-clear.show {
            display: block;
        }

        .ajax-search-loading {
            position: absolute;
            right: 14px;
            color: #3b82f6;
            font-size: 14px;
            display: none;
        }

        .ajax-search-loading.show {
            display: block;
        }

        .ajax-search-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-height: 350px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .ajax-search-dropdown.show {
            display: block;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ajax-search-result-item {
            padding: 12px 14px;
            cursor: pointer;
            transition: all 0.15s;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ajax-search-result-item:last-child {
            border-bottom: none;
        }

        .ajax-search-result-item:hover {
            background: #eff6ff;
        }

        .ajax-search-result-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .ajax-search-result-content {
            flex: 1;
        }

        .ajax-search-result-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .ajax-search-result-title .highlight {
            background: #fef3c7;
            color: #92400e;
            padding: 0 2px;
            border-radius: 2px;
        }

        .ajax-search-result-subtitle {
            font-size: 12px;
            color: #64748b;
        }

        .ajax-search-no-results {
            padding: 24px;
            text-align: center;
            color: #94a3b8;
        }

        .ajax-search-no-results i {
            font-size: 32px;
            margin-bottom: 8px;
            display: block;
            opacity: 0.5;
        }

        /* Input Styles */
        .form-control-modern {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            transition: all 0.2s;
        }

        .form-control-modern:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .form-control-modern:disabled,
        .form-control-modern:read-only {
            background-color: #f1f5f9;
            cursor: not-allowed;
        }

        /* P1/P2 Sections */
        .p-section {
            background: #f8fafc;
            padding: 16px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .p-section.p2-section {
            background: white;
            border: 2px solid #bfdbfe;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.05);
        }

        .p-section-label {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .p-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            background: white;
            border: 1px solid #cbd5e1;
            color: #64748b;
        }

        .currency-input-group {
            position: relative;
        }

        .currency-symbol {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            font-family: serif;
            pointer-events: none;
            z-index: 1;
        }

        .currency-input-group input {
            padding-left: 40px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: 700;
            text-align: right;
            color: #1e293b;
        }

        .input-hint {
            font-size: 11px;
            color: #64748b;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .input-hint i {
            color: #3b82f6;
            font-size: 12px;
        }

        .input-hint.warning {
            color: #dc2626;
        }

        .input-hint.warning i {
            color: #dc2626;
        }

        /* Total Banner */
        .total-salary-banner {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e2e8f0;
            margin-top: 20px;
        }

        .total-salary-label {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .total-salary-icon {
            background: white;
            padding: 10px;
            border-radius: 50%;
            color: #3b82f6;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .total-salary-text {
            font-size: 11px;
            color: #3b82f6;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .total-salary-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .total-salary-amount {
            font-size: 32px;
            font-weight: 900;
            color: #1e293b;
            font-family: 'Courier New', monospace;
        }

        /* Sidebar */
        .info-sidebar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .sidebar-title {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 13px;
            color: #64748b;
        }

        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Timeline */
        .timeline {
            position: relative;
            border-left: 2px solid #e2e8f0;
            margin-left: 12px;
            padding-left: 28px;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 28px;
        }

        .timeline-dot {
            position: absolute;
            left: -37px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .timeline-dot.completed {
            background: #10b981;
        }

        .timeline-dot.current {
            background: #3b82f6;
        }

        .timeline-dot.pending {
            background: #cbd5e1;
        }

        .timeline-status {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .timeline-status.completed {
            color: #10b981;
        }

        .timeline-status.current {
            color: #3b82f6;
        }

        .timeline-status.pending {
            color: #94a3b8;
        }

        .timeline-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .timeline-desc {
            font-size: 11px;
            color: #64748b;
        }

        /* Footer */
        .modal-footer-modern {
            padding: 20px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            margin: 0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            border-radius: 0 0 12px 12px;
        }

        .btn-modern {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
        }

        .btn-secondary-modern {
            background: white;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        .btn-secondary-modern:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: 1px solid #2563eb;
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: 1px solid #059669;
        }

        .btn-success-modern:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 4px 8px rgba(5, 150, 105, 0.3);
        }

        .alert-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: start;
            gap: 8px;
            margin-top: 16px;
        }

        .alert-box i {
            color: #f59e0b;
            margin-top: 2px;
        }

        .alert-box-text {
            font-size: 11px;
            color: #92400e;
            line-height: 1.5;
        }

        .alert-box-text strong {
            font-weight: 700;
        }

        .auto-fill-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-top: 16px;
        }

        .auto-fill-item span {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .auto-fill-item p {
            font-size: 13px;
            color: #1e40af;
            font-weight: 600;
            margin: 0;
        }

        .required-star {
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                margin: 10px !important;
                width: 95% !important;
            }
        }

        .modal-open .select2-container--open .select2-dropdown {
            z-index: 1055 !important;
            /* > z-index modal backdrop ~1050 */
        }

        .modal .select2-container {
            z-index: 9999 !important;
        }

        .select2-container--open {
            z-index: 999999 !important;
        }

        /* Nếu dropdown vẫn bị cắt hoặc lệch khi modal scroll */
        .modal-body {
            position: relative;
            /* rất quan trọng */
        }

        .select2-dropdown {
            z-index: 99999 !important;
            top: 100% !important;
            /* đôi khi cần */
        }
    </style>

    <div class="modal-dialog" style="width: 70%; max-width: 1800px; height: 95vh; margin: 2.5vh auto;">
        <div class="modal-content" style="height: 100%; display: flex; flex-direction: column;">
            <!-- Header - Fixed -->
            <div class="offer-form-header" style="flex-shrink: 0; position: relative;">
                <h4>
                    <i class="fa fa-file-text"></i>
                    <?= isset($offer) && $offer ? "Chi tiết Offer: {$offer->ma_offer}" : "Tạo Offer Mới (Bước E)" ?>
                </h4>
                <p class="offer-form-subtitle">Thiết lập lương 3P & chế độ phúc lợi</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng" style="position: absolute; top: 18px; right: 24px; font-size: 28px; color: #fff; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body - Scrollable -->
            <div class="modal-body" style="flex: 1; overflow-y: auto; padding: 20px; background: #f8fafc;">
                <?php
                $isApproved = isset($offer) && $offer && $offer->trang_thai == 'DA_DUYET';
                $isReadOnly = $isApproved ? 'readonly disabled' : '';
                ?>
                <?php echo form_open(base_url('admin/propose_offer/handling/' . (isset($offer) ? $offer->id : '0')), ['id' => 'offer-form']); ?>

                <?php if ($isApproved): ?>
                    <div class="alert alert-warning" style="margin-bottom: 20px;">
                        <i class="fa fa-lock"></i> <strong>Offer đã được duyệt</strong> - Không thể chỉnh sửa. Bạn có thể gửi email cho ứng viên.
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- LEFT COLUMN: Form Inputs -->
                    <div class="col-md-8">

                        <!-- CARD 1: Thông tin & Vị trí (Kế thừa YCTD) -->
                        <div class="form-section-card">
                            <div class="form-section-header">
                                <div class="form-section-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <h3 class="form-section-title">I. Thông tin & Vị trí (Kế thừa YCTD)</h3>
                            </div>
                            <div class="form-section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                1. Chọn YCTD (Đang tuyển) <span class="required-star">*</span>
                                            </label>
                                            <div class="ajax-search-wrapper">
                                                <div class="ajax-search-input-wrapper">
                                                    <input type="text" id="yctd_search_input" class="ajax-search-input"
                                                        placeholder="Chọn hoặc tìm kiếm YCTD..." autocomplete="off"
                                                        <?= !empty($candidate) ? 'readonly disabled style="background: #f1f5f9; cursor: not-allowed;"' : '' ?>>
                                                    <i class="fa fa-search ajax-search-icon"></i>
                                                    <i class="fa fa-times ajax-search-clear" id="yctd_clear"></i>
                                                    <i class="fa fa-chevron-down ajax-search-arrow" id="yctd_arrow"></i>
                                                    <i class="fa fa-spinner fa-spin ajax-search-loading"
                                                        id="yctd_loading"></i>
                                                </div>
                                                <div class="ajax-search-dropdown" id="yctd_dropdown"></div>
                                                <input type="hidden" name="ma_yctd" id="ma_yctd"
                                                    value="<?= isset($offer) && $offer->ma_yctd ? $offer->ma_yctd : (!empty($candidate) ? $candidate->id_requirements : '') ?>" />
                                            </div>
                                            <?php if (!empty($candidate)): ?>
                                                <div class="input-hint" style="color: #f59e0b; margin-top: 6px;">
                                                    <i class="fa fa-lock"></i>
                                                    <span>YCTD đã được chọn tự động từ ứng viên, không thể thay đổi.</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                2. Chọn Ứng viên (Đã Pass PV) <span class="required-star">*</span>
                                            </label>
                                            <select data-id="<?= !empty($candidate) ? $candidate->id : (isset($offer) ? $offer->kqpv_id : '') ?>" name="kqpv_id" id="kqpv_id" class="form-control form-control-modern"
                                                onchange="loadCandidateInfo()" <?= isset($candidate) ? 'disabled style="font-weight: 700; background: #f1f5f9; cursor: not-allowed;"' : 'disabled style="font-weight: 700;"' ?>>
                                                <option value="">-- Chọn Ứng viên --</option>
                                                <?php if (!empty($candidate)): ?>
                                                    <option value="<?= $candidate->id ?>" selected><?= $candidate->full_name ?></option>
                                                <?php elseif (!empty($offer) && $offer->kqpv_id && $offer->ten_ung_vien): ?>
                                                    <option value="<?= $offer->kqpv_id ?>" selected><?= $offer->ten_ung_vien ?></option>
                                                <?php endif; ?>
                                            </select>
                                            <input type="hidden" name="evaluation_employee_id" id="evaluation_employee_id"
                                                value="<?= !empty($candidate) ? $candidate->id : (isset($offer) ? $offer->evaluation_employee_id : '') ?>">
                                            <div class="input-hint" id="candidate-hint"
                                                style="display: none; color: #f59e0b;">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span>Chưa có ứng viên đạt phỏng vấn cho YCTD này.</span>
                                            </div>
                                            </input>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" style="color: #64748b;">Họ tên Ứng viên
                                                (Auto)</label>
                                            <input type="text" name="ten_ung_vien" id="ten_ung_vien"
                                                class="form-control form-control-modern"
                                                value="<?= isset($offer) ? $offer->ten_ung_vien : (!empty($candidate) ? $candidate->full_name : '') ?>" readonly
                                                style="background: #f1f5f9; font-weight: 600;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"
                                                style="color: #64748b; font-size: 13px; margin-bottom: 8px;">Kết quả
                                                Đánh giá</label>
                                            <div
                                                style="display: grid; grid-template-columns: 1fr 2fr 3fr; gap: 10px; align-items: start;">
                                                <div>
                                                    <label
                                                        style="font-size: 11px; color: #64748b; margin-bottom: 4px; font-weight: 600;">Điểm</label>
                                                    <div id="evaluation_point" class="form-control form-control-modern"
                                                        style="background: #ecfdf5; border: 1px solid #6ee7b7; color: #059669; font-weight: 700; text-align: center; font-size: 16px; height: 46px; display: flex; align-items: center; justify-content: center;">
                                                        --
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        style="font-size: 11px; color: #64748b; margin-bottom: 4px; font-weight: 600;">Rating</label>
                                                    <div id="evaluation_rating" class="form-control form-control-modern"
                                                        style="background: #fef3c7; border: 1px solid #fbbf24; color: #d97706; font-weight: 600; text-align: center; font-size: 12px; line-height: 1.3; word-wrap: break-word; min-height: 46px; display: flex; align-items: center; justify-content: center; padding: 6px;">
                                                        --
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        style="font-size: 11px; color: #64748b; margin-bottom: 4px; font-weight: 600;">Warning</label>
                                                    <div id="evaluation_warning"
                                                        class="form-control form-control-modern"
                                                        style="background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; font-weight: 600; text-align: center; font-size: 11px; line-height: 1.3; word-wrap: break-word; min-height: 46px; display: flex; align-items: center; justify-content: center; padding: 6px;">
                                                        --
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="auto-fill-info">
                                        <div class="auto-fill-item">
                                            <span>Vị trí</span>
                                            <p id="display_vi_tri">
                                                <?= isset($offer) && $offer->vi_tri_offer ? $offer->vi_tri_offer : '...' ?>
                                            </p>
                                            <input type="hidden" name="vi_tri_offer" id="vi_tri_offer"
                                                value="<?= isset($offer) ? $offer->vi_tri_offer : '' ?>">
                                        </div>
                                        <div class="auto-fill-item">
                                            <span>Cấp bậc</span>
                                            <p id="display_lever">
                                                <?= isset($offer) && $offer->name_lever_offer ? $offer->name_lever_offer : '...' ?>
                                            </p>
                                            <input type="hidden" name="lever_offer" id="lever_offer"
                                                value="<?= isset($offer) ? $offer->name_lever_offer : '' ?>">
                                        </div>
                                        <div class="auto-fill-item">
                                            <span>Phòng ban</span>
                                            <p id="display_phong_ban">
                                                <?= isset($offer) && $offer->phong_ban_offer ? $offer->phong_ban_offer : '...' ?>
                                            </p>
                                            <input type="hidden" name="phong_ban_offer" id="phong_ban_offer"
                                                value="<?= isset($offer) ? $offer->phong_ban_offer : '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD 2: Cơ cấu Lương 3P (Highlight) -->
                            <div class="form-section-card"
                                style="border: 2px solid #bfdbfe; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);">
                                <div class="salary-section-header">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="form-section-icon">
                                            <i class="fa fa-money"></i>
                                        </div>
                                        <h3 class="form-section-title">II. Cơ cấu Lương 3P (Offer)</h3>
                                    </div>
                                    <div class="salary-highlight-badge">Quy chế Lương</div>
                                </div>
                                <div class="form-section-body" style="padding: 28px;">
                                    <div class="row">
                                        <!-- P1 Section -->
                                        <div class="col-md-6">
                                            <div class="p-section">
                                                <div class="p-section-label">
                                                    P1 - Lương Vị trí
                                                    <span class="p-badge">Cố định</span>
                                                </div>
                                                <div class="currency-input-group">
                                                    <span class="currency-symbol">₫</span>
                                                    <input type="text" name="luong_p1" id="luong_p1"
                                                        class="form-control form-control-modern number-format"
                                                        value="<?= isset($offer) ? formatMoney($offer->luong_p1, 0, ',', ',') : 0 ?>"
                                                        readonly onchange="calculateTotal()"
                                                        style="background: #f1f5f9; cursor: not-allowed;">
                                                </div>
                                                <div class="input-hint">
                                                    <i class="fa fa-info-circle"></i>
                                                    <span>Kế thừa từ YCTD. Là cơ sở đóng BHXH.</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- P2 Section -->
                                        <div class="col-md-6">
                                            <div class="p-section p2-section">
                                                <div class="p-section-label">
                                                    P2 - Lương Năng lực
                                                    <span class="p-badge"
                                                        style="color: #1e40af; border-color: #bfdbfe;">VND/tháng</span>
                                                </div>
                                                <div class="currency-input-group">
                                                    <span class="currency-symbol">₫</span>
                                                    <input type="text" readonly name="luong_p2" id="luong_p2"
                                                        class="form-control form-control-modern number-format"
                                                        value="<?= isset($offer) ? formatMoney($offer->luong_p2, 0, ',', ',') : 0 ?>"
                                                        onchange="calculateTotal(); validateP2()"
                                                        style="border: 2px solid #3b82f6;"
                                                        <?= $isReadOnly ?>>
                                                </div>
                                                <div class="input-hint" id="p2_hint">
                                                    <i class="fa fa-check-circle"></i>
                                                    <span>Nhập lương năng lực dựa trên đánh giá PV</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Banner -->
                                    <div class="total-salary-banner">
                                        <div class="total-salary-label">
                                            <div class="total-salary-icon">
                                                <i class="fa fa-money" style="font-size: 20px;"></i>
                                            </div>
                                            <div>
                                                <div class="total-salary-text">Tổng thu nhập đề xuất (Gross)</div>
                                                <div class="total-salary-sub">Bao gồm P1 + P2 + Phụ cấp</div>
                                            </div>
                                        </div>
                                        <div class="total-salary-amount" id="total_income">0 ₫</div>
                                    </div>

                                    <div class="row"
                                        style="margin-top: 24px; padding-top: 24px; border-top: 1px dashed #cbd5e1;">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Phụ cấp (Nếu có)</label>
                                                <div class="currency-input-group">
                                                    <span class="currency-symbol">₫</span>
                                                    <input type="text" readonly name="phu_cap" id="phu_cap"
                                                        class="form-control form-control-modern number-format"
                                                        value="<?= isset($offer) ? formatMoney($offer->phu_cap) : 0 ?>"
                                                        onchange="calculateTotal()"
                                                        <?= $isReadOnly ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">P3 - Lương Thành
                                                    tích</label>
                                                <div class="currency-input-group">
                                                    <span class="currency-symbol">₫</span>
                                                    <input type="text" readonly name="luong_p3" id="luong_p3"
                                                        class="form-control form-control-modern number-format"
                                                        value="<?= isset($offer) ? formatMoney($offer->luong_p3) : 0 ?>"
                                                        onchange="calculateTotal()"
                                                        <?= $isReadOnly ?>>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD 3: Thời gian & Hồ sơ -->
                            <div class="form-section-card">
                                <div class="form-section-header">
                                    <div class="form-section-icon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <h3 class="form-section-title">III. Thời gian & Hồ sơ</h3>
                                </div>
                                <div class="form-section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Ngày bắt đầu làm việc</label>
                                                <input type="date" name="ngay_bat_dau_du_kien"
                                                    class="form-control form-control-modern"
                                                    value="<?= isset($offer) ? $offer->ngay_bat_dau_du_kien : _d(date('Y-m-d')) ?>"
                                                    <?= $isReadOnly ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Thời hạn phản hồi Offer</label>
                                                <select name="thoi_han_offer" class="form-control form-control-modern"
                                                    <?= $isReadOnly ?>>
                                                    <option value="3 Ngày">3 Ngày</option>
                                                    <option value="5 Ngày">5 Ngày</option>
                                                    <option value="7 Ngày" selected>7 Ngày</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                    <!-- RIGHT COLUMN: Sidebar -->
                    <div class="col-md-4">
                        <!-- System Info -->
                        <div class="info-sidebar" style="margin-bottom: 20px;">
                            <div class="sidebar-title">
                                <i class="fa fa-bell"></i> Thông tin hệ thống
                            </div>
                            <div class="info-item">
                                <span class="info-label">Mã Offer</span>
                                <span class="info-value"
                                    style="font-family: 'Courier New', monospace; color: #2563eb; background: #eff6ff; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                    <?= isset($offer) ? $offer->ma_offer : 'OFR' . date('ymd') . rand(100, 999) ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Người tạo</span>
                                <div style="text-align: right;">
                                    <div class="info-value" style="font-size: 12px;"><?= $staff_create ?> </div>
                                    <div style="font-size: 10px; color: #94a3b8;">
                                        <?= (!empty($staff_roles_create) ? $staff_roles_create['role_name'] : '') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày tạo</span>
                                <span class="info-value"><?= date('d/m/Y') ?></span>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="info-sidebar">
                            <div class="sidebar-title">Tiến độ Bước E</div>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-dot completed"></div>
                                    <div class="timeline-status completed">Hoàn thành</div>
                                    <div class="timeline-title">Tạo Offer (Lương 3P)</div>
                                    <div class="timeline-desc"><?= date('d/m/Y') ?></div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot current"></div>
                                    <div class="timeline-status current">Hiện tại</div>
                                    <div class="timeline-title">Phê duyệt nội bộ</div>
                                    <div class="timeline-desc">Trưởng bộ phận & HR Manager</div>
                                </div>
                                <div class="timeline-item" style="margin-bottom: 0;">
                                    <div class="timeline-dot pending"></div>
                                    <div class="timeline-status pending">Bước tiếp</div>
                                    <div class="timeline-title">Gửi Email & Import FOSO</div>
                                    <div class="timeline-desc">Chuyển sang S7: Đối chiếu hồ sơ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>

            <!-- Footer - Fixed -->
            <div class="modal-footer modal-footer-modern" style="flex-shrink: 0;">
                <button type="button" class="btn btn-modern btn-secondary-modern" data-dismiss="modal">
                    <i class="fa fa-times"></i> Đóng
                </button>
                <button type="button" class="btn btn-modern btn-primary-modern" onclick="previewOffer()">
                    <i class="fa fa-eye"></i> Xem Letter
                </button>
                <?php if (isset($offer) && $offer && $offer->trang_thai == 'DA_DUYET'): ?>
                    <button type="button" class="btn btn-modern btn-success-modern" onclick="sendOfferEmail(<?= $offer->id ?>)">
                        <i class="fa fa-envelope"></i> Gửi Email Offer
                    </button>
                <?php elseif (isset($offer) && $offer && in_array($offer->trang_thai, ['DRAFT', 'DANG_CHO_DUYET'])): ?>
                    <button type="button" class="btn btn-modern" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;" onclick="approveOffer(<?= $offer->id ?>)">
                        <i class="fa fa-check-circle"></i> Duyệt Offer
                    </button>
                    <button type="submit" class="btn btn-modern btn-success-modern" form="offer-form">
                        <i class="fa fa-save"></i> Lưu Offer
                    </button>
                <?php else: ?>
                    <!-- <button type="submit" class="btn btn-modern btn-success-modern" form="offer-form">
                        <i class="fa fa-save"></i> Lưu Offer
                    </button> -->
                <?php endif; ?>
                <?php if (!isset($offer)) { ?>
                    <button type="submit" class="btn btn-modern btn-success-modern" form="offer-form">
                        <i class="fa fa-save"></i> Lưu Offer
                    </button>
                <?php } ?>
            </div>
        </div>

        <script>
            // Approve offer function
            function approveOffer(offerId) {
                if (!offerId) {
                    alert_float('danger', 'ID Offer không hợp lệ');
                    return;
                }

                if (confirm('Bạn có chắc chắn muốn duyệt Offer này?\n\nSau khi duyệt, bạn sẽ không thể chỉnh sửa nội dung.')) {
                    $.ajax({
                        url: admin_url + 'propose_offer/approve/' + offerId,
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
                        },
                        success: function(response) {
                            if (response.result == 1) {
                                alert_float('success', response.message || 'Duyệt Offer thành công!');
                                setTimeout(function() {
                                    if (typeof modal_requirements != 'undefined') {
                                        $('#tnhModal').modal('hide');
                                    } else {
                                        location.reload();
                                    }
                                }, 500);
                            } else {
                                alert_float('danger', response.message || 'Có lỗi khi duyệt Offer');
                            }
                        },
                        error: function() {
                            alert_float('danger', 'Có lỗi xảy ra khi duyệt Offer');
                        }
                    });
                }
            }

            // Send offer email function
            function sendOfferEmail(offerId) {
                if (!offerId) {
                    alert_float('danger', 'ID Offer không hợp lệ');
                    return;
                }

                if (confirm('Bạn có chắc chắn muốn gửi Email Offer này đến ứng viên?')) {
                    $.ajax({
                        url: admin_url + 'propose_offer/send_email/' + offerId,
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
                        },
                        success: function(response) {
                            if (response.result == 1) {
                                alert_float('success', response.message || 'Đã gửi email thành công!');
                                setTimeout(function() {
                                    if (typeof modal_requirements != 'undefined') {
                                        $('#tnhModal').modal('hide');
                                    } else {
                                        location.reload();
                                    }
                                }, 500);
                            } else {
                                alert_float('danger', response.message || 'Có lỗi khi gửi email');
                            }
                        },
                        error: function() {
                            alert_float('danger', 'Có lỗi xảy ra khi gửi email');
                        }
                    });
                }
            }

            // Use var instead of const to avoid redeclaration error
            var CANDIDATES_DATA = window.CANDIDATES_DATA || {
                'YCTD045': [{
                        id: 'KQPV001',
                        name: 'Nguyễn Thị Lan Anh',
                        score: 8.5,
                        note: 'Tư duy logic tốt, phù hợp văn hóa.',
                        p2: 2500000
                    },
                    {
                        id: 'KQPV002',
                        name: 'Lê Hoàng Nam',
                        score: 7.0,
                        note: 'Khá, cần đào tạo thêm về SQL.',
                        p2: 1500000
                    }
                ],
                'YCTD048': [{
                    id: 'KQPV003',
                    name: 'Trần Văn Cường',
                    score: 9.0,
                    note: 'Xuất sắc, Technical lead tiềm năng.',
                    p2: 18000000
                }],
                'YCTD050': []
            };

            var currentYCTD = null;

            function loadYCTDInfo(yctdData) {
                const candidateSelect = $('#kqpv_id');
                const candidateHint = $('#candidate-hint');

                if (!yctdData) {
                    candidateSelect.prop('disabled', true).html('<option value="">-- Chọn Ứng viên --</option>');
                    $('#display_vi_tri, #display_phong_ban').text('...');
                    $('#luong_p1').val(0);
                    candidateHint.hide();
                    currentYCTD = null;
                    return;
                }

                currentYCTD = yctdData;

                // Fill position info
                $('#display_vi_tri').text(yctdData.role_name || yctdData.role_name);
                $('#display_lever').text(yctdData.role_level || yctdData.role_level);
                $('#display_phong_ban').text(yctdData.room_name);
                $('#vi_tri_offer').val(yctdData.role_name || yctdData.role_name);
                $('#phong_ban_offer').val(yctdData.room_name);

                // Lấy P1/P2 từ Salary3P theo role_id (mặc định 0 tháng cho ứng viên mới)
                if (yctdData.role_id) {
                    $.ajax({
                        url: admin_url + 'propose_offer/getSalaryByRole',
                        type: 'GET',
                        data: {
                            role_id: yctdData.role_id,
                            role_level_id: yctdData.role_level_id,
                            seniority_months: 0
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.success && res.data) {
                                $('#luong_p1').val(tnhFormatMoney(res.data.salary_p1));
                                $('#luong_p2').val(tnhFormatMoney(res.data.salary_p2));
                                $('#luong_p3').val(tnhFormatMoney(res.data.salary_p3));
                                $('#phu_cap').val(tnhFormatMoney(res.data.phu_cap));
                                currentYCTD.p2_min = res.data.p2_min;
                                currentYCTD.p2_max = res.data.p2_max;
                                calculateTotal();
                                validateP2();
                            } else {
                                // Show alert with error message and prevent creation
                                var errorMessage = res.message || 'Không thể lấy thông tin lương cho vị trí này';
                                alert(errorMessage);

                                // Clear the form and prevent further actions
                                $('#luong_p1').val('');
                                $('#luong_p2').val('');
                                $('#yctd_search_input').val('').removeClass('has-value');
                                $('#ma_yctd').val('');
                                selectedYCTD = null;
                                currentYCTD = null;

                                // Disable submit buttons to prevent creation
                                $('button[type="submit"][form="offer-form"]').prop('disabled', true);

                                return false;
                            }
                        },
                        error: function() {
                            $('#luong_p1').val(tnhFormatMoney(yctdData.p1_std || yctdData.p1 || 0));
                            currentYCTD.p2_min = yctdData.p2_min || 0;
                            currentYCTD.p2_max = yctdData.p2_max || 0;
                            calculateTotal();
                        }
                    });
                } else {
                    $('#luong_p1').val(tnhFormatMoney(yctdData.p1_std || yctdData.p1 || 0));
                    currentYCTD.p2_min = yctdData.p2_min || 0;
                    currentYCTD.p2_max = yctdData.p2_max || 0;
                    calculateTotal();
                }

                // Store P2 range
                // currentYCTD.p2_min = yctdData.p2_min || 0;
                // currentYCTD.p2_max = yctdData.p2_max || 0;

                // Load candidates (mock for now - can be replaced with AJAX)
                const yctdCode = yctdData.code || yctdData.ma_yctd;

                // Reset candidate fields
                $('#ten_ung_vien').val('');
                $('#interview_note').text('Chưa có dữ liệu');
                $('#luong_p2').val(0);
                fetchCandidatesForYCTD(yctdData.id);
                calculateTotal();
            }
            var cachedCandidatesByYCTD = {};

            /**
             * Fetch candidate list for a given YCTD (AJAX GET).
             * Expects server response JSON: { items: [ { id, name, note, p2, score }, ... ] }
             */
            function fetchCandidatesForYCTD(ma_yctd) {
                var candidateSelect = $('#kqpv_id');
                var candidateHint = $('#candidate-hint');

                if (!ma_yctd) {
                    candidateSelect.prop('disabled', true).html('<option value="">-- Chọn Ứng viên --</option>');
                    candidateHint.show();
                    return;
                }

                // Use cached results if available
                if (cachedCandidatesByYCTD[ma_yctd]) {
                    populateCandidateSelect(cachedCandidatesByYCTD[ma_yctd]);
                    return;
                }

                // candidateSelect.prop('disabled', true).html('<option value="">Đang tải...</option>');
                candidateHint.hide();

                $.ajax({
                    url: admin_url + 'propose_offer/getCandidatesByYctd', // backend endpoint (adjust if needed)
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        ma_yctd: ma_yctd,
                        kqpv_id: candidateSelect.val()
                    },
                    success: function(res) {
                        // Support two formats:
                        // 1) { items: [...] }
                        // 2) [...] (array)
                        var items = [];
                        if (Array.isArray(res)) {
                            items = res;
                        } else if (res && Array.isArray(res.items)) {
                            items = res.items;
                        } else if (res && res.data && Array.isArray(res.data)) {
                            items = res.data;
                        }

                        // normalize to expected shape if candidate_data present
                        items = items.map(function(it) {
                            // if the API returns legacy shape (id,name,note,p2) keep as is
                            if (it.candidate_data) {
                                return {
                                    id: it.id,
                                    name: it.candidate_data.full_name || it.text || it.name || '',
                                    note: [
                                        it.candidate_data.email ? 'Email: ' + it.candidate_data.email : null,
                                        it.candidate_data.phone_number ? 'Phone: ' + it.candidate_data.phone_number : null,
                                        it.candidate_data.role_level ? 'Level: ' + it.candidate_data.role_level : null,
                                        it.candidate_data.years_of_experience ? 'Exp: ' + it.candidate_data.years_of_experience + ' yrs' : null
                                    ].filter(Boolean).join(' • '),
                                    p2: it.candidate_data.expected_salary || 0,
                                    point: it.candidate_data.point || '--',
                                    rating: it.candidate_data.rating || '--',
                                    warning: it.candidate_data.warning || '--',
                                    evaluation_id: it.evaluation_id || ''
                                };
                            }
                            // fallback - keep existing fields
                            return {
                                id: it.id || it.value || '',
                                name: it.text || it.name || '',
                                note: it.note || '',
                                p2: it.p2 || 0,
                                point: it.point || '--',
                                rating: it.rating || '--',
                                warning: it.warning || '--',
                                evaluation_id: it.evaluation_id || ''
                            };
                        });

                        // cache
                        cachedCandidatesByYCTD[ma_yctd] = items;
                        populateCandidateSelect(items);
                    },
                    error: function() {
                        candidateSelect.prop('disabled', true).html('<option value="">-- Chọn Ứng viên --</option>');
                        candidateHint.show();
                        alert_float('danger', 'Không thể tải danh sách ứng viên. Vui lòng thử lại.');
                    }
                });

                function escapeHtml(str) {
                    if (str === null || str === undefined) return '';
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                }

                function populateCandidateSelect(items) {
                    // Get current selected value before replacing options
                    var currentValue = candidateSelect.attr('data-id');

                    if (!items || items.length === 0) {
                        candidateSelect.prop('disabled', true).html('<option value="">-- Chọn Ứng viên --</option>');
                        candidateHint.show();
                        return;
                    }

                    var options = '<option value="">-- Chọn Ứng viên --</option>';
                    items.forEach(function(c) {
                        var scoreText = c.point && c.point !== '--' ? ' (Điểm: ' + escapeHtml(c.point) + ')' : '';
                        var name = escapeHtml(c.name || '');
                        var note = escapeHtml(c.note || '');
                        var p2 = Number(c.p2) || 0;
                        var point = escapeHtml(c.point || '--');
                        var rating = escapeHtml(c.rating || '--');
                        var warning = escapeHtml(c.warning || '--');
                        var evaluation_id = escapeHtml(c.evaluation_id || '');
                        options += '<option value="' + escapeHtml(c.id) + '" data-name="' + name + '" data-note="' + note + '" data-p2="' + p2 + '" data-point="' + point + '" data-rating="' + rating + '" data-warning="' + warning + '" data-evaluation-id="' + evaluation_id + '">' + name + scoreText + '</option>';
                    });

                    candidateSelect.html(options).prop('disabled', false);

                    // Restore selected value if it exists in new options
                    if (currentValue) {
                        candidateSelect.val(currentValue);
                        loadCandidateInfo();
                    }

                    candidateHint.hide();
                }
            }

            // Trigger fetch when hidden ma_yctd changes (this will run when user picks an item from dropdown)
            // $(document).on('change', '#ma_yctd', function () {
            //     var ma = $(this).val();
            //     fetchCandidatesForYCTD(ma);
            // });
            function loadCandidateInfo() {
                const selected = $('#kqpv_id option:selected');
                const name = selected.data('name') || '';
                const note = selected.data('note') || 'Chưa có dữ liệu';
                const p2 = selected.data('p2') || 0;
                const point = selected.data('point') || '--';
                const rating = selected.data('rating') || '--';
                const warning = selected.data('warning') || '--';
                const evaluation_id = selected.data('evaluation-id') || '';

                $('#ten_ung_vien').val(name);
                $('#evaluation_point').text(point);
                $('#evaluation_rating').text(rating);
                $('#evaluation_warning').text(warning);
                $('#evaluation_employee_id').val(evaluation_id);

                calculateTotal();
                validateP2();
            }

            function validateP2() {
                if (!currentYCTD) return;

                const p2Value = intVal($('#luong_p2').val()) || 0;
                const hint = $('#p2_hint');

                if (p2Value < currentYCTD.p2_min || p2Value > currentYCTD.p2_max) {
                    hint.removeClass('input-hint').addClass('input-hint warning');
                    hint.find('i').removeClass('fa-check-circle').addClass('fa-exclamation-triangle');
                    hint.find('span').text(`Ngoài khung P2 (${formatCurrency(currentYCTD.p2_min)} - ${formatCurrency(currentYCTD.p2_max)})`);
                } else {
                    hint.removeClass('warning').addClass('input-hint');
                    hint.find('i').removeClass('fa-exclamation-triangle').addClass('fa-check-circle');
                    hint.find('span').text(`Khung cho phép: ${formatCurrency(currentYCTD.p2_min)} - ${formatCurrency(currentYCTD.p2_max)}`);
                }
            }

            function calculateTotal() {
                const p1 = intVal($('#luong_p1').val()) || 0;
                const p2 = intVal($('#luong_p2').val()) || 0;
                const phuCap = intVal($('#phu_cap').val()) || 0;
                const total = p1 + p2 + phuCap;

                $('#total_income').text(formatCurrency(total));
            }

            function formatCurrency(amount) {
                if (!amount) return '0 ₫';
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND',
                    minimumFractionDigits: 0
                }).format(amount);
            }

            function previewOffer() {
                const offerId = '<?= isset($offer) ? $offer->id : '' ?>';
                if (offerId) {
                    var url = '<?= base_url('admin/propose_offer/preview/') ?>' + offerId;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#tnhModal2').html(response);
                            $('#tnhModal2').modal('show');
                        },
                        error: function() {
                            alert_float('danger', 'Có lỗi xảy ra khi tải preview');
                        }
                    });
                } else {
                    alert_float('warning', 'Vui lòng lưu Offer trước khi xem trước');
                }
            }

            function handleOfferSubmit(form) {
                var submitBtn = $(form).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');

                $.ajax({
                    url: form.action,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.result) {
                            alert_float('success', response.message);
                            if (typeof modal_requirements != 'undefined') {
                                $('#tnhModal').modal('hide');
                                if (typeof tableEprofile !== 'undefined' && tableEprofile) {
                                    tableEprofile.ajax.reload();
                                }
                                if (typeof oTable !== 'undefined' && oTable) {
                                    oTable.ajax.reload();
                                }
                            } else {
                                if (typeof oTable !== 'undefined' && oTable) {
                                    oTable.ajax.reload();
                                }
                                $('.modal-dialog .close, [data-dismiss="modal"]').trigger('click');
                            }
                        } else {
                            alert_float('danger', response.message || 'Có lỗi xảy ra');
                        }
                    },
                });
            }
            // Custom AJAX Search for YCTD
            var searchTimeout;
            var selectedYCTD = null;
            var initialDataLoaded = false;
            var cachedResults = [];

            function initYCTDSearch() {
                const searchInput = $('#yctd_search_input');
                const dropdown = $('#yctd_dropdown');
                const clearBtn = $('#yctd_clear');
                const arrow = $('#yctd_arrow');
                const loading = $('#yctd_loading');

                // Focus - load initial 10 items
                searchInput.on('focus', function() {
                    const currentVal = $(this).val().trim();

                    if (currentVal.length === 0 && !initialDataLoaded) {
                        loading.addClass('show');
                        arrow.addClass('rotate');
                        loadInitialYCTD();
                    } else if (currentVal.length === 0 && cachedResults.length > 0) {
                        displayYCTDResults(cachedResults);
                        arrow.addClass('rotate');
                    }
                });

                // Arrow click - toggle dropdown
                arrow.on('click', function() {
                    if (dropdown.hasClass('show')) {
                        dropdown.removeClass('show');
                        arrow.removeClass('rotate');
                    } else {
                        searchInput.focus();
                    }
                });

                // Search on input
                searchInput.on('input', function() {
                    const query = $(this).val().trim();

                    clearTimeout(searchTimeout);

                    if (query.length === 0) {
                        clearBtn.removeClass('show');
                        arrow.show();
                        // Show initial data if available
                        if (cachedResults.length > 0) {
                            displayYCTDResults(cachedResults);
                        }
                        return;
                    }

                    clearBtn.addClass('show');
                    arrow.hide();
                    loading.addClass('show');

                    searchTimeout = setTimeout(function() {
                        searchYCTD(query);
                    }, 300);
                });

                // Clear button
                clearBtn.on('click', function(e) {
                    e.stopPropagation();
                    searchInput.val('').removeClass('has-value').focus();
                    clearBtn.removeClass('show');
                    arrow.show();
                    $('#ma_yctd').val('');
                    selectedYCTD = null;
                    loadYCTDInfo(null);

                    // Show initial data
                    if (cachedResults.length > 0) {
                        displayYCTDResults(cachedResults);
                    }
                });

                // Click outside to close
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.ajax-search-wrapper').length) {
                        dropdown.removeClass('show');
                        arrow.removeClass('rotate');
                    }
                });
            }

            function loadInitialYCTD() {
                const loading = $('#yctd_loading');

                $.ajax({
                    url: '<?= base_url('admin/propose_offer/getYCTDList') ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        q: '',
                        page: 1
                    },
                    success: function(response) {
                        loading.removeClass('show');
                        initialDataLoaded = true;
                        cachedResults = response.items || [];
                        displayYCTDResults(cachedResults.slice(0, 10));
                    },
                    error: function() {
                        loading.removeClass('show');
                        $('#yctd_dropdown').html('<div class="ajax-search-no-results"><i class="fa fa-exclamation-triangle"></i><div>Có lỗi xảy ra</div></div>').addClass('show');
                    }
                });
            }

            function searchYCTD(query) {
                const dropdown = $('#yctd_dropdown');
                const loading = $('#yctd_loading');

                $.ajax({
                    url: '<?= admin_url('propose_offer/getYCTDList') ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        q: query,
                        page: 1
                    },
                    success: function(response) {
                        loading.removeClass('show');
                        displayYCTDResults(response.items || []);
                    },
                    error: function() {
                        loading.removeClass('show');
                        dropdown.html('<div class="ajax-search-no-results"><i class="fa fa-exclamation-triangle"></i><div>Có lỗi xảy ra</div></div>').addClass('show');
                    }
                });
            }

            function displayYCTDResults(items) {
                const dropdown = $('#yctd_dropdown');
                const arrow = $('#yctd_arrow');
                const query = $('#yctd_search_input').val().toLowerCase();

                if (items.length === 0) {
                    dropdown.html('<div class="ajax-search-no-results"><i class="fa fa-inbox"></i><div>Không tìm thấy YCTD nào</div></div>').addClass('show');
                    arrow.addClass('rotate');
                    return;
                }

                let html = '';
                items.forEach(function(item) {
                    const text = item.text || '';
                    const highlighted = highlightText(text, query);

                    html += `
                    <div class="ajax-search-result-item" data-id="${item.id}" data-yctd='${JSON.stringify(item.yctd_data)}'>
                        <div class="ajax-search-result-icon">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                        <div class="ajax-search-result-content">
                            <div class="ajax-search-result-title">${highlighted}</div>
                            <div class="ajax-search-result-subtitle">
                                <i class="fa fa-users"></i> ${item.yctd_data.department || 'N/A'}
                            </div>
                        </div>
                    </div>
                `;
                });

                dropdown.html(html).addClass('show');
                arrow.addClass('rotate');

                // Handle result click
                $('.ajax-search-result-item').on('click', function() {
                    const id = $(this).data('id');
                    const yctdData = $(this).data('yctd');
                    const text = $(this).find('.ajax-search-result-title').text();

                    $('#yctd_search_input').val(text).addClass('has-value');
                    $('#ma_yctd').val(id);
                    dropdown.removeClass('show');
                    arrow.removeClass('rotate').hide();
                    $('#yctd_clear').addClass('show');

                    selectedYCTD = yctdData;
                    loadYCTDInfo(yctdData);
                });
            }

            function highlightText(text, query) {
                if (!query) return text;
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<span class="highlight">$1</span>');
            }

            $(document).ready(function() {
                calculateTotal();
                initYCTDSearch();

                // Load initial value if exists (from $offer)
                <?php if (isset($offer) && $offer->id_yctd): ?>
                    $('#yctd_search_input').val('<?= $offer->id_yctd ?> - <?= $offer->vi_tri_offer ?>').addClass('has-value');
                    $('#yctd_clear').addClass('show');

                    // Load candidate info immediately from pre-filled values
                    <?php if (isset($offer->kqpv_id) && $offer->kqpv_id): ?>
                        $('#kqpv_id').val('<?= $offer->kqpv_id ?>');
                    <?php endif; ?>

                    // Then fetch full candidates list in background to enable dropdown
                    fetchCandidatesForYCTD('<?= $offer->id_yctd ?>');
                <?php endif; ?>

                // Load initial value from $candidate (auto-lock mode)
                <?php if (!empty($candidate) && $candidate->id_requirements): ?>
                    // Disable search box interactions
                    $('#yctd_search_input').prop('readonly', true).prop('disabled', true);
                    $('#yctd_clear, #yctd_arrow').hide();

                    // Load YCTD info by AJAX
                    $.ajax({
                        url: '<?= admin_url('propose_offer/getYCTDById') ?>',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            id: '<?= $candidate->id_requirements ?>'
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                var yctdData = response.data;
                                $('#yctd_search_input').val(yctdData.text || yctdData.code).addClass('has-value');
                                $('#ma_yctd').val('<?= $candidate->id_requirements ?>');
                                loadYCTDInfo(yctdData.yctd_data || yctdData);

                                // Load candidate info immediately
                                $('#kqpv_id').val('<?= $candidate->id ?>');
                                $('#ten_ung_vien').val('<?= $candidate->full_name ?>');
                                $('#evaluation_employee_id').val('<?= $candidate->id ?>');
                                calculateTotal();
                            }
                        },
                        error: function() {
                            alert_float('warning', 'Không thể tải thông tin YCTD');
                        }
                    });
                <?php endif; ?>

                // Form validation
                appValidateForm($('#offer-form'), {
                    ma_yctd: 'required',
                    ten_ung_vien: 'required'
                }, handleOfferSubmit);
            });

            $(document).on('change', 'input#ma_yctd', function(e) {
                ma = $(this).val();
                fetchCandidatesForYCTD(ma);
            });
        </script>