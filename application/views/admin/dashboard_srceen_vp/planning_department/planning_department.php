<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/planning_department/planning_department_css'); ?>
</head>

<body>
    <div class="app_pkh">
        <!-- HEADER -->
        <div class="header_pkh">
            <div class="logo_pkh">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_pkh">
                <span class="main">PHÒNG KẾ HOẠCH</span><br>
            </div>
            <div class="header-right_pkh">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_pkh" style="display:flex;gap:7px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_pkh">
                <div class="kpi-box_pkh blue_pkh">
                    <div class="label">Đơn đặt hàng</div>
                </div>
                <div class="thongke-box_pkh thongke-grid_pkh">
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label">TỔNG ĐƠN ĐÃ MỞ</div>
                        <div class="value blue js-total-orders-kh">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label">TỔNG ĐƠN ĐÃ DUYỆT</div>
                        <div class="value blue js-total-orders-kh-approved">-</div>
                    </div>
                    <div class="box-pkh">
                        <div style="display: flex;flex-direction: row-reverse;justify-content: space-around;align-items: center;align-content: flex-start;">
                            <div>
                                <div class="kpi-sum"><span class="kpi-sub">Đã duyệt</span><span class="pill js-orders-kh-approved" style="background-color: #01C532;color:white;">-</span></div>
                                <div class="kpi-sum" style="margin-top:-4px"><span class="kpi-sub">Chưa duyệt</span><span class="pill js-orders-kh-reject" style="background-color: #E56464;color:white;">-</span></div>
                            </div>
                            <div class="donut-wrap">
                                <div class="donut donut-order-kh" data-percent="0" id="donut-order">
                                    <svg viewBox="0 0 42 42" aria-hidden="true">
                                        <circle class="ring-bg_pkh" cx="21" cy="21" r="15.915"></circle>
                                        <circle class="ring-val_pkh" cx="21" cy="21" r="15.915"></circle>
                                    </svg>
                                    <div class="txt"><span class="js-donut-order-kh">0%</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-pkh">
                        <div style="display: flex;flex-direction: row-reverse;justify-content: space-around;align-items: center;align-content: flex-start;">
                            <div>
                                <div class="kpi-sum"><span class="kpi-sub">Đã hoàn thành</span><span class="pill js-orders-deli-success" style="background-color: #01C532;color:white;">-</span></div>
                                <div class="kpi-sum" style="margin-top:-4px"><span class="kpi-sub">Chưa hoàn thành</span><span class="pill js-orders-deli-no-success" style="background-color: #E56464;color:white;">-</span></div>
                            </div>
                            <div class="donut-wrap">
                                <div class="donut donut-orders-delivered-success" data-percent="0" id="donut-orders-success">
                                    <svg viewBox="0 0 42 42" aria-hidden="true">
                                        <circle class="ring-bg_pkh" cx="21" cy="21" r="15.915"></circle>
                                        <circle class="ring-val_pkh" cx="21" cy="21" r="15.915"></circle>
                                    </svg>
                                    <div class="txt"><span class="js-donut-orders-delivered-success">0%</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kpi-box_pkh blue_pkh">
                    <div class="label">Ghép size/ Dàn trang/ Ghi kẽm</div>
                </div>
                <div class="thongke-box_pkh thongke-grid_pkh-2">
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG SL DÀN TRANG CÓ ĐƠN HÀNG</div>
                        <div class="value-2 green js-total-dan-trang-has-order">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG SL DÀN TRANG CHƯA CÓ ĐƠN HÀNG</div>
                        <div class="value-2 red js-total-dan-trang-no-order">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG SL GHÉP SIZE CÓ ĐƠN HÀNG</div>
                        <div class="value-2 green js-total-ghép-size-has-order">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG SL GHÉP SIZE CHƯA CÓ ĐƠN HÀNG</div>
                        <div class="value-2 red js-total-ghép-size-no-order">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG SL GHI KẼM CÓ ĐƠN HÀNG</div>
                        <div class="value-2 green js-total-ghi-kem-has-order">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG SL GHI KẼM CHƯA CÓ ĐƠN HÀNG</div>
                        <div class="value-2 red js-total-ghi-kem-no-order">-</div>
                    </div>
                </div>
            </aside>

            <!-- TABLE -->
            <section class="table-wrapper_pkh">
                <div class="kpi-box_pkh blue_pkh">
                    <div class="label">Xuất kho</div>
                </div>
                <div class="box-pkh">
                    <div class="thongke-box_pkh thongke-grid_pkh" style="grid-template-rows:unset;">
                        <div class="box-pkh" style="padding: 25px;border-bottom: none;">
                            <div class="label">TỔNG NPL</div>
                            <div class="value blue js-total-npl">-</div>
                        </div>
                        <div class="box-pkh" style="border-bottom: none;">
                            <div style="display: flex;flex-direction: row-reverse;justify-content: space-around;align-items: center;align-content: flex-start;">
                                <div>
                                    <div class="kpi-sum"><span class="kpi-sub">Đã xuất kho</span><span class="pill js-npl-approved" style="background-color: #01C532;color:white;">-</span></div>
                                    <div class="kpi-sum" style="margin-top:-4px"><span class="kpi-sub">Chưa xuất kho</span><span class="pill js-npl-reject" style="background-color: #E56464;color:white;">-</span></div>
                                </div>
                                <div class="donut-wrap">
                                    <div class="donut donut-npl" data-percent="0" id="donut-npl">
                                        <svg viewBox="0 0 42 42" aria-hidden="true">
                                            <circle class="ring-bg_pkh" cx="21" cy="21" r="15.915"></circle>
                                            <circle class="ring-val_pkh" cx="21" cy="21" r="15.915"></circle>
                                        </svg>
                                        <div class="txt"><span class="js-donut-npl">0%</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-pkh">
                    <div class="thongke-box_pkh thongke-grid_pkh" style="grid-template-rows:unset;">
                        <div class="box-pkh" style="padding: 25px;border-bottom: none;">
                            <div class="label">TỔNG TP</div>
                            <div class="value blue js-total-tp">-</div>
                        </div>
                        <div class="box-pkh" style="border-bottom: none;">
                            <div style="display: flex;flex-direction: row-reverse;justify-content: space-around;align-items: center;align-content: flex-start;">
                                <div>
                                    <div class="kpi-sum"><span class="kpi-sub">Đã xuất kho</span><span class="pill js-tp-approved" style="background-color: #01C532;color:white;">-</span></div>
                                    <div class="kpi-sum" style="margin-top:-4px"><span class="kpi-sub">Chưa xuất kho</span><span class="pill js-tp-reject" style="background-color: #E56464;color:white;">-</span></div>
                                </div>
                                <div class="donut-wrap">
                                    <div class="donut donut-tp" data-percent="0" id="donut-tp">
                                        <svg viewBox="0 0 42 42" aria-hidden="true">
                                            <circle class="ring-bg_pkh" cx="21" cy="21" r="15.915"></circle>
                                            <circle class="ring-val_pkh" cx="21" cy="21" r="15.915"></circle>
                                        </svg>
                                        <div class="txt"><span class="js-donut-tp">0%</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kpi-box_pkh blue_pkh">
                    <div class="label">Lệnh sản xuất</div>
                </div>
                <div class="thongke-box_pkh thongke-grid_pkh-3">
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX ĐÃ MỞ</div>
                        <div class="value-2 green js-total-lsx-open">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX CHƯA MỞ</div>
                        <div class="value-2 red js-total-lsx-no-open">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX ĐÃ XUẤT KHUÔN BỂ</div>
                        <div class="value-2 green js-total-lsx-has-khuon-be">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX CHƯA XUẤT KHUÔN BỂ</div>
                        <div class="value-2 red js-total-lsx-no-khuon-be">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX ĐÃ ĐỦ NPL</div>
                        <div class="value-2 green js-total-lsx-du-npl">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX CHƯA ĐỦ NPL</div>
                        <div class="value-2 red js-total-lsx-no-du-npl">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX ĐÃ ĐỦ VTSX</div>
                        <div class="value-2 green js-total-lsx-du-vtsx">-</div>
                    </div>
                    <div class="box-pkh" style="padding: 25px;">
                        <div class="label-2">TỔNG LSX CHƯA ĐỦ VTSX</div>
                        <div class="value-2 red js-total-lsx-no-du-vtsx">-</div>
                    </div>
                </div>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
    function formatNumber(num, decimals = 2, decPoint = '.', thousandsSep = ',') {
        if (isNaN(num) || num === null) return '-';
        num = parseFloat(num);
        if (num == 0 || num === '' || num === '0') return '-';
        const fixed = num.toFixed(decimals);

        let [intPart, decPart] = fixed.split('.');
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

        return decPart ? intPart + decPoint + decPart : intPart;
    }

    function numVal(v) {
        if (v === null || v === undefined) return 0;
        if (typeof v === 'number' && isFinite(v)) return v;
        let s = String(v).trim();
        if (!s) return 0;

        // âm trong ngoặc () -> chuyển thành âm
        const isNegative = /^\(.*\)$/.test(s);
        s = s.replace(/[()]/g, '');

        // phát hiện % nếu có (không tự động chia 100 để giữ nguyên logic sử dụng)
        const hasPercent = s.indexOf('%') !== -1;
        s = s.replace(/%/g, '');

        // Xử lý dấu ngăn nghìn / decimal:
        // - nếu cả '.' và ',' xuất hiện -> giả sử '.' là ngăn nghìn, ',' là decimal (ví dụ "1.234,56")
        // - nếu chỉ có ',' -> coi ',' là decimal (ví dụ "1234,56")
        // - ngược lại bỏ dấu ',' (thousand) và giữ '.' làm decimal
        if (s.indexOf('.') !== -1 && s.indexOf(',') !== -1) {
            s = s.replace(/\./g, '').replace(/,/g, '.');
        } else if (s.indexOf(',') !== -1 && s.indexOf('.') === -1) {
            s = s.replace(/,/g, '.');
        } else {
            s = s.replace(/,/g, '');
        }

        // bỏ ký tự không phải số, -, .
        s = s.replace(/[^\d\.\-]+/g, '');

        let n = parseFloat(s);
        if (isNaN(n)) n = 0;
        if (isNegative) n = -n;

        // nếu trước đó có '%' và bạn muốn giá trị thực (chia 100) -> uncomment:
        // if (hasPercent) n = n / 100;

        return n;
    }
    // ====== TARGET ======

    // ====== STATE ======
    let rowsPKHTK = []; // dữ liệu gốc
    // ====== CẬP NHẬT KPI + DONUT (SỬA LỖI PHÉP GÁN) ======
    function updatePlanningStatsPKH_pkh(stats) {
        console.log(stats)
        let total = numVal(stats.total_approved) + numVal(stats.total_un_approved);
        $(".js-total-orders-kh").text(formatNumber(numVal(stats.total_approved) + numVal(stats.total_un_approved), 0));
        $(".js-total-orders-kh-approved").text(formatNumber(numVal(stats.total_approved), 0));
        $(".js-orders-kh-approved").text(formatNumber(numVal(stats.total_approved), 0));
        $(".js-orders-kh-reject").text(formatNumber(numVal(stats.total_un_approved), 0));

        const avg = total > 0 ? Number(((numVal(stats.total_approved) / total) * 100).toFixed(2)) : 0;
        const doneArc = Math.min(100, Math.max(0, avg));
        const todoArc = 100 - doneArc;
        const $circles = $(".donut-order-kh svg circle");
        $circles.eq(0).attr("stroke-dasharray", `${todoArc} ${doneArc}`); // nền
        $circles.eq(1).attr("stroke-dasharray", `${doneArc} ${todoArc}`).attr("stroke-dashoffset", -todoArc);
        $circles.eq(1).css("stroke-dasharray", `${doneArc} ${todoArc}`);

        $(".js-donut-order-kh").text(avg + "%");


        $(".js-orders-deli-success").text(formatNumber(numVal(stats.total_delivered), 0));
        $(".js-orders-deli-no-success").text(formatNumber(numVal(stats.total_not_delivered), 0));

        const avg_delivered = total > 0 ? Number(((numVal(stats.total_delivered) / total) * 100).toFixed(2)) : 0;
        const doneArc_delivered = Math.min(100, Math.max(0, avg_delivered));
        const todoArc_delivered = 100 - doneArc_delivered;
        const $circles_delivered = $(".donut-orders-delivered-success svg circle");
        $circles_delivered.eq(0).attr("stroke-dasharray", `${todoArc_delivered} ${doneArc_delivered}`); // nền
        $circles_delivered.eq(1).attr("stroke-dasharray", `${doneArc_delivered} ${todoArc_delivered}`).attr("stroke-dashoffset", -todoArc_delivered);
        $circles_delivered.eq(1).css("stroke-dasharray", `${doneArc_delivered} ${todoArc_delivered}`);

        $(".js-donut-orders-delivered-success").text(avg_delivered + "%");


        $(".js-total-dan-trang-has-order").text(formatNumber(numVal(stats.rows_dan_trang_in_active), 0));
        $(".js-total-dan-trang-no-order").text(formatNumber(numVal(stats.rows_dan_trang_in_inactive), 0));

        $(".js-total-ghép-size-has-order").text(formatNumber(numVal(stats.rows_ghep_size_active), 0));
        $(".js-total-ghép-size-no-order").text(formatNumber(numVal(stats.rows_ghep_size_inactive), 0));


        $(".js-total-ghi-kem-has-order").text(formatNumber(numVal(stats.export_total_approved), 0));
        $(".js-total-ghi-kem-no-order").text(formatNumber(numVal(stats.export_total_un_approved), 0));


        let total_npl = numVal(stats.transfer_nvl_total_dx) + numVal(stats.transfer_nvl_total_cx);
        $(".js-total-npl").text(formatNumber(numVal(stats.transfer_nvl_total_dx) + numVal(stats.transfer_nvl_total_cx), 0));
        $(".js-npl-approved").text(formatNumber(numVal(stats.transfer_nvl_total_dx), 0));
        $(".js-npl-reject").text(formatNumber(numVal(stats.transfer_nvl_total_cx), 0));

        const avg_npl = total_npl > 0 ? Number(((numVal(stats.transfer_nvl_total_dx) / total_npl) * 100).toFixed(2)) : 0;
        const doneArc_npl = Math.min(100, Math.max(0, avg_npl));
        const todoArc_npl = 100 - doneArc_npl;
        const $circles_npl = $(".donut-npl svg circle");
        $circles_npl.eq(0).attr("stroke-dasharray", `${todoArc_npl} ${doneArc_npl}`); // nền
        $circles_npl.eq(1).attr("stroke-dasharray", `${doneArc_npl} ${todoArc_npl}`).attr("stroke-dashoffset", -todoArc_npl);
        $circles_npl.eq(1).css("stroke-dasharray", `${doneArc_npl} ${todoArc_npl}`);


        $(".js-donut-npl").text(avg_npl + "%");


        let total_tp = numVal(stats.transfer_product_total_dx) + numVal(stats.transfer_product_total_cx);
        $(".js-total-tp").text(formatNumber(numVal(stats.transfer_product_total_dx) + numVal(stats.transfer_product_total_cx), 0));
        $(".js-tp-approved").text(formatNumber(numVal(stats.transfer_product_total_dx), 0));
        $(".js-tp-reject").text(formatNumber(numVal(stats.transfer_product_total_cx), 0));

        const avg_tp = total_tp > 0 ? Number(((numVal(stats.transfer_product_total_dx) / total_tp) * 100).toFixed(2)) : 0;
        const doneArc_tp = Math.min(100, Math.max(0, avg_tp));
        const todoArc_tp = 100 - doneArc_tp;
        const $circles_tp = $(".donut-tp svg circle");
        $circles_tp.eq(0).attr("stroke-dasharray", `${todoArc_tp} ${doneArc_tp}`); // nền
        $circles_tp.eq(1).attr("stroke-dasharray", `${doneArc_tp} ${todoArc_tp}`).attr("stroke-dashoffset", -todoArc_tp);
        $circles_tp.eq(1).css("stroke-dasharray", `${doneArc_tp} ${todoArc_tp}`);


        $(".js-donut-tp").text(avg_tp + "%");


        $(".js-total-lsx-open").text(formatNumber(numVal(stats.count_has_preventive), 0));
        $(".js-total-lsx-no-open").text(formatNumber(numVal(stats.count_no_preventive), 0));

        $(".js-total-lsx-has-khuon-be").text(formatNumber(numVal(stats.export_kb_total_approved), 0));
        $(".js-total-lsx-no-khuon-be").text(formatNumber(numVal(stats.export_kb_total_un_approved), 0));
    }

    // ====== LOAD LẦN ĐẦU ======
    function loadPlanningData_pkh() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatePlanningDepartment') ?>", res => {
            if (!res || !res.success) return;
            const stats = res.stats;

            updatePlanningStatsPKH_pkh(stats);
        });
    }

    // gọi ngay
    loadPlanningData_pkh();
    setInterval(loadPlanningData_pkh, 60000);


    // window.manuDash = {
    //     applyMarquee: () => {
    //         applyMarqueeManu();
    //     },
    //     play: (pages, onDone) => startAutoSwitchManu(pages, onDone),
    //     pause: () => {
    //         stopAutoSwitchManu(); // clear interval
    //         _isPlayingManu = false;
    //         _onDoneManu = null;
    //         _pageQuotaManu = Infinity;
    //         _pagesRanManu = 0;
    //     },
    //     resume: (pages, onDone) => startAutoSwitchManu(pages, onDone),
    //     nextPage: () => {
    //         const total = _getTotalPagesManu();
    //         currentPageManu = (currentPageManu + 1) % total;
    //         _showAndCountManu(currentPageManu);
    //     },
    //     getState: () => ({
    //         currentPageManu,
    //         totalPages: _getTotalPagesManu(),
    //         isPlaying: _isPlayingManu
    //     })
    // };
</script>

<script>
    // Đồng hồ realtime theo Asia/Ho_Chi_Minh
    (function startRealtimeClock_pkh_hm() {
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