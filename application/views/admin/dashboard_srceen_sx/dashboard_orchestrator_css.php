    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #349eff, #349eff);
            color: #ffffff;
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

        .app {
            width: 100%;
            height: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0
        }

        /* Header */
        .header {
            background: linear-gradient(90deg, #0348a2, #0348a2);
            padding: 0px 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #ffffff;
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
            font-weight: 800;
            letter-spacing: 2px;
            text-align: center;
        }

        .header-title .main-title {
            font-size: 2.3rem;
        }

        .header-title .sub-title {
            font-size: 2.3rem;
        }

        .header-title .child {
            font-size: 12px;
        }

        .header-right {
            text-align: right;
            font-size: 14px;
            font-weight: 500
        }

        .container {
            padding: 7px 0 10px 0;
            border-radius: 12px;
        }

        .body-footer {
            background: linear-gradient(90deg, #0348a2, #0348a2);
            height: 60px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            left: 10px;
            right: 10px;
            bottom: 5px;
            margin: 0;
            z-index: 100;
            border-radius: 12px;
        }

        .botton_tab {
            flex: 1 0 .8%;
            width: 5.8%;
            height: 55px;
            font-size: 0.8rem;
            border: unset;
            background: #b3946c;
            color: #fff;
            cursor: pointer;
            padding: 10px;
            border-radius: 12px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }

        .botton_tab.active {
            background: #ad6200;
            color: #fff;
            box-shadow: 0 4px 16px rgba(3, 72, 162, 0.15);
            font-weight: 700;
            font-size: 0.75rem;
            border: 2px solid #ad6200;
        }

        .container {
            background: linear-gradient(135deg, #0348a2, #0348a2);
            border-radius: 12px;
            margin: 5px 0 0px 0;
        }

        .sidebar-top {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .sidebar-left {
            width: 50%;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 165px);
            overflow-y: auto;
            float: left;
        }

        .sidebar-right {
            width: 49%;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 165px);
            overflow-y: auto;
        }

        .container-detail {
            flex: 1;
            min-height: 0;
            /* display: flex; */
            /* gap: 7px; */
            margin-top: -10px;
        }

        .kpi-box.nau {
            background: #BA925D;
        }

        .kpi-box {
            border-radius: 12px;
            padding: 10px;
            margin: 10px 10px 0px 10px;
            box-shadow: var(--shadow_pkd);
        }

        .sidebar .kpi-box,
        .sidebar .thongke-box,
        .sidebar .head {
            flex: none;
        }

        .thongke-grid>.box {
            text-align: center;
            font-weight: 700;
            font-size: 30px;
        }

        .hide {
            display: none !important;
        }

        .clearfix:after {
            visibility: hidden;
            display: block;
            font-size: 0;
            content: " ";
            clear: both;
            height: 0
        }

        .clearfix {
            display: inline-block
        }

        * html .clearfix {
            height: 1%
        }

        .clearfix {
            display: block
        }

        .clearfix {
            display: block
        }

        .kpi-box .label {
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
        }

        .box .value {
            font-size: 90px;
            font-weight: 600;
        }

        .red {
            color: #f44336;
        }

        .sidebar-left .label {
            color: #f44336;
        }

        .total_dashboard {
            width: 66%;
            float: left;
        }

        .chart_dashboard {
            width: 33%;
            float: right;
        }

        .sub-menu {
            width: 100%;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            position: absolute;
            bottom: 70px;
            padding: 10px;
        }

        .sub-menu-child {
            background: #4177bd;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin: 3px;
            font-size: 14px;
            cursor: pointer;
        }

        .sub-menu-child.active {
            background: #0348A2;
            color: #fff;
            box-shadow: 0 4px 16px rgba(3, 72, 162, 0.15);
            font-weight: 700;
            font-size: 14px;
            border: 2px solid #0348A2;
        }

        .sub-menu-child-new {
            background: #b3946c;
            color: #fff;
            padding: 5px;
            border-radius: 8px;
            margin: 3px;
            font-size: 13px;
            cursor: pointer;
        }

        .sub-menu-child-new.active {
            background: #ad6200;
            color: #fff;
            padding: 5px;
            box-shadow: 0 4px 16px rgba(3, 72, 162, 0.15);
            font-weight: 700;
            font-size: 13px;
            border: 2px solid #ad6200;
        }

        .sub-menu-child-accting {
            background: #b3946c;
            color: #fff;
            padding: 7px;
            border-radius: 8px;
            margin: 3px;
            font-size: 14px;
            cursor: pointer;
        }

        .sub-menu-child-accting.active {
            background: #0348A2;
            color: #fff;
            box-shadow: 0 4px 16px rgba(3, 72, 162, 0.15);
            font-weight: 700;
            font-size: 0.75rem;
            border: 2px solid #0348A2;
        }

        .box_vs1_wrap {
            display: flex;
            flex-wrap: wrap;
            /* Cho phép xuống dòng nếu quá */
            width: 100%;
            height: 95%;
        }

        .box_vs1 {
            width: 20%;
            padding: 10px;
            border-bottom: 1px solid #d0d0d0;
            border-right: 1px solid #d0d0d0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-weight: 700;
            box-sizing: border-box;
            text-align: center;
        }

        /* Backdrop + layout (Light) */
        .modal {
            position: fixed !important;
            inset: 0;
            z-index: 1050;
            display: none;
            align-items: center;
            /* giữa dọc */
            justify-content: center;
            /* giữa ngang */
            background: rgba(2, 6, 23, 0.55);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity .2s ease;
        }

        /* Khi hiển thị modal */
        .modal.show {
            display: flex !important;
            /* ép về flex để canh giữa */
            opacity: 1;
        }

        /* Dialog + animation */
        .modal-dialog {
            width: 100%;
            max-width: 560px;
            /* mặc định vừa mắt */
            margin: 0 !important;
            /* bỏ margin đẩy lệch */
            pointer-events: auto;
            /* cho phép click bên trong */
            transform: translateY(8px) scale(.98);
            opacity: 0;
            transition: transform .24s cubic-bezier(.2, .7, .2, 1), opacity .24s ease;
        }

        .modal.show .modal-dialog {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* Kích cỡ */
        .modal-dialog.modal-sm {
            max-width: 420px;
        }

        .modal-dialog.modal-lg {
            max-width: 920px;
        }

        .modal-dialog.modal-xl {
            max-width: 1120px;
        }

        /* Fullscreen on very small screens */
        @media (max-width: 480px) {
            .modal {
                padding: 12px;
            }

            .modal-dialog {
                max-width: 100%;
            }
        }

        /* Content (Light) */
        .modal-content {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            color: #0f172a;
            /* slate-900 */
            border: 1px solid rgba(2, 6, 23, 0.06);
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.18), 0 8px 20px rgba(2, 6, 23, 0.10);
            overflow: hidden;
            /* bo góc + tránh tràn */
            max-height: calc(100vh - 48px);
            /* luôn nằm giữa, không vượt quá màn hình */
        }

        .modal-body {
            flex: 1 1 auto;
            overflow: auto;
            /* nội dung dài có thể cuộn */
            padding: 18px;
            color: #0f172a;
        }

        .modal-body p {
            color: #64748b;
        }

        /* slate-500 */

        /* Header (Light) */
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 20px;
            background: linear-gradient(180deg, #BA925D 0%, #A37E4F 100%);
            color: #fff;
            border-bottom: none;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.2),
                0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .modal-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: .3px;
            color: #fff;
            /* chữ trắng rõ ràng trên nền vàng nâu */
        }

        /* Close button – hiện đại, sáng nhẹ */
        .close,
        .btn-close {
            appearance: none;
            border: 0;
            background: rgba(255, 255, 255, 0.15);
            font-size: 18px;
            line-height: 1;
            color: #fff;
            opacity: 0.9;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all .2s ease;
        }

        .close:hover,
        .btn-close:hover {
            background: rgba(255, 255, 255, 0.25);
            opacity: 1;
            transform: scale(1.05);
        }

        .close:active,
        .btn-close:active {
            transform: scale(0.95);
            background: rgba(255, 255, 255, 0.35);
        }

        .close:focus-visible,
        .btn-close:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 2px;
        }

        /* Footer (Light) */
        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 18px;
            border-top: 1px solid rgba(2, 6, 23, 0.06);
            background: linear-gradient(0deg, #f8fafc, transparent);
        }

        /* Buttons */
        .modal .btn {
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid rgba(2, 6, 23, 0.06);
            background: #0ea5e9;
            /* sky-500 */
            color: #fff;
            font-weight: 600;
            letter-spacing: .2px;
            transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
            box-shadow: 0 8px 18px rgba(14, 165, 233, .25);
            cursor: pointer;
        }

        .modal .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(14, 165, 233, .28);
        }

        .modal .btn:active {
            transform: translateY(0);
        }

        .modal .btn.btn-outline {
            background: transparent;
            color: #0f172a;
            box-shadow: none;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            .modal {
                background: rgba(0, 0, 0, 0.65);
            }

            .modal-content {
                background: #0b1220;
                color: #e5e7eb;
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), 0 8px 20px rgba(0, 0, 0, 0.35);
            }

            .modal-header {
                background: linear-gradient(180deg, #0f172a, transparent);
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .modal-body {
                color: #e5e7eb;
            }

            .modal-body p {
                color: #94a3b8;
            }

            .close,
            .btn-close {
                color: #e5e7eb;
            }

            .modal .btn.btn-outline {
                color: #e5e7eb;
                border-color: rgba(255, 255, 255, 0.08);
            }

            .modal-footer {
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                background: linear-gradient(0deg, #0f172a, transparent);
            }
        }

        /* Tắt animation nếu người dùng chọn */
        @media (prefers-reduced-motion: reduce) {

            .modal,
            .modal-dialog {
                transition: none !important;
            }

            .modal-dialog {
                transform: none !important;
                opacity: 1 !important;
            }
        }

        .staff-profile-image-small {
            height: 32px;
            width: 32px;
            border-radius: 50%
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            font-size: 15px;
            box-shadow: 0 4px 24px rgba(25, 118, 210, 0.08);
            border-radius: 10px;
            overflow: hidden;
        }

        .table th,
        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e3e8ee;
            text-align: center;
            vertical-align: middle;
            transition: background 0.2s;
        }

        .table thead th {
            background: #fef7e2;
            color: black;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #1565c0;
        }

        .table tbody tr:nth-child(even) {
            background: #f7fbff;
        }

        .table tbody tr:hover {
            background: #e3f2fd;
            box-shadow: 0 2px 8px rgba(25, 118, 210, 0.07);
        }

        .table tfoot td {
            background: #f1f5fa;
            font-weight: 600;
            color: black;
            border-top: 2px solid #e3e8ee;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 14px;
            margin: 2px;
            border-radius: 6px;
            background: #e3eafc;
            color: #1976d2 !important;
            border: none;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #1976d2;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            padding: 6px 12px;
            margin-left: 8px;
            transition: border 0.2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #1976d2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.08);
        }

        .dataTables_wrapper .dataTables_length {
            margin: 16px 0 16px 16px;
            font-size: 15px;
            display: inline-block;
        }

        .dataTables_wrapper .dataTables_length label {
            font-weight: 500;
            color: #1976d2;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            padding: 6px 12px;
            margin-left: 8px;
            transition: border 0.2s;
            font-size: 15px;
        }

        .dataTables_wrapper .dataTables_filter {
            margin: 16px 16px 16px 0;
            float: right;
            font-size: 15px;
        }

        .dataTables_wrapper .dataTables_filter label {
            font-weight: 500;
            color: #1976d2;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin: 16px 0 16px 0;
            text-align: center;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 14px;
            margin: 2px;
            border-radius: 6px;
            background: #e3eafc;
            color: #1976d2 !important;
            border: none;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #1976d2;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_info {
            margin: 16px 0 16px 16px;
            font-size: 14px;
            color: #616161;
            display: inline-block;
        }

        /* Custom styles for DataTables simple number pagination */
        .dataTables_wrapper .dataTables_paginate.paging_simple_numbers {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px;
            margin: 16px 0 16px 0;
        }

        .dataTables_wrapper .dataTables_paginate.paging_simple_numbers .paginate_button {
            padding: 6px 14px;
            margin: 2px;
            border-radius: 6px;
            background: #e3eafc;
            color: #1976d2 !important;
            border: none;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
            min-width: 36px;
        }

        .dataTables_wrapper .dataTables_paginate.paging_simple_numbers .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate.paging_simple_numbers .paginate_button:hover {
            background: #1976d2;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate.paging_simple_numbers .paginate_button.disabled {
            opacity: 0.5;
            pointer-events: none;
            background: #e3eafc;
            color: #bdbdbd !important;
        }

        .mright5 {
            margin-right: 5px;
        }

        img {
            vertical-align: middle;
        }

        .avatar-sm_puwa {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user_puwa img {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Timeline tiến độ trong cột Tiến độ */
        .timeline_puwa {
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            padding-left: 18px;
        }

        .step_puwa {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .step_puwa::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 12px;
            bottom: -8px;
            width: 2px;
            background: repeating-linear-gradient(to bottom,
                    #d1d5db,
                    #d1d5db 4px,
                    transparent 4px,
                    transparent 10px);
            /* line dạng chấm tròn dọc */
        }

        .step_puwa:last-child::before {
            display: none;
            /* không nối sau bước cuối */
        }



        .step_puwa.done_puwa .dot_puwa_progress {
            background: #10b981;
            /* xanh */
        }

        .step_puwa.pending_puwa .dot_puwa_progress {
            background: #ccc;
            /* xám chờ */
        }

        .content_puwa {
            margin-left: 20px;
        }

        .title_puwa {
            font-weight: 700;
            color: #111827;
            font-size: 14px;
            line-height: 1.3;
        }

        .dot_puwa_progress {
            left: 0;
            top: 5px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d1d5db;
            border: 2px solid white;
            z-index: 2;
        }
    </style>