<style>
    :root {
        --bg_thu: #f3f6fb;
        --card_thu: #ffffff;
        --text_thu: #0f1b40;
        --muted_thu: #6b7280;
        --line_thu: #e5e7eb;
        --primary_thu: #0a58ff;
        --green_thu: #10b981;
        --red_thu: #ef4444;
        --yellow_thu: #f59e0b;
        --chip_thu: #eef2ff;
        --radius_thu: 12px;
        --shadow_thu: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_thu {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_thu {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_thu {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_thu {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_thu {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_thu .main {
        font-size: 1.8rem;
    }

    .header-title_thu .child {
        font-size: 20px;
    }

    .header-right_thu {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_thu {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_thu {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_thu {
        background: var(--card_thu);
        border-radius: var(--radius_thu);
        box-shadow: var(--shadow_thu);
    }

    .card-pad_thu {
        padding: 16px 18px;
    }

    .card-table_thu {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_thu {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_thu {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_thu {
        border: 1px solid var(--line_thu);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_thu .label_thu {
        color: var(--muted_thu);
        font-weight: 600;
        color: #002F81;
    }

    .stat_thu .value_thu {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_thu {
        background-color: #DCFDE9;
    }

    .label-hong_thu {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_thu {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_thu {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_thu .pill_thu {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_thu);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_thu);
    }

    .kpi-sub_thu {
        color: var(--muted_thu);
        font-weight: 600;
    }

    .dot_thu_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_thu {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_thu {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_thu {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_thu {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_thu {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_thu svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_thu .txt_thu {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_thu {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_thu {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_thu {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_thu .head_thu {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_thu .h-title_thu {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_thu {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_thu {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_thu.green_thu {
        background: var(--green_thu);
    }

    .label-status_thu.red_thu {
        background: var(--red_thu);
    }

    .label-status_thu.yellow_thu {
        background: var(--yellow_thu);
    }

    .text-status_thu {
        font-size: 14px;
        color: var(--text_thu);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_thu {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_thu_progress.green_thu {
        background: var(--green_thu);
    }

    .dot_thu_progress.red_thu {
        background: var(--red_thu);
    }

    .dot_thu_progress.yellow_thu {
        background: var(--yellow_thu);
    }

    .dot_thu.green_thu {
        background: var(--green_thu);
    }

    .dot_thu.red_thu {
        background: var(--red_thu);
    }

    .dot_thu.yellow_thu {
        background: var(--yellow_thu);
    }

    table.thu {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_thu .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row-reverse;
    }

    thead.thu th.thu {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_thu);
        border-right: 1px solid var(--line_thu);
        font-size: 20px;
        text-align: center;
    }

    tbody.thu td.thu {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_thu);
        border-left: 1px solid var(--line_thu);
        border-right: 1px solid var(--line_thu);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.thu tr:hover td.thu {
        background: #fcfcff;
    }

    tbody.thu tr:nth-child(even) td.thu {
        background: #EEEEEE;
    }

    .status-badge_thu {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_thu {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_thu.green_thu {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_thu);
    }

    .chip_thu.red_thu {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_thu);
    }

    .chip_thu.yellow_thu {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_thu);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_thu {
            grid-template-columns: 1fr;
        }

        .tables_thu {
            grid-template-columns: 1fr;
        }

        .title_thu {
            font-size: 28px;
        }
    }

    .sidebar_thu {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_thu {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_thu);
        color: var(--text_thu);
        text-align: center;
    }

    .kpi-box_thu .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_thu .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_thu.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_thu.hong {
        background: #FFE9E9;
    }

    .kpi-box_thu.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_thu.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_thu.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_thu.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_thu {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_thu div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_thu {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_thu {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_thu::before {
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

    .step_thu:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_thu.done_thu .dot_thu_progress {
        background: #10b981;
        /* xanh */
    }

    .step_thu.pending_thu .dot_thu_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_thu {
        margin-left: 20px;
    }

    .title_thu {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_thu {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_thu {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_thu .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_thu {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_thu);
        overflow: hidden;
    }

    .table-wrapper_thu .table-body-thu {
        height: 100%;
        ;
    }

    .table-wrapper_thu table.thu tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_thu {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_thu {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.thu {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-thu {
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
    .table-wrapper_thu thead,
    .table-wrapper_thu tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.thu th.thu,
    tbody.thu td.thu {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.thu th.thu:nth-child(1), tbody.thu td.thu:nth-child(1) { width: 10%; }
thead.thu th.thu:nth-child(2), tbody.thu td.thu:nth-child(2) { width: 20%; }
thead.thu th.thu:nth-child(3), tbody.thu td.thu:nth-child(3) { width: 10%; }
thead.thu th.thu:nth-child(4), tbody.thu td.thu:nth-child(4) { width: 15%; }
thead.thu th.thu:nth-child(5), tbody.thu td.thu:nth-child(5) { width: 15%; }
thead.thu th.thu:nth-child(6), tbody.thu td.thu:nth-child(6) { width: 10%; }
thead.thu th.thu:nth-child(7), tbody.thu td.thu:nth-child(7) { width: 20%; }
*/
</style>