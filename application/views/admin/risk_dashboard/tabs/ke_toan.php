<div class="space-y-6">

    <!-- Filter Year/Month -->
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Năm:</label>
            <select id="kt_filter_year" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Tháng:</label>
            <select id="kt_filter_month" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == (int)date('m') ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5">
            <h3 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Chi phí thực tế (Tháng)</h3>
            <div class="text-2xl font-bold tracking-tight text-slate-900 kt-actual-month">-</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5">
            <h3 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Chi phí dự kiến (Tháng)</h3>
            <div class="text-2xl font-bold tracking-tight text-blue-600 kt-planned-month">-</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5">
            <h3 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Sử dụng ngân sách</h3>
            <div class="text-2xl font-bold tracking-tight kt-budget-usage">-</div>
            <div class="mt-2 w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div class="kt-budget-bar bg-blue-500 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm p-5">
            <h3 class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-2">Thực tế cả năm</h3>
            <div class="text-2xl font-bold tracking-tight text-emerald-700 kt-actual-year">-</div>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 shadow-sm p-5">
            <h3 class="text-xs font-medium text-amber-600 uppercase tracking-wider mb-2">Dự kiến cả năm</h3>
            <div class="text-2xl font-bold tracking-tight text-amber-700 kt-planned-year">-</div>
        </div>
    </div>

    <!-- Cảnh báo Rủi ro Thuế & Chi phí -->
    <div id="kt_warnings_container">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Cảnh báo Rủi ro Thuế & Chi phí</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-3" id="kt_warnings_list">
                    <li class="flex items-center justify-center p-4 text-slate-400 text-sm" id="kt_no_warnings">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-2 text-emerald-400"></i>
                        Không có cảnh báo rủi ro nào trong kỳ này
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Chart Trend + Đối tượng -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Trend 12 tháng -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Xu hướng Chi phí theo tháng</h3>
            <div id="kt_trend_chart" style="min-height: 350px;"></div>
        </div>
        <!-- Chi phí theo đối tượng -->
        <div class="lg:col-span-1 rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Chi phí theo đối tượng</h3>
            <div id="kt_object_chart" style="min-height: 350px;"></div>
        </div>
    </div>

    <!-- Chi phí theo loại + Phiếu chi gần nhất -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top chi phí theo loại -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Top 10 Loại chi phí trong tháng</h3>
            </div>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Loại chi phí</th>
                            <th class="text-right">Tổng chi (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody id="kt_cost_category_tbody">
                        <tr>
                            <td colspan="3" class="text-center text-slate-400 py-8">Đang tải...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phiếu chi gần nhất -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Phiếu chi gần nhất</h3>
            </div>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày</th>
                            <th>Loại chi phí</th>
                            <th class="text-right">Số tiền (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody id="kt_recent_tbody">
                        <tr>
                            <td colspan="4" class="text-center text-slate-400 py-8">Đang tải...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== SECTION: CHI PHÍ ĐẶC BIỆT ===== -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
            <h3 class="text-base font-semibold text-slate-900">Phân tích Chi Phí Đặc Biệt <span class="text-sm font-normal text-slate-400">(theo tên loại chi phí)</span></h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="kt_special_cost_cards">
                <?php for ($i = 0; $i < 8; $i++): ?>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4 text-center animate-pulse">
                    <div class="h-4 bg-slate-200 rounded mb-2"></div>
                    <div class="h-7 bg-slate-200 rounded"></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- ===== SECTION: CHI PHÍ LƯƠNG THEO PHÒNG BAN ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Chi Phí Lương theo Phòng Ban</h3>
            <div id="kt_salary_dept_chart" style="min-height: 280px;"></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Chi tiết Lương — Phòng ban</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Phòng ban</th>
                            <th class="px-3 py-3 text-center">Số phiếu</th>
                            <th class="px-3 py-3 text-right">Tổng lương (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody id="kt_salary_dept_tbody">
                        <tr><td colspan="3" class="text-center py-6 text-slate-400">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- jQuery + Highcharts CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
(function() {
    var AJAX_URL = '<?= site_url("admin/RiskDashboard/get_ke_toan_dashboard_data") ?>';

    function fmtMoney(n) {
        if (!n || isNaN(n)) return '0';
        return Number(n).toLocaleString('vi-VN');
    }

    function loadKeToanDashboard() {
        var year  = $('#kt_filter_year').val();
        var month = $('#kt_filter_month').val();

        $.ajax({
            url: AJAX_URL,
            type: 'GET',
            dataType: 'json',
            data: { year: year, month: month }
        })
        .done(function(res) {
            if (!res || !res.success) return;
            var d = res.data;
            var s = d.summary;

            // 1. Summary cards
            $('.kt-actual-month').text(fmtMoney(s.actual_month));
            $('.kt-planned-month').text(fmtMoney(s.planned_month));
            $('.kt-actual-year').text(fmtMoney(s.actual_year));
            $('.kt-planned-year').text(fmtMoney(s.planned_year));

            // Budget usage with color
            var usage = s.budget_usage;
            var usageColor = usage > 90 ? 'text-red-600' : (usage > 75 ? 'text-orange-600' : 'text-emerald-600');
            var barColor = usage > 90 ? 'bg-red-500' : (usage > 75 ? 'bg-orange-500' : 'bg-blue-500');
            $('.kt-budget-usage').text(usage + '%').removeClass('text-red-600 text-orange-600 text-emerald-600').addClass(usageColor);
            $('.kt-budget-bar').css('width', Math.min(usage, 100) + '%').removeClass('bg-red-500 bg-orange-500 bg-blue-500').addClass(barColor);

            // 2. Warnings
            var warnList = $('#kt_warnings_list');
            warnList.empty();
            if (d.warnings.length > 0) {
                $.each(d.warnings, function(i, w) {
                    var bgClass = w.level === 'danger' ? 'bg-red-50 border-red-100' : 'bg-yellow-50 border-yellow-100';
                    var iconClass = w.level === 'danger' ? 'text-red-600' : 'text-yellow-600';
                    var titleClass = w.level === 'danger' ? 'text-red-800' : 'text-yellow-800';
                    var descClass = w.level === 'danger' ? 'text-red-700' : 'text-yellow-700';
                    var iconName = w.level === 'danger' ? 'alert-circle' : 'alert-triangle';
                    warnList.append(
                        '<li class="flex items-start p-4 ' + bgClass + ' border rounded-lg">' +
                            '<i data-lucide="' + iconName + '" class="w-5 h-5 ' + iconClass + ' mt-0.5 mr-3 flex-shrink-0"></i>' +
                            '<div>' +
                                '<h4 class="font-medium ' + titleClass + '">' + w.title + '</h4>' +
                                '<p class="text-sm ' + descClass + ' mt-1">' + w.desc + '</p>' +
                            '</div>' +
                        '</li>'
                    );
                });
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                warnList.html(
                    '<li class="flex items-center justify-center p-4 text-slate-400 text-sm">' +
                        '<i data-lucide="check-circle" class="w-5 h-5 mr-2 text-emerald-400"></i>' +
                        'Không có cảnh báo rủi ro nào trong kỳ này' +
                    '</li>'
                );
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            // 3. Monthly trend chart
            var trend = d.monthly_trend;
            Highcharts.chart('kt_trend_chart', {
                chart: { type: 'column' },
                title: { text: null },
                xAxis: {
                    categories: trend.labels,
                    labels: { style: { fontSize: '11px' } }
                },
                yAxis: {
                    min: 0,
                    title: { text: 'VNĐ' },
                    labels: {
                        formatter: function() {
                            if (this.value >= 1000000000) return (this.value / 1000000000).toFixed(1) + ' tỷ';
                            if (this.value >= 1000000) return (this.value / 1000000).toFixed(0) + ' tr';
                            return this.value.toLocaleString();
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    pointFormatter: function() {
                        return '<span style="color:' + this.color + '">●</span> ' + this.series.name + ': <b>' + fmtMoney(this.y) + ' VNĐ</b><br/>';
                    }
                },
                plotOptions: {
                    column: {
                        borderRadius: 4,
                        groupPadding: 0.15
                    }
                },
                series: [
                    { name: 'Thực tế', data: trend.actual, color: '#3b82f6' },
                    { name: 'Dự kiến', data: trend.planned, color: '#94a3b8' }
                ],
                credits: { enabled: false }
            });

            // 4. Cost by object pie chart
            if (d.cost_by_object.length > 0) {
                var objColors = { 'Khách hàng': '#3b82f6', 'Nhà cung cấp': '#f59e0b', 'Nhân viên': '#10b981', 'Khác': '#8b5cf6', 'TSCĐ': '#ef4444' };
                var objData = [];
                $.each(d.cost_by_object, function(i, item) {
                    objData.push({
                        name: item.object_name,
                        y: parseFloat(item.sum_total) || 0,
                        color: objColors[item.object_name] || '#94a3b8'
                    });
                });
                Highcharts.chart('kt_object_chart', {
                    chart: { type: 'pie' },
                    title: { text: null },
                    tooltip: {
                        pointFormatter: function() {
                            return '<b>' + fmtMoney(this.y) + ' VNĐ</b> (' + this.percentage.toFixed(1) + '%)';
                        }
                    },
                    plotOptions: {
                        pie: {
                            innerSize: '50%',
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.percentage:.1f}%',
                                style: { fontSize: '11px' }
                            }
                        }
                    },
                    series: [{ name: 'Chi phí', data: objData }],
                    credits: { enabled: false }
                });
            } else {
                $('#kt_object_chart').html('<div class="h-full flex items-center justify-center text-slate-400 text-sm">Chưa có dữ liệu</div>');
            }

            // 5. Cost by category table
            var catBody = $('#kt_cost_category_tbody');
            catBody.empty();
            if (d.cost_by_category.length === 0) {
                catBody.html('<tr><td colspan="3" class="text-center text-slate-400 py-8">Chưa có dữ liệu</td></tr>');
            } else {
                var maxCat = parseFloat(d.cost_by_category[0].sum_total) || 1;
                $.each(d.cost_by_category, function(i, item) {
                    var val = parseFloat(item.sum_total) || 0;
                    var barW = Math.round((val / maxCat) * 100);
                    catBody.append(
                        '<tr>' +
                            '<td class="text-slate-500 w-12">' + (i + 1) + '</td>' +
                            '<td class="font-medium text-slate-900">' + (item.cost_name || 'N/A') + '</td>' +
                            '<td class="text-right w-48">' +
                                '<div class="flex items-center justify-end gap-2">' +
                                    '<div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden">' +
                                        '<div class="bg-blue-500 h-2 rounded-full" style="width:' + barW + '%"></div>' +
                                    '</div>' +
                                    '<span class="font-mono font-semibold text-slate-800">' + fmtMoney(val) + '</span>' +
                                '</div>' +
                            '</td>' +
                        '</tr>'
                    );
                });
            }

            // 6. Recent payslips table
            var recBody = $('#kt_recent_tbody');
            recBody.empty();
            if (d.recent_payslips.length === 0) {
                recBody.html('<tr><td colspan="4" class="text-center text-slate-400 py-8">Chưa có phiếu chi</td></tr>');
            } else {
                $.each(d.recent_payslips, function(i, p) {
                    var code = (p.prefix || '') + '-' + (p.code || '');
                    recBody.append(
                        '<tr>' +
                            '<td class="font-mono font-medium text-blue-600">' + code + '</td>' +
                            '<td class="text-slate-500">' + (p.date || '-') + '</td>' +
                            '<td class="text-slate-700">' + (p.cost_name || '-') + '</td>' +
                            '<td class="text-right font-mono font-semibold text-slate-800">' + fmtMoney(p.total) + '</td>' +
                        '</tr>'
                    );
                });
            }

            // 7. Chi phí đặc biệt — cards theo nhóm
            var SPECIAL_ICONS = {
                'Chi Phí Tăng Ca':    { icon: '⏱', cls: 'border-blue-200 bg-blue-50', valCls: 'text-blue-700' },
                'Chi Phí Gia Công':   { icon: '🏭', cls: 'border-purple-200 bg-purple-50', valCls: 'text-purple-700' },
                'Chi Phí Sửa Chữa':  { icon: '🔧', cls: 'border-orange-200 bg-orange-50', valCls: 'text-orange-700' },
                'Chi Phí Tuyển Dụng':{ icon: '👥', cls: 'border-teal-200 bg-teal-50', valCls: 'text-teal-700' },
                'Chi Phí Đào Tạo':   { icon: '📚', cls: 'border-indigo-200 bg-indigo-50', valCls: 'text-indigo-700' },
                'Chi Gấp Khẩn':      { icon: '🚨', cls: 'border-red-200 bg-red-50', valCls: 'text-red-700' },
                'Chi Theo Kế Hoạch': { icon: '📋', cls: 'border-emerald-200 bg-emerald-50', valCls: 'text-emerald-700' },
                'Chi Phí Lương':     { icon: '💰', cls: 'border-amber-200 bg-amber-50', valCls: 'text-amber-700' },
            };
            var cardsEl = $('#kt_special_cost_cards');
            cardsEl.empty();
            if (d.special_costs && d.special_costs.length) {
                $.each(d.special_costs, function(i, sc) {
                    var info = SPECIAL_ICONS[sc.label] || { icon: '📌', cls: 'border-slate-200 bg-slate-50', valCls: 'text-slate-700' };
                    var total = sc.sum_total || 0;
                    var formatted = total > 0 ? fmtMoney(total) : '—';
                    var badge = sc.so_phieu > 0
                        ? '<span class="text-xs text-slate-400 mt-1 block">' + sc.so_phieu + ' phiếu</span>'
                        : '<span class="text-xs text-slate-300 mt-1 block">Không có</span>';
                    cardsEl.append(
                        '<div class="rounded-lg border p-4 text-center ' + info.cls + '">' +
                            '<div class="text-xl mb-1">' + info.icon + '</div>' +
                            '<div class="text-xs font-medium text-slate-600 mb-1 leading-tight">' + sc.label + '</div>' +
                            '<div class="text-lg font-bold ' + info.valCls + '">' + formatted + '</div>' +
                            badge +
                        '</div>'
                    );
                });
            } else {
                cardsEl.html('<div class="col-span-4 text-center py-4 text-slate-400">Không có dữ liệu chi phí đặc biệt</div>');
            }

            // 8. Lương theo phòng ban — chart + table
            var salBody = $('#kt_salary_dept_tbody');
            salBody.empty();
            if (d.salary_by_dept && d.salary_by_dept.length) {
                var deptNames = [], deptVals = [];
                var maxSal = 1;
                $.each(d.salary_by_dept, function(i, r) {
                    var v = parseFloat(r.sum_total) || 0;
                    if (v > maxSal) maxSal = v;
                    deptNames.push(r.department_name || 'N/A');
                    deptVals.push(v);
                });

                // bar chart
                Highcharts.chart('kt_salary_dept_chart', {
                    chart: { type: 'bar' },
                    title: { text: null },
                    xAxis: { categories: deptNames, labels: { style: { fontSize: '12px' } } },
                    yAxis: {
                        min: 0,
                        title: { text: 'VNĐ' },
                        labels: {
                            formatter: function() {
                                if (this.value >= 1000000000) return (this.value / 1000000000).toFixed(1) + ' tỷ';
                                if (this.value >= 1000000) return (this.value / 1000000).toFixed(0) + ' tr';
                                return this.value.toLocaleString();
                            }
                        }
                    },
                    tooltip: {
                        pointFormatter: function() {
                            return '<b>' + fmtMoney(this.y) + ' VNĐ</b>';
                        }
                    },
                    plotOptions: { bar: { dataLabels: {
                        enabled: true,
                        formatter: function() {
                            if (this.y >= 1000000) return (this.y / 1000000).toFixed(0) + 'tr';
                            return this.y.toLocaleString();
                        }
                    }, colorByPoint: true } },
                    legend: { enabled: false },
                    series: [{ name: 'Lương', data: deptVals }],
                    credits: { enabled: false }
                });

                // table
                $.each(d.salary_by_dept, function(i, r) {
                    var v = parseFloat(r.sum_total) || 0;
                    var barW = maxSal > 0 ? Math.round((v / maxSal) * 100) : 0;
                    salBody.append(
                        '<tr class="border-t border-slate-100 hover:bg-slate-50">' +
                        '<td class="px-4 py-2 font-medium text-slate-800">' + (r.department_name || 'N/A') + '</td>' +
                        '<td class="px-3 py-2 text-center text-slate-600">' + (r.so_phieu || 0) + '</td>' +
                        '<td class="px-3 py-2 text-right">' +
                            '<div class="flex items-center justify-end gap-2">' +
                                '<div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden">' +
                                    '<div class="bg-amber-400 h-2 rounded-full" style="width:' + barW + '%"></div>' +
                                '</div>' +
                                '<span class="font-mono font-semibold text-amber-700">' + fmtMoney(v) + '</span>' +
                            '</div>' +
                        '</td>' +
                        '</tr>'
                    );
                });
            } else {
                $('#kt_salary_dept_chart').html('<div class="flex items-center justify-center h-full text-slate-400 py-8">Không có dữ liệu lương</div>');
                salBody.html('<tr><td colspan="3" class="text-center py-6 text-slate-400">Không có dữ liệu</td></tr>');
            }
        })
        .fail(function() {
            console.error('Lỗi tải dữ liệu kế toán dashboard');
        });
    }

    // Filter change events
    $('#kt_filter_year, #kt_filter_month').on('change', function() {
        loadKeToanDashboard();
    });

    // Load lần đầu
    loadKeToanDashboard();

    // Auto refresh mỗi 30 giây
    setInterval(loadKeToanDashboard, 30000);
})();
</script>
