<div id="dashboard_manufactures" class="dashboard-manufactures hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ LƯỢNG LỆNH</div>
                <div class="value green js-manufactures-total">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;margin-bottom: 100px;">
                    <div class="box">
                        <div class="label">CHƯA HOÀN THÀNH</div>
                        <div class="value red js-manufactures-pending">-</div>
                    </div>
                </div>
            </section>
            <!-- Thanh ở giữa -->
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 475px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;margin-bottom: 100px;">
                    <div class="box">
                        <div class="label">ĐÃ HOÀN THÀNH</div>
                        <div class="value green js-manufactures-approved">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div style="width:1px;background:#d0d0d0;height: calc(100vh - 475px);margin: 0 0;float:left;display:inline-block;"></div>
    <div class="chart_dashboard">
        <div class="container-detail">
            <section class="col-md-12" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;margin-bottom: 100px;">
                    <div class="chart js-chart-manufactures" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-manufactures" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
    <div class="sub-menu">
        <?php if (!empty($categoryStage)) { ?>
            <?php foreach ($categoryStage as $key => $value) { ?>
                <div class="sub-menu-child-new child-ksnb <?= $key == 0 ? 'active' : '' ?>" data-value="<?= $value['id'] ?>" data-name="<?= $value['name'] ?>" onclick="changeFilterMN(this,<?= $value['id'] ?>)"><?= $value['name'] ?></div>
            <?php } ?>
        <?php } ?>
    </div>
</div>

<script>
    // Click handler for "CHƯA DUYỆT" box - navigate to manufactures list filtered to unapproved
    // Add pointer cursor to the "CHƯA DUYỆT" box for better UX
    setInterval(function() {
        // Chọn ngẫu nhiên một mục khác trong sub-menu và gọi changeFilterMN
        var $items = $('.sub-menu-child-new');
        if ($items.length > 1) {
            var currentId = $('.sub-menu-child-new.active').data('value');
            var idx = $items.index($('.sub-menu-child-new.active'));
            var nextIdx = (idx + 1) % $items.length;
            var $next = $items.eq(nextIdx);
            changeFilterMN($next[0], $next.data('value'));
        }
    }, 120000); // 2 phút

    function changeFilterMN(ele, id) {
        $('.sub-menu-child-new').removeClass('active');
        $(ele).addClass('active');
        var name = $(ele).data('name');
        window.filterManufacturesStageId = $(ele).data('value');
        $('.main-title').text('LỆNH SẢN XUẤT: ' + name);
        countmanufactures($(ele).data('value'));
    }


    $(document).on('click', '.js-manufactures-pending', function(e) {
        var manufactures = $('.js-manufactures-pending').text();
        if (manufactures != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen/modal_manufactures/') ?>' + window.filterManufacturesStageId,
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
    });


    function UpdateStatsmanufactures(stats) {
        $('.js-manufactures-total').text(mainFmt((mainNum(stats?.total_manufactures_approved) + mainNum(stats?.total_manufactures_pending)), 0));
        $('.js-manufactures-approved').text(mainFmt(mainNum(stats?.total_manufactures_approved), 0));
        $('.js-manufactures-pending').text(mainFmt(mainNum(stats.total_manufactures_pending), 0));
        $('.js-manufactures-pending').css('cursor', 'unset');
        if (mainNum(stats.total_manufactures_pending) > 0) {
            $('.js-manufactures-pending').css('cursor', 'pointer');
        }
        // Destroy and re-create chart only if data changed
        const approved = mainNum(stats?.total_manufactures_approved);
        const pending = mainNum(stats?.total_manufactures_pending);
        const total = approved + pending;

        // Store previous chart data for comparison
        if (!window._manufacturesChartData) {
            window._manufacturesChartData = {
                approved: null,
                pending: null
            };
        }
        const prev = window._manufacturesChartData;
        if (prev.approved !== approved || prev.pending !== pending) {
            window._manufacturesChartData = {
                approved,
                pending
            };
            if (window.manufacturesChart) {
                window.manufacturesChart.destroy();
            }
            const ctx = document.querySelector('canvas.js-chart-manufactures').getContext('2d');
            window.manufacturesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Đã hoàn thành', 'Chưa hoàn thành'],
                    datasets: [{
                        data: [approved, pending],
                        backgroundColor: ['#4caf50', '#f44336'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            enabled: true
                        },
                        doughnutCenterText: {
                            display: true,
                            text: total,
                            subText: approved + ' / ' + pending
                        }
                    }
                },
                plugins: [{
                    id: 'doughnutCenterText',
                    afterDraw: function(chart) {
                        if (chart.config.options.plugins.doughnutCenterText?.display) {
                            const ctx = chart.ctx;
                            const width = chart.width,
                                height = chart.height;
                            ctx.save();
                            ctx.font = 'bold 48px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#fff';
                            ctx.fillText(chart.config.options.plugins.doughnutCenterText.text, width / 2, height / 2 - 10);
                            ctx.font = '20px Arial';
                            ctx.fillStyle = '#fff';
                            ctx.fillText(chart.config.options.plugins.doughnutCenterText.subText, width / 2, height / 2 + 30);
                            ctx.restore();
                        }
                    }
                }]
            });
        }
        // Thu nhỏ canvas bằng CSS
        document.querySelector('canvas.js-chart-manufactures').style.maxWidth = '500px';
        document.querySelector('canvas.js-chart-manufactures').style.maxHeight = '500px';
    }

    function countmanufactures() {
        if ($('#dashboard-manufactures').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen/countmanufactures') ?>/" + window.filterManufacturesStageId, res => {
                if (!res || !res.success) return;
                UpdateStatsmanufactures(res.stats || {});
            });
        }
    }

    setInterval(countmanufactures, 20000);
</script>