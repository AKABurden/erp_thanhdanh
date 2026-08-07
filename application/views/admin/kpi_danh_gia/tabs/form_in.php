<?php
// views/admin/kpi_danh_gia/tabs/form_in.php
?>
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">In Phiếu Đánh giá</h2>
        <p class="text-sm text-slate-500">Chọn phiếu để xem và in kết quả</p>
    </div>
    <?php if (!empty($selected)): ?>
    <button onclick="printPreviewOnly()"
        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors no-print">
        <i data-lucide="printer" class="w-4 h-4"></i> In phiếu
    </button>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Danh sách phiếu -->
    <div class="lg:col-span-1 no-print">
        <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="fi-search" placeholder="Tìm theo tên, mã NV..." oninput="filterFormIn()"
                        class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="divide-y divide-slate-50 max-h-[calc(100vh-260px)] overflow-y-auto">
                <?php if (!empty($danh_gia_list)): foreach ($danh_gia_list as $dg):
                    $isSelected = !empty($selected) && $selected['id'] == $dg['id'];
                    $qd = $dg['quyet_dinh'];
                    $qcls = $qd === 'ĐẠT' ? 'text-emerald-600' : ($qd === 'GIÁM SÁT' ? 'text-amber-600' : 'text-red-600');
                ?>
                <a href="<?= site_url('admin/KpiDanhGia/index/form_in?id=' . $dg['id']) ?>"
                   class="fi-item flex items-center justify-between p-4 hover:bg-slate-50 transition-colors <?= $isSelected ? 'bg-blue-50' : '' ?>"
                   data-search="<?= strtolower(($dg['ho_ten'] ?? '') . ' ' . ($dg['ma_nhan_vien'] ?? '')) ?>">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-text" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                        <div>
                            <div class="text-sm font-medium text-slate-800"><?= htmlspecialchars($dg['ho_ten'] ?? '-') ?></div>
                            <div class="text-xs text-slate-500"><?= htmlspecialchars($dg['loai_danh_gia']) ?> — <?= htmlspecialchars($dg['ky_danh_gia']) ?></div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <div class="text-sm font-bold text-slate-800"><?= number_format($dg['tong_diem'], 2) ?>đ</div>
                        <div class="text-xs font-semibold <?= $qcls ?>"><?= $qd ?></div>
                    </div>
                </a>
                <?php endforeach; else: ?>
                <div class="p-8 text-center text-slate-400 text-sm">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Preview phiếu in -->
    <div class="lg:col-span-2">
        <?php if (!empty($selected)): $s = $selected; ?>
        <div class="bg-white rounded-xl border border-slate-100 p-8 print-area" id="print-preview-area">
            <div class="max-w-xl mx-auto">
                <!-- Tiêu đề -->
                <div class="text-center border-b-2 border-slate-900 pb-4 mb-6">
                    <h1 class="text-xl font-bold text-slate-900">PHIẾU ĐÁNH GIÁ KPI NHÂN SỰ</h1>
                    <p class="text-sm text-slate-600 mt-1">Theo Vòng Đời — Gate 1 đến Gate 5</p>
                </div>

                <!-- I. Thông tin chung -->
                <div class="mb-6">
                    <h2 class="font-semibold text-slate-900 mb-3 bg-slate-100 px-3 py-2 rounded text-sm">I. THÔNG TIN CHUNG</h2>
                    <div class="grid grid-cols-2 gap-4 px-3 text-sm">
                        <?php
                        $info = [
                            'Họ tên'        => $s['ho_ten']       ?? '-',
                            'Mã nhân viên'  => $s['ma_nhan_vien'] ?? '-',
                            'Chức vụ'       => $s['chuc_vu']      ?? '-',
                            'Phòng ban'     => $s['ten_phong_ban']?? '-',
                            'Loại đánh giá' => $s['loai_danh_gia'],
                            'Kỳ đánh giá'  => $s['ky_danh_gia'],
                        ];
                        foreach ($info as $label => $val): ?>
                        <div>
                            <p class="text-slate-500"><?= $label ?></p>
                            <p class="font-medium text-slate-900"><?= htmlspecialchars($val) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- II. Gate 1 -->
                <div class="mb-6">
                    <h2 class="font-semibold text-slate-900 mb-3 bg-slate-100 px-3 py-2 rounded text-sm">II. GATE 1 — HỒ SƠ & ĐIỀU KIỆN ĐẦU VÀO</h2>
                    <div class="grid grid-cols-3 gap-3 px-3">
                        <?php
                        $gates1 = [
                            'Hồ sơ đầy đủ'     => $s['ho_so_day_du'],
                            'Training completed' => $s['training_completed'],
                            'SOP compliance'     => $s['sop_compliance'],
                        ];
                        foreach ($gates1 as $lbl => $val): ?>
                        <div class="text-center p-3 bg-slate-50 rounded text-sm">
                            <p class="text-xs text-slate-500 mb-1"><?= $lbl ?></p>
                            <p class="font-bold <?= $val ? 'text-green-600' : 'text-red-600' ?>"><?= $val ? 'YES' : 'NO' ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="px-4 py-1.5 rounded-lg text-sm font-bold <?= $s['gate_1_result'] === 'PASS' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            Kết quả Gate 1: <?= $s['gate_1_result'] ?>
                        </span>
                    </div>
                </div>

                <!-- III. Kết quả đánh giá -->
                <div class="mb-6">
                    <h2 class="font-semibold text-slate-900 mb-3 bg-slate-100 px-3 py-2 rounded text-sm">III. KẾT QUẢ ĐÁNH GIÁ</h2>
                    <div class="space-y-3 px-3">
                        <?php
                        $gates = [
                            ['label'=>'Gate 2 — P2 KPI Final', 'raw'=>$s['p2_raw'], 'final'=>$s['p2_final'], 'max'=>60, 'bg'=>'bg-blue-50', 'txt'=>'text-blue-600'],
                            ['label'=>'Gate 3 — Compliance',    'raw'=>$s['compliance_raw'], 'final'=>$s['compliance_final'], 'max'=>20, 'bg'=>'bg-amber-50', 'txt'=>'text-amber-600'],
                            ['label'=>'Gate 4 — P3 Contribution','raw'=>$s['p3_raw'], 'final'=>$s['p3_final'], 'max'=>20, 'bg'=>'bg-emerald-50', 'txt'=>'text-emerald-600'],
                        ];
                        foreach ($gates as $g): ?>
                        <div class="flex items-center justify-between p-3 <?= $g['bg'] ?> rounded-lg text-sm">
                            <div>
                                <p class="text-slate-700"><?= $g['label'] ?></p>
                                <p class="text-xs text-slate-500">Raw: <?= number_format($g['raw'], 2) ?></p>
                            </div>
                            <p class="text-xl font-bold <?= $g['txt'] ?>"><?= number_format($g['final'], 2) ?> / <?= $g['max'] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- IV. Gate 5 kết quả cuối -->
                <div class="mb-6">
                    <h2 class="font-semibold text-slate-900 mb-3 bg-slate-100 px-3 py-2 rounded text-sm">IV. GATE 5 — KẾT QUẢ CUỐI</h2>
                    <div class="rounded-lg p-5 text-center grid grid-cols-4 gap-4 border border-slate-100 bg-gradient-to-br from-blue-50 via-white to-cyan-50">
                        <div class="rounded-lg p-3 bg-white/70 shadow-sm border border-white/70">
                            <p class="text-xs text-slate-500 mb-1">Tổng điểm</p>
                            <p class="text-3xl font-bold text-blue-700"><?= number_format($s['tong_diem'], 2) ?></p>
                        </div>
                        <div class="rounded-lg p-3 bg-white/70 shadow-sm border border-white/70">
                            <p class="text-xs text-slate-500 mb-1">Xếp loại</p>
                            <p class="text-base font-bold text-slate-800"><?= htmlspecialchars($s['xep_loai']) ?></p>
                        </div>
                        <div class="rounded-lg p-3 bg-white/70 shadow-sm border border-white/70">
                            <p class="text-xs text-slate-500 mb-1">Quyết định</p>
                            <p class="text-base font-bold text-slate-800"><?= htmlspecialchars($s['quyet_dinh']) ?></p>
                        </div>
                        <div class="rounded-lg p-3 bg-white/70 shadow-sm border border-white/70">
                            <p class="text-xs text-slate-500 mb-1">Risk Level</p>
                            <p class="text-base font-bold text-slate-800"><?= htmlspecialchars($s['risk_level']) ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($s['ghi_chu'])): ?>
                <div class="mb-6">
                    <h2 class="font-semibold text-slate-900 mb-3 bg-slate-100 px-3 py-2 rounded text-sm">V. GHI CHÚ</h2>
                    <p class="text-sm text-slate-700 px-3"><?= htmlspecialchars($s['ghi_chu']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Footer phiếu -->
                <div class="border-t border-slate-200 pt-4 flex justify-between text-xs text-slate-400">
                    <span>Ngày tạo: <?= date('d/m/Y', strtotime($s['created_at'])) ?></span>
                    <span>Ngày in: <?= date('d/m/Y') ?></span>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-xl border border-slate-100 p-16 text-center text-slate-400">
            <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-3 text-slate-200"></i>
            <p>Chọn một phiếu từ danh sách bên trái để xem và in</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
@page {
    size: A4 portrait;
    margin: 10mm;
}

@media print {
    html, body {
        width: 210mm;
        height: auto;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    body * { visibility: hidden !important; }
    #print-preview-area, #print-preview-area * { visibility: visible !important; }

    #print-preview-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 190mm !important;
        max-width: 190mm !important;
        margin: 0 auto !important;
        padding: 0 !important;
    }

    .no-print { display: none !important; }

    .print-area {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
    }

    .print-area * {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>

<script>
function printPreviewOnly() {
    var area = document.getElementById('print-preview-area');
    if (!area) return;
    window.print();
}

function filterFormIn() {
    const q = document.getElementById('fi-search').value.toLowerCase();
    document.querySelectorAll('.fi-item').forEach(el => {
        el.style.display = !q || el.dataset.search.includes(q) ? '' : 'none';
    });
}
</script>
