<div id="dashboard_delivery" class="dashboard-delivery hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ LƯỢNG PHIẾU GIAO</div>
                <div class="value green js-delivery-total">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;margin-bottom: 100px;">
                    <div class="box">
                        <div class="label">CHƯA DUYỆT KHO</div>
                        <div class="value red js-delivery-pending">-</div>
                    </div>
                </div>
            </section>
            <!-- Thanh ở giữa -->
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 375px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;margin-bottom: 100px;">
                    <div class="box">
                        <div class="label">ĐÃ DUYỆT KHO</div>
                        <div class="value green js-delivery-approved">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div style="width:1px;background:#d0d0d0;height: calc(100vh - 375px);margin: 0 0;float:left;display:inline-block;"></div>
    <div class="chart_dashboard">
        <div class="container-detail">
            <section class="col-md-12" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;margin-bottom: 100px;">
                    <div class="chart js-chart-delivery" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-delivery" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
</div>

<script>
    $(document).on('click', '.js-delivery-pending', function(e) {
        var delivery = $('.js-delivery-pending').text();
        if (delivery != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen/modal_delivery') ?>',
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


    function UpdateStatsdelivery(stats) {
        $('.js-delivery-total').text(mainFmt((mainNum(stats?.total_delivery_approved) + mainNum(stats?.total_delivery_pending)), 0));
        $('.js-delivery-approved').text(mainFmt(mainNum(stats?.total_delivery_approved), 0));
        $('.js-delivery-pending').text(mainFmt(mainNum(stats.total_delivery_pending), 0));
        $('.js-delivery-pending').css('cursor', 'unset');
        if (mainNum(stats.total_delivery_pending) > 0) {
            $('.js-delivery-pending').css('cursor', 'pointer');
        }
        // Destroy and re-create chart only if data has changed
        const approved = mainNum(stats?.total_delivery_approved);
        const pending = mainNum(stats?.total_delivery_pending);
        const total = approved + pending;
        const chartData = [approved, pending];

        if (!window.deliveryChart || 
            JSON.stringify(window.deliveryChart.data.datasets[0].data) !== JSON.stringify(chartData)) {
            if (window.deliveryChart) {
            window.deliveryChart.destroy();
            }
            const ctx = document.querySelector('canvas.js-chart-delivery').getContext('2d');
            window.deliveryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Đã hoàn thành', 'Chưa hoàn thành'],
                datasets: [{
                data: chartData,
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
        document.querySelector('canvas.js-chart-delivery').style.maxWidth = '500px';
        document.querySelector('canvas.js-chart-delivery').style.maxHeight = '500px';
    }

    function countdelivery() {
        if ($('#dashboard-delivery').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen/countdelivery') ?>/" + window.filterdeliveryStageId, res => {
                if (!res || !res.success) return;
                UpdateStatsdelivery(res.stats || {});
            });
        }
    }

    setInterval(countdelivery, 20000);
</script>