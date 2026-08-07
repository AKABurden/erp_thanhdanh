<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('fileinput/fileinput.min.css') ?>">

<?php echo form_open('admin/personnel/add_personnel', array('id' => 'personnel')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        	<?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
	<div class="content ae-content form-tnh">
		<div class="row">
			<div class="col-md-12">
				<div role="tabpanel">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#general" aria-controls="info-general" role="tab" data-toggle="tab"><?= lang('tnh_info_general') ?></a>
						</li>
						<li role="presentation">
							<a href="#assigned" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('assigned') ?></a>
						</li>
						<li role="presentation">
							<a href="#insurrance" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_insurrance') ?></a>
						</li>
						<li role="presentation">
							<a href="#receive" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_receive') ?></a>
						</li>
						<li role="presentation">
							<a href="#attachments" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_attachments') ?></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-12">
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="general">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-info">
									<?= lang('tnh_info_general') ?>
								</h3>
							</div>
							<div id="collapse-info" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td style="width: 15%;">
													<?= lang('tnh_code_personnel', 'code_personnel') ?>
												</td>
												<td style="width: 35%;">
													<div class="form-group">
														<input type="text" name="code_personnel" placeholder="<?= lang('tnh_auto') ?>" class="form-control" id="code_personnel" value="" readonly="">
													</div>
												</td>
												<td style="width: 15%;">
													<?= lang('tnh_fullname', 'fullname') ?>
												</td>
												<td style="width: 35%;">
													<?= form_input('fullname', set_value('fullname') ? set_value('fullname') : '', 'id="fullname" class="form-control" placeholder="'.lang('tnh_fullname').'" required ') ?>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_birthday', 'birthday') ?></td>
												<td>
													<?= form_input('birthday', set_value('birthday') ? set_value('birthday') : '', 'id="birthday" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'" required ') ?>
												</td>
												<td><?= lang('tnh_gender', 'gender') ?></td>
												<td>
													<select name="gender" id="gender" data-placeholder="<?= lang('tnh_gender') ?>" class="gender" style="width: 100%;" required="required">
														<option value=""></option>
														<option value="male"><?= lang('tnh_male') ?></option>
														<option value="female"><?= lang('tnh_female') ?></option>
														<option value="other"><?= lang('tnh_other') ?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_birthplace', 'birthplace') ?></td>
												<td>
													<input type="text" name="birthplace" id="birthplace" placeholder="<?= lang('tnh_birthplace') ?>" class="form-control" value="" title="">
												</td>
												<td><?= lang('tnh_domicile', 'domicile') ?></td>
												<td>
													<input type="text" name="domicile" id="domicile" placeholder="<?= lang('tnh_domicile') ?>" class="form-control" value="" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_cmnd_id_passport', 'cmnd_id_passport') ?></td>
												<td>
													<input type="text" name="cmnd_id_passport" id="cmnd_id_passport" placeholder="<?= lang('tnh_cmnd_id_passport') ?>" class="form-control" value="" title="">
												</td>
												<td><?= lang('tnh_date_range', 'date_range') ?></td>
												<td>
													<?= form_input('date_range', set_value('date_range') ? set_value('date_range') : '', 'id="date_range" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'"') ?>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_issued_by', 'issued_by') ?></td>
												<td>
													<input type="text" name="issued_by" id="issued_by" placeholder="<?= lang('tnh_issued_by') ?>" class="form-control" value="" title="">
												</td>
												<td><?= lang('tnh_marital_status', 'marital_status') ?></td>
												<td>
													<select name="marital_status" id="marital_status" data-placeholder="<?= lang('tnh_marital_status') ?>" class="marital_status" style="width: 100%;">
														<option value=""></option>
														<option value="alone"><?= lang('tnh_alone') ?></option>
														<option value="marriage"><?= lang('tnh_marriage') ?></option>
														<option value="divorce"><?= lang('tnh_divorce') ?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_nationality', 'nationality') ?></td>
												<td>
													<input type="text" name="nationality" id="nationality" placeholder="<?= lang('tnh_nationality') ?>" class="form-control nationality" value="" title="">
												</td>
												<td><?= lang('tnh_nation', 'nation') ?></td>
												<td>
													<input type="text" name="nation" id="nation" placeholder="<?= lang('tnh_nation') ?>" class="form-control nation" value="" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_account_name', 'account_name') ?></td>
												<td>
													<input type="text" name="account_name" id="account_name" placeholder="<?= lang('tnh_account_name') ?>" class="form-control account_name" value="" title="">
												</td>
												<td><?= lang('tnh_bank', 'bank') ?></td>
												<td>
													<input type="text" name="bank" id="bank" placeholder="<?= lang('tnh_bank') ?>" class="form-control bank" value="" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_branch', 'branch') ?></td>
												<td>
													<input type="text" name="branch" id="branch" placeholder="<?= lang('tnh_branch') ?>" class="form-control branch" value="" title="">
												</td>
												<td><?= lang('tnh_personal_tax_code', 'personal_tax_code') ?></td>
												<td>
													<input type="text" name="personal_tax_code" id="personal_tax_code" placeholder="<?= lang('tnh_personal_tax_code') ?>" class="form-control personal_tax_code" value="" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_religion', 'religion') ?></td>
												<td>
													<input type="text" name="religion" id="religion" placeholder="<?= lang('tnh_religion') ?>" class="form-control" value="" title="">
												</td>
												<td><?= lang('note', 'note') ?></td>
												<td colspan="1">
													<textarea name="note" id="note" class="form-control note" placeholder="<?= lang('note') ?>" rows="3"></textarea>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_images', 'images') ?></td>
												<td colspan="3">
													<div class="kv-avatar">
														<div class="file-loading">
															<input id="avatar-1" name="images" type="file">
														</div>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-contact"><?= lang('tnh_contact_information') ?></h3>
							</div>
							<div id="collapse-contact" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td style="width: 15%;">
													<?= lang('tnh_telephone', 'telephone') ?>
												</td>
												<td style="width: 35%;">
													<input type="text" name="telephone" placeholder="<?= lang('tnh_telephone') ?>" class="form-control" id="telephone" value="">
												</td>
												<td style="width: 15%;">
													<?= lang('Email', 'email') ?>
												</td>
												<td style="width: 35%;">
													<input type="email" name="email" placeholder="<?= lang('Email') ?>" class="form-control" id="telephone" value="">
												</td>
											</tr>
											<tr>
												<td><?= lang('Skype', 'skype') ?></td>
												<td>
													<input type="text" name="skype" placeholder="<?= lang('tnh_telephone') ?>" class="form-control" id="skype" value="">
												</td>
												<td><?= lang('Facebook', 'facebook') ?></td>
												<td>
													<input type="text" name="facebook" placeholder="<?= lang('Link facebook') ?>" class="form-control" id="facebook" value="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_resident', 'resident') ?></td>
												<td>
													<input type="text" name="resident" placeholder="<?= lang('tnh_resident') ?>" class="form-control" id="resident" value="">
												</td>
												<td><?= lang('tnh_current_accommodation', 'current_accommodation') ?></td>
												<td>
													<input type="text" name="current_accommodation" placeholder="<?= lang('tnh_current_accommodation') ?>" class="form-control" id="current_accommodation" value="">
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-family"><?= lang('tnh_family_information') ?></h3>
							</div>
							<div id="collapse-family" class="panel-collapse collapse in">
								<div class="panel-body">
									<div>
										<table id="tb-family" class="dt-tnh table tnh-table table-bordered table-hover">
											<thead>
												<tr>
													<th class="text-center" style="width: 50px;">
														<a class="btn btn-info btn-icon add-row-family"><i class="fa fa-plus"></i></a>
													</th>
													<th style="width: 120px;"><?= lang('tnh_relationship') ?><span class="red">*</span></th>
													<th><?= lang('tnh_fullname') ?><span class="red">*</span></th>
													<th style="width: 100px;"><?= lang('tnh_year_birthday') ?></th>
													<th style="width: 150px;"><?= lang('tnh_career') ?></th>
													<th style="width: 200px;"><?= lang('tnh_address') ?></th>
													<th style="width: 150px;"><?= lang('tnh_telephone') ?><span class="red">*</span></th>
													<th style="width: 80px;"><?= lang('actions') ?></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-literacy"><?= lang('tnh_literacy') ?></h3>
							</div>
							<div id="collapse-literacy" class="panel-collapse collapse in">
								<div class="panel-body">
									<div>
										<table id="tb-literacy" class="dt-tnh tnh-table table table-bordered table-hover">
											<thead>
												<tr>
													<th class="text-center" style="width: 50px;">
														<a class="btn btn-info btn-icon add-row-literacy"><i class="fa fa-plus"></i></a>
													</th>
													<th style="width: 120px;"><?= lang('from_date') ?><span class="red">*</span></th>
													<th style="width: 120px;"><?= lang('to_date') ?><span class="red">*</span></th>
													<th style="width: 150px;"><?= lang('tnh_literacy') ?><span class="red">*</span></th>
													<th style="width: 150px;"><?= lang('tnh_training_places') ?><span class="red">*</span></th>
													<th style="width: 150px;"><?= lang('tnh_specialized') ?><span class="red">*</span></th>
													<th style="width: 100px;"><?= lang('tnh_classification') ?><span class="red">*</span></th>
													<th style="width: 80px;"><?= lang('actions') ?><span class="red">*</span></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="assigned">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-assigned"><?= lang('assigned') ?></h3>
							</div>
							<div id="collapse-assigned" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td style="width: 15%;">
													<?= lang('departments', 'departments') ?>
												</td>
												<td style="width: 35%;">
													<select name="departments" id="departments" data-placeholder="<?= lang('departments') ?>" class="departments" style="width: 100%;">
														<option value=""></option>
														<?php foreach ($deparments as $key => $value): ?>
															<option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td style="width: 15%;">
													<?= lang('tnh_vt', 'locations') ?>
												</td>
												<td style="width: 35%;">
													<select name="locations" id="locations" data-placeholder="<?= lang('tnh_vt') ?>" class="locations" style="width: 100%;">
														<option value=""></option>
														<?php foreach ($locations as $key => $value): ?>
															<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('role', 'role') ?></td>
												<td>
													<select name="role" id="role" data-placeholder="<?= lang('role') ?>" class="locations" style="width: 100%;">
														<option value=""></option>
														<?php foreach ($roles as $key => $value): ?>
															<option value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td><?= lang('tnh_workplace', 'workplace') ?></td>
												<td>
													<select name="workplace" id="workplace" data-placeholder="<?= lang('tnh_workplace') ?>" class="workplace" style="width: 100%;">
														<option value=""></option>
														<?php foreach ($workplace as $key => $value): ?>
															<option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_day_in', 'day_in') ?></td>
												<td>
													<?= form_input('day_in', set_value('day_in') ? set_value('day_in') : '', 'id="day_in" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'"') ?>
												</td>
												<td><?= lang('tnh_day_in_primary', 'day_in_primary') ?></td>
												<td>
													<?= form_input('day_in_primary', set_value('day_in_primary') ? set_value('day_in_primary') : '', 'id="day_in_primary" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'"') ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-concurrently"><?= lang('tnh_concurrently') ?></h3>
							</div>
							<div id="collapse-concurrently" class="panel-collapse collapse in">
								<div class="panel-body">
									<div>
										<table id="tb-concurrently" class="dt-tnh tnh-table table table-bordered table-hover">
											<thead>
												<tr>
													<th class="text-center" style="width: 50px;">
														<a class="btn btn-info btn-icon add-row-concurrently"><i class="fa fa-plus"></i></a>
													</th>
													<th style="width: 300px;"><?= lang('tnh_depart_concurrently') ?><span class="red">*</span></th>
													<th style="width: 300px;"><?= lang('tnh_vt') ?><span class="red">*</span></th>
													<th style="width: 300px;"><?= lang('role') ?><span class="red">*</span></th>
													<th style="width: 80px;"><?= lang('actions') ?></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-salary"><?= lang('tnh_salary_and_allowance') ?></h3>
							</div>
							<div id="collapse-salary" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="append-salary">
										<!-- salary -->
									</div>
									<div class="btn btn-primary mtop10 add-salary" onClick="createdSalary(this)"><i class="fa fa-plus"> <?= lang('tnh_add_salary') ?></i></div>
								</div>
							</div>
						</div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-signature"><?= lang('tnh_signature') ?></h3>
							</div>
							<div id="collapse-signature" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td style="width: 15%;"><?= lang('tnh_the_signer', 'signer') ?></td>
												<td colspan="3">
													<input type="text" name="signer" id="signer" data-placeholder="<?= lang('tnh_the_signer') ?>" class="" style="width: 100%;" value="">
												</td>
											</tr>
											<tr>
												<td style="width: 15%;">
													<?= lang('role', 'role_signer') ?>
												</td>
												<td style="width: 35%;">
													<input type="text" name="role_signer" placeholder="<?= lang('role') ?>" class="form-control role_signer" value="" readonly>
												</td>
												<td style="width: 15%;">
													<?= lang('tnh_sign_day', 'sign_day') ?>
												</td>
												<td style="width: 35%;">
													<?= form_input('sign_day', set_value('sign_day') ? set_value('sign_day') : date('d/m/Y'), 'id="sign_day" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'"') ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="insurrance">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-insurrance"><?= lang('tnh_insurrance') ?></h3>
							</div>
							<div id="collapse-insurrance" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="tnh-tb table-bordered table-hover">
										<tbody>
											<tr>
												<td style="width: 15%;">
													<?= lang('tnh_insurrance_book_number', 'insurrance_book_number') ?>
												</td>
												<td style="width: 35%;">
													<input type="text" name="insurrance_book_number" placeholder="<?= lang('tnh_insurrance_book_number') ?>" class="form-control insurrance_book_number" value="">
												</td>
												<td style="width: 15%;">
													<?= lang('tnh_number_bhty', 'number_bhty') ?>
												</td>
												<td style="width: 35%;">
													<input type="text" name="number_bhty" placeholder="<?= lang('tnh_number_bhty') ?>" class="form-control number_bhty" value="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_province_code', 'province_code') ?></td>
												<td>
													<select name="province_code" data-placeholder="<?= lang('tnh_province_code') ?>" id="province_code" class="province_code" style="width: 100%;">
														<option value=""></option>
														<?php foreach ($provinceLevel as $key => $value): ?>
															<option value="<?= $value['id'] ?>"><?= $value['name'] ?> - <?= $value['code'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td><?= lang('tnh_hospital_registration') ?></td>
												<td>
													<input type="text" name="hospital_registration" data-placeholder="<?= lang('tnh_hospital_registration') ?>" id="hospital_registration" class="hospital_registration" style="width: 100%;" value="">
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-history-insurrance"><?= lang('tnh_history_insurrance') ?></h3>
							</div>
							<div id="collapse-history-insurrance" class="panel-collapse collapse in">
								<div class="panel-body">
									<table id="tb-history-insurrance" class="dt-tnh tnh-table table table-bordered table-hover dont-responsive-table dataTable no-footer">
										<thead>
											<tr>
												<th class="text-center" style="width: 50px;">
													<a class="btn btn-info btn-icon add-row-history-insurrance" onClick="createdHistoryInsurrance(this)"><i class="fa fa-plus"></i></a>
												</th>
												<th style="width: 100px;"><?= lang('tnh_form_month') ?><span class="red">*</span></th>
												<th style="width: 100px;"><?= lang('tnh_hinhthuc') ?><span class="red">*</span></th>
												<th style="width: 200px;"><?= lang('tnh_insurrance') ?><span class="red">*</span></th>
												<th style="width: 100px;"><?= lang('tnh_premium_rates') ?></th>
												<th style="width: 100px;"><?= lang('tnh_rate_company') ?></th>
												<th style="width: 100px;"><?= lang('tnh_rate_worker') ?></th>
												<th style="width: 80px;"><?= lang('actions') ?></th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="receive">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-receive"><?= lang('tnh_receive') ?></h3>
							</div>
							<div id="collapse-receive" class="panel-collapse collapse in">
								<div class="panel-body">
									<?php foreach (getReceivePersonnel() as $key => $value): ?>
										<div class="checkbox checkbox-info">
											<input type="checkbox" name="receive[]" id="<?= $key ?>" value="<?= $key ?>">
											<label for="<?= $key ?>"><?= $value ?></label>
										</div>
									<?php endforeach ?>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="attachments">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title" style="cursor: pointer;" data-toggle="collapse" href="#collapse-attachments"><?= lang('tnh_attachments') ?></h3>
							</div>
							<div id="collapse-attachments" class="panel-collapse collapse in">
								<div class="panel-body">
									<input type="file" name="attachments[]" class="form-control attachments" value="" multiple>
								</div>
							</div>
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
<script type="text/javascript" src="<?= js('fileinput/fileinput.min.js') ?>"></script>
<!-- <script type="text/javascript" src="<?= js('fileinput/locales/LANG.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('fileinput/locales/vi.js') ?>"></script>
<script type="text/javascript">
	var langPersonnel = <?= json_encode(['tnh_relationship' => lang('tnh_relationship'), 'tnh_fullname' => lang('tnh_fullname'), 'tnh_career' => lang('tnh_career'), 'tnh_address' => lang('tnh_address'), 'tnh_telephone' => lang('tnh_telephone'), 'tnh_year_birthday' => lang('tnh_year_birthday'), 'from_date' => lang('from_date'), 'to_date' => lang('to_date'), 'tnh_literacy' => lang('tnh_literacy'), 'tnh_training_places' => lang('tnh_training_places'), 'tnh_specialized' => lang('tnh_specialized'), 'tnh_classification' => lang('tnh_classification'), 'tnh_depart_concurrently' => lang('tnh_depart_concurrently'), 'tnh_vt' => lang('tnh_vt'), 'tnh_allowance' => lang('tnh_allowance'), 'from_date' => lang('from_date'), 'note' => lang('note'), 'tnh_salary_form' => lang('tnh_salary_form'), 'tnh_amount_of_money' => lang('tnh_amount_of_money'), 'actions' => lang('actions'), 'tnh_salary' => lang('tnh_salary'), 'tnh_hinhthuc' => lang('tnh_hinhthuc'), 'role' => lang('role')]) ?>;

	var token = "<?= $this->security->get_csrf_token_name() ?>";
	var hash = "<?= $this->security->get_csrf_hash() ?>";
	var relationship = <?= !empty(getRelationship()) ? json_encode(getRelationship()) : '{}' ?>;
	var literacy = <?= !empty(getLiteracy()) ? json_encode(getLiteracy()) : '{}' ?>;
	var classification = <?= !empty(getClassification()) ? json_encode(getClassification()) : '{}' ?>;
	var locations = <?= !empty($locations) ? json_encode($locations) : '{}' ?>;
	var roles = <?= !empty($roles) ? json_encode($roles) : '{}' ?>;
	var deparments = <?= !empty($deparments) ? json_encode($deparments) : '{}' ?>;
	var allowance = <?= !empty($allowance) ? json_encode($allowance) : '{}' ?>;
	var salaryForm = <?= !empty($salaryForm) ? json_encode($salaryForm) : '{}' ?>;
	var formInsurrance = <?= !empty(getFormInsurrance()) ? json_encode(getFormInsurrance()) : '{}' ?>;

	var edit = 0;
	var counterFamily = 0;
	var counterLiteracy = 0;
	var counterConcurrently = 0;
	var counterSalary = 0;
	var counterInsurrance = 0;
	var count_errors = 0;
	// var table = '';
</script>

<script type="text/javascript" src="<?= js('personnel.js?vs=1.2') ?>"></script>