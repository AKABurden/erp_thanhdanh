<style type="text/css">
	.table-bom tr td {
		vertical-align: top !important;
	}
</style>
<?php echo form_open("admin/products/add_bom/$id/$actions", array('id'=>'add-bom')); ?>
<div class="modal-dialog modal-lg" style="width: 95%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_design_bom') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_versions', 'versions') ?>
						<?php echo form_input('versions', (isset($_POST['versions']) ? $_POST['versions'] : (!empty($bom['versions']) ? $bom['versions'] : '')), 'placeholder="'.lang('tnh_versions').'" id="versions" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-4 hide">
					<div class="form-group">
						<?= lang('date_start', 'date_start') ?>
						<?php echo form_input('date_start', (isset($_POST['date_start']) ? $_POST['date_start'] : (!empty($bom['date_start']) ? _d($bom['date_start']) : '')), 'placeholder="'.lang('date_start').'" id="date_start" class="form-control input-tip datepicker"'); ?>
					</div>
				</div>
				<div class="col-md-4 hide">
					<div class="form-group">
						<?= lang('date_end', 'date_end') ?>
						<?php echo form_input('date_end', (isset($_POST['date_end']) ? $_POST['date_end'] : (!empty($bom['date_end']) ? _d($bom['date_end']) : '')), 'placeholder="'.lang('date_end').'" id="date_end" class="form-control input-tip datepicker"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="">
						<table class="table table-hover table-bordered table-condensed table-bom" style="margin: 0;">
							<thead>
								<tr style="background: #5cb0d5;">
									<th style="width: 50px;">
										<div class="text-center">
											<button type="button" class="btn btn-warning btn-icon btn-add-element hide"><i class="fa fa-plus"></i></button>
										</div>
									</th>
									<th style="width: 400px;" colspan="2" class="text-center"><?= lang('tnh_element_name') ?><span style="color: red;">*</span></th>
									<th style="width: 100px;" class="text-center"><?= lang('unit') ?><span style="color: red;">*</span></th>
									<th style="width: 105px;" class="text-center"><?= lang('tnh_landscape_print_size') ?></th>
									<th style="width: 105px;" class="text-center"><?= lang('tnh_number_children_size') ?></th>
									<th style="width: 105px;" class="text-center"><?= lang('tnh_exchange_value') ?></th>
									<th style="width: 105px;" class="text-center"><?= lang('tnh_paper_exchange') ?></th>
									<th style="width: 105px;" class="text-center"><?= lang('tnh_quantity_compensation') ?></th>
									<th style="width: 180px;" class="text-center"><?= lang('tnh_stage') ?></th>
									<th style="width: 40px; text-align: center;" class="text-center"><i class="fa fa-trash-o"></i></th>
								</tr>
							</thead>
							<tbody>
								<?= !empty($html_BOM) ? $html_BOM : '' ?>
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    var lang_bom = <?= json_encode(array('tnh_element_name' => lang('tnh_element_name'), 'type' => lang('type'), 'choose' => lang('choose'))) ?>;
    var i = <?= !empty($count_i) ? $count_i : 0 ?>;
    var k = <?= !empty($count_k) ? $count_k : 0 ?>;
    var kR = <?= !empty($kR) ? $kR : 0 ?>;
    var bom_id = 0;
    var type_design_bom = <?= json_encode(type_design_bom('all')) ?>;
	var category_product = '<?= recursiveCategoryProducts() ?>';
    var category_material = '<?= recursiveCategoryItems() ?>';
    var list_stages = '<?= $list_stages ?>';
    var list_stages_primary = '<?= $list_stages_primary ?>';
	var vProduct = '';
    json['versions'] = 'required';

    <?php if (!empty($html_BOM)): ?>
    	$(document).ready(function() {
    		for (n = 0; n <= k; n++)
    		{
    			$('#type_design_bom_'+n).selectpicker();
    			json['type_design_bom_'+n] = 'required';
    			type_design_bom_current = $('#type_design_bom_'+n).val();
    			if (type_design_bom_current == "semi_products") {
    				ajaxSelectParamsCallback('#items_'+ n +'', 'admin/products/searchSelect2SemiProducts', $('#items_'+ n +'').val());
    			} else if (type_design_bom_current == "semi_products_outside") {
    				ajaxSelectParamsCallback('#items_'+ n +'', 'admin/products/searchSelect2SemiProductsOutside', $('#items_'+ n +'').val());
    			} else {
    				ajaxSelectParamsCallback('#items_'+ n +'', 'admin/items/searchSelect2Materials', $('#items_'+ n +'').val());
    			}
    			json['items_'+n] = 'required';
				$('#stage_'+n).selectpicker();
				stageEdit = $('#stage_edit_'+n).val();
				if (typeof stageEdit != "undefined") {
    				$('#stage_'+n).val(stageEdit).selectpicker('refresh');
    			}
    		}

    		//handling replace
    		for (n = 0; n <= kR; n++)
    		{
    			trCurr = $('#items_replace_'+ n +'').closest('tr');
    			typeCurr = trCurr.find('input.typeMaterial').val();
    			if (typeCurr == "semi_products") {
    				ajaxSelectParamsCallback('#items_replace_'+ n +'', 'admin/products/searchSelect2SemiProducts', $('#items_replace_'+ n +'').val());
    			} else if (typeCurr == "semi_products_outside") {
    				ajaxSelectParamsCallback('#items_replace_'+ n +'', 'admin/products/searchSelect2SemiProductsOutside', $('#items_replace_'+ n +'').val());
    			} else {
    				ajaxSelectParamsCallback('#items_replace_'+ n +'', 'admin/items/searchSelect2Materials', $('#items_replace_'+ n +'').val());
    			}
    			// ajaxSelectParamsCallback('#items_replace_'+ n +'', 'admin/items/searchSelect2Materials', $('#items_replace_'+ n +'').val());
    		}
    		//end handling

    		$('.units').select2();
    		$('.units-replace').select2();
			$('select.category_product_search_bom').selectpicker();
			$('select.category_material_search_bom').selectpicker();
    	});
	<?php else: ?>
		for(iCT = 0; iCT <= 1; iCT++) {
			$('.btn-add-element').click();
			tr_cur = $('.i[value="'+iCT+'"]').closest('tr');
			strCT = '';

			iTP = 0;
			if (iCT == 0) {
				strCT = 'NPL chính';
				iTP = 1;
			} else if (iCT == 1) {
				strCT = 'NPL phụ';
				iTP = 2;
			}
			$(tr_cur).find('.element_name').val(strCT);
			$(tr_cur).find('.txt-type-element').html(strCT);
			$(tr_cur).find('.type_element').val(iTP);
		}
    <?php endif ?>
    $(document).ready(function() {
    	init_datepicker();
    	appValidateForm($('#add-bom'), json, addBOM);
	    function addBOM(form) {
	    	$('.add').attr('disabled', 'disabled');
	    	var url = form.action;
	        var data = $(form).serialize();
	        $.ajax({
	        	url: url,
	        	type: 'POST',
	        	dataType: 'JSON',
	        	data: data,
	        })
	        .done(function(data) {
	        	if (data.result) {
	        		alert_float('success', data.message);
	        		if (typeof oTable != 'undefined') {
	        			oTable.draw();
	        		}
	        		$('.modal-dialog .close').trigger('click');
	        	} else {
	        		alert_float('danger', data.message);
	        		$('.add').removeAttr('disabled', 'disabled');
	        	}
	        })
	        .fail(function() {
	        	alert_float('danger', 'error');
	            $('.add').removeAttr('disabled', 'disabled');
	        });
	        return false;
	    }
    });
</script>
