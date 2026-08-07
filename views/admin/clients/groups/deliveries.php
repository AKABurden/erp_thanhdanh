<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<?php render_datatable(array(
        _l('date'),
        _l('tnh_reference_deliveries'),
        _l('customers'),
        _l('tnh_address_delivery'),
        _l('tnh_reference_orders'),
        _l('tnh_grand_total'),
        _l('tnh_created_by'),
        _l('tnh_status'),
        _l('note')
    ),'deliveries-single-client'); ?>
<?php } ?>
