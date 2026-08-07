<style>
    :root {
        --bg_puwakh: #f3f6fb;
        --card_puwakh: #ffffff;
        --text_puwakh: #0f1b40;
        --muted_puwakh: #6b7280;
        --line_puwakh: #e5e7eb;
        --primary_puwakh: #0a58ff;
        --green_puwakh: #10b981;
        --red_puwakh: #ef4444;
        --yellow_puwakh: #f59e0b;
        --chip_puwakh: #eef2ff;
        --radius_puwakh: 12px;
        --shadow_puwakh: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_puwakh {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_puwakh {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_puwakh {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_puwakh {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_puwakh {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_puwakh .main {
        font-size: 1.8rem;
    }

    .header-title_puwakh .child {
        font-size: 20px;
    }

    .header-right_puwakh {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_puwakh {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_puwakh {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_puwakh {
        background: var(--card_puwakh);
        border-radius: var(--radius_puwakh);
        box-shadow: var(--shadow_puwakh);
    }

    .card-pad_puwakh {
        padding: 16px 18px;
    }

    .card-table_puwakh {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_puwakh {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_puwakh {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_puwakh {
        border: 1px solid var(--line_puwakh);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_puwakh .label_puwakh {
        color: var(--muted_puwakh);
        font-weight: 600;
        color: #002F81;
    }

    .stat_puwakh .value_puwakh {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_puwakh {
        background-color: #DCFDE9;
    }

    .label-hong_puwakh {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_puwakh {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_puwakh {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_puwakh .pill_puwakh {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_puwakh);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_puwakh);
    }

    .kpi-sub_puwakh {
        color: var(--muted_puwakh);
        font-weight: 600;
    }

    .dot_puwakh_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_puwakh {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_puwakh {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_puwakh {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_puwakh {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_puwakh {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_puwakh svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_puwakh .txt_puwakh {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_puwakh {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_puwakh {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_puwakh {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_puwakh .head_puwakh {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_puwakh .h-title_puwakh {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_puwakh {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_puwakh {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_puwakh.green_puwakh {
        background: var(--green_puwakh);
    }

    .label-status_puwakh.red_puwakh {
        background: var(--red_puwakh);
    }

    .label-status_puwakh.yellow_puwakh {
        background: var(--yellow_puwakh);
    }

    .text-status_puwakh {
        font-size: 14px;
        color: var(--text_puwakh);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_puwakh {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_puwakh_progress.green_puwakh {
        background: var(--green_puwakh);
    }

    .dot_puwakh_progress.red_puwakh {
        background: var(--red_puwakh);
    }

    .dot_puwakh_progress.yellow_puwakh {
        background: var(--yellow_puwakh);
    }

    .dot_puwakh.green_puwakh {
        background: var(--green_puwakh);
    }

    .dot_puwakh.red_puwakh {
        background: var(--red_puwakh);
    }

    .dot_puwakh.yellow_puwakh {
        background: var(--yellow_puwakh);
    }

    table.puwakh {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_puwakh .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row;
    }

    thead.puwakh th.puwakh {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_puwakh);
        border-right: 1px solid var(--line_puwakh);
        font-size: 20px;
        text-align: center;
    }

    tbody.puwakh td.puwakh {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_puwakh);
        border-left: 1px solid var(--line_puwakh);
        border-right: 1px solid var(--line_puwakh);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.puwakh tr:hover td.puwakh {
        background: #fcfcff;
    }

    tbody.puwakh tr:nth-child(even) td.puwakh {
        background: #EEEEEE;
    }

    .status-badge_puwakh {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_puwakh {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_puwakh.green_puwakh {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_puwakh);
    }

    .chip_puwakh.red_puwakh {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_puwakh);
    }

    .chip_puwakh.yellow_puwakh {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_puwakh);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_puwakh {
            grid-template-columns: 1fr;
        }

        .tables_puwakh {
            grid-template-columns: 1fr;
        }

        .title_puwakh {
            font-size: 28px;
        }
    }

    .sidebar_puwakh {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_puwakh {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_puwakh);
        color: var(--text_puwakh);
        text-align: center;
    }

    .kpi-box_puwakh .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_puwakh .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_puwakh.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_puwakh.hong {
        background: #FFE9E9;
    }

    .kpi-box_puwakh.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_puwakh.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_puwakh.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_puwakh.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_puwakh {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_puwakh div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_puwakh {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_puwakh {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_puwakh::before {
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

    .step_puwakh:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_puwakh.done_puwakh .dot_puwakh_progress {
        background: #10b981;
        /* xanh */
    }

    .step_puwakh.pending_puwakh .dot_puwakh_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_puwakh {
        margin-left: 20px;
    }

    .title_puwakh {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_puwakh {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_puwakh {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_puwakh .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_puwakh {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_puwakh);
        overflow: hidden;
    }

    .table-wrapper_puwakh .table-body-puwakh {
        height: 100%;
        ;
    }

    .table-wrapper_puwakh table.puwakh tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_puwakh {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_puwakh {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.puwakh {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-puwakh {
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
    .table-wrapper_puwakh thead,
    .table-wrapper_puwakh tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.puwakh th.puwakh,
    tbody.puwakh td.puwakh {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.puwakh th.puwakh:nth-child(1), tbody.puwakh td.puwakh:nth-child(1) { width: 10%; }
thead.puwakh th.puwakh:nth-child(2), tbody.puwakh td.puwakh:nth-child(2) { width: 20%; }
thead.puwakh th.puwakh:nth-child(3), tbody.puwakh td.puwakh:nth-child(3) { width: 10%; }
thead.puwakh th.puwakh:nth-child(4), tbody.puwakh td.puwakh:nth-child(4) { width: 15%; }
thead.puwakh th.puwakh:nth-child(5), tbody.puwakh td.puwakh:nth-child(5) { width: 15%; }
thead.puwakh th.puwakh:nth-child(6), tbody.puwakh td.puwakh:nth-child(6) { width: 10%; }
thead.puwakh th.puwakh:nth-child(7), tbody.puwakh td.puwakh:nth-child(7) { width: 20%; }
*/
</style>