<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Tổng cảnh báo mới</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900"><?= isset($mock_data['alerts_count']) ? $mock_data['alerts_count'] : 12 ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Severity 3-4 (Nghiêm trọng)</h3>
            <div class="text-3xl font-bold tracking-tight text-red-600"><?= isset($mock_data['sev34_count']) ? $mock_data['sev34_count'] : 5 ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Cảnh báo lặp lại</h3>
            <div class="text-3xl font-bold tracking-tight text-orange-500">3</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Tổng Impact (VNĐ)</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900"><?= number_format($mock_data['total_impact'] ?? 70000000, 0, ',', '.') ?></div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Danh sách Cảnh báo tự động</h3>
        </div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thời gian</th>
                        <th>Phòng ban</th>
                        <th>Mức độ</th>
                        <th>Nội dung</th>
                        <th>Impact (VNĐ)</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dữ liệu mẫu, trong thực tế dùng vòng lặp foreach ($alerts as $alert) -->
                    <tr>
                        <td class="font-medium text-slate-900">AL-001</td>
                        <td class="text-slate-500">2h trước</td>
                        <td>Sản xuất</td>
                        <td><span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-800">YELLOW</span></td>
                        <td>Lỗi quy trình đóng gói</td>
                        <td class="font-mono text-slate-900">5.000.000</td>
                        <td><span class="px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Mới</span></td>
                    </tr>
                    <tr>
                        <td class="font-medium text-slate-900">AL-002</td>
                        <td class="text-slate-500">5h trước</td>
                        <td>Kế toán</td>
                        <td><span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-800">RED</span></td>
                        <td>Chi phí tăng bất thường > 20%</td>
                        <td class="font-mono text-slate-900">45.000.000</td>
                        <td><span class="px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Đang xử lý</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
