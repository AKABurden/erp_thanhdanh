<?php echo form_open("admin/products/design_stages/$id", array('id' => 'add-stage')); ?>
<div class="modal-dialog modal-lg" style="width: 90%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('stages') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('tnh_versions', 'versions') ?>
						<?php echo form_input('versions', (isset($_POST['versions']) ? $_POST['versions'] : (!empty($stage['versions']) ? $stage['versions'] : 'VS'.date('Y'))), 'placeholder="' . lang('tnh_versions') . '" id="versions" required class="form-control input-tip"'); ?>
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
				<input type="hidden" name="product_id" id="product_id" class="form-control product_id" value="<?= $id ?>">
				<div class="col-md-12">
					<div class="">
						<table class="table table-stages table-hover sortable dataTable" style="margin: 0;">
							<thead>
								<tr style="background: #5cb0d5;">
									<th style="width: 5%;">
										<div class="text-center">
											<button type="button" class="btn btn-warning btn-icon btn-add-stage"><i class="fa fa-plus"></i></button>
										</div>
									</th>
									<th class="text-center" style="width: 200px;"><?= lang('tnh_stage_name') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('Đánh dấu công đoạn phát sinh btp hoặc tp chưa hoàn thiện') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('tnh_final_stage') ?></th>
									<th class="text-center" style="width: 150px;"><?= lang('Máy móc') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('Số lần/trên mặt') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('Số lần vận hành') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('Số đường dao cắt') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('Định mức TG canh bài mặt 1') ?></th>
									<th class="text-center" style="width: 100px;"><?= lang('Định mức TG canh bài mặt 2') ?></th>
									<th class="text-center" style="width: 50px; text-align: center;"><i class="fa fa-trash-o"></i></th>
								</tr>
							</thead>
							<tbody class="ui-sortable">
								<?php if (!empty($html_stages)) : ?>
									<?= $html_stages ?>
								<?php else : ?>
									<?php if (!empty($list_stages)) : ?>
										<?php
											$items = [];
											$i_stage = 0;

											$dtStagesMaterials = get_table_where('tbl_stages', ['id' => STAGES_MATERIAL], '', 'row_array');
										?>
										<?php if(!empty($dtStagesMaterials)): ?>
											<tr class="sortable item">
												<input type="hidden" name="i_stage[]" id="i_stage" class="form-control i_stage" value="<?= $i_stage ?>">
												<input type="hidden" name="number[]" id="number" class="form-control number" value="1">
												<td class="stt text-center dragger"><?= 1 ?></td>

												<td>
													<select name="stage[]" data-live-search="true" onchange="_changeStage(this)" data-none-selected-text="<?= lang('choose') ?>" id="stage_<?= $i_stage ?>" class="form-control" required="required">
														<option value=""></option>
														<?php foreach($list_stages_array as $k => $v): ?>
															<option <?= STAGES_MATERIAL == $v['id'] ? 'selected' : '' ?> data-subtext="<?= $v['code'] ?>" value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
														<?php endforeach; ?>
													</select>
													<div class="checkbox checkbox-info" style="margin-top: 5px;">
														<input type="checkbox" name="face[<?= $i_stage ?>]" id="face_<?= $i_stage ?>" value="1">
														<label for="face_<?= $i_stage ?>">Mặt trước</label>
													</div>
													<div class="checkbox checkbox-info">
														<input type="checkbox" name="face_after[<?= $i_stage ?>]" id="face_after_<?= $i_stage ?>" value="2">
														<label for="face_after_<?= $i_stage ?>">Mặt sau</label>
													</div>
												</td>
												<!-- <td>
													<input type="number" name="number_hours[]"  class="form-control" value="0" title="">
												</td> -->
												<td>
													<input type="hidden" name="type[]" value="0">
												</td>
												<td>
													<div class="radio radio-primary">
														<input type="radio" name="final_stage" id="final_stage_<?= $i_stage ?>" value="<?= $i_stage ?>">
														<label  for="final_stage_<?= $i_stage ?>"><?= lang('choose') ?></label>
													</div>
												</td>
												<td>
													<select name="machines[<?= $i_stage ?>][]"  data-live-search="true" data-none-selected-text="<?= lang('tnh_machines') ?>" id="machines_<?= $i_stage ?>" class="form-control ajax-search" >
														<option value=""></option>
													</select>
												</td>
												<td>
													<input type="text" name="number_face[]" placeholder="<?= lang('Số lần/trên mặt') ?>" class="form-control number-format" value="">
												</td>
												<td>
													<input type="text" name="number_operations[]" placeholder="<?= lang('Số lần vận hành') ?>" class="form-control number-format" value="">
												</td>
												<td>
													<input type="text" name="number_cutting[]" placeholder="<?= lang('Số đường dao cắt') ?>" class="form-control number-format" value="">
												</td>
												<td>
													<input type="text" name="quota_time_f1[]" placeholder="<?= lang('Định mức TG canh bài mặt 1') ?>" class="form-control number-format" value="">
												</td>
												<td>
													<input type="text" name="quota_time_f2[]" placeholder="<?= lang('Định mức TG canh bài mặt 2') ?>" class="form-control number-format" value="">
												</td>
												<td>
													<!-- <div class="text-center"><i class="btn btn-danger fa fa-remove remove-stage"></i></div> -->
												</td>
											</tr>
											<?php 
												$items[] = [];
												$i_stage++;
											?>
										<?php endif; ?>
										<?php foreach ($list_stages_array as $key => $value) : ?>
											<?php continue; ?>
											<tr class="sortable item">
												<input type="hidden" name="i_stage[]" id="i_stage" class="form-control i_stage" value="<?= $i_stage ?>">
												<input type="hidden" name="number[]" id="number" class="form-control number" value="<?= $key + 1 ?>">
												<td class="stt text-center dragger"><?= $key + 1 ?></td>

												<td>
													<select name="stage[]" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>" id="stage_<?= $i_stage ?>" class="form-control" required="required">
														<option value=""></option>
														<?php foreach($list_stages_array as $k => $v): ?>
															<option  data-subtext="<?= $v['code'] ?>"  <?= $key == $k ? 'selected' : '' ?> value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
														<?php endforeach; ?>
													</select>
												</td>
												<td>
													<input type="number" name="number_hours[]"  class="form-control" value="0" title="">
												</td>
												<td>
													<div class="radio radio-primary">
														<input type="radio" <?= count($list_stages_array) - 1 == $key ? 'checked' : '' ?> name="final_stage" id="final_stage_<?= $i_stage ?>" value="<?= $i_stage ?>">
														<label  for="final_stage_<?= $i_stage ?>"><?= lang('choose') ?></label>
													</div>
												</td>
												<td>
													<div class="text-center"><i class="btn btn-danger fa fa-remove remove-stage"></i></div>
												</td>
											</tr>
											<?php 
												$items[] = [];
												$i_stage++;
											?>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endif; ?>
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
            <a data-tnh="modal" class="tnh-modal hide click_design_stage"
               href=" <?= base_url() ?>admin/products/view_product/<?= $id ?>" data-toggle="modal"
               data-target="#myModal"></a>
			<button type="submit" class="btn btn-primary add"><?= lang('save') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	var i_stage = <?= !empty($items) ? count($items) : 0 ?>;
	var product_stage_id = <?= !empty($stage['id']) ? $stage['id'] : 0 ?>;
	var list_stages = '<?= $list_stages ?>';
	var json_stage = {};
	json_stage['versions'] = 'required';

	<?php if (!empty($items)) : ?>
		$(document).ready(function() {
			for (n = 0; n <= i_stage; n++) {
				$('select#stage_' + n + '').selectpicker();
				_stage_id = $('select#stage_' + n + '').val();
				// selectAjax($('select#stage_' + n + ''), false, 'admin/products/searchStages');
				// selectAjax($('select#machines_' + n + ''), false, 'admin/categories/searchMachines');
				selectAjax($('select#machines_' + n + ''), {_stage_id: _stage_id}, 'admin/categories/searchMachines', 'categories/searchMachines');
				json_stage['stage_' + n + ''] = 'required';
			}
		});
		$(document).ready(function () {
			init_selectpicker();
			selectAjax($('select#machines_' + 0 + ''), false, 'admin/categories/searchMachines', 'categories/searchMachines');
		});
	<?php endif ?>

	function totalStages() {
		var table_stage = $('.table-stages tbody tr').length;
		var stt = 0;
		for (ii = 0; ii < table_stage; ii++) {
			stt++;
			element = $('.table-stages tbody tr')[ii];
			$(element).find('.stt').html(stt);
			$(element).find('.number').val(stt);
            // $(element).find('select.type').selectpicker('val', 0);
            if (stt == 2){
                $(element).find('select.type').selectpicker('val', 2);
            }
		}
	}

	function _changeStage(_this) {
		var cTr = $(_this).closest('tr');
		_stage_id = $(_this).val();
		_i_stage = $(cTr).find('.i_stage').val();
		selectAjax($('select#machines_' + _i_stage + ''), {_stage_id: _stage_id}, 'admin/categories/searchMachines', 'categories/searchMachines');
	}

	$(document).ready(function() {
		$('.sortable tbody').sortable({
			start: function() {},
			stop: function() {
				totalStages();
			}
		});

		$('.btn-add-stage').click(function(event) {
			event.preventDefault();
			tr_html = '';
			tr_html += '<tr class="sortable item">';
			tr_html += '<input type="hidden" name="i_stage[]" id="i_stage" class="form-control i_stage" value="' + i_stage + '">'
			tr_html += '<input type="hidden" name="number[]" id="number" class="form-control number">'
			tr_html += '<td class="stt text-center dragger"></td>';

			tr_html += '<td>\
	                        <select name="stage[]"  data-live-search="true" onchange="_changeStage(this)" data-none-selected-text="<?= lang('choose') ?>" id="stage_' + i_stage + '" class="form-control" required="required">\
	                            <option value=""></option>\
	                            ' + list_stages + '\
	                        </select>\
							<div class="checkbox checkbox-info" style="margin-top: 5px;">\
                                <input type="checkbox" name="face['+i_stage+']" id="face_'+i_stage+'" value="1">\
                                <label for="face_'+i_stage+'">Mặt trước</label>\
							</div>\
							<div class="checkbox checkbox-info">\
								<input type="checkbox" name="face_after['+i_stage+']" id="face_after_'+i_stage+'" value="2">\
								<label for="face_after_'+i_stage+'">Mặt sau</label>\
							</div>\
	                    </td>';
			// tr_html += '<td>\
	        //                 <input type="number" name="number_hours[]" id="input" class="form-control" value="0" title="">\
	        //             </td>';
			// <option value="1"><?= lang('tnh_semi_finished_product') ?></option>
			tr_html += `<td>
				<select name="type[]" data-none-selected-text="<?= lang('type') ?>" class="form-control type selectpicker">
					<option value=""></option>
					<option value="2"><?= lang('tnh_unfinished_product') ?></option>
				</select>
			</td>`;

			tr_html += '<td>\
	                        <div class="radio radio-primary">\
	                        	<input type="radio" name="final_stage" id="final_stage_' + i_stage + '" value="' + i_stage + '">\
	                        	<label for="final_stage_' + i_stage + '">' + lang_core['choose'] + '</label>\
	                        </div>\
	                    </td>';
			tr_html += '<td>\
				<select name="machines['+i_stage+'][]"  data-live-search="true" data-none-selected-text="<?= lang('tnh_machines') ?>" id="machines_'+ i_stage +'" class="form-control ajax-search" >\
					<option value=""></option>\
				</select>\
			</td>';

			tr_html+= `<td>
				<input type="text" name="number_face[]" placeholder="<?= lang('Số lần/trên mặt') ?>" class="form-control number-format" value="">
			</td>
			<td>
				<input type="text" name="number_operations[]" placeholder="<?= lang('Số lần vận hành') ?>" class="form-control number-format" value="">
			</td>
			<td>
				<input type="text" name="number_cutting[]" placeholder="<?= lang('Số đường dao cắt') ?>" class="form-control number-format" value="">
			</td>`;

			tr_html+= `<td>
				<input type="text" name="quota_time_f1[]" placeholder="<?= lang('Định mức TG canh bài mặt 1') ?>" class="form-control number-format" value="">
			</td>
			<td>
				<input type="text" name="quota_time_f2[]" placeholder="<?= lang('Định mức TG canh bài mặt 2') ?>" class="form-control number-format" value="">
			</td>`;

			tr_html += '<td>\
							<div class="text-center"><i class="btn btn-danger fa fa-remove remove-stage"></i></div>\
						</td>';
			tr_html += '</tr>';

			$('.table-stages tbody').append(tr_html);
			// selectAjax($('select#stage_'+ i_stage +''), false, 'admin/products/searchStages');
			$('select#stage_' + i_stage + '').selectpicker();
			selectAjax($('select#machines_' + i_stage + ''), false, 'admin/categories/searchMachines');
			json_stage['stage_' + i_stage + ''] = 'required';
			appValidateForm($('#add-stage'), json, addStages);
			totalStages();
			i_stage++;
			init_selectpicker();
		});

		$('.modal').on('click', '.remove-stage', function(e) {
			e.preventDefault();
			$(this).closest('tr').remove();
			totalStages();
		});

		appValidateForm($('#add-stage'), json_stage, addStages);

		function addStages(form) {
			$('.add').attr('disabled', 'disabled');
			product_id = $('#product_id').val();
			if (!product_id) {
				alert_float('danger', 'errors');
				$('.add').removeAttr('disabled', 'disabled');
				return;
			}
			var data = $(form).serialize();
			var url = form.action;
			$.ajax({
					url: site.base_url + 'admin/products/design_stages/' + product_id + '/' + product_stage_id,
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
                        $(".click_design_stage")[0].click();
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