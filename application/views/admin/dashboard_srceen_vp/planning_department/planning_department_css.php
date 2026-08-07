<style>
    :root {
        --bg_pkh: #f3f6fb;
        --card_pkh: #ffffff;
        --text_pkh: #0f1b40;
        --muted_pkh: #6b7280;
        --line_pkh: #e5e7eb;
        --primary_pkh: #0a58ff;
        --green_pkh: #10b981;
        --red_pkh: #ef4444;
        --yellow_pkh: #FFD400;
        --chip_pkh: #eef2ff;
        --radius_pkh: 12px;
        --shadow_pkh: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_pkh {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_pkh {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_pkh {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_pkh {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_pkh {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_pkh .main {
        font-size: 1.8rem;
    }

    .header-title_pkh .child {
        font-size: 12px;
    }

    .header-right_pkh {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_pkh {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 10px 20px;
        min-height: 0
    }

    .top-kpis_pkh {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_pkh {
        background: var(--card_pkh);
        border-radius: var(--radius_pkh);
        box-shadow: var(--shadow_pkh);
    }

    .card-pad_pkh {
        padding: 16px 18px;
    }

    .card-table_pkh {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_pkh {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_pkh {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_pkh {
        border: 1px solid var(--line_pkh);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_pkh .label_pkh {
        color: var(--muted_pkh);
        font-weight: 600;
        color: #002F81;
    }

    .stat_pkh .value_pkh {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_pkh {
        background-color: #DCFDE9;
    }

    .label-hong_pkh {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_pkh {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_pkh {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_pkh .pill_pkh {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_pkh);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_pkh);
    }

    .kpi-sub_pkh {
        color: var(--muted_pkh);
        font-weight: 600;
    }

    .donut-wrap_pkh {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_pkh {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_pkh {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_pkh {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_pkh svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_pkh .txt_pkh {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_pkh {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_pkh {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_pkh {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_pkh .head_pkh {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_pkh .h-title_pkh {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_pkh {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_pkh {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_pkh.green_pkh {
        background: var(--green_pkh);
    }

    .label-status_pkh.red_pkh {
        background: var(--red_pkh);
    }

    .label-status_pkh.yellow_pkh {
        background: var(--yellow_pkh);
    }

    .text-status_pkh {
        font-size: 14px;
        color: var(--text_pkh);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_pkh {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_pkh.green_pkh {
        background: var(--green_pkh);
    }

    .dot_pkh.red_pkh {
        background: var(--red_pkh);
    }

    .dot_pkh.yellow_pkh {
        background: var(--yellow_pkh);
    }

    .head_pkh {
        padding: 20px;
    }

    table.pkh {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_pkh .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    thead.pkh th.pkh {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_pkh);
        border-right: 1px solid var(--line_pkh);
        font-size: 20px;
        text-align: center;
    }

    tbody.pkh td.pkh {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_pkh);
        border-left: 1px solid var(--line_pkh);
        border-right: 1px solid var(--line_pkh);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.pkh tr:hover td.pkh {
        background: #fcfcff;
    }

    tbody.pkh tr:nth-child(even) td.pkh {
        background: #EEEEEE;
    }

    .status-badge_pkh {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_pkh {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_pkh.green_pkh {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_pkh);
    }

    .chip_pkh.red_pkh {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_pkh);
    }

    .chip_pkh.yellow_pkh {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_pkh);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_pkh {
            grid-template-columns: 1fr;
        }

        .tables_pkh {
            grid-template-columns: 1fr;
        }

        .title_pkh {
            font-size: 28px;
        }
    }

    .sidebar_pkh {
        background: white;
        border-radius: 10px;
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_pkh {
        border-radius: 12px;
        padding: 10px;
        margin: 10px 10px 0px 10px;
        box-shadow: var(--shadow_pkh);
    }

    .kpi-box_pkh.blue_pkh {
        background: #0348A2;
    }


    .kpi-box_pkh .label {
        color: #ffff;
        font-size: 18px;
        font-weight: 600;
    }

    .box-pkh {
        text-align: center;
    }

    .box-pkh .label-2 {
        font-weight: 600;
        color: #002F81;
        font-size: 19px;
    }

    .box-pkh .value-2 {
        font-size: 40px;
        font-weight: 800;
    }

    .box-pkh .value {
        font-size: 40px;
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



    .table-wrapper_pkh>.box-pkh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-bottom: 1px solid #0348A2;
    }

    .table-wrapper_pkh>.box-pkh:nth-child(2),
    .table-wrapper_pkh>.box-pkh:nth-child(4) {
        border-right: none;
    }

    .table-wrapper_pkh>.box-pkh:nth-child(3),
    .table-wrapper_pkh>.box-pkh:nth-child(4) {
        border-bottom: none;
    }


    /* 2 trên 2 dưới */
    .thongke-grid_pkh {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_pkh>.box-pkh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 24px;
        border-right: 1px solid #0348A2;
        border-bottom: 1px solid #0348A2;
    }

    .thongke-grid_pkh>.box-pkh:nth-child(2),
    .thongke-grid_pkh>.box-pkh:nth-child(4) {
        border-right: none;
    }

    .thongke-grid_pkh>.box-pkh:nth-child(3),
    .thongke-grid_pkh>.box-pkh:nth-child(4) {
        border-bottom: none;
    }


    /*2  2 trên 2 dưới */
    .thongke-grid_pkh-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_pkh-2>.box-pkh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        border-bottom: 1px solid #0348A2;
    }

    .thongke-grid_pkh-2>.box-pkh:nth-child(2),
    .thongke-grid_pkh-2>.box-pkh:nth-child(4),
    .thongke-grid_pkh-2>.box-pkh:nth-child(6) {
        border-right: none;
    }

    .thongke-grid_pkh-2>.box-pkh:nth-child(3),
    .thongke-grid_pkh-2>.box-pkh:nth-child(4),
    .thongke-grid_pkh-2>.box-pkh:nth-child(5),
    .thongke-grid_pkh-2>.box-pkh:nth-child(6) {
        border-bottom: none;
    }

    .thongke-grid_pkh-2>.box-pkh:nth-child(3),
    .thongke-grid_pkh-2>.box-pkh:nth-child(4) {
        border-bottom: 1px solid #0348A2;
    }

    /*3  2 trên 2 dưới */
    .thongke-grid_pkh-3 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_pkh-3>.box-pkh {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        border-bottom: 1px solid #0348A2;
    }

    .thongke-grid_pkh-3>.box-pkh:nth-child(2),
    .thongke-grid_pkh-3>.box-pkh:nth-child(4),
    .thongke-grid_pkh-3>.box-pkh:nth-child(6),
    .thongke-grid_pkh-3>.box-pkh:nth-child(8) {
        border-right: none;
    }

    .thongke-grid_pkh-3>.box-pkh:nth-child(3),
    .thongke-grid_pkh-3>.box-pkh:nth-child(4),
    .thongke-grid_pkh-3>.box-pkh:nth-child(5),
    .thongke-grid_pkh-3>.box-pkh:nth-child(6),
    .thongke-grid_pkh-3>.box-pkh:nth-child(7),
    .thongke-grid_pkh-3>.box-pkh:nth-child(8) {
        border-bottom: none;
    }

    .thongke-grid_pkh-3>.box-pkh:nth-child(3),
    .thongke-grid_pkh-3>.box-pkh:nth-child(4),
    .thongke-grid_pkh-3>.box-pkh:nth-child(5),
    .thongke-grid_pkh-3>.box-pkh:nth-child(6) {
        border-bottom: 1px solid #0348A2;
    }

    .kpi-box_pkh.hong {
        background: #FFE9E9;
    }

    .kpi-box_pkh.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_pkh.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_pkh.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_pkh.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }

    .table-wrapper_pkh {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_pkh);
        overflow: hidden;
    }

    .avatar_pkh {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_pkh div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_pkh {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_pkh {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_pkh::before {
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

    .step_pkh:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }


    .step_pkh.done_pkh .dot_pkh {
        background: #10b981;
        /* xanh */
    }

    .step_pkh.pending_pkh .dot_pkh {
        background: #ccc;
        /* xám chờ */
    }

    .content_pkh {
        margin-left: 20px;
    }

    .title_pkh {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_pkh {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_pkh {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    table.pkh thead th {
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
    .app_pkh {
        height: 100%;
    }

    .app_pkh {
        display: flex;
        flex-direction: column;
    }

    .header_pkh {
        /* --header-h: 110px; */
        /* height: var(--header-h); */
    }

    .container_pkh {
        flex: 1;
        min-height: 0;
        display: flex;
        gap: 7px;
    }

    /* 2) Sidebar là column, table chiếm hết phần còn lại */
    .sidebar_pkh {
        display: flex;
        flex-direction: column;
        min-width: 420px;
    }

    .sidebar_pkh .kpi-box_pkh,
    .sidebar_pkh .thongke-box_pkh,
    .sidebar_pkh .head_pkh {
        flex: none;
    }

    /* 3) Table flex:1; tbody scroll full height */
    .sidebar_pkh table.pkh {
        flex: 1;
        /* chiếm toàn bộ chiều cao còn lại của sidebar */
        min-height: 0;
        display: flex;
        flex-direction: column;
        border-collapse: separate;
        /* tránh bug khi sticky */
    }

    .sidebar_pkh table.pkh thead {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
        /* màu nền để che nội dung khi sticky */
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .sidebar_pkh table.pkh tbody {
        display: block;
        /* để set overflow */
        flex: 1;
        /* quan trọng: fill hết chiều cao còn lại */
        min-height: 0;
        overflow: auto;
        /* scroll ở tbody */
    }

    .sidebar_pkh table.pkh tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* canh cột thead/tbody khớp nhau */
    }



    /* Hiệu ứng ẩn/hiện mượt khi bạn render lại */
    .hidden_pkh {
        opacity: 0;
        transition: opacity .15s ease;
    }

    .table-body_pkh {
        transition: opacity .15s ease;
    }

    .table-body_pkh .image img {
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
</style>