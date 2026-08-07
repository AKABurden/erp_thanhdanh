<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Thống kê báo cáo vi phạm</h2>
        <p class="text-sm text-slate-500">Thống kê số lượng báo cáo không phù hợp & vi phạm theo phòng ban trong năm</p>
    </div>

    <div class="flex items-center gap-2">
        <label for="vp_year_search" class="text-sm font-medium text-slate-700">Năm:</label>
        <select id="vp_year_search" onchange="changeVpYear(this.value)" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
            <?php
            $current_year = date('Y');
            for ($y = $current_year - 3; $y <= $current_year + 1; $y++) {
                $sel = ($violate_year == $y) ? 'selected' : '';
                echo "<option value=\"$y\" $sel>$y</option>";
            }
            ?>
        </select>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-100 overflow-hidden p-4">
    <div class="overflow-x-auto" style="max-height: calc(100vh - 250px); overflow-y: auto;">
        <table id="vp-synthetic-table" class="w-full text-left border-collapse" style="min-width: 1400px;">
            <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                <tr>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border border-slate-200" rowspan="2" style="background:#f8fafc">STT</th>
                    <th class="px-3 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border border-slate-200" rowspan="2" style="background:#f8fafc; position:sticky; left:0; z-index:20;">Phòng ban</th>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <th class="px-3 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center border border-slate-200" colspan="2" style="background:#f8fafc">Tháng <?= $m ?></th>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <th class="px-2 py-2 text-[10px] font-bold text-slate-500 uppercase text-center border border-slate-200 bg-white" title="Không phù hợp">KPH</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-red-500 uppercase text-center border border-slate-200 bg-red-50" title="Vi phạm">VP</th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($synthetic_reports)): ?>
                    <?php
                    $idx = 0;
                    // Foot totals
                    $footData = [];
                    for ($m = 1; $m <= 12; $m++) {
                        $mStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                        $footData[$mStr] = [0 => 0, 1 => 0];
                    }

                    foreach ($synthetic_reports as $room => $monthsData):
                        $idx++;
                    ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2 text-center text-xs text-slate-500 border border-slate-100"><?= $idx ?></td>
                            <td class="px-3 py-2 text-xs font-semibold text-slate-700 border border-slate-100 bg-white sticky left-0 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] z-10 whitespace-nowrap">
                                <?= htmlspecialchars($room) ?>
                            </td>

                            <?php for ($m = 1; $m <= 12; $m++):
                                $mStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                                $val0 = $monthsData[$mStr][0] ?? 0;
                                $val1 = $monthsData[$mStr][1] ?? 0;

                                $footData[$mStr][0] += $val0;
                                $footData[$mStr][1] += $val1;
                            ?>
                                <td class="px-2 py-2 text-center text-xs font-mono border border-slate-100 <?= $val0 > 0 ? 'font-bold text-slate-700' : 'text-slate-300' ?>">
                                    <?= $val0 > 0 ? $val0 : '-' ?>
                                </td>
                                <td class="px-2 py-2 text-center text-xs font-mono border border-slate-100 <?= $val1 > 0 ? 'font-bold text-red-600 bg-red-50/50' : 'text-slate-300' ?>">
                                    <?= $val1 > 0 ? $val1 : '-' ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="26" class="text-center py-12 text-slate-400">
                            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-30"></i>
                            <div>Không có dữ liệu báo cáo nào trong năm <?= $violate_year ?></div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

            <?php if (!empty($synthetic_reports)): ?>
                <tfoot class="bg-slate-100 sticky bottom-0 shadow-[0_-2px_5px_-2px_rgba(0,0,0,0.05)]">
                    <tr>
                        <td colspan="2" class="px-3 py-3 text-right text-xs font-bold text-slate-700 border border-slate-200 sticky left-0 z-10 bg-slate-100">TỔNG CỘNG</td>
                        <?php for ($m = 1; $m <= 12; $m++):
                            $mStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                            $total0 = $footData[$mStr][0];
                            $total1 = $footData[$mStr][1];
                        ?>
                            <td class="px-2 py-3 text-center text-xs font-bold font-mono border border-slate-200 text-slate-700">
                                <?= $total0 > 0 ? $total0 : '-' ?>
                            </td>
                            <td class="px-2 py-3 text-center text-xs font-bold font-mono border border-slate-200 text-red-600 bg-red-50/50">
                                <?= $total1 > 0 ? $total1 : '-' ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Chú thích -->
<div class="mt-4 flex flex-wrap gap-4 text-[11px] text-slate-500">
    <div class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded bg-white border border-slate-200 block"></span> KPH: Báo cáo không phù hợp
    </div>
    <div class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded bg-red-50 border border-red-200 block"></span> VP: Báo cáo vi phạm
    </div>
</div>

<style>
    /* Đảm bảo table header không bị trôi khi cuộn ngang */
    #vp-synthetic-table th {
        position: relative;
    }
</style>

<script>
    function changeVpYear(year) {
        // Navigate with year param
        var url = new URL(window.location.href);
        url.searchParams.set('year', year);
        window.location.href = url.toString();
    }

    // Re-init lucide icons just in case
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>