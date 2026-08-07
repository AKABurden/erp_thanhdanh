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
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_quotes'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_sample'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_order_plan'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_open_production_order'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_ghep_size'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_dan_trang'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_ghi_kem'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_export'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_purchases'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_purchase_orders'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_purchase_products'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_accounting'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_hcns'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_task'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_ksnb'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_foso'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_technial'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_qa'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_kpi'); ?>
            <?php $this->load->view('admin/dashboard_srceen_office/dashboard_warehouse'); ?>
        </div>
        <div class="body-footer">
            <div style="display: flex; flex-wrap: wrap; width: 100%;gap:3px;padding: 0px 4px 0 4px;">
                <button class="botton_tab" id="dashboard-quotes"
                    onclick="init_report(this,'dashboard-quotes')">
                    Báo giá <br>(1)
                </button>
                <button class="botton_tab"
                    id="dashboard-sample"
                    onclick="init_report(this,'dashboard-sample')">
                    PTM <br>(2)
                </button>
                <button class="botton_tab"
                    id="dashboard-order-plan"
                    onclick="init_report(this,'dashboard-order-plan')">
                    KH Đơn hàng <br>(3)
                </button>
                <button class="botton_tab"
                    id="dashboard-open-production-order"
                    onclick="init_report(this,'dashboard-open-production-order')">
                    Mở lệnh <br>(4)
                </button>
                <button class="botton_tab"
                    id="dashboard-ghep-size"
                    onclick="init_report(this,'dashboard-ghep-size')">
                    Ghép Size <br>(5)
                </button>
                <button class="botton_tab"
                    id="dashboard-dan-trang"
                    onclick="init_report(this,'dashboard-dan-trang')">
                    Dàn Trang <br>(6)
                </button>
                <button class="botton_tab"
                    id="dashboard-ghi-kem"
                    onclick="init_report(this,'dashboard-ghi-kem')">
                    Ghi Kẽm <br>(7)
                </button>
                <button class="botton_tab"
                    id="dashboard-export"
                    onclick="init_report(this,'dashboard-export')">
                    Xuất Kho <br>(8)
                </button>
                <button class="botton_tab"
                    id="dashboard-purchases"
                    onclick="init_report(this,'dashboard-purchases')">
                    Mua Hàng <br>(9)
                </button>
                <button class="botton_tab"
                    id="dashboard-purchase_orders"
                    onclick="init_report(this,'dashboard-purchase_orders')">
                    Về Hàng <br>(q)
                </button>
                <button class="botton_tab"
                    id="dashboard-purchase_products"
                    onclick="init_report(this,'dashboard-purchase_products')">
                    Nhập Hàng TP <br>(w)
                </button>
                <button class="botton_tab"
                    id="dashboard-accounting"
                    onclick="init_report(this,'dashboard-accounting')">
                    Kế Toán <br>(e)
                </button>
                <button class="botton_tab"
                    id="dashboard-hcns" onclick="init_report(this,'dashboard-hcns')">
                    HCNS <br>(r)
                </button>
                <button class="botton_tab"
                    id="dashboard-task" data-value="dashboard-task" onclick="init_report(this,'dashboard-task')">
                    Công việc <br>(t)
                </button>
                <button class="botton_tab" id="dashboard-ksnb" onclick="init_report(this,'dashboard-ksnb')">
                    KSNB <br>(y)
                </button>
                <button class="botton_tab" id="dashboard-foso" onclick="init_report(this,'dashboard-foso')">
                    Foso <br>(u)
                </button>
                <button class="botton_tab" id="dashboard-technial" onclick="init_report(this,'dashboard-technial')">
                    Kỹ Thuật <br>(i)
                </button>
                <button class="botton_tab" id="dashboard-qa" onclick="init_report(this,'dashboard-qa')">
                    QA <br>(o)
                </button>
                <button class="botton_tab" id="dashboard-kpi" onclick="init_report(this,'dashboard-kpi')">
                    KPI <br>(p)
                </button>
                <button class="botton_tab" id="dashboard-warehouse" onclick="init_report(this,'dashboard-warehouse')">
                    TỒN KHO <br>(a)
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
    var dashboard_quotes = $('#dashboard_quotes');
    var dashboard_sample = $('#dashboard_sample');
    var dashboard_order_plan = $('#dashboard_order_plan');
    var dashboard_open_production_order = $('#dashboard_open_production_order');
    var dashboard_ghep_size = $('#dashboard_ghep_size');
    var dashboard_dan_trang = $('#dashboard_dan_trang');
    var dashboard_ghi_kem = $('#dashboard_ghi_kem');
    var dashboard_xuat_kho = $('#dashboard_export');
    var dashboard_mua_hang = $('#dashboard_purchases');
    var dashboard_purchase_orders = $('#dashboard_purchase_orders');
    var dashboard_purchase_products = $('#dashboard_purchase_products');
    var dashboard_accounting = $('#dashboard_accounting');
    var dashboard_hcns = $('#dashboard_hcns');
    var dashboard_task = $('#dashboard_task');
    var dashboard_ksnb = $('#dashboard_ksnb');
    var dashboard_foso = $('#dashboard_foso');
    var dashboard_technial = $('#dashboard_technial');
    var dashboard_qa = $('#dashboard_qa');
    var dashboard_kpi = $('#dashboard_kpi');
    var dashboard_warehouse = $('#dashboard_warehouse');

    function init_report(e, type) {
        dashboard_quotes.addClass('hide');
        dashboard_sample.addClass('hide');
        dashboard_order_plan.addClass('hide');
        dashboard_open_production_order.addClass('hide');
        dashboard_ghep_size.addClass('hide');
        dashboard_dan_trang.addClass('hide');
        dashboard_ghi_kem.addClass('hide');
        dashboard_xuat_kho.addClass('hide');
        dashboard_mua_hang.addClass('hide');
        dashboard_purchase_orders.addClass('hide');
        dashboard_purchase_products.addClass('hide');
        dashboard_accounting.addClass('hide');
        dashboard_hcns.addClass('hide');
        dashboard_task.addClass('hide');
        dashboard_ksnb.addClass('hide');
        dashboard_foso.addClass('hide');
        dashboard_technial.addClass('hide');
        dashboard_qa.addClass('hide');
        dashboard_kpi.addClass('hide');
        dashboard_warehouse.addClass('hide');

        stopAutoLoopTask();
        stopAutoLoopKpi();
        $(".sub-title").text(' ');

        $('.botton_tab').removeClass('active');
        $('#' + type).addClass('active');
        if (type === 'dashboard-quotes') {
            dashboard_quotes.removeClass('hide');
            $('.main-title').text('BÁO GIÁ');
            countQuotes();
        } else if (type === 'dashboard-sample') {
            dashboard_sample.removeClass('hide');
            $('.main-title').text('PHÁT TRIỂN MẪU');
            countsample();
        } else if (type === 'dashboard-order-plan') {
            $('#dashboard_order_plan').removeClass('hide');
            $('.main-title').text('KẾ HOẠCH ĐƠN HÀNG');
            countorder_plan();
        } else if (type === 'dashboard-open-production-order') {
            $('#dashboard_open_production_order').removeClass('hide');
            $('.main-title').text('MỞ LỆNH SẢN XUẤT');
            count_open_production();
        } else if (type === 'dashboard-ghep-size') {
            $('#dashboard_ghep_size').removeClass('hide');
            $('.main-title').text('GHÉP SIZE');
            count_ghep_size();
        } else if (type === 'dashboard-dan-trang') {
            $('#dashboard_dan_trang').removeClass('hide');
            $('.main-title').text('DÀN TRANG');
            count_dan_trang();
        } else if (type === 'dashboard-ghi-kem') {
            $('#dashboard_ghi_kem').removeClass('hide');
            $('.main-title').text('GHI KẼM');
            count_ghi_kem();
        } else if (type === 'dashboard-export') {
            $('#dashboard_export').removeClass('hide');
            $('.main-title').text('XUẤT KHO');
            count_export();
        } else if (type === 'dashboard-purchases') {
            $('#dashboard_purchases').removeClass('hide');
            $('.main-title').text('MUA HÀNG');
            count_purchases();
        } else if (type === 'dashboard-purchase_orders') {
            $('#dashboard_purchase_orders').removeClass('hide');
            $('.main-title').text('VỀ HÀNG');
            countpurchase_orders();
        } else if (type === 'dashboard-purchase_products') {
            $('#dashboard_purchase_products').removeClass('hide');
            $('.main-title').text('NHẬP HÀNG TP');
            countpurchase_products();
        } else if (type === 'dashboard-accounting') {
            $('#dashboard_accounting').removeClass('hide');
            $('.main-title').text('KẾ TOÁN');
            count_accounting();
        } else if (type === 'dashboard-hcns') {
            $('#dashboard_hcns').removeClass('hide');
            $('.main-title').text('HÀNH CHÍNH NHÂN SỰ');
            count_hcns();
        } else if (type === 'dashboard-task') {
            $('#dashboard_task').removeClass('hide');
            $('.main-title').text('CÔNG VIỆC');
            countTask();
            startAutoLoopTask(true);
        } else if (type === 'dashboard-ksnb') {
            $('#dashboard_ksnb').removeClass('hide');
            $('.main-title').text('KIỂM SOÁT NỘI BỘ');
            count_ksnb();
        } else if (type === 'dashboard-foso') {
            $('#dashboard_foso').removeClass('hide');
            $('.main-title').text('DỮ LIỆU FOSO');
            count_foso();
        } else if (type === 'dashboard-technial') {
            $('#dashboard_technial').removeClass('hide');
            $('.main-title').text('DỮ LIỆU KỸ THUẬT');
            count_technial();
        } else if (type === 'dashboard-qa') {
            $('#dashboard_qa').removeClass('hide');
            $('.main-title').text('QA');
            count_qa();
        } else if (type === 'dashboard-kpi') {
            $('#dashboard_kpi').removeClass('hide');
            $('.main-title').text('THỐNG KÊ KPI');
            count_kpi();
            startAutoLoopKpi(true);
        } else if (type === 'dashboard-warehouse') {
            $('#dashboard_warehouse').removeClass('hide');
            $('.main-title').text('TỔNG QUAN TỒN KHO');
            count_warehouse();
        }
    }
    init_report('', 'dashboard-quotes');
    window.addEventListener('keydown', (e) => {
        const k = e.key.toLowerCase();
        if (k === '1') {
            init_report('', 'dashboard-quotes');
        }
        if (k === '2') {
            init_report('', 'dashboard-sample');
        }
        if (k === '3') {
            init_report('', 'dashboard-order-plan');
        }
        if (k === '4') {
            init_report('', 'dashboard-open-production-order');
        }
        if (k === '5') {
            init_report('', 'dashboard-ghep-size');
        }
        if (k === '6') {
            init_report('', 'dashboard-dan-trang');
        }
        if (k === '7') {
            init_report('', 'dashboard-ghi-kem');
        }
        if (k === '8') {
            init_report('', 'dashboard-export');
        }
        if (k === '9') {
            init_report('', 'dashboard-purchases');
        }
        if (k === 'q') {
            init_report('', 'dashboard-purchase_orders');
        }
        if (k === 'w') {
            init_report('', 'dashboard-purchase_products');
        }
        if (k === 'e') {
            init_report('', 'dashboard-accounting');
        }
        if (k === 'r') {
            init_report('', 'dashboard-hcns');
        }
        if (k === 't') {
            init_report('', 'dashboard-task');
        }
        if (k === 'y') {
            init_report('', 'dashboard-ksnb');
        }
        if (k === 'u') {
            init_report('', 'dashboard-foso');
        }
        if (k === 'i') {
            init_report('', 'dashboard-technial');
        }
        if (k === 'o') {
            init_report('', 'dashboard-qa');
        }
        if (k === 'p') {
            init_report('', 'dashboard-kpi');
        }
        if (k === 'a') {
            init_report('', 'dashboard-warehouse');
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
        if (k === 'd') {
            var el = $('.child-ksnb[data-value="1"]')[0];
            changeFilterKsnb(el, 1);
        }
        if (k === 'f') {
            var el = $('.child-ksnb[data-value="3"]')[0];
            changeFilterKsnb(el, 3);
        }
        if (k === 'g') {
            var el = $('.child-ksnb[data-value="4"]')[0];
            changeFilterKsnb(el, 4);
        }
        if (k === 'h') {
            var el = $('.child-ksnb[data-value="5"]')[0];
            changeFilterKsnb(el, 5);
        }
        if (k === 'j') {
            var el = $('.child-ksnb[data-value="2"]')[0];
            changeFilterKsnb(el, 2);
        }
    });
    (function() {
        const INTERVAL_MS = 5 * 60 * 1000; // 5 phút
        let pageToggleTimer = null;
        let currentPage = $('.page_1').hasClass('hide') ? 2 : 1;

        function showPage1() {
            $('.page_1').removeClass('hide');
            $('.page_2').addClass('hide');
            $('.page_3').addClass('hide');
            $('.page_4').addClass('hide');
            $('.page_1_class_page').addClass('active');
            $('.page_2_class_page').removeClass('active');
            $('.page_3_class_page').removeClass('active');
            $('.page_4_class_page').removeClass('active');
            currentPage = 1;
            restartPageToggleTimer();
        }

        function showPage2() {
            $('.page_1').addClass('hide');
            $('.page_2').removeClass('hide');
            $('.page_3').addClass('hide');
            $('.page_4').addClass('hide');
            $('.page_2_class_page').addClass('active');
            $('.page_1_class_page').removeClass('active');
            $('.page_3_class_page').removeClass('active');
            $('.page_4_class_page').removeClass('active');
            currentPage = 2;
            restartPageToggleTimer();
        }

        function showPage3() {
            $('.page_1').addClass('hide');
            $('.page_2').addClass('hide');
            $('.page_3').removeClass('hide');
            $('.page_4').addClass('hide');
            $('.page_3_class_page').addClass('active');
            $('.page_1_class_page').removeClass('active');
            $('.page_2_class_page').removeClass('active');
            $('.page_4_class_page').removeClass('active');
            currentPage = 3;
            restartPageToggleTimer();
        }

        function showPage4() {
            $('.page_1').addClass('hide');
            $('.page_2').addClass('hide');
            $('.page_3').addClass('hide');
            $('.page_4').removeClass('hide');
            $('.page_4_class_page').addClass('active');
            $('.page_1_class_page').removeClass('active');
            $('.page_2_class_page').removeClass('active');
            $('.page_3_class_page').removeClass('active');
            currentPage = 4;
            restartPageToggleTimer();
        }

        function togglePages() {
            if (currentPage === 1) {
                showPage2();
            } else if (currentPage === 2) {
                showPage3();
            } else if (currentPage === 3) {
                showPage4();
            } else {
                showPage1();
            }
        }

        function restartPageToggleTimer() {
            if (pageToggleTimer) clearInterval(pageToggleTimer);
            pageToggleTimer = setInterval(togglePages, INTERVAL_MS);
        }

        // Nếu người dùng bấm Z/X bằng phím tắt đã có thì reset lại timer để tính 5 phút từ lần thao tác cuối
        window.addEventListener('keydown', (e) => {
            const k = (e.key || '').toLowerCase();
            if (k === 'z' || k === 'x') {
                // chỉ reset timer, DOM đã được thay đổi bởi handler hiện tại
                restartPageToggleTimer();
            }
        });

        // Bắt đầu tự động chuyển trang
        restartPageToggleTimer();

        // Tùy chọn expose hàm nếu cần gọi trực tiếp từ nơi khác
        window.showPage1 = showPage1;
        window.showPage2 = showPage2;
        window.restartPageToggleTimer = restartPageToggleTimer;
    })();
    let autoLoopIndex = 0;
    const autoLoopTypes = [
        'dashboard-quotes',
        'dashboard-sample',
        'dashboard-order-plan',
        'dashboard-open-production-order',
        'dashboard-ghep-size',
        'dashboard-dan-trang',
        'dashboard-ghi-kem',
        'dashboard-export',
        'dashboard-purchases',
        'dashboard-purchase_orders',
        'dashboard-purchase_products',
        'dashboard-accounting',
        'dashboard-hcns',
        'dashboard-task',
        'dashboard-ksnb',
        'dashboard-foso',
        'dashboard-technial',
        'dashboard-qa',
        'dashboard-kpi',
        'dashboard-warehouse',
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