<style>
    :root {
        --bg_puwa: #f3f6fb;
        --card_puwa: #ffffff;
        --text_puwa: #0f1b40;
        --muted_puwa: #6b7280;
        --line_puwa: #e5e7eb;
        --primary_puwa: #0a58ff;
        --green_puwa: #10b981;
        --red_puwa: #ef4444;
        --yellow_puwa: #f59e0b;
        --chip_puwa: #eef2ff;
        --radius_puwa: 12px;
        --shadow_puwa: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_puwa {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_puwa {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_puwa {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_puwa {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_puwa {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_puwa .main {
        font-size: 1.8rem;
    }

    .header-title_puwa .child {
        font-size: 20px;
    }

    .header-right_puwa {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_puwa {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_puwa {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_puwa {
        background: var(--card_puwa);
        border-radius: var(--radius_puwa);
        box-shadow: var(--shadow_puwa);
    }

    .card-pad_puwa {
        padding: 16px 18px;
    }

    .card-table_puwa {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_puwa {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_puwa {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_puwa {
        border: 1px solid var(--line_puwa);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_puwa .label_puwa {
        color: var(--muted_puwa);
        font-weight: 600;
        color: #002F81;
    }

    .stat_puwa .value_puwa {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_puwa {
        background-color: #DCFDE9;
    }

    .label-hong_puwa {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_puwa {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_puwa {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_puwa .pill_puwa {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_puwa);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_puwa);
    }

    .kpi-sub_puwa {
        color: var(--muted_puwa);
        font-weight: 600;
    }

    .dot_puwa_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_puwa {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_puwa {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_puwa {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_puwa {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_puwa {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_puwa svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_puwa .txt_puwa {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_puwa {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_puwa {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_puwa {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_puwa .head_puwa {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_puwa .h-title_puwa {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_puwa {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_puwa {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_puwa.green_puwa {
        background: var(--green_puwa);
    }

    .label-status_puwa.red_puwa {
        background: var(--red_puwa);
    }

    .label-status_puwa.yellow_puwa {
        background: var(--yellow_puwa);
    }

    .text-status_puwa {
        font-size: 14px;
        color: var(--text_puwa);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_puwa {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_puwa_progress.green_puwa {
        background: var(--green_puwa);
    }

    .dot_puwa_progress.red_puwa {
        background: var(--red_puwa);
    }

    .dot_puwa_progress.yellow_puwa {
        background: var(--yellow_puwa);
    }

    .dot_puwa.green_puwa {
        background: var(--green_puwa);
    }

    .dot_puwa.red_puwa {
        background: var(--red_puwa);
    }

    .dot_puwa.yellow_puwa {
        background: var(--yellow_puwa);
    }

    table.puwa {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_puwa .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row;
    }

    thead.puwa th.puwa {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_puwa);
        border-right: 1px solid var(--line_puwa);
        font-size: 20px;
        text-align: center;
    }

    tbody.puwa td.puwa {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_puwa);
        border-left: 1px solid var(--line_puwa);
        border-right: 1px solid var(--line_puwa);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.puwa tr:hover td.puwa {
        background: #fcfcff;
    }

    tbody.puwa tr:nth-child(even) td.puwa {
        background: #EEEEEE;
    }

    .status-badge_puwa {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_puwa {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_puwa.green_puwa {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_puwa);
    }

    .chip_puwa.red_puwa {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_puwa);
    }

    .chip_puwa.yellow_puwa {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_puwa);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_puwa {
            grid-template-columns: 1fr;
        }

        .tables_puwa {
            grid-template-columns: 1fr;
        }

        .title_puwa {
            font-size: 28px;
        }
    }

    .sidebar_puwa {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_puwa {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_puwa);
        color: var(--text_puwa);
        text-align: center;
    }

    .kpi-box_puwa .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_puwa .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_puwa.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_puwa.hong {
        background: #FFE9E9;
    }

    .kpi-box_puwa.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_puwa.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_puwa.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_puwa.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_puwa {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_puwa div {
        gap: 6px;
        font-size: 16px;
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

    .user_puwa {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_puwa {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_puwa .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_puwa {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_puwa);
        overflow: hidden;
    }

    .table-wrapper_puwa .table-body-puwa {
        height: 100%;
        ;
    }

    .table-wrapper_puwa table.puwa tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_puwa {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_puwa {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.puwa {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-puwa {
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
    .table-wrapper_puwa thead,
    .table-wrapper_puwa tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.puwa th.puwa,
    tbody.puwa td.puwa {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.puwa th.puwa:nth-child(1), tbody.puwa td.puwa:nth-child(1) { width: 10%; }
thead.puwa th.puwa:nth-child(2), tbody.puwa td.puwa:nth-child(2) { width: 20%; }
thead.puwa th.puwa:nth-child(3), tbody.puwa td.puwa:nth-child(3) { width: 10%; }
thead.puwa th.puwa:nth-child(4), tbody.puwa td.puwa:nth-child(4) { width: 15%; }
thead.puwa th.puwa:nth-child(5), tbody.puwa td.puwa:nth-child(5) { width: 15%; }
thead.puwa th.puwa:nth-child(6), tbody.puwa td.puwa:nth-child(6) { width: 10%; }
thead.puwa th.puwa:nth-child(7), tbody.puwa td.puwa:nth-child(7) { width: 20%; }
*/
</style>