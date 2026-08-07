<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_customs/financial_accounting_customs_css'); ?>
</head>

<body>
    <div class="app_khq">
        <!-- HEADER -->
        <div class="header_khq">
            <div class="logo_khq">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_khq">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Đơn chưa khai hải quan</span>
            </div>
            <div class="header-right_khq">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_khq" style="display:flex;gap:14px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_dxtc">
                <div class="kpi-box_dxtc xanh">
                    <div class="label">Đề xuất nội bộ đã hoàn thành</div>
                    <div class="value js-dxtc_success">-</div>
                    <div style="border-bottom:1px solid #00691A;margin:8px 0;"></div>
                    <div class="label">Đề xuất nội bộ chưa hoàn thành</div>
                    <div class="value js-dxtc_pending">-</div>
                </div>
                <div class="kpi-box_dxtc vang">
                    <div class="label">Hóa đơn bán chưa xuất</div>
                    <div class="value js-count_delivery_cx">-</div>
                    <div style="border-bottom:1px solid #AF9514;margin:8px 0;"></div>
                    <div class="label">Hóa đơn bán chưa kê khai</div>
                    <div class="value js-dxtc_code_custom_null ">-</div>
                    <div style="border-bottom:1px solid #AF9514;margin:8px 0;"></div>
                    <div class="label">Hóa đơn bán chưa thu</div>
                    <div class="value js-count_delivery_thu ">-</div>
                </div>
                <div class="kpi-box_dxtc cam">
                    <div class="label">Hóa đơn mua chưa khai Hải quan</div>
                    <div class="value js-count_delivery_supplier_code_null">-</div>
                </div>
                <div class="kpi-box_dxtc xanh-nhat">
                    <div class="label">Phiếu đề xuất tài chính chưa xử lý</div>
                    <div class="value js-count_dxtc">-</div>
                </div>
                <div class="kpi-box_dxtc tim">
                    <div class="label">Phiếu yêu cầu chi chưa xử lý</div>
                    <div class="value js-count_ycc">-</div>
                </div>

            </aside>

            <!-- TABLE -->
            <section class="table-wrapper_khq">
                <!-- 👇 Bọc table trong khung scroll -->
                <table class="khq">
                    <thead>
                        <tr>
                            <th style="width: 22%;">Số phiếu giao</th>
                            <th style="width: 22%;">Ngày giao</th>
                            <th style="width: 34%;">Khách hàng</th>
                            <th style="width: 22%;">Số đơn hàng</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_khq" id="table-body-khq" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function khqEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function khqNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function khqFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function khqRowTplkhp(r) {

        const code = khqEsc(r.code || r.reference_no || '');
        const date = khqEsc(r.date || '');
        const company = khqEsc(r.company || '');
        const order_reference_no = khqEsc(r.order_reference_no || '');
        return `
        <tr data-id="${code}">
            <td style="width: 22%;" title="${code}">${code}</td>
            <td style="width: 22%;" title="${date}">${date}</td>
            <td style="width: 34%;" title="${company}">${company}</td>
            <td style="width: 22%;" title="${order_reference_no}">${order_reference_no}</td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createkhqPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPagekhq() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 60;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPagekhq() > 10 ? getMaxRowsPerPagekhq() : 10;
        //     const totalPages = Math.max(1, Math.ceil(rows.length / max));
        //     currentPage = Math.min(index, totalPages - 1);
        //     const start = currentPage * max;
        //     const slice = rows.slice(start, start + max);

        //     $tbody.addClass("hidden_pkd");
        //     setTimeout(() => {
        //         $tbody.empty();
        //         slice.forEach(r => $tbody.append(rowTplkhp(r)));
        //         $tbody.removeClass("hidden_pkd");
        //     }, 120);
        // }
        function renderPage(index = 0) {
            const max = getMaxRowsPerPagekhq() > 10 ? getMaxRowsPerPagekhq() : 10;
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(khqRowTplkhp(r)));
        }

        function startAutoSwitch(interval = 10000) {
            if (autoSwitch) clearInterval(autoSwitch);
            const max = getMaxRowsPerPagekhq();
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            if (totalPages > 1) {
                autoSwitch = setInterval(() => {
                    currentPage = (currentPage + 1) % totalPages;
                    renderPage(currentPage);
                }, interval);
            } else {
                autoSwitch = setInterval(() => renderPage(0), interval);
            }
        }

        function stopAutoSwitch() {
            if (autoSwitch) clearInterval(autoSwitch);
            autoSwitch = null;
        }

        function setRows(newRows) {
            rows = Array.isArray(newRows) ? newRows.slice() : [];
            renderPage(0);
        }

        function updateRow(row, keyFn) {
            const key = keyFn ? keyFn(row) : (row.code || row.reference_no || '');
            if (!key) return;
            const idx = rows.findIndex(x => (x.code || x.reference_no) === key);
            if (idx >= 0) rows[idx] = row;
            else rows.push(row);

            const $tr = $tbody.find(`tr[data-id="${key}"]`);
            if (!$tr.length) return;
            $tr.html($(rowTplkhp(row)).html());
            $tr.addClass("highlight_pkd");
            $tr.one("animationend webkitAnimationEnd oAnimationEnd", () => $tr.removeClass("highlight_pkd"));
        }

        return {
            setRows,
            renderPage,
            startAutoSwitch,
            stopAutoSwitch,
            updateRow,
            getRows: () => rows,
            getMaxRowsPerPagekhq
        };
    }

    /* ========= Khởi tạo pager riêng cho khq ========= */
    const khqPager = createkhqPager({
        tbodySelector: "#table-body-khq",
        rowTplkhp: khqRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function khqUpdateStats(stats) {
        // $(".js-dxtc_success").text(khqFmt(khqNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(khqFmt(khqNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(khqFmt(khqNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(khqFmt(khqNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(khqFmt(khqNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(khqFmt(khqNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(khqFmt(khqNum(stats?.countdxtc), 0));
        // $(".js-count_delivery_thu").text(khqFmt(khqNum(stats?.count_delivery_thu), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function khqLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingKHQ') ?>", res => {
            if (!res || !res.success) return;
            khqUpdateStats(res.stats || {});
            const list = Array.isArray(res.deliveries) ? res.deliveries : [];
            khqPager.setRows(list);
            // khqPager.startAutoSwitch(10000);
        });
    }
    // khqLoadData();
    function realtimekhqLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingKHQ') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.deliveries) ? res.deliveries : [];
            list.forEach(row => {
                khqPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = khqPager.getRows().filter(r => ids.includes(r.code));
            khqPager.setRows(filtered);
        });
    }
    setInterval(realtimekhqLoadData, 30000);
    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        khqPager.renderPage(0);
        // khqPager.startAutoSwitch(10000);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('khq:update', (row)=> khqPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function khqStartClock() {
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
            second: '2-digit',
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

    // ====== Dashboard Control Variables ======
    let _isPlayingKhq = false;
    let _onDoneKhq = null;
    let _pageQuotaKhq = Infinity;
    let _pagesRanKhq = 0;
    let currentPageKhq = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueeKhq() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesKhq() {
        const max = khqPager.getRows().length > 0 ? khqPager.getMaxRowsPerPagekhq() : 1;
        return Math.max(1, Math.ceil(khqPager.getRows().length / max));
    }

    function _showAndCountKhq(pageIndex) {
        khqPager.renderPage(pageIndex);
        applyMarqueeKhq();
        if (_isPlayingKhq) {
            _pagesRanKhq++;
            if (_pagesRanKhq >= _pageQuotaKhq && _onDoneKhq) {
                _onDoneKhq();
            }
        }
    }

    function startAutoSwitchKhq(pages = Infinity, onDone = null) {
        stopAutoSwitchKhq();
        _isPlayingKhq = true;
        _onDoneKhq = onDone;
        _pageQuotaKhq = pages;
        _pagesRanKhq = 0;

        const total = _getTotalPagesKhq();

        if (total < 1) {
            _showAndCountKhq(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDoneKhq === 'function') {
                setTimeout(() => {
                    console.log('✅ KHQ: done (only 1 page)');
                    _onDoneKhq();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountKhq(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDoneKhq === 'function') {
                setTimeout(() => {
                    console.log('✅ KHQ: done (only 1 page)');
                    _onDoneKhq();
                }, 30000);
            }
            return;
        }
        _showAndCountKhq(currentPageKhq);

        window._khqInterval = setInterval(() => {
            currentPageKhq = (currentPageKhq + 1) % total;
            _showAndCountKhq(currentPageKhq);

            // 🔔 Khi đã hiển thị đủ số vòng được yêu cầu (pageQuota)
            if (_isPlayingKhq && _pagesRanKhq >= _pageQuotaKhq) {
                clearInterval(window._khqInterval);
                window._khqInterval = null;
                _isPlayingKhq = false;

                if (typeof _onDoneKhq === 'function') {
                    console.log('✅ KHQ: auto switch complete, moving to next dashboard...');
                    _onDoneKhq();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchKhq() {
        if (window._khqInterval) {
            clearInterval(window._khqInterval);
            window._khqInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_customs = {
        applyMarquee: () => {
            applyMarqueeKhq();
        },
        play: (pages, onDone) => {
            if (!khqPager.getRows().length) khqLoadData();
            startAutoSwitchKhq(pages, onDone);
        },

        pause: () => {
            stopAutoSwitchKhq();
            _isPlayingKhq = false;
            _onDoneKhq = null;
            _pageQuotaKhq = Infinity;
            _pagesRanKhq = 0;
        },
        resume: (pages, onDone) => startAutoSwitchKhq(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesKhq();
            currentPageKhq = (currentPageKhq + 1) % total;
            _showAndCountKhq(currentPageKhq);
        },
        getState: () => ({
            currentPageKhq,
            totalPages: _getTotalPagesKhq(),
            isPlaying: _isPlayingKhq
        })
    };
</script>

</html>