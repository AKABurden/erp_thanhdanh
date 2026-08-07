<style>
    :root {
        --bg_purchaseorder: #f3f6fb;
        --card_purchaseorder: #ffffff;
        --text_purchaseorder: #0f1b40;
        --muted_purchaseorder: #6b7280;
        --line_purchaseorder: #e5e7eb;
        --primary_purchaseorder: #0a58ff;
        --green_purchaseorder: #10b981;
        --red_purchaseorder: #ef4444;
        --yellow_purchaseorder: #f59e0b;
        --chip_purchaseorder: #eef2ff;
        --radius_purchaseorder: 12px;
        --shadow_purchaseorder: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_purchaseorder {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_purchaseorder {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_purchaseorder {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_purchaseorder {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_purchaseorder {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_purchaseorder .main {
        font-size: 1.8rem;
    }

    .header-title_purchaseorder .child {
        font-size: 20px;
    }

    .header-right_purchaseorder {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_purchaseorder {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_purchaseorder {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_purchaseorder {
        background: var(--card_purchaseorder);
        border-radius: var(--radius_purchaseorder);
        box-shadow: var(--shadow_purchaseorder);
    }

    .card-pad_purchaseorder {
        padding: 16px 18px;
    }

    .card-table_purchaseorder {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_purchaseorder {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_purchaseorder {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_purchaseorder {
        border: 1px solid var(--line_purchaseorder);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_purchaseorder .label_purchaseorder {
        color: var(--muted_purchaseorder);
        font-weight: 600;
        color: #002F81;
    }

    .stat_purchaseorder .value_purchaseorder {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_purchaseorder {
        background-color: #DCFDE9;
    }

    .label-hong_purchaseorder {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_purchaseorder {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_purchaseorder {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_purchaseorder .pill_purchaseorder {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_purchaseorder);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_purchaseorder);
    }

    .kpi-sub_purchaseorder {
        color: var(--muted_purchaseorder);
        font-weight: 600;
    }

    .dot_purchaseorder_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_purchaseorder {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_purchaseorder {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_purchaseorder {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_purchaseorder {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_purchaseorder {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_purchaseorder svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_purchaseorder .txt_purchaseorder {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_purchaseorder {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_purchaseorder {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_purchaseorder {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_purchaseorder .head_purchaseorder {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_purchaseorder .h-title_purchaseorder {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_purchaseorder {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_purchaseorder {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_purchaseorder.green_purchaseorder {
        background: var(--green_purchaseorder);
    }

    .label-status_purchaseorder.red_purchaseorder {
        background: var(--red_purchaseorder);
    }

    .label-status_purchaseorder.yellow_purchaseorder {
        background: var(--yellow_purchaseorder);
    }

    .text-status_purchaseorder {
        font-size: 14px;
        color: var(--text_purchaseorder);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_purchaseorder {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_purchaseorder_progress.green_purchaseorder {
        background: var(--green_purchaseorder);
    }

    .dot_purchaseorder_progress.red_purchaseorder {
        background: var(--red_purchaseorder);
    }

    .dot_purchaseorder_progress.yellow_purchaseorder {
        background: var(--yellow_purchaseorder);
    }

    .dot_purchaseorder.green_purchaseorder {
        background: var(--green_purchaseorder);
    }

    .dot_purchaseorder.red_purchaseorder {
        background: var(--red_purchaseorder);
    }

    .dot_purchaseorder.yellow_purchaseorder {
        background: var(--yellow_purchaseorder);
    }

    table.purchaseorder {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_purchaseorder .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row;
    }

    thead.purchaseorder th.purchaseorder {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_purchaseorder);
        border-right: 1px solid var(--line_purchaseorder);
        font-size: 20px;
        text-align: center;
    }

    tbody.purchaseorder td.purchaseorder {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_purchaseorder);
        border-left: 1px solid var(--line_purchaseorder);
        border-right: 1px solid var(--line_purchaseorder);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.purchaseorder tr:hover td.purchaseorder {
        background: #fcfcff;
    }

    tbody.purchaseorder tr:nth-child(even) td.purchaseorder {
        background: #EEEEEE;
    }

    .status-badge_purchaseorder {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_purchaseorder {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_purchaseorder.green_purchaseorder {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_purchaseorder);
    }

    .chip_purchaseorder.red_purchaseorder {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_purchaseorder);
    }

    .chip_purchaseorder.yellow_purchaseorder {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_purchaseorder);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_purchaseorder {
            grid-template-columns: 1fr;
        }

        .tables_purchaseorder {
            grid-template-columns: 1fr;
        }

        .title_purchaseorder {
            font-size: 28px;
        }
    }

    .sidebar_purchaseorder {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_purchaseorder {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_purchaseorder);
        color: var(--text_purchaseorder);
        text-align: center;
    }

    .kpi-box_purchaseorder .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_purchaseorder .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_purchaseorder.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_purchaseorder.hong {
        background: #FFE9E9;
    }

    .kpi-box_purchaseorder.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_purchaseorder.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_purchaseorder.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_purchaseorder.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_purchaseorder {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_purchaseorder div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_purchaseorder {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_purchaseorder {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_purchaseorder::before {
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

    .step_purchaseorder:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_purchaseorder.done_purchaseorder .dot_purchaseorder_progress {
        background: #10b981;
        /* xanh */
    }

    .step_purchaseorder.pending_purchaseorder .dot_purchaseorder_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_purchaseorder {
        margin-left: 20px;
    }

    .title_purchaseorder {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_purchaseorder {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_purchaseorder {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_purchaseorder .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_purchaseorder {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_purchaseorder);
        overflow: hidden;
    }

    .table-wrapper_purchaseorder .table-body-purchaseorder {
        height: 100%;
        ;
    }

    .table-wrapper_purchaseorder table.purchaseorder tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_purchaseorder {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_purchaseorder {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.purchaseorder {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-purchaseorder {
        flex: 1;
        /* ĐÂY LÀ THUỘC TÍNH BẠN YÊU CẦU */
        overflow-y: auto;
        /* Thêm thanh cuộn khi cần */
        display: block;
        /* Bắt buộc để overflow hoạt động */
    }


    /* --- PHẦN 3: SỬA LỖI CÁC CỘT BỊ LỆCH SAU KHI DÙNG FLEX --- */
    /* (Quan trọng) */

    /* Vì table đã là flex, ta phải "ép" thead và các hàng tr quay lại layout bảng */
    .table-wrapper_purchaseorder thead,
    .table-wrapper_purchaseorder tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.purchaseorder th.purchaseorder,
    tbody.purchaseorder td.purchaseorder {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.purchaseorder th.purchaseorder:nth-child(1), tbody.purchaseorder td.purchaseorder:nth-child(1) { width: 10%; }
thead.purchaseorder th.purchaseorder:nth-child(2), tbody.purchaseorder td.purchaseorder:nth-child(2) { width: 20%; }
thead.purchaseorder th.purchaseorder:nth-child(3), tbody.purchaseorder td.purchaseorder:nth-child(3) { width: 10%; }
thead.purchaseorder th.purchaseorder:nth-child(4), tbody.purchaseorder td.purchaseorder:nth-child(4) { width: 15%; }
thead.purchaseorder th.purchaseorder:nth-child(5), tbody.purchaseorder td.purchaseorder:nth-child(5) { width: 15%; }
thead.purchaseorder th.purchaseorder:nth-child(6), tbody.purchaseorder td.purchaseorder:nth-child(6) { width: 10%; }
thead.purchaseorder th.purchaseorder:nth-child(7), tbody.purchaseorder td.purchaseorder:nth-child(7) { width: 20%; }
*/
    td.marquee_purchaseorder {
        position: relative;
        width: 200px; /* Set a fixed width, adjust as needed */
        overflow: hidden;
        vertical-align: middle;
        padding: 0;
    }
    td.marquee_purchaseorder .marquee-content {
        display: inline-block;
        padding-left: 100%;
        animation: marquee_purchaseorder 8s linear infinite;
        white-space: nowrap;
    }

    @keyframes marquee_purchaseorder {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-100%);
        }
    }
</style>