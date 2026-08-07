<?php
// views/admin/dashboard_kpi/tabs/cong_viec.php
defined('BASEPATH') or exit('No direct script access allowed');
$filters = isset($filters) ? $filters : [];
$task_statuses = [
    1 => ['name' => 'Chưa bắt đầu', 'color' => '#989898'],
    2 => ['name' => 'Đang thực hiện', 'color' => '#2196F3'],
    3 => ['name' => 'Chờ xác nhận', 'color' => '#ff9800'],
    4 => ['name' => 'Hoàn thành', 'color' => '#84c529'],
    5 => ['name' => 'Đã hoàn tất', 'color' => '#4caf50'],
];
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Công việc & Quy trình</h2>
        <p class="text-xs text-slate-500">Xem task, tiến độ và quy trình xử lý — Dữ liệu từ Tasks hệ thống</p>
    </div>
    <div class="flex gap-2">
        <button onclick="toggleFilter()" class="flex items-center gap-1.5 px-3 py-2 text-xs bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition">
            <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i> Bộ lọc
        </button>
        <a href="<?= admin_url('tasks') ?>" target="_blank" class="flex items-center gap-1.5 px-3 py-2 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Mở Tasks
        </a>
    </div>
</div>

<!-- Bộ lọc Năm / Tháng + Kỳ tabs -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4 space-y-4">
    <div class="flex flex-wrap items-center gap-4">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Năm</span>
            <select id="cv-year" onchange="onCvYearMonthChange()" style="padding:10px 16px;font-size:14px;font-weight:700;border:1px solid #e2e8f0;border-radius:10px;min-width:100px;outline:none;color:#1e293b;background:#f8fafc;cursor:pointer;">
                <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Tháng</span>
            <select id="cv-month" onchange="onCvYearMonthChange()" style="padding:10px 16px;font-size:14px;font-weight:700;border:1px solid #e2e8f0;border-radius:10px;min-width:140px;outline:none;color:#1e293b;background:#f8fafc;cursor:pointer;">
                <option value="">— Chọn tháng —</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == (int)date('m') ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <input type="text" id="cv-search" placeholder="Tìm kiếm..." oninput="debounceCvSearch()" class="px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500" style="min-width:220px;">
        <div class="text-sm text-slate-400 flex items-center gap-1.5 ml-auto font-medium">
            <i data-lucide="hash" style="width:16px;height:16px"></i>
            <span id="cv-count"><?= $total ?> task</span>
        </div>
    </div>
    <input type="hidden" id="cv-ky" value="">
    <div id="cv-ky-tabs" class="flex flex-wrap gap-2.5"></div>
</div>

<!-- Filters -->
<div id="filter-panel" class="bg-white rounded-xl border border-slate-100 p-4 mb-4 <?= empty($filters['staff_id']) && empty($filters['status']) && empty($filters['date_from']) && empty($filters['department_id']) ? 'hidden' : '' ?>">
    <form method="GET" action="<?= site_url('admin/DashboardKpi/index/cong_viec') ?>" class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Nhân sự</label>
            <select name="staff_id" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Tất cả</option>
                <?php foreach ($staff_list as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($filters['staff_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['ho_ten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Trạng thái</label>
            <select name="status" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Tất cả</option>
                <?php foreach ($task_statuses as $sid => $sv): ?>
                    <option value="<?= $sid ?>" <?= ($filters['status'] ?? '') == $sid ? 'selected' : '' ?>><?= $sv['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Từ ngày</label>
            <input type="date" name="date_from" value="<?= $filters['date_from'] ?? '' ?>"
                class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Đến ngày</label>
            <input type="date" name="date_to" value="<?= $filters['date_to'] ?? '' ?>"
                class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="flex items-end gap-2">
            <button type="button" onclick="loadCvPage(1)" class="flex-1 px-3 py-2 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i data-lucide="search" class="w-3 h-3 inline mr-1"></i> Lọc
            </button>
            <a href="<?= site_url('admin/DashboardKpi/index/cong_viec') ?>" class="px-3 py-2 text-xs bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition">Xóa</a>
        </div>
    </form>
</div>

<!-- Summary Stats -->
<?php
$total = count($tasks ?? []);
$done = 0;
$overdue = 0;
$has_process = 0;
foreach (($tasks ?? []) as $t) {
    if ($t['status'] == 5) $done++;
    if ($t['is_overdue']) $overdue++;
    if ($t['total_steps'] > 0) $has_process++;
}
?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center"><i data-lucide="list-checks" class="w-4 h-4 text-blue-600"></i></div>
        <div>
            <div class="text-lg font-bold text-slate-800" id="cv-stat-total"><?= $total ?></div>
            <div class="text-[10px] text-slate-400">Tổng task</div>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i></div>
        <div>
            <div class="text-lg font-bold text-emerald-700" id="cv-stat-done"><?= $done ?></div>
            <div class="text-[10px] text-slate-400">Hoàn thành</div>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center"><i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i></div>
        <div>
            <div class="text-lg font-bold text-red-600" id="cv-stat-overdue"><?= $overdue ?></div>
            <div class="text-[10px] text-slate-400">Trễ hạn</div>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center"><i data-lucide="git-branch" class="w-4 h-4 text-violet-600"></i></div>
        <div>
            <div class="text-lg font-bold text-violet-700" id="cv-stat-process"><?= $has_process ?></div>
            <div class="text-[10px] text-slate-400">Có quy trình</div>
        </div>
    </div>
</div>

<!-- Tasks Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tasks-table">
            <thead>
                <tr>
                    <th class="w-8"></th>
                    <th>ID</th>
                    <th>Mã CV</th>
                    <th>Tên công việc</th>
                    <th>Người thực hiện</th>
                    <th>Bắt đầu</th>
                    <th>Hạn chót</th>
                    <th>Trạng thái</th>
                    <th>Quy trình</th>
                    <th class="text-center">Tiến độ QT</th>
                </tr>
            </thead>
            <tbody id="cv-tbody">
                <tr>
                    <td colspan="10" class="text-center text-slate-400 py-12">
                        <i data-lucide="loader-2" class="w-8 h-8 mx-auto text-blue-500 animate-spin mb-2"></i>
                        <div>Đang tải dữ liệu...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="p-3 border-t border-slate-100 flex justify-center bg-slate-50" id="cv-load-more-container" style="display:none;">
        <button type="button" onclick="loadCvPage(window.cvCurrentPage + 1)" id="cv-btn-loadmore" class="px-4 py-2 text-xs font-medium text-blue-600 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition shadow-sm">
            Tải thêm công việc
        </button>
    </div>
</div>

<!-- Styles -->
<style>
    /* Tasks Table */
    #tasks-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }

    #tasks-table thead tr {
        background: #f8fafc !important;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #tasks-table th {
        padding: 10px 12px !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #475569 !important;
        background: #f1f5f9 !important;
        white-space: nowrap;
        border-bottom: 2px solid #e2e8f0 !important;
        border-right: 1px solid #e2e8f0;
    }

    #tasks-table td {
        padding: 8px 12px !important;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #334155;
        white-space: nowrap;
        vertical-align: middle;
    }

    #tasks-table .task-row:hover td {
        background-color: #f0f9ff;
    }

    #tasks-table .task-row {
        cursor: pointer;
    }

    /* Detail rows - hidden by default */
    .task-detail-row {
        display: none;
    }

    .task-detail-row.open {
        display: table-row;
    }

    .task-detail-row td {
        padding: 0 !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    /* Inner detail table */
    .task-detail-row table {
        width: 100%;
        border-collapse: collapse;
    }

    .task-detail-row table th {
        padding: 6px 10px !important;
        font-size: 0.65rem !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b !important;
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    .task-detail-row table td {
        padding: 6px 10px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        font-size: 0.75rem;
        vertical-align: middle;
    }

    .task-detail-row table tbody tr:hover td {
        background: #fafafa;
    }

    /* Progressbar KPI */
    .progressbar-kpi {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0 0 8px;
        counter-reset: step;
    }

    .progressbar-kpi li {
        flex: 1;
        text-align: center;
        position: relative;
        padding-top: 28px;
    }

    .progressbar-kpi li::before {
        content: counter(step);
        counter-increment: step;
        width: 22px;
        height: 22px;
        line-height: 22px;
        display: block;
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        border-radius: 50%;
        margin: 0 auto 6px;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        transition: all .3s;
    }

    .progressbar-kpi li::after {
        content: '';
        position: absolute;
        height: 2px;
        background: #e2e8f0;
        top: 10px;
        left: -50%;
        right: 50%;
        z-index: 1;
        transition: background .3s;
    }

    .progressbar-kpi li:first-child::after {
        display: none;
    }

    /* Active (done) */
    .progressbar-kpi li.active::before {
        content: '✓';
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }

    .progressbar-kpi li.active::after {
        background: #10b981;
    }

    /* Current */
    .progressbar-kpi li.current::before {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {

        0%,
        100% {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.1);
        }
    }
</style>

<script>
    function toggleFilter() {
        document.getElementById('filter-panel').classList.toggle('hidden');
        lucide.createIcons();
    }

    function toggleTaskDetail(taskId) {
        var row = document.getElementById('detail-' + taskId);
        var arrow = document.getElementById('arrow-' + taskId);
        if (!row) return;
        row.classList.toggle('open');
        if (arrow) {
            arrow.style.transform = row.classList.contains('open') ? 'rotate(90deg)' : 'rotate(0deg)';
        }
        lucide.createIcons();
    }

    // === Bộ lọc Năm/Tháng + Kỳ tabs ===
    var cvQuarterPeriods = [{
            value: '',
            label: 'Tất cả',
            icon: 'layers'
        },
        {
            value: '3 tháng',
            label: 'Kỳ 3 tháng',
            icon: 'calendar-days'
        },
        {
            value: '6 tháng',
            label: 'Kỳ 6 tháng',
            icon: 'calendar-range'
        },
        {
            value: '9 tháng',
            label: 'Kỳ 9 tháng',
            icon: 'calendar-clock'
        },
        {
            value: '12 tháng',
            label: 'Kỳ 12 tháng',
            icon: 'calendar-check'
        }
    ];
    var cvWeekPeriods = [{
            value: '',
            label: 'Tất cả',
            icon: 'layers'
        },
        {
            value: '1',
            label: 'Tuần 1',
            icon: 'calendar'
        },
        {
            value: '2',
            label: 'Tuần 2',
            icon: 'calendar'
        },
        {
            value: '3',
            label: 'Tuần 3',
            icon: 'calendar'
        },
        {
            value: '4',
            label: 'Tuần 4',
            icon: 'calendar'
        }
    ];

    function onCvYearMonthChange() {
        var month = document.getElementById('cv-month').value;
        var container = document.getElementById('cv-ky-tabs');

        var periods = [];
        periods.push(cvQuarterPeriods[0]);

        cvWeekPeriods.forEach(function(p, idx) {
            if (idx > 0 && p.value !== '') periods.push(p);
        });

        cvQuarterPeriods.forEach(function(p, idx) {
            if (idx > 0 && p.value !== '') periods.push(p);
        });
        document.getElementById('cv-ky').value = '';

        var html = '';
        periods.forEach(function(p, idx) {
            var isActive = (idx === 0);
            var cls = isActive ?
                'background:#7c3aed;color:#fff;border-color:#7c3aed;box-shadow:0 1px 3px rgba(124,58,237,0.3);' :
                'background:#fff;color:#475569;border-color:#e2e8f0;';
            html += '<button type="button" onclick="selectCvKyTab(this, \'' + p.value + '\')" ' +
                'class="cv-ky-tab" ' +
                'style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;' +
                'font-size:13px;font-weight:700;border:1px solid;cursor:pointer;transition:all .15s;white-space:nowrap;' +
                cls + '">' +
                '<i data-lucide="' + p.icon + '" style="width:15px;height:15px"></i>' +
                p.label +
                '</button>';
        });
        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        loadCvPage(1);
    }

    function selectCvKyTab(btn, value) {
        document.getElementById('cv-ky').value = value;
        document.querySelectorAll('.cv-ky-tab').forEach(function(b) {
            b.style.background = '#fff';
            b.style.color = '#475569';
            b.style.borderColor = '#e2e8f0';
            b.style.boxShadow = 'none';
        });
        btn.style.background = '#7c3aed';
        btn.style.color = '#fff';
        btn.style.borderColor = '#7c3aed';
        btn.style.boxShadow = '0 1px 3px rgba(124,58,237,0.3)';
        loadCvPage(1);
    }

    // === AJAX Pagination & Fetching ===
    window.cvCurrentPage = 1;
    var searchTimeout = null;

    function debounceCvSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadCvPage(1);
        }, 500);
    }

    function loadCvPage(page) {
        window.cvCurrentPage = page;
        var tbody = document.getElementById('cv-tbody');
        var loadMoreBtn = document.getElementById('cv-btn-loadmore');
        var loadMoreContainer = document.getElementById('cv-load-more-container');

        // Show loading
        if (page === 1) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-slate-400 py-12"><i data-lucide="loader-2" class="w-8 h-8 mx-auto text-blue-500 animate-spin mb-2"></i><div>Đang tải dữ liệu...</div></td></tr>';
            if (typeof lucide !== 'undefined') lucide.createIcons();
            loadMoreContainer.style.display = 'none';
        } else {
            if (loadMoreBtn) {
                loadMoreBtn.disabled = true;
                loadMoreBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline animate-spin mr-1"></i> Đang tải...';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }

        // Build form data
        var fd = new FormData();
        fd.append('<?= $this->security->get_csrf_token_name() ?>', '<?= $this->security->get_csrf_hash() ?>');
        fd.append('staff_id', document.querySelector('select[name="staff_id"]').value);
        fd.append('status', document.querySelector('select[name="status"]').value);
        fd.append('date_from', document.querySelector('input[name="date_from"]').value);
        fd.append('date_to', document.querySelector('input[name="date_to"]').value);
        fd.append('year', document.getElementById('cv-year').value);
        fd.append('month', document.getElementById('cv-month').value);
        fd.append('ky', document.getElementById('cv-ky').value);
        fd.append('q', document.getElementById('cv-search').value);
        fd.append('page', page);

        fetch('<?= site_url("admin/DashboardKpi/ajax_cong_viec") ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                if (page === 1) {
                    tbody.innerHTML = res.html;
                } else {
                    tbody.insertAdjacentHTML('beforeend', res.html);
                }

                if (typeof lucide !== 'undefined') lucide.createIcons();

                // Update stats
                if (res.stats) {
                    document.getElementById('cv-stat-total').textContent = res.stats.total;
                    document.getElementById('cv-stat-done').textContent = res.stats.done;
                    document.getElementById('cv-stat-overdue').textContent = res.stats.overdue;
                    document.getElementById('cv-stat-process').textContent = res.stats.has_process;
                    document.getElementById('cv-count').textContent = res.stats.total + ' task';
                }

                // Handle load more
                if (res.has_more) {
                    loadMoreContainer.style.display = 'flex';
                    if (loadMoreBtn) {
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.innerHTML = 'Tải thêm công việc';
                    }
                } else {
                    loadMoreContainer.style.display = 'none';
                }
            })
            .catch(err => {
                console.error(err);
                if (page === 1) {
                    tbody.innerHTML = '<tr><td colspan="10" class="text-center text-red-500 py-12">Lỗi kết nối. Vui lòng thử lại.</td></tr>';
                } else if (loadMoreBtn) {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.innerHTML = 'Tải thêm công việc';
                    alert('Lỗi tải thêm dữ liệu');
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        onCvYearMonthChange(); // Calls loadCvPage(1)
    });
</script>