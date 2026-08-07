<div id="dashboard_purchases" class="dashboard-purchases hide">
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG PHIẾU ĐỀ XUẤT CHƯA DUYỆT</div>
                        <div class="value red js-purchases-pending">-</div>
                    </div>
                    <div class="box">
                        <div class="label">TỔNG PHIẾU YÊU CẦU KẾ HOẠCH MUA CHƯA DUYỆT</div>
                        <div class="value red js-suggest_plan_purchase-pending">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG PHIẾU ĐỀ XUẤT ĐÃ DUYỆT</div>
                        <div class="value green js-purchases-approved">-</div>
                    </div>
                    <div class="box">
                        <div class="label">TỔNG PHIẾU YÊU CẦU KẾ HOẠCH MUA ĐÃ DUYỆT</div>
                        <div class="value green js-suggest_plan_purchase-approved">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div style="width:1px;background:#d0d0d0;height: calc(100vh - 268px);float:left;display:inline-block;margin-left: -12px;margin-top: 40px;"></div>
    <div class="chart_dashboard">
        <div class="container-detail">
            <section class="col-md-12" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;">
                    <div class="chart js-chart-sample" style="width:400px; height:400px;">
                        <canvas class="chart js-chart-purchases" style="width:400px; height:400px;"></canvas>
                    </div>
                    <div class="chart js-chart-sample" style="width:400px; height:400px;">
                        <canvas class="chart js-chart-suggest_plan_purchase" style="width:400px; height:400px;"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.js-purchases-pending', function(e) {
        var sample = $('.js-purchases-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/purchases_dxnb') ?>',
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
    $(document).on('click', '.js-suggest_plan_purchase-pending', function(e) {
        var sample = $('.js-suggest_plan_purchase-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/suggest_plan_purchase') ?>',
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
    function UpdateStatsPurchases(stats) {
        $('.js-purchases-pending').css('cursor', 'unset');
        if (mainNum(stats.internal_proposal_purchases_pending) > 0) {
            $('.js-purchases-pending').css('cursor', 'pointer');
        }
        $('.js-suggest_plan_purchase-pending').css('cursor', 'unset');
        if (mainNum(stats.suggest_plan_purchase_pending) > 0) {
            $('.js-suggest_plan_purchase-pending').css('cursor', 'pointer');
        }
        // map API fields to the classes used in the view
        $('.js-purchases-pending').text(mainFmt(mainNum(stats?.internal_proposal_purchases_pending), 0));
        $('.js-suggest_plan_purchase-pending').text(mainFmt(mainNum(stats?.suggest_plan_purchase_pending), 0));

        $('.js-purchases-approved').text(mainFmt(mainNum(stats?.internal_proposal_purchases_approved), 0));
        $('.js-suggest_plan_purchase-approved').text(mainFmt(mainNum(stats?.suggest_plan_purchase_approved), 0));
        (() => {
            const pending = Number(mainNum(stats?.internal_proposal_purchases_pending)) || 0;
            const approved = Number(mainNum(stats?.internal_proposal_purchases_approved)) || 0;
            const total = pending + approved;

            const canvas = document.querySelector('.js-chart-purchases');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            const chartData = {
                labels: ['Chưa duyệt', 'Đã duyệt'],
                datasets: [{
                    data: [pending, approved],
                    backgroundColor: ['#e74c3c', '#2ecc71'],
                    hoverBackgroundColor: ['#c0392b', '#27ae60'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            };

            // plugin to draw total in the center of the doughnut
            const centerTextPlugin = {
                id: 'doughnutCenterText',
                beforeDraw(chart, args, options) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();

                    // total number (bigger)
                    ctx.fillStyle = options.totalColor || '#333';
                    const totalFontSize = Math.round((chart.width / 100) * 6) || 18;
                    ctx.font = `bold ${totalFontSize}px Arial`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(mainFmt(total, 0), cx, cy - (totalFontSize * 0.12));

                    // label under total
                    ctx.fillStyle = options.labelColor || '#666';
                    const labelFontSize = Math.round((chart.width / 100) * 3.2) || 12;
                    ctx.font = `${labelFontSize}px Arial`;
                    ctx.fillText(options.label || 'Tổng', cx, cy + (totalFontSize * 0.7));

                    ctx.restore();
                }
            };

            const centerOptions = {
                label: 'Tổng',
                total: total,
                totalColor: '#222',
                labelColor: '#666'
            };

            const chartOptions = {
                type: 'doughnut',
                cutout: '70%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const val = context.parsed || 0;
                                const t = (chartData.datasets[0].data || []).reduce((a, b) => a + b, 0) || 1;
                                const perc = ((val / t) * 100).toFixed(1);
                                return `${context.label}: ${mainFmt(val, 0)} (${perc}%)`;
                            }
                        }
                    },
                    // pass center text options to plugin
                    doughnutCenterText: centerOptions
                }
            };

            // if chart exists, compare data — only destroy & recreate when data changed
            if (window.purchasesChart) {
                const existing = (window.purchasesChart.data && window.purchasesChart.data.datasets && window.purchasesChart.data.datasets[0].data) || [];
                const newData = chartData.datasets[0].data.map(n => Number(n) || 0);
                const same = existing.length === newData.length && existing.every((v, i) => Number(v) === newData[i]);

                if (same) {
                    // Still update center text/options in case totals/labels styling changed
                    window.purchasesChart.options.plugins.doughnutCenterText = centerOptions;
                    window.purchasesChart.update();
                    return;
                }

                // data changed -> destroy and recreate
                try {
                    window.purchasesChart.destroy();
                } catch (e) {
                    /* ignore */ }
                delete window.purchasesChart;
            }

            // create chart with the plugin
            window.purchasesChart = new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: chartOptions,
                plugins: [centerTextPlugin]
            });
        })();
        (() => {
            const pending = Number(mainNum(stats?.suggest_plan_purchase_pending)) || 0;
            const approved = Number(mainNum(stats?.suggest_plan_purchase_approved)) || 0;
            const total = pending + approved;

            const canvas = document.querySelector('.js-chart-suggest_plan_purchase');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            const chartData = {
                labels: ['Chưa duyệt', 'Đã duyệt'],
                datasets: [{
                    data: [pending, approved],
                    backgroundColor: ['#e74c3c', '#2ecc71'],
                    hoverBackgroundColor: ['#c0392b', '#27ae60'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            };

            const centerTextPlugin = {
                id: 'doughnutCenterTextSuggest',
                beforeDraw(chart, args, options) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();

                    ctx.fillStyle = options.totalColor || '#333';
                    const totalFontSize = Math.round((chart.width / 100) * 6) || 18;
                    ctx.font = `bold ${totalFontSize}px Arial`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(mainFmt(total, 0), cx, cy - (totalFontSize * 0.12));

                    ctx.fillStyle = options.labelColor || '#666';
                    const labelFontSize = Math.round((chart.width / 100) * 3.2) || 12;
                    ctx.font = `${labelFontSize}px Arial`;
                    ctx.fillText(options.label || 'Tổng', cx, cy + (totalFontSize * 0.7));

                    ctx.restore();
                }
            };

            const centerOptions = {
                label: 'Tổng',
                total: total,
                totalColor: '#222',
                labelColor: '#666'
            };

            const chartOptions = {
                type: 'doughnut',
                cutout: '70%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const val = context.parsed || 0;
                                const t = (chartData.datasets[0].data || []).reduce((a, b) => a + b, 0) || 1;
                                const perc = ((val / t) * 100).toFixed(1);
                                return `${context.label}: ${mainFmt(val, 0)} (${perc}%)`;
                            }
                        }
                    },
                    doughnutCenterText: centerOptions
                }
            };

            if (window.suggestPlanPurchaseChart) {
                const existing = (window.suggestPlanPurchaseChart.data && window.suggestPlanPurchaseChart.data.datasets && window.suggestPlanPurchaseChart.data.datasets[0].data) || [];
                const newData = chartData.datasets[0].data.map(n => Number(n) || 0);
                const same = existing.length === newData.length && existing.every((v, i) => Number(v) === newData[i]);

                if (same) {
                    window.suggestPlanPurchaseChart.options.plugins.doughnutCenterText = centerOptions;
                    window.suggestPlanPurchaseChart.update();
                    return;
                }

                try {
                    window.suggestPlanPurchaseChart.destroy();
                } catch (e) {
                    /* ignore */ }
                delete window.suggestPlanPurchaseChart;
            }

            window.suggestPlanPurchaseChart = new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: chartOptions,
                plugins: [centerTextPlugin]
            });
        })();
    }

    function count_purchases() {
        if ($('#dashboard-purchases').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_purchases') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsPurchases(res.stats);
            });
        }
    }
    count_purchases();
    setInterval(count_purchases, 20000);
</script>