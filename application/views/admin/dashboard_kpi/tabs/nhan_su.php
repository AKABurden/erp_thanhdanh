<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Danh sách nhân sự</h2>
        <p class="text-sm text-slate-500">Nhân sự đang hoạt động trong hệ thống KPI</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <i data-lucide="users" class="w-4 h-4"></i>
        <span class="font-semibold text-slate-900"><?= count($nhan_su_list) ?></span> nhân sự
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4 flex flex-wrap gap-3">
    <div class="relative flex-1 min-w-[200px]">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="ns-search" placeholder="Tìm tên, email, phòng ban..." oninput="filterNs()"
            class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <select id="ns-dept" onchange="filterNs()" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Tất cả phòng ban</option>
        <?php
        $depts = array_unique(array_filter(array_column($nhan_su_list, 'ten_phong_ban')));
        sort($depts);
        foreach ($depts as $d): ?>
        <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
        <?php endforeach; ?>
    </select>
    <select id="ns-status" onchange="filterNs()" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Tất cả trạng thái</option>
        <option value="1">Đang làm</option>
        <option value="0">Nghỉ việc</option>
    </select>
</div>

<!-- Grid cards -->
<div id="ns-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <?php foreach ($nhan_su_list as $ns): ?>
    <?php
    $active = (int)($ns['active']??1);
    $initials = '';
    $parts = explode(' ', trim($ns['ho_ten']??'NS'));
    foreach ($parts as $p) $initials .= strtoupper(mb_substr($p,0,1));
    $initials = mb_substr($initials, -2);
    $colors = ['bg-blue-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-cyan-500','bg-indigo-500'];
    $colorClass = $colors[crc32($ns['ho_ten']??'') % count($colors)];
    ?>
    <div class="ns-card bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow"
         data-search="<?= strtolower(($ns['ho_ten']??'').' '.($ns['email']??'').' '.($ns['ten_phong_ban']??'')) ?>"
         data-dept="<?= htmlspecialchars($ns['ten_phong_ban']??'') ?>"
         data-active="<?= $active ?>">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full <?= $colorClass ?> flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                <?= $initials ?>
            </div>
            <div class="min-w-0">
                <div class="font-semibold text-slate-900 text-sm truncate"><?= htmlspecialchars($ns['ho_ten']??'-') ?></div>
                <div class="text-xs text-slate-400 truncate"><?= htmlspecialchars($ns['email']??'') ?></div>
            </div>
        </div>
        <div class="space-y-1.5 text-xs">
            <div class="flex items-center gap-1.5 text-slate-500">
                <i data-lucide="building-2" class="w-3.5 h-3.5 flex-shrink-0"></i>
                <span class="truncate"><?= htmlspecialchars($ns['ten_phong_ban']??'Chưa phân phòng') ?></span>
            </div>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between">
            <span class="px-2 py-0.5 rounded text-xs font-medium <?= $active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                <?= $active ? 'Đang làm' : 'Nghỉ việc' ?>
            </span>
            <a href="<?= site_url('admin/dashboardKpi/index/phieu_danh_gia') ?>"
               class="text-xs text-blue-600 hover:underline">Xem phiếu</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($nhan_su_list)): ?>
    <div class="col-span-4 text-center py-16 text-slate-400">
        <i data-lucide="users-x" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
        <div>Không có nhân sự nào</div>
    </div>
    <?php endif; ?>
</div>

<script>
function filterNs() {
    var q    = document.getElementById('ns-search').value.toLowerCase();
    var dept = document.getElementById('ns-dept').value;
    var act  = document.getElementById('ns-status').value;
    document.querySelectorAll('.ns-card').forEach(function(c) {
        var mQ = !q    || c.dataset.search.includes(q);
        var mD = !dept || c.dataset.dept === dept;
        var mA = !act  || c.dataset.active === act;
        c.style.display = (mQ && mD && mA) ? '' : 'none';
    });
}
</script>
