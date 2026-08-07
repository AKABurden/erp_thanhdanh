<style>
    :root {
        --bg_dxnb_purchases: #f3f6fb;
        --card_dxnb_purchases: #ffffff;
        --text_dxnb_purchases: #0f1b40;
        --muted_dxnb_purchases: #6b7280;
        --line_dxnb_purchases: #e5e7eb;
        --primary_dxnb_purchases: #0a58ff;
        --green_dxnb_purchases: #10b981;
        --red_dxnb_purchases: #ef4444;
        --yellow_dxnb_purchases: #f59e0b;
        --chip_dxnb_purchases: #eef2ff;
        --radius_dxnb_purchases: 12px;
        --shadow_dxnb_purchases: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_dxnb_purchases {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_dxnb_purchases {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_dxnb_purchases {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_dxnb_purchases {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_dxnb_purchases {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_dxnb_purchases .main {
        font-size: 1.8rem;
    }

    .header-title_dxnb_purchases .child {
        font-size: 20px;
    }

    .header-right_dxnb_purchases {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_dxnb_purchases {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_dxnb_purchases {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_dxnb_purchases {
        background: var(--card_dxnb_purchases);
        border-radius: var(--radius_dxnb_purchases);
        box-shadow: var(--shadow_dxnb_purchases);
    }

    .card-pad_dxnb_purchases {
        padding: 16px 18px;
    }

    .card-table_dxnb_purchases {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dxnb_purchases {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_dxnb_purchases {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_dxnb_purchases {
        border: 1px solid var(--line_dxnb_purchases);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_dxnb_purchases .label_dxnb_purchases {
        color: var(--muted_dxnb_purchases);
        font-weight: 600;
        color: #002F81;
    }

    .stat_dxnb_purchases .value_dxnb_purchases {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_dxnb_purchases {
        background-color: #DCFDE9;
    }

    .label-hong_dxnb_purchases {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_dxnb_purchases {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_dxnb_purchases {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_dxnb_purchases .pill_dxnb_purchases {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_dxnb_purchases);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_dxnb_purchases);
    }

    .kpi-sub_dxnb_purchases {
        color: var(--muted_dxnb_purchases);
        font-weight: 600;
    }

    .dot_dxnb_purchases_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_dxnb_purchases {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_dxnb_purchases {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_dxnb_purchases {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_dxnb_purchases {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_dxnb_purchases {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_dxnb_purchases svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_dxnb_purchases .txt_dxnb_purchases {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_dxnb_purchases {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_dxnb_purchases {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_dxnb_purchases {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dxnb_purchases .head_dxnb_purchases {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_dxnb_purchases .h-title_dxnb_purchases {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_dxnb_purchases {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_dxnb_purchases {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_dxnb_purchases.green_dxnb_purchases {
        background: var(--green_dxnb_purchases);
    }

    .label-status_dxnb_purchases.red_dxnb_purchases {
        background: var(--red_dxnb_purchases);
    }

    .label-status_dxnb_purchases.yellow_dxnb_purchases {
        background: var(--yellow_dxnb_purchases);
    }

    .text-status_dxnb_purchases {
        font-size: 14px;
        color: var(--text_dxnb_purchases);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_dxnb_purchases {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_dxnb_purchases_progress.green_dxnb_purchases {
        background: var(--green_dxnb_purchases);
    }

    .dot_dxnb_purchases_progress.red_dxnb_purchases {
        background: var(--red_dxnb_purchases);
    }

    .dot_dxnb_purchases_progress.yellow_dxnb_purchases {
        background: var(--yellow_dxnb_purchases);
    }

    .dot_dxnb_purchases.green_dxnb_purchases {
        background: var(--green_dxnb_purchases);
    }

    .dot_dxnb_purchases.red_dxnb_purchases {
        background: var(--red_dxnb_purchases);
    }

    .dot_dxnb_purchases.yellow_dxnb_purchases {
        background: var(--yellow_dxnb_purchases);
    }

    table.dxnb_purchases {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_dxnb_purchases .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row;
    }

    thead.dxnb_purchases th.dxnb_purchases {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_dxnb_purchases);
        border-right: 1px solid var(--line_dxnb_purchases);
        font-size: 20px;
        text-align: center;
    }

    tbody.dxnb_purchases td.dxnb_purchases {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_dxnb_purchases);
        border-left: 1px solid var(--line_dxnb_purchases);
        border-right: 1px solid var(--line_dxnb_purchases);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.dxnb_purchases tr:hover td.dxnb_purchases {
        background: #fcfcff;
    }

    tbody.dxnb_purchases tr:nth-child(even) td.dxnb_purchases {
        background: #EEEEEE;
    }

    .status-badge_dxnb_purchases {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_dxnb_purchases {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_dxnb_purchases.green_dxnb_purchases {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_dxnb_purchases);
    }

    .chip_dxnb_purchases.red_dxnb_purchases {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_dxnb_purchases);
    }

    .chip_dxnb_purchases.yellow_dxnb_purchases {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_dxnb_purchases);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_dxnb_purchases {
            grid-template-columns: 1fr;
        }

        .tables_dxnb_purchases {
            grid-template-columns: 1fr;
        }

        .title_dxnb_purchases {
            font-size: 28px;
        }
    }

    .sidebar_dxnb_purchases {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_dxnb_purchases {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_dxnb_purchases);
        color: var(--text_dxnb_purchases);
        text-align: center;
    }

    .kpi-box_dxnb_purchases .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_dxnb_purchases .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_dxnb_purchases.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_dxnb_purchases.hong {
        background: #FFE9E9;
    }

    .kpi-box_dxnb_purchases.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_dxnb_purchases.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_dxnb_purchases.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_dxnb_purchases.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_dxnb_purchases {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_dxnb_purchases div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_dxnb_purchases {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_dxnb_purchases {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_dxnb_purchases::before {
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

    .step_dxnb_purchases:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_dxnb_purchases.done_dxnb_purchases .dot_dxnb_purchases_progress {
        background: #10b981;
        /* xanh */
    }

    .step_dxnb_purchases.pending_dxnb_purchases .dot_dxnb_purchases_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_dxnb_purchases {
        margin-left: 20px;
    }

    .title_dxnb_purchases {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_dxnb_purchases {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_dxnb_purchases {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_dxnb_purchases .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_dxnb_purchases {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_dxnb_purchases);
        overflow: hidden;
    }

    .table-wrapper_dxnb_purchases .table-body-dxnb_purchases {
        height: 100%;
        ;
    }

    .table-wrapper_dxnb_purchases table.dxnb_purchases tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_dxnb_purchases {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_dxnb_purchases {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.dxnb_purchases {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-dxnb_purchases {
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
    .table-wrapper_dxnb_purchases thead,
    .table-wrapper_dxnb_purchases tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.dxnb_purchases th.dxnb_purchases,
    tbody.dxnb_purchases td.dxnb_purchases {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.dxnb_purchases th.dxnb_purchases:nth-child(1), tbody.dxnb_purchases td.dxnb_purchases:nth-child(1) { width: 10%; }
thead.dxnb_purchases th.dxnb_purchases:nth-child(2), tbody.dxnb_purchases td.dxnb_purchases:nth-child(2) { width: 20%; }
thead.dxnb_purchases th.dxnb_purchases:nth-child(3), tbody.dxnb_purchases td.dxnb_purchases:nth-child(3) { width: 10%; }
thead.dxnb_purchases th.dxnb_purchases:nth-child(4), tbody.dxnb_purchases td.dxnb_purchases:nth-child(4) { width: 15%; }
thead.dxnb_purchases th.dxnb_purchases:nth-child(5), tbody.dxnb_purchases td.dxnb_purchases:nth-child(5) { width: 15%; }
thead.dxnb_purchases th.dxnb_purchases:nth-child(6), tbody.dxnb_purchases td.dxnb_purchases:nth-child(6) { width: 10%; }
thead.dxnb_purchases th.dxnb_purchases:nth-child(7), tbody.dxnb_purchases td.dxnb_purchases:nth-child(7) { width: 20%; }
*/
    td.marquee_dxnb_purchases {
        position: relative;
        width: 200px;
        /* Set a fixed width, adjust as needed */
        overflow: hidden;
        vertical-align: middle;
        padding: 0;
    }

    td.marquee_dxnb_purchases .marquee-content {
        display: inline-block;
        padding-left: 100%;
        animation: marquee_dxnb_purchases 8s linear infinite;
        white-space: nowrap;
    }

    @keyframes marquee_dxnb_purchases {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-100%);
        }
    }
</style>