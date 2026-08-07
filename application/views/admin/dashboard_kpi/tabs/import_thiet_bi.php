<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
            <i data-lucide="settings" class="w-5 h-5 text-orange-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">KPI Thiết bị Công đoạn</h2>
            <p class="text-sm text-slate-500">Quản lý chỉ số vận hành và hiệu suất máy móc thiết bị</p>
        </div>
    </div>
    <div class="flex gap-2">
        <button onclick="openKpiModal('Import Thiết bị', 'import_thiet_bi')"
            class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors">
            <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
        </button>
        <button onclick="exportTbExcel()"
            class="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i> Xuất Excel
        </button>
    </div>
</div>

<!-- Filter bar -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="relative flex-1 min-w-[300px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Tìm kiếm thiết bị</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="tb-table-search" placeholder="Mã thiết bị, tên máy, công đoạn..." oninput="filterTbTable()"
                    class="w-full pl-8 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-400" style="min-width:120px">
            <i data-lucide="monitor" class="w-4 h-4"></i>
            <span id="tb-count-text">Đang tải...</span>
        </div>
        <div id="tb-loading" class="hidden">
            <div class="w-5 h-5 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div id="tb-table-area" class="overflow-x-auto" style="max-height: calc(100vh - 340px); overflow-y: auto;">
        <div id="tb-placeholder" class="py-20 text-center text-slate-300">
            <i data-lucide="cpu" class="w-14 h-14 mx-auto mb-3 opacity-20"></i>
            <div class="text-sm font-medium text-slate-400">Đang tải dữ liệu...</div>
        </div>
        <div id="tb-table-inject" class="hidden"></div>
    </div>
</div>

<style>
#tb-table-inject table { width:100%; border-collapse:collapse; font-size:0.8125rem; }
#tb-table-inject table thead tr { background:#f8fafc !important; position:sticky; top:0; z-index:2; }
#tb-table-inject table th {
    padding:10px 12px !important; font-size:0.7rem !important; font-weight:600 !important;
    text-transform:uppercase; letter-spacing:0.03em; color:#475569 !important;
    background:#f1f5f9 !important; white-space:nowrap;
    border-bottom:2px solid #e2e8f0 !important; border-right:1px solid #e2e8f0;
}
#tb-table-inject table td {
    padding:8px 12px !important; border-bottom:1px solid #f1f5f9;
    border-right:1px solid #f8fafc; color:#334155; vertical-align:middle;
}
#tb-table-inject table tbody tr:hover td { background-color:#fff7ed; }
.tb-status { padding:2px 6px; border-radius:4px; font-size:10px; font-weight:600; display:inline-block; }
.tb-status-active { background:#dcfce7; color:#166534; }
.tb-status-stop { background:#fee2e2; color:#991b1b; }
.tb-status-maint { background:#fef3c7; color:#92400e; }

.tb-nowrap { white-space: nowrap !important; }
.tb-long-text {
    white-space: normal !important;
    min-width: 150px;
    max-width: 250px;
    word-wrap: break-word;
    line-height: 1.4;
}
.tb-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #ea580c; 
    background-color: #fff7ed;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    border: 1px solid #ffedd5;
}
.tb-percent {
    font-weight: 700;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
.tb-money {
    color: #475569;
    font-weight: 600;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
</style>

<script>
var TB_BASE = '<?= site_url('admin/DashboardKpi/') ?>';

$(function() {
    loadTbTable();
});

function tbStatus(st) {
    if (!st) return '';
    var cls = 'bg-slate-100 text-slate-600';
    if (st.toLowerCase().includes('hoạt động')) cls = 'tb-status-active';
    else if (st.toLowerCase().includes('ngừng')) cls = 'tb-status-stop';
    else if (st.toLowerCase().includes('bảo trì')) cls = 'tb-status-maint';
    return '<span class="tb-status ' + cls + '">' + st + '</span>';
}

function getEquipmentProposal(score) {
    var p = parseFloat(score);
    if (isNaN(p)) return '-';
    if (p < 75) return '<div class="text-xs leading-tight text-red-600 font-bold uppercase">Kém</div><div class="text-[11px] text-red-500 leading-tight mt-0.5">Đánh Giá TSTB, Bảo Dưỡng, Xét thay thế</div>';
    if (p >= 75 && p < 80) return '<div class="text-xs leading-tight text-orange-600 font-bold uppercase">Tối Thiểu</div><div class="text-[11px] text-orange-500 leading-tight mt-0.5">Đánh Giá Bảo Dưỡng, Giám Sát-Đạt</div>';
    if (p >= 80 && p < 90) return '<div class="text-xs leading-tight text-blue-600 font-bold uppercase">Đạt</div><div class="text-[11px] text-blue-500 leading-tight mt-0.5">Duy trì sử dụng, bảo dưỡng</div>';
    if (p >= 90 && p <= 100) return '<div class="text-xs leading-tight text-emerald-600 font-bold uppercase">Tốt</div><div class="text-[11px] text-emerald-500 leading-tight mt-0.5">Đánh giá P3, Xét nâng cấp</div>';
    if (p > 100) return '<div class="text-xs leading-tight text-purple-600 font-bold uppercase">Xuất sắc</div><div class="text-[11px] text-purple-500 leading-tight mt-0.5">Đánh giá +P3, Nâng cấp ngân sách</div>';
    return '<span class="text-slate-300">-</span>';
}

function loadTbTable() {
    var loading = document.getElementById('tb-loading');
    var inject  = document.getElementById('tb-table-inject');
    var holder  = document.getElementById('tb-placeholder');
    loading.classList.remove('hidden');
    inject.classList.add('hidden');
    holder.classList.add('hidden');

    $.ajax({
        type: 'GET',
        url: TB_BASE + 'ajax_kpi_equipment_stage',
        dataType: 'json',
        success: function(res) {
            loading.classList.add('hidden');
            if (!res.success || !res.data || !res.data.length) {
                holder.classList.remove('hidden');
                holder.innerHTML = '<div class="py-16 text-center text-slate-300"><i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-30"></i><div class="text-sm">Không có dữ liệu thiết bị</div></div>';
                document.getElementById('tb-count-text').textContent = '0 thiết bị';
                lucide.createIcons();
                return;
            }
            var rows = res.data;
            var html = '<table><thead><tr>';
            var cols = ['STT','Công đoạn','Mã CĐ','Mã TB','Tên thiết bị','Trạng thái',
                'Ngừng máy (p)','Sản lượng KH','Thực tế','% Đạt',
                'Chi phí sửa','Chi phí bảo trì','Tổng chi phí','Cảnh báo', 'Đề xuất Sử dụng', 'Thao tác'];
            cols.forEach(function(c){ html += '<th class="text-center tb-nowrap">'+c+'</th>'; });
            html += '</tr></thead><tbody>';

            rows.forEach(function(r, i) {
                html += '<tr>';
                html += '<td class="text-center tb-nowrap">'+(i+1)+'</td>';
                html += '<td class="tb-long-text text-slate-600">'+(r.group_stage||'')+'</td>';
                html += '<td class="tb-nowrap"><span class="tb-code">'+(r.stage_code||'')+'</span></td>';
                html += '<td class="tb-nowrap"><span class="tb-code">'+(r.equipment_code||'')+'</span></td>';
                html += '<td class="tb-long-text font-medium text-slate-800">'+(r.equipment_name||'')+'</td>';
                html += '<td class="text-center tb-nowrap">'+tbStatus(r.equipment_status)+'</td>';
                var downtime = parseFloat(r.downtime_minutes) || 0;
                var planned = parseFloat(r.planned_output) || 0;
                var actual = parseFloat(r.actual_output) || 0;
                var pct = parseFloat(r.target_achievement_pct)||0;
                var repair = parseFloat(r.repair_cost) || 0;
                var maint = parseFloat(r.maintenance_cost) || 0;
                var total = parseFloat(r.total_cost) || 0;

                html += '<td class="text-right tb-nowrap font-mono text-slate-600">'+(downtime == 0 ? '-' : downtime)+'</td>';
                html += '<td class="text-right tb-nowrap font-mono text-slate-600">'+(planned == 0 ? '-' : planned.toLocaleString())+'</td>';
                html += '<td class="text-right tb-nowrap font-mono text-slate-800 font-semibold">'+(actual == 0 ? '-' : actual.toLocaleString())+'</td>';
                
                var pctCls = pct >= 100 ? 'text-emerald-600' : (pct >= 80 ? 'text-amber-600' : 'text-red-600');
                
                html += '<td class="text-center tb-nowrap tb-percent ' + pctCls + '">'+(pct == 0 ? '-' : pct+'%')+'</td>';
                html += '<td class="text-right tb-nowrap tb-money">'+(repair == 0 ? '-' : repair.toLocaleString())+'</td>';
                html += '<td class="text-right tb-nowrap tb-money">'+(maint == 0 ? '-' : maint.toLocaleString())+'</td>';
                html += '<td class="text-right tb-nowrap tb-money text-orange-700">'+(total == 0 ? '-' : total.toLocaleString())+'</td>';
                html += '<td class="text-center tb-long-text text-xs text-slate-500">'+(r.warning_status||'')+'</td>';
                html += '<td class="text-center tb-nowrap">' + getEquipmentProposal(pct) + '</td>';
                html += '<td class="tb-nowrap" style="text-align:center">';
                html += '<button onclick="deleteTb('+r.id+')" style="padding:5px;color:#e11d48;border:none;background:none;cursor:pointer;border-radius:6px" onmouseover="this.style.background=\'#fff1f2\'" onmouseout="this.style.background=\'none\'" title="Xóa">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>' +
                        '</button>';
                html += '</td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            inject.innerHTML = html;
            inject.classList.remove('hidden');
            document.getElementById('tb-count-text').textContent = rows.length + ' thiết bị';
            lucide.createIcons();
            filterTbTable();
        },
        error: function() {
            loading.classList.add('hidden');
            holder.classList.remove('hidden');
            holder.innerHTML = '<div class="py-16 text-center text-red-300 text-sm">Lỗi kết nối!</div>';
        }
    });
}

function filterTbTable() {
    var q = document.getElementById('tb-table-search').value.toLowerCase();
    var rows = document.querySelectorAll('#tb-table-inject tbody tr');
    var visible = 0;
    rows.forEach(function(r) {
        var match = !q || r.textContent.toLowerCase().indexOf(q) >= 0;
        r.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    if (q) document.getElementById('tb-count-text').textContent = visible + ' / ' + rows.length + ' thiết bị';
}

function exportTbExcel() {
    // Logic gọi export từ controller gốc
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= admin_url('kpi_equipment_stage/export_excel') ?>';
    
    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '<?= $this->security->get_csrf_token_name() ?>';
    csrf.value = '<?= $this->security->get_csrf_hash() ?>';
    form.appendChild(csrf);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function deleteTb(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa mục tiêu thiết bị này?')) return;
    var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
    $.ajax({
        type: 'POST',
        url: TB_BASE + 'delete_kpi_entry/import_thiet_bi/' + id,
        data: { [csrfName]: csrfHash },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert_float('success', res.message);
                loadTbTable();
            } else {
                alert_float('danger', res.message);
            }
        }
    });
}
</script>
