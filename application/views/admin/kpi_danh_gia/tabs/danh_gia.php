<?php
// views/admin/kpi_danh_gia/tabs/danh_gia.php
?>
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Đánh giá KPI</h2>
        <p class="text-sm text-slate-500">Form đánh giá theo vòng đời Gate 1 → Gate 5</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('dg-import-modal').classList.remove('hidden'); lucide.createIcons();"
            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
            <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
        </button>
        <button onclick="openDanhGiaForm()"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Tạo đánh giá mới
        </button>
    </div>
</div>

<!-- MODAL: Import Excel XLSX -->
<div id="dg-import-modal" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width:780px">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="upload" class="w-4 h-4 text-blue-600"></i> Import dữ liệu Đánh giá KPI từ Excel
            </h3>
            <button onclick="document.getElementById('dg-import-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5 space-y-4">
            <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-800 flex items-start justify-between gap-4">
                <div>
                    <div class="font-medium">Tải file mẫu trước khi import</div>
                    <div class="text-xs text-blue-700 mt-1">File mẫu có đúng cấu trúc cột để nhập nhanh dữ liệu.</div>
                </div>
                <a href="<?= site_url('admin/KpiDanhGia/download_template_danh_gia') ?>"
                   class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Tải file mẫu
                </a>
            </div>

            <div class="border-2 border-dashed border-slate-200 rounded-lg p-6 text-center relative hover:border-blue-400 transition-colors">
                <input type="file" id="dg-xlsx-file" accept=".xlsx,.xls"
                    onchange="readXlsxDG(this)"
                    class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                <i data-lucide="file-spreadsheet" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                <p class="text-sm text-slate-500">Kéo thả hoặc <span class="text-blue-600 font-medium">chọn file Excel</span></p>
                <p id="dg-filename" class="text-xs text-slate-400 mt-1"></p>
            </div>

            <div id="dg-preview-wrap" class="hidden">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium text-slate-700">
                        Xem trước: <span id="dg-row-count" class="font-bold text-blue-600">0</span> dòng
                    </p>
                    <button onclick="submitImportDG(event)"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        <i data-lucide="cloud-upload" class="w-4 h-4"></i> Xác nhận Import
                    </button>
                </div>
                <div class="overflow-auto max-h-64 rounded-lg border border-slate-100">
                    <table>
                        <thead id="dg-preview-head"></thead>
                        <tbody id="dg-preview-body"></tbody>
                    </table>
                </div>
                <div id="dg-import-result" class="mt-3 text-sm"></div>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="relative">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="dg-search" placeholder="Tìm theo tên, mã nhân viên, loại đánh giá..." oninput="filterDanhGia()"
            class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>Nhân sự</th>
                    <th>Loại đánh giá</th>
                    <th>Kỳ đánh giá</th>
                    <th>Gate 1</th>
                    <th>P2</th>
                    <th>Compliance</th>
                    <th>P3</th>
                    <th>Tổng điểm</th>
                    <th>Xếp loại</th>
                    <th>Quyết định</th>
                    <th class="text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($danh_gia_list)): foreach ($danh_gia_list as $dg): ?>
                <tr class="dg-row"
                    data-search="<?= strtolower(($dg['ho_ten'] ?? '') . ' ' . ($dg['ma_nhan_vien'] ?? '') . ' ' . $dg['loai_danh_gia']) ?>">
                    <td>
                        <div class="font-medium text-slate-900"><?= htmlspecialchars($dg['ho_ten'] ?? '-') ?></div>
                        <div class="text-xs text-slate-500"><?= htmlspecialchars($dg['ma_nhan_vien'] ?? '') ?></div>
                    </td>
                    <td class="text-slate-600 text-xs"><?= htmlspecialchars($dg['loai_danh_gia']) ?></td>
                    <td class="font-mono text-xs text-slate-700"><?= htmlspecialchars($dg['ky_danh_gia']) ?></td>
                    <td>
                        <span class="px-2 py-0.5 rounded text-xs font-medium <?= $dg['gate_1_result'] === 'PASS' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $dg['gate_1_result'] ?>
                        </span>
                    </td>
                    <td class="text-slate-700"><?= number_format($dg['p2_final'], 2) ?></td>
                    <td class="text-slate-700"><?= number_format($dg['compliance_final'], 2) ?></td>
                    <td class="text-slate-700"><?= number_format($dg['p3_final'], 2) ?></td>
                    <td class="font-bold text-slate-900"><?= number_format($dg['tong_diem'], 2) ?></td>
                    <td class="text-slate-600 text-xs"><?= htmlspecialchars($dg['xep_loai']) ?></td>
                    <td>
                        <?php
                        $qd = $dg['quyet_dinh'];
                        $cls = $qd === 'ĐẠT' ? 'badge-dat' : ($qd === 'GIÁM SÁT' ? 'badge-giam-sat' : 'badge-fail');
                        ?>
                        <span class="px-2 py-0.5 rounded text-xs font-bold <?= $cls ?>"><?= $qd ?></span>
                    </td>
                    <td class="text-right">
                        <button onclick='viewDanhGia(<?= json_encode($dg) ?>)' class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="Xem chi tiết">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deleteDanhGia(<?= $dg['id'] ?>)" class="p-1.5 text-slate-400 hover:text-red-600 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="11" class="text-center text-slate-400 py-10">Chưa có dữ liệu đánh giá</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: Tạo đánh giá (Gate 1 → 5) -->
<div id="dg-modal" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width:680px">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">Form Đánh giá KPI — Gate 1 đến Gate 5</h3>
            <button onclick="document.getElementById('dg-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="dg-form" onsubmit="submitDanhGia(event)" class="p-5 space-y-5">
            <input type="hidden" id="dg-id" name="id" value="0">

            <!-- Nhân sự & kỳ -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nhân sự <span class="text-red-500">*</span></label>
                    <select name="nhan_su_id" id="dg-nhan_su_id" required
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Chọn nhân sự</option>
                        <?php foreach ($nhan_su_list as $ns): ?>
                        <option value="<?= $ns['id'] ?>"><?= htmlspecialchars($ns['ho_ten']) ?> — <?= $ns['ma_nhan_vien'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Loại đánh giá <span class="text-red-500">*</span></label>
                    <select name="loai_danh_gia" id="dg-loai_danh_gia" required
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach (['KPI tuần','KPI tháng','KPI 3 tháng','KPI 6 tháng','KPI năm'] as $loai): ?>
                        <option value="<?= $loai ?>"><?= $loai ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kỳ đánh giá <span class="text-red-500">*</span></label>
                    <input type="text" name="ky_danh_gia" id="dg-ky_danh_gia" placeholder="VD: 2024-M01" required
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Gate 1 -->
            <div class="bg-slate-50 rounded-lg p-4">
                <h4 class="font-semibold text-slate-800 mb-3 text-sm">Gate 1 — Hồ sơ & Điều kiện đầu vào</h4>
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" name="ho_so_day_du" id="dg-ho_so_day_du" value="1" checked
                            class="w-4 h-4 text-blue-600 rounded" onchange="calcGate1()">
                        Hồ sơ đầy đủ
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" name="training_completed" id="dg-training_completed" value="1" checked
                            class="w-4 h-4 text-blue-600 rounded" onchange="calcGate1()">
                        Training completed
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" name="sop_compliance" id="dg-sop_compliance" value="1" checked
                            class="w-4 h-4 text-blue-600 rounded" onchange="calcGate1()">
                        SOP compliance
                    </label>
                </div>
                <div class="mt-3 flex items-center gap-2 text-sm">
                    <span class="text-slate-600">Kết quả Gate 1:</span>
                    <span id="gate1-badge" class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">PASS</span>
                </div>
            </div>

            <!-- Gate 2, 3, 4 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-slate-800 mb-2 text-sm">Gate 2 — P2 KPI (60đ)</h4>
                    <label class="block text-xs text-slate-600 mb-1">P2 Raw (0–100)</label>
                    <input type="number" name="p2_raw" id="dg-p2_raw" value="0" min="0" max="100" step="0.1"
                        oninput="calcScore()"
                        class="w-full px-3 py-2 text-sm border border-slate-200 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="mt-2 text-xs text-slate-500">P2 Final: <span id="p2-final" class="font-bold text-blue-600">0.00</span>/60</div>
                </div>
                <div class="bg-amber-50 rounded-lg p-4">
                    <h4 class="font-semibold text-slate-800 mb-2 text-sm">Gate 3 — Compliance (20đ)</h4>
                    <label class="block text-xs text-slate-600 mb-1">Compliance Raw (0–100)</label>
                    <input type="number" name="compliance_raw" id="dg-compliance_raw" value="0" min="0" max="100" step="0.1"
                        oninput="calcScore()"
                        class="w-full px-3 py-2 text-sm border border-slate-200 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="mt-2 text-xs text-slate-500">Compliance Final: <span id="comp-final" class="font-bold text-amber-600">0.00</span>/20</div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4">
                    <h4 class="font-semibold text-slate-800 mb-2 text-sm">Gate 4 — P3 (20đ)</h4>
                    <label class="block text-xs text-slate-600 mb-1">P3 Raw (0–100)</label>
                    <input type="number" name="p3_raw" id="dg-p3_raw" value="0" min="0" max="100" step="0.1"
                        oninput="calcScore()"
                        class="w-full px-3 py-2 text-sm border border-slate-200 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="mt-2 text-xs text-slate-500">P3 Final: <span id="p3-final" class="font-bold text-emerald-600">0.00</span>/20</div>
                </div>
            </div>

            <!-- Gate 5 (live preview) -->
            <div class="rounded-xl p-4 border border-blue-100 bg-gradient-to-br from-blue-50 via-sky-50 to-indigo-50">
                <h4 class="font-semibold mb-3 text-sm text-slate-800">Gate 5 — Tổng điểm & Xếp loại</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
                    <div class="rounded-lg p-3 bg-white/70 border border-white shadow-sm">
                        <div class="text-xs text-slate-500 mb-1">Tổng điểm</div>
                        <div id="preview-tong" class="text-2xl font-bold text-blue-700">0.00</div>
                    </div>
                    <div class="rounded-lg p-3 bg-white/70 border border-white shadow-sm">
                        <div class="text-xs text-slate-500 mb-1">Xếp loại</div>
                        <div id="preview-xep-loai" class="text-base font-bold text-slate-800">Không đạt</div>
                    </div>
                    <div class="rounded-lg p-3 bg-white/70 border border-white shadow-sm">
                        <div class="text-xs text-slate-500 mb-1">Quyết định</div>
                        <div id="preview-quyet-dinh" class="text-base font-bold text-rose-600">FAIL</div>
                    </div>
                    <div class="rounded-lg p-3 bg-white/70 border border-white shadow-sm">
                        <div class="text-xs text-slate-500 mb-1">Risk Level</div>
                        <div id="preview-risk" class="text-base font-bold text-amber-600">High</div>
                    </div>
                </div>
            </div>

            <!-- Ghi chú -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Ghi chú</label>
                <textarea name="ghi_chu" id="dg-ghi_chu" rows="2"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('dg-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">Hủy</button>
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i data-lucide="calculator" class="w-4 h-4"></i> Tính & Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Xem chi tiết -->
<div id="dg-view-modal" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width:600px">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">Chi tiết Đánh giá KPI</h3>
            <button onclick="document.getElementById('dg-view-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="dg-view-body" class="p-5 space-y-4 text-sm"></div>
    </div>
</div>

<script>
const DG_CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
const DG_CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

function filterDanhGia() {
    const q = document.getElementById('dg-search').value.toLowerCase();
    document.querySelectorAll('.dg-row').forEach(r => { r.style.display = !q || r.dataset.search.includes(q) ? '' : 'none'; });
}

// Live calculation
function calcGate1() {
    const pass = document.getElementById('dg-ho_so_day_du').checked
              && document.getElementById('dg-training_completed').checked
              && document.getElementById('dg-sop_compliance').checked;
    const badge = document.getElementById('gate1-badge');
    badge.textContent = pass ? 'PASS' : 'FAIL';
    badge.className = 'px-2 py-0.5 rounded text-xs font-bold ' + (pass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
    calcScore();
}

function calcScore() {
    const p2   = Math.min(60, Math.max(0, parseFloat(document.getElementById('dg-p2_raw').value||0) * 0.6));
    const comp = Math.min(20, Math.max(0, parseFloat(document.getElementById('dg-compliance_raw').value||0) * 0.2));
    const p3   = Math.min(20, Math.max(0, parseFloat(document.getElementById('dg-p3_raw').value||0) * 0.2));
    const tot  = p2 + comp + p3;
    const gate1Pass = document.getElementById('gate1-badge').textContent === 'PASS';

    document.getElementById('p2-final').textContent   = p2.toFixed(2);
    document.getElementById('comp-final').textContent = comp.toFixed(2);
    document.getElementById('p3-final').textContent   = p3.toFixed(2);
    document.getElementById('preview-tong').textContent = tot.toFixed(2);

    let xl = 'Không đạt', qd = 'FAIL', risk = 'High';
    if (tot >= 90) xl = 'Xuất sắc'; else if (tot >= 80) xl = 'Tốt'; else if (tot >= 70) xl = 'Đạt'; else if (tot >= 60) xl = 'Cần giám sát';
    if (!gate1Pass) qd = 'FAIL'; else if (tot >= 70) qd = 'ĐẠT'; else if (tot >= 60) qd = 'GIÁM SÁT';
    if (tot >= 80) risk = 'Low'; else if (tot >= 60) risk = 'Medium';

    document.getElementById('preview-xep-loai').textContent  = xl;
    document.getElementById('preview-quyet-dinh').textContent = qd;
    document.getElementById('preview-risk').textContent      = risk;
}

function openDanhGiaForm() {
    document.getElementById('dg-id').value = '0';
    ['p2_raw','compliance_raw','p3_raw'].forEach(f => document.getElementById('dg-' + f).value = '0');
    document.getElementById('dg-ghi_chu').value = '';
    ['ho_so_day_du','training_completed','sop_compliance'].forEach(f => document.getElementById('dg-' + f).checked = true);
    calcGate1(); calcScore();
    document.getElementById('dg-modal').classList.remove('hidden');
    lucide.createIcons();
}

function submitDanhGia(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('dg-form'));
    // Send checkbox values correctly
    ['ho_so_day_du','training_completed','sop_compliance'].forEach(f => {
        fd.set(f, document.getElementById('dg-' + f).checked ? 1 : 0);
    });
    fd.set(DG_CSRF_NAME, DG_CSRF_HASH);
    fetch('<?= site_url('admin/KpiDanhGia/save_danh_gia') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
        .then(r => r.json()).then(res => { if (res.success) location.reload(); else alert(res.message); });
}

var dgParsedRows = [];

function readXlsxDG(input) {
    var file = input.files[0];
    if (!file) return;
    document.getElementById('dg-filename').textContent = file.name;

    var reader = new FileReader();
    reader.onload = function(e) {
        var wb  = XLSX.read(e.target.result, { type: 'binary' });
        var ws  = wb.Sheets[wb.SheetNames[0]];
        var raw = XLSX.utils.sheet_to_json(ws, { defval: '' });
        dgParsedRows = raw;
        renderPreviewDG(raw);
    };
    reader.readAsBinaryString(file);
}

function renderPreviewDG(rows) {
    if (!rows.length) { document.getElementById('dg-preview-wrap').classList.add('hidden'); return; }

    var cols = Object.keys(rows[0]);
    document.getElementById('dg-row-count').textContent = rows.length;
    document.getElementById('dg-preview-head').innerHTML = '<tr>' + cols.map(function(c) { return '<th>' + c + '</th>'; }).join('') + '</tr>';
    document.getElementById('dg-preview-body').innerHTML = rows.slice(0, 5).map(function(r) {
        return '<tr>' + cols.map(function(c) {
            return '<td class="text-xs text-slate-600 max-w-xs truncate">' + (r[c] ?? '') + '</td>';
        }).join('') + '</tr>';
    }).join('') + (rows.length > 5 ? '<tr><td colspan="' + cols.length + '" class="text-center text-xs text-slate-400 py-2">... và ' + (rows.length - 5) + ' dòng nữa</td></tr>' : '');
    document.getElementById('dg-preview-wrap').classList.remove('hidden');
    lucide.createIcons();
}

function submitImportDG(e) {
    e.preventDefault();
    if (!dgParsedRows.length) return;

    var btn = e.target.closest('button');
    var old = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Đang import...';

    var fd = new FormData();
    fd.append('rows', JSON.stringify(dgParsedRows));
    fd.append(DG_CSRF_NAME, DG_CSRF_HASH);

    fetch('<?= site_url('admin/KpiDanhGia/bulk_import_danh_gia') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        var el = document.getElementById('dg-import-result');
        if (res.success) {
            el.innerHTML = '<div class="p-3 bg-green-50 text-green-700 rounded-lg">' + res.message + '</div>';
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            el.innerHTML = '<div class="p-3 bg-red-50 text-red-700 rounded-lg">' + res.message + '</div>';
            btn.disabled = false;
            btn.innerHTML = old;
        }
    });
}

function deleteDanhGia(id) {
    if (!confirm('Xoá đánh giá này?')) return;
    const fd = new FormData(); fd.append('id', id);
    fetch('<?= site_url('admin/KpiDanhGia/delete_danh_gia') ?>', { method: 'POST', body: fd })
        .then(r => r.json()).then(res => { if (res.success) location.reload(); else alert(res.message); });
}

function viewDanhGia(d) {
    const rl = d.risk_level === 'Low' ? 'badge-low' : (d.risk_level === 'Medium' ? 'badge-medium' : 'badge-high');
    const qd = d.quyet_dinh === 'ĐẠT' ? 'badge-dat' : (d.quyet_dinh === 'GIÁM SÁT' ? 'badge-giam-sat' : 'badge-fail');
    document.getElementById('dg-view-body').innerHTML = `
        <div class="grid grid-cols-2 gap-3">
            <div><span class="text-slate-500">Họ tên:</span><div class="font-medium">${d.ho_ten || '-'}</div></div>
            <div><span class="text-slate-500">Mã NV:</span><div class="font-mono">${d.ma_nhan_vien || '-'}</div></div>
            <div><span class="text-slate-500">Chức vụ:</span><div>${d.chuc_vu || '-'}</div></div>
            <div><span class="text-slate-500">Phòng ban:</span><div>${d.ten_phong_ban || '-'}</div></div>
            <div><span class="text-slate-500">Loại đánh giá:</span><div>${d.loai_danh_gia}</div></div>
            <div><span class="text-slate-500">Kỳ đánh giá:</span><div class="font-mono">${d.ky_danh_gia}</div></div>
        </div>
        <hr class="border-slate-100">
        <div>
            <div class="font-semibold text-slate-700 mb-2">Gate 1</div>
            <div class="flex gap-4 text-xs">
                <span class="px-2 py-1 rounded ${d.ho_so_day_du ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">Hồ sơ: ${d.ho_so_day_du ? 'YES' : 'NO'}</span>
                <span class="px-2 py-1 rounded ${d.training_completed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">Training: ${d.training_completed ? 'YES' : 'NO'}</span>
                <span class="px-2 py-1 rounded ${d.sop_compliance ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">SOP: ${d.sop_compliance ? 'YES' : 'NO'}</span>
                <span class="px-2 py-1 rounded font-bold ${d.gate_1_result === 'PASS' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${d.gate_1_result}</span>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3 text-sm">
            <div class="bg-blue-50 rounded-lg p-3 text-center"><div class="text-xs text-slate-500 mb-1">P2 Final</div><div class="text-lg font-bold text-blue-600">${parseFloat(d.p2_final).toFixed(2)}/60</div></div>
            <div class="bg-amber-50 rounded-lg p-3 text-center"><div class="text-xs text-slate-500 mb-1">Compliance Final</div><div class="text-lg font-bold text-amber-600">${parseFloat(d.compliance_final).toFixed(2)}/20</div></div>
            <div class="bg-emerald-50 rounded-lg p-3 text-center"><div class="text-xs text-slate-500 mb-1">P3 Final</div><div class="text-lg font-bold text-emerald-600">${parseFloat(d.p3_final).toFixed(2)}/20</div></div>
        </div>
        <div class="rounded-lg p-4 text-center grid grid-cols-4 gap-3 border border-slate-100 bg-gradient-to-br from-slate-50 to-blue-50">
            <div class="rounded-lg p-3 bg-white/80 border border-slate-100 shadow-sm">
                <div class="text-xs text-slate-500">Tổng điểm</div>
                <div class="text-2xl font-bold text-slate-900">${parseFloat(d.tong_diem).toFixed(2)}</div>
            </div>
            <div class="rounded-lg p-3 bg-white/80 border border-slate-100 shadow-sm">
                <div class="text-xs text-slate-500">Xếp loại</div>
                <div class="font-bold text-slate-800">${d.xep_loai}</div>
            </div>
            <div class="rounded-lg p-3 bg-white/80 border border-slate-100 shadow-sm">
                <div class="text-xs text-slate-500">Quyết định</div>
                <div class="font-bold"><span class="px-2 py-0.5 rounded text-xs ${qd}">${d.quyet_dinh}</span></div>
            </div>
            <div class="rounded-lg p-3 bg-white/80 border border-slate-100 shadow-sm">
                <div class="text-xs text-slate-500">Risk</div>
                <div class="font-bold"><span class="px-2 py-0.5 rounded text-xs ${rl}">${d.risk_level}</span></div>
            </div>
        </div>
        ${d.ghi_chu ? '<div class="bg-slate-50 rounded-lg p-3 text-slate-600 text-xs">' + d.ghi_chu + '</div>' : ''}`;
    document.getElementById('dg-view-modal').classList.remove('hidden');
    lucide.createIcons();
}
</script>
