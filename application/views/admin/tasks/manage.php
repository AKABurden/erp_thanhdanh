<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .progressbar li:not(.initli) {
        width: 110px !important;
    }

    .table-tasks tbody tr:nth-child(1) td:nth-child(5) .dropdown-menu-right {
        transform: translate3d(-29px, 241px, 0px) !important;
    }

    .table-tasks tbody tr:nth-child(2) td:nth-child(5) .dropdown-menu-right {
        transform: translate3d(-29px, 241px, 0px) !important;
    }

    .table-tasks tbody tr:nth-child(3) td:nth-child(5) .dropdown-menu-right {
        transform: translate3d(-29px, 241px, 0px) !important;
    }

    /*.table-tasks tbody tr:nth-child(1) td:nth-child(9) .dropdown-menu-right {*/
    /*    transform: translate3d(-29px, 133px, 0px)!important;*/
    /*}*/
    /*.table-tasks tbody tr:nth-child(2) td:nth-child(9) .dropdown-menu-right {*/
    /*    transform: translate3d(-29px, 133px, 0px)!important;*/
    /*}*/
    /*.table-tasks tbody tr:nth-child(3) td:nth-child(9) .dropdown-menu-right {*/
    /*    transform: translate3d(-29px, 133px, 0px)!important;*/
    /*}*/

    .table-tasks tbody tr td:nth-child(9) {
        min-width: 150px;
    }

    .lableMinus {
        font-size: 11px;
        padding-top: 2px;
        padding-bottom: 2px;
        margin-top: 5px;
        display: inline-grid;
    }

    .font-10 {
        font-size: 10px !important;
    }

    .kan-ban-col {
        width: 24%;
    }

    .div-tasks .buttons-collection {
        display: none;
    }

    /* Giao diện Mobile: Tối ưu Grid/Table/Button Layout thành giống App */
    @media (max-width: 768px) {
        /* Tối ưu Thanh Công cụ (Buttons) */
        .panel-body._buttons {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            padding: 15px !important;
        }
        .panel-body._buttons .H_title {
            margin-bottom: 20px;
            text-align: center;
            display: block;
        }
        .panel-body._buttons .pull-right {
            float: none !important;
            width: 100% !important;
            margin: 0 !important;
            display: flex;
            flex-direction: column;
        }
        .panel-body._buttons .btn {
            width: 100% !important;
            margin: 0 0 12px 0 !important;
            padding: 12px !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            text-align: center;
            float: none !important;
            display: flex !important;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        
        /* Giao diện Bộ lọc (Filters) */
        .row > .col-md-2, .row > .col-md-3 {
            margin-bottom: 15px !important;
        }
        
        /* Cải thiện ô nhập liệu, combobox chống zoom iOS (chuẩn 16px) */
        .form-control, .bootstrap-select .dropdown-toggle {
            height: 44px !important;
            border-radius: 8px !important;
            font-size: 16px !important; 
        }
        .form-group label, .control-label {
            font-size: 14px !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
            color: #374151 !important;
        }
        
        /* Kéo vuốt Tab trạng thái mượt mà kiểu Chip (Pill) */
        .horizontal-scrollable-tabs {
            margin-bottom: 25px !important;
        }
        .scroller-left, .scroller-right {
            display: none !important; /* Bỏ mũi tên rườm rà trên đt */
        }
        .horizontal-tabs .nav-tabs-horizontal {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            padding-bottom: 10px !important; /* Dành chỗ cho scrollbar */
        }
        .horizontal-tabs .nav-tabs-horizontal li {
            white-space: nowrap !important;
        }
        .horizontal-tabs .nav-tabs-horizontal li a {
            padding: 10px 18px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            border-radius: 30px !important;
            margin-right: 10px !important;
            background: #f3f4f6 !important;
            border: none !important;
            color: #4b5563 !important;
        }
        .horizontal-tabs .nav-tabs-horizontal li.active a {
            background: #3b82f6 !important;
            color: #fff !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3) !important;
        }
        
        /* CHỈNH TRỰC TIẾP DATATABLE TRÊN MOBILE */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            float: none !important;
            text-align: center !important;
            margin-top: 15px !important;
            margin-bottom: 15px !important;
            width: 100% !important;
        }
        .dataTables_wrapper .dataTables_filter label {
            width: 100% !important;
            text-align: left !important;
            display: block !important;
            font-weight: 600 !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            width: 100% !important;
            margin-left: 0 !important;
            margin-top: 8px !important;
            display: block !important;
            height: 44px !important;
            border-radius: 8px !important;
            font-size: 16px !important;
        }
        /* Phân trang (Pagination) lớn hơn, dễ bấm */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 14px !important;
            margin-bottom: 5px !important;
            display: inline-block !important;
            border-radius: 6px !important;
            font-size: 15px !important;
        }
        .dataTables_wrapper .dt-buttons {
            float: none !important;
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: center !important;
            margin-bottom: 15px !important;
            gap: 8px !important;
        }
        .dataTables_wrapper .dt-buttons .btn {
            margin: 0 !important;
            flex: 1 !important;
        }
        
        /* ĐẬP ĐI XÂY LẠI BẢNG (DATA TABLE) THÀNH DẠNG CARD TRÊN MOBILE */
        .table-responsive, .dataTables_scrollBody {
            border: none !important;
            overflow-x: hidden !important; /* BỎ TRƯỢT NGANG */
            width: 100% !important;
            box-shadow: none !important;
        }
        
        /* Ẩn dải tiêu đề ngang truyền thống đi */
        .table-tasks thead {
            display: none !important;
        }
        
        .table-tasks {
            width: 100% !important;
            min-width: 100% !important; /* Hủy mốc chặn 800px vì ko vuốt ngang nữa */
            display: block !important;
        }
        
        .table-tasks tbody {
            display: block !important;
            width: 100% !important;
        }
        
        /* Biến 1 Ròng (Tr) thành 1 Thẻ Card (Box) với dải dọc Flexbox */
        .table-tasks > tbody > tr {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            margin-bottom: 18px !important;
            background-color: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            padding: 12px 15px !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important;
            position: relative !important;
        }
        
        /* Biến các Cột (Td) thành các dòng nhỏ bên trong Card, bẻ chữ tự do */
        .table-tasks > tbody > tr:not(.child) > td {
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            align-items: flex-start !important;
            border: none !important;
            border-bottom: 1px dashed #f3f4f6 !important;
            padding: 10px 0 !important;
            text-align: left !important;
            font-size: 14px !important;
            white-space: normal !important; /* Cho rớt dòng thay vì kéo dài */
            word-break: break-word !important;
            min-height: auto !important;
        }
        
        .table-tasks > tbody > tr:not(.child) > td:last-child {
            border-bottom: none !important;
            align-items: center !important; /* Nút tuỳ chọn đưa ra giữa hoặc đẹp */
        }
        
        /* Phóng to một số link, nhãn chính nổi bật hơn trong Card */
        .table-tasks > tbody > tr > td > a {
            font-weight: 600 !important;
            color: #2563eb !important;
            font-size: 15px !important;
        }
        
        /* Định dạng các Nhãn Tiêu Đề ảo trước mỗi ô dữ liệu, thay cho thead bị ẩn */
        .table-tasks > tbody > tr:not(.child) > td::before {
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            margin-bottom: 4px !important;
            display: block !important;
            letter-spacing: 0.5px !important;
        }
        
        /* Gán cứng tên Nhãn cho từng Cột Dữ Liệu */
        .table-tasks > tbody > tr:not(.child) > td:nth-child(2)::before { content: "Chỉ mục / Bấm Mở Chi Tiết"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(3)::before { content: "Liên quan đến"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(4)::before { content: "Mã công việc"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(5)::before { content: "Tên công việc / Chủ đề"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(6)::before { content: "Loại phiếu"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(7)::before { content: "Mã phiếu"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(8)::before { content: "Ngày bắt đầu"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(9)::before { content: "Hạn chót"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(10)::before { content: "Người giao việc"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(11)::before { content: "Người phân công"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(12)::before { content: "Trạng thái công việc"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(13)::before { content: "Kết quả"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(14)::before { content: "Độ ưu tiên"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(15)::before { content: "STT Ưu tiên"; }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(16)::before { content: "Báo cáo sự cố"; }
        
        /* NÚT BẤM "MỞ XEM QUY TRÌNH" (Thực chất là cột đầu tiên chứa dấu Caret mũi tên) */
        .table-tasks > tbody > tr:not(.child) > td:nth-child(1) {
            border-bottom: none !important;
            padding: 0 !important;
            position: static !important;
            order: 99 !important; /* Đẩy xuống vách đáy của thẻ Card */
            width: 100% !important;
            margin-top: 15px !important;
            display: block !important;
        }
        
        .table-tasks > tbody > tr:not(.child) > td:nth-child(1)::before { content: none !important; }
        
        /* Giả lập Mũi tên thành Nút bấm khổng lồ cho Mobile dễ chạm */
        .table-tasks > tbody > tr:not(.child) > td:nth-child(1) > a {
            display: block !important;
            width: 100% !important;
            padding: 12px !important;
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border-radius: 8px !important;
            text-align: center !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            border: 1px solid #bfdbfe !important;
            text-decoration: none !important;
        }
        
        .table-tasks > tbody > tr:not(.child) > td:nth-child(1) > a.fa-caret-right::after { 
            content: " Mở Xem Quy Trình" !important; 
            font-family: 'Inter', 'Segoe UI', sans-serif !important; 
        }
        .table-tasks > tbody > tr:not(.child) > td:nth-child(1) > a.fa-caret-down::after { 
            content: " Đóng Quy Trình" !important; 
            font-family: 'Inter', 'Segoe UI', sans-serif !important; 
        }
        
        /* Fix lỗi Menu tuỳ chọn bị nát */
        .table-tasks > tbody > tr > td .dropdown-menu {
            position: absolute !important;   
            transform: none !important;
            top: auto !important;
            bottom: 100% !important; /* Hiển thị ngoi lên trên nút */
            left: 0 !important;
            z-index: 10000 !important;
        }
        
        /* Hiệu ứng của thẻ Row-Child khi mở (Nội dung chi tiết lồng) */
        .table-tasks > tbody > tr.shown + tr.child {
            margin-top: -20px !important;
            background-color: #fafbfd !important;
            border-top: none !important;
            border-radius: 0 0 12px 12px !important;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.03) !important;
            padding: 15px 10px !important;
        }
        
        /* ----------------------------------------------------- */
        /* SỬA LỖI NÁT GIAO DIỆN "QUY TRÌNH" BÊN TRONG CHI TIẾT  */
        /* ----------------------------------------------------- */
        .table-tasks > tbody > tr.shown + tr.child > td {
            max-width: calc(100vw - 30px) !important;
            overflow: hidden !important;
            display: block !important;
            border: none !important;
            padding: 0 !important;
        }
        
        /* ================================================================ */
        /* TIMELINE QUY TRÌNH DỌC TRÊN MOBILE - PHIÊN BẢN HOÀN CHỈNH     */
        /* ================================================================ */
        
        /* BƯỚC 1: Đặt lại container thành dọc */
        .tnh-timeline,
        .tnh-timeline-sm {
            display: flex !important;
            flex-direction: column !important;   /* <-- THEN đứng dọc */
            align-items: stretch !important;
            justify-content: flex-start !important;
            list-style-type: none !important;
            padding: 8px 0 8px 0 !important;
            margin: 0 !important;
            width: 100% !important;
            overflow: visible !important;
        }
        
        /* BƯỚC 2: Mỗi bước quy trình (li) trở thành 1 hàng ngang [chấm | nội dung] */
        .tnh-li,
        .tnh-li-sm {
            display: flex !important;
            flex-direction: row !important;      /* TẠM: chấm BÊN TRÁI, nội dung BÊN PHẢI */
            align-items: flex-start !important;
            justify-content: flex-start !important;
            float: none !important;
            width: 100% !important;
            min-width: 100% !important;
            position: relative !important;
            padding: 0 0 20px 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }
        
        /* Ẩn đường sau item cuối */
        .tnh-li:last-child,
        .tnh-li-sm:last-child {
            padding-bottom: 6px !important;
        }
        
        /* BƯỚC 3: Phần chấm + đường dọc nằm bên trái */
        .tnh-status,
        .tnh-status-sm {
            /* Gỡ bỏ hoàn toàn thiết kế ngang */
            border-top: none !important;
            
            /* Cột trái: chấm bi + đường dọc */
            flex: 0 0 36px !important;
            min-width: 36px !important;
            width: 36px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: flex-start !important;
            position: relative !important;
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            background: transparent !important;
        }
        
        /* BƯỚC 4: Chấm bi (::before) */
        .tnh-status::before,
        .tnh-status-sm::before {
            content: "" !important;
            flex-shrink: 0 !important;
            width: 16px !important;
            height: 16px !important;
            border-radius: 50% !important;
            background-color: #d1d5db !important;  /* xám mặc định */
            border: 2px solid #adb5bd !important;
            position: relative !important;           /* không dùng absolute nữa */
            top: 4px !important;
            left: auto !important;
            margin: 0 !important;
            z-index: 1 !important;
            display: block !important;
        }
        
        /* BƯỚC 5: Đường thẳng dọc nối các bước (::after trên .tnh-status) */
        .tnh-status::after,
        .tnh-status-sm::after {
            content: "" !important;
            display: block !important;
            width: 2px !important;
            flex: 1 1 auto !important;
            background-color: #d1d5db !important;
            margin-top: 4px !important;
            min-height: 30px !important;
        }
        
        /* Ẩn đường kẻ dọc sau bước cuối cùng */
        .tnh-li:last-child .tnh-status::after,
        .tnh-li-sm:last-child .tnh-status-sm::after {
            display: none !important;
        }
        
        /* BƯỚC 6: Màu theo trạng thái - Chấm bi */
        .tnh-li.tnh-complete .tnh-status::before,
        .tnh-li-sm.tnh-complete-sm .tnh-status-sm::before {
            background-color: #2680c8 !important;
            border-color: #2680c8 !important;
        }
        .tnh-li.tnh-complete .tnh-status::after,
        .tnh-li-sm.tnh-complete-sm .tnh-status-sm::after {
            background-color: #2680c8 !important;
        }
        .tnh-li-sm.tnh-success-sm .tnh-status-sm::before {
            background-color: #84c529 !important;
            border-color: #84c529 !important;
        }
        .tnh-li-sm.tnh-success-sm .tnh-status-sm::after {
            background-color: #84c529 !important;
        }
        .tnh-li-sm.tnh-danger-sm .tnh-status-sm::before {
            background-color: #ff6f00 !important;
            border-color: #ff6f00 !important;
        }
        .tnh-li-sm.tnh-danger-sm .tnh-status-sm::after {
            background-color: #ff6f00 !important;
        }
        
        /* BƯỚC 7: Tiêu đề h4 trong .tnh-status */
        .tnh-status h4,
        .tnh-status-sm h4 {
            display: none !important;  /* h4 trong status (chấm) được ẩn; nội dung ở timestamp */
        }
        
        /* BƯỚC 8: Phần nội dung (timestamp) nằm bên phải chấm bi */
        .tnh-timestamp,
        .tnh-timestamp-sm {
            flex: 1 1 auto !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            justify-content: flex-start !important;
            padding: 0 0 0 10px !important;   /* lề trái cách chấm bi */
            margin: 0 !important;
            text-align: left !important;
            word-break: break-word !important;
            width: auto !important;
        }
        
        /* Tiêu đề bước quy trình trong timestamp */
        .tnh-timestamp h4,
        .tnh-timestamp-sm h4 {
            font-size: 14px !important;
            font-weight: 600 !important;
            margin: 2px 0 6px 0 !important;
            color: #1f2937 !important;
            white-space: normal !important;
            word-break: break-word !important;
            text-align: left !important;
            line-height: 1.4 !important;
        }
        .tnh-li.tnh-complete .tnh-timestamp h4,
        .tnh-li-sm.tnh-complete-sm .tnh-timestamp-sm h4 {
            color: #2680c8 !important;
        }
        .tnh-li-sm.tnh-success-sm .tnh-timestamp-sm h4 {
            color: #84c529 !important;
        }
        .tnh-li-sm.tnh-danger-sm .tnh-timestamp-sm h4 {
            color: #ff6f00 !important;
        }
        
        /* Nội dung phụ (thông tin người duyệt, ngày tháng...) trong timestamp */
        .tnh-timestamp small,
        .tnh-timestamp-sm small,
        .tnh-timestamp span,
        .tnh-timestamp-sm span,
        .tnh-timestamp p,
        .tnh-timestamp-sm p {
            font-size: 12px !important;
            color: #6b7280 !important;
            line-height: 1.5 !important;
            display: block !important;
            margin-bottom: 4px !important;
        }
        
        /* Nút bấm (duyệt, từ chối...) trong timestamp */
        .tnh-timestamp .btn,
        .tnh-timestamp-sm .btn,
        .tnh-timestamp button,
        .tnh-timestamp-sm button {
            margin-top: 6px !important;
            margin-bottom: 4px !important;
            font-size: 13px !important;
            padding: 6px 12px !important;
            border-radius: 6px !important;
            width: 100% !important;
        }
        
        /* Fix lỗi nội dung HTML tuỳ chỉnh trong Quy Trình bị tràn */
        .table-tasks > tbody > tr.shown + tr.child > td div {
            max-width: 100% !important;
            overflow-x: auto !important;
        }
        
        /* ----------------------------------------------------- */
        /* ĐẬP BẢNG QUY TRÌNH LỒNG (NESTED TABLE) THÀNH HÀNG DỌC  */
        /* ----------------------------------------------------- */
        .table-tasks > tbody > tr.shown + tr.child > td table {
            display: block !important;
            width: 100% !important;
            border: none !important;
        }
        
        .table-tasks > tbody > tr.shown + tr.child > td table thead {
            display: none !important; /* Cắt bỏ dải tiêu đề ngang truyền thống */
        }
        
        .table-tasks > tbody > tr.shown + tr.child > td table tbody {
            display: block !important;
            width: 100% !important;
        }
        
        /* Trở thành các thẻ con (Sub-cards) dọc bên trong Quy Trình */
        .table-tasks > tbody > tr.shown + tr.child > td table tr {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            margin-bottom: 12px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            padding: 10px !important;
            background: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        }
        
        /* Từng Ô vuông dữ liệu trở thành 1 dòng thông tin rớt dọc */
        .table-tasks > tbody > tr.shown + tr.child > td table td {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: flex-start !important;
            text-align: left !important;
            border: none !important;
            border-bottom: 1px dashed #f3f4f6 !important;
            padding: 8px 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        
        .table-tasks > tbody > tr.shown + tr.child > td table td:last-child {
            border-bottom: none !important;
        }
        
        /* Điều chỉnh lại các form biểu mẫu (nếu Quy trình có Dropdown, Input) thả lỏng 100% */
        .table-tasks > tbody > tr.shown + tr.child > td table td input,
        .table-tasks > tbody > tr.shown + tr.child > td table td select,
        .table-tasks > tbody > tr.shown + tr.child > td table td .bootstrap-select {
            width: 100% !important;
            max-width: 100% !important;
        }
        
        /* Nếu có hình ảnh hoặc nút lồng bên trong, cho phép ép lại giới hạn dọc */
        .table-tasks > tbody > tr.shown + tr.child > td table td img {
            max-width: 100% !important;
            height: auto !important;
        }
        
        /* Giao diện Kanban */
        .kan-ban-col {
            width: 100% !important;
            min-width: 100% !important;
            margin-bottom: 20px !important;
        }
    }
</style>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?php if (!$this->input->get('project_id')) {
                                echo admin_url('tasks/switch_kanban/' . $switch_kanban);
                            } else {
                                echo admin_url('projects/view/' . $this->input->get('project_id') . '?group=project_tasks');
                            }; ?>" class="btn btn-info pull-left H_action_button <?= $switch_kanban == 1 ? 'h_switch' : '' ?>">
                    <?php if ($switch_kanban == 1) {
                        echo _l('switch_to_list_view');
                    } else {
                        echo _l('leads_switch_to_kanban');
                    }; ?>
                </a>
            </div>
            <?php if (has_permission('tasks', '', 'create')) { ?>
                <div class="pull-right mright5 H_border mleft5">
                    <a href="#" onclick="new_task(<?php if ($this->input->get('project_id')) {
                                                        echo "'" . admin_url('tasks/task?rel_id=' . $this->input->get('project_id') . '&rel_type=project') . "'";
                                                    } ?>); return false;" class="btn btn-info pull-left new H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?>
                    </a>
                    <a href="#" onclick="exportExcel(); return false;" class="btn btn-info pull-left new H_action_button mleft5">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        Xuất Excel
                    </a>
                    <a href="#" onclick="exportExcelTasks(); return false;" class="btn btn-info pull-left new H_action_button mleft5">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        Xuất Excel phân công
                    </a>
                </div>
            <?php } ?>
            <?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
            <?php } else { ?>
                <div class="">
                    <?php $this->load->view('admin/tasks/tasks_filter_by', array('view_table_name' => '.table-tasks')); ?>
                    <?php if (is_admin()) { ?>
                        <a href="<?php echo admin_url('tasks/detailed_overview'); ?>" class="btn btn-success pull-right mright5"><?php echo _l('detailed_overview'); ?></a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                <div class="col-md-3 hide">
                    <?php echo render_select('departments_task', !empty($departments) ? $departments : [], ['departmentid', 'name'], 'departments', '', ['multiple' => true, 'onchange' => 'tasks_kanban()', 'data-actions-box' => true], [], '', '', false) ?>
                </div>
                <div class="col-md-3">
                    <?php echo render_select('room_task', !empty($room) ? $room : [], ['id', 'name', 'code'], 'Phòng', '', ['multiple' => true, 'onchange' => 'tasks_kanban()', 'data-actions-box' => true], [], '', '', false) ?>
                </div>
                <div class="col-md-3">
                    <?php echo render_select('staff_task', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Nhân viên', '', ['multiple' => true, 'onchange' => 'tasks_kanban()', 'data-actions-box' => true], [], '', '', false) ?>
                </div>
                <div class="col-md-3">
                    <div class="mtop25" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
                        <?php echo render_input('search', '', '', 'search', array('data-name' => 'search', 'onkeyup' => 'tasks_kanban();', 'placeholder' => _l('search_tasks')), array(), 'no-margin') ?>
                    </div>
                </div>
                <div class="clearfix"></div>

            <?php } ?>
            <div class="panel_s">
                <div class="panel-body div-tasks">
                    <?php
                    if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                        <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                            <div class="row">
                                <div id="kanban-params">
                                    <?php echo form_hidden('project_id', $this->input->get('project_id')); ?>
                                </div>
                                <div class="container-fluid">
                                    <div id="kan-ban"></div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <!--						--><?php //$this->load->view('admin/tasks/_summary', array('table' => '.table-tasks')); 
                                                        ?>
                        <div class="row">
                            <div class="col-md-2 hide">
                                <?php echo render_select('departments_task', !empty($departments) ? $departments : [], ['departmentid', 'name'], 'departments', '', ['multiple' => true]) ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_select('room_task', !empty($room) ? $room : [], ['id', 'name', 'code'], 'Phòng', '', ['multiple' => true]) ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_select('staff_task', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người được phân công', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false) ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_select('staff_follower', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người được phân công theo dõi', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false) ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_select('staff_task_create', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người giao việc', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false) ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('date_start', 'Ngày bắt đầu từ') ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('date_end', 'Ngày bắt đầu đến') ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-2">
                                <?php echo render_date_input('date_start_end', 'Ngày hoàn thành từ') ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('date_end_end', 'Ngày hoàn thành đến') ?>
                            </div>
                            <div class="col-md-2">
                                <?= lang('15. Loại phiếu yêu cầu', 'category_recommended_id') ?>
                                <select name="category_recommended_id" data-none-selected-text="Loại phiếu yêu cầu" data-live-search="true" id="category_recommended_id" class="form-control selectpicker category_recommended_id">
                                    <option value=""></option>
                                    <?php if (!empty($categoryRecommended)) : ?>
                                        <?php foreach ($categoryRecommended as $key => $value) : ?>
                                            <option <?= !empty($object->category_recommended_id) && $object->category_recommended_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <?= lang('Phiếu yêu cầu', 'suggest_id') ?>
                                <select class="form-control suggest_id selectpicker" name="suggest_id" id="suggest_id" data-live-search="true" data-none-selected-text="<?= _l('dropdown_non_selected_tex') ?>">
                                    <option></option>
                                    <?php if (!empty($dtSuggest)) { ?>
                                        <?php foreach ($dtSuggest as $key => $value) { ?>
                                            <option data-subtext="<?= $value['staff_suggest_name'] ?>" <?= ($value['id'] == $valueSelected) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="category_tasks_search">
                                    <label for="category_tasks_search" class="control-label">Mã công việc</label>
                                    <select id="category_tasks_search" name="category_tasks_search" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                        <option></option>
                                        <?php if (!empty($category_tasks)) {
                                            foreach ($category_tasks as $key => $v) { ?>
                                                <option value="<?= $v['id'] ?>" data-subtext="<?= $v['content'] ?>" data-departments="<?= $v['departments'] ?>"><?= $v['code'] ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="type_tasks_search">
                                    <label for="type_tasks_search" class="control-label">Loại công việc</label>
                                    <select id="type_tasks_search" name="type_tasks_search" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                        <option></option>
                                        <option value="1">Công việc đột xuất</option>
                                        <option value="2">Công việc thường xuyên</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="procedure_tasks">
                                    <label for="procedure_tasks" class="control-label">Quy trình cần duyệt nhanh</label>
                                    <select id="procedure_tasks" name="procedure_tasks" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div><a href="javacript:void(0)" onclick="approve_all()" class="btn btn-info pull-right mright5">Duyệt nhanh quy trình</a></div> -->
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="task_id_search">
                                    <label for="task_id_search" class="control-label">Số phiếu công việc</label>
                                    <select id="task_id_search" name="task_id_search" class="ajax-search" data-width="100%" data-none-selected-text="Chọn phiếu..." data-live-search="true">
                                        <?php if (!empty($taskid)) { ?>
                                            <option value="<?php echo $taskid; ?>" selected><?php echo $taskid; ?></option>
                                        <?php } else { ?>
                                        <option value=""></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-tasks"><?php echo _l('bulk_actions'); ?></a>
                        <div class="btn-group mbot10" style="width: 100%;">
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left disabled" style="display: block;">
                                    <i class="fa fa-angle-left"></i>
                                </div>
                                <div class="scroller scroller-right arrow-right" style="display: block;">
                                    <i class="fa fa-angle-right"></i>
                                </div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                        <li class="active">
                                            <a class="H_filter" data-id="">
                                                <?= _l('cong_all') ?> <b class="filter_all"></b>
                                            </a>
                                        </li>
                                        <?php if (!empty($task_statuses)) { ?>
                                            <?php foreach ($task_statuses as $key => $value) { ?>
                                                <li class="<?= (($value['id'] == 2 || $value['id'] == 3) ? 'hide' : '') ?>">
                                                    <a class="H_filter" data-id="<?= $value['id'] ?>">
                                                        <?= $value['name'] ?> <b class="filter_<?= $value['id'] ?>"></b>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                        <li class="">
                                            <a class="H_filter" data-id="2,3">
                                                Đang Tạm Dừng(BC Không Phù Hợp) <b class="filter_2_3"></b>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php $this->load->view('admin/tasks/_table', array('bulk_actions' => true)); ?>
                        <?php $this->load->view('admin/tasks/_bulk_actions'); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="data_inspection_criteria_all"></div>
<?php init_tail(); ?>
<script>
    taskid = '<?php echo $taskid; ?>';
    $(function() {
        tasks_kanban();
        if ($('.h_switch').length > 0) {
            $('.action-menu').trigger('click');
        }
    });
</script>
</body>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<!--<script type="text/javascript" src="--><? //= js('datatables/dataTables.fixedHeader.min.js') 
                                            ?><!--"></script>-->
<script>
    $('#category_recommended_id').change(function() {
        var category_recommended_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['category_recommended_id'] = category_recommended_id;

        $.post(admin_url + 'internal_proposal/getSuggestByRecommendedSingle', data, function(data) {
            data = JSON.parse(data);
            $('#suggest_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#suggest_id').append(`<option data-subtext="${value.staff_suggest_name}" value="${value.id}">${value.reference_no}</option>`);
            })
            $('#suggest_id').selectpicker('refresh');
        });
    });
    $('body').on('click', '.H_filter', function(e) {
        $('.H_filter').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('input[name="filterStatus"]').val($(this).attr('data-id')).trigger('change');
        // tAPI.draw('page');
    });
    $('body').on('change', `input[name="filterStatus"],
        input[name="list_staff"],
        select[name="suggest_id"],
        select[name="staff_follower"],
        select[name="category_recommended_id"],
        input[name="date_start_end_search"],
        input[name="date_end_end_search"],
        input[name="date_start_search"],
        input[name="date_end_search"],
        input[name="procedure_tasks_search"],
        input[name="category_tasks_search_search"],
        select[name="type_tasks_search"],
        input[name="list_departments"],
        input[name="room_task"],
        input[name="list_staff_create"]`, function() {
        if (_table_api) {
            _table_api.draw('page');
        }
    })
    $('#type_tasks_search').change(function() {
        $('input[name="type_tasks_search_"]').val($(this).val()).trigger('change');
    })
    $('#category_recommended_id').change(function() {
        $('input[name="category_recommended_id_search"]').val($(this).val()).trigger('change');
    })

    $('#suggest_id').change(function() {
        $('input[name="suggest_id_search"]').val($(this).val()).trigger('change');
    })

    $('.table-tasks').on('draw.dt', function() {
        var expenseReportsTable = $(this).DataTable();
        var total = expenseReportsTable.ajax.json().total;
        $.each(total, function(i, v) {
            $('.filter_' + i).html('(' + tnhFormatNumber(v) + ')');
        })
        $('.rows-child-all').trigger('click');
        $('.rows-child-all.fa-caret-right').trigger('click');
        $('.rows-child.fa-caret-right').trigger('click');

        if (id_background) {
            var idShow = id_background;
            id_background = null;
            $('.tr_' + idShow).addClass('bg-danger');
            setTimeout(function() {
                $('.tr_' + idShow).removeClass('bg-danger');
            }, 2000)
        }
    });


    $('.table-tasks tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = _table_api.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadItemsTasks(row.data())).show();
            tr.addClass('shown');
        }
    });

    $('.table-tasks thead').on('click', '.rows-child-all', function() {
        if ($(this).hasClass('fa-caret-right')) {
            $(this).addClass('fa-caret-down');
            $(this).removeClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = _table_api.row(tr);
                $(value).removeClass('fa-caret-right');
                $(value).addClass('fa-caret-down');
                row.child(loadItemsTasks(row.data())).show();
                tr.addClass('shown');
            })
        } else {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = _table_api.row(tr);
                $(value).removeClass('fa-caret-down');
                $(value).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            })
        }

    });

    function loadItemsTasks(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        cHtml = cData[16];
        return `<div>${cHtml}</div>`;
    }

    $('#staff_task').change(function() {
        var staff_task = $(this).val();
        staff_task = staff_task.toString();
        $('input[name="list_staff"]').val(staff_task).trigger('change');
    })

    $('#staff_task_create').change(function() {
        var staff_task_create = $(this).val();
        staff_task_create = staff_task_create.toString();
        $('input[name="list_staff_create"]').val(staff_task_create).trigger('change');
    })

    $('#staff_follower').change(function() {
        var staff_follower = $(this).val();
        staff_follower = staff_follower.toString();
        $('input[name="staff_follower_search"]').val(staff_follower).trigger('change');
    })

    // $('#departments_task').change(function() {
    //     var departments_task = $(this).val();
    //     departments_task = departments_task.toString();
    //     $('input[name="list_departments"]').val(departments_task).trigger('change');
    // })

    $('#room_task').change(function() {
        var departments_task = $(this).val();
        departments_task = departments_task.toString();
        $('input[name="list_departments"]').val(departments_task).trigger('change');
    })

    $('#date_start').change(function() {
        $('input[name="date_start_search"]').val($(this).val()).trigger('change');
    })

    $('#date_end').change(function() {
        $('input[name="date_end_search"]').val($(this).val()).trigger('change');
    })
    $('#date_start_end').change(function() {
        $('input[name="date_start_end_search"]').val($(this).val()).trigger('change');
    })

    $('#date_end_end').change(function() {
        $('input[name="date_end_end_search"]').val($(this).val()).trigger('change');
    })
    $('#procedure_tasks').change(function() {
        $('input[name="procedure_tasks_search"]').val($(this).val()).trigger('change');
    })
    $('#category_tasks_search').change(function() {
        $('input[name="category_tasks_search_search"]').val($(this).val()).trigger('change');
        var category_tasks_search = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['category_tasks_search'] = category_tasks_search;

        $.post(admin_url + 'tasks/getProcedureTasks', data, function(data) {
            data = JSON.parse(data);
            $('#procedure_tasks').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#procedure_tasks').append(`<option value="${value.id}">${value.name}</option>`);
            })
            $('#procedure_tasks').selectpicker('refresh');
        });
        $('#procedure_tasks').trigger('change');
    })
</script>
<script>
    // function status_checklist(id, status, _this) {
    //     $.get(admin_url + 'tasks/checkbox_action_list/' + id + '/' + status, function(result) {
    //         result = JSON.parse(result);
    //         if(result.success) {
    //             if(result.data.finished == 1) {
    //                 $(_this).attr('onclick', 'status_checklist('+id+', 0, this)');
    //                 $(_this).addClass('active');
    //                 $(_this).find('.active_poin').text("Được " + result.data.name_finished_from + ' hoàn thành');
    //             }
    //             else {
    //                 $(_this).attr('onclick', 'status_checklist('+id+', 1, this)');
    //                 $(_this).removeClass('active');
    //                 $(_this).find('.active_poin').text('');
    //             }
    //         }
    //     })
    // }
    function status_checklist(id, _status, type) {
        bootbox.confirm('Bạn có muốn duyệt không?', function(result) {
            if (result) {
                var data = {};
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['listid'] = id;
                data['status'] = _status;
                data['type'] = type;

                $.ajax({
                    type: "POST",
                    url: site.base_url + 'admin/tasks/checkbox_action_list_new',
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        if (response.result == 0) {
                            alert_float('danger', response.message);
                        } else {
                            alert_float('success', response.message);
                            _table_api.draw(false);
                        }
                    }
                });
            }
        });
    }


    $('#room_task').on('change', function() {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['room'] = $(this).val();
        $.post(admin_url + 'tasks/get_select_staff', data, function(result) {
            result = JSON.parse(result);
            $('#staff_task').html('');
            $.each(result, function(index, value) {
                $('#staff_task').append(`<option value="${value.staffid}">${value.fullname}</option>`)
            })
            $('#staff_task').selectpicker('refresh');
        })
    })


    function exportExcel() {
        var row_tasks_id = $('.row-tasks-id');
        var list_id = [];
        $.each(row_tasks_id, function(index, value) {
            list_id.push($(value).data('id'));
        })

        search_code = $('[name="search_code"]').val();
        search_id_suppliers = $('[name="search_id_suppliers[]"]').val();
        custom_item_select = $('[name="custom_item_select[]"]').val();
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/tasks/export_excel',
            data: {
                csrf_token_name: hash,
                list_id: list_id,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    var id_background;

    function arrow_up_tr(_this) {
        var order_before = $(_this).data('order-before');
        var id_before = $(_this).data('id-before');
        var order_by = $(_this).data('order');
        var id = $(_this).data('id');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['order_before'] = order_before;
        data['id_before'] = id_before;
        data['order_by'] = order_by;
        data['id'] = id;
        $.post(admin_url + 'tasks/order_by_tasks', data, function(result) {
            result = JSON.parse(result);
            if (result.success) {
                id_background = id;
                _table_api.draw('page');

            }
        })
    }

    function arrow_down_tr(_this) {
        var order_after = $(_this).data('order-after');
        var id_after = $(_this).data('id-after');
        var order_by = $(_this).data('order');
        var id = $(_this).data('id');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }

        data['order_before'] = order_by;
        data['id_before'] = id;
        data['order_by'] = order_after;
        data['id'] = id_after;
        $.post(admin_url + 'tasks/order_by_tasks', data, function(result) {
            result = JSON.parse(result);
            if (result.success) {
                id_background = id;
                _table_api.draw('page');
            }
        })
    }

    function approve_all() {
        var procedure_tasks = $('#procedure_tasks').val();
        var category_tasks_search = $('#category_tasks_search').val();
        if(procedure_tasks == ''){
            alert('Vui lòng chọn quy trình duyệt nhanh');
            return false;
        }
        var date_start = $('#date_start').val();
        var date_end = $('#date_end').val();
        var date_start_end = $('#date_start_end').val();
        var date_end_end = $('#date_end_end').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['procedure_tasks'] = procedure_tasks;
        data['category_tasks_search'] = category_tasks_search;
        data['date_start'] = date_start;
        data['date_end'] = date_end;
        data['date_start_end'] = date_start_end;
        data['date_end_end'] = date_end_end;
        $.post(admin_url + 'tasks/inspection_criteria_all', data, function(data) {
            $('#data_inspection_criteria_all').html(data);
            $('#view_modal_data_inspection_criteria_all').modal('show');
        });
        return false;
    }
</script>

<script>
    $(document).ready(function () {
        $('#room_task').change(function (e) { 
            e.preventDefault();

            var dataGET = {};
            var room_task = $(this).val();
            // dataPOST[csrfData['token_name']] = csrfData['hash'];
            dataGET['room_task'] = room_task;
            $.ajax({
                type: "GET",
                url: site.base_url+'admin/tasks/searchCategoryTasks',
                data: dataGET,
                dataType: 'json',
                success: function (response) {
                    var options = '<option></option>';
                    $.each(response?.category_tasks, function (index, value) { 
                        options+= `<option data-subtext="${value.content}" value="${value.id}">${value.code}</option>`;
                    });

                    $('#category_tasks_search').html(options);
                    $('#category_tasks_search').selectpicker('refresh');
                }   
            });
        });
        
        // Task search combobox logic
        // init_ajax_search('task', '#task_id_search', undefined, admin_url + 'tasks/task_id_search');
        selectAjax($('#task_id_search'), false, 'admin/tasks/task_id_search', false);
        
        $('#task_id_search').on('change', function(e, clickedIndex, isSelected, previousValue) {
            var val = $(this).val();
            $('input[name="task_id_search"]').val(val);
            $('.table-tasks').DataTable().ajax.reload();
        });

        // Handle initial task ID from URL
        if (typeof(taskid) !== 'undefined' && taskid !== '') {
            setTimeout(function() {
                // Add option to search box
                var option = new Option(taskid, taskid, true, true);
                $('#task_id_search').append(option).trigger('change');
                $('#task_id_search').selectpicker('refresh');
                
                // Modal is already opened by main.js if taskid is defined
                // But we trigger change to filter table and reset URL
            }, 1000);
        }

        <?php if (!empty($_GET['id'])): ?>
            $(document).ready(function() {
                if (window.history.pushState) {
                    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.pushState({ path: newurl }, '', newurl);
                }
            });
        <?php endif; ?>
    });

    function exportExcelTasks() {

        var dataPOST = {};
		dataPOST[csrfData['token_name']] = csrfData['hash'];

        u = $("._hidden_inputs._filters._tasks_filters input"), $.each(u, function () {
            dataPOST[$(this).attr("name")] = $('[name="' + $(this).attr("name") + '"]').val();
        });

        var date_start_search = dataPOST['date_start_search'];
        var date_end_search = dataPOST['date_end_search'];

        if (!date_start_search && !date_end_search) {
            alert_float('danger', 'Vui lòng chọn ngày bắt đầu và kết thúc');
            return;
        }

        dataPOST['export_excel'] = 1;
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/tasks/export_excel_tasks',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>
</html>