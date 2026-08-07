<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Phê duyệt KPI</h2>
        <p class="text-sm text-slate-500">Xử lý các phiếu đang chờ theo từng cấp phê duyệt</p>
    </div>
</div>

<!-- Bộ lọc Năm / Tháng / Phòng ban + Kỳ tabs -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4 space-y-3">
    <div class="flex flex-wrap items-center gap-3">
        <!-- Năm -->
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Năm</span>
            <select id="pd-year" onchange="onPdYearMonthChange()" style="padding:8px 14px;font-size:13px;font-weight:700;border:1px solid #e2e8f0;border-radius:10px;min-width:90px;outline:none;color:#1e293b;background:#f8fafc;cursor:pointer;">
                <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <!-- Tháng -->
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Tháng</span>
            <select id="pd-month" onchange="onPdYearMonthChange()" style="padding:8px 14px;font-size:13px;font-weight:700;border:1px solid #e2e8f0;border-radius:10px;min-width:120px;outline:none;color:#1e293b;background:#f8fafc;cursor:pointer;">
                <option value="">— Cả năm —</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == (int)date('m') ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <!-- Phòng ban -->
        <select id="pd-room" onchange="filterPdTable()" class="px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
            <option value="">Tất cả phòng ban</option>
            <?php foreach (($pd_room_list ?? []) as $rm): ?>
                <option value="<?= htmlspecialchars($rm['name']) ?>"><?= htmlspecialchars($rm['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="pd-search" placeholder="Tìm kiếm..." oninput="filterPdTable()" class="px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500" style="min-width:180px;">
    </div>
    <!-- Kỳ tabs -->
    <input type="hidden" id="pd-ky" value="">
    <div id="pd-ky-tabs" class="flex flex-wrap gap-2"></div>
</div>

<!-- Status Tabs -->
<div id="pd-status-tabs" class="flex flex-wrap gap-2 mb-4"></div>
<input type="hidden" id="pd-status-filter" value="all">

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto" style="max-height: calc(100vh - 380px); overflow-y: auto;">
        <table class="w-full text-left border-collapse" id="pd-table" style="width:100%">
            <thead class="bg-slate-50" style="position:sticky;top:0;z-index:2;">
                <tr>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Mã phiếu</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Nhân sự</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">Bộ phận</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border-b border-slate-200">Loại</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border-b border-slate-200">Kỳ đánh giá</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border-b border-slate-200">Ngày BĐ</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border-b border-slate-200">Ngày KT</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right border-b border-slate-200">Tổng điểm</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border-b border-slate-200">Kết quả KPI</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border-b border-slate-200">Trạng thái</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right border-b border-slate-200">Thao tác</th>
                </tr>
            </thead>
            <tbody id="pd-tbody">
                <tr><td colspan="11" style="text-align:center;padding:48px 0;color:#94a3b8">
                    <div class="animate-pulse text-sm font-bold uppercase tracking-wide">Đang tải dữ liệu...</div>
                </td></tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="flex items-center justify-between px-4 py-3 border-t border-slate-100">
        <div class="text-xs text-slate-500" id="pd-page-info">—</div>
        <div class="flex items-center gap-1" id="pd-pagination"></div>
    </div>
    <div class="text-xs text-slate-400 px-4 pb-3 flex items-center gap-1">
        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
        <span id="pd-count">0 phiếu</span>
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
                <button id="pd-confirm-btn" onclick="confirmPdApprove()" class="px-4 py-2 text-sm text-white rounded-lg font-medium shadow-sm transition-colors">Xác nhận</button>
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
                    <?php foreach (($staff_list ?? []) as $stf): ?>
                        <option value="<?= $stf['id'] ?>"><?= htmlspecialchars($stf['ho_ten']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button onclick="document.getElementById('pd-audit-modal').classList.add('hidden')" class="px-4 py-2 text-sm bg-slate-100 text-slate-600 rounded-lg font-medium hover:bg-slate-200 transition-colors">Hủy</button>
                <button id="pd-audit-confirm-btn" onclick="confirmPdCreateAudit()" class="px-4 py-2 text-sm text-white rounded-lg bg-violet-600 hover:bg-violet-700 font-medium shadow-sm transition-colors">Tạo phiếu</button>
            </div>
        </div>
    </div>
</div>

<script>
    var PD_CSRF = '<?= $this->security->get_csrf_token_name() ?>';
    var PD_HASH = '<?= $this->security->get_csrf_hash() ?>';
    var PD_BASE = '<?= site_url('admin/dashboardKpi/') ?>';

    function actPdApprove(id, action) {
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

    function confirmPdApprove() {
        var fd = new FormData();
        fd.append('approval_id', document.getElementById('pd-approval-id').value);
        fd.append('action', document.getElementById('pd-action').value);
        fd.append('note', document.getElementById('pd-note').value);
        fd.append(PD_CSRF, PD_HASH);
        var btn = document.getElementById('pd-confirm-btn');
        btn.innerHTML = 'Đang xử lý...'; btn.disabled = true;
        fetch(PD_BASE + 'approve_probationary', {method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body:fd})
        .then(function(r){return r.json()}).then(function(res){
            if (res.success) { PDA.load(); document.getElementById('pd-note-modal').classList.add('hidden'); btn.innerHTML='Xác nhận'; btn.disabled=false; }
            else { alert(res.message||'Lỗi!'); btn.innerHTML='Xác nhận'; btn.disabled=false; }
        }).catch(function(){ alert('Lỗi mạng'); btn.innerHTML='Xác nhận'; btn.disabled=false; });
    }

    function openPdAuditModal(aid, rid) {
        if (!rid) { alert('Không tìm thấy phòng ban.'); return; }
        document.getElementById('pd-audit-assessment-id').value = aid;
        document.getElementById('pd-audit-room-id').value = rid;
        document.getElementById('pd-audit-auditor').value = '';
        document.getElementById('pd-audit-modal').classList.remove('hidden');
    }

    function confirmPdCreateAudit() {
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
                .then(r2=>r2.json()).then(()=>{ document.getElementById('pd-audit-modal').classList.add('hidden'); window.open('<?= admin_url('audit_management?open_audit=') ?>'+res.audit_id,'_blank'); PDA.load(); });
            } else { alert(res.message||'Lỗi tạo audit'); btn.innerHTML='Tạo phiếu'; btn.disabled=false; }
        }).catch(()=>{ alert('Lỗi kết nối!'); btn.innerHTML='Tạo phiếu'; btn.disabled=false; });
    }

    document.addEventListener('DOMContentLoaded', function() { onPdYearMonthChange(); });
</script>
<script src="<?= base_url('assets/js/pd_approval_ajax.js?v=') . time() ?>"></script>