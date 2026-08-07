<?php
// views/admin/kpi_danh_gia/tabs/tong_hop.php
?>
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-slate-900">Tổng hợp KPI</h2>
        <p class="text-sm text-slate-500">Bảng tổng hợp kết quả đánh giá nhân sự</p>
    </div>
    <select id="th-filter" onchange="loadTongHop()"
        class="px-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="all">Tất cả loại đánh giá</option>
        <?php foreach (['KPI tuần','KPI tháng','KPI 3 tháng','KPI 6 tháng','KPI năm'] as $loai): ?>
        <option value="<?= $loai ?>"><?= $loai ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Stat cards -->
<div id="th-stats" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-100 p-4 animate-pulse"><div class="h-12 bg-slate-100 rounded"></div></div>
    <div class="bg-white rounded-xl border border-slate-100 p-4 animate-pulse"><div class="h-12 bg-slate-100 rounded"></div></div>
    <div class="bg-white rounded-xl border border-slate-100 p-4 animate-pulse"><div class="h-12 bg-slate-100 rounded"></div></div>
    <div class="bg-white rounded-xl border border-slate-100 p-4 animate-pulse"><div class="h-12 bg-slate-100 rounded"></div></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Phân bổ quyết định -->
    <div class="bg-white rounded-xl border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-4 h-4 text-slate-400"></i> Phân bổ Quyết định
        </h3>
        <div id="th-decision-chart" class="space-y-4"></div>
    </div>

    <!-- Quy tắc quy đổi -->
    <div class="bg-white rounded-xl border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="refresh-cw" class="w-4 h-4 text-slate-400"></i> Quy tắc Quy đổi
        </h3>
        <div class="space-y-2">
            <?php if (!empty($conversion_rules)): foreach ($conversion_rules as $r):
                $cls = $r['ket_qua'] === 'Xuất sắc' ? 'bg-emerald-100 text-emerald-700'
                     : ($r['ket_qua'] === 'Tốt'      ? 'bg-blue-100 text-blue-700'
                     : ($r['ket_qua'] === 'Đạt'       ? 'bg-green-100 text-green-700'
                     : ($r['ket_qua'] === 'Cần giám sát' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')));
            ?>
            <div class="flex items-center justify-between px-3 py-2 bg-slate-50 rounded-lg text-sm">
                <span class="text-slate-600"><?= $r['tu_gia_tri'] ?>–<?= $r['den_gia_tri'] ?>%</span>
                <span class="px-2 py-0.5 rounded text-xs font-medium <?= $cls ?>"><?= $r['ket_qua'] ?></span>
            </div>
            <?php endforeach; else: ?>
            <div class="text-slate-400 text-sm text-center py-4">Chưa có dữ liệu quy đổi</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Điểm trung bình -->
<div class="rounded-xl p-6 mb-6 border border-slate-100 bg-gradient-to-br from-indigo-50 via-white to-cyan-50 shadow-sm">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">Điểm trung bình</p>
            <p id="th-avg-score" class="text-4xl font-bold mt-1 text-slate-900">-</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-slate-500 mb-2">Gate 1</p>
            <div class="flex gap-6">
                <div class="text-center rounded-lg px-3 py-2 bg-white/70 border border-white/80 shadow-sm">
                    <p id="th-gate1-pass" class="text-2xl font-bold text-emerald-600">-</p>
                    <p class="text-xs text-slate-500">PASS</p>
                </div>
                <div class="text-center rounded-lg px-3 py-2 bg-white/70 border border-white/80 shadow-sm">
                    <p id="th-gate1-fail" class="text-2xl font-bold text-rose-600">-</p>
                    <p class="text-xs text-slate-500">FAIL</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadTongHop() {
    const loai = document.getElementById('th-filter').value;
    fetch(`<?= site_url('admin/KpiDanhGia/get_tong_hop_data') ?>?loai=${encodeURIComponent(loai)}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;

            // Stats
            const cards = [
                { label:'Tổng đánh giá', val:d.total, icon:'users', bg:'bg-blue-50', txt:'text-blue-600' },
                { label:'ĐẠT',           val:d.pass,  icon:'check-circle', bg:'bg-emerald-50', txt:'text-emerald-600' },
                { label:'GIÁM SÁT',      val:d.giam_sat, icon:'alert-triangle', bg:'bg-amber-50', txt:'text-amber-600' },
                { label:'FAIL',          val:d.fail,  icon:'x-circle', bg:'bg-red-50', txt:'text-red-600' },
            ];
            document.getElementById('th-stats').innerHTML = cards.map(c => `
                <div class="bg-white rounded-xl border border-slate-100 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg ${c.bg} flex items-center justify-center">
                            <i data-lucide="${c.icon}" class="w-5 h-5 ${c.txt}"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">${c.label}</div>
                            <div class="text-xl font-bold text-slate-900">${c.val}</div>
                        </div>
                    </div>
                </div>`).join('');

            // Decision chart
            const total = d.total || 1;
            const items = [
                { label:'ĐẠT', val:d.pass, cls:'bg-emerald-500' },
                { label:'GIÁM SÁT', val:d.giam_sat, cls:'bg-amber-500' },
                { label:'FAIL', val:d.fail, cls:'bg-red-500' },
            ];
            document.getElementById('th-decision-chart').innerHTML = items.map(it => {
                const pct = Math.round(it.val / total * 100);
                return `<div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600">${it.label}</span>
                        <span class="font-medium">${it.val} (${pct}%)</span>
                    </div>
                    <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full ${it.cls} rounded-full transition-all" style="width:${pct}%"></div>
                    </div>
                </div>`;
            }).join('');

            // Gate 1 summary
            const gate1Pass = d.list.filter(r => r.gate_1_result === 'PASS').length;
            const gate1Fail = d.list.length - gate1Pass;
            document.getElementById('th-avg-score').textContent = d.avg_score;
            document.getElementById('th-gate1-pass').textContent = gate1Pass;
            document.getElementById('th-gate1-fail').textContent = gate1Fail;

            lucide.createIcons();
        });
}
loadTongHop();
</script>
