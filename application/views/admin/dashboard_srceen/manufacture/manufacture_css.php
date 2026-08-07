<style>
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

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background: linear-gradient(135deg, #349eff, #349eff);
        color: white;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .app_manufacture {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0
    }

    .container_manufacture {
        flex: 1;
        display: flex;
        gap: 20px;
        padding: 7px 20px 20px 20px;
        min-height: 0
    }

    .sidebar_manufacture {
        width: 280px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        flex-shrink: 0
    }

    .main_manufacture {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        background: linear-gradient(135deg, #0348a2, #0348a2);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        overflow: hidden
    }

    .table-body_manufacture {
        flex: 1;
        overflow-y: auto;
        padding: 10px
    }

    /* Header */
    .header_manufacture {
        background: linear-gradient(90deg, #0348a2, #0348a2);
        padding: 20px 40px;
        margin: 5px 20px 0;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .logo_manufacture {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .logo-circle_manufacture {
        width: 48px;
        height: 48px;
        font-size: 20px
    }

    .header-title_manufacture {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: 2px
    }

    .header-right_manufacture {
        text-align: right;
        font-size: 14px;
        font-weight: 500
    }

    /* Card */
    .card_manufacture {
        background: linear-gradient(135deg, #002f81, #002f81);
        border-radius: 12px;
        padding: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3)
    }

    .card_manufacture .row_manufacture {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        font-size: 19px;
        font-weight: 500
    }

    .card_manufacture .row-2_manufacture {
        background: #0348a2;
        border-radius: 10px
    }

    .card_manufacture .row_manufacture span:last-child {
        font-weight: 800;
        font-size: 25px
    }

    .progress-title_manufacture {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px
    }

    .progress-item_manufacture {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        font-weight: 500;
        font-size: 24px
    }

    .badge_manufacture {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: bold;
        color: white !important;
    }

    .badge_manufacture.green_manufacture {
        background: #10b981;
        font-size: 22px;
        width: 75px;
        text-align: center
    }

    .badge_manufacture.red_manufacture {
        background: #ef4444;
        font-size: 22px;
        width: 75px;
        text-align: center
    }

    .donut_manufacture {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        position: relative
    }

    .donut_manufacture svg {
        width: 200px;
        height: 200px;
        transform: rotate(-90deg)
    }

    .donut-text_manufacture {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 28px;
        font-weight: bold
    }

    /* Table */
    .table-header_manufacture,
    .table-row_manufacture {
        display: grid;
        /* grid-template-columns: 180px 160px 200px 125px 120px 180px 1fr; */
        grid-template-columns:
            minmax(190px, 1.9fr) minmax(180px, 1.8fr) minmax(220px, 2.3fr) minmax(100px, 1fr) minmax(100px, 1fr) minmax(100px, 1fr) minmax(150px, 1.5fr);
        gap: 20px;
        padding: 16px 24px;
        font-size: 25px;
        font-weight: 500;
        align-items: center;
    }

    .table-header_manufacture {
        font-weight: 600;
        text-align: center;
        flex-shrink: 0
    }

    .table-row_manufacture:nth-child(odd) {
        background: #002F81
    }

    .yellow_manufacture {
        color: #facc15;
        font-weight: 600
    }

    .green_manufacture {
        color: #01C532;
        font-weight: 600
    }

    .red_manufacture {
        color: #FF7679;
        font-weight: 600
    }

    .progress-bar_manufacture {
        display: flex;
        align-items: center;
        gap: 8px
    }

    .bar_manufacture {
        flex: 1;
        height: 10px;
        background: rgba(255, 255, 255, .2);
        border-radius: 6px;
        overflow: hidden
    }

    .bar-fill_manufacture {
        height: 100%;
        border-radius: 6px
    }

    .percent_manufacture {
        font-weight: bold;
        font-size: 22px;
        min-width: 40px;
        text-align: right
    }

    /* Animation */
    .table-row_manufacture {
        transition: transform .4s ease, opacity .3s ease;
        will-change: transform
    }

    .table-row_manufacture.highlight_manufacture {
        animation: flash_manufacture 1s ease forwards;
    }

    @keyframes flash_manufacture {
        0% {
            background: rgba(255, 255, 0, 0.6);
        }

        100% {
            background: transparent;
        }
    }

    .table-row_manufacture.enter_manufacture {
        opacity: 0;
        transform: translateY(-12px)
    }

    .table-row_manufacture.enter-active_manufacture {
        opacity: 1;
        transform: translateY(0)
    }

    .table-row_manufacture.fade-out_manufacture {
        opacity: 0;
        transform: translateY(16px);
        transition: transform .35s ease, opacity .3s ease
    }

    .table-body_manufacture {
        transition: opacity 0.5s ease;
    }

    .table-body_manufacture.hidden_manufacture {
        opacity: 0;
    }

    /* Header: cho phép xuống dòng */
    .table-header_manufacture>div {
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
    }

    /* Body: kiểm tra để áp dụng marquee */
    .table-row_manufacture>div {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
    }

    .table-row_manufacture>div.marquee_manufacture span {
        display: inline-block;
        padding-left: 100%;
        animation: marquee_manufacture 8s linear infinite;
        white-space: nowrap;
    }

    @keyframes marquee_manufacture {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-100%);
        }
    }
</style>