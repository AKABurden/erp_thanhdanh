<?php
// views/admin/kpi_danh_gia/tabs/dashboard.php
// Dữ liệu được load AJAX từ KpiDanhGia::get_dashboard_data()
?>
<!-- STAT CARDS (load via AJAX) -->
<div id="stat-cards" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    <!-- Skeleton -->
    <?php for ($i = 0; $i < 6; $i++): ?>
    <div class="bg-white rounded-xl border border-slate-100 p-4 animate-pulse">
        <div class="h-8 bg-slate-100 rounded mb-2"></div>
        <div class="h-6 bg-slate-100 rounded w-1/2"></div>
    </div>
    <?php endfor; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Phân bổ quyết định -->
    <div class="bg-white rounded-xl border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-4 h-4 text-slate-400"></i> Phân bổ Quyết định
        </h3>
        <div id="decision-chart" class="space-y-4">
            <div class="text-sm text-slate-400 text-center py-6">Đang tải...</div>
        </div>
    </div>

    <!-- Ngưỡng đánh giá -->
    <div class="bg-white rounded-xl border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="sliders-horizontal" class="w-4 h-4 text-slate-400"></i> Ngưỡng Đánh giá
        </h3>
        <div id="threshold-list" class="divide-y divide-slate-50">
            <div class="text-sm text-slate-400 text-center py-6">Đang tải...</div>
        </div>
    </div>
</div>

<!-- Gate info -->
<div class="rounded-xl p-6 mb-4 border border-slate-100 bg-gradient-to-br from-blue-50 via-white to-cyan-50">
    <h3 class="font-semibold mb-4 text-slate-800">Hệ Thống Tự Đánh Giá KPI Theo Vòng Đời</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        <?php
        $gates = [
            ['gate' => 'Gate 1', 'desc' => 'Hồ sơ & Điều kiện đầu vào', 'chip' => 'bg-blue-100 text-blue-700'],
            ['gate' => 'Gate 2', 'desc' => 'P2 KPI Final (60 điểm)', 'chip' => 'bg-indigo-100 text-indigo-700'],
            ['gate' => 'Gate 3', 'desc' => 'Điểm Compliance (20 điểm)', 'chip' => 'bg-amber-100 text-amber-700'],
            ['gate' => 'Gate 4', 'desc' => 'P3 Contribution (20 điểm)', 'chip' => 'bg-emerald-100 text-emerald-700'],
            ['gate' => 'Gate 5', 'desc' => 'Tổng điểm & Xếp loại', 'chip' => 'bg-violet-100 text-violet-700'],
            ['gate' => 'Quyết định', 'desc' => 'ĐẠT / GIÁM SÁT / FAIL', 'chip' => 'bg-slate-100 text-slate-700'],
        ];
        foreach ($gates as $g): ?>
        <div class="rounded-lg p-3 border border-white/70 bg-white/70 shadow-sm backdrop-blur-sm">
            <div class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold <?= $g['chip'] ?> mb-2"><?= $g['gate'] ?></div>
            <div class="font-medium text-sm text-slate-700"><?= $g['desc'] ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function () {
    fetch('<?= site_url('admin/KpiDanhGia/get_dashboard_data') ?>')
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;

            // Stat cards
            const cards = [
                { label: 'KPI Import',   value: d.total_import,   icon: 'file-spreadsheet', bg: 'bg-blue-50',   text: 'text-blue-600' },
                { label: 'Nhân sự',      value: d.total_nhan_su,  icon: 'users',            bg: 'bg-green-50',  text: 'text-green-600' },
                { label: 'Tổng đánh giá',value: d.total_danh_gia, icon: 'clipboard-check',  bg: 'bg-amber-50',  text: 'text-amber-600' },
                { label: 'ĐẠT',          value: d.pass,           icon: 'check-circle',     bg: 'bg-emerald-50',text: 'text-emerald-600' },
                { label: 'GIÁM SÁT',     value: d.giam_sat,       icon: 'alert-triangle',   bg: 'bg-yellow-50', text: 'text-yellow-600' },
                { label: 'FAIL',         value: d.fail,           icon: 'x-circle',         bg: 'bg-red-50',    text: 'text-red-600' },
            ];
            document.getElementById('stat-cards').innerHTML = cards.map(c => `
                <div class="bg-white rounded-xl border border-slate-100 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg ${c.bg} flex items-center justify-center flex-shrink-0">
                            <i data-lucide="${c.icon}" class="w-5 h-5 ${c.text}"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">${c.label}</div>
                            <div class="text-xl font-bold text-slate-900">${c.value}</div>
                        </div>
                    </div>
                </div>`).join('');

            // Decision chart
            const total = d.pass + d.fail + d.giam_sat || 1;
            const items = [
                { label: 'ĐẠT',      val: d.pass,     cls: 'bg-emerald-500' },
                { label: 'GIÁM SÁT', val: d.giam_sat, cls: 'bg-amber-500' },
                { label: 'FAIL',     val: d.fail,     cls: 'bg-red-500' },
            ];
            document.getElementById('decision-chart').innerHTML = items.map(it => {
                const pct = Math.round(it.val / total * 100);
                return `<div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600">${it.label}</span>
                        <span class="font-medium">${it.val} (${pct}%)</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full ${it.cls} rounded-full" style="width:${pct}%"></div>
                    </div>
                </div>`;
            }).join('');

            // Thresholds
            document.getElementById('threshold-list').innerHTML = (d.thresholds || []).map(t => `
                <div class="flex justify-between py-2 text-sm">
                    <span class="text-slate-600">${t.ten_nguong}</span>
                    <span class="font-semibold text-slate-900">${t.gia_tri}</span>
                </div>`).join('') || '<div class="text-sm text-slate-400 py-4 text-center">Chưa có dữ liệu</div>';

            lucide.createIcons();
        });
})();
</script>
