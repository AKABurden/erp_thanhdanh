<div id="dashboard_purchase_products" class="dashboard-purchase_products hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ PHIẾU KHKD</div>
                <div class="value green js-purchase_products-full">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG SỐ PHIẾU KHKD CHƯA NHẬP</div>
                        <div class="value red js-purchase_products-not_import">-</div>
                    </div>
                    <div class="box">
                        <div class="label">TỔNG SỐ PHIẾU KHKD CHƯA DUYỆT KHO</div>
                        <div class="value red js-purchase_products-import_not_warehouseman">-</div>
                    </div>
                </div>
            </section>
            <!-- Thanh ở giữa -->
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 375px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label blue">TỔNG SỐ PHIẾU KHKD ĐÃ NHẬP</div>
                        <div class="value blue js-purchase_products-import">-</div>
                    </div>
                    <div class="box">
                        <div class="label blue">TỔNG SỐ PHIẾU KHKD ĐÃ DUYỆT KHO</div>
                        <div class="value blue js-purchase_products-import_warehouseman">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div style="width:1px;background:#d0d0d0;height: calc(100vh - 375px);margin: 0 0;float:left;display:inline-block;"></div>
    <div class="chart_dashboard">
        <div class="container-detail">
            <section class="col-md-12" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;">
                    <div class="chart js-chart-quotes" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-purchase_products-import" style="width:100%; height:100%;"></canvas>
                    </div>
                    <div class="chart js-chart-quotes" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-purchase_products-import_warehouseman" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
</div>
<script>
    $(document).on('click', '.js-purchase_products-not_import', function(e) {
        var sample = $('.js-purchase_products-not_import').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/purchase_products_plan_kd/1') ?>',
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
    $(document).on('click', '.js-purchase_products-import_not_warehouseman', function(e) {
        var sample = $('.js-purchase_products-import_not_warehouseman').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/purchase_products_plan_kd/2') ?>',
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
    function UpdateStatspurchase_products(stats) {
        $('.js-purchase_products-not_import').css('cursor', 'unset');
        if (mainNum(stats.total_no_import) > 0) {
            $('.js-purchase_products-not_import').css('cursor', 'pointer');
        }
        $('.js-purchase_products-import_not_warehouseman').css('cursor', 'unset');
        if (mainNum(stats.total_import_pending) > 0) {
            $('.js-purchase_products-import_not_warehouseman').css('cursor', 'pointer');
        }

        $('.js-purchase_products-full').text(mainFmt(mainNum(stats?.total_orders), 0));
        $('.js-purchase_products-not_import').text(mainFmt(mainNum(stats?.total_no_import), 0));
        $('.js-purchase_products-import_not_warehouseman').text(mainFmt(mainNum(stats?.total_import_pending), 0));

        $('.js-purchase_products-import').text(mainFmt(mainNum(stats?.total_has_import), 0));
        $('.js-purchase_products-import_warehouseman').text(mainFmt(mainNum(stats?.total_import_approved), 0));
        // render donut chart for "import" stats
        (function() {
            const canvas = document.querySelector('.js-chart-purchase_products-import');
            if (!canvas) return;

            const imported = Number(mainNum(stats?.total_has_import) || 0);
            const notImported = Number(mainNum(stats?.total_no_import) || 0);
            const total = Number(mainNum(stats?.total_orders) || (imported + notImported));

            const newKey = JSON.stringify([imported, notImported, total]);

            // If a chart already exists - update it if data changed, otherwise do nothing
            if (window._chart_purchase_products_import) {
                if (window._chart_purchase_products_import._dataKey === newKey) {
                    // no change -> nothing to do
                    return;
                } else {
                    // update existing chart data and total used by the center plugin
                    try {
                        window._chart_purchase_products_import.data.datasets[0].data = [imported, notImported];
                        window._chart_purchase_products_import._total = total;
                        window._chart_purchase_products_import._dataKey = newKey;
                        window._chart_purchase_products_import.update();
                    } catch (e) {
                        // fallback: destroy and recreate if update fails
                        try {
                            window._chart_purchase_products_import.destroy();
                        } catch (err) {}
                        window._chart_purchase_products_import = null;
                    }
                    if (window._chart_purchase_products_import) return;
                }
            }

            // create (or recreate) chart
            const centerTextPlugin = {
                id: 'centerTextPlugin',
                beforeDraw(chart) {
                    const ctx = chart.ctx;
                    const {
                        left,
                        right,
                        top,
                        bottom
                    } = chart.chartArea;
                    const x = (left + right) / 2;
                    const y = (top + bottom) / 2;
                    const t = chart._total || 0;

                    ctx.save();
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    // total (big)
                    ctx.font = '600 20px Arial';
                    ctx.fillText(mainFmt(t, 0), x, y - 8);

                    // label (small)
                    ctx.font = 'normal 12px Arial';
                    ctx.fillStyle = '#666';
                    ctx.fillText('TỔNG PHIẾU', x, y + 14);

                    ctx.restore();
                }
            };

            window._chart_purchase_products_import = new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Đã nhập', 'Chưa nhập'],
                    datasets: [{
                        data: [imported, notImported],
                        backgroundColor: ['#2b9df4', '#ff6b6b'],
                        hoverBackgroundColor: ['#1f7ee0', '#ff4b4b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '70%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'PHIẾU KHKD - NHẬP',
                            padding: {
                                top: 6,
                                bottom: 6
                            },
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.label + ': ' + mainFmt(mainNum(ctx.raw), 0);
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });

            // store current data key and total on the chart instance for future comparisons/updates
            window._chart_purchase_products_import._dataKey = newKey;
            window._chart_purchase_products_import._total = total;
        })();

        // render donut chart for "import_warehouseman" stats
        (function() {
            const canvas = document.querySelector('.js-chart-purchase_products-import_warehouseman');
            if (!canvas) return;

            const approved = Number(mainNum(stats?.total_import_approved) || 0);
            const notApproved = Number(mainNum(stats?.total_import_pending) || 0);
            const total = Number(mainNum(stats?.total_has_import) || (approved + notApproved));

            const newKey = JSON.stringify([approved, notApproved, total]);

            if (window._chart_purchase_products_import_warehouseman) {
                if (window._chart_purchase_products_import_warehouseman._dataKey === newKey) {
                    return;
                } else {
                    try {
                        window._chart_purchase_products_import_warehouseman.data.datasets[0].data = [approved, notApproved];
                        window._chart_purchase_products_import_warehouseman._total = total;
                        window._chart_purchase_products_import_warehouseman._dataKey = newKey;
                        window._chart_purchase_products_import_warehouseman.update();
                    } catch (e) {
                        try {
                            window._chart_purchase_products_import_warehouseman.destroy();
                        } catch (err) {}
                        window._chart_purchase_products_import_warehouseman = null;
                    }
                    if (window._chart_purchase_products_import_warehouseman) return;
                }
            }

            const centerTextPluginWarehouseman = {
                id: 'centerTextPluginWarehouseman',
                beforeDraw(chart) {
                    const ctx = chart.ctx;
                    const {
                        left,
                        right,
                        top,
                        bottom
                    } = chart.chartArea;
                    const x = (left + right) / 2;
                    const y = (top + bottom) / 2;
                    const t = chart._total || 0;

                    ctx.save();
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    ctx.font = '600 20px Arial';
                    ctx.fillText(mainFmt(t, 0), x, y - 8);

                    ctx.font = 'normal 12px Arial';
                    ctx.fillStyle = '#666';
                    ctx.fillText('TỔNG PHIẾU', x, y + 14);

                    ctx.restore();
                }
            };

            window._chart_purchase_products_import_warehouseman = new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Đã duyệt', 'Chưa duyệt'],
                    datasets: [{
                        data: [approved, notApproved],
                        backgroundColor: ['#2b9df4', '#ff6b6b'],
                        hoverBackgroundColor: ['#1f7ee0', '#ff4b4b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '70%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'PHIẾU KHKD - DUYỆT KHO',
                            padding: {
                                top: 6,
                                bottom: 6
                            },
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.label + ': ' + mainFmt(mainNum(ctx.raw), 0);
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPluginWarehouseman]
            });

            window._chart_purchase_products_import_warehouseman._dataKey = newKey;
            window._chart_purchase_products_import_warehouseman._total = total;
        })();
    }

    function countpurchase_products() {
        if ($('#dashboard-purchase_products').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_purchase_products') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatspurchase_products(res.stats || {});
            });
        }
    }
    countpurchase_products();
    setInterval(countpurchase_products, 20000);
</script>