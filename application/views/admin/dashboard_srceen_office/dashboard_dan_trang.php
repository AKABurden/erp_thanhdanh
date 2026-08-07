<div id="dashboard_dan_trang" class="dashboard-dan_trang hide">
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH CHƯA TỚI BƯỚC DÀN TRANG</div>
                        <div class="value red js-dan-trang-pending">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH ĐÃ TỚI BƯỚC DÀN TRANG</div>
                        <div class="value green js-dan-trang-approved">-</div>
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
                        <canvas class="chart js-chart-dan_trang" style="width:400px; height:400px;"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.js-dan-trang-pending', function(e) {
        var sample = $('.js-dan-trang-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_mo_lenh/2') ?>',
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
    function UpdateStatsDanTrang(stats) {
        $('.js-dan-trang-pending').css('cursor', 'unset');
        if (mainNum(stats.total_dan_trang_pending) > 0) {
            $('.js-dan-trang-pending').css('cursor', 'pointer');
        }
        // map API fields to the classes used in the view
        $('.js-dan-trang-pending').text(mainFmt(mainNum(stats?.total_dan_trang_pending), 0)); // "chưa mở lệnh"
        $('.js-dan-trang-approved').text(mainFmt(mainNum(stats?.total_dan_trang_approved), 0)); // "đã mở lệnh"
        (function() {
            const ctxtd = document.querySelector('.js-chart-dan_trang');
            if (!ctxtd) return;

            // make chart a bit smaller
            ctxtd.style.width = '200px';
            ctxtd.style.height = '200px';

            const ctx = ctxtd.getContext('2d');

            // Accept either totals or series arrays from API
            const pendingRaw = stats?.total_dan_trang_pending ?? stats?.dan_trang_pending_series ?? stats?.chart?.pending ?? stats?.chart?.datasets?.[0]?.data ?? 0;
            const approvedRaw = stats?.total_dan_trang_approved ?? stats?.dan_trang_approved_series ?? stats?.chart?.approved ?? stats?.chart?.datasets?.[1]?.data ?? 0;

            const sumValues = v => {
                if (v == null) return 0;
                if (Array.isArray(v)) return v.reduce((s, x) => s + (Number(x) || 0), 0);
                return Number(v) || 0;
            };

            const valPending = sumValues(pendingRaw);
            const valApproved = sumValues(approvedRaw);
            const total = valPending + valApproved;

            const pieLabels = ['Chưa tới bước dàn trang', 'Đã tới bước dàn trang'];
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

            // build a simple signature to detect meaningful data/type changes
            const newSignature = JSON.stringify({
                type: config.type,
                labels: pieLabels,
                data: pieData
            });

            try {
                // if a chart exists and signature is identical, just update values/title without destroying
                if (window._danTrangChart && window._danTrangChart instanceof Chart) {
                    if (window._danTrangChartSignature === newSignature) {
                        // update numeric values and title only
                        window._danTrangChart.data.datasets[0].data = pieData.slice();
                        window._danTrangChart.options.plugins.title.text = 'Tổng: ' + mainFmt(mainNum(total), 0);
                        window._danTrangChart.update();
                        return;
                    } else {
                        // signature changed => destroy and recreate
                        try {
                            window._danTrangChart.destroy();
                        } catch (e) {}
                        window._danTrangChart = null;
                    }
                }
            } catch (e) {
                // fallback: if anything goes wrong, ensure we don't keep a broken chart reference
                try {
                    if (window._danTrangChart) window._danTrangChart.destroy();
                } catch (e2) {}
                window._danTrangChart = null;
            }

            // create new chart
            window._danTrangChart = new Chart(ctx, config);
            window._danTrangChartSignature = newSignature;
        })();
    }

    function count_dan_trang() {
        if ($('#dashboard-dan-trang').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_dan_trang') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsDanTrang(res.stats || {});
            });
        }
    }
    count_dan_trang();
    setInterval(count_dan_trang, 20000);
</script>