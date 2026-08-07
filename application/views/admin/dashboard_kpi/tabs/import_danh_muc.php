<?php defined('BASEPATH') or exit('No direct script access allowed');
$config = isset($import_config) ? $import_config : ['title'=>'Import','table'=>'','fields'=>[]];
$tab    = isset($active_tab) ? $active_tab : '';
$tabLabel = ['import_phong_ban'=>'Phòng ban','import_khach_hang'=>'Khách hàng','import_ncc'=>'Nhà cung cấp','import_thiet_bi'=>'Thiết bị'];
$tabIcon  = ['import_phong_ban'=>'building-2','import_khach_hang'=>'users','import_ncc'=>'truck','import_thiet_bi'=>'monitor'];
$icon = $tabIcon[$tab] ?? 'upload';
$label = $tabLabel[$tab] ?? $config['title'];
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Import <?= $label ?></h2>
        <p class="text-sm text-slate-500">Nhập dữ liệu <?= $label ?> từ file Excel vào hệ thống</p>
    </div>
    <a href="#" onclick="document.getElementById('idc-tpl-link').click(); return false;"
        class="flex items-center gap-2 px-4 py-2 bg-slate-700 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors">
        <i data-lucide="download" class="w-4 h-4"></i> Tải mẫu Excel
    </a>
</div>

<!-- Drop zone -->
<div class="bg-white rounded-xl border-2 border-dashed border-slate-200 p-10 mb-5 text-center hover:border-blue-400 transition-colors"
     id="idc-dropzone"
     ondragover="event.preventDefault();this.classList.add('border-blue-400','bg-blue-50')"
     ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
     ondrop="handleIdcDrop(event)">
    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i data-lucide="<?= $icon ?>" class="w-8 h-8 text-blue-500"></i>
    </div>
    <div class="text-slate-700 font-semibold mb-1">Kéo thả file Excel vào đây</div>
    <div class="text-slate-400 text-sm mb-4">Hỗ trợ định dạng .xlsx, .xls, .csv</div>
    <button onclick="document.getElementById('idc-file-input').click()"
        class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
        Chọn file
    </button>
    <input type="file" id="idc-file-input" accept=".xlsx,.xls,.csv" class="hidden" onchange="handleIdcFile(this.files[0])">
    <a id="idc-tpl-link" href="<?= site_url('admin/DashboardKpi/download_import_template/'.$tab) ?>" class="hidden"></a>
</div>

<div id="idc-preview-area" class="hidden">
    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center">
        <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="file-check" class="w-8 h-8"></i>
        </div>
        <h3 id="idc-file-info" class="text-lg font-bold text-slate-900 mb-1">File đã sẵn sàng</h3>
        <p id="idc-row-count" class="text-slate-500 text-sm mb-6">0 dòng dữ liệu tìm thấy</p>
        
        <div class="flex justify-center gap-3">
            <button onclick="submitIdcImport()" id="idc-import-btn"
                class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-2">
                <i data-lucide="zap" class="w-5 h-5"></i> Xác nhận nhập dữ liệu
            </button>
            <button onclick="location.reload()" class="px-6 py-3 bg-slate-100 text-slate-600 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                Hủy
            </button>
        </div>
    </div>
<div id="idc-result" class="px-5 pb-4 mt-4 hidden"></div>
</div>

<script>
var IDC_CSRF  = '<?= $this->security->get_csrf_token_name() ?>';
var IDC_HASH  = '<?= $this->security->get_csrf_hash() ?>';
var IDC_TAB   = '<?= $tab ?>';

// Map tab to legacy endpoint for processing
var IDC_ENDPOINTS = {
    'import_phong_ban':  '<?= admin_url('kpi/import_list_criteria_department') ?>',
    'import_khach_hang': '<?= admin_url('kpi_targets_clients/excel_import') ?>',
    'import_ncc':        '<?= admin_url('kpi_targets_supplier/excel_import') ?>',
    'import_thiet_bi':   '<?= admin_url('kpi_equipment_stage/excel_import') ?>',
    'department_budget': '<?= admin_url('department_budget/import_excel') ?>'
};

var IDC_FILE = null;

function handleIdcDrop(e) {
    e.preventDefault();
    document.getElementById('idc-dropzone').classList.remove('border-blue-400','bg-blue-50');
    var file = e.dataTransfer.files[0];
    if (file) handleIdcFile(file);
}

function handleIdcFile(file) {
    if (!file) return;
    IDC_FILE = file;
    
    document.getElementById('idc-dropzone').classList.add('hidden');
    document.getElementById('idc-preview-area').classList.remove('hidden');
    document.getElementById('idc-file-info').textContent = file.name;
    document.getElementById('idc-row-count').textContent = (file.size / 1024).toFixed(1) + ' KB';
    lucide.createIcons();
}

function submitIdcImport() {
    if (!IDC_FILE) return;
    var btn = document.getElementById('idc-import-btn');
    var resDiv = document.getElementById('idc-result');
    
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang xử lý...';
    lucide.createIcons();
    
    var fd = new FormData();
    fd.append('file', IDC_FILE);
    fd.append(IDC_CSRF, IDC_HASH);
    
    // Pass department_id if needed (for criteria)
    if (IDC_TAB === 'import_phong_ban') {
        var deptSelect = document.getElementById('pb-dept-select');
        if (deptSelect) fd.append('department_id', deptSelect.value);
    }

    fetch(IDC_ENDPOINTS[IDC_TAB], {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res => {
        resDiv.classList.remove('hidden');
        if (res.success) {
            resDiv.innerHTML = '<div class="p-6 bg-emerald-50 border-2 border-emerald-200 text-emerald-800 rounded-2xl flex flex-col items-center text-center gap-2 shadow-sm animate-bounce-short">' +
                               '<div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-1"><i data-lucide="check" class="w-6 h-6"></i></div>' +
                               '<div class="text-lg font-bold">Import Thành Công!</div>' +
                               '<div class="text-sm opacity-90">' + (res.message || 'Dữ liệu đã được cập nhật vào hệ thống.') + '</div>' +
                               '<div class="mt-4 text-[10px] text-emerald-500 uppercase tracking-widest font-bold">Trang sẽ tự tải lại sau 3 giây...</div></div>';
            
            // Cuộn xuống để thấy thông báo
            resDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => { location.reload(); }, 3000);
        } else {
            resDiv.innerHTML = '<div class="p-5 bg-rose-50 border-2 border-rose-100 text-rose-700 rounded-2xl flex items-center gap-4 shadow-sm">' +
                               '<div class="w-10 h-10 bg-rose-100 text-rose-500 rounded-full flex items-center justify-center flex-shrink-0"><i data-lucide="alert-circle" class="w-5 h-5"></i></div>' +
                               '<div><div class="font-bold">Có lỗi xảy ra</div><div class="text-sm">' + (res.message || 'Vui lòng kiểm tra lại định dạng file hoặc dữ liệu.') + '</div></div></div>';
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="zap" class="w-5 h-5"></i> Thử lại ngay';
        }
        lucide.createIcons();
    })
    .catch(err => {
        resDiv.classList.remove('hidden');
        resDiv.innerHTML = '<div class="p-5 bg-amber-50 border-2 border-amber-100 text-amber-700 rounded-2xl flex items-center gap-4">' +
                           '<div class="w-10 h-10 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center flex-shrink-0"><i data-lucide="help-circle" class="w-5 h-5"></i></div>' +
                           '<div><div class="font-bold">Thông báo hệ thống</div><div class="text-sm">Yêu cầu đã được gửi. Nếu trang không tự tải lại, vui lòng nhấn F5.</div></div></div>';
        btn.disabled = false;
        btn.innerHTML = 'Thử lại';
        lucide.createIcons();
    });
}
</script>
