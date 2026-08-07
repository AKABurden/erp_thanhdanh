<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/purchase_order_dxnb/purchase_order_dxnb_css'); ?>
</head>

<body>
    <div class="app_dxnb_purchases">
        <!-- HEADER -->
        <div class="header_dxnb_purchases">
            <div class="logo_dxnb_purchases">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_dxnb_purchases">
                <span class="main">PHÒNG KẾ TOÁN - TÀI CHÍNH</span><br>
                <span class="child">Hóa đơn mua hàng theo ĐXNB</span>
            </div>
            <div class="header-right_dxnb_purchases">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_dxnb_purchases" style="display:flex;gap:14px;">
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
            <section class="table-wrapper_dxnb_purchases">
                <div class="head">
                    <h2 class="h-title_dxnb_purchases"></h2>
                    <div class="legend_dxnb_purchases">
                        <span class="text-status"><i class="dot_dxnb_purchases green_dxnb_purchases"></i><span>Đã nhập</span></span>
                        <span class="text-status"><i class="dot_dxnb_purchases yellow_dxnb_purchases"></i><span>Nhập 1 phần</span></span>
                        <span class="text-status"><i class="dot_dxnb_purchases red_dxnb_purchases"></i><span>Chưa nhập</span></span>
                    </div>
                </div>

                <!-- 👇 Bọc table trong khung scroll -->
                <table class="dxnb_purchases">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Ngày</th>
                            <th style="width: 13%;">Mã ĐXNB</th>
                            <th style="width: 13%;">Mã PO</th>
                            <th style="width: 13%;">Mã hàng</th>
                            <th style="width: 12%;">Số lượng mua</th>
                            <th style="width: 12%;">Số lượng đã nhập</th>
                            <th style="width: 12%;">Số lượng Còn lại</th>
                            <th style="width: 10%;">Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_dxnb_purchases" id="table-body-dxnb_purchases" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function dxnb_purchasesEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function dxnb_purchasesNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function dxnb_purchasesFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function dxnb_purchasesRowTpl(r) {
        const code = dxnb_purchasesEsc(r.code || r.reference_no || '');
        const code_internal_proposal = dxnb_purchasesEsc(r.code_internal_proposal || '');
        const date = dxnb_purchasesEsc(r.date || '');
        const code_items = dxnb_purchasesEsc(r.code_items || '');
        const quantity_unit = r.quantity_unit || '';
        const quantity_stock = r.quantity_stock || '';
        const quantity_payment = r.quantity_payment || '';
        const quantity_stock_import = r.quantity_stock_import || '';
        const quantity_stock_left = r.quantity_stock_left || '';
        const statusClass = r.statusClass || '';
        const unit = r.unit || '';
        const unit_name_payment = r.unit_name_payment || '';
        const unit_name_stock = r.unit_name_stock || '';
        const idd = r.idd || '';

        const steps = Array.isArray(r.progress) ? r.progress : [];
        return `
        <tr data-id="${idd}">
            <td style="width: 8%;" title="${date}">${date}</td>
            <td style="width: 13%;" title="${code_internal_proposal}">${code_internal_proposal}</td>
            <td style="width: 13%;" title="${code}">${code}</td>
            <td style="width: 13%;" class="marquee_dxnb_purchases" title="${code_items}"><span class="marquee-content">${code_items}</span></td>
            <td style="width: 12%;" title="${quantity_stock}">${quantity_stock}/<span style="font-size:14px">${unit_name_stock}</span></td>
            <td style="width: 12%;" title="${quantity_stock_import}">${quantity_stock_import}/<span style="font-size:14px">${unit_name_stock}</span></td>
            <td style="width: 12%;color:red" title="${quantity_stock_left}">${quantity_stock_left}/<span style="font-size:14px">${unit_name_stock}</span></td>
            <td style="width: 10%;"><span class="dot_dxnb_purchases ${statusClass}"></span></td>
        </tr>`;
    }


    /* ========= Pager factory (đặt tên riêng) ========= */
    function createdxnb_purchasesPager({
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
            // Lọc bỏ các record có status == 1
            rows = Array.isArray(newRows) ? newRows.filter(item => dxnb_purchasesNum(item.quantity_stock_left) > 0) : [];
            renderPage(0);
        }

        function updateRow(row, keyFn) {
            const key = keyFn ? keyFn(row) : (row.idd || '');
            if (!key) return;
            const idx = rows.findIndex(x => (x.idd) === key); // Sửa ở đây
            if (idx >= 0) rows[idx] = row;
            else rows.push(row);

            renderPage(currentPage);
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

    /* ========= Khởi tạo pager riêng cho dxnb_purchases ========= */
    const dxnb_purchasesPager = createdxnb_purchasesPager({
        tbodySelector: "#table-body-dxnb_purchases",
        rowTpl: dxnb_purchasesRowTpl
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function dxnb_purchasesUpdateStats(stats) {

        // $(".js-dxnb_approved").text(dxnb_purchasesFmt(dxnb_purchasesNum(stats?.status_finish_1), 0));
        // $(".js-dxnb_un_approved").text(dxnb_purchasesFmt(dxnb_purchasesNum(stats?.status_finish_0), 0));

        // $(".js-count_purchase_import").text(dxnb_purchasesFmt(dxnb_purchasesNum(stats?.count_purchase_order_dxnb_import), 0));
        // $(".js-count_purchase_not_import").text(dxnb_purchasesFmt(dxnb_purchasesNum(stats?.count_purchase_order_dxnb_not_import), 0));

    }

    /* ========= Load data (đặt tên riêng) ========= */
    function dxnb_purchasesLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateWarehousePurchasesDXNB') ?>", res => {
            if (!res || !res.success) return;
            dxnb_purchasesUpdateStats(res.stats || {});
            const list = Array.isArray(res.purchase_order_dxnb) ? res.purchase_order_dxnb : [];
            dxnb_purchasesPager.setRows(list);
            // dxnb_purchasesPager.startAutoSwitch(10000);
        });
    }
    function realtimedxnb_purchasesLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateWarehousePurchasesDXNB') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.purchase_order_dxnb) ? res.purchase_order_dxnb : [];
            list.forEach(row => {
                dxnb_purchasesPager.updateRow(row, r => r.idd);
            });
            const ids = list.map(r => r.idd);
            const filtered = dxnb_purchasesPager.getRows().filter(r => ids.includes(r.idd));
            dxnb_purchasesPager.setRows(filtered);
        });
    }
    setInterval(realtimedxnb_purchasesLoadData, 30000);

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        dxnb_purchasesPager.renderPage(0);
        // dxnb_purchasesPager.startAutoSwitch(10000);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('dxnb_purchases:update', (row)=> dxnb_purchasesPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function dxnb_purchasesStartClock() {
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
    let _isPlayingdxnb_purchases = false;
    let _onDonedxnb_purchases = null;
    let _pageQuotadxnb_purchases = Infinity;
    let _pagesRandxnb_purchases = 0;
    let currentPagedxnb_purchases = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueedxnb_purchases() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagesdxnb_purchases_new() {
        const max = dxnb_purchasesPager.getRows().length > 0 ? dxnb_purchasesPager.getMaxRowsPerPage() : 1;

        return Math.max(1, Math.ceil(dxnb_purchasesPager.getRows().length / max));
    }

    function _showAndCountdxnb_purchases(pageIndex) {
        dxnb_purchasesPager.renderPage(pageIndex);
        applyMarqueedxnb_purchases();
        if (_isPlayingdxnb_purchases) {
            _pagesRandxnb_purchases++;
            if (_pagesRandxnb_purchases >= _pageQuotadxnb_purchases && _onDonedxnb_purchases) {
                _onDonedxnb_purchases();
            }
        }
    }

    function startAutoSwitchdxnb_purchases(pages = Infinity, onDone = null) {
        stopAutoSwitchdxnb_purchases();
        _isPlayingdxnb_purchases = true;
        _onDonedxnb_purchases = onDone;
        _pageQuotadxnb_purchases = pages;
        _pagesRandxnb_purchases = 0;

        const total = _getTotalPagesdxnb_purchases_new();

        if (total < 1) {
            _showAndCountdxnb_purchases(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDonedxnb_purchases === 'function') {
                setTimeout(() => {
                    console.log('✅ purchase_order_dxnb: done (only 1 page)');
                    _onDonedxnb_purchases();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountdxnb_purchases(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDonedxnb_purchases === 'function') {
                setTimeout(() => {
                    console.log('✅ purchase_order_dxnb: done (only 1 page)');
                    _onDonedxnb_purchases();
                }, 30000);
            }
            return;
        }
        _showAndCountdxnb_purchases(currentPagedxnb_purchases);

        window._dxnb_purchasesInterval = setInterval(() => {
            currentPagedxnb_purchases = (currentPagedxnb_purchases + 1) % total;
            _showAndCountdxnb_purchases(currentPagedxnb_purchases);

            // 🔔 Khi đã chạy đủ số vòng thì báo Orchestrator
            if (_isPlayingdxnb_purchases && _pagesRandxnb_purchases >= _pageQuotadxnb_purchases) {
                clearInterval(window._dxnb_purchasesInterval);
                window._dxnb_purchasesInterval = null;
                _isPlayingdxnb_purchases = false;

                if (typeof _onDonedxnb_purchases === 'function') {
                    console.log('✅ purchase_order_dxnb: auto switch complete, moving to next dashboard...');
                    _onDonedxnb_purchases();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchdxnb_purchases() {
        if (window._dxnb_purchasesInterval) {
            clearInterval(window._dxnb_purchasesInterval);
            window._dxnb_purchasesInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.dxnb_purchasesDash = {
        applyMarquee: () => {
            applyMarqueedxnb_purchases();
        },
        play: (pages, onDone) => {
            if (!dxnb_purchasesPager.getRows().length) dxnb_purchasesLoadData();
            startAutoSwitchdxnb_purchases(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchdxnb_purchases();
            _isPlayingdxnb_purchases = false;
            _onDonedxnb_purchases = null;
            _pageQuotadxnb_purchases = Infinity;
            _pagesRandxnb_purchases = 0;
        },
        resume: (pages, onDone) => startAutoSwitchdxnb_purchases(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesdxnb_purchases_new();
            currentPagedxnb_purchases = (currentPagedxnb_purchases + 1) % total;
            _showAndCountdxnb_purchases(currentPagedxnb_purchases);
        },
        getState: () => ({
            currentPagedxnb_purchases,
            totalPages: _getTotalPagesdxnb_purchases_new(),
            isPlaying: _isPlayingdxnb_purchases
        })
    };

    // ====== Socket realtime cho dxnb_purchases ======
    // Giả sử bạn đã có biến socket = io(endpoint)
    // socket.on('update_dashboard_purchase', ...)

    // Ví dụ:
    // const socket = io(/* endpoint */);
    // socket.on('update_dashboard_purchase', ...);

    // Đoạn dưới đây là phiên bản đúng cho dxnb_purchasesPager
    // socket.on('update_dashboard_purchase', (payload) => {
    //     const data = payload && payload.data !== undefined ? payload.data : payload;
    //     if (!data) return;
    //     switch (data.action) {
    //         case 'add': {
    //             const newRow = data.newRow;
    //             if (newRow) {
    //                 // Thêm dòng mới vào pager
    //                 dxnb_purchasesPager.updateRow(newRow, r => r.idd);
    //             }
    //             // Cập nhật lại thống kê nếu có
    //             if (data.stats) dxnb_purchasesUpdateStats(data.stats);
    //             break;
    //         }
    //         case 'update': {
    //             const updatedRow = data.updatedRow;
    //             if (updatedRow) {
    //                 dxnb_purchasesPager.updateRow(updatedRow, r => r.idd);
    //             }
    //             if (data.stats) dxnb_purchasesUpdateStats(data.stats);
    //             break;
    //         }
    //         case 'delete': {
    //             const deletedId = data.deleted_id;
    //             if (deletedId) {
    //                 // Xoá khỏi danh sách và render lại
    //                 const rows = dxnb_purchasesPager.getRows().filter(
    //                     r => (r.idd) !== deletedId
    //                 );
    //                 dxnb_purchasesPager.setRows(rows);
    //             }
    //             if (data.stats) dxnb_purchasesUpdateStats(data.stats);
    //             break;
    //         }
    //     }
    // });
</script>

</html>