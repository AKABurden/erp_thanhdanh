<style>
    :root {
        --bg_dwh: #f3f6fb;
        --card_dwh: #ffffff;
        --text_dwh: #0f1b40;
        --muted_dwh: #6b7280;
        --line_dwh: #e5e7eb;
        --primary_dwh: #0a58ff;
        --green_dwh: #10b981;
        --red_dwh: #ef4444;
        --yellow_dwh: #FFD400;
        --chip_dwh: #eef2ff;
        --radius_dwh: 12px;
        --shadow_dwh: 0 10px 24px rgba(16, 24, 40, 0.12);
    }

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

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    thead th {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line);
        border-right: 1px solid var(--line);
        font-size: 25px;
        text-align: center;
    }

    tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line);
        border-left: 1px solid var(--line);
        border-right: 1px solid var(--line);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 26px;
    }

    tbody tr:hover td {
        background: #fcfcff;
    }

    tbody tr:nth-child(even) td {
        background: #EEEEEE;
    }

    .app_dwh {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_dwh {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_dwh {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_dwh {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_dwh {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_dwh .main {
        font-size: 1.8rem;
    }

    .header-title_dwh .child {
        font-size: 12px;
    }

    .header-right_dwh {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_dwh {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 10px 20px;
        min-height: 0
    }

    .top-kpis_dwh {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_dwh {
        background: var(--card_dwh);
        border-radius: var(--radius_dwh);
        box-shadow: var(--shadow_dwh);
    }

    .card-pad_dwh {
        padding: 16px 18px;
    }

    .card-table_dwh {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dwh {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_dwh {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_dwh {
        border: 1px solid var(--line_dwh);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_dwh .label_dwh {
        color: var(--muted_dwh);
        font-weight: 600;
        color: #002F81;
    }

    .stat_dwh .value_dwh {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_dwh {
        background-color: #DCFDE9;
    }

    .label-hong_dwh {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_dwh {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_dwh {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_dwh .pill_dwh {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_dwh);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_dwh);
    }

    .kpi-sub_dwh {
        color: var(--muted_dwh);
        font-weight: 600;
    }

    .donut-wrap_dwh {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_dwh {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_dwh {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_dwh {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_dwh svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_dwh .txt_dwh {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_dwh {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_dwh {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_dwh {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dwh .head_dwh {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_dwh .h-title_dwh {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_dwh {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_dwh {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_dwh.green_dwh {
        background: var(--green_dwh);
    }

    .label-status_dwh.red_dwh {
        background: var(--red_dwh);
    }

    .label-status_dwh.yellow_dwh {
        background: var(--yellow_dwh);
    }

    .text-status_dwh {
        font-size: 14px;
        color: var(--text_dwh);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_dwh {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_dwh.green_dwh {
        background: var(--green_dwh);
    }

    .dot_dwh.red_dwh {
        background: var(--red_dwh);
    }

    .dot_dwh.yellow_dwh {
        background: var(--yellow_dwh);
    }

    .head_dwh {
        padding: 20px;
    }

    table.dwh {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_dwh .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    thead.dwh th.dwh {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_dwh);
        border-right: 1px solid var(--line_dwh);
        font-size: 20px;
        text-align: center;
    }

    tbody.dwh td.dwh {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_dwh);
        border-left: 1px solid var(--line_dwh);
        border-right: 1px solid var(--line_dwh);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.dwh tr:hover td.dwh {
        background: #fcfcff;
    }

    tbody.dwh tr:nth-child(even) td.dwh {
        background: #EEEEEE;
    }

    .status-badge_dwh {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_dwh {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_dwh.green_dwh {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_dwh);
    }

    .chip_dwh.red_dwh {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_dwh);
    }

    .chip_dwh.yellow_dwh {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_dwh);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_dwh {
            grid-template-columns: 1fr;
        }

        .tables_dwh {
            grid-template-columns: 1fr;
        }

        .title_dwh {
            font-size: 28px;
        }
    }

    .sidebar_dwh {
        background: white;
        border-radius: 10px;
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_dwh {
        border-radius: 12px;
        padding: 7px;
        margin: 10px 10px 0px 10px;
        box-shadow: var(--shadow_dwh);
    }

    .kpi-box_dwh.blue_dwh {
        background: #0348A2;
    }


    .kpi-box_dwh .label {
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
    }

    .box-dwh {
        text-align: center;
    }

    .box-dwh .label {
        font-weight: 600;
        color: #002F81;
        font-size: 25px;
    }

    .box-dwh .label-2 {
        font-weight: 600;
        color: #002F81;
        font-size: 25px;
    }

    .box-dwh .value-2 {
        font-size: 50px;
        font-weight: 800;
    }

    .box-dwh .value {
        font-size: 50px;
        font-weight: 800;
    }

    .green {
        color: #00691A;
    }

    .blue {
        color: #002F81;
    }

    .red {
        color: #D34242;
    }



    .table-wrapper_dwh>.box-dwh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-bottom: 1px solid #0348A2;
    }

    .table-wrapper_dwh>.box-dwh:nth-child(2),
    .table-wrapper_dwh>.box-dwh:nth-child(4) {
        border-right: none;
    }

    .table-wrapper_dwh>.box-dwh:nth-child(3),
    .table-wrapper_dwh>.box-dwh:nth-child(4) {
        border-bottom: none;
    }


    /* 2 trên 2 dưới */
    .thongke-grid_dwh {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_dwh>.box-dwh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        border-bottom: 1px solid #0348A2;
    }

    .thongke-grid_dwh>.box-dwh:nth-child(2),
    .thongke-grid_dwh>.box-dwh:nth-child(4) {
        border-right: none;
    }

    .thongke-grid_dwh>.box-dwh:nth-child(3),
    .thongke-grid_dwh>.box-dwh:nth-child(4) {
        border-bottom: none;
    }


    /*2  2 trên 2 dưới */
    .thongke-grid_dwh-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_dwh-2>.box-dwh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        border-bottom: 1px solid #0348A2;
    }

    .thongke-grid_dwh-2>.box-dwh:nth-child(2),
    .thongke-grid_dwh-2>.box-dwh:nth-child(4),
    .thongke-grid_dwh-2>.box-dwh:nth-child(6) {
        border-right: none;
    }

    .thongke-grid_dwh-2>.box-dwh:nth-child(3),
    .thongke-grid_dwh-2>.box-dwh:nth-child(4),
    .thongke-grid_dwh-2>.box-dwh:nth-child(5),
    .thongke-grid_dwh-2>.box-dwh:nth-child(6) {
        border-bottom: none;
    }

    .thongke-grid_dwh-2>.box-dwh:nth-child(3),
    .thongke-grid_dwh-2>.box-dwh:nth-child(4) {
        border-bottom: 1px solid #0348A2;
    }

    /*3  2 trên 2 dưới */
    .thongke-grid_dwh-3 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_dwh-3>.box-dwh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        border-bottom: 1px solid #0348A2;
    }

    .thongke-grid_dwh-2>.box-dwh:nth-child(2),
    .thongke-grid_dwh-2>.box-dwh:nth-child(4),
    .thongke-grid_dwh-2>.box-dwh:nth-child(6) {
        border-right: none;
    }

    .thongke-grid_dwh-3>.box-dwh:nth-child(2),
    .thongke-grid_dwh-3>.box-dwh:nth-child(4),
    .thongke-grid_dwh-3>.box-dwh:nth-child(6) {
        border-right: none;
    }

    .thongke-grid_dwh-3>.box-dwh:nth-child(3),
    .thongke-grid_dwh-3>.box-dwh:nth-child(4),
    .thongke-grid_dwh-3>.box-dwh:nth-child(5),
    .thongke-grid_dwh-3>.box-dwh:nth-child(6),
    .thongke-grid_dwh-3>.box-dwh:nth-child(7),
    .thongke-grid_dwh-3>.box-dwh:nth-child(8) {
        border-bottom: none;
    }

    .thongke-grid_dwh-3>.box-dwh:nth-child(3),
    .thongke-grid_dwh-3>.box-dwh:nth-child(4),
    .thongke-grid_dwh-3>.box-dwh:nth-child(5),
    .thongke-grid_dwh-3>.box-dwh:nth-child(6) {
        border-bottom: 1px solid #0348A2;
    }

    .kpi-box_dwh.hong {
        background: #FFE9E9;
    }

    .kpi-box_dwh.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_dwh.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_dwh.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_dwh.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }

    .table-wrapper_dwh {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_dwh);
        overflow: hidden;
    }

    .avatar_dwh {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_dwh div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_dwh {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_dwh {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_dwh::before {
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

    .step_dwh:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }


    .step_dwh.done_dwh .dot_dwh {
        background: #10b981;
        /* xanh */
    }

    .step_dwh.pending_dwh .dot_dwh {
        background: #ccc;
        /* xám chờ */
    }

    .content_dwh {
        margin-left: 20px;
    }

    .title_dwh {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_dwh {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_dwh {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    table.dwh thead th {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 700;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line);
        border-right: 1px solid var(--line);
        font-size: 20px;
        text-align: center;
    }

    /* 1) Toàn trang co giãn chuẩn */
    html,
    body,
    .app_dwh {
        height: 100%;
    }

    .app_dwh {
        display: flex;
        flex-direction: column;
    }

    .header_dwh {
        /* --header-h: 110px; */
        /* height: var(--header-h); */
    }

    .container_dwh {
        flex: 1;
        min-height: 0;
        display: flex;
        gap: 7px;
    }

    /* 2) Sidebar là column, table chiếm hết phần còn lại */
    .sidebar_dwh {
        display: flex;
        flex-direction: column;
        min-width: 420px;
    }

    .sidebar_dwh .kpi-box_dwh,
    .sidebar_dwh .thongke-box_dwh,
    .sidebar_dwh .head_dwh {
        flex: none;
    }

    /* 3) Table flex:1; tbody scroll full height */
    .sidebar_dwh table.dwh {
        flex: 1;
        /* chiếm toàn bộ chiều cao còn lại của sidebar */
        min-height: 0;
        display: flex;
        flex-direction: column;
        border-collapse: separate;
        /* tránh bug khi sticky */
    }

    .sidebar_dwh table.dwh thead {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
        /* màu nền để che nội dung khi sticky */
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .sidebar_dwh table.dwh tbody {
        display: block;
        /* để set overflow */
        flex: 1;
        /* quan trọng: fill hết chiều cao còn lại */
        min-height: 0;
        overflow: auto;
        /* scroll ở tbody */
    }

    .sidebar_dwh table.dwh tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* canh cột thead/tbody khớp nhau */
    }



    /* Hiệu ứng ẩn/hiện mượt khi bạn render lại */
    .hidden_dwh {
        opacity: 0;
        transition: opacity .15s ease;
    }

    .table-body_dwh {
        transition: opacity .15s ease;
    }

    .table-body_dwh .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    /* KPI donut blocks */
    .kpi-title {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum .pill {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip);
        font-size: 27px;
        font-weight: 800;
        text-align: center;
        color: var(--text);
    }

    .kpi-sub {
        color: var(--muted);
        font-weight: 600;
    }

    .donut-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    .donut {
        width: 120px;
        height: 120px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut .txt {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .text-status {
        display: flex;
        align-items: center;
    }

    .thongke-grid_dwh-1 {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 55px 12px 60px 12px;
    }

    .thongke-grid_dwh-1>.box-dwh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        /* border-right: 1px solid #0348A2;
        border-bottom: 1px solid #0348A2; */
    }

    .thongke-grid_dwh-1>.box-dwh:nth-child(1),
    .thongke-grid_dwh-1>.box-dwh:nth-child(2) {
        border-right: none;
    }

    .sidebar_dwh .value {
        color: red !important;
    }

    .sidebar_dwh .label::not(.blue_dfac) {
        color: red !important;
    }

    .table-wrapper_dfac .value {
        color: #002F81 !important;
    }

    .table-wrapper_dfac .label::not(.blue_dfac) {
        color: #002F81 !important;
    }
</style>