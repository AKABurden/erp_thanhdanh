<style>
    :root {
        --bg_pkd: #f3f6fb;
        --card_pkd: #ffffff;
        --text_pkd: #0f1b40;
        --muted_pkd: #6b7280;
        --line_pkd: #e5e7eb;
        --primary_pkd: #0a58ff;
        --green_pkd: #10b981;
        --red_pkd: #ef4444;
        --yellow_pkd: #FFD400;
        --chip_pkd: #eef2ff;
        --radius_pkd: 12px;
        --shadow_pkd: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_pkd {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_pkd {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_pkd {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_pkd {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_pkd {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_pkd .main {
        font-size: 1.8rem;
    }

    .header-title_pkd .child {
        font-size: 12px;
    }

    .header-right_pkd {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .dot_pkd {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .container_pkd {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 10px 20px;
        min-height: 0
    }

    .top-kpis_pkd {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_pkd {
        background: var(--card_pkd);
        border-radius: var(--radius_pkd);
        box-shadow: var(--shadow_pkd);
    }

    .card-pad_pkd {
        padding: 16px 18px;
    }

    .card-table_pkd {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_pkd {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_pkd {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_pkd {
        border: 1px solid var(--line_pkd);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_pkd .label_pkd {
        color: var(--muted_pkd);
        font-weight: 600;
        color: #002F81;
    }

    .stat_pkd .value_pkd {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_pkd {
        background-color: #DCFDE9;
    }

    .label-hong_pkd {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_pkd {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_pkd {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_pkd .pill_pkd {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_pkd);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_pkd);
    }

    .kpi-sub_pkd {
        color: var(--muted_pkd);
        font-weight: 600;
    }

    .donut-wrap_pkd {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_pkd {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_pkd {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_pkd {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_pkd svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_pkd .txt_pkd {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_pkd {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_pkd {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_pkd {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_pkd .head_pkd {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_pkd .h-title_pkd {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_pkd {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_pkd {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_pkd.green_pkd {
        background: var(--green_pkd);
    }

    .label-status_pkd.red_pkd {
        background: var(--red_pkd);
    }

    .label-status_pkd.yellow_pkd {
        background: var(--yellow_pkd);
    }

    .text-status_pkd {
        font-size: 14px;
        color: var(--text_pkd);
        font-weight: 600;
        display: flex;
        align-items: center;
    }



    .dot_pkd.green_pkd {
        background: var(--green_pkd);
    }

    .dot_pkd.red_pkd {
        background: var(--red_pkd);
    }

    .dot_pkd.yellow_pkd {
        background: var(--yellow_pkd);
    }

    .head_pkd {
        padding: 20px;
    }

    table.pkd {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

 
    thead.pkd th.pkd {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_pkd);
        border-right: 1px solid var(--line_pkd);
        font-size: 20px;
        text-align: center;
    }

    tbody.pkd td.pkd {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_pkd);
        border-left: 1px solid var(--line_pkd);
        border-right: 1px solid var(--line_pkd);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.pkd tr:hover td.pkd {
        background: #fcfcff;
    }

    tbody.pkd tr:nth-child(even) td.pkd {
        background: #EEEEEE;
    }

    .status-badge_pkd {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_pkd {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_pkd.green_pkd {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_pkd);
    }

    .chip_pkd.red_pkd {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_pkd);
    }

    .chip_pkd.yellow_pkd {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_pkd);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_pkd {
            grid-template-columns: 1fr;
        }

        .tables_pkd {
            grid-template-columns: 1fr;
        }

        .title_pkd {
            font-size: 28px;
        }
    }

    .sidebar_pkd {
        background: white;
        border-radius: 10px;
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_pkd {
        border-radius: 12px;
        padding: 10px;
        margin: 10px 10px 0px 10px;
        box-shadow: var(--shadow_pkd);
    }

    .kpi-box_pkd.nau {
        background: #BA925D;
    }

    .kpi-box_pkd .label {
        color: #ffff;
        font-size: 22px;
        font-weight: 600;
    }

    .box-pkd {
        text-align: center;
    }

    .box-pkd .label {
        font-weight: 600;
        color: #002F81;
        font-size: 25px;
    }

    .box-pkd .value {
        font-size: 45px;
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

    /* 2 trên 2 dưới */
    .thongke-grid_pkd {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_pkd>.box-pkd {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 22px;
        border-right: 1px solid #BA925D;
        border-bottom: 1px solid #BA925D;
    }

    .thongke-grid_pkd>.box-pkd:nth-child(2),
    .thongke-grid_pkd>.box-pkd:nth-child(4) {
        border-right: none;
    }

    .thongke-grid_pkd>.box-pkd:nth-child(3),
    .thongke-grid_pkd>.box-pkd:nth-child(4) {
        border-bottom: none;
    }

    .kpi-box_pkd.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_pkd.hong {
        background: #FFE9E9;
    }

    .kpi-box_pkd.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_pkd.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_pkd.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_pkd.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }

    .table-wrapper_pkd {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_pkd);
        overflow: hidden;
    }

    .avatar_pkd {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_pkd div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_pkd {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_pkd {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_pkd::before {
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

    .step_pkd:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }


    .step_pkd.done_pkd .dot_pkd {
        background: #10b981;
        /* xanh */
    }

    .step_pkd.pending_pkd .dot_pkd {
        background: #ccc;
        /* xám chờ */
    }

    .content_pkd {
        margin-left: 20px;
    }

    .title_pkd {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_pkd {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_pkd {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    table.pkd thead th {
        background: #BA925D;
        color: white;
        font-weight: 800;
        padding: 12px 14px;
        border-left: 1px solid #838383;
        border-right: unset;
        font-size: 20px;
        width: 25%;
        text-align: center;
    }

    /* 1) Toàn trang co giãn chuẩn */
    html,
    body,
    .app_pkd {
        height: 100%;
    }

    .app_pkd {
        display: flex;
        flex-direction: column;
    }

    .header_pkd {
        /* --header-h: 110px;
        height: var(--header-h); */
    }

    .container_pkd {
        flex: 1;
        min-height: 0;
        display: flex;
        gap: 7px;
    }

    /* 2) Sidebar là column, table chiếm hết phần còn lại */
    .sidebar_pkd {
        display: flex;
        flex-direction: column;
        min-width: 420px;
    }

    .sidebar_pkd .kpi-box_pkd,
    .sidebar_pkd .thongke-box_pkd,
    .sidebar_pkd .head_pkd {
        flex: none;
    }

    /* 3) Table flex:1; tbody scroll full height */
    .sidebar_pkd table.pkd {
        flex: 1;
        /* chiếm toàn bộ chiều cao còn lại của sidebar */
        min-height: 0;
        display: flex;
        flex-direction: column;
        border-collapse: separate;
        /* tránh bug khi sticky */
    }

    .sidebar_pkd table.pkd thead {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
        /* màu nền để che nội dung khi sticky */
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .sidebar_pkd table.pkd tbody {
        display: block;
        /* để set overflow */
        flex: 1;
        /* quan trọng: fill hết chiều cao còn lại */
        min-height: 0;
        overflow: auto;
        /* scroll ở tbody */
    }

    .sidebar_pkd table.pkd tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* canh cột thead/tbody khớp nhau */
    }

    .table-wrapper_pkd .kpi-box_pkd,
    .table-wrapper_pkd .thongke-box_pkd,
    .table-wrapper_pkd .head_pkd {
        flex: none;
    }

    /* 3) Table flex:1; tbody scroll full height */
    .table-wrapper_pkd table.pkd {
        flex: 1;
        /* chiếm toàn bộ chiều cao còn lại của sidebar */
        min-height: 0;
        display: flex;
        flex-direction: column;
        border-collapse: separate;
        /* tránh bug khi sticky */
    }

    .table-wrapper_pkd table.pkd thead {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
        /* màu nền để che nội dung khi sticky */
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .table-wrapper_pkd table.pkd tbody {
        display: block;
        /* để set overflow */
        flex: 1;
        /* quan trọng: fill hết chiều cao còn lại */
        min-height: 0;
        overflow: auto;
        /* scroll ở tbody */
    }

    .table-wrapper_pkd table.pkd tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* canh cột thead/tbody khớp nhau */
    }


    /* Hiệu ứng ẩn/hiện mượt khi bạn render lại */
    .hidden_pkd {
        opacity: 0;
        transition: opacity .15s ease;
    }

    .table-body_pkd {
        transition: opacity .15s ease;
    }

    .table-body_pkd .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .dot_pkd {
        width: 40px !important;
        height: 18px !important;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    
</style>