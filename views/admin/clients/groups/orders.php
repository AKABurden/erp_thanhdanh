<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<?php if(has_permission('orders','','create')){ ?>
		<a href="<?php echo admin_url('orders/add?customer_id='.$client->userid); ?>" class="btn btn-info mbot15<?php if($client->active == 0){echo ' disabled';} ?>">
			<?php echo _l('create_add_new'); ?>
		</a>
	<?php } ?>
	<?php if(has_permission('orders','','view') || has_permission('orders','','view_own')) { ?>
		<?php render_datatable(array(
            _l('date'),
            _l('tnh_reference_orders'),
            _l('customers'),
            _l('tnh_address_delivery'),
            _l('tnh_grand_total'),
            _l('tnh_created_by'),
            _l('tnh_agree'),
            _l('tnh_count_delivery'),
            _l('tnh_status'),
            _l('tnh_type_bills'),
            _l('note'),
        ),'orders_singer_client'); ?>
	<?php } ?>
<?php } ?>
