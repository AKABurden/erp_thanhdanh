<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Đóng góp & Thưởng</h2>
        <p class="text-sm text-slate-500">Ghi nhận đóng góp để cộng điểm bonus vào P3</p>
    </div>
    <button onclick="openDgForm()"
        class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i> Thêm đóng góp
    </button>
</div>

<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="relative">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="dg2-search" placeholder="Tìm nhân sự, tiêu đề..." oninput="filterDg2()"
            class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table>
            <thead><tr>
                <th>Nhân sự</th><th>Kỳ</th><th>Loại đóng góp</th><th>Tiêu đề</th>
                <th class="text-right">Điểm thưởng</th><th class="text-center">Trạng thái</th><th class="text-right">Thao tác</th>
            </tr></thead>
            <tbody>
                <?php if (!empty($contributions)): foreach ($contributions as $c): ?>
                <?php $stcls = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'];
                $stlbl = ['pending'=>'Chờ duyệt','approved'=>'Đã duyệt','rejected'=>'Từ chối']; $st = $c['status']??'pending'; ?>
                <tr class="dg2-row" data-search="<?= strtolower(($c['ho_ten']??'').' '.($c['title']??'')) ?>">
                    <td class="font-medium text-slate-900"><?= htmlspecialchars($c['ho_ten']??'-') ?></td>
                    <td class="text-xs text-slate-500"><?= htmlspecialchars($c['period_name']??'-') ?></td>
                    <td><span class="px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700"><?= htmlspecialchars($c['contribution_type']??'') ?></span></td>
                    <td class="max-w-[200px] truncate"><?= htmlspecialchars($c['title']??'') ?></td>
                    <td class="text-right font-bold text-emerald-600">+<?= number_format($c['bonus_score']??0,1) ?></td>
                    <td class="text-center"><span class="px-2 py-0.5 rounded text-xs <?= $stcls[$st]??$stcls['pending'] ?>"><?= $stlbl[$st]??$st ?></span></td>
                    <td class="text-right whitespace-nowrap">
                        <button onclick='openDgForm(<?= json_encode($c) ?>)' class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                        <button onclick="deleteDg2(<?= $c['id'] ?>)" class="p-1.5 text-slate-400 hover:text-red-600 transition-colors"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="7" class="text-center text-slate-400 py-12"><i data-lucide="award" class="w-10 h-10 mx-auto mb-2 opacity-30"></i><div>Chưa có đóng góp nào</div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL -->
<div id="dg2-modal" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width:520px">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900 flex items-center gap-2"><i data-lucide="award" class="w-4 h-4 text-emerald-600"></i> Ghi nhận đóng góp</h3>
            <button onclick="document.getElementById('dg2-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form id="dg2-form" onsubmit="submitDg2(event)" class="p-5 space-y-4">
            <input type="hidden" id="dg2-id" value="0">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nhân sự <span class="text-red-500">*</span></label>
                    <select id="dg2-staff" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Chọn --</option>
                        <?php foreach ($staff_list as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['ho_ten']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kỳ <span class="text-red-500">*</span></label>
                    <select id="dg2-period" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Chọn --</option>
                        <?php foreach ($periods as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Loại đóng góp</label>
                    <select id="dg2-type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="innovation">Sáng kiến</option><option value="leadership">Lãnh đạo</option>
                        <option value="training">Đào tạo</option><option value="project">Dự án</option><option value="other">Khác</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Điểm bonus (0–5)</label>
                    <input type="number" id="dg2-bonus" value="1" min="0" max="5" step="0.5" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" id="dg2-title" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mô tả chi tiết</label>
                <textarea id="dg2-desc" rows="2" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div id="dg2-result"></div>
            <div class="flex justify-end gap-3 pt-1">
                <button type="button" onclick="document.getElementById('dg2-modal').classList.add('hidden')" class="px-5 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg">Hủy</button>
                <button type="submit" id="dg2-btn" class="flex items-center gap-2 px-5 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i data-lucide="save" class="w-4 h-4"></i> Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
var DG2_CSRF = '<?= $this->security->get_csrf_token_name() ?>';
var DG2_HASH = '<?= $this->security->get_csrf_hash() ?>';
var DG2_BASE = '<?= site_url('admin/dashboardKpi/') ?>';

function filterDg2() {
    var q = document.getElementById('dg2-search').value.toLowerCase();
    document.querySelectorAll('.dg2-row').forEach(function(r){ r.style.display = !q||r.dataset.search.includes(q)?'':'none'; });
}
function openDgForm(data) {
    document.getElementById('dg2-id').value     = data ? data.id : '0';
    document.getElementById('dg2-staff').value  = data ? (data.staff_id||'') : '';
    document.getElementById('dg2-period').value = data ? (data.period_id||'') : '';
    document.getElementById('dg2-type').value   = data ? (data.contribution_type||'innovation') : 'innovation';
    document.getElementById('dg2-bonus').value  = data ? (data.bonus_score||1) : 1;
    document.getElementById('dg2-title').value  = data ? (data.title||'') : '';
    document.getElementById('dg2-desc').value   = data ? (data.description||'') : '';
    document.getElementById('dg2-result').innerHTML = '';
    document.getElementById('dg2-modal').classList.remove('hidden');
    lucide.createIcons();
}
function submitDg2(e) {
    e.preventDefault();
    var btn = document.getElementById('dg2-btn'); btn.disabled = true;
    var fd = new FormData();
    fd.append('id',                document.getElementById('dg2-id').value);
    fd.append('staff_id',          document.getElementById('dg2-staff').value);
    fd.append('period_id',         document.getElementById('dg2-period').value);
    fd.append('contribution_type', document.getElementById('dg2-type').value);
    fd.append('bonus_score',       document.getElementById('dg2-bonus').value);
    fd.append('title',             document.getElementById('dg2-title').value);
    fd.append('description',       document.getElementById('dg2-desc').value);
    fd.append(DG2_CSRF, DG2_HASH);
    fetch(DG2_BASE + 'save_contribution', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd })
    .then(function(r){ return r.json(); })
    .then(function(res){ if(res.success) location.reload(); else { document.getElementById('dg2-result').innerHTML='<p class="text-red-500 text-sm">'+(res.message||'Lỗi!')+'</p>'; btn.disabled=false; } });
}
function deleteDg2(id) {
    if (!confirm('Xóa đóng góp này?')) return;
    var fd = new FormData(); fd.append('id', id); fd.append(DG2_CSRF, DG2_HASH);
    fetch(DG2_BASE + 'delete_contribution', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd })
    .then(function(r){ return r.json(); }).then(function(res){ if(res.success) location.reload(); });
}
</script>
