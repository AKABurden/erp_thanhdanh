<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<?php if(has_permission('quotes','','view') || has_permission('quotes','','view_own')) { ?>
		<?php render_datatable(array(
            _l('date'),
            _l('tnh_reference_no_quote'),
            _l('tnh_pre_reference_no_quote'),
            _l('customers'),
            _l('tnh_validity'),
            _l('tnh_grand_total'),
            _l('tnh_note_internal'),
            _l('tnh_created_by'),
            _l('tnh_status'),
            _l('tnh_status_order'),
            _l('tnh_status_contract')
        ),'estimates-single-client'); ?>
	<?php } ?>
<?php } ?>
