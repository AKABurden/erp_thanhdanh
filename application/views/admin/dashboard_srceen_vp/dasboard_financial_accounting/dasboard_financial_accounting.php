<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/dasboard_financial_accounting/dasboard_financial_accounting_css'); ?>
</head>

<body>
    <div class="app_dfac">
        <!-- HEADER -->
        <div class="header_dfac">
            <div class="logo_dfac">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_dfac">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
            </div>
            <div class="header-right_dfac">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_dfac" style="display:flex;gap:7px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_dfac">
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">HÓA ĐƠN BÁN</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac-3">
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN BÁN CHƯA XUẤT</div>
                        <div class="value green js-hoa-don-ban-not-issued">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN BÁN CHƯA KÊ KHAI</div>
                        <div class="value red js-hoa-don-ban-not-declared">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN BÁN CHƯA THU</div>
                        <div class="value green js-hoa-don-ban-not-received">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">ĐỀ XUẤT NỘI BỘ - ĐỀ XUẤT TÀI CHÍNH</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac">
                    <div class="box-dfac">
                        <div class="label">ĐỀ XUẤT NỘI BỘ CHƯA HOÀN THÀNH</div>
                        <div class="value blue js-de-xuat-noi-bo-pending">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">PHIẾU ĐỀ XUẤT TÀI CHÍNH CHƯA XỬ LÝ</div>
                        <div class="value blue js-de-xuat-tc-pending">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">HÓA ĐƠN MUA</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac">
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN MUA CHƯA KÊ KHAI</div>
                        <div class="value blue js-hoa-don-mua-not-declared">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">CHƯA NHẬP THEO ĐXNB</div>
                        <div class="value blue js-hoa-don-mua-dxnb-not-in">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">TÁI KÝ HỢP ĐỒNG</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac">
                    <div class="box-dfac">
                        <div class="label">TÁI KÝ HỢP ĐỒNG THEO KHÁCH HÀNG</div>
                        <div class="value blue js-tai-ky-khach-hang">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">TÁI KÝ HỢP ĐỒNG THEO NHÀ CUNG CẤP</div>
                        <div class="value blue js-tai-ky-ncc">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">YÊU CẦU CHI</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac-1">
                    <div class="box-dfac">
                        <div class="label">PHIẾU YÊU CẦU CHI CHƯA XỬ LÝ</div>
                        <div class="value blue js-yeu-cau-chi-unprocessed">-</div>
                    </div>
                </div>

                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">PHIẾU GIAO HÀNG</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac-1">
                    <div class="box-dfac">
                        <div class="label">CHƯA THU TIỀN PHIẾU GIAO HÀNG</div>
                        <div class="value blue js-phieu-giao-chua-thu">-</div>
                    </div>
                </div>
            </aside>
            <section class="table-wrapper_dfac">
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">HÓA ĐƠN BÁN</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac-3">
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN BÁN ĐÃ XUẤT</div>
                        <div class="value green js-hoa-don-ban-issued">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN BÁN ĐÃ KÊ KHAI</div>
                        <div class="value red js-hoa-don-ban-declared">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN BÁN ĐÃ THU</div>
                        <div class="value green js-hoa-don-ban-received">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">ĐỀ XUẤT NỘI BỘ - ĐỀ XUẤT TÀI CHÍNH</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac">
                    <div class="box-dfac">
                        <div class="label">ĐỀ XUẤT NỘI BỘ ĐÃ HOÀN THÀNH</div>
                        <div class="value blue js-de-xuat-noi-bo-completed">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">PHIẾU ĐỀ XUẤT TÀI CHÍNH ĐÃ XỬ LÝ</div>
                        <div class="value blue js-de-xuat-tc-processed">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">HÓA ĐƠN MUA</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac">
                    <div class="box-dfac">
                        <div class="label">HÓA ĐƠN MUA ĐÃ KÊ KHAI</div>
                        <div class="value blue js-hoa-don-mua-declared">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">ĐÃ NHẬP THEO ĐXNB</div>
                        <div class="value blue js-hoa-don-mua-dxnb-in">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">TÁI KÝ HỢP ĐỒNG</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac">
                    <div class="box-dfac">
                        <div class="label">ĐÃ TÁI KÝ HỢP ĐỒNG THEO KHÁCH HÀNG</div>
                        <div class="value blue js-tai-ky-khach-hang">-</div>
                    </div>
                    <div class="box-dfac">
                        <div class="label">ĐÃ TÁI KÝ HỢP ĐỒNG THEO NHÀ CUNG CẤP</div>
                        <div class="value blue js-tai-ky-ncc">-</div>
                    </div>
                </div>
                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">YÊU CẦU CHI</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac-1">
                    <div class="box-dfac">
                        <div class="label">PHIẾU ĐỀ XUẤT TÀI CHÍNH CHƯA XỬ LÝ</div>
                        <div class="value blue js-yeu-cau-chi-pending">-</div>
                    </div>
                </div>

                <div class="kpi-box_dfac blue_dfac" style="background-color: #BA925D;">
                    <div class="label">PHIẾU GIAO HÀNG</div>
                </div>
                <div class="thongke-box_dfac thongke-grid_dfac-1">
                    <div class="box-dfac">
                        <div class="label">ĐÃ THU TIỀN PHIẾU GIAO HÀNG</div>
                        <div class="value blue js-phieu-giao-da-thu">-</div>
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
    (function startRealtimeClock_dfac_hm() {
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