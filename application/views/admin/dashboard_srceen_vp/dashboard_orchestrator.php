<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Orchestrator</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #CBD0D8, #CBD0D8);
            color: #0348A2;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .dash-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }


        .dash-active {
            display: block;
        }

        .dash-wrap {
            display: none !important;
        }

        .dash-wrap.dash-active {
            display: block !important;
        }
    </style>
</head>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
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

    function countkinhdoanh() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/countkinhdoanh') ?>", res => {
            if (!res || !res.success) return;
            UpdateStatscountkinhdoanh(res.stats || {});
        });
    }

    function countwarehouse() {
        $.getJSON("<?= site_url('dashboard_srceen_vp/countwarehouse') ?>", res => {
            if (!res || !res.success) return;
            UpdateStatscountwarehouse(res.stats || {});
        });
    }

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

    function UpdateStatscountkinhdoanh(stats) {
        $(".js-dxtc_success").text(mainFmt(mainNum(stats?.status_finish_1), 0));
        $(".js-dxtc_pending").text(mainFmt(mainNum(stats?.status_finish_0), 0));
        $(".js-dxtc_code_custom_null").text(mainFmt(mainNum(stats?.count_code_custom_null), 0));
        $(".js-count_delivery_supplier_code_null").text(mainFmt(mainNum(stats?.count_delivery_supplier_code_null), 0));

        $(".js-count_delivery_cx").text(mainFmt(mainNum(stats?.count_delivery_cx), 0));
        $(".js-count_ycc").text(mainFmt(mainNum(stats?.count_ycc), 0));
        $(".js-count_dxtc").text(mainFmt(mainNum(stats?.countdxtc), 0));
        $(".js-count_delivery_thu").text(mainFmt(mainNum(stats?.count_delivery_thu), 0));
    }

    function UpdateStatscountwarehouse(stats) {
        $(".js-dxnb_approved").text(mainFmt(mainNum(stats?.status_finish_1), 0));
        $(".js-dxnb_un_approved").text(mainFmt(mainNum(stats?.status_finish_0), 0));

        $(".js-count_purchase_import").text(mainFmt(mainNum(stats?.count_purchase_order_import), 0));
        $(".js-count_purchase_not_import").text(mainFmt(mainNum(stats?.count_purchase_order_not_import), 0));

        $(".js-count_purchase_products_import").text(mainFmt(mainNum(stats?.total_pp), 0));
        $(".js-count_purchase_products_not_import").text(mainFmt(mainNum(stats?.total_bp), 0));
    }
    countkinhdoanh();
    countwarehouse();
    setInterval(() => {
        countkinhdoanh();
        countwarehouse();
    }, 60000);
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
        socket.on('update_dashboard_warehouse', (payload) => {
            countwarehouse();

        });
    })();
</script>

<body>

    <div id="wrap_planning_department" class="dash-wrap dash-active">
        <?php $this->load->view('admin/dashboard_srceen_vp/planning_department/planning_department');
        ?>
    </div>
    <div id="wrap_planning_department_table" class="dash-wrap  ">
        <?php //$this->load->view('admin/dashboard_srceen_vp/planning_department/planning_department_table');
        ?>
    </div>
    <div id="wrap_business_department" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/business_department/business_department');
        ?>
    </div>
    <div id="wrap_financial_accounting" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting/financial_accounting');
        ?>
    </div>
    <div id="wrap_financial_accounting_customs" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_customs/financial_accounting_customs');
        ?>
    </div>
    <div id="wrap_financial_accounting_purchase" class="dash-wrap">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_purchase/financial_accounting_purchase');
        ?>
    </div>
    <div id="wrap_financial_accounting_deliveris" class="dash-wrap">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_deliveris/financial_accounting_deliveris');
        ?>
    </div>
    <div id="wrap_financial_accounting_ycc" class="dash-wrap">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_ycc/financial_accounting_ycc');
        ?>
    </div>
    <div id="wrap_financial_accounting_dxtc" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_dxtc/financial_accounting_dxtc');
        ?>
    </div>
    <div id="wrap_financial_accounting_deliveris_thu" class="dash-wrap">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_deliveris_thu/financial_accounting_deliveris_thu');
        ?>
    </div>
    <div id="wrap_purchase_order_warehouse" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/purchase_order_warehouse/purchase_order_warehouse');
        ?>
    </div>
    <div id="wrap_purchase_order_warehouse_kh" class="dash-wrap">
        <?php //$this->load->view('admin/dashboard_srceen_vp/purchase_order_warehouse_kh/purchase_order_warehouse_kh'); 
        ?>
    </div>
    <div id="wrap_purchase_order" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/purchase_order/purchase_order');
        ?>
    </div>
    <div id="wrap_financial_accounting_ncc_contract" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_ncc_contract/financial_accounting_ncc_contract');
        ?>
    </div>
    <div id="wrap_financial_accounting_clients_contract" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen_vp/financial_accounting_clients_contract/financial_accounting_clients_contract');
        ?>
    </div>
    <div id="wrap_purchase_order_dxnb" class="dash-wrap">
    <?php $this->load->view('admin/dashboard_srceen_vp/purchase_order_dxnb/purchase_order_dxnb');
        ?>
    </div>
    <script>
        // ====== Auth + Socket ======


        function formatNumber(num, decimals = 2, decPoint = '.', thousandsSep = ',') {
            if (isNaN(num) || num === null) return '-';
            num = parseFloat(num);
            if (num == 0 || num === '' || num === '0') return '-';
            const fixed = num.toFixed(decimals);

            let [intPart, decPart] = fixed.split('.');
            intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

            return decPart ? intPart + decPoint + decPart : intPart;
        }

        function numVal(v) {
            if (v === null || v === undefined) return 0;
            if (typeof v === 'number' && isFinite(v)) return v;
            let s = String(v).trim();
            if (!s) return 0;

            // âm trong ngoặc () -> chuyển thành âm
            const isNegative = /^\(.*\)$/.test(s);
            s = s.replace(/[()]/g, '');

            // phát hiện % nếu có (không tự động chia 100 để giữ nguyên logic sử dụng)
            const hasPercent = s.indexOf('%') !== -1;
            s = s.replace(/%/g, '');

            // Xử lý dấu ngăn nghìn / decimal:
            // - nếu cả '.' và ',' xuất hiện -> giả sử '.' là ngăn nghìn, ',' là decimal (ví dụ "1.234,56")
            // - nếu chỉ có ',' -> coi ',' là decimal (ví dụ "1234,56")
            // - ngược lại bỏ dấu ',' (thousand) và giữ '.' làm decimal
            if (s.indexOf('.') !== -1 && s.indexOf(',') !== -1) {
                s = s.replace(/\./g, '').replace(/,/g, '.');
            } else if (s.indexOf(',') !== -1 && s.indexOf('.') === -1) {
                s = s.replace(/,/g, '.');
            } else {
                s = s.replace(/,/g, '');
            }

            // bỏ ký tự không phải số, -, .
            s = s.replace(/[^\d\.\-]+/g, '');

            let n = parseFloat(s);
            if (isNaN(n)) n = 0;
            if (isNegative) n = -n;

            // nếu trước đó có '%' và bạn muốn giá trị thực (chia 100) -> uncomment:
            // if (hasPercent) n = n / 100;

            return n;
        }
    </script>
    <script>
        // ============ CẤU HÌNH ============ //
        const DELIVERY_PAGES = 6; // Business (delivery)
        const MANU_PAGES = 6; // Planning (manufacture)
        const FIN_PAGES = 6; // Financial (tùy bạn cần)

        // Thời gian dừng nếu màn không có controller play/pause (ms)
        const DEFAULT_DWELL = 300000;

        // Khai báo mapping giữa DIV wrap, tên controller toàn cục và số trang
        // ĐỔI 'financeDash' nếu controller tài chính của bạn tên khác
        const DASHES = [{
                key: 'planning',
                elId: 'wrap_planning_department',
                ctrlName: 'manuDash', // ví dụ: window.manuDash từ planning_department
                pages: MANU_PAGES,
                dwell: DEFAULT_DWELL,
            },
            // {
            //     key: 'planning_table',
            //     elId: 'wrap_planning_department_table',
            //     ctrlName: 'manuDash', // ví dụ: window.manuDash từ planning_department
            //     pages: MANU_PAGES,
            //     dwell: DEFAULT_DWELL,
            // },
            {
                key: 'business',
                elId: 'wrap_business_department',
                ctrlName: 'businessDash', // ví dụ: window.businessDash từ business_department
                pages: DELIVERY_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'financial',
                elId: 'wrap_financial_accounting',
                ctrlName: 'dxntDash', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'financial_customs',
                elId: 'wrap_financial_accounting_customs',
                ctrlName: 'financeDash_customs', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'financial_purchase',
                elId: 'wrap_financial_accounting_purchase',
                ctrlName: 'financeDash_purchase', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'dxnb_purchasesDash',
                elId: 'wrap_purchase_order_dxnb',
                ctrlName: 'dxnb_purchasesDash', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'financial_ycc',
                elId: 'wrap_financial_accounting_ycc',
                ctrlName: 'financeDash_ycc', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'financial_dxtc',
                elId: 'wrap_financial_accounting_dxtc',
                ctrlName: 'financeDash_dxtc', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'accounting_deliveris',
                elId: 'wrap_financial_accounting_deliveris',
                ctrlName: 'financeDash_deliveris', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'accounting_deliveris_thu',
                elId: 'wrap_financial_accounting_deliveris_thu',
                ctrlName: 'financeDash_deliveris_thu', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'ncc_contract',
                elId: 'wrap_financial_accounting_ncc_contract',
                ctrlName: 'financeDash_deliverisncc_contract', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'ncc_client',
                elId: 'wrap_financial_accounting_clients_contract',
                ctrlName: 'financeDash_deliverisclient_contract', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'purchase_order_warehouse',
                elId: 'wrap_purchase_order_warehouse',
                ctrlName: 'warehouseDash', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            },
            {
                key: 'purchase_order',
                elId: 'wrap_purchase_order',
                ctrlName: 'purchaseorderDash', // ví dụ: window.financeDash (đổi theo thực tế)
                pages: FIN_PAGES,
                dwell: DEFAULT_DWELL,
            }
        ];

        // ============ LOGIC ORCHESTRATOR ============ //
        const els = Object.fromEntries(
            DASHES.map(d => [d.key, document.getElementById(d.elId)])
        );

        let currentIndex = 0;
        let playingController = null;
        let dwellTimer = null;
        let paused = false;

        function setActive(index) {
            // Tắt hết
            DASHES.forEach(d => {
                const el = els[d.key];
                if (el) el.classList.remove('dash-active');
            });

            // Bật màn cần hiển thị
            const dash = DASHES[index];
            const el = els[dash.key];
            if (el) el.classList.add('dash-active');
        }

        function stopCurrent() {
            // Hủy timer dwell nếu có
            if (dwellTimer) {
                clearTimeout(dwellTimer);
                dwellTimer = null;
            }

            // Pause controller cũ nếu có
            if (playingController && typeof playingController.pause === 'function') {
                try {
                    playingController.pause();
                } catch (_) {}
            }
            playingController = null;
        }

        function playIndex(index) {

            currentIndex = index % DASHES.length;
            stopCurrent();
            setActive(currentIndex);

            const dash = DASHES[currentIndex];
            const ctrl = window[dash.ctrlName];
            // console.log(dash);
            console.log('123: ', index);

            // Nếu đang global paused thì đừng chạy, giữ nguyên màn
            if (paused) return;

            if (ctrl && typeof ctrl.play === 'function') {
                // Có controller → dùng play(pages, done)
                playingController = ctrl;
                try {
                    ctrl.play(dash.pages, () => {
                        // xong thì qua màn sau
                        next();
                    });
                } catch (e) {
                    // nếu play lỗi thì fallback sang dwell
                    dwellTimer = setTimeout(next, dash.dwell || DEFAULT_DWELL);
                }
            } else {
                // Không có controller → dùng dwell timeout
                dwellTimer = setTimeout(next, dash.dwell || DEFAULT_DWELL);
            }
        }

        function next() {
            playIndex((currentIndex + 1) % DASHES.length);
        }

        function goToKey(key) {
            const idx = DASHES.findIndex(d => d.key === key);
            if (idx >= 0) playIndex(idx);
        }

        // ============ KHỞI CHẠY ============ //
        // Đợi một chút cho các view con khởi tạo controller (nếu có)
        setTimeout(() => playIndex(0), 1000);

        // ============ PHÍM TẮT ============ //
        window.addEventListener('keydown', (e) => {
            const k = e.key.toLowerCase();
            if (k === '1') {
                paused = false;
                goToKey('planning');
            }
            if (k === '2') {
                paused = false;
                goToKey('planning_table');
            }
            if (k === '3') {
                paused = false;
                goToKey('business');
            }
            if (k === '4') {
                paused = false;
                goToKey('financial');
            }
            if (k === '5') {
                paused = false;
                goToKey('financial_customs');
            }
            if (k === '6') {
                paused = false;
                goToKey('financial_purchase');
            }
            if (k === '7') {
                paused = false;
                goToKey('dxnb_purchasesDash');
            }
            if (k === '8') {
                paused = false;
                goToKey('financial_ycc');
            }
            if (k === '9') {
                paused = false;
                goToKey('financial_dxtc');
            }
            if (k === 'q') {
                paused = false;
                goToKey('accounting_deliveris');
            }
            if (k === 'w') {
                paused = false;
                goToKey('accounting_deliveris_thu');
            }
            if (k === 'e') {
                paused = false;
                goToKey('purchase_order_warehouse');
            }
            if (k === 'r') {
                paused = false;
                goToKey('purchase_order');
            }
            if (k === 't') {
                paused = false;
                goToKey('ncc_contract');
            }
            if (k === 'y') {
                paused = false;
                goToKey('ncc_client');
            }
            if (k === 'p') {
                // Pause/Resume
                paused = !paused;
                if (paused) {
                    stopCurrent();
                } else {
                    // resume: chạy lại màn hiện tại
                    playIndex(currentIndex);
                }
            }
        });

        // Expose vài hàm tiện debug
        window._dashNext = next;
        window._dashPause = () => {
            paused = true;
            stopCurrent();
        };
        window._dashResume = () => {
            if (!paused) return;
            paused = false;
            playIndex(currentIndex);
        };
    </script>
</body>

</html>