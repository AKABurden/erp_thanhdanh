<div class="app_manufacture">
    <?php $this->load->view('admin/dashboard_srceen/manufacture/manufacture_css'); ?>
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
                <div id="clock-date-manu"><?= $dateStr ?></div>
                <div id="clock-time-manu"><?= $timeStr ?></div>
            </div>
        </div>
    </div>

    <div class="container_manufacture">
        <!-- Sidebar -->
        <div class="sidebar_manufacture">
            <div class="card_manufacture" style="min-height: 360px;">
                <div class="row_manufacture"><span>Tổng lệnh sản xuất</span><span><?= $stats['totalOrders'] ?></span></div>
                <div class="row_manufacture row-2_manufacture"><span>Tổng công đoạn</span><span><?= $stats['totalStages'] ?></span></div>
                <div class="row_manufacture"><span>Đã hoàn thành</span><span><?= $stats['completed'] ?></span></div>
                <div class="row_manufacture row-2_manufacture"><span>Chưa hoàn thành</span><span><?= $stats['uncompleted'] ?></span></div>
                <!-- <div class="row_manufacture"><span>Quá hạn</span><span><?= $stats['overdue'] ?></span></div> -->
                <div class="row_manufacture"><span>Tiến độ trung bình</span><span><?= $stats['avgPercent'] ?>%</span></div>
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
                <div class="donut_manufacture">
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

<script>
    (function() {
        const $body = $("#wrapManufacture #table-body");
        let allRowsManu = [],
            allPagesManu = [],
            currentPageManu = 0,
            autoSwitchManu = null,
            lastUpdatedIdManu = null;
        // quota state
        let _pageQuotaManu = Infinity,
            _pagesRanManu = 0,
            _onDoneManu = null,
            _isPlayingManu = false;

        function isVisibleRow(r) {
            return Number(r.qty_todo) > 0;
        }

        function visibleRowsManu() {
            return allRowsManu.filter(isVisibleRow);
        }

        function _getTotalPagesManu() {
            const max = getMaxRowsPerPage();
            const totalVisible = Math.max(1, Math.ceil(visibleRowsManu().length / max));
            return totalVisible;
        }

        function getMaxRowsPerPage() {
            let h = $body.height();
            if (h < 0) {
                h = 600;
            }
            const rowH = $body.find(".table-row_manufacture").first().outerHeight() || 60;
            console.log(Math.max(1, Math.floor(h / rowH)));
            return Math.max(1, Math.floor(h / rowH));
        }
        function paginateRowsManu(rows, reset = true) {
            // Sắp xếp theo stage_id tăng dần trước khi phân trang
            const sortedRows = rows.slice().sort((a, b) => Number(a.stage_idd) - Number(b.stage_idd));
            const max = getMaxRowsPerPage();
            allPagesManu = [];
            const v = sortedRows.filter(isVisibleRow);
            for (let i = 0; i < v.length; i += max) {
                allPagesManu.push(v.slice(i, i + max));
            }
            if (reset) currentPageManu = 0;
        }

        function applyMarqueeManu() {
            $(".table-row_manufacture > div:not(.progress-bar_manufacture)").each(function() {
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

        function showPageManu(pageIndex) {
            const max = getMaxRowsPerPage();
            console.log(max);

            const total = Math.max(1, Math.ceil(visibleRowsManu().length / max));
            currentPageManu = Math.min(pageIndex, total - 1);
            const start = currentPageManu * max;
            const end = start + max;
            const rows = visibleRowsManu().slice(start, end);
            $body.addClass("hidden");
            setTimeout(() => {
                $body.empty();
                rows.forEach(r => {
                    const $r = $(rowTpl(r));
                    $body.append($r);
                });
                $body.removeClass("hidden");
                applyMarqueeManu();
            }, 200);
        }

        function _showAndCountManu(pageIndex) {
            applyMarqueeManu();

            showPageManu(pageIndex);
            if (_isPlayingManu) {
                _pagesRanManu++;
                console.log("[MANU DEBUG] page:", pageIndex, "pagesRan:", _pagesRanManu, "/", _pageQuotaManu);
                if (_pagesRanManu >= _pageQuotaManu) {
                    stopAutoSwitchManu();
                    _isPlayingManu = false;
                    const cb = _onDoneManu;
                    _onDoneManu = null;
                    if (typeof cb === "function") {
                        console.log("[MANU DEBUG] Gọi callback => chuyển sang Delivery");
                        cb();
                    }
                }
            }
        }

        function startAutoSwitchManu(pagesQuota = Infinity, onDone = null) {

            if (autoSwitchManu) clearInterval(autoSwitchManu);
            paginateRowsManu(allRowsManu, false);

            _pageQuotaManu = pagesQuota;
            _pagesRanManu = 0;
            _onDoneManu = onDone;
            _isPlayingManu = true;

            const total = _getTotalPagesManu();

            if (total > 1) {
                // hiển thị trang đầu

                autoSwitchManu = setInterval(() => {
                    if (!_isPlayingManu) return; // tránh chạy tiếp sau khi đã stop
                    currentPageManu = (currentPageManu + 1) % total;
                    _showAndCountManu(currentPageManu);
                    applyMarqueeManu();
                }, 10000);
            } else {
                setInterval(() => {
                    _showAndCountManu(0); // quota xử lý luôn tại đây
                    applyMarqueeManu();
                }, 10000);
            }
        }


        function stopAutoSwitchManu() {
            if (autoSwitchManu) clearInterval(autoSwitchManu);
            autoSwitchManu = null;
        }

        function loadProgress() {
            $.getJSON("<?= site_url('dashboard_srceen/updateProgress') ?>", res => {
                if (!res || !res.success) return;
                // giữ allRowsManu đầy đủ, pagination dựa trên visibleRowsManu()
                allRowsManu = Array.isArray(res.rows) ? res.rows.slice() : [];
                paginateRowsManu(allRowsManu, true);
                _showAndCountManu(0);
                updateStatsManu(allRowsManu);
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
            socket.on('update_dashboard', (payload) => {
                location.reload();
            });
            socket.on('loadProgress', (payload) => {
                console.log(payload)
                const data = payload && payload.data !== undefined ? payload.data : payload;
                if (!data) return;
                switch (data.action) {
                    case 'add': {
                        const newRow = data.newRow;
                        if (newRow) {
                            // 1) luôn thêm vào allRowsManu
                            allRowsManu.push(newRow);

                            // 2) nếu hàng VISIBLE -> append DOM / else chỉ cập nhật paginate
                            if (isVisibleRow(newRow)) {
                                const maxRows = getMaxRowsPerPage();
                                const visibleCount = $body.find(".table-row_manufacture").length;
                                const startIndex = currentPageManu * maxRows;
                                // chỗ hiển thị: nếu còn chỗ và newRow nằm trong trang hiện tại thì append
                                const vIdx = visibleRowsManu().indexOf(newRow);
                                if (visibleCount < maxRows && vIdx >= startIndex && vIdx < startIndex + maxRows) {
                                    const $row = $(rowTpl(newRow));
                                    $body.append($row);
                                    applyMarqueeManu();
                                    $row.addClass("highlight_manufacture");
                                    $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight_manufacture"));
                                } else {
                                    paginateRowsManu(allRowsManu, false);
                                }
                            } else {
                                // không hiển thị -> chỉ cập nhật paginate (vì số visible không thay đổi)
                                paginateRowsManu(allRowsManu, false);
                            }
                        }
                        updateStatsManu(allRowsManu);
                        startAutoSwitchManu();
                        break;
                    }

                    case 'update': {
                        const r = data.updatedRow;
                        if (!r) break;

                        // tìm trạng thái hiển thị trước khi cập nhật
                        const prev = allRowsManu.find(x => x.stage_id === r.stage_id);
                        const wasVisible = prev ? isVisibleRow(prev) : false;

                        // 1) cập nhật vào allRowsManu (không xóa)
                        const i = allRowsManu.findIndex(x => x.stage_id === r.stage_id);
                        if (i >= 0) allRowsManu[i] = r;
                        else allRowsManu.push(r);

                        const nowVisible = isVisibleRow(r);

                        // 2) xử lý DOM: nếu trước visible mà giờ không -> ẩn DOM; nếu trước không nhưng giờ có -> repaginate/show
                        if (wasVisible && !nowVisible) {
                            // ẩn DOM nhưng không xóa khỏi allRowsManu
                            const $row = $body.find(`.table-row_manufacture[data-id="${r.stage_id}"]`);
                            if ($row.length) {
                                $row.addClass("fade-out_manufacture");
                                setTimeout(() => {
                                    // remove element from DOM only
                                    $row.remove();
                                    paginateRowsManu(allRowsManu, false);
                                    showPageManu(currentPageManu);
                                }, 250);
                            } else {
                                paginateRowsManu(allRowsManu, false);
                                showPageManu(currentPageManu);
                            }
                        } else if (!wasVisible && nowVisible) {
                            // trở thành visible -> repaginate và show (đơn giản nhất)
                            paginateRowsManu(allRowsManu, false);
                            showPageManu(currentPageManu);
                        } else {
                            // visible -> chỉ cập nhật nội dung nếu đang hiển thị
                            updateRow(r);
                        }

                        updateStatsManu(allRowsManu);
                        break;
                    }

                    case 'delete': {
                        if (!data.deleted_id) break;

                        // 1) xóa khỏi allRowsManu
                        allRowsManu = allRowsManu.filter(r => r.stage_id !== data.deleted_id);

                        // 2) nếu dòng đang hiển thị -> xóa DOM nhẹ nhàng, rồi vẽ lại trang để lấp chỗ
                        const $row = $body.find(`.table-row_manufacture[data-id="${data.deleted_id}"]`);
                        if ($row.length) {
                            $row.addClass("fade-out_manufacture");
                            setTimeout(() => {
                                paginateRowsManu(allRowsManu, false);
                                showPageManu(currentPageManu);
                            }, 250);
                        } else {
                            paginateRowsManu(allRowsManu, false);
                        }

                        updateStatsManu(allRowsManu);
                        startAutoSwitchManu(); // cấu trúc trang thay đổi
                        break;
                    }
                }
            });

            // Re-paginate khi thay đổi kích thước cửa sổ (đổi số dòng/trang)
            $(window).on('resize', () => {
                paginateRowsManu(allRowsManu, false);
                showPageManu(currentPageManu);
                startAutoSwitchManu();
            });
        })();

        function rowTpl(r) {
            return `<div class="table-row_manufacture" data-id="${r.stage_id}">
                <div>${r.order_code}</div>
                <div>${r.sku}</div>
                <div>${r.stage}</div>
                <div class="yellow" style="text-align: center;">${Number(r.qty_plan).toLocaleString()}</div>
                <div class="green" style="text-align: center;">${Number(r.qty_done).toLocaleString()}</div>
                <div class="red" style="text-align: center;">${Number(r.qty_todo).toLocaleString()}</div>
                <div class="progress-bar_manufacture"><div class="bar"><div class="bar-fill" style="width:${r.percent}%;background:${r.bar_color}"></div></div><span class="percent">${r.percent}%</span></div>
            </div>`;
        }

        function updateRow(row) {
            // Cập nhật allRowsManu nhưng KHÔNG xóa nếu qty_todo <= 0
            const idx = allRowsManu.findIndex(r => r.stage_id === row.stage_id);
            if (idx >= 0) {
                allRowsManu[idx] = row;
            } else {
                allRowsManu.push(row);
            }

            // Nếu row không visible -> không render
            if (!isVisibleRow(row)) {
                const $row = $body.find(`.table-row_manufacture[data-id="${row.stage_id}"]`);
                if ($row.length) {
                    $row.addClass("fade-out_manufacture");
                    setTimeout(() => {
                        $row.remove();
                        paginateRowsManu(allRowsManu, false);
                        showPageManu(currentPageManu);
                    }, 200);
                }
                return;
            }

            // update DOM nếu đang hiển thị
            const $row = $body.find(`.table-row_manufacture[data-id="${row.stage_id}"]`);
            if ($row.length) {
                const $cells = $row.children('div');
                $cells.eq(4).text(Number(row.qty_done).toLocaleString());
                $cells.eq(5).text(Number(row.qty_todo).toLocaleString());
                $cells.eq(6).html(`<div class="bar"><div class="bar-fill" style="width:${row.percent}%;background:${row.bar_color}"></div></div><span class="percent">${row.percent}%</span>`);
                $row.removeClass("highlight");
                void $row[0].offsetWidth;
                $row.addClass("highlight");
                $row.one("animationend webkitAnimationEnd oAnimationEnd", () => $row.removeClass("highlight"));
            } else {
                // nếu không có trong DOM (nhưng visible) -> repaginate/show để hiển thị nếu trang chứa nó
                paginateRowsManu(allRowsManu, false);
                showPageManu(currentPageManu);
            }
        }

        function updateStatsManu(rows) {
            const total = rows.length;
            const totalOrders = new Set(rows.map(r => r.order_code)).size;
            const completed = rows.filter(r => Number(r.percent) >= 100).length;
            const uncompleted = total - completed;
            const avg = total > 0 ?
                Math.round(rows.reduce((s, r) => s + Number(r.percent || 0), 0) / total) :
                0;

            // sidebar
            $(".card_manufacture .row_manufacture:contains('Tổng lệnh sản xuất') span:last").text(formatNumber(totalOrders));
            $(".card_manufacture .row-2_manufacture:contains('Tổng công đoạn') span:last").text(formatNumber(total));
            $(".card_manufacture .row_manufacture:contains('Đã hoàn thành') span:last").text(formatNumber(completed));
            $(".card_manufacture .row-2_manufacture:contains('Chưa hoàn thành') span:last").text(formatNumber(uncompleted));
            $(".card_manufacture .row_manufacture:contains('Tiến độ trung bình') span:last").text(avg + "%");

            $(".progress-item_manufacture:contains('Hoàn thành') .badge_manufacture.green_manufacture").text(formatNumber(completed));
            $(".progress-item_manufacture:contains('Chưa hoàn thành') .badge_manufacture.red_manufacture").text(formatNumber(uncompleted));

            // donut
            const doneArc = Math.min(100, Math.max(0, avg));
            const todoArc = 100 - doneArc;

            const $circles = $(".donut_manufacture svg circle");
            $circles.eq(0).attr("stroke-dasharray", `${todoArc} ${doneArc}`); // đỏ (todo)
            $circles.eq(1).attr("stroke-dasharray", `${doneArc} ${todoArc}`) // xanh (done)
                .attr("stroke-dashoffset", -todoArc);
            $(".donut-text_manufacture").text(avg + "%");
        }

        function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
            nStr += '';
            x = nStr.split(decSeperate);
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            x2 = x2.substr(0, 2);
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
            }
            return x1 + x2;
        };
        window.manuDash = {
            applyMarquee: () => {
                applyMarqueeManu();
            },
            play: (pages, onDone) => startAutoSwitchManu(pages, onDone),
            pause: () => {
                stopAutoSwitchManu(); // clear interval
                _isPlayingManu = false;
                _onDoneManu = null;
                _pageQuotaManu = Infinity;
                _pagesRanManu = 0;
            },
            resume: (pages, onDone) => startAutoSwitchManu(pages, onDone),
            nextPage: () => {
                const total = _getTotalPagesManu();
                currentPageManu = (currentPageManu + 1) % total;
                _showAndCountManu(currentPageManu);
            },
            getState: () => ({
                currentPageManu,
                totalPages: _getTotalPagesManu(),
                isPlaying: _isPlayingManu
            })
        };
        (function startRealtimeClock() {
            const elDate = document.getElementById('clock-date-manu');
            const elTime = document.getElementById('clock-time-manu');
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