<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
            <i data-lucide="users" class="w-5 h-5 text-emerald-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Mục tiêu KPI Khách hàng</h2>
            <p class="text-sm text-slate-500">Theo dõi chỉ tiêu KPI khách hàng theo từng năm</p>
        </div>
    </div>
    <div class="flex gap-2">
        <button onclick="openKpiModal('Import Khách hàng', 'import_khach_hang')"
            class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors">
            <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
        </button>
        <button onclick="exportKhExcel()"
            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i> Xuất Excel
        </button>
    </div>
</div>

<!-- Filter bar -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="w-36">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Năm</label>
            <select id="kh-year-select" style="width:100%">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($y == date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="relative flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Tìm kiếm</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="kh-table-search" placeholder="Mã KH, tên công ty..." oninput="filterKhTable()"
                    class="w-full pl-8 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-400" style="min-width:120px">
            <i data-lucide="table-2" class="w-4 h-4"></i>
            <span id="kh-count-text">Đang tải...</span>
        </div>
        <div id="kh-loading" class="hidden">
            <div class="w-5 h-5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div id="kh-table-area" class="overflow-x-auto" style="max-height: calc(100vh - 340px); overflow-y: auto;">
        <div id="kh-placeholder" class="py-20 text-center text-slate-300">
            <i data-lucide="users" class="w-14 h-14 mx-auto mb-3 opacity-20"></i>
            <div class="text-sm font-medium text-slate-400">Đang tải dữ liệu...</div>
        </div>
        <div id="kh-table-inject" class="hidden"></div>
    </div>
</div>

<style>
    #kh-table-inject table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }

    #kh-table-inject table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #kh-table-inject table thead tr {
        background: #f8fafc !important;
    }

    #kh-table-inject table th {
        padding: 10px 12px !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #475569 !important;
        background: #f1f5f9 !important;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0 !important;
        border-right: 1px solid #e2e8f0;
    }

    #kh-table-inject table td {
        padding: 8px 12px !important;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #334155;
        vertical-align: middle;
    }

    #kh-table-inject table tbody tr:hover td {
        background-color: #f0fdf4;
    }

    #kh-table-inject table tbody tr:nth-child(even) td {
        background-color: #fafafa;
    }

    #kh-table-inject table tbody tr:nth-child(even):hover td {
        background-color: #f0fdf4;
    }

    .kh-nowrap {
        white-space: nowrap !important;
    }

    .kh-long-text {
        white-space: normal !important;
        min-width: 140px;
        max-width: 220px;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .kh-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        color: #059669;
        background-color: #f0fdf4;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        border: 1px solid #bbf7d0;
    }

    .kh-frac-wrap {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 1px;
        line-height: 1;
    }

    .kh-frac-main {
        font-size: 13px;
        font-weight: 700;
        color: #059669;
    }

    .kh-frac-line {
        width: 20px;
        height: 1.5px;
        background: #bbf7d0;
        margin: 2px 0;
    }

    .kh-frac-sub {
        font-size: 11px;
        font-weight: 500;
        color: #94a3b8;
    }

    .kh-col-sub {
        font-size: 9px;
        color: #94a3b8;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 2px;
        font-weight: 400;
    }

    .kh-score-pos {
        color: #059669;
        font-weight: 700;
    }

    .kh-score-neg {
        color: #e11d48;
        font-weight: 700;
    }

    .kh-badge {
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .kh-badge-good {
        background: #d1fae5;
        color: #065f46;
    }

    .kh-badge-normal {
        background: #dbeafe;
        color: #1e40af;
    }

    .kh-badge-warn {
        background: #fef3c7;
        color: #92400e;
    }

    .kh-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .kh-actual {
        font-weight: 700;
        color: #059669;
        font-size: 13px;
    }

    .kh-target {
        font-weight: 500;
        color: #94a3b8;
        font-size: 12px;
    }
</style>

<script>
    var KH_BASE = '<?= site_url('admin/DashboardKpi/') ?>';
    var KH_CSRF = '<?= $this->security->get_csrf_token_name() ?>';
    var KH_HASH = '<?= $this->security->get_csrf_hash() ?>';

    $(function() {
        $('#kh-year-select').select2({
            minimumResultsForSearch: -1
        });
        $('#kh-year-select').on('change', function() {
            loadKhTable();
        });
        loadKhTable();
    });

    function khActual(v) {
        v = v || 0;
        return '<span class="kh-val-actual">' + v + '</span>';
    }

    function khTarget(v) {
        v = v || 0;
        return '<span class="kh-val-target">' + v + '</span>';
    }

    function statusBadge(score) {
        score = parseInt(score) || 0;
        if (score >= 80) return '<span class="kh-badge kh-badge-good">Tốt</span>';
        if (score >= 60) return '<span class="kh-badge kh-badge-normal">Bình Thường</span>';
        if (score >= 40) return '<span class="kh-badge kh-badge-warn">Cảnh Báo</span>';
        return '<span class="kh-badge kh-badge-danger">Nguy Cơ</span>';
    }

    function getCooperationProposal(score) {
        var p = parseFloat(score);
        if (isNaN(p)) return '-';
        if (p >= 75 && p < 80) return '<div class="text-xs leading-tight text-orange-600 font-bold uppercase">Tối Thiểu</div><div class="text-[11px] text-orange-500 leading-tight mt-0.5">Duy trì hợp tác, Giám sát đến khi đạt</div>';
        if (p >= 80 && p < 90) return '<div class="text-xs leading-tight text-blue-600 font-bold uppercase">Đạt</div><div class="text-[11px] text-blue-500 leading-tight mt-0.5">Duy Trì Hợp Tác</div>';
        if (p >= 90 && p <= 100) return '<div class="text-xs leading-tight text-emerald-600 font-bold uppercase">Tốt</div><div class="text-[11px] text-emerald-500 leading-tight mt-0.5">Đánh Giá P3, Nâng Cấp ngân sách CSKH</div>';
        if (p > 100) return '<div class="text-xs leading-tight text-purple-600 font-bold uppercase">Xuất sắc</div><div class="text-[11px] text-purple-500 leading-tight mt-0.5">Đánh giá P3, Nâng cấp ngân sách, triết khấu</div>';
        return '<span class="text-slate-300">-</span>';
    }

    function loadKhTable() {
        var year = $('#kh-year-select').val();
        var loading = document.getElementById('kh-loading');
        var inject = document.getElementById('kh-table-inject');
        var holder = document.getElementById('kh-placeholder');
        loading.classList.remove('hidden');
        inject.classList.add('hidden');
        holder.classList.add('hidden');

        $.ajax({
            type: 'GET',
            url: KH_BASE + 'ajax_kpi_targets_clients',
            data: {
                year: year
            },
            dataType: 'json',
            success: function(res) {
                loading.classList.add('hidden');
                if (!res.success || !res.data || !res.data.length) {
                    holder.classList.remove('hidden');
                    holder.innerHTML = '<div class="py-16 text-center text-slate-300"><i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-30"></i><div class="text-sm">Không có dữ liệu</div></div>';
                    document.getElementById('kh-count-text').textContent = '0 khách hàng';
                    lucide.createIcons();
                    return;
                }
                var rows = res.data;
                var html = '<table><thead>';
                // Row 1: group headers
                html += '<tr>';
                html += '<th rowspan="3" class="kh-nowrap">STT</th>';
                html += '<th rowspan="3" class="kh-nowrap">Mã KH</th>';
                html += '<th rowspan="3" style="min-width:150px">Tên khách hàng</th>';
                html += '<th rowspan="3" class="kh-nowrap">Nhóm</th>';
                html += '<th colspan="6" style="text-align:center;background:#ecfdf5;color:#059669">Báo giá</th>';
                html += '<th colspan="4" style="text-align:center;background:#eff6ff;color:#2563eb">Đơn hàng</th>';
                html += '<th colspan="4" style="text-align:center;background:#f5f3ff;color:#7c3aed">PTM</th>';
                html += '<th colspan="2" style="text-align:center;background:#fdf4ff;color:#a21caf">Complain</th>';
                html += '<th rowspan="3" class="kh-nowrap">Mẫu L1</th>';
                html += '<th rowspan="3" class="kh-nowrap">Mẫu L2</th>';
                html += '<th colspan="3" style="text-align:center;background:#fff7ed;color:#ea580c">Điểm KPI</th>';
                html += '<th rowspan="3" class="kh-nowrap">Trạng thái</th>';
                html += '<th rowspan="3" class="kh-nowrap">Đề xuất Hợp tác</th>';
                html += '<th rowspan="3" class="kh-nowrap"></th>';
                html += '</tr>';
                // Row 2: sub-group labels
                html += '<tr>';
                html += '<th colspan="2" style="background:#ecfdf5;color:#065f46;text-align:center">Tổng BG</th>';
                html += '<th colspan="2" style="background:#ecfdf5;color:#065f46;text-align:center">BG đã duyệt</th>';
                html += '<th colspan="2" style="background:#ecfdf5;color:#065f46;text-align:center">BG chưa duyệt</th>';
                html += '<th colspan="2" style="background:#eff6ff;color:#1d4ed8;text-align:center">Có đơn hàng</th>';
                html += '<th colspan="2" style="background:#eff6ff;color:#1d4ed8;text-align:center">Không đơn</th>';
                html += '<th colspan="2" style="background:#f5f3ff;color:#6d28d9;text-align:center">PTM có đơn</th>';
                html += '<th colspan="2" style="background:#f5f3ff;color:#6d28d9;text-align:center">PTM không đơn</th>';
                html += '<th colspan="2" style="background:#fdf4ff;color:#a21caf;text-align:center">Số complain</th>';
                html += '<th rowspan="2" style="background:#fff7ed;color:#16a34a;text-align:center">Cộng</th>';
                html += '<th rowspan="2" style="background:#fff7ed;color:#e11d48;text-align:center">Trừ</th>';
                html += '<th rowspan="2" style="background:#fff7ed;text-align:center">Tổng</th>';
                html += '</tr>';
                // Row 3: Thực tế / Chỉ tiêu labels
                var bgSub = ['#ecfdf5', '#ecfdf5', '#ecfdf5', '#eff6ff', '#eff6ff', '#f5f3ff', '#f5f3ff', '#fdf4ff'];
                for (var _i = 0; _i < 8; _i++) {
                    html += '<th style="background:' + bgSub[_i] + ';color:#059669;text-align:center;font-size:9px">Thực tế</th>';
                    html += '<th style="background:' + bgSub[_i] + ';color:#94a3b8;text-align:center;font-size:9px">Chỉ tiêu</th>';
                }
                html += '</tr></thead><tbody>';

                rows.forEach(function(r, i) {
                    var DiemCong = parseInt(r.DonHangCoTT) || 0;
                    var DiemTru = 0;
                    var sc = parseInt(r.SoComplainTT) || 0;
                    if (sc == 1) DiemTru = 3;
                    else if (sc == 2) DiemTru = 5;
                    else if (sc > 2) DiemTru = (10 * (sc - 2)) + 8;
                    var Tong = DiemCong - DiemTru;

                    html += '<tr>';
                    html += '<td class="text-center kh-nowrap text-slate-400">' + (i + 1) + '</td>';
                    html += '<td class="kh-nowrap"><span class="kh-code">' + (r.zcode || '') + '</span></td>';
                    html += '<td class="kh-long-text font-medium text-slate-800">' + (r.company || '') + '</td>';
                    html += '<td class="kh-nowrap text-xs text-slate-500">' + (r.list_name_group || '') + '</td>';
                    var f0 = function(v) { var n = parseFloat(v); return (isNaN(n) || n === 0) ? '-' : n; };
                    
                    // Báo giá
                    html += '<td class="text-center kh-actual" style="background:#f9fefb">' + f0(r.SoBaoGiaTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#f9fefb">' + f0(r.SoBaoGia) + '</td>';
                    html += '<td class="text-center kh-actual" style="background:#f9fefb">' + f0(r.BaoGiaDaDuyetTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#f9fefb">' + f0(r.BaoGiaDaDuyet) + '</td>';
                    html += '<td class="text-center kh-actual" style="background:#f9fefb">' + f0(r.BaoGiaChuaDuyetTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#f9fefb">' + f0(r.BaoGiaChuaDuyet) + '</td>';
                    // Đơn hàng
                    html += '<td class="text-center kh-actual" style="background:#f5f9ff">' + f0(r.DonHangCoTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#f5f9ff">' + f0(r.DonHangCo) + '</td>';
                    html += '<td class="text-center kh-actual" style="background:#f5f9ff">' + f0(r.DonHangKhongCoTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#f5f9ff">' + f0(r.DonHangKhongCo) + '</td>';
                    // PTM
                    html += '<td class="text-center kh-actual" style="background:#faf5ff">' + f0(r.PTMCoDonTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#faf5ff">' + f0(r.PTMCoDon) + '</td>';
                    html += '<td class="text-center kh-actual" style="background:#faf5ff">' + f0(r.PTMKhongDonTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#faf5ff">' + f0(r.PTMKhongDon) + '</td>';
                    // Complain
                    html += '<td class="text-center kh-actual" style="background:#fdf4ff">' + f0(r.SoComplainTT) + '</td>';
                    html += '<td class="text-center kh-target" style="background:#fdf4ff">' + f0(r.SoComplain) + '</td>';
                    // Mẫu
                    html += '<td class="text-center kh-nowrap font-semibold text-slate-700">' + f0(r.MauLan1) + '</td>';
                    html += '<td class="text-center kh-nowrap font-semibold text-slate-700">' + f0(r.MauLan2) + '</td>';
                    // Điểm
                    html += '<td class="text-center kh-nowrap text-emerald-600 font-bold" style="background:#f0fdf4">' + (DiemCong == 0 ? '-' : '+' + DiemCong) + '</td>';
                    html += '<td class="text-center kh-nowrap text-rose-600 font-bold" style="background:#fff1f2">' + (DiemTru == 0 ? '-' : '-' + DiemTru) + '</td>';
                    html += '<td class="text-center kh-nowrap font-black text-base text-slate-800">' + (Tong == 0 ? '-' : Tong) + '</td>';
                    html += '<td class="text-center kh-nowrap">' + statusBadge(Tong) + '</td>';
                    html += '<td class="text-center kh-nowrap">' + getCooperationProposal(Tong) + '</td>';
                    html += '<td class="text-center kh-nowrap"><button onclick="deleteKh(' + r.id + ')" class="text-rose-500 p-1"><i data-lucide="trash-2" class="w-4 h-4"></i></button></td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                inject.innerHTML = html;
                inject.classList.remove('hidden');
                document.getElementById('kh-count-text').textContent = rows.length + ' khách hàng';
                lucide.createIcons();
                filterKhTable();
            },
            error: function() {
                loading.classList.add('hidden');
                holder.classList.remove('hidden');
                holder.innerHTML = '<div class="py-16 text-center text-red-300 text-sm">Lỗi kết nối!</div>';
            }
        });
    }

    function filterKhTable() {
        var q = document.getElementById('kh-table-search').value.toLowerCase();
        var rows = document.querySelectorAll('#kh-table-inject tbody tr');
        var visible = 0;
        rows.forEach(function(r) {
            var match = !q || r.textContent.toLowerCase().indexOf(q) >= 0;
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (q) document.getElementById('kh-count-text').textContent = visible + ' / ' + rows.length + ' khách hàng';
    }

    function exportKhExcel() {
        var year = $('#kh-year-select').val();
        $.ajax({
            type: 'POST',
            url: '<?= site_url('admin/kpi_targets_clients/export_excel') ?>',
            data: {
                year_search: year,
                export_excel: 1,
                [KH_CSRF]: KH_HASH
            },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                if (res.result) {
                    var a = document.createElement('a');
                    a.href = res.file;
                    a.download = res.filename;
                    a.click();
                }
            }
        });
    }

    function deleteKh(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa mục tiêu KPI này?')) return;
        $.ajax({
            type: 'POST',
            url: KH_BASE + 'delete_kpi_entry/import_khach_hang/' + id,
            data: {
                [KH_CSRF]: KH_HASH
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert_float('success', res.message);
                    loadKhTable();
                } else {
                    alert_float('danger', res.message);
                }
            }
        });
    }
</script>