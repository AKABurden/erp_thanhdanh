<?php
defined('BASEPATH') or exit('No direct script access allowed');
$filters = isset($filters) ? $filters : [];
$type_labels = [
    1 => ['name' => 'Báo cáo KPH', 'color' => '#16a34a', 'bg' => '#dcfce7'],
    2 => ['name' => 'Báo cáo vượt', 'color' => '#2563eb', 'bg' => '#dbeafe'],
    3 => ['name' => 'Báo cáo cải tiến', 'color' => '#d97706', 'bg' => '#fef3c7'],
    4 => ['name' => 'Báo cáo vi phạm', 'color' => '#dc2626', 'bg' => '#fee2e2'],
];
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
            <i data-lucide="clipboard-list" class="w-5 h-5 text-red-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Phiếu báo cáo vi phạm</h2>
            <p class="text-xs text-slate-500">Theo dõi phiếu báo cáo không phù hợp, vi phạm, cải tiến — Dữ liệu từ Production Report</p>
        </div>
    </div>
    <div class="flex gap-2">
        <button onclick="togglePrFilter()" class="flex items-center gap-1.5 px-3 py-2 text-xs bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition">
            <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i> Bộ lọc
        </button>
    </div>
</div>

<!-- Bộ lọc Năm / Tháng + Kỳ tabs -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4 space-y-4">
    <div class="flex flex-wrap items-center gap-4">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Năm</span>
            <select id="pr-year" onchange="onPrYearMonthChange()" style="padding:10px 16px;font-size:14px;font-weight:700;border:1px solid #e2e8f0;border-radius:10px;min-width:100px;outline:none;color:#1e293b;background:#f8fafc;cursor:pointer;">
                <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Tháng</span>
            <select id="pr-month" onchange="onPrYearMonthChange()" style="padding:10px 16px;font-size:14px;font-weight:700;border:1px solid #e2e8f0;border-radius:10px;min-width:140px;outline:none;color:#1e293b;background:#f8fafc;cursor:pointer;">
                <option value="">— Chọn tháng —</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == (int)date('m') ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <input type="hidden" id="pr-ky" value="">
    <div id="pr-ky-tabs" class="flex flex-wrap gap-2.5"></div>
</div>

<!-- Filters -->
<div id="pr-filter-panel" class="bg-white rounded-xl border border-slate-100 p-4 mb-4 <?= empty($filters['date_from']) && empty($filters['type_report']) && empty($filters['room_id']) && empty($filters['recommend_group']) ? 'hidden' : '' ?>">
    <form method="GET" action="<?= site_url('admin/DashboardKpi/index/production_report') ?>" class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Loại phiếu</label>
            <select name="type_report" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
                <option value="">Tất cả</option>
                <?php foreach ($type_labels as $tid => $tv): ?>
                    <option value="<?= $tid ?>" <?= ($filters['type_report'] ?? '') == $tid ? 'selected' : '' ?>><?= $tv['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Bộ phận</label>
            <select name="room_id" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
                <option value="">Tất cả</option>
                <?php foreach (($room_list ?? []) as $rm): ?>
                    <option value="<?= $rm['id'] ?>" <?= ($filters['room_id'] ?? '') == $rm['id'] ? 'selected' : '' ?>><?= htmlspecialchars($rm['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Nhóm</label>
            <select name="recommend_group" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
                <option value="">Tất cả</option>
                <?php foreach (($recommended_list ?? []) as $rl): ?>
                    <option value="<?= $rl['id'] ?>" <?= ($filters['recommend_group'] ?? '') == $rl['id'] ? 'selected' : '' ?>><?= htmlspecialchars($rl['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 mb-1">Từ ngày</label>
            <input type="date" name="date_from" value="<?= $filters['date_from'] ?? '' ?>"
                class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
        </div>
        <div class="flex items-end gap-2">
            <div class="flex-1">
                <label class="block text-[11px] text-slate-500 mb-1">Đến ngày</label>
                <input type="date" name="date_to" value="<?= $filters['date_to'] ?? '' ?>"
                    class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>
            <button type="button" onclick="loadPrPage(1)" class="flex-1 px-3 py-2 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i data-lucide="search" class="w-3 h-3 inline mr-1"></i> Lọc
            </button>
            <a href="<?= site_url('admin/DashboardKpi/index/production_report') ?>" class="px-3 py-2 text-xs bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition">Xóa</a>
        </div>
    </form>
</div>

<!-- Summary Stats -->
<?php
$total = count($reports ?? []);
$type_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
$fully_approved = 0;
foreach (($reports ?? []) as $r) {
    if (isset($type_counts[$r['type_report']])) $type_counts[$r['type_report']]++;
    if ($r['process_rate'] >= 100) $fully_approved++;
}
?>
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center"><i data-lucide="file-text" class="w-4 h-4 text-slate-600"></i></div>
        <div>
            <div class="text-lg font-bold text-slate-800" id="pr-stat-total"><?= $total ?></div>
            <div class="text-[10px] text-slate-400">Tổng phiếu</div>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center"><i data-lucide="file-check" class="w-4 h-4 text-green-600"></i></div>
        <div>
            <div class="text-lg font-bold text-green-700" id="pr-stat-kph"><?= $type_counts[1] ?></div>
            <div class="text-[10px] text-slate-400">KPH</div>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center"><i data-lucide="alert-octagon" class="w-4 h-4 text-red-600"></i></div>
        <div>
            <div class="text-lg font-bold text-red-600" id="pr-stat-vp"><?= $type_counts[4] ?></div>
            <div class="text-[10px] text-slate-400">Vi phạm</div>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center"><i data-lucide="lightbulb" class="w-4 h-4 text-amber-600"></i></div>
        <div>
            <div class="text-lg font-bold text-amber-700" id="pr-stat-ct"><?= $type_counts[3] ?></div>
            <div class="text-[10px] text-slate-400">Cải tiến</div>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-slate-100 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i></div>
        <div>
            <div class="text-lg font-bold text-emerald-700" id="pr-stat-approved"><?= $fully_approved ?></div>
            <div class="text-[10px] text-slate-400">Đã duyệt hết</div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-xl border border-slate-100 p-3 mb-4">
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[250px]">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="pr-search" placeholder="Tìm theo số phiếu, tên phiếu, sự cố..." oninput="debouncePrSearch()"
                class="w-full pl-8 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
        <div class="text-xs text-slate-400 flex items-center gap-1">
            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
            <span id="pr-count"><?= $total ?> phiếu</span>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto" style="max-height: calc(100vh - 380px); overflow-y: auto;">
        <table id="pr-table">
            <thead>
                <tr>
                    <th class="w-8"></th>
                    <th>Ngày</th>
                    <th>Số phiếu</th>
                    <th>Tên phiếu</th>
                    <th>Loại</th>
                    <th>Bộ phận</th>
                    <th>Sự cố</th>
                    <th>Nhóm VP</th>
                    <th>Người lập</th>
                    <th>Người chịu TN</th>
                    <th>SL</th>
                    <th>Quy trình</th>
                    <th class="text-center">Tiến độ</th>
                </tr>
            </thead>
            <tbody id="pr-tbody">
                <tr>
                    <td colspan="13" style="text-align:center;color:#94a3b8;padding:48px 0">
                        <i data-lucide="loader-2" class="w-10 h-10 mx-auto text-blue-500 animate-spin mb-2"></i>
                        <div>Đang tải dữ liệu...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="p-3 border-t border-slate-100 flex justify-center bg-slate-50" id="pr-load-more-container" style="display:none;">
        <button type="button" onclick="loadPrPage(window.prCurrentPage + 1)" id="pr-btn-loadmore" class="px-4 py-2 text-xs font-medium text-blue-600 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition shadow-sm">
            Tải thêm báo cáo
        </button>
    </div>
</div>


<!-- Styles -->
<style>
    #pr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }

    #pr-table thead tr {
        background: #f8fafc !important;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #pr-table th {
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

    #pr-table td {
        padding: 8px 12px !important;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #334155;
        white-space: nowrap;
        vertical-align: middle;
    }

    #pr-table .pr-row:hover td {
        background-color: #fff5f5;
    }

    #pr-table .pr-row {
        cursor: pointer;
    }

    .pr-detail-row {
        display: none;
    }

    .pr-detail-row.open {
        display: table-row;
    }

    /* Progressbar KPI - reused from cong_viec */
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

    .progressbar-kpi li.active::before {
        content: '✓';
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }

    .progressbar-kpi li.active::after {
        background: #10b981;
    }

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
    function togglePrFilter() {
        document.getElementById('pr-filter-panel').classList.toggle('hidden');
        lucide.createIcons();
    }

    function togglePrDetail(id) {
        var row = document.getElementById('pr-detail-' + id);
        var arrow = document.getElementById('pr-arrow-' + id);
        if (!row) return;
        row.classList.toggle('open');
        if (arrow) {
            arrow.style.transform = row.classList.contains('open') ? 'rotate(90deg)' : 'rotate(0deg)';
        }
    }

    var prQuarterPeriods = [{
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
    var prWeekPeriods = [{
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

    function onPrYearMonthChange() {
        var month = document.getElementById('pr-month').value;
        var container = document.getElementById('pr-ky-tabs');

        var periods = [];
        periods.push(prQuarterPeriods[0]);

        prWeekPeriods.forEach(function(p, idx) {
            if (idx > 0 && p.value !== '') periods.push(p);
        });

        prQuarterPeriods.forEach(function(p, idx) {
            if (idx > 0 && p.value !== '') periods.push(p);
        });
        document.getElementById('pr-ky').value = '';
        var html = '';
        periods.forEach(function(p, idx) {
            var isActive = (idx === 0);
            var cls = isActive ?
                'background:#7c3aed;color:#fff;border-color:#7c3aed;box-shadow:0 1px 3px rgba(124,58,237,0.3);' :
                'background:#fff;color:#475569;border-color:#e2e8f0;';
            html += '<button type="button" onclick="selectPrKyTab(this, \'' + p.value + '\')" ' +
                'class="pr-ky-tab" ' +
                'style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;' +
                'font-size:13px;font-weight:700;border:1px solid;cursor:pointer;transition:all .15s;white-space:nowrap;' +
                cls + '">' +
                '<i data-lucide="' + p.icon + '" style="width:15px;height:15px"></i>' +
                p.label +
                '</button>';
        });
        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        loadPrPage(1);
    }

    function selectPrKyTab(btn, value) {
        document.getElementById('pr-ky').value = value;
        document.querySelectorAll('.pr-ky-tab').forEach(function(b) {
            b.style.background = '#fff';
            b.style.color = '#475569';
            b.style.borderColor = '#e2e8f0';
            b.style.boxShadow = 'none';
        });
        btn.style.background = '#7c3aed';
        btn.style.color = '#fff';
        btn.style.borderColor = '#7c3aed';
        btn.style.boxShadow = '0 1px 3px rgba(124,58,237,0.3)';
        loadPrPage(1);
    }

    // === AJAX Pagination & Fetching ===
    window.prCurrentPage = 1;
    var searchPrTimeout = null;

    function debouncePrSearch() {
        clearTimeout(searchPrTimeout);
        searchPrTimeout = setTimeout(function() {
            loadPrPage(1);
        }, 500);
    }

    function loadPrPage(page) {
        window.prCurrentPage = page;
        var tbody = document.getElementById('pr-tbody');
        var loadMoreBtn = document.getElementById('pr-btn-loadmore');
        var loadMoreContainer = document.getElementById('pr-load-more-container');

        if (page === 1) {
            tbody.innerHTML = '<tr><td colspan="13" style="text-align:center;color:#94a3b8;padding:48px 0"><i data-lucide="loader-2" class="w-10 h-10 mx-auto text-blue-500 animate-spin mb-2"></i><div>Đang tải dữ liệu...</div></td></tr>';
            if (typeof lucide !== 'undefined') lucide.createIcons();
            loadMoreContainer.style.display = 'none';
        } else {
            if (loadMoreBtn) {
                loadMoreBtn.disabled = true;
                loadMoreBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline animate-spin mr-1"></i> Đang tải...';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }

        var fd = new FormData();
        fd.append('<?= $this->security->get_csrf_token_name() ?>', '<?= $this->security->get_csrf_hash() ?>');

        fd.append('date_from', document.querySelector('input[name="date_from"]').value);
        fd.append('date_to', document.querySelector('input[name="date_to"]').value);
        fd.append('type_report', document.querySelector('select[name="type_report"]').value);
        fd.append('room_id', document.querySelector('select[name="room_id"]').value);
        fd.append('recommend_group', document.querySelector('select[name="recommend_group"]').value);

        fd.append('year', document.getElementById('pr-year').value);
        fd.append('month', document.getElementById('pr-month').value);
        fd.append('ky', document.getElementById('pr-ky').value);
        fd.append('q', document.getElementById('pr-search').value);
        fd.append('page', page);

        fetch('<?= site_url("admin/DashboardKpi/ajax_production_report") ?>', {
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

                if (res.stats) {
                    document.getElementById('pr-stat-total').textContent = res.stats.total;
                    document.getElementById('pr-stat-kph').textContent = res.stats.kph;
                    document.getElementById('pr-stat-vp').textContent = res.stats.vp;
                    document.getElementById('pr-stat-ct').textContent = res.stats.ct;
                    document.getElementById('pr-stat-approved').textContent = res.stats.approved;
                    document.getElementById('pr-count').textContent = res.stats.total + ' phiếu';
                }

                if (res.has_more) {
                    loadMoreContainer.style.display = 'flex';
                    if (loadMoreBtn) {
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.innerHTML = 'Tải thêm báo cáo';
                    }
                } else {
                    loadMoreContainer.style.display = 'none';
                }
            })
            .catch(err => {
                console.error(err);
                if (page === 1) {
                    tbody.innerHTML = '<tr><td colspan="13" class="text-center text-red-500 py-12">Lỗi kết nối. Vui lòng thử lại.</td></tr>';
                } else if (loadMoreBtn) {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.innerHTML = 'Tải thêm báo cáo';
                    alert('Lỗi tải thêm dữ liệu');
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        onPrYearMonthChange();
    });
</script>