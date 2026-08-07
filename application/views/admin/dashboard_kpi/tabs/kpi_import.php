<?php
// views/admin/dashboardKpi/tabs/kpi_import.php
?>
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">KPI Import (Dashboard)</h2>
        <p class="text-sm text-slate-500">Quản lý dữ liệu nguồn KPI cho phòng ban, vị trí và công việc</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('dk-import-modal').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-emerald-700 hover:shadow transition-all">
            <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
        </button>
        <button onclick="openDkKpiImportForm()"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-blue-700 hover:shadow transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i> Thêm mới
        </button>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="dk-ki-search" placeholder="Tìm kiếm theo mục tiêu, mã công việc, tên công việc..." oninput="filterDkKpiImport()"
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
        </div>
        <select id="dk-ki-filter-muc-tieu" onchange="filterDkKpiImport()"
            class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow bg-white min-w-[200px]">
            <option value="">Tất cả mục tiêu KPI</option>
            <?php if (!empty($unique_muc_tieu)): foreach ($unique_muc_tieu as $mt): ?>
                    <option value="<?= htmlspecialchars($mt['muc_tieu_kpi']) ?>"><?= htmlspecialchars($mt['muc_tieu_kpi']) ?></option>
            <?php endforeach;
            endif; ?>
        </select>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-sm font-medium text-slate-600">
                    <th class="py-3 px-4">Mục tiêu KPI</th>
                    <th class="py-3 px-4">Phòng ban</th>
                    <th class="py-3 px-4">Chức vụ</th>
                    <th class="py-3 px-4">Mã công việc</th>
                    <th class="py-3 px-4">Điểm chuẩn</th>
                    <th class="py-3 px-4">Loại KPI</th>
                    <th class="py-3 px-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody id="dk-ki-tbody" class="text-sm divide-y divide-slate-100">
                <?php if (!empty($kpi_import_list)): foreach ($kpi_import_list as $ki): ?>
                        <tr class="dk-ki-row hover:bg-slate-50 transition-colors group"
                            data-search="<?= strtolower($ki['muc_tieu_kpi'] . ' ' . ($ki['ma_cong_viec'] ?? '') . ' ' . ($ki['ten_cong_viec'] ?? '')) ?>"
                            data-muc="<?= htmlspecialchars($ki['muc_tieu_kpi']) ?>">
                            <td class="py-3 px-4 font-medium text-slate-800 max-w-xs truncate" title="<?= htmlspecialchars($ki['muc_tieu_kpi']) ?>">
                                <?= htmlspecialchars($ki['muc_tieu_kpi']) ?>
                            </td>
                            <td class="py-3 px-4 text-slate-600"><?= htmlspecialchars($ki['ten_phong_ban']) ?></td>
                            <td class="py-3 px-4 text-slate-600"><?= htmlspecialchars($ki['chuc_vu']) ?></td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">
                                    <?= htmlspecialchars($ki['ma_cong_viec'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-900"><?= number_format($ki['diem_chuan'], 0) ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $ki['loai_kpi'] === 'P2' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-purple-100 text-purple-700 border border-purple-200' ?>">
                                    <?= $ki['loai_kpi'] ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick='openDkKpiImportForm(<?= json_encode($ki) ?>)' class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Sửa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="deleteDkKpiImport(<?= $ki['id'] ?>)" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-slate-500 py-12">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="database" class="w-10 h-10 text-slate-300 mb-3"></i>
                                <p>Chưa có dữ liệu KPI Import</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: Form thêm/sửa -->
<div id="dk-ki-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col transform transition-all">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 id="dk-ki-modal-title" class="text-lg font-bold text-slate-900">Thêm mới KPI</h3>
            <button onclick="closeDkKiModal()" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1.5 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto custom-scrollbar">
            <form id="dk-ki-form" onsubmit="submitDkKiForm(event)" class="space-y-5">
                <input type="hidden" id="dk-ki-id" name="id" value="0">

                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <h4 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2"><i data-lucide="info" class="w-4 h-4 text-blue-500"></i> Thông tin chung</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mục tiêu KPI <span class="text-red-500">*</span></label>
                            <input type="text" name="muc_tieu_kpi" id="dk-ki-muc_tieu_kpi" required
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mã phòng ban <span class="text-red-500">*</span></label>
                            <select name="ma_phong_ban" id="dk-ki-ma_phong_ban" required style="width: 100%;">
                                <option value="">-- Chọn phòng ban --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tên phòng ban <span class="text-red-500">*</span></label>
                            <input type="text" name="ten_phong_ban" id="dk-ki-ten_phong_ban" required readonly
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mã vị trí <span class="text-red-500">*</span></label>
                            <select name="ma_vi_tri" id="dk-ki-ma_vi_tri" required style="width: 100%;">
                                <option value="">-- Chọn mã vị trí --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Chức vụ <span class="text-red-500">*</span></label>
                            <input type="text" name="chuc_vu" id="dk-ki-chuc_vu" required readonly
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <h4 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2"><i data-lucide="briefcase" class="w-4 h-4 text-emerald-500"></i> Công việc & Vi phạm</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mã công việc</label>
                            <select name="ma_cong_viec" id="dk-ki-ma_cong_viec" style="width: 100%;">
                                <option value="">-- Chọn mã công việc --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tên công việc</label>
                            <input type="text" name="ten_cong_viec" id="dk-ki-ten_cong_viec" readonly
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mã vi phạm</label>
                            <select name="ma_vi_pham" id="dk-ki-ma_vi_pham" style="width: 100%;">
                                <option value="">-- Chọn mã vi phạm --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Loại vi phạm</label>
                            <input type="text" name="loai_vi_pham" id="dk-ki-loai_vi_pham" readonly
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <h4 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2"><i data-lucide="calculator" class="w-4 h-4 text-purple-500"></i> Chỉ số & Tính toán</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Loại KPI</label>
                            <select name="loai_kpi" id="dk-ki-loai_kpi"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="P2">P2</option>
                                <option value="P3">P3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Điểm chuẩn</label>
                            <input type="number" name="diem_chuan" id="dk-ki-diem_chuan" value="100" step="0.01" min="0"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Điểm sau xử lý</label>
                            <input type="number" name="diem_sau_xu_ly" id="dk-ki-diem_sau_xu_ly" value="100" step="0.01" min="0"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">KPI tiền chuẩn</label>
                            <input type="number" name="kpi_tien_chuan" id="dk-ki-kpi_tien_chuan" value="0" step="0.01" min="0"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">KPI tiền thực nhận</label>
                            <input type="number" name="kpi_tien_thuc_nhan" id="dk-ki-kpi_tien_thuc_nhan" value="0" step="0.01" min="0"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tỷ lệ hưởng KPI</label>
                            <input type="number" name="ty_le_huong_kpi" id="dk-ki-ty_le_huong_kpi" value="1" step="0.01" min="0" max="100"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-5 border-t border-slate-100 flex justify-end gap-3 bg-slate-50/50 rounded-b-xl">
            <button type="button" onclick="closeDkKiModal()"
                class="px-5 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">Hủy</button>
            <button type="button" onclick="document.getElementById('dk-ki-form').requestSubmit()"
                class="px-5 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Lưu dữ liệu
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Import từ CSDL -->
<div id="dk-import-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[92vh] flex flex-col transform transition-all">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="database" class="w-5 h-5 text-blue-600"></i> Import từ Cơ sở dữ liệu
            </h3>
            <button onclick="document.getElementById('dk-import-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1.5 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto custom-scrollbar space-y-5">

            <!-- Hướng dẫn -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg shrink-0">
                    <i data-lucide="info" class="w-4 h-4"></i>
                </div>
                <div class="text-xs text-blue-700 leading-relaxed">
                    <p class="font-semibold text-blue-900 mb-0.5">Hướng dẫn</p>
                    Chọn Phòng ban &amp; Vị trí (bắt buộc). Nếu chọn Công việc hoặc Vi phạm, hệ thống sẽ tạo <strong>tổ hợp</strong> bản ghi cho từng kết hợp. Mục tiêu KPI để trống sẽ tự ghép tên Phòng ban - Vị trí.
                </div>
            </div>

            <!-- Step 1: Chọn đối tượng -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-4">
                <h4 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-blue-500"></i> Bước 1: Chọn đối tượng
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phòng ban <span class="text-red-500">*</span></label>
                        <select id="imp-db-rooms" name="room_codes[]" multiple style="width:100%">
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Vị trí / Chức vụ <span class="text-red-500">*</span></label>
                        <select id="imp-db-roles" name="role_codes[]" multiple style="width:100%">
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Công việc <span class="text-slate-400 text-xs">(tuỳ chọn)</span></label>
                        <select id="imp-db-tasks" name="task_codes[]" multiple style="width:100%">
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Vi phạm <span class="text-slate-400 text-xs">(tuỳ chọn)</span></label>
                        <select id="imp-db-viols" name="viol_codes[]" multiple style="width:100%">
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Tham số KPI -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-4">
                <h4 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-purple-500"></i> Bước 2: Tham số KPI
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mục tiêu KPI <span class="text-slate-400 text-xs">(để trống sẽ tự ghép)</span></label>
                        <input type="text" id="imp-db-muc_tieu" placeholder="VD: Hoàn thành chỉ tiêu Q2/2026..."
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Loại KPI</label>
                        <select id="imp-db-loai_kpi" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="P2">P2</option>
                            <option value="P3">P3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Điểm chuẩn</label>
                        <input type="number" id="imp-db-diem_chuan" value="100" step="0.01" min="0"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Điểm sau xử lý</label>
                        <input type="number" id="imp-db-diem_sau_xu_ly" value="100" step="0.01" min="0"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tỷ lệ hưởng KPI</label>
                        <input type="number" id="imp-db-ty_le_huong_kpi" value="1" step="0.01" min="0" max="100"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">KPI tiền chuẩn</label>
                        <input type="number" id="imp-db-kpi_tien_chuan" value="0" step="1" min="0"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">KPI tiền thực nhận</label>
                        <input type="number" id="imp-db-kpi_tien_thuc_nhan" value="0" step="1" min="0"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div id="dk-db-preview" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                <i data-lucide="layers" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <p class="text-sm text-emerald-800">
                    Sẽ tạo <strong id="dk-db-count" class="text-emerald-600 text-base">0</strong> bản ghi KPI từ tổ hợp đã chọn.
                </p>
            </div>

            <div id="dk-db-import-result" class="text-sm"></div>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-xl flex justify-between items-center gap-3">
            <button type="button" onclick="calcDkDbPreview()"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                <i data-lucide="calculator" class="w-4 h-4"></i> Tính số bản ghi
            </button>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('dk-import-modal').classList.add('hidden')"
                    class="px-5 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">Hủy</button>
                <button type="button" onclick="submitDkImportDB()" id="dk-btn-confirm-import"
                    class="flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <i data-lucide="database" class="w-4 h-4"></i> Xác nhận Import
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for inner elements */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
    const DK_KI_CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
    const DK_KI_CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

    // ── DB Import: Preview count ──────────────────────────────────────────────
    function calcDkDbPreview() {
        const rooms = $('#imp-db-rooms').val() || [];
        const roles = $('#imp-db-roles').val() || [];
        const tasks = $('#imp-db-tasks').val() || [];
        const viols = $('#imp-db-viols').val() || [];

        const r = rooms.length || 0;
        const ro = roles.length || 0;
        const t = tasks.length || 1;   // default 1 row if no task
        const v = viols.length || 1;   // default 1 row if no viol

        const count = r * ro * t * v;
        document.getElementById('dk-db-count').textContent = count;
        document.getElementById('dk-db-preview').classList.toggle('hidden', count === 0);
        lucide.createIcons();
    }

    // ── DB Import: Submit ─────────────────────────────────────────────────────
    function submitDkImportDB() {
        const rooms = $('#imp-db-rooms').val() || [];
        const roles = $('#imp-db-roles').val() || [];
        if (!rooms.length || !roles.length) {
            alert('Vui lòng chọn ít nhất 1 Phòng ban và 1 Vị trí!');
            return;
        }

        const btn = document.getElementById('dk-btn-confirm-import');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang xử lý...';
        lucide.createIcons();

        const fd = new FormData();
        rooms.forEach(v => fd.append('room_codes[]', v));
        roles.forEach(v => fd.append('role_codes[]', v));
        ($('#imp-db-tasks').val() || []).forEach(v => fd.append('task_codes[]', v));
        ($('#imp-db-viols').val() || []).forEach(v => fd.append('viol_codes[]', v));
        fd.append('muc_tieu_kpi',        document.getElementById('imp-db-muc_tieu').value);
        fd.append('loai_kpi',            document.getElementById('imp-db-loai_kpi').value);
        fd.append('diem_chuan',          document.getElementById('imp-db-diem_chuan').value);
        fd.append('diem_sau_xu_ly',      document.getElementById('imp-db-diem_sau_xu_ly').value);
        fd.append('ty_le_huong_kpi',     document.getElementById('imp-db-ty_le_huong_kpi').value);
        fd.append('kpi_tien_chuan',      document.getElementById('imp-db-kpi_tien_chuan').value);
        fd.append('kpi_tien_thuc_nhan',  document.getElementById('imp-db-kpi_tien_thuc_nhan').value);
        fd.append(DK_KI_CSRF_NAME, DK_KI_CSRF_HASH);

        fetch('<?= site_url('admin/dashboardKpi/import_from_db') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(r => r.json())
        .then(res => {
            const el = document.getElementById('dk-db-import-result');
            if (res.success) {
                el.innerHTML = '<div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i> ' + res.message + '</div>';
                lucide.createIcons();
                setTimeout(() => location.reload(), 1500);
            } else {
                el.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3"><i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i> ' + (res.message || 'Có lỗi xảy ra!') + '</div>';
                btn.disabled = false;
                btn.innerHTML = origHtml;
                lucide.createIcons();
            }
        })
        .catch(() => {
            document.getElementById('dk-db-import-result').innerHTML = '<div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3"><i data-lucide="wifi-off" class="w-5 h-5 text-red-500"></i> Lỗi kết nối mạng hoặc máy chủ!</div>';
            btn.disabled = false;
            btn.innerHTML = origHtml;
            lucide.createIcons();
        });
    }
</script>



<script>
    function filterDkKpiImport() {
        const q = document.getElementById('dk-ki-search').value.toLowerCase();
        const muc = document.getElementById('dk-ki-filter-muc-tieu').value;
        document.querySelectorAll('.dk-ki-row').forEach(row => {
            const matchQ = !q || row.dataset.search.includes(q);
            const matchMuc = !muc || row.dataset.muc === muc;
            row.style.display = (matchQ && matchMuc) ? '' : 'none';
        });
    }

    const dkKiFields = ['id', 'ma_phong_ban', 'ten_phong_ban', 'muc_tieu_kpi', 'ma_vi_tri', 'chuc_vu', 'ma_cong_viec', 'ten_cong_viec', 'ma_vi_pham', 'loai_vi_pham', 'diem_chuan', 'diem_sau_xu_ly', 'kpi_tien_chuan', 'kpi_tien_thuc_nhan', 'ty_le_huong_kpi', 'loai_kpi'];

    function openDkKpiImportForm(data = null) {
        dkKiFields.forEach(f => {
            const el = document.getElementById('dk-ki-' + f);
            if (el) {
                if (data) {
                    if (['ma_phong_ban', 'ma_vi_tri', 'ma_cong_viec', 'ma_vi_pham'].includes(f)) {
                        // Create option dynamically for select2
                        if (data[f]) {
                            let optionText = data[f];
                            if (f === 'ma_phong_ban' && data.ten_phong_ban) optionText += ' - ' + data.ten_phong_ban;
                            if (f === 'ma_vi_tri' && data.chuc_vu) optionText += ' - ' + data.chuc_vu;
                            if (f === 'ma_cong_viec' && data.ten_cong_viec) optionText += ' - ' + data.ten_cong_viec;
                            if (f === 'ma_vi_pham' && data.loai_vi_pham) optionText += ' - ' + data.loai_vi_pham;

                            if ($(el).find("option[value='" + data[f] + "']").length) {
                                $(el).val(data[f]).trigger('change.select2');
                            } else {
                                var newOption = new Option(optionText, data[f], true, true);
                                $(el).append(newOption).trigger('change.select2');
                            }
                        } else {
                            $(el).val(null).trigger('change.select2');
                        }
                    } else {
                        el.value = (data[f] !== null && data[f] !== undefined) ? data[f] : '';
                    }
                } else {
                    // Default values for new form
                    if (['ma_phong_ban', 'ma_vi_tri', 'ma_cong_viec', 'ma_vi_pham'].includes(f)) {
                        $(el).val(null).trigger('change.select2');
                    } else if (f === 'id') el.value = '0';
                    else if (f === 'diem_chuan' || f === 'diem_sau_xu_ly') el.value = '100';
                    else if (f === 'kpi_tien_chuan' || f === 'kpi_tien_thuc_nhan') el.value = '0';
                    else if (f === 'ty_le_huong_kpi') el.value = '1';
                    else if (f === 'loai_kpi') el.value = 'P2';
                    else el.value = '';
                }
            }
        });

        document.getElementById('dk-ki-modal-title').textContent = data ? 'Cập nhật thông tin KPI' : 'Thêm mới KPI Import';
        document.getElementById('dk-ki-modal').classList.remove('hidden');
        // small timeout to allow display block before triggering animation/icons
        setTimeout(() => lucide.createIcons(), 50);
    }

    function closeDkKiModal() {
        document.getElementById('dk-ki-modal').classList.add('hidden');
    }

    function submitDkKiForm(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button[type="button"][onclick*="requestSubmit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang lưu...';
            lucide.createIcons();
        }

        const fd = new FormData(document.getElementById('dk-ki-form'));
        fd.append(DK_KI_CSRF_NAME, DK_KI_CSRF_HASH);

        fetch('<?= site_url('admin/dashboardKpi/save_kpi_import') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message || 'Có lỗi xảy ra!');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Lưu dữ liệu';
                        lucide.createIcons();
                    }
                }
            })
            .catch(err => {
                alert('Lỗi kết nối mạng hoặc máy chủ!');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Lưu dữ liệu';
                    lucide.createIcons();
                }
            });
    }

    function deleteDkKpiImport(id) {
        if (!confirm('Bạn có chắc chắn muốn xoá bản ghi này? Hành động này không thể hoàn tác.')) return;

        const fd = new FormData();
        fd.append('id', id);
        fd.append(DK_KI_CSRF_NAME, DK_KI_CSRF_HASH);

        fetch('<?= site_url('admin/dashboardKpi/delete_kpi_import') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message || 'Xóa thất bại!');
                }
            })
            .catch(err => alert('Lỗi kết nối mạng hoặc máy chủ!'));
    }

    // Close modals when clicking outside
    document.addEventListener('mousedown', function(e) {
        const kiModal = document.getElementById('dk-ki-modal');
        if (!kiModal.classList.contains('hidden') && e.target === kiModal) {
            closeDkKiModal();
        }

        const importModal = document.getElementById('dk-import-modal');
        if (!importModal.classList.contains('hidden') && e.target === importModal) {
            importModal.classList.add('hidden');
        }
    });

    $(document).ready(function() {
        // Init Select2 AJAX
        function initDkKiSelect2(selector, url, nameTargetId) {
            $(selector).select2({
                ajax: {
                    url: '<?= site_url('admin/dashboardKpi/') ?>' + url,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                dropdownParent: $('#dk-ki-modal')
            }).on('select2:select', function(e) {
                var data = e.params.data;
                document.getElementById(nameTargetId).value = data.name || '';
            }).on('select2:unselect', function(e) {
                document.getElementById(nameTargetId).value = '';
            });
        }

        initDkKiSelect2('#dk-ki-ma_phong_ban', 'ajax_search_rooms', 'dk-ki-ten_phong_ban');
        initDkKiSelect2('#dk-ki-ma_vi_tri', 'ajax_search_roles', 'dk-ki-chuc_vu');
        initDkKiSelect2('#dk-ki-ma_cong_viec', 'ajax_search_tasks', 'dk-ki-ten_cong_viec');
        initDkKiSelect2('#dk-ki-ma_vi_pham', 'ajax_search_violations', 'dk-ki-loai_vi_pham');

        // ── Init Select2 multi cho modal Import từ CSDL ──────────────────────
        function initDkDbMulti(selector, url) {
            $(selector).select2({
                ajax: {
                    url: '<?= site_url('admin/dashboardKpi/') ?>' + url,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { q: params.term }; },
                    processResults: function(data) { return { results: data.results }; },
                    cache: true
                },
                minimumInputLength: 0,
                dropdownParent: $('#dk-import-modal'),
                placeholder: '-- Tìm kiếm và chọn (có thể chọn nhiều) --',
                allowClear: true
            }).on('change', function() { calcDkDbPreview(); });
        }

        initDkDbMulti('#imp-db-rooms', 'ajax_search_rooms');
        initDkDbMulti('#imp-db-roles', 'ajax_search_roles');
        initDkDbMulti('#imp-db-tasks', 'ajax_search_tasks');
        initDkDbMulti('#imp-db-viols', 'ajax_search_violations');
    });
</script>