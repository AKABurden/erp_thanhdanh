<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_ycc/financial_accounting_ycc_css'); ?>
</head>

<body>
    <div class="app_ycc">
        <!-- HEADER -->
        <div class="header_ycc">
            <div class="logo_ycc">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_ycc">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Phiếu yêu cầu chi chưa xử lý</span>
            </div>
            <div class="header-right_ycc">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_ycc" style="display:flex;gap:14px;">
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
            <section class="table-wrapper_ycc">
                <!-- 👇 Bọc table trong khung scroll -->
                <div class="head">
                    <div class="legend_dxnt">
                        <span class="text-status"><i class="dot_dxnt green_dxnt"></i><span>Chưa duyệt</span></span>
                        <span class="text-status"><i class="dot_dxnt yellow_dxnt"></i><span>Chưa tạo phiếu chi</span></span>
                    </div>
                </div>
                <table class="ycc">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Số phiếu chi</th>
                            <th style="width: 15%;">Ngày chi</th>
                            <th style="width: 19%;">Người lập phiếu</th>
                            <th style="width: 20%;">Nhà cung cấp</th>
                            <th style="width: 18%;">Danh mục chi</th>
                            <th style="width: 15%;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_ycc" id="table-body-ycc" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function yccEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function yccNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function yccFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function yccRowTplkhp(r) {

        const code = yccEsc(r.code || '');
        const cate = yccEsc(r.note_item || '');
        const date = yccEsc(r.date || '');
        const company = yccEsc(r.company || '');
        const proposerImg = (r.proposerImg || '');
        const statusClass = r.status_color || "";

        return `
        <tr data-id="${code}">
            <td style="width: 15%;" title="${code}">${code}</td>
            <td style="width: 19%;" title="${date}">${date}</td>
            <td style="width: 19%;" class="image">${proposerImg}</td>
            <td style="width: 20%;" title="${company}">${company}</td>
            <td style="width: 18%;" title="${cate}">${cate}</td>
            <td style="width: 15%;"><span class="dot_dxnt ${statusClass}"></span></td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createyccPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPageycc() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 100;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        function renderPage(index = 0) {
            const max = getMaxRowsPerPageycc();
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTplkhp(r)));
        }
        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPageycc();
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

        // function startAutoSwitch(interval = 10000) {
        //     if (autoSwitch) clearInterval(autoSwitch);
        //     const max = getMaxRowsPerPageycc();
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
            getMaxRowsPerPageycc
        };
    }

    /* ========= Khởi tạo pager riêng cho ycc ========= */
    const yccPager = createyccPager({
        tbodySelector: "#table-body-ycc",
        rowTplkhp: yccRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function yccUpdateStats(stats) {

        // $(".js-dxtc_success").text(yccFmt(yccNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(yccFmt(yccNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(yccFmt(yccNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(yccFmt(yccNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(yccFmt(yccNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(yccFmt(yccNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(yccFmt(yccNum(stats?.countdxtc), 0));
        // $(".js-count_delivery_thu").text(yccFmt(yccNum(stats?.count_delivery_thu), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function yccLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingycc') ?>", res => {
            if (!res || !res.success) return;
            yccUpdateStats(res.stats || {});
            // const list = Array.isArray(res.ycc) ? res.ycc : [];
            const list = Array.isArray(res.ycc) ? res.ycc.filter(item => item.status_hide != 1) : [];
            yccPager.setRows(list);
        });
    }

    function realtimeyccLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingycc') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.ycc) ? res.ycc.filter(item => item.status_hide != 1) : [];
            yccUpdateStats(res.stats || {});
            list.forEach(row => {
                yccPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = yccPager.getRows().filter(r => ids.includes(r.code));
            yccPager.setRows(filtered);
        });
    }
    setInterval(realtimeyccLoadData, 30000);
    // yccLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        yccPager.renderPage(0);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('ycc:update', (row)=> yccPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function yccStartClock() {
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
    let _isPlayingycc = false;
    let _onDoneycc = null;
    let _pageQuotaycc = Infinity;
    let _pagesRanycc = 0;
    let currentPageycc = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueeycc() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesycc() {
        const max = yccPager.getRows().length > 0 ? yccPager.getMaxRowsPerPageycc() : 1;
        return Math.max(1, Math.ceil(yccPager.getRows().length / max));
    }

    function _showAndCountycc(pageIndex) {
        yccPager.renderPage(pageIndex);
        applyMarqueeycc();
        if (_isPlayingycc) {
            _pagesRanycc++;
            if (_pagesRanycc >= _pageQuotaycc && _onDoneycc) {
                _onDoneycc();
            }
        }
    }

    function startAutoSwitchycc(pages = Infinity, onDone = null) {
        stopAutoSwitchycc();
        _isPlayingycc = true;
        _onDoneycc = onDone;
        _pageQuotaycc = pages;
        _pagesRanycc = 0;

        const total = _getTotalPagesycc();

        if (total < 1) {
            _showAndCountycc(0);
            // 🔔 Nếu chỉ có 1 trang, gọi done sau 2 giây để không kẹt Orchestrator
            if (typeof _onDoneycc === 'function') {
                setTimeout(() => {
                    console.log('✅ YCC: done (only 1 page)');
                    _onDoneycc();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountycc(0);
            // 🔔 Nếu chỉ có 1 trang, gọi done sau 2 giây để không kẹt Orchestrator
            if (typeof _onDoneycc === 'function') {
                setTimeout(() => {
                    console.log('✅ YCC: done (only 1 page)');
                    _onDoneycc();
                }, 30000);
            }
            return;
        }
        _showAndCountycc(currentPageycc);

        window._yccInterval = setInterval(() => {
            currentPageycc = (currentPageycc + 1) % total;
            _showAndCountycc(currentPageycc);

            // 🔔 Khi chạy đủ số vòng → báo Orchestrator để next
            if (_isPlayingycc && _pagesRanycc >= _pageQuotaycc) {
                clearInterval(window._yccInterval);
                window._yccInterval = null;
                _isPlayingycc = false;

                if (typeof _onDoneycc === 'function') {
                    console.log('✅ YCC: auto switch complete, moving to next dashboard...');
                    _onDoneycc();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchycc() {
        if (window._yccInterval) {
            clearInterval(window._yccInterval);
            window._yccInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_ycc = {
        applyMarquee: () => {
            applyMarqueeycc();
        },
        play: (pages, onDone) => {
            if (!yccPager.getRows().length) yccLoadData();
            startAutoSwitchycc(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchycc();
            _isPlayingycc = false;
            _onDoneycc = null;
            _pageQuotaycc = Infinity;
            _pagesRanycc = 0;
        },
        resume: (pages, onDone) => startAutoSwitchycc(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesycc();
            currentPageycc = (currentPageycc + 1) % total;
            _showAndCountycc(currentPageycc);
        },
        getState: () => ({
            currentPageycc,
            totalPages: _getTotalPagesycc(),
            isPlaying: _isPlayingycc
        })
    };
</script>

</html>