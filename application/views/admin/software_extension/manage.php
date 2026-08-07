<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(false); ?>
<style>
    .wrap-container-software {
        margin: 25px 0;
    }
    .wrap-content-software {
        width: 60%;
        background: #fff;
        box-shadow: 1px 4px 8px #00000036;
        margin: 0 auto;
    }
    .wrap-header-software {
        text-align: center;
        font-size: 16px;
        text-transform: uppercase;
        font-weight: 500;
        padding: 10px 10px;
        margin: 0 10px 20px 10px;
        border-bottom: 1px solid #d2d2d2;
    }
    .wrap-note-software {
        padding: 10px;
        display: flex;
        align-items: center;
    }
    .wrap-left-note {
        color: #737373;
        float: left;
        width: 70%;
    }
    .wrap-right-note {
        float: left;
        width: 30%;
        border-bottom: 1px solid #afafaf;
    }
    .wrap-total-title {
        float: left;
        font-size: 16px;
        font-weight: 500;
    }
    .wrap-total-number {
        float: right;
        font-size: 16px;
        font-weight: 500;
    }
    .wrap-payment-software {
        text-align: right;
        padding: 10px;
    }
    .wrap-btn-payment {
        background: #30d454;
        padding: 10px 15px;
        line-height: 3;
        color: #fff;
        border-radius: 2px;
        text-transform: uppercase;
    }
    .wrap-btn-payment:hover {
        background: #24a240;
        color: #fff;
    }
    .wrap-content-footer {
        padding: 0 10px;
    }
    .wrap-text-strong {
        color: #f00;
        font-weight: 500;
    }
    .wrap-text-default {
        color: #737373;
    }
    .wrap-content-bank {
        height: 180px;
        float: left;
        width: 33%;
    }
    .wrap-list-bank {
        padding: 10px 0;
        display: flex;
        justify-content: center;
    }
    .wrap-content-bank:not(.not-border) {
        border-right: 1px solid #989898;
    }
    .wrap-img-bank {
        text-align: center;
        padding-bottom: 10px;
    }
    .wrap-img-bank img {
        height: 40px;
    }
    .wrap-text-bank {
        padding: 5px 10px;
        font-weight: 500;
    }
</style>
<div class="wrap-container-software">
    <div class="wrap-content-software">
        <div class="wrap-header-software">
          Gia hạn SERVER
        </div>
        <div class="wrap-body-software">
            <div class="col-md-6">
                <?php
                    $arrTime = array(
                        array(
                            'id' => 6,
                            'name' => '6 tháng'
                        ),
                        array(
                            'id' => 12,
                            'name' => '12 tháng'
                        ),
                        array(
                            'id' => 24,
                            'name' => '24 tháng'
                        ),
                        array(
                            'id' => 36,
                            'name' => '36 tháng'
                        )
                    );
                ?>
                <?php echo render_select('time', $arrTime, array('id', 'name'), 'Thời hạn', '', array(), array(), '', '', false); ?>
            </div>
            <div class="col-md-6">
                <?php echo render_input('user_quantity', 'Số user tăng thêm', 0, 'number'); ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="wrap-note-software">
            <div class="wrap-left-note">
                * 1,200,000/tháng: bao gồm 5 user.
                <br>
                * User tăng thêm: (5 user đầu: 200,000đ/user/tháng; user thứ 6 trở đi: 100,000đ/user/tháng).
            </div>
            <div class="wrap-right-note">
                <div class="wrap-total-title">
                    Tổng cộng:
                </div>
                <div class="wrap-total-number">
                    0
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="wrap-payment-software">
            <a class="wrap-btn-payment">Thanh toán</a>
        </div>
        <div class="wrap-header-software">
          Hướng dẫn thanh toán
        </div>
        <div class="wrap-content-footer">
            <div class="wrap-text-default">
                Sau khi tạo mới hoặc nâng cấp gói cước, bạn vui lòng chuyễn khoản vào một tài khoản bên dưới với <span class="wrap-text-strong">nội dung chuyển khoản</span> là <span class="wrap-text-strong">mã gia hạn</span>  vừa được tạo.
            </div>
            <div class="wrap-list-bank">
                <div class="wrap-content-bank">
                    <div class="wrap-img-bank">
                        <img src="<?= base_url('uploads/ACB_logo.png'); ?>">
                    </div>
                    <div class="wrap-text-bank">
                        TÊN TK: CÔNG TY TNHH GIẢI PHÁP PHẦN MỀM FOSO
                    </div>
                    <div class="wrap-text-bank">
                        SỐ TK: 881688
                    </div>
                    <div class="wrap-text-bank">
                        NGÂN HÀNG: Á CHÂU (ACB) - CN: TP.HCM
                    </div>
                </div>
                <div class="wrap-content-bank">
                    <div class="wrap-img-bank">
                        <img src="<?= base_url('uploads/ACB_logo.png'); ?>">
                    </div>
                    <div class="wrap-text-bank">
                        TÊN TK: BÙI PHẠM THANH THUỶ
                    </div>
                    <div class="wrap-text-bank">
                        SỐ TK: 18694 6789
                    </div>
                    <div class="wrap-text-bank">
                        NGÂN HÀNG: Á CHÂU (ACB) - CN: TP.HCM
                    </div>
                </div>
                <div class="wrap-content-bank not-border">
                    <div class="wrap-img-bank">
                        <img src="<?= base_url('uploads/sacombank_logo.png'); ?>">
                    </div>
                    <div class="wrap-text-bank">
                        TÊN TK: BÙI PHẠM THANH THUỶ
                    </div>
                    <div class="wrap-text-bank">
                        SỐ TK: 0600 8673 4888
                    </div>
                    <div class="wrap-text-bank">
                        NGÂN HÀNG: SACOMBANK - CN: LĂNG CHA CẢ
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="wrap-text-default" style="padding-bottom: 10px;">
                Sau khi nhận được thông báo từ ngân hàng và gói cước được xác thực, hệ thống sẽ kích hoạt gói cước sau 5-15 phút.
                <br>
                Trong trường hợp bạn điền sai thông tin hoặc có bất kỳ sự cố gì khiến hệ thống không thể tự động kích hoạt, vui lòng liên hệ BQT để được hỗ trợ sớm nhất.
            </div>
        </div>
    </div>
</div>

<div class="view_modal_payment"></div>
<?php init_tail(); ?>
<script>
    $(function(){
        resetTotal();
    });
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    }

    function unformatNumber(nStr, decSeperate=".", groupSeperate=",") {
        return nStr.replace(/\,/g,'');
    }

    $(document).on('change','#time', function (e) {
        resetTotal();
    });

    $(document).on('change','#user_quantity', function (e) {
        resetTotal();
    });

    $(document).on('keyup','#user_quantity', function (e) {
        resetTotal();
    });

    function resetTotal() {
        var time = $('#time').val();
        var user_quantity = $('#user_quantity').val();
        var total = (1200000 * time);
        if(user_quantity <= 5) {
            total += 200000 * user_quantity * time;
        } else {
            var new_quantity = user_quantity - 5;
            total += 200000 * 5 * time;
            total += 100000 * new_quantity * time;
        }
        $('.wrap-total-number').text(formatNumber(total));
    }

    $(document).on('click','.wrap-btn-payment', function (e) {
        $('.view_modal_payment').html('');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        var time = $('#time').val();
        var user_quantity = $('#user_quantity').val();
        data['time'] = time;
        data['user_quantity'] = user_quantity;
        
        $.post(admin_url+'software_extension/getView_modal_payment', data).done(function(response){
            $('.view_modal_payment').html(response);
            $('#modal_payment').modal({backdrop: 'static', keyboard: false});
        });
    });
</script>
</body>
</html>