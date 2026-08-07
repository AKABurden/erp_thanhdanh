<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-xl border border-red-200 bg-red-50 shadow-sm p-6">
            <h3 class="text-sm font-medium text-red-800 mb-2">Tổng Impact (Rủi ro cao)</h3>
            <div class="text-3xl font-bold tracking-tight text-red-900" id="bod_total_impact">Đang tải...</div>
            <div class="mt-2 flex items-center gap-2 text-xs text-red-600">
                <span id="bod_sev_red_badge" class="px-2 py-0.5 rounded-full bg-red-200 font-semibold">0</span> Đỏ
                <span id="bod_sev_yellow_badge" class="px-2 py-0.5 rounded-full bg-yellow-200 text-yellow-800 font-semibold ml-2">0</span> Vàng
            </div>
        </div>
        <div class="rounded-xl border shadow-sm p-6" id="bod_status_card">
            <h3 class="text-sm font-medium mb-2" id="bod_status_title">Trạng thái Hệ thống</h3>
            <div class="text-3xl font-bold tracking-tight" id="bod_system_label">Đang tải...</div>
            <div class="mt-2 text-xs" id="bod_alerts_summary"></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Top Phòng ban rủi ro</h3>
            <div class="text-2xl font-bold tracking-tight text-slate-900" id="bod_top_depts">Đang tải...</div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3" id="bod_quick_stats">
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">Ngân sách</div>
            <div class="text-lg font-bold text-slate-900" id="qs_budget">-</div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">Tuân thủ TB</div>
            <div class="text-lg font-bold text-emerald-600" id="qs_compliance">-</div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">Audit (YTD)</div>
            <div class="text-lg font-bold text-blue-600" id="qs_audits">-</div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">CAPA Mở</div>
            <div class="text-lg font-bold text-orange-600" id="qs_capa">-</div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">Vi phạm SX</div>
            <div class="text-lg font-bold text-red-600" id="qs_violations">-</div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">KH Nguy cơ</div>
            <div class="text-lg font-bold text-red-600" id="qs_client_risk">-</div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">NCC Nguy cơ</div>
            <div class="text-lg font-bold text-yellow-600" id="qs_supplier_risk">-</div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
            <div class="text-xs text-slate-500 mb-1">MoM</div>
            <div class="text-lg font-bold" id="qs_mom">-</div>
        </div>
    </div>

    <!-- Charts & Heatmap -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Radar 5 Trục Rủi Ro</h3>
            <div id="bod_radar_chart" style="min-height: 350px;"></div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Risk Heatmap (Theo phòng ban)</h3>
            <div id="bod_heatmap_container" class="grid grid-cols-3 gap-2 min-h-[320px]">
                <div class="col-span-3 flex items-center justify-center text-slate-400 text-sm">Đang tải...</div>
            </div>
        </div>
    </div>

    <!-- Checklist Xử Lý, Cải Tiến & Form Import -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Checklist Table (Liên kết tạo công việc) -->
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase flex items-center">
                        <i data-lucide="list-checks" class="w-4 h-4 mr-2 text-indigo-600"></i>
                        Danh sách Cần Khắc Phục / Cải Tiến (CAPA)
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Quản lý và liên kết tạo công việc xử lý vi phạm, vấn đề audit.</p>
                </div>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Mã BC/Audit</th>
                            <th class="px-4 py-3 text-left font-semibold">Vấn đề cần xử lý</th>
                            <th class="px-4 py-3 text-left font-semibold">Phòng ban</th>
                            <th class="px-4 py-3 text-center font-semibold">Tình trạng</th>
                            <th class="px-4 py-3 text-center font-semibold">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="bod_checklist_tbody" class="divide-y divide-slate-100">
                        <!-- Render qua JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Import Form -->
        <div class="rounded-xl border border-blue-200 bg-blue-50 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-blue-900 uppercase flex items-center mb-2">
                    <i data-lucide="upload-cloud" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Import Thiết Lập Quy Trình
                </h3>
                <p class="text-xs text-blue-700 mb-6 leading-relaxed">
                    Sử dụng form chuẩn bên dưới để import hàng loạt Quy trình, Checklist giám sát hoặc Tham số an toàn cho phòng ban. Giúp đồng bộ và chuẩn hóa quy trình nhanh chóng.
                </p>

                <div class="space-y-4">
                    <a href="<?= base_url('uploads/template/Template_ma_cong_viec.xlsx?vs=1.6') ?>" target="_blank" class="w-full flex items-center justify-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-bold text-blue-700 bg-white hover:bg-blue-100 transition-colors shadow-sm">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i> Tải Form Chuẩn (Excel)
                    </a>

                    <div class="relative border-2 border-dashed border-blue-400 bg-white rounded-lg p-6 text-center hover:bg-blue-100 hover:border-blue-500 transition-all cursor-pointer group" onclick="document.getElementById('bod_import_file').click()">
                        <i data-lucide="file-spreadsheet" class="w-8 h-8 text-blue-400 group-hover:text-blue-600 mx-auto mb-3 transition-colors"></i>
                        <span class="text-sm font-bold text-blue-800">Bấm để chọn file Import</span>
                        <p class="text-[10px] text-slate-400 mt-1">Hỗ trợ định dạng .xlsx, .xls</p>
                        <input type="file" id="bod_import_file" name="file" class="hidden" accept=".xlsx, .xls" onchange="$('#bod_file_name_display').text(this.files[0]?.name || '')">
                    </div>
                    <div id="bod_file_name_display" class="text-xs text-center text-blue-600 font-medium truncate"></div>
                </div>
            </div>

            <button id="btn_execute_bod_import" class="mt-6 w-full flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm" onclick="executeBodImport()">
                <i data-lucide="play-circle" class="w-5 h-5 mr-2"></i> Thực Hiện Import
            </button>
        </div>

    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Highcharts (đã có từ audit tab) -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>


<script>
    (function() {
        var AJAX_URL = '<?= site_url("admin/RiskDashboard/get_bod_dashboard_data") ?>';

        function fmtMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        function loadBodDashboard() {
            $.ajax({
                    url: AJAX_URL,
                    type: 'GET',
                    dataType: 'json'
                })
                .done(function(res) {
                    if (!res || !res.success) return;
                    var d = res.data;
                    var s = d.summary;
                    var qs = d.quick_stats;

                    // 1. Summary Cards
                    document.getElementById('bod_total_impact').textContent = fmtMoney(s.total_impact) + ' VNĐ';
                    document.getElementById('bod_sev_red_badge').textContent = s.sev_red;
                    document.getElementById('bod_sev_yellow_badge').textContent = s.sev_yellow;

                    // Status Card - thay đổi màu theo trạng thái
                    var statusCard = document.getElementById('bod_status_card');
                    var statusTitle = document.getElementById('bod_status_title');
                    var statusLabel = document.getElementById('bod_system_label');
                    var alertsSummary = document.getElementById('bod_alerts_summary');

                    var statusColors = {
                        'RED': {
                            border: 'border-red-200',
                            bg: 'bg-red-50',
                            title: 'text-red-800',
                            label: 'text-red-900',
                            summary: 'text-red-600'
                        },
                        'ORANGE': {
                            border: 'border-orange-200',
                            bg: 'bg-orange-50',
                            title: 'text-orange-800',
                            label: 'text-orange-900',
                            summary: 'text-orange-600'
                        },
                        'YELLOW': {
                            border: 'border-yellow-200',
                            bg: 'bg-yellow-50',
                            title: 'text-yellow-800',
                            label: 'text-yellow-900',
                            summary: 'text-yellow-600'
                        },
                        'GREEN': {
                            border: 'border-emerald-200',
                            bg: 'bg-emerald-50',
                            title: 'text-emerald-800',
                            label: 'text-emerald-900',
                            summary: 'text-emerald-600'
                        }
                    };
                    var sc = statusColors[s.system_status] || statusColors['GREEN'];
                    statusCard.className = 'rounded-xl border shadow-sm p-6 ' + sc.border + ' ' + sc.bg;
                    statusTitle.className = 'text-sm font-medium mb-2 ' + sc.title;
                    statusLabel.className = 'text-3xl font-bold tracking-tight ' + sc.label;
                    statusLabel.textContent = s.system_label + ' (' + s.system_status + ')';
                    alertsSummary.className = 'mt-2 text-xs ' + sc.summary;
                    alertsSummary.textContent = 'Tổng ' + s.total_alerts + ' cảnh báo (' + s.sev_red + ' đỏ, ' + s.sev_yellow + ' vàng)';

                    document.getElementById('bod_top_depts').textContent = s.top_dept_label;

                    // 2. Quick Stats
                    document.getElementById('qs_budget').textContent = qs.budget_usage + '%';
                    document.getElementById('qs_compliance').textContent = qs.avg_compliance + '%';
                    document.getElementById('qs_audits').textContent = qs.total_audits_ytd;
                    document.getElementById('qs_capa').textContent = qs.capa_open;
                    document.getElementById('qs_violations').textContent = qs.violations_month;
                    document.getElementById('qs_client_risk').textContent = qs.client_risk;
                    document.getElementById('qs_supplier_risk').textContent = qs.supplier_risk;

                    var momEl = document.getElementById('qs_mom');
                    momEl.textContent = (qs.mom_change >= 0 ? '+' : '') + qs.mom_change + '%';
                    momEl.className = 'text-lg font-bold ' + (qs.mom_change > 10 ? 'text-red-600' : (qs.mom_change < -5 ? 'text-emerald-600' : 'text-slate-900'));

                    // 3. Radar Chart (Highcharts Polar/Spider)
                    var r = d.radar;
                    Highcharts.chart('bod_radar_chart', {
                        chart: {
                            polar: true,
                            type: 'area'
                        },
                        title: {
                            text: null
                        },
                        pane: {
                            size: '85%'
                        },
                        xAxis: {
                            categories: ['Tài chính', 'Vận hành', 'Tuân thủ', 'Chiến lược', 'Uy tín'],
                            tickmarkPlacement: 'on',
                            lineWidth: 0,
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    fontWeight: '600'
                                }
                            }
                        },
                        yAxis: {
                            gridLineInterpolation: 'polygon',
                            lineWidth: 0,
                            min: 0,
                            max: 100,
                            tickInterval: 25,
                            labels: {
                                format: '{value}',
                                style: {
                                    fontSize: '10px'
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            pointFormat: '<b>{point.y}</b> điểm rủi ro<br/>'
                        },
                        series: [{
                            name: 'Mức độ rủi ro',
                            data: [r.tai_chinh, r.van_hanh, r.tuan_thu, r.chien_luoc, r.uy_tin],
                            pointPlacement: 'on',
                            color: 'rgba(239, 68, 68, 0.6)',
                            fillColor: 'rgba(239, 68, 68, 0.15)',
                            lineWidth: 2,
                            marker: {
                                radius: 4,
                                fillColor: '#ef4444'
                            }
                        }],
                        legend: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        }
                    });

                    // 4. Heatmap
                    var heatmapContainer = document.getElementById('bod_heatmap_container');
                    heatmapContainer.innerHTML = '';

                    if (d.heatmap.length === 0) {
                        heatmapContainer.innerHTML = '<div class="col-span-3 flex items-center justify-center text-slate-400 text-sm">Không có dữ liệu rủi ro phòng ban</div>';
                    } else {
                        var colorMap = {
                            'red': {
                                bg: 'bg-red-500',
                                text: 'text-white'
                            },
                            'orange': {
                                bg: 'bg-orange-400',
                                text: 'text-white'
                            },
                            'yellow': {
                                bg: 'bg-yellow-300',
                                text: 'text-yellow-900'
                            },
                            'green': {
                                bg: 'bg-green-500',
                                text: 'text-white'
                            }
                        };

                        d.heatmap.forEach(function(h) {
                            var cm = colorMap[h.color] || colorMap['green'];
                            var detailText = h.details && h.details.length > 0 ? h.details.join(', ') : '';
                            var div = document.createElement('div');
                            div.className = cm.bg + ' ' + cm.text + ' flex flex-col items-center justify-center rounded-md font-medium text-sm p-3 text-center min-h-[80px] transition-transform hover:scale-105 cursor-default';
                            div.title = detailText ? h.department + ': ' + detailText : h.department;
                            div.innerHTML = '<span class="font-bold text-base">' + h.department + '</span>' +
                                '<span class="text-xs opacity-90 mt-1">(' + h.level + ')</span>' +
                                (detailText ? '<span class="text-[10px] opacity-75 mt-0.5 leading-tight">' + detailText + '</span>' : '');
                            heatmapContainer.appendChild(div);
                        });
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Lỗi tải dữ liệu BOD dashboard:', error);
                    document.getElementById('bod_total_impact').textContent = 'Lỗi tải';
                    document.getElementById('bod_system_label').textContent = 'Lỗi';
                    document.getElementById('bod_top_depts').textContent = 'Lỗi';
                })
                .always(function() {
                    // Load Checklists Data từ Server (Dữ liệu thật)
                    loadChecklistsData();
                });
        }

        function loadChecklistsData() {
            var tbody = $('#bod_checklist_tbody').empty();
            tbody.append('<tr><td colspan="5" class="text-center py-4 text-slate-400">Đang tải dữ liệu thực tế...</td></tr>');

            $.get('<?= site_url("admin/RiskDashboard/get_bod_capa_checklists") ?>').done(function(res) {
                if (!res || !res.success) return;
                tbody.empty();
                var data = res.data;

                if (!data || data.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center py-4 text-slate-400">Không có dữ liệu khắc phục.</td></tr>');
                    return;
                }

                $.each(data, function(i, item) {
                    var stColor = item.status === 'PENDING' ? 'bg-rose-100 text-rose-700' :
                        (item.status === 'IN_PROGRESS' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                    var stLabel = item.status === 'PENDING' ? 'Chưa xử lý' :
                        (item.status === 'IN_PROGRESS' ? 'Đang xử lý' : 'Đã xử lý');

                    var btnAction = '';
                    if (item.status === 'COMPLETED') {
                        btnAction = '<button class="px-2 py-1 bg-slate-100 text-slate-400 rounded text-[10px] cursor-not-allowed border border-slate-200">Đã khóa</button>';
                    } else if (item.type === 'audit') {
                        btnAction = `<button onclick="viewAuditModal(${item.id})" class="px-2 py-1 bg-blue-600 text-white rounded text-[10px] hover:bg-blue-700 font-medium inline-flex items-center transition-colors border-none"><i data-lucide="eye" class="w-3 h-3 mr-1"></i> Xem KQ Audit</button>`;
                    } else {
                        btnAction = `<button onclick="viewReportModal(${item.id})" class="px-2 py-1 bg-slate-600 text-white rounded text-[10px] hover:bg-slate-700 font-medium inline-flex items-center transition-colors border-none"><i data-lucide="file-text" class="w-3 h-3 mr-1"></i> Xem BC Vi Phạm</button>`;
                    }

                    var row = `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-semibold text-slate-700">${item.code}</td>
                        <td class="px-4 py-3 text-slate-600 font-medium truncate max-w-[200px]" title="${item.title}">${item.title}</td>
                        <td class="px-4 py-3 text-slate-500">${item.dept}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${stColor}">${stLabel}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            ${btnAction}
                        </td>
                    </tr>
                `;
                    tbody.append(row);
                });
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }).fail(function() {
                tbody.html('<tr><td colspan="5" class="text-center py-4 text-red-500">Lỗi kết nối cơ sở dữ liệu Checklist</td></tr>');
            });
        }

        // Load lần đầu
        loadBodDashboard();

        // Auto refresh mỗi 60 giây
        setInterval(loadBodDashboard, 60000);

    })();

    // CÁC HÀM TOÀN CỤC (GLOBAL) ĐỂ ONCLICK GỌI ĐƯỢC
    // CÁC HÀM TOÀN CỤC (GLOBAL) ĐỂ ONCLICK GỌI ĐƯỢC
    window.viewAuditModal = function(id) {
        openBodIframePopup('<?= admin_url('RiskDashboard/view_checklist_full/') ?>' + id + '/audit');
    };
    
    window.viewReportModal = function(id) {
        openBodIframePopup('<?= admin_url('RiskDashboard/view_checklist_full/') ?>' + id + '/report');
    };

    function openBodIframePopup(url) {
        $('#bod_custom_overlay').remove();
        
        var overlayHtml = 
            '<div id="bod_custom_overlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:999999; display:flex; align-items:center; justify-content:center; padding:15px;">' +
                '<div id="bod_custom_content" style="background:#fff; width:100%; max-width:1150px; height:92vh; border-radius:12px; position:relative; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.5); overflow:hidden;">' +
                    '<div style="padding:15px 20px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">' +
                        '<span style="font-weight:700; color:#0f172a;"><i class="fa fa-clipboard-check"></i> CHI TIẾT NỘI DUNG</span>' +
                        '<button id="bod_close_btn" style="background:#f1f5f9; border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; font-size:20px; color:#64748b;">&times;</button>' +
                    '</div>' +
                    '<iframe id="bod_iframe" src="' + url + '" style="width:100%; flex:1; border:none;" onload="document.getElementById(\'bod_loading_box\').style.display=\'none\'"></iframe>' +
                    '<div id="bod_loading_box" style="position:absolute; top:60px; left:0; width:100%; height:calc(100% - 60px); background:#fff; display:flex; align-items:center; justify-content:center; flex-direction:column; z-index:10;">' +
                        '<i class="fa fa-spinner fa-spin fa-3x" style="color:#2563eb;"></i>' +
                        '<p style="margin-top:15px; color:#475569;">Đang tải nội dung chuẩn CSS...</p>' +
                    '</div>' +
                '</div>' +
            '</div>';
            
        $('body').append(overlayHtml).css('overflow', 'hidden');
        
        $('#bod_close_btn').on('click', function() {
            $('#bod_custom_overlay').remove();
            $('body').css('overflow', 'auto');
        });

        $('#bod_custom_overlay').on('click', function(e) {
            if (e.target.id === 'bod_custom_overlay') {
                $(this).remove();
                $('body').css('overflow', 'auto');
            }
        });
    }

    // HÀM XỬ LÝ IMPORT QUY TRÌNH
    window.executeBodImport = function() {
        var fileInput = document.getElementById('bod_import_file');
        var file = fileInput.files[0];
        
        if (!file) {
            alert_float('warning', 'Vui lòng chọn file Excel trước khi thực hiện!');
            return;
        }

        var btn = $('#btn_execute_bod_import');
        var originalHtml = btn.html();
        
        // Disable nút và hiện hiệu ứng đang chạy
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i> Đang xử lý...');

        var formData = new FormData();
        formData.append('file', file);
        
        // Thêm CSRF Token
        if (typeof(csrfData) !== 'undefined') {
            formData.append(csrfData.token_name, csrfData.hash);
        }

        $.ajax({
            url: '<?= admin_url('category_tasks/import') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var res = JSON.parse(response);
                alert_float(res.alert_type, res.message);
                
                if (res.success) {
                    fileInput.value = '';
                    $('#bod_file_name_display').text('');
                    // Reload dashboard nếu cần
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra trong quá trình upload!');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    };
</script>