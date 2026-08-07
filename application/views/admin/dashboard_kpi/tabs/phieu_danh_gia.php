<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
            <i data-lucide="file-check-2" class="w-5 h-5 text-violet-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Phiếu đánh giá nhân viên (CT)</h2>
            <p class="text-xs text-slate-500">Danh sách phiếu đánh giá chính thức — Dữ liệu từ Probationary Assessment (type=2)</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="<?= admin_url('DashboardKpi/index/phieu_danh_gia_detail?id=0') ?>" id="btn-create-new"
            class="flex items-center gap-1.5 px-3 py-2 text-xs bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tạo phiếu mới
        </a>
    </div>
</div>

<!-- Bộ lọc Kỳ đánh giá Nổi bật -->
<div class="bg-white rounded-2xl border-2 border-violet-100 shadow-sm p-4 mb-4">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center">
                <i data-lucide="calendar-clock" class="w-4 h-4 text-violet-600"></i>
            </div>
            <span class="text-sm font-bold text-slate-700 uppercase tracking-wide">Kỳ đánh giá</span>
        </div>

        <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

        <div class="flex items-center gap-2">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Năm</span>
            <select id="pdg-year" onchange="onPdgYearMonthChange()" class="px-3 py-2 text-sm font-bold border-2 border-slate-200 rounded-xl focus:outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-200 text-slate-800 bg-slate-50 cursor-pointer min-w-[90px] transition-all">
                <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

        <div class="flex items-center gap-2">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tháng</span>
            <select id="pdg-month" onchange="onPdgYearMonthChange()" class="px-3 py-2 text-sm font-bold border-2 border-slate-200 rounded-xl focus:outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-200 text-slate-800 bg-slate-50 cursor-pointer min-w-[140px] transition-all">
                <option value="">— Cả năm —</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == (int)date('m') ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-4 mt-4">
        <!-- Kỳ đánh giá tabs (dynamic) -->
        <div id="pdg-ky-tabs" class="flex flex-wrap gap-2 items-center w-full">
            <!-- Populated by JS -->
        </div>
    </div>
</div>
<!-- Status Tabs -->
<div id="pdg-status-tabs" class="flex flex-wrap gap-2 mb-4">
    <!-- Rendered by JS -->
</div>
<input type="hidden" id="pdg-status-filter" value="all">

<!-- Filter + Search -->
<div class="bg-white rounded-xl border border-slate-100 p-3 mb-4">
    <!-- Row 1: Search + Room + Count -->
    <div class="flex flex-wrap gap-3 items-center mb-3">
        <div class="relative flex-1 min-w-[250px]">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="pdg-search" placeholder="Tìm mã phiếu, nhân viên, phòng ban..." oninput="filterPdgTable()"
                class="w-full pl-8 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
        </div>
        <select id="pdg-room" onchange="filterPdgTable()" class="px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
            <option value="">Tất cả phòng ban</option>
            <?php foreach (($room_list ?? []) as $rm): ?>
                <option value="<?= htmlspecialchars($rm['name']) ?>"><?= htmlspecialchars($rm['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="text-xs text-slate-400 flex items-center gap-1">
            <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i>
            <span id="pdg-count">0 phiếu</span>
        </div>
    </div>
    <!-- Row 2 deleted -->
</div>
<input type="hidden" id="pdg-ky" value="">

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto" style="max-height: calc(100vh - 380px); overflow-y: auto;">
        <table id="pdg-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã phiếu</th>
                    <th>Nhân viên / Chức vụ</th>
                    <th>Phòng ban</th>
                    <th class="text-center">Loại</th>
                    <th class="text-center" id="pdg-ky-header">Kỳ đánh giá</th>
                    <th class="text-center">Thời gian</th>
                    <th class="text-center">Điểm</th>
                    <th class="text-center">Kết quả KPI</th>
                    <th class="text-center">% P2 Tuân thủ</th>
                    <th class="text-center">Audit</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="pdg-tbody">
                <tr><td colspan="13" style="text-align:center;padding:48px 0;color:#94a3b8">
                    <div class="animate-pulse text-sm font-bold uppercase tracking-wide">Đang tải dữ liệu...</div>
                </td></tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="flex items-center justify-between px-4 py-3 border-t border-slate-100">
        <div class="text-xs text-slate-500" id="pdg-page-info">—</div>
        <div class="flex items-center gap-1" id="pdg-pagination"></div>
    </div>
</div>



<!-- Note modal -->
<div id="pd-note-modal" class="modal-backdrop hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center">
    <div class="modal-box bg-white rounded-2xl shadow-xl w-full" style="max-width:420px">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 id="pd-note-title" class="font-semibold text-slate-900">Ghi chú phê duyệt</h3>
            <button onclick="document.getElementById('pd-note-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="p-5 space-y-4">
            <input type="hidden" id="pd-approval-id">
            <input type="hidden" id="pd-action">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Ghi chú (tuỳ chọn)</label>
                <textarea id="pd-note" rows="3" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button onclick="document.getElementById('pd-note-modal').classList.add('hidden')" class="px-4 py-2 text-sm bg-slate-100 text-slate-600 rounded-lg font-medium hover:bg-slate-200 transition-colors">Hủy</button>
                <button id="pd-confirm-btn" onclick="confirmApprove()" class="px-4 py-2 text-sm text-white rounded-lg font-medium shadow-sm transition-colors">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Audit Create Modal -->
<div id="pd-audit-modal" class="modal-backdrop hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center">
    <div class="modal-box bg-white rounded-2xl shadow-xl w-full" style="max-width:420px">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">Tạo phiếu Audit</h3>
            <button onclick="document.getElementById('pd-audit-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="p-5 space-y-4">
            <input type="hidden" id="pd-audit-assessment-id">
            <input type="hidden" id="pd-audit-room-id">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Trưởng đoàn</label>
                <select id="pd-audit-auditor" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="">-- Chọn trưởng đoàn --</option>
                    <?php
                    $staff_list = $this->db->get('tblstaff')->result_array();
                    foreach (($staff_list ?? []) as $stf): ?>
                        <option value="<?= $stf['staffid'] ?>"><?= htmlspecialchars(trim($stf['firstname'] . ' ' . $stf['lastname'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button onclick="document.getElementById('pd-audit-modal').classList.add('hidden')" class="px-4 py-2 text-sm bg-slate-100 text-slate-600 rounded-lg font-medium hover:bg-slate-200 transition-colors">Hủy</button>
                <button id="pd-audit-confirm-btn" onclick="confirmCreateAudit()" class="px-4 py-2 text-sm text-white rounded-lg bg-violet-600 hover:bg-violet-700 font-medium shadow-sm transition-colors">Tạo phiếu</button>
            </div>
        </div>
    </div>
</div>

<style>
    #pdg-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }

    #pdg-table thead tr {
        background: #f8fafc !important;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #pdg-table th {
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

    #pdg-table td {
        padding: 8px 12px !important;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        color: #334155;
        white-space: nowrap;
        vertical-align: middle;
    }

    #pdg-table td.role-cell {
        white-space: normal !important;
        min-width: 140px;
        max-width: 220px;
        line-height: 1.4;
    }

    #pdg-table tbody tr:hover td {
        background-color: #faf5ff;
    }
</style>

<script>
    var PD_CSRF = '<?= $this->security->get_csrf_token_name() ?>';
    var PD_HASH = '<?= $this->security->get_csrf_hash() ?>';
    var PD_BASE = '<?= site_url('admin/dashboardKpi/') ?>';
    
    function printInIframe(url) {
        var iframe = document.getElementById('print-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-iframe';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url;
    }

    function toggleProcessRow(btn) {
        var tr = $(btn).closest('tr.pdg-row');
        var expandTr = tr.next('.pdg-expand-row');
        var svg = $(btn).find('svg');
        if (expandTr.hasClass('hidden')) { expandTr.removeClass('hidden'); svg.css('transform','rotate(90deg)'); }
        else { expandTr.addClass('hidden'); svg.css('transform','rotate(0deg)'); }
    }

    function actApprove(id, action) {
        document.getElementById('pd-approval-id').value = id;
        document.getElementById('pd-action').value = action;
        document.getElementById('pd-note').value = '';
        var btn = document.getElementById('pd-confirm-btn');
        if (action === 'approved') {
            document.getElementById('pd-note-title').textContent = 'Xác nhận Phê duyệt';
            btn.className = 'px-4 py-2 text-sm text-white rounded-lg bg-emerald-600 hover:bg-emerald-700 font-medium shadow-sm transition-colors';
        } else {
            document.getElementById('pd-note-title').textContent = 'Xác nhận Từ chối';
            btn.className = 'px-4 py-2 text-sm text-white rounded-lg bg-red-600 hover:bg-red-700 font-medium shadow-sm transition-colors';
        }
        document.getElementById('pd-note-modal').classList.remove('hidden');
    }

    function confirmApprove() {
        var fd = new FormData();
        fd.append('approval_id', document.getElementById('pd-approval-id').value);
        fd.append('action', document.getElementById('pd-action').value);
        fd.append('note', document.getElementById('pd-note').value);
        fd.append(PD_CSRF, PD_HASH);
        var btn = document.getElementById('pd-confirm-btn');
        btn.innerHTML = 'Đang xử lý...'; btn.disabled = true;
        fetch(PD_BASE + 'approve_probationary', {method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body:fd})
        .then(function(r){return r.json()}).then(function(res){
            if (res.success) { PDG.load(); document.getElementById('pd-note-modal').classList.add('hidden'); btn.innerHTML='Xác nhận'; btn.disabled=false; }
            else { alert(res.message||'Lỗi!'); btn.innerHTML='Xác nhận'; btn.disabled=false; }
        }).catch(function(){ alert('Lỗi mạng'); btn.innerHTML='Xác nhận'; btn.disabled=false; });
    }

    function openCreateAuditModal(aid, rid) {
        if (!rid) { alert('Không tìm thấy phòng ban.'); return; }
        document.getElementById('pd-audit-assessment-id').value = aid;
        document.getElementById('pd-audit-room-id').value = rid;
        document.getElementById('pd-audit-auditor').value = '';
        document.getElementById('pd-audit-modal').classList.remove('hidden');
    }

    function confirmCreateAudit() {
        var assessment_id = document.getElementById('pd-audit-assessment-id').value;
        var room_id = document.getElementById('pd-audit-room-id').value;
        var auditor_id = document.getElementById('pd-audit-auditor').value;
        if (!auditor_id) { alert('Vui lòng chọn trưởng đoàn!'); return; }
        var btn = document.getElementById('pd-audit-confirm-btn');
        btn.innerHTML = 'Đang tạo...'; btn.disabled = true;
        var fd = new FormData();
        fd.append('dept_id', room_id); fd.append('auditor_id', auditor_id); fd.append(PD_CSRF, PD_HASH);
        fetch('<?= admin_url('audit_management/create') ?>', {method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>r.json()).then(res=>{
            if (res.result===1) {
                var linkFd = new FormData();
                linkFd.append('assessment_id', assessment_id); linkFd.append('audit_id', res.audit_id); linkFd.append(PD_CSRF, PD_HASH);
                fetch(PD_BASE+'link_audit_to_assessment',{method:'POST',body:linkFd,headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(r2=>r2.json()).then(()=>{ document.getElementById('pd-audit-modal').classList.add('hidden'); window.open('<?= admin_url('audit_management?open_audit=') ?>'+res.audit_id,'_blank'); PDG.load(); });
            } else { alert(res.message||'Lỗi tạo audit'); btn.innerHTML='Tạo phiếu'; btn.disabled=false; }
        }).catch(()=>{ alert('Lỗi kết nối!'); btn.innerHTML='Tạo phiếu'; btn.disabled=false; });
    }

    document.addEventListener('DOMContentLoaded', function() { onPdgYearMonthChange(); });
</script>
<script src="<?= base_url('assets/js/pdg_ajax.js?v=') . time() ?>"></script>