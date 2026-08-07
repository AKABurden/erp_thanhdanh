<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_ncc_contract/financial_accounting_ncc_contract_css'); ?>
</head>

<body>
    <div class="appncc_contract">
        <!-- HEADER -->
        <div class="headerncc_contract">
            <div class="logoncc_contract">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-titlencc_contract">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Nhà cung cấp tái ký hợp đồng</span>
            </div>
            <div class="header-rightncc_contract">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="containerncc_contract" style="display:flex;gap:14px;">
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
            <section class="table-wrapperncc_contract">
                <!-- 👇 Bọc table trong khung scroll -->
                <table class="ncc_contract">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Mã hợp đồng</th>
                            <th style="width: 28%;">Tên nhà cung cấp</th>
                            <th style="width: 18%;">Ngày bắt đầu hợp đồng</th>
                            <th style="width: 18%;color:red">Ngày kết thúc hợp đồng</th>
                            <th style="width: 18%;color:red">Số ngày trễ</th>
                        </tr>
                    </thead>
                    <tbody class="table-bodyncc_contract" id="table-body-ncc_contract" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function ncc_contractEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function ncc_contractNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function ncc_contractFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function ncc_contractRowTplkhp(r) {

        const code = khqEsc(r.code || r.reference_no || '');
        const date_start = khqEsc(r.date_start || '');
        const date_end = khqEsc(r.date_end || '');
        const company = khqEsc(r.company || '');
        const order_reference_no = khqEsc(r.order_reference_no || '');
        const status_payment_text = (r.status_payment_text || '');
        const today = new Date();
        // Chuyển đổi ngày kết thúc hợp đồng từ dạng dd/mm/yyyy sang Date object
        let endDate = null;
        if (r.date_end) {
            const parts = r.date_end.split('/');
            if (parts.length === 3) {
            // parts[2]: năm, parts[1]: tháng, parts[0]: ngày
            endDate = new Date(parts[2], parts[1] - 1, parts[0]);
            } else {
            endDate = new Date(r.date_end);
            }
        }
        let lateDays = '';
        if (endDate && today > endDate) {
            const diffTime = today - endDate;
            lateDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + ' Ngày';
        } else {
            lateDays = '-';
        }
        return `
        <tr data-id="${code}">
            <td style="width: 18%;" title="${code}">${code}</td>
            <td style="width: 28%;" title="${company}">${company}</td>
            <td style="width: 18%;" title="${date_start}">${date_start}</td>
            <td style="width: 18%;color:red" title="${date_end}">${date_end}</td>
            <td style="width: 18%;color:red" title="${lateDays}">${lateDays}</td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createncc_contractPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPagencc_contract() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 60;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        function renderPage(index = 0) {
            const max = getMaxRowsPerPagencc_contract() > 10 ? getMaxRowsPerPagencc_contract() : 10;
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTplkhp(r)));
        }

        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPagencc_contract() > 10 ? getMaxRowsPerPagencc_contract() : 10;
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
            const max = getMaxRowsPerPagencc_contract();
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
            getMaxRowsPerPagencc_contract
        };
    }

    /* ========= Khởi tạo pager riêng cho ncc_contract ========= */
    const ncc_contractPager = createncc_contractPager({
        tbodySelector: "#table-body-ncc_contract",
        rowTplkhp: ncc_contractRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function ncc_contractUpdateStats(stats) {
        // $(".js-dxtc_success").text(ncc_contractFmt(ncc_contractNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(ncc_contractFmt(ncc_contractNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(ncc_contractFmt(ncc_contractNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(ncc_contractFmt(ncc_contractNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(ncc_contractFmt(ncc_contractNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(ncc_contractFmt(ncc_contractNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(ncc_contractFmt(ncc_contractNum(stats?.countdxtc), 0));
        // $(".js-count_deliveryncc_contract").text(ncc_contractFmt(ncc_contractNum(stats?.count_deliveryncc_contract), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function ncc_contractLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingncc_contract') ?>", res => {
            if (!res || !res.success) return;
            ncc_contractUpdateStats(res.stats || {});
            const list = Array.isArray(res.contracts_supplier) ? res.contracts_supplier : [];
            ncc_contractPager.setRows(list);
        });
    }

    function realtimencc_contractLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingncc_contract') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.contracts_supplier) ? res.contracts_supplier : [];
            ncc_contractUpdateStats(res.stats || {});
            list.forEach(row => {
                ncc_contractPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = ncc_contractPager.getRows().filter(r => ids.includes(r.code));
            ncc_contractPager.setRows(filtered);
        });
    }
    setInterval(realtimencc_contractLoadData, 30000);
    // ncc_contractLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        ncc_contractPager.renderPage(0);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('ncc_contract:update', (row)=> ncc_contractPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function ncc_contractStartClock() {
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
    let _isPlayingncc_contract = false;
    let _onDonencc_contract = null;
    let _pageQuotancc_contract = Infinity;
    let _pagesRanncc_contract = 0;
    let currentPagencc_contract = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueencc_contract() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesncc_contract() {
        const max = ncc_contractPager.getRows().length > 0 ? ncc_contractPager.getMaxRowsPerPagencc_contract() : 1;
        return Math.max(1, Math.ceil(ncc_contractPager.getRows().length / max));
    }

    function _showAndCountncc_contract(pageIndex) {
        ncc_contractPager.renderPage(pageIndex);
        applyMarqueencc_contract();
        if (_isPlayingncc_contract) {
            _pagesRanncc_contract++;
            if (_pagesRanncc_contract >= _pageQuotancc_contract && _onDonencc_contract) {
                _onDonencc_contract();
            }
        }
    }

    function startAutoSwitchncc_contract(pages = Infinity, onDone = null) {
        stopAutoSwitchncc_contract();
        _isPlayingncc_contract = true;
        _onDonencc_contract = onDone;
        _pageQuotancc_contract = pages;
        _pagesRanncc_contract = 0;

        const total = _getTotalPagesncc_contract();

        if (total < 1) {
            _showAndCountncc_contract(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất sau 2 giây
            if (typeof _onDonencc_contract === 'function') {
                setTimeout(() => {
                    console.log('✅ NCC_CONTRACT: done (only 1 page)');
                    _onDonencc_contract();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountncc_contract(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất sau 2 giây
            if (typeof _onDonencc_contract === 'function') {
                setTimeout(() => {
                    console.log('✅ NCC_CONTRACT: done (only 1 page)');
                    _onDonencc_contract();
                }, 30000);
            }
            return;
        }
        _showAndCountncc_contract(currentPagencc_contract);

        window.ncc_contractInterval = setInterval(() => {
            currentPagencc_contract = (currentPagencc_contract + 1) % total;
            _showAndCountncc_contract(currentPagencc_contract);

            // 🔔 Khi đã đủ số vòng chạy thì dừng và báo Orchestrator
            if (_isPlayingncc_contract && _pagesRanncc_contract >= _pageQuotancc_contract) {
                clearInterval(window.ncc_contractInterval);
                window.ncc_contractInterval = null;
                _isPlayingncc_contract = false;

                if (typeof _onDonencc_contract === 'function') {
                    console.log('✅ NCC_CONTRACT: auto switch complete, moving to next dashboard...');
                    _onDonencc_contract();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchncc_contract() {
        if (window.ncc_contractInterval) {
            clearInterval(window.ncc_contractInterval);
            window.ncc_contractInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_deliverisncc_contract = {
        applyMarquee: () => {
            applyMarqueencc_contract();
        },
        play: (pages, onDone) => {
            if (!ncc_contractPager.getRows().length) ncc_contractLoadData();
            startAutoSwitchncc_contract(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchncc_contract();
            _isPlayingncc_contract = false;
            _onDonencc_contract = null;
            _pageQuotancc_contract = Infinity;
            _pagesRanncc_contract = 0;
        },
        resume: (pages, onDone) => startAutoSwitchncc_contract(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesncc_contract();
            currentPagencc_contract = (currentPagencc_contract + 1) % total;
            _showAndCountncc_contract(currentPagencc_contract);
        },
        getState: () => ({
            currentPagencc_contract,
            totalPages: _getTotalPagesncc_contract(),
            isPlaying: _isPlayingncc_contract
        })
    };
</script>

</html>