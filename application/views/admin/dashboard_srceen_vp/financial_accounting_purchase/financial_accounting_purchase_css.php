<style>
    :root {
        --bg_hdm: #f3f6fb;
        --card_hdm: #ffffff;
        --text_hdm: #0f1b40;
        --muted_hdm: #6b7280;
        --line_hdm: #e5e7eb;
        --primary_hdm: #0a58ff;
        --green_hdm: #10b981;
        --red_hdm: #ef4444;
        --yellow_hdm: #f59e0b;
        --chip_hdm: #eef2ff;
        --radius_hdm: 12px;
        --shadow_hdm: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_hdm {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_hdm {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_hdm {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_hdm {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_hdm {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_hdm .main {
        font-size: 1.8rem;
    }

    .header-title_hdm .child {
        font-size: 20px;
    }

    .header-right_hdm {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_hdm {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_hdm {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_hdm {
        background: var(--card_hdm);
        border-radius: var(--radius_hdm);
        box-shadow: var(--shadow_hdm);
    }

    .card-pad_hdm {
        padding: 16px 18px;
    }

    .card-table_hdm {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_hdm {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_hdm {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_hdm {
        border: 1px solid var(--line_hdm);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_hdm .label_hdm {
        color: var(--muted_hdm);
        font-weight: 600;
        color: #002F81;
    }

    .stat_hdm .value_hdm {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_hdm {
        background-color: #DCFDE9;
    }

    .label-hong_hdm {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_hdm {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_hdm {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_hdm .pill_hdm {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_hdm);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_hdm);
    }

    .kpi-sub_hdm {
        color: var(--muted_hdm);
        font-weight: 600;
    }

    .dot_hdm_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_hdm {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_hdm {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_hdm {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_hdm {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_hdm {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_hdm svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_hdm .txt_hdm {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_hdm {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_hdm {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_hdm {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_hdm .head_hdm {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_hdm .h-title_hdm {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_hdm {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_hdm {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_hdm.green_hdm {
        background: var(--green_hdm);
    }

    .label-status_hdm.red_hdm {
        background: var(--red_hdm);
    }

    .label-status_hdm.yellow_hdm {
        background: var(--yellow_hdm);
    }

    .text-status_hdm {
        font-size: 14px;
        color: var(--text_hdm);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_hdm {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_hdm_progress.green_hdm {
        background: var(--green_hdm);
    }

    .dot_hdm_progress.red_hdm {
        background: var(--red_hdm);
    }

    .dot_hdm_progress.yellow_hdm {
        background: var(--yellow_hdm);
    }

    .dot_hdm.green_hdm {
        background: var(--green_hdm);
    }

    .dot_hdm.red_hdm {
        background: var(--red_hdm);
    }

    .dot_hdm.yellow_hdm {
        background: var(--yellow_hdm);
    }

    table.hdm {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_hdm .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row-reverse;
    }

    thead.hdm th.hdm {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_hdm);
        border-right: 1px solid var(--line_hdm);
        font-size: 20px;
        text-align: center;
    }

    tbody.hdm td.hdm {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_hdm);
        border-left: 1px solid var(--line_hdm);
        border-right: 1px solid var(--line_hdm);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.hdm tr:hover td.hdm {
        background: #fcfcff;
    }

    tbody.hdm tr:nth-child(even) td.hdm {
        background: #EEEEEE;
    }

    .status-badge_hdm {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_hdm {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_hdm.green_hdm {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_hdm);
    }

    .chip_hdm.red_hdm {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_hdm);
    }

    .chip_hdm.yellow_hdm {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_hdm);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_hdm {
            grid-template-columns: 1fr;
        }

        .tables_hdm {
            grid-template-columns: 1fr;
        }

        .title_hdm {
            font-size: 28px;
        }
    }

    .sidebar_hdm {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_hdm {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_hdm);
        color: var(--text_hdm);
        text-align: center;
    }

    .kpi-box_hdm .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_hdm .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_hdm.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_hdm.hong {
        background: #FFE9E9;
    }

    .kpi-box_hdm.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_hdm.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_hdm.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_hdm.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_hdm {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_hdm div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_hdm {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_hdm {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_hdm::before {
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

    .step_hdm:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_hdm.done_hdm .dot_hdm_progress {
        background: #10b981;
        /* xanh */
    }

    .step_hdm.pending_hdm .dot_hdm_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_hdm {
        margin-left: 20px;
    }

    .title_hdm {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_hdm {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_hdm {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_hdm .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_hdm {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_hdm);
        overflow: hidden;
    }

    .table-wrapper_hdm .table-body-hdm {
        height: 100%;
        ;
    }

    .table-wrapper_hdm table.hdm tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_hdm {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_hdm {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.hdm {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-hdm {
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
    .table-wrapper_hdm thead,
    .table-wrapper_hdm tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.hdm th.hdm,
    tbody.hdm td.hdm {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.hdm th.hdm:nth-child(1), tbody.hdm td.hdm:nth-child(1) { width: 10%; }
thead.hdm th.hdm:nth-child(2), tbody.hdm td.hdm:nth-child(2) { width: 20%; }
thead.hdm th.hdm:nth-child(3), tbody.hdm td.hdm:nth-child(3) { width: 10%; }
thead.hdm th.hdm:nth-child(4), tbody.hdm td.hdm:nth-child(4) { width: 15%; }
thead.hdm th.hdm:nth-child(5), tbody.hdm td.hdm:nth-child(5) { width: 15%; }
thead.hdm th.hdm:nth-child(6), tbody.hdm td.hdm:nth-child(6) { width: 10%; }
thead.hdm th.hdm:nth-child(7), tbody.hdm td.hdm:nth-child(7) { width: 20%; }
*/
</style>