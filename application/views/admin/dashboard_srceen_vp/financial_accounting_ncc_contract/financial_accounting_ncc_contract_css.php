<style>
    :root {
        --bgncc_contract: #f3f6fb;
        --cardncc_contract: #ffffff;
        --textncc_contract: #0f1b40;
        --mutedncc_contract: #6b7280;
        --linencc_contract: #e5e7eb;
        --primaryncc_contract: #0a58ff;
        --greenncc_contract: #10b981;
        --redncc_contract: #ef4444;
        --yellowncc_contract: #f59e0b;
        --chipncc_contract: #eef2ff;
        --radiusncc_contract: 12px;
        --shadowncc_contract: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .appncc_contract {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .headerncc_contract {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logoncc_contract {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circlencc_contract {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-titlencc_contract {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-titlencc_contract .main {
        font-size: 1.8rem;
    }

    .header-titlencc_contract .child {
        font-size: 20px;
    }

    .header-rightncc_contract {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .containerncc_contract {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpisncc_contract {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .cardncc_contract {
        background: var(--cardncc_contract);
        border-radius: var(--radiusncc_contract);
        box-shadow: var(--shadowncc_contract);
    }

    .card-padncc_contract {
        padding: 16px 18px;
    }

    .card-tablencc_contract {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-cardncc_contract {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-gridncc_contract {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .statncc_contract {
        border: 1px solid var(--linencc_contract);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .statncc_contract .labelncc_contract {
        color: var(--mutedncc_contract);
        font-weight: 600;
        color: #002F81;
    }

    .statncc_contract .valuencc_contract {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanhncc_contract {
        background-color: #DCFDE9;
    }

    .label-hongncc_contract {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-titlencc_contract {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sumncc_contract {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sumncc_contract .pillncc_contract {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chipncc_contract);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--textncc_contract);
    }

    .kpi-subncc_contract {
        color: var(--mutedncc_contract);
        font-weight: 600;
    }

    .dotncc_contract_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dotncc_contract {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrapncc_contract {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donutncc_contract {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donutncc_contract {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-textncc_contract {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donutncc_contract svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donutncc_contract .txtncc_contract {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bgncc_contract {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-valncc_contract {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tablesncc_contract {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-cardncc_contract .headncc_contract {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .headncc_contract .h-titlencc_contract {
        font-weight: 800;
        font-size: 18px;
    }

    .legendncc_contract {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-statusncc_contract {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-statusncc_contract.greenncc_contract {
        background: var(--greenncc_contract);
    }

    .label-statusncc_contract.redncc_contract {
        background: var(--redncc_contract);
    }

    .label-statusncc_contract.yellowncc_contract {
        background: var(--yellowncc_contract);
    }

    .text-statusncc_contract {
        font-size: 14px;
        color: var(--textncc_contract);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dotncc_contract {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dotncc_contract_progress.greenncc_contract {
        background: var(--greenncc_contract);
    }

    .dotncc_contract_progress.redncc_contract {
        background: var(--redncc_contract);
    }

    .dotncc_contract_progress.yellowncc_contract {
        background: var(--yellowncc_contract);
    }

    .dotncc_contract.greenncc_contract {
        background: var(--greenncc_contract);
    }

    .dotncc_contract.redncc_contract {
        background: var(--redncc_contract);
    }

    .dotncc_contract.yellowncc_contract {
        background: var(--yellowncc_contract);
    }

    table.ncc_contract {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapperncc_contract .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row-reverse;
    }

    thead.ncc_contract th.ncc_contract {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--linencc_contract);
        border-right: 1px solid var(--linencc_contract);
        font-size: 20px;
        text-align: center;
    }

    tbody.ncc_contract td.ncc_contract {
        padding: 12px 14px;
        border-bottom: 1px solid var(--linencc_contract);
        border-left: 1px solid var(--linencc_contract);
        border-right: 1px solid var(--linencc_contract);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.ncc_contract tr:hover td.ncc_contract {
        background: #fcfcff;
    }

    tbody.ncc_contract tr:nth-child(even) td.ncc_contract {
        background: #EEEEEE;
    }

    .status-badgencc_contract {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chipncc_contract {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chipncc_contract.greenncc_contract {
        background: rgba(16, 185, 129, 0.15);
        color: var(--greenncc_contract);
    }

    .chipncc_contract.redncc_contract {
        background: rgba(239, 68, 68, 0.15);
        color: var(--redncc_contract);
    }

    .chipncc_contract.yellowncc_contract {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellowncc_contract);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpisncc_contract {
            grid-template-columns: 1fr;
        }

        .tablesncc_contract {
            grid-template-columns: 1fr;
        }

        .titlencc_contract {
            font-size: 28px;
        }
    }

    .sidebarncc_contract {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-boxncc_contract {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadowncc_contract);
        color: var(--textncc_contract);
        text-align: center;
    }

    .kpi-boxncc_contract .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-boxncc_contract .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-boxncc_contract.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-boxncc_contract.hong {
        background: #FFE9E9;
    }

    .kpi-boxncc_contract.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-boxncc_contract.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-boxncc_contract.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-boxncc_contract.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatarncc_contract {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progressncc_contract div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timelinencc_contract {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .stepncc_contract {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .stepncc_contract::before {
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

    .stepncc_contract:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .stepncc_contract.donencc_contract .dotncc_contract_progress {
        background: #10b981;
        /* xanh */
    }

    .stepncc_contract.pendingncc_contract .dotncc_contract_progress {
        background: #ccc;
        /* xám chờ */
    }

    .contentncc_contract {
        margin-left: 20px;
    }

    .titlencc_contract {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .userncc_contract {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-smncc_contract {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-bodyncc_contract .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapperncc_contract {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadowncc_contract);
        overflow: hidden;
    }

    .table-wrapperncc_contract .table-body-ncc_contract {
        height: 100%;
        ;
    }

    .table-wrapperncc_contract table.ncc_contract tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .containerncc_contract {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapperncc_contract {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.ncc_contract {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-ncc_contract {
        flex: 1;
        /* ĐÂY LÀ ncc_contractỘC TÍNH BẠN YÊU CẦU */
        overflow-y: auto;
        /* Thêm thanh cuộn khi cần */
        display: block;
        /* Bắt buộc để overflow hoạt động */
    }


    /* --- PHẦN 3: SỬA LỖI CÁC CỘT BỊ LỆCH SAU KHI DÙNG FLEX --- */
    /* (Quan trọng) */

    /* Vì table đã là flex, ta phải "ép" thead và các hàng tr quay lại layout bảng */
    .table-wrapperncc_contract thead,
    .table-wrapperncc_contract tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.ncc_contract th.ncc_contract,
    tbody.ncc_contract td.ncc_contract {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.ncc_contract th.ncc_contract:nth-child(1), tbody.ncc_contract td.ncc_contract:nth-child(1) { width: 10%; }
thead.ncc_contract th.ncc_contract:nth-child(2), tbody.ncc_contract td.ncc_contract:nth-child(2) { width: 20%; }
thead.ncc_contract th.ncc_contract:nth-child(3), tbody.ncc_contract td.ncc_contract:nth-child(3) { width: 10%; }
thead.ncc_contract th.ncc_contract:nth-child(4), tbody.ncc_contract td.ncc_contract:nth-child(4) { width: 15%; }
thead.ncc_contract th.ncc_contract:nth-child(5), tbody.ncc_contract td.ncc_contract:nth-child(5) { width: 15%; }
thead.ncc_contract th.ncc_contract:nth-child(6), tbody.ncc_contract td.ncc_contract:nth-child(6) { width: 10%; }
thead.ncc_contract th.ncc_contract:nth-child(7), tbody.ncc_contract td.ncc_contract:nth-child(7) { width: 20%; }
*/
</style>