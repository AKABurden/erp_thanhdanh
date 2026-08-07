<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phòng Kinh Doanh</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->load->view('admin/dashboard_srceen_vp/business_department/business_department_css'); ?>
</head>

<body>
    <div class="app_pkd">
        <!-- HEADER -->
        <div class="header_pkd">
            <div class="logo_pkd">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:90px;height:90px;">
            </div>
            <div class="header-title_pkd">
                <span class="main">PHÒNG KINH DOANH</span><br>
            </div>
            <div class="header-right_pkd">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>

        <!-- BODY -->
        <div class="container_pkd" style="display:flex;gap:7px;">
            <!-- SIDEBAR KPI -->
            <aside class="sidebar_pkd">
                <div class="kpi-box_pkd nau">
                    <div class="label">Báo giá</div>
                </div>
                <div class="thongke-box_pkd thongke-grid_pkd">
                    <div class="box-pkd" style="padding: 30px;">
                        <div class="label">TỔNG SL BÁO GIÁ</div>
                        <div class="value blue js-total-quotes">-</div>
                    </div>
                    <div class="box-pkd" style="padding: 30px;">
                        <div class="label">BÁO GIÁ CÓ ĐƠN HÀNG</div>
                        <div class="value green js-total-quotes-has-order">-</div>
                    </div>
                    <div class="box-pkd">
                        <div style="display: flex;flex-direction: row-reverse;justify-content: space-around;align-items: center;align-content: flex-start;">
                            <div>
                                <div class="kpi-sum"><span class="kpi-sub">Đã duyệt</span><span class="pill js-quotes-approved" style="background-color: #01C532;color:white;">-</span></div>
                                <div class="kpi-sum" style="margin-top:-4px"><span class="kpi-sub">Chưa duyệt</span><span class="pill js-quotes-reject" style="background-color: #E56464;color:white;">-</span></div>
                            </div>
                            <div class="donut-wrap">
                                <div class="donut donut-quotes" data-percent="0" id="donut-quotes">
                                    <svg viewBox="0 0 42 42" aria-hidden="true">
                                        <circle class="ring-bg_pkd" cx="21" cy="21" r="15.915"></circle>
                                        <circle class="ring-val_pkd" cx="21" cy="21" r="15.915"></circle>
                                    </svg>
                                    <div class="txt"><span class="js-donut-quotes">0%</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-pkd" style="padding: 30px;">
                        <div class="label">BÁO GIÁ CHƯA CÓ ĐƠN HÀNG</div>
                        <div class="value red js-total-quotes-no-order">-</div>
                    </div>
                </div>

                <table class="pkd">
                    <thead>
                        <tr>
                            <th style="width:  25%;">Mã báo giá</th>
                            <th style="width:  20%;">Khách hàng</th>
                            <th style="width:  40%;">Người phụ trách</th>
                            <th style="width:  15%;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_pkd" id="table-body-kd-bg" style="height:100%;"></tbody>
                </table>

                <div class="head_pkd">
                    <div class="legend_pkd">
                        <span class="text-status"><i class="dot_pkd red_pkd"></i><span>Chưa duyệt</span></span>
                        <span class="text-status"><i class="dot_pkd yellow_pkd"></i><span>Chưa có đơn hàng</span></span>
                    </div>
                </div>
            </aside>

            <!-- TABLE -->
            <aside class="sidebar_pkd">
                <div class="kpi-box_pkd nau">
                    <div class="label">Phát triển mẫu</div>
                </div>

                <div class="thongke-box_pkd thongke-grid_pkd">
                    <div class="box-pkd" style="padding: 30px;">
                        <div class="label">TỔNG SL PHÁT TRIỂN MẪU</div>
                        <div class="value blue js-total-ptm">-</div>
                    </div>
                    <div class="box-pkd" style="padding: 30px;">
                        <div class="label">PTM CÓ ĐƠN HÀNG</div>
                        <div class="value green js-total-ptm-has-order">-</div>
                    </div>
                    <div class="box-pkd">
                        <div style="display: flex;flex-direction: row-reverse;justify-content: space-around;align-items: center;align-content: flex-start;">
                            <div>
                                <div class="kpi-sum"><span class="kpi-sub">Đã duyệt</span><span class="pill js-ptm-approved" style="background-color: #01C532;color:white;">-</span></div>
                                <div class="kpi-sum" style="margin-top:-4px"><span class="kpi-sub">Chưa duyệt</span><span class="pill js-ptm-reject" style="background-color: #E56464;color:white;">-</span></div>
                            </div>
                            <div class="donut-wrap">
                                <div class="donut donut-ptm" data-percent="0" id="donut-ptm">
                                    <svg viewBox="0 0 42 42" aria-hidden="true">
                                        <circle class="ring-bg_pkd" cx="21" cy="21" r="15.915"></circle>
                                        <circle class="ring-val_pkd" cx="21" cy="21" r="15.915"></circle>
                                    </svg>
                                    <div class="txt"><span class="js-donut-ptm">0%</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-pkd" style="padding: 30px;">
                        <div class="label">PTM CHƯA CÓ ĐƠN HÀNG</div>
                        <div class="value red js-total-ptm-no-order">-</div>
                    </div>
                </div>

                <table class="pkd">
                    <thead>
                        <tr>
                            <th style="width:  25%;">Mã PTM</th>
                            <th style="width:  20%;">Khách hàng</th>
                            <th style="width:  40%;">Người phụ trách</th>
                            <th style="width:  15%;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="table-body_pkd" id="table-body-kd-ptm" style="height:100%;"></tbody>
                </table>

                <div class="head_pkd">
                    <div class="legend_pkd">
                        <span class="text-status"><i class="dot_pkd red_pkd"></i><span>Chưa duyệt</span></span>
                        <span class="text-status"><i class="dot_pkd yellow_pkd"></i><span>Chưa có đơn hàng</span></span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</body>

<!-- scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    // small helper: safe html escape (for titles)
    function esc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // ====== ROW TEMPLATES ======
    function rowTplBG(r) {
        const job = esc(r.reference_no || r.code || '');
        const clients = esc(r.company || r.clients || '');
        const proposer = r.image_employee || (`<img src="${r.avatar_url || '<?= base_url('uploads/user.png') ?>'}" class="avatar_pkd"> ${esc(r.proposer || r.proposer_name || '')}`);
        const statusClass = r.status_color || (r.status && String(r.status).toLowerCase() === 'approved' ? 'green_pkd' : 'red_pkd');
        return `<tr data-id="${job}"><td style="width: 25%;" title="${job}">${job}</td><td style="width: 20%;" class="marquee-text" title="${clients}">${clients}</td><td style="width:  40%;" class="image">${proposer}</td><td><span style="width: 15%;" class="dot_pkd ${statusClass}"></span></td></tr>`;
    }

    function rowTplPTM(r) {
        const job = esc(r.job_code || r.reference_no || r.code || '');
        const clients = esc(r.clients || r.company || '');
        const proposer = r.image_employee || (`<img src="${r.avatar_url || '<?= base_url('uploads/user.png') ?>'}" class="avatar_pkd"> ${esc(r.proposer || r.proposer_name || '')}`);
        const statusClass = r.status_color || (r.status && String(r.status).toLowerCase() === 'approved' ? 'green_pkd' : 'red_pkd');
        return `<tr data-id="${job}"><td style="width: 25%;" title="${job}">${job}</td><td style="width: 20%;" class="marquee-text"  title="${clients}">${clients}</td><td style="width: 40%;" class="image">${proposer}</td><td><span style="width: 15%;" class="dot_pkd ${statusClass}"></span></td></tr>`;
    }

    // ====== Generic pager factory (used by BG and PTM) ======
    function createTablePager({
        tbodySelector,
        rowTpl
    }) {
        const $tbody = $(tbodySelector);
        let rows = [];
        let currentPage = 0;
        let autoSwitch = null;

        function getMaxRowsPerPage() {
            const parentH = $tbody.parent().height() || 400;
            let rowH = $tbody.find("tr").first().outerHeight();
            if (!rowH || rowH < 10) rowH = 44;
            return Math.max(1, Math.floor(parentH / rowH));
        }

        // function renderPage(index = 0) {
        //     const max = getMaxRowsPerPage();
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
        function renderPage(index = 0) {
            const max = getMaxRowsPerPage();
            const totalPages = Math.max(1, Math.ceil(rows.length / max));
            currentPage = Math.min(index, totalPages - 1);
            const start = currentPage * max;
            const slice = rows.slice(start, start + max);
            $tbody.empty();
            slice.forEach(r => $tbody.append(rowTpl(r)));
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
            const key = keyFn ? keyFn(row) : (row.reference_no || row.job_code || row.code || '');
            if (!key) return;
            const idx = rows.findIndex(x => (x.reference_no || x.job_code || x.code) === key);
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

    // ====== Instantiate pagers ======
    const pagerBG = createTablePager({
        tbodySelector: "#table-body-kd-bg",
        rowTpl: rowTplBG
    });
    const pagerPTM = createTablePager({
        tbodySelector: "#table-body-kd-ptm",
        rowTpl: rowTplPTM
    });

    // ====== KPI / Donut updaters ======
    function updatePlanningStatsBG_pkd(list) {
        const total = list.length;
        const hasOrder = list.filter(r => Number(r.order_id || 0) > 0).length;
        $(".js-total-quotes").text(total ? total : '-');
        $(".js-total-quotes-has-order").text(hasOrder ? hasOrder : '-');
        $(".js-total-quotes-no-order").text((total - hasOrder) ? (total - hasOrder) : '-');

        const approved = list.filter(r => String(r.status || r.status_text || '').toLowerCase() === 'approved').length;
        $(".js-quotes-approved").text(approved ? approved : '-');
        $(".js-quotes-reject").text(total - approved ? (total - approved) : '-');

        const avg = total > 0 ? Math.round((approved / total) * 100) : 0;

        // use circumference-based dash values (fix offset calculation)
        const percent = Math.min(100, Math.max(0, avg));
        const radius = 15.915;
        const circumference = 2 * Math.PI * radius;
        const filledOffset = circumference * (1 - percent / 100);

        const $svg = $(".donut-quotes svg");
        const $valCircle = $svg.find("circle").eq(1);
        // full stroke length, offset to show percent
        $valCircle.css({
            "stroke-dasharray": circumference,
            "stroke-dashoffset": filledOffset
        });

        $(".js-donut-quotes").text(percent + "%");
    }

    function updatePlanningStatsPTM_pkd(list) {
        const total = list.length;
        const hasOrder = list.filter(r => Number(r.order_id || 0) > 0).length;
        $(".js-total-ptm").text(total ? total : '-');
        $(".js-total-ptm-has-order").text(hasOrder ? hasOrder : '-');
        $(".js-total-ptm-no-order").text(total - hasOrder ? (total - hasOrder) : '-');

        const approved = list.filter(r => String(r.status || r.status_text || '').toLowerCase() === 'approved').length;
        $(".js-ptm-approved").text(approved ? approved : '-');
        $(".js-ptm-reject").text(total - approved ? (total - approved) : '-');

        const avg = total > 0 ? Math.round((approved / total) * 100) : 0;

        // use circumference-based dash values (fix offset calculation)
        const percent = Math.min(100, Math.max(0, avg));
        const radius = 15.915;
        const circumference = 2 * Math.PI * radius;
        const filledOffset = circumference * (1 - percent / 100);

        const $svg = $(".donut-ptm svg");
        const $valCircle = $svg.find("circle").eq(1);
        $valCircle.css({
            "stroke-dasharray": circumference,
            "stroke-dashoffset": filledOffset
        });

        $(".js-donut-ptm").text(percent + "%");
    }

    // ====== Dashboard Control Variables ======
    let _isPlayingPkd = false;
    let _onDonePkd = null;
    let _pageQuotaPkd = Infinity;
    let _pagesRanPkd = 0;
    let currentPagePkd = 0;

    // ====== Dashboard Control Functions ======
    function applyMarqueePkd() {
        // Apply marquee effect if needed
        $('.marquee-text').each(function() {
            const $this = $(this);
            if ($this.prop('scrollWidth') > $this.width()) {
                $this.addClass('marquee-active');
            }
        });
    }

    function _getTotalPagesPkd() {
        const maxBG = Math.max(1, Math.ceil(pagerBG.getRows().length / pagerBG.getMaxRowsPerPage()));
        const maxPTM = Math.max(1, Math.ceil(pagerPTM.getRows().length / pagerPTM.getMaxRowsPerPage()));
        return Math.max(maxBG, maxPTM);
    }

    function _showAndCountPkd(pageIndex) {
        pagerBG.renderPage(pageIndex);
        pagerPTM.renderPage(pageIndex);
        applyMarqueePkd();

        if (_isPlayingPkd) {
            _pagesRanPkd++;
            if (_pagesRanPkd >= _pageQuotaPkd && _onDonePkd) {
                _onDonePkd();
            }
        }
    }

    function startAutoSwitchPkd(pages = Infinity, onDone = null) {
        stopAutoSwitchPkd();
        _isPlayingPkd = true;
        _onDonePkd = onDone;
        _pageQuotaPkd = pages;
        _pagesRanPkd = 0;

        const total = _getTotalPagesPkd();

        if (total < 1) {
            _showAndCountPkd(0);
            // 🔔 gọi done luôn nếu không có nhiều trang
            if (typeof _onDonePkd === 'function') {
                setTimeout(_onDonePkd, 2000);
            }
            return;
        }
        if (total == 1) {
            _showAndCountPkd(0);
            // 🔔 gọi done luôn nếu không có nhiều trang
            if (typeof _onDonePkd === 'function') {
                setTimeout(_onDonePkd, 30000);
            }
            return;
        }
        _showAndCountPkd(currentPagePkd);
        const interval = setInterval(() => {
            currentPagePkd = (currentPagePkd + 1) % total;
            _showAndCountPkd(currentPagePkd);

            // 🔔 Khi đủ số vòng theo pageQuota thì gọi callback và dừng
            if (_isPlayingPkd && _pagesRanPkd >= _pageQuotaPkd) {
                clearInterval(interval);
                window._pkdInterval = null;
                _isPlayingPkd = false;
                if (typeof _onDonePkd === 'function') _onDonePkd();
            }
        }, 30000);

        // Store interval for cleanup
        window._pkdInterval = interval;
    }
    // function startAutoSwitchPkd(pages = Infinity, onDone = null) {
    //     stopAutoSwitchPkd();
    //     _isPlayingPkd = true;
    //     _onDonePkd = onDone;
    //     _pageQuotaPkd = pages;
    //     _pagesRanPkd = 0;

    //     const total = _getTotalPagesPkd();

    //     if (total <= 1) {
    //         _showAndCountPkd(0);
    //         return;
    //     }

    //     _showAndCountPkd(currentPagePkd);
    //     const interval = setInterval(() => {
    //         currentPagePkd = (currentPagePkd + 1) % total;
    //         _showAndCountPkd(currentPagePkd);
    //     }, 10000);

    //     // Store interval for cleanup
    //     window._pkdInterval = interval;
    // }

    function stopAutoSwitchPkd() {
        if (window._pkdInterval) {
            clearInterval(window._pkdInterval);
            window._pkdInterval = null;
        }
        pagerBG.stopAutoSwitch();
        pagerPTM.stopAutoSwitch();
    }

    // ====== Export Dashboard Control Object ======
    window.businessDash = {
        applyMarquee: () => {
            applyMarqueePkd();
        },
        play: (pages, onDone) => {
            if (!pagerBG.getRows().length && !pagerPTM.getRows().length) loadPlanningData_pkd();
            startAutoSwitchPkd(pages, onDone);
        },
        pause: () => {
            stopAutoSwitchPkd();
            _isPlayingPkd = false;
            _onDonePkd = null;
            _pageQuotaPkd = Infinity;
            _pagesRanPkd = 0;
        },
        resume: (pages, onDone) => startAutoSwitchPkd(pages, onDone),
        nextPage: () => {
            const total = _getTotalPagesPkd();
            currentPagePkd = (currentPagePkd + 1) % total;
            _showAndCountPkd(currentPagePkd);
        },
        getState: () => ({
            currentPagePkd,
            totalPages: _getTotalPagesPkd(),
            isPlaying: _isPlayingPkd
        })
    };

    // ====== Load initial data ======
    function loadPlanningData_pkd() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateBusinessDepartment') ?>", res => {
            if (!res || !res.success) return;

            // Lọc bỏ các record có status_hide = 1
            const data = Array.isArray(res.quotes) ? res.quotes.filter(item => item.status_hide != 1) : [];
            const request_template = Array.isArray(res.request_template) ? res.request_template.filter(item => item.status_hide != 1) : [];

            pagerBG.setRows(data);
            pagerPTM.setRows(request_template);

            updatePlanningStatsBG_pkd(data);
            updatePlanningStatsPTM_pkd(request_template);

        });
    }
    loadPlanningData_pkd();
    startAutoSwitchPkd();

    function realtimeloadPlanningData_pkd() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/updateBusinessDepartment') ?>", res => {
            if (!res || !res.success) return;
            // const list = Array.isArray(res.purchase_order) ? res.purchase_order : [];
            const data = Array.isArray(res.quotes) ? res.quotes.filter(item => item.status_hide != 1) : [];
            const request_template = Array.isArray(res.request_template) ? res.request_template.filter(item => item.status_hide != 1) : [];
            data.forEach(row => {
                pagerBG.updateRow(row, r => r.reference_no);
            });
            const ids = data.map(r => r.reference_no);
            const filtered = pagerBG.getRows().filter(r => ids.includes(r.reference_no));
            pagerBG.setRows(filtered);


            request_template.forEach(row => {
                pagerPTM.updateRow(row, r => r.reference_no);
            });
            const idss = request_template.map(r => r.reference_no);
            const filtereds = pagerPTM.getRows().filter(r => idss.includes(r.reference_no));
            pagerPTM.setRows(filtereds);

        });
    }
    setInterval(realtimeloadPlanningData_pkd, 30000);
    // run load
    // loadPlanningData_pkd();

    // window resize -> repaginate lightly
    $(window).on("resize", () => {
        pagerBG.renderPage(0);
        pagerBG.startAutoSwitch();
        pagerPTM.renderPage(0);
        pagerPTM.startAutoSwitch();
    });

    // ====== small helpers: clock and donut init ======
    (function startRealtimeClock_pkd() {
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
            hour12: false
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

    function initDonut($donut) {
        const percent = parseInt($donut.data('percent') || 0, 10);
        const $circle = $donut.find(".ring-val_pkd");
        const radius = 15.915;
        const circumference = 2 * Math.PI * radius;
        const offset = circumference * (1 - Math.min(100, Math.max(0, percent)) / 100);
        $circle.css({
            strokeDasharray: circumference,
            strokeDashoffset: offset
        });
        $donut.find('.txt span').text(percent + '%');
    }
    $(".donut-quotes, .donut-ptm").each(function() {
        initDonut($(this));
    });
</script>

</html>