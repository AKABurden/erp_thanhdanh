<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$title?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel='stylesheet' href='https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.css'>
    <script src='https://cdn.rawgit.com/t4t5/sweetalert/v0.2.0/lib/sweet-alert.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
    <style>
        .text-center {
            text-align: center;
        }
        #wrapper {
            height: calc(100%);
        }
        .wap-containter {
            position: relative;
            overflow-x: hidden;
            height: calc(100%);
        }
        .wap-containter-menu {
            height: calc(100% - 200px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .wap-content-menu {
            width: 750px;
        }
        .content-menu {
            float: left;
            width: 33%;
            padding: 15px 5px;
            text-align: center;
        }
        .img-content-menu img {
            width: 50px;
            height: 50px;
        }
        .text-content-menu {
            text-transform: uppercase;
            margin-top: 5px;
            font-size: 18px;
        }
        .group-content {
            cursor: pointer;
            position: relative;
            background: #a5c0e2;
            padding: 10px 10px;
            border: 1px solid #3d6e98;
        }
        .icon-done {
            position: absolute;
            right: 5px;
            top: 0px;
            color: #fff;
            font-size: 25px;
        }
        .group-content:hover {
            background: #3b7bca;
        }
        .group-content.active {
            background: #3b7bca;
        }
        .wap-choise-step-scan {
            height: 200px;
        }
        .wap-choise-text {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            color: #06f;
            text-transform: uppercase;
        }
        .wap-choise-img {
            text-align: center;
        }
        .wap-choise-img img {
            width: 100px;
        }
        .wap-left {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0%;
            transition: all 0.5s;
            -webkit-transition: all 0.5s;
        }
        .wap-right {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 100%;
            transition: all 0.5s;
            -webkit-transition: all 0.5s;
        }
        .wap-right-staff {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 200%;
            transition: all 0.5s;
            -webkit-transition: all 0.5s;
        }
        .wap-back-menu {
            float: right;
        }
        .wap-back-menu {
            float: right;
            margin-right: 20px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wap-btn {
            cursor: pointer;
            border-radius: 5px;
            border: 1px solid #289230;
            background: #5ace34;
            color: #fff;
            padding: 10px 40px;
            text-transform: uppercase;
        }
        .wap-btn:hover {
            background: #4ba92c;
            color: #f9f9f9;
        }

        .wap-btn.goback, .wap-btn.backstep {
            border: 1px solid #949494;
            background: #d0d0d0;
        }
        .wap-btn.goback:hover, .wap-btn.backstep:hover {
            background: #c6c6c6;
            color: #fff;
        }
        .wap-title-menu {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .title-content {
            width: 250px;
            text-align: center;
            background: #a5c0e2;
            padding: 10px 10px;
            border: 1px solid #3d6e98;
        }
        .wap-input-barcode {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .input-barcode {
            width: 50%;
            height: 70px !important;
            padding: 0 0 0 80px;
            font-size: 35px !important;
            outline: auto !important;
        }
        .input-staff {
            width: 50%;
            height: 70px !important;
            padding: 0 0 0 80px;
            font-size: 35px !important;
            outline: auto !important;
        }
        .wap-img-input img {
            width: 70px;
            height: 68px;
        }
        .wap-img-input {
            position: absolute;
            left: 25%;
        }
        .wap-title-order {
            text-align: center;
            font-size: 20px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .wap-containter-order {
            position: relative;
        }
        .wap-btn-done {
            position: absolute;
            top: 10px;
            right: 20px;
        }
        .wap-containter-detail-order {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wap-content-order {
            margin-top: 15px;
            width: 750px;
        }
        .wap-detail-order {
            font-size: 16px;
        }
        .wap-table-detail {
            margin-top: 15px;
        }
        .wap-footer {
            position: fixed;
            z-index: 999;
            bottom: 0;
            height: 45px;
            width: 100%;
            background: #a5c0e2;
            color: #06f;
            padding: 10px;
            text-align: center;
            font-size: 16px;
            text-transform: uppercase;
        }
        .table-detail-order thead {
            background: #74a2f7;
            color: #fff;
        }
        .table-detail-order .odd {
            background: #d6e3ff;
        }
        .table-detail-order .even {
            background: #fff;
        }
        .table-detail-staff thead {
            background: #74a2f7;
            color: #fff;
        }
        .table-detail-staff .odd {
            background: #d6e3ff;
        }
        .table-detail-staff .even {
            background: #fff;
        }
        .wap-title-order-menu {
            width: 750px;
            padding: 0 5px;
        }
        ::-webkit-input-placeholder {
          color: #b1b1b1;
        }

        :-ms-input-placeholder {
          color: #b1b1b1;
        }

        ::placeholder {
          color: #b1b1b1;
        }
        .img-staff {
            border-radius: 50%;
            margin: 5px 11px;
        }
        .img-staff:hover {
            cursor: pointer;
            border: 3px solid #74a2f7;
        }
        .img-staff.active {
            border: 3px solid #74a2f7;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <div class="wap-containter">
            <div class="wap-left">
                <div class="wap-input-barcode">
                    <div class="wap-img-input">
                        <img src="<?=base_url('uploads/img_menu/barcode.PNG')?>">
                    </div>
                    <input type="text" class="input-barcode" placeholder="Quét mã đơn hàng..." onblur="this.focus()" autofocus>
                </div>
                <div class="wap-containter-order">
                    <div class="wap-title-order">
                        <?=_l('Thông tin đơn hàng')?>
                    </div>
                    <div class="wap-btn-done">
                        <span class="wap-btn next-step"><?=_l('Xác nhận')?></span>
                    </div>
                    <div class="wap-containter-detail-order">
                        <div class="wap-content-order">
                            <div class="wap-detail-order">
                                <input type="hidden" name="reference_order" id="reference_order" class="form-control" value="">
                                <?=_l('Mã đơn hàng')?>: <span class="code_order"></span>
                            </div>
                            <div class="wap-detail-order">
                                <?=_l('Khách hàng')?>: <span class="name_client"></span>
                            </div>
                            <div class="wap-table-detail">
                                <table class="table table-bordered table-detail-order" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 10%;">
                                                <?php echo _l('STT'); ?>
                                            </th>
                                            <th class="text-center" style="width: 20%;">
                                                <?php echo _l('Mã Hàng'); ?>
                                            </th>
                                            <th class="text-center" style="width: 50%;">
                                                <?php echo _l('Tên Hàng'); ?>
                                            </th>
                                            <th class="text-center" style="width: 20%;">
                                                <?php echo _l('Số Lượng'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wap-right">
                <div class="wap-containter-menu">
                    <div class="wap-title-order-menu">
                        <div class="title-child-content">
                            <div class="wap-detail-order">
                                <?=_l('Mã đơn hàng')?>: <span class="code_order"></span>
                            </div>
                            <div class="wap-detail-order">
                                <?=_l('Khách hàng')?>: <span class="name_client"></span>
                            </div>
                        </div>
                    </div>
                    <div class="wap-content-menu">
                        <div class="content-menu">
                            <div class="group-content">
                                <div class="icon-done"><i class="fa fa-check-circle"></i></div>
                                <div class="img-content-menu tick-process" value="1">
                                    <img src="<?=base_url('uploads/img_menu/fast-delivery.png')?>">
                                </div>
                                <div class="text-content-menu">
                                    <?=_l('Soạn hàng')?>
                                </div>
                            </div>
                        </div>
                        <div class="content-menu">
                            <div class="group-content">
                                <div class="icon-done"><i class="fa fa-check-circle"></i></div>
                                <div class="img-content-menu tick-process" value="2">
                                    <img src="<?=base_url('uploads/img_menu/Capture2_03.png')?>">
                                </div>
                                <div class="text-content-menu">
                                    <?=_l('Sản xuất')?>
                                </div>
                            </div>
                        </div>
                        <div class="content-menu">
                            <div class="group-content">
                                <div class="icon-done"><i class="fa fa-check-circle"></i></div>
                                <div class="img-content-menu tick-process" value="7">
                                    <img src="<?=base_url('uploads/img_menu/stock.png')?>">
                                </div>
                                <div class="text-content-menu">
                                    <?=_l('Tập kết giao hàng')?>
                                </div>
                            </div>
                        </div>
                        <div class="content-menu">
                            <div class="group-content">
                                <div class="icon-done"><i class="fa fa-check-circle"></i></div>
                                <div class="img-content-menu tick-process" value="8">
                                    <img src="<?=base_url('uploads/img_menu/shield.png')?>">
                                </div>
                                <div class="text-content-menu">
                                    <?=_l('Giao hàng bảo vệ')?>
                                </div>
                            </div>
                        </div>
                        <div class="content-menu">
                            <div class="group-content">
                                <div class="icon-done"><i class="fa fa-check-circle"></i></div>
                                <div class="img-content-menu tick-process" value="9">
                                    <img src="<?=base_url('uploads/img_menu/car.png')?>">
                                </div>
                                <div class="text-content-menu">
                                    <?=_l('Giao hàng xe')?>
                                </div>
                            </div>
                        </div>
                        <div class="content-menu">
                            <div class="group-content">
                                <div class="icon-done"><i class="fa fa-check-circle"></i></div>
                                <div class="img-content-menu tick-process" value="10">
                                    <img src="<?=base_url('uploads/img_menu/post-office.png')?>">
                                </div>
                                <div class="text-content-menu">
                                    <?=_l('Giao hàng văn phòng')?>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="tick-process-val" value="">
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="wap-choise-step-scan">
                    <div class="wap-choise-content">
                        <div class="wap-choise-text">
                            <span class="wap-btn goback"><?=_l('Đơn hàng')?></span>
                            <span class="wap-btn next-step-staff"><?=_l('Xác nhận')?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wap-right-staff" style="margin-bottom: 100px;">
                <div class="wap-input-barcode">
                    <div class="wap-img-input">
                        <img src="<?=base_url('uploads/img_menu/barcode.PNG')?>">
                    </div>
                    <input type="text" class="input-staff" placeholder="Quét mã nhân viên...">
                </div>
                <div class="wap-containter-order">
                    <div class="wap-title-order">
                        <?=_l('Thông tin nhân viên')?>
                    </div>
                    <div class="wap-containter-detail-order">
                        <div class="wap-content-order">
                            <div class="wap-detail-order">
                                <input type="hidden" name="reference_order" id="reference_order" class="form-control" value="">
                                <?=_l('Mã đơn hàng')?>: <span class="code_order"></span>
                            </div>
                            <div class="wap-detail-order">
                                <?=_l('Khách hàng')?>: <span class="name_client"></span>
                            </div>
                            <div class="wap-table-detail">
                                <table class="table table-bordered table-detail-staff" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">
                                                <?php echo _l('Danh sách nhân viên quản lý quy trình'); ?>
                                                <input type="hidden" class="staff_id" value="">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <span class="wap-btn backstep"><?=_l('Quy trình')?></span>
                                <span class="wap-btn" onclick="clickFinished(this)"><?=_l('Hoàn tất')?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wap-footer">
            Phần mềm quản lý sản xuất
        </div>
    </div>
</body>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<link href="https://datatables.net/download/build/dataTables.responsive.nightly.css" rel="stylesheet" type="text/css" />
<script src="https://datatables.net/download/build/dataTables.responsive.nightly.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script>
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    $( document ).ready(function() {
        var height_document = $(document).height();
        $('body').css('height',height_document+'px');
    });
    $(document).on('click','.next-step', function (e) {
        if(!$('#reference_order').val()) {
            swal({
                title: 'Vui lòng nhập đơn hàng',
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Đóng',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Đóng",
                closeOnCancel: false
            });
        }
        else {
            $('.wap-left').css('left','-100%');
            $('.wap-right').css('left','0');
            $('.wap-right-staff').css('left','100%');

            $('.input-barcode').removeAttr('onblur');
            $('.input-barcode').attr('autofocus',false);
            $('.input-staff').removeAttr('onblur');
            $('.input-staff').attr('autofocus',false);
        }
    });
    $(document).on('click','.next-step-staff', function (e) {
        if(!$('#reference_order').val()) {
            swal({
                title: 'Vui lòng nhập đơn hàng',
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Đóng',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Đóng",
                closeOnCancel: false
            });
        }
        else if(!$('.tick-process-val').val()) {
            swal({
                title: 'Vui lòng chọn quy trình',
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Đóng',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Đóng",
                closeOnCancel: false
            });
        }
        else {
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['process'] = $('.tick-process-val').val();
            $.post("<?=base_url('scan_qr/getData_staff_inProcess')?>", data).done(function(response){
                response = JSON.parse(response);
                var html = '';
                if(response == '') {
                    html = '<tr class="odd">\
                                <td>Không có nhân viên quản lý quy trình này!</td>\
                            </tr>';
                }
                else {
                    html += '<tr><td class="text-center">';
                    html += response;
                    html += '</td></tr>';
                }
                $('.table-detail-staff').find('tbody').html(html);

                $('.wap-left').css('left','-200%');
                $('.wap-right').css('left','-100%');
                $('.wap-right-staff').css('left','0%');

                $('.input-staff').attr('onblur','this.focus()');
                $('.input-staff').attr('autofocus',true);
                setTimeout(function(){ $('.input-staff').focus(); }, 500);
            });
        }
    });

    $(document).on('click','.goback', function (e) {
        $('.wap-left').css('left','0%');
        $('.wap-right').css('left','100%');
        $('.wap-right-staff').css('left','200%');

        $('.input-barcode').focus();
        $('.input-barcode').attr('onblur','this.focus()');
        $('.input-barcode').attr('autofocus',true);
    });

    $(document).on('click','.backstep', function (e) {
        $('.wap-left').css('left','-100%');
        $('.wap-right').css('left','0%');
        $('.wap-right-staff').css('left','100%');

        $('.input-barcode').removeAttr('onblur');
        $('.input-barcode').attr('autofocus',false);
        $('.input-staff').removeAttr('onblur');
        $('.input-staff').attr('autofocus',false);
    });
    
    
    $(document).on('click','.group-content', function (e) {
        $('.group-content').removeClass('active');
        var target = $(e.currentTarget);
        if(!target.find('.icon-done').hasClass('hide')) {
            swal({
                title: 'Quy trình đã được thêm!',
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Đóng',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Đóng",
                closeOnCancel: false
            });
            $('.tick-process-val').val('');
        }
        else {
            target.addClass('active');
            var val = target.find('.tick-process').attr('value');
            $('.tick-process-val').val(val);
        }
    });

    $(document).on('click','.img-staff', function (e) {
        $('.img-staff').removeClass('active');
        var target = $(e.currentTarget);
        target.addClass('active');
        var val = target.attr('data-id');
        $('.staff_id').val(val);
    });

    var t;

    // console.log('<?=lang('tnh_you_want_finished_this_process') ?>');

    function clickFinished(el)
    {
        reference_order = $('#reference_order').val();
        status = $('.tick-process-val').val();
        staff_id = $('.staff_id').val();
        
        if (!status || status == '') {
            swal({
                title: 'Vui lòng chọn quy trình',
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Đóng',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Đóng",
                closeOnCancel: false
            });
        }
        else if (!staff_id || staff_id == '') {
            swal({
                title: 'Vui lòng nhập nhân viên',
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Đóng',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Đóng",
                closeOnCancel: false
            });
        }
        else if (!reference_order || reference_order == '') {
            swal({
                title: 'Vui lòng nhập đơn hàng',
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Đóng',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Đóng",
                closeOnCancel: false
            });
        } else {
            swal({
                title: '<?= lang('tnh_you_want_finished_this_process') ?>',
                type: "warning",
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonColor: '#2ec48c',
                confirmButtonText: 'Có',
                cancelButtonColor: '#DD6B55',
                cancelButtonText: "Không",
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: '<?= base_url('scan_qr/updateProcessOrders') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            reference_order: reference_order,
                            status: status,
                            staff_id: staff_id,
                            csrf_token_name: hash
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            swal({
                                title: data.message,
                                type: "success",
                                showCancelButton: false,
                                showConfirmButton: false,
                                confirmButtonColor: '#2ec48c',
                                confirmButtonText: 'Đóng',
                                cancelButtonColor: '#DD6B55',
                                cancelButtonText: "Đóng",
                                closeOnCancel: false
                            });

                            $('.input-barcode').val(null).trigger('change');
                            $('.input-staff').val('');
                            $('.img-staff').removeClass('active');
                            $('.group-content').removeClass('active');
                            $('.tick-process-val').val('');
                            $('.staff_id').val('');

                            $('.wap-left').css('left','0%');
                            $('.wap-right').css('left','100%');
                            $('.wap-right-staff').css('left','200%');

                            $('.input-staff').removeAttr('onblur');
                            $('.input-staff').attr('autofocus',false);
                            $('.input-staff').blur();

                            $('.input-barcode').attr('onblur','this.focus()');
                            $('.input-barcode').attr('autofocus',true);
                            setTimeout(function(){ $('.input-barcode').focus(); }, 500);
                        } else {
                            swal({
                                title: data.message,
                                type: "warning",
                                showCancelButton: false,
                                showConfirmButton: false,
                                confirmButtonColor: '#2ec48c',
                                confirmButtonText: 'Đóng',
                                cancelButtonColor: '#DD6B55',
                                cancelButtonText: "Đóng",
                                closeOnCancel: false
                            });
                        }
                    })
                    .fail(function() {
                        swal({
                            title: '<?= lang('tnh_error_please_reload_page') ?>',
                            type: "warning",
                            showCancelButton: false,
                            showConfirmButton: false,
                            confirmButtonColor: '#2ec48c',
                            confirmButtonText: 'Đóng',
                            cancelButtonColor: '#DD6B55',
                            cancelButtonText: "Đóng",
                            closeOnCancel: false
                        });
                    });
                }
            });
        }
    }

    $(document).ready(function() {
        t = $('.table-detail-order').DataTable(
            {
                "bLengthChange": false,
                searching : false,
                responsive : true,
                "oLanguage":{
                    "sProcessing":   "Đang xử lý...",
                    "sLengthMenu":   "Xem _MENU_ mục",
                    "sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
                    "sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
                    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                    "sInfoPostFix":  "",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "Đầu",
                        "sPrevious": "Trước",
                        "sNext":     "Tiếp",
                        "sLast":     "Cuối"
                    }
                },
                pageLength: 10
            }
        );
    });
    $(document).on('change','.input-barcode', function (e) {
        $('.code_order').html('');
        $('.name_client').html('');
        $('.table-detail-order').find('tbody').html('');
        $('.icon-done').addClass('hide');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['code_order'] = $('.input-barcode').val();
        $.post("<?=base_url('scan_qr/getData')?>", data).done(function(response){
            t.rows().remove().draw();
            response = JSON.parse(response);
            $('.code_order').text(response.code_order);
            $('.name_client').text(response.client);
            $('#reference_order').val(response.code_order);
            var html = '';
            $.each(response.items, function(i, v){
                var stt = ++i;
                t.row.add([
                    '<div class="text-center">'+stt+'</div>',
                    '<div class="text-center">'+v.code+'</div>',
                    '<div class="text-center">'+v.name+'</div>',
                    '<div class="text-center">'+v.quantity+'</div>',
                ]).draw(false);
            });
            $('.input-barcode').val('');
            $('.group-content').removeClass('active');
            $('.tick-process-val').val('');

            if(typeof response.code_order !== 'undefined') {
                var all_tick_process = $('.tick-process');
                $.each(all_tick_process, function(i, v){
                    for (var i = 0; i < response.arrStatus.length; i++) {
                        if(response.arrStatus[i] == $(v).attr('value').toString()) {
                            $(v).parents('.group-content').find('.icon-done').removeClass('hide');
                        }
                    }
                });
            }
        });
    });

    $(document).on('change','.input-staff', function (e) {
        $('.img-staff').removeClass('active');
        $('.staff_id').val('');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['code_staff'] = $('.input-staff').val();
        $.post("<?=base_url('scan_qr/getData_staff')?>", data).done(function(response){
            response = JSON.parse(response);
            if(typeof response.code === 'undefined' ) {
                swal({
                    title: 'Không tìm thấy nhân viên!',
                    type: "warning",
                    showCancelButton: false,
                    showConfirmButton: false,
                    confirmButtonColor: '#2ec48c',
                    confirmButtonText: 'Đóng',
                    cancelButtonColor: '#DD6B55',
                    cancelButtonText: "Đóng",
                    closeOnCancel: false
                });
            }
            else {
                if($('img[data-id="'+response.staff_id+'"]').length == 0) {
                    swal({
                        title: 'Nhân viên không quản lý quy trình này!',
                        type: "warning",
                        showCancelButton: false,
                        showConfirmButton: false,
                        confirmButtonColor: '#2ec48c',
                        confirmButtonText: 'Đóng',
                        cancelButtonColor: '#DD6B55',
                        cancelButtonText: "Đóng",
                        closeOnCancel: false
                    });
                }
                else {
                    $('img[data-id="'+response.staff_id+'"]').addClass('active');
                    $('.staff_id').val(response.staff_id);
                }
            }
            $('.input-staff').val('');
        });
    });
</script>
</html>