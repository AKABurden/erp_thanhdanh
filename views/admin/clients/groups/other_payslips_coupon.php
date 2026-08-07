<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<?php render_datatable(array(
        _l('ch_code_number'),
        _l('ch_date_p'),
        _l('ch_type_objects'),
        _l('ch_objects'),
        _l('ch_type_of_document'),
        _l('ch_code_p'),
        _l('ch_HTTT'),
        _l('ticket_dt_status'),
        _l('expense_add_edit_amount'),
        _l('ch_addedfrom'),
        _l('ch_note_pay_slips')
    ),'other-payslips-coupon-single-client'); ?>
<?php } ?>
