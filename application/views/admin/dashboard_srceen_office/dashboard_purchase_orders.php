<div id="dashboard_purchase_orders" class="dashboard-purchase_orders hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ PHIẾU NHẬP ĐỦ</div>
                <div class="value green js-purchase_orders-full">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG SỐ PHIẾU CHƯA NHẬP</div>
                        <div class="value red js-purchase_orders-not_import">-</div>
                    </div>
                </div>
            </section>
            <!-- Thanh ở giữa -->
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 375px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label red">TỔNG SỐ PHIẾU NHẬP 1 PHẦN</div>
                        <div class="value red js-purchase_orders-part_import">-</div>
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
                        <canvas class="chart js-chart-purchase_orders" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
</div>
<script>
    $(document).on('click', '.js-purchase_orders-part_import', function(e) {
        var sample = $('.js-purchase_orders-part_import').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/purchase_orders_import/2') ?>',
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
    $(document).on('click', '.js-purchase_orders-not_import', function(e) {
        var sample = $('.js-purchase_orders-not_import').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/purchase_orders_import/1') ?>',
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
    function UpdateStatspurchase_orders(stats) {
        $('.js-purchase_orders-part_import').css('cursor', 'unset');
        if (mainNum(stats.part_import) > 0) {
            $('.js-purchase_orders-part_import').css('cursor', 'pointer');
        }
        $('.js-purchase_orders-not_import').css('cursor', 'unset');
        if (mainNum(stats.not_import) > 0) {
            $('.js-purchase_orders-not_import').css('cursor', 'pointer');
        }

        $('.js-purchase_orders-full').text(mainFmt(mainNum(stats?.import_full), 0));
        $('.js-purchase_orders-part_import').text(mainFmt(mainNum(stats?.part_import), 0));
        $('.js-purchase_orders-not_import').text(mainFmt(mainNum(stats?.not_import), 0));
        const fullVal = mainNum(stats?.import_full) || 0;
        const partVal = mainNum(stats?.part_import) || 0;
        const notVal = mainNum(stats?.not_import) || 0;

        const canvas = document.querySelector('.js-chart-purchase_orders');
        if (canvas) {
            // make chart smaller
            canvas.style.height = '250px';
            canvas.style.maxWidth = '380px';
            if (canvas.parentElement) {
                canvas.parentElement.style.display = 'flex';
                canvas.parentElement.style.flexDirection = 'row'; // show chart + legend side by side
                canvas.parentElement.style.justifyContent = 'center';
                canvas.parentElement.style.alignItems = 'center';
                canvas.parentElement.style.gap = '16px';
            }

            const ctx = canvas.getContext('2d');

            const total = (fullVal || 0) + (partVal || 0) + (notVal || 0);
            const values = [fullVal, partVal, notVal];
            const baseLabels = ['TỔNG SỐ PHIẾU NHẬP ĐỦ', 'TỔNG SỐ PHIẾU NHẬP 1 PHẦN', 'TỔNG SỐ PHIẾU CHƯA NHẬP'];
            const labelsWithNumbers = baseLabels.map((l, i) => {
                const v = Number(values[i]) || 0;
                const pct = total ? ((v / total) * 100).toFixed(1) + '%' : '0%';
                return `${l} — ${mainFmt(v, 0)} (${pct})`;
            });

            const colors = ['#28a745', '#fd7e14', '#dc3545'];

            // Only destroy/recreate the chart if data actually changed
            const newDataKey = JSON.stringify(values);
            const prevDataKey = window.__chart_purchase_orders_data || null;

            if (prevDataKey === newDataKey && window.__chart_purchase_orders) {
                // data didn't change — no need to rebuild chart or legend
                return;
            }

            // destroy previous chart instance if exists and data changed
            if (window.__chart_purchase_orders) {
                try {
                    window.__chart_purchase_orders.destroy();
                } catch (e) {
                    /* ignore */ }
                window.__chart_purchase_orders = null;
            }

            // center text plugin to show total
            const centerText = {
                id: 'centerText',
                afterDraw(chart) {
                    const {
                        ctx,
                        chartArea: {
                            left,
                            right,
                            top,
                            bottom
                        }
                    } = chart;
                    const cx = (left + right) / 2;
                    const cy = (top + bottom) / 2;
                    ctx.save();
                    ctx.font = 'bold 14px Arial';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('TỔNG: ' + mainFmt(total, 0), cx, cy);
                    ctx.restore();
                }
            };

            window.__chart_purchase_orders = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labelsWithNumbers,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }, // hide default, we'll create custom annotation
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = Number(context.parsed) || 0;
                                    const pct = total ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                    return context.label.split(' — ')[0] + ': ' + mainFmt(value, 0) + ' (' + pct + ')';
                                }
                            }
                        }
                    }
                },
                plugins: [centerText]
            });

            // store current data key to avoid unnecessary destroys next time
            window.__chart_purchase_orders_data = newDataKey;

            // create or update a custom legend with a colored bar and arrow for each type
            try {
                // remove existing legend if any
                const existingLegend = canvas.parentElement.querySelector('.js-purchase-orders-legend');
                if (existingLegend) existingLegend.remove();

                const legend = document.createElement('div');
                legend.className = 'js-purchase-orders-legend';
                // styling for legend container
                Object.assign(legend.style, {
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '10px',
                    alignItems: 'flex-start',
                    fontSize: '13px',
                    color: '#333',
                    maxWidth: '260px'
                });

                labelsWithNumbers.forEach((labelText, idx) => {
                    const item = document.createElement('div');
                    Object.assign(item.style, {
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px'
                    });

                    // colored bar
                    const bar = document.createElement('span');
                    Object.assign(bar.style, {
                        display: 'inline-block',
                        width: '36px',
                        height: '8px',
                        backgroundColor: colors[idx] || '#999',
                        borderRadius: '4px',
                        flex: '0 0 auto'
                    });

                    // arrow (triangle) pointing toward the chart
                    const arrow = document.createElement('span');
                    Object.assign(arrow.style, {
                        display: 'inline-block',
                        width: '0',
                        height: '0',
                        borderLeft: '6px solid transparent',
                        borderRight: '6px solid transparent',
                        borderTop: `8px solid ${colors[idx] || '#999'}`,
                        transform: 'rotate(-90deg)', // point right towards the chart
                        marginLeft: '2px',
                        flex: '0 0 auto'
                    });

                    // label text (first part before ' — ' to keep it short) and numbers
                    const label = document.createElement('div');
                    label.innerText = labelText;
                    Object.assign(label.style, {
                        lineHeight: '1.1',
                        maxWidth: '200px',
                        wordBreak: 'break-word'
                    });

                    item.appendChild(bar);
                    item.appendChild(arrow);
                    item.appendChild(label);
                    legend.appendChild(item);
                });

                // append legend after canvas (inside same parent)
                canvas.parentElement.appendChild(legend);
            } catch (e) {
                // silently ignore DOM issues
            }
        }
    }

    function countpurchase_orders() {
        if ($('#dashboard-purchase_orders').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_purchase_orders') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatspurchase_orders(res.stats || {});
            });
        }
    }
    countpurchase_orders();
    setInterval(countpurchase_orders, 20000);
</script>