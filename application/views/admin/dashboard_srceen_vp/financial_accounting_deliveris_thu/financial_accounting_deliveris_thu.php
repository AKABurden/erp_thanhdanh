<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_deliveris_thu/financial_accounting_deliveris_thu_css'); ?>
</head>

<body>
    <div class="app_thu">
        <!-- HEADER -->
        <div class="header_thu">
            <div class="logo_thu">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_thu">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Thu tiền phiếu giao hàng</span>
            </div>
            <div class="header-right_thu">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_thu" style="display:flex;gap:14px;">
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
            <section class="table-wrapper_thu">
                <!-- 👇 Bọc table trong khung scroll -->
                <table class="thu">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Số phiếu giao</th>
                            <th style="width: 18%;">Ngày giao</th>
                            <th style="width: 18%;">Khách hàng</th>
                            <th style="width: 28%;">Số đơn hàng</th>
                            <th style="width: 18%;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_thu" id="table-body-thu" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function thuEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function thuNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function thuFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function thuRowTplkhp(r) {

        const code = khqEsc(r.code || r.reference_no || '');
        const date = khqEsc(r.date || '');
        const company = khqEsc(r.company || '');
        const order_reference_no = khqEsc(r.order_reference_no || '');
        const status_payment_text = (r.status_payment_text || '');
        const status_payment = (r.status_payment || '');
        return `
        <tr data-id="${code}">
            <td style="width: 22%;" title="${code}">${code}</td>
            <td style="width: 22%;" title="${date}">${date}</td>
            <td style="width: 34%;" title="${company}">${company}</td>
            <td style="width: 22%;" title="${order_reference_no}">${order_reference_no}</td>
            <td style="width: 22%;${status_payment == 0 ? 'color:red;' : ''}" title="${status_payment_text}">${status_payment_text}</td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createthuPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPagethu() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 60;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        function renderPage(index = 0) {
            const max = getMaxRowsPerPagethu() > 10 ? getMaxRowsPerPagethu() : 10;
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTplkhp(r)));
        }
        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPagethu() > 10 ? getMaxRowsPerPagethu() : 10;
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

        function startAutoSwitch(interval = 10000) {
            if (autoSwitch) clearInterval(autoSwitch);
            const max = getMaxRowsPerPagethu();
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
            getMaxRowsPerPagethu
        };
    }

    /* ========= Khởi tạo pager riêng cho thu ========= */
    const thuPager = createthuPager({
        tbodySelector: "#table-body-thu",
        rowTplkhp: thuRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function thuUpdateStats(stats) {
        // $(".js-dxtc_success").text(thuFmt(thuNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(thuFmt(thuNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(thuFmt(thuNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(thuFmt(thuNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(thuFmt(thuNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(thuFmt(thuNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(thuFmt(thuNum(stats?.countdxtc), 0));
        // $(".js-count_delivery_thu").text(thuFmt(thuNum(stats?.count_delivery_thu), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function thuLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingthu') ?>", res => {
            if (!res || !res.success) return;
            thuUpdateStats(res.stats || {});
            const list = Array.isArray(res.deliveriescx) ? res.deliveriescx : [];
            thuPager.setRows(list);
        });
    }

    function realtimethuLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingthu') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.deliveriescx) ? res.deliveriescx : [];
            list.forEach(row => {
                thuPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = thuPager.getRows().filter(r => ids.includes(r.code));
            thuPager.setRows(filtered);
        });
    }
    setInterval(realtimethuLoadData, 30000);
    // thuLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        thuPager.renderPage(0);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('thu:update', (row)=> thuPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function thuStartClock() {
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
    let _isPlayingthu = false;
    let _onDonethu = null;
    let _pageQuotathu = Infinity;
    let _pagesRanthu = 0;
    let currentPagethu = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueethu() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesthu() {
        const max = thuPager.getRows().length > 0 ? thuPager.getMaxRowsPerPagethu() : 1;
        return Math.max(1, Math.ceil(thuPager.getRows().length / max));
    }

    function _showAndCountthu(pageIndex) {
        thuPager.renderPage(pageIndex);
        applyMarqueethu();
        if (_isPlayingthu) {
            _pagesRanthu++;
            if (_pagesRanthu >= _pageQuotathu && _onDonethu) {
                _onDonethu();
            }
        }
    }

    function startAutoSwitchthu(pages = Infinity, onDone = null) {
        stopAutoSwitchthu();
        _isPlayingthu = true;
        _onDonethu = onDone;
        _pageQuotathu = pages;
        _pagesRanthu = 0;

        const total = _getTotalPagesthu();

        if (total < 1) {
            _showAndCountthu(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDonethu === 'function') {
                setTimeout(() => {
                    console.log('✅ THU: done (only 1 page)');
                    _onDonethu();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountthu(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDonethu === 'function') {
                setTimeout(() => {
                    console.log('✅ THU: done (only 1 page)');
                    _onDonethu();
                }, 30000);
            }
            return;
        }
        _showAndCountthu(currentPagethu);

        window._thuInterval = setInterval(() => {
            currentPagethu = (currentPagethu + 1) % total;
            _showAndCountthu(currentPagethu);

            // 🔔 Khi đã đủ số vòng thì gọi callback cho Orchestrator
            if (_isPlayingthu && _pagesRanthu >= _pageQuotathu) {
                clearInterval(window._thuInterval);
                window._thuInterval = null;
                _isPlayingthu = false;

                if (typeof _onDonethu === 'function') {
                    console.log('✅ THU: auto switch complete, moving to next dashboard...');
                    _onDonethu();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchthu() {
        if (window._thuInterval) {
            clearInterval(window._thuInterval);
            window._thuInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_deliveris_thu = {
        applyMarquee: () => {
            applyMarqueethu();
        },
        play: (pages, onDone) => {
            if (!thuPager.getRows().length) thuLoadData();
            startAutoSwitchthu(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchthu();
            _isPlayingthu = false;
            _onDonethu = null;
            _pageQuotathu = Infinity;
            _pagesRanthu = 0;
        },
        resume: (pages, onDone) => startAutoSwitchthu(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesthu();
            currentPagethu = (currentPagethu + 1) % total;
            _showAndCountthu(currentPagethu);
        },
        getState: () => ({
            currentPagethu,
            totalPages: _getTotalPagesthu(),
            isPlaying: _isPlayingthu
        })
    };
</script>

</html>