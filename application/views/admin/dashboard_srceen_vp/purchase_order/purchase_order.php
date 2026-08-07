<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/purchase_order/purchase_order_css'); ?>
</head>

<body>
    <div class="app_purchaseorder">
        <!-- HEADER -->
        <div class="header_purchaseorder">
            <div class="logo_purchaseorder">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_purchaseorder">
                <span class="main">PHÒNG MUA HÀNG - KHO HÀNG</span><br>
            </div>
            <div class="header-right_purchaseorder">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_purchaseorder" style="display:flex;gap:14px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_purchaseorder">
                <div class="kpi-box_purchaseorder xanh">
                    <div class="label">Đề xuất nội bộ đã duyệt</div>
                    <div class="value js-dxnb_approved">-</div>
                    <div style="border-bottom:1px solid #00691A;margin:8px 0;"></div>
                    <div class="label">Đề xuất nội bộ chưa duyệt</div>
                    <div class="value js-dxnb_un_approved">-</div>
                </div>
                <div class="kpi-box_purchaseorder vang">
                    <div class="label">PO đã nhập kho</div>
                    <div class="value js-count_purchase_import">-</div>
                    <div style="border-bottom:1px solid #AF9514;margin:8px 0;"></div>
                    <div class="label">PO chưa nhập kho</div>
                    <div class="value js-count_purchase_not_import ">-</div>
                </div>
                <div class="kpi-box_purchaseorder tim">
                    <div class="label">Tổng SL thành phẩm đã nhập</div>
                    <div class="value js-count_purchase_products_import">-</div>
                    <div style="border-bottom:1px solid #4F507F;margin:8px 0;"></div>
                    <div class="label">Tổng SL thành phẩm chưa nhập</div>
                    <div class="value js-count_purchase_products_not_import">-</div>
                </div>

            </aside>

            <!-- TABLE -->
            <section class="table-wrapper_purchaseorder">
                <div class="head">
                    <h2 class="h-title_purchaseorder">Danh sách PO nhập hàng</h2>
                    <div class="legend_purchaseorder">
                        <span class="text-status"><i class="dot_purchaseorder green_purchaseorder"></i><span>Đã nhập</span></span>
                        <span class="text-status"><i class="dot_purchaseorder yellow_purchaseorder"></i><span>Nhập 1 phần</span></span>
                        <span class="text-status"><i class="dot_purchaseorder red_purchaseorder"></i><span>Chưa nhập</span></span>
                    </div>
                </div>

                <!-- 👇 Bọc table trong khung scroll -->
                <table class="purchaseorder">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Ngày</th>
                            <th style="width: 13%;">Mã PO</th>
                            <th style="width: 13%;">Mã hàng</th>
                            <th style="width: 12%;">SLĐV chuẩn</th>
                            <th style="width: 12%;">SLĐV lưu kho</th>
                            <th style="width: 12%;">SLĐV thanh toán</th>
                            <th style="width: 12%;">SLĐV lưu kho đã nhập</th>
                            <th style="width: 12%;">SLĐV lưu kho còn lại</th>
                            <th style="width: 10%;">Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_purchaseorder" id="table-body-purchaseorder" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function purchaseorderEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function purchaseorderNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function purchaseorderFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function purchaseorderRowTpl(r) {
        const code = purchaseorderEsc(r.code || r.reference_no || '');
        const date = purchaseorderEsc(r.date || '');
        const code_items = purchaseorderEsc(r.code_items || '');
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
            <td style="width: 13%;" title="${code}">${code}</td>
            <td style="width: 13%;" class="marquee_purchaseorder" title="${code_items}"><span class="marquee-content">${code_items}</span></td>
            <td style="width: 12%;" title="${quantity_unit}">${quantity_unit}/<span style="font-size:14px">${unit}</span></td>
            <td style="width: 12%;" title="${quantity_stock}">${quantity_stock}/<span style="font-size:14px">${unit_name_stock}</span></td>
            <td style="width: 12%;" title="${quantity_payment}">${quantity_payment}/<span style="font-size:14px">${unit_name_payment}</span></td>
            <td style="width: 12%;" title="${quantity_stock_import}">${quantity_stock_import}/<span style="font-size:14px">${unit_name_stock}</span></td>
            <td style="width: 12%;color:red" title="${quantity_stock_left}">${quantity_stock_left}/<span style="font-size:14px">${unit_name_stock}</span></td>
            <td style="width: 10%;"><span class="dot_purchaseorder ${statusClass}"></span></td>
        </tr>`;
    }


    /* ========= Pager factory (đặt tên riêng) ========= */
    function createpurchaseorderPager({
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
            rows = Array.isArray(newRows) ? newRows.filter(item => purchaseorderNum(item.quantity_stock_left) > 0) : [];
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

    /* ========= Khởi tạo pager riêng cho purchaseorder ========= */
    const purchaseorderPager = createpurchaseorderPager({
        tbodySelector: "#table-body-purchaseorder",
        rowTpl: purchaseorderRowTpl
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function purchaseorderUpdateStats(stats) {

        // $(".js-dxnb_approved").text(purchaseorderFmt(purchaseorderNum(stats?.status_finish_1), 0));
        // $(".js-dxnb_un_approved").text(purchaseorderFmt(purchaseorderNum(stats?.status_finish_0), 0));

        // $(".js-count_purchase_import").text(purchaseorderFmt(purchaseorderNum(stats?.count_purchase_order_import), 0));
        // $(".js-count_purchase_not_import").text(purchaseorderFmt(purchaseorderNum(stats?.count_purchase_order_not_import), 0));

    }

    /* ========= Load data (đặt tên riêng) ========= */
    function purchaseorderLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateWarehousePurchases') ?>", res => {
            if (!res || !res.success) return;
            purchaseorderUpdateStats(res.stats || {});
            const list = Array.isArray(res.purchase_order) ? res.purchase_order : [];
            purchaseorderPager.setRows(list);
            // purchaseorderPager.startAutoSwitch(10000);
        });
    }

    function realtimepurchaseorderLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateWarehousePurchases') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.purchase_order) ? res.purchase_order : [];
            list.forEach(row => {
                purchaseorderPager.updateRow(row, r => r.idd);
            });
            const ids = list.map(r => r.idd);
            const filtered = purchaseorderPager.getRows().filter(r => ids.includes(r.idd));
            purchaseorderPager.setRows(filtered);
        });
    }
    setInterval(realtimepurchaseorderLoadData, 30000);

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        purchaseorderPager.renderPage(0);
        // purchaseorderPager.startAutoSwitch(10000);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('purchaseorder:update', (row)=> purchaseorderPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function purchaseorderStartClock() {
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
    let _isPlayingpurchaseorder = false;
    let _onDonepurchaseorder = null;
    let _pageQuotapurchaseorder = Infinity;
    let _pagesRanpurchaseorder = 0;
    let currentPagepurchaseorder = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueepurchaseorder() {
        $(".table-purchaseorder").each(function() {
            const $el = $(this);
            // reset trước
            if ($el.hasClass("marquee_purchaseorder")) {
                const text = $el.find("span").text();
                $el.removeClass("marquee_purchaseorder").text(text);
            }
            // chỉ áp dụng nếu chữ dài hơn ô
            if (this.scrollWidth > this.clientWidth) {

                const text = $el.text();
                $el.addClass("marquee_purchaseorder").html(`<span>${text}</span>`);
            }
        });
    }

    function _getTotalPagespurchaseorder_new() {
        const max = purchaseorderPager.getRows().length > 0 ? purchaseorderPager.getMaxRowsPerPage() : 1;

        return Math.max(1, Math.ceil(purchaseorderPager.getRows().length / max));
    }

    function _showAndCountpurchaseorder(pageIndex) {
        purchaseorderPager.renderPage(pageIndex);
        applyMarqueepurchaseorder();
        if (_isPlayingpurchaseorder) {
            _pagesRanpurchaseorder++;
            if (_pagesRanpurchaseorder >= _pageQuotapurchaseorder && _onDonepurchaseorder) {
                _onDonepurchaseorder();
            }
        }
    }

    function startAutoSwitchpurchaseorder(pages = Infinity, onDone = null) {
        stopAutoSwitchpurchaseorder();
        _isPlayingpurchaseorder = true;
        _onDonepurchaseorder = onDone;
        _pageQuotapurchaseorder = pages;
        _pagesRanpurchaseorder = 0;

        const total = _getTotalPagespurchaseorder_new();

        if (total < 1) {
            _showAndCountpurchaseorder(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDonepurchaseorder === 'function') {
                setTimeout(() => {
                    console.log('✅ PURCHASE_ORDER: done (only 1 page)');
                    _onDonepurchaseorder();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountpurchaseorder(0);
            // 🔔 Nếu chỉ có 1 trang thì báo hoàn tất luôn sau 2 giây
            if (typeof _onDonepurchaseorder === 'function') {
                setTimeout(() => {
                    console.log('✅ PURCHASE_ORDER: done (only 1 page)');
                    _onDonepurchaseorder();
                }, 30000);
            }
            return;
        }
        _showAndCountpurchaseorder(currentPagepurchaseorder);

        window._purchaseorderInterval = setInterval(() => {
            currentPagepurchaseorder = (currentPagepurchaseorder + 1) % total;
            _showAndCountpurchaseorder(currentPagepurchaseorder);

            // 🔔 Khi đã chạy đủ số vòng thì báo Orchestrator
            if (_isPlayingpurchaseorder && _pagesRanpurchaseorder >= _pageQuotapurchaseorder) {
                clearInterval(window._purchaseorderInterval);
                window._purchaseorderInterval = null;
                _isPlayingpurchaseorder = false;

                if (typeof _onDonepurchaseorder === 'function') {
                    console.log('✅ PURCHASE_ORDER: auto switch complete, moving to next dashboard...');
                    _onDonepurchaseorder();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchpurchaseorder() {
        if (window._purchaseorderInterval) {
            clearInterval(window._purchaseorderInterval);
            window._purchaseorderInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.purchaseorderDash = {
        applyMarquee: () => {
            applyMarqueepurchaseorder();
        },
        play: (pages, onDone) => {
            if (!purchaseorderPager.getRows().length) purchaseorderLoadData();
            startAutoSwitchpurchaseorder(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchpurchaseorder();
            _isPlayingpurchaseorder = false;
            _onDonepurchaseorder = null;
            _pageQuotapurchaseorder = Infinity;
            _pagesRanpurchaseorder = 0;
        },
        resume: (pages, onDone) => startAutoSwitchpurchaseorder(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagespurchaseorder_new();
            currentPagepurchaseorder = (currentPagepurchaseorder + 1) % total;
            _showAndCountpurchaseorder(currentPagepurchaseorder);
        },
        getState: () => ({
            currentPagepurchaseorder,
            totalPages: _getTotalPagespurchaseorder_new(),
            isPlaying: _isPlayingpurchaseorder
        })
    };

    // ====== Socket realtime cho purchaseorder ======
    // Giả sử bạn đã có biến socket = io(endpoint)
    // socket.on('update_dashboard_purchase', ...)

    // Ví dụ:
    // const socket = io(/* endpoint */);
    // socket.on('update_dashboard_purchase', ...);

    // Đoạn dưới đây là phiên bản đúng cho purchaseorderPager
    // socket.on('update_dashboard_purchase', (payload) => {
    //     const data = payload && payload.data !== undefined ? payload.data : payload;
    //     if (!data) return;
    //     switch (data.action) {
    //         case 'add': {
    //             const newRow = data.newRow;
    //             if (newRow) {
    //                 // Thêm dòng mới vào pager
    //                 purchaseorderPager.updateRow(newRow, r => r.idd);
    //             }
    //             // Cập nhật lại thống kê nếu có
    //             if (data.stats) purchaseorderUpdateStats(data.stats);
    //             break;
    //         }
    //         case 'update': {
    //             const updatedRow = data.updatedRow;
    //             if (updatedRow) {
    //                 purchaseorderPager.updateRow(updatedRow, r => r.idd);
    //             }
    //             if (data.stats) purchaseorderUpdateStats(data.stats);
    //             break;
    //         }
    //         case 'delete': {
    //             const deletedId = data.deleted_id;
    //             if (deletedId) {
    //                 // Xoá khỏi danh sách và render lại
    //                 const rows = purchaseorderPager.getRows().filter(
    //                     r => (r.idd) !== deletedId
    //                 );
    //                 purchaseorderPager.setRows(rows);
    //             }
    //             if (data.stats) purchaseorderUpdateStats(data.stats);
    //             break;
    //         }
    //     }
    // });
</script>

</html>