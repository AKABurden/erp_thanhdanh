<style>
    :root {
        --bg_dxnt: #f3f6fb;
        --card_dxnt: #ffffff;
        --text_dxnt: #0f1b40;
        --muted_dxnt: #6b7280;
        --line_dxnt: #e5e7eb;
        --primary_dxnt: #0a58ff;
        --green_dxnt: #10b981;
        --red_dxnt: #ef4444;
        --yellow_dxnt: #f59e0b;
        --chip_dxnt: #eef2ff;
        --radius_dxnt: 12px;
        --shadow_dxnt: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_dxnt {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_dxnt {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_dxnt {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_dxnt {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_dxnt {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_dxnt .main {
        font-size: 1.8rem;
    }

    .header-title_dxnt .child {
        font-size: 20px;
    }

    .header-right_dxnt {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_dxnt {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_dxnt {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_dxnt {
        background: var(--card_dxnt);
        border-radius: var(--radius_dxnt);
        box-shadow: var(--shadow_dxnt);
    }

    .card-pad_dxnt {
        padding: 16px 18px;
    }

    .card-table_dxnt {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dxnt {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_dxnt {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_dxnt {
        border: 1px solid var(--line_dxnt);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_dxnt .label_dxnt {
        color: var(--muted_dxnt);
        font-weight: 600;
        color: #002F81;
    }

    .stat_dxnt .value_dxnt {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_dxnt {
        background-color: #DCFDE9;
    }

    .label-hong_dxnt {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_dxnt {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_dxnt {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_dxnt .pill_dxnt {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_dxnt);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_dxnt);
    }

    .kpi-sub_dxnt {
        color: var(--muted_dxnt);
        font-weight: 600;
    }

    .dot_dxnt_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_dxnt {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_dxnt {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_dxnt {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_dxnt {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_dxnt {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_dxnt svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_dxnt .txt_dxnt {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_dxnt {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_dxnt {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_dxnt {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dxnt .head_dxnt {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_dxnt .h-title_dxnt {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_dxnt {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_dxnt {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_dxnt.green_dxnt {
        background: var(--green_dxnt);
    }

    .label-status_dxnt.red_dxnt {
        background: var(--red_dxnt);
    }

    .label-status_dxnt.yellow_dxnt {
        background: var(--yellow_dxnt);
    }

    .text-status_dxnt {
        font-size: 14px;
        color: var(--text_dxnt);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_dxnt {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_dxnt_progress.green_dxnt {
        background: var(--green_dxnt);
    }

    .dot_dxnt_progress.red_dxnt {
        background: var(--red_dxnt);
    }

    .dot_dxnt_progress.yellow_dxnt {
        background: var(--yellow_dxnt);
    }

    .dot_dxnt.green_dxnt {
        background: var(--green_dxnt);
    }

    .dot_dxnt.red_dxnt {
        background: var(--red_dxnt);
    }

    .dot_dxnt.yellow_dxnt {
        background: var(--yellow_dxnt);
    }

    table.dxnt {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_dxnt .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row-reverse;
    }

    thead.dxnt th.dxnt {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_dxnt);
        border-right: 1px solid var(--line_dxnt);
        font-size: 20px;
        text-align: center;
    }

    tbody.dxnt td.dxnt {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_dxnt);
        border-left: 1px solid var(--line_dxnt);
        border-right: 1px solid var(--line_dxnt);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.dxnt tr:hover td.dxnt {
        background: #fcfcff;
    }

    tbody.dxnt tr:nth-child(even) td.dxnt {
        background: #EEEEEE;
    }

    .status-badge_dxnt {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_dxnt {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_dxnt.green_dxnt {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_dxnt);
    }

    .chip_dxnt.red_dxnt {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_dxnt);
    }

    .chip_dxnt.yellow_dxnt {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_dxnt);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_dxnt {
            grid-template-columns: 1fr;
        }

        .tables_dxnt {
            grid-template-columns: 1fr;
        }

        .title_dxnt {
            font-size: 28px;
        }
    }

    .sidebar_dxnt {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_dxnt {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_dxnt);
        color: var(--text_dxnt);
        text-align: center;
    }

    .kpi-box_dxnt .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_dxnt .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_dxnt.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_dxnt.hong {
        background: #FFE9E9;
    }

    .kpi-box_dxnt.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_dxnt.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_dxnt.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_dxnt.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_dxnt {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_dxnt div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_dxnt {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_dxnt {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_dxnt::before {
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

    .step_dxnt:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_dxnt.done_dxnt .dot_dxnt_progress {
        background: #10b981;
        /* xanh */
    }

    .step_dxnt.pending_dxnt .dot_dxnt_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_dxnt {
        margin-left: 20px;
    }

    .title_dxnt {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_dxnt {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_dxnt {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_dxnt .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_dxnt {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_dxnt);
        overflow: hidden;
    }

    .table-wrapper_dxnt .table-body-dxnt {
        height: 100%;
        ;
    }

    .table-wrapper_dxnt table.dxnt tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_dxnt {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_dxnt {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.dxnt {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-dxnt {
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
    .table-wrapper_dxnt thead,
    .table-wrapper_dxnt tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.dxnt th.dxnt,
    tbody.dxnt td.dxnt {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.dxnt th.dxnt:nth-child(1), tbody.dxnt td.dxnt:nth-child(1) { width: 10%; }
thead.dxnt th.dxnt:nth-child(2), tbody.dxnt td.dxnt:nth-child(2) { width: 20%; }
thead.dxnt th.dxnt:nth-child(3), tbody.dxnt td.dxnt:nth-child(3) { width: 10%; }
thead.dxnt th.dxnt:nth-child(4), tbody.dxnt td.dxnt:nth-child(4) { width: 15%; }
thead.dxnt th.dxnt:nth-child(5), tbody.dxnt td.dxnt:nth-child(5) { width: 15%; }
thead.dxnt th.dxnt:nth-child(6), tbody.dxnt td.dxnt:nth-child(6) { width: 10%; }
thead.dxnt th.dxnt:nth-child(7), tbody.dxnt td.dxnt:nth-child(7) { width: 20%; }
*/
</style>