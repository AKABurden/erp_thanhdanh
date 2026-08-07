<?php defined('BASEPATH') or exit('No direct script access allowed');
$selected = isset($selected) ? $selected : null;
$f = $selected ?? [];

// Helper logic for Proposals
function getProbationProposalPrint($score) {
    $p = (float)$score;
    if ($p < 75) return '<span style="color:#dc2626; font-weight:bold">Kém:</span> Không Tuyển, Chấm dứt thử việc';
    if ($p >= 75 && $p < 80) return '<span style="color:#ea580c; font-weight:bold">Tối Thiểu:</span> Duy trì thử việc, Giám Sát Đến Khi Đạt';
    if ($p >= 80 && $p < 85) return '<span style="color:#2563eb; font-weight:bold">Đạt:</span> Thử việc Chính Thức';
    if ($p >= 85) return '<span style="color:#059669; font-weight:bold">Tốt:</span> Ký HĐ chính thức';
    return '-';
}

function getKpiProposalPrint($score) {
    $p = (float)$score;
    if ($p < 75) return '<span style="color:#dc2626; font-weight:bold">Kém:</span> Xem xét lại';
    if ($p >= 75 && $p < 80) return '<span style="color:#ea580c; font-weight:bold">Tối Thiểu:</span> Cảnh báo, đào tạo lại ngay';
    if ($p >= 80 && $p < 90) return '<span style="color:#2563eb; font-weight:bold">Đạt:</span> Duy trì';
    if ($p >= 90 && $p <= 100) return '<span style="color:#059669; font-weight:bold">Tốt:</span> Đánh giá P3, đào tạo nâng cấp';
    if ($p > 100) return '<span style="color:#9333ea; font-weight:bold">Xuất sắc:</span> Đánh giá P3, đào tạo thăng chức';
    return '-';
}
?>

<?php if (empty($is_print_compact)): ?>
<style>
/* Print + screen styles */
.kpi-form-wrap { font-family: 'Times New Roman', serif; font-size: 12px; color: #1a1a1a; }
.kpi-form-wrap table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
.kpi-form-wrap td, .kpi-form-wrap th { border: 1px solid #999; padding: 5px 8px; vertical-align: middle; }
.kpi-form-wrap .title-row td { background: #1f3864; color: #fff; font-weight: bold; font-size: 14px; text-align: center; padding: 8px; }
.kpi-form-wrap .section-header td { background: #2e5597; color: #fff; font-weight: bold; font-size: 12px; padding: 5px 8px; }
.kpi-form-wrap .subsec td { background: #d6e4f7; font-weight: bold; text-align: center; padding: 4px; }
.kpi-form-wrap .col-head { background: #bdd7ee; font-weight: bold; text-align: center; }
.kpi-form-wrap .auto-cell { background: #fffacd; }
.kpi-form-wrap .summary-label { text-align: right; font-weight: bold; }
.kpi-form-wrap .label-cell { background: #f2f2f2; font-weight: bold; white-space: nowrap; }
.kpi-form-wrap .period-active { background: #1f3864 !important; color: #fff !important; font-weight: bold; text-align: center; }
.kpi-form-wrap .period-inactive { background: #d6e4f7; color: #555; text-align: center; font-size: 10px; }
@page { size: A4 portrait; margin: 10mm 14mm; }
</style>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 no-print">
    <div>
        <h2 class="text-lg font-bold text-slate-900">In phiếu đánh giá nhân sự</h2>
        <p class="text-sm text-slate-500">Chọn phiếu để xem và in</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    <!-- Danh sách phiếu -->
    <div class="bg-white rounded-xl border border-slate-100 overflow-hidden no-print">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <input type="text" id="fi-search" placeholder="Tìm phiếu..." oninput="filterFi()"
                class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="overflow-y-auto" style="max-height:calc(100vh - 220px)">
            <?php if (!empty($forms)): foreach ($forms as $fm): ?>
            <a href="<?= site_url('admin/DashboardKpi/index/form_in?id='.$fm['id']) ?>"
               class="fi-item block px-3 py-2.5 border-b border-slate-50 hover:bg-blue-50 transition-colors <?= $selected && $selected['id']==$fm['id'] ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' ?>"
               data-search="<?= strtolower(($fm['staff_name']??'').' '.($fm['code']??'').' '.($fm['ky_danh_gia']??'')) ?>">
                <div class="font-semibold text-slate-800 text-xs"><?= htmlspecialchars($fm['staff_name']??'-') ?></div>
                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($fm['ky_danh_gia']??'-') ?> — <?= htmlspecialchars($fm['room_name']??'') ?></div>
                <div class="flex items-center gap-1 mt-0.5">
                    <span class="text-[10px] font-mono text-blue-500"><?= htmlspecialchars($fm['code']??'') ?></span>
                    <?php if (!empty($fm['rating_name'])): ?>
                    <span class="text-[9px] px-1.5 py-0.5 rounded-full font-bold" style="background:<?= $fm['rating_color']??'#e2e8f0' ?>22;color:<?= $fm['rating_color']??'#64748b' ?>"><?= htmlspecialchars($fm['rating_name']) ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; else: ?>
            <div class="px-4 py-8 text-center text-slate-400 text-sm">Chưa có phiếu</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Phiếu chi tiết -->
    <div class="lg:col-span-3">
        <?php if ($selected): ?>
        <!-- Toolbar -->
        <div class="flex justify-between items-center mb-3 no-print">
            <span class="text-sm text-slate-500">Phiếu #<?= $f['id'] ?> — <?= htmlspecialchars($f['code']??'') ?></span>
            <button onclick="printPhieu()" class="flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm rounded-lg hover:bg-slate-700">
                <i data-lucide="printer" class="w-4 h-4"></i> In phiếu
            </button>
        </div>

        <div id="fi-print-area" class="kpi-form-wrap bg-white p-6 rounded-xl border border-slate-100">
        <?php else: ?>
        <div class="bg-white rounded-xl border border-slate-100 h-full flex items-center justify-center py-24 text-slate-400">
            <div class="text-center">
                <i data-lucide="mouse-pointer-click" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                <div>Chọn phiếu bên trái để xem và in</div>
            </div>
        </div>
        <?php endif; ?>
<?php else: ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>In phiếu <?= htmlspecialchars($f['code'] ?? '') ?></title>
<style>
.kpi-form-wrap { font-family: 'Times New Roman', serif; font-size: 12px; color: #1a1a1a; }
.kpi-form-wrap table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
.kpi-form-wrap td, .kpi-form-wrap th { border: 1px solid #999; padding: 5px 8px; vertical-align: middle; }
.kpi-form-wrap .title-row td { background: #1f3864; color: #fff; font-weight: bold; font-size: 14px; text-align: center; padding: 8px; }
.kpi-form-wrap .section-header td { background: #2e5597; color: #fff; font-weight: bold; font-size: 12px; padding: 5px 8px; }
.kpi-form-wrap .subsec td { background: #d6e4f7; font-weight: bold; text-align: center; padding: 4px; }
.kpi-form-wrap .col-head { background: #bdd7ee; font-weight: bold; text-align: center; }
.kpi-form-wrap .auto-cell { background: #fffacd; }
.kpi-form-wrap .summary-label { text-align: right; font-weight: bold; }
.kpi-form-wrap .label-cell { background: #f2f2f2; font-weight: bold; white-space: nowrap; }
.kpi-form-wrap .period-active { background: #1f3864 !important; color: #fff !important; font-weight: bold; text-align: center; }
.kpi-form-wrap .period-inactive { background: #d6e4f7 !important; color: #555; text-align: center; font-size: 10px; }
tr { page-break-inside: avoid; }
* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
@page { size: A4 portrait; margin: 5mm 8mm; }
</style>
</head>
<body>
<div class="kpi-form-wrap" id="fi-print-area">
<?php endif; ?>

<?php if ($selected || !empty($is_print_compact)): ?>

            <!-- Tiêu đề + Chỉ báo kỳ đánh giá -->
            <?php
            $kyHienTai = $f['ky_danh_gia'] ?? '';
            $kyList = [
                '3 tháng'  => 'KPI 3 tháng',
                '6 tháng'  => 'KPI 6 tháng',
                '9 tháng'  => 'KPI 9 tháng',
                '12 tháng' => 'KPI 12 tháng',
            ];
            ?>
            <table>
                <tr><td class="title-row" colspan="4">PHIẾU IN ĐÁNH GIÁ NHÂN SỰ THEO VÒNG ĐỜI</td></tr>
                <tr>
                    <?php foreach ($kyList as $key => $label):
                        $isActive = (stripos($kyHienTai, $key) !== false);
                    ?>
                    <td class="<?= $isActive ? 'period-active' : 'period-inactive' ?>" style="font-size:11px;padding:5px 8px;width:25%">
                        <?= $label ?><?php if ($isActive): ?> ✓<?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
            </table>

            <!-- I. THÔNG TIN CHUNG -->
            <table>
                <tr><td class="section-header" colspan="4">I. THÔNG TIN CHUNG</td></tr>
                <tr>
                    <td class="label-cell" style="width:18%">Họ và tên</td>
                    <td class="auto-cell" style="width:32%"><?= htmlspecialchars($f['staff_name']??'') ?></td>
                    <td class="label-cell" style="width:18%">Mã nhân sự</td>
                    <td class="auto-cell" style="width:32%"><?= htmlspecialchars($f['code']??'') ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Phòng ban</td>
                    <td class="auto-cell"><?= htmlspecialchars($f['room_name']??'') ?></td>
                    <td class="label-cell">Vị trí</td>
                    <td class="auto-cell"><?= htmlspecialchars($f['role_name']??'') ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Loại đánh giá</td>
                    <td class="auto-cell"><?= $f['type']==2 ? 'Đánh giá nhân viên (CT)' : 'Thử việc' ?></td>
                    <td class="label-cell">Kỳ đánh giá</td>
                    <td class="auto-cell"><?= htmlspecialchars($f['ky_danh_gia']??'') ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Ngày bắt đầu</td>
                    <td class="auto-cell"><?= !empty($f['date_start']) ? date('d/m/Y', strtotime($f['date_start'])) : '' ?></td>
                    <td class="label-cell">Ngày kết thúc</td>
                    <td class="auto-cell"><?= !empty($f['date_end']) ? date('d/m/Y', strtotime($f['date_end'])) : '' ?></td>
                </tr>
            </table>

            <!-- II. KẾT QUẢ ĐÁNH GIÁ QUA CÁC GATE -->
            <table>
                <tr><td class="section-header" colspan="4">II. KẾT QUẢ ĐÁNH GIÁ QUA CÁC GATE</td></tr>
            </table>

            <!-- A. ĐIỀU KIỆN BẮT BUỘC (GATE) -->
            <?php if (!empty($checkList['A'])): ?>
            <table>
                <tr><td class="subsec" colspan="4">A. ĐIỀU KIỆN BẮT BUỘC (GATE)</td></tr>
                <tr>
                    <th class="col-head">Điều kiện bắt buộc</th>
                    <th class="col-head" style="width:15%">YES</th>
                    <th class="col-head" style="width:15%">NO</th>
                    <th class="col-head" style="width:30%">Ghi chú</th>
                </tr>
                <?php foreach ($checkList['A'] as $val): 
                    $saved = $checkListItems['A'][$val['id']] ?? null;
                    $gate = $saved['gate'] ?? '';
                ?>
                <tr>
                    <td><?= htmlspecialchars($val['name']) ?></td>
                    <td style="text-align:center; font-weight:bold; color:#155724"><?= $gate == '1' ? '✓' : '' ?></td>
                    <td style="text-align:center; font-weight:bold; color:#c00000"><?= $gate == '0' ? '✓' : '' ?></td>
                    <td class="auto-cell"><?= htmlspecialchars($saved['note'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>

            <!-- B. ĐÁNH GIÁ KPI -->
            <?php if (!empty($checkList['B'])): ?>
            <table>
                <tr><td class="subsec" colspan="4">B. ĐÁNH GIÁ KPI</td></tr>
                <tr>
                    <th class="col-head">Tiêu chí</th>
                    <th class="col-head" style="width:20%">Chuẩn</th>
                    <th class="col-head" style="width:20%">Thực tế (%)</th>
                    <th class="col-head" style="width:20%">Điểm</th>
                </tr>
                <?php $totalPointB = 0; foreach ($checkList['B'] as $val): 
                    $totalPointB += $val['point'];
                    $saved = $checkListItems['B'][$val['id']] ?? null;
                ?>
                <tr>
                    <td><?= htmlspecialchars($val['name']) ?></td>
                    <td style="text-align:center"><?= htmlspecialchars($val['conditions'].' '.$val['prefix']) ?></td>
                    <td class="auto-cell" style="text-align:center"><?= htmlspecialchars($saved['percent'] ?? '') ?></td>
                    <td class="auto-cell" style="text-align:center; font-weight:bold"><?= htmlspecialchars($saved['point'] ?? '') ?> <span style="font-weight:normal; font-size:10px; color:#666">/<?= $val['point'] ?></span></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="summary-label">Tổng điểm Phần B:</td>
                    <td class="auto-cell" style="text-align:center; font-weight:bold; background:#e2f0d9"><?= $f['point_b']??0 ?> <span style="font-weight:normal; font-size:10px; color:#666">/ <?= $totalPointB ?></span></td>
                </tr>
            </table>
            <?php endif; ?>

            <!-- C. TUÂN THỦ -->
            <?php if (!empty($checkList['C'])): ?>
            <table>
                <tr><td class="subsec" colspan="4">C. TUÂN THỦ</td></tr>
                <tr>
                    <th class="col-head">Nội dung</th>
                    <th class="col-head" style="width:20%">Chuẩn</th>
                    <th class="col-head" style="width:20%">Thực tế</th>
                    <th class="col-head" style="width:20%">Điểm</th>
                </tr>
                <?php $totalPointC = 0; foreach ($checkList['C'] as $val): 
                    $totalPointC += $val['point'];
                    $saved = $checkListItems['C'][$val['id']] ?? null;
                ?>
                <tr>
                    <td><?= htmlspecialchars($val['name']) ?></td>
                    <td style="text-align:center"><?= htmlspecialchars($val['conditions'].' '.$val['prefix']) ?></td>
                    <td class="auto-cell" style="text-align:center"><?= htmlspecialchars($saved['percent'] ?? '') ?></td>
                    <td class="auto-cell" style="text-align:center; font-weight:bold"><?= htmlspecialchars($saved['point'] ?? '') ?> <span style="font-weight:normal; font-size:10px; color:#666">/<?= $val['point'] ?></span></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="summary-label">Tổng điểm Phần C:</td>
                    <td class="auto-cell" style="text-align:center; font-weight:bold; background:#e2f0d9"><?= $f['point_c']??0 ?> <span style="font-weight:normal; font-size:10px; color:#666">/ <?= $totalPointC ?></span></td>
                </tr>
            </table>
            <?php endif; ?>

            <!-- D. NĂNG LỰC -->
            <?php if (!empty($checkList['D'])): ?>
            <table>
                <tr><td class="subsec" colspan="2">D. NĂNG LỰC / PHÙ HỢP</td></tr>
                <tr>
                    <th class="col-head">Tiêu chí đánh giá</th>
                    <th class="col-head" style="width:20%">Điểm</th>
                </tr>
                <?php $totalPointD = 0; foreach ($checkList['D'] as $val): 
                    $totalPointD += $val['point'];
                    $saved = $checkListItems['D'][$val['id']] ?? null;
                ?>
                <tr>
                    <td><?= htmlspecialchars($val['name']) ?></td>
                    <td class="auto-cell" style="text-align:center; font-weight:bold"><?= htmlspecialchars($saved['point'] ?? '') ?> <span style="font-weight:normal; font-size:10px; color:#666">/<?= $val['point'] ?></span></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="summary-label">Tổng điểm Phần D:</td>
                    <td class="auto-cell" style="text-align:center; font-weight:bold; background:#e2f0d9"><?= $f['point_d']??0 ?> <span style="font-weight:normal; font-size:10px; color:#666">/ <?= $totalPointD ?></span></td>
                </tr>
            </table>
            <?php endif; ?>

            <!-- III. TỔNG HỢP & XẾP LOẠI -->
            <table style="margin-top:15px; border: 2px solid #1f3864; page-break-before: always;">
                <tr><td class="section-header" colspan="2" style="font-size:14px; text-transform:uppercase">III. Tổng hợp & Kết luận</td></tr>
                <tr>
                    <td class="summary-label" style="width:40%; font-size:16px; padding:15px">TỔNG ĐIỂM ĐẠT ĐƯỢC</td>
                    <td class="auto-cell" style="text-align:center; font-weight:900; font-size:24px; color:#c00000; background:#fff3cd; padding:15px"><?= $f['point']??0 ?> <span style="font-size:14px; color:#666">/ 100</span></td>
                </tr>
                <tr>
                    <td class="summary-label" style="font-size:14px; padding:10px">KẾT QUẢ KPI</td>
                    <td class="auto-cell" style="text-align:center; font-size:15px; padding:10px">
                        <?= ((int)($f['type'] ?? 1) === 2) ? getKpiProposalPrint($f['point'] ?? 0) : getProbationProposalPrint($f['point'] ?? 0) ?>
                    </td>
                </tr>
                <tr>
                    <td class="summary-label" style="padding:10px">GHI CHÚ</td>
                    <td class="auto-cell" style="padding:10px"><?= htmlspecialchars($f['note']??'') ?></td>
                </tr>
            </table>

            <!-- Phê duyệt -->
            <table style="margin-top:8px">
                <tr><td class="section-header" colspan="4">V. PHÊ DUYỆT</td></tr>
                <tr>
                    <th class="col-head" style="width:20%">Cấp duyệt</th>
                    <th class="col-head" style="width:30%">Checklist</th>
                    <th class="col-head" style="width:25%">Kết quả</th>
                    <th class="col-head" style="width:25%">Ý kiến / ký tên</th>
                </tr>
                <?php
                $approvalSteps = [
                    ['role'=>'TP.HCNS/TLCM','check'=>'KPI / năng lực / hồ sơ đạt','min'=>1],
                    ['role'=>'KTNB','check'=>'Data / audit / gian lận','min'=>2],
                    ['role'=>'KSRR','check'=>'SOP / pháp lý / cảnh báo','min'=>3],
                    ['role'=>'BAN GIÁM ĐỐC (BOD)','check'=>'Phê duyệt ngoại lệ / nâng bậc / thăng chức','min'=>4],
                ];
                $approvalStatus = (int)($f['approval_status']??0);
                foreach ($approvalSteps as $step):
                    $done = $approvalStatus >= $step['min'];
                ?>
                <tr>
                    <td style="font-weight:bold"><?= $step['role'] ?></td>
                    <td><?= $step['check'] ?></td>
                    <td class="auto-cell" style="text-align:center">
                        <?php if ($done): ?>
                            <span style="color:#155724;font-weight:bold">✔ Đã duyệt</span>
                        <?php else: ?>
                            <span style="color:#999">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="auto-cell"></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <!-- VI. AUDIT & XÁC NHẬN -->
            <table>
                <tr><td class="section-header" colspan="4">VI. AUDIT & XÁC NHẬN</td></tr>
                <tr>
                    <td class="label-cell">Ngày lập phiếu</td>
                    <td class="auto-cell"><?= !empty($f['date_created']) ? date('d/m/Y', strtotime($f['date_created'])) : date('d/m/Y') ?></td>
                    <td class="label-cell">Người lập</td>
                    <td class="auto-cell"><?= htmlspecialchars($f['staff_name']??'') ?></td>
                </tr>
            </table>

            <!-- J. KÝ DUYỆT -->
            <table style="margin-top:12px">
                <tr><td colspan="8" style="text-align:center;font-weight:bold;background:#f2f2f2;padding:6px">J. KÝ DUYỆT</td></tr>
                <tr>
                    <td style="width:25%;text-align:center;font-weight:bold">TP.HCNS</td>
                    <td style="width:25%;text-align:center;font-weight:bold">KTNB</td>
                    <td style="width:25%;text-align:center;font-weight:bold">KSRR</td>
                    <td style="width:25%;text-align:center;font-weight:bold">BOD / Giám đốc</td>
                </tr>
                <tr><td style="height:70px"></td><td></td><td></td><td></td></tr>
                <tr>
                    <td style="text-align:center;font-size:10px;color:#666">(Ký, ghi rõ họ tên)</td>
                    <td style="text-align:center;font-size:10px;color:#666">(Ký, ghi rõ họ tên)</td>
                    <td style="text-align:center;font-size:10px;color:#666">(Ký, ghi rõ họ tên)</td>
                    <td style="text-align:center;font-size:10px;color:#666">(Ký, ghi rõ họ tên)</td>
                </tr>
            </table>

        </div><!-- end fi-print-area -->
<?php endif; ?>

<?php if (empty($is_print_compact)): ?>
        <?php if (!$selected): ?>
        <div class="bg-white rounded-xl border border-slate-100 h-full flex items-center justify-center py-24 text-slate-400">
            <div class="text-center">
                <i data-lucide="mouse-pointer-click" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                <div>Chọn phiếu bên trái để xem và in</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
</div>
<script>
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
<?php endif; ?>

<script>
function filterFi() {
    var q = document.getElementById('fi-search').value.toLowerCase();
    document.querySelectorAll('.fi-item').forEach(function(r){
        r.style.display = !q || r.dataset.search.includes(q) ? '' : 'none';
    });
}

function printPhieu() {
    var content = document.getElementById('fi-print-area');
    if (!content) return;
    var css = `
        body { font-family: 'Times New Roman', serif; font-size: 12px; color: #1a1a1a; margin: 5mm 8mm; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        td, th { border: 1px solid #999; padding: 4px 7px; vertical-align: middle; }
        .title-row td { background: #1f3864 !important; color: #fff !important; font-weight: bold; font-size: 14px; text-align: center; padding: 7px; }
        .section-header td { background: #2e5597 !important; color: #fff !important; font-weight: bold; font-size: 12px; padding: 4px 7px; }
        .subsec td { background: #d6e4f7 !important; font-weight: bold; text-align: center; padding: 3px; }
        .col-head { background: #bdd7ee !important; font-weight: bold; text-align: center; }
        .auto-cell { background: #fffacd !important; }
        .label-cell { background: #f2f2f2 !important; font-weight: bold; }
        .summary-label { text-align: right; font-weight: bold; }
        .period-active { background: #1f3864 !important; color: #fff !important; font-weight: bold; text-align: center; }
        .period-inactive { background: #d6e4f7 !important; color: #555; text-align: center; font-size: 10px; }
        tr { page-break-inside: avoid; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        @page { size: A4 portrait; margin: 5mm 8mm; }
    `;
    var w = window.open('', '_blank', 'width=900,height=700');
    w.document.open();
    w.document.write('<html><head><meta charset="UTF-8"><title>In phiếu</title><style>' + css + '</style></head><body>');
    w.document.write(content.innerHTML);
    w.document.write('</body></html>');
    w.document.close();
    w.onload = function() { w.focus(); w.print(); };
}
    document.addEventListener('DOMContentLoaded', function() {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            setTimeout(printPhieu, 1000); // Small delay to ensure everything is rendered
        }
    });
</script>
