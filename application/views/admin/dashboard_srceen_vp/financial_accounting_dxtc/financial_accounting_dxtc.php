<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_dxtc/financial_accounting_dxtc_css'); ?>
</head>

<body>
    <div class="app_dxtc">
        <!-- HEADER -->
        <div class="header_dxtc">
            <div class="logo_dxtc">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_dxtc">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Phiếu đề xuất tài chính chưa xử lý</span>
            </div>
            <div class="header-right_dxtc">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_dxtc" style="display:flex;gap:14px;">
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
            <section class="table-wrapper_dxtc">
                <!-- 👇 Bọc table trong khung scroll -->
                <div class="head">
                    <div class="legend_dxtc">
                        <span class="text-status"><i class="dot_dxtc red_dxtc"></i><span>Chưa duyệt</span></span>
                        <span class="text-status"><i class="dot_dxtc green_dxtc"></i><span>Đã duyệt</span></span>
                    </div>
                </div>
                <table class="dxtc">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Ngày chứng từ</th>
                            <th style="width: 16%;">Mã chứng từ</th>
                            <th style="width: 12%;">Trạng thái</th>
                            <th style="width: 15%;">Người duyệt</th>
                            <th style="width: 15%;">Người đề xuất</th>
                            <th style="width: 10%;">Người đề xuất duyệt</th>
                            <th style="width: 10%;">Trưởng phòng duyệt</th>
                            <th style="width: 10%;">Thủ quỹ hoàn thành</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_dxtc" id="table-body-dxtc" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function dxtcEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function dxtcNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function dxtcFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function dxtcRowTplkhp(r) {

        const code = dxtcEsc(r.code || '');
        const cate = dxtcEsc(r.note_item || '');
        const date = dxtcEsc(r.date || '');
        const status = (r.status || '');
        const proposerImg_nd = (r.image_employee_staff_browse || '');
        const proposerImg_dx = (r.image_employee || '');
        const proposerImg_dxd = (r.image_employee || '');
        const statusdn = (r.statusdn || '');
        const statustp = (r.statustp || '');
        const statustq = (r.statustq || '');

        return `
        <tr data-id="${code}">
            <td style="width: 12%;" title="${date}">${date}</td>
            <td style="width: 16%;" title="${code}">${code}</td>
            <td style="width: 12%;">${status}</td>
            <td style="width: 15%;font-size:18px;" class="image">${proposerImg_nd}</td>
            <td style="width: 15%;font-size:18px;" class="image">${proposerImg_dx}</td>
            <td style="width: 10%;" class="image">${statusdn}</td>
            <td style="width: 10%;" class="image">${statustp}</td>
            <td style="width: 10%;" class="image">${statustq}</td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createdxtcPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPagedxtc() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 120;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        function renderPage(index = 0) {
            const max = getMaxRowsPerPagedxtc();
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTplkhp(r)));
        }
        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPagedxtc();
        //     const totalPages = Math.max(1, Math.ceil(rows.length / max));
        //     currentPage = Math.min(index, totalPages - 1);
        //     const start = currentPage * max;
        //     console.log("Render page", currentPage + 1, "of", totalPages, "| rows:", rows.length, "| max per page:", max);

        //     const slice = rows.slice(start, start + max);

        //     $tbody.addClass("hidden_pkd");
        //     setTimeout(() => {
        //         $tbody.empty();
        //         slice.forEach(r => $tbody.append(rowTplkhp(r)));
        //         $tbody.removeClass("hidden_pkd");
        //     }, 120);
        // }

        // function startAutoSwitch(interval = 10000) {
        //     alert(123)
        //     if (autoSwitch) clearInterval(autoSwitch);
        //     const max = getMaxRowsPerPagedxtc() > 10 ? getMaxRowsPerPagedxtc() : 10;
        //     const totalPages = Math.max(1, Math.ceil(rows.length / max));
        //     if (totalPages > 1) {
        //         autoSwitch = setInterval(() => {
        //             currentPage = (currentPage + 1) % totalPages;
        //             renderPage(currentPage);
        //         }, interval);
        //     } else {
        //         autoSwitch = setInterval(() => renderPage(0), interval);
        //     }
        // }

        // function stopAutoSwitch() {
        //     if (autoSwitch) clearInterval(autoSwitch);
        //     autoSwitch = null;
        // }

        function setRows(newRows) {
            // Lọc bỏ các record có status_hide = 1
            rows = Array.isArray(newRows) ? newRows.filter(item => item.status_hide != 1) : [];
            renderPage(0);
        }

        function updateRow(row, keyFn) {
            // Bỏ qua nếu row có status_hide = 1
            if (row && row.status_hide == 1) return;

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
            updateRow,
            getRows: () => rows,
            getMaxRowsPerPagedxtc
        };
    }

    /* ========= Khởi tạo pager riêng cho dxtc ========= */
    const dxtcPager = createdxtcPager({
        tbodySelector: "#table-body-dxtc",
        rowTplkhp: dxtcRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function dxtcUpdateStats(stats) {
        // $(".js-dxtc_success").text(dxtcFmt(dxtcNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(dxtcFmt(dxtcNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(dxtcFmt(dxtcNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(dxtcFmt(dxtcNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(dxtcFmt(dxtcNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(dxtcFmt(dxtcNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(dxtcFmt(dxtcNum(stats?.countdxtc), 0));
        // $(".js-count_delivery_thu").text(thuFmt(thuNum(stats?.count_delivery_thu), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function dxtcLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingdxtc') ?>", res => {
            if (!res || !res.success) return;
            dxtcUpdateStats(res.stats || {});
            // const list = Array.isArray(res.dxtc) ? res.dxtc : [];
            const list = Array.isArray(res.dxtc) ? res.dxtc.filter(item => item.status_hide != 1) : [];
            dxtcPager.setRows(list);
        });
    }
    // dxtcLoadData();
    function realtimedxtcLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingdxtc') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.dxtc) ? res.dxtc.filter(item => item.status_hide != 1) : [];
            dxtcUpdateStats(res.stats || {});
            list.forEach(row => {
                dxtcPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = dxtcPager.getRows().filter(r => ids.includes(r.code));
            dxtcPager.setRows(filtered);
        });
    }
    setInterval(realtimedxtcLoadData, 30000);
    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        dxtcPager.renderPage(0);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('dxtc:update', (row)=> dxtcPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function dxtcStartClock() {
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
    let _isPlayingdxtc = false;
    let _onDonedxtc = null;
    let _pageQuotadxtc = Infinity;
    let _pagesRandxtc = 0;
    let currentPagedxtc = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueedxtc() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesdxtc() {
        const max = dxtcPager.getRows().length > 0 ? dxtcPager.getMaxRowsPerPagedxtc() : 1;
        return Math.max(1, Math.ceil(dxtcPager.getRows().length / max));
    }

    function _showAndCountdxtc(pageIndex) {
        dxtcPager.renderPage(pageIndex);
        applyMarqueedxtc();

        if (_isPlayingdxtc) {
            _pagesRandxtc++;
            if (_pagesRandxtc >= _pageQuotadxtc && _onDonedxtc) {
                _onDonedxtc();
            }
        }
    }

    function startAutoSwitchdxtc(pages = Infinity, onDone = null) {
        stopAutoSwitchdxtc();
        _isPlayingdxtc = true;
        _onDonedxtc = onDone;
        _pageQuotadxtc = pages;
        _pagesRandxtc = 0;

        const total = _getTotalPagesdxtc();

        if (total < 1) {
            _showAndCountdxtc(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done luôn sau 2 giây
            if (typeof _onDonedxtc === 'function') {
                setTimeout(() => {
                    console.log('✅ DXTC: done (only 1 page)');
                    _onDonedxtc();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountdxtc(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done luôn sau 2 giây
            if (typeof _onDonedxtc === 'function') {
                setTimeout(() => {
                    console.log('✅ DXTC: done (only 1 page)');
                    _onDonedxtc();
                }, 30000);
            }
            return;
        }
        _showAndCountdxtc(currentPagedxtc);

        window._dxtcInterval = setInterval(() => {
            currentPagedxtc = (currentPagedxtc + 1) % total;
            _showAndCountdxtc(currentPagedxtc);

            // 🔔 Khi đã chạy đủ số vòng yêu cầu thì báo Orchestrator
            if (_isPlayingdxtc && _pagesRandxtc >= _pageQuotadxtc) {
                clearInterval(window._dxtcInterval);
                window._dxtcInterval = null;
                _isPlayingdxtc = false;

                if (typeof _onDonedxtc === 'function') {
                    console.log('✅ DXTC: auto switch complete, moving to next dashboard...');
                    _onDonedxtc();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchdxtc() {
        if (window._dxtcInterval) {
            clearInterval(window._dxtcInterval);
            window._dxtcInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_dxtc = {
        applyMarquee: () => {
            applyMarqueedxtc();
        },
        play: (pages, onDone) => {
            if (!dxtcPager.getRows().length) dxtcLoadData();
            startAutoSwitchdxtc(pages, onDone)
        },
        pause: () => {
            stopAutoSwitchdxtc();
            _isPlayingdxtc = false;
            _onDonedxtc = null;
            _pageQuotadxtc = Infinity;
            _pagesRandxtc = 0;
        },

        resume: (pages, onDone) => startAutoSwitchdxtc(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesdxtc();
            currentPagedxtc = (currentPagedxtc + 1) % total;
            _showAndCountdxtc(currentPagedxtc);
        },
        getState: () => ({
            currentPagedxtc,
            totalPages: _getTotalPagesdxtc(),
            isPlaying: _isPlayingdxtc
        })
    };
</script>

</html>