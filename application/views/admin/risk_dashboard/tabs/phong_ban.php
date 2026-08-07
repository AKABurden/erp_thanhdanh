<?php
$departments = isset($departments) ? $departments : [];
?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-900">Dashboard Phòng Ban <span class="sub-title text-slate-500 font-normal"></span></h2>
    </div>

    <!-- Filter phòng ban -->
    <div class="flex flex-wrap gap-2">
        <button class="pb-dept-filter px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-blue-600 text-white" data-value="-1" onclick="pbChangeFilter(this, -1)">Tất cả</button>
        <?php if (!empty($departments)) { ?>
            <?php foreach ($departments as $value) { ?>
                <button class="pb-dept-filter px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-slate-100 text-slate-700 hover:bg-slate-200" data-value="<?= $value['id'] ?>" onclick="pbChangeFilter(this, <?= $value['id'] ?>)"><?= $value['name'] ?></button>
            <?php } ?>
        <?php } ?>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tổng -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6 text-center">
            <div class="text-sm font-medium text-slate-500 mb-2">TỔNG SỐ LƯỢNG CÔNG VIỆC</div>
            <div class="text-3xl font-bold text-emerald-600 pb-count-all">-</div>
        </div>
        <!-- Chưa hoàn thành -->
        <div class="rounded-xl border border-red-200 bg-red-50 shadow-sm p-6 text-center cursor-pointer hover:shadow-md transition-shadow" onclick="pbDetailTask(this, 1)">
            <div class="text-sm font-medium text-red-500 mb-2">CHƯA HOÀN THÀNH</div>
            <div class="text-3xl font-bold text-red-600 pb-count-processing">-</div>
        </div>
        <!-- Đã hoàn thành -->
        <div class="rounded-xl border border-blue-200 bg-blue-50 shadow-sm p-6 text-center cursor-pointer hover:shadow-md transition-shadow" onclick="pbDetailTask(this, 2)">
            <div class="text-sm font-medium text-blue-500 mb-2">ĐÃ HOÀN THÀNH</div>
            <div class="text-3xl font-bold text-blue-600 pb-count-finish">-</div>
        </div>
    </div>

    <!-- Chart -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
        <div id="pb_container_task" style="min-height: 400px;"></div>
    </div>

    <!-- Bảng nhân viên theo phòng ban -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900">Thống kê nhân viên
                <span class="sub-title text-slate-400 text-sm font-normal"></span>
            </h3>
            <div class="flex items-center gap-2">
                <label class="text-sm text-slate-500">Tháng:</label>
                <input type="month" id="pb_month_filter" class="border border-slate-200 rounded-lg px-2 py-1 text-sm"
                    value="<?= date('Y-m') ?>" onchange="pbLoadStaffTable()">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nhân viên</th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">TG làm việc<br><span class="text-slate-400 normal-case">(giờ)</span></th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">Tăng ca<br><span class="text-slate-400 normal-case">(giờ)</span></th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">Vắng mặt<br><span class="text-slate-400 normal-case">(ngày)</span></th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">Phép<br><span class="text-slate-400 normal-case">(ngày)</span></th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">BCKPH<br><span class="text-slate-400 normal-case">(chờ/tổng)</span></th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">BC Vi phạm<br><span class="text-slate-400 normal-case">(số / điểm)</span></th>
                        <th class="px-3 py-3 text-center">Có thiết hại</th>
                    </tr>
                </thead>
                <tbody id="pb_staff_tbody">
                    <tr><td colspan="8" class="text-center py-6 text-slate-400">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- jQuery + Highcharts CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    (function() {
        var AJAX_URL      = '<?= site_url("admin/RiskDashboard/countTask") ?>';
        var STAFF_URL     = '<?= site_url("admin/RiskDashboard/get_phong_ban_staff_data") ?>';

        function pbGetNum(selector) {
            var text = $(selector).text().trim();
            return text === '-' ? 0 : parseFloat(text) || 0;
        }

        window.pbChangeFilter = function(_this, id) {
            $('.pb-dept-filter').removeClass('bg-blue-600 text-white').addClass('bg-slate-100 text-slate-700');
            $(_this).removeClass('bg-slate-100 text-slate-700').addClass('bg-blue-600 text-white');
            var title = $(_this).text();
            $(".sub-title").text(' - ' + title);
            pbCountTask(id);
            pbLoadStaffTable();
        };

        window.pbDetailTask = function(_this, type) {
            var department_id = $('.pb-dept-filter.bg-blue-600').data('value');
            var value = pbGetNum($(_this).find('.text-3xl'));
            if (value == 0) return;
            $.ajax({
                url: '<?= base_url("dashboard_srceen_office/modal_detail_task/") ?>' + type + '/' + department_id,
                type: 'GET',
                success: function(html) {
                    if ($('#chModal_dashboard').length) {
                        $('#chModal_dashboard').html(html);
                        openModal('chModal_dashboard');
                    }
                }
            });
        };

        function pbCountTask(department_id) {
            if (typeof department_id === 'undefined') department_id = -1;
            var oldProcessing = pbGetNum('.pb-count-processing');
            var oldFinish = pbGetNum('.pb-count-finish');
            $.ajax({
                    url: AJAX_URL,
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        department_id: department_id
                    }
                })
                .done(function(data) {
                    if (!data || !data.success) return;
                    var d = data.data;
                    var fmtAll = Number(d.count_all).toLocaleString('vi-VN');
                    var fmtProc = Number(d.count_procesing).toLocaleString('vi-VN');
                    var fmtFin = Number(d.count_finish).toLocaleString('vi-VN');

                    $('.pb-count-all').text(fmtAll);
                    $('.pb-count-processing').text(fmtProc);
                    $('.pb-count-finish').text(fmtFin);

                    // Vẽ chart nếu data thay đổi
                    if (oldProcessing != d.count_procesing || oldFinish != d.count_finish) {
                        pbLoadChart([{
                                name: 'Chưa hoàn thành - ' + fmtProc,
                                y: d.count_procesing,
                                color: '#ef4444'
                            },
                            {
                                name: 'Đã hoàn thành - ' + fmtFin,
                                y: d.count_finish,
                                color: '#2563eb'
                            }
                        ]);
                    }
                })
                .fail(function() {
                    console.log('pbCountTask error');
                });
        }

        function pbLoadChart(dataChart) {
            Highcharts.chart('pb_container_task', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Tổng quan công việc'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            style: {
                                fontSize: '15px'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Số lượng',
                    colorByPoint: true,
                    data: dataChart
                }]
            });
        }

        // ===== BẢNG NHÂN VIÊN =====
        window.pbLoadStaffTable = function() {
            var department_id = $('.pb-dept-filter.bg-blue-600').data('value') || -1;
            var month = $('#pb_month_filter').val();
            var tbody = document.getElementById('pb_staff_tbody');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Đang tải...</td></tr>';

            $.ajax({
                url: STAFF_URL,
                type: 'GET',
                dataType: 'JSON',
                data: { department_id: department_id, month: month }
            }).done(function(res) {
                if (!res || !res.success || !res.data.length) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Không có dữ liệu.</td></tr>';
                    return;
                }
                var html = '';
                res.data.forEach(function(s) {
                    // BCKPH badge
                    var bckphBadge = '';
                    if (s.bckph_total > 0) {
                        var pendingCls = s.bckph_pending > 0 ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700';
                        bckphBadge = '<span class="px-2 py-0.5 rounded text-xs font-semibold ' + pendingCls + '">'
                            + s.bckph_pending + '/' + s.bckph_total + '</span>';
                    } else {
                        bckphBadge = '<span class="text-slate-300">—</span>';
                    }

                    // Vi phạm badge
                    var viBadge = '';
                    if (s.violate_count > 0) {
                        viBadge = '<span class="px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">'
                            + s.violate_count + ' VP</span>'
                            + (s.tong_diem_vi_pham > 0
                                ? ' <span class="text-xs text-red-500">(-' + s.tong_diem_vi_pham + ' điểm)</span>'
                                : '');
                    } else {
                        viBadge = '<span class="text-slate-300">—</span>';
                    }

                    // Thiết hại
                    var damageBadge = '';
                    if (s.has_damage && s.damage_detail.length) {
                        var items = s.damage_detail.map(function(d) {
                            var xuLyCls = d.da_xu_ly ? 'text-emerald-600' : 'text-orange-500';
                            var xuLyTxt = d.da_xu_ly ? '✓ đã xử lý' : '⏳ chưa xử lý';
                            return '<div class="text-xs border-b border-slate-100 py-1">'
                                + '<span class="font-medium text-slate-700">' + d.ref_no + '</span>'
                                + (d.production_stage ? ' <span class="text-slate-400">[' + d.production_stage + ']</span>' : '')
                                + (d.damage_cost > 0
                                    ? ' <span class="text-red-600 font-semibold">' + Number(d.damage_cost).toLocaleString('vi-VN') + '₫</span>'
                                    : '')
                                + ' <span class="' + xuLyCls + '">' + xuLyTxt + '</span>'
                                + '</div>';
                        }).join('');
                        damageBadge = '<div class="max-w-xs">' + items + '</div>';
                    } else {
                        damageBadge = '<span class="text-slate-300">—</span>';
                    }

                    html += '<tr class="border-t border-slate-100 hover:bg-slate-50">'
                        + '<td class="px-4 py-2 font-medium text-slate-800">' + s.name + '</td>'
                        + '<td class="px-3 py-2 text-center">' + (s.work_hours > 0 ? s.work_hours + 'h' : '<span class="text-slate-300">—</span>') + '</td>'
                        + '<td class="px-3 py-2 text-center">' + (s.ot_hours > 0 ? '<span class="text-blue-600 font-semibold">+' + s.ot_hours + 'h</span>' : '<span class="text-slate-300">—</span>') + '</td>'
                        + '<td class="px-3 py-2 text-center">' + (s.absent_days > 0 ? '<span class="text-orange-600">' + s.absent_days + ' ngày</span>' : '<span class="text-slate-300">—</span>') + '</td>'
                        + '<td class="px-3 py-2 text-center">' + (s.leave_days > 0 ? s.leave_days + ' ngày' : '<span class="text-slate-300">—</span>') + '</td>'
                        + '<td class="px-3 py-2 text-center">' + bckphBadge + '</td>'
                        + '<td class="px-3 py-2 text-center">' + viBadge + '</td>'
                        + '<td class="px-3 py-2">' + damageBadge + '</td>'
                        + '</tr>';
                });
                tbody.innerHTML = html;
            }).fail(function() {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-red-400">Lỗi tải dữ liệu nhân viên.</td></tr>';
            });
        };

        // Auto load lần đầu
        pbCountTask(-1);
        pbLoadStaffTable();

        // Auto refresh mỗi 20 giây
        setInterval(function() {
            var activeId = $('.pb-dept-filter.bg-blue-600').data('value');
            pbCountTask(activeId);
        }, 20000);

        // Auto loop qua các phòng ban
        var pbLoopIndex = 0;
        var pbLoopItems = ['-1'];
        <?php if (!empty($departments)) { ?>
            <?php foreach ($departments as $value) { ?>
                pbLoopItems.push('<?= $value['id'] ?>');
            <?php } ?>
        <?php } ?>
        var pbLoopTimer = null;
        var pbLoopTime = 5; // phút

        function pbStartLoop(reset) {
            if (reset) pbLoopIndex = -1;
            if (pbLoopTimer) clearTimeout(pbLoopTimer);

            function next() {
                pbLoopIndex = (pbLoopIndex + 1) % pbLoopItems.length;
                pbLoopTimer = setTimeout(next, pbLoopTime * 60 * 1000);
                var btn = $('.pb-dept-filter[data-value="' + pbLoopItems[pbLoopIndex] + '"]');
                if (btn.length) pbChangeFilter(btn[0], pbLoopItems[pbLoopIndex]);
            }
            next();
        }
    })();
</script>