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
            <section class="table-wrapper_pkh">
                <div class="head">
                    <h2 class="h-title_pkh">Kế hoạch đơn hàng</h2>
                    <div class="legend_pkh">
                        <span class="text-status"><i class="dot_pkh green_pkh"></i><span>Đã duyệt/ Đã mở</span></span>
                        <span class="text-status"><i class="dot_pkh red_pkh"></i><span>Quá hạn</span></span>
                        <span class="text-status"><i class="dot_pkh yellow_pkh"></i><span>Chờ duyệt/ Chờ mở</span></span>
                    </div>
                </div>
                <table class="pkh">
                    <thead>
                        <tr>
                            <th>Lệnh sản xuất</th>
                            <th>Mã sản phẩm</th>
                            <th>Đơn hàng</th>
                            <th>Trạng thái duyệt </th>
                            <th>Trạng thái mở lệnh</th>
                            <th>NVL & VTSX</th>
                            <th>Khuôn bế theo LSX</th>
                            <th>Ghép size</th>
                            <th>Dàn trang</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_pkh" id="table-body-pkh">
                        <!-- <tr>
                            <td>2025/09/29</td>
                            <td>Đề Xuất_Tra Soát_Đánh Giá_Điều Độ</td>
                            <td><span class="dot green"></span></td>
                            <td>P07_QL_008</td>
                            <td>
                                <img src="<?= base_url('uploads/user.png') ?>" class="avatar_pkh"> Trần An
                            </td>
                            <td><strong>100.000 đ</strong></td>
                            <td class="td-progress_pkh">
                                <div class="timeline_pkh">
                                    <div class="step_pkh done_pkh">
                                        <div class="dot_pkh"></div>
                                        <div class="content_pkh">
                                            <div class="title_pkh">Duyệt đề xuất</div>
                                            <div class="user_pkh">
                                                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" class="avatar-sm_pkh">
                                                Nguyễn V.Anh
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step_pkh pending_pkh">
                                        <div class="dot_pkh"></div>
                                        <div class="content_pkh">
                                            <div class="title_pkh">Duyệt thực thi</div>
                                            <div class="user_pkh">
                                                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" class="avatar-sm_pkh">
                                                Trần V.Hậu
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
    // ====== TARGET ======
    const $tbodyPKH = $("#table-body-kd-PKH");

    // ====== STATE ======
    let rowsPKH = []; // dữ liệu gốc
    let currentPagePKH = 0; // index trang
    let autoSwitchPKH = null;

    // ====== TEMPLATE 1 DÒNG (7 cột) ======
    function rowTplPKH(r) {
        const day = r.date || r.created_at || r.ngay || "";
        const type = r.type || r.request_type || r.category || "";
        const statusText = ((r.status || "").toString().toLowerCase() == 'approved' ? ((Number(r.order_id || 0) > 0 ? 2 :
                1) || 1) :
            1) || 1;
        const statusClass = r.status_color || STATUS_CLASS[statusText] || "";
        const job = r.job_code || r.reference_no || r.code || "";
        const clients = r.clients || r.company || "";
        const proposerName = r.proposer_name || r.proposer || r.created_by || "";
        const avatar = r.avatar_url || '<?= base_url('uploads/user.png') ?>';
        const image_employee = r.image_employee;
        const amountNum = Number(r.amount ?? r.total_amount ?? r.money ?? 0);
        const amount = isFinite(amountNum) ? amountNum.toLocaleString('vi-VN') + " ₫" : "";

        // % tiến độ: ưu tiên r.progress, fallback r.percent
        const percent = Math.max(0, Math.min(100, parseInt(r.progress ?? r.percent ?? 0, 10) || 0));
        const pColor = r.progress_color || (percent >= 100 ? "#10b981" : percent >= 50 ? "#f59e0b" : "#ef4444");

        return `
            <tr data-id="${job}">
                <td title="${job}">${job}</td>
                <td title="${clients}">${clients}</td>
                <td title="${proposerName}" class="image">
                ${image_employee}
                </td>
                <td><span class="dot ${statusClass}"></span></td>
            </tr>
        `;
    }

    // ====== TÍNH SỐ DÒNG/TRANG ======
    function getMaxRowsPerPagePKH() {
        const bodyH = $tbodyPKH.parent().height() || 400; // tự co theo layout
        // Tạo 1 probe nếu chưa có dòng để ước lượng chiều cao
        let rowH = $tbodyPKH.find("tr").first().outerHeight();

        if (!rowH || rowH < 10) rowH = 44; // default ~44px
        console.log($tbodyPKH.parent().height());

        return Math.max(1, Math.floor(bodyH / rowH));
    }

    // ====== VẼ 1 TRANG ======
    function showPagePKH(pageIndex = 0) {
        const maxRows = getMaxRowsPerPagePKH();
        const totalPages = Math.max(1, Math.ceil(rowsPKH.length / maxRows));
        currentPagePKH = Math.min(pageIndex, totalPages - 1);

        const start = currentPagePKH * maxRows;
        const end = start + maxRows;

        const slice = rowsPKH.slice(start, end);
        $tbodyPKH.addClass("hidden_pkh");
        setTimeout(() => {
            $tbodyPKH.empty();
            slice.forEach(r => $tbodyPKH.append(rowTplPKH(r)));
            $tbodyPKH.removeClass("hidden_pkh");
        }, 150);
    }

    // ====== AUTO SWITCH ======
    function startAutoSwitchPKH() {
        if (autoSwitchPKH) clearInterval(autoSwitchPKH);
        const maxRows = getMaxRowsPerPagePKH();
        const totalPages = Math.max(1, Math.ceil(rowsPKH.length / maxRows));
        if (totalPages > 1) {
            autoSwitchPKH = setInterval(() => {
                currentPagePKH = (currentPagePKH + 1) % totalPages;
                showPagePKH(currentPagePKH);
            }, 10000);
        }
    }

    // ====== CẬP NHẬT 1 DÒNG ĐANG HIỂN THỊ (NHẸ, KHÔNG RE-RENDER CẢ TRANG) ======
    function updateRowPKH(row) {
        // sync vào rowsPKH(giữ nguyên thứ tự)
        const key = row.job_code || row.reference_no || row.code;
        if (!key) return;

        const idx = rowsPKH.findIndex(x =>
            (x.job_code || x.reference_no || x.code) === key
        );
        if (idx >= 0) rowsPKH[idx] = row;
        else rowsPKH.push(row);

        // cập nhật DOM nếu đang hiển thị
        const $tr = $tbodyPKH.find(`tr[data-id="${key}"]`);
        if (!$tr.length) return;

        // rebuild cells nhanh cho gọn
        const html = $(rowTplPKH(row)).html();
        $tr.html(html);

        // highlight nhẹ
        $tr.addClass("highlight_pkh");
        $tr.one("animationend webkitAnimationEnd oAnimationEnd", () => $tr.removeClass("highlight_pkh"));
    }

    // ====== CẬP NHẬT KPI + DONUT (SỬA LỖI PHÉP GÁN) ======
   
    // ====== LOAD LẦN ĐẦU ======
    function loadPlanningData_pkh() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateBusinessDepartment') ?>", res => {
            if (!res || !res.success) return;
            const data = Array.isArray(res.quotes) ? res.quotes : [];

            rowsPKH = data.slice(); // giữ thứ tự backend

            showPagePKH(0);
            startAutoSwitchPKH();
        });
    }

    // gọi ngay
    // loadPlanningData_pkh();

    // ====== RESIZE → REPAGINATE NHẸ ======
    $(window).on("resize", () => {
        showPagePKH(currentPagePKH);
        startAutoSwitchPKH();
    });
</script>
<script>
    // Đồng hồ realtime theo Asia/Ho_Chi_Minh
    (function startRealtimeClock_pkh() {
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