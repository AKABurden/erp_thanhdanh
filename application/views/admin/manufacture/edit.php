<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/manufacture/edit/' . $id, array('id' => 'add-productions-orders')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
		<div class="panel-body _buttons">
			<span class="bold uppercase fsize18 H_title"><?= $title ?></span>
			<?= $this->load->view('admin/breadcrumb') ?>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('info') ?></h3>
					</div>
					<div class="panel-body">
						<table class="tnh-tb table-bordered table-hover">
							<tbody>
								<tr>
									<td style="width: 15%;">
										<?= lang('Số lệnh', 'reference_no') ?>
									</td>
									<td style="width: 35%;">
										<div class="form-group">
											<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $manufactures['reference_no'] ?>" readonly="" aria-invalid="false">
										</div>
									</td>
									<td style="width: 15%;"><?= lang('date', 'date') ?></td>
									<td style="width: 35%;">
										<?= form_input('date', set_value('date') ? set_value('date') : _d($manufactures['date']), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('Lệnh SX chi tiết', 'id_production_detail') ?></td>
									<td>
										<?php $value = !empty($manufactures['id_production_detail']) ? $manufactures['id_production_detail'] : '' ?>
										<?php echo render_select('id_production_detail', !empty($productions_detail) ? $productions_detail : [], ['id', 'reference_no', 'name_product'], '', $value) ?>
									</td>
									<td><?= lang('note', 'note') ?></td>
									<td>
										<textarea name="note" id="note" class="form-control" rows="3"><?= $manufactures['note'] ?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('cong_info_items') ?></h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-8">
								<div class="form-group">
									<?= lang('Nguyên phụ liệu BOM', 'items') ?>
									<input type="text" name="" id="items" class="items" style="width: 100%;" data-placeholder="<?= lang('Nguyên phụ liệu BOM') ?>" value="">
								</div>
							</div>
						</div>
						<div class="tb-height">
							<table id="tb-productions-orders" class="dt-tnh tnh-table table table-bordered table-hover dataTable dont-responsive-table" style="width: 100%;">
								<thead>
									<tr>
                                        <th class="text-center" style="width: 50px;">
                                            <?= lang('tnh_numbers') ?>
                                        </th>
                                        <th class="text-center"><?= lang('tnh_material_code') ?></th>
                                        <th class="text-center"><?= lang('tnh_material_name') ?></th>
                                        <th class="text-center"><?= lang('tnh_height_cm') ?></th>
                                        <th class="text-center"><?= lang('tnh_total_height_cm') ?></th>
                                        <th class="text-center"><?= lang('tnh_number_paper') ?></th>
                                        <th class="text-center"><?= lang('tnh_landscape_print_size') ?></th>
                                        <th class="text-center"><?= lang('tnh_number_children_size') ?></th>
                                        <th class="text-center"><?= lang('tnh_exchange_value') ?></th>
                                        <th class="text-center"><?= lang('tnh_paper_exchange_unit_paper') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('note') ?></th>
                                        <th class="text-center" style="width: 80px;" class="text-center"><?= lang('actions') ?></th>
                                    </tr>
								</thead>
								<tbody class="tbody-items">
									<?php
									$warehouses = $this->manufacture_model->getWarehouse();
									$manufactures_items = $this->manufacture_model->getManufacturesItems($id);
									$counter = 0;
									$stt = 0;
									?>
									<?php if (!empty($manufactures_items)) : ?>
										<?php foreach ($manufactures_items as $key => $value) : ?>
											<?php

											$type_item = $value['type_items'];
											$items_id = $value['item_id'];
											// $info = $this->items_model->rowItems($value['item_id']);
											$images = base_url('assets/images/tnh/no_image.png');
											$height = 0;

											if ($type_item == "products") {
												$info = $this->products_model->rowProduct($items_id);
												$unit = $this->unit_model->rowUnit($info['unit_id']);
												if (!empty($info['images'])) {
													$images = base_url('uploads/products/' . $info['images']);
												}
												$model = $info['model'];
											} elseif ($type_item == "items") {
												$info = $this->items_model->rowItems($items_id);
												$unit = $this->unit_model->rowUnit($info['unit']);
												if (!empty($info['avatar'])) {
													$images = base_url($info['avatar']);
												}
											} elseif ($type_item == "materials") {
												$info = $this->items_model->rowMaterial($items_id);
												$unit = $this->unit_model->rowUnit($info['unit_id']);
												if (!empty($info['images'])) {
													$images = base_url('uploads/materials/' . $info['images']);
												}
												$height = $info['height'];
											} elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
												$info = $this->tools_supplies_model->rowToolsSupplies($items_id);
												$unit = $this->unit_model->rowUnit($info['unit_id']);
												if (!empty($info['avatar'])) {
													$images = base_url('uploads/tools_supplies/' . $info['images']);
												}
											}
											$tdNumber = '<td class="text-center stt">
												' . ++$stt . '
											</td>';
											$tdCode = '<td>
												<input type="hidden" name="manufacture_item_id[' . $counter . ']" class="form-control" value="'.$value['id'].'">
												<input type="hidden" name="counter[' . $counter . ']" id="counter_' . $counter . '" class="form-control counter" value="' . $counter . '">
												<input type="hidden" name="manufactures_item_id[' . $counter . ']" class="form-control" value="' . $value['id'] . '">
												<input type="hidden" name="items_id[' . $counter . ']" id="items_' . $counter . '" class="items_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $value['item_id'] . '__' . $type_item . '">'.$info['code'].'
											</td>';

											$tdName = '<td><div class="td-item-name">' . $info['name'] . '</div></td>';

											$tdLandscapePrintSize = '<td class="tdLandscapePrintSize text-center">'.formatNumber($value['landscape_print_size']).'</td>';
											$tdVerticalPrintSize = '<td class="tdVerticalPrintSize text-center">'.formatNumber($value['vertical_print_size']).'</td>';
											$tdNumberChildrenSize = '<td class="tdNumberChildrenSize text-center">'.formatNumber($value['number_children_size']).'</td>';
											$tdExchangeValue = '<td class="tdExchangeValue text-center">'.formatNumber($value['quantity_single']).'</td>';
											$tdPaperExchange = '<td class="tdPaperExchange text-center">'.formatNumber($value['paper_exchange']).'</td>';
											$quantity_compensation = $value['quantity_compensation'] + $value['quantity_compensation_sm'];
											$tdQuantityCompensation = '<td class="tdQuantityCompensation text-center">'.formatNumber($quantity_compensation).'</td>';

											$tdQuantityNeed = '<td class="tdQuantityNeed text-center">'.formatNumber($value['quantity'] + $quantity_compensation, 0).'</td>';
											$height = $info['height'];
											$total_height = $height * ($quantity_compensation + $value['quantity']);
											$tdHeight = '<td class="tdHeight text-center">'.$height.'</td>';
											$tdTotalHeight = '<td class="tdTotalHeight text-center">'.formatNumber($total_height).'</td>';
										
											$tdNote = '<td>
													<div class="td-note"><textarea name="note_items[' . $counter . ']" class="form-control" rows="3">' . $value['note_item'] . '</textarea></div>
												</td>';

											$tdActions = '<td>
													<div class="text-center"><i onclick="removeOrdersItems(this, ' . $counter . ')" class="fa fa-remove btn btn-danger remove-row"></i></div>
												</td>';
											?>
											<tr>
												<?= $tdNumber ?>
												<?= $tdCode ?>
												<?= $tdName ?>
												<?= $tdHeight ?>
												<?= $tdTotalHeight ?>
												<?= $tdQuantityNeed ?>
												<?= $tdLandscapePrintSize ?>
												<?= $tdNumberChildrenSize ?>
												<?= $tdExchangeValue ?>
												<?= $tdPaperExchange ?>
												<?= ''//$tdQuantityCompensation ?>
												<?= $tdNote ?>
												<?= $tdActions ?>
											</tr>
											<?php
											$counter++;
											?>
										<?php endforeach; ?>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="add" id="" class="form-control" value="1">
				<button type="submit" class="btn btn-info only-save customer-form-submiter add">
					<?php echo _l('submit'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<script type="text/javascript">
	var lang_core = <?= json_encode(['Lot' => lang('Lot'), 'ch_date_of_manufacture' => lang('ch_date_of_manufacture'), 'ch_items_dateed' => lang('ch_items_dateed')]) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 1;
	var counter = <?= $counter ?>;
	var count_errors = 0;
	var arr_productions_plan_id = [];
</script>

<script type="text/javascript" src="<?= js('manufacture_new_1.js?vs=2.2') ?>"></script>
<script>
	// $('body').on('change', '#id_production_detail', function(e) {
	// 	var trTable = $('#tb-productions-orders').find('tbody').find('tr');
	// 	$.each(trTable, function(index, value) {
	// 		$(value).find('.remove-row').trigger('click');
	// 	})
	// 	ajaxSelectItemsCallBack($('#items'), 'admin/manufacture/searchProductAndGoods', 0, {
	// 		id_production_detail: $('#id_production_detail').val()
	// 	});
	// })
</script>