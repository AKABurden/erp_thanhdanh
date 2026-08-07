<div id="dashboard_order_plan" class="dashboard-order_plan hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ LƯỢNG KẾ HOẠCH ĐƠN HÀNG</div>
                <div class="value green js-order-plan-total">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 350px);">
                <!-- <div class="kpi-box nau">
                <div class="label">KẾ HOẠCH ĐƠN HÀNG</div>
            </div> -->
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">KẾ HOẠCH ĐƠN HÀNG CHƯA DUYỆT</div>
                        <div class="value red js-order-plan-pending">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">CHƯA MỞ PHIẾU GIAO HÀNG</div>
                        <div class="value red js-order-plan-no-order">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">KẾ HOẠCH KINH DOANH CHƯA DUYỆT</div>
                        <div class="value red js-kh-plan-pending">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 375px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 350px);">
                <!-- <div class="kpi-box nau">
                <div class="label">KẾ HOẠCH ĐƠN HÀNG</div>
            </div> -->
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">KẾ HOẠCH ĐƠN HÀNG ĐÃ DUYỆT</div>
                        <div class="value green js-order-plan-approved">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">ĐÃ MỞ PHIẾU GIAO HÀNG</div>
                        <div class="value green js-order-plan-with-order">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">KẾ HOẠCH KINH DOANH ĐÃ DUYỆT</div>
                        <div class="value green js-kh-plan-with-order">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div style="width:1px;background:#d0d0d0;height: calc(100vh - 375px);float:left;display:inline-block;margin-left: -12px;"></div>
    <div class="chart_dashboard">
        <div class="container-detail">
            <section class="col-md-12" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;">
                    <div class="chart js-chart-sample" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-plan-order" style="width:100%; height:100%;"></canvas>
                    </div>
                    <div class="chart js-chart-quotes" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-plan-kd" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
</div>
<script>
    $(document).on('click', '.js-order-plan-pending', function(e) {
        var sample = $('.js-order-plan-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/order_plan/2') ?>',
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
    $(document).on('click', '.js-order-plan-no-order', function(e) {
        var sample = $('.js-order-plan-no-order').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/order_plan/1') ?>',
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
    $(document).on('click', '.js-kh-plan-pending', function(e) {
        var sample = $('.js-kh-plan-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/kh_plan') ?>',
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

    function UpdateStatsorder_plan(stats) {
        let total = mainNum(stats.total_approved) + mainNum(stats.total_un_approved);
        $('.js-order-plan-pending').css('cursor', 'unset');
        if (mainNum(stats.total_un_approved) > 0) {
            $('.js-order-plan-pending').css('cursor', 'pointer');
        }
        $('.js-order-plan-no-order').css('cursor', 'unset');
        if (mainNum(stats.total_not_delivered) > 0) {
            $('.js-order-plan-no-order').css('cursor', 'pointer');
        }
        $('.js-kh-plan-pending').css('cursor', 'unset');
        if (mainNum(stats.business_plan_total_un_approved) > 0) {
            $('.js-kh-plan-pending').css('cursor', 'pointer');
        }


        $('.js-order-plan-total').text(mainFmt(mainNum(total), 0));
        $('.js-order-plan-approved').text(mainFmt(mainNum(stats?.total_approved), 0));
        $('.js-order-plan-pending').text(mainFmt(mainNum(stats.total_un_approved), 0));
        $('.js-order-plan-with-order').text(mainFmt(mainNum(stats.total_delivered), 0));
        $('.js-order-plan-no-order').text(mainFmt(mainNum(stats.total_not_delivered), 0));

        $('.js-kh-plan-with-order').text(mainFmt(mainNum(stats.business_plan_total_approved), 0));
        $('.js-kh-plan-pending').text(mainFmt(mainNum(stats.business_plan_total_un_approved), 0));

        (function() {
            const canvas = document.querySelector('.js-chart-plan-order');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            // values from stats in scope
            const approved = mainNum(stats?.total_approved ?? 0);
            const unapproved = mainNum(stats?.total_un_approved ?? 0);
            const withOrder = mainNum(stats?.total_delivered ?? 0);
            const noOrder = mainNum(stats?.total_not_delivered ?? 0);

            const totalOrders = approved + unapproved + withOrder + noOrder;

            // if no data now
            if (totalOrders === 0) {
                // if there was an existing chart, remove it because now there's nothing to show
                if (window._chartPlanOrders) {
                    try {
                        window._chartPlanOrders.destroy();
                    } catch (e) {}
                    window._chartPlanOrders = null;
                    window._chartPlanOrdersKey = null;
                }
                return;
            }

            // compute key to detect changes
            const newKeyOrders = JSON.stringify([approved, unapproved, withOrder, noOrder]);

            // if data didn't change, do nothing (avoid unnecessary destroy/recreate)
            if (window._chartPlanOrdersKey === newKeyOrders) return;

            // otherwise recreate chart
            if (window._chartPlanOrders) {
                try {
                    window._chartPlanOrders.destroy();
                } catch (e) {}
                window._chartPlanOrders = null;
            }
            window._chartPlanOrdersKey = newKeyOrders;

            // center text & external labels plugin
            const centerTextPlugin = {
                id: 'centerTextPlugin',
                afterDraw(chart) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    let totalOuter = 0;
                    const outerDs = chart.data.datasets[0] || {
                        data: []
                    };
                    (outerDs.data || []).forEach(v => {
                        totalOuter += mainNum(v || 0);
                    });

                    const centerX = (chartArea.left + chartArea.right) / 2;
                    const centerY = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();

                    ctx.font = 'bold 20px sans-serif';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(mainFmt(mainNum(totalOuter), 0), centerX, centerY - 8);

                    ctx.font = '12px sans-serif';
                    ctx.fillStyle = '#666';
                    ctx.fillText('Tổng', centerX, centerY + 16);

                    const metaOuter = chart.getDatasetMeta(0);
                    if (!metaOuter || !metaOuter.data.length) {
                        ctx.restore();
                        return;
                    }

                    const baseY = centerY - 8;
                    const outerRadiusMax = metaOuter.data[0].outerRadius || Math.min(chartArea.width, chartArea.height) / 4;
                    const labelSpacingY = 18;

                    [0, 1].forEach(dsIndex => {
                        const meta = chart.getDatasetMeta(dsIndex);
                        if (!meta || !meta.data.length) return;

                        const ds = chart.data.datasets[dsIndex];
                        const firstArc = meta.data[0];

                        const midRadius = ((firstArc.outerRadius || outerRadiusMax) + (firstArc.innerRadius || 0)) / 2;
                        const side = dsIndex === 0 ? 1 : -1;
                        const angle = side === 1 ? 0 : Math.PI;
                        const targetX = centerX + Math.cos(angle) * midRadius;
                        const targetY = centerY + Math.sin(angle) * midRadius;

                        const labelX = centerX + side * (outerRadiusMax + 60);
                        const labelY = baseY + dsIndex * labelSpacingY;

                        ctx.beginPath();
                        ctx.strokeStyle = '#666';
                        ctx.lineWidth = 1;
                        const midLineX = centerX + side * (outerRadiusMax + 20);
                        const midLineY = labelY;
                        ctx.moveTo(labelX - side * 6, labelY);
                        ctx.lineTo(midLineX, midLineY);
                        ctx.lineTo(targetX, targetY);
                        ctx.stroke();

                        const arrowSize = 8;
                        const ang = Math.atan2(targetY - midLineY, targetX - midLineX);
                        ctx.save();
                        ctx.translate(targetX, targetY);
                        ctx.rotate(ang);
                        ctx.beginPath();
                        ctx.fillStyle = '#666';
                        ctx.moveTo(0, 0);
                        ctx.lineTo(-arrowSize, -arrowSize / 2);
                        ctx.lineTo(-arrowSize, arrowSize / 2);
                        ctx.closePath();
                        ctx.fill();
                        ctx.restore();

                        const boxSize = 10;
                        const boxX = labelX - side * (boxSize + 6);
                        const boxY = labelY - boxSize / 2;
                        let color = '#999';
                        if (Array.isArray(ds.backgroundColor)) color = ds.backgroundColor[0] || ds.backgroundColor[dsIndex] || color;
                        else color = ds.backgroundColor || color;
                        ctx.fillStyle = color;
                        ctx.fillRect(boxX, boxY, boxSize, boxSize);

                        ctx.textAlign = side === 1 ? 'left' : 'right';
                        ctx.font = '12px sans-serif';
                        ctx.fillStyle = '#333';
                        const textX = labelX + side * (boxSize / 2 + 8);
                        ctx.fillText(ds.label || (`Vòng ${dsIndex + 1}`), textX, labelY);
                    });

                    ctx.restore();
                }
            };

            window._chartPlanOrders = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Đã duyệt', 'Chưa duyệt'],
                    datasets: [{
                            label: 'Trạng thái',
                            data: [approved, unapproved],
                            backgroundColor: ['#28a745', '#e9626fff'],
                            hoverBackgroundColor: ['#2ecc71', '#e9626fff'],
                            hoverOffset: 6,
                            radius: '90%',
                            innerRadius: '60%'
                        },
                        {
                            label: 'Mở phiếu giao hàng',
                            data: [withOrder, noOrder],
                            backgroundColor: ['#1e7e34', '#e9626fff'],
                            hoverBackgroundColor: ['#27ae60', '#e9626fff'],
                            hoverOffset: 6,
                            radius: '75%',
                            innerRadius: '45%'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 8
                            }
                        },
                        title: {
                            display: true,
                            text: 'Kế hoạch đơn hàng — Duyệt & Giao hàng'
                        },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed ?? 0;
                                    const dsLabel = ctx.dataset.label ? (ctx.dataset.label + ' - ') : '';
                                    return dsLabel + (ctx.label || '') + ': ' + mainFmt(mainNum(v), 0);
                                },
                                afterBody(items) {
                                    if (!items || !items.length) return;
                                    const info = items[0];
                                    const ds = info.chart.data.datasets[info.datasetIndex];
                                    const total = (ds.data[0] ?? 0) + (ds.data[1] ?? 0);
                                    return 'Tổng: ' + mainFmt(mainNum(total), 0);
                                }
                            }
                        }
                    },
                    cutout: '40%'
                },
                plugins: [(window.ChartDataLabels || null), centerTextPlugin].filter(Boolean)
            });

            if (ctx.canvas && ctx.canvas.parentNode) {
                ctx.canvas.parentNode.style.height = '400px';
                ctx.canvas.parentNode.style.width = '100%';
            }
        })();
        (function() {
            const canvasKD = document.querySelector('.js-chart-plan-kd');
            if (!canvasKD) return;

            const ctxKD = canvasKD.getContext('2d');

            const approvedKD = mainNum(stats?.business_plan_total_approved ?? 0);
            const unapprovedKD = mainNum(stats?.business_plan_total_un_approved ?? 0);

            const totalKD = approvedKD + unapprovedKD;

            // if no data now
            if (totalKD === 0) {
                if (window._chartPlanKD) {
                    try {
                        window._chartPlanKD.destroy();
                    } catch (e) {}
                    window._chartPlanKD = null;
                    window._chartPlanKDKey = null;
                }
                return;
            }

            const newKeyKD = JSON.stringify([approvedKD, unapprovedKD]);

            if (window._chartPlanKDKey === newKeyKD) return;

            if (window._chartPlanKD) {
                try {
                    window._chartPlanKD.destroy();
                } catch (e) {}
                window._chartPlanKD = null;
            }
            window._chartPlanKDKey = newKeyKD;

            window._chartPlanKD = new Chart(ctxKD, {
                type: 'bar',
                data: {
                    labels: ['Kế hoạch KD'],
                    datasets: [{
                            label: 'Đã duyệt',
                            data: [approvedKD],
                            backgroundColor: '#28a745',
                            maxBarThickness: 40
                        },
                        {
                            label: 'Chưa duyệt',
                            data: [unapprovedKD],
                            backgroundColor: '#e9626f',
                            maxBarThickness: 40
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Kế hoạch Kinh Doanh — Trạng thái'
                        },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = (ctx.parsed && (ctx.parsed.y ?? ctx.parsed)) || 0;
                                    return (ctx.dataset.label ? (ctx.dataset.label + ': ') : '') + mainFmt(mainNum(v), 0);
                                },
                                afterBody() {
                                    const total = mainFmt(mainNum(totalKD), 0);
                                    return 'Tổng: ' + total;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            categoryPercentage: 0.3,
                            barPercentage: 0.9
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback(value) {
                                    return mainFmt(mainNum(value), 0);
                                }
                            }
                        }
                    }
                }
            });

            if (ctxKD.canvas && ctxKD.canvas.parentNode) {
                ctxKD.canvas.parentNode.style.height = '250px';
                ctxKD.canvas.parentNode.style.width = '100%';
            }

            (function renderKDNotes() {
                const parent = ctxKD.canvas.parentNode;
                if (!parent) return;
                const existing = parent.querySelector('.js-chart-kd-notes');
                if (existing) existing.remove();

                const notes = document.createElement('div');
                notes.className = 'js-chart-kd-notes';
                notes.style.display = 'flex';
                notes.style.justifyContent = 'center';
                notes.style.alignItems = 'center';
                notes.style.gap = '18px';
                notes.style.marginTop = '8px';
                notes.style.fontSize = '13px';
                notes.style.color = '#333';

                const makeItem = (color, label, value) => {
                    const wrap = document.createElement('div');
                    wrap.style.display = 'flex';
                    wrap.style.alignItems = 'center';
                    const box = document.createElement('span');
                    box.style.display = 'inline-block';
                    box.style.width = '12px';
                    box.style.height = '12px';
                    box.style.background = color;
                    box.style.marginRight = '8px';
                    box.style.borderRadius = '2px';
                    const txt = document.createElement('span');
                    txt.innerText = label + ': ' + mainFmt(mainNum(value), 0);
                    wrap.appendChild(box);
                    wrap.appendChild(txt);
                    return wrap;
                };

                notes.appendChild(makeItem('#999', 'Tổng', totalKD));
                notes.appendChild(makeItem('#28a745', 'Đã duyệt', approvedKD));
                notes.appendChild(makeItem('#e9626f', 'Chưa duyệt', unapprovedKD));

                parent.appendChild(notes);
            })();
        })();
    }

    function countorder_plan() {
        if ($('#dashboard-order-plan').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/countorder_plan') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsorder_plan(res.stats || {});
            });
        }
    }
    countorder_plan();
    setInterval(countorder_plan, 20000);
</script>