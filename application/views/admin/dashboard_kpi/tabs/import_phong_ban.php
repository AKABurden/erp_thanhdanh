<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
            <i data-lucide="building-2" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Tiêu chí KPI theo Phòng ban</h2>
            <p class="text-sm text-slate-500">Xem danh sách tiêu chí đánh giá KPI theo từng phòng ban</p>
        </div>
    </div>
    <button onclick="openKpiModal('Import Tiêu chí Phòng ban', 'import_phong_ban')"
        class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors">
        <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
    </button>
</div>

<!-- Filter bar -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Phòng ban</label>
            <select id="pb-dept-select" style="width:100%">
                <?php if (!empty($dtDepartment)): foreach ($dtDepartment as $d): ?>
                        <option value="<?= $d['departmentid'] ?>"><?= htmlspecialchars(($d['code'] ? $d['code'] . ' — ' : '') . $d['name']) ?></option>
                <?php endforeach;
                endif; ?>
            </select>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-400" id="pb-row-count" style="min-width:120px">
            <i data-lucide="table-2" class="w-4 h-4"></i>
            <span id="pb-count-text">Chọn phòng ban</span>
        </div>
    </div>
</div>

<!-- Table container -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <!-- Toolbar -->
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <div class="text-sm font-semibold text-slate-700 flex items-center gap-2">
            <i data-lucide="layout-list" class="w-4 h-4 text-blue-500"></i>
            Danh sách tiêu chí KPI
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="pb-table-search" placeholder="Lọc nhanh..." oninput="filterPbTable()"
                    class="pl-8 pr-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-48">
            </div>
            <div id="pb-loading" class="hidden">
                <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
    </div>

    <!-- The actual data area -->
    <div id="pb-table-area" class="overflow-x-auto" style="max-height: calc(100vh - 340px); overflow-y: auto;">
        <!-- Placeholder -->
        <div id="pb-placeholder" class="py-20 text-center text-slate-300">
            <i data-lucide="building-2" class="w-14 h-14 mx-auto mb-3 opacity-20"></i>
            <div class="text-sm font-medium text-slate-400">Chọn phòng ban để xem tiêu chí KPI</div>
        </div>
        <!-- Injected HTML from old endpoint -->
        <div id="pb-table-inject" class="hidden"></div>
    </div>
</div>

<!-- Custom CSS cho bảng cũ phù hợp theme mới -->
<style>
    #pb-table-inject table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
        font-family: inherit;
    }

    #pb-table-inject table thead tr {
        background: #f8fafc !important;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #pb-table-inject table th {
        padding: 10px 12px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #475569 !important;
        background: #f1f5f9 !important;
        white-space: nowrap;
        border-bottom: 2px solid #e2e8f0 !important;
        border-right: 1px solid #e2e8f0;
    }

    #pb-table-inject table td {
        padding: 8px 12px !important;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #334155;
        vertical-align: middle;
    }

    #pb-table-inject table tbody tr:hover td {
        background-color: #f0f9ff;
    }

    #pb-table-inject table tbody tr:nth-child(even) td {
        background-color: #fafafa;
    }

    #pb-table-inject table tbody tr:nth-child(even):hover td {
        background-color: #f0f9ff;
    }

    /* Ẩn các element không cần từ HTML cũ */
    #pb-table-inject .table-responsive {
        overflow: visible !important;
    }

    /* Custom classes cho cell hiển thị */
    .pb-nowrap {
        white-space: nowrap !important;
    }
    .pb-long-text {
        white-space: normal !important;
        min-width: 150px;
        max-width: 250px;
        word-wrap: break-word;
        line-height: 1.4;
    }
    .pb-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: #2563eb; 
        background-color: #eff6ff;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    .pb-percent {
        color: #059669;
        font-weight: 700;
    }
    .pb-tag-base {
        display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em;
    }
    .pb-tag-p1 {
        background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;
    }
    .pb-tag-p2 {
        background-color: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe;
    }
    .pb-tag-p3 {
        background-color: #fae8ff; color: #86198f; border: 1px solid #f5d0fe;
    }
</style>

<script>
    var PB_BASE = '<?= site_url('admin/DashboardKpi/') ?>';
    var PB_CSRF = '<?= $this->security->get_csrf_token_name() ?>';
    var PB_HASH = '<?= $this->security->get_csrf_hash() ?>';
    var PB_COLS = [{
            key: 'code_department',
            label: 'Mã PB',
            isCode: true
        },
        {
            key: 'name_department',
            label: 'Tên phòng ban'
        },
        {
            key: '_code_parent',
            label: 'Mã mục tiêu',
            isCode: true,
            fn: function(r) {
                return r.code_parent_new || r.code_parent || '';
            }
        },
        {
            key: '_name_parent',
            label: 'Tên mục tiêu',
            longText: true,
            fn: function(r) {
                return r.name_parent_new || r.name_parent || '';
            }
        },
        {
            key: '_kpi_pb',
            label: 'KPI phòng ban',
            longText: true,
            fn: function(r) {
                return (r.name_parent ? r.name_parent + '-' : '') + (r.evaluation_criteria || '');
            }
        },
        {
            key: 'weight',
            label: 'Trọng số',
            suffix: '%',
            align: 'center',
            isPercent: true
        },
        {
            key: 'code_role',
            label: 'Mã vị trí',
            isCode: true
        },
        {
            key: 'name_position',
            label: 'Chức vụ'
        },
        {
            key: 'name_tasks',
            label: 'Mã CV',
            isCode: true
        },
        {
            key: 'name_tasks_process',
            label: 'Công việc',
            longText: true
        },
        {
            key: 'kpi_position',
            label: 'KPI vị trí',
            longText: true
        },
        {
            key: 'weight_kpi_position',
            label: 'TS KPI vị trí',
            suffix: '%',
            align: 'center',
            isPercent: true
        },
        {
            key: 'point_kpi_position',
            label: 'Điểm chuẩn',
            align: 'center',
            isPercent: true
        },
        {
            key: 'check_violate',
            label: 'Cho phép VP'
        },
        {
            key: 'violate',
            label: 'Mã VP',
            isCode: true
        },
        {
            key: 'type_violate',
            label: 'Loại VP'
        },
        {
            key: 'level_violate',
            label: 'Mức độ VP'
        },
        {
            key: 'note_violate',
            label: 'Mô tả VP',
            longText: true
        },
        {
            key: 'total_point',
            label: 'Tổng điểm trừ',
            align: 'right'
        },
        {
            key: 'adjust_browsing',
            label: 'Điều chỉnh duyệt',
            align: 'right'
        },
        {
            key: 'adjust_point',
            label: 'Điểm điều chỉnh',
            align: 'right'
        },
        {
            key: 'point_old',
            label: 'Điểm sau xử lý',
            align: 'right'
        },
        {
            key: 'money_kpi',
            label: 'KPI tiền chuẩn',
            align: 'right',
            money: true
        },
        {
            key: 'ratio_kpi',
            label: 'Tỉ lệ KPI',
            align: 'right'
        },
        {
            key: 'money_real_kpi',
            label: 'KPI thực nhận',
            align: 'right',
            money: true
        },
        {
            key: '_type_p',
            label: 'Loại KPI',
            align: 'center',
            isTagP: true,
            fn: function(r) {
                return r.type_p == 1 ? 'P1' : r.type_p == 2 ? 'P2' : r.type_p == 3 ? 'P3' : '';
            }
        },
        {
            key: 'note',
            label: 'Ghi chú',
            longText: true
        }
    ];

    $(function() {

        $('#pb-dept-select').select2({
            placeholder: 'Gõ tên phòng ban để tìm...',
            allowClear: true
        });
        $('#pb-dept-select').on('change', function() {
            loadPbTable($(this).val() || 0);
        });
        $('#pb-dept-select').change();

    });

    function fmtMoney(v) {
        if (!v || isNaN(v)) return '';
        return Number(v).toLocaleString('vi-VN');
    }

    function loadPbTable(deptId) {
        var loading = document.getElementById('pb-loading');
        var inject = document.getElementById('pb-table-inject');
        var holder = document.getElementById('pb-placeholder');
        loading.classList.remove('hidden');
        inject.classList.add('hidden');
        holder.classList.add('hidden');

        $.ajax({
            type: 'GET',
            url: PB_BASE + 'ajax_criteria_department',
            data: {
                department_id: deptId
            },
            dataType: 'json',
            success: function(res) {
                loading.classList.add('hidden');
                if (!res.success || !res.data || !res.data.length) {
                    holder.classList.remove('hidden');
                    holder.innerHTML = '<div class="py-16 text-center text-slate-300"><i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-30"></i><div class="text-sm">Không có dữ liệu cho phòng ban này</div></div>';
                    document.getElementById('pb-count-text').textContent = '0 tiêu chí';
                    lucide.createIcons();
                    return;
                }
                var rows = res.data;
                var html = '<table><thead><tr>';
                PB_COLS.forEach(function(c) {
                    html += '<th style="text-align:' + (c.align || 'left') + '">' + c.label + '</th>';
                });
                html += '<th class="text-center pb-nowrap">Thao tác</th>';
                html += '</tr></thead><tbody>';
                rows.forEach(function(r) {
                    html += '<tr>';
                    PB_COLS.forEach(function(c) {
                        var val = c.fn ? c.fn(r) : r[c.key];
                        if (val === null || val === undefined) val = '';
                        
                        if (!c.isCode && c.key !== '_type_p') {
                            if (val === 0 || val === '0' || val === '0.00' || val === '0.0') {
                                val = '-';
                            }
                        }
                        
                        var rawVal = val;
                        if (val !== '-') {
                            if (c.money && val) val = fmtMoney(val);
                            if (c.suffix && val) val = val + c.suffix;
                        }
                        
                        var tdClass = c.longText ? "pb-long-text" : "pb-nowrap";
                        
                        if (c.isCode && val && val !== '-') {
                            val = '<span class="pb-code">' + val + '</span>';
                        }
                        if (c.isPercent && rawVal && rawVal !== '-') {
                            val = '<span class="pb-percent">' + val + '</span>';
                        }
                        if (c.isTagP && rawVal && rawVal !== '-') {
                            var tCls = rawVal === 'P1' ? 'pb-tag-p1' : rawVal === 'P2' ? 'pb-tag-p2' : rawVal === 'P3' ? 'pb-tag-p3' : '';
                            val = '<span class="pb-tag-base ' + tCls + '">' + val + '</span>';
                        }
                        
                        html += '<td class="'+tdClass+'" style="text-align:' + (c.align || 'left') + '">' + val + '</td>';
                    });
                    html += '<td class="pb-nowrap" style="text-align:center">';
                    html += '<button onclick="deletePb('+r.id+')" style="padding:4px;color:#e11d48;border:none;background:none;cursor:pointer;border-radius:4px" onmouseover="this.style.background=\'#fff1f2\'" onmouseout="this.style.background=\'none\'">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>' +
                            '</button>';
                    html += '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                inject.innerHTML = html;
                inject.classList.remove('hidden');
                document.getElementById('pb-count-text').textContent = rows.length + ' tiêu chí';
                lucide.createIcons();
                filterPbTable();
            },
            error: function() {
                loading.classList.add('hidden');
                holder.classList.remove('hidden');
                holder.innerHTML = '<div class="py-16 text-center text-red-300 text-sm">Lỗi kết nối!</div>';
            }
        });
    }

    function filterPbTable() {
        var q = document.getElementById('pb-table-search').value.toLowerCase();
        var rows = document.querySelectorAll('#pb-table-inject tbody tr');
        var visible = 0;
        rows.forEach(function(r) {
            var match = !q || r.textContent.toLowerCase().includes(q);
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (q) document.getElementById('pb-count-text').textContent = visible + ' / ' + rows.length + ' tiêu chí';
    }

    function deletePb(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa tiêu chí này?')) return;
        $.ajax({
            type: 'POST',
            url: PB_BASE + 'delete_kpi_entry/import_phong_ban/' + id,
            data: { [PB_CSRF]: PB_HASH },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert_float('success', res.message);
                    loadPbTable($('#pb-dept-select').val());
                } else {
                    alert_float('danger', res.message);
                }
            }
        });
    }
</script>