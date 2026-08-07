<div id="dashboard_export" class="dashboard-export hide">
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG PHIẾU XUẤT CHƯA DUYỆT NPL</div>
                        <div class="value red js-export-NPL-pending">-</div>
                    </div>
                    <div class="box">
                        <div class="label">TỔNG PHIẾU XUẤT CHƯA DUYỆT TP</div>
                        <div class="value red js-export-TP-pending">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG PHIẾU XUẤT ĐÃ DUYỆT NPL</div>
                        <div class="value green js-export-NPL-approved">-</div>
                    </div>
                    <div class="box">
                        <div class="label">TỔNG PHIẾU XUẤT ĐÃ DUYỆT TP</div>
                        <div class="value green js-export-TP-approved">-</div>
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
                        <canvas class="chart js-chart-export" style="width:400px; height:400px;"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.js-export-NPL-pending', function(e) {
        var sample = $('.js-export-NPL-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/export_npl_tp/1') ?>',
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
    $(document).on('click', '.js-export-TP-pending', function(e) {
        var sample = $('.js-export-TP-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/export_npl_tp/2') ?>',
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
    function UpdateStatsEport(stats) {
        $('.js-export-NPL-pending').css('cursor', 'unset');
        if (mainNum(stats.nvl_total_cx) > 0) {
            $('.js-export-NPL-pending').css('cursor', 'pointer');
        }
        $('.js-export-TP-pending').css('cursor', 'unset');
        if (mainNum(stats.product_total_cx) > 0) {
            $('.js-export-TP-pending').css('cursor', 'pointer');
        }
        // --- Cập nhật số liệu chữ ---
        $('.js-export-NPL-pending').text(mainFmt(mainNum(stats?.nvl_total_cx), 0));
        $('.js-export-TP-pending').text(mainFmt(mainNum(stats?.product_total_cx), 0));
        $('.js-export-NPL-approved').text(mainFmt(mainNum(stats?.nvl_total_dx), 0));
        $('.js-export-TP-approved').text(mainFmt(mainNum(stats?.product_total_dx), 0));

        // --- Biểu đồ cột chồng ngang ---
        const newChartData = {
            pending: [stats?.nvl_total_cx ?? 0, stats?.product_total_cx ?? 0],
            approved: [stats?.nvl_total_dx ?? 0, stats?.product_total_dx ?? 0]
        };

        // Nếu dữ liệu không đổi thì không destroy / recreate chart
        if (window.myChartExportData && JSON.stringify(window.myChartExportData) === JSON.stringify(newChartData)) {
            return;
        }
        window.myChartExportData = newChartData;

        if (window.myChartExport) window.myChartExport.destroy();

        const ctx = document.querySelector('.js-chart-export').getContext('2d');

        // ✅ Plugin chắc chắn hiển thị nhãn "Chưa / Đã / Tổng"
        const smartLabelPlugin = {
            id: 'smartLabelPlugin',
            afterRender(chart) {
                const ctx = chart.ctx;
                const scale = chart.scales.x;
                const meta0 = chart.getDatasetMeta(0);
                const data0 = chart.data.datasets[0].data;
                const data1 = chart.data.datasets[1].data;

                ctx.save();
                ctx.font = 'bold 12px sans-serif';
                ctx.textBaseline = 'middle';
                ctx.textAlign = 'left';
                ctx.fillStyle = '#000';

                meta0.data.forEach((bar, i) => {
                    const c0 = data0[i] || 0;
                    const c1 = data1[i] || 0;
                    const total = c0 + c1;
                    if (c0 === 0 && c1 === 0) return;

                    const xStart = scale.getPixelForValue(0);
                    const xEnd = scale.getPixelForValue(total);
                    const barLength = Math.abs(xEnd - xStart);
                    const y = bar.y;

                    const label = `Chưa: ${mainFmt(mainNum(c0), 0)}   Đã: ${mainFmt(mainNum(c1), 0)}   Tổng: ${mainFmt(mainNum(total), 0)}`;
                    const labelWidth = ctx.measureText(label).width;

                    const insideFits = labelWidth + 20 <= barLength;
                    let xText;
                    if (insideFits) {
                        // chữ nằm trong thanh
                        ctx.fillStyle = '#left';
                        ctx.textAlign = 'center';
                        xText = xStart + barLength / 2;
                    } else {
                        // chữ nằm ngoài
                        ctx.fillStyle = '#000';
                        ctx.textAlign = 'left';
                        xText = xEnd + 8;
                    }

                    // 🔥 Bỏ giới hạn clip → vẽ toàn canvas
                    ctx.save();
                    ctx.beginPath();
                    ctx.rect(0, 0, chart.width + 500, chart.height + 100);
                    ctx.clip();
                    ctx.fillText(label, xText, y);
                    ctx.restore();
                });

                ctx.restore();
            }
        };

        window.myChartExport = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Phiếu xuất NPL', 'Phiếu xuất TP'],
                datasets: [{
                        label: 'Chưa duyệt',
                        data: newChartData.pending,
                        backgroundColor: '#f5b7b1'
                    },
                    {
                        label: 'Đã duyệt',
                        data: newChartData.approved,
                        backgroundColor: '#82e0aa'
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right: 250,
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
                        text: 'Tình trạng duyệt phiếu xuất kho',
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
                            text: 'Số lượng phiếu'
                        },
                        grid: {
                            color: '#eee'
                        }
                    },
                    y: {
                        stacked: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 13
                            }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels, smartLabelPlugin]
        });

        ctx.canvas.parentNode.style.height = "420px";
        ctx.canvas.parentNode.style.width = "100%";
    }




    function count_export() {
        if ($('#dashboard-export').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_export') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsEport(res.stats || {});
            });
        }
    }
    count_export();
    setInterval(count_export, 20000);
</script>