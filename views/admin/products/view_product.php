<div class="modal-dialog modal-lg" style="width: 80%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= $product['code'] ?></h4>
		</div>
		<div class="modal-body" style="padding-top: 0;">
			<div class="row">
				<div class="">
					<div role="tabpanel">
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active">
								<a href="#info" aria-controls="info" role="tab" data-toggle="tab"><?= lang('info') ?></a>
							</li>
							<?php if ($product['type_products'] != 'semi_products_outside') : ?>
								<li role="presentation" class="">
									<a href="#BOM" aria-controls="BOM" role="tab" data-toggle="tab"><?= lang('BOM') ?></a>
								</li>
								<li role="presentation">
									<a href="#tab-stages" aria-controls="tab-stages" role="tab" data-toggle="tab"><?= lang('stages') ?></a>
								</li>
							<?php endif ?>
						</ul>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="info">
								<div class="col-md-12">
									<a class="tnh-modal pull-right" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/products/edit_product/' . $id) ?>"><i class="fa fa-pencil width-icon-actions"></i> <?= lang('edit') ?></a>
								</div>
								<div class="lead-view" id="leadViewWrapper">
									<div class="col-md-4 col-xs-4 lead-information-col mbot10">
										<div class="wap-content second">
											<span class="bold font-medium-xs mbot15">
												<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_images_represent') ?>: </span>
												<?php $images = ($product['images'] != null) ? base_url("uploads/products/" . $product['images']) : base_url("assets/images/preview-not-available.jpg"); ?>
												<div class="preview_image" style="width: auto;">
													<div class="display-block contract-attachment-wrapper img">
														<div style="width:45px;">
															<a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
																<div class="">
																	<img src="<?= $images ?>" style="border-radius: 50%" />
																</div>
															</a>
														</div>
													</div>
												</div>
											</span>
										</div>
										<div class="wap-content firt">
											<span class="bold font-medium-xs mbot15">
												<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_images_multiple') ?>: </span>
												<span class="bold font-medium-xs lead-name">
													<?php if (!empty($product['images_multiple'])) : ?>
														<div class="preview_image" style="width: auto; display: flex">
															<?php foreach (explode('||', $product['images_multiple']) as $key => $value) : ?>
																<?php $images_multiple = !empty($value) ? pathProduct($value) : base_url("assets/images/preview-not-available.jpg"); ?>
																<div class="display-block contract-attachment-wrapper img">
																	<div style="width:45px;">
																		<a href="<?= $images_multiple ?>" data-lightbox="customer-profile" class="display-block mbot5">
																			<div class="">
																				<img src="<?= $images_multiple ?>" style="border-radius: 50%" />
																			</div>
																		</a>
																	</div>
																</div>
															<?php endforeach ?>
														</div>
													<?php endif ?>
												</span>
											</span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('category') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['category_name'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_branch') ?>: </span>
											<span class="bold font-medium-xs lead-name">
												<?php
													$dtBranch = $this->site_model->getBranchById($product['id_branch']);
													echo $dtBranch['name'];
												?>
											</span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_species') ?>: </span>
											<span class="bold font-medium-xs lead-name">
												<?php
													$dtSpecies = $this->species_model->getSpeciesById($product['species']);
													if (!empty($dtSpecies)) {
														echo $dtSpecies['name'];
													}
												?>
											</span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_type_products') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= lang($product['type_products']) ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_type_print') ?>: </span>
											<span class="bold font-medium-xs mbot15">
												<?php
													$dtTypePrint = $this->products_model->getTypePrintById($product['type_print']);
													if (!empty($dtTypePrint)) {
														echo $dtTypePrint['name'];
													} 
												?>
											</span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_customer') ?>: </span>
											<span class="bold font-medium-xs lead-name">
												<?php
													$dtCusmoter = $this->site_model->rowCustomer($product['customer']);
													if (!empty($dtCusmoter)) {
														echo $dtCusmoter['company'];
													}
												?>
											</span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_product_code') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['code'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_code_bom') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['code_bom'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_product_name') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['name'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_product_code_customer') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['product_code_customer'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_product_name_customer') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['product_name_customer'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_sample_cover_code') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['sample_cover_code'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_mold_code') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['mold_code'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('colors') ?>: </span>
											<span class="bold font-medium-xs mbot15">
												<?php foreach ($colors as $key => $value) : ?>
													<?= $value['color_name'] ?>
													<?php if ($key != (count($colors) - 1)) : ?>
														|
													<?php endif ?>
												<?php endforeach ?>
											</span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_mode') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['mode'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_brand') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['brand'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_price_import') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= formatNumber($product['price_import']) ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_price_sell') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= formatNumber($product['price_sell']) ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_quantity_minimum') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= formatNumber($product['quantity_minimum']) ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_quantity_max') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= formatNumber($product['quantity_max']) ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_price_processing') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= formatNumber($product['price_processing']) ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_number_day') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= formatNumber($product['number_day']) ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('size') ?>: </span>
											<span class="bold font-medium-xs mbot15">
												<?php
													$dtSize = get_table_where('tblsize', ['id' => $product['size']], '', 'row_array');
													if (!empty($dtSize)) {
														echo $dtSize['name'];
													}
												?>
											</span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_color_formula') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['color_formula'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_ball_formula') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['ball_formula'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('note') ?>: </span>
											<span class="bold font-medium-xs mbot15"><?= $product['note'] ?></span>
										</div>
										<?php if (!empty($custom_fields)) : ?>
											<?php foreach ($custom_fields as $key => $value) : ?>
												<div class="wap-content <?= ($key % 2 == 0) ? 'firt' : 'second' ?>">
													<span class="text-muted lead-field-heading no-mtop"><?= $value['name'] ?>: </span>
													<span class="bold font-medium-xs mbot15"><?= get_custom_field_value($product['id'], $value['id'], $value['fieldto']) ?></span>
												</div>
											<?php endforeach ?>
										<?php endif ?>
									</div>
									<div class="col-md-4 col-xs-4 lead-information-col mbot10 hide-semi-product">
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_longs') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= formatNumber($product['longs']) ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_wide') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= formatNumber($product['wide']) ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_height') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= formatNumber($product['height']) ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_mode_product') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['mode_product'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_stage_mode') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['stage_mode'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_stage_standard') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['stage_standard'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_operating_gauge') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['operating_gauge'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quota_productivity_h') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['quota_productivity_h'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quota_power_consumption_h') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['quota_power_consumption_h'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quota_material_replace_t') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['quota_material_replace_t'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quota_depreciation_ts_date') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['quota_depreciation_ts_date'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quota_npl_consumption_one') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['quota_npl_consumption_one'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quota_time_change_one') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['quota_time_change_one'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_person_charge') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['person_charge'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_property_grant') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['property_grant'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_completion_standard') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['completion_standard'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_control_criteria') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['control_criteria'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_productivity_m_w_n') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['productivity_m_w_n'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quality_problem') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['quality_problem'] ?></span>
										</div>
										<div class="wap-content firt hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_loss') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['loss'] ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quantity_child_sheet') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= formatNumber($product['quantity_child_sheet']) ?></span>
										</div>
										<div class="wap-content second hide-semi-product">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quantity_sheet_bale') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= formatNumber($product['quantity_sheet_bale']) ?></span>
										</div>
                                        <div class="wap-content second hide-semi-product">
                                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('dt_quantity_child_molds') ?>: </span>
                                            <span class="bold font-medium-xs lead-name"><?= formatNumber($product['quantity_child_molds']) ?></span>
                                        </div>
										<div class="wap-content second hide-semi-product">
                                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quantity_child_molds_offset') ?>: </span>
                                            <span class="bold font-medium-xs lead-name"><?= formatNumber($product['quantity_child_molds_offset']) ?></span>
                                        </div>
										<div class="wap-content second hide-semi-product">
                                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_quantity_child_molds_flexo') ?>: </span>
                                            <span class="bold font-medium-xs lead-name"><?= formatNumber($product['quantity_child_molds_flexo']) ?></span>
                                        </div>
										<?php
											$dtCustomer = $this->site_model->rowCustomer($product['customer']);
											$is_separate_guest = $dtCustomer['is_separate_guest'];
											$styleGuest = '';
											if (empty($is_separate_guest)) {
												$styleGuest = 'style="display: none;"';
											}
										?>
										<div class="wap-content second div-separate-guest" <?= $styleGuest ?>>
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_color_size') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['color_size'] ?></span>
										</div>
										<div class="wap-content second div-separate-guest" <?= $styleGuest ?>>
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_gw') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['gw'] ?></span>
										</div>
										<div class="wap-content second div-separate-guest" <?= $styleGuest ?>>
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_carton_size') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['carton_size'] ?></span>
										</div>
									</div>
									<div class="col-md-4 col-xs-4 lead-information-col mbot10">
										<div class="wap-content firt">
												<span class="text-muted lead-field-heading no-mtop bold"><?= lang('columns') ?>: </span>
												<span class="bold font-medium-xs lead-name">
													<?php if(!empty($product)): ?>
														<?php
															$dtColumns = $this->products_model->getProductsColumnsMul($product['id']);
															if (!empty($dtColumns)) {
																foreach ($dtColumns as $key => $value) {
																	echo $value['code_columns'].', ';
																}
															}
															// $column = $this->columns_model->getColumnsById($product['columns_id']);
															// if (!empty($column)) {
															// 	echo $column['code'];
															// }
														?>
													<?php endif; ?>
												</span>
											</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_incident_record') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['incident_record'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_operating_procedure') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['operating_procedure'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_withdraw_check_procedure') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['withdraw_check_procedure'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_prevent_procedure') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['prevent_procedure'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_time_inventory') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['time_inventory'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_standard_colors') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['standard_colors'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_pp_check') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['pp_check'] ?></span>
										</div>
										<div class="wap-content firt hide">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_number_child_sue') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['number_child_sue'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_packing') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['packing'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_qr') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['qr'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_time_stock') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['time_stock'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_mode') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['mode'] ?></span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('unit') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $unit['unit'] ?></span>
										</div>
										<div class="wap-content firt">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_conversion_unit') ?>: </span>
											<span class="bold font-medium-xs lead-name">
												<?php
													$dtConversionUnit = $this->unit_model->rowUnit($product['conversion_unit']);
													echo $dtConversionUnit['unit'];
												?>
											</span>
										</div>
										<div class="wap-content second">
											<span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_conversion_quantity_unit') ?>: </span>
											<span class="bold font-medium-xs lead-name"><?= $product['conversion_quantity_unit'] ?></span>
										</div>
										<div class="wap-content firt hide">
											<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_exchange') ?>: </span>
											<table class="tnh-tb table-exchange table-bordered table-hover dataTable">
												<thead>
													<tr>
														<th style="width: 30px; text-align: center;">
															#
														</th>
														<th class="text-center"><?= lang('unit') ?></th>
														<th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
													</tr>
												</thead>
												<tbody>
													<?php if (!empty($exchanges)) : ?>
														<?php foreach ($exchanges as $key => $value) : ?>
															<tr>
																<td class="text-center"><?= ++$key ?></td>
																<td class="text-center"><?= $value['unit_name'] ?></td>
																<td class="text-center"><?= $value['number_exchange'] ?></td>
															</tr>
														<?php endforeach ?>
													<?php endif ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="col-md-6 pull-right mtop10">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
											</div>
											<div class="panel-body">
												<div class="col-md-6">
													<div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
													<div><?= lang('tnh_date_creted') ?>: <?= _dt($product['date_created']) ?></div>
												</div>
												<div class="col-md-6">
													<?php if (!empty($updated_by)) : ?>
														<div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
														<div><?= lang('tnh_date_updated') ?>: <?= _dt($product['date_updated']) ?></div>
													<?php endif ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="BOM">
								<div class="col-md-12" style="max-height: 500px; overflow: auto;">
									<?= $BOM ?>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="tab-stages">
								<div class="col-md-12" style="max-height: 500px; overflow: auto;">
									<?= $html_stages ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		// $('.cols').trigger('click');
		$('.tbbb').DataTable({
			"language": app.lang.datatables,
			"pageLength": app.options.tables_pagination_limit,
			// "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
		});

		<?php if ($product['type_products'] != "products"): ?>
			// $('.hide-semi-product').hide();
		<?php endif; ?>
	});
</script>