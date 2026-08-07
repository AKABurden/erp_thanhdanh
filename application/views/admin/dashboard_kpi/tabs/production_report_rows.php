<?php if (!empty($reports)): foreach ($reports as $r):
        $type = isset($type_labels[$r['type_report']]) ? $type_labels[$r['type_report']] : ['name' => 'N/A', 'color' => '#999', 'bg' => '#f1f5f9'];
        $hasProcess = !empty($r['processes']);
?>
        <tr class="pr-row cursor-pointer" onclick="togglePrDetail(<?= $r['id'] ?>)" data-search="<?= strtolower($r['reference_no'] . ' ' . $r['name_report'] . ' ' . ($r['trouble_name'] ?? '') . ' ' . ($r['vg_name'] ?? '')) ?>" data-date="<?= $r['date'] ?? '' ?>" data-type="<?= $r['type_report'] ?? '' ?>" data-process-rate="<?= $r['process_rate'] ?? 0 ?>">
            <td class="text-center">
                <?php if ($hasProcess): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pr-arrow" id="pr-arrow-<?= $r['id'] ?>" style="color:#94a3b8;transition:transform .2s">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                <?php endif; ?>
            </td>
            <td class="font-mono text-xs text-slate-600"><?= !empty($r['date']) ? date('d/m/Y', strtotime($r['date'])) : '-' ?></td>
            <td>
                <a href="<?= admin_url('production_report/detail/' . $r['id']) ?>" onclick="event.stopPropagation()" target="_blank"
                    class="font-mono text-blue-600 hover:text-blue-800 font-medium text-xs"><?= htmlspecialchars($r['reference_no'] ?? '') ?></a>
            </td>
            <td>
                <div class="max-w-[200px] truncate text-xs font-medium text-slate-800" title="<?= htmlspecialchars($r['name_report'] ?? '') ?>">
                    <?= htmlspecialchars($r['name_report'] ?? '-') ?>
                </div>
            </td>
            <td>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold" style="color:<?= $type['color'] ?>;background:<?= $type['bg'] ?>">
                    <?= $type['name'] ?>
                </span>
            </td>
            <td class="text-xs text-slate-600"><?= htmlspecialchars($r['room_name'] ?? '-') ?></td>
            <td class="text-xs">
                <?php if (!empty($r['trouble_code'])): ?>
                    <span class="text-slate-700"><?= htmlspecialchars($r['trouble_code']) ?></span>
                    <div class="text-[10px] text-slate-400 truncate max-w-[120px]" title="<?= htmlspecialchars($r['trouble_name'] ?? '') ?>"><?= htmlspecialchars($r['trouble_name'] ?? '') ?></div>
                <?php else: ?>
                    <span class="text-slate-300">—</span>
                <?php endif; ?>
            </td>
            <td class="text-xs">
                <?php if (!empty($r['vg_code'])): ?>
                    <span class="text-slate-700"><?= htmlspecialchars($r['vg_code']) ?></span>
                <?php else: ?>
                    <span class="text-slate-300">—</span>
                <?php endif; ?>
            </td>
            <td class="text-xs text-slate-600 max-w-[100px] truncate"><?= htmlspecialchars(trim($r['creator_name'] ?? '')) ?: '-' ?></td>
            <td class="text-xs text-slate-600 max-w-[100px] truncate"><?= htmlspecialchars(trim($r['responsible_name'] ?? '')) ?: '-' ?></td>
            <td class="text-center text-xs font-mono"><?= $r['quantity'] ? number_format($r['quantity']) : '-' ?></td>
            <td class="text-xs">
                <?php if ($hasProcess): ?>
                    <span class="text-slate-600"><?= $r['done_steps'] ?>/<?= $r['total_steps'] ?> bước</span>
                <?php else: ?>
                    <span class="text-[10px] text-slate-400">Chưa có</span>
                <?php endif; ?>
            </td>
            <td style="min-width:80px">
                <?php if ($r['total_steps'] > 0): ?>
                    <div class="flex items-center gap-1.5">
                        <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                    <?= $r['process_rate'] >= 100 ? 'bg-emerald-500' : ($r['process_rate'] >= 50 ? 'bg-blue-500' : 'bg-amber-500') ?>"
                                style="width:<?= $r['process_rate'] ?>%"></div>
                        </div>
                        <span class="text-[10px] font-mono font-bold <?= $r['process_rate'] >= 100 ? 'text-emerald-600' : 'text-slate-500' ?>"><?= $r['process_rate'] ?>%</span>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <!-- Detail Row: Process Steps -->
        <?php if ($hasProcess): ?>
            <tr class="pr-detail-row" id="pr-detail-<?= $r['id'] ?>">
                <td colspan="13" style="padding:0 !important;border-bottom:2px solid #e2e8f0 !important;background:linear-gradient(to bottom,#f8fafc,#fff)">
                    <div style="padding:20px 24px">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="6" y1="3" x2="6" y2="15"></line>
                                <circle cx="18" cy="6" r="3"></circle>
                                <circle cx="6" cy="18" r="3"></circle>
                                <path d="M18 9a9 9 0 0 1-9 9"></path>
                            </svg>
                            <span style="font-size:12px;font-weight:600;color:#334155">Quy trình duyệt — <?= $r['total_steps'] ?> bước</span>
                            <span style="margin-left:auto;font-size:10px;padding:2px 8px;border-radius:999px;font-weight:500;
                    <?= $r['process_rate'] >= 100 ? 'background:#dcfce7;color:#166534' : ($r['process_rate'] >= 50 ? 'background:#dbeafe;color:#1e40af' : 'background:#fef3c7;color:#92400e') ?>">
                                <?= $r['process_rate'] ?>% hoàn thành
                            </span>
                        </div>

                        <!-- Progress Steps -->
                        <div style="overflow-x:auto;padding-bottom:8px">
                            <ul class="progressbar-kpi" style="min-width:<?= max(400, count($r['processes']) * 130) ?>px">
                                <?php
                                $found_current = false;
                                foreach ($r['processes'] as $pi):
                                    $is_done = !empty($pi['staff_process']);
                                    $is_current = false;
                                    if (!$is_done && !$found_current) {
                                        $is_current = true;
                                        $found_current = true;
                                    }
                                    $liClass = $is_done ? 'active' : ($is_current ? 'current' : '');
                                ?>
                                    <li class="<?= $liClass ?>">
                                        <div style="max-width:110px;margin:0 auto">
                                            <div style="font-size:10px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                                color:<?= $is_done ? '#166534' : ($is_current ? '#1d4ed8' : '#94a3b8') ?>"
                                                title="<?= htmlspecialchars($pi['name'] ?? '') ?>">
                                                <?= htmlspecialchars($pi['name'] ?? 'Bước') ?>
                                            </div>
                                            <?php if ($is_done): ?>
                                                <div style="font-size:9px;color:#16a34a;margin-top:2px">
                                                    ✓ <?= !empty($pi['staff_process']) ? get_staff_full_name($pi['staff_process']) : '' ?>
                                                </div>
                                                <?php if (!empty($pi['date_process'])): ?>
                                                    <div style="font-size:9px;color:#94a3b8"><?= date('d/m/Y', strtotime($pi['date_process'])) ?></div>
                                                <?php endif; ?>
                                            <?php elseif ($is_current): ?>
                                                <div style="font-size:9px;color:#3b82f6;margin-top:2px">Đang chờ...</div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Detail info -->
                        <?php if (!empty($r['described'])): ?>
                            <div style="margin-top:12px;padding:10px 14px;background:#f8fafc;border-radius:8px;border:1px solid #f1f5f9">
                                <div style="font-size:10px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:4px">Mô tả sự KPH</div>
                                <div style="font-size:12px;color:#334155;white-space:pre-wrap"><?= htmlspecialchars($r['described']) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($r['violation_level_name']) && $r['violate']): ?>
                            <div style="margin-top:8px;display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:6px;background:#fee2e2;border:1px solid #fca5a5">
                                <span style="font-size:11px;font-weight:600;color:#dc2626"><?= htmlspecialchars($r['violation_level_name']) ?></span>
                                <span style="font-size:10px;color:#991b1b">(trừ <?= $r['trouble_violation_point'] ?> điểm)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach;
else: ?>
    <tr>
        <td colspan="13" style="text-align:center;color:#94a3b8;padding:48px 0">
            <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-200 mb-2"></i>
            <div>Chưa có dữ liệu. Hãy sử dụng bộ lọc để xem phiếu báo cáo.</div>
        </td>
    </tr>
<?php endif; ?>
