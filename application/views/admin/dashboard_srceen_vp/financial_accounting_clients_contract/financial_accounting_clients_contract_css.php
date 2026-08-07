<style>
    :root {
        --bgclient_contract: #f3f6fb;
        --cardclient_contract: #ffffff;
        --textclient_contract: #0f1b40;
        --mutedclient_contract: #6b7280;
        --lineclient_contract: #e5e7eb;
        --primaryclient_contract: #0a58ff;
        --greenclient_contract: #10b981;
        --redclient_contract: #ef4444;
        --yellowclient_contract: #f59e0b;
        --chipclient_contract: #eef2ff;
        --radiusclient_contract: 12px;
        --shadowclient_contract: 0 10px 24px rgba(16, 24, 40, 0.12);
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

    .appclient_contract {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    /* Header */
    .headerclient_contract {
        background: linear-gradient(90deg, #FFFFFF, #FFFFFF);
        padding: 0px 20px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logoclient_contract {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circleclient_contract {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-titleclient_contract {
        font-weight: 800;
        letter-spacing: 2px;
        text-align: center;
    }

    .header-titleclient_contract .main {
        font-size: 1.8rem;
    }

    .header-titleclient_contract .child {
        font-size: 20px;
    }

    .header-rightclient_contract {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    .containerclient_contract {
        flex: 1;
        display: flex;
        gap: 10px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .top-kpisclient_contract {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 12px;
    }

    .cardclient_contract {
        background: var(--cardclient_contract);
        border-radius: var(--radiusclient_contract);
        box-shadow: var(--shadowclient_contract);
    }

    .card-padclient_contract {
        padding: 16px 18px;
    }

    .card-tableclient_contract {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-cardclient_contract {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    /* KPI left block */
    .stat-gridclient_contract {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .statclient_contract {
        border: 1px solid var(--lineclient_contract);
        border-radius: 10px;
        padding: 7px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .statclient_contract .labelclient_contract {
        color: var(--mutedclient_contract);
        font-weight: 600;
        color: #002F81;
    }

    .statclient_contract .valueclient_contract {
        font-size: 26px;
        font-weight: 800;
    }

    .label-xanhclient_contract {
        background-color: #DCFDE9;
    }

    .label-hongclient_contract {
        background-color: #FFEBEB;
    }

    /* KPI donut blocks */
    .kpi-titleclient_contract {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .kpi-sumclient_contract {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        justify-content: space-between;
    }

    .kpi-sumclient_contract .pillclient_contract {
        min-width: 70px;
        height: 50px;
        line-height: 1.3;
        padding: 0px 10px;
        border-radius: 10px;
        background: var(--chipclient_contract);
        font-size: 35px;
        font-weight: 800;
        text-align: center;
        color: var(--textclient_contract);
    }

    .kpi-subclient_contract {
        color: var(--mutedclient_contract);
        font-weight: 600;
    }

    .dotclient_contract_progress {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .dotclient_contract {
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        border: 2px solid white;
        z-index: 2;
    }

    .donut-wrapclient_contract {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    /* .donutclient_contract {
        width: 150px;
        height: 150px;
        position: relative;
    } */
    .donutclient_contract {
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative;
    }

    .donut-textclient_contract {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold;
    }

    .donutclient_contract svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donutclient_contract .txtclient_contract {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
    }

    .ring-bgclient_contract {
        stroke: #E56464;
        stroke-width: 8;
        fill: transparent;
    }

    .ring-valclient_contract {
        stroke: #01C532;
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        stroke-dasharray: 0 100;
        /* will be set by JS */
    }

    /* Tables */
    .tablesclient_contract {
        display: grid;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .table-cardclient_contract .headclient_contract {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
    }

    .headclient_contract .h-titleclient_contract {
        font-weight: 800;
        font-size: 18px;
    }

    .legendclient_contract {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .label-statusclient_contract {
        width: 40px;
        height: 16px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .label-statusclient_contract.greenclient_contract {
        background: var(--greenclient_contract);
    }

    .label-statusclient_contract.redclient_contract {
        background: var(--redclient_contract);
    }

    .label-statusclient_contract.yellowclient_contract {
        background: var(--yellowclient_contract);
    }

    .text-statusclient_contract {
        font-size: 14px;
        color: var(--textclient_contract);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .dotclient_contract {
        width: 40px;
        height: 18px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 6px;
    }

    .dotclient_contract_progress.greenclient_contract {
        background: var(--greenclient_contract);
    }

    .dotclient_contract_progress.redclient_contract {
        background: var(--redclient_contract);
    }

    .dotclient_contract_progress.yellowclient_contract {
        background: var(--yellowclient_contract);
    }

    .dotclient_contract.greenclient_contract {
        background: var(--greenclient_contract);
    }

    .dotclient_contract.redclient_contract {
        background: var(--redclient_contract);
    }

    .dotclient_contract.yellowclient_contract {
        background: var(--yellowclient_contract);
    }

    table.client_contract {
        padding: 10px 10px 0px 10px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: none;
        font-size: 15px;
    }

    .table-wrapperclient_contract .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 8px;
        float: right;
        flex-direction: row-reverse;
    }

    thead.client_contract th.client_contract {
        text-align: left;
        background: #EEEEEE;
        color: #333333;
        font-weight: 800;
        padding: 12px 14px;
        /* Không có border riêng cho th */
        border-left: 1px solid var(--lineclient_contract);
        border-right: 1px solid var(--lineclient_contract);
        font-size: 20px;
        text-align: center;
    }

    tbody.client_contract td.client_contract {
        padding: 12px 14px;
        border-bottom: 1px solid var(--lineclient_contract);
        border-left: 1px solid var(--lineclient_contract);
        border-right: 1px solid var(--lineclient_contract);
        background: #fff;
        text-align: center;
        font-weight: 700;
        font-size: 20px;
    }

    tbody.client_contract tr:hover td.client_contract {
        background: #fcfcff;
    }

    tbody.client_contract tr:nth-child(even) td.client_contract {
        background: #EEEEEE;
    }

    .status-badgeclient_contract {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .chipclient_contract {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .chipclient_contract.greenclient_contract {
        background: rgba(16, 185, 129, 0.15);
        color: var(--greenclient_contract);
    }

    .chipclient_contract.redclient_contract {
        background: rgba(239, 68, 68, 0.15);
        color: var(--redclient_contract);
    }

    .chipclient_contract.yellowclient_contract {
        background: rgba(245, 158, 11, 0.18);
        color: var(--yellowclient_contract);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-kpisclient_contract {
            grid-template-columns: 1fr;
        }

        .tablesclient_contract {
            grid-template-columns: 1fr;
        }

        .titleclient_contract {
            font-size: 28px;
        }
    }

    .sidebarclient_contract {
        background: white;
        border-radius: 10px;
        width: 200px;
        display: flex;
        flex-direction: column;
    }

    .kpi-boxclient_contract {
        border-radius: 12px;
        padding: 10px;
        margin: 4px 10px 5px 10px;
        box-shadow: var(--shadowclient_contract);
        color: var(--textclient_contract);
        text-align: center;
    }

    .kpi-boxclient_contract .label {
        color: #002F81;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .kpi-boxclient_contract .value {
        font-size: 28px;
        color: #002F81;
        font-weight: 800;
    }

    .kpi-boxclient_contract.xanh {
        background: #DCFDE9;
        border: 1px solid #00691A;
    }

    .kpi-boxclient_contract.hong {
        background: #FFE9E9;
    }

    .kpi-boxclient_contract.vang {
        background: #FFF4BF;
        border: 1px solid #AF9514;
    }

    .kpi-boxclient_contract.tim {
        background: #EFE8FF;
        border: 1px solid #4F507F;
    }

    .kpi-boxclient_contract.cam {
        background: #FFE8E8;
        border: 1px solid #FF5656;
    }

    .kpi-boxclient_contract.xanh-nhat {
        background: #DCEEFF;
        border: 1px solid #002F81;
    }



    .avatarclient_contract {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
    }

    .td-progressclient_contract div {
        gap: 6px;
        font-size: 16px;
    }

    /* Timeline tiến độ trong cột Tiến độ */
    .timelineclient_contract {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        padding-left: 18px;
    }

    .stepclient_contract {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .stepclient_contract::before {
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

    .stepclient_contract:last-child::before {
        display: none;
        /* không nối sau bước cuối */
    }



    .stepclient_contract.doneclient_contract .dotclient_contract_progress {
        background: #10b981;
        /* xanh */
    }

    .stepclient_contract.pendingclient_contract .dotclient_contract_progress {
        background: #ccc;
        /* xám chờ */
    }

    .contentclient_contract {
        margin-left: 20px;
    }

    .titleclient_contract {
        font-weight: 700;
        color: #111827;
        font-size: 14px;
        line-height: 1.3;
    }

    .userclient_contract {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #047857;
        /* xanh nhẹ */
    }

    .avatar-smclient_contract {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .table-bodyclient_contract .image img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }

    .table-wrapperclient_contract {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadowclient_contract);
        overflow: hidden;
    }

    .table-wrapperclient_contract .table-body-client_contract {
        height: 100%;
        ;
    }

    .table-wrapperclient_contract table.client_contract tbody {
        flex: 1;
        min-height: 0;
        overflow: auto;
    }

    /* --- PHẦN 1: THIẾT LẬP KHUNG CHỨA BÊN NGOÀI --- */

    /* Bắt buộc container chính phải co giãn */
    .containerclient_contract {
        flex: 1;
        min-height: 0;
    }

    /* Cho khung bọc bảng co giãn và sắp xếp nội dung theo chiều dọc */
    .table-wrapperclient_contract {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }


    /* --- PHẦN 2: BIẾN TABLE THÀNH FLEX CONTAINER (Theo yêu cầu) --- */

    /* Biến thẻ table thành flex container để có thể dùng flex: 1 cho tbody */
    table.client_contract {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* Table sẽ chiếm hết không gian còn lại trong wrapper */
        min-height: 0;
    }

    /* Áp dụng flex: 1 cho tbody để nó co giãn và cuộn */
    .table-body-client_contract {
        flex: 1;
        /* ĐÂY LÀ client_contractỘC TÍNH BẠN YÊU CẦU */
        overflow-y: auto;
        /* Thêm thanh cuộn khi cần */
        display: block;
        /* Bắt buộc để overflow hoạt động */
    }


    /* --- PHẦN 3: SỬA LỖI CÁC CỘT BỊ LỆCH SAU KHI DÙNG FLEX --- */
    /* (Quan trọng) */

    /* Vì table đã là flex, ta phải "ép" thead và các hàng tr quay lại layout bảng */
    .table-wrapperclient_contract thead,
    .table-wrapperclient_contract tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Giúp các cột có chiều rộng cố định */
    }

    /* BẠN CẦN CHỈNH SỬA CHIỀU RỘNG CÁC CỘT Ở ĐÂY */
    thead.client_contract th.client_contract,
    tbody.client_contract td.client_contract {
        /* Ví dụ chiều rộng cho 7 cột */
        width: 14.2%;
    }

    /* Hoặc bạn có thể set chiều rộng cho từng cột riêng lẻ */
    /*
thead.client_contract th.client_contract:nth-child(1), tbody.client_contract td.client_contract:nth-child(1) { width: 10%; }
thead.client_contract th.client_contract:nth-child(2), tbody.client_contract td.client_contract:nth-child(2) { width: 20%; }
thead.client_contract th.client_contract:nth-child(3), tbody.client_contract td.client_contract:nth-child(3) { width: 10%; }
thead.client_contract th.client_contract:nth-child(4), tbody.client_contract td.client_contract:nth-child(4) { width: 15%; }
thead.client_contract th.client_contract:nth-child(5), tbody.client_contract td.client_contract:nth-child(5) { width: 15%; }
thead.client_contract th.client_contract:nth-child(6), tbody.client_contract td.client_contract:nth-child(6) { width: 10%; }
thead.client_contract th.client_contract:nth-child(7), tbody.client_contract td.client_contract:nth-child(7) { width: 20%; }
*/
</style>