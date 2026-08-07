<?php defined('BASEPATH') or exit('No direct script access allowed');
$ratingMap = ['excellent'=>['label'=>'Xuất sắc','cls'=>'bg-emerald-100 text-emerald-700','dot'=>'bg-emerald-500'],'good'=>['label'=>'Tốt','cls'=>'bg-blue-100 text-blue-700','dot'=>'bg-blue-500'],'passed'=>['label'=>'Đạt','cls'=>'bg-sky-100 text-sky-700','dot'=>'bg-sky-500'],'need_monitoring'=>['label'=>'Giám sát','cls'=>'bg-amber-100 text-amber-700','dot'=>'bg-amber-500'],'failed'=>['label'=>'Không đạt','cls'=>'bg-red-100 text-red-700','dot'=>'bg-red-500']];
$sid = isset($selected_staff) && $selected_staff ? $selected_staff['id'] : '';
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Hồ sơ đánh giá KPI</h2>
        <p class="text-sm text-slate-500">Xem toàn bộ lịch sử KPI của một nhân sự</p>
    </div>
    <?php if ($selected_staff): ?>
    <a href="<?= site_url('admin/dashboardKpi/index/phieu_danh_gia') ?>"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Tạo phiếu mới
    </a>
    <?php endif; ?>
</div>

<!-- Staff picker -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-5">
    <form method="GET" action="<?= site_url('admin/dashboardKpi/index/danh_gia') ?>" class="flex gap-3 flex-wrap items-end" id="dg-picker-form">
        <div class="flex-1 min-w-[260px]">
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Tìm nhân sự</label>
            <select name="staff_id" id="dg-picker" style="width:100%">
                <?php if ($selected_staff): ?>
                <option value="<?= $selected_staff['id'] ?>" selected><?= htmlspecialchars($selected_staff['ho_ten']) ?></option>
                <?php else: ?>
                <option value=""></option>
                <?php endif; ?>
            </select>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm rounded-lg hover:bg-slate-700 transition-colors flex items-center gap-2">
            <i data-lucide="user-search" class="w-4 h-4"></i> Xem hồ sơ
        </button>
    </form>
</div>
<script>
$(function() {
    $('#dg-picker').select2({
        placeholder: 'Gõ tên hoặc email nhân sự...',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: '<?= site_url('admin/dashboardKpi/ajax_search_staff') ?>',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function(params) { return { q: params.term || '' }; },
            processResults: function(data) { return { results: data.results }; },
            cache: true
        },
        templateResult: function(d) {
            if (!d.id) return d.text;
            return $('<div class="flex flex-col py-0.5"><span class="font-medium text-slate-900">' + d.text + '</span>' + (d.email ? '<span class="text-xs text-slate-400">' + d.email + '</span>' : '') + '</div>');
        },
        templateSelection: function(d) { return d.text || d.id; }
    });
    // Auto-submit khi chọn xong
    $('#dg-picker').on('select2:select', function() {
        document.getElementById('dg-picker-form').submit();
    });
});
</script>

<?php if (!$selected_staff): ?>
<!-- Placeholder -->
<div class="bg-white rounded-xl border border-slate-100 py-20 text-center text-slate-400">
    <i data-lucide="user-search" class="w-14 h-14 mx-auto mb-3 opacity-20"></i>
    <div class="text-base font-medium">Chọn nhân sự để xem hồ sơ KPI</div>
    <div class="text-sm mt-1">Toàn bộ lịch sử đánh giá, vi phạm và đóng góp sẽ hiện ở đây</div>
</div>

<?php else: ?>

<!-- Profile header -->
<?php
$initials = '';
foreach (explode(' ', trim($selected_staff['ho_ten'])) as $p) $initials .= strtoupper(mb_substr($p,0,1));
$initials = mb_substr($initials,-2);
$totalForms   = count($staff_forms);
$avgScore     = $totalForms > 0 ? round(array_sum(array_column($staff_forms,'total_score'))/$totalForms,1) : 0;
$totalViol    = count($staff_violations);
$totalContrib = count($staff_contribs);
?>
<div class="bg-gradient-to-r from-slate-900 to-slate-700 rounded-xl p-6 mb-5 flex flex-wrap gap-5 items-center">
    <div class="w-16 h-16 rounded-2xl bg-blue-500 flex items-center justify-center text-white text-xl font-black flex-shrink-0">
        <?= $initials ?>
    </div>
    <div class="flex-1 min-w-0">
        <div class="text-white text-xl font-bold"><?= htmlspecialchars($selected_staff['ho_ten']) ?></div>
        <div class="text-slate-400 text-sm mt-0.5"><?= htmlspecialchars($selected_staff['email']??'') ?> • <?= htmlspecialchars($selected_staff['ten_phong_ban']??'—') ?></div>
    </div>
    <div class="flex gap-4 flex-wrap">
        <div class="text-center"><div class="text-2xl font-black text-white"><?= $totalForms ?></div><div class="text-xs text-slate-400">Phiếu KPI</div></div>
        <div class="text-center"><div class="text-2xl font-black text-blue-400"><?= $avgScore ?></div><div class="text-xs text-slate-400">Điểm TB</div></div>
        <div class="text-center"><div class="text-2xl font-black <?= $totalViol>0?'text-red-400':'text-slate-300' ?>"><?= $totalViol ?></div><div class="text-xs text-slate-400">Vi phạm</div></div>
        <div class="text-center"><div class="text-2xl font-black text-emerald-400"><?= $totalContrib ?></div><div class="text-xs text-slate-400">Đóng góp</div></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Lịch sử phiếu KPI -->
    <div class="lg:col-span-2 space-y-3">
        <div class="font-semibold text-slate-800 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600"></i> Lịch sử phiếu KPI
        </div>

        <?php if (empty($staff_forms)): ?>
        <div class="bg-white rounded-xl border border-slate-100 py-10 text-center text-slate-400">
            <i data-lucide="file-x" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
            <div class="text-sm">Chưa có phiếu đánh giá nào</div>
        </div>
        <?php else: foreach ($staff_forms as $i => $f):
            $rt = $ratingMap[$f['final_rating']??'failed'] ?? $ratingMap['failed'];
            $stcls = ['draft'=>'text-slate-400','waiting_approval'=>'text-amber-500','approved'=>'text-emerald-500','rejected'=>'text-red-500'];
            $stlbl = ['draft'=>'Nháp','waiting_approval'=>'Chờ duyệt','approved'=>'Đã duyệt','rejected'=>'Từ chối'];
            $st = $f['status']??'draft';
        ?>
        <div class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-sm transition-shadow">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <div class="font-semibold text-slate-900 text-sm"><?= htmlspecialchars($f['period_name']??'—') ?></div>
                    <div class="text-xs text-slate-400 mt-0.5"><?= $f['period_type']??'' ?> • <?= $f['evaluation_type']??'' ?></div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="px-2 py-0.5 rounded text-xs font-medium <?= $rt['cls'] ?>"><?= $rt['label'] ?></span>
                    <span class="text-xs <?= $stcls[$st]??'text-slate-400' ?>"><?= $stlbl[$st]??$st ?></span>
                </div>
            </div>
            <!-- Score bars -->
            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                <div class="bg-slate-50 rounded-lg p-2">
                    <div class="text-slate-400 mb-0.5">Gate 1</div>
                    <div class="font-bold <?= ($f['gate1_result']??'fail')==='pass'?'text-emerald-600':'text-red-500' ?>"><?= strtoupper($f['gate1_result']??'N/A') ?></div>
                </div>
                <div class="bg-blue-50 rounded-lg p-2">
                    <div class="text-slate-400 mb-0.5">P2</div>
                    <div class="font-bold text-blue-700"><?= number_format($f['p2_performance']??0,1) ?></div>
                </div>
                <div class="bg-amber-50 rounded-lg p-2">
                    <div class="text-slate-400 mb-0.5">Gate 3</div>
                    <div class="font-bold text-amber-700"><?= number_format($f['gate3_compliance']??0,1) ?></div>
                </div>
                <div class="bg-slate-900 rounded-lg p-2">
                    <div class="text-slate-400 mb-0.5">Tổng</div>
                    <div class="font-black text-white"><?= number_format($f['total_score']??0,1) ?></div>
                </div>
            </div>
            <!-- Progress bar điểm -->
            <div class="mt-3">
                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full <?= $rt['dot'] ?> transition-all"
                         style="width:<?= min(100,($f['total_score']??0)) ?>%"></div>
                </div>
            </div>
            <div class="mt-2 flex justify-end">
                <a href="<?= site_url('admin/dashboardKpi/index/form_in?id='.$f['id']) ?>"
                   class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                    <i data-lucide="file-text" class="w-3 h-3"></i> Xem phiếu
                </a>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Sidebar: Vi phạm + Đóng góp -->
    <div class="space-y-5">
        <!-- Vi phạm -->
        <div>
            <div class="font-semibold text-slate-800 flex items-center gap-2 mb-3">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i> Vi phạm
            </div>
            <?php if (empty($staff_violations)): ?>
            <div class="bg-white rounded-xl border border-slate-100 py-6 text-center text-slate-300 text-sm">Không có vi phạm</div>
            <?php else: foreach (array_slice($staff_violations,0,5) as $v): ?>
            <div class="bg-white rounded-xl border border-slate-100 px-4 py-3 mb-2">
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <div class="text-sm font-medium text-slate-800"><?= htmlspecialchars($v['violation_name']??$v['violation_code']??'—') ?></div>
                        <div class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($v['period_name']??'') ?></div>
                    </div>
                    <span class="text-xs font-bold text-red-600 flex-shrink-0">-<?= number_format($v['penalty_score']??0,1) ?>đ</span>
                </div>
                <?php if ($v['is_hard_fail']): ?><div class="mt-1"><span class="px-1.5 py-0.5 rounded text-xs font-bold bg-red-600 text-white">HARD FAIL</span></div><?php endif; ?>
            </div>
            <?php endforeach; if (count($staff_violations)>5): ?>
            <div class="text-xs text-slate-400 text-center">... và <?= count($staff_violations)-5 ?> vi phạm khác</div>
            <?php endif; endif; ?>
        </div>

        <!-- Đóng góp -->
        <div>
            <div class="font-semibold text-slate-800 flex items-center gap-2 mb-3">
                <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i> Đóng góp
            </div>
            <?php if (empty($staff_contribs)): ?>
            <div class="bg-white rounded-xl border border-slate-100 py-6 text-center text-slate-300 text-sm">Chưa có đóng góp</div>
            <?php else: foreach (array_slice($staff_contribs,0,5) as $c): ?>
            <div class="bg-white rounded-xl border border-slate-100 px-4 py-3 mb-2">
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <div class="text-sm font-medium text-slate-800"><?= htmlspecialchars($c['title']??'—') ?></div>
                        <div class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($c['period_name']??'') ?></div>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 flex-shrink-0">+<?= number_format($c['bonus_score']??0,1) ?>đ</span>
                </div>
            </div>
            <?php endforeach; if (count($staff_contribs)>5): ?>
            <div class="text-xs text-slate-400 text-center">... và <?= count($staff_contribs)-5 ?> đóng góp khác</div>
            <?php endif; endif; ?>
        </div>
    </div>
</div>

<?php endif; ?>
