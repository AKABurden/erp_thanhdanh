<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">So sánh Tuần (WoW)</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900">+12.5%</div>
            <p class="text-xs text-red-500 flex items-center mt-2 font-medium">
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 mr-1"></i> Tăng cảnh báo
            </p>
        </div>
        <!-- Card 2 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">So sánh Tháng (MoM)</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900">-5.2%</div>
            <p class="text-xs text-green-500 flex items-center mt-2 font-medium">
                <i data-lucide="arrow-down-right" class="w-3.5 h-3.5 mr-1"></i> Giảm cảnh báo
            </p>
        </div>
        <!-- Card 3 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">So sánh Quý (QoQ)</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900">+2.1%</div>
            <p class="text-xs text-red-500 flex items-center mt-2 font-medium">
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 mr-1"></i> Tăng nhẹ
            </p>
        </div>
        <!-- Card 4 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Trạng thái kỳ này</h3>
            <div class="flex items-center space-x-3 mt-1">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="text-2xl font-bold tracking-tight text-orange-600">ALERT</span>
            </div>
        </div>
    </div>

    <!-- Charts Placeholder -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Xu hướng cảnh báo theo tuần</h3>
            <div class="h-64 bg-slate-50 flex items-center justify-center border border-dashed border-slate-300 rounded-lg text-slate-500">
                <p class="text-center">Tích hợp Chart.js hoặc ApexCharts tại đây<br><span class="text-xs">(Line Chart)</span></p>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Xu hướng thiệt hại (VNĐ)</h3>
            <div class="h-64 bg-slate-50 flex items-center justify-center border border-dashed border-slate-300 rounded-lg text-slate-500">
                <p class="text-center">Tích hợp Chart.js hoặc ApexCharts tại đây<br><span class="text-xs">(Bar Chart)</span></p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Cấu hình tham số (PARAMS)</h3>
        </div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Tham số</th>
                        <th>Ngưỡng cảnh báo</th>
                        <th>Mức độ</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-medium">Biến động MoM</td>
                        <td class="font-mono text-xs">&gt; 10%</td>
                        <td><span class="px-2.5 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-semibold">YELLOW</span></td>
                        <td class="text-blue-600 hover:text-blue-800 cursor-pointer font-medium">Sửa</td>
                    </tr>
                    <tr>
                        <td class="font-medium">Biến động MoM</td>
                        <td class="font-mono text-xs">&gt; 20%</td>
                        <td><span class="px-2.5 py-1 bg-red-100 text-red-800 rounded-md text-xs font-semibold">RED</span></td>
                        <td class="text-blue-600 hover:text-blue-800 cursor-pointer font-medium">Sửa</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
