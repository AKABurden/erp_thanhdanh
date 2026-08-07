<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(false); ?>
<style>
    .wrap-container-security {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        margin: 50px 0;
    }
</style>
<div class="wrap-container-security">
    <div class="col-md-3">
        <?php echo render_input('security_code','Nhập mã bảo vệ','','password'); ?>
        <input type="hidden" class="time" value="<?= $time ?>">
    </div>
    <div class="clearfix"></div>
    <div style="padding: 10px 0;">
        Gia hạn đến ngày: <?= $dateTime ?>
    </div>
    <div>
        <a class="btn btn-info submit-code">Xác nhận</a>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).on('click','.submit-code', function (e) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        var time = $('.time').val();
        var security_code = $('#security_code').val();
        data['security_code'] = security_code;

        $.post(admin_url+'software_extension/updateDateSoftware_update/'+time, data).done(function(response){
            if(response == 0) {
                alert_float('danger','Sai mã bảo vệ!');
            }
            else {
                window.location.href = admin_url + "settings?group=software_extension";
            }
        });
    });
</script>
</body>
</html>