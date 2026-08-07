<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Orchestrator</title>
    <?php $this->load->view('admin/dashboard_srceen_sx/dashboard_orchestrator_css'); ?>
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
            <?php $this->load->view('admin/dashboard_srceen_sx/dashboard_manufactures'); ?>
            <?php $this->load->view('admin/dashboard_srceen_sx/dashboard_delivery'); ?>
        </div>
        <div class="body-footer">
            <div style="display: flex; flex-wrap: wrap; width: 100%;gap:3px;padding: 0px 4px 0 4px;">
                <button class="botton_tab" id="dashboard-manufactures"
                    onclick="init_report(this,'dashboard-manufactures')">
                    Lệnh sản xuất <br>(1)
                </button>
                <button class="botton_tab"
                    id="dashboard-delivery"
                    onclick="init_report(this,'dashboard-delivery')">
                    Giao hàng <br>(2)
                </button>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
    var dashboard_manufactures = $('#dashboard_manufactures');
    var dashboard_delivery = $('#dashboard_delivery');

    function init_report(e, type) {
        dashboard_manufactures.addClass('hide');
        dashboard_delivery.addClass('hide');
        $(".sub-title").text(' ');

        $('.botton_tab').removeClass('active');
        $('#' + type).addClass('active');
        if (type === 'dashboard-manufactures') {
            dashboard_manufactures.removeClass('hide');
            $('.main-title').text('LỆNH SẢN XUẤT');
            $('.sub-menu-child-new.active').click();
        } else if (type === 'dashboard-delivery') {
            dashboard_delivery.removeClass('hide');
            $('.main-title').text('PHIẾU GIAO HÀNG');
            countdelivery();
        } 
    }
    init_report('', 'dashboard-manufactures');
    window.addEventListener('keydown', (e) => {
        const k = e.key.toLowerCase();
        if (k === '1') {
            init_report('', 'dashboard-manufactures');
        }
        if (k === '2') {
            init_report('', 'dashboard-delivery');
        }
    });
    
    let autoLoopIndex = 0;
    const autoLoopTypes = [
        'dashboard-manufactures',
        'dashboard-delivery',
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

    function tnhInitDataTable(selector, url, initParams, notsearchable = [0], notsortable = [0], fnserverparams = [], defaultorder = [0], fixedColumns = {leftColumns: 0, rightColumns: 0}, btnButton = 0) {
        // var table = typeof (selector) == 'string' ? $("body").find('table' + selector) : selector;
        table = $(selector);
        if (table.length === 0) {
            return false;
        }


        fnserverparams = (fnserverparams == 'undefined' || typeof (fnserverparams) == 'undefined') ? [] : fnserverparams;

        // If not order is passed order by the first column
        if (typeof (defaultorder) == 'undefined') {
            defaultorder = [
                [0, 'asc']
            ];
        } else {
            if (defaultorder.length === 1) {
                defaultorder = [defaultorder];
            }
        }

        var user_table_default_order = table.attr('data-default-order');

        if (!empty(user_table_default_order)) {
            var tmp_new_default_order = JSON.parse(user_table_default_order);
            var new_defaultorder = [];
            for (var i in tmp_new_default_order) {
                // If the order index do not exists will throw errors
                if (table.find('thead th:eq(' + tmp_new_default_order[i][0] + ')').length > 0) {
                    new_defaultorder.push(tmp_new_default_order[i]);
                }
            }
            if (new_defaultorder.length > 0) {
                defaultorder = new_defaultorder;
            }
        }

        var length_options = [10, 25, 50, 100];
        var length_options_names = [10, 25, 50, 100];

        app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit);

        if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
            length_options.push(app.options.tables_pagination_limit);
            length_options_names.push(app.options.tables_pagination_limit);
        }

        length_options.sort(function (a, b) {
            return a - b;
        });
        length_options_names.sort(function (a, b) {
            return a - b;
        });

        length_options.push(-1);
        length_options_names.push(app.lang.dt_length_menu_all);
        var width_document = $(document).width();
        if (Number(width_document) <= 768) {
            fixedColumns.leftColumns = 0;
            fixedColumns.rightColumns = 0;
        }
        var dtSettings = {
            "language": app.lang.datatables,
            "processing": true,
            "retrieve": true,
            "serverSide": true,
            'paginate': true,
            'searchDelay': 750,
            "bDeferRender": true,
            // scrollY: '400px',
            // scrollX: true,
            // fixedColumns: {
            //     leftColumns: fixedColumns.leftColumns,
            //     rightColumns: fixedColumns.rightColumns
            // },
            // "responsive": true,
            "autoWidth": false,
            dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>",
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [length_options, length_options_names],
            "columnDefs": [{
                "searchable": false,
                "targets": notsearchable,
            }, {
                "sortable": false,
                "targets": notsortable
            }],
            "fnDrawCallback": function (oSettings) {
                _table_jump_to_page(this, oSettings);
                if (oSettings.aoData.length === 0) {
                    $(oSettings.nTableWrapper).addClass('app_dt_empty');
                } else {
                    $(oSettings.nTableWrapper).removeClass('app_dt_empty');
                }
            },
            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                // If tooltips found
                $(nRow).attr('data-title', aData.Data_Title);
                $(nRow).attr('data-toggle', aData.Data_Toggle);
            },
            "initComplete": function (settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
            "order": defaultorder,
            "ajax": {
                "url": url,
                "type": "POST",
                "data": function (d) {
                    if (typeof (csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                }
            },
            buttons: get_datatable_buttons(table),
        };

        if (table.hasClass('scroll-responsive') || app.options.scroll_responsive_tables == 1) {
            dtSettings.responsive = false;
        }

        //tnh custom
        if (initParams) {
            if (typeof initParams.order !== 'undefined') {
                dtSettings.order = initParams.order;
            }
            if (typeof initParams.ajax !== 'undefined') {
                dtSettings.ajax = initParams.ajax;
            }

            if (typeof initParams.sAjaxSource !== 'undefined') {
                dtSettings.sAjaxSource = initParams.sAjaxSource;
            }

            if (typeof initParams.fnServerData !== 'undefined') {
                dtSettings.fnServerData = initParams.fnServerData;
            }

            if (typeof initParams.columnDefs !== 'undefined') {
                dtSettings.columnDefs = initParams.columnDefs;
            }

            if (typeof initParams.fixedHeader !== 'undefined') {
                dtSettings.fixedHeader = initParams.fixedHeader;
            }

            if (typeof initParams.responsive !== 'undefined') {
                dtSettings.responsive = initParams.responsive;
            }

            if (typeof initParams.searching !== 'undefined') {
                dtSettings.searching = initParams.searching;
            }

            if (typeof initParams.ordering !== 'undefined') {
                dtSettings.ordering = initParams.ordering;
            }

            if (typeof initParams.fixedColumns !== 'undefined') {
                dtSettings.fixedColumns = initParams.fixedColumns;
            }

            if (typeof initParams.scrollY !== 'undefined') {
                dtSettings.scrollY = initParams.scrollY;
            }

            if (typeof initParams.scrollX !== 'undefined') {
                dtSettings.scrollX = initParams.scrollX;
            }

            if (typeof initParams.createdRow !== 'undefined') {
                dtSettings.fnCreatedRow = initParams.createdRow;
            }

            if (typeof initParams.dom !== 'undefined') {
                dtSettings.dom = initParams.dom;
            }

            if (typeof initParams.paging !== 'undefined') {
                dtSettings.paging = initParams.paging;
            }

            if (typeof initParams.info !== 'undefined') {
                dtSettings.info = initParams.info;
            }

            if (typeof initParams.fnRowCallback !== 'undefined') {
                dtSettings.fnRowCallback = initParams.fnRowCallback;
            }

            if (typeof initParams.initComplete !== 'undefined') {
                dtSettings.initComplete = initParams.initComplete;
            }

            if (typeof initParams.buttons !== 'undefined') {
                dtSettings.buttons = initParams.buttons;
            }

            if (typeof initParams.btnButtons !== 'undefined') {
                var buttonTwo = [{
                    extend: "excel",
                    text: app.lang.dt_button_excel,
                    footer: !0,
                    exportOptions: {
                        columns: [":not(.not-export)"],
                        rows: function (t) {
                            return _dt_maybe_export_only_selected_rows(t, $('#table-items-modal'))
                        },
                        format: {
                            header: function ( data, columnIdx ) {
                                var _data = `<p>${data}</p>`;
                                return $(_data).text().toUpperCase();
                            },
                            body: function(data, row, column, node) {
                                data = $('<p>' + data + '</p>').text();
                                if(column == 4){
                                    let trimmedText = data.trim();
                                    let noWhiteSpaceText = trimmedText.replace(/\s+/g, " ");
                                    let noCommaText = noWhiteSpaceText.replace(/,/g, "");
                                    return noCommaText;
                                }
                                else{
                                    // return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
                                    return $.isNumeric(data.replace(/,/g, '')) ? data.replace(/,/g, '') : data;
                                }
                            },
                            footer: function ( data, columnIdx ) {
                                data = $('<p>' + data + '</p>').text();
                                return $.isNumeric(data.replace(/,/g, '')) ? data.replace(/,/g, '') : data.toUpperCase();
                                // return data.toUpperCase();
                            },
                        }
                    },
                    customize: function (xlsx) {
                        var footers = $('row:last-child', sheet); // Giả định dòng cuối cùng là footer

                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var mergeCells = $('mergeCells', sheet);

                        footers.each(function() {
                            var cols = $(this).find('c');
                            var prevColspan = 0;

                            cols.each(function(index) {
                                var cell = $(this);
                                var cellAddress = cell.attr('r');
                                var colIndex = cellAddress.replace(/[0-9]/g, ''); // Lấy chỉ số cột từ địa chỉ ô

                                // Bỏ qua các ô bị trùng do colspan
                                if (prevColspan > 0) {
                                    prevColspan--;
                                    cell.remove();
                                    return;
                                }

                                // Kiểm tra và thiết lập colspan
                                var colspan = 1; // Đặt giá trị colspan của ô tại đây nếu có
                                if (colspan > 1) {
                                    // Thay đổi giá trị này theo colspan thực tế
                                    prevColspan = colspan - 1;

                                    // Mã xử lý để kết hợp các ô nếu cần thiết
                                    var newCellAddress = colIndex + (index + 1); // Điều chỉnh theo nhu cầu của bạn
                                    cell.attr('r', newCellAddress);
                                    cell.attr('s', '0'); // Đặt style ID theo nhu cầu của bạn
                                    // Bạn có thể cần cập nhật nội dung của ô hoặc giá trị thuộc tính khác
                                }
                            });
                        });

                        var columsExcel = [
                            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
                            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
                            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
                            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
                        ];


                        var downrows = 0;
                        var code = '';
                        var list_name = '';
                        var titleExcel = $(table).attr('title_excel');
                        if(titleExcel) {
                            titleExcel = JSON.parse(titleExcel);
                            code = typeof titleExcel[0] != 'undefined' ? titleExcel[0] : '';
                            list_name = typeof titleExcel[1] != 'undefined' ? titleExcel[1] : '';

                        }
                        // console.log(list_name);
                        // code = $('.code_data').text();
                        // name = $('.name_data').text();

                        var downrows = 2;
                        mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                            attr: {
                                ref: 'A1:G1'
                            }
                        }));
                        mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                            attr: {
                                ref: 'A2:G2'
                            }
                        }));
                        if($.isArray(list_name)) {
                            var isStt = 0;
                            $.each(list_name, function(index, value) {
                                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                    attr: {
                                        ref: columsExcel[isStt] + '3:'+columsExcel[isStt + 1] + '3'
                                    }
                                }));
                                console.log(columsExcel[isStt] + '3:'+columsExcel[isStt + 1] + '3')
                                isStt = isStt + 2;
                            })
                        }
                        else {
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'A3:G3'
                                }
                            }));
                        }


                        var clRow = $('row', sheet);

                        function _createNode(doc, nodeName, opts) {
                            var tempNode = doc.createElement(nodeName);

                            if (opts) {
                                if (opts.attr) {
                                    $(tempNode).attr(opts.attr);
                                }

                                if (opts.children) {
                                    $.each(opts.children, function (key, value) {
                                        tempNode.appendChild(value);
                                    });
                                }

                                if (opts.text !== null && opts.text !== undefined) {
                                    tempNode.appendChild(doc.createTextNode(opts.text));
                                }
                            }

                            return tempNode;
                        }

                        //update Row
                        clRow.each(function () {
                            var attr = $(this).attr('r');
                            var ind = parseInt(attr);
                            ind = ind + downrows;
                            $(this).attr("r", ind);
                        });

                        // Update  row > c
                        var maxRow = 0;//lấy số lượng max của cột để kẻ khung
                        $('row c ', sheet).each(function () {
                            var attr = $(this).attr('r');
                            var pre = attr.substring(0, 1);
                            var ind = parseInt(attr.substring(1, attr.length));
                            ind = ind + downrows;
                            if($.inArray(pre, columsExcel) > maxRow) {
                                maxRow = $.inArray(pre, columsExcel);
                            }

                            $(this).attr("r", pre + ind);
                            if(ind > 3) {
                                $(this).attr("s", '25');
                            }
                            else if(ind == 3) {
                                $(this).attr("s", '22');
                            }
                            if(ind == 4) {
                                $(this).attr("s", '27');
                            }
                        });
                        maxRow += 1; // vì mảng bắt đầu từ 0 đến + 1 để đi theo sheet của excel

                        $('row', sheet).each(function (index, value) {
                            if(index >= 2) {
                                var indexRow = $(value).attr('r');
                                for(var i = 0; i < maxRow; i++) {
                                    if($($(sheet).find('row')[index]).find(`c[r="${columsExcel[i]}${indexRow}"]`).length == 0) { //kiểm tra cột nào chưa có để bổ sung
                                        if(i == 0) {// là cột đầu tiên
                                            $($(sheet).find('row')[index]).prepend(`<c r="${columsExcel[i]}${indexRow}" s="25"><t><is></is></t></c>`);
                                        }
                                        else {// là các cột tiếp theo.. thêm vào để kẻ khung table
                                            $($(sheet).find('row')[index]).find(`c[r="${columsExcel[i - 1]}${indexRow}"]`).after(`<c r="${columsExcel[i]}${indexRow}" s="25"><t><is></is></t></c>`);
                                        }
                                    }
                                }
                            }
                        })
                        function Addrow(index, data) {
                            msg = '<row r="' + index + '">'
                            for (i = 0; i < data.length; i++) {
                                var key = data[i].k;
                                var value = data[i].v;
                                msg += '<c t="inlineStr" r="' + key + index + '" s="' + (index == 1 ? '51' : '2') + '">';
                                msg += '<is>';
                                msg += '    <t>' + value + '</t>';
                                msg += '</is>';
                                msg += '</c>';
                            }
                            msg += '</row>';
                            return msg;
                        }

                        var r3 = Addrow(1, [{ k: 'A', v: escapeHTML($('head title').text()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                        var r1 = code ? Addrow(2, [{ k: 'A', v: escapeHTML(code.toUpperCase()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]) : '';
                        var r2 = ''
                        if($.isArray(list_name)) {
                            var isStt = 0;
                            var listRow = [];
                            $.each(list_name, function(index, value) {
                                listRow.push({ k: columsExcel[isStt], v: (value.toUpperCase()) });
                                isStt = isStt + 2;
                            })
                            r2 = Addrow(3, listRow);
                        }
                        else {
                            r2 = Addrow(3, [{ k: 'A', v: escapeHTML(list_name.toUpperCase()) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                        }

                        sheet.childNodes[0].childNodes[1].innerHTML = r3 + r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML;
                        $('row', sheet).each(function (index, value) {
                            // console.log(value);
                        })
                    }
                }];
                var buttonOne = get_datatable_buttons(table);
                delete buttonOne[0];
                buttonOne.push(buttonTwo);
                dtSettings.buttons = buttonOne;
            }
            // console.log(dtSettings);
        }

        table = table.dataTable(dtSettings);
        var tableApi = table.DataTable();

        var hiddenHeadings = table.find('th.not_visible');
        var hiddenIndexes = [];

        $.each(hiddenHeadings, function () {
            hiddenIndexes.push(this.cellIndex);
        });

        setTimeout(function () {
            for (var i in hiddenIndexes) {
                tableApi.columns(hiddenIndexes[i]).visible(false, false).columns.adjust();
            }
        }, 10);

        if (table.hasClass('customizable-table')) {

            var tableToggleAbleHeadings = table.find('th.toggleable');
            var invisible = $('#hidden-columns-' + table.attr('id'));
            try {
                invisible = JSON.parse(invisible.text());
            } catch (err) {
                invisible = [];
            }

            $.each(tableToggleAbleHeadings, function () {
                var cID = $(this).attr('id');
                if ($.inArray(cID, invisible) > -1) {
                    tableApi.column('#' + cID).visible(false);
                }
            });
        }

        // Fix for hidden tables colspan not correct if the table is empty
        if (table.is(':hidden')) {
            table.find('.dataTables_empty').attr('colspan', table.find('thead th').length);
        }

        table.on('preXhr.dt', function (e, settings, data) {
            if (settings.jqXHR) settings.jqXHR.abort();
        });

        if (typeof initParams.btnButtons !== 'undefined') {
            table.on('draw.dt', function(e, settings, data) {
                var paymentReceivedReportsTable = $(this).DataTable();
                var title_excel = paymentReceivedReportsTable.ajax.json().title_excel;
                if(title_excel) {
                    $(this).attr('title_excel', JSON.stringify(title_excel));
                }
            });
        }

        return tableApi;
    }

</script>

</html>