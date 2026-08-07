<div id="dashboard_ghi_kem" class="dashboard-ghi_kem hide">
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH CHƯA XUẤT GHI KẼM</div>
                        <div class="value red js-ghi-kem-pending">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right" style="height: calc(100vh - 169px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">TỔNG LỆNH ĐÃ XUẤT GHI KẼM</div>
                        <div class="value green js-ghi-kem-approved">-</div>
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
                        <canvas class="chart js-chart-ghi_kem" style="width:400px; height:400px;"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.js-ghi-kem-pending', function(e) {
        var sample = $('.js-ghi-kem-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/export_khuonbe/2') ?>',
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

    function UpdateStatsGhiKem(stats) {
        $('.js-ghi-kem-pending').css('cursor', 'unset');
        if (mainNum(stats.total_ghep_size_pending) > 0) {
            $('.js-ghi-kem-pending').css('cursor', 'pointer');
        }
        // map API fields to the classes used in the view
        $('.js-ghi-kem-pending').text(mainFmt(mainNum(stats?.total_ghikem - stats?.total_export_ghikem), 0)); // "chưa mở lệnh"
        $('.js-ghi-kem-approved').text(mainFmt(mainNum(stats?.total_export_ghikem), 0)); // "đã mở lệnh"
        (function() {
            const canvas = document.querySelector('.js-chart-ghi_kem');
            if (!canvas || typeof Chart === 'undefined') return;

            const pending = Math.max(0, mainNum(stats?.total_ghikem - stats?.total_export_ghikem));
            const approved = Math.max(0, mainNum(stats?.total_export_ghikem));

            const data = {
                labels: ['Chưa xuất', 'Đã xuất'],
                datasets: [{
                    data: [pending, approved],
                    backgroundColor: ['#e74c3c', '#27ae60'],
                    hoverBackgroundColor: ['#c0392b', '#219150'],
                    borderWidth: 0
                }]
            };

            const cfg = {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const v = context.parsed;
                                    return context.label + ': ' + mainFmt(v, 0);
                                }
                            }
                        }
                    }
                }
            };

            // If chart exists and data didn't change, do nothing.
            const newDataArr = [pending, approved];
            if (window._ghiKemLastData && Array.isArray(window._ghiKemLastData) &&
                window._ghiKemLastData.length === 2 &&
                window._ghiKemLastData[0] === newDataArr[0] &&
                window._ghiKemLastData[1] === newDataArr[1]) {
                return;
            }

            // Try to update existing chart in-place if present
            if (window.ghiKemChart) {
                try {
                    // update data and redraw
                    window.ghiKemChart.data.datasets[0].data = newDataArr;
                    window.ghiKemChart.update();
                    window._ghiKemLastData = newDataArr;
                    return;
                } catch (e) {
                    // fallback to destroy & recreate
                    try {
                        window.ghiKemChart.destroy();
                    } catch (e2) {}
                    window.ghiKemChart = null;
                }
            }

            // create new chart
            try {
                window.ghiKemChart = new Chart(canvas.getContext('2d'), cfg);
                window._ghiKemLastData = newDataArr;
            } catch (e) {
                // ignore chart creation errors
            }
        })();
    }

    function count_ghi_kem() {
        if ($('#dashboard-ghi-kem').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_ghi_kem') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsGhiKem(res.stats || {});
            });
        }
    }
    count_ghi_kem();
    setInterval(count_ghi_kem, 20000);
</script>