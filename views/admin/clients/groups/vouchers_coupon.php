<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<?php render_datatable(array(
        _l('ch_code_p'),
        _l('ch_date_p'),
        _l('client'),
        _l('cong_code_orders'),
        _l('staff_coupon'),
        _l('acs_sales_payment_modes_submenu'),
        _l('ch_total_total'),
        _l('ch_total_payment'),
        _l('status'),
        _l('note'),
    ),'vouchers-coupon-single-client'); ?>
<?php } ?>
