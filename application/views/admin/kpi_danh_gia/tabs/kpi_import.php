<?php
// views/admin/kpi_danh_gia/tabs/kpi_import.php
?>
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">KPI Import</h2>
        <p class="text-sm text-slate-500">Dữ liệu nguồn KPI cho phòng ban / vị trí / công việc</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('import-modal').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
            <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
        </button>
        <button onclick="openKpiImportForm()"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Thêm mới
        </button>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-4">
    <div class="flex flex-col md:flex-row gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="ki-search" placeholder="Tìm theo mục tiêu, mã công việc..." oninput="filterKpiImport()"
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <select id="ki-filter-muc-tieu" onchange="filterKpiImport()"
            class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả mục tiêu</option>
            <?php if (!empty($unique_muc_tieu)): foreach ($unique_muc_tieu as $mt): ?>
            <option value="<?= htmlspecialchars($mt['muc_tieu_kpi']) ?>"><?= htmlspecialchars($mt['muc_tieu_kpi']) ?></option>
            <?php endforeach; endif; ?>
        </select>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>Mục tiêu KPI</th>
                    <th>Phòng ban</th>
                    <th>Chức vụ</th>
                    <th>Mã công việc</th>
                    <th>Điểm chuẩn</th>
                    <th>Loại KPI</th>
                    <th class="text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody id="ki-tbody">
                <?php if (!empty($kpi_import_list)): foreach ($kpi_import_list as $ki): ?>
                <tr class="ki-row"
                    data-search="<?= strtolower($ki['muc_tieu_kpi'] . ' ' . ($ki['ma_cong_viec'] ?? '') . ' ' . ($ki['ten_cong_viec'] ?? '')) ?>"
                    data-muc="<?= htmlspecialchars($ki['muc_tieu_kpi']) ?>">
                    <td class="font-medium text-slate-800 max-w-xs truncate"><?= htmlspecialchars($ki['muc_tieu_kpi']) ?></td>
                    <td class="text-slate-600"><?= htmlspecialchars($ki['ten_phong_ban']) ?></td>
                    <td class="text-slate-600"><?= htmlspecialchars($ki['chuc_vu']) ?></td>
                    <td class="font-mono text-xs text-slate-500"><?= htmlspecialchars($ki['ma_cong_viec'] ?? '-') ?></td>
                    <td class="font-semibold text-slate-900"><?= number_format($ki['diem_chuan'], 0) ?></td>
                    <td>
                        <span class="px-2 py-0.5 rounded text-xs font-medium <?= $ki['loai_kpi'] === 'P2' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' ?>">
                            <?= $ki['loai_kpi'] ?>
                        </span>
                    </td>
                    <td class="text-right">
                        <button onclick='openKpiImportForm(<?= json_encode($ki) ?>)' class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deleteKpiImport(<?= $ki['id'] ?>)" class="p-1.5 text-slate-400 hover:text-red-600 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="7" class="text-center text-slate-400 py-10">Chưa có dữ liệu KPI Import</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: Form thêm/sửa -->
<div id="ki-modal" class="modal-backdrop hidden">
    <div class="modal-box">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 id="ki-modal-title" class="font-semibold text-slate-900">Thêm KPI Import</h3>
            <button onclick="closeKiModal()" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form id="ki-form" onsubmit="submitKiForm(event)" class="p-5 space-y-4">
            <input type="hidden" id="ki-id" name="id" value="0">
            <div class="grid grid-cols-2 gap-4">
                <?php
                $fields = [
                    ['name'=>'ma_phong_ban','label'=>'Mã phòng ban','req'=>true],
                    ['name'=>'ten_phong_ban','label'=>'Tên phòng ban','req'=>true],
                    ['name'=>'muc_tieu_kpi','label'=>'Mục tiêu KPI','req'=>true,'full'=>true],
                    ['name'=>'ma_vi_tri','label'=>'Mã vị trí','req'=>true],
                    ['name'=>'chuc_vu','label'=>'Chức vụ','req'=>true],
                    ['name'=>'ma_cong_viec','label'=>'Mã công việc'],
                    ['name'=>'ten_cong_viec','label'=>'Tên công việc'],
                    ['name'=>'ma_vi_pham','label'=>'Mã vi phạm'],
                    ['name'=>'loai_vi_pham','label'=>'Loại vi phạm'],
                ];
                foreach ($fields as $f): ?>
                <div class="<?= !empty($f['full']) ? 'col-span-2' : '' ?>">
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        <?= $f['label'] ?><?= !empty($f['req']) ? ' <span class="text-red-500">*</span>' : '' ?>
                    </label>
                    <input type="text" name="<?= $f['name'] ?>" id="ki-<?= $f['name'] ?>"
                        <?= !empty($f['req']) ? 'required' : '' ?>
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <?php endforeach; ?>
                <?php
                $nums = [
                    ['name'=>'diem_chuan','label'=>'Điểm chuẩn','default'=>'100'],
                    ['name'=>'diem_sau_xu_ly','label'=>'Điểm sau xử lý','default'=>'100'],
                    ['name'=>'kpi_tien_chuan','label'=>'KPI tiền chuẩn','default'=>'0'],
                    ['name'=>'kpi_tien_thuc_nhan','label'=>'KPI tiền thực nhận','default'=>'0'],
                    ['name'=>'ty_le_huong_kpi','label'=>'Tỷ lệ hưởng KPI','default'=>'1'],
                ];
                foreach ($nums as $n): ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1"><?= $n['label'] ?></label>
                    <input type="number" name="<?= $n['name'] ?>" id="ki-<?= $n['name'] ?>" value="<?= $n['default'] ?>" step="0.01" min="0"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <?php endforeach; ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Loại KPI</label>
                    <select name="loai_kpi" id="ki-loai_kpi"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="P2">P2</option>
                        <option value="P3">P3</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeKiModal()"
                    class="px-4 py-2 text-sm text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Hủy</button>
                <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Import Excel XLSX -->
<div id="import-modal" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width:780px">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="upload" class="w-4 h-4 text-blue-600"></i> Import dữ liệu từ Excel
            </h3>
            <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5 space-y-4">
            <!-- Hướng dẫn + tải mẫu -->
            <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-800 flex items-start justify-between gap-4">
                <a href="<?= site_url('admin/KpiDanhGia/download_template_kpi') ?>"
                   class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Tải file mẫu
                </a>
            </div>

            <!-- Chọn file -->
            <div class="border-2 border-dashed border-slate-200 rounded-lg p-6 text-center relative hover:border-blue-400 transition-colors">
                <input type="file" id="xlsx-file-ki" accept=".xlsx,.xls"
                    onchange="readXlsxKI(this)"
                    class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                <i data-lucide="file-spreadsheet" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                <p class="text-sm text-slate-500">Kéo thả hoặc <span class="text-blue-600 font-medium">chọn file Excel</span></p>
                <p id="ki-filename" class="text-xs text-slate-400 mt-1"></p>
            </div>

            <!-- Preview table -->
            <div id="ki-preview-wrap" class="hidden">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium text-slate-700">
                        Xem trước: <span id="ki-row-count" class="font-bold text-blue-600">0</span> dòng
                    </p>
                    <button onclick="submitImportKI()"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        <i data-lucide="cloud-upload" class="w-4 h-4"></i> Xác nhận Import
                    </button>
                </div>
                <div class="overflow-auto max-h-64 rounded-lg border border-slate-100">
                    <table>
                        <thead id="ki-preview-head"></thead>
                        <tbody id="ki-preview-body"></tbody>
                    </table>
                </div>
                <div id="ki-import-result" class="mt-3 text-sm"></div>
            </div>
        </div>
    </div>
</div>

<!-- SheetJS CDN -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
const KI_CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
const KI_CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

var kiParsedRows = [];

function readXlsxKI(input) {
    var file = input.files[0];
    if (!file) return;
    document.getElementById('ki-filename').textContent = file.name;

    var reader = new FileReader();
    reader.onload = function(e) {
        var wb  = XLSX.read(e.target.result, { type: 'binary' });
        var ws  = wb.Sheets[wb.SheetNames[0]];
        var raw = XLSX.utils.sheet_to_json(ws, { defval: '' });

        kiParsedRows = raw;
        renderPreviewKI(raw);
    };
    reader.readAsBinaryString(file);
}

function renderPreviewKI(rows) {
    if (!rows.length) { document.getElementById('ki-preview-wrap').classList.add('hidden'); return; }

    var cols = Object.keys(rows[0]);
    document.getElementById('ki-row-count').textContent = rows.length;

    // Header
    document.getElementById('ki-preview-head').innerHTML =
        '<tr>' + cols.map(function(c) { return '<th>' + c + '</th>'; }).join('') + '</tr>';

    // Body (max 5 rows preview)
    document.getElementById('ki-preview-body').innerHTML = rows.slice(0, 5).map(function(r) {
        return '<tr>' + cols.map(function(c) {
            return '<td class="text-xs text-slate-600 max-w-xs truncate">' + (r[c] ?? '') + '</td>';
        }).join('') + '</tr>';
    }).join('') + (rows.length > 5 ? '<tr><td colspan="' + cols.length + '" class="text-center text-xs text-slate-400 py-2">... và ' + (rows.length - 5) + ' dòng nữa</td></tr>' : '');

    document.getElementById('ki-preview-wrap').classList.remove('hidden');
    lucide.createIcons();
}

function submitImportKI() {
    if (!kiParsedRows.length) return;

    var btn = event.target.closest('button');
    btn.disabled = true; btn.textContent = 'Đang import...';

    var fd = new FormData();
    fd.append('rows', JSON.stringify(kiParsedRows));
    fd.append(KI_CSRF_NAME, KI_CSRF_HASH);

    fetch('<?= site_url('admin/KpiDanhGia/bulk_import_kpi') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        var el = document.getElementById('ki-import-result');
        if (res.success) {
            el.innerHTML = '<div class="p-3 bg-green-50 text-green-700 rounded-lg">' + res.message + '</div>';
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            el.innerHTML = '<div class="p-3 bg-red-50 text-red-700 rounded-lg">' + res.message + '</div>';
            btn.disabled = false; btn.textContent = 'Xác nhận Import';
        }
    });
}
</script>


<script>
function filterKpiImport() {
    const q   = document.getElementById('ki-search').value.toLowerCase();
    const muc = document.getElementById('ki-filter-muc-tieu').value;
    document.querySelectorAll('.ki-row').forEach(row => {
        const matchQ   = !q   || row.dataset.search.includes(q);
        const matchMuc = !muc || row.dataset.muc === muc;
        row.style.display = (matchQ && matchMuc) ? '' : 'none';
    });
}

const kiFields = ['id','ma_phong_ban','ten_phong_ban','muc_tieu_kpi','ma_vi_tri','chuc_vu','ma_cong_viec','ten_cong_viec','ma_vi_pham','loai_vi_pham','diem_chuan','diem_sau_xu_ly','kpi_tien_chuan','kpi_tien_thuc_nhan','ty_le_huong_kpi','loai_kpi'];

function openKpiImportForm(data = null) {
    kiFields.forEach(f => { const el = document.getElementById('ki-' + f); if (el) el.value = data ? (data[f] ?? '') : (f === 'id' ? '0' : ''); });
    document.getElementById('ki-modal-title').textContent = data ? 'Cập nhật KPI Import' : 'Thêm KPI Import';
    document.getElementById('ki-modal').classList.remove('hidden');
    lucide.createIcons();
}
function closeKiModal() { document.getElementById('ki-modal').classList.add('hidden'); }

function submitKiForm(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('ki-form'));
    fd.append(KI_CSRF_NAME, KI_CSRF_HASH);
    fetch('<?= site_url('admin/KpiDanhGia/save_kpi_import') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
        .then(r => r.json()).then(res => { if (res.success) location.reload(); else alert(res.message); });
}

function deleteKpiImport(id) {
    if (!confirm('Xoá bản ghi này?')) return;
    const fd = new FormData(); fd.append('id', id); fd.append(KI_CSRF_NAME, KI_CSRF_HASH);
    fetch('<?= site_url('admin/KpiDanhGia/delete_kpi_import') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
        .then(r => r.json()).then(res => { if (res.success) location.reload(); else alert(res.message); });
}
</script>
