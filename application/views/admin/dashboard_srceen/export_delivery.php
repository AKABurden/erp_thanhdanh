<div class="app">
    <?php $this->load->view('admin/dashboard_srceen/export_delivery/export_delivery_css'); ?>

    <!-- Header -->
    <div class="header">
        <div class="logo">
            <div class="logo-circle"><img style="width: 100px;height: 100px;margin-top: -30px;" src="<?= base_url('uploads/logo_dashboard_srceen.png') ?>"></div>
        </div>
        <div class="header-title">XUẤT KHO GIAO HÀNG</div>
        <div class="header-right">
            <div class="header-right">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="card" style="min-height: 360px;">
                <div class="row"><span>Tổng đơn giao</span><span></span></div>
                <div class="row row-2"><span>Đã xuất</span><span></span></div>
                <div class="row"><span>Còn lại</span><span></span></div>
                <div class="row row-2"><span>Trễ hẹn</span><span></span></div>
            </div>

            <div class="card" style="flex:1;display:flex;flex-direction:column">
                <div class="progress-title">% Tiến độ</div>
                <div class="progress-item"><span>Hoàn thành</span>
                    <span class="badge green"></span>
                </div>
                <div class="progress-item"><span>Chưa hoàn thành</span>
                    <span class="badge red"></span>
                </div>
                <?php $avg = (int)$stats['avgPercent'];
                $doneArc = $avg;
                $todoArc = 100 - $doneArc; ?>
                <div class="donut">
                    <svg viewBox="0 0 42 42">
                        <circle cx="21" cy="21" r="15.915" fill="transparent"
                            stroke="#ef4444" stroke-width="3"
                            stroke-dasharray="<?= $todoArc ?> <?= $doneArc ?>" stroke-dashoffset="0" />
                        <circle cx="21" cy="21" r="15.915" fill="transparent"
                            stroke="#10b981" stroke-width="3"
                            stroke-dasharray="<?= $doneArc ?> <?= $todoArc ?>" stroke-dashoffset="-<?= $todoArc ?>" />
                    </svg>
                    <div class="donut-text"><?= $avg ?>%</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main">
            <div class="table-header">
                <div>Giờ hẹn xuất</div>
                <div>Đơn hàng (DDH-)</div>
                <div>Phiếu giao (PGH-)</div>
                <div>Mã KH</div>
                <div>Mã sản phẩm</div>
                <div class="yellow">Số lượng</div>
                <div>Duyệt kho</div>
                <div>Người phụ trách</div>
            </div>
            <div class="table-body" id="table-body">
                <?php foreach ($rows as $r): ?>
                    <div class="table-row" data-id="<?= $r['id'] ?>">
                        <div><?= $r['code_orders'] ?></div>
                        <div><?= $r['reference_no'] ?></div>
                        <div><?= $r['company'] ?></div>
                        <div><?= $r['item_code'] ?></div>
                        <div class="yellow"><?= number_format($r['quantity']) ?></div>
                        <div><?= number_format($r['warehouseman_id']) ?></div>
                        <div class=""><?= ($r['fullname_employee']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const $body = $("#wrapDelivery #table-body");
        let allRows = [],
            allPages = [],
            currentPage = 0,
            autoSwitch = null,
            lastUpdatedId = null;

        let _pageQuota = Infinity,
            _pagesRan = 0,
            _onDone = null,
            _isPlaying = false;

        function _getTotalPages() {
            const maxRows = getMaxRowsPerPage();
            return Math.max(1, Math.ceil(allRows.length / maxRows));
        }
        // ====== Tính số dòng/trang theo chiều cao hiện tại ======
        function getMaxRowsPerPage() {
            const bodyHeight = $body.height();
            const rowHeightProbe = $body.find(".table-row").first().outerHeight() || 60;
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
        // ====== Tạo 1 dòng HTML ======
        function rowTpl(r) {
            var code_orders = (r.code_orders || '').replace(/^DDH-/, '');
            var reference_no = (r.reference_no || '').replace(/^PGH-/, '');
            return `<div class="table-row" data-id="${r.id}">
            <div class="datetime"><span>${r.date}</span></div>
            <div><span>${code_orders}</span></div>
            <div><span>${reference_no}</span></div>
            <div><span>${r.zcode}</span></div>
            <div class="col-item-code"><span>${r.item_code}</span></div> <!-- CHỈ CỘT NÀY -->
            <div class="yellow" ><span style="font-size:35px;">${Number(r.quantity).toLocaleString()}</span><span style="font-size:20px;color: #ffff;">/ ${r.unit_name}</span></div>
            <div class="${(r.warehouseman_id > 0 ? 'text_delivery' : 'text_no')}"><span>${(r.warehouseman_id > 0 ? 'Đã duyệt' : 'Chưa duyệt')}</span></div>
            <div class="image" style="text-align:center;"><span>${r.image_employee}</span></div>
        </div>`;
        }

        // ====== Tính số dòng/trang theo chiều cao hiện tại ======
        function getMaxRowsPerPage() {
            const bodyHeight = $body.height();
            const rowHeightProbe = $body.find(".table-row").first().outerHeight() || 60;
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


        function showPage(pageIndex) {
            const maxRows = getMaxRowsPerPage();

            // chỉ tính những dòng chưa duyệt để hiển thị
            const visibleRows = allRows;

            const totalPages = Math.max(1, Math.ceil(visibleRows.length / maxRows));
            currentPage = Math.min(pageIndex, totalPages - 1);

            const start = currentPage * maxRows;
            const end = start + maxRows;
            const rows = visibleRows.slice(start, end);

            $body.addClass("hidden");
            setTimeout(() => {
                $body.empty();
                rows.forEach(r => {
                    const $row = $(rowTpl(r));

                    // Kiểm tra trễ hẹn: nếu r.date < thời gian hiện tại và chưa duyệt kho
                    let isOverdue = false;
                    if (r.date) {
                        const [datePart, timePart] = r.date.split(' ');
                        if (datePart && timePart) {
                            const [day, month, year] = datePart.split('/').map(Number);
                            const [hour, minute, second] = timePart.split(':').map(Number);
                            const deliveryDate = new Date(year, month - 1, day, hour, minute, second);
                            const now = new Date();
                            // Check if overdue more than 3 days and not yet approved
                            const diffMs = now - deliveryDate;
                            const diffDays = diffMs / (1000 * 60 * 60 * 24);
                            if (diffDays > 3 && Number(r.warehouseman_id) === 0) {
                                isOverdue = true;
                            }
                        }
                    }
                    if (isOverdue) {
                        $row.css('color', '#ff7e7e');
                    }

                    if (lastUpdatedId && r.id === lastUpdatedId) {
                        requestAnimationFrame(() => {
                            $row.addClass("highlight");
                            $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight"));
                        });
                    }
                    $body.append($row);
                });
                $body.removeClass("hidden");
                applyMarquee();
                lastUpdatedId = null;
            }, 200);
        }


        // ====== Marquee cho ô bị tràn chữ ======
        function applyMarquee() {
            $("#table-body .table-row > .col-item-code").each(function() {
                const $el = $(this);
                if ($el.hasClass("marquee")) {
                    const text = $el.find("span").text();
                    $el.removeClass("marquee").text(text);
                }
                if (this.scrollWidth > this.clientWidth) {
                    const text = $el.text();
                    $el.addClass("marquee").html(`<span>${text}</span>`);
                }
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
                    const socket = io('<?= get_option('link_connect_socket') ?>', {
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

        // ====== Lắng nghe socket và xử lý payload (KHÔNG sắp xếp, KHÔNG re-render khi update) ======
        (async () => {
            loadProgress();

            const socket = await getSocket();
            socket.on('update_dashboard', (payload) => {
                location.reload();
            });
            socket.on('ExportDeliveryloadProgress', (payload) => {
                // chấp cả 2 kiểu emit: emit(data) hoặc emit({ data })
                const data = payload && payload.data !== undefined ? payload.data : payload;
                console.log(data)

                if (!data) return;

                switch (data.action) {
                    case 'add': {
                        const newRow = data.newRow;
                        if (newRow) {
                            // Gọi API thông báo thêm mới
                            $.get("<?= site_url('dashboard_srceen/UpdateDateDelivery') ?>").fail(err => {
                                console.log('API notify_add failed:', err);
                            });

                            // 1) update mảng gốc (không sắp xếp)
                            allRows.push(newRow);

                            // 2) nếu trang hiện tại còn chỗ trống -> append DOM tại chỗ, không re-render
                            const maxRows = getMaxRowsPerPage();
                            const visibleCount = $body.find(".table-row").length;
                            const startIndex = currentPage * maxRows;
                            const endIndex = startIndex + visibleCount; // index cuối cùng đang hiển thị (exclusive)

                            // nếu newRow nằm trong range sẽ hiển thị và còn chỗ -> append
                            if (visibleCount < maxRows && allRows.indexOf(newRow) < startIndex + maxRows) {
                                const $row = $(rowTpl(newRow));
                                $body.append($row);
                                applyMarquee();

                                // highlight dòng mới
                                $row.addClass("highlight");
                                $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight"));
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

                        // Gọi API thông báo cập nhật
                        $.get("<?= site_url('dashboard_srceen/UpdateDateDelivery') ?>").fail(err => {
                            console.log('API notify_update failed:', err);
                        });

                        // 1) cập nhật vào allRows (không sắp xếp)
                        const i = allRows.findIndex(x => x.id === r.id);
                        if (i >= 0) allRows[i] = r;
                        else allRows.push(r);

                        // 2) cập nhật DOM nếu đang hiển thị (KHÔNG re-render trang)
                        updateRow(r);

                        // 3) nếu backend báo removed (đạt 100% và xóa), ta loại khỏi mảng & re-render NHẸ trang hiện tại

                        lastUpdatedId = null; // lúc này không cần highlight vì vừa xóa
                        paginateRows(allRows, false);
                        showPage(currentPage);
                        startAutoSwitch(); // cấu trúc trang thay đổi
                        // }

                        updateStats(allRows);
                        break;
                    }
                    case 'delete': {
                        if (!data.deleted_id) break;

                        // 1) xóa khỏi allRows
                        allRows = allRows.filter(r => r.id !== data.deleted_id);

                        // 2) nếu dòng đang hiển thị -> xóa DOM nhẹ nhàng, rồi vẽ lại trang để lấp chỗ
                        const $row = $body.find(`.table-row[data-id="${data.deleted_id}"]`);
                        if ($row.length) {
                            $row.addClass("fade-out");
                            setTimeout(() => {
                                // re-render để fill chỗ trống bằng item tiếp theo trong mảng (nhưng không “nhảy” cả bảng)
                                paginateRows(allRows, false);
                                showPage(currentPage);
                            }, 250);
                        } else {
                            // nếu không ở trang hiện tại, chỉ cần cập nhật paginate
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
        // function updateStats(rows) {
        //     const total = rows.length;
        //     const completed = rows.filter(r => Number(r.warehouseman_id) > 0).length;
        //     const uncompleted = total - completed;

        //     // Tính số chuyến trễ hẹn (so sánh với thời gian hiện tại)
        //     const currentDateTime = new Date();
        //     const overdue = rows.filter(r => {
        //         if (!r.date) return false;
        //         // Chuyển đổi từ định dạng "14/01/2025 13:30:40" sang đối tượng Date
        //         const [datePart, timePart] = r.date.split(' ');
        //         if (!datePart || !timePart) return false;
        //         const [day, month, year] = datePart.split('/').map(Number);
        //         const [hour, minute, second] = timePart.split(':').map(Number);
        //         const deliveryDate = new Date(year, month - 1, day, hour, minute, second);
        //         return deliveryDate < currentDateTime && Number(r.warehouseman_id) === 0; // chưa xuất và quá hẹn
        //     }).length;


        //     // sidebar
        //     $(".card .row:contains('Tổng đơn giao') span:last").text(total);
        //     $(".card .row-2:contains('Đã xuất') span:last").text(completed);
        //     $(".card .row:contains('Còn lại') span:last").text(uncompleted);
        //     $(".card .row-2:contains('Trễ hẹn') span:last").text(overdue);

        //     $(".progress-item:contains('Hoàn thành') .badge.green").text(completed);
        //     $(".progress-item:contains('Chưa hoàn thành') .badge.red").text(uncompleted);

        //     const avg = total > 0 ? Math.round((Number(completed) / Number(total)) * 100 * 100) / 100 : 0;
        //     // donut
        //     const doneArc = Math.min(100, Math.max(0, avg));
        //     const todoArc = 100 - doneArc;

        //     const $circles = $(".donut svg circle");
        //     $circles.eq(0).attr("stroke-dasharray", `${todoArc} ${doneArc}`); // đỏ (todo)
        //     $circles.eq(1).attr("stroke-dasharray", `${doneArc} ${todoArc}`) // xanh (done)
        //         .attr("stroke-dashoffset", -todoArc);
        //     $(".donut-text").text(avg + "%");
        // }
        function updateStats(rows) {
            // Lấy danh sách đơn giao duy nhất
            const ordersMap = {};
            rows.forEach(r => {
                if (!ordersMap[r.code_orders]) {
                    ordersMap[r.code_orders] = [];
                }
                ordersMap[r.code_orders].push(r);
            });
            const orders = Object.values(ordersMap);

            const total = orders.length;
            // Đã xuất: đơn giao có ít nhất 1 dòng warehouseman_id > 0
            const completed = orders.filter(orderRows => orderRows.some(r => Number(r.warehouseman_id) > 0)).length;
            const uncompleted = total - completed;

            // Trễ hẹn: đơn giao có ít nhất 1 dòng chưa xuất (warehouseman_id == 0) và date < hiện tại
            const currentDateTime = new Date();
            const overdue = orders.filter(orderRows =>
                orderRows.some(r => {
                    if (!r.date) return false;
                    const [datePart, timePart] = r.date.split(' ');
                    if (!datePart || !timePart) return false;
                    const [day, month, year] = datePart.split('/').map(Number);
                    const [hour, minute, second] = timePart.split(':').map(Number);
                    const deliveryDate = new Date(year, month - 1, day, hour, minute, second);
                    return deliveryDate < currentDateTime && Number(r.warehouseman_id) === 0;
                })
            ).length;

            // sidebar
            $(".card .row:contains('Tổng đơn giao') span:last").text(total);
            $(".card .row-2:contains('Đã xuất') span:last").text(completed);
            $(".card .row:contains('Còn lại') span:last").text(uncompleted);
            $(".card .row-2:contains('Trễ hẹn') span:last").text(overdue);

            $(".progress-item:contains('Hoàn thành') .badge.green").text(completed);
            $(".progress-item:contains('Chưa hoàn thành') .badge.red").text(uncompleted);

            const avg = total > 0 ? Math.round((Number(completed) / Number(total)) * 100 * 100) / 100 : 0;
            // donut
            const doneArc = Math.min(100, Math.max(0, avg));
            const todoArc = 100 - doneArc;

            const $circles = $(".donut svg circle");
            $circles.eq(0).attr("stroke-dasharray", `${todoArc} ${doneArc}`); // đỏ (todo)
            $circles.eq(1).attr("stroke-dasharray", `${doneArc} ${todoArc}`) // xanh (done)
                .attr("stroke-dashoffset", -todoArc);
            $(".donut-text").text(avg + "%");
        }

        function updateRow(row) {
            // 1) cập nhật vào allRows
            const idx = allRows.findIndex(r => r.id === row.id);
            if (idx >= 0) {
                allRows[idx] = row;
            } else {
                allRows.push(row);
            }

            // 2) tìm dòng trong DOM nếu đang hiển thị
            const $row = $body.find(`.table-row[data-id="${row.id}"]`);
            if ($row.length) {
                const $cells = $row.children('div');

                // Cột 0: Giờ hẹn xuất
                $cells.eq(0).text(row.date || '');

                // Cột 1: Đơn hàng
                setCellText($cells.eq(1), row.code_orders);

                // Cột 2: Phiếu giao
                setCellText($cells.eq(2), row.reference_no);

                // Cột 3: Mã khách hàng
                setCellText($cells.eq(3), row.company);

                // Cột 4: Mã sản phẩm
                setCellText($cells.eq(4), row.item_code);

                // Cột 5: Số lượng
                $cells.eq(5).text(Number(row.quantity).toLocaleString());

                // Cột 6: Duyệt kho
                if (Number(row.warehouseman_id) > 0) {
                    $cells.eq(6).removeClass('text_no').addClass('green').text('Đã duyệt');
                } else {
                    $cells.eq(6).removeClass('green').addClass('text_no').text('Chưa duyệt kho');
                }

                // Cột 7: Người phụ trách
                setCellText($cells.eq(7), row.fullname_employee || '');

                // re-apply marquee cho các cột có thể tràn chữ
                [$cells.eq(1), $cells.eq(2), $cells.eq(3), $cells.eq(4), $cells.eq(7)].forEach($el => {
                    if ($el[0].scrollWidth > $el[0].clientWidth) {
                        const text = $el.text();
                        $el.addClass("marquee").html(`<span>${text}</span>`);
                    } else {
                        $el.removeClass("marquee");
                    }
                });

                // highlight
                $row.removeClass("highlight");
                void $row[0].offsetWidth; // ép reflow
                $row.addClass("highlight");
                $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight"));
            }

            // helper nội bộ
            function setCellText($el, text) {
                if ($el.hasClass('marquee')) {
                    $el.removeClass('marquee').empty().text(text);
                } else {
                    $el.text(text);
                }
            }
        }


        function _showAndCount(pageIndex) {
            showPage(pageIndex);
            if (_isPlaying) {
                _pagesRan++;
                if (_pagesRan >= _pageQuota) {
                    stopAutoSwitch();
                    _isPlaying = false;
                    const cb = _onDone;
                    _onDone = null;
                    if (cb) cb();
                }
            }
        }

        function startAutoSwitch(pagesQuota = Infinity, onDone = null) {
            if (autoSwitch) clearInterval(autoSwitch);
            paginateRows(allRows, false);
            _pageQuota = pagesQuota;
            _pagesRan = 0;
            _onDone = onDone;
            _isPlaying = true;
            const total = _getTotalPages();

            if (total > 1) {
                // Hiển thị trang đầu tiên và tính quota luôn

                autoSwitch = setInterval(() => {
                    currentPage = (currentPage + 1) % total;
                    _showAndCount(currentPage);
                }, 10000); // hoặc 30000 cho export_delivery
            } else {
                setInterval(() => {
                    // nếu chỉ có 1 trang thì vẫn tính là đã chạy đủ quota
                    _showAndCount(0);
                    if (_isPlaying) {
                        _pagesRan = _pageQuota; // coi như đủ quota
                        stopAutoSwitch();
                        _isPlaying = false;
                        const cb = _onDone;
                        _onDone = null;
                        if (cb) cb();
                    }
                }, 10000); // hoặc 30000 cho export_delivery
            }
        }

        function stopAutoSwitch() {
            if (autoSwitch) clearInterval(autoSwitch);
            autoSwitch = null;
        }

        // ===== init =====
        function loadProgress() {
            $.getJSON("<?= site_url('dashboard_srceen/updateProgressExportDelivery') ?>", res => {
                if (!res || !res.success) return;
                allRows = Array.isArray(res.rows) ? res.rows.slice() : [];
                paginateRows(allRows, true);
                _showAndCount(0);
                updateStats(allRows);
            });
        }

        // expose API
        window.deliveryDash = {
            play: (pages, onDone) => startAutoSwitch(pages, onDone),
            pause: () => {
                stopAutoSwitch(); // clear interval
                _isPlaying = false;
                _onDone = null;
                _pageQuota = Infinity;
                _pagesRan = 0;
            },
            resume: (pages, onDone) => startAutoSwitch(pages, onDone),
            nextPage: () => {
                const total = _getTotalPages();
                currentPage = (currentPage + 1) % total;
                _showAndCount(currentPage);
            },
            getState: () => ({
                currentPage,
                totalPages: _getTotalPages(),
                isPlaying: _isPlaying
            })
        };
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
    })();
</script>