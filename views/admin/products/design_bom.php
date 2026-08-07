<style type="text/css">
	.table-bom tr td {
		vertical-align: top !important;
	}

	/* .stage_items_primary  {
		pointer-events: none;
	} */
</style>
<?php echo form_open("admin/products/design_bom/$id/$bom_id/$actions", array('id'=>'add-category')); ?>
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
						<?php echo form_input('versions', (isset($_POST['versions']) ? $_POST['versions'] : (!empty($bom['versions']) ? $bom['versions'] : 'VS'.date('Y'))), 'placeholder="'.lang('tnh_versions').'" id="versions" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_bom_sample', 'bom_sample') ?>
						<input type="text" name="bom_sample" id="bom_sample" data-placeholder="<?= lang('tnh_bom_sample') ?>" class="bom_sample modal-select2" style="width: 100%;" value="">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group mtop35">
						<div class="checkbox checkbox-info">
							<input type="checkbox" checked id="use_version" name="use_version" value="1">
							<label for="use_version"><?= lang('tnh_use_version') ?></label>
						</div>
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
				<input type="hidden" name="product_id" id="product_id" class="form-control product_id" value="<?= $id ?>">
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
									<!-- <th style="width: 105px;" class="text-center"><?= ''//lang('tnh_vertical_print_size') ?></th> -->
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
            <a data-tnh="modal" class="tnh-modal hide click_design_bom"
               href=" <?= base_url() ?>admin/products/view_product/<?= $id ?>" data-toggle="modal"
               data-target="#myModal"></a>
			<button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    var lang_bom = <?= json_encode(array('tnh_element_name' => lang('tnh_element_name'), 'type' => lang('type'), 'choose' => lang('choose'))) ?>;
    var i = <?= !empty($count_i) ? $count_i : 0 ?>;
    var k = <?= !empty($count_k) ? $count_k : 0 ?>;
    var kR = <?= !empty($kR) ? $kR : 0 ?>;
    var bom_id = <?= $bom_id ?>;
    var type_design_bom = <?= json_encode(type_design_bom($products['type_products'] == 'products' ? 'all' : 'not_all')) ?>;
    var category_product = '<?= recursiveCategoryProducts() ?>';
    var category_material = '<?= recursiveCategoryItems() ?>';
    var list_stages = '<?= $list_stages ?>';
    var list_stages_primary = '<?= $list_stages_primary ?>';

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
    				// selectAjax($('select#items_'+ n +''), false, 'admin/products/searchSemiProducts', 'products/searchSemiProducts');
    			} else if (type_design_bom_current == "semi_products_outside") {
    				ajaxSelectParamsCallback('#items_'+ n +'', 'admin/products/searchSelect2SemiProductsOutside', $('#items_'+ n +'').val());
    			} else {
    				ajaxSelectParamsCallback('#items_'+ n +'', 'admin/items/searchSelect2Materials', $('#items_'+ n +'').val());
    				// selectAjax($('select#items_'+ n +''), false, 'admin/items/searchMaterials', 'items/searchMaterials');
    			}

    			json['items_'+n] = 'required';

    			$('#stage_'+n).selectpicker();
    			stageEdit = $('#stage_edit_'+n).val();
				selectAjax($('select#machines_' + n), false, 'admin/categories/searchMachines');
    			if (typeof stageEdit != "undefined")
    			{
    				$('#stage_'+n).val(stageEdit).selectpicker('refresh');
    			}
    		}

			$('select.category_product_search_bom').selectpicker();
			$('select.category_material_search_bom').selectpicker();

    		//handling replace
    		// console.log(kR);
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
    			$('#stage_replace'+n).selectpicker();
    			stageEdit = $('#stage_edit_replace'+n).val();
    			if (typeof stageEdit != "undefined")
    			{
    				$('#stage_replace'+n).val(stageEdit).selectpicker('refresh');
    			}
    		}
    		//end handling

    		$('.units').select2();
    		$('.units-replace').select2();
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
    	appValidateForm($('#add-category'), json, addBOM);

		ajaxSelectParamsCallback('#bom_sample', 'admin/products/searchBOMSample', 0, false, true);
		
		$('#bom_sample').change(function(event) {
			bom_sample = $(this).val();
			if (bom_sample) {
				var dataPOST = {};
				dataPOST[csrfData['token_name']] = csrfData['hash'];
				dataPOST['bom_sample'] = bom_sample;
				dataPOST['type_products'] = '<?= $products['type_products'] ?>';

				$.ajax({
					type: "POST",
					url: site.base_url+'admin/products/getDataBomSample',
					data: dataPOST,
					dataType: "json",
					success: function (response) {
						$('.table-bom tbody').html(response.html_BOM);
						i = intVal(response.count_i);
    					k = intVal(response.count_k);

						for (n = 0; n <= k; n++)
						{
							$('#type_design_bom_'+n).selectpicker();

							json['type_design_bom_'+n] = 'required';
							type_design_bom_current = $('#type_design_bom_'+n).val();
							if (type_design_bom_current == "semi_products") {
								ajaxSelectParamsCallback('#items_'+ n +'', 'admin/products/searchSelect2SemiProducts', $('#items_'+ n +'').val());
								// selectAjax($('select#items_'+ n +''), false, 'admin/products/searchSemiProducts', 'products/searchSemiProducts');
							} else if (type_design_bom_current == "semi_products_outside") {
								ajaxSelectParamsCallback('#items_'+ n +'', 'admin/products/searchSelect2SemiProductsOutside', $('#items_'+ n +'').val());
							} else {
								ajaxSelectParamsCallback('#items_'+ n +'', 'admin/items/searchSelect2Materials', $('#items_'+ n +'').val());
								// selectAjax($('select#items_'+ n +''), false, 'admin/items/searchMaterials', 'items/searchMaterials');
							}

							json['items_'+n] = 'required';

							$('#stage_'+n).selectpicker();
							stageEdit = $('#stage_edit_'+n).val();
							selectAjax($('select#machines_' + n), false, 'admin/categories/searchMachines');
							if (typeof stageEdit != "undefined")
							{
								$('#stage_'+n).val(stageEdit).selectpicker('refresh');
							}
						}

						$('select.category_product_search_bom').selectpicker();
						$('select.category_material_search_bom').selectpicker();
						$('.units').select2();
					}
				});
			} else {
				$('.tr-child-item').remove();
			}
		});

	    function addBOM(form) {
	    	$('.add').attr('disabled', 'disabled');
	        product_id = $('#product_id').val();
	        if (!product_id) {
	            alert_float('danger', 'errors');
	            $('.add').removeAttr('disabled', 'disabled');
	            return;
	        }
	        var url = form.action;
	        var data = $(form).serialize();
	        $.ajax({
	        	// url: site.base_url+'admin/products/design_bom/'+product_id+'/'+bom_id,
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
                    $(".click_design_bom")[0].click();
	        		// $('.modal-dialog .close').trigger('click');
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
