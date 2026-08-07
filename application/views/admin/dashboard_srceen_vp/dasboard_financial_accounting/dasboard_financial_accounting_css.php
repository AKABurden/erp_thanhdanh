<style>
    :root {
        --bg_dfac: #f3f6fb;
        --card_dfac: #ffffff;
        --text_dfac: #0f1b40;
        --muted_dfac: #6b7280;
        --line_dfac: #e5e7eb;
        --primary_dfac: #0a58ff;
        --green_dfac: #10b981;
        --red_dfac: #ef4444;
        --yellow_dfac: #FFD400;
        --chip_dfac: #eef2ff;
        --radius_dfac: 12px;
        --shadow_dfac: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_dfac {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_dfac {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_dfac {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_dfac {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_dfac {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_dfac .main {
        font-size: 1.8rem;
    }

    .header-title_dfac .child {
        font-size: 12px;
    }

    .header-right_dfac {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_dfac {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 10px 20px;
        min-height: 0
    }

    .top-kpis_dfac {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_dfac {
        background: var(--card_dfac);
        border-radius: var(--radius_dfac);
        box-shadow: var(--shadow_dfac);
    }

    .card-pad_dfac {
        padding: 16px 18px;
    }

    .card-table_dfac {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dfac {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_dfac {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_dfac {
        border: 1px solid var(--line_dfac);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_dfac .label_dfac {
        color: var(--muted_dfac);
        font-weight: 600;
        color: #002F81;
    }

    .stat_dfac .value_dfac {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_dfac {
        background-color: #DCFDE9;
    }

    .label-hong_dfac {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_dfac {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_dfac {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_dfac .pill_dfac {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_dfac);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_dfac);
    }

    .kpi-sub_dfac {
        color: var(--muted_dfac);
        font-weight: 600;
    }

    .donut-wrap_dfac {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_dfac {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_dfac {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_dfac {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_dfac svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_dfac .txt_dfac {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_dfac {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_dfac {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_dfac {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dfac .head_dfac {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_dfac .h-title_dfac {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_dfac {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_dfac {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_dfac.green_dfac {
        background: var(--green_dfac);
    }

    .label-status_dfac.red_dfac {
        background: var(--red_dfac);
    }

    .label-status_dfac.yellow_dfac {
        background: var(--yellow_dfac);
    }

    .text-status_dfac {
        font-size: 14px;
        color: var(--text_dfac);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_dfac {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_dfac.green_dfac {
        background: var(--green_dfac);
    }

    .dot_dfac.red_dfac {
        background: var(--red_dfac);
    }

    .dot_dfac.yellow_dfac {
        background: var(--yellow_dfac);
    }

    .head_dfac {
        padding: 20px;
    }

    table.dfac {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_dfac .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    thead.dfac th.dfac {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_dfac);
        border-right: 1px solid var(--line_dfac);
        font-size: 20px;
        text-align: center;
    }

    tbody.dfac td.dfac {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_dfac);
        border-left: 1px solid var(--line_dfac);
        border-right: 1px solid var(--line_dfac);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.dfac tr:hover td.dfac {
        background: #fcfcff;
    }

    tbody.dfac tr:nth-child(even) td.dfac {
        background: #EEEEEE;
    }

    .status-badge_dfac {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_dfac {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_dfac.green_dfac {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_dfac);
    }

    .chip_dfac.red_dfac {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_dfac);
    }

    .chip_dfac.yellow_dfac {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_dfac);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_dfac {
            grid-template-columns: 1fr;
        }

        .tables_dfac {
            grid-template-columns: 1fr;
        }

        .title_dfac {
            font-size: 28px;
        }
    }

    .sidebar_dfac {
        background: white;
        border-radius: 10px;
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_dfac {
        border-radius: 12px;
        padding: 7px;
        margin: 10px 10px 0px 10px;
        box-shadow: var(--shadow_dfac);
    }

    .kpi-box_dfac.blue_dfac {
        background: #0348A2;
    }


    .kpi-box_dfac .label {
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
    }

    .box-dfac {
        text-align: center;
        padding: 15px;
    }

    .box-dfac .label {
        font-weight: 600;
        color: #002F81;
        font-size: 19px;
    }

    .box-dfac .label-2 {
        font-weight: 600;
        color: #002F81;
        font-size: 19px;
    }

    .box-dfac .value-2 {
        font-size: 40px;
        font-weight: 800;
    }

    .box-dfac .value {
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



    .table-wrapper_dfac>.box-dfac {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        /* border-bottom: 1px solid #0348A2; */
    }

    .table-wrapper_dfac>.box-dfac:nth-child(2),
    .table-wrapper_dfac>.box-dfac:nth-child(4) {
        border-right: none;
    }

    .table-wrapper_dfac>.box-dfac:nth-child(3),
    .table-wrapper_dfac>.box-dfac:nth-child(4) {
        border-bottom: none;
    }


    /* 2 trên 2 dưới */
    .thongke-grid_dfac {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_dfac>.box-dfac {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        /* border-bottom: 1px solid #0348A2; */
    }

    .thongke-grid_dfac>.box-dfac:nth-child(2),
    .thongke-grid_dfac>.box-dfac:nth-child(4) {
        border-right: none;
    }

    .thongke-grid_dfac>.box-dfac:nth-child(3),
    .thongke-grid_dfac>.box-dfac:nth-child(4) {
        border-bottom: none;
    }


    /*2  2 trên 2 dưới */
    .thongke-grid_dfac-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_dfac-2>.box-dfac {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        /* border-bottom: 1px solid #0348A2; */
    }

    .thongke-grid_dfac-2>.box-dfac:nth-child(2),
    .thongke-grid_dfac-2>.box-dfac:nth-child(4),
    .thongke-grid_dfac-2>.box-dfac:nth-child(6) {
        border-right: none;
    }

    .thongke-grid_dfac-2>.box-dfac:nth-child(3),
    .thongke-grid_dfac-2>.box-dfac:nth-child(4),
    .thongke-grid_dfac-2>.box-dfac:nth-child(5),
    .thongke-grid_dfac-2>.box-dfac:nth-child(6) {
        border-bottom: none;
    }

    .thongke-grid_dfac-2>.box-dfac:nth-child(3),
    .thongke-grid_dfac-2>.box-dfac:nth-child(4) {
        border-bottom: 1px solid #0348A2;
    }

    /*3  2 trên 2 dưới */
    .thongke-grid_dfac-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_dfac-3>.box-dfac {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        /* border-bottom: 1px solid #0348A2; */
    }

    .thongke-grid_dfac-2>.box-dfac:nth-child(1),
    .thongke-grid_dfac-2>.box-dfac:nth-child(2),
    .thongke-grid_dfac-2>.box-dfac:nth-child(3),
    .thongke-grid_dfac-2>.box-dfac:nth-child(4),
    .thongke-grid_dfac-2>.box-dfac:nth-child(6) {
        border-right: none;
    }

    .thongke-grid_dfac-3>.box-dfac:nth-child(3),
    .thongke-grid_dfac-3>.box-dfac:nth-child(4),
    .thongke-grid_dfac-3>.box-dfac:nth-child(6) {
        border-right: none;
    }

    .thongke-grid_dfac-3>.box-dfac:nth-child(3),
    .thongke-grid_dfac-3>.box-dfac:nth-child(4),
    .thongke-grid_dfac-3>.box-dfac:nth-child(5),
    .thongke-grid_dfac-3>.box-dfac:nth-child(6),
    .thongke-grid_dfac-3>.box-dfac:nth-child(7),
    .thongke-grid_dfac-3>.box-dfac:nth-child(8) {
        border-bottom: none;
    }


    .kpi-box_dfac.hong {
        background: #FFE9E9;
    }

    .kpi-box_dfac.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_dfac.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_dfac.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_dfac.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }

    .table-wrapper_dfac {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_dfac);
        overflow: hidden;
    }

    .avatar_dfac {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_dfac div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_dfac {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_dfac {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_dfac::before {
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

    .step_dfac:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }


    .step_dfac.done_dfac .dot_dfac {
        background: #10b981;
        /* xanh */
    }

    .step_dfac.pending_dfac .dot_dfac {
        background: #ccc;
        /* xám chờ */
    }

    .content_dfac {
        margin-left: 20px;
    }

    .title_dfac {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_dfac {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_dfac {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    table.dfac thead th {
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
    .app_dfac {
        height: 100%;
    }

    .app_dfac {
        display: flex;
        flex-direction: column;
    }

    .header_dfac {
        /* --header-h: 110px; */
        /* height: var(--header-h); */
    }

    .container_dfac {
        flex: 1;
        min-height: 0;
        display: flex;
        gap: 7px;
    }

    /* 2) Sidebar là column, table chiếm hết phần còn lại */
    .sidebar_dfac {
        display: flex;
        flex-direction: column;
        min-width: 420px;
        color: red !important;
    }

    .sidebar_dfac .kpi-box_dfac,
    .sidebar_dfac .thongke-box_dfac,
    .sidebar_dfac .head_dfac {
        flex: none;
    }

    /* 3) Table flex:1; tbody scroll full height */
    .sidebar_dfac table.dfac {
        flex: 1;
        /* chiếm toàn bộ chiều cao còn lại của sidebar */
        min-height: 0;
        display: flex;
        flex-direction: column;
        border-collapse: separate;
        /* tránh bug khi sticky */
    }

    .sidebar_dfac table.dfac thead {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
        /* màu nền để che nội dung khi sticky */
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .sidebar_dfac table.dfac tbody {
        display: block;
        /* để set overflow */
        flex: 1;
        /* quan trọng: fill hết chiều cao còn lại */
        min-height: 0;
        overflow: auto;
        /* scroll ở tbody */
    }

    .sidebar_dfac table.dfac tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* canh cột thead/tbody khớp nhau */
    }



    /* Hiệu ứng ẩn/hiện mượt khi bạn render lại */
    .hidden_dfac {
        opacity: 0;
        transition: opacity .15s ease;
    }

    .table-body_dfac {
        transition: opacity .15s ease;
    }

    .table-body_dfac .image img {
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

    .thongke-grid_dfac-1 {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        border-radius: 8px;
        overflow: hidden;
        padding: 0px 12px 0px 12px;
    }

    .thongke-grid_dfac-1>.box-dfac {
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        border-right: 1px solid #0348A2;
        /* border-bottom: 1px solid #0348A2; */
    }

    .thongke-grid_dfac-1>.box-dfac:nth-child(1) {
        border-right: none;
    }

    .sidebar_dfac .value {
        color: red !important;
    }

    .sidebar_dfac .label::not(.blue_dfac) {
        color: red !important;
    }

    .table-wrapper_dfac .value {
        color: #002F81 !important;
    }

    .table-wrapper_dfac .label::not(.blue_dfac) {
        color: #002F81 !important;
    }
</style>