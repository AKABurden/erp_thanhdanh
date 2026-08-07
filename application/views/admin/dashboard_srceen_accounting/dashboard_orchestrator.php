<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Orchestrator</title>
    <?php $this->load->view('admin/dashboard_srceen_office/dashboard_orchestrator_css'); ?>
</head>
<!-- Bootstrap CSS -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<!-- Bootstrap 4 CSS for modal support -->
<script>
    function mainEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    }

    function mainNum(v) {
        const n = Number(String(v).replace(/[^\d.-]/g, ''));
        return isNaN(n) ? 0 : n
    }

    function mainFmt(n, d = 0) {
        if (n === '0' || n === 0 || n === null || n === undefined) return '-';

        return Number(n).toLocaleString('vi-VN', {
            minimumFractionDigits: d,
            maximumFractionDigits: d
        })
    }
</script>

<body>
    <div class="app">
        <!-- HEADER -->
        <div class="header">
            <div class="logo">
                <img src="<?= base_url('uploads/logo_dashboard_srceen_mau.png') ?>" style="width:80px;height:80px;">
            </div>
            <div class="header-title">
                <span class="main-title">PHÒNG KẾ TOÁN - TÀI CHÍNH</span> <span class="sub-title" style="text-transform: uppercase"></span>
            </div>
            <div class="header-right">
                <div id="clock-date"><?= $dateStr ?></div>
                <div id="clock-time"><?= $timeStr ?></div>
            </div>
        </div>
        <div class="container">
            <div class="modal fade" id="chModal_dashboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_accounting'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_hcns'); ?>
        </div>
        <div class="body-footer">
            <div style="display: flex; flex-wrap: wrap; width: 100%;gap:3px;padding: 0px 4px 0 4px;">
                <button class="botton_tab"
                    id="dashboard-accounting"
                    onclick="init_report(this,'dashboard-accounting')">
                    Kế Toán <br>(1)
                </button>
                <button class="botton_tab"
                    id="dashboard-hcns" onclick="init_report(this,'dashboard-hcns')">
                    HCNS <br>(2)
                </button>
            </div>
        </div>
    </div>
</body>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
    (function startRealtimeClock_pkh_hm() {
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
    async function loginSocket() {
        try {
            const {
                data,
                status
            } = await $.ajax({
                type: 'POST',
                url: '<?= admin_url('socket_controler/login_socket?csrf_protection=true') ?>',
                data: {
                    user_id: '67',
                    user_name: 'thuan foso',
                    db_name: '<?= $dbname ?>'
                },
                dataType: 'json'
            });
            if (status) {
                localStorage.setItem('tokenSocket', data);
                return data;
            }
            throw new Error('Login socket không thành công');
        } catch (err) {
            console.error('Lỗi loginSocket:', err);
            throw err;
        }
    }

    async function connectSocket() {
        try {
            let token = localStorage.getItem('tokenSocket');
            if (!token || token === 'undefined' || token === 'null') {
                token = await loginSocket();
            }
            if (!token) throw new Error('Không có token');

            return new Promise((resolve, reject) => {
                const socket = io('<?= $link_connect_socket ?>', {
                    extraHeaders: {
                        auth: token
                    }
                });

                socket.on('connect', () => {
                    console.log('✅ Socket connected:', socket.id);
                    socket.emit('connectedData', {
                        user_id: '67',
                        user_name: 'thuan foso',
                        db_name: '<?= $dbname ?>'
                    });
                    resolve(socket);
                });

                socket.on('connect_error', err => {
                    console.error('❌ Socket connect_error:', err);
                    reject(err);
                });
            });
        } catch (err) {
            console.error('Lỗi connectSocket:', err);
            throw err;
        }
    }
    async function getSocket() {
        if (window.socket && window.socket.connected) return window.socket;
        window.socket = await connectSocket();
        return window.socket;
    }
    // ====== Lắng nghe socket và xử lý payload ======
    (async () => {
        const socket = await getSocket();
        socket.on('update_dashboard', (payload) => {
            location.reload();
        });
    })();
</script>
<script>
    var dashboard_accounting = $('#dashboard_accounting');
    var dashboard_hcns = $('#dashboard_hcns');

    function init_report(e, type) {
        dashboard_accounting.addClass('hide');
        dashboard_hcns.addClass('hide');

        $(".sub-title").text(' ');

        $('.botton_tab').removeClass('active');
        $('#' + type).addClass('active');

        if (type === 'dashboard-accounting') {
            $('#dashboard_accounting').removeClass('hide');
            $('.main-title').text('KẾ TOÁN');
            count_accounting();
        } else if (type === 'dashboard-hcns') {
            $('#dashboard_hcns').removeClass('hide');
            $('.main-title').text('HÀNH CHÍNH NHÂN SỰ');
            count_hcns();
        }
    }
    init_report('', 'dashboard-accounting');
    window.addEventListener('keydown', (e) => {
        const k = e.key.toLowerCase();

        if (k === '1') {
            init_report('', 'dashboard-accounting');
        }
        if (k === '2') {
            init_report('', 'dashboard-hcns');
        }

        if (k === 'z') {
            $('.page_1').removeClass('hide');
            $('.page_2').addClass('hide');
            $('.page_3').addClass('hide');
            $('.page_4').addClass('hide');
            $('.page_1_class_page').addClass('active');
            $('.page_2_class_page').removeClass('active');
            $('.page_3_class_page').removeClass('active');
            $('.page_4_class_page').removeClass('active');
        }
        if (k === 'x') {
            $('.page_1').addClass('hide');
            $('.page_2').removeClass('hide');
            $('.page_3').addClass('hide');
            $('.page_4').addClass('hide');
            $('.page_2_class_page').addClass('active');
            $('.page_1_class_page').removeClass('active');
            $('.page_3_class_page').removeClass('active');
            $('.page_4_class_page').removeClass('active');
        }
        if (k === 'c') {
            $('.page_1').addClass('hide');
            $('.page_2').addClass('hide');
            $('.page_3').removeClass('hide');
            $('.page_4').addClass('hide');
            $('.page_3_class_page').addClass('active');
            $('.page_1_class_page').removeClass('active');
            $('.page_2_class_page').removeClass('active');
            $('.page_4_class_page').removeClass('active');
        }
        if (k === 'v') {
            $('.page_1').addClass('hide');
            $('.page_2').addClass('hide');
            $('.page_3').addClass('hide');
            $('.page_4').removeClass('hide');
            $('.page_4_class_page').addClass('active');
            $('.page_1_class_page').removeClass('active');
            $('.page_2_class_page').removeClass('active');
            $('.page_3_class_page').removeClass('active');
        }

        if (k === 'a') {
            var el = $('.child-hcns[data-value="1"]')[0];
            changeFilterHcns(el, 1);
        }
        if (k === 's') {
            var el = $('.child-hcns[data-value="2"]')[0];
            changeFilterHcns(el, 2);
        }
    });
    let autoLoopIndex = 0;
    const autoLoopTypes = [
        'dashboard-accounting',
        'dashboard-hcns',
    ];
    let autoLoopTimer = null;
    var time = '<?= get_option('time_dashboard_srceen') ? get_option('time_dashboard_srceen') : 30 ?>';

    function startAutoLoop() {
        if (autoLoopTimer) clearTimeout(autoLoopTimer);

        function next() {
            init_report('', autoLoopTypes[autoLoopIndex]);
            autoLoopIndex = (autoLoopIndex + 1) % autoLoopTypes.length;
            autoLoopTimer = setTimeout(next, time * 60 * 1000); // 30 phút
            // autoLoopTimer = setTimeout(next, 10000); // 1 phút
        }
        next();
    }
    // Bắt đầu vòng lặp tự động khi trang tải xong
    $(document).ready(function() {
        startAutoLoop();
    });

    function openModal(id) {
        const m = document.getElementById(id);
        m.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('show');
        // đợi animation xong mới cho scroll lại
        setTimeout(() => {
            document.body.style.overflow = '';
        }, 250);
    }

    function initDataTable_ch(e, t, a, i, n, s) {
        var o = typeof e === "string" ? $("body").find("table" + e) : e;
        if (o.length === 0) return false;

        n = typeof n === "undefined" ? [] : n;
        s = typeof s === "undefined" ? [
            [0, "asc"]
        ] : (s.length === 1 ? [s] : s);

        var l = o.attr("data-default-order");
        if (l && l !== "") {
            try {
                var d = JSON.parse(l),
                    r = [];
                for (var c in d) {
                    if (o.find("thead th:eq(" + d[c][0] + ")").length > 0) r.push(d[c]);
                }
                if (r.length > 0) s = r;
            } catch (err) {}
        }

        // Config fallback
        var tables_pagination_limit = window.app?.options?.tables_pagination_limit ? parseFloat(window.app.options.tables_pagination_limit) : 10;
        var dt_length_menu_all = window.app?.lang?.dt_length_menu_all || "All";
        var datatables_lang = window.app?.lang?.datatables || {};
        var dt_button_reload = window.app?.lang?.dt_button_reload || "Reload";
        var dt_button_column_visibility = window.app?.lang?.dt_button_column_visibility || "Column visibility";
        var scroll_responsive_tables = window.app?.options?.scroll_responsive_tables || 0;

        // Page length options
        var p = [10, 25, 50, 100],
            _ = [10, 25, 50, 100];
        if ($.inArray(tables_pagination_limit, p) === -1) {
            p.push(tables_pagination_limit);
            _.push(tables_pagination_limit);
        }
        p.sort((e, t) => e - t);
        _.sort((e, t) => e - t);
        p.push(-1);
        _.push(dt_length_menu_all);

        // ✅ Nút reload luôn có mặt
        var dtButtons = typeof get_datatable_buttons === "function" ? get_datatable_buttons(o) : [];
        if (!Array.isArray(dtButtons)) dtButtons = [];
        dtButtons.unshift({
            text: '<i class="fa fa-refresh"></i>',
            className: 'btn btn-default btn-sm btn-dt-reload',
            action: function(e, dt) {
                dt.ajax.reload(null, false);
            },
            titleAttr: dt_button_reload
        });

        var config = {
            rowsGroup: [0, 1],
            language: datatables_lang,
            processing: true,
            retrieve: true,
            serverSide: true,
            paginate: true,
            searchDelay: 750,
            bDeferRender: true,
            responsive: true,
            autoWidth: false,

            // 👇 Giữ layout gốc của Perfex CRM
            dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",

            pageLength: tables_pagination_limit,
            lengthMenu: [p, _],
            columnDefs: [{
                    searchable: false,
                    targets: a
                },
                {
                    orderable: false,
                    targets: i
                }
            ],

            fnDrawCallback: function(e) {
                if (typeof _table_jump_to_page === "function") _table_jump_to_page(this, e);
                $(e.nTableWrapper).toggleClass("app_dt_empty", e.aoData.length === 0);
            },

            fnCreatedRow: function(row, data) {
                $(row).attr("data-title", data.Data_Title);
                $(row).attr("data-toggle", data.Data_Toggle);
            },

            initComplete: function() {
                var table = this;
                var reloadBtn = $(".btn-dt-reload");
                reloadBtn.attr("data-toggle", "tooltip").attr("title", dt_button_reload);
                var visBtn = $(".dt-column-visibility");
                visBtn.attr("data-toggle", "tooltip").attr("title", dt_button_column_visibility);

                if (table.hasClass("scroll-responsive") || scroll_responsive_tables === 1)
                    table.wrap('<div class="table-responsive"></div>');

                var empty = table.find(".dataTables_empty");
                if (empty.length) empty.attr("colspan", table.find("thead th").length);

                if (typeof is_mobile === "function" && is_mobile() && $(window).width() < 400 &&
                    table.find('tbody td:first-child input[type="checkbox"]').length > 0) {
                    table.DataTable().column(0).visible(false, false).columns.adjust();
                    $("a[data-target*='bulk_actions']").addClass("hide");
                }

                table.parents(".table-loading").removeClass("table-loading");
                table.removeClass("dt-table-loading");

                var last = table.find("thead th:last-child"),
                    first = table.find("thead th:first-child");
                if (last.text().trim() === (window.app?.lang?.options || "Options"))
                    last.addClass("not-export");
                if (first.find('input[type="checkbox"]').length > 0)
                    first.addClass("not-export");

                if (typeof mainWrapperHeightFix === "function") mainWrapperHeightFix();
            },

            order: s,

            ajax: {
                url: t,
                type: "POST",
                data: function(d) {
                    if (typeof csrfData !== "undefined") {
                        d[csrfData.token_name] = csrfData.hash;
                    }
                    for (var key in n) d[key] = $(n[key]).val();
                    if (o.attr("data-last-order-identifier"))
                        d.last_order_identifier = o.attr("data-last-order-identifier");
                }
            },

            buttons: dtButtons
        };

        if (o.hasClass("scroll-responsive") || scroll_responsive_tables === 1)
            config.responsive = false;

        var dt = (o = o.dataTable(config)).DataTable();

        // Ẩn cột không hiển thị
        var hiddenCols = [];
        o.find("th.not_visible").each(function() {
            hiddenCols.push(this.cellIndex);
        });
        setTimeout(() => {
            hiddenCols.forEach(idx => dt.columns(idx).visible(false, false).columns.adjust());
        }, 10);

        // Customizable table
        if (o.hasClass("customizable-table")) {
            var toggles = o.find("th.toggleable"),
                hiddenData = $("#hidden-columns-" + o.attr("id"));
            try {
                hiddenData = JSON.parse(hiddenData.text());
            } catch {
                hiddenData = [];
            }
            toggles.each(function() {
                var id = $(this).attr("id");
                if ($.inArray(id, hiddenData) > -1) dt.column("#" + id).visible(false);
            });
        }

        if (o.is(":hidden"))
            o.find(".dataTables_empty").attr("colspan", o.find("thead th").length);

        o.on("preXhr.dt", function(e, t) {
            if (t.jqXHR) t.jqXHR.abort();
        });

        return dt;
    }
</script>

</html>