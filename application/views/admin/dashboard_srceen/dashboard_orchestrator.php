<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Orchestrator</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #349eff, #349eff);
            color: white;
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

<body>

    <div id="wrapDelivery" class="dash-wrap dash-active">
        <?php $this->load->view('admin/dashboard_srceen/export_delivery'); ?>
    </div>

    <div id="wrapManufacture" class="dash-wrap ">
        <?php $this->load->view('admin/dashboard_srceen/manufacture'); ?>
    </div>


    <script>
        const DELIVERY_PAGES = 2; // số trang mỗi lượt giao hàng
        const MANU_PAGES = 2; // số trang mỗi lượt sản xuất

        const wrapDelivery = document.getElementById('wrapDelivery');
        const wrapManufacture = document.getElementById('wrapManufacture');

        function showDelivery() {
            wrapDelivery.classList.add('dash-active');
            wrapManufacture.classList.remove('dash-active');
        }

        function showManufacture() {
            wrapManufacture.classList.add('dash-active');
            wrapDelivery.classList.remove('dash-active');
            window.manuDash.applyMarquee();
        }

        function runDeliveryThenSwitch() {
            if (window.manuDash) window.manuDash.pause(); // dừng Manufacture
            showDelivery();
            if (window.deliveryDash) {
                window.deliveryDash.play(DELIVERY_PAGES, () => {
                    runManufactureThenSwitch();
                });
            } else {
                setTimeout(runDeliveryThenSwitch, 2000);
            }
        }

        function runManufactureThenSwitch() {
            if (window.deliveryDash) window.deliveryDash.pause(); // dừng Delivery
            showManufacture();
            if (window.manuDash) {
                console.log(MANU_PAGES)
                window.manuDash.play(2, () => {
                    runDeliveryThenSwitch();
                });
            } else {
                setTimeout(runManufactureThenSwitch, 2000);
            }
        }

        setTimeout(runManufactureThenSwitch, 1500);

        window.addEventListener('keydown', e => {
            if (e.key.toLowerCase() === 'g') {
                if (window.manuDash) window.manuDash.pause();
                runDeliveryThenSwitch();
            }
            if (e.key.toLowerCase() === 's') {
                if (window.deliveryDash) window.deliveryDash.pause();
                runManufactureThenSwitch();
            }
        });
    </script>
</body>

</html>