<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/dasboard_warehouse/dasboard_warehouse_css'); ?>
</head>

<body>
    <div class="app_dwh">
        <!-- HEADER -->
        <div class="header_dwh">
            <div class="logo_dwh">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_dwh">
                <span class="main">PHÒNG MUA HÀNG - KHO HÀNG</span><br>
            </div>
            <div class="header-right_dwh">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_dwh" style="display:flex;gap:7px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_dwh">
                <div class="kpi-box_dwh blue_dwh" style="background-color: #BA925D;">
                    <div class="label">ĐỀ XUẤT NỘI BỘ</div>
                </div>
                <div class="thongke-box_dwh thongke-grid_dwh-1">
                    <div class="box-dwh" style="padding: 25px;">
                        <div class="label">ĐỀ XUẤT NỘI BỘ CHƯA DUYỆT</div>
                        <div class="value blue js-de-xuat-noi-bo-unapproved">-</div>
                    </div>
                </div>
                <div class="kpi-box_dwh blue_dwh" style="background-color: #BA925D;">
                    <div class="label">MUA HÀNG</div>
                </div>
                <div class="thongke-box_dwh thongke-grid_dwh-1">
                    <div class="box-dwh" style="padding: 25px;">
                        <div class="label-2">PO CHƯA NHẬP KHO</div>
                        <div class="value js-po-not-in">-</div>
                    </div>
                </div>

                <div class="kpi-box_dwh blue_dwh" style="background-color: #BA925D;">
                    <div class="label">THÀNH PHẨM</div>
                </div>
                <div class="thongke-box_dwh thongke-grid_dwh-1">
                    <div class="box-dwh" style="padding: 25px;">
                        <div class="label">TỔNG SL THÀNH PHẨM CHƯA NHẬP</div>
                        <div class="value js-finished-qty-not-in">-</div>
                    </div>
                </div>
            </aside>

            <section class="table-wrapper_dwh">
                <div class="kpi-box_dwh blue_dwh" style="background-color: #BA925D;">
                    <div class="label">ĐỀ XUẤT NỘI BỘ</div>
                </div>
                <div class="thongke-grid_dwh-1">
                    <div class="box-dwh" style="padding: 25px;">
                        <div class="label">ĐỀ XUẤT NỘI BỘ ĐÃ DUYỆT</div>
                        <div class="value blue js-de-xuat-noi-bo-approved">-</div>
                    </div>
                </div>
                <div class="kpi-box_dwh blue_dwh" style="background-color: #BA925D;">
                    <div class="label">MUA HÀNG</div>
                </div>
                <div class="thongke-grid_dwh-1">
                    <div class="box-dwh" style="padding: 25px;">
                        <div class="label-2">PO ĐÃ NHẬP KHO</div>
                        <div class="value js-po-in">-</div>
                    </div>
                </div>

                <div class="kpi-box_dwh blue_dwh" style="background-color: #BA925D;">
                    <div class="label">THÀNH PHẨM</div>
                </div>
                <div class="thongke-box_dwh thongke-grid_dwh-1">
                    <div class="box-dwh" style="padding: 25px;">
                        <div class="label">TỔNG SL THÀNH PHẨM ĐÃ NHẬP</div>
                        <div class="value blue js-finished-qty-in">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>

</script>

<script>
    // Đồng hồ realtime theo Asia/Ho_Chi_Minh
    (function startRealtimeClock_dwh_hm() {
        const elDate = document.getElementById('clock-date');
        const elTime = document.getElementById('clock-time');
        if (!elDate || !elTime) return;

        const tz = 'Asia/Ho_Chi_Minh';

        // Format ngày: bạn có thể chỉnh lại tùy ý
        const fmtDate = new Intl.DateTimeFormat('vi-VN', {
            timeZone: tz,
            weekday: 'long',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        // Format giờ 12h với AM/PM giống "9:29 AM"
        const fmtTime = new Intl.DateTimeFormat('en-US', {
            timeZone: tz,
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        function tick() {
            const now = new Date();
            elDate.textContent = fmtDate.format(now); // ví dụ: "thứ Hai, 29/09/2025"
            elTime.textContent = fmtTime.format(now); // ví dụ: "9:29:05 AM"
        }

        // Căn cho đúng đầu giây rồi mới setInterval mỗi 1s để đỡ lệch nhịp
        tick();
        const ms = 1000 - (Date.now() % 1000);
        setTimeout(() => {
            tick();
            setInterval(tick, 1000);
        }, ms);
    })();
</script>


</html>