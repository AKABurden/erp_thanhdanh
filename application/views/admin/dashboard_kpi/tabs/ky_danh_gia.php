<?php defined('BASEPATH') or exit('No direct script access allowed');

// 4 kỳ đánh giá cố định
$currentYear = date('Y');
$fixed_periods = [
    [
        'key'   => 'q1',
        'name'  => 'Kỳ 3 tháng',
        'label' => 'Quý I',
        'months' => 'Tháng 1 – 3',
        'date_start' => $currentYear . '-01-01',
        'date_end'   => $currentYear . '-03-31',
        'icon'  => 'calendar-days',
        'color' => '#3b82f6',
        'bg'    => '#eff6ff',
        'border' => '#bfdbfe',
    ],
    [
        'key'   => 'q2',
        'name'  => 'Kỳ 6 tháng',
        'label' => 'Nửa năm đầu',
        'months' => 'Tháng 1 – 6',
        'date_start' => $currentYear . '-01-01',
        'date_end'   => $currentYear . '-06-30',
        'icon'  => 'calendar-range',
        'color' => '#8b5cf6',
        'bg'    => '#f5f3ff',
        'border' => '#ddd6fe',
    ],
    [
        'key'   => 'q3',
        'name'  => 'Kỳ 9 tháng',
        'label' => '3 Quý',
        'months' => 'Tháng 1 – 9',
        'date_start' => $currentYear . '-01-01',
        'date_end'   => $currentYear . '-09-30',
        'icon'  => 'calendar-clock',
        'color' => '#f59e0b',
        'bg'    => '#fffbeb',
        'border' => '#fde68a',
    ],
    [
        'key'   => 'q4',
        'name'  => 'Kỳ 12 tháng',
        'label' => 'Cả năm',
        'months' => 'Tháng 1 – 12',
        'date_start' => $currentYear . '-01-01',
        'date_end'   => $currentYear . '-12-31',
        'icon'  => 'calendar-check',
        'color' => '#10b981',
        'bg'    => '#ecfdf5',
        'border' => '#a7f3d0',
    ],
];

// Tính tháng hiện tại để xác định kỳ đang hoạt động
$currentMonth = (int) date('n');
$activeKey = 'q4';
if ($currentMonth <= 3) $activeKey = 'q1';
elseif ($currentMonth <= 6) $activeKey = 'q2';
elseif ($currentMonth <= 9) $activeKey = 'q3';

// Thêm các kỳ theo 4 tuần trong tháng hiện tại
$daysInMonth = date('t', strtotime("$currentYear-$currentMonth-01"));
$week_periods = [];
for ($w = 1; $w <= 4; $w++) {
    $startDay = ($w - 1) * 7 + 1;
    $endDay = ($w == 4) ? $daysInMonth : $startDay + 6;
    
    $date_start = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $startDay);
    $date_end   = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $endDay);
    
    $week_periods[] = [
        'key'   => 'w' . $w,
        'name'  => 'Tuần ' . $w,
        'label' => 'Tuần ' . $w,
        'months' => 'Tháng ' . $currentMonth,
        'date_start' => $date_start,
        'date_end'   => $date_end,
        'icon'  => 'calendar',
        'color' => '#ec4899',
        'bg'    => '#fdf2f8',
        'border' => '#fbcfe8',
    ];
}

$fixed_periods = array_merge($fixed_periods, $week_periods);

// Tính tuần đang hoạt động (w1, w2, w3, w4) dựa vào ngày hiện tại
$today = (int) date('j');
$currentWeekKey = 'w1';
if ($today > 21) $currentWeekKey = 'w4';
elseif ($today > 14) $currentWeekKey = 'w3';
elseif ($today > 7) $currentWeekKey = 'w2';
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
            <i data-lucide="calendar-range" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">Kỳ đánh giá</h2>
            <p class="text-xs text-slate-500">Các kỳ đánh giá KPI cố định trong năm <?= $currentYear ?></p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs text-slate-400">Năm đánh giá:</span>
        <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-800"><?= $currentYear ?></span>
    </div>
</div>

<!-- Period Cards -->


<!-- Thông tin kỳ -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px">
        <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
        <span style="font-size:13px;font-weight:600;color:#334155">Thông tin kỳ đánh giá</span>
    </div>
    <div style="padding:20px">
        <table id="ky-info-table">
            <thead>
                <tr>
                    <th>Kỳ</th>
                    <th class="text-center">Khoảng thời gian</th>
                    <th class="text-center">Từ ngày</th>
                    <th class="text-center">Đến ngày</th>
                    <th class="text-center">Số ngày</th>
                    <th class="text-center">Tiến độ</th>
                    <th class="text-center">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fixed_periods as $p):
                    $isActive = ($p['key'] === $activeKey || $p['key'] === $currentWeekKey);
                    $daysTotal = (strtotime($p['date_end']) - strtotime($p['date_start'])) / 86400 + 1;
                    $daysPassed = max(0, min($daysTotal, (time() - strtotime($p['date_start'])) / 86400));
                    $progress = $daysTotal > 0 ? min(100, round(($daysPassed / $daysTotal) * 100)) : 0;
                    $isPast = (time() > strtotime($p['date_end']));
                    $isFuture = (time() < strtotime($p['date_start']));
                ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="width:28px;height:28px;border-radius:8px;background:<?= $p['bg'] ?>;display:flex;align-items:center;justify-content:center">
                                    <i data-lucide="<?= $p['icon'] ?>" style="width:14px;height:14px;color:<?= $p['color'] ?>"></i>
                                </div>
                                <span style="font-weight:600;color:#0f172a;font-size:13px"><?= $p['name'] ?></span>
                            </div>
                        </td>
                        <td class="text-center" style="font-size:12px;color:#64748b"><?= $p['months'] ?></td>
                        <td class="text-center" style="font-size:12px;font-family:monospace;color:#475569"><?= date('d/m/Y', strtotime($p['date_start'])) ?></td>
                        <td class="text-center" style="font-size:12px;font-family:monospace;color:#475569"><?= date('d/m/Y', strtotime($p['date_end'])) ?></td>
                        <td class="text-center" style="font-size:12px;font-weight:600;color:#334155"><?= round($daysTotal) ?></td>
                        <td class="text-center" style="min-width:100px">
                            <div style="display:flex;align-items:center;gap:6px;justify-content:center">
                                <div style="width:60px;height:4px;background:#e2e8f0;border-radius:999px;overflow:hidden">
                                    <div style="height:100%;border-radius:999px;background:<?= $p['color'] ?>;width:<?= $progress ?>%"></div>
                                </div>
                                <span style="font-size:10px;font-weight:700;color:<?= $p['color'] ?>;font-family:monospace"><?= $progress ?>%</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php if ($isPast): ?>
                                <span style="padding:3px 10px;border-radius:999px;font-size:10px;font-weight:600;background:#f1f5f9;color:#64748b">Đã kết thúc</span>
                            <?php elseif ($isActive): ?>
                                <span style="padding:3px 10px;border-radius:999px;font-size:10px;font-weight:600;background:<?= $p['bg'] ?>;color:<?= $p['color'] ?>;border:1px solid <?= $p['border'] ?>">Đang mở</span>
                            <?php else: ?>
                                <span style="padding:3px 10px;border-radius:999px;font-size:10px;font-weight:600;background:#f8fafc;color:#94a3b8">Chưa đến</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }
    }

    #ky-info-table {
        width: 100%;
        border-collapse: collapse;
    }

    #ky-info-table th {
        padding: 10px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #475569;
        background: #f8fafc;
        white-space: nowrap;
        border-bottom: 2px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
    }

    #ky-info-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        vertical-align: middle;
    }

    #ky-info-table tbody tr:hover td {
        background: #f8fafc;
    }
</style>