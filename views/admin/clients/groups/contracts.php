<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<?php render_datatable(array(
        _l('code_contract_sales'),
        _l('clients'),
        _l('title_contract_sales'),
        _l('Mã báo giá'),
        _l('als_staff'),
        _l('contract_value'),
        _l('date_start'),
        _l('date_end')
    ),'contracts-single-client'); ?>
<?php } ?>
