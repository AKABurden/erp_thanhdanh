<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">So sánh Tuần (WoW)</h3>
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-bold tracking-tight text-slate-900" id="ss-wow-val">--%</div>
                <div class="text-xs font-bold" id="ss-wow-diff"></div>
            </div>
            <p class="text-xs text-slate-500 flex items-center mt-2 font-medium" id="ss-wow-desc">
                <i data-lucide="minus" class="w-3.5 h-3.5 mr-1" id="ss-wow-icon"></i> <span id="ss-wow-text">Đang tải...</span>
            </p>
        </div>
        <!-- Card 2 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">So sánh Tháng (MoM)</h3>
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-bold tracking-tight text-slate-900" id="ss-mom-val">--%</div>
                <div class="text-xs font-bold" id="ss-mom-diff"></div>
            </div>
            <p class="text-xs text-slate-500 flex items-center mt-2 font-medium" id="ss-mom-desc">
                <i data-lucide="minus" class="w-3.5 h-3.5 mr-1" id="ss-mom-icon"></i> <span id="ss-mom-text">Đang tải...</span>
            </p>
        </div>
        <!-- Card 3 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">So sánh Quý (QoQ)</h3>
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-bold tracking-tight text-slate-900" id="ss-qoq-val">--%</div>
                <div class="text-xs font-bold" id="ss-qoq-diff"></div>
            </div>
            <p class="text-xs text-slate-500 flex items-center mt-2 font-medium" id="ss-qoq-desc">
                <i data-lucide="minus" class="w-3.5 h-3.5 mr-1" id="ss-qoq-icon"></i> <span id="ss-qoq-text">Đang tải...</span>
            </p>
        </div>
        <!-- Card 4 -->
        <div class="rounded-xl border border-slate-200 bg-white text-slate-950 shadow-sm p-6">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Trạng thái kỳ này</h3>
            <div class="flex items-center space-x-3 mt-1">
                <div class="p-2 bg-slate-100 rounded-lg" id="ss-status-icon-bg">
                    <i data-lucide="loader" class="w-6 h-6 text-slate-600 animate-spin" id="ss-status-icon"></i>
                </div>
                <span class="text-2xl font-bold tracking-tight text-slate-600" id="ss-status-text">--</span>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Xu hướng cảnh báo theo tuần (4 tuần)</h3>
            <div class="h-64 w-full">
                <canvas id="ssTrendChart"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Xu hướng thiệt hại / chi phí (VNĐ)</h3>
            <div class="h-64 w-full">
                <canvas id="ssDamageChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Table PARAMS -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Cấu hình tham số (PARAMS)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Ngưỡng % biến động kích hoạt cảnh báo WoW / MoM / QoQ. Thay đổi sẽ ảnh hưởng tới trạng thái tổng quan.</p>
            </div>
            <button onclick="loadRiskParams()" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 border border-slate-200 rounded-lg px-3 py-1.5 hover:bg-slate-50 transition">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Tải lại
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="params-table">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tham số</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Chỉ số</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Hiện tại</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Ngưỡng (%)</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Mức độ</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Mô tả</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Hành động</th>
                    </tr>
                </thead>
                <tbody id="params-tbody">
                    <tr><td colspan="6" class="text-center py-8 text-slate-400 text-sm">
                        <i data-lucide="loader" class="w-4 h-4 animate-spin inline-block mr-2"></i> Đang tải tham số...
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== MODAL SỬA NGƯỠNG ===== -->
<div id="modal-edit-param" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h4 class="text-base font-semibold text-slate-900" id="modal-param-title">Sửa ngưỡng</h4>
            <button onclick="closeParamModal()" class="text-slate-400 hover:text-slate-700 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-edit-param" onsubmit="submitParamForm(event)">
            <input type="hidden" id="ep-param-key">
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Ngưỡng kích hoạt (%)</label>
                    <div class="relative">
                        <input type="number" id="ep-threshold" min="0" max="999" step="0.1"
                            class="w-full border border-slate-200 rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ví dụ: 10">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">%</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Biến động vượt ngưỡng này sẽ kích hoạt cảnh báo tương ứng.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mô tả</label>
                    <input type="text" id="ep-description"
                        class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Mô tả ngắn về ngưỡng này">
                </div>
                <div id="ep-alert-box" class="hidden rounded-lg px-4 py-2.5 text-xs font-medium"></div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeParamModal()"
                    class="px-4 py-2 text-sm text-slate-600 hover:text-slate-900 border border-slate-200 rounded-lg hover:bg-slate-100 transition">Huỷ</button>
                <button type="submit" id="ep-save-btn"
                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition flex items-center gap-2">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// Khởi chạy
function initSoSanh() {
    if (typeof jQuery === 'undefined') {
        setTimeout(initSoSanh, 100);
        return;
    }
    window.$ = jQuery;
    
    // Gọi dashboard
    $.ajax({
        url: "<?= site_url('admin/RiskDashboard/get_so_sanh_dashboard_data') ?>",
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const data = response.data;
                window.lastDashboardData = data; // Lưu để dùng cho bảng tham số
                updateCompareCard('wow', data.wow, data.wow_raw.curr, data.wow_raw.prev);
                updateCompareCard('mom', data.mom, data.mom_raw.curr, data.mom_raw.prev);
                updateCompareCard('qoq', data.qoq, data.qoq_raw.curr, data.qoq_raw.prev);
                updateStatusCard(data.status);
                renderTrendChart(data.trend.labels, data.trend.alerts);
                renderDamageChart(data.trend.labels, data.trend.damages);
                if (typeof lucide !== 'undefined') lucide.createIcons();
                
                // Refresh params table if it's already loaded to show "Hiện tại"
                loadRiskParams();
            }
        },
        error: function(err) {
            console.error('[SoSanh] Lỗi load dashboard dữ liệu:', err);
        }
    });

    // Gọi params
    loadRiskParams();
    
    // Event binding
    $('#modal-edit-param').on('click', function(e) {
        if ($(e.target).is('#modal-edit-param')) closeParamModal();
    });
}

function updateCompareCard(prefix, pct, curr, prev) {
    if (typeof jQuery === 'undefined') return;
    let sign = pct > 0 ? '+' : '';
    $(`#ss-${prefix}-val`).text(sign + pct + '%');
    
    // Tính biến động tuyệt đối
    let diff = curr - prev;
    let diffSign = diff > 0 ? '+' : '';
    let diffLabel = diff > 0 ? 'Tăng' : (diff < 0 ? 'Giảm' : 'Biến động');
    // Nếu < 0 thì màu đỏ, > 0 thì màu xanh (theo yêu cầu người dùng)
    let diffColor = diff < 0 ? 'text-red-600' : (diff > 0 ? 'text-green-600' : 'text-slate-500');
    
    $(`#ss-${prefix}-diff`)
        .text(`${diffLabel}: ${diffSign}${diff}`)
        .removeClass('text-red-600 text-green-600 text-slate-500')
        .addClass(diffColor);

    let text = `${curr} (kỳ trước: ${prev})`;
    $(`#ss-${prefix}-text`).text(text);
    
    let wrapper = $(`#ss-${prefix}-desc`);
    let icon = $(`#ss-${prefix}-icon`);
    wrapper.removeClass('text-slate-500 text-red-500 text-green-500 text-yellow-500');
    
    if (pct < 0) {
        wrapper.addClass('text-red-500'); 
        icon.attr('data-lucide', 'arrow-down-right');
    } else if (pct > 0) {
        wrapper.addClass('text-green-500'); 
        icon.attr('data-lucide', 'arrow-up-right');
    } else {
        wrapper.addClass('text-slate-500');
        icon.attr('data-lucide', 'minus');
    }
}

function updateStatusCard(status) {
    if (typeof jQuery === 'undefined') return;
    let textObj = $('#ss-status-text');
    let bgObj = $('#ss-status-icon-bg');
    let iconObj = $('#ss-status-icon');
    
    bgObj.removeClass('bg-slate-100 bg-red-100 bg-yellow-100 bg-green-100');
    textObj.removeClass('text-slate-600 text-red-600 text-yellow-600 text-green-600');
    iconObj.removeClass('text-slate-600 text-red-600 text-yellow-600 text-green-600 animate-spin');
    
    if (status === 'ALERT') {
        textObj.text('NGUY HIỂM').addClass('text-red-600');
        bgObj.addClass('bg-red-100');
        iconObj.addClass('text-red-600').attr('data-lucide', 'alert-triangle');
    } else if (status === 'WARNING') {
        textObj.text('CẢNH BÁO').addClass('text-yellow-600');
        bgObj.addClass('bg-yellow-100');
        iconObj.addClass('text-yellow-600').attr('data-lucide', 'alert-circle');
    } else {
        textObj.text('ỔN ĐỊNH').addClass('text-green-600');
        bgObj.addClass('bg-green-100');
        iconObj.addClass('text-green-600').attr('data-lucide', 'check-circle');
    }
}

let trendChartInstance = null;
function renderTrendChart(labels, data) {
    const el = document.getElementById('ssTrendChart');
    if (!el) return;
    const ctx = el.getContext('2d');
    if (trendChartInstance) trendChartInstance.destroy();
    if (typeof Chart === 'undefined') return;

    trendChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Số sự kiện rủi ro',
                data: data,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#ef4444',
                pointRadius: 4,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
}

let damageChartInstance = null;
function renderDamageChart(labels, data) {
    const el = document.getElementById('ssDamageChart');
    if (!el) return;
    const ctx = el.getContext('2d');
    if (damageChartInstance) damageChartInstance.destroy();
    if (typeof Chart === 'undefined') return;

    damageChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Thiệt hại / Vượt ngân sách (VNĐ)',
                data: data,
                backgroundColor: '#f59e0b',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency', currency: 'VND', maximumSignificantDigits: 3
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
}

function loadRiskParams() {
    if (typeof jQuery === 'undefined') return;
    $('#params-tbody').html('<tr><td colspan="6" class="text-center py-8 text-slate-400 text-sm"><i data-lucide="loader" class="w-4 h-4 animate-spin inline-block mr-2"></i> Đang tải...</td></tr>');
    if (typeof lucide !== 'undefined') lucide.createIcons();

    $.ajax({
        url: "<?= site_url('admin/RiskDashboard/get_risk_params') ?>",
        type: 'GET',
        success: function(res) {
            if (typeof res === 'string') {
                try { res = JSON.parse(res); } catch(e) {
                    $('#params-tbody').html('<tr><td colspan="6" class="text-center py-8 text-red-400">Lỗi format dữ liệu.</td></tr>');
                    return;
                }
            }
            if (!res || !res.success || !res.data || !res.data.length) {
                $('#params-tbody').html('<tr><td colspan="6" class="text-center py-8 text-slate-400">Không có dữ liệu cấu hình.</td></tr>');
                return;
            }
            const metricLabel = { wow: 'WoW (Tuần)', mom: 'MoM (Tháng)', qoq: 'QoQ (Quý)' };
            let html = '';
            res.data.forEach(function(p) {
                const sevBadge = p.severity === 'RED'
                    ? '<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 text-red-800 rounded-md text-xs font-semibold">🔴 RED</span>'
                    : '<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-semibold">🟡 YELLOW</span>';
                
                // Lấy giá trị hiện tại từ dashboard data
                let currentVal = '--';
                let currentClass = 'text-slate-400';
                if (window.lastDashboardData && window.lastDashboardData[p.metric] !== undefined) {
                    const val = window.lastDashboardData[p.metric];
                    currentVal = (val > 0 ? '+' : '') + val + '%';
                    // Nếu < 0 thì màu đỏ, > 0 thì màu xanh (theo yêu cầu người dùng)
                    currentClass = val < 0 ? 'text-red-600 font-bold' : (val > 0 ? 'text-green-600 font-medium' : 'text-slate-400');
                }

                html += `<tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5 font-medium text-slate-800">${p.param_label}</td>
                    <td class="px-5 py-3.5 text-slate-600">${metricLabel[p.metric] || p.metric}</td>
                    <td class="px-5 py-3.5 text-center"><span class="${currentClass}">${currentVal}</span></td>
                    <td class="px-5 py-3.5 text-center"><span class="font-mono font-bold text-slate-800 text-base">&gt; ${parseFloat(p.threshold).toFixed(1)}%</span></td>
                    <td class="px-5 py-3.5 text-center">${sevBadge}</td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs">${p.description || '—'}</td>
                    <td class="px-5 py-3.5 text-center">
                        <button onclick='openParamModal(${JSON.stringify(p)})' type="button" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg px-3 py-1.5 hover:bg-blue-50 transition">
                            <i data-lucide="pencil" class="w-3 h-3"></i> Sửa
                        </button>
                    </td>
                </tr>`;
            });
            $('#params-tbody').html(html);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        },
        error: function(xhr) {
            console.error('[PARAMS] Lỗi:', xhr);
            $('#params-tbody').html('<tr><td colspan="6" class="text-center py-8 text-red-400">Lỗi kết nối API lấy tham số.</td></tr>');
        }
    });
}

function openParamModal(param) {
    if (typeof jQuery === 'undefined') return;
    $('#modal-param-title').text('Sửa: ' + param.param_label);
    $('#ep-param-key').val(param.param_key);
    $('#ep-threshold').val(parseFloat(param.threshold));
    $('#ep-description').val(param.description || '');
    $('#ep-alert-box').addClass('hidden').text('');
    $('#ep-save-btn').prop('disabled', false).html('<i data-lucide="save" class="w-3.5 h-3.5"></i> Lưu thay đổi');
    $('#modal-edit-param').removeClass('hidden').addClass('flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeParamModal() {
    if (typeof jQuery === 'undefined') return;
    $('#modal-edit-param').addClass('hidden').removeClass('flex');
}

function submitParamForm(e) {
    e.preventDefault();
    if (typeof jQuery === 'undefined') return;
    const paramKey = $('#ep-param-key').val();
    const threshold = parseFloat($('#ep-threshold').val());
    const description = $('#ep-description').val();

    if (!paramKey || isNaN(threshold) || threshold < 0) {
        showParamAlert('danger', 'Ngưỡng không hợp lệ.'); return;
    }

    $('#ep-save-btn').prop('disabled', true).html('<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i> Đang lưu...');
    
    $.ajax({
        url: "<?= site_url('admin/RiskDashboard/save_risk_params') ?>",
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ param_key: paramKey, threshold: threshold, description: description }),
        success: function(res) {
            if (typeof res === 'string') { try { res = JSON.parse(res); } catch(ex){} }
            if (res.success) {
                showParamAlert('success', 'Đã lưu thành công!');
                setTimeout(function(){ closeParamModal(); loadRiskParams(); }, 800);
            } else {
                showParamAlert('danger', res.message || 'Lỗi khi lưu.');
                $('#ep-save-btn').prop('disabled', false).html('Lưu thay đổi');
            }
        },
        error: function() {
            showParamAlert('danger', 'Lỗi Server!');
            $('#ep-save-btn').prop('disabled', false).html('Lưu thay đổi');
        }
    });
}

function showParamAlert(type, msg) {
    const box = $('#ep-alert-box');
    box.removeClass('hidden bg-green-100 text-green-800 bg-red-100 text-red-800');
    box.addClass(type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
    box.text(msg).removeClass('hidden');
}

// Bắt đầu
document.addEventListener("DOMContentLoaded", function() {
    initSoSanh();
});
// Nếu page đã load xong mà user bấm qua tab, DOMContentLoaded ko chạy lại:
if (document.readyState === "complete" || document.readyState === "interactive") {
    setTimeout(initSoSanh, 10);
}
</script>
