<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI ĐÁNH GIÁ NHÂN SỰ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        table {
            width: 100%;
            text-align: left;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead {
            background-color: rgba(248, 250, 252, .8);
            color: #475569;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }

        td {
            padding: 0.625rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        tbody tr:hover {
            background-color: rgba(248, 250, 252, .6);
            transition: background-color .15s;
        }

        /* Sidebar override (dùng màu nhạt giống RiskDashboard) */
        .bg-slate-950 {
            background-color: #FEF7E2 !important;
        }

        .text-slate-300 {
            color: #000 !important;
        }

        .border-slate-800\/60 {
            border: none !important;
        }

        /* Toggle switch */
        .onoffswitch {
            position: relative;
            width: 56px;
            user-select: none;
            flex-shrink: 0;
        }

        .onoffswitch-checkbox {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .onoffswitch-label {
            display: block;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid #d1d5db;
            border-radius: 20px;
            transition: border-color .2s;
        }

        .onoffswitch-label:before {
            content: "";
            display: block;
            width: 26px;
            height: 26px;
            margin: 2px;
            background: #d1d5db;
            border-radius: 50%;
            transition: background .2s, transform .2s;
        }

        .onoffswitch-checkbox:checked+.onoffswitch-label {
            border-color: #16a34a;
        }

        .onoffswitch-checkbox:checked+.onoffswitch-label:before {
            background: #16a34a;
            transform: translateX(24px);
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-box {
            background: #fff;
            border-radius: 12px;
            width: 100%;
            max-width: 560px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
        }

        /* Gate badges */
        .badge-dat {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-fail {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-giam-sat {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-low {
            background: #dcfce7;
            color: #166534;
        }

        .badge-medium {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-high {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 overflow-hidden h-screen flex">

    <!-- ===== SIDEBAR ===== -->
    <aside class="bg-slate-950 text-slate-300 w-64 flex-shrink-0 flex flex-col">
        <div class="p-5 flex items-center justify-between">
            <a href="<?= admin_url('dashboard') ?>">
                <img src="<?= base_url('uploads/logo_thanh_danh.png') ?>" alt="Logo">
            </a>
        </div>

        <div class="flex-1 overflow-y-auto py-6">
            <nav class="space-y-1 px-3">
                <?php
                $tabs = [
                    'dashboard'  => ['name' => 'Tổng quan',       'icon' => 'layout-dashboard'],
                    'kpi_import' => ['name' => 'KPI Import',       'icon' => 'file-spreadsheet'],
                    'danh_gia'   => ['name' => 'Đánh giá KPI',     'icon' => 'clipboard-check'],
                    'tong_hop'   => ['name' => 'Tổng hợp',         'icon' => 'pie-chart'],
                    'form_in'    => ['name' => 'In phiếu',         'icon' => 'printer'],
                ];
                foreach ($tabs as $id => $tab):
                    $isActive  = ($active_tab === $id);
                    $bgClass   = $isActive ? 'bg-blue-600/10 text-black font-semibold' : 'text-slate-800 hover:bg-slate-800/10 hover:text-black';
                    $iconClass = $isActive ? 'text-blue-600' : 'text-slate-600';
                ?>
                    <a href="<?= site_url('admin/KpiDanhGia/index/' . $id) ?>"
                        class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?= $bgClass ?>">
                        <i data-lucide="<?= $tab['icon'] ?>" class="w-5 h-5 mr-3 flex-shrink-0 <?= $iconClass ?>"></i>
                        <span class="truncate"><?= $tab['name'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Sidebar footer -->
        <div class="p-4 border-t border-slate-200/20 text-xs text-slate-500 text-center">
            KPI System &mdash; V10_KPI_NSPB
        </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <header class="bg-white border-b border-slate-200 z-10">
            <div class="px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900 tracking-tight">
                    <?= isset($tabs[$active_tab]) ? $tabs[$active_tab]['name'] : 'Dashboard' ?>
                </h2>
                <span class="text-sm text-slate-500">Cập nhật lần cuối: Hôm nay <?= date('H:i') ?></span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <div class="mx-auto">
                <!-- Load tab tương ứng -->
                <?php $this->load->view('admin/kpi_danh_gia/tabs/' . $active_tab); ?>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>