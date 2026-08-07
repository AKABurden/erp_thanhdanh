    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        html,
        body {
            width: 100%;
            height: 100%
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #349eff, #349eff);
            color: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .app {
            width: 100%;
            height: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0
        }

        .container {
            flex: 1;
            display: flex;
            gap: 20px;
            padding: 7px 20px 20px 20px;
            min-height: 0
        }

        .sidebar {
            width: 280px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex-shrink: 0
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            background: linear-gradient(135deg, #0348a2, #0348a2);
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
            overflow: hidden
        }

        .table-body {
            flex: 1;
            overflow-y: auto;
            padding: 10px
        }

        /* Header */
        .header {
            background: linear-gradient(90deg, #0348a2, #0348a2);
            padding: 20px 40px;
            margin: 5px 20px 0;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .logo-circle {
            width: 48px;
            height: 48px;
            font-size: 20px
        }

        .header-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 2px
        }

        .header-right {
            text-align: right;
            font-size: 14px;
            font-weight: 500
        }

        /* Card */
        .card {
            background: linear-gradient(135deg, #002f81, #002f81);
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .3)
        }

        .card .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            font-size: 21px;
            font-weight: 500
        }

        .card .row-2 {
            background: #0348a2;
            border-radius: 10px
        }

        .card .row span:last-child {
            font-weight: 800;
            font-size: 25px
        }

        .progress-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px
        }

        .progress-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 24px
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            color: white !important;
        }

        .badge.green {
            background: #10b981;
            font-size: 22px;
            width: 75px;
            text-align: center
        }

        .badge.red {
            background: #ef4444;
            font-size: 22px;
            width: 75px;
            text-align: center
        }

        .donut {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            position: relative
        }

        .donut svg {
            width: 200px;
            height: 200px;
            transform: rotate(-90deg)
        }

        .donut-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 28px;
            font-weight: bold
        }

        /* Table */
        /* .table-header,
        .table-row {
            display: grid;
            grid-template-columns:130px 150px 150px 150px 250px 130px 120px 150px;
            gap: 20px;
            padding: 16px 24px;
            font-size: 24px;
            font-weight: 500;
            align-items: center;
        } */

        .table-header,
        .table-row {
            display: grid;
            /* responsive columns: ưu tiên cột sản phẩm (4th column) */
            grid-template-columns:
                minmax(100px, 1fr)
                /* số đơn / order */
                minmax(110px, 1.1fr)
                /* sku */
                minmax(110px, 1.1fr)
                /* stage */
                minmax(100px, 1fr)
                /* SẢN PHẨM - ưu tiên lớn */
                minmax(150px, 2.1fr)
                /* mô tả / note / địa chỉ */
                minmax(120px, 1.2fr)
                /* qty plan */
                minmax(80px, 0.9fr)
                /* qty done */
                minmax(80px, 0.9fr);
            /* percent */
            gap: 20px;
            padding: 16px 24px;
            font-size: 24px;
            font-weight: 500;
            align-items: center;
        }

        /* đảm bảo cột sản phẩm co giãn đúng và text bị ẩn/ellipsis khi quá dài */
        .table-row>.col-item-code {
            min-width: 0;
            /* cho phép ellipsis trong grid */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-header {
            font-weight: 600;
            flex-shrink: 0;
            text-align: center;
        }

        .table-row:nth-child(odd) {
            background: #002F81
        }

        .yellow {
            text-align: center;
            color: #facc15;
            font-weight: 600
        }

        .green {
            font-weight: 600;
            color: #01C532;
        }

        .red {
            color: #FF7679;
            font-weight: 600
        }

        .progress-bar {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .bar {
            flex: 1;
            height: 10px;
            background: rgba(255, 255, 255, .2);
            border-radius: 6px;
            overflow: hidden
        }

        .bar-fill {
            height: 100%;
            border-radius: 6px
        }

        .percent {
            font-weight: bold;
            font-size: 22px;
            min-width: 40px;
            text-align: right
        }

        /* Animation */
        .table-row {
            transition: transform .4s ease, opacity .3s ease;
            will-change: transform
        }

        .table-row.highlight {
            animation: flash 1s ease forwards;
        }

        @keyframes flash {
            0% {
                background: rgba(255, 255, 0, 0.6);
            }

            100% {
                background: transparent;
            }
        }

        .table-row.enter {
            opacity: 0;
            transform: translateY(-12px)
        }

        .table-row.enter-active {
            opacity: 1;
            transform: translateY(0)
        }

        .table-row.fade-out {
            opacity: 0;
            transform: translateY(16px);
            transition: transform .35s ease, opacity .3s ease
        }

        .table-body {
            transition: opacity 0.5s ease;
        }

        .table-body.hidden {
            opacity: 0;
        }

        /* Header: cho phép xuống dòng */
        .table-header>div {
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
        }

        /* Body: kiểm tra để áp dụng marquee */
        /* Mặc định: cho phép xuống dòng */
        .table-row>div {
            white-space: normal;
            word-break: break-word;
        }

        /* Riêng cột sản phẩm */
        .table-row>.col-item-code {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Marquee chỉ áp dụng cho cột sản phẩm */
        .table-row>.col-item-code.marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 20s linear infinite;
            white-space: nowrap;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .table-row .datetime {
            white-space: normal;
            /* cho phép xuống dòng */
            word-break: break-word;
        }

        .table-row .text_delivery {
            white-space: normal;
            word-break: break-word;
            color: #0fdb19ff;
            font-weight: 600;
        }

        .table-row .text_no {
            white-space: normal;
            word-break: break-word;
            color: #E56464;
            font-weight: 600;
        }

        .table-row .image {
            overflow: hidden;
            display: flex;
            justify-content: center;
            font-size: 20px;
        }

        .table-row .image img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }
    </style>