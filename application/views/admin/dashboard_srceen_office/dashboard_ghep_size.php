<div id="dashboard_ghep_size" class="dashboard-ghep_size hide">
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH CHƯA TỚI BƯỚC GHÉP SIZE</div>
                        <div class="value red js-ghep-size-pending">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH ĐÃ TỚI BƯỚC GHÉP SIZE</div>
                        <div class="value green js-ghep-size-approved">-</div>
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
                        <canvas class="chart js-chart-ghep_size" style="width:400px; height:400px;"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.js-ghep-size-pending', function(e) {
        var sample = $('.js-ghep-size-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_mo_lenh/1') ?>',
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

    function UpdateStatsGhepSize(stats) {
        $('.js-ghep-size-pending').css('cursor', 'unset');
        if (mainNum(stats.total_ghep_size_pending) > 0) {
            $('.js-ghep-size-pending').css('cursor', 'pointer');
        }
        // map API fields to the classes used in the view
        $('.js-ghep-size-pending').text(mainFmt(mainNum(stats?.total_ghep_size_pending), 0)); // "chưa mở lệnh"
        $('.js-ghep-size-approved').text(mainFmt(mainNum(stats?.total_ghep_size_approved), 0)); // "đã mở lệnh"
        // render/update Chart.js for the canvas.js-chart-ghep_size
        (function() {
            const ctxEl = document.querySelector('.js-chart-ghep_size');
            if (!ctxEl) return;

            // make chart a bit smaller
            ctxEl.style.width = '200px';
            ctxEl.style.height = '200px';

            const ctx = ctxEl.getContext('2d');

            // Accept either totals or series arrays from API
            const pendingRaw = stats?.total_ghep_size_pending ?? stats?.ghep_size_pending_series ?? stats?.chart?.pending ?? stats?.chart?.datasets?.[0]?.data ?? 0;
            const approvedRaw = stats?.total_ghep_size_approved ?? stats?.ghep_size_approved_series ?? stats?.chart?.approved ?? stats?.chart?.datasets?.[1]?.data ?? 0;

            const sumValues = v => {
                if (v == null) return 0;
                if (Array.isArray(v)) return v.reduce((s, x) => s + (Number(x) || 0), 0);
                return Number(v) || 0;
            };

            const valPending = sumValues(pendingRaw);
            const valApproved = sumValues(approvedRaw);
            const total = valPending + valApproved;

            const pieLabels = ['Chưa tới bước ghép size', 'Đã tới bước ghép size'];
            const pieData = [valPending, valApproved];
            const bgColors = ['rgba(231,76,60,0.85)', 'rgba(39,174,96,0.85)'];
            const borderColors = ['#e74c3c', '#27ae60'];

            const config = {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieData,
                        backgroundColor: bgColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tổng: ' + mainFmt(mainNum(total), 0),
                            font: {
                                size: 14
                            }
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (!data || !data.datasets.length) return [];
                                    return data.labels.map((label, i) => ({
                                        text: label + ': ' + (Number(data.datasets[0].data[i]) || 0),
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: chart.getDataVisibility ? !chart.getDataVisibility(i) : false,
                                        index: i
                                    }));
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const v = ctx.parsed ?? ctx.raw;
                                    return ctx.label + ': ' + (Number(v) || 0);
                                }
                            }
                        }
                    }
                }
            };

            // Only destroy/recreate chart when the underlying data/labels changed.
            // Otherwise update the existing chart to avoid flicker.
            const newHash = JSON.stringify({
                labels: pieLabels,
                data: pieData
            });
            if (window._ghepSizeChart) {
                try {
                    if (window._ghepSizeChart._ghepSizeDataHash === newHash) {
                        // same data -> just update title (total may change) and redraw
                        if (window._ghepSizeChart.options && window._ghepSizeChart.options.plugins && window._ghepSizeChart.options.plugins.title) {
                            window._ghepSizeChart.options.plugins.title.text = 'Tổng: ' + mainFmt(mainNum(total), 0);
                        }
                        window._ghepSizeChart.update();
                        return;
                    } else {
                        // data changed -> destroy and recreate
                        window._ghepSizeChart.destroy();
                        window._ghepSizeChart = null;
                    }
                } catch (e) {
                    window._ghepSizeChart = null;
                }
            }

            window._ghepSizeChart = new Chart(ctx, config);
            // store hash so next update can decide whether to destroy
            window._ghepSizeChart._ghepSizeDataHash = newHash;
        })();
    }

    function count_ghep_size() {
        if ($('#dashboard-ghep-size').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_ghep_size') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsGhepSize(res.stats || {});
            });
        }
    }
    count_ghep_size();
    setInterval(count_ghep_size, 20000);
</script>