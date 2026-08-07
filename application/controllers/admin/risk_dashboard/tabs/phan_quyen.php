<div class="space-y-6">
    <!-- Cấu hình Override -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Cấu hình Override & Can thiệp đặc biệt</h3>
        </div>
        <div class="p-6">
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-medium text-yellow-800 flex items-center">
                    <i data-lucide="shield" class="w-4 h-4 mr-2"></i>
                    Quy định Override
                </h4>
                <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                    <li>Mọi hành động override phải ghi rõ lý do và người duyệt.</li>
                    <li>Giới hạn số lần override: 3 lần/tháng/phòng ban.</li>
                    <li>Cảnh báo từ mức ORANGE trở lên cần có người duyệt thứ 2 (BOD/Audit).</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Nhật ký Override -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Nhật ký Override gần đây</h3>
        </div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Mã Override</th>
                        <th>Thời gian</th>
                        <th>Người yêu cầu</th>
                        <th>Vai trò</th>
                        <th>Lý do</th>
                        <th>Người duyệt 1</th>
                        <th>Người duyệt 2 (ORANGE+)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-medium text-slate-900">OV-001</td>
                        <td class="text-slate-500">2026-04-08 14:30</td>
                        <td>Nguyen Van A</td>
                        <td>Trưởng phòng KD</td>
                        <td>Khách hàng VIP yêu cầu gấp</td>
                        <td>Tran Thi B (Giám đốc)</td>
                        <td class="text-slate-400">-</td>
                    </tr>
                    <tr>
                        <td class="font-medium text-slate-900">OV-002</td>
                        <td class="text-slate-500">2026-04-07 09:15</td>
                        <td>Le Van C</td>
                        <td>Trưởng phòng Kho</td>
                        <td>Lỗi hệ thống ERP lệch số liệu</td>
                        <td>Tran Thi B (Giám đốc)</td>
                        <td class="text-green-600 font-medium">Pham Van D (Audit)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
