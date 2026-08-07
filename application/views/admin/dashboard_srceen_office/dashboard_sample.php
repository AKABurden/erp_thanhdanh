<div id="dashboard_sample" class="dashboard-sample hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ LƯỢNG PHÁT TRIỂN MẪU</div>
                <div class="value green js-sample-total">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 350px);">
                <!-- <div class="kpi-box nau">
                <div class="label">PHÁT TRIỂN MẪU</div>
            </div> -->
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">CHƯA DUYỆT</div>
                        <div class="value red js-sample-pending">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">CHƯA TẠO ĐƠN HÀNG</div>
                        <div class="value red js-sample-no-order">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 375px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 350px);">
                <!-- <div class="kpi-box nau">
                <div class="label">PHÁT TRIỂN MẪU</div>
            </div> -->
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">ĐÃ DUYỆT</div>
                        <div class="value green js-sample-approved">-</div>
                    </div>
                    <hr style="margin: 10px 0px 0px 0px;border: none;border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">ĐÃ TẠO ĐƠN HÀNG</div>
                        <div class="value green js-sample-with-order">-</div>
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
                        <canvas class="chart js-chart-sample-approved" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
</div>
<script>
    $(document).on('click', '.js-sample-no-order', function(e) {
        var sample = $('.js-sample-no-order').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_sample_no_order/1') ?>',
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
    $(document).on('click', '.js-sample-pending', function(e) {
        var sample = $('.js-sample-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_sample_no_order/2') ?>',
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

    function UpdateStatssample(stats) {

        $('.js-sample-no-order').css('cursor', 'unset');
        if (mainNum(stats.no_order) > 0) {
            $('.js-sample-no-order').css('cursor', 'pointer');
        }
        $('.js-sample-pending').css('cursor', 'unset');
        if (mainNum(stats.unapproved) > 0) {
            $('.js-sample-pending').css('cursor', 'pointer');
        }
        $('.js-sample-total').text(mainFmt(mainNum(stats?.total), 0));
        $('.js-sample-approved').text(mainFmt(mainNum(stats?.approved), 0));
        $('.js-sample-pending').text(mainFmt(mainNum(stats.unapproved), 0));
        $('.js-sample-with-order').text(mainFmt(mainNum(stats.has_order), 0));
        $('.js-sample-no-order').text(mainFmt(mainNum(stats.no_order), 0));
        // draw/update concentric (nested) doughnut chart: outer = approved/unapproved, inner = with_order/no_order
        (function() {
            const canvas = document.querySelector('.js-chart-sample-approved');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            // values from stats in scope
            const approved = mainNum(stats?.approved ?? 0);
            const unapproved = mainNum(stats?.unapproved ?? 0);
            const withOrder = mainNum(stats?.has_order ?? 0);
            const noOrder = mainNum(stats?.no_order ?? 0);

            // quick check: if chart exists and data unchanged, do nothing
            const newStats = {
                approved,
                unapproved,
                withOrder,
                noOrder
            };
            if (window._chartSampleApproved && window._chartSampleApproved._lastStats) {
                const last = window._chartSampleApproved._lastStats;
                if (last.approved === newStats.approved &&
                    last.unapproved === newStats.unapproved &&
                    last.withOrder === newStats.withOrder &&
                    last.noOrder === newStats.noOrder) {
                    // no change -> avoid re-rendering/destroying chart
                    return;
                }
            }

            // center text & external labels plugin
            const centerTextPlugin = {
                id: 'centerTextPlugin',
                afterDraw(chart) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    // total of outer dataset (dataset 0)
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

                    // big total number
                    ctx.font = 'bold 20px sans-serif';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(mainFmt(mainNum(totalOuter), 0), centerX, centerY - 8);

                    // small label under total
                    ctx.font = '12px sans-serif';
                    ctx.fillStyle = '#666';
                    ctx.fillText('Tổng', centerX, centerY + 16);

                    // get arc metas for drawing external labels/arrows
                    const metaOuter = chart.getDatasetMeta(0);
                    if (!metaOuter || !metaOuter.data.length || !metaOuter.data[0]) {
                        ctx.restore();
                        return;
                    }

                    const baseY = centerY - 8;
                    const outerRadiusMax = metaOuter.data[0].outerRadius || Math.min(chartArea.width, chartArea.height) / 4;
                    const labelSpacingY = 18;

                    // draw two external labels (dataset 0 -> right, dataset 1 -> left)
                    [0, 1].forEach(dsIndex => {
                        const meta = chart.getDatasetMeta(dsIndex);
                        if (!meta || !meta.data.length || !meta.data[0]) return;

                        const ds = chart.data.datasets[dsIndex];
                        const firstArc = meta.data[0];

                        // Defensive: ensure firstArc has required properties
                        if (typeof firstArc.outerRadius !== 'number' || typeof firstArc.innerRadius !== 'number') return;

                        const midRadius = ((firstArc.outerRadius || outerRadiusMax) + (firstArc.innerRadius || 0)) / 2;
                        const side = dsIndex === 0 ? 1 : -1;
                        const angle = side === 1 ? 0 : Math.PI;
                        const targetX = centerX + Math.cos(angle) * midRadius;
                        const targetY = centerY + Math.sin(angle) * midRadius;

                        const labelX = centerX + side * (outerRadiusMax + 60);
                        const labelY = baseY + dsIndex * labelSpacingY;

                        // connecting line
                        ctx.beginPath();
                        ctx.strokeStyle = '#666';
                        ctx.lineWidth = 1;
                        const midLineX = centerX + side * (outerRadiusMax + 20);
                        const midLineY = labelY;
                        ctx.moveTo(labelX - side * 6, labelY);
                        ctx.lineTo(midLineX, midLineY);
                        ctx.lineTo(targetX, targetY);
                        ctx.stroke();

                        // arrow triangle at target
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

                        // color box next to label
                        const boxSize = 10;
                        const boxX = labelX - side * (boxSize + 6);
                        const boxY = labelY - boxSize / 2;
                        let color = '#999';
                        if (Array.isArray(ds.backgroundColor)) color = ds.backgroundColor[0] || ds.backgroundColor[dsIndex] || color;
                        else color = ds.backgroundColor || color;
                        ctx.fillStyle = color;
                        ctx.fillRect(boxX, boxY, boxSize, boxSize);

                        // label text
                        ctx.textAlign = side === 1 ? 'left' : 'right';
                        ctx.font = '12px sans-serif';
                        ctx.fillStyle = '#333';
                        const textX = labelX + side * (boxSize / 2 + 8);
                        ctx.fillText(ds.label || (`Vòng ${dsIndex + 1}`), textX, labelY);
                    });

                    ctx.restore();
                }
            };

            // if chart exists -> update datasets and avoid full recreate
            if (window._chartSampleApproved) {
                try {
                    const chart = window._chartSampleApproved;
                    chart.data.datasets[0].data = [approved, unapproved];
                    chart.data.datasets[1].data = [withOrder, noOrder];
                    // update labels in case locale/data changed
                    chart.data.labels = ['Đã duyệt', 'Chưa duyệt'];
                    chart._lastStats = newStats;
                    chart.update({
                        duration: 400,
                        easing: 'easeOutQuart'
                    });
                } catch (e) {
                    // fallback: destroy and recreate if update fails
                    try {
                        window._chartSampleApproved.destroy();
                    } catch (e2) {}
                    window._chartSampleApproved = null;
                }
                return;
            }

            // create chart for the first time
            window._chartSampleApproved = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    // labels used in tooltips
                    labels: ['Đã duyệt', 'Chưa duyệt'],
                    datasets: [{
                            label: 'Trạng thái',
                            data: [approved, unapproved],
                            backgroundColor: ['#28a745', '#dc3545'],
                            hoverBackgroundColor: ['#2ecc71', '#e74c3c'],
                            hoverOffset: 6,
                            radius: '90%',
                            innerRadius: '60%'
                        },
                        {
                            label: 'Tạo đơn hàng',
                            data: [withOrder, noOrder],
                            backgroundColor: ['#1e7e34', '#c82333'],
                            hoverBackgroundColor: ['#27ae60', '#c0392b'],
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
                            text: 'Phát triển mẫu — Duyệt & Đơn hàng'
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

            window._chartSampleApproved._lastStats = newStats;

            // ensure parent has a reasonable fixed height
            if (ctx.canvas && ctx.canvas.parentNode) {
                ctx.canvas.parentNode.style.height = '350px';
                ctx.canvas.parentNode.style.width = '100%';
            }
        })();
    }

    function countsample() {
        if ($('#dashboard-sample').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/countsample') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatssample(res.stats || {});
            });
        }
    }
    countsample();
    setInterval(countsample, 20000);
</script>