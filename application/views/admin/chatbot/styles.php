<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f5f5;
    }

    .container {
        display: flex;
        min-height: 100vh;
        width: 100%;
    }

    /* Sidebar Styles */
    .sidebar {
        display: flex;
        flex-direction: column;
        height: 100vh;
        width: 250px;
        background-color: #ffffff;
        border-right: 1px solid #ddd;
        padding: 0;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
    }

    .sidebar-body {
        padding: 6px;
        flex: 1;
        overflow-y: auto;
    }

    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid #eee;
    }



    .sidebar-item {
        padding: 8px 5px;
        font-size: 13px;
        margin-bottom: 4px;
        border-radius: 6px;
        transition: background-color 0.2s ease;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }



    .sidebar-item .icon-circle {
        width: 24px;
        height: 24px;
        font-size: 12px;
        background-color: #f0fdf4;
        color: #4caf50;
        margin-right: 8px;
    }

    .sidebar-item span {
        font-size: 13px;
        color: #333;
        display: block;
        line-height: 1.2;
    }

    .sidebar-item:hover {
        background-color: #f3f3f3;
    }

    .sidebar-item small {
        font-size: 11px;
        color: #888;
    }

    .sidebar-item.active {
        background-color: #f0f0f0;
        border-radius: 8px;
    }

    .icon-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #e6f7e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        color: #4caf50;
    }

    .sidebar-item span {
        font-size: 14px;
        color: #333;
        flex: 1;
    }

    .sidebar-item.child {
        padding: 6px 12px;
        font-size: 12px;
        margin-left: 10px;
        margin-right: 10px;
        border-radius: 4px;
        background-color: #f9f9f9;
    }

    .sidebar-item.child:hover {
        background-color: #eeeeee;
        font-weight: normal;
    }

    .sidebar-item.child .icon-circle {
        width: 18px;
        height: 18px;
        font-size: 11px;
        margin-right: 6px;
        color: #666;
        background-color: #e9f5e9;
    }

    .sidebar-item.child span {
        font-size: 12px;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-item.child small {
        display: block;
        font-size: 10px;
        color: #999;
    }

    .view-more {
        margin-top: 20px;
        color: #666;
    }

    /* Main Content Styles */
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Header Styles */
    .header {
        background-color: white;
        padding: 20px;
        border-bottom: 1px solid #eaeaea;
    }

    .header-content {
        display: flex;
        align-items: flex-start;
    }

    .header-icon {
        width: 40px;
        height: 40px;
        background-color: #e6f7e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: #4caf50;
    }

    .header-text h1 {
        font-size: 20px;
        color: #333;
        margin-bottom: 5px;
    }

    .header-text p {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        background-color: #f0f0f0;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 13px;
        color: #555;
    }

    .tag i {
        margin-right: 5px;
    }

    /* Content Wrapper Styles */
    .content-wrapper {
        display: flex;
        flex: 1;
        padding: 20px;
        gap: 20px;
    }

    /* Input Section Styles */
    .input-section {
        /* flex: 1; */
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .input-section h2 {
        font-size: 18px;
        color: #333;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        color: #333;
        margin-bottom: 8px;
    }

    .required {
        color: #f44336;
    }

    .select-wrapper {
        position: relative;
    }

    .select-wrapper select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        appearance: none;
        background-color: white;
        font-size: 14px;
        color: #333;
    }

    .select-wrapper i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        pointer-events: none;
    }

    .button-group {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }

    .btn-clear,
    .btn-generate {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        border: none;
    }

    .btn-clear {
        background-color: white;
        color: #333;
        border: 1px solid #ddd;
    }

    .btn-generate {
        background-color: #00bfa5;
        color: white;
    }

    .btn-clear i,
    .btn-generate i {
        margin-right: 8px;
    }

    /* Output Section Styles */
    .output-section {
        flex: 1;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    .output-header {
        background-color: #00bfa5;
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
    }

    .output-icon {
        margin-right: 10px;
        font-size: 18px;
    }

    .output-header h2 {
        font-size: 18px;
        font-weight: 500;
    }

    .tabs {
        display: flex;
        border-bottom: 1px solid #eaeaea;
    }

    .tab {
        padding: 12px 20px;
        font-size: 14px;
        color: #666;
        cursor: pointer;
        position: relative;
    }

    .tab.active {
        color: #00bfa5;
    }

    .tab.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #00bfa5;
    }

    .output-history {
        flex: 1;
        align-items: center;
        justify-content: center;
        padding: 30px;
    }

    .output-content {
        flex: 1;
        align-items: center;
        justify-content: center;
        padding: 30px;
    }

    .empty-state {
        text-align: center;
        color: #aaa;
    }

    .empty-icon {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 14px;
    }

    a {
        text-decoration: none;
    }

    .disabled-button {
        background-color: #a9a9a9;
    }

    .hide {
        display: none;
    }

    .disabled-state {
        text-align: center;
        color: #aaa;
    }

    .disabled-icon {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .disabled-state p {
        font-size: 16px;
        font-weight: 500;
    }

    .loading-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-content {
        /* background-color: white; */
        padding: 20px;
        border-radius: 5px;
        text-align: center;
        /* box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3); */
    }

    .loading-content p {
        font-size: 18px;
        color: #333;
    }

    .sinusoidal-text {
        font-size: 3em;
        display: inline-block;
        position: relative;
    }

    .sinusoidal-text span {
        display: inline-block;
        animation: wave 2s infinite ease-in-out;
    }

    .sinusoidal-text span:nth-child(odd) {
        animation-delay: 0.1s;
    }

    .sinusoidal-text span:nth-child(even) {
        animation-delay: 0.2s;
    }

    @keyframes wave {
        0% {
            transform: translateY(0);
        }

        25% {
            transform: translateY(-15px);
        }

        50% {
            transform: translateY(0);
        }

        75% {
            transform: translateY(15px);
        }

        100% {
            transform: translateY(0);
        }
    }

    button:disabled {
        background-color: #ddd;
        cursor: not-allowed;
    }

    button .spinner {
        border: 3px solid #f3f3f3;
        /* màu nền của spinner */
        border-top: 3px solid #3498db;
        /* màu spinner */
        border-radius: 50%;
        width: 16px;
        height: 16px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 10px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .action-buttons {
        left: 15px;
        bottom: -45px;
        display: flex;
        gap: 10px;
    }

    .action-button {
        background-color: #f8f9fa;
        border: 1px solid rgb(102, 102, 102);
        border-radius: 24px;
        color: #3c4043;
        font-size: 14px;
        padding: 8px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .action-button:hover {
        border-color: rgb(5, 5, 5);
        background-color: rgb(243 243 243);
        box-shadow: 0 1px 1px rgba(0, 0, 0, .1);
    }

    .action-button.active {
        background-color: rgb(191 213 235);
        border: 1px solid rgb(71 153 233);
        font-weight: 500;
        color: rgb(0 50 159);
    }

    .tab-content.active {
        display: block;
    }

    .tab-content {
        display: none;
    }

    /* history */
    .activity-container {
        max-height: 300px;
        overflow: auto;
        padding: 15px 0 0 20px;
    }

    .feed-item {
        position: relative;
        padding-left: 30px;
        padding-bottom: 10px;
        border-left: 2px solid #84c529;
    }

    .activity-text {
        font-weight: bold;
    }

    .activity-time {
        color: #989898;
    }

    .activity-module {
        background: #fff2b4;
        padding: 2px 10px;
        border-radius: 3px;
        color: #656565;
        font-weight: bold;
        display: inline-block;
    }

    .text-center {
        text-align: center;
    }

    .btn-info {
        color: #fff;
        background-color: #03a9f4;
        border: 0;
    }

    .btn {
        text-transform: uppercase;
        font-size: 12px;
        outline-offset: 0;
        border: 1px solid transparent;
        transition: all .15sease-in-out;
        -o-transition: all .15s ease-in-out;
        -moz-transition: all .15s ease-in-out;
        -webkit-transition: all .15sease-in-out;
    }

    .btn {
        display: inline-block;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        border-radius: 4px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .activity-text img {
        position: absolute;
        top: -10px;
        left: -16px;
        z-index: 1;
    }

    .staff-profile-image-small {
        height: 32px;
        width: 32px;
        border-radius: 50%;
    }

    img {
        vertical-align: middle;
    }

    img {
        border: 0;
    }

    .row {
        display: -ms-flexbox;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
        margin-bottom: 25px;
    }

    .select2-default {
        min-height: 35px;
    }

    .input-section {
        width: 650px;
    }

    

    /* --- Gợi ý dạng card đẹp --- */
    .suggestion-box {
        background: #eaf5ef;
        padding: 15px;
        border-radius: 10px;
        max-width: 480px;
        margin: 15px 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        font-size: 14px;
    }

    .suggestion-box b {
        display: block;
        margin-bottom: 8px;
        color: #222;
        font-size: 15px;
    }

    /* Button gợi ý */
    .suggestion-box button {
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .suggestion-box button:hover {
        background-color: #f2fdf9;
        border-color: #00bfa5;
    }

    .suggestion-box button i {
        color: #00bfa5;
    }

    /* --- Select nhân viên đẹp hơn --- */
    select#staffSelect {
        width: 100%;
        padding: 10px 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        background-color: #fff;
        outline: none;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    /* Nút thực hiện */
    .suggestion-box .submit-button {
        background-color: #00bfa5;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 14px;
        cursor: pointer;
        margin-top: 10px;
    }

    .suggestion-box .submit-button:hover {
        background-color: #009e8a;
    }
</style>