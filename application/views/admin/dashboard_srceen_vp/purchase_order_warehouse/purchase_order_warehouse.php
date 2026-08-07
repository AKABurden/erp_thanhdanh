<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xuất kho giao hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/purchase_order_warehouse/purchase_order_warehouse_css'); ?>
</head>

<body>
    <div class="app_puwa">
        <!-- HEADER -->
        <div class="header_puwa">
            <div class="logo_puwa">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_puwa">
                <span class="main">PHÒNG MUA HÀNG - KHO HÀNG</span><br>
            </div>
            <div class="header-right_puwa">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_puwa" style="display:flex;gap:14px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_puwa">
                <div class="kpi-box_puwa xanh">
                    <div class="label">Đề xuất nội bộ đã duyệt</div>
                    <div class="value js-dxnb_approved">-</div>
                    <div style="border-bottom:1px solid #00691A;margin:8px 0;"></div>
                    <div class="label">Đề xuất nội bộ chưa duyệt</div>
                    <div class="value js-dxnb_un_approved">-</div>
                </div>
                <div class="kpi-box_puwa vang">
                    <div class="label">PO đã nhập kho</div>
                    <div class="value js-count_purchase_import">-</div>
                    <div style="border-bottom:1px solid #AF9514;margin:8px 0;"></div>
                    <div class="label">PO chưa nhập kho</div>
                    <div class="value js-count_purchase_not_import ">-</div>
                </div>
                <div class="kpi-box_puwa tim">
                    <div class="label">Tổng SL thành phẩm đã nhập</div>
                    <div class="value js-count_purchase_products_import">-</div>
                    <div style="border-bottom:1px solid #4F507F;margin:8px 0;"></div>
                    <div class="label">Tổng SL thành phẩm chưa nhập</div>
                    <div class="value js-count_purchase_products_not_import">-</div>
                </div>

            </aside>

            <!-- TABLE -->
            <section class="table-wrapper_puwa">
                <div class="head">
                    <h2 class="h-title_puwa">Danh sách đề xuất mua</h2>
                    <div class="legend_puwa">
                        <span class="text-status"><i class="dot_puwa green_puwa"></i><span>Đã duyệt</span></span>
                        <span class="text-status"><i class="dot_puwa yellow_puwa"></i><span>Chưa duyệt</span></span>
                    </div>
                </div>

                <!-- 👇 Bọc table trong khung scroll -->
                <table class="puwa">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Ngày</th>
                            <th style="width: 18%;">Mã đề xuất</th>
                            <th style="width: 13%;">Trạng thái</th>
                            <th style="width: 13%;">Người đề xuất</th>
                            <th style="width: 13%;">Số tiền</th>
                            <th style="width: 18%;">Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_puwa" id="table-body-puwa" style="height: 100%;"></tbody>
                </table>
            </section>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    /* ========= Helpers (đặt tên riêng) ========= */
    function puwaEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function puwaNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function puwaFmt(n, d = 0) {
        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }

    /* ========= Row template (đặt tên riêng) ========= */
    function puwaRowTpl(r) {
        const code = puwaEsc(r.code || r.reference_no || '');
        const date = puwaEsc(r.date || '');
        const code_internal_proposal = puwaEsc(r.code_internal_proposal || '');
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
            <div class="step_puwa ${st}">
            <div class="dot_puwa_progress"></div>
            <div class="content_puwa">
                <div class="title_puwa" ${userStyle}>${title}</div>
                <div class="user_puwa" ${userStyle}>
                <img src="${avatarUrl}" class="avatar-sm_puwa" alt="avatar">
                ${user}
                </div>
            </div>
            </div>`;
        }).join('');
        //         const stepsHtml = steps.map(step => {
        //     const title = puwaEsc(step.title || '');
        //     const user = puwaEsc(step.user || '');
        //     const avatar = puwaEsc(step.avatar_url || '<?= base_url('assets/images/user-placeholder.jpg') ?>');
        //     const st = puwaEsc(step.status || '');
        //     return `
        //         <div class="step_puwa ${st}">
        //             <div class="dot_puwa_progress"></div>
        //             <div class="content_puwa">
        //                 <div class="title_puwa">${title}</div>
        //                 <div class="user_puwa">
        //                     <img src="${avatar}" class="avatar-sm_puwa"> ${user}
        //                 </div>
        //             </div>
        //         </div>`;
        // }).join('');
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
            <td style="width: 18%;" title="${code}">${code}</td>
            <td style="width: 13%;"><span class="dot_puwa ${statusClass}"></span></td>
            <td style="width: 13%;font-size:18px;" class="image">${proposerImg}</td>
            <td style="width: 13%;color:red;"><strong>${amount}</strong></td>
            <td style="width: 18%;" class="td-progress_puwa"><div class="timeline_puwa">${stepsHtml}</div></td>
        </tr>`;
    }

    /* ========= Pager factory (đặt tên riêng) ========= */
    function createpuwaPager({
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
            rows = Array.isArray(newRows) ? newRows.filter(item => item.status_finish != 1) : [];
            renderPage(0);
        }

        function updateRow(row, keyFn) {
            // Bỏ qua nếu row có status == 1
            if (row && row.status_finish == 1) return;

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

    /* ========= Khởi tạo pager riêng cho puwa ========= */
    const puwaPager = createpuwaPager({
        tbodySelector: "#table-body-puwa",
        rowTpl: puwaRowTpl
    });

    /* ========= KPI / Stats (đặt tên riêng) ========= */
    function puwaUpdateStats(stats) {

        // $(".js-dxnb_approved").text(puwaFmt(puwaNum(stats?.status_finish_1), 0));
        // $(".js-dxnb_un_approved").text(puwaFmt(puwaNum(stats?.status_finish_0), 0));

        // $(".js-count_purchase_import").text(puwaFmt(puwaNum(stats?.count_purchase_order_import), 0));
        // $(".js-count_purchase_not_import").text(puwaFmt(puwaNum(stats?.count_purchase_order_not_import), 0));

    }

    /* ========= Load data (đặt tên riêng) ========= */
    function puwaLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateWarehouseImport') ?>", res => {
            if (!res || !res.success) return;
            puwaUpdateStats(res.stats || {});
            const list = Array.isArray(res.purchase_order) ? res.purchase_order : [];
            puwaPager.setRows(list);
            // puwaPager.startAutoSwitch(10000);
        });
    }

    function realtimedpuwaLoadData() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateWarehouseImport') ?>", res => {
            if (!res || !res.success) return;
            const list = Array.isArray(res.purchase_order) ? res.purchase_order : [];
            list.forEach(row => {
                puwaPager.updateRow(row, r => r.code);
            });
            const ids = list.map(r => r.code);
            const filtered = puwaPager.getRows().filter(r => ids.includes(r.code));
            puwaPager.setRows(filtered);
        });
    }
    setInterval(realtimedpuwaLoadData, 30000);
    // puwaLoadData();

    /* ========= Reflow khi resize ========= */
    $(window).on("resize", () => {
        puwaPager.renderPage(0);
        // puwaPager.startAutoSwitch(10000);
    });

    /* ========= Socket realtime (tuỳ chọn) ========= */
    // const socket = io(/* endpoint */);
    // socket.on('puwa:update', (row)=> puwaPager.updateRow(row, r=> r.code));

    /* ========= Đồng hồ realtime (đặt tên riêng) ========= */
    (function puwaStartClock() {
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
    let _isPlayingpuwa = false;
    let _onDonepuwa = null;
    let _pageQuotapuwa = Infinity;
    let _pagesRanpuwa = 0;
    let currentPagepuwa = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueepuwa() {
        // Nếu có hiệu ứng marquee thì xử lý ở đây
    }

    function _getTotalPagespuwa_new() {
        const max = puwaPager.getRows().length > 0 ? puwaPager.getMaxRowsPerPage() : 1;

        return Math.max(1, Math.ceil(puwaPager.getRows().length / max));
    }

    function _showAndCountpuwa(pageIndex) {
        puwaPager.renderPage(pageIndex);
        applyMarqueepuwa();
        if (_isPlayingpuwa) {
            _pagesRanpuwa++;
            if (_pagesRanpuwa >= _pageQuotapuwa && _onDonepuwa) {
                _onDonepuwa();
            }
        }
    }

    function startAutoSwitchpuwa(pages = Infinity, onDone = null) {
        stopAutoSwitchpuwa();
        _isPlayingpuwa = true;
        _onDonepuwa = onDone;
        _pageQuotapuwa = pages;
        _pagesRanpuwa = 0;

        const total = _getTotalPagespuwa_new();

        if (total < 1) {
            _showAndCountpuwa(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDonepuwa === 'function') {
                setTimeout(() => {
                    console.log('✅ PUWA: done (only 1 page)');
                    _onDonepuwa();
                }, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountpuwa(0);
            // 🔔 Nếu chỉ có 1 trang thì gọi done sau 2 giây
            if (typeof _onDonepuwa === 'function') {
                setTimeout(() => {
                    console.log('✅ PUWA: done (only 1 page)');
                    _onDonepuwa();
                }, 30000);
            }
            return;
        }
        _showAndCountpuwa(currentPagepuwa);

        window._puwaInterval = setInterval(() => {
            currentPagepuwa = (currentPagepuwa + 1) % total;
            _showAndCountpuwa(currentPagepuwa);

            // 🔔 Khi đã hiển thị đủ số vòng yêu cầu thì báo Orchestrator
            if (_isPlayingpuwa && _pagesRanpuwa >= _pageQuotapuwa) {
                clearInterval(window._puwaInterval);
                window._puwaInterval = null;
                _isPlayingpuwa = false;

                if (typeof _onDonepuwa === 'function') {
                    console.log('✅ PUWA: auto switch complete, moving to next dashboard...');
                    _onDonepuwa();
                }
            }
        }, 30000);
    }


    function stopAutoSwitchpuwa() {
        if (window._puwaInterval) {
            clearInterval(window._puwaInterval);
            window._puwaInterval = null;
        }
    }

    // ====== Export Dashboard Control Object ======
    window.warehouseDash = {
        applyMarquee: () => {
            applyMarqueepuwa();
        },
        play: (pages, onDone) => {
            if (!puwaPager.getRows().length) puwaLoadData();
            startAutoSwitchpuwa(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchpuwa();
            _isPlayingpuwa = false;
            _onDonepuwa = null;
            _pageQuotapuwa = Infinity;
            _pagesRanpuwa = 0;
        },
        resume: (pages, onDone) => startAutoSwitchpuwa(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagespuwa_new();
            currentPagepuwa = (currentPagepuwa + 1) % total;
            _showAndCountpuwa(currentPagepuwa);
        },
        getState: () => ({
            currentPagepuwa,
            totalPages: _getTotalPagespuwa_new(),
            isPlaying: _isPlayingpuwa
        })
    };
</script>

</html>