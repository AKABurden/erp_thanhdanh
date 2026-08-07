<div class="space-y-6">

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Tổng audit -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5 text-center">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Tổng Audit</div>
            <div class="text-3xl font-bold text-slate-900 audit-total-audits">-</div>
        </div>
        <!-- Đã hoàn thành -->
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm p-5 text-center">
            <div class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-2">Đã hoàn thành</div>
            <div class="text-3xl font-bold text-emerald-600 audit-completed">-</div>
        </div>
        <!-- Đang thực hiện -->
        <div class="rounded-xl border border-blue-200 bg-blue-50 shadow-sm p-5 text-center">
            <div class="text-xs font-medium text-blue-600 uppercase tracking-wider mb-2">Đang thực hiện</div>
            <div class="text-3xl font-bold text-blue-600 audit-in-progress">-</div>
        </div>
        <!-- Tỷ lệ tuân thủ TB -->
        <div class="rounded-xl border border-amber-200 bg-amber-50 shadow-sm p-5 text-center">
            <div class="text-xs font-medium text-amber-600 uppercase tracking-wider mb-2">Tuân thủ TB</div>
            <div class="text-3xl font-bold text-amber-600 audit-avg-compliance">-</div>
        </div>
        <!-- Vấn đề nghiêm trọng -->
        <div class="rounded-xl border border-red-200 bg-red-50 shadow-sm p-5 text-center">
            <div class="text-xs font-medium text-red-600 uppercase tracking-wider mb-2">Vấn đề vi phạm</div>
            <div class="text-3xl font-bold text-red-600 audit-critical">-</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Tỷ lệ tuân thủ -->
        <div class="lg:col-span-1 rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Tỷ lệ tuân thủ toàn công ty</h3>
            <div id="audit_compliance_chart" style="min-height: 280px;"></div>
        </div>

        <!-- Table Top Issues -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Top 10 Vấn đề vi phạm nhiều nhất</h3>
            </div>
            <div class="overflow-x-auto flex-1">
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Vấn đề / Tiêu chí vi phạm</th>
                            <th class="text-right">Số lần vi phạm</th>
                        </tr>
                    </thead>
                    <tbody id="audit_top_issues_tbody">
                        <tr>
                            <td colspan="3" class="text-center text-slate-400 py-8">Đang tải dữ liệu...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CAPA & Tuân thủ theo phòng ban -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- CAPA Stats -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Thống kê CAPA</h3>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-slate-50 rounded-lg p-4 text-center border border-slate-100">
                    <div class="text-xs text-slate-500 uppercase mb-1">Tổng CAPA</div>
                    <div class="text-2xl font-bold text-slate-800 audit-capa-total">-</div>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 text-center border border-orange-100">
                    <div class="text-xs text-orange-600 uppercase mb-1">Đang mở</div>
                    <div class="text-2xl font-bold text-orange-600 audit-capa-open">-</div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 text-center border border-emerald-100">
                    <div class="text-xs text-emerald-600 uppercase mb-1">Đã hoàn thành</div>
                    <div class="text-2xl font-bold text-emerald-600 audit-capa-completed">-</div>
                </div>
            </div>
            <div id="audit_capa_chart" style="min-height: 200px;"></div>
        </div>

        <!-- Tuân thủ theo phòng ban -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Tỷ lệ tuân thủ theo phòng ban</h3>
            <div id="audit_dept_chart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- Giám sát Override & Audit gần nhất -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Override theo phòng ban -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Vi phạm theo phòng ban</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-2 text-sm" id="audit_override_list">
                    <li class="text-center text-slate-400 py-4">Đang tải...</li>
                </ul>
            </div>
        </div>

        <!-- Audit gần nhất -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Audit gần nhất</h3>
            </div>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Mã Audit</th>
                            <th>Phòng ban</th>
                            <th>Ngày</th>
                            <th class="text-right">Tuân thủ</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="audit_recent_tbody">
                        <tr>
                            <td colspan="5" class="text-center text-slate-400 py-8">Đang tải dữ liệu...</td>
                        </tr>
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
    var AJAX_URL = '<?= site_url("admin/RiskDashboard/get_audit_dashboard_data") ?>';

    function fmtNum(n) {
        return Number(n).toLocaleString('vi-VN');
    }

    function loadAuditDashboard() {
        $.ajax({
            url: AJAX_URL,
            type: 'GET',
            dataType: 'json'
        })
        .done(function(res) {
            if (!res || !res.success) return;
            var d = res.data;

            // 1. Summary cards
            var s = d.summary;
            $('.audit-total-audits').text(fmtNum(s.total_audits));
            $('.audit-completed').text(fmtNum(s.completed_audits));
            $('.audit-in-progress').text(fmtNum(s.in_progress));
            $('.audit-avg-compliance').text(s.avg_compliance + '%');
            $('.audit-critical').text(fmtNum(s.critical_issues));

            // 2. Compliance pie chart
            var cc = d.compliance_chart;
            Highcharts.chart('audit_compliance_chart', {
                chart: { type: 'pie' },
                title: { text: null },
                tooltip: {
                    pointFormat: '<b>{point.y}</b> tiêu chí ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}: {point.percentage:.1f}%',
                            style: { fontSize: '12px' }
                        }
                    }
                },
                series: [{
                    name: 'Tiêu chí',
                    colorByPoint: true,
                    data: [
                        { name: 'Tuân thủ (Yes)', y: cc.yes, color: '#10b981' },
                        { name: 'Vi phạm (No)', y: cc.no, color: '#ef4444' },
                        { name: 'Chưa kiểm tra', y: cc.pending, color: '#94a3b8' }
                    ]
                }],
                credits: { enabled: false }
            });

            // 3. Top issues table
            var tbody = $('#audit_top_issues_tbody');
            tbody.empty();
            if (d.top_issues.length === 0) {
                tbody.html('<tr><td colspan="3" class="text-center text-slate-400 py-8">Chưa có dữ liệu</td></tr>');
            } else {
                $.each(d.top_issues, function(i, item) {
                    var barWidth = d.top_issues[0].count_no > 0
                        ? Math.round((item.count_no / d.top_issues[0].count_no) * 100) : 0;
                    tbody.append(
                        '<tr>' +
                            '<td class="text-slate-500 w-12">' + (i + 1) + '</td>' +
                            '<td class="font-medium text-slate-900">' + item.item_text + '</td>' +
                            '<td class="text-right w-40">' +
                                '<div class="flex items-center justify-end gap-2">' +
                                    '<div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden">' +
                                        '<div class="bg-red-500 h-2 rounded-full" style="width:' + barWidth + '%"></div>' +
                                    '</div>' +
                                    '<span class="text-red-600 font-mono font-semibold">' + item.count_no + '</span>' +
                                '</div>' +
                            '</td>' +
                        '</tr>'
                    );
                });
            }

            // 4. CAPA stats
            var cp = d.capa;
            $('.audit-capa-total').text(fmtNum(cp.total));
            $('.audit-capa-open').text(fmtNum(cp.open));
            $('.audit-capa-completed').text(fmtNum(cp.completed));

            // CAPA chart
            if (cp.total > 0) {
                Highcharts.chart('audit_capa_chart', {
                    chart: { type: 'pie', height: 200 },
                    title: { text: null },
                    plotOptions: {
                        pie: {
                            innerSize: '60%',
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.y}',
                                style: { fontSize: '11px' }
                            }
                        }
                    },
                    series: [{
                        name: 'CAPA',
                        data: [
                            { name: 'Đang mở', y: cp.open, color: '#f97316' },
                            { name: 'Đã hoàn thành', y: cp.completed, color: '#10b981' }
                        ]
                    }],
                    credits: { enabled: false }
                });
            } else {
                $('#audit_capa_chart').html('<div class="h-full flex items-center justify-center text-slate-400 text-sm">Chưa có dữ liệu CAPA</div>');
            }

            // 5. Compliance by department - bar chart
            if (d.compliance_by_dept.length > 0) {
                var deptNames = [], deptValues = [], deptColors = [];
                $.each(d.compliance_by_dept, function(i, item) {
                    deptNames.push(item.department || 'N/A');
                    var val = parseFloat(item.avg_pct) || 0;
                    deptValues.push(Math.round(val * 10) / 10);
                    deptColors.push(val >= 80 ? '#10b981' : (val >= 60 ? '#f59e0b' : '#ef4444'));
                });

                Highcharts.chart('audit_dept_chart', {
                    chart: { type: 'bar' },
                    title: { text: null },
                    xAxis: {
                        categories: deptNames,
                        labels: { style: { fontSize: '12px' } }
                    },
                    yAxis: {
                        min: 0, max: 100,
                        title: { text: '% Tuân thủ' }
                    },
                    tooltip: {
                        valueSuffix: '%',
                        pointFormat: 'Tuân thủ: <b>{point.y}%</b>'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: { enabled: true, format: '{y}%' },
                            colorByPoint: true
                        }
                    },
                    legend: { enabled: false },
                    series: [{
                        name: 'Tuân thủ',
                        data: deptValues,
                        colors: deptColors
                    }],
                    credits: { enabled: false }
                });
            } else {
                $('#audit_dept_chart').html('<div class="h-full flex items-center justify-center text-slate-400 text-sm">Chưa có dữ liệu</div>');
            }

            // 6. Override by dept list
            var overList = $('#audit_override_list');
            overList.empty();
            if (d.override_by_dept.length === 0) {
                overList.html('<li class="text-center text-slate-400 py-4">Không có dữ liệu</li>');
            } else {
                $.each(d.override_by_dept, function(i, item) {
                    overList.append(
                        '<li class="flex justify-between items-center p-3 bg-slate-50 rounded-lg border border-slate-100">' +
                            '<span class="text-slate-700">' + (item.department || 'N/A') + '</span>' +
                            '<span class="font-bold text-orange-600">' + item.no_count + ' lần</span>' +
                        '</li>'
                    );
                });
            }

            // 7. Recent audits table
            var rtbody = $('#audit_recent_tbody');
            rtbody.empty();
            if (d.recent_audits.length === 0) {
                rtbody.html('<tr><td colspan="5" class="text-center text-slate-400 py-8">Chưa có dữ liệu audit</td></tr>');
            } else {
                $.each(d.recent_audits, function(i, a) {
                    var statusClass = '', statusText = '';
                    if (a.status === 'COMPLETED') {
                        statusClass = 'bg-emerald-100 text-emerald-700';
                        statusText = 'Hoàn thành';
                    } else if (a.status === 'IN_PROGRESS') {
                        statusClass = 'bg-blue-100 text-blue-700';
                        statusText = 'Đang thực hiện';
                    } else {
                        statusClass = 'bg-slate-100 text-slate-600';
                        statusText = a.status || 'N/A';
                    }

                    var pct = parseFloat(a.result_percentage) || 0;
                    var pctClass = pct >= 80 ? 'text-emerald-600' : (pct >= 60 ? 'text-amber-600' : 'text-red-600');

                    rtbody.append(
                        '<tr>' +
                            '<td class="font-mono font-medium text-blue-600">' + (a.audit_code || '-') + '</td>' +
                            '<td class="text-slate-700">' + (a.department || '-') + '</td>' +
                            '<td class="text-slate-500">' + (a.audit_date || '-') + '</td>' +
                            '<td class="text-right font-mono font-semibold ' + pctClass + '">' + (a.status === 'COMPLETED' ? pct + '%' : '-') + '</td>' +
                            '<td class="text-center"><span class="px-2.5 py-1 rounded-full text-xs font-medium ' + statusClass + '">' + statusText + '</span></td>' +
                        '</tr>'
                    );
                });
            }
        })
        .fail(function() {
            console.error('Lỗi tải dữ liệu audit dashboard');
        });
    }

    // Load lần đầu
    loadAuditDashboard();

    // Auto refresh mỗi 30 giây
    setInterval(loadAuditDashboard, 30000);
})();
</script>
