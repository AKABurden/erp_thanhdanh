<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/business_plan/edit/'.$business_plan['id'], array('id'=>'add-business-plan')); ?>
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
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-file"></i>
												</span>
												<input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= $business_plan['reference_no'] ?>" readonly="" aria-invalid="false">
											</div>
										</div>
									</td>
									<td><?= lang('date', 'date') ?></td>
									<td>
										<?= form_input('date', set_value('date') ? set_value('date') : _dt($business_plan['date']), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
									</td>
								</tr>
								<tr>
									<td><?= lang('tnh_plan_name', 'plan_name') ?></td>
									<td>
										<input type="text" name="plan_name" placeholder="<?= lang('tnh_plan_name') ?>" class="form-control" id="plan_name" value="<?= $business_plan['plan_name'] ?>">
									</td>
									<td><?= lang('departments', 'departments') ?></td>
									<td>
										<select name="departments" data-placeholder="<?= lang('departments') ?>" id="departments" class="tnh-select" required="required" style="width: 100%;">
											<option value=""></option>
											<?php foreach ($departments as $key => $value): ?>
												<option <?= $value['departmentid'] == $business_plan['departments_id'] ? 'selected' : '' ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
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
													<option <?= $value['id'] == $business_plan['id_branch'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
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
										<textarea name="note" id="note" class="form-control" rows="3">
											<?= $business_plan['note'] ?>
										</textarea>
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
						<div class="tb-height">
							<table id="tb-business-plan" class="dt-tnh table table-hover" style="width: 100%;">
								<thead>
									<tr>
										<th class="text-center" style="width: 7%;">
											<a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
		                                </th>
		                                <th style="width: 20%;"><?= lang('tnh_product_code') ?></th>
		                                <th style="width: 10%;"><?= lang('tnh_images') ?></th>
		                                <th style="width: 20%;"><?= lang('tnh_product_name') ?></th>
		                                <th style="width: 10%;"><?= lang('quantity') ?></th>
		                                <th style="width: 20%;"><?= lang('cong_shipment_date') ?></th>
		                                <th style="width: 10%;"><?= lang('note') ?></th>
		                                <th style="width: 3%;"><?= lang('actions') ?></th>
									</tr>
								</thead>
								<tbody>
									<?= $tr_html ?>
								</tbody>
								<tfoot>
									<tr>
										<th class="text-center"><a class="btn btn-info btn-icon add-row-foot"><i class="fa fa-plus"></i></a></th>
										<th></th>
										<th></th>
										<th></th>
										<th class="th-total-quantity text-center"><?= number_format($business_plan['total_quantity']) ?></th>
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
				<input type="hidden" name="edit" id="" class="form-control" value="1">
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
	var edit = 1;
	var counter = <?= $counter ?>;
	var count_errors = 0;
</script>

<script type="text/javascript" src="<?= js('business_plan.js?vs=1.7') ?>"></script>