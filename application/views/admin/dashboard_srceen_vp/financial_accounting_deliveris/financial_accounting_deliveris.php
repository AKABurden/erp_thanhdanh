<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_deliveris/financial_accounting_deliveris_css'); ?>
</head>

<body>
    <div class="app_hdbcx">
        <!-- HEADER -->
        <div class="header_hdbcx">
            <div class="logo_hdbcx">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_hdbcx">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Hóa đơn bán chưa xuất</span>
            </div>
            <div class="header-right_hdbcx">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_hdbcx" style="display:flex;gap:14px;">
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
            <section class="table-wrapper_hdbcx">
                <!-- 👇 Bọc table trong khung scroll -->
                <table class="hdbcx">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Số phiếu giao</th>
                            <th style="width: 18%;">Ngày giao</th>
                            <th style="width: 18%;">Khách hàng</th>
                            <th style="width: 28%;">Số đơn hàng</th>
                            <th style="width: 18%;">Ngày trễ hạn</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_hdbcx" id="table-body-hdbcx" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function hdbcxEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function hdbcxNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function hdbcxFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function hdbcxRowTplkhp(r) {

        const code = khqEsc(r.code || r.reference_no || '');
        const date = khqEsc(r.date || '');
        const company = khqEsc(r.company || '');
        const order_reference_no = khqEsc(r.order_reference_no || '');
        let tre = khqEsc(r.tre || '');
        if (tre == '0 ngày') {
            tre = '-';
        }

        return `
        <tr data-id="${code}">
            <td style="width: 22%;" title="${code}">${code}</td>
            <td style="width: 22%;" title="${date}">${date}</td>
            <td style="width: 34%;" title="${company}">${company}</td>
            <td style="width: 22%;" title="${order_reference_no}">${order_reference_no}</td>
            <td style="width: 22%;" title="${tre}">${tre}</td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createhdbcxPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPagehdbcx() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 60;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPagehdbcx() > 10 ? getMaxRowsPerPagehdbcx() : 10;
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
            const max = getMaxRowsPerPagehdbcx() > 10 ? getMaxRowsPerPagehdbcx() : 10;
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTplkhp(r)));
        }

        function startAutoSwitch(interval = 10000) {
            if (autoSwitch) clearInterval(autoSwitch);
            const max = getMaxRowsPerPagehdbcx();
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
            getMaxRowsPerPagehdbcx
        };
    }

    /* ========= Khởi tạo pager riêng cho hdbcx ========= */
    const hdbcxPager = createhdbcxPager({
        tbodySelector: "#table-body-hdbcx",
        rowTplkhp: hdbcxRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function hdbcxUpdateStats(stats) {
        // $(".js-dxtc_success").text(hdbcxFmt(hdbcxNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(hdbcxFmt(hdbcxNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(hdbcxFmt(hdbcxNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(hdbcxFmt(hdbcxNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(hdbcxFmt(hdbcxNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(hdbcxFmt(hdbcxNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(hdbcxFmt(hdbcxNum(stats?.countdxtc), 0));
        // $(".js-count_delivery_thu").text(hdbcxFmt(hdbcxNum(stats?.count_delivery_thu), 0));

    }

    /* ========= Load data (đặt tên riêng) ========= */
    function hdbcxLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountinghdbcx') ?>", res => {
            if (!res || !res.success) return;
            hdbcxUpdateStats(res.stats || {});
            const list = Array.isArray(res.deliveriescx) ? res.deliveriescx : [];
            hdbcxPager.setRows(list);
        });
    }
    // hdbcxLoadData();
    function realtimehdbcxLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountinghdbcx') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.deliveriescx) ? res.deliveriescx : [];
            list.forEach(row => {
                hdbcxPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = hdbcxPager.getRows().filter(r => ids.includes(r.code));
            hdbcxPager.setRows(filtered);
        });
    }
    setInterval(realtimehdbcxLoadData, 30000);

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        hdbcxPager.renderPage(0);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('hdbcx:update', (row)=> hdbcxPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function hdbcxStartClock() {
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
    let _isPlayinghdbcx = false;
    let _onDonehdbcx = null;
    let _pageQuotahdbcx = Infinity;
    let _pagesRanhdbcx = 0;
    let currentPagehdbcx = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueehdbcx() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPageshdbcx() {
        const max = hdbcxPager.getRows().length > 0 ? hdbcxPager.getMaxRowsPerPagehdbcx() : 1;
        return Math.max(1, Math.ceil(hdbcxPager.getRows().length / max));
    }

    function _showAndCounthdbcx(pageIndex) {
        hdbcxPager.renderPage(pageIndex);
        applyMarqueehdbcx();
        if (_isPlayinghdbcx) {
            _pagesRanhdbcx++;
            if (_pagesRanhdbcx >= _pageQuotahdbcx && _onDonehdbcx) {
                _onDonehdbcx();
            }
        }
    }

    function startAutoSwitchhdbcx(pages = Infinity, onDone = null) {
        stopAutoSwitchhdbcx();
        _isPlayinghdbcx = true;
        _onDonehdbcx = onDone;
        _pageQuotahdbcx = pages;
        _pagesRanhdbcx = 0;

        const total = _getTotalPageshdbcx();

        if (total < 1) {
            _showAndCounthdbcx(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDonehdbcx === 'function') {
                setTimeout(() => {
                    console.log('✅ HDBCX: done (only 1 page)');
                    _onDonehdbcx();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCounthdbcx(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDonehdbcx === 'function') {
                setTimeout(() => {
                    console.log('✅ HDBCX: done (only 1 page)');
                    _onDonehdbcx();
                }, 30000);
            }
            return;
        }
        _showAndCounthdbcx(currentPagehdbcx);

        window._hdbcxInterval = setInterval(() => {
            currentPagehdbcx = (currentPagehdbcx + 1) % total;
            _showAndCounthdbcx(currentPagehdbcx);

            // 🔔 Khi đã chạy đủ số vòng thì báo Orchestrator biết
            if (_isPlayinghdbcx && _pagesRanhdbcx >= _pageQuotahdbcx) {
                clearInterval(window._hdbcxInterval);
                window._hdbcxInterval = null;
                _isPlayinghdbcx = false;

                if (typeof _onDonehdbcx === 'function') {
                    console.log('✅ HDBCX: auto switch complete, moving to next dashboard...');
                    _onDonehdbcx();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchhdbcx() {
        if (window._hdbcxInterval) {
            clearInterval(window._hdbcxInterval);
            window._hdbcxInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_deliveris = {
        applyMarquee: () => {
            applyMarqueehdbcx();
        },
        play: (pages, onDone) => {
            if (!hdbcxPager.getRows().length) hdbcxLoadData();
            startAutoSwitchhdbcx(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchhdbcx();
            _isPlayinghdbcx = false;
            _onDonehdbcx = null;
            _pageQuotahdbcx = Infinity;
            _pagesRanhdbcx = 0;
        },
        resume: (pages, onDone) => startAutoSwitchhdbcx(pages, onDone),
        nextPage: () => {
            const total = _getTotalPageshdbcx();
            currentPagehdbcx = (currentPagehdbcx + 1) % total;
            _showAndCounthdbcx(currentPagehdbcx);
        },
        getState: () => ({
            currentPagehdbcx,
            totalPages: _getTotalPageshdbcx(),
            isPlaying: _isPlayinghdbcx
        })
    };
</script>

</html>