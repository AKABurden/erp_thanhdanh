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
</div>

<!-- jQuery + Highcharts CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    (function() {
        var AJAX_URL = '<?= site_url("admin/RiskDashboard/countTask") ?>';

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

        // Auto load lần đầu
        pbCountTask(-1);

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