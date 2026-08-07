<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/orders/gift/'.$id, array('id' => 'gift')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        	<?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
	<div class="content ae-content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('info') ?></h3>
					</div>
					<div class="panel-body">
						<div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($order['date']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_orders') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= ($order['reference_no']) ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('customers') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $order['customer_name'] ?></span>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="tabset">
				  	<!-- Tab 1 -->
				  	<input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
				  	<label for="tab1"><i class="fa fa-gift"></i> <?= lang('tnh_gift') ?></label>

				  	<div class="tab-panels">
				    	<section id="marzen" class="tab-panel">
				    		<table class="tnh-table table-hover table-bordered">
				    			<thead>
				    				<tr>
				    					<th class="text-center" style="width: 80px;"><?= lang('tnh_numbers') ?></th>
				    					<th><?= lang('tnh_gift_name') ?></th>
				    					<th><?= lang('tnh_time_application') ?></th>
				    					<th class="text-center" style="width: 100px;"><?= lang('actions') ?></th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php foreach ($gift as $key => $value): ?>
				    					<tr>
				    						<td class="text-center"><?= ++$key ?></td>
				    						<td><?= $value['name'] ?></td>
				    						<td><?= _d($value['date_active_start']) ?> - <?= _d($value['date_active_end']) ?></td>
				    						<td class="text-center">
				    							<?php
				    							$checked = '';
				    							foreach ($orderGift as $k => $val) {
				    								if ($val['gift_id'] == $value['id']) {
				    									$checked = "checked";
				    									break;
				    								}
				    							}
				    							?>
				    							<div class="checkbox checkbox-primary" style="margin-bottom: 0;">
						                            <input type="checkbox" name="chosen_promotions[<?= $value['id'] ?>]" id="chosen_promotions[<?= $value['id'] ?>]" <?= $checked ?> class="chosen_promotions" value="<?= $value['id'] ?>">
						                            <label for="chosen_promotions[<?= $value['id'] ?>]"><?= lang('choose') ?></label>
						                        </div>
				    						</td>
				    					</tr>
				    				<?php endforeach ?>
				    			</tbody>
				    		</table>

				    		<table id="tb-items-gift" class="tnh-table table-hover table-bordered mtop10">
				    			<thead>
				    				<tr>
				    					<th class="text-danger" colspan="6"><?= lang('tnh_items_gift') ?></th>
				    				</tr>
				    				<tr>
				    					<th class="text-center" style="width: 80px;"><?= lang('tnh_numbers') ?></th>
				    					<th><?= lang('tnh_gift_name') ?></th>
				    					<th><?= lang('tnh_item_name') ?></th>
				    					<th><?= lang('tnh_type') ?></th>
				    					<th class="text-center"><?= lang('quantity') ?></th>
				    					<th class="text-center" style="width: 100px;"><?= lang('actions') ?></th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    			</tbody>
				    		</table>
				  		</section>
				  	</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="save" id="" class="form-control" value="1">
				<button type="submit" class="btn btn-info only-save customer-form-submiter add">
					<?php echo _l( 'submit'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
	var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var itemsGift = <?= !empty($orderGift) ? json_encode($orderGift) : '{}' ?>;
	function loadItemsGift()
	{
		$.each(itemsGift, function(index, el) {
			promotion_id = el.gift_id;

			$.ajax({
				url: site.base_url+'admin/orders/getGiftItems',
				type: 'POST',
				dataType: 'json',
				data: {
					csrf_token_name: hash,
					promotion_id: promotion_id,
					order_id: '<?= $id ?>'
				},
			})
			.done(function(data) {
				if (data) {
					console.log(data);
					$('#tb-items-gift tbody').append(data.htmlBody);
				} else {
					// $('#tb-items-gift tbody').remove();
				}
			});
		});
	}

	$(document).ready(function() {
		$(document).on('click', '.chosen_promotions', function(event) {
			promotion_id = $(this).val();
			$('#tb-items-gift tbody tr[data-promotion-id="'+promotion_id+'"]').remove();
			if ($(this).is(':checked')) {
				$.ajax({
					url: site.base_url+'admin/orders/getGiftItems',
					type: 'POST',
					dataType: 'json',
					data: {
						csrf_token_name: hash,
						promotion_id: promotion_id,
						order_id: '<?= $id ?>'
					},
				})
				.done(function(data) {
					if (data) {
						console.log(data);
						$('#tb-items-gift tbody').append(data.htmlBody);
					} else {
						// $('#tb-items-gift tbody').remove();
					}
				});
        	}
		});

		loadItemsGift();
		appValidateForm($('#gift'), false, db);

		function db(form) {
	    	$('.add').attr('disabled', 'disabled');
	        for (var i = 0; i < tinymce.editors.length; i++) {
	            tinymce.editors[i].save();
	        }
	        var url = form.action;
	        var form = $(form),
	            formData = new FormData(),
	            formParams = form.serializeArray();

	        $.each(form.find('input[type="file"]'), function(i, tag) {
	            $.each($(tag)[0].files, function(i, file) {
	                formData.append(tag.name, file);
	            });
	        });
	        $.each(formParams, function(i, val) {
	            formData.append(val.name, val.value);
	        });

	        $.ajax({
	        	url : url,
	        	type : 'POST',
	        	dataType: 'JSON',
	            cache : false,
	            contentType : false,
	            processData : false,
	        	data: formData,
	        })
	        .done(function(data) {
	        	if (data.result) {
	        		alert_float('success', data.message);
	              	window.location.href = site.base_url+'admin/orders';
	        	} else {
	        		alert_float('danger', data.message);
	        		$('.add').removeAttr('disabled', 'disabled');
	        	}
	        })
	        .fail(function() {
	            alert_float('danger', lang_core['errors']);
	        	$('.add').removeAttr('disabled', 'disabled');
	        });
	        return false;
	    }
	});
</script>
