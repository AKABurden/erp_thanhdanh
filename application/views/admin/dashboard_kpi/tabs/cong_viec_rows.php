<?php
$task_statuses = [
    1 => ['name' => 'Chưa bắt đầu', 'color' => '#989898'],
    2 => ['name' => 'Đang thực hiện', 'color' => '#2196F3'],
    3 => ['name' => 'Chờ xác nhận', 'color' => '#ff9800'],
    4 => ['name' => 'Hoàn thành', 'color' => '#84c529'],
    5 => ['name' => 'Đã hoàn tất', 'color' => '#4caf50'],
];

if (!empty($tasks)): foreach ($tasks as $t):
    $st = isset($task_statuses[$t['status']]) ? $task_statuses[$t['status']] : ['name'=>'N/A','color'=>'#999'];
    $hasChecklist = !empty($t['checklist']);
?>
<!-- Task Row -->
<tr class="task-row cv-row cursor-pointer" onclick="toggleTaskDetail(<?= $t['id'] ?>)">
    <td class="text-center">
        <?php if ($hasChecklist): ?>
        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 transition-transform duration-200" id="arrow-<?= $t['id'] ?>"></i>
        <?php endif; ?>
    </td>
    <td>
        <a href="<?= admin_url('tasks/view/' . $t['id']) ?>" onclick="event.stopPropagation(); init_task_modal(<?= $t['id'] ?>); return false;"
           class="font-mono text-blue-600 hover:text-blue-800 font-medium">#<?= $t['id'] ?></a>
    </td>
    <td class="text-xs text-slate-500"><?= htmlspecialchars($t['task_code'] ?? '') ?></td>
    <td>
        <div class="max-w-xs">
            <div class="font-medium text-slate-800 truncate text-xs"><?= htmlspecialchars($t['task_name']) ?></div>
            <?php if (!empty($t['task_category_name'])): ?>
            <div class="text-[10px] text-slate-400 truncate"><?= htmlspecialchars($t['task_category_name']) ?></div>
            <?php endif; ?>
        </div>
    </td>
    <td class="text-xs"><?= htmlspecialchars($t['assignees'] ?? '-') ?></td>
    <td class="font-mono text-xs text-slate-600"><?= !empty($t['startdate']) ? date('d/m/Y', strtotime($t['startdate'])) : '-' ?></td>
    <td class="font-mono text-xs <?= $t['is_overdue'] ? 'text-red-600 font-semibold' : 'text-slate-600' ?>">
        <?= !empty($t['duedate']) ? date('d/m/Y', strtotime($t['duedate'])) : '-' ?>
        <?php if ($t['is_overdue']): ?><i data-lucide="alert-circle" class="w-3 h-3 inline text-red-500 ml-0.5"></i><?php endif; ?>
    </td>
    <td>
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium"
              style="color:<?= $st['color'] ?>;border:1px solid <?= $st['color'] ?>;background:<?= $st['color'] ?>12">
            <?= $st['name'] ?>
        </span>
    </td>
    <td>
        <?php if ($hasChecklist): ?>
        <span class="text-xs text-slate-600"><?= $t['done_steps'] ?>/<?= $t['total_steps'] ?> bước</span>
        <?php else: ?>
        <span class="text-[10px] text-slate-400">Chưa có</span>
        <?php endif; ?>
    </td>
    <td class="text-center" style="min-width:80px">
        <?php if ($t['total_steps'] > 0): ?>
        <div class="flex items-center gap-1.5">
            <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500
                    <?= $t['process_rate'] >= 100 ? 'bg-emerald-500' : ($t['process_rate'] >= 50 ? 'bg-blue-500' : 'bg-amber-500') ?>"
                    style="width:<?= $t['process_rate'] ?>%"></div>
            </div>
            <span class="text-[10px] font-mono font-bold <?= $t['process_rate'] >= 100 ? 'text-emerald-600' : 'text-slate-500' ?>"><?= $t['process_rate'] ?>%</span>
        </div>
        <?php endif; ?>
    </td>
</tr>
<!-- Detail Row: Process Steps -->
<?php if ($hasChecklist): ?>
<tr class="task-detail-row" id="detail-<?= $t['id'] ?>">
    <td colspan="10" class="bg-gradient-to-b from-slate-50 to-white p-0">
        <div class="px-6 py-5">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="git-branch" class="w-4 h-4 text-violet-500"></i>
                <span class="text-xs font-semibold text-slate-700">Quy trình xử lý — <?= $t['total_steps'] ?> bước</span>
                <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full font-medium
                    <?= $t['process_rate'] >= 100 ? 'bg-emerald-100 text-emerald-700' : ($t['process_rate'] >= 50 ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') ?>">
                    <?= $t['process_rate'] ?>% hoàn thành
                </span>
            </div>

            <!-- Progressbar -->
            <div class="overflow-x-auto pb-2">
                <ul class="progressbar-kpi" style="min-width:<?= max(400, count($t['checklist']) * 120) ?>px">
                    <?php
                    $found_current = false;
                    foreach ($t['checklist'] as $ci):
                        $is_done = !empty($ci['finished']);
                        $is_current = false;
                        if (!$is_done && !$found_current) {
                            $is_current = true;
                            $found_current = true;
                        }
                        $liClass = $is_done ? 'active' : ($is_current ? 'current' : '');
                    ?>
                    <li class="<?= $liClass ?>">
                        <div class="max-w-[110px] mx-auto">
                            <div class="text-[10px] font-medium truncate <?= $is_done ? 'text-emerald-700' : ($is_current ? 'text-blue-700' : 'text-slate-400') ?>"
                                 title="<?= htmlspecialchars($ci['description'] ?? '') ?>">
                                <?= htmlspecialchars($ci['description'] ?? 'Bước') ?>
                            </div>
                            <?php if ($is_done && !empty($ci['finished_from'])): ?>
                            <div class="text-[9px] text-emerald-600 mt-0.5 truncate" title="<?= get_staff_full_name($ci['finished_from']) ?>">
                                ✓ <?= get_staff_full_name($ci['finished_from']) ?>
                            </div>
                            <?php if (!empty($ci['date_finished'])): ?>
                            <div class="text-[9px] text-slate-400"><?= date('d/m/Y', strtotime($ci['date_finished'])) ?></div>
                            <?php endif; ?>
                            <?php elseif ($is_current): ?>
                            <div class="text-[9px] text-blue-500 mt-0.5">Đang chờ...</div>
                            <?php endif; ?>

                            <?php if (!empty($ci['count_process']) && $ci['count_process'] > 0): ?>
                            <div class="mt-1">
                                <?php
                                $done_criteria = $ci['count_process'] - ($ci['count_not_process'] ?? 0);
                                $criteria_pct = round(($done_criteria / $ci['count_process']) * 100);
                                ?>
                                <div class="w-full bg-slate-200 rounded-full h-1 overflow-hidden">
                                    <div class="h-full rounded-full <?= $criteria_pct >= 100 ? 'bg-emerald-500' : 'bg-blue-400' ?>"
                                         style="width:<?= $criteria_pct ?>%"></div>
                                </div>
                                <div class="text-[8px] text-slate-400 mt-0.5"><?= $done_criteria ?>/<?= $ci['count_process'] ?> tiêu chí</div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Detail table -->
            <div class="mt-4 rounded-lg border border-slate-100 overflow-hidden">
                <table class="text-xs w-full text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-[10px] py-2 px-3">#</th>
                            <th class="text-[10px] py-2 px-3">Quy trình</th>
                            <th class="text-[10px] py-2 px-3">Trạng thái</th>
                            <th class="text-[10px] py-2 px-3">Người duyệt</th>
                            <th class="text-[10px] py-2 px-3">Ngày hoàn thành</th>
                            <th class="text-[10px] py-2 px-3">Tiêu chí</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($t['checklist'] as $idx => $ci):
                            $is_done = !empty($ci['finished']);
                        ?>
                        <tr class="border-b border-slate-100 last:border-0">
                            <td class="text-slate-400 font-mono py-2 px-3"><?= $idx + 1 ?></td>
                            <td class="font-medium text-slate-700 py-2 px-3"><?= htmlspecialchars($ci['description'] ?? '') ?></td>
                            <td class="py-2 px-3">
                                <?php if ($is_done): ?>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-medium">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i> Hoàn thành
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-medium">
                                    <i data-lucide="clock" class="w-2.5 h-2.5"></i> Chờ xử lý
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-slate-600 py-2 px-3">
                                <?= $is_done && !empty($ci['finished_from']) ? get_staff_full_name($ci['finished_from']) : '-' ?>
                            </td>
                            <td class="font-mono text-slate-500 py-2 px-3">
                                <?= $is_done && !empty($ci['date_finished']) ? date('d/m/Y H:i', strtotime($ci['date_finished'])) : '-' ?>
                            </td>
                            <td class="py-2 px-3">
                                <?php if (!empty($ci['count_process']) && $ci['count_process'] > 0):
                                    $done_c = $ci['count_process'] - ($ci['count_not_process'] ?? 0);
                                ?>
                                <span class="text-[10px] font-medium <?= $done_c == $ci['count_process'] ? 'text-emerald-600' : 'text-amber-600' ?>">
                                    <?= $done_c ?>/<?= $ci['count_process'] ?>
                                </span>
                                <?php else: ?>
                                <span class="text-slate-300">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </td>
</tr>
<?php endif; ?>
<?php endforeach; else: ?>
<tr><td colspan="10" class="text-center text-slate-400 py-12">
    <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-200 mb-2"></i>
    <div>Chưa có dữ liệu. Hãy thay đổi bộ lọc.</div>
</td></tr>
<?php endif; ?>
