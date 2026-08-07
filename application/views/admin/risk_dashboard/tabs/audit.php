<div class="space-y-6">

    <!-- ===== SECTION 1: SUMMARY CARDS ===== -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 text-center">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Tổng Audit</div>
            <div class="text-3xl font-bold text-slate-900 audit-total-audits">-</div>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm p-4 text-center">
            <div class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-1">Đã hoàn thành</div>
            <div class="text-3xl font-bold text-emerald-600 audit-completed">-</div>
        </div>
        <div class="rounded-xl border border-blue-200 bg-blue-50 shadow-sm p-4 text-center">
            <div class="text-xs font-medium text-blue-600 uppercase tracking-wider mb-1">Đang thực hiện</div>
            <div class="text-3xl font-bold text-blue-600 audit-in-progress">-</div>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 shadow-sm p-4 text-center">
            <div class="text-xs font-medium text-amber-600 uppercase tracking-wider mb-1">Tuân thủ TB</div>
            <div class="text-3xl font-bold text-amber-600 audit-avg-compliance">-</div>
        </div>
        <div class="rounded-xl border border-red-200 bg-red-50 shadow-sm p-4 text-center">
            <div class="text-xs font-medium text-red-600 uppercase tracking-wider mb-1">Vấn đề vi phạm</div>
            <div class="text-3xl font-bold text-red-600 audit-critical">-</div>
        </div>
        <div class="rounded-xl border border-purple-200 bg-purple-50 shadow-sm p-4 text-center">
            <div class="text-xs font-medium text-purple-600 uppercase tracking-wider mb-1">Tuần này thiếu</div>
            <div class="text-3xl font-bold text-purple-600" id="audit-missing-week">-</div>
        </div>
    </div>

    <!-- ===== SECTION 2: BẮT BUỘC AUDIT HÀNG TUẦN THEO PHÒNG BAN ===== -->
    <div class="rounded-xl border border-red-200 bg-white shadow-sm overflow-hidden">
        <div class="p-4 border-b border-red-100 bg-red-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                <h3 class="text-base font-semibold text-red-800">🗓 Yêu cầu Audit bắt buộc hàng tuần theo phòng ban</h3>
            </div>
            <span class="text-xs text-red-500 font-medium" id="audit-week-label">Tuần hiện tại</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Phòng ban</th>
                        <th class="px-3 py-3 text-center">Tuần này</th>
                        <th class="px-3 py-3 text-center">Tuần trước</th>
                        <th class="px-3 py-3 text-center">Tổng tháng</th>
                        <th class="px-3 py-3 text-center">Tỷ lệ tuân thủ</th>
                        <th class="px-3 py-3 text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="audit_weekly_dept_tbody">
                    <tr><td colspan="6" class="text-center py-6 text-slate-400">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== SECTION 3: KẾT QUẢ AUDIT - 3 loại ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- BC Vi phạm -->
        <div class="rounded-xl border border-red-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-red-50">
                <h3 class="text-sm font-semibold text-red-800">📋 Kết quả theo BC Vi phạm</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Phòng ban</th>
                            <th class="px-2 py-2 text-center">Số BC VP</th>
                            <th class="px-2 py-2 text-center">Điểm trừ</th>
                        </tr>
                    </thead>
                    <tbody id="audit_vipham_tbody">
                        <tr><td colspan="3" class="text-center py-4 text-slate-400">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BC KPH -->
        <div class="rounded-xl border border-orange-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-orange-50">
                <h3 class="text-sm font-semibold text-orange-800">📊 Kết quả theo BC KPH</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Phòng ban</th>
                            <th class="px-2 py-2 text-center">Tổng KPH</th>
                            <th class="px-2 py-2 text-center">Chờ XL</th>
                        </tr>
                    </thead>
                    <tbody id="audit_kph_tbody">
                        <tr><td colspan="3" class="text-center py-4 text-slate-400">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Theo danh mục Audit Tuần -->
        <div class="rounded-xl border border-blue-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-blue-50">
                <h3 class="text-sm font-semibold text-blue-800">📅 Danh mục Audit Tuần</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Danh mục</th>
                            <th class="px-2 py-2 text-center">Tuần</th>
                            <th class="px-2 py-2 text-center">Yes/No</th>
                        </tr>
                    </thead>
                    <tbody id="audit_weekly_cat_tbody">
                        <tr><td colspan="3" class="text-center py-4 text-slate-400">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 4: ACTIONS CẦN DUYỆT ===== -->
    <div class="rounded-xl border border-amber-200 bg-white shadow-sm overflow-hidden">
        <div class="p-4 border-b border-amber-100 bg-amber-50 flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <h3 class="text-base font-semibold text-amber-800">⚡ Cảnh báo cần Action — Chờ duyệt</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Nhân viên</th>
                        <th class="px-3 py-3 text-center">Loại đề xuất</th>
                        <th class="px-3 py-3 text-left">Lý do</th>
                        <th class="px-3 py-3 text-center">Nguồn</th>
                        <th class="px-3 py-3 text-center">Ngày tạo</th>
                        <th class="px-3 py-3 text-center">Trạng thái</th>
                        <th class="px-3 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="audit_action_tbody">
                    <tr><td colspan="7" class="text-center py-6 text-slate-400">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== SECTION 5: KTNB / KSRR / BOD ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- KTNB -->
        <div class="rounded-xl border border-indigo-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 border-b border-indigo-100 bg-indigo-50">
                <h3 class="text-sm font-semibold text-indigo-800">🏛 Kiểm toán nội bộ (KTNB)</h3>
            </div>
            <div id="audit_ktnb_list" class="p-4 space-y-2 text-sm min-h-[120px]">
                <div class="text-slate-400 text-center py-4">Đang tải...</div>
            </div>
        </div>

        <!-- KSRR -->
        <div class="rounded-xl border border-teal-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 border-b border-teal-100 bg-teal-50">
                <h3 class="text-sm font-semibold text-teal-800">🛡 Kiểm soát rủi ro (KSRR)</h3>
            </div>
            <div id="audit_ksrr_list" class="p-4 space-y-2 text-sm min-h-[120px]">
                <div class="text-slate-400 text-center py-4">Đang tải...</div>
            </div>
        </div>

        <!-- BOD -->
        <div class="rounded-xl border border-rose-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 border-b border-rose-100 bg-rose-50">
                <h3 class="text-sm font-semibold text-rose-800">👑 Yêu cầu từ Ban điều hành (BOD)</h3>
            </div>
            <div id="audit_bod_list" class="p-4 space-y-2 text-sm min-h-[120px]">
                <div class="text-slate-400 text-center py-4">Đang tải...</div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 6: CHART & TOP ISSUES ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Tỷ lệ tuân thủ toàn công ty</h3>
            <div id="audit_compliance_chart" style="min-height: 280px;"></div>
        </div>
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Top 10 Vấn đề vi phạm nhiều nhất</h3>
            </div>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr><th>STT</th><th>Vấn đề / Tiêu chí vi phạm</th><th class="text-right">Số lần</th></tr>
                    </thead>
                    <tbody id="audit_top_issues_tbody">
                        <tr><td colspan="3" class="text-center text-slate-400 py-8">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CAPA & Tuân thủ theo phòng ban -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Tỷ lệ tuân thủ theo phòng ban</h3>
            <div id="audit_dept_chart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- Override & Recent audits -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Audit gần nhất</h3>
            </div>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Mã Audit</th><th>Phòng ban</th><th>Ngày</th>
                            <th class="text-right">Tuân thủ</th><th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="audit_recent_tbody">
                        <tr><td colspan="5" class="text-center text-slate-400 py-8">Đang tải dữ liệu...</td></tr>
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
    var AJAX_URL        = '<?= site_url("admin/RiskDashboard/get_audit_dashboard_data") ?>';
    var WEEKLY_URL      = '<?= site_url("admin/RiskDashboard/get_audit_weekly_dept") ?>';
    var ACTION_URL      = '<?= site_url("admin/RiskDashboard/get_audit_action_pending") ?>';
    var APPROVE_URL     = '<?= site_url("admin/RiskDashboard/approve_audit_action") ?>';

    function fmtNum(n) { return Number(n).toLocaleString('vi-VN'); }

    /* ===========================
       LOAD MAIN AUDIT DASHBOARD
    =========================== */
    function loadAuditDashboard() {
        $.ajax({ url: AJAX_URL, type: 'GET', dataType: 'json' })
        .done(function(res) {
            if (!res || !res.success) return;
            var d = res.data;

            // Summary cards
            var s = d.summary;
            $('.audit-total-audits').text(fmtNum(s.total_audits));
            $('.audit-completed').text(fmtNum(s.completed_audits));
            $('.audit-in-progress').text(fmtNum(s.in_progress));
            $('.audit-avg-compliance').text(s.avg_compliance + '%');
            $('.audit-critical').text(fmtNum(s.critical_issues));

            // Compliance pie chart
            var cc = d.compliance_chart;
            Highcharts.chart('audit_compliance_chart', {
                chart: { type: 'pie' }, title: { text: null },
                tooltip: { pointFormat: '<b>{point.y}</b> tiêu chí ({point.percentage:.1f}%)' },
                plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer',
                    dataLabels: { enabled: true, format: '{point.name}: {point.percentage:.1f}%', style: { fontSize: '12px' } }
                }},
                series: [{ name: 'Tiêu chí', colorByPoint: true, data: [
                    { name: 'Tuân thủ (Yes)', y: cc.yes, color: '#10b981' },
                    { name: 'Vi phạm (No)', y: cc.no, color: '#ef4444' },
                    { name: 'Chưa kiểm tra', y: cc.pending, color: '#94a3b8' }
                ]}],
                credits: { enabled: false }
            });

            // Top issues table
            var tbody = $('#audit_top_issues_tbody');
            tbody.empty();
            if (!d.top_issues.length) {
                tbody.html('<tr><td colspan="3" class="text-center text-slate-400 py-8">Chưa có dữ liệu</td></tr>');
            } else {
                $.each(d.top_issues, function(i, item) {
                    var barWidth = d.top_issues[0].count_no > 0 ? Math.round((item.count_no / d.top_issues[0].count_no) * 100) : 0;
                    tbody.append(
                        '<tr>' +
                        '<td class="text-slate-500 w-12">' + (i+1) + '</td>' +
                        '<td class="font-medium text-slate-900">' + item.item_text + '</td>' +
                        '<td class="text-right w-40"><div class="flex items-center justify-end gap-2">' +
                            '<div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden"><div class="bg-red-500 h-2 rounded-full" style="width:' + barWidth + '%"></div></div>' +
                            '<span class="text-red-600 font-mono font-semibold">' + item.count_no + '</span>' +
                        '</div></td></tr>'
                    );
                });
            }

            // CAPA stats
            var cp = d.capa;
            $('.audit-capa-total').text(fmtNum(cp.total));
            $('.audit-capa-open').text(fmtNum(cp.open));
            $('.audit-capa-completed').text(fmtNum(cp.completed));
            if (cp.total > 0) {
                Highcharts.chart('audit_capa_chart', {
                    chart: { type: 'pie', height: 200 }, title: { text: null },
                    plotOptions: { pie: { innerSize: '60%', dataLabels: { enabled: true, format: '{point.name}: {point.y}', style: { fontSize: '11px' } } } },
                    series: [{ name: 'CAPA', data: [
                        { name: 'Đang mở', y: cp.open, color: '#f97316' },
                        { name: 'Đã hoàn thành', y: cp.completed, color: '#10b981' }
                    ]}],
                    credits: { enabled: false }
                });
            } else {
                $('#audit_capa_chart').html('<div class="flex items-center justify-center h-full text-slate-400 text-sm py-8">Chưa có dữ liệu CAPA</div>');
            }

            // Compliance by dept bar chart
            if (d.compliance_by_dept.length) {
                var deptNames = [], deptValues = [], deptColors = [];
                $.each(d.compliance_by_dept, function(i, item) {
                    deptNames.push(item.department || 'N/A');
                    var val = parseFloat(item.avg_pct) || 0;
                    deptValues.push(Math.round(val * 10) / 10);
                    deptColors.push(val >= 80 ? '#10b981' : (val >= 60 ? '#f59e0b' : '#ef4444'));
                });
                Highcharts.chart('audit_dept_chart', {
                    chart: { type: 'bar' }, title: { text: null },
                    xAxis: { categories: deptNames, labels: { style: { fontSize: '12px' } } },
                    yAxis: { min: 0, max: 100, title: { text: '% Tuân thủ' } },
                    tooltip: { valueSuffix: '%', pointFormat: 'Tuân thủ: <b>{point.y}%</b>' },
                    plotOptions: { bar: { dataLabels: { enabled: true, format: '{y}%' }, colorByPoint: true } },
                    legend: { enabled: false },
                    series: [{ name: 'Tuân thủ', data: deptValues, colors: deptColors }],
                    credits: { enabled: false }
                });
            } else {
                $('#audit_dept_chart').html('<div class="flex items-center justify-center h-full text-slate-400 text-sm py-8">Chưa có dữ liệu</div>');
            }

            // Override by dept
            var overList = $('#audit_override_list');
            overList.empty();
            if (!d.override_by_dept.length) {
                overList.html('<li class="text-center text-slate-400 py-4">Không có dữ liệu</li>');
            } else {
                $.each(d.override_by_dept, function(i, item) {
                    overList.append(
                        '<li class="flex justify-between items-center p-3 bg-slate-50 rounded-lg border border-slate-100">' +
                        '<span class="text-slate-700">' + (item.department || 'N/A') + '</span>' +
                        '<span class="font-bold text-orange-600">' + item.no_count + ' lần</span></li>'
                    );
                });
            }

            // Recent audits
            var rtbody = $('#audit_recent_tbody');
            rtbody.empty();
            if (!d.recent_audits.length) {
                rtbody.html('<tr><td colspan="5" class="text-center text-slate-400 py-8">Chưa có dữ liệu audit</td></tr>');
            } else {
                $.each(d.recent_audits, function(i, a) {
                    var scls = '', stxt = '';
                    if (a.status === 'COMPLETED') { scls = 'bg-emerald-100 text-emerald-700'; stxt = 'Hoàn thành'; }
                    else if (a.status === 'IN_PROGRESS') { scls = 'bg-blue-100 text-blue-700'; stxt = 'Đang thực hiện'; }
                    else { scls = 'bg-slate-100 text-slate-600'; stxt = a.status || 'N/A'; }
                    var pct = parseFloat(a.result_percentage) || 0;
                    var pcls = pct >= 80 ? 'text-emerald-600' : (pct >= 60 ? 'text-amber-600' : 'text-red-600');
                    rtbody.append(
                        '<tr>' +
                        '<td class="font-mono font-medium text-blue-600">' + (a.audit_code || '-') + '</td>' +
                        '<td class="text-slate-700">' + (a.department || '-') + '</td>' +
                        '<td class="text-slate-500">' + (a.audit_date || '-') + '</td>' +
                        '<td class="text-right font-mono font-semibold ' + pcls + '">' + (a.status === 'COMPLETED' ? pct + '%' : '-') + '</td>' +
                        '<td class="text-center"><span class="px-2.5 py-1 rounded-full text-xs font-medium ' + scls + '">' + stxt + '</span></td>' +
                        '</tr>'
                    );
                });
            }
        })
        .fail(function() { console.error('Lỗi tải dữ liệu audit dashboard'); });
    }

    /* ===========================
       LOAD WEEKLY DEPT AUDIT
    =========================== */
    function loadWeeklyDept() {
        // Tính tuần hiện tại
        var now = new Date();
        var dayOfWeek = now.getDay() || 7;
        var monday = new Date(now);
        monday.setDate(now.getDate() - dayOfWeek + 1);
        var sunday = new Date(monday);
        sunday.setDate(monday.getDate() + 6);
        var fmt = function(d) {
            return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth()+1)).slice(-2);
        };
        $('#audit-week-label').text('Tuần: ' + fmt(monday) + ' – ' + fmt(sunday));

        $.ajax({ url: WEEKLY_URL, type: 'GET', dataType: 'json' })
        .done(function(res) {
            if (!res || !res.success) return;
            var tbody = $('#audit_weekly_dept_tbody');
            tbody.empty();
            var missingCount = 0;

            // BC Vi phạm table
            var vpTbody = $('#audit_vipham_tbody');
            vpTbody.empty();
            // BC KPH table
            var kphTbody = $('#audit_kph_tbody');
            kphTbody.empty();
            // Weekly cat table
            var catTbody = $('#audit_weekly_cat_tbody');
            catTbody.empty();

            if (!res.data || !res.data.length) {
                tbody.html('<tr><td colspan="6" class="text-center py-6 text-slate-400">Không có dữ liệu phòng ban</td></tr>');
                return;
            }

            $.each(res.data, function(i, r) {
                var hasThisWeek  = r.this_week > 0;
                var hasLastWeek  = r.last_week > 0;
                var compliance   = parseFloat(r.compliance_pct) || 0;
                var statusHtml   = '';
                var rowCls       = '';

                if (!hasThisWeek) {
                    statusHtml = '<span class="px-2 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700">⚠ Chưa có</span>';
                    rowCls = 'bg-red-50';
                    missingCount++;
                } else {
                    statusHtml = '<span class="px-2 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-700">✓ Đã có</span>';
                }

                var compCls = compliance >= 80 ? 'text-emerald-600' : (compliance >= 60 ? 'text-amber-600' : 'text-red-600');

                tbody.append(
                    '<tr class="border-t border-slate-100 ' + rowCls + '">' +
                    '<td class="px-4 py-2 font-medium text-slate-800">' + (r.department || 'N/A') + '</td>' +
                    '<td class="px-3 py-2 text-center font-bold ' + (hasThisWeek ? 'text-emerald-600' : 'text-red-500') + '">' + r.this_week + '</td>' +
                    '<td class="px-3 py-2 text-center text-slate-600">' + r.last_week + '</td>' +
                    '<td class="px-3 py-2 text-center text-slate-600">' + r.month_total + '</td>' +
                    '<td class="px-3 py-2 text-center font-semibold ' + compCls + '">' + compliance + '%</td>' +
                    '<td class="px-3 py-2 text-center">' + statusHtml + '</td>' +
                    '</tr>'
                );

                // BC Vi phạm rows
                if (r.violate_count > 0 || r.violate_point > 0) {
                    var vpCls = r.violate_count > 5 ? 'text-red-600 font-bold' : 'text-orange-600';
                    vpTbody.append(
                        '<tr class="border-t border-slate-100">' +
                        '<td class="px-3 py-2 text-slate-700">' + (r.department || 'N/A') + '</td>' +
                        '<td class="px-2 py-2 text-center ' + vpCls + '">' + r.violate_count + '</td>' +
                        '<td class="px-2 py-2 text-center text-red-500">-' + r.violate_point + '</td>' +
                        '</tr>'
                    );
                }

                // BC KPH rows
                if (r.kph_total > 0) {
                    var kphPendingCls = r.kph_pending > 0 ? 'text-orange-600 font-semibold' : 'text-slate-400';
                    kphTbody.append(
                        '<tr class="border-t border-slate-100">' +
                        '<td class="px-3 py-2 text-slate-700">' + (r.department || 'N/A') + '</td>' +
                        '<td class="px-2 py-2 text-center text-slate-700">' + r.kph_total + '</td>' +
                        '<td class="px-2 py-2 text-center ' + kphPendingCls + '">' + r.kph_pending + '</td>' +
                        '</tr>'
                    );
                }
            });

            $('#audit-missing-week').text(missingCount);

            // Weekly category results
            if (res.weekly_cats && res.weekly_cats.length) {
                $.each(res.weekly_cats, function(i, c) {
                    var yesCls = c.yes_count > 0 ? 'text-emerald-600' : 'text-slate-400';
                    catTbody.append(
                        '<tr class="border-t border-slate-100">' +
                        '<td class="px-3 py-2 text-slate-700">' + (c.category || 'N/A') + '</td>' +
                        '<td class="px-2 py-2 text-center text-blue-600 font-semibold">' + c.week_count + '</td>' +
                        '<td class="px-2 py-2 text-center"><span class="' + yesCls + '">' + c.yes_count + '✓</span> / <span class="text-red-500">' + c.no_count + '✗</span></td>' +
                        '</tr>'
                    );
                });
            } else {
                catTbody.html('<tr><td colspan="3" class="text-center py-4 text-slate-400">Không có dữ liệu</td></tr>');
            }

            if (vpTbody.children().length === 0) vpTbody.html('<tr><td colspan="3" class="text-center py-4 text-slate-400">Không có vi phạm</td></tr>');
            if (kphTbody.children().length === 0) kphTbody.html('<tr><td colspan="3" class="text-center py-4 text-slate-400">Không có KPH</td></tr>');
        })
        .fail(function() {
            $('#audit_weekly_dept_tbody').html('<tr><td colspan="6" class="text-center py-6 text-red-400">Lỗi tải dữ liệu tuần</td></tr>');
        });
    }

    /* ===========================
       LOAD ACTION PENDING
    =========================== */
    var ACTION_TYPES = {
        're_evaluate': { label: 'Đánh giá lại',  cls: 'bg-blue-100 text-blue-700' },
        'transfer':    { label: 'Thuyên chuyển', cls: 'bg-purple-100 text-purple-700' },
        'resign':      { label: 'Thôi việc',     cls: 'bg-red-100 text-red-700' },
        'promote':     { label: 'Thăng chức',    cls: 'bg-emerald-100 text-emerald-700' },
        'salary':      { label: 'Xét tăng lương',cls: 'bg-amber-100 text-amber-700' },
    };
    var SOURCE_TYPES = {
        'ktnb': { label: 'KTNB',  cls: 'bg-indigo-100 text-indigo-700' },
        'ksrr': { label: 'KSRR',  cls: 'bg-teal-100 text-teal-700' },
        'bod':  { label: 'BOD',   cls: 'bg-rose-100 text-rose-700' },
        'audit':{ label: 'Audit', cls: 'bg-slate-100 text-slate-700' },
    };

    function loadActionPending() {
        $.ajax({ url: ACTION_URL, type: 'GET', dataType: 'json' })
        .done(function(res) {
            var tbody = $('#audit_action_tbody');
            tbody.empty();
            var ktnbList = $('#audit_ktnb_list').empty();
            var ksrrList = $('#audit_ksrr_list').empty();
            var bodList  = $('#audit_bod_list').empty();

            if (!res || !res.success || !res.data.length) {
                tbody.html('<tr><td colspan="7" class="text-center py-6 text-slate-400">Không có yêu cầu chờ duyệt.</td></tr>');
                ktnbList.html('<div class="text-slate-400 text-center py-4">Không có yêu cầu</div>');
                ksrrList.html('<div class="text-slate-400 text-center py-4">Không có yêu cầu</div>');
                bodList.html('<div class="text-slate-400 text-center py-4">Không có yêu cầu</div>');
                return;
            }

            var ktnbCount = 0, ksrrCount = 0, bodCount = 0;

            $.each(res.data, function(i, item) {
                var atInfo  = ACTION_TYPES[item.action_type]  || { label: item.action_type, cls: 'bg-slate-100 text-slate-600' };
                var srcInfo = SOURCE_TYPES[item.source]       || { label: item.source, cls: 'bg-slate-100 text-slate-600' };
                var statusCls = item.status === 'approved' ? 'bg-emerald-100 text-emerald-700'
                              : (item.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                var statusTxt = item.status === 'approved' ? 'Đã duyệt'
                              : (item.status === 'rejected' ? 'Từ chối' : 'Chờ duyệt');

                var actionBtn = item.status === 'pending'
                    ? '<div class="flex gap-1 justify-center">' +
                        '<button onclick="auditApprove(' + item.id + ',\'approved\')" class="px-2 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700">✓ Duyệt</button>' +
                        '<button onclick="auditApprove(' + item.id + ',\'rejected\')" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">✗</button>' +
                      '</div>'
                    : '<span class="text-xs text-slate-400">—</span>';

                tbody.append(
                    '<tr class="border-t border-slate-100 hover:bg-slate-50">' +
                    '<td class="px-4 py-2 font-medium text-slate-800">' + (item.staff_name || 'N/A') + '</td>' +
                    '<td class="px-3 py-2 text-center"><span class="px-2 py-0.5 rounded text-xs font-semibold ' + atInfo.cls + '">' + atInfo.label + '</span></td>' +
                    '<td class="px-3 py-2 text-slate-600 text-xs max-w-xs">' + (item.reason || '—') + '</td>' +
                    '<td class="px-3 py-2 text-center"><span class="px-2 py-0.5 rounded text-xs ' + srcInfo.cls + '">' + srcInfo.label + '</span></td>' +
                    '<td class="px-3 py-2 text-center text-xs text-slate-500">' + (item.created_date || '—') + '</td>' +
                    '<td class="px-3 py-2 text-center"><span class="px-2 py-0.5 rounded text-xs font-semibold ' + statusCls + '">' + statusTxt + '</span></td>' +
                    '<td class="px-3 py-2 text-center">' + actionBtn + '</td>' +
                    '</tr>'
                );

                // Phân loại sang KTNB / KSRR / BOD
                var shortItem = '<div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-100 text-xs">' +
                    '<span class="font-medium text-slate-700">' + (item.staff_name || 'N/A') + '</span>' +
                    '<span class="px-1.5 py-0.5 rounded ' + atInfo.cls + '">' + atInfo.label + '</span>' +
                    '</div>';
                if (item.source === 'ktnb') { ktnbList.append(shortItem); ktnbCount++; }
                if (item.source === 'ksrr') { ksrrList.append(shortItem); ksrrCount++; }
                if (item.source === 'bod')  { bodList.append(shortItem);  bodCount++;  }
            });

            if (ktnbCount === 0) ktnbList.html('<div class="text-slate-400 text-center py-4">Không có yêu cầu</div>');
            if (ksrrCount === 0) ksrrList.html('<div class="text-slate-400 text-center py-4">Không có yêu cầu</div>');
            if (bodCount  === 0) bodList.html('<div class="text-slate-400 text-center py-4">Không có yêu cầu</div>');
        })
        .fail(function() {
            $('#audit_action_tbody').html('<tr><td colspan="7" class="text-center py-6 text-red-400">Lỗi tải dữ liệu Action</td></tr>');
        });
    }

    /* Approve / Reject action */
    window.auditApprove = function(id, status) {
        var label = status === 'approved' ? 'Duyệt' : 'Từ chối';
        if (!confirm(label + ' yêu cầu này?')) return;
        $.ajax({
            url: APPROVE_URL, type: 'POST', dataType: 'json',
            data: { id: id, status: status }
        }).done(function(res) {
            if (res && res.success) {
                loadActionPending();
            } else {
                alert('Lỗi: ' + (res.message || 'Không thành công'));
            }
        });
    };

    // ===== INIT =====
    loadAuditDashboard();
    loadWeeklyDept();
    loadActionPending();

    // Auto refresh
    setInterval(loadAuditDashboard, 30000);
    setInterval(function() { loadWeeklyDept(); loadActionPending(); }, 60000);
})();
</script>
