<?php
// views/admin/kpi_danh_gia/tabs/nhan_su.php
?>
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Quản lý Nhân sự</h2>
        <p class="text-sm text-slate-500">Danh sách nhân sự trong hệ thống KPI</p>
    </div>
    <button onclick="openNhanSuForm()"
        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Thêm nhân sự
    </button>
</div>

<!-- Search -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="relative">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="ns-search" placeholder="Tìm theo tên, mã nhân viên, chức vụ..."
            oninput="filterNhanSu()"
            class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

<!-- Grid cards -->
<div id="ns-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <?php if (!empty($nhan_su_list)): foreach ($nhan_su_list as $ns): ?>
    <div class="ns-card bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow"
         data-name="<?= strtolower($ns['ho_ten']) ?>"
         data-manv="<?= strtolower($ns['ma_nhan_vien']) ?>"
         data-chucvu="<?= strtolower($ns['chuc_vu']) ?>">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <div class="font-semibold text-slate-900"><?= htmlspecialchars($ns['ho_ten']) ?></div>
                    <div class="text-xs text-slate-500"><?= htmlspecialchars($ns['ma_nhan_vien']) ?></div>
                </div>
            </div>
            <div class="flex gap-1">
                <button onclick='openNhanSuForm(<?= json_encode($ns) ?>)'
                    class="p-1.5 text-slate-400 hover:text-blue-600 rounded transition-colors">
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <button onclick="deleteNhanSu(<?= $ns['id'] ?>, '<?= htmlspecialchars($ns['ho_ten']) ?>')"
                    class="p-1.5 text-slate-400 hover:text-red-600 rounded transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="mt-3 space-y-1.5 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Chức vụ:</span><span class="text-slate-800 font-medium"><?= htmlspecialchars($ns['chuc_vu']) ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Phòng ban:</span><span class="text-slate-800"><?= htmlspecialchars($ns['ten_phong_ban']) ?></span></div>
            <div class="flex justify-between"><span class="text-slate-500">Mã vị trí:</span><span class="text-slate-700 font-mono text-xs"><?= htmlspecialchars($ns['ma_vi_tri']) ?></span></div>
            <div class="flex justify-between items-center">
                <span class="text-slate-500">Trạng thái:</span>
                <span class="px-2 py-0.5 rounded text-xs font-medium <?=
                    $ns['trang_thai'] === 'active'    ? 'bg-green-100 text-green-700' :
                   ($ns['trang_thai'] === 'probation' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600')
                ?>">
                    <?= $ns['trang_thai'] === 'active' ? 'Đang làm việc' : ($ns['trang_thai'] === 'probation' ? 'Thử việc' : 'Nghỉ việc') ?>
                </span>
            </div>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div class="col-span-full bg-white rounded-xl border border-slate-100 p-12 text-center text-slate-400">
        Chưa có dữ liệu nhân sự
    </div>
    <?php endif; ?>
</div>

<!-- MODAL Form -->
<div id="ns-modal" class="modal-backdrop hidden">
    <div class="modal-box">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 id="ns-modal-title" class="font-semibold text-slate-900">Thêm nhân sự mới</h3>
            <button onclick="closeNhanSuModal()" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="ns-form" onsubmit="submitNhanSu(event)" class="p-5 space-y-4">
            <input type="hidden" id="ns-id" name="id" value="0">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mã nhân viên <span class="text-red-500">*</span></label>
                    <input type="text" name="ma_nhan_vien" id="ns-ma_nhan_vien" required
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ho_ten" id="ns-ho_ten" required
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mã phòng ban</label>
                    <input type="text" name="ma_phong_ban" id="ns-ma_phong_ban"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tên phòng ban</label>
                    <input type="text" name="ten_phong_ban" id="ns-ten_phong_ban"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mã vị trí</label>
                    <input type="text" name="ma_vi_tri" id="ns-ma_vi_tri"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Chức vụ</label>
                    <input type="text" name="chuc_vu" id="ns-chuc_vu"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ngày bắt đầu làm</label>
                    <input type="date" name="ngay_bat_dau_lam" id="ns-ngay_bat_dau_lam"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ngày kết thúc thử việc</label>
                    <input type="date" name="ngay_ket_thuc_thu_viec" id="ns-ngay_ket_thuc_thu_viec"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Trạng thái</label>
                    <select name="trang_thai" id="ns-trang_thai"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active">Đang làm việc</option>
                        <option value="probation">Thử việc</option>
                        <option value="inactive">Nghỉ việc</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeNhanSuModal()"
                    class="px-4 py-2 text-sm text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Hủy</button>
                <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterNhanSu() {
    const q = document.getElementById('ns-search').value.toLowerCase();
    document.querySelectorAll('.ns-card').forEach(card => {
        const match = card.dataset.name.includes(q) || card.dataset.manv.includes(q) || card.dataset.chucvu.includes(q);
        card.style.display = match ? '' : 'none';
    });
}

function openNhanSuForm(data = null) {
    const fields = ['id', 'ma_nhan_vien', 'ho_ten', 'ma_phong_ban', 'ten_phong_ban', 'ma_vi_tri', 'chuc_vu', 'ngay_bat_dau_lam', 'ngay_ket_thuc_thu_viec', 'trang_thai'];
    fields.forEach(f => { const el = document.getElementById('ns-' + f); if (el) el.value = data ? (data[f] || '') : (f === 'id' ? '0' : ''); });
    document.getElementById('ns-trang_thai').value = data ? (data.trang_thai || 'active') : 'active';
    document.getElementById('ns-modal-title').textContent = data ? 'Cập nhật nhân sự' : 'Thêm nhân sự mới';
    document.getElementById('ns-modal').classList.remove('hidden');
    lucide.createIcons();
}

function closeNhanSuModal() { document.getElementById('ns-modal').classList.add('hidden'); }

function submitNhanSu(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('ns-form'));
    fetch('<?= site_url('admin/KpiDanhGia/save_nhan_su') ?>', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.success) { location.reload(); }
            else { alert('Lỗi: ' + res.message); }
        });
}

function deleteNhanSu(id, name) {
    if (!confirm('Xoá nhân sự "' + name + '"?')) return;
    const fd = new FormData(); fd.append('id', id);
    fetch('<?= site_url('admin/KpiDanhGia/delete_nhan_su') ?>', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => { if (res.success) location.reload(); else alert(res.message); });
}
</script>
