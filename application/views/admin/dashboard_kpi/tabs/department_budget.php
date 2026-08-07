<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
            <i data-lucide="building-2" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Ngân sách Phòng ban</h2>
            <p class="text-sm text-slate-500">Quản lý định mức ngân sách và chi phí thực tế theo năm <?= date('Y') ?></p>
        </div>
    </div>
    <div class="flex gap-2">
        <button onclick="openKpiModal('Import Ngân sách Phòng ban', 'department_budget')"
            class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors">
            <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
        </button>
    </div>
</div>

<!-- Filter bar -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="min-w-[240px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Phòng ban</label>
            <select id="db-dept-select" style="width:100%">
                <option value="">Tất cả phòng ban</option>
                <?php foreach ($dtDepartment as $d): ?>
                <option value="<?= $d['departmentid'] ?>"><?= $d['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="relative flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Tìm kiếm loại chi phí</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="db-table-search" placeholder="Mã phí, tên phí..." oninput="filterDbTable()"
                    class="w-full pl-8 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-400" style="min-width:120px">
            <i data-lucide="calculator" class="w-4 h-4"></i>
            <span id="db-count-text">Đang tải...</span>
        </div>
        <div id="db-loading" class="hidden">
            <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div id="db-table-area" class="overflow-x-auto" style="max-height: calc(100vh - 340px); overflow-y: auto;">
        <div id="db-placeholder" class="py-20 text-center text-slate-300">
            <i data-lucide="building-2" class="w-14 h-14 mx-auto mb-3 opacity-20"></i>
            <div class="text-sm font-medium text-slate-400">Đang tải dữ liệu ngân sách...</div>
        </div>
        <div id="db-table-inject" class="hidden"></div>
    </div>
</div>

<style>
#db-table-inject table { width:100%; border-collapse:collapse; font-size:0.8125rem; }
#db-table-inject table thead tr { background:#f8fafc !important; position:sticky; top:0; z-index:2; }
#db-table-inject table th {
    padding:10px 12px !important; font-size:0.7rem !important; font-weight:600 !important;
    text-transform:uppercase; letter-spacing:0.03em; color:#475569 !important;
    background:#f1f5f9 !important; white-space:nowrap;
    border-bottom:2px solid #e2e8f0 !important; border-right:1px solid #e2e8f0;
}
#db-table-inject table td {
    padding:8px 12px !important; border-bottom:1px solid #f1f5f9;
    border-right:1px solid #f8fafc; color:#334155; vertical-align:middle;
}
#db-table-inject table tbody tr:hover td { background-color:#eff6ff; }
.db-badge { padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; display:inline-block; }
.db-badge-good { background:#dcfce7; color:#166534; }
.db-badge-ok { background:#dbeafe; color:#1e40af; }
.db-badge-warn { background:#fef3c7; color:#92400e; }
.db-badge-danger { background:#fee2e2; color:#991b1b; }

.db-nowrap { white-space: nowrap !important; }
.db-long-text {
    white-space: normal !important;
    min-width: 150px;
    max-width: 250px;
    word-wrap: break-word;
    line-height: 1.4;
}
.db-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #2563eb; 
    background-color: #eff6ff;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
}
.db-percent {
    color: #059669;
    font-weight: 700;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
.db-money {
    color: #475569;
    font-weight: 600;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
</style>

<script>
var DB_BASE = '<?= site_url('admin/DashboardKpi/') ?>';

$(function() {
    $('#db-dept-select').select2();
    $('#db-dept-select').on('change', function() { loadDbTable(); });
    loadDbTable();
});

function getBudgetStatus(ratio) {
    if (ratio <= 90) return { label: 'Tốt', cls: 'db-badge-good', score: 100 };
    if (ratio <= 100) return { label: 'Đạt', cls: 'db-badge-ok', score: 90 };
    if (ratio <= 110) return { label: 'Cảnh báo', cls: 'db-badge-warn', score: 70 };
    return { label: 'Vượt', cls: 'db-badge-danger', score: 50 };
}

function loadDbTable() {
    var deptId  = $('#db-dept-select').val();
    var loading = document.getElementById('db-loading');
    var inject  = document.getElementById('db-table-inject');
    var holder  = document.getElementById('db-placeholder');
    loading.classList.remove('hidden');
    inject.classList.add('hidden');
    holder.classList.add('hidden');

    $.ajax({
        type: 'GET',
        url: DB_BASE + 'ajax_department_budget',
        data: { department_id: deptId },
        dataType: 'json',
        success: function(res) {
            loading.classList.add('hidden');
            if (!res.success || !res.data || !res.data.length) {
                holder.classList.remove('hidden');
                holder.innerHTML = '<div class="py-16 text-center text-slate-300"><i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-30"></i><div class="text-sm">Không tìm thấy định mức ngân sách</div></div>';
                document.getElementById('db-count-text').textContent = '0 mục';
                lucide.createIcons();
                return;
            }
            var rows = res.data;
            var html = '<table><thead><tr>';
            var cols = ['STT','Mã PB','Phòng ban','Mã phí','Loại chi phí',
                'Ngân sách cấp','Thực chi','Chênh lệch','% Sử dụng',
                'Trạng thái','Điểm KPI','Ghi chú', 'Thao tác'];
            cols.forEach(function(c){ html += '<th class="text-center db-nowrap">'+c+'</th>'; });
            html += '</tr></thead><tbody>';

            rows.forEach(function(r, i) {
                var budget = parseFloat(r.ngan_sach_duoc_cap) || 0;
                var actual = parseFloat(r.chi_phi_thuc_te) || 0;
                var diff   = actual - budget;
                var ratio  = budget > 0 ? (actual / budget * 100) : 0;
                var st     = getBudgetStatus(ratio);
                
                var clClass = diff > 0 ? 'text-red-600 font-bold' : 'text-emerald-600 font-bold';

                html += '<tr>';
                html += '<td class="text-center db-nowrap">'+(i+1)+'</td>';
                html += '<td class="db-nowrap"><span class="db-code">'+(r.ma_phong_ban||'')+'</span></td>';
                html += '<td class="db-long-text font-medium text-slate-800">'+(r.ten_phong_ban||'')+'</td>';
                html += '<td class="db-nowrap"><span class="db-code">'+(r.ma_loai_chi_phi||'')+'</span></td>';
                html += '<td class="db-long-text">'+(r.ten_loai_chi_phi||'')+'</td>';
                html += '<td class="text-right db-nowrap db-money">'+(budget == 0 ? '-' : budget.toLocaleString())+'</td>';
                html += '<td class="text-right db-nowrap db-money">'+(actual == 0 ? '-' : actual.toLocaleString())+'</td>';
                html += '<td class="text-right db-nowrap db-money '+clClass+'">'+(diff == 0 ? '-' : (diff > 0 ? '+' : '')+diff.toLocaleString())+'</td>';
                html += '<td class="text-center db-nowrap db-percent">'+(ratio == 0 ? '-' : ratio.toFixed(2)+'%')+'</td>';
                html += '<td class="text-center db-nowrap"><span class="db-badge '+st.cls+'">'+st.label+'</span></td>';
                html += '<td class="text-center db-nowrap text-amber-600 font-black text-sm">'+(st.score == 0 ? '-' : st.score)+'</td>';
                html += '<td class="db-long-text text-xs text-slate-500">'+(r.ghi_chu||'')+'</td>';
                html += '<td class="db-nowrap" style="text-align:center">';
                html += '<button onclick="deleteDb('+r.id+')" style="padding:5px;color:#e11d48;border:none;background:none;cursor:pointer;border-radius:6px" onmouseover="this.style.background=\'#fff1f2\'" onmouseout="this.style.background=\'none\'" title="Xóa">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>' +
                        '</button>';
                html += '</td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            inject.innerHTML = html;
            inject.classList.remove('hidden');
            document.getElementById('db-count-text').textContent = rows.length + ' mục';
            lucide.createIcons();
            filterDbTable();
        },
        error: function() {
            loading.classList.add('hidden');
            holder.classList.remove('hidden');
            holder.innerHTML = '<div class="py-16 text-center text-red-300 text-sm">Lỗi kết nối!</div>';
        }
    });
}

function filterDbTable() {
    var q = document.getElementById('db-table-search').value.toLowerCase();
    var rows = document.querySelectorAll('#db-table-inject tbody tr');
    var visible = 0;
    rows.forEach(function(r) {
        var match = !q || r.textContent.toLowerCase().indexOf(q) >= 0;
        r.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    if (q) document.getElementById('db-count-text').textContent = visible + ' / ' + rows.length + ' mục';
}

function deleteDb(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa định mức ngân sách này?')) return;
    var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
    $.ajax({
        type: 'POST',
        url: DB_BASE + 'delete_kpi_entry/department_budget/' + id,
        data: { [csrfName]: csrfHash },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert_float('success', res.message);
                loadDbTable();
            } else {
                alert_float('danger', res.message);
            }
        }
    });
}
</script>
