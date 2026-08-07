<style>
    :root {
        --bg_hdbcx: #f3f6fb;
        --card_hdbcx: #ffffff;
        --text_hdbcx: #0f1b40;
        --muted_hdbcx: #6b7280;
        --line_hdbcx: #e5e7eb;
        --primary_hdbcx: #0a58ff;
        --green_hdbcx: #10b981;
        --red_hdbcx: #ef4444;
        --yellow_hdbcx: #f59e0b;
        --chip_hdbcx: #eef2ff;
        --radius_hdbcx: 12px;
        --shadow_hdbcx: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_hdbcx {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_hdbcx {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_hdbcx {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_hdbcx {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_hdbcx {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_hdbcx .main {
        font-size: 1.8rem;
    }

    .header-title_hdbcx .child {
        font-size: 20px;
    }

    .header-right_hdbcx {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_hdbcx {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_hdbcx {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_hdbcx {
        background: var(--card_hdbcx);
        border-radius: var(--radius_hdbcx);
        box-shadow: var(--shadow_hdbcx);
    }

    .card-pad_hdbcx {
        padding: 16px 18px;
    }

    .card-table_hdbcx {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_hdbcx {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_hdbcx {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_hdbcx {
        border: 1px solid var(--line_hdbcx);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_hdbcx .label_hdbcx {
        color: var(--muted_hdbcx);
        font-weight: 600;
        color: #002F81;
    }

    .stat_hdbcx .value_hdbcx {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_hdbcx {
        background-color: #DCFDE9;
    }

    .label-hong_hdbcx {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_hdbcx {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_hdbcx {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_hdbcx .pill_hdbcx {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_hdbcx);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_hdbcx);
    }

    .kpi-sub_hdbcx {
        color: var(--muted_hdbcx);
        font-weight: 600;
    }

    .dot_hdbcx_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_hdbcx {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_hdbcx {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_hdbcx {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_hdbcx {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_hdbcx {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_hdbcx svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_hdbcx .txt_hdbcx {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_hdbcx {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_hdbcx {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_hdbcx {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_hdbcx .head_hdbcx {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_hdbcx .h-title_hdbcx {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_hdbcx {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_hdbcx {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_hdbcx.green_hdbcx {
        background: var(--green_hdbcx);
    }

    .label-status_hdbcx.red_hdbcx {
        background: var(--red_hdbcx);
    }

    .label-status_hdbcx.yellow_hdbcx {
        background: var(--yellow_hdbcx);
    }

    .text-status_hdbcx {
        font-size: 14px;
        color: var(--text_hdbcx);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_hdbcx {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_hdbcx_progress.green_hdbcx {
        background: var(--green_hdbcx);
    }

    .dot_hdbcx_progress.red_hdbcx {
        background: var(--red_hdbcx);
    }

    .dot_hdbcx_progress.yellow_hdbcx {
        background: var(--yellow_hdbcx);
    }

    .dot_hdbcx.green_hdbcx {
        background: var(--green_hdbcx);
    }

    .dot_hdbcx.red_hdbcx {
        background: var(--red_hdbcx);
    }

    .dot_hdbcx.yellow_hdbcx {
        background: var(--yellow_hdbcx);
    }

    table.hdbcx {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_hdbcx .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row-reverse;
    }

    thead.hdbcx th.hdbcx {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_hdbcx);
        border-right: 1px solid var(--line_hdbcx);
        font-size: 20px;
        text-align: center;
    }

    tbody.hdbcx td.hdbcx {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_hdbcx);
        border-left: 1px solid var(--line_hdbcx);
        border-right: 1px solid var(--line_hdbcx);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.hdbcx tr:hover td.hdbcx {
        background: #fcfcff;
    }

    tbody.hdbcx tr:nth-child(even) td.hdbcx {
        background: #EEEEEE;
    }

    .status-badge_hdbcx {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_hdbcx {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_hdbcx.green_hdbcx {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_hdbcx);
    }

    .chip_hdbcx.red_hdbcx {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_hdbcx);
    }

    .chip_hdbcx.yellow_hdbcx {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_hdbcx);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_hdbcx {
            grid-template-columns: 1fr;
        }

        .tables_hdbcx {
            grid-template-columns: 1fr;
        }

        .title_hdbcx {
            font-size: 28px;
        }
    }

    .sidebar_hdbcx {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_hdbcx {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_hdbcx);
        color: var(--text_hdbcx);
        text-align: center;
    }

    .kpi-box_hdbcx .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_hdbcx .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_hdbcx.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_hdbcx.hong {
        background: #FFE9E9;
    }

    .kpi-box_hdbcx.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_hdbcx.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_hdbcx.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_hdbcx.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_hdbcx {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_hdbcx div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_hdbcx {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_hdbcx {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_hdbcx::before {
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

    .step_hdbcx:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_hdbcx.done_hdbcx .dot_hdbcx_progress {
        background: #10b981;
        /* xanh */
    }

    .step_hdbcx.pending_hdbcx .dot_hdbcx_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_hdbcx {
        margin-left: 20px;
    }

    .title_hdbcx {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_hdbcx {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_hdbcx {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_hdbcx .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_hdbcx {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_hdbcx);
        overflow: hidden;
    }

    .table-wrapper_hdbcx .table-body-hdbcx {
        height: 100%;
        ;
    }

    .table-wrapper_hdbcx table.hdbcx tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_hdbcx {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_hdbcx {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.hdbcx {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-hdbcx {
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
    .table-wrapper_hdbcx thead,
    .table-wrapper_hdbcx tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.hdbcx th.hdbcx,
    tbody.hdbcx td.hdbcx {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.hdbcx th.hdbcx:nth-child(1), tbody.hdbcx td.hdbcx:nth-child(1) { width: 10%; }
thead.hdbcx th.hdbcx:nth-child(2), tbody.hdbcx td.hdbcx:nth-child(2) { width: 20%; }
thead.hdbcx th.hdbcx:nth-child(3), tbody.hdbcx td.hdbcx:nth-child(3) { width: 10%; }
thead.hdbcx th.hdbcx:nth-child(4), tbody.hdbcx td.hdbcx:nth-child(4) { width: 15%; }
thead.hdbcx th.hdbcx:nth-child(5), tbody.hdbcx td.hdbcx:nth-child(5) { width: 15%; }
thead.hdbcx th.hdbcx:nth-child(6), tbody.hdbcx td.hdbcx:nth-child(6) { width: 10%; }
thead.hdbcx th.hdbcx:nth-child(7), tbody.hdbcx td.hdbcx:nth-child(7) { width: 20%; }
*/
</style>