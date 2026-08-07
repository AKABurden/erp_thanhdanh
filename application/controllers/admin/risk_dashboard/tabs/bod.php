<div class="space-y-6">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-xl border border-red-200 bg-red-50 shadow-sm p-6">
            <h3 class="text-sm font-medium text-red-800 mb-2">Tổng Impact (Rủi ro cao)</h3>
            <div class="text-3xl font-bold tracking-tight text-red-900">1.250.000.000 VNĐ</div>
        </div>
        <div class="rounded-xl border border-orange-200 bg-orange-50 shadow-sm p-6">
            <h3 class="text-sm font-medium text-orange-800 mb-2">Trạng thái Hệ thống</h3>
            <div class="text-3xl font-bold tracking-tight text-orange-900">CẢNH BÁO (ORANGE)</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Top Phòng ban rủi ro</h3>
            <div class="text-3xl font-bold tracking-tight text-slate-900">Kế toán, Kho</div>
        </div>
    </div>

    <!-- Charts & Heatmap -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Radar 5 Trục Rủi Ro</h3>
            <div class="h-80 bg-slate-50 flex items-center justify-center border border-dashed border-slate-300 rounded-lg text-slate-500">
                <p class="text-center">Tích hợp Chart.js Radar Chart tại đây<br><span class="text-xs">(Tài chính, Vận hành, Tuân thủ, Chiến lược, Uy tín)</span></p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Risk Heatmap (Theo phòng ban)</h3>
            <div class="grid grid-cols-3 gap-2 h-80">
                <!-- Mock Heatmap Grid -->
                <div class="bg-red-500 text-white flex items-center justify-center rounded-md font-medium text-sm p-2 text-center">Kế toán<br>(High)</div>
                <div class="bg-orange-400 text-white flex items-center justify-center rounded-md font-medium text-sm p-2 text-center">Kho<br>(Med-High)</div>
                <div class="bg-yellow-300 text-yellow-900 flex items-center justify-center rounded-md font-medium text-sm p-2 text-center">Sản xuất<br>(Med)</div>
                <div class="bg-green-500 text-white flex items-center justify-center rounded-md font-medium text-sm p-2 text-center">Nhân sự<br>(Low)</div>
                <div class="bg-yellow-300 text-yellow-900 flex items-center justify-center rounded-md font-medium text-sm p-2 text-center">Kinh doanh<br>(Med)</div>
                <div class="bg-green-500 text-white flex items-center justify-center rounded-md font-medium text-sm p-2 text-center">IT<br>(Low)</div>
            </div>
        </div>
    </div>
</div>
