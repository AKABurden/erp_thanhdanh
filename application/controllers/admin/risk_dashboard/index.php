<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Dashboard - CodeIgniter 3</title>
    <!-- Tích hợp Tailwind CSS qua CDN cho môi trường CI3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tích hợp Icon Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Style cho table */
        table {
            width: 100%;
            text-align: left;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead {
            background-color: rgba(248, 250, 252, 0.8);
            color: #475569;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        tbody tr:hover {
            background-color: rgba(248, 250, 252, 0.5);
            transition: background-color 0.2s;
        }

        .bg-slate-950 {
            background-color: #FEF7E2 !important;
        }

        .text-slate-300 {
            color: #000000 !important;
        }

        .border-slate-800\/60 {
            border: none !important;
        }

        /* ===== OnOffSwitch ===== */
        .onoffswitch {
            position: relative;
            width: 56px;
            -webkit-user-select: none;
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
    </style>
</head>

<body class="bg-slate-50 text-slate-900 overflow-hidden h-screen flex">

    <!-- Sidebar -->
    <aside class="bg-slate-950 text-slate-300 w-64 flex-shrink-0 flex flex-col">
        <div class="p-5 flex items-center justify-between">
            <!-- <h1 class="text-xl font-bold text-white tracking-tight">Risk<span class="text-blue-500">Dash</span></h1> -->
            <a href="<?= admin_url('dashboard') ?>"><img src="<?= base_url('uploads/logo_thanh_danh.png') ?>"><a></a>
        </div>
        <div class="flex-1 overflow-y-auto py-6">
            <nav class="space-y-1 px-3">
                <?php
                $tabs = [
                    'so_sanh' => ['name' => 'So sánh & Đánh giá', 'icon' => 'layout-dashboard'],
                    'canh_bao' => ['name' => 'Hệ thống Cảnh báo', 'icon' => 'bell-ring'],
                    'sinh_phieu' => ['name' => 'Tự động Sinh phiếu', 'icon' => 'file-plus'],
                    // 'phan_quyen' => ['name' => 'Phân quyền & Override', 'icon' => 'shield'],
                    'phong_ban' => ['name' => 'Dashboard Phòng ban', 'icon' => 'building-2'],
                    'audit' => ['name' => 'Dashboard Audit', 'icon' => 'search-check'],
                    'ke_toan' => ['name' => 'Dashboard Kế toán', 'icon' => 'calculator'],
                    'bod' => ['name' => 'Dashboard BOD', 'icon' => 'briefcase'],
                    'audit_trail' => ['name' => 'Kiểm soát & Audit Trail', 'icon' => 'history'],
                    // 'quy_trinh' => ['name' => 'Quy trình Xử lý', 'icon' => 'git-merge'],
                    // 'tai_lieu' => ['name' => 'Tài liệu & Nghiệm thu', 'icon' => 'book-open'],
                ];
                foreach ($tabs as $id => $tab):
                    $isActive = ($active_tab === $id);
                    $bgClass = $isActive ? 'bg-blue-600/10 text-black font-semibold' : 'text-slate-800 hover:bg-slate-800/10 hover:text-black';
                    $iconClass = $isActive ? 'text-black' : 'text-slate-600';
                ?>
                    <a href="<?= site_url('admin/RiskDashboard/index/' . $id) ?>" class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?= $bgClass ?>">
                        <i data-lucide="<?= $tab['icon'] ?>" class="w-5 h-5 mr-3 flex-shrink-0 <?= $iconClass ?>"></i>
                        <span class="truncate"><?= $tab['name'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <header class="bg-white border-b border-slate-200 z-10">
            <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900 tracking-tight">
                    <?= isset($tabs[$active_tab]) ? $tabs[$active_tab]['name'] : 'Dashboard' ?>
                </h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-slate-500">Cập nhật lần cuối: Hôm nay <?= date('H:i') ?></span>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="mx-auto">
                <!-- Load nội dung tab tương ứng -->
                <?php $this->load->view('admin/risk_dashboard/tabs/' . $active_tab); ?>
            </div>
        </div>
    </main>

    <!-- Khởi tạo Icon -->
    <script>
        lucide.createIcons();
    </script>
</body>

</html>