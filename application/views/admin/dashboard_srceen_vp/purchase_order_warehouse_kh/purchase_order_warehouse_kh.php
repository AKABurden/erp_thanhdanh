<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/purchase_order_warehouse_kh/purchase_order_warehouse_kh_css'); ?>
</head>

<body>
    <div class="app_puwakh">
        <!-- HEADER -->
        <div class="header_puwakh">
            <div class="logo_puwakh">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_puwakh">
                <span class="main">PHÒNG MUA HÀNG - KHO HÀNG</span><br>
            </div>
            <div class="header-right_puwakh">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_puwakh" style="display:flex;gap:14px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_puwakh">
                <div class="kpi-box_puwakh xanh">
                    <div class="label">Đề xuất nội bộ đã duyệt</div>
                    <div class="value js-dxnb_approved">0</div>
                    <div style="border-bottom:1px solid #00691A;margin:8px 0;"></div>
                    <div class="label">Đề xuất nội bộ chưa duyệt</div>
                    <div class="value js-dxnb_un_approved">0</div>
                </div>
                <div class="kpi-box_puwakh vang">
                    <div class="label">PO đã nhập kho</div>
                    <div class="value js-count_purchase_import">0</div>
                    <div style="border-bottom:1px solid #AF9514;margin:8px 0;"></div>
                    <div class="label">PO chưa nhập kho</div>
                    <div class="value js-count_purchase_not_import ">0</div>
                </div>
                <div class="kpi-box_puwakh tim">
                    <div class="label">Tổng SL thành phẩm đã nhập</div>
                    <div class="value js-count_purchase_import">0</div>
                    <div style="border-bottom:1px solid #4F507F;margin:8px 0;"></div>
                    <div class="label">Tổng SL thành phẩm chưa nhập</div>
                    <div class="value js-count_purchase_not_import ">0</div>
                </div>

            </aside>

            <!-- TABLE -->
            <section class="table-wrapper_puwakh">
                <div class="head">
                    <h2 class="h-title_puwakh">Kế hoạch mua hàng</h2>
                </div>

                <!-- 👇 Bọc table trong khung scroll -->
                <table class="puwakh">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Tên hàng</th>
                            <th style="width: 18%;">Số lượng PO đv chuẩn</th>
                            <th style="width: 13%;">Số lượng PO đơn vị kho</th>
                            <th style="width: 13%;">Tồn được duyệt</th>
                            <th style="width: 13%;">Tồn sẵn</th>
                            <th style="width: 13%;">Số lượng được duyệt</th>
                            <th style="width: 18%;">Nhà cung cấp</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_puwakh" id="table-body-puwakh" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
    function renderPlanningTable_puwakh(rows) {
        const $tbody = $("table.puwakh tbody").empty();
        rows.forEach(r => {
            const tr = `<tr>
                <td>${r.date || ""}</td>
                <td>${r.name_recommended_list || ""}</td>
                <td><span class="dot_puwakh ${r.status_color || ""}"></span></td>
                <td>${r.code || ""}</td>
                <td class="image">
                    ${r.image_employee}
                </td>
                <td><strong>${r.amount || ""}</strong></td>
                <td class="td-progress_puwakh">
                    <div class="timeline_puwakh">
                        ${(r.progress || []).map(step => `
                            <div class="step_puwakh ${step.status}">
                                <div class="dot_puwakh_progress"></div>
                                <div class="content_puwakh">
                                    <div class="title_puwakh">${step.title}</div>
                                    <div class="user_puwakh">
                                        <img src="${step.avatar_url || '<?= base_url('assets/images/user-placeholder.jpg') ?>'}" class="avatar-sm_puwakh">
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

    function updatePlanningStats_puwakh(stats) {
        // Cập nhật text số liệu
        $(".js-puwakh_success").text(formatNumber(numVal(stats.status_finish_1), 0));
        $(".js-puwakh_pending").text(formatNumber(numVal(stats.status_finish_0), 0));
    }

    function loadPlanningData_puwakh() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingDXNB') ?>", res => {
            if (!res || !res.success) return;

            // cập nhật thống kê
            updatePlanningStats_puwakh(res.stats);

            // vẽ bảng
            renderPlanningTable_puwakh(res.internal_proposal);
        });
    }
    loadPlanningData_puwakh()
</script>
<script>
    // Đồng hồ realtime theo Asia/Ho_Chi_Minh
    (function startRealtimeClock_puwakh() {
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
    function puwakhEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function puwakhNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function puwakhFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function puwakhRowTpl(r) {
        const code = puwakhEsc(r.code || r.reference_no || '');
        const date = puwakhEsc(r.date || '');
        const type = puwakhEsc(r.name_recommended_list || '');
        const amount = puwakhEsc(r.amount || r.money || '');
        const proposerImg = r.image_employee || '';
        const statusClass = r.status_color || '';

        const steps = Array.isArray(r.progress) ? r.progress : [];
        const stepsHtml = steps.map(step => {
            const title = puwakhEsc(step.title || '');
            const user = puwakhEsc(step.user || '');
            const avatar = puwakhEsc(step.avatar_url || '<?= base_url('assets/images/user-placeholder.jpg') ?>');
            const st = puwakhEsc(step.status || '');
            return `
                <div class="step_puwakh ${st}">
                    <div class="dot_puwakh_progress"></div>
                    <div class="content_puwakh">
                        <div class="title_puwakh">${title}</div>
                        <div class="user_puwakh">
                            <img src="${avatar}" class="avatar-sm_puwakh"> ${user}
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
            <td style="width: 13%;"><span class="dot_puwakh ${statusClass}"></span></td>
            <td style="width: 13%;" title="${code}">${code}</td>
            <td style="width: 13%;" class="image">${proposerImg}</td>
            <td style="width: 13%;"><strong>${amount}</strong></td>
            <td style="width: 18%;" class="td-progress_puwakh"><div class="timeline_puwakh">${stepsHtml}</div></td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createpuwakhPager({
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
            $tbody.addClass("hidden_pkd");
            setTimeout(() => {
                $tbody.empty();
                slice.forEach(r => $tbody.append(rowTpl(r)));
                $tbody.removeClass("hidden_pkd");
            }, 120);
        }

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

    /* ========= Khởi tạo pager riêng cho puwakh ========= */
    const puwakhPager = createpuwakhPager({
        tbodySelector: "#table-body-puwakh",
        rowTpl: puwakhRowTpl
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function puwakhUpdateStats(stats) {
        // $(".js-puwakh_success").text(puwakhFmt(puwakhNum(stats?.status_finish_1), 0));
        // $(".js-puwakh_pending").text(puwakhFmt(puwakhNum(stats?.status_finish_0), 0));
        // $(".js-puwakh_code_custom_null").text(puwakhFmt(puwakhNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(puwakhFmt(puwakhNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(puwakhFmt(puwakhNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(puwakhFmt(puwakhNum(stats?.count_ycc), 0));
        // $(".js-count_puwakh").text(puwakhFmt(puwakhNum(stats?.countpuwakh), 0));
        // $(".js-count_delivery_thu").text(puwakhFmt(puwakhNum(stats?.count_delivery_thu), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function puwakhLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingDXNB') ?>", res => {
            if (!res || !res.success) return;
            puwakhUpdateStats(res.stats || {});
            const list = Array.isArray(res.internal_proposal) ? res.internal_proposal : [];
            puwakhPager.setRows(list);
            // puwakhPager.startAutoSwitch(10000);
        });
    }
    puwakhLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        puwakhPager.renderPage(0);
        // puwakhPager.startAutoSwitch(10000);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('puwakh:update', (row)=> puwakhPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function puwakhStartClock() {
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
    let _isPlayingpuwakh = false;
    let _onDonepuwakh = null;
    let _pageQuotapuwakh = Infinity;
    let _pagesRanpuwakh = 0;
    let currentPagepuwakh = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueepuwakh() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagespuwakh_new() {
        const max = puwakhPager.getRows().length > 0 ? puwakhPager.getMaxRowsPerPage() : 1;

        return Math.max(1, Math.ceil(puwakhPager.getRows().length / max));
    }

    function _showAndCountpuwakh(pageIndex) {
        puwakhPager.renderPage(pageIndex);
        applyMarqueepuwakh();
        if (_isPlayingpuwakh) {
            _pagesRanpuwakh++;
            if (_pagesRanpuwakh >= _pageQuotapuwakh && _onDonepuwakh) {
                _onDonepuwakh();
            }
        }
    }

    function startAutoSwitchpuwakh(pages = Infinity, onDone = null) {
        stopAutoSwitchpuwakh();
        _isPlayingpuwakh = true;
        _onDonepuwakh = onDone;
        _pageQuotapuwakh = pages;
        _pagesRanpuwakh = 0;

        const total = _getTotalPagespuwakh_new();

        if (total < 1) {
            _showAndCountpuwakh(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây để không kẹt Orchestrator
            if (typeof _onDonepuwakh === 'function') {
                setTimeout(() => {
                    console.log('✅ PUWAKH: done (only 1 page)');
                    _onDonepuwakh();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountpuwakh(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây để không kẹt Orchestrator
            if (typeof _onDonepuwakh === 'function') {
                setTimeout(() => {
                    console.log('✅ PUWAKH: done (only 1 page)');
                    _onDonepuwakh();
                }, 30000);
            }
            return;
        }
        _showAndCountpuwakh(currentPagepuwakh);

        window._puwakhInterval = setInterval(() => {
            currentPagepuwakh = (currentPagepuwakh + 1) % total;
            _showAndCountpuwakh(currentPagepuwakh);

            // 🔔 Khi đã hiển thị đủ số vòng (được định nghĩa trong Orchestrator)
            if (_isPlayingpuwakh && _pagesRanpuwakh >= _pageQuotapuwakh) {
                clearInterval(window._puwakhInterval);
                window._puwakhInterval = null;
                _isPlayingpuwakh = false;

                if (typeof _onDonepuwakh === 'function') {
                    console.log('✅ PUWAKH: auto switch complete, moving to next dashboard...');
                    _onDonepuwakh();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchpuwakh() {
        if (window._puwakhInterval) {
            clearInterval(window._puwakhInterval);
            window._puwakhInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.puwakhDash = {
        applyMarquee: () => {
            applyMarqueepuwakh();
        },
        play: (pages, onDone) => {
            // if (!puwakhPager.getRows().length) puwakhLoadData();
            startAutoSwitchpuwakh(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchpuwakh();
            _isPlayingpuwakh = false;
            _onDonepuwakh = null;
            _pageQuotapuwakh = Infinity;
            _pagesRanpuwakh = 0;
        },
        resume: (pages, onDone) => startAutoSwitchpuwakh(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagespuwakh_new();
            currentPagepuwakh = (currentPagepuwakh + 1) % total;
            _showAndCountpuwakh(currentPagepuwakh);
        },
        getState: () => ({
            currentPagepuwakh,
            totalPages: _getTotalPagespuwakh_new(),
            isPlaying: _isPlayingpuwakh
        })
    };
</script>

</html>