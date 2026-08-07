<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
            <i data-lucide="truck" class="w-5 h-5 text-indigo-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Mục tiêu KPI Nhà cung cấp</h2>
            <p class="text-sm text-slate-500">Theo dõi chỉ tiêu KPI nhà cung cấp theo năm</p>
        </div>
    </div>
    <div class="flex gap-2">
        <button onclick="openKpiModal('Import Nhà cung cấp', 'import_ncc')"
            class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors">
            <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
        </button>
        <button onclick="exportNccExcel()"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i> Xuất Excel
        </button>
    </div>
</div>

<!-- Filter bar -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="w-36">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Năm</label>
            <select id="ncc-year-select" style="width:100%">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($y == date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="relative flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Tìm kiếm</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="ncc-table-search" placeholder="Mã NCC, tên công ty..." oninput="filterNccTable()"
                    class="w-full pl-8 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-400" style="min-width:120px">
            <i data-lucide="table-2" class="w-4 h-4"></i>
            <span id="ncc-count-text">Đang tải...</span>
        </div>
        <div id="ncc-loading" class="hidden">
            <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div id="ncc-table-area" class="overflow-x-auto" style="max-height: calc(100vh - 340px); overflow-y: auto;">
        <div id="ncc-placeholder" class="py-20 text-center text-slate-300">
            <i data-lucide="truck" class="w-14 h-14 mx-auto mb-3 opacity-20"></i>
            <div class="text-sm font-medium text-slate-400">Đang tải dữ liệu...</div>
        </div>
        <div id="ncc-table-inject" class="hidden"></div>
    </div>
</div>

<style>
    #ncc-table-inject table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }

    #ncc-table-inject table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #ncc-table-inject table thead tr {
        background: #f8fafc !important;
    }

    #ncc-table-inject table th {
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

    #ncc-table-inject table td {
        padding: 8px 12px !important;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #334155;
        vertical-align: middle;
    }

    #ncc-table-inject table tbody tr:hover td {
        background-color: #f5f3ff;
    }

    #ncc-table-inject table tbody tr:nth-child(even) td {
        background-color: #fafafa;
    }

    .ncc-nowrap {
        white-space: nowrap !important;
    }

    .ncc-long-text {
        white-space: normal !important;
        min-width: 140px;
        max-width: 220px;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .ncc-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        color: #4f46e5;
        background-color: #eef2ff;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        border: 1px solid #c7d2fe;
    }

    .ncc-frac-wrap {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 1px;
        line-height: 1;
    }

    .ncc-frac-main {
        font-size: 13px;
        font-weight: 700;
        color: #4f46e5;
    }

    .ncc-frac-line {
        width: 20px;
        height: 1.5px;
        background: #c7d2fe;
        margin: 2px 0;
    }

    .ncc-frac-sub {
        font-size: 11px;
        font-weight: 500;
        color: #94a3b8;
    }

    .ncc-col-sub {
        font-size: 9px;
        color: #94a3b8;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 2px;
        font-weight: 400;
    }

    .ncc-badge {
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .ncc-badge-good {
        background: #e0e7ff;
        color: #3730a3;
    }

    .ncc-badge-normal {
        background: #dcfce7;
        color: #166534;
    }

    .ncc-badge-warn {
        background: #fef3c7;
        color: #92400e;
    }

    .ncc-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .ncc-actual {
        font-weight: 700;
        color: #4f46e5;
        font-size: 13px;
    }

    .ncc-target {
        font-weight: 500;
        color: #94a3b8;
        font-size: 12px;
    }
</style>

<script>
    var NCC_BASE = '<?= site_url('admin/DashboardKpi/') ?>';
    var NCC_CSRF = '<?= $this->security->get_csrf_token_name() ?>';
    var NCC_HASH = '<?= $this->security->get_csrf_hash() ?>';

    $(function() {
        $('#ncc-year-select').select2({
            minimumResultsForSearch: -1
        });
        $('#ncc-year-select').on('change', function() {
            loadNccTable();
        });
        loadNccTable();
    });

    function nfrac(tt, mt) {
        tt = tt || 0;
        mt = mt || 0;
        return '<div class="text-center ncc-nowrap"><div class="ncc-frac-wrap"><span class="ncc-frac-main">' + tt + '</span><span class="ncc-frac-line"></span><span class="ncc-frac-sub">' + mt + '</span></div></div>';
    }

    function nccStatusBadge(score) {
        score = parseInt(score) || 0;
        if (score >= 80) return '<span class="ncc-badge ncc-badge-good">NCC Tốt</span>';
        if (score >= 60) return '<span class="ncc-badge ncc-badge-normal">Bình Thường</span>';
        if (score >= 40) return '<span class="ncc-badge ncc-badge-warn">Cảnh Báo</span>';
        return '<span class="ncc-badge ncc-badge-danger">Thay Thế</span>';
    }

    function getSupplierProposal(score) {
        var p = parseFloat(score);
        if (isNaN(p)) return '-';
        if (p >= 75 && p < 80) return '<div class="text-xs leading-tight text-orange-600 font-bold uppercase">Tối Thiểu</div><div class="text-[11px] text-orange-500 leading-tight mt-0.5">Duy trì hợp tác, Giám sát đến khi đạt</div>';
        if (p >= 80 && p < 90) return '<div class="text-xs leading-tight text-blue-600 font-bold uppercase">Đạt</div><div class="text-[11px] text-blue-500 leading-tight mt-0.5">Duy Trì Hợp Tác</div>';
        if (p >= 90 && p <= 100) return '<div class="text-xs leading-tight text-emerald-600 font-bold uppercase">Tốt</div><div class="text-[11px] text-emerald-500 leading-tight mt-0.5">Đánh giá P3, Nâng cấp ngân sách Mua</div>';
        if (p > 100) return '<div class="text-xs leading-tight text-purple-600 font-bold uppercase">Xuất sắc</div><div class="text-[11px] text-purple-500 leading-tight mt-0.5">Đánh giá P3, Nâng cấp ngân sách, Tăng SL mua cũng thêm</div>';
        return '<span class="text-slate-300">-</span>';
    }

    function loadNccTable() {
        var year = $('#ncc-year-select').val();
        var loading = document.getElementById('ncc-loading');
        var inject = document.getElementById('ncc-table-inject');
        var holder = document.getElementById('ncc-placeholder');
        loading.classList.remove('hidden');
        inject.classList.add('hidden');
        holder.classList.add('hidden');

        $.ajax({
            type: 'GET',
            url: NCC_BASE + 'ajax_kpi_targets_supplier',
            data: {
                year: year
            },
            dataType: 'json',
            success: function(res) {
                loading.classList.add('hidden');
                if (!res.success || !res.data || !res.data.length) {
                    holder.classList.remove('hidden');
                    holder.innerHTML = '<div class="py-16 text-center text-slate-300"><i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-30"></i><div class="text-sm">Không có dữ liệu</div></div>';
                    document.getElementById('ncc-count-text').textContent = '0 nhà cung cấp';
                    lucide.createIcons();
                    return;
                }
                var rows = res.data;
                var y = year;
                // 3-row grouped thead
                var html = '<table><thead>';
                html += '<tr>';
                html += '<th rowspan="3" class="ncc-nowrap text-center">STT</th>';
                html += '<th rowspan="3" class="ncc-nowrap">Mã NCC</th>';
                html += '<th rowspan="3" style="min-width:150px">Tên nhà cung cấp</th>';
                html += '<th rowspan="3" class="ncc-nowrap">Nhóm NCC</th>';
                html += '<th colspan="6" style="text-align:center;background:#eef2ff;color:#4f46e5">Báo giá ' + y + '</th>';
                html += '<th colspan="8" style="text-align:center;background:#f0fdf4;color:#16a34a">Đơn hàng ' + y + '</th>';
                html += '<th colspan="2" style="text-align:center;background:#fdf4ff;color:#7c3aed">Complain</th>';
                html += '<th colspan="3" style="text-align:center;background:#fff7ed;color:#ea580c">Điểm KPI</th>';
                html += '<th rowspan="3" class="ncc-nowrap">Trạng thái</th>';
                html += '<th rowspan="3" class="ncc-nowrap">Đề xuất Hợp tác</th>';
                html += '<th rowspan="3" class="ncc-nowrap">Thao tác</th>';
                html += '</tr>';
                html += '<tr>';
                html += '<th colspan="2" style="background:#eef2ff;color:#3730a3;text-align:center">BG nhận</th>';
                html += '<th colspan="2" style="background:#eef2ff;color:#3730a3;text-align:center">BG duyệt</th>';
                html += '<th colspan="2" style="background:#eef2ff;color:#3730a3;text-align:center">BG chưa duyệt</th>';
                html += '<th colspan="2" style="background:#f0fdf4;color:#15803d;text-align:center">Số đơn hàng</th>';
                html += '<th colspan="2" style="background:#f0fdf4;color:#15803d;text-align:center">Giao đúng hạn</th>';
                html += '<th colspan="2" style="background:#fff5f5;color:#dc2626;text-align:center">Giao trễ</th>';
                html += '<th colspan="2" style="background:#fff5f5;color:#dc2626;text-align:center">Lỗi chất lượng</th>';
                html += '<th colspan="2" style="background:#fdf4ff;color:#7c3aed;text-align:center">Số lần</th>';
                html += '<th rowspan="2" style="background:#fff7ed;color:#16a34a;text-align:center">Cộng</th>';
                html += '<th rowspan="2" style="background:#fff7ed;color:#e11d48;text-align:center">Trừ</th>';
                html += '<th rowspan="2" style="background:#fff7ed;text-align:center">Tổng</th>';
                html += '</tr>';
                var bgN = ['#eef2ff', '#eef2ff', '#eef2ff', '#f0fdf4', '#f0fdf4', '#fff5f5', '#fff5f5', '#fdf4ff'];
                for (var _j = 0; _j < 8; _j++) {
                    html += '<th style="background:' + bgN[_j] + ';color:#4f46e5;text-align:center;font-size:9px">Thực tế</th>';
                    html += '<th style="background:' + bgN[_j] + ';color:#94a3b8;text-align:center;font-size:9px">Chỉ tiêu</th>';
                }
                html += '</tr></thead><tbody>';

                rows.forEach(function(r, i) {
                    var DiemCong = parseInt(r.GiaoHangDungHanTT) || 0;
                    var DiemTru = 0;
                    var sc = parseInt(r.SoLanComplainTT) || 0;
                    if (sc == 1) DiemTru = 3;
                    else if (sc == 2) DiemTru = 5;
                    else if (sc > 2) DiemTru = 10;
                    var Tong = DiemCong - DiemTru;

                    html += '<tr>';
                    html += '<td class="text-center ncc-nowrap font-bold text-slate-400">' + (i + 1) + '</td>';
                    html += '<td class="ncc-nowrap"><span class="ncc-code">' + (r.code_supplier || '') + '</span></td>';
                    html += '<td class="ncc-long-text font-medium text-slate-800">' + (r.company || '') + '</td>';
                    html += '<td class="ncc-nowrap text-xs text-slate-500">' + (r.list_name_group || '') + '</td>';
                    var f0 = function(v) { var n = parseFloat(v); return (isNaN(n) || n === 0) ? '-' : n; };
                    
                    // Báo giá
                    html += '<td class="text-center ncc-actual" style="background:#f5f7ff">-</td>';
                    html += '<td class="text-center ncc-target" style="background:#f5f7ff">' + f0(r.SoBaoGiaNhan) + '</td>';
                    html += '<td class="text-center ncc-actual" style="background:#f5f7ff">-</td>';
                    html += '<td class="text-center ncc-target" style="background:#f5f7ff">' + f0(r.BaoGiaDaDuyet) + '</td>';
                    html += '<td class="text-center ncc-actual" style="background:#f5f7ff">-</td>';
                    html += '<td class="text-center ncc-target" style="background:#f5f7ff">' + f0(r.BaoGiaChuaDuyet) + '</td>';
                    // Đơn hàng
                    html += '<td class="text-center ncc-actual" style="background:#f9fefb">' + f0(r.SoDonHangTT) + '</td>';
                    html += '<td class="text-center ncc-target" style="background:#f9fefb">' + f0(r.SoDonHang) + '</td>';
                    html += '<td class="text-center ncc-actual" style="background:#f9fefb">' + f0(r.GiaoHangDungHanTT) + '</td>';
                    html += '<td class="text-center ncc-target" style="background:#f9fefb">' + f0(r.GiaoHangDungHan) + '</td>';
                    html += '<td class="text-center ncc-actual" style="background:#fff5f5">' + f0(r.GiaoHangTreTT) + '</td>';
                    html += '<td class="text-center ncc-target" style="background:#fff5f5">' + f0(r.GiaoHangTre) + '</td>';
                    html += '<td class="text-center ncc-actual" style="background:#fff5f5">' + f0(r.SoLanLoiChatLuongTT) + '</td>';
                    html += '<td class="text-center ncc-target" style="background:#fff5f5">' + f0(r.SoLanLoiChatLuong) + '</td>';
                    // Complain
                    html += '<td class="text-center ncc-actual" style="background:#fdf4ff">' + f0(r.SoLanComplainTT) + '</td>';
                    html += '<td class="text-center ncc-target" style="background:#fdf4ff">' + f0(r.SoLanComplain) + '</td>';
                    // Điểm
                    html += '<td class="text-center ncc-nowrap text-emerald-600 font-bold" style="background:#f0fdf4">' + (DiemCong == 0 ? '-' : '+' + DiemCong) + '</td>';
                    html += '<td class="text-center ncc-nowrap text-rose-600 font-bold" style="background:#fff1f2">' + (DiemTru == 0 ? '-' : '-' + DiemTru) + '</td>';
                    html += '<td class="text-center ncc-nowrap font-black text-base text-slate-800">' + (Tong == 0 ? '-' : Tong) + '</td>';
                    html += '<td class="text-center ncc-nowrap">' + nccStatusBadge(Tong) + '</td>';
                    html += '<td class="text-center ncc-nowrap">' + getSupplierProposal(Tong) + '</td>';
                    html += '<td class="text-center ncc-nowrap"><button onclick="deleteNcc(' + r.id + ')" class="text-rose-500 hover:text-rose-700 p-1"><i data-lucide="trash-2" class="w-4 h-4"></i></button></td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                inject.innerHTML = html;
                inject.classList.remove('hidden');
                document.getElementById('ncc-count-text').textContent = rows.length + ' nhà cung cấp';
                lucide.createIcons();
                filterNccTable();
            },
            error: function() {
                loading.classList.add('hidden');
                holder.classList.remove('hidden');
                holder.innerHTML = '<div class="py-16 text-center text-red-300 text-sm">Lỗi kết nối!</div>';
            }
        });
    }

    function filterNccTable() {
        var q = document.getElementById('ncc-table-search').value.toLowerCase();
        var rows = document.querySelectorAll('#ncc-table-inject tbody tr');
        var visible = 0;
        rows.forEach(function(r) {
            var match = !q || r.textContent.toLowerCase().indexOf(q) >= 0;
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (q) document.getElementById('ncc-count-text').textContent = visible + ' / ' + rows.length + ' nhà cung cấp';
    }

    function exportNccExcel() {
        var year = $('#ncc-year-select').val();
        $.ajax({
            type: 'POST',
            url: '<?= site_url('admin/kpi_targets_supplier/export_excel') ?>',
            data: {
                year_search: year,
                export_excel: 1,
                [NCC_CSRF]: NCC_HASH
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

    function deleteNcc(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa mục tiêu KPI NCC này?')) return;
        $.ajax({
            type: 'POST',
            url: NCC_BASE + 'delete_kpi_entry/import_ncc/' + id,
            data: {
                [NCC_CSRF]: NCC_HASH
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert_float('success', res.message);
                    loadNccTable();
                } else {
                    alert_float('danger', res.message);
                }
            }
        });
    }
</script>