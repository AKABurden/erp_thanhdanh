<div id="dashboard_quotes" class="dashboard-quotes hide">
    <section class="sidebar-top" style="height: 180px;">
        <div class="thongke-box thongke-grid " style="padding: 12px;">
            <div class="box">
                <div class="label">TỔNG SỐ LƯỢNG BÁO GIÁ</div>
                <div class="value green js-quotes-total">-</div>
            </div>
        </div>
    </section>
    <div class="total_dashboard">
        <div class="container-detail">
            <section class="sidebar-left col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">CHƯA DUYỆT</div>
                        <div class="value red js-quotes-pending">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">CHƯA TẠO ĐƠN HÀNG</div>
                        <div class="value red js-quotes-no-order">-</div>
                    </div>
                </div>
            </section>
            <!-- Thanh ở giữa -->
            <div style="width:1px; background:#d0d0d0; height:calc(100vh - 375px); margin:50 0; float:left; display:inline-block;"></div>
            <section class="sidebar-right col-md-6" style="height: calc(100vh - 350px);">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label">ĐÃ DUYỆT</div>
                        <div class="value green js-quotes-approved">-</div>
                    </div>
                    <hr style="margin: 10px 0px 0px 0px;border: none;border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label">ĐÃ TẠO ĐƠN HÀNG</div>
                        <div class="value green js-quotes-with-order">-</div>
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
                        <canvas class="chart js-chart-quotes-approved" style="width:100%; height:100%;"></canvas>
                    </div>
                    <hr style="margin: 10px 97px 0 0;border: none;border-top: 1px solid #d0d0d0;width: 100%;">
                    <div class="chart js-chart-quotes" style="width:100%; height:100%;">
                        <canvas class="chart js-chart-quotes-delivery" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
        </div>
        </section>
    </div>
</div>

<script>
    // Click handler for "CHƯA DUYỆT" box - navigate to quotes list filtered to unapproved
    // Add pointer cursor to the "CHƯA DUYỆT" box for better UX

    $(document).on('click', '.js-quotes-no-order', function(e) {
        var quotes = $('.js-quotes-no-order').text();
        if (quotes != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_quotes_no_order/1') ?>',
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
    $(document).on('click', '.js-quotes-pending', function(e) {
        var quotes = $('.js-quotes-pending').text();
        if (quotes != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_quotes_no_order/2') ?>',
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


    function UpdateStatsQuotes(stats) {
        $('.js-quotes-total').text(mainFmt(mainNum(stats?.total), 0));
        $('.js-quotes-approved').text(mainFmt(mainNum(stats?.approved), 0));
        $('.js-quotes-pending').text(mainFmt(mainNum(stats.unapproved), 0));
        $('.js-quotes-with-order').text(mainFmt(mainNum(stats.has_order), 0));
        $('.js-quotes-no-order').text(mainFmt(mainNum(stats.no_order), 0));
        $('.js-quotes-no-order').css('cursor', 'unset');
        if (mainNum(stats.no_order) > 0) {
            $('.js-quotes-no-order').css('cursor', 'pointer');
        }
        $('.js-quotes-pending').css('cursor', 'unset');
        if (mainNum(stats.unapproved) > 0) {
            $('.js-quotes-pending').css('cursor', 'pointer');
        }
        // Only update/destroy/create chart if data changed
        if (
            window._lastStatsQuotes === undefined ||
            window._lastStatsQuotes.approved !== stats?.approved ||
            window._lastStatsQuotes.unapproved !== stats?.unapproved ||
            window._lastStatsQuotes.has_order !== stats?.has_order ||
            window._lastStatsQuotes.no_order !== stats?.no_order
        ) {
            // Approved/Pending donut chart with title
            if (window.myPieChart) window.myPieChart.destroy();
            const ctx = document.querySelector('.js-chart-quotes-approved').getContext('2d');
            window.myPieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Đã duyệt', 'Chưa duyệt'],
                    datasets: [{
                        data: [stats?.approved || 0, stats.unapproved || 0],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Tình trạng duyệt báo giá'
                        }
                    },
                    layout: {
                        padding: 0
                    },
                    cutout: '60%'
                }
            });
            ctx.canvas.parentNode.style.height = "240px";
            ctx.canvas.parentNode.style.width = "240px";
            ctx.canvas.style.height = "240px";
            ctx.canvas.style.width = "240px";

            // Order/No order donut chart with title
            if (window.myPieChartDelivery) window.myPieChartDelivery.destroy();
            const ctxDelivery = document.querySelector('.js-chart-quotes-delivery').getContext('2d');
            window.myPieChartDelivery = new Chart(ctxDelivery, {
                type: 'doughnut',
                data: {
                    labels: ['Đã tạo đơn hàng', 'Chưa tạo đơn hàng'],
                    datasets: [{
                        data: [stats?.has_order || 0, stats.no_order || 0],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Tình trạng tạo đơn hàng'
                        }
                    },
                    layout: {
                        padding: 0
                    },
                    cutout: '60%'
                }
            });
            ctxDelivery.canvas.parentNode.style.height = "240px";
            ctxDelivery.canvas.parentNode.style.width = "240px";
            ctxDelivery.canvas.style.height = "240px";
            ctxDelivery.canvas.style.width = "240px";

            // Save last stats for comparison
            window._lastStatsQuotes = {
                approved: stats?.approved,
                unapproved: stats?.unapproved,
                has_order: stats?.has_order,
                no_order: stats?.no_order
            };
        }
    }

    function countQuotes() {
        if ($('#dashboard-quotes').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/countQuotes') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsQuotes(res.stats || {});
            });
        }
    }
    
    setInterval(countQuotes, 20000);
</script>