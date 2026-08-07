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
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-weight: bold;
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
            font-size: 19px;
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
            font-size: 22px
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            color: white
        }

        .badge.green {
            background: #10b981;
            font-size: 18px;
            width: 50px;
            text-align: center
        }

        .badge.red {
            background: #ef4444;
            font-size: 18px;
            width: 50px;
            text-align: center
        }

        .donut {
            display: flex;
            justify-content: center;
            margin-top: auto;
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
        .table-header,
        .table-row {
            display: grid;
            grid-template-columns:
                180px 160px 200px 125px 120px 180px 1fr;
            gap: 20px;
            padding: 16px 24px;
            font-size: 22px;
            font-weight: 500;
            align-items: center;
        }

        .table-header {
            font-weight: 600;
            flex-shrink: 0
        }

        .table-row:nth-child(odd) {
            background: #002F81
        }

        .yellow {
            color: #facc15;
            font-weight: 600
        }

        .green {
            font-weight: 600
        }

        .red {
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
        .table-row>div {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            position: relative;
        }

        .table-row>div.marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 8s linear infinite;
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
    </style>