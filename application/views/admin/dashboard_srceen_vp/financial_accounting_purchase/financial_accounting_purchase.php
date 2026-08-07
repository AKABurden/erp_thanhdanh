<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_purchase/financial_accounting_purchase_css'); ?>
</head>

<body>
    <div class="app_hdm">
        <!-- HEADER -->
        <div class="header_hdm">
            <div class="logo_hdm">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_hdm">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Hóa đơn mua chưa kê khai</span>
            </div>
            <div class="header-right_hdm">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_hdm" style="display:flex;gap:14px;">
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
            <section class="table-wrapper_hdm">
                <!-- 👇 Bọc table trong khung scroll -->
                <table class="hdm">
                    <thead>
                        <tr>
                            <th style="width: 17%;">Số phiếu nhập</th>
                            <th style="width: 17%;">Số PO</th>
                            <th style="width: 17%;">Ngày nhập</th>
                            <th style="width: 28%;">Nhà cung cấp</th>
                            <th style="width: 21%;">Người phụ trách</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_hdm" id="table-body-hdm" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function hdmEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function hdmNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function hdmFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function hdmRowTplkhp(r) {

        const code_import = hdmEsc(r.code_import || '');
        const code_orders = hdmEsc(r.code_orders || '');
        const date = hdmEsc(r.date || '');
        const company = hdmEsc(r.company || '');
        const proposerImg = (r.proposerImg || '');
        return `
        <tr data-id="${code_import}">
            <td style="width: 17%;" title="${code_import}">${code_import}</td>
            <td style="width: 17%;" title="${code_orders}">${code_orders}</td>
            <td style="width: 17%;" title="${date}">${date}</td>
            <td style="width: 28%;" title="${company}">${company}</td>
            <td style="width: 21%;" class="image">${proposerImg}</td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createhdmPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPagehdm() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 180;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        function renderPage(index = 0) {
            const max = getMaxRowsPerPagehdm();
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTplkhp(r)));
        }
        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPagehdm();
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


        function setRows(newRows) {
            rows = Array.isArray(newRows) ? newRows.slice() : [];
            renderPage(0);
        }

        function updateRow(row, keyFn) {
            const key = keyFn ? keyFn(row) : (row.code || row.reference_no || row.code_import || '');
            if (!key) return;
            const idx = rows.findIndex(x => (x.code || x.reference_no || x.code_import) === key);
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
            updateRow,
            getRows: () => rows,
            getMaxRowsPerPagehdm
        };
    }

    /* ========= Khởi tạo pager riêng cho hdm ========= */
    const hdmPager = createhdmPager({
        tbodySelector: "#table-body-hdm",
        rowTplkhp: hdmRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function hdmUpdateStats(stats) {
        // $(".js-dxtc_success").text(hdmFmt(hdmNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(hdmFmt(hdmNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(hdmFmt(hdmNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(hdmFmt(hdmNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(hdmFmt(hdmNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(hdmFmt(hdmNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(hdmFmt(hdmNum(stats?.countdxtc), 0));
        // $(".js-count_delivery_thu").text(hdmFmt(hdmNum(stats?.count_delivery_thu), 0));

    }

    /* ========= Load data (đặt tên riêng) ========= */
    function hdmLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountinghdm') ?>", res => {
            if (!res || !res.success) return;
            hdmUpdateStats(res.stats || {});
            const list = Array.isArray(res.import) ? res.import : [];
            hdmPager.setRows(list);
        });
    }

    function realtimehdmLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountinghdm') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.import) ? res.import : [];
            hdmUpdateStats(res.stats || {});
            list.forEach(row => {
                hdmPager.updateRow(row, r => r.code_import);
            });
            const ids = list.map(r => r.code_import);
            const filtered = hdmPager.getRows().filter(r => ids.includes(r.code_import));
            hdmPager.setRows(filtered);
        });
    }
    setInterval(realtimehdmLoadData, 30000);
    // hdmLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        hdmPager.renderPage(0);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('hdm:update', (row)=> hdmPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function hdmStartClock() {
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
    let _isPlayinghdm = false;
    let _onDonehdm = null;
    let _pageQuotahdm = Infinity;
    let _pagesRanhdm = 0;
    let currentPagehdm = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueehdm() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPageshdm() {
        const max = hdmPager.getRows().length > 0 ? hdmPager.getMaxRowsPerPagehdm() : 1;
        return Math.max(1, Math.ceil(hdmPager.getRows().length / max));
    }

    function _showAndCounthdm(pageIndex) {
        hdmPager.renderPage(pageIndex);
        applyMarqueehdm();
        if (_isPlayinghdm) {
            _pagesRanhdm++;
            if (_pagesRanhdm >= _pageQuotahdm && _onDonehdm) {
                _onDonehdm();
            }
        }
    }

    function startAutoSwitchhdm(pages = Infinity, onDone = null) {
        stopAutoSwitchhdm();
        _isPlayinghdm = true;
        _onDonehdm = onDone;
        _pageQuotahdm = pages;
        _pagesRanhdm = 0;

        const total = _getTotalPageshdm();

        if (total < 1) {
            _showAndCounthdm(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDonehdm === 'function') {
                setTimeout(() => {
                    console.log('✅ HDM: done (only 1 page)');
                    _onDonehdm();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCounthdm(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDonehdm === 'function') {
                setTimeout(() => {
                    console.log('✅ HDM: done (only 1 page)');
                    _onDonehdm();
                }, 30000);
            }
            return;
        }
        _showAndCounthdm(currentPagehdm);

        window._hdmInterval = setInterval(() => {
            currentPagehdm = (currentPagehdm + 1) % total;
            _showAndCounthdm(currentPagehdm);

            // 🔔 Khi đã chạy đủ số vòng thì báo Orchestrator biết
            if (_isPlayinghdm && _pagesRanhdm >= _pageQuotahdm) {
                clearInterval(window._hdmInterval);
                window._hdmInterval = null;
                _isPlayinghdm = false;

                if (typeof _onDonehdm === 'function') {
                    console.log('✅ HDM: auto switch complete, moving to next dashboard...');
                    _onDonehdm();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchhdm() {
        if (window._hdmInterval) {
            clearInterval(window._hdmInterval);
            window._hdmInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_purchase = {
        applyMarquee: () => {
            applyMarqueehdm();
        },
        play: (pages, onDone) => {
            if (!hdmPager.getRows().length) hdmLoadData();
            startAutoSwitchhdm(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchhdm();
            _isPlayinghdm = false;
            _onDonehdm = null;
            _pageQuotahdm = Infinity;
            _pagesRanhdm = 0;
        },
        resume: (pages, onDone) => startAutoSwitchhdm(pages, onDone),
        nextPage: () => {
            const total = _getTotalPageshdm();
            currentPagehdm = (currentPagehdm + 1) % total;
            _showAndCounthdm(currentPagehdm);
        },
        getState: () => ({
            currentPagehdm,
            totalPages: _getTotalPageshdm(),
            isPlaying: _isPlayinghdm
        })
    };
</script>

</html>