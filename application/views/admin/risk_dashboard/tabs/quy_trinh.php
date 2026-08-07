<div class="space-y-6">
    <!-- Flow Diagram -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-6">Quy trình Nâng cấp Cảnh báo (Escalation)</h3>
        <div class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-4">
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-center w-full md:w-1/3">
                <div class="font-bold text-yellow-800 mb-1">YELLOW</div>
                <div class="text-sm text-yellow-700">Cảnh báo lần 1<br>Gửi NV phụ trách</div>
            </div>
            <i data-lucide="arrow-right" class="hidden md:block text-slate-400"></i>
            <i data-lucide="arrow-down" class="block md:hidden text-slate-400"></i>
            <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg text-center w-full md:w-1/3">
                <div class="font-bold text-orange-800 mb-1">ORANGE</div>
                <div class="text-sm text-orange-700">Quá hạn SLA 24h<br>Gửi Trưởng phòng</div>
            </div>
            <i data-lucide="arrow-right" class="hidden md:block text-slate-400"></i>
            <i data-lucide="arrow-down" class="block md:hidden text-slate-400"></i>
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-center w-full md:w-1/3">
                <div class="font-bold text-red-800 mb-1">RED</div>
                <div class="text-sm text-red-700">Quá hạn SLA 48h<br>Gửi BOD & Khóa thao tác</div>
            </div>
        </div>
    </div>

    <!-- SLA Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Cấu hình SLA theo Đối tượng</h3>
        </div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Loại đối tượng</th>
                        <th>SLA Xử lý (Giờ)</th>
                        <th>Hành động khi vi phạm</th>
                        <th>Người nhận cảnh báo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-medium text-slate-900">Task / Công việc thường</td>
                        <td>48h</td>
                        <td>Chuyển trạng thái ORANGE</td>
                        <td>Trưởng phòng</td>
                    </tr>
                    <tr>
                        <td class="font-medium text-slate-900">Work Order (Sản xuất)</td>
                        <td>24h</td>
                        <td>Chuyển trạng thái RED, Dừng máy</td>
                        <td>Giám đốc Nhà máy</td>
                    </tr>
                    <tr>
                        <td class="font-medium text-slate-900">Thanh toán (Kế toán)</td>
                        <td>12h</td>
                        <td>Chuyển trạng thái RED, Khóa duyệt</td>
                        <td>CFO, CEO</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Timeline -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-6">Chu trình xử lý hoàn chỉnh (5 Bước)</h3>
        <div class="relative border-l-2 border-slate-200 ml-3 space-y-8">
            <div class="relative pl-6">
                <div class="absolute w-4 h-4 bg-blue-500 rounded-full -left-[9px] top-1 border-4 border-white"></div>
                <h4 class="font-semibold text-slate-900">1. Phát hiện</h4>
                <p class="text-sm text-slate-500 mt-1">Hệ thống tự động quét dữ liệu và tạo cảnh báo dựa trên Rule.</p>
            </div>
            <div class="relative pl-6">
                <div class="absolute w-4 h-4 bg-slate-300 rounded-full -left-[9px] top-1 border-4 border-white"></div>
                <h4 class="font-semibold text-slate-900">2. Phân loại & Giao việc</h4>
                <p class="text-sm text-slate-500 mt-1">Gán mức độ (Y/O/R) và tự động assign cho nhân sự liên quan.</p>
            </div>
            <div class="relative pl-6">
                <div class="absolute w-4 h-4 bg-slate-300 rounded-full -left-[9px] top-1 border-4 border-white"></div>
                <h4 class="font-semibold text-slate-900">3. Xử lý & Phản hồi</h4>
                <p class="text-sm text-slate-500 mt-1">Nhân sự cập nhật trạng thái, đính kèm bằng chứng khắc phục.</p>
            </div>
            <div class="relative pl-6">
                <div class="absolute w-4 h-4 bg-slate-300 rounded-full -left-[9px] top-1 border-4 border-white"></div>
                <h4 class="font-semibold text-slate-900">4. Đánh giá (Audit)</h4>
                <p class="text-sm text-slate-500 mt-1">Ban Audit kiểm tra chéo kết quả xử lý.</p>
            </div>
            <div class="relative pl-6">
                <div class="absolute w-4 h-4 bg-slate-300 rounded-full -left-[9px] top-1 border-4 border-white"></div>
                <h4 class="font-semibold text-slate-900">5. Đóng cảnh báo</h4>
                <p class="text-sm text-slate-500 mt-1">Hệ thống lưu log Audit Trail và cập nhật báo cáo.</p>
            </div>
        </div>
    </div>
</div>
