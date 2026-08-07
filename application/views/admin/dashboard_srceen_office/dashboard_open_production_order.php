<div id="dashboard_open_production_order" class="dashboard-open_production hide">
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG ĐƠN CHƯA MỞ LỆNH SẢN XUẤT</div>
                        <div class="value red js-open_production-pending">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH CHƯA XUẤT KHUÔN BẾ</div>
                        <div class="value red js-open_production-no-order">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH CHƯA XUẤT ĐỦ NPL</div>
                        <div class="value red js-open_production-npl-not-ready">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH CHƯA XUẤT ĐỦ VTSX</div>
                        <div class="value red js-open_production-vtsx-not-ready">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG ĐƠN ĐÃ MỞ LỆNH SẢN XUẤT</div>
                        <div class="value green js-open_production-approved">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH ĐÃ XUẤT KHUÔN BẾ</div>
                        <div class="value green js-open_production-with-order">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH XUẤT ĐỦ NPL</div>
                        <div class="value green js-open_production-npl-ready">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH XUẤT ĐỦ VTSX</div>
                        <div class="value green js-open_production-vtsx-ready">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div style="width:1px;background:#d0d0d0;height: calc(100vh - 268px);float:left;display:inline-block;margin-left: -12px;margin-top: 40px;"></div>
    <div class="chart_dashboard">
        <div class="container-detail">
            <section class="col-md-12" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;">
                    <div class="chart js-chart-sample" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-open_production" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
</div>
<script>
    $(document).on('click', '.js-open_production-pending', function(e) {
        var sample = $('.js-open_production-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/open_production') ?>',
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
    $(document).on('click', '.js-open_production-no-order', function(e) {
        var sample = $('.js-open_production-no-order').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/export_khuonbe/1') ?>',
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

    function UpdateStatsopen_production(stats) {
        $('.js-open_production-pending').css('cursor', 'unset');
        if (mainNum(stats.total_no_production_order) > 0) {
            $('.js-open_production-pending').css('cursor', 'pointer');
        }

        $('.js-open_production-no-order').css('cursor', 'unset');
        if (mainNum(stats?.total_be - stats?.total_export_be) > 0) {
            $('.js-open_production-no-order').css('cursor', 'pointer');
        }

        // map API fields to the classes used in the view
        $('.js-open_production-pending').text(mainFmt(mainNum(stats?.total_no_production_order), 0)); // "chưa mở lệnh"
        $('.js-open_production-approved').text(mainFmt(mainNum(stats?.total_has_production_order), 0)); // "đã mở lệnh"
        $('.js-open_production-with-order').text(mainFmt(mainNum(stats?.total_export_be), 0));
        $('.js-open_production-no-order').text(mainFmt(mainNum(stats?.total_be - stats?.total_export_be), 0));

        $('.js-open_production-npl-ready').css('cursor', 'unset');
        if (mainNum(stats?.npl_ready) > 0) {
            $('.js-open_production-npl-ready').css('cursor', 'pointer');
        }

        $('.js-open_production-npl-not-ready').css('cursor', 'unset');
        if (mainNum(stats?.vtsx_ready) > 0) {
            $('.js-open_production-npl-not-ready').css('cursor', 'pointer');
        }

        $('.js-open_production-vtsx-ready').css('cursor', 'unset');
        if (mainNum(stats?.vtsx_ready) > 0) {
            $('.js-open_production-vtsx-ready').css('cursor', 'pointer');
        }

        $('.js-open_production-vtsx-not-ready').css('cursor', 'unset');
        if (mainNum(stats?.vtsx_ready) > 0) {
            $('.js-open_production-vtsx-not-ready').css('cursor', 'pointer');
        }

        $('.js-open_production-npl-ready').text(mainFmt(mainNum(stats?.npl_ready), 0));
        $('.js-open_production-npl-not-ready').text(mainFmt(mainNum(stats?.npl_not_ready), 0));
        $('.js-open_production-vtsx-ready').text(mainFmt(mainNum(stats?.vtsx_ready), 0));
        $('.js-open_production-vtsx-not-ready').text(mainFmt(mainNum(stats?.vtsx_not_ready), 0));

        // prepare new chart data
        const labels = [
            'Tổng đơn',
            'Lệnh khuôn/bế',
            'NPL',
            'VTSX'
        ];

        const newDatasets = [{
                label: 'Chưa xử lý',
                data: [
                    mainNum(stats?.total_no_production_order) ?? 0,
                    (mainNum(stats?.total_be ?? 0) - mainNum(stats?.total_export_be ?? 0)) ?? 0,
                    mainNum(stats?.npl_not_ready) ?? 0,
                    mainNum(stats?.vtsx_not_ready) ?? 0, // fix: dùng vtsx_not_ready cho "chưa xử lý"
                ],
                backgroundColor: '#f5b7b1'
            },
            {
                label: 'Đã xử lý',
                data: [
                    mainNum(stats?.total_has_production_order) ?? 0,
                    mainNum(stats?.total_export_be) ?? 0,
                    mainNum(stats?.npl_ready) ?? 0,
                    mainNum(stats?.vtsx_ready) ?? 0,
                ],
                backgroundColor: '#82e0aa'
            }
        ];

        // if chart exists, compare only labels and data arrays; destroy only when changed
        if (window.myChartOpenProduction) {
            try {
                const old = window.myChartOpenProduction.data || {};
                const sameLabels = JSON.stringify(old.labels || []) === JSON.stringify(labels);
                const oldDatasets = (old.datasets || []).map(d => JSON.stringify(d.data || []));
                const newDatasetsData = newDatasets.map(d => JSON.stringify(d.data || []));
                const sameDatasets = oldDatasets.length === newDatasetsData.length &&
                    oldDatasets.every((d, i) => d === newDatasetsData[i]);

                if (sameLabels && sameDatasets) {
                    // data didn't change -> keep existing chart
                    return;
                }
            } catch (e) {
                // if any error during compare, fall back to recreate
            }
            window.myChartOpenProduction.destroy();
        }

        const ctx = document.querySelector('.js-chart-open_production').getContext('2d');


        // 🧩 Plugin tùy chỉnh vẽ nhãn “Chưa / Đã / Tổng”
        const smartLabelPlugin = {
            id: 'smartLabelPlugin',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                const scale = chart.scales.x;
                const meta0 = chart.getDatasetMeta(0);
                const data0 = chart.data.datasets[0].data;
                const data1 = chart.data.datasets[1].data;

                ctx.save();
                ctx.font = 'bold 11px sans-serif';
                ctx.textBaseline = 'middle';

                meta0.data.forEach((bar, i) => {
                    const c0 = data0[i] || 0;
                    const c1 = data1[i] || 0;
                    const total = c0 + c1;
                    if (c0 === 0 && c1 === 0) return;

                    const xStart = scale.getPixelForValue(0);
                    const xEnd = scale.getPixelForValue(total);
                    const barLength = xEnd - xStart;
                    const y = bar.y;

                    const label = `Chưa: ${mainFmt(mainNum(c0), 0)}   Đã: ${mainFmt(mainNum(c1), 0)}   Tổng: ${mainFmt(mainNum(total), 0)}`;

                    const inside = barLength > 200; // nếu cột dài hơn 200px => để chữ trong
                    const labelWidth = ctx.measureText(label).width;

                    const xText = inside ?
                        xStart + barLength / 2 - labelWidth / 2 // nằm giữa cột
                        :
                        xEnd + 10; // nằm bên phải cột

                    ctx.fillStyle = inside ? '#000' : '#000';
                    ctx.fillText(label, xText, y);
                });

                ctx.restore();
            }
        };

        window.myChartOpenProduction = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: newDatasets
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right: 200, // chừa chỗ bên phải để text không bị cắt
                        left: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    datalabels: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Tổng hợp trạng thái mở lệnh sản xuất',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.x || 0;
                                return `${label}: ${mainFmt(mainNum(value), 0)}`;
                            },
                            afterBody(context) {
                                const idx = context[0].dataIndex;
                                const total =
                                    (context[0].chart.data.datasets[0].data[idx] || 0) +
                                    (context[0].chart.data.datasets[1].data[idx] || 0);
                                return 'Tổng: ' + mainFmt(mainNum(total), 0);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng / giá trị'
                        },
                        grid: {
                            color: '#eee'
                        }
                    },
                    y: {
                        stacked: true,
                        ticks: {
                            font: {
                                size: 13
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            },
            plugins: [ChartDataLabels, smartLabelPlugin] // ✅ đăng ký plugin tại đây
        });

        ctx.canvas.parentNode.style.height = "640px";
        ctx.canvas.parentNode.style.width = "100%";
    }

    function count_open_production() {
        if ($('#dashboard-open-production-order').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_open_production') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsopen_production(res.stats || {});
            });
        }
    }
    count_open_production();
    setInterval(count_open_production, 20000);
</script>
<script>
    $(document).ready(function () {
        // Gộp 4 sự kiện click cho các nút NPL/VTSX
        const exportVtsxMap = {
            '.js-open_production-npl-not-ready': 1,
            '.js-open_production-npl-ready': 2,
            '.js-open_production-vtsx-not-ready': 3,
            '.js-open_production-vtsx-ready': 4
        };

        $.each(exportVtsxMap, function(selector, type) {
            $(document).on('click', selector, function(e) {
                var sample = $(selector).text();
                if (sample != '-') {
                    e.preventDefault();
                    var $btn = $(this);
                    $btn.addClass('loading');
                    $.ajax({
                        url: '<?= base_url('dashboard_srceen_vp/export_vtsx/') ?>' + type,
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
        });
    });
</script>