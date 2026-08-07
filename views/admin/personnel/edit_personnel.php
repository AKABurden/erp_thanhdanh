<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('fileinput/fileinput.min.css') ?>">

<?php echo form_open('admin/personnel/edit_personnel/'.$id, array('id' => 'personnel')); ?>
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
														<input type="text" name="code_personnel" placeholder="<?= lang('tnh_auto') ?>" class="form-control" id="code_personnel" value="<?= $personnel['code'] ?>" readonly="">
													</div>
												</td>
												<td style="width: 15%;">
													<?= lang('tnh_fullname', 'fullname') ?>
												</td>
												<td style="width: 35%;">
													<?= form_input('fullname', set_value('fullname') ? set_value('fullname') : $personnel['fullname'], 'id="fullname" class="form-control" placeholder="'.lang('tnh_fullname').'" required ') ?>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_birthday', 'birthday') ?></td>
												<td>
													<?= form_input('birthday', set_value('birthday') ? set_value('birthday') : _d($personnel['birthday']), 'id="birthday" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'" required ') ?>
												</td>
												<td><?= lang('tnh_gender', 'gender') ?></td>
												<td>
													<select name="gender" id="gender" data-placeholder="<?= lang('tnh_gender') ?>" class="gender" style="width: 100%;" required="required">
														<option value=""></option>
														<option <?= $personnel['gender'] == "male" ? 'selected' : '' ?> value="male"><?= lang('tnh_male') ?></option>
														<option <?= $personnel['gender'] == "female" ? 'selected' : '' ?> value="female"><?= lang('tnh_female') ?></option>
														<option <?= $personnel['gender'] == "other" ? 'selected' : '' ?> value="other"><?= lang('tnh_other') ?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_birthplace', 'birthplace') ?></td>
												<td>
													<input type="text" name="birthplace" id="birthplace" placeholder="<?= lang('tnh_birthplace') ?>" class="form-control" value="<?= $personnel['birthplace'] ?>" title="">
												</td>
												<td><?= lang('tnh_domicile', 'domicile') ?></td>
												<td>
													<input type="text" name="domicile" id="domicile" placeholder="<?= lang('tnh_domicile') ?>" class="form-control" value="<?= $personnel['domicile'] ?>" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_cmnd_id_passport', 'cmnd_id_passport') ?></td>
												<td>
													<input type="text" name="cmnd_id_passport" id="cmnd_id_passport" placeholder="<?= lang('tnh_cmnd_id_passport') ?>" class="form-control" value="<?= $personnel['cmnd_id_passport'] ?>" title="">
												</td>
												<td><?= lang('tnh_date_range', 'date_range') ?></td>
												<td>
													<?= form_input('date_range', set_value('date_range') ? set_value('date_range') : _d($personnel['date_range']), 'id="date_range" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'"') ?>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_issued_by', 'issued_by') ?></td>
												<td>
													<input type="text" name="issued_by" id="issued_by" placeholder="<?= lang('tnh_issued_by') ?>" class="form-control" value="<?= $personnel['issued_by'] ?>" title="">
												</td>
												<td><?= lang('tnh_marital_status', 'marital_status') ?></td>
												<td>
													<select name="marital_status" id="marital_status" data-placeholder="<?= lang('tnh_marital_status') ?>" class="marital_status" style="width: 100%;">
														<option value=""></option>
														<option <?= $personnel['marital_status'] == "alone" ? 'selected' : '' ?> value="alone"><?= lang('tnh_alone') ?></option>
														<option <?= $personnel['marital_status'] == "marriage" ? 'selected' : '' ?> value="marriage"><?= lang('tnh_marriage') ?></option>
														<option <?= $personnel['marital_status'] == "divorce" ? 'selected' : '' ?> value="divorce"><?= lang('tnh_divorce') ?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_nationality', 'nationality') ?></td>
												<td>
													<input type="text" name="nationality" id="nationality" placeholder="<?= lang('tnh_nationality') ?>" class="form-control nationality" value="<?= $personnel['nationality'] ?>" title="">
												</td>
												<td><?= lang('tnh_nation', 'nation') ?></td>
												<td>
													<input type="text" name="nation" id="nation" placeholder="<?= lang('tnh_nation') ?>" class="form-control nation" value="<?= $personnel['nation'] ?>" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_account_name', 'account_name') ?></td>
												<td>
													<input type="text" name="account_name" id="account_name" placeholder="<?= lang('tnh_account_name') ?>" class="form-control account_name" value="<?= $personnel['account_name'] ?>" title="">
												</td>
												<td><?= lang('tnh_bank', 'bank') ?></td>
												<td>
													<input type="text" name="bank" id="bank" placeholder="<?= lang('tnh_bank') ?>" class="form-control bank" value="<?= $personnel['bank'] ?>" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_branch', 'branch') ?></td>
												<td>
													<input type="text" name="branch" id="branch" placeholder="<?= lang('tnh_branch') ?>" class="form-control branch" value="<?= $personnel['branch'] ?>" title="">
												</td>
												<td><?= lang('tnh_personal_tax_code', 'personal_tax_code') ?></td>
												<td>
													<input type="text" name="personal_tax_code" id="personal_tax_code" placeholder="<?= lang('tnh_personal_tax_code') ?>" class="form-control personal_tax_code" value="<?= $personnel['personal_tax_code'] ?>" title="">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_religion', 'religion') ?></td>
												<td>
													<input type="text" name="religion" id="religion" placeholder="<?= lang('tnh_religion') ?>" class="form-control" value="<?= $personnel['religion'] ?>" title="">
												</td>
												<td><?= lang('note', 'note') ?></td>
												<td colspan="1">
													<textarea name="note" id="note" class="form-control note" placeholder="<?= lang('note') ?>" rows="3"><?= $personnel['note'] ?></textarea>
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
													<input type="text" name="telephone" placeholder="<?= lang('tnh_telephone') ?>" class="form-control" id="telephone" value="<?= $personnel['telephone'] ?>">
												</td>
												<td style="width: 15%;">
													<?= lang('Email', 'email') ?>
												</td>
												<td style="width: 35%;">
													<input type="email" name="email" placeholder="<?= lang('Email') ?>" class="form-control" id="telephone" value="<?= $personnel['email'] ?>">
												</td>
											</tr>
											<tr>
												<td><?= lang('Skype', 'skype') ?></td>
												<td>
													<input type="text" name="skype" placeholder="<?= lang('tnh_telephone') ?>" class="form-control" id="skype" value="<?= $personnel['skype'] ?>">
												</td>
												<td><?= lang('Facebook', 'facebook') ?></td>
												<td>
													<input type="text" name="facebook" placeholder="<?= lang('Link facebook') ?>" class="form-control" id="facebook" value="<?= $personnel['facebook'] ?>">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_resident', 'resident') ?></td>
												<td>
													<input type="text" name="resident" placeholder="<?= lang('tnh_resident') ?>" class="form-control" id="resident" value="<?= $personnel['resident'] ?>">
												</td>
												<td><?= lang('tnh_current_accommodation', 'current_accommodation') ?></td>
												<td>
													<input type="text" name="current_accommodation" placeholder="<?= lang('tnh_current_accommodation') ?>" class="form-control" id="current_accommodation" value="<?= $personnel['current_accommodation'] ?>">
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
												<?php
													$counterFamily = 0;
												?>
												<?php if (!empty($family)): ?>
													<?php foreach ($family as $key => $value): ?>
														<tr>
															<td><div class="stt-family text-center"><?= ++$key ?></div></td>
															<td>
																<div class="td-relationship">
																	<select name="relationship_family[<?= $counterFamily ?>]" data-placeholder="<?= lang('tnh_relationship') ?>" id="relationship-family" class="relationship-family" style="width: 100%;">
																		<option value=""></option>
																		<?php foreach (getRelationship() as $k => $val): ?>
																			<option <?= $k == $value['relationship_family'] ? 'selected' : '' ?> value="<?= $k ?>"><?= $val ?></option>
																		<?php endforeach ?>
																	</select>
																</div>
																<input type="hidden" name="counterFamily[]" id="counterFamily" class="form-control" value="<?= $counterFamily ?>">
															</td>
															<td>
																<div class="td-fullname"><input type="text" name="fullname_family[<?= $counterFamily ?>]" id="fullname_family" placeholder="<?= lang('tnh_fullname') ?>" class="form-control fullname_family" style="width: 100%;" value="<?= $value['fullname_family'] ?>"></div>
															</td>
															<td>
																<div class="td-year-birthday"><input type="number" name="year_birthday_family[<?= $counterFamily ?>]" id="year_birthday_family" placeholder="<?= lang('tnh_year_birthday') ?>" class="form-control fullname_family" style="width: 100%;" value="<?= $value['year_birthday_family'] ?>"></div>
															</td>
															<td>
																<div class="td-career"><input type="text" name="career_family[<?= $counterFamily ?>]" id="career_family" placeholder="<?= lang('tnh_career') ?>" class="form-control career_family" style="width: 100%;" value="<?= $value['career_family'] ?>"></div>
															</td>
															<td>
																<div class="td-address"><input type="text" name="address_family[<?= $counterFamily ?>]" id="address_family" placeholder="<?= lang('tnh_address') ?>" class="form-control address_family" style="width: 100%;" value="<?= $value['address_family'] ?>"></div>
															</td>
															<td>
																<div class="td-telephone"><input type="text" name="telephone_family[<?= $counterFamily ?>]" id="telephone_family" placeholder="<?= lang('tnh_telephone') ?>'" class="form-control telephone_family" style="width: 100%;" value="<?= $value['telephone_family'] ?>"></div>
															</td>
															<td>
																<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger remove-row-family"></span></div>
															</td>
														</tr>
														<?php $counterFamily++; ?>
													<?php endforeach ?>
												<?php endif ?>
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
												<?php $counterLiteracy = 0; ?>
												<?php if (!empty($literacy)): ?>
													<?php foreach ($literacy as $key => $value): ?>
														<tr>
															<td>
																<div class="stt-literacy text-center"><?= ++$key ?></div>
															</td>
															<td>
																<div class="td-from-date"><input type="text" name="from_date_literacy[<?= $counterLiteracy ?>]" id="from_date_literacy" placeholder="dd/mm/yyyy" class="form-control datepicker" style="width: 100%;" value="<?= _d($value['from_date_literacy']) ?>"></div>
															</td>
															<td>
																<div class="td-from-date">
																	<input type="text" name="to_date_literacy[<?= $counterLiteracy ?>]" id="to_date_literacy" placeholder="dd/mm/yyyy" class="form-control datepicker" style="width: 100%;" value="<?= _d($value['to_date_literacy']) ?>">
																	<input type="hidden" name="counterLiteracy[]" id="counterLiteracy" class="form-control" value="<?= $counterLiteracy ?>">
																</div>
															</td>
															<td>
																<div class="td-literacy">
																	<select name="literacy[<?= $counterLiteracy ?>]" data-placeholder="<?= lang('tnh_literacy') ?>'" id="literacy" class="literacy" style="width: 100%;">
																		<option></option>
																		<?php foreach (getLiteracy() as $k => $val): ?>
																			<option <?= $k == $value['literacy'] ? 'selected' : '' ?> value="<?= $k ?>"><?= $val ?></option>
																		<?php endforeach ?>
																	</select>
																</div>
															</td>
															<td>
																<div class="td-training-places">
																	<input type="text" name="training_places_literacy[<?= $counterLiteracy ?>]" id="training_places_literacy" placeholder="<?= lang('tnh_training_places') ?>" class="form-control training_places_literacy" style="width: 100%;" value="<?= $value['training_places_literacy'] ?>">
																</div>
															</td>
															<td>
																<div class="td-specialized"><input type="text" name="specialized_literacy[<?= $counterLiteracy ?>]" id="specialized_literacy" placeholder="<?= lang('tnh_training_places') ?>" class="form-control specialized_literacy" style="width: 100%;" value="<?= $value['specialized_literacy'] ?>"></div>
															</td>
															<td>
																<div class="td-classification">
																	<select name="classification_literacy[<?= $counterLiteracy ?>]" data-placeholder="'+ langPersonnel['tnh_classification'] +'" id="classification-literacy" class="classification_literacy" style="width: 100%;">
																		<option></option>
																		<?php foreach (getClassification() as $k => $val): ?>
																			<option <?= $k == $value['classification_literacy'] ? 'selected' : '' ?> value="<?= $k ?>"><?= $val ?></option>
																		<?php endforeach ?>
																	</select>
																</div>
															</td>
															<td>
																<div class="td-actions text-center"><span class="fa fa-remove btn btn-danger remove-row-literacy"></span></div>
															</td>
														</tr>
													<?php endforeach ?>
												<?php endif ?>
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
															<option <?= $value['departmentid'] == $personnel['departments'] ? 'selected' : '' ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
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
															<option <?= $value['id'] == $personnel['locations'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
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
															<option <?= $value['roleid'] == $personnel['role'] ? 'selected' : '' ?> value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td><?= lang('tnh_workplace', 'workplace') ?></td>
												<td>
													<select name="workplace" id="workplace" data-placeholder="<?= lang('tnh_workplace') ?>" class="workplace" style="width: 100%;">
														<option value=""></option>
														<?php foreach ($workplace as $key => $value): ?>
															<option <?= $value['id'] == $personnel['workplace'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_day_in', 'day_in') ?></td>
												<td>
													<?= form_input('day_in', set_value('day_in') ? set_value('day_in') : _d($personnel['day_in']), 'id="day_in" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'"') ?>
												</td>
												<td><?= lang('tnh_day_in_primary', 'day_in_primary') ?></td>
												<td>
													<?= form_input('day_in_primary', set_value('day_in_primary') ? set_value('day_in_primary') : _d($personnel['day_in_primary']), 'id="day_in_primary" class="form-control datepicker" placeholder="'.lang('dd/mm/yyyy').'"') ?>
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
												<?php $counterConcurrently = 0; ?>
												<?php if (!empty($concurrently)): ?>
													<?php foreach ($concurrently as $key => $value): ?>
														<tr>
															<td>
																<div class="stt-concurrently text-center"><?= ++$key ?></div>
															</td>
															<td>
																<div class="td-deparments">
																	<input type="hidden" name="counterConcurrently[]" id="counterConcurrently" class="form-control" value="<?= $counterConcurrently ?>">
																	<select name="deparments_concurrently[<?= $counterConcurrently ?>]" data-placeholder="<?= lang('tnh_depart_concurrently') ?>" id="deparments_concurrently" class="deparments_concurrently" style="width: 100%;">
																		<option value=""></option>
																		<?php foreach ($deparments as $k => $val): ?>
																			<option <?= $val['departmentid'] == $value['deparments_concurrently'] ? 'selected' : '' ?> value="<?= $val['departmentid'] ?>"><?= $val['name'] ?></option>
																		<?php endforeach ?>
																	</select>
																</div>
															</td>
															<td>
																<div class="td-location">
																	<select name="location_concurrently[<?= $counterConcurrently ?>]" data-placeholder="<?= lang('tnh_vt') ?>" id="location_concurrently" class="location_concurrently" style="width: 100%;">
																		<option value=""></option>
																		<?php foreach ($locations as $k => $val): ?>
																			<option <?= $val['id'] == $value['location_concurrently'] ? 'selected' : '' ?> value="<?= $val['id'] ?>"><?= $val['name'] ?></option>
																		<?php endforeach ?>
																	</select>
																</div>
															</td>
															<td>
																<div class="td-role">
																	<select name="role_concurrently[<?= $counterConcurrently ?>]" data-placeholder="<?= lang('role') ?>" id="role_concurrently" class="role_concurrently" style="width: 100%;">
																		<option value=""></option>
																		<?php foreach ($roles as $k => $val): ?>
																			<option <?= $val['roleid'] == $value['role_concurrently'] ? 'selected' : '' ?> value="<?= $val['roleid'] ?>"><?= $val['name'] ?></option>
																		<?php endforeach ?>
																	</select>
																</div>
															</td>
															<td>
																<div class="td-actions text-center">
																	<span class="fa fa-remove btn btn-danger remove-row-concurrently"></span>
																</div>
															</td>
														</tr>
														<?php $counterConcurrently++; ?>
													<?php endforeach ?>
												<?php endif ?>
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
										<?php $counterSalary = 0; ?>
										<?php if (!empty($salary)): ?>
											<?php foreach ($salary as $key => $value): ?>
											<div class="mbot10">
									            <table id="tb-salary-<?= $counterSalary ?>" class="dt-tnh tnh-table table table-bordered table-hover dont-responsive-table dataTable no-footer tb-salary">
									                <thead>
									                    <tr>
									                        <th class="text-center" style="width: 50px;">
									                            <a class="btn btn-info btn-icon add-row-salary" data-toggle="tooltip" title="<?= lang('tnh_allowance') ?>" onClick="addAllowance(this, <?= $counterSalary ?>)"><i class="fa fa-plus"></i></a>
									                        </th>
									                        <th style="width: 100px;"><?= lang('from_date') ?><span class="red">*</span></th>
									                        <th style="width: 200px;"><?= lang('note') ?></th>
									                        <th style="width: 200px;"><?= lang('tnh_salary_form') ?><span class="red">*</span></th>
									                        <th style="width: 200px;"><?= lang('tnh_amount_of_money') ?></th>
									                        <th style="width: 80px;"><?= lang('actions') ?></th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <tr>
									                        <td>
									                        	<div class="td-salary text-center"><span class="label label-primary"><?= lang('tnh_salary') ?></span></div>
									                        </td>
									                        <td>
										                        <div class="td-from-date"><input type="text" name="from_date_salary[<?= $counterSalary ?>]" id="from_date_salary" placeholder="dd/mm/yyyy" class="form-control datepicker" style="width: 100%;" value="<?= _d($value['from_date_salary']) ?>"></div>
										                        <input type="hidden" name="counterSalary[]" id="counterSalary" class="form-control counterSalary" value="<?= $counterSalary ?>">
										                    </td>
									                        <td>
									                        	<div class="td-note">
									                        		<textarea placeholder="<?= lang('note') ?>" name="note_salary[<?= $counterSalary ?>]" id="input" class="form-control"><?= $value['note_salary'] ?></textarea>
									                        	</td>
									                        </td>
									                        <td>
									                        	<div class="td-salary-form">
									                        		<select name="salary_form[<?= $counterSalary ?>]" data-placeholder="<?= lang('tnh_salary_form') ?>" id="salary_form" class="salary_form" style="width: 100%;">
									                        			<option value=""></option>
									                        			<?php foreach ($salaryForm as $k => $val): ?>
																		<option <?= $val['id'] == $value['salary_form'] ? 'selected' : '' ?> value="<?= $val['id'] ?>"><?= $val['name'] ?></option>
									                        			<?php endforeach ?>
									                        		</select>
									                        	</div>
									                        </td>
									                        <td>
									                        	<div class="td-money">
									                        		<input type="text" name="money_salary[<?= $counterSalary ?>]" id="money_salary" class="form-control money_salary money-format" style="width: 100%;" value="<?= formatMoney($value['money_salary']) ?>">
									                        	</div>
									                        </td>
									                        <td>
									                        	<div class="td-actions text-center">
									                        		<span class="fa fa-remove btn btn-danger" onClick="removeSalary(this)"></span>
									                        	</div>
									                        </td>
									                    </tr>
									                    <!-- allowance -->
									                    <?php $allowanceDT = $this->personnel_model->getPersonnelSalaryAllowance($value['id']); ?>
									                    <?php if (!empty($allowanceDT)): ?>
									                    	<?php foreach ($allowanceDT as $k => $val): ?>
									                    		<tr>
									                    			<td class="text-center">
									                    				<div class="td-name-allowance text-center"><span class="label label-warning"><?= lang('tnh_allowance') ?></span></div>
									                    			</td>
									                    			<td colspan="3">
									                    				<div class="td-salary-form-allowance">
									                    					<select name="salary_form_allowance[<?= $counterSalary ?>][]" data-placeholder="<?= lang('tnh_allowance') ?>" id="salary_form_allowance" class="salary_form_allowance" style="width: 100%;">
									                    						<option value=""></option>
									                    						<?php foreach ($allowance as $kk => $vv): ?>
																					<option <?= $val['salary_form_allowance'] == $vv['id'] ? 'selected' : '' ?> value="<?= $vv['id'] ?>"><?= $vv['name'] ?></option>
									                    						<?php endforeach ?>
									                    					</select>
									                    				</div>
									                    			</td>
									                    			<td>
									                    				<div class="td-money-allowance">
									                    					<input type="text" name="money_salary_allowance[<?= $counterSalary ?>][]" id="money_salary_allowance" class="form-control money_salary_allowance money-format" style="width: 100%;" value="<?= $val['money_salary_allowance'] ?>">
									                    				</div>
									                    			</td>
									                    			<td>
									                    				<div class="td-actions-allowance text-center">
									                    					<span class="fa fa-remove btn btn-danger" onClick="removeAllowance(this)"></span>
									                    				</div>
									                    			</td>
									                    		</tr>
									                    	<?php endforeach ?>
									                    <?php endif ?>
									                </tbody>
									            </table>
									        </div>
									        <?php $counterSalary++; ?>
									        <?php endforeach ?>
										<?php endif ?>
									</div>
									<div class="btn btn-primary mtop10 add-salary" onClick="createdSalary(this)"><i class="fa fa-plus"> <?= lang('tnh_add_salary') ?></i></div>
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
													<input type="text" name="insurrance_book_number" placeholder="<?= lang('tnh_insurrance_book_number') ?>" class="form-control insurrance_book_number" value="<?= $personnel['insurrance_book_number'] ?>">
												</td>
												<td style="width: 15%;">
													<?= lang('tnh_number_bhty', 'number_bhty') ?>
												</td>
												<td style="width: 35%;">
													<input type="text" name="number_bhty" placeholder="<?= lang('tnh_number_bhty') ?>" class="form-control number_bhty" value="<?= $personnel['number_bhty'] ?>">
												</td>
											</tr>
											<tr>
												<td><?= lang('tnh_province_code', 'province_code') ?></td>
												<td>
													<select name="province_code" data-placeholder="<?= lang('tnh_province_code') ?>" id="province_code" class="province_code" style="width: 100%;">
														<option value=""></option>
														<?php foreach ($provinceLevel as $key => $value): ?>
															<option <?= $personnel['province_code'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?> - <?= $value['code'] ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td><?= lang('tnh_hospital_registration') ?></td>
												<td>
													<input type="text" name="hospital_registration" data-placeholder="<?= lang('tnh_hospital_registration') ?>" id="hospital_registration" class="hospital_registration" style="width: 100%;" value="<?= $personnel['hospital_registration'] ?>">
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
											<?php $counterInsurrance = 0; ?>
											<?php if (!empty($insurrance)): ?>
												<?php foreach ($insurrance as $key => $value): ?>
													<tr>
														<td>
															<div class="text-center td-number"><?= ++$key ?></div>
															<input type="hidden" name="counter_insurrance[]" id="counter_insurrance" class="form-control counter_insurrance" value="<?= $counterInsurrance ?>">
														</td>
														<td>
															<div class="td-from-month"><input type="text" name="from_month_insurrance[<?= $counterInsurrance ?>]" id="from_date_salary" placeholder="mm/yyyy" class="form-control datepicker" style="width: 100%;" value="<?= $value['from_month_insurrance'] ?>"></div>
														</td>
														<td>
															<div class="td-form-surrance">
																<select name="form_insurrance[<?= $counterInsurrance ?>]" data-placeholder="<?= lang('tnh_hinhthuc') ?>" id="form_insurrance" class="form_insurrance" style="width: 100%;">
																	<option></option>
																	<?php foreach (getFormInsurrance() as $k => $val): ?>
																		<option <?= $value['form_insurrance'] == $k ? 'selected' : '' ?> value="<?= $k ?>"><?= $val ?></option>
																	<?php endforeach ?>
																</select>
															</div>
														</td>
														<td>
															<div class="td-form-surrance">
																<input type="text" name="insurrance[<?= $counterInsurrance ?>]" id="insurrance_<?= $counterInsurrance ?>" placeholder="" class="insurrance" style="width: 100%;" value="<?= $value['insurrance'] ?>">
															</div>
														</td>
														<td>
															<div class="td-money">
																<input type="text" name="money_insurrance[<?= $counterInsurrance ?>]" id="money_insurrance" class="form-control money_insurrance money-format" style="width: 100%;" value="<?= formatMoney($value['money_insurrance']) ?>">
															</div>
														</td>
														<td>
															<div class="td-rate-company">
																<input type="text" name="rate_company_insurrance[<?= $counterInsurrance ?>]" id="rate_company_insurrance" class="form-control rate_company_insurrance" style="width: 100%;" value="<?= $value['rate_company_insurrance'] ?>" readonly></div>
															</div>
														</td>
														<td>
															<div class="td-rate-worker">
																<input type="text" name="rate_worker_insurrance[<?= $counterInsurrance ?>]" id="rate_worker_insurrance" class="form-control rate_worker_insurrance money-format" style="width: 100%;" value="<?= $value['rate_worker_insurrance'] ?>" readonly></div>
															</div>
														</td>
														<td>
															<div class="td-actions text-center">
																<span class="fa fa-remove btn btn-danger" onClick="removeInsurrance(this)"></span>
															</div>
														</td>
													</tr>
													<?php $counterInsurrance++; ?>
												<?php endforeach ?>
											<?php endif ?>
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
											<input <?= in_array($key, $arrReceive) ? 'checked' : '' ?> type="checkbox" name="receive[]" id="<?= $key ?>" value="<?= $key ?>">
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

	var edit = 1;
	var counterFamily = <?= $counterFamily ?>;
	var counterLiteracy = <?= $counterLiteracy ?>;
	var counterConcurrently = <?= $counterConcurrently ?>;
	var counterSalary = <?= $counterSalary ?>;
	var counterInsurrance = <?= $counterInsurrance ?>;
	var count_errors = 0;
	// var table = '';
</script>

<script type="text/javascript" src="<?= js('personnel.js?vs=1.2') ?>"></script>