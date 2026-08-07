<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/business_plan/add', array('id'=>'add-business-plan')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
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
									<td>
										<?= lang('tnh_reference_business_plan', 'reference_no') ?>
									</td>
									<td>
										<div class="form-group">
											<!-- <div class="input-group"> -->
												<!-- <span title="<?= ''//lang('tnh_referesh') ?>" data-toggle="tooltip" class="input-group-addon btn btn-danger referesh-reference">
													<i class="fa fa-undo"></i>
												</span> -->
												<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= lang('auto') ?>" readonly="" aria-invalid="false">
											<!-- </div> -->
										</div>
									</td>
									<td><?= lang('date', 'date') ?></td>
									<td>
										<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_plan_name', 'plan_name') ?></td>
									<td>
										<input type="text" name="plan_name" placeholder="<?= lang('tnh_plan_name') ?>" class="form-control" id="plan_name" value="<?= !empty($order) ? 'Tạo sản xuất mẫu '.$order['reference_no'] : '' ?>">
									</td>
									<td><?= lang('departments', 'departments') ?></td>
									<td>
										<select name="departments" data-placeholder="<?= lang('departments') ?>" id="departments" class="tnh-select" required="required" style="width: 100%;">
											<option value=""></option>
											<?php foreach ($departments as $key => $value): ?>
												<option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
											<?php endforeach ?>
										</select>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_branch', 'id_branch') ?></td>
									<td>
										<select name="id_branch" id="id_branch" class="id_branch" required="required" style="width: 100%;"  data-placeholder="<?= lang('tnh_branch') ?>">
											<option value=""></option>
											<?php if(!empty($branch)): ?>
												<?php foreach($branch as $key => $value): ?>
													<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
												<?php endforeach; ?>
											<?php endif; ?>
										</select>
									</td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td><?= lang('note', 'note') ?></td>
									<td colspan="3">
										<textarea name="note" id="note" class="form-control" rows="3"></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="order_id" id="order_id" class="form-control" value="<?= !empty($order) ? $order['id'] : 0 ?>">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('cong_info_items') ?></h3>
					</div>
					<div class="panel-body">
						<div class="tb-height">
							<table id="tb-business-plan" class="dt-tnh table table-hover" style="width: 100%;">
								<thead>
									<tr>
										<th class="text-center" style="width: 30px;">
											<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
		                                </th>
		                                <th style="width: 140px;"><?= lang('tnh_product_code') ?></th>
		                                <th style="width: 50px;"><?= lang('tnh_images') ?></th>
		                                <th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
		                                <th style="width: 100px;"><?= lang('quantity') ?></th>
		                                <th style="width: 200px;"><?= lang('cong_shipment_date') ?></th>
		                                <th style="width: 150px;"><?= lang('note') ?></th>
		                                <th style="width: 50px;"><?= lang('actions') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php $counter = 0; ?>
									<?php if(!empty($order)): ?>
										<?php
											$this->db->select('
												tbl_order_items.*,
												tbl_products.code as item_code,
												tbl_products.name as item_name,
												tbl_products.images as images
											', false);
											$this->db->from('tbl_order_items');
											$this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
											$this->db->where('tbl_order_items.order_id', $order['id']);
											$this->db->where('tbl_order_items.type_item', 'products');
											$order_items = $this->db->get()->result_array();
											if (!empty($order_items)) {
												foreach ($order_items as $key => $value) {
													$images = base_url('assets/images/tnh/no_image.png');
													if (!empty($value['images'])) {
														$images = base_url('uploads/products/' . $value['images']);
													}

													$td1 = '<div class="stt text-center"></div>';
													$td2 = '
														<input type="hidden" name="counter[]" id="input" class="form-control" value="'.$counter.'">
														<input type="hidden" name="items_id[]" class="form-control items_id" value="'.$value['item_id'].'">
														<input type="hidden" name="order_item_id[]" class="form-control order_item_id" value="'.$value['id'].'">
														'.$value['item_code'].'
													';
													$td3 = '<div class="td-image">
																<div class="preview_image" style="width: auto;">
																	<div class="display-block contract-attachment-wrapper img">
																		<div style="width:45px;">
																			<a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">
																				<div class="">
																					<img src="'.$images.'" style="border-radius: 50%">
																				</div>
																			</a>
																		</div>
																	</div>
																</div>
														</div>';
													$td4 = '<div class="td-item-name">'.$value['item_name'].'</div>';
													$td5 = '<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" value="1"></div>';
													$td6 = '<div class="td-date">
														<div class="sub">
															<div class="sb">
																<div class="col-md-7" style="padding: 0px; padding-right: 5px;">
																	<input type="text" name="date_sub['.$counter.'][]" id="input" class="form-control datepicker date_sub" autocomplete="off" placeholder="'.lang('date').'" value="'.date('d/m/Y').'" style="width: 100%;" title=""></div>
																	<div class="col-md-4" style="padding: 0px;"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" name="quantity_sub['.$counter.'][]" id="input" class="form-control quantity_sub" value="1" title="">
																</div>
															</div>
														</div>
													</div>';
													$td7 = '<div class="td-note"><textarea name="note_items[]" id="note_items[]" class="form-control" rows="3"></textarea></div>';
													$td8 = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

													echo '<tr>
														<td>'.$td1.'</td>
														<td>'.$td2.'</td>
														<td>'.$td3.'</td>
														<td>'.$td4.'</td>
														<td>'.$td5.'</td>
														<td>'.$td6.'</td>
														<td>'.$td7.'</td>
														<td>'.$td8.'</td>
													</tr>';

													$counter++;
												}
											}
										?>
									<?php endif; ?>
								</tbody>
								<tfoot>
									<tr>
										<th class="text-center"><a class="btn btn-info btn-icon add-row-foot"><i class="fa fa-plus"></i></a></th>
										<th></th>
										<th></th>
										<th></th>
										<th class="th-total-quantity text-center"></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
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

<script type="text/javascript">
	var site = <?= json_encode(array('base_url' => base_url())) ?>;
	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var edit = 0;
	var counter = <?= $counter ?>;
	var count_errors = 0;
</script>

<script type="text/javascript" src="<?= js('business_plan.js?vs=1.7') ?>"></script>