<div id="dashboard_task" class="dashboard-quotes hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ LƯỢNG CÔNG VIỆC</div>
                <div class="value green count_all">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label red">CHƯA HOÀN THÀNH</div>
                        <div class="value red count_procesing" style="cursor: pointer" onclick="detailTask(this,1)">-</div>
                    </div>
                </div>
            </section>
            <!-- Thanh ở giữa -->
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 415px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">ĐÃ HOÀN THÀNH</div>
                        <div class="value count_finish" style="cursor: pointer" onclick="detailTask(this,2)">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div style="width:1px;background:#d0d0d0;height: calc(100vh - 415px);margin: 0 0;float:left;display:inline-block;"></div>
    <div class="chart_dashboard">
        <div class="container-detail">
            <section class="col-md-12" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;">
                    <div id="container_task"></div>
                </div>
        </div>
        </section>
    </div>
    <div class="sub-menu">
        <div class="sub-menu-child active" data-value="-1" onclick="changeFilterTask(this,-1)">Tất cả</div>
        <?php if (!empty($departments)) { ?>
            <?php foreach ($departments as $key => $value) { ?>
                <div class="sub-menu-child" data-value="<?= $value['id'] ?>" onclick="changeFilterTask(this,<?= $value['id'] ?>)"><?= $value['name'] ?></div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<script>
    function detailTask(_this, type = 1) {
        department_id = $('.sub-menu-child.active').data('value');
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_task/') ?>${type}/${department_id}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function changeFilterTask(_this, id) {
        $('.sub-menu-child').removeClass('active');
        $(_this).addClass('active');
        title = $(_this).text();
        $(".sub-title").text(' - ' + title);
        countTask(id);
    }

    function getNumberFromText(selector) {
        let text = $(selector).text().trim();
        return text === '-' ? 0 : parseFloat(text) || 0;
    }

    function countTask(department_id = -1) {
        dataChartTask = [];
        let count_procesing_old = getNumberFromText('.count_procesing');
        let count_finish_old = getNumberFromText('.count_finish');
        if ($('#dashboard-task').hasClass('active')) {
            $.ajax({
                    url: "<?= site_url('dashboard_srceen_office/countTask') ?>",
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        department_id: department_id
                    },
                })
                .done(function(data) {
                    dataChartTask = [];
                    if (!data || !data.success) return;
                    $('.count_all').text(mainFmt(mainNum(data.data.count_all)));
                    $('.count_procesing').text(mainFmt(mainNum(data.data.count_procesing)));
                    $('.count_finish').text(mainFmt(mainNum(data.data.count_finish)));

                    dataChartTask.push({
                        name: 'Chưa hoàn thành - ' + mainFmt(mainNum(data.data.count_procesing)),
                        y: mainNum(data.data.count_procesing),
                        color: '#ff4c4c'
                    });
                    dataChartTask.push({
                        name: 'Đã hoàn thành - ' + mainFmt(mainNum(data.data.count_finish)),
                        y: mainNum(data.data.count_finish),
                        color: '#0348A2'
                    });

                    if (count_procesing_old != $('.count_procesing').text() || count_finish_old != $('.count_finish').text()) {
                        loadChartTask(dataChartTask);
                    }
                })
                .fail(function() {
                    console.log("error");
                });
        }
    }
    countTask($('.sub-menu-child.active').data('value'));
    setInterval(function() {
        countTask($('.sub-menu-child.active').data('value'));
    }, 20000);

    function loadChartTask(dataChart = {}) {
        Highcharts.chart('container_task', {
            chart: {
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
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
                            fontSize: '15px',
                        },
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

    let autoLoopTaskIndex = 0;
    const autoLoopTask = [
        '-1'
    ];

    <?php if (!empty($departments)) { ?>
        <?php foreach ($departments as $key => $value) { ?>
            autoLoopTask.push('<?= $value['id'] ?>');
        <?php } ?>
    <?php } ?>
    let autoLoopTimerTask = null;
    var timeTask = '<?= get_option('time_dashboard_srceen') ? get_option('time_dashboard_srceen') : 30 ?>';
    var timeTask = 5;

    function startAutoLoopTask(reset = false) {
        if (reset) {
            autoLoopTaskIndex = -1;
        }
        if (autoLoopTaskIndex) clearTimeout(autoLoopTimerTask);

        function nextTask() {
            autoLoopTaskIndex = (autoLoopTaskIndex + 1) % autoLoopTask.length;
            autoLoopTimerTask = setTimeout(nextTask, timeTask * 60 * 1000); // 10 phút
            changeFilterTask($('.sub-menu-child[data-value="' + autoLoopTask[autoLoopTaskIndex] + '"]'), autoLoopTask[autoLoopTaskIndex]);
        }
        nextTask();
    }

    function stopAutoLoopTask() {
        if (autoLoopTimerTask) {
            clearTimeout(autoLoopTimerTask);
            autoLoopTimerTask = null;
        }
    }
</script>