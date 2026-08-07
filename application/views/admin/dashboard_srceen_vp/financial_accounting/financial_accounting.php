<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting/financial_accounting_css'); ?>
</head>

<body>
    <div class="app_dxnt">
        <!-- HEADER -->
        <div class="header_dxnt">
            <div class="logo_dxnt">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_dxnt">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Đề xuất nội bộ</span>
            </div>
            <div class="header-right_dxnt">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_dxnt" style="display:flex;gap:14px;">
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
            <section class="table-wrapper_dxnt">
                <div class="head">
                    <div class="legend_dxnt">
                        <span class="text-status"><i class="dot_dxnt green_dxnt"></i><span>Đã duyệt/Đã mở</span></span>
                        <span class="text-status"><i class="dot_dxnt red_dxnt"></i><span>Quá hạn</span></span>
                        <span class="text-status"><i class="dot_dxnt yellow_dxnt"></i><span>Chờ duyệt/Chờ mở</span></span>
                    </div>
                </div>

                <!-- 👇 Bọc table trong khung scroll -->
                <table class="dxnt">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Ngày</th>
                            <th style="width: 18%;">Loại đề xuất</th>
                            <th style="width: 13%;">Trạng thái</th>
                            <th style="width: 13%;">Mã công việc</th>
                            <th style="width: 13%;">Người đề xuất</th>
                            <th style="width: 13%;">Số tiền</th>
                            <th style="width: 18%;">Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_dxnt" id="table-body-dxnt" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
    function renderPlanningTable_dxnt(rows) {
        const $tbody = $("table.dxnt tbody").empty();
        rows.forEach(r => {
            const tr = `<tr>
                <td>${r.date || ""}</td>
                <td>${r.name_recommended_list || ""}</td>
                <td><span class="dot_dxnt ${r.status_color || ""}"></span></td>
                <td>${r.code || ""}</td>
                <td class="image">
                    ${r.image_employee}
                </td>
                <td><strong>${r.amount || ""}</strong></td>
                <td class="td-progress_dxnt">
                    <div class="timeline_dxnt">
                        ${(r.progress || []).map(step => `
                            <div class="step_dxnt ${step.status}">
                                <div class="dot_dxnt_progress"></div>
                                <div class="content_dxnt">
                                    <div class="title_dxnt">${step.title}</div>
                                    <div class="user_dxnt">
                                        <img src="${step.avatar_url || '<?= base_url('assets/images/user-placeholder.jpg') ?>'}" class="avatar-sm_dxnt">
                                        ${step.user}
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </td>
            </tr>`;
            $tbody.append(tr);
        });
    }

    function updatePlanningStats_dxnt(stats) {
        // Cập nhật text số liệu
        $(".js-dxnt_success").text(formatNumber(numVal(stats.status_finish_1), 0));
        $(".js-dxnt_pending").text(formatNumber(numVal(stats.status_finish_0), 0));
    }

    function loadPlanningData_dxnt() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingDXNB') ?>", res => {
            if (!res || !res.success) return;

            // cập nhật thống kê
            updatePlanningStats_dxnt(res.stats);

            // vẽ bảng
            renderPlanningTable_dxnt(res.internal_proposal);
        });
    }
    loadPlanningData_dxnt()
</script>
<script>
    // Đồng hồ realtime theo Asia/Ho_Chi_Minh
    (function startRealtimeClock_dxnt() {
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
</script> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function dxntEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function dxntNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function dxntFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function dxntRowTpl(r) {
        const code = dxntEsc(r.code || r.reference_no || '');
        const date = dxntEsc(r.date || '');
        const type = dxntEsc(r.name_recommended_list || '');
        const amount = (dxntNum(r.amount) > 0) ? dxntEsc(r.amount || r.money || '') + 'đ' : '-';
        const proposerImg = r.image_employee || '';
        const statusClass = r.status_color || '';

        const steps = Array.isArray(r.progress) ? r.progress : [];
        const stepsHtml = steps.map((step, idx) => {
            const title = dxntEsc(step.title || '');
            const user = dxntEsc(step.user || '');
            const avatarUrl = step.avatar_url || '<?= base_url('assets/images/user-placeholder.jpg') ?>';
            const st = dxntEsc(step.status || '');
            // Nếu là dòng thứ 2 thì thêm màu đỏ cho user
            const userStyle = idx === 1 ? 'style="font-size:18px;"' : 'style="font-size:14px;"';
            return `
            <div class="step_dxnt ${st}">
            <div class="dot_dxnt_progress"></div>
            <div class="content_dxnt">
                <div class="title_dxnt" ${userStyle}>${title}</div>
                <div class="user_dxnt" ${userStyle}>
                <img src="${avatarUrl}" class="avatar-sm_dxnt" alt="avatar">
                ${user}
                </div>
            </div>
            </div>`;
        }).join('');
        //   <th style="width: 8%;">Ngày</th>
        //                             <th style="width: 18%;">Loại đề xuất</th>
        //                             <th style="width: 13%;">Trạng thái</th>
        //                             <th style="width: 13%;">Mã công việc</th>
        //                             <th style="width: 13%;">Người đề xuất</th>
        //                             <th style="width: 13%;">Số tiền</th>
        //                             <th style="width: 22%;">Tiến độ</th>
        return `
        <tr data-id="${code}">
            <td style="width: 8%;" title="${date}">${date}</td>
            <td style="width: 18%;" title="${type}">${type}</td>
            <td style="width: 13%;"><span class="dot_dxnt ${statusClass}"></span></td>
            <td style="width: 13%;" title="${code}">${code}</td>
            <td style="width: 13%;font-size:18px;" class="image">${proposerImg}</td>
            <td style="width: 13%;color:red;"><strong>${amount}</strong></td>
            <td style="width: 18%;" class="td-progress_dxnt"><div class="timeline_dxnt">${stepsHtml}</div></td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createDXNTPager({
        tbodySelector,
        rowTpl
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPage() {
            let parentH = $tbody.parent().height() || 480;
            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 70;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        function renderPage(index = 0) {
            const max = getMaxRowsPerPage() > 5 ? getMaxRowsPerPage() : 5;
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTpl(r)));
        }
        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPage() > 5 ? getMaxRowsPerPage() : 5;
        //     const totalPages = Math.max(1, Math.ceil(rows.length / max));
        //     currentPage = Math.min(index, totalPages - 1);
        //     const start = currentPage * max;
        //     const slice = rows.slice(start, start + max);
        //     $tbody.addClass("hidden_pkd");
        //     setTimeout(() => {
        //         $tbody.empty();
        //         slice.forEach(r => $tbody.append(rowTpl(r)));
        //         $tbody.removeClass("hidden_pkd");
        //     }, 120);
        // }

        function startAutoSwitch(interval = 10000) {
            if (autoSwitch) clearInterval(autoSwitch);
            const max = getMaxRowsPerPage();
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
            $tr.html($(rowTpl(row)).html());
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
            getMaxRowsPerPage
        };
    }

    /* ========= Khởi tạo pager riêng cho DXNT ========= */
    const dxntPager = createDXNTPager({
        tbodySelector: "#table-body-dxnt",
        rowTpl: dxntRowTpl
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function dxntUpdateStats(stats) {
        // $(".js-dxtc_success").text(dxntFmt(dxntNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(dxntFmt(dxntNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(dxntFmt(dxntNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(dxntFmt(dxntNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(dxntFmt(dxntNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(dxntFmt(dxntNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(dxntFmt(dxntNum(stats?.countdxtc), 0));
        // $(".js-count_delivery_thu").text(dxntFmt(dxntNum(stats?.count_delivery_thu), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function dxntLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingDXNB') ?>", res => {
            if (!res || !res.success) return;
            dxntUpdateStats(res.stats || {});
            const list = Array.isArray(res.internal_proposal) ? res.internal_proposal : [];
            dxntPager.setRows(list);
            // dxntPager.startAutoSwitch(10000);
        });
    }

    function realtimedxntLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingDXNB') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.internal_proposal) ? res.internal_proposal : [];
            list.forEach(row => {
                dxntPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = dxntPager.getRows().filter(r => ids.includes(r.code));
            dxntPager.setRows(filtered);
        });
    }
    setInterval(realtimedxntLoadData, 30000);
    // dxntLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        dxntPager.renderPage(0);
        // dxntPager.startAutoSwitch(10000);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('dxnt:update', (row)=> dxntPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function dxntStartClock() {
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
    let _isPlayingDxnt = false;
    let _onDoneDxnt = null;
    let _pageQuotaDxnt = Infinity;
    let _pagesRanDxnt = 0;
    let currentPageDxnt = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueeDxnt() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesDxnt_new() {
        const max = dxntPager.getRows().length > 0 ? dxntPager.getMaxRowsPerPage() : 1;

        return Math.max(1, Math.ceil(dxntPager.getRows().length / max));
    }

    function _showAndCountDxnt(pageIndex) {
        dxntPager.renderPage(pageIndex);
        applyMarqueeDxnt();

        // if (_isPlayingDxnt) {
        //     _pagesRanDxnt++;

        //     // 🔹 Khi đã hiển thị đủ số trang yêu cầu (ví dụ 2 trang)
        //     if (_pagesRanDxnt >= _pageQuotaDxnt) {
        //         console.log(`✅ DXNT: Đã hiển thị ${_pagesRanDxnt} / ${_pageQuotaDxnt} trang → chuyển dashboard tiếp theo`);

        //         // Dừng auto-switch để tránh bị lặp vô hạn
        //         stopAutoSwitchDxnt();

        //         // Gọi callback Orchestrator để chuyển sang dashboard kế tiếp
        //         if (typeof _onDoneDxnt === 'function') {
        //             _onDoneDxnt();
        //         }

        //         _isPlayingDxnt = false;
        //     }
        // }
    }


    function startAutoSwitchDxnt(pages = Infinity, onDone = null) {
        stopAutoSwitchDxnt();
        _isPlayingDxnt = true;
        _onDoneDxnt = onDone;
        _pageQuotaDxnt = pages;
        _pagesRanDxnt = 0;

        const total = _getTotalPagesDxnt_new();
        console.log('total: ', total);
        if (total < 1) {
            _showAndCountDxnt(0);
            // 🔔 nếu chỉ có 1 trang thì gọi done luôn sau 2 giây
            if (typeof _onDoneDxnt === 'function') {
                setTimeout(_onDoneDxnt, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountDxnt(0);
            // 🔔 nếu chỉ có 1 trang thì gọi done luôn sau 2 giây
            if (typeof _onDoneDxnt === 'function') {
                setTimeout(_onDoneDxnt, 30000);
            }
            return;
        }

        _showAndCountDxnt(currentPageDxnt);
        console.log('currentPageDxnt1: ', currentPageDxnt);

        window._dxntInterval = setInterval(() => {
            currentPageDxnt = (currentPageDxnt + 1) % total;
            _showAndCountDxnt(currentPageDxnt);
            _pagesRanDxnt++;
            console.log('currentPageDxnt2: ', currentPageDxnt);

            // 🔔 Nếu đã đủ số lượt (pageQuotaDxnt) thì gọi done
            if (_isPlayingDxnt && _pagesRanDxnt >= _pageQuotaDxnt) {
                clearInterval(window._dxntInterval);
                window._dxntInterval = null;
                _isPlayingDxnt = false;
                if (typeof _onDoneDxnt === 'function') {
                    _onDoneDxnt();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchDxnt() {
        if (window._dxntInterval) {
            clearInterval(window._dxntInterval);
            window._dxntInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.dxntDash = {
        applyMarquee: () => {
            applyMarqueeDxnt();
        },
        play: (pages, onDone) => {
            const hasRows = dxntPager.getRows().length > 0;

            if (hasRows) {
                // ✅ Có sẵn dữ liệu → chạy ngay
                startAutoSwitchDxnt(pages, onDone);
            } else {
                // ❌ Chưa có dữ liệu → chờ load xong rồi mới chạy
                dxntLoadData();
                // Đợi AJAX trả về (vì load là async)
                const checkLoaded = setInterval(() => {
                    if (dxntPager.getRows().length > 0) {
                        clearInterval(checkLoaded);
                        startAutoSwitchDxnt(pages, onDone);
                    }
                }, 300);
            }
        },
        pause: () => {
            stopAutoSwitchDxnt();
            _isPlayingDxnt = false;
            _onDoneDxnt = null;
            _pageQuotaDxnt = Infinity;
            _pagesRanDxnt = 0;
        },
        resume: (pages, onDone) => startAutoSwitchDxnt(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesDxnt_new();
            currentPageDxnt = (currentPageDxnt + 1) % total;
            _showAndCountDxnt(currentPageDxnt);
        },
        getState: () => ({
            currentPageDxnt,
            totalPages: _getTotalPagesDxnt_new(),
            isPlaying: _isPlayingDxnt
        })
    };
</script>

</html>