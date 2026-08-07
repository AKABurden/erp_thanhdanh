<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace
        }

        table {
            width: 100%;
            text-align: left;
            border-collapse: collapse;
            font-size: .8125rem
        }

        thead {
            background: rgba(248, 250, 252, .8);
            color: #475569;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0
        }

        th {
            padding: .625rem .75rem;
            white-space: nowrap
        }

        td {
            padding: .5rem .75rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155
        }

        tbody tr:hover {
            background: rgba(248, 250, 252, .6);
            transition: background .15s
        }

        .kpi-sidebar {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%)
        }

        .progressbar-kpi {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            counter-reset: step
        }

        .progressbar-kpi li {
            flex: 1;
            text-align: center;
            position: relative;
            font-size: .6875rem;
            color: #94a3b8;
            padding-top: 28px
        }

        .progressbar-kpi li:before {
            content: counter(step);
            counter-increment: step;
            width: 22px;
            height: 22px;
            line-height: 22px;
            border-radius: 50%;
            display: block;
            text-align: center;
            margin: 0 auto;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background: #e2e8f0;
            color: #64748b;
            font-size: .625rem;
            font-weight: 600;
            z-index: 2;
            transition: all .3s
        }

        .progressbar-kpi li:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background: #e2e8f0;
            top: 10px;
            left: -50%;
            z-index: 1;
            transition: all .3s
        }

        .progressbar-kpi li:first-child:after {
            content: none
        }

        .progressbar-kpi li.active:before {
            background: #16a34a;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, .15)
        }

        .progressbar-kpi li.active:after {
            background: #16a34a
        }

        .progressbar-kpi li.current:before {
            background: #3b82f6;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
            animation: pulse-b 2s infinite
        }

        @keyframes pulse-b {

            0%,
            100% {
                box-shadow: 0 0 0 3px rgba(59, 130, 246, .2)
            }

            50% {
                box-shadow: 0 0 0 6px rgba(59, 130, 246, .1)
            }
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px)
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .25)
        }

        .task-detail-row {
            display: none
        }

        .task-detail-row.open {
            display: table-row
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px
        }

        /* Select2 custom styling */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            padding: 4px 8px;
            font-size: 0.875rem;
            background-color: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #94a3b8;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
            outline: none;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: #334155;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
        }

        .select2-dropdown {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 6px 10px;
            font-size: 0.875rem;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #3b82f6;
            color: #fff;
        }

        .select2-container--default .select2-results__option--selectable {
            padding: 8px 12px;
            font-size: 0.875rem;
        }

        .select2-container {
            width: 100% !important;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
    <!-- SheetJS (Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>

<body class="bg-slate-50 text-slate-900 overflow-hidden h-screen flex">
    <aside class="bg-[#FEF7E2] border-r border-slate-200 text-black w-60 flex-shrink-0 flex flex-col">
        <div class="p-5 flex items-center gap-3 border-b border-slate-800/10">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center">
                <a href="<?= admin_url('dashboard') ?>" class="w-full h-full flex items-center justify-center">
                    <img src="<?= base_url('uploads/logo_thanh_danh.png') ?>" alt="Logo" class="max-w-[40px] max-h-[40px] object-contain">
                </a>
            </div>
            <div>
                <div class="text-sm font-bold text-black">KPI Dashboard</div>
                <div class="text-[10px] text-slate-600">Đánh giá KPI</div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto py-4">
            <nav class="space-y-0.5 px-3">
                <?php
                $tabs = [
                    'dashboard'      => ['name' => 'Tổng quan',      'icon' => 'layout-dashboard', 'group' => 'main', 'permission' => 'DashboardKpi'],
                    '__import_menu'  => ['type' => 'dropdown', 'name' => 'Import KPI', 'icon' => 'upload', 'group' => 'data', 'permission' => 'DashboardKpi_Import', 'children' => [
                        ['id' => 'import_phong_ban', 'name' => 'Phòng ban',   'icon' => 'building-2'],
                        ['id' => 'department_budget', 'name' => 'Ngân sách phòng ban',   'icon' => 'wallet'],
                        ['id' => 'import_khach_hang', 'name' => 'Khách hàng',  'icon' => 'users'],
                        ['id' => 'import_ncc',       'name' => 'Nhà cung cấp', 'icon' => 'truck'],
                        ['id' => 'import_thiet_bi',  'name' => 'Thiết bị',    'icon' => 'monitor'],
                    ]],
                    'cong_viec'      => ['name' => 'Công việc',       'icon' => 'clipboard-list',   'group' => 'data', 'permission' => 'DashboardKpi_CongViec'],
                    'production_report'      => ['name' => 'Phiếu báo cáo vi phạm',       'icon' => 'clipboard-list',   'group' => 'data', 'permission' => 'DashboardKpi_ProductionReport'],
                    'ky_danh_gia'    => ['name' => 'Kỳ đánh giá',     'icon' => 'calendar-range',   'group' => 'eval', 'permission' => 'DashboardKpi_KyDanhGia'],
                    'phieu_danh_gia' => ['name' => 'Phiếu đánh giá',  'icon' => 'file-check-2',     'group' => 'eval', 'permission' => 'DashboardKpi_PhieuDanhGia'],
                    'vi_pham'        => ['name' => 'Vi phạm',         'icon' => 'alert-triangle',   'group' => 'eval', 'permission' => 'DashboardKpi_ViPham'],
                    'phe_duyet'      => ['name' => 'Phê duyệt',       'icon' => 'shield-check',     'group' => 'report', 'permission' => 'DashboardKpi_PheDuyet'],
                    'form_in'        => ['name' => 'In phiếu',        'icon' => 'printer',          'group' => 'report', 'permission' => 'DashboardKpi_FormIn'],
                    'tong_hop'       => ['name' => 'Tổng hợp',        'icon' => 'pie-chart',        'group' => 'report', 'permission' => 'DashboardKpi_TongHop'],
                ];
                $groups = ['main' => '', 'data' => 'Dữ liệu', 'eval' => 'Đánh giá', 'report' => 'Báo cáo'];
                $lastGroup = '';
                $importDropdownIds = ['import_phong_ban', 'department_budget', 'import_khach_hang', 'import_ncc', 'import_thiet_bi'];
                $dropdownOpen = in_array($active_tab, $importDropdownIds);

                foreach ($tabs as $id => $tab):
                    if (!empty($tab['permission']) && !has_permission($tab['permission'], '', 'view')) {
                        continue;
                    }
                    // Group header
                    if ($tab['group'] !== $lastGroup) {
                        $lastGroup = $tab['group'];
                        if (!empty($groups[$lastGroup])): ?>
                            <div class="text-[10px] text-slate-500 uppercase tracking-wider px-3 mt-4 mb-1 font-semibold"><?= $groups[$lastGroup] ?></div>
                        <?php endif;
                    }

                    // Dropdown type
                    if (isset($tab['type']) && $tab['type'] === 'dropdown'):
                        $ddOpen = $dropdownOpen;
                        $ddActive = $ddOpen ? 'bg-blue-600/10 text-black font-semibold' : 'text-slate-800 hover:bg-slate-800/10 hover:text-black';
                        $ddIc = $ddOpen ? 'text-blue-600' : 'text-slate-600';
                        ?>
                        <div class="kpi-dropdown-wrap">
                            <button type="button" onclick="toggleKpiDropdown(this)"
                                class="w-full flex items-center px-3 py-2.5 text-sm rounded-lg transition-all <?= $ddActive ?> kpi-dd-btn">
                                <i data-lucide="<?= $tab['icon'] ?>" class="w-[18px] h-[18px] mr-3 flex-shrink-0 <?= $ddIc ?>"></i>
                                <span class="truncate flex-1 text-left"><?= $tab['name'] ?></span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 kpi-dd-arrow <?= $ddOpen ? 'rotate-180' : '' ?>"></i>
                            </button>
                            <div class="kpi-dd-children pl-4 mt-0.5 space-y-0.5 overflow-hidden transition-all duration-200 <?= $ddOpen ? '' : 'hidden' ?>">
                                <?php foreach ($tab['children'] as $child):
                                    $cActive = ($active_tab === $child['id']);
                                    $cBg = $cActive ? 'bg-blue-600/10 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-800/10 hover:text-black';
                                    $cIc = $cActive ? 'text-blue-600' : 'text-slate-500';
                                ?>
                                    <a href="<?= site_url('admin/DashboardKpi/index/' . $child['id']) ?>"
                                        class="w-full flex items-center px-3 py-2 text-sm rounded-lg transition-all <?= $cBg ?>">
                                        <i data-lucide="<?= $child['icon'] ?>" class="w-[16px] h-[16px] mr-3 flex-shrink-0 <?= $cIc ?>"></i>
                                        <span class="truncate"><?= $child['name'] ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php else:
                        // Normal tab
                        $isActive = ($active_tab === $id);
                        $bg = $isActive ? 'bg-blue-600/10 text-black font-semibold' : 'text-slate-800 hover:bg-slate-800/10 hover:text-black';
                        $ic = $isActive ? 'text-blue-600' : 'text-slate-600';
                    ?>
                        <a href="<?= site_url('admin/DashboardKpi/index/' . $id) ?>" class="w-full flex items-center px-3 py-2.5 text-sm rounded-lg transition-all <?= $bg ?>">
                            <i data-lucide="<?= $tab['icon'] ?>" class="w-[18px] h-[18px] mr-3 flex-shrink-0 <?= $ic ?>"></i>
                            <span class="truncate"><?= $tab['name'] ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </div>
    </aside>
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <header class="bg-white border-b border-slate-200 z-10">
            <div class="px-6 py-4 flex items-center justify-between">
                <?php
                // Title: tìm trong tabs hoặc trong dropdown children
                $pageTitle = 'Dashboard';
                $importTitles = [
                    'import_phong_ban' => 'Tiêu chí Phòng ban',
                    'import_khach_hang' => 'Mục tiêu Khách hàng',
                    'import_ncc'       => 'Mục tiêu Nhà cung cấp',
                    'import_thiet_bi'  => 'KPI Thiết bị Công đoạn'
                ];
                if (isset($importTitles[$active_tab])) {
                    $pageTitle = $importTitles[$active_tab];
                } else {
                    foreach ($tabs as $id => $tab) {
                        if ($id === $active_tab) {
                            $pageTitle = $tab['name'];
                            break;
                        }
                    }
                }
                ?>
                <h2 class="text-lg font-semibold text-slate-900"><?= $pageTitle ?></h2>
                <span class="text-xs text-slate-400"><?= date('d/m/Y H:i') ?></span>
            </div>
        </header>
        <div class="flex-1 overflow-y-auto p-6">
            <div class="mx-auto"><?php $this->load->view('admin/dashboard_kpi/tabs/' . $active_tab); ?></div>
        </div>
    </main>

    <!-- Modal Container -->
    <div id="kpi-custom-modal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeKpiModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 id="kpi-modal-title" class="text-lg font-bold text-slate-900">Modal Title</h3>
                    <button onclick="closeKpiModal()" class="p-2 hover:bg-slate-100 rounded-xl transition-colors">
                        <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                    </button>
                </div>
                <div id="kpi-modal-body" class="p-6 overflow-y-auto">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Polyfill alert_float cho dashboard KPI
        function alert_float(type, message) {
            var colors = {
                success: {
                    bg: '#ecfdf5',
                    border: '#6ee7b7',
                    text: '#065f46',
                    icon: '✅'
                },
                danger: {
                    bg: '#fef2f2',
                    border: '#fca5a5',
                    text: '#991b1b',
                    icon: '❌'
                },
                warning: {
                    bg: '#fffbeb',
                    border: '#fcd34d',
                    text: '#92400e',
                    icon: '⚠️'
                },
                info: {
                    bg: '#eff6ff',
                    border: '#93c5fd',
                    text: '#1e40af',
                    icon: 'ℹ️'
                }
            };
            var c = colors[type] || colors.info;
            var toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;padding:14px 20px;border-radius:12px;' +
                'background:' + c.bg + ';border:1px solid ' + c.border + ';color:' + c.text + ';' +
                'font-size:14px;font-weight:500;box-shadow:0 10px 25px rgba(0,0,0,0.1);' +
                'display:flex;align-items:center;gap:10px;max-width:400px;' +
                'animation:slideInRight .3s ease;transition:opacity .3s,transform .3s';
            toast.innerHTML = '<span style="font-size:18px">' + c.icon + '</span><span>' + message + '</span>';
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(30px)';
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        function toggleKpiDropdown(btn) {
            var wrap = btn.closest('.kpi-dropdown-wrap');
            var content = wrap.querySelector('.kpi-dd-children');
            var arrow = btn.querySelector('.kpi-dd-arrow');
            var isOpen = !content.classList.contains('hidden');
            if (isOpen) {
                content.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            } else {
                content.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            }
            lucide.createIcons();
        }

        function openKpiModal(title, url, type = 'import') {
            const modal = document.getElementById('kpi-custom-modal');
            const body = document.getElementById('kpi-modal-body');
            document.getElementById('kpi-modal-title').textContent = title;

            modal.classList.remove('hidden');
            body.innerHTML = '<div class="py-20 text-center"><div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">Đang tải...</p></div>';

            // Nếu type là import, ta load view import_danh_muc qua AJAX
            // Ở đây ta giả định url là id của tab (import_khach_hang, ...)
            fetch('<?= site_url('admin/DashboardKpi/load_import_view/') ?>' + url)
                .then(r => r.text())
                .then(html => {
                    body.innerHTML = html;
                    lucide.createIcons();
                    // Khởi tạo các scripts trong HTML mới nếu cần
                    const scripts = body.getElementsByTagName('script');
                    for (let s of Array.from(scripts)) {
                        const newScript = document.createElement('script');
                        if (s.src) {
                            newScript.src = s.src;
                        } else {
                            newScript.text = s.text;
                        }
                        document.body.appendChild(newScript);
                        // Không xóa script nếu nó có src ngay lập tức vì nó cần thời gian tải
                        if (!s.src) {
                            newScript.parentNode.removeChild(newScript);
                        }
                    }
                })
                .catch(err => {
                    body.innerHTML = '<div class="py-20 text-center text-red-500">Lỗi tải dữ liệu!</div>';
                });
        }

        function openDetailModal(title, url) {
            const modal = document.getElementById('kpi-custom-modal');
            const body = document.getElementById('kpi-modal-body');
            document.getElementById('kpi-modal-title').textContent = title;

            modal.classList.remove('hidden');
            body.innerHTML = '<div class="py-20 text-center"><div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div><p class="text-slate-500">Đang tải...</p></div>';

            fetch(url)
                .then(r => r.text())
                .then(html => {
                    body.innerHTML = html;
                    lucide.createIcons();
                    const scripts = body.getElementsByTagName('script');
                    for (let s of Array.from(scripts)) {
                        const newScript = document.createElement('script');
                        if (s.src) {
                            newScript.src = s.src;
                        } else {
                            newScript.text = s.text;
                        }
                        document.body.appendChild(newScript);
                        if (!s.src) {
                            newScript.parentNode.removeChild(newScript);
                        }
                    }
                })
                .catch(err => {
                    body.innerHTML = '<div class="py-20 text-center text-red-500">Lỗi tải dữ liệu!</div>';
                });
        }

        function closeKpiModal() {
            document.getElementById('kpi-custom-modal').classList.add('hidden');
        }
    </script>
</body>

</html>