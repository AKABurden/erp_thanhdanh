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
</style>