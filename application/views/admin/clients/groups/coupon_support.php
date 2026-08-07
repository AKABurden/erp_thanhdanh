<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
	form#form_add #s2id_customer_id a.select2-choice {
		background: #f3eeee;
		pointer-events: none;
	}
</style>
<a class="btn btn-info mbot20" onclick="add_coupon_support(); return false;">
    <?php echo _l('create_add_new'); ?>
</a>
<?php if(isset($client)){ ?>
	<?php render_datatable(array(
        _l('#'),
        _l('ch_code_number'),
        _l('coupon_support_date'),
        _l('method'),
        _l('tnh_employees_charge'),
        _l('note')
    ),'coupon-support-single-client'); ?>
<?php } ?>
<div class="view_add"></div>
<script>
var tAPI;
function add_coupon_support() {
    $('.view_add').html('');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'coupon_support/getView_add', data).done(function(response){
        $('.view_add').html(response);
        var opt = {
            format: 'd/m/Y H:i',
            timepicker: true,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 0,
        };
        $('#appointment_date').datetimepicker(opt);

        $('#customer_id').val('customers__'+<?= $client->userid ?>);
        $('#customer_id').parents('.form-group').css({'cursor':'no-drop'});
        $('#add_coupon_support').modal({backdrop: 'static', keyboard: false});
    });
}
</script>