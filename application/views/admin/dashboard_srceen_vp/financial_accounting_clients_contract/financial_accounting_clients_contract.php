<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_clients_contract/financial_accounting_clients_contract_css'); ?>
</head>

<body>
    <div class="appclient_contract">
        <!-- HEADER -->
        <div class="headerclient_contract">
            <div class="logoclient_contract">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-titleclient_contract">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Khách hàng tái ký hợp đồng</span>
            </div>
            <div class="header-rightclient_contract">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="containerclient_contract" style="display:flex;gap:14px;">
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
            <section class="table-wrapperclient_contract">
                <!-- 👇 Bọc table trong khung scroll -->
                <table class="client_contract">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Mã hợp đồng</th>
                            <th style="width: 28%;">Khách hàng</th>
                            <th style="width: 18%;">Ngày bắt đầu hợp đồng</th>
                            <th style="width: 18%;color:red">Ngày kết thúc hợp đồng</th>
                            <th style="width: 18%;color:red">Số ngày trễ</th>
                        </tr>
                    </thead>
                    <tbody class="table-bodyclient_contract" id="table-body-client_contract" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function client_contractEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function client_contractNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function client_contractFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function client_contractRowTplkhp(r) {

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
    function createclient_contractPager({
        tbodySelector,
        rowTplkhp
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPageclient_contract() {
            let parentH = $tbody.parent().height() || 480;

            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 12) rowH = 60;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPageclient_contract() > 10 ? getMaxRowsPerPageclient_contract() : 10;
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
            const max = getMaxRowsPerPageclient_contract() > 10 ? getMaxRowsPerPageclient_contract() : 10;
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTplkhp(r)));
        }

        function startAutoSwitch(interval = 10000) {
            if (autoSwitch) clearInterval(autoSwitch);
            const max = getMaxRowsPerPageclient_contract();
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
            getMaxRowsPerPageclient_contract
        };
    }

    /* ========= Khởi tạo pager riêng cho client_contract ========= */
    const client_contractPager = createclient_contractPager({
        tbodySelector: "#table-body-client_contract",
        rowTplkhp: client_contractRowTplkhp
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function client_contractUpdateStats(stats) {
        // $(".js-dxtc_success").text(client_contractFmt(client_contractNum(stats?.status_finish_1), 0));
        // $(".js-dxtc_pending").text(client_contractFmt(client_contractNum(stats?.status_finish_0), 0));
        // $(".js-dxtc_code_custom_null").text(client_contractFmt(client_contractNum(stats?.count_code_custom_null), 0));
        // $(".js-count_delivery_supplier_code_null").text(client_contractFmt(client_contractNum(stats?.count_delivery_supplier_code_null), 0));
        // $(".js-count_delivery_cx").text(client_contractFmt(client_contractNum(stats?.count_delivery_cx), 0));
        // $(".js-count_ycc").text(client_contractFmt(client_contractNum(stats?.count_ycc), 0));
        // $(".js-count_dxtc").text(client_contractFmt(client_contractNum(stats?.countdxtc), 0));
        // $(".js-count_deliveryclient_contract").text(client_contractFmt(client_contractNum(stats?.count_deliveryclient_contract), 0));
    }

    /* ========= Load data (đặt tên riêng) ========= */
    function client_contractLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingclient_contract') ?>", res => {
            if (!res || !res.success) return;
            client_contractUpdateStats(res.stats || {});
            const list = Array.isArray(res.contracts_clients) ? res.contracts_clients : [];
            client_contractPager.setRows(list);
        });
    }

    function realtimeclient_contractLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updatefinancialAccountingclient_contract') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.contracts_clients) ? res.contracts_clients : [];
            list.forEach(row => {
                client_contractPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = client_contractPager.getRows().filter(r => ids.includes(r.code));
            client_contractPager.setRows(filtered);
        });
    }
    setInterval(realtimeclient_contractLoadData, 30000);
    // client_contractLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        client_contractPager.renderPage(0);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('client_contract:update', (row)=> client_contractPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function client_contractStartClock() {
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
    let _isPlayingclient_contract = false;
    let _onDoneclient_contract = null;
    let _pageQuotaclient_contract = Infinity;
    let _pagesRanclient_contract = 0;
    let currentPageclient_contract = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueeclient_contract() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesclient_contract() {
        const max = client_contractPager.getRows().length > 0 ? client_contractPager.getMaxRowsPerPageclient_contract() : 1;
        return Math.max(1, Math.ceil(client_contractPager.getRows().length / max));
    }

    function _showAndCountclient_contract(pageIndex) {
        client_contractPager.renderPage(pageIndex);
        applyMarqueeclient_contract();
        // if (_isPlayingclient_contract) {
        //     _pagesRanclient_contract++;
        //     if (_pagesRanclient_contract >= _pageQuotaclient_contract && _onDoneclient_contract) {
        //         _onDoneclient_contract();
        //     }
        // }
    }

    function startAutoSwitchclient_contract(pages = Infinity, onDone = null) {
        stopAutoSwitchclient_contract();
        _isPlayingclient_contract = true;
        _onDoneclient_contract = onDone;
        _pageQuotaclient_contract = pages;
        _pagesRanclient_contract = 0;

        const total = _getTotalPagesclient_contract();

        if (total < 1) {
            _showAndCountclient_contract(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDoneclient_contract === 'function') {
                setTimeout(() => {
                    console.log('✅ client_contract: done (1 page)');
                    _onDoneclient_contract();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountclient_contract(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDoneclient_contract === 'function') {
                setTimeout(() => {
                    console.log('✅ client_contract: done (1 page)');
                    _onDoneclient_contract();
                }, 30000);
            }
            return;
        }
        _showAndCountclient_contract(currentPageclient_contract);

        window.client_contractInterval = setInterval(() => {
            currentPageclient_contract = (currentPageclient_contract + 1) % total;
            _showAndCountclient_contract(currentPageclient_contract);
            _pagesRanclient_contract++;
            // 🔔 Khi chạy đủ số vòng yêu cầu thì báo Orchestrator
            if (_isPlayingclient_contract && _pagesRanclient_contract >= _pageQuotaclient_contract) {
                clearInterval(window.client_contractInterval);
                window.client_contractInterval = null;
                _isPlayingclient_contract = false;

                if (typeof _onDoneclient_contract === 'function') {
                    console.log('✅ client_contract: auto switch done, moving to next dashboard...');
                    _onDoneclient_contract();
                }
            }
        }, 30000);
    }

    function stopAutoSwitchclient_contract() {
        if (window.client_contractInterval) {
            clearInterval(window.client_contractInterval);
            window.client_contractInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.financeDash_deliverisclient_contract = {
        applyMarquee: () => {
            applyMarqueeclient_contract();
        },
        play: (pages, onDone) => {
            // if (!client_contractPager.getRows().length) client_contractLoadData();
            // startAutoSwitchclient_contract(pages, onDone);
            const hasRows = client_contractPager.getRows().length > 0;

            if (hasRows) {
                // ✅ Có sẵn dữ liệu → chạy ngay
                startAutoSwitchclient_contract(pages, onDone);
            } else {
                // ❌ Chưa có dữ liệu → chờ load xong rồi mới chạy
                client_contractLoadData();
                // Đợi AJAX trả về (vì load là async)
                const checkLoaded = setInterval(() => {
                    if (client_contractPager.getRows().length > 0) {
                        clearInterval(checkLoaded);
                        startAutoSwitchclient_contract(pages, onDone);
                    }
                }, 300);
            }
        },
        pause: () => {
            stopAutoSwitchclient_contract();
            _isPlayingclient_contract = false;
            _onDoneclient_contract = null;
            _pageQuotaclient_contract = Infinity;
            _pagesRanclient_contract = 0;
        },
        resume: (pages, onDone) => startAutoSwitchclient_contract(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesclient_contract();
            currentPageclient_contract = (currentPageclient_contract + 1) % total;
            _showAndCountclient_contract(currentPageclient_contract);
        },
        getState: () => ({
            currentPageclient_contract,
            totalPages: _getTotalPagesclient_contract(),
            isPlaying: _isPlayingclient_contract
        })
    };
</script>

</html>