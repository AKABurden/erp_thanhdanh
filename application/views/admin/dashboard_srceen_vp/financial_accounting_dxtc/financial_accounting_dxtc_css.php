<style>
    :root {
        --bg_dxtc: #f3f6fb;
        --card_dxtc: #ffffff;
        --text_dxtc: #0f1b40;
        --muted_dxtc: #6b7280;
        --line_dxtc: #e5e7eb;
        --primary_dxtc: #0a58ff;
        --green_dxtc: #10b981;
        --red_dxtc: #ef4444;
        --yellow_dxtc: #f59e0b;
        --chip_dxtc: #eef2ff;
        --radius_dxtc: 12px;
        --shadow_dxtc: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .app_dxtc {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .header_dxtc {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_dxtc {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_dxtc {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_dxtc {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-title_dxtc .main {
        font-size: 1.8rem;
    }

    .header-title_dxtc .child {
        font-size: 20px;
    }

    .header-right_dxtc {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .container_dxtc {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpis_dxtc {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .card_dxtc {
        background: var(--card_dxtc);
        border-radius: var(--radius_dxtc);
        box-shadow: var(--shadow_dxtc);
    }

    .card-pad_dxtc {
        padding: 16px 18px;
    }

    .card-table_dxtc {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dxtc {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-grid_dxtc {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .stat_dxtc {
        border: 1px solid var(--line_dxtc);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat_dxtc .label_dxtc {
        color: var(--muted_dxtc);
        font-weight: 600;
        color: #002F81;
    }

    .stat_dxtc .value_dxtc {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanh_dxtc {
        background-color: #DCFDE9;
    }

    .label-hong_dxtc {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-title_dxtc {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sum_dxtc {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sum_dxtc .pill_dxtc {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chip_dxtc);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--text_dxtc);
    }

    .kpi-sub_dxtc {
        color: var(--muted_dxtc);
        font-weight: 600;
    }

    .dot_dxtc_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dot_dxtc {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrap_dxtc {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donut_dxtc {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donut_dxtc {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-text_dxtc {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donut_dxtc svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut_dxtc .txt_dxtc {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bg_dxtc {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-val_dxtc {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tables_dxtc {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-card_dxtc .head_dxtc {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .head_dxtc .h-title_dxtc {
        font-weight: 800;
        font-size: 18px;
    }

    .legend_dxtc {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-status_dxtc {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-status_dxtc.green_dxtc {
        background: var(--green_dxtc);
    }

    .label-status_dxtc.red_dxtc {
        background: var(--red_dxtc);
    }

    .label-status_dxtc.yellow_dxtc {
        background: var(--yellow_dxtc);
    }

    .text-status_dxtc {
        font-size: 14px;
        color: var(--text_dxtc);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dot_dxtc {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dot_dxtc_progress.green_dxtc {
        background: var(--green_dxtc);
    }

    .dot_dxtc_progress.red_dxtc {
        background: var(--red_dxtc);
    }

    .dot_dxtc_progress.yellow_dxtc {
        background: var(--yellow_dxtc);
    }

    .dot_dxtc.green_dxtc {
        background: var(--green_dxtc);
    }

    .dot_dxtc.red_dxtc {
        background: var(--red_dxtc);
    }

    .dot_dxtc.yellow_dxtc {
        background: var(--yellow_dxtc);
    }

    table.dxtc {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapper_dxtc .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row-reverse;
    }

    thead.dxtc th.dxtc {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--line_dxtc);
        border-right: 1px solid var(--line_dxtc);
        font-size: 20px;
        text-align: center;
    }

    tbody.dxtc td.dxtc {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line_dxtc);
        border-left: 1px solid var(--line_dxtc);
        border-right: 1px solid var(--line_dxtc);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.dxtc tr:hover td.dxtc {
        background: #fcfcff;
    }

    tbody.dxtc tr:nth-child(even) td.dxtc {
        background: #EEEEEE;
    }

    .status-badge_dxtc {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chip_dxtc {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chip_dxtc.green_dxtc {
        background: rgba(16, 185, 129, 0.15);
        color: var(--green_dxtc);
    }

    .chip_dxtc.red_dxtc {
        background: rgba(239, 68, 68, 0.15);
        color: var(--red_dxtc);
    }

    .chip_dxtc.yellow_dxtc {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellow_dxtc);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpis_dxtc {
            grid-template-columns: 1fr;
        }

        .tables_dxtc {
            grid-template-columns: 1fr;
        }

        .title_dxtc {
            font-size: 28px;
        }
    }

    .sidebar_dxtc {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-box_dxtc {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadow_dxtc);
        color: var(--text_dxtc);
        text-align: center;
    }

    .kpi-box_dxtc .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-box_dxtc .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-box_dxtc.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-box_dxtc.hong {
        background: #FFE9E9;
    }

    .kpi-box_dxtc.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-box_dxtc.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-box_dxtc.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-box_dxtc.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatar_dxtc {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progress_dxtc div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timeline_dxtc {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .step_dxtc {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .step_dxtc::before {
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

    .step_dxtc:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .step_dxtc.done_dxtc .dot_dxtc_progress {
        background: #10b981;
        /* xanh */
    }

    .step_dxtc.pending_dxtc .dot_dxtc_progress {
        background: #ccc;
        /* xám chờ */
    }

    .content_dxtc {
        margin-left: 20px;
    }

    .title_dxtc {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .user_dxtc {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-sm_dxtc {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-body_dxtc .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapper_dxtc {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow_dxtc);
        overflow: hidden;
    }

    .table-wrapper_dxtc .table-body-dxtc {
        height: 100%;
        ;
    }

    .table-wrapper_dxtc table.dxtc tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .container_dxtc {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapper_dxtc {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.dxtc {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-dxtc {
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
    .table-wrapper_dxtc thead,
    .table-wrapper_dxtc tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.dxtc th.dxtc,
    tbody.dxtc td.dxtc {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    .label-text {
        border-radius: 10px;
        font-size: 20px;
        font-weight: 700;
        padding: .3em .7em .3em;
    }

    .label-success {
        background: 0 0;
        border: 1px solid #84c529;
        color: #84c529;
    }
    .label-warning {
        background: 0 0;
        border: 1px solid #ffb400;
        color: #ffb400;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.dxtc th.dxtc:nth-child(1), tbody.dxtc td.dxtc:nth-child(1) { width: 10%; }
thead.dxtc th.dxtc:nth-child(2), tbody.dxtc td.dxtc:nth-child(2) { width: 20%; }
thead.dxtc th.dxtc:nth-child(3), tbody.dxtc td.dxtc:nth-child(3) { width: 10%; }
thead.dxtc th.dxtc:nth-child(4), tbody.dxtc td.dxtc:nth-child(4) { width: 15%; }
thead.dxtc th.dxtc:nth-child(5), tbody.dxtc td.dxtc:nth-child(5) { width: 15%; }
thead.dxtc th.dxtc:nth-child(6), tbody.dxtc td.dxtc:nth-child(6) { width: 10%; }
thead.dxtc th.dxtc:nth-child(7), tbody.dxtc td.dxtc:nth-child(7) { width: 20%; }
*/
</style>