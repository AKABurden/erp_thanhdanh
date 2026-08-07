<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
        <div class="flex items-center gap-3">
            <?php
            $backUrl = site_url('admin/DashboardKpi/index/phieu_danh_gia');
            if (!empty($url_year) || !empty($url_month) || !empty($url_ky)) {
                $backUrl .= '?' . http_build_query([
                    'year' => $url_year,
                    'month' => $url_month,
                    'ky' => $url_ky
                ]);
            }
            ?>
            <a href="<?= $backUrl ?>" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900"><?= ($id > 0) ? 'Chỉnh sửa phiếu đánh giá' : 'Tạo mới phiếu đánh giá' ?></h2>
                    <?php
                    $display_ky = !empty($url_ky) ? $url_ky : (!empty($ky_danh_gia) ? $ky_danh_gia : '');
                    if (!empty($display_ky)): ?>
                        <span id="ky-label-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-100 text-violet-700 border border-violet-200">Kỳ <?= $display_ky ?></span>
                    <?php else: ?>
                        <span id="ky-label-badge" class="hidden inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-100 text-violet-700 border border-violet-200"></span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-slate-500">Đánh giá nhân viên chính thức (CT) theo tiêu chuẩn KPI</p>
            </div>
        </div>
        <div class="flex gap-2">
            <?php if ($id > 0): ?>
                <button type="button" id="btn-fetch-real-data" style="display: none;" onclick="fetchRealData(this)"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition shadow-sm mr-2">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Lấy dữ liệu thực tế
                </button>
                <button type="button" onclick="printInIframe('<?= admin_url('DashboardKpi/print_compact/' . $id . '?print=1') ?>')"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> In phiếu
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Approval Stepper -->
    <?php if ($id > 0): ?>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 mb-5">
            <?php
            $st = (int)($dtData['approval_status'] ?? 0);
            if ($st == -1):
            ?>
                <div class="flex items-center gap-3 text-red-600 bg-red-50 p-3 rounded-lg border border-red-100">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                    <span class="text-sm font-bold">PHIẾU ĐÃ BỊ TỪ CHỐI</span>
                </div>
            <?php else: ?>
                <div class="relative flex justify-between items-start w-full max-w-[700px] mx-auto py-4">
                    <!-- Progress Line Background -->
                    <div class="absolute left-[10%] right-[10%] top-[34px] h-[3px] rounded-full bg-slate-100 z-0"></div>
                    <!-- Progress Line Active -->
                    <?php
                    $progressWidth = 0;
                    if ($st == 1) $progressWidth = 33.3;
                    elseif ($st == 2) $progressWidth = 66.6;
                    elseif ($st >= 3) $progressWidth = 100;
                    ?>
                    <div class="absolute left-[10%] top-[34px] h-[3px] rounded-full bg-emerald-400 z-0 transition-all duration-500" style="width: calc(<?= $progressWidth ?>% * 0.8)"></div>

                    <?php
                    $steps = [
                        ['name' => 'HCNS', 'by' => $dtData['hcns_name'] ?? '', 'date' => $dtData['hcns_approve_date'] ?? ''],
                        ['name' => 'KTNB', 'by' => $dtData['ktnb_name'] ?? '', 'date' => $dtData['ktnb_approve_date'] ?? ''],
                        ['name' => 'KSRR', 'by' => $dtData['ksrr_name'] ?? '', 'date' => $dtData['ksrr_approve_date'] ?? ''],
                        ['name' => 'BOD',  'by' => $dtData['bod_name'] ?? '',  'date' => $dtData['bod_approve_date'] ?? ''],
                    ];

                    foreach ($steps as $i => $s):
                        $past = $st > $i;
                        $cur = $st == $i;
                        $nameStr = ($past && !empty(trim($s['by']))) ? $s['by'] : '';
                        $dateStr = $past ? (!empty($s['date']) ? date('H:i d/m/y', strtotime($s['date'])) : '') : '';
                    ?>
                        <div class="relative z-10 flex flex-col items-center" style="width: 140px;">
                            <?php if ($past): ?>
                                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg ring-4 ring-emerald-50 transition-all">
                                    <i data-lucide="check" class="w-5 h-5"></i>
                                </div>
                            <?php elseif ($cur): ?>
                                <div class="flex items-center bg-white px-1.5 gap-2 h-[38px] rounded-full ring-4 ring-blue-100 shadow-sm border border-blue-200">
                                    <button type="button" onclick="actApprove(<?= $id ?>, 'approved')" class="w-8 h-8 flex items-center justify-center bg-blue-600 text-white rounded-full hover:bg-blue-700 shadow-sm transition-all" title="Duyệt">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                    <button type="button" onclick="actApprove(<?= $id ?>, 'rejected')" class="w-8 h-8 flex items-center justify-center bg-red-500 text-white rounded-full hover:bg-red-600 shadow-sm transition-all" title="Từ chối">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-white text-slate-300 flex items-center justify-center border-2 border-slate-100 transition-all">
                                    <span class="text-sm font-bold"><?= $i + 1 ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3 text-center">
                                <div class="text-xs font-bold uppercase tracking-wider <?= $cur ? 'text-blue-700' : ($past ? 'text-emerald-700' : 'text-slate-400') ?>">
                                    <?= $s['name'] ?>
                                </div>
                                <?php if ($nameStr): ?>
                                    <div class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight max-w-[120px] truncate mx-auto" title="<?= $nameStr ?>">
                                        <?= $nameStr ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($dateStr): ?>
                                    <div class="mt-0.5 text-[10px] font-mono text-slate-400">
                                        <?= $dateStr ?>
                                    </div>
                                <?php elseif ($cur): ?>
                                    <div class="mt-1 text-[10px] font-medium text-blue-500 animate-pulse">Đang chờ duyệt</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-slate-100 overflow-hidden shadow-sm">
        <?php echo form_open('admin/dashboardKpi/detaildanhgia/' . $id, array('id' => 'evaluation_employee', 'class' => 'p-6')); ?>
        <input type="hidden" name="type" id="type" value="2">
        <input type="hidden" name="add" value="1">
        <input type="hidden" name="view_detail" value="1">

        <!-- Info Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mã phiếu <span class="text-red-500">*</span></label>
                <input name="code" id="code" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:outline-none" readonly value="<?= $code ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nhân viên <span class="text-red-500">*</span></label>
                <div class="p-2 border border-slate-200 rounded bg-slate-50 font-medium text-slate-800">
                    <?= !empty($dtData['staff_name']) ? $dtData['staff_name'] : '<span class="text-slate-400">Chưa chọn nhân viên</span>' ?>
                </div>
                <input type="hidden" name="staff_id" id="staff_id" value="<?= $dtData['staff_id'] ?? '' ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Chức vụ</label>
                <input type="text" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:outline-none role" name="role" readonly value="<?= $dtData['name_role'] ?? '' ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phòng ban</label>
                <input type="text" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:outline-none room" name="room" readonly value="<?= $dtData['name_room'] ?? '' ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Đánh giá từ ngày <span class="text-red-500">*</span></label>
                <input type="date" name="date_start" id="date_start" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500" value="<?= $dtData['date_start'] ?? '' ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Đến ngày <span class="text-red-500">*</span></label>
                <input type="date" name="date_end" id="date_end" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500" value="<?= $dtData['date_end'] ?? '' ?>">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Level mục tiêu</label>
                <div class="flex flex-wrap gap-4 mt-2">
                    <?php foreach ($levelChecklist as $val): ?>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="level_target" value="<?= $val['id'] ?>" <?= (!empty($dtData) && $dtData['level_target'] == $val['id']) ? 'checked' : '' ?> class="text-violet-600 focus:ring-violet-500 w-4 h-4">
                            <?= $val['code'] ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Alert for Gate -->
        <div id="gate-alert" class="hidden mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-bold text-red-800">CẢNH BÁO</h4>
                <p class="text-xs text-red-600 mt-0.5">Bạn có tiêu chí chọn "NO". Kết luận mặc định là KHÔNG ĐẠT.</p>
            </div>
        </div>

        <!-- Section A: GATE -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-100 px-4 py-2.5 rounded-t-lg border border-slate-200 border-b-0">A. Điều kiện bắt buộc (Gate)</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-b-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs text-slate-600 uppercase border-b border-slate-200">
                            <th class="px-4 py-2 font-semibold">Điều kiện bắt buộc</th>
                            <th class="px-4 py-2 font-semibold text-center w-20">YES</th>
                            <th class="px-4 py-2 font-semibold text-center w-20">NO</th>
                            <th class="px-4 py-2 font-semibold w-64">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php if (!empty($checkList['A'])): foreach ($checkList['A'] as $val):
                                $saved = $checkListItems['A'][$val['id']] ?? null;
                                $gate = $saved['gate'] ?? '';
                        ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50 kpi-row" data-name="<?= htmlspecialchars($val['name']) ?>">
                                    <td class="px-4 py-3 text-slate-800"><?= $val['name'] ?></td>
                                    <td class="px-4 py-3 text-center"><input type="radio" <?= $gate == '1' ? 'checked' : '' ?> name="gate[<?= $val['id'] ?>]" value="1" class="gate-check text-violet-600 w-4 h-4 cursor-pointer"></td>
                                    <td class="px-4 py-3 text-center"><input type="radio" <?= $gate == '0' ? 'checked' : '' ?> name="gate[<?= $val['id'] ?>]" value="0" class="gate-check text-red-600 w-4 h-4 cursor-pointer"></td>
                                    <td class="px-4 py-3"><input type="text" name="note_a[<?= $val['id'] ?>]" value="<?= $saved['note'] ?? '' ?>" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded focus:outline-none focus:border-violet-500"></td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section B: KPI -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-100 px-4 py-2.5 rounded-t-lg border border-slate-200 border-b-0">B. Đánh giá KPI</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-b-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs text-slate-600 uppercase border-b border-slate-200">
                            <th class="px-4 py-2 font-semibold">Tiêu chí</th>
                            <th class="px-4 py-2 font-semibold w-40">Chuẩn</th>
                            <th class="px-4 py-2 font-semibold w-32 text-center">Thực tế (%)</th>
                            <th class="px-4 py-2 font-semibold w-40 text-center">Điểm</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php $totalPointB = 0;
                        if (!empty($checkList['B'])): foreach ($checkList['B'] as $val):
                                $totalPointB += $val['point'];
                                $saved = $checkListItems['B'][$val['id']] ?? null;
                        ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50 kpi-row" data-name="<?= htmlspecialchars($val['name']) ?>" data-condition="<?= htmlspecialchars($val['conditions']) ?>">
                                    <td class="px-4 py-3 text-slate-800"><?= $val['name'] ?></td>
                                    <td class="px-4 py-3 text-slate-600 text-xs"><?= $val['conditions'] ?> <?= $val['prefix'] ?></td>
                                    <td class="px-4 py-3"><input type="number" step="any" name="percent_b[<?= $val['id'] ?>]" value="<?= $saved['percent'] ?? '' ?>" placeholder="%" class="w-full px-2 py-1.5 text-xs text-center border border-slate-200 rounded focus:outline-none focus:border-violet-500"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center bg-slate-50 border border-slate-200 rounded overflow-hidden">
                                            <input type="number" step="any" name="point_b[<?= $val['id'] ?>]" value="<?= $saved['point'] ?? '' ?>" max="<?= $val['point'] ?>" placeholder="0" class="w-full px-2 py-1.5 text-xs text-center font-bold focus:outline-none calc-b">
                                            <span class="px-2 text-xs text-slate-500 bg-slate-100 border-l border-slate-200">/<?= $val['point'] ?></span>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                        <tr class="bg-slate-50 font-bold">
                            <td colspan="3" class="px-4 py-3 text-right text-slate-700">Tổng điểm Phần B:</td>
                            <td class="px-4 py-3 text-center text-violet-700 text-base"><span id="total_b"><?= $dtData['point_b'] ?? 0 ?></span> / <?= $totalPointB ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section C: Compliance -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-100 px-4 py-2.5 rounded-t-lg border border-slate-200 border-b-0">C. Tuân thủ</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-b-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs text-slate-600 uppercase border-b border-slate-200">
                            <th class="px-4 py-2 font-semibold">Nội dung</th>
                            <th class="px-4 py-2 font-semibold w-40">Chuẩn</th>
                            <th class="px-4 py-2 font-semibold w-32 text-center">Thực tế</th>
                            <th class="px-4 py-2 font-semibold w-40 text-center">Điểm</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php $totalPointC = 0;
                        if (!empty($checkList['C'])): foreach ($checkList['C'] as $val):
                                $totalPointC += $val['point'];
                                $saved = $checkListItems['C'][$val['id']] ?? null;
                        ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50 compliance-row" data-name="<?= htmlspecialchars($val['name']) ?>" data-condition="<?= htmlspecialchars($val['conditions']) ?>">
                                    <td class="px-4 py-3 text-slate-800"><?= $val['name'] ?></td>
                                    <td class="px-4 py-3 text-slate-600 text-xs"><?= $val['conditions'] ?> <?= $val['prefix'] ?></td>
                                    <td class="px-4 py-3"><input type="number" step="any" name="percent_c[<?= $val['id'] ?>]" value="<?= $saved['percent'] ?? '' ?>" class="w-full px-2 py-1.5 text-xs text-center border border-slate-200 rounded focus:outline-none focus:border-violet-500"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center bg-slate-50 border border-slate-200 rounded overflow-hidden">
                                            <input type="number" step="any" name="point_c[<?= $val['id'] ?>]" value="<?= $saved['point'] ?? '' ?>" max="<?= $val['point'] ?>" placeholder="0" class="w-full px-2 py-1.5 text-xs text-center font-bold focus:outline-none calc-c">
                                            <span class="px-2 text-xs text-slate-500 bg-slate-100 border-l border-slate-200">/<?= $val['point'] ?></span>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                        <tr class="bg-slate-50 font-bold">
                            <td colspan="3" class="px-4 py-3 text-right text-slate-700">Tổng điểm Phần C:</td>
                            <td class="px-4 py-3 text-center text-violet-700 text-base"><span id="total_c"><?= $dtData['point_c'] ?? 0 ?></span> / <?= $totalPointC ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Section D: Competency -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-100 px-4 py-2.5 rounded-t-lg border border-slate-200 border-b-0">D. Năng lực</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-b-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs text-slate-600 uppercase border-b border-slate-200">
                            <th class="px-4 py-2 font-semibold">Tiêu chí đánh giá</th>
                            <th class="px-4 py-2 font-semibold w-40 text-center">Điểm (Max 5)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php $totalPointD = 0;
                        if (!empty($checkList['D'])): foreach ($checkList['D'] as $val):
                                $totalPointD += $val['point'];
                                $saved = $checkListItems['D'][$val['id']] ?? null;
                        ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50 kpi-row" data-name="<?= htmlspecialchars($val['name']) ?>">
                                    <td class="px-4 py-3 text-slate-800"><?= $val['name'] ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center bg-slate-50 border border-slate-200 rounded overflow-hidden">
                                            <input type="number" step="any" name="point_d[<?= $val['id'] ?>]" value="<?= $saved['point'] ?? '' ?>" max="<?= $val['point'] ?>" placeholder="0" class="w-full px-2 py-1.5 text-xs text-center font-bold focus:outline-none calc-d">
                                            <span class="px-2 text-xs text-slate-500 bg-slate-100 border-l border-slate-200">/<?= $val['point'] ?></span>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                        <tr class="bg-slate-50 font-bold">
                            <td class="px-4 py-3 text-right text-slate-700">Tổng điểm Phần D:</td>
                            <td class="px-4 py-3 text-center text-violet-700 text-base"><span id="total_d"><?= $dtData['point_d'] ?? 0 ?></span> / <?= $totalPointD ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary & Conclusion -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Level Achieved -->
            <div>
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-100 px-4 py-2.5 rounded-t-lg border border-slate-200 border-b-0">E. Level Đạt Được</h3>
                <div class="p-4 border border-slate-200 rounded-b-lg bg-white">
                    <div class="grid grid-cols-2 gap-4">
                        <?php foreach ($levelChecklist as $val): ?>
                            <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded hover:bg-slate-50 border border-transparent hover:border-slate-200 transition">
                                <input type="radio" name="level_achieved" value="<?= $val['id'] ?>" <?= (!empty($dtData) && $dtData['level_achieved'] == $val['id']) ? 'checked' : '' ?> class="text-violet-600 focus:ring-violet-500 w-4 h-4">
                                <span class="font-semibold text-slate-700"><?= $val['code'] ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Final Decision -->
            <div>
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-100 px-4 py-2.5 rounded-t-lg border border-slate-200 border-b-0">F. Tổng hợp & Kết luận</h3>
                <div class="p-5 border border-slate-200 rounded-b-lg bg-white h-[calc(100%-41px)] flex flex-col items-center justify-center text-center">
                    <div class="text-sm text-slate-500 font-medium uppercase tracking-wider mb-2">Tổng điểm đạt được</div>
                    <div class="text-4xl font-black text-violet-600 mb-2">
                        <span id="grand_total"><?= $dtData['point'] ?? 0 ?></span><span class="text-xl text-slate-400"> / <?= $totalPointB + $totalPointC + $totalPointD ?></span>
                    </div>

                    <div id="classification-box" class="w-full mt-4 p-4 rounded-xl border-2 border-slate-100 transition-colors duration-300">
                        <div class="text-center bg-white bg-opacity-50 p-3 rounded-lg border border-slate-100 shadow-sm">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kết quả KPI</div>
                            <div id="kpi-proposal-container" class="space-y-1">
                                <!-- JS will populate this based on score -->
                            </div>
                            <input type="hidden" name="final_decision" id="hidden_final_decision" value="<?= $dtData['rating_list'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="<?= site_url('admin/DashboardKpi/index/phieu_danh_gia') ?>" class="px-5 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition">Hủy</a>
            <button type="submit" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition shadow-sm add">
                <i data-lucide="save" class="w-4 h-4"></i> Lưu Phiếu Đánh Giá
            </button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<style>
    .status-pass {
        background-color: #ecfdf5 !important;
        border-color: #10b981 !important;
        color: #047857 !important;
    }

    .status-extend {
        background-color: #fffbeb !important;
        border-color: #f59e0b !important;
        color: #b45309 !important;
    }

    .status-fail {
        background-color: #fef2f2 !important;
        border-color: #ef4444 !important;
        color: #b91c1c !important;
    }

    .status-pass #classification-text {
        color: #047857;
    }

    .status-extend #classification-text {
        color: #b45309;
    }

    .status-fail #classification-text {
        color: #b91c1c;
    }
</style>

<script>
    const site = window.site || {
        base_url: '<?= base_url() ?>'
    };
    const resultChecklist = <?= json_encode($resultChecklist) ?>;

    $(document).ready(function() {
        lucide.createIcons();

        // Tự động lấy dữ liệu thực tế khi vào trang
        const btnFetch = document.getElementById('btn-fetch-real-data');
        if (btnFetch) {
            setTimeout(() => {
                fetchRealData(btnFetch);
            }, 500);
        }

        // Select2 setup removed as per request - using hidden input and text display

        $(document).on('change', '#staff_id', function(event) {
            event.preventDefault();
            var data = $(this).select2('data');
            if (data) {
                $(".role").val(data.name_role || '');
                $(".room").val(data.name_room || '');
            }
        });

        // Form submission
        $('#evaluation_employee').on('submit', function(e) {
            e.preventDefault();
            $('.add').prop('disabled', true).html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang lưu...');
            lucide.createIcons();

            var form = $(this);
            var formData = new FormData(form[0]);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.result) {
                        alert_float('success', res.message || 'Lưu thành công!');
                        setTimeout(() => window.location.href = site.base_url + 'admin/DashboardKpi/index/phieu_danh_gia', 1000);
                    } else {
                        alert_float('danger', res.message || 'Có lỗi xảy ra!');
                        resetBtn();
                    }
                },
                error: function() {
                    alert_float('danger', 'Lỗi kết nối máy chủ!');
                    resetBtn();
                }
            });
        });

        function resetBtn() {
            $('.add').prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4"></i> Lưu Phiếu Đánh Giá');
            lucide.createIcons();
        }
    });

    function resetBtn() {
        $('.add').prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4"></i> Lưu Phiếu Đánh Giá');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Gate check
    $('.gate-check').change(function() {
        checkGate();
    });

    function checkGate() {
        let hasNo = false;
        $('input.gate-check[value="0"]').each(function() {
            if ($(this).is(':checked')) hasNo = true;
        });

        if (hasNo) {
            $('#gate-alert').removeClass('hidden');
            forceFail();
        } else {
            $('#gate-alert').addClass('hidden');
            calculateTotal();
        }
    }

    // Points calculation
    $(document).on('input', '.calc-b, .calc-c, .calc-d', function() {
        let max = parseFloat($(this).attr('max')) || 0;
        let val = parseFloat($(this).val());

        if (!isNaN(val)) {
            if (val > max) $(this).val(max);
            if (val < 0) $(this).val(0);
        }
        calculateTotal();
    });

    $(document).on('input', 'input[name^="percent_b"], input[name^="percent_c"]', function() {
        let percent = parseFloat($(this).val()) || 0;
        let row = $(this).closest('tr');
        let pointInput = row.find('.calc-b, .calc-c');
        let maxPoint = parseFloat(pointInput.attr('max')) || 0;

        let conditionAttr = row.data('condition') || '';
        let nameAttr = row.data('name');
        if (!nameAttr) return;
        let name = nameAttr.toLowerCase();

        let calculatedPoint = 0;
        if (name.includes('chất lượng') || name.includes('qa') || name.includes('hoàn thành công việc') || name.includes('tuân thủ nội quy')) {
            calculatedPoint = Math.round((percent * maxPoint / 100) * 10) / 10;
        } else if (name.includes('tuân thủ sop') || name.includes('hồ sơ') || name.includes('báo cáo')) {
            // Tuân thủ SOP, Hồ sơ & báo cáo đầy đủ: thực tế = 0 → full điểm
            if (percent == 0) calculatedPoint = maxPoint;
            else {
                let std = parseInt(conditionAttr) || 0;
                if (percent <= std) calculatedPoint = maxPoint;
                else calculatedPoint = 0;
            }
        } else {
            // Kỷ luật, vi phạm, lặp lại... (các lỗi đếm được)
            let std = parseInt(conditionAttr) || 0;
            if (percent <= std) calculatedPoint = maxPoint;
            else calculatedPoint = 0;
        }

        if (maxPoint > 0) {
            pointInput.val(calculatedPoint);
            calculateTotal();
        }
    });

    function calculateTotal() {
        let hasNo = false;
        $('input.gate-check[value="0"]').each(function() {
            if ($(this).is(':checked')) hasNo = true;
        });

        if (hasNo) {
            forceFail();
        }

        let totalB = 0,
            totalC = 0,
            totalD = 0;
        $('.calc-b').each(function() {
            totalB += parseFloat($(this).val()) || 0;
        });
        $('.calc-c').each(function() {
            totalC += parseFloat($(this).val()) || 0;
        });
        $('.calc-d').each(function() {
            totalD += parseFloat($(this).val()) || 0;
        });

        totalB = Math.round(totalB * 10) / 10;
        totalC = Math.round(totalC * 10) / 10;
        totalD = Math.round(totalD * 10) / 10;

        $('#total_b').text(totalB);
        $('#total_c').text(totalC);
        $('#total_d').text(totalD);

        let grandTotal = Math.round((totalB + totalC + totalD) * 10) / 10;
        $('#grand_total').text(grandTotal);

        if (!hasNo) {
            updateClassification(grandTotal);
        }
    }

    function updateClassification(score) {
        let box = $('#classification-box');

        box.removeClass('status-pass status-extend status-fail');

        let matched = resultChecklist.find(item => {
            let start = parseFloat(item.point_start);
            let end = parseFloat(item.point_end);
            return score >= start && score <= end;
        });

        if (matched) {
            const statusMap = {
                3: 'status-pass',
                2: 'status-extend',
                1: 'status-fail'
            };
            if (statusMap[matched.id]) {
                box.addClass(statusMap[matched.id]);
            }
            $('#hidden_final_decision').val(matched.id);
        } else {
            $('#hidden_final_decision').val('');
        }

        // Cập nhật text Kết quả KPI dựa trên điểm
        let proposalHtml = '-';
        var p = parseFloat(score);
        if (!isNaN(p)) {
            // Logic KPI (type=2)
            if (p < 75) proposalHtml = '<div class="text-base leading-tight text-red-600 font-bold uppercase">Kém</div><div class="text-sm text-red-500 leading-tight mt-1">Xem xét lại</div>';
            else if (p >= 75 && p < 80) proposalHtml = '<div class="text-base leading-tight text-orange-600 font-bold uppercase">Tối Thiểu</div><div class="text-sm text-orange-500 leading-tight mt-1">Cảnh báo, đào tạo lại ngay</div>';
            else if (p >= 80 && p < 90) proposalHtml = '<div class="text-base leading-tight text-blue-600 font-bold uppercase">Đạt</div><div class="text-sm text-blue-500 leading-tight mt-1">Duy trì</div>';
            else if (p >= 90 && p <= 100) proposalHtml = '<div class="text-base leading-tight text-emerald-600 font-bold uppercase">Tốt</div><div class="text-sm text-emerald-500 leading-tight mt-1">Đánh giá P3, đào tạo nâng cấp</div>';
            else if (p > 100) proposalHtml = '<div class="text-base leading-tight text-purple-600 font-bold uppercase">Xuất sắc</div><div class="text-sm text-purple-500 leading-tight mt-1">Đánh giá P3, đào tạo thăng chức</div>';
        }
        $('#kpi-proposal-container').html(proposalHtml);
    }

    function forceFail() {
        let box = $('#classification-box');
        box.removeClass('status-pass status-extend').addClass('status-fail');

        let failItem = resultChecklist.find(item => item.name.toLowerCase().includes('chấm dứt') || item.id == 1);
        if (failItem) {
            $('#hidden_final_decision').val(failItem.id);
        }

        let proposalHtml = '<div class="text-base leading-tight text-red-600 font-bold uppercase">Kém</div><div class="text-sm text-red-500 leading-tight mt-1">Xem xét lại (Vi phạm Gate)</div>';
        $('#kpi-proposal-container').html(proposalHtml);
    }

    // Helper functions from framework
    function ajaxSelectCallBack(element, url, id, text = '') {
        if (!$(element).length) return;

        const evalType = $('#type').val() || 1;

        // Set value first if provided
        if (id) {
            $(element).val(id);
        }

        $(element).select2({
            width: '100%',
            initSelection: function(el, callback) {
                var initialId = $(el).val();
                if (initialId) {
                    if (text) {
                        callback({
                            id: initialId,
                            text: text
                        });
                    } else {
                        var ajaxUrl = site.base_url + (site.base_url.endsWith('/') ? '' : '/') + url + '/' + initialId + '/' + evalType;
                        $.ajax({
                            type: "get",
                            async: false,
                            url: ajaxUrl,
                            dataType: "json",
                            success: function(data) {
                                if (data && data.row) {
                                    callback(data.row);
                                }
                            }
                        });
                    }
                }
            },
            ajax: {
                url: site.base_url + (site.base_url.endsWith('/') ? '' : '/') + url,
                dataType: 'json',
                quietMillis: 15,
                data: function(term) {
                    return {
                        term: term,
                        type: evalType,
                        type_staff: 1,
                        limit: 50
                    };
                },
                results: function(data) {
                    return data.results ? {
                        results: data.results
                    } : {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        });
    }

    // Initial calc
    calculateTotal();

    var PD_CSRF = '<?= $this->security->get_csrf_token_name() ?>';
    var PD_HASH = '<?= $this->security->get_csrf_hash() ?>';
    var PD_BASE = '<?= site_url('admin/dashboardKpi/') ?>';

    function printInIframe(url) {
        var iframe = document.getElementById('print-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-iframe';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url;
    }

    function actApprove(id, action) {
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

    function confirmApprove() {
        var fd = new FormData();
        fd.append('approval_id', document.getElementById('pd-approval-id').value);
        fd.append('action', document.getElementById('pd-action').value);
        fd.append('note', document.getElementById('pd-note').value);
        fd.append(PD_CSRF, PD_HASH);
        var btn = document.getElementById('pd-confirm-btn');
        btn.innerHTML = 'Đang xử lý...';
        btn.disabled = true;
        fetch(PD_BASE + 'approve_probationary', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            })
            .then(function(r) {
                return r.json()
            }).then(function(res) {
                if (res.success) {
                    alert_float('success', 'Thao tác thành công');
                    location.reload();
                } else {
                    alert_float('danger', res.message || 'Lỗi!');
                    btn.innerHTML = 'Xác nhận';
                    btn.disabled = false;
                }
            }).catch(function() {
                alert_float('danger', 'Lỗi mạng');
                btn.innerHTML = 'Xác nhận';
                btn.disabled = false;
            });
    }

    // --- AUTO FETCH DATA ---
    function fetchRealData(btn) {
        const staffId = $('#staff_id').val();
        const dateStart = $('input[name="date_start"]').val();
        const dateEnd = $('input[name="date_end"]').val();

        if (!staffId || !dateStart || !dateEnd) {
            alert_float('warning', 'Vui lòng chọn nhân viên và khoảng thời gian (Từ ngày - Đến ngày) trước khi lấy dữ liệu.');
            return;
        }

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="refresh-cw" class="w-3.5 h-3.5 animate-spin"></i> Đang lấy...';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const postData = {
            staff_id: staffId,
            date_start: dateStart,
            date_end: dateEnd
        };
        postData[PD_CSRF] = PD_HASH;

        $.ajax({
            url: PD_BASE + 'ajax_get_real_stats',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                try {
                    if (res.success) {
                        const d = res.data;

                        // Fill Section B (KPI)
                        $('.kpi-row').each(function() {
                            const nameAttr = $(this).data('name');
                            const conditionAttr = $(this).data('condition');
                            if (!nameAttr) return;
                            const name = nameAttr.toLowerCase();
                            const input = $(this).find('input[name^="percent_b"]');
                            if (!input.length) return;

                            if (name.includes('hoàn thành công việc')) {
                                input.val(d.task_percent).trigger('input');
                            } else if (name.includes('chất lượng') || name.includes('qa')) {
                                input.val(d.qa_percent).trigger('input');
                            } else if (name.includes('tuân thủ sop') || name.includes('hồ sơ') || name.includes('báo cáo')) {
                                input.val(0).trigger('input'); // Thực tế 0 → full điểm
                            }
                        });

                        // Fill Section C (Compliance)
                        $('.compliance-row').each(function() {
                            const nameAttr = $(this).data('name');
                            const conditionAttr = $(this).data('condition');
                            if (!nameAttr) return;
                            const name = nameAttr.toLowerCase();
                            const input = $(this).find('input[name^="percent_c"]');
                            if (!input.length) return;

                            if (name.includes('tuân thủ nội quy')) {
                                input.val(100).trigger('input');
                            } else if (name.includes('lặp lại')) {
                                input.val(d.count_lap_lai).trigger('input');
                            } else if (name.includes('kỷ luật') || name.includes('vi phạm')) {
                                input.val(d.count_vp).trigger('input');
                            } else {
                                input.val(0).trigger('input');
                            }
                        });

                        // Fill Section D (Năng lực)
                        $('.kpi-row').each(function() {
                            const input = $(this).find('input[name^="point_d"]');
                            if (!input.length) return;
                            const max = parseFloat(input.attr('max')) || 0;
                            input.val(max).trigger('input');
                        });

                        alert_float('success', 'Đã cập nhật dữ liệu thực tế từ hệ thống.');
                        calculateTotal(); // Recalculate points
                    } else {
                        alert_float('danger', res.message);
                    }
                } catch (e) {
                    console.error('Lỗi xử lý dữ liệu KPI:', e);
                    alert_float('danger', 'Có lỗi xảy ra khi xử lý dữ liệu.');
                }
            },
            complete: function() {
                // Đảm bảo nút luôn được reset
                $(btn).prop('disabled', false);
                $(btn).html(originalHtml);
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }
</script>

<!-- Note modal -->
<div id="pd-note-modal" class="modal-backdrop hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center">
    <div class="modal-box bg-white rounded-2xl shadow-xl w-full mx-4" style="max-width:420px">
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
                <button id="pd-confirm-btn" onclick="confirmApprove()" class="px-4 py-2 text-sm text-white rounded-lg font-medium shadow-sm transition-colors">Xác nhận</button>
            </div>
        </div>
    </div>
</div>