<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Bộ Phận Sản Xuất</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen/manufacture/manufacture_css'); ?>
</head>

<body>
    <div class="app_manufacture">
        <!-- Header -->
        <div class="header_manufacture">
            <div class="logo_manufacture">
                <div class="logo-circle_manufacture">
                    <img style="width: 100px;height: 100px;margin-top: -30px;" src="<?= base_url('uploads/logo_dashboard_srceen.png') ?>">
                </div>
            </div>
            <div class="header-title_manufacture">BỘ PHẬN SẢN XUẤT</div>
            <div class="header-right_manufacture">
                <div class="header-right_manufacture">
                    <div id="clock-date"><?= $dateStr ?></div>
                    <div id="clock-time"><?= $timeStr ?></div>
                </div>
            </div>
        </div>

        <div class="container_manufacture">
            <!-- Sidebar -->
            <div class="sidebar_manufacture">
                <div class="card_manufacture">
                    <div class="row_manufacture"><span>Tổng lệnh sản xuất</span><span><?= $stats['totalOrders'] ?></span></div>
                    <div class="row_manufacture row-2_manufacture"><span>Tổng công đoạn</span><span><?= $stats['totalStages'] ?></span></div>
                    <div class="row_manufacture"><span>Đã hoàn thành</span><span><?= $stats['completed'] ?></span></div>
                    <div class="row_manufacture row-2_manufacture"><span>Chưa hoàn thành</span><span><?= $stats['uncompleted'] ?></span></div>
                    <div class="row_manufacture"><span>Quá hạn</span><span><?= $stats['overdue'] ?></span></div>
                    <div class="row_manufacture row-2_manufacture"><span>Tiến độ trung bình</span><span><?= $stats['avgPercent'] ?>%</span></div>
                </div>

                <div class="card_manufacture" style="flex:1;display:flex;flex-direction:column">
                    <div class="progress-title_manufacture">% Tiến độ</div>
                    <div class="progress-item_manufacture"><span>Hoàn thành</span>
                        <span class="badge_manufacture green_manufacture"><?= $progressCounts['done'] ?></span>
                    </div>
                    <div class="progress-item_manufacture"><span>Chưa hoàn thành</span>
                        <span class="badge_manufacture red_manufacture"><?= $progressCounts['todo'] ?></span>
                    </div>
                    <?php $avg = (int)$stats['avgPercent'];
                    $doneArc = $avg;
                    $todoArc = 100 - $doneArc; ?>
                    <div class="donut_manufacture" style="margin-top:auto">
                        <svg viewBox="0 0 42 42">
                            <circle cx="21" cy="21" r="15.915" fill="transparent"
                                stroke="#ef4444" stroke-width="3"
                                stroke-dasharray="<?= $todoArc ?> <?= $doneArc ?>" stroke-dashoffset="0" />
                            <circle cx="21" cy="21" r="15.915" fill="transparent"
                                stroke="#10b981" stroke-width="3"
                                stroke-dasharray="<?= $doneArc ?> <?= $todoArc ?>" stroke-dashoffset="-<?= $todoArc ?>" />
                        </svg>
                        <div class="donut-text_manufacture"><?= $avg ?>%</div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main_manufacture">
                <div class="table-header_manufacture">
                    <div>Lệnh sản xuất</div>
                    <div>Mã sản phẩm</div>
                    <div>Công đoạn</div>
                    <div class="yellow_manufacture">Số lượng SX</div>
                    <div class="green_manufacture">Hoàn thành</div>
                    <div class="red_manufacture">Chưa hoàn thành</div>
                    <div>Tiến độ công đoạn</div>
                </div>
                <div class="table-body_manufacture" id="table-body">
                    <?php foreach ($rows as $r): ?>
                        <div class="table-row_manufacture" data-id="<?= $r['order_code'] ?>">
                            <div><?= $r['order_code'] ?></div>
                            <div><?= $r['sku'] ?></div>
                            <div><?= $r['stage'] ?></div>
                            <div class="yellow_manufacture"><?= number_format($r['qty_plan']) ?></div>
                            <div class="green_manufacture"><?= number_format($r['qty_done']) ?></div>
                            <div class="red_manufacture"><?= number_format($r['qty_todo']) ?></div>
                            <div class="progress-bar_manufacture">
                                <div class="bar_manufacture">
                                    <div class="bar-fill_manufacture"
                                        style="width:<?= $r['percent'] ?>%; background:<?= $r['bar_color'] ?>"></div>
                                </div>
                                <span class="percent_manufacture"><?= $r['percent'] ?>%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
    const $body = $("#table-body");

    // State
    let allRows = []; // dữ liệu gốc
    let allPages = []; // cache phân trang
    let currentPage = 0; // index trang hiện tại
    let autoSwitch = null; // interval autoswitch
    let lastUpdatedId = null; // order_code vừa update để highlight sau render (chủ yếu dùng khi add/delete/resize)

    // ====== Tạo 1 dòng HTML ======
    function rowTpl(r) {
        return `<div class="table-row_manufacture" data-id="${r.order_code}">
      <div class="marquee_manufacture"><span>${r.order_code}</span></div>
      <div class="marquee_manufacture"><span>${r.sku}</span></div>
      <div class="marquee_manufacture"><span>${r.stage}</span></div>
      <div class="yellow_manufacture">${Number(r.qty_plan).toLocaleString()}</div>
      <div class="green_manufacture">${Number(r.qty_done).toLocaleString()}</div>
      <div class="red_manufacture">${Number(r.qty_todo).toLocaleString()}</div>
      <div class="progress-bar_manufacture">
        <div class="bar_manufacture"><div class="bar-fill_manufacture" style="width:${r.percent}%; background:${r.bar_color}"></div></div>
        <span class="percent_manufacture">${r.percent}%</span>
      </div>
    </div>`;
    }

    // ====== Tính số dòng/trang theo chiều cao hiện tại ======
    function getMaxRowsPerPage() {
        const bodyHeight = $body.height();
        const rowHeightProbe = $body.find(".table-row_manufacture").first().outerHeight() || 60;
        return Math.max(1, Math.floor(bodyHeight / rowHeightProbe));
    }

    // ====== Chia trang từ allRows (tùy chọn reset page) ======
    function paginateRows(rows, resetPage = true) {
        const maxRows = getMaxRowsPerPage();
        allPages = [];
        for (let i = 0; i < rows.length; i += maxRows) {
            allPages.push(rows.slice(i, i + maxRows));
        }
        if (resetPage) currentPage = 0;
    }

    // ====== Vẽ 1 trang từ allRows ======
    function showPage(pageIndex) {
        const maxRows = getMaxRowsPerPage();
        const totalPages = Math.max(1, Math.ceil(allRows.length / maxRows));
        currentPage = Math.min(pageIndex, totalPages - 1);

        const start = currentPage * maxRows;
        theEnd = start + maxRows;
        const end = theEnd;
        const rows = allRows.slice(start, end);

        $body.addClass("hidden_manufacture");
        setTimeout(() => {
            $body.empty();
            rows.forEach(r => {
                const $row = $(rowTpl(r));
                if (lastUpdatedId && r.order_code === lastUpdatedId) {
                    requestAnimationFrame(() => {
                        $row.addClass("highlight_manufacture");
                        $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight_manufacture"));
                    });
                }
                $body.append($row);
            });
            $body.removeClass("hidden_manufacture");
            applyMarquee();
            lastUpdatedId = null;
        }, 200);
    }

    // ====== Auto switch trang ======
    function startAutoSwitch() {
        if (autoSwitch) clearInterval(autoSwitch);
        paginateRows(allRows, false);

        if (allPages.length > 1) {
            autoSwitch = setInterval(() => {
                currentPage = (currentPage + 1) % allPages.length;
                showPage(currentPage);
            }, 10000);
        }
    }

    // ====== Marquee cho ô bị tràn chữ ======
    function applyMarquee() {
        $("#table-body .table-row_manufacture > div:not(.progress-bar_manufacture)").each(function() {
            const $el = $(this);
            // reset trước
            if ($el.hasClass("marquee_manufacture")) {
                const text = $el.find("span").text();
                $el.removeClass("marquee_manufacture").text(text);
            }
            // chỉ áp dụng nếu chữ dài hơn ô
            if (this.scrollWidth > this.clientWidth) {
                const text = $el.text();
                $el.addClass("marquee_manufacture").html(`<span>${text}</span>`);
            }
        });
    }

    // ====== Cập nhật một dòng đang hiển thị (không re-render trang), kèm highlight ======
    function updateRow(row) {
        // đồng bộ vào allRows theo thứ tự hiện có (không sắp xếp)
        const idx = allRows.findIndex(r => r.order_code === row.order_code);
        if (idx >= 0) {
            allRows[idx] = row;
        } else {
            allRows.push(row);
        }

        const $row = $body.find(`.table-row_manufacture[data-id="${row.order_code}"]`);
        if ($row.length) {
            const $cells = $row.children('div');
            const $order = $cells.eq(0);
            const $sku = $cells.eq(1);
            const $stage = $cells.eq(2);

            const setCellText = ($el, text) => {
                if ($el.hasClass('marquee_manufacture')) {
                    $el.removeClass('marquee_manufacture').empty().text(text);
                } else {
                    $el.text(text);
                }
            };

            setCellText($order, row.order_code);
            setCellText($sku, row.sku);
            setCellText($stage, row.stage);

            $row.find(".yellow_manufacture").text(Number(row.qty_plan).toLocaleString());
            $row.find(".green_manufacture").text(Number(row.qty_done).toLocaleString());
            $row.find(".red_manufacture").text(Number(row.qty_todo).toLocaleString());

            $row.find(".bar-fill_manufacture").css({
                width: row.percent + "%",
                background: row.bar_color
            });
            $row.find(".progress-bar_manufacture .percent_manufacture").text(row.percent + "%");

            // re-apply marquee cho 3 ô đầu nếu tràn
            [$order, $sku, $stage].forEach($el => {
                if ($el[0].scrollWidth > $el[0].clientWidth) {
                    const text = $el.text();
                    $el.addClass("marquee_manufacture").html(`<span>${text}</span>`);
                }
            });

            // highlight
            $row.removeClass("highlight_manufacture");
            void $row[0].offsetWidth; // ép reflow để animation chạy lại
            $row.addClass("highlight_manufacture");
            $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight_manufacture"));
        }
    }

    // ====== Load lần đầu từ server ======
    function loadProgress() {
        $.getJSON("<?= site_url('admin/dashboard_srceen/updateProgress') ?>", res => {
            if (!res || !res.success) return;

            allRows = Array.isArray(res.rows) ? res.rows.slice() : [];
            // KHÔNG sắp xếp — giữ nguyên thứ tự backend đưa lên

            paginateRows(allRows, true);
            showPage(0);
            startAutoSwitch();
            updateStats(allRows);
        });
    }

    // ====== Auth + Socket ======
    async function loginSocket() {
        try {
            const {
                data,
                status
            } = await $.ajax({
                type: 'POST',
                url: '<?= admin_url('socket_controler/login_socket?csrf_protection=true') ?>',
                data: {
                    user_id: '<?= get_staff_user_id() ?>',
                    user_name: '<?= get_staff_full_name() ?>',
                    db_name: '<?= $dbname ?>'
                },
                dataType: 'json'
            });
            if (status) {
                localStorage.setItem('tokenSocket', data);
                return data;
            }
            throw new Error('Login socket không thành công');
        } catch (err) {
            console.error('Lỗi loginSocket:', err);
            throw err;
        }
    }

    async function connectSocket() {
        try {
            let token = localStorage.getItem('tokenSocket');
            if (!token || token === 'undefined' || token === 'null') {
                token = await loginSocket();
            }
            if (!token) throw new Error('Không có token');

            return new Promise((resolve, reject) => {
                const socket = io('https://socketfoso.fmrp.vn', {
                    extraHeaders: {
                        auth: token
                    }
                });

                socket.on('connect', () => {
                    console.log('✅ Socket connected:', socket.id);
                    socket.emit('connectedData', {
                        user_id: '<?= get_staff_user_id() ?>',
                        user_name: '<?= get_staff_full_name() ?>',
                        db_name: '<?= $dbname ?>'
                    });
                    resolve(socket);
                });

                socket.on('connect_error', err => {
                    console.error('❌ Socket connect_error:', err);
                    reject(err);
                });
            });
        } catch (err) {
            console.error('Lỗi connectSocket:', err);
            throw err;
        }
    }

    async function getSocket() {
        if (window.socket && window.socket.connected) return window.socket;
        window.socket = await connectSocket();
        return window.socket;
    }

    // ====== Lắng nghe socket và xử lý payload ======
    (async () => {
        loadProgress();

        const socket = await getSocket();

        socket.on('loadProgress', (payload) => {
            // chấp cả 2 kiểu emit: emit(data) hoặc emit({ data })
            const data = payload && payload.data !== undefined ? payload.data : payload;
            if (!data) return;

            switch (data.action) {
                case 'add': {
                    const newRow = data.newRow;
                    if (newRow) {
                        // 1) update mảng gốc (không sắp xếp)
                        allRows.push(newRow);

                        // 2) nếu trang hiện tại còn chỗ trống -> append DOM tại chỗ, không re-render
                        const maxRows = getMaxRowsPerPage();
                        const visibleCount = $body.find(".table-row_manufacture").length;
                        const startIndex = currentPage * maxRows;
                        const endIndex = startIndex + visibleCount; // index cuối (exclusive) đang hiển thị

                        // nếu newRow nằm trong range hiển thị và còn chỗ -> append
                        if (visibleCount < maxRows && allRows.indexOf(newRow) < startIndex + maxRows) {
                            const $row = $(rowTpl(newRow));
                            $body.append($row);
                            applyMarquee();

                            // highlight dòng mới
                            $row.addClass("highlight_manufacture");
                            $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight_manufacture"));
                        } else {
                            // nếu không hiển thị ở trang hiện tại -> chỉ cập nhật paginate
                            paginateRows(allRows, false);
                        }
                    }
                    updateStats(allRows);
                    startAutoSwitch(); // chỉ add/delete mới cần
                    break;
                }

                case 'update': {
                    const r = data.updatedRow;
                    if (!r) break;

                    // 1) cập nhật vào allRows (không sắp xếp)
                    const i = allRows.findIndex(x => x.order_code === r.order_code);
                    if (i >= 0) allRows[i] = r;
                    else allRows.push(r);

                    // 2) cập nhật DOM nếu đang hiển thị (KHÔNG re-render trang)
                    updateRow(r);

                    // 3) nếu backend báo removed (đạt 100% và xóa), ta loại khỏi mảng & re-render nhẹ trang hiện tại
                    if (data.removed === true) {
                        allRows = allRows.filter(x => x.order_code !== r.order_code);
                        // vẽ lại trang hiện tại để lấp chỗ trống
                        lastUpdatedId = null;
                        paginateRows(allRows, false);
                        showPage(currentPage);
                        startAutoSwitch(); // cấu trúc trang thay đổi
                    }

                    updateStats(allRows);
                    break;
                }

                case 'delete': {
                    if (!data.deleted_id) break;

                    // 1) xóa khỏi allRows
                    allRows = allRows.filter(r => r.order_code !== data.deleted_id);

                    // 2) nếu dòng đang hiển thị -> xóa DOM nhẹ nhàng, rồi vẽ lại trang để lấp chỗ
                    const $row = $body.find(`.table-row_manufacture[data-id="${data.deleted_id}"]`);
                    if ($row.length) {
                        $row.addClass("fade-out_manufacture");
                        setTimeout(() => {
                            paginateRows(allRows, false);
                            showPage(currentPage);
                        }, 250);
                    } else {
                        paginateRows(allRows, false);
                    }

                    updateStats(allRows);
                    startAutoSwitch(); // cấu trúc trang thay đổi
                    break;
                }
            }
        });

        // Re-paginate khi thay đổi kích thước cửa sổ (đổi số dòng/trang)
        $(window).on('resize', () => {
            paginateRows(allRows, false);
            showPage(currentPage);
            startAutoSwitch();
        });
    })();

    // ====== Cập nhật số liệu thống kê + donut ======
    function updateStats(rows) {
        const total = rows.length;
        const completed = rows.filter(r => Number(r.percent) >= 100).length;
        const uncompleted = total - completed;
        const avg = total > 0 ?
            Math.round(rows.reduce((s, r) => s + Number(r.percent || 0), 0) / total) :
            0;

        // sidebar
        $(".card_manufacture .row_manufacture:contains('Tổng lệnh sản xuất') span:last").text(total);
        $(".card_manufacture .row-2_manufacture:contains('Tổng công đoạn') span:last").text(total);
        $(".card_manufacture .row_manufacture:contains('Đã hoàn thành') span:last").text(completed);
        $(".card_manufacture .row-2_manufacture:contains('Chưa hoàn thành') span:last").text(uncompleted);
        $(".card_manufacture .row_manufacture:contains('Tiến độ trung bình') span:last").text(avg + "%");

        $(".progress-item_manufacture:contains('Hoàn thành') .badge_manufacture.green_manufacture").text(completed);
        $(".progress-item_manufacture:contains('Chưa hoàn thành') .badge_manufacture.red_manufacture").text(uncompleted);

        // donut
        const doneArc = Math.min(100, Math.max(0, avg));
        const todoArc = 100 - doneArc;

        const $circles = $(".donut_manufacture svg circle");
        $circles.eq(0).attr("stroke-dasharray", `${todoArc} ${doneArc}`); // đỏ (todo)
        $circles.eq(1).attr("stroke-dasharray", `${doneArc} ${todoArc}`) // xanh (done)
            .attr("stroke-dashoffset", -todoArc);
        $(".donut-text_manufacture").text(avg + "%");
    }
</script>

<script>
    // Đồng hồ realtime theo Asia/Ho_Chi_Minh (ID giữ nguyên)
    (function startRealtimeClock() {
        const elDate = document.getElementById('clock-date');
        const elTime = document.getElementById('clock-time');
        if (!elDate || !elTime) return;

        const tz = 'Asia/Ho_Chi_Minh';

        const fmtDate = new Intl.DateTimeFormat('vi-VN', {
            timeZone: tz,
            weekday: 'long',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        const fmtTime = new Intl.DateTimeFormat('en-US', {
            timeZone: tz,
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        function tick() {
            const now = new Date();
            elDate.textContent = fmtDate.format(now);
            elTime.textContent = fmtTime.format(now);
        }

        tick();
        const ms = 1000 - (Date.now() % 1000);
        setTimeout(() => {
            tick();
            setInterval(tick, 1000);
        }, ms);
    })();
</script>

</html>