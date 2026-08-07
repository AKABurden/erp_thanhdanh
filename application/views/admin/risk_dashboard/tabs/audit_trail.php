<div class="space-y-6">
    <!-- Cảnh báo Gian lận & Rủi ro -->
    <div id="at_alerts_container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Dashboard alerts will be loaded here dynamically -->
    </div>

    <!-- Bảng Audit Trail -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-base font-semibold text-slate-900">Nhật ký Hệ thống (Audit Trail)</h3>
            <div class="flex space-x-2">
                <input type="text" id="at_search_input" placeholder="Tìm kiếm user, bảng, hành động..." class="border border-slate-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                <button id="at_btn_search" class="bg-slate-900 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-slate-800 transition-colors">Tìm kiếm</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Thời gian</th>
                        <th class="px-6 py-3 text-left">Người sửa (User)</th>
                        <th class="px-6 py-3 text-left">Hành động</th>
                        <th class="px-6 py-3 text-center">Phê duyệt</th>
                        <th class="px-6 py-3 text-center">Lần sửa</th>
                        <th class="px-6 py-3 text-left">Chi tiết thay đổi</th>
                    </tr>
                </thead>
                <tbody id="at_logs_tbody" class="divide-y divide-gray-200 text-sm">
                    <tr>
                        <td colspan="6" class="text-center text-slate-400 py-8">Đang tải dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    (function() {
        var AJAX_URL = '<?= site_url("admin/RiskDashboard/get_audit_trail_dashboard_data") ?>';

        function loadAuditTrailData() {
            var searchQuery = $('#at_search_input').val();

            $.ajax({
                    url: AJAX_URL,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        search: searchQuery
                    }
                })
                .done(function(res) {
                    if (!res || !res.success) return;
                    var d = res.data;

                    // Render Alerts
                    var alertsContainer = $('#at_alerts_container');
                    alertsContainer.empty();
                    if (d.alerts && d.alerts.length > 0) {
                        $.each(d.alerts, function(i, a) {
                            var alertHtml =
                                '<div class="rounded-xl border ' + a.bg_color + ' shadow-sm p-6 flex items-start">' +
                                '<i data-lucide="' + a.icon + '" class="w-8 h-8 ' + a.icon_color + ' mr-4 flex-shrink-0"></i>' +
                                '<div>' +
                                '<h3 class="text-lg font-semibold ' + a.title_color + '">' + a.title + '</h3>' +
                                '<p class="text-sm ' + a.desc_color + ' mt-1">' + a.desc + '</p>' +
                                '</div>' +
                                '</div>';
                            alertsContainer.append(alertHtml);
                        });
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    }

                    // Render Logs Table
                    var tbody = $('#at_logs_tbody');
                    tbody.empty();
                    if (d.logs && d.logs.length > 0) {
                        $.each(d.logs, function(i, log) {
                            // Xóa tags HTML hoặc escape để an toàn
                            var escapedDetails = $('<div>').text(log.details).html();

                            if (escapedDetails.length > 100) {
                                escapedDetails = '<span title="' + escapedDetails + '">' + escapedDetails.substring(0, 100) + '...</span>';
                            }

                            var approveHtml = log.is_approved ? 
                                '<span class="text-emerald-600 font-bold"><i class="fa fa-check-circle"></i> Có</span>' : 
                                '<span class="text-slate-400">Không</span>';

                            var rowHtml = 
                                '<tr class="hover:bg-gray-50">' +
                                '<td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">' + log.date + '</td>' +
                                '<td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">' + log.user + '</td>' +
                                '<td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded text-[10px] font-bold ' + log.action_color + '">' + log.action + '</span></td>' +
                                '<td class="px-6 py-4 text-center">' + approveHtml + '</td>' +
                                '<td class="px-6 py-4 text-center"><span class="bg-slate-100 px-2 py-1 rounded-full text-xs font-bold">' + log.revision_count + '</span></td>' +
                                '<td class="px-6 py-4 text-slate-500 text-xs">' + escapedDetails + '</td>' +
                                '</tr>';
                            tbody.append(rowHtml);
                        });
                    } else {
                        tbody.html('<tr><td colspan="6" class="text-center text-slate-400 py-8">Không tìm thấy dữ liệu.</td></tr>');
                    }
                })
                .fail(function() {
                    var tbody = $('#at_logs_tbody');
                    tbody.html('<tr><td colspan="6" class="text-center text-red-500 py-8">Lỗi tải dữ liệu. Vui lòng thử lại.</td></tr>');
                });
        }

        // Bind event
        $('#at_btn_search').on('click', function() {
            loadAuditTrailData();
        });

        $('#at_search_input').on('keypress', function(e) {
            if (e.which === 13) {
                loadAuditTrailData();
            }
        });

        // Initial load
        loadAuditTrailData();
    })();
</script>