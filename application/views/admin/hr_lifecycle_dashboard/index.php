<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Quản trị & Kiểm soát Nội bộ</title>
<script src="https://cdn.tailwindcss.com"></script>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                }
            }
        }
    }
</script>
<style>
    /* ===== RESET & GLOBAL ===== */
    .collapse {
        visibility: unset !important;
    }

    body,
    #wrapper {
        font-family: 'Inter', sans-serif;
    }

    /* ===== CUSTOM SCROLLBAR ===== */
    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: #eef2ff;
    }

    ::-webkit-scrollbar-thumb {
        background: #a5b4fc;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #818cf8;
    }

    /* ===== DASHBOARD HEADER ===== */
    .dash-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #1d4ed8 100%);
        padding: 8px 28px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 4px 24px rgba(30, 27, 75, 0.3);
    }

    .dash-header .logo-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 12px;
        padding: 7px 14px;
        backdrop-filter: blur(8px);
    }

    .dash-header .logo-wrap i {
        color: #a5b4fc;
    }

    .dash-header .logo-text {
        font-size: 17px;
        font-weight: 800;
        color: #fff;
        letter-spacing: .5px;
    }

    .dash-header .divider {
        width: 1px;
        height: 28px;
        background: rgba(255, 255, 255, 0.2);
    }

    .dash-header h1 {
        font-size: 16px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.88);
        letter-spacing: .2px;
    }

    .dash-header .time-badge {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.75);
    }

    .dash-header .time-badge i {
        color: #a5b4fc;
    }

    /* ===== MAIN WRAPPER ===== */
    .dash-main {
        padding: 28px;
        max-width: 100%;
    }

    /* ===== SECTION LABEL ===== */
    .section-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #6366f1;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, #e0e7ff, transparent);
    }

    /* ===== STAT CARDS ===== */
    .stat-card {
        border-radius: 16px;
        padding: 22px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(.4, 0, .2, 1), box-shadow 0.3s cubic-bezier(.4, 0, .2, 1);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.14);
    }

    .stat-card .card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .stat-card .card-icon i {
        color: #fff;
    }

    .stat-card .card-label {
        font-size: 12px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.75);
        letter-spacing: .3px;
    }

    .stat-card .card-value {
        font-size: 30px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .stat-card .card-delta {
        font-size: 12px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .stat-card .card-delta span.muted {
        font-weight: 400;
        color: rgba(255, 255, 255, 0.55);
    }

    .card-blue {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .card-indigo {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
    }

    .card-green {
        background: linear-gradient(135deg, #16a34a, #15803d);
    }

    .card-red {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .card-orange {
        background: linear-gradient(135deg, #f97316, #ea580c);
    }

    /* ===== CHART / PANEL CARDS ===== */
    .panel {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: box-shadow 0.3s;
    }

    .panel:hover {
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.10);
    }

    .panel-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-title i {
        color: #6366f1;
    }

    .panel-subtitle {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* ===== LIFECYCLE FUNNEL ===== */
    .lifecycle-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .lifecycle-bubble {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 10px;
        position: relative;
        transition: transform 0.25s, box-shadow 0.25s;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .lifecycle-bubble:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
    }

    .lifecycle-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        text-align: center;
    }

    .lc-gray {
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        color: #475569;
    }

    .lc-indigo {
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4338ca;
    }

    .lc-blue {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1d4ed8;
    }

    .lc-yellow {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
    }

    .lc-green {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
    }

    .lc-arrow {
        color: #cbd5e1;
        flex-shrink: 0;
        margin-top: -20px;
    }

    /* ===== ALERT ITEMS ===== */
    .alert-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 10px;
        border: 1px solid transparent;
        transition: transform 0.2s;
    }

    .alert-item:last-child {
        margin-bottom: 0;
    }

    .alert-item:hover {
        transform: translateX(3px);
    }

    .alert-item .alert-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .alert-red {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .alert-red .alert-icon-wrap {
        background: #fee2e2;
    }

    .alert-red i {
        color: #ef4444;
    }

    .alert-orange {
        background: #fff7ed;
        border-color: #fed7aa;
    }

    .alert-orange .alert-icon-wrap {
        background: #ffedd5;
    }

    .alert-orange i {
        color: #f97316;
    }

    .alert-blue {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .alert-blue .alert-icon-wrap {
        background: #dbeafe;
    }

    .alert-blue i {
        color: #3b82f6;
    }

    .alert-item .alert-title {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.4;
    }

    .alert-item .alert-time {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 3px;
    }

    /* ===== TABLE ===== */
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 700px;
    }

    .styled-table thead tr {
        background: linear-gradient(90deg, #f0f4ff, #e0e7ff);
    }

    .styled-table thead th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #6366f1;
        text-align: left;
    }

    .styled-table thead th:first-child {
        border-radius: 10px 0 0 10px;
    }

    .styled-table thead th:last-child {
        border-radius: 0 10px 10px 0;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.18s;
    }

    .styled-table tbody tr:last-child {
        border-bottom: none;
    }

    .styled-table tbody tr:hover {
        background: #f8faff;
    }

    .styled-table tbody td {
        padding: 13px 16px;
        font-size: 13px;
        color: #475569;
        vertical-align: middle;
    }

    .styled-table tbody td.name {
        font-weight: 700;
        color: #1e293b;
    }

    /* Avatar */
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        margin-right: 8px;
        flex-shrink: 0;
    }

    .av-blue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .av-purple {
        background: #ede9fe;
        color: #7c3aed;
    }

    .av-green {
        background: #dcfce7;
        color: #15803d;
    }

    /* Milestone Badges */
    .milestone-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 99px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
    }

    .milestone-badge i {
        color: #6366f1;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-yellow {
        background: #fef9c3;
        color: #a16207;
    }

    .status-yellow::before {
        background: #ca8a04;
    }

    .status-green {
        background: #dcfce7;
        color: #15803d;
    }

    .status-green::before {
        background: #16a34a;
    }

    .status-blue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-blue::before {
        background: #3b82f6;
    }

    /* ===== KPI Score Bar ===== */
    .kpi-bar-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .kpi-bar-bg {
        flex: 1;
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .kpi-bar-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 1s ease;
    }

    .kpi-score {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
    }

    /* ===== FOOTER ===== */
    .dash-footer {
        background: #fff;
        border-top: 1px solid #e0e7ff;
        padding: 14px 28px;
        text-align: center;
        font-size: 12px;
        color: #94a3b8;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(18px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .anim {
        animation: fadeInUp 0.45s ease both;
    }

    .anim-d1 {
        animation-delay: .05s;
    }

    .anim-d2 {
        animation-delay: .12s;
    }

    .anim-d3 {
        animation-delay: .19s;
    }

    .anim-d4 {
        animation-delay: .26s;
    }

    .anim-d5 {
        animation-delay: .33s;
    }

    .anim-d6 {
        animation-delay: .40s;
    }

    @keyframes pulse-ring {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, .4);
        }

        70% {
            box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    .pulse-red {
        animation: pulse-ring 2s infinite;
    }

    i[data-lucide] {
        stroke-width: 2px;
    }

    /* ===== DATATABLE CUSTOM STYLES ===== */
    .eval-dt-wrap .dataTables_wrapper {
        font-family: 'Inter', sans-serif;
    }

    .eval-dt-wrap .dataTables_length,
    .eval-dt-wrap .dataTables_filter {
        display: none;
        /* ẩn control mặc định, dùng control tuỳ chỉnh */
    }

    /* Custom toolbar */
    .eval-dt-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .eval-dt-search {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8faff;
        border: 1px solid #e0e7ff;
        border-radius: 10px;
        padding: 7px 14px;
        flex: 1;
        min-width: 160px;
        max-width: 320px;
    }

    .eval-dt-search i {
        color: #6366f1;
        flex-shrink: 0;
    }

    .eval-dt-search input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 13px;
        color: #1e293b;
        width: 100%;
        font-family: 'Inter', sans-serif;
    }

    .eval-dt-search input::placeholder {
        color: #94a3b8;
    }

    .eval-dt-perpage {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .eval-dt-perpage select {
        border: 1px solid #e0e7ff;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12px;
        font-family: 'Inter', sans-serif;
        color: #1e293b;
        background: #f8faff;
        outline: none;
        cursor: pointer;
    }

    /* Info & Pagination */
    .eval-dt-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .eval-dt-info {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 500;
    }

    .eval-dt-wrap .dataTables_paginate {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .eval-dt-wrap .dataTables_paginate .paginate_button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #475569 !important;
        cursor: pointer;
        border: 1px solid #e0e7ff;
        background: #f8faff;
        transition: background 0.18s, color 0.18s, border-color 0.18s;
        user-select: none;
    }

    .eval-dt-wrap .dataTables_paginate .paginate_button:hover {
        background: #e0e7ff;
        color: #4f46e5 !important;
        border-color: #c7d2fe;
    }

    .eval-dt-wrap .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff !important;
        border-color: #4f46e5;
    }

    .eval-dt-wrap .dataTables_paginate .paginate_button.disabled,
    .eval-dt-wrap .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4;
        cursor: default;
        background: #f8faff;
        color: #94a3b8 !important;
        border-color: #e0e7ff;
    }

    .eval-dt-wrap .dataTables_info {
        display: none;
    }

    /* ===== FILTER TOGGLE BUTTONS ===== */
    .filter-toggle {
        display: flex;
        align-items: center;
        gap: 4px;
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 10px;
        padding: 4px;
    }

    .filter-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.65);
        cursor: pointer;
        border: none;
        background: transparent;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        text-decoration: none;
        white-space: nowrap;
    }

    .filter-btn:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .filter-btn.active {
        background: rgba(255, 255, 255, 0.22);
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
    }

    .filter-btn i {
        flex-shrink: 0;
    }
</style>

<div id="wrapper" style="display:flex;flex-direction:column;min-height:100vh;">

    <!-- ===== HEADER ===== -->
    <header class="dash-header">
        <div class="logo-wrap">
            <i data-lucide="shield-check" style="width:20px;height:20px;"></i>
            <span class="logo-text">KSNB</span>
        </div>
        <div class="divider"></div>
        <h1>Dashboard Quản trị &amp; Kiểm soát Nội bộ</h1>

        <!-- Filter Toggle -->
        <div class="filter-toggle" style="margin-left:auto;">
            <a href="?filter=day" class="filter-btn <?= $filter_mode === 'day' ? 'active' : '' ?>">
                <i data-lucide="sun" style="width:13px;height:13px;"></i>
                Ngày hiện tại
            </a>
            <a href="?filter=month" class="filter-btn <?= $filter_mode === 'month' ? 'active' : '' ?>">
                <i data-lucide="calendar" style="width:13px;height:13px;"></i>
                Tháng hiện tại
            </a>
        </div>

        <div class="time-badge">
            <i data-lucide="clock" style="width:13px;height:13px;"></i>
            <span id="live-time">--:-- --/--/----</span>
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main class="dash-main" style="flex:1;">

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px;margin-bottom:28px;">
            <div class="panel anim anim-d3">
                <div style="margin-bottom:18px;">
                    <div class="panel-title"> Điều kiện chạy cron đánh giá nhân viên TV</div>
                </div>
                <div>
                    <div>+ Nhân viên ở trạng thái thử việc</div>
                    <div>+ Nhân viên chưa có phiếu đánh giá</div>
                    <div>+ Ngày vào làm + 14 ngày nhỏ hơn bằng ngày hiện tại </div>
                    <div>* Chạy tự động mỗi ngày vào 2h30 sáng </div>
                </div>
            </div>

            <div class="panel anim anim-d3">
                <div style="margin-bottom:18px;">
                    <div class="panel-title"> Điều kiện chạy cron đánh giá nhân viên CT</div>
                </div>
                <div>
                    <div>+ Nhân viên ở trạng thái đang làm việc</div>
                    <div>+ Nhân viên phải có vị trí, cấp bậc vai trò, trên vị trí phải có vòng đời đánh giá (số ngày)</div>
                    <div>+ Nếu nhân viên chưa có phiếu đánh giá sẽ lấy ngày 01/02/2026 + với số ngày vòng đời đánh giá nhỏ hơn hoặc bằng</div>
                    <div>+ Nếu nhân viên có phiếu đánh giá thì sẽ lấy phiếu đánh giá có ngày gần nhất + với số ngày vòng đời đánh giá nhỏ hơn hoặc bằng</div>
                    <div>* Chạy tự động mỗi ngày vào 2h30 sáng </div>
                </div>
            </div>
        </div>

        <!-- SECTION: KPI STATS -->
        <div class="section-label"><i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i> Chỉ số KPI tổng quan</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:28px;">

            <div class="stat-card card-blue anim anim-d1">
                <div class="card-icon"><i data-lucide="briefcase" style="width:22px;height:22px;"></i></div>
                <div>
                    <div class="card-label">KPI: Năng suất (SL CV)</div>
                    <div class="card-value"><?= formatNumber($tasks_completed_process) ?></div>
                </div>
                <?php
                $diff = $tasks_completed_process - $tasks_completed_process_old;
                if ($diff > 0) {
                    $delta_icon  = 'trending-up';
                    $delta_color = '#16a34a'; // xanh lá
                    $delta_sign  = '+';
                } elseif ($diff < 0) {
                    $delta_icon  = 'trending-down';
                    $delta_color = '#ef4444'; // đỏ
                    $delta_sign  = '';
                } else {
                    $delta_icon  = 'minus';
                    $delta_color = '#94a3b8'; // xám
                    $delta_sign  = '';
                }
                ?>
                <div class="card-delta" style="color:<?= $delta_color ?>;">
                    <i data-lucide="<?= $delta_icon ?>" style="width:14px;height:14px;"></i>
                    <?= $delta_sign . formatNumber($diff) ?> CV
                    <span class="muted">so với tháng trước</span>
                </div>
            </div>

            <div class="stat-card card-indigo anim anim-d2">
                <div class="card-icon"><i data-lucide="award" style="width:22px;height:22px;"></i></div>
                <?php
                // % chất lượng = tỉ lệ phiếu KHÔNG vi phạm / tổng phiếu
                $pr_total     = (int)$production_report;
                $pr_violate   = (int)$p3_type2_count;
                $pr_ok        = $pr_total - $pr_violate;
                $quality_pct  = $pr_total > 0 ? round($pr_ok / $pr_total * 100, 1) : 0;

                // Kỳ trước
                $pr_total_old   = (int)$production_report_old;
                $pr_violate_old = (int)$production_report_violate_old;
                $pr_ok_old      = $pr_total_old - $pr_violate_old;
                $quality_pct_old = $pr_total_old > 0 ? round($pr_ok_old / $pr_total_old * 100, 1) : 0;

                $q_diff = round($quality_pct - $quality_pct_old, 1);
                if ($q_diff > 0) {
                    $q_icon  = 'trending-up';
                    $q_color = 'rgba(255,255,255,0.85)';
                    $q_sign  = '+';
                } elseif ($q_diff < 0) {
                    $q_icon  = 'trending-down';
                    $q_color = '#c7d2fe';
                    $q_sign  = '';
                } else {
                    $q_icon  = 'minus';
                    $q_color = 'rgba(255,255,255,0.55)';
                    $q_sign  = '';
                }
                ?>
                <div>
                    <div class="card-label">KPI: Chất lượng</div>
                    <div class="card-value"><?= $quality_pct ?>%</div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.55);margin-top:2px;">
                        <?= $pr_ok ?> / <?= $pr_total ?> phiếu không vi phạm
                    </div>
                </div>
                <div class="card-delta" style="color:<?= $q_color ?>;">
                    <i data-lucide="<?= $q_icon ?>" style="width:14px;height:14px;"></i>
                    <?= $q_sign . $q_diff ?>%
                    <span class="muted">so với <?= $filter_label_old ?></span>
                </div>
            </div>


            <div class="stat-card card-green anim anim-d3">
                <div class="card-icon"><i data-lucide="check-square" style="width:22px;height:22px;"></i></div>
                <?php
                // Tổng tasks hiện tại và kỳ trước
                $total_now = $tasks_no_check + $tasks_in_progress + $tasks_completed_process;
                $total_old_now = $tasks_no_check_old + $tasks_in_progress_old + $tasks_completed_process_old;

                // % hoàn thành
                $pct_now = $total_now > 0 ? round(($tasks_completed_process / $total_now) * 100, 1) : 0;
                $pct_old_now = $total_old_now > 0 ? round(($tasks_completed_process_old / $total_old_now) * 100, 1) : 0;

                $pct_diff = round($pct_now - $pct_old_now, 1);
                if ($pct_diff > 0) {
                    $pct_icon  = 'trending-up';
                    $pct_color = 'rgba(255,255,255,0.85)';
                    $pct_sign  = '+';
                } elseif ($pct_diff < 0) {
                    $pct_icon  = 'trending-down';
                    $pct_color = '#fca5a5';
                    $pct_sign  = '';
                } else {
                    $pct_icon  = 'minus';
                    $pct_color = 'rgba(255,255,255,0.55)';
                    $pct_sign  = '';
                }
                ?>
                <div>
                    <div class="card-label">Tiến độ (Hoàn thành)</div>
                    <div class="card-value"><?= $pct_now ?>%</div>
                </div>
                <div class="card-delta" style="color:<?= $pct_color ?>;">
                    <i data-lucide="<?= $pct_icon ?>" style="width:14px;height:14px;"></i>
                    <?= $pct_sign . $pct_diff ?>%
                    <span class="muted">so với <?= $filter_label_old ?></span>
                </div>
            </div>


            <div class="stat-card card-red anim anim-d4">
                <div class="card-icon pulse-red"><i data-lucide="alert-triangle" style="width:22px;height:22px;"></i></div>
                <div>
                    <div class="card-label">Vi phạm &amp; Lỗi (KPH)</div>
                    <div class="card-value"><?= formatNumber($production_report) ?></div>
                </div>
                <div class="card-delta">
                    <?php
                    $diff = $production_report - $production_report_old;
                    if ($diff > 0) {
                        $delta_icon  = 'trending-up';
                        $delta_color = '#16a34a'; // xanh lá
                        $delta_sign  = '+';
                    } elseif ($diff < 0) {
                        $delta_icon  = 'trending-down';
                        $delta_color = '#ef4444'; // đỏ
                        $delta_sign  = '';
                    } else {
                        $delta_icon  = 'minus';
                        $delta_color = '#94a3b8'; // xám
                        $delta_sign  = '';
                    }
                    ?>
                    <i data-lucide="<?= $delta_icon ?>" style="width:14px;height:14px;"></i> <?= $delta_sign . formatNumber($diff) ?>
                    <span class="muted">so với tháng trước</span>
                </div>
            </div>

            <!-- <div class="stat-card card-orange anim anim-d5">
                <div class="card-icon"><i data-lucide="list-checks" style="width:22px;height:22px;"></i></div>
                <div>
                    <div class="card-label">Quy trình chưa check hết</div>
                    <div class="card-value"><?= formatNumber($tasks_incomplete_process) ?></div>
                </div>
                <?php
                $diff = $tasks_incomplete_process - $tasks_incomplete_process_old;
                if ($diff > 0) {
                    $delta_icon  = 'trending-up';
                    $delta_color = '#ef4444';
                    $delta_sign  = '+';
                } elseif ($diff < 0) {
                    $delta_icon  = 'trending-down';
                    $delta_color = '#16a34a';
                    $delta_sign  = '';
                } else {
                    $delta_icon  = 'minus';
                    $delta_color = 'rgba(255,255,255,0.75)';
                    $delta_sign  = '';
                }
                ?>
                <div class="card-delta" style="color:<?= $delta_color ?>;">
                    <i data-lucide="<?= $delta_icon ?>" style="width:14px;height:14px;"></i>
                    <?= $delta_sign . formatNumber($diff) ?> CV
                    <span class="muted">so với tháng trước</span>
                </div>
            </div> -->

        </div>

        <!-- SECTION: CHARTS -->
        <div class="section-label"><i data-lucide="activity" style="width:14px;height:14px;"></i> Biểu đồ thống kê</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px;margin-bottom:28px;">

            <!-- Line Chart -->
            <div class="panel anim anim-d2">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;">
                    <div>
                        <div class="panel-title"><i data-lucide="trending-up" style="width:16px;height:16px;"></i> Hiệu suất hoạt động</div>
                        <div class="panel-subtitle">Số vi phạm theo tháng</div>
                    </div>
                    <select id="kpiRangeSelect" style="font-size:11px;background:#f0f4ff;border:1px solid #c7d2fe;color:#4f46e5;border-radius:8px;padding:5px 10px;outline:none;font-weight:600;">
                        <option value="6month">6 tháng gần đây</option>
                        <option value="year">Năm nay</option>
                    </select>
                </div>
                <div style="position:relative;height:220px;">
                    <canvas id="kpiChart"></canvas>
                    <div id="kpiChartLoading" style="display:none;position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.75);border-radius:8px;">
                        <div style="width:28px;height:28px;border:3px solid #e0e7ff;border-top-color:#6366f1;border-radius:50%;animation:spin 0.7s linear infinite;"></div>
                    </div>
                </div>
            </div>


            <!-- Doughnut Chart -->
            <div class="panel anim anim-d3">
                <div style="margin-bottom:18px;">
                    <div class="panel-title"><i data-lucide="pie-chart" style="width:16px;height:16px;"></i> Phân bổ tiến độ CV</div>
                    <div class="panel-subtitle">Tỷ lệ trạng thái công việc</div>
                </div>
                <div style="position:relative;height:220px;display:flex;align-items:center;justify-content:center;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

        </div>
        
        <div class="section-label"><i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i> Biểu đồ KPI năm 2026</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px;margin-bottom:28px;">

            <!-- Chart 1: KPI Khách hàng – Pie Chart -->
            <div class="panel anim anim-d2">
                <div style="margin-bottom:14px;">
                    <div class="panel-title"><i data-lucide="users" style="width:16px;height:16px;"></i> KPI Khách hàng 2026</div>
                    <div class="panel-subtitle">Phân bổ trạng thái theo điểm KPI</div>
                </div>
                <div style="position:relative;height:220px;display:flex;align-items:center;justify-content:center;">
                    <canvas id="kpiClientPieChart"></canvas>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;justify-content:center;">
                    <?php
                    $clientColors = ['Khách Tốt'=>'#22c55e','Bình Thường'=>'#3b82f6','Cảnh Báo'=>'#f59e0b','Nguy Cơ Mất Khách'=>'#ef4444'];
                    foreach ($kpi_client_status as $label => $cnt):
                    ?>
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#475569;">
                        <span style="width:10px;height:10px;border-radius:50%;background:<?= $clientColors[$label] ?>;display:inline-block;"></span>
                        <?= $label ?>: <strong><?= $cnt ?></strong>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Chart 2: KPI Nhà cung cấp – Bar Chart -->
            <div class="panel anim anim-d3">
                <div style="margin-bottom:14px;">
                    <div class="panel-title"><i data-lucide="package" style="width:16px;height:16px;"></i> KPI Nhà cung cấp 2026</div>
                    <div class="panel-subtitle">Phân bổ trạng thái theo điểm KPI</div>
                </div>
                <div style="position:relative;height:220px;">
                    <canvas id="kpiSupplierBarChart"></canvas>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;justify-content:center;">
                    <?php
                    $supplierColors = ['Nhà cung cấp tốt'=>'#22c55e','Bình thường'=>'#6366f1','Cảnh báo'=>'#f59e0b','Cần xem xét thay thế'=>'#ef4444'];
                    foreach ($kpi_supplier_status as $label => $cnt):
                    ?>
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#475569;">
                        <span style="width:10px;height:10px;border-radius:3px;background:<?= $supplierColors[$label] ?>;display:inline-block;"></span>
                        <?= $label ?>: <strong><?= $cnt ?></strong>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Chart 3: KPI Ngân sách phòng ban – Doughnut Chart -->
            <div class="panel anim anim-d4">
                <div style="margin-bottom:14px;">
                    <div class="panel-title"><i data-lucide="landmark" style="width:16px;height:16px;"></i> KPI Ngân sách Phòng ban 2026</div>
                    <div class="panel-subtitle">Phân bổ trạng thái sử dụng ngân sách</div>
                </div>
                <div style="position:relative;height:220px;display:flex;align-items:center;justify-content:center;">
                    <canvas id="kpiBudgetDoughnutChart"></canvas>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;justify-content:center;">
                    <?php
                    $budgetColors = ['Tốt'=>'#22c55e','Đạt'=>'#3b82f6','Cảnh báo'=>'#f59e0b','Vượt'=>'#ef4444'];
                    foreach ($kpi_budget_status as $label => $cnt):
                    ?>
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#475569;">
                        <span style="width:10px;height:10px;border-radius:50%;background:<?= $budgetColors[$label] ?>;display:inline-block;"></span>
                        <?= $label ?>: <strong><?= $cnt ?></strong>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <?php
        // Chuẩn bị data JSON cho JS charts
        $jsClientLabels   = json_encode(array_keys($kpi_client_status));
        $jsClientData     = json_encode(array_values($kpi_client_status));
        $jsClientColors   = json_encode(array_values($clientColors));

        $jsSupplierLabels = json_encode(array_keys($kpi_supplier_status));
        $jsSupplierData   = json_encode(array_values($kpi_supplier_status));
        $jsSupplierColors = json_encode(array_values($supplierColors));

        $jsBudgetLabels   = json_encode(array_keys($kpi_budget_status));
        $jsBudgetData     = json_encode(array_values($kpi_budget_status));
        $jsBudgetColors   = json_encode(array_values($budgetColors));
        ?>
        <script>
        (function() {
            // ===== PIE CHART: KPI Khách hàng =====
            var ctxClient = document.getElementById('kpiClientPieChart').getContext('2d');
            new Chart(ctxClient, {
                type: 'pie',
                data: {
                    labels: <?= $jsClientLabels ?>,
                    datasets: [{
                        data: <?= $jsClientData ?>,
                        backgroundColor: <?= $jsClientColors ?>,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                    var pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                                    return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // ===== BAR CHART: KPI Nhà cung cấp =====
            var ctxSupplier = document.getElementById('kpiSupplierBarChart').getContext('2d');
            new Chart(ctxSupplier, {
                type: 'bar',
                data: {
                    labels: <?= $jsSupplierLabels ?>,
                    datasets: [{
                        label: 'Số nhà cung cấp',
                        data: <?= $jsSupplierData ?>,
                        backgroundColor: <?= $jsSupplierColors ?>,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 52
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) { return ' ' + ctx.parsed.y + ' nhà cung cấp'; }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#64748b', maxRotation: 15 }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { stepSize: 1, font: { size: 11 }, color: '#94a3b8' }
                        }
                    }
                }
            });

            // ===== DOUGHNUT CHART: KPI Ngân sách phòng ban =====
            var ctxBudget = document.getElementById('kpiBudgetDoughnutChart').getContext('2d');
            new Chart(ctxBudget, {
                type: 'doughnut',
                data: {
                    labels: <?= $jsBudgetLabels ?>,
                    datasets: [{
                        data: <?= $jsBudgetData ?>,
                        backgroundColor: <?= $jsBudgetColors ?>,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6,
                        cutout: '65%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                    var pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                                    return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        })();
        </script>


        <!-- SECTION: LIFECYCLE + ALERTS -->
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:28px;" class="anim anim-d4">

            <!-- Lifecycle Funnel -->
            <div class="panel">
                <div style="margin-bottom:22px;">
                    <div class="panel-title"><i data-lucide="git-branch" style="width:16px;height:16px;"></i> Chu trình &amp; Vòng đời công việc</div>
                    <div class="panel-subtitle">Trạng thái luân chuyển công việc trong hệ thống</div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:4px;padding:10px 0;">

                    <div class="lifecycle-step">
                        <div class="lifecycle-bubble lc-gray"><?= $tasks_no_check + $tasks_in_progress ?></div>
                        <div class="lifecycle-label">Tạo mới</div>
                    </div>
                    <div style="flex-shrink:0;color:#c7d2fe;margin-bottom:20px;">
                        <i data-lucide="chevron-right" style="width:22px;height:22px;"></i>
                    </div>



                    <div class="lifecycle-step">
                        <div class="lifecycle-bubble lc-blue"><?= $tasks_in_progress ?></div>
                        <div class="lifecycle-label">Đang thực hiện</div>
                    </div>
                    <div style="flex-shrink:0;color:#c7d2fe;margin-bottom:20px;">
                        <i data-lucide="chevron-right" style="width:22px;height:22px;"></i>
                    </div>



                    <div class="lifecycle-step">
                        <div class="lifecycle-bubble lc-green"><?= $tasks_completed_process ?></div>
                        <div class="lifecycle-label">CLOSED</div>
                    </div>

                </div>

                <!-- Mini Progress Bar -->
                <div style="margin-top:18px;padding:14px 16px;background:#f8faff;border-radius:12px;border:1px solid #e0e7ff;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                        <span style="font-size:12px;font-weight:600;color:#475569;">Tổng tiến độ hoàn thành</span>
                        <?php $total_tasks = $tasks_no_check + $tasks_in_progress + $tasks_completed_process;
                        $pct = $total_tasks > 0 ? round(($tasks_completed_process / $total_tasks) * 100, 2) : 0; ?>
                        <span style="font-size:12px;font-weight:700;color:#4f46e5;"><?= $pct ?>%</span>
                    </div>
                    <div style="height:8px;background:#e0e7ff;border-radius:99px;overflow:hidden;">
                        <div style="width:<?= $pct ?>%;height:100%;background:linear-gradient(90deg,#6366f1,#2563eb);border-radius:99px;"></div>
                    </div>
                </div>
            </div>

            <!-- Alerts Panel -->
            <div class="panel">
                <div style="margin-bottom:18px;">
                    <div class="panel-title">
                        <i data-lucide="shield-alert" style="width:16px;height:16px;color:#ef4444;"></i>
                        Vi phạm &amp; Cảnh báo KSNB
                    </div>
                    <div class="panel-subtitle"><?= $filter_label ?></div>
                </div>

                <?php
                $hasAlert = $p3_type2_count > 0 || $p3_type1_count > 0 || $p3_type3_count > 0 || $p3_type4_count > 0;
                ?>

                <?php if ($p3_type2_count > 0): ?>
                    <div class="alert-item alert-red">
                        <div class="alert-icon-wrap">
                            <i data-lucide="alert-triangle" style="width:16px;height:16px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div class="alert-title">Phiếu vi phạm</div>
                        </div>
                        <div style="background:#ef4444;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
                            <?= $p3_type2_count ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($p3_type1_count > 0): ?>
                    <div class="alert-item alert-orange">
                        <div class="alert-icon-wrap">
                            <i data-lucide="file-text" style="width:16px;height:16px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div class="alert-title">BCKPH chưa hoàn thành</div>
                        </div>
                        <div style="background:#f97316;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
                            <?= $p3_type1_count ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($p3_type3_count > 0): ?>
                    <div class="alert-item alert-blue">
                        <div class="alert-icon-wrap">
                            <i data-lucide="clock" style="width:16px;height:16px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div class="alert-title">Công việc chưa hoàn thành</div>
                        </div>
                        <div style="background:#3b82f6;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
                            <?= $p3_type3_count ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($p3_type4_count > 0): ?>
                    <div class="alert-item" style="background:#fffbeb;border-color:#fde68a;">
                        <div class="alert-icon-wrap" style="background:#fef3c7;">
                            <i data-lucide="clipboard-x" style="width:16px;height:16px;color:#d97706;"></i>
                        </div>
                        <div style="flex:1;">
                            <div class="alert-title">Audit fail</div>
                        </div>
                        <div style="background:#d97706;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
                            <?= $p3_type4_count ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$hasAlert): ?>
                    <div style="text-align:center;padding:32px 16px;color:#94a3b8;">
                        <i data-lucide="check-circle-2" style="width:36px;height:36px;color:#22c55e;margin-bottom:10px;display:block;margin-left:auto;margin-right:auto;"></i>
                        <div style="font-size:13px;font-weight:600;color:#475569;">Không có cảnh báo</div>
                        <div style="font-size:12px;margin-top:4px;">Mọi chỉ số trong tháng đều ổn định</div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <!-- SECTION: HR TABLE -->
        <div class="section-label anim anim-d5"><i data-lucide="users" style="width:14px;height:14px;"></i> Lộ trình sự nghiệp &amp; Đánh giá định kỳ</div>
        <div class="panel anim anim-d5" style="margin-bottom:28px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
                <div>
                    <div class="panel-title"><i data-lucide="user-check" style="width:16px;height:16px;"></i> Danh sách đánh giá nhân sự</div>
                    <div class="panel-subtitle">Hệ thống tự động sinh Phiếu YC đánh giá nâng bậc dựa trên thời gian làm việc</div>
                </div>
            </div>
            <div class="eval-dt-toolbar">
                <div class="eval-dt-search">
                    <i data-lucide="search" style="width:14px;height:14px;"></i>
                    <input type="text" id="evalTableSearch" placeholder="Tìm kiếm nhân sự, vị trí...">
                </div>
                <div class="eval-dt-perpage">
                    <span>Hiển thị</span>
                    <select id="evalTableLength">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="-1">Tất cả</option>
                    </select>
                    <span>dòng</span>
                </div>
            </div>
            <div class="eval-dt-wrap" style="overflow-x:auto;">
                <table class="styled-table" id="evalTable">
                    <thead>
                        <tr>
                            <th>Nhân sự</th>
                            <th>Vị trí</th>
                            <th>Cột mốc</th>
                            <th>Điểm KPI</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $av_colors = ['av-blue', 'av-purple', 'av-green'];
                        $av_bar_gradients = [
                            'av-blue'   => 'linear-gradient(90deg,#6366f1,#4f46e5)',
                            'av-purple' => 'linear-gradient(90deg,#a855f7,#7c3aed)',
                            'av-green'  => 'linear-gradient(90deg,#22c55e,#16a34a)',
                        ];
                        if (!empty($eval_list)):
                            foreach ($eval_list as $idx => $ev):
                                $name      = trim($ev['staff_name']);
                                // Tạo chữ viết tắt từ 2 từ đầu tiên
                                $words     = explode(' ', $name);
                                $abbr      = '';
                                foreach (array_slice($words, 0, 2) as $w) {
                                    $abbr .= mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'), 'UTF-8');
                                }
                                $av_class  = $av_colors[$idx % count($av_colors)];
                                $gradient  = $av_bar_gradients[$av_class];
                                $milestone = (int)$ev['milestone_month'];
                                $point     = (float)$ev['point'];
                                $has_point = !empty($ev['rating_list']) && $point > 0;
                                $bar_width = $has_point ? (min(5, $point) / 5) * 100 : 0;

                        ?>
                                <tr>
                                    <td>
                                        <span class="user-avatar <?= $av_class ?>"><?= $abbr ?></span>
                                        <span class="name">
                                            <a href="<?= base_url('admin/probationary_assessment/detail/' . $ev['id']) ?>?type=2" style="color:inherit;text-decoration:none;">
                                                <?= htmlspecialchars($name) ?>
                                            </a>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($ev['name_role'] ?? '—') ?></td>
                                    <td>
                                        <span class="milestone-badge">
                                            <i data-lucide="clock" style="width:11px;height:11px;"></i>
                                            Đánh giá <?= $milestone ?> tháng
                                        </span>
                                    </td>
                                    <td>
                                        <div class="kpi-bar-wrap">
                                            <div class="kpi-bar-bg">
                                                <div class="kpi-bar-fill" style="width:<?= $bar_width ?>%;background:<?= $gradient ?>;"></div>
                                            </div>
                                            <?php if ($has_point): ?>
                                                <span class="kpi-score"><?= number_format($point, 1) ?>/5</span>
                                            <?php else: ?>
                                                <span class="kpi-score" style="color:#94a3b8;">--</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($has_point): ?>
                                            <span class="status-badge status-green">Hoàn thành</span>
                                        <?php else: ?>
                                            <span class="status-badge status-blue">Chưa đánh giá</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:32px 16px;color:#94a3b8;">
                                    <i data-lucide="inbox" style="width:32px;height:32px;display:block;margin:0 auto 10px;color:#c7d2fe;"></i>
                                    <div style="font-size:13px;font-weight:600;color:#475569;">Không có dữ liệu đánh giá</div>
                                    <div style="font-size:12px;margin-top:4px;">Chưa có phiếu đánh giá nhân viên nào trong năm <?= date('Y') ?></div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="eval-dt-bottom">
                <div class="eval-dt-info" id="evalTableInfo"></div>
                <div id="evalTablePaging"></div>
            </div>
        </div>
        <!-- SECTION: ĐÁNH GIÁ RỦI RO -->
        <div class="section-label anim anim-d5"><i data-lucide="shield-alert" style="width:14px;height:14px;"></i> Cần đánh giá rủi ro &mdash; <span style="font-weight:500;color:#ef4444;"><?= $filter_label ?></span></div>
        <div class="panel anim anim-d5" style="margin-bottom:28px;border-left:4px solid #ef4444;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
                <div>
                    <div class="panel-title"><i data-lucide="alert-triangle" style="width:16px;height:16px;color:#ef4444;"></i> Phiếu có rủi ro cao chưa đánh giá</div>
                    <div class="panel-subtitle">Các phiếu trong <strong><?= $filter_label ?></strong></div>
                </div>
                <?php if (!empty($big_risk_list)): ?>
                    <div style="background:#fee2e2;color:#ef4444;font-size:12px;font-weight:700;padding:5px 14px;border-radius:99px;border:1px solid #fecaca;">
                        <?= count($big_risk_list) ?> phiếu cần xử lý
                    </div>
                <?php endif; ?>
            </div>
            <div style="overflow-x:auto;">
                <?php if (!empty($big_risk_list)): ?>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Nhân sự</th>
                                <th>Vị trí</th>
                                <th>Cấp bậc</th>
                                <th>Mã phiếu</th>
                                <th>Ngày tạo</th>
                                <th>Mức rủi ro</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($big_risk_list as $ri => $risk): ?>
                                <?php
                                $risk_val  = (int)$risk['big_risk'];
                                $risk_color = $risk_val >= 3 ? '#dc2626' : ($risk_val == 2 ? '#f97316' : '#eab308');
                                $risk_bg    = $risk_val >= 3 ? '#fee2e2' : ($risk_val == 2 ? '#ffedd5' : '#fef9c3');
                                $risk_label = $risk_val >= 3 ? 'Rất cao' : ($risk_val == 2 ? 'Cao' : 'Trung bình');
                                ?>
                                <tr>
                                    <td style="color:#94a3b8;font-size:12px;"><?= $ri + 1 ?></td>
                                    <td class="name">
                                        <a href="<?= base_url('admin/probationary_assessment/detail/' . $risk['id'] . '?type=2') ?>" style="color:inherit;text-decoration:none;">
                                            <?= htmlspecialchars($risk['staff_name'] ?: '—') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($risk['name_role'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($risk['code_role_level'] ?? '—') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/probationary_assessment/detail/' . $risk['id'] . '?type=2') ?>" style="font-weight:600;color:#6366f1;font-size:12px;">
                                            <?= htmlspecialchars($risk['code']) ?>
                                        </a>
                                    </td>
                                    <td style="font-size:12px;color:#94a3b8;"><?= date('d/m/Y', strtotime($risk['date'])) ?></td>
                                    <td>
                                        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;background:<?= $risk_bg ?>;color:<?= $risk_color ?>;border:1px solid <?= $risk_color ?>30;">
                                            <span style="width:6px;height:6px;border-radius:50%;background:<?= $risk_color ?>;display:inline-block;"></span>
                                            <?= $risk_label ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/probationary_assessment/detail/' . $risk['id'] . '?type=2') ?>" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;background:#fef2f2;color:#ef4444;font-size:12px;font-weight:600;border:1px solid #fecaca;text-decoration:none;white-space:nowrap;">
                                            <i data-lucide="clipboard-check" style="width:13px;height:13px;"></i> Đánh giá
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align:center;padding:32px 16px;color:#94a3b8;">
                        <i data-lucide="shield-check" style="width:36px;height:36px;color:#22c55e;display:block;margin:0 auto 10px;"></i>
                        <div style="font-size:13px;font-weight:600;color:#475569;">Không có phiếu rủi ro cần xử lý</div>
                        <div style="font-size:12px;margin-top:4px;">Tất cả phiếu trong <?= $filter_label ?> đã được đánh giá hoặc không có rủi ro</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- SECTION: TOP 5 NHÂN VIÊN -->
        <div class="section-label anim anim-d6"><i data-lucide="trophy" style="width:14px;height:14px;"></i> Top 5 nhân viên theo từng chỉ số vi phạm</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-bottom:28px;" class="anim anim-d6">

            <!-- Top 5: Phiếu vi phạm -->
            <div class="panel">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:34px;height:34px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="alert-triangle" style="width:17px;height:17px;color:#ef4444;"></i>
                    </div>
                    <div>
                        <div class="panel-title" style="color:#ef4444;">Phiếu vi phạm</div>
                        <div class="panel-subtitle"><?= $filter_label ?></div>
                    </div>
                </div>
                <?php if (!empty($top5_type2)): ?>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <?php foreach ($top5_type2 as $rank => $row): ?>
                            <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#fff1f2' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#fecdd3' : '#f1f5f9' ?>;">
                                <div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#ef4444' : ($rank === 1 ? '#f87171' : '#fca5a5') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
                                <div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
                                <div style="background:#ef4444;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center;padding:24px 12px;color:#94a3b8;">
                        <i data-lucide="check-circle-2" style="width:28px;height:28px;color:#22c55e;display:block;margin:0 auto 8px;"></i>
                        <div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Top 5: BCKPH chưa hoàn thành -->
            <div class="panel">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:34px;height:34px;border-radius:10px;background:#ffedd5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="file-text" style="width:17px;height:17px;color:#f97316;"></i>
                    </div>
                    <div>
                        <div class="panel-title" style="color:#f97316;">BCKPH chưa hoàn thành</div>
                        <div class="panel-subtitle"><?= $filter_label ?></div>
                    </div>
                </div>
                <?php if (!empty($top5_type1)): ?>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <?php foreach ($top5_type1 as $rank => $row): ?>
                            <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#fff7ed' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#fed7aa' : '#f1f5f9' ?>;">
                                <div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#f97316' : ($rank === 1 ? '#fb923c' : '#fdba74') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
                                <div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
                                <div style="background:#f97316;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center;padding:24px 12px;color:#94a3b8;">
                        <i data-lucide="check-circle-2" style="width:28px;height:28px;color:#22c55e;display:block;margin:0 auto 8px;"></i>
                        <div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Top 5: Công việc chưa hoàn thành -->
            <div class="panel">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:34px;height:34px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="clock" style="width:17px;height:17px;color:#3b82f6;"></i>
                    </div>
                    <div>
                        <div class="panel-title" style="color:#3b82f6;">Công việc chưa hoàn thành</div>
                        <div class="panel-subtitle"><?= $filter_label ?></div>
                    </div>
                </div>
                <?php if (!empty($top5_type3)): ?>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <?php foreach ($top5_type3 as $rank => $row): ?>
                            <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#eff6ff' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#bfdbfe' : '#f1f5f9' ?>;">
                                <div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#3b82f6' : ($rank === 1 ? '#60a5fa' : '#93c5fd') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
                                <div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
                                <div style="background:#3b82f6;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center;padding:24px 12px;color:#94a3b8;">
                        <i data-lucide="check-circle-2" style="width:28px;height:28px;color:#22c55e;display:block;margin:0 auto 8px;"></i>
                        <div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Top 5: Audit fail -->
            <div class="panel">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:34px;height:34px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="clipboard-x" style="width:17px;height:17px;color:#d97706;"></i>
                    </div>
                    <div>
                        <div class="panel-title" style="color:#d97706;">Audit fail</div>
                        <div class="panel-subtitle"><?= $filter_label ?></div>
                    </div>
                </div>
                <?php if (!empty($top5_type4)): ?>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <?php foreach ($top5_type4 as $rank => $row): ?>
                            <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#fffbeb' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#fde68a' : '#f1f5f9' ?>;">
                                <div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#d97706' : ($rank === 1 ? '#f59e0b' : '#fcd34d') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
                                <div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
                                <div style="background:#d97706;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center;padding:24px 12px;color:#94a3b8;">
                        <i data-lucide="check-circle-2" style="width:28px;height:28px;color:#22c55e;display:block;margin:0 auto 8px;"></i>
                        <div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="dash-footer">
        &copy; 2026 FOSO &mdash; Hệ thống Kiểm soát Nội bộ (KSNB)
    </footer>

</div>
<?php init_tail(); ?>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    /* Override DataTables CSS cho bảng eval — số ưu tiên cao hơn Tailwind */
    #evalTable_wrapper .dataTables_length,
    #evalTable_wrapper .dataTables_filter {
        display: none !important;
    }

    #evalTable_wrapper .dataTables_info {
        font-size: 12px !important;
        color: #94a3b8 !important;
        font-weight: 500 !important;
        font-family: 'Inter', sans-serif !important;
        padding-top: 0 !important;
    }

    #evalTable_wrapper .dataTables_paginate {
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
        flex-wrap: wrap !important;
    }

    #evalTable_wrapper .dataTables_paginate span {
        display: flex !important;
        gap: 4px !important;
    }

    #evalTable_wrapper .dataTables_paginate .paginate_button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 32px !important;
        height: 32px !important;
        padding: 0 10px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        font-family: 'Inter', sans-serif !important;
        color: #475569 !important;
        cursor: pointer !important;
        border: 1px solid #e0e7ff !important;
        background: #f8faff !important;
        box-shadow: none !important;
        box-sizing: border-box !important;
        line-height: 1 !important;
        text-decoration: none !important;
        transition: background 0.18s, color 0.18s !important;
    }

    #evalTable_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e0e7ff !important;
        color: #4f46e5 !important;
        border-color: #c7d2fe !important;
    }

    #evalTable_wrapper .dataTables_paginate .paginate_button.current,
    #evalTable_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
        color: #fff !important;
        border-color: #4f46e5 !important;
    }

    #evalTable_wrapper .dataTables_paginate .paginate_button.disabled,
    #evalTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4 !important;
        cursor: default !important;
        background: #f8faff !important;
        color: #94a3b8 !important;
        border-color: #e0e7ff !important;
    }

    #evalTable_wrapper .dataTables_paginate .ellipsis {
        display: inline-flex !important;
        align-items: center !important;
        height: 32px !important;
        padding: 0 4px !important;
        font-size: 12px !important;
        color: #94a3b8 !important;
        border: none !important;
        background: none !important;
        cursor: default !important;
    }

    #evalTable_wrapper .dataTables_bottom {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-top: 14px !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
    }
</style>
<script>
    // Lucide Icons
    lucide.createIcons();

    // ===== DataTable: Danh sách đánh giá nhân sự =====
    var evalDT = $('#evalTable').DataTable({
        pageLength: 10,
        pagingType: 'simple_numbers',
        dom: '<"dataTables_top"fl>t<"dataTables_bottom"ip>',
        language: {
            search: '',
            lengthMenu: '_MENU_ dòng',
            info: 'Hiển thị _START_–_END_ / _TOTAL_ nhân sự',
            infoEmpty: 'Không có dữ liệu',
            infoFiltered: '(lọc từ _MAX_ bản ghi)',
            zeroRecords: 'Không tìm thấy kết quả',
            paginate: {
                first: '«',
                previous: '‹',
                next: '›',
                last: '»'
            }
        },
        drawCallback: function() {
            lucide.createIcons();
        }
    });

    // Kết nối ô tìm kiếm tuỳ chỉnh
    document.getElementById('evalTableSearch').addEventListener('keyup', function() {
        evalDT.search(this.value).draw();
    });

    // Kết nối select số dòng/trang tuỳ chỉnh
    document.getElementById('evalTableLength').addEventListener('change', function() {
        evalDT.page.len(parseInt(this.value)).draw();
    });

    // Live Clock
    function updateTime() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const mo = String(now.getMonth() + 1).padStart(2, '0');
        const yy = now.getFullYear();
        document.getElementById('live-time').textContent = `${hh}:${mm} ${dd}/${mo}/${yy}`;
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Chart.js defaults
    if (Chart.defaults.font) {
        Chart.defaults.font.family = "'Inter', sans-serif";
    }
    Chart.defaults.color = '#64748b';

    // ===== @keyframes spin cho loading spinner =====
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `@keyframes spin { to { transform: rotate(360deg); } }`;
    document.head.appendChild(styleSheet);

    // --- Line Chart (Ajax từ vi phạm thực tế) ---
    let kpiChartInstance = null;

    function loadKpiChart(range) {
        const loading = document.getElementById('kpiChartLoading');
        if (loading) loading.style.display = 'flex';

        fetch(`<?= base_url('admin/hr_lifecycle_dashboard/get_kpi_chart_data') ?>?range=` + range)
            .then(r => r.json())
            .then(function(res) {
                const ctxKpi = document.getElementById('kpiChart').getContext('2d');

                // Destroy chart cũ nếu tồn tại
                if (kpiChartInstance) {
                    kpiChartInstance.destroy();
                    kpiChartInstance = null;
                }

                const gradientKpi = ctxKpi.createLinearGradient(0, 0, 0, 220);
                gradientKpi.addColorStop(0, 'rgba(239,68,68,0.22)');
                gradientKpi.addColorStop(1, 'rgba(239,68,68,0)');

                kpiChartInstance = new Chart(ctxKpi, {
                    type: 'line',
                    data: {
                        labels: res.labels,
                        datasets: [{
                            label: 'Vi phạm',
                            data: res.data,
                            borderColor: '#ef4444',
                            backgroundColor: gradientKpi,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#ef4444',
                            pointBorderWidth: 2.5,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            fill: true,
                            tension: 0.45
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                cornerRadius: 10,
                                titleFont: {
                                    size: 12,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 14,
                                    weight: '700'
                                },
                                displayColors: false,
                                callbacks: {
                                    label: function(ctx) {
                                        return ' Vi phạm: ' + ctx.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f0f4ff',
                                    drawBorder: false
                                },
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });

                if (loading) loading.style.display = 'none';
            })
            .catch(function() {
                if (loading) loading.style.display = 'none';
            });
    }

    // Load lần đầu
    loadKpiChart('6month');

    // Khi đổi lựa chọn
    document.getElementById('kpiRangeSelect').addEventListener('change', function() {
        loadKpiChart(this.value);
    });

    // --- Doughnut Chart ---
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Hoàn thành', 'Đang xử lý', 'Trễ hạn'],
            datasets: [{
                data: [<?= $tasks_completed_process ?>, <?= $tasks_in_progress ?>, <?= $tasks_overdue ?>],
                backgroundColor: ['#16a34a', '#6366f1', '#ef4444'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyleWidth: 10,
                        padding: 16,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(ctx) {
                            return ` ${ctx.label}: ${ctx.parsed}%`;
                        }
                    }
                }
            }
        }
    });
</script>