<?php echo form_open('admin/categories/add_machines', array('id' => 'add-machines', 'enctype' => 'multipart/form-data')); ?>
<style>
	#tb-process tbody tr td {
		vertical-align: middle !important;
	}
</style>
<div class="modal-dialog modal-lg" style="min-width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= _l('tnh_add_machines'); ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('Mã Thiết Bị/Công Việc', 'code') ?>
						<?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : ''), 'placeholder="' . lang('Mã Thiết Bị/Công Việc') . '" id="code" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<?= lang('Tên Thiết Bị/Công Việc', 'name') ?>
						<?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ''), 'placeholder="' . lang('name') . '" id="name" required class="form-control input-tip"'); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('tnh_operating_gauge', 'operating_gauge') ?>
								<?php echo form_input('operating_gauge', (isset($_POST['operating_gauge']) ? $_POST['operating_gauge'] : ''), 'placeholder="' . lang('tnh_operating_gauge') . '" id="operating_gauge" class="form-control input-tip"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('tnh_standard', 'standard') ?>
                                <select name="standard" id="standard" data-none-selected-text="<?= lang('Tiêu chuẩn') ?>" class="form-control standard">
                                    <option value=""></option>
									<?php if(!empty($standard)) {?>
										<?php foreach ($standard as $key => $value) : ?>
                                            <option value="<?= $value['id'] ?>" data-content-name="<?=$value['name']?>"><?= $value['code'] ?></option>
										<?php endforeach ?>
									<?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('tnh_pp_measure', 'pp_measure') ?>
								<?php echo form_input('pp_measure', (isset($_POST['pp_measure']) ? $_POST['pp_measure'] : ''), 'placeholder="' . lang('tnh_pp_measure') . '" id="pp_measure" class="form-control input-tip"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('tnh_quota_productivity', 'quota_productivity').'/h' ?>
								<?php echo form_input('quota_productivity', (isset($_POST['quota_productivity']) ? $_POST['quota_productivity'] : ''), 'placeholder="' . lang('tnh_quota_productivity') . '" id="quota_productivity" class="form-control number-format input-tip"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('Ngày Bắt Đầu Bảo Trì', 'day_operation') ?>
								<?php echo render_date_input('day_operation', '', (isset($_POST['day_operation']) ? $_POST['day_operation'] : '')); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('Số ngày cần bảo trì', 'number_day_operation') ?>
								<?php echo render_input('number_day_operation', '', (isset($_POST['number_day_operation']) ? $_POST['number_day_operation'] : '')); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
						<div class="col-md-4">
							<div class="form-group">
								<?= lang('Trạng Thái', 'status') ?>
								<select name="status" id="status" data-none-selected-text="<?= lang('tnh_status') ?>" class="form-control status" required="required">
									<option value=""></option>
									<?php foreach (status_machine_new() as $key => $value) : ?>
										<option value="<?= $key ?>"><?= $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
						</div>
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('Định Mức Năng Suất/Tháng', 'product_in_month') ?>
								<?php echo form_input('product_in_month', (isset($_POST['product_in_month']) ? $_POST['product_in_month'] : ''), 'placeholder="' . lang('tnh_product_in_month') . '" id="product_in_month" onkeyup="formatNumBerKeyUpCus(this)" class="form-control input-tip"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('Thời Gian Chuẩn Bị (Giờ)', 'preparation_time') ?>
								<?php echo render_input('preparation_time', '', (isset($_POST['preparation_time']) ? $_POST['preparation_time'] : ''), 'text', ['onchange' => 'formatNumBerKeyUpCus(this)']); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group">
								<?= lang('Định Mức Thời Gian Duyệt Màu', 'product_color') ?>
								<?php echo form_input('product_color', (isset($_POST['product_color']) ? $_POST['product_color'] : ''), 'placeholder="' . lang('Định mức thời gian duyệt màu') . '" id="product_in_month" onkeyup="formatNumBerKeyUpCus(this)" class="form-control input-tip"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('NPL canh bài', 'soup_ingredients') ?>
                                <input type="text" name="soup_ingredients" id="soup_ingredients" class="form-control soup_ingredients number-format" value="">
                            </div>
                        </div>
                        <div class="clearfix"></div>
					</div>
				</div>





                <div class="clearfix"></div>
				<div class="col-md-12 mbot20">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab_procedure">Quy Trình</a></li>
                        <li><a data-toggle="tab" href="#tab_maintenance">Bảo Trì</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tab_procedure" class="tab-pane fade in active">
                            <table id="tb-process" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addProcess()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th><?= lang('tnh_process') ?><span class="text-danger">*</span></th>
                                        <th><?= lang('File') ?></th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="tab_maintenance" class="tab-pane fade in">
                            <table id="tb-maintenance" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="hover-svg dropdown-toggle add-row" onclick="addMaintenance()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                                </svg>
                                            </a>
                                        </th>
                                        <th>Bộ phận máy móc<span class="text-danger">*</span></th>
                                        <th>Số ngày cần bảo trì<span class="text-danger">*</span></th>
                                        <th>Ghi chú cách thức bảo trì</th>
                                        <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
				</div>
                <hr/>
                <div class="clearfix"></div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<div class="form-group">
								<?= lang('tnh_specifications', 'specifications') ?>
								<?php echo form_textarea('specifications', (isset($_POST['specifications']) ? $_POST['specifications'] : ''), 'placeholder="' . lang('tnh_specifications') . '" id="specifications" class="form-control input-tip tinymce"'); ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?= lang('note', 'note') ?>
								<?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="' . lang('note') . '" id="note" class="form-control input-tip tinymce"'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
			<button type="submit" class="btn btn-primary add"><?= _l('add') ?></button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<script>
    var countItems = 0;
    var countItemsMain = 0;
	$(function() {
        init_datepicker();
		appValidateForm($('#add-machines'), {
			code: 'required',
			name: 'required',
			status: 'required',
		}, addMachines);

		// function addMachines(form) {
		// 	$('.add').attr('disabled', 'disabled');
		// 	tinymce.get('note').save();
		// 	tinymce.get('specifications').save();
		// 	var data = $(form).serialize();
		// 	var url = form.action;
		// 	$.ajax({
		// 			url: site.base_url + 'admin/categories/add_machines',
		// 			type: 'POST',
		// 			dataType: 'JSON',
		// 			data: data,
		// 		})
		// 		.done(function(data) {
		// 			if (data.result) {
		// 				alert_float('success', data.message);
		// 				if (typeof oTable != 'undefined') {
		// 					oTable.draw();
		// 				}
		// 				$('.modal-dialog .close').trigger('click');
		// 			} else {
		// 				alert_float('danger', data.message);
		// 				$('.add').removeAttr('disabled', 'disabled');
		// 			}
		// 		})
		// 		.fail(function() {
		// 			console.log("error");
		// 		});
		// 	return false;
		// }

        function addMachines(form) {
            $('.add').attr('disabled', 'disabled');
            tinymce.get('note').save();
            tinymce.get('specifications').save();

            var formParams = $('#add-machines').serializeArray();

            var formData = new FormData();
            // return

            $.each($('#add-machines').find('input[type="file"]'), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });


            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                url: site.base_url+'admin/categories/add_machines',
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            }).done(function (data) {
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
            .fail(function () {
                console.log("error");
            });

            return false;
        }


		$('.status').selectpicker();
		$('.standard').selectpicker();
		init_editor('textarea[name="note"]');
		init_editor('textarea[name="specifications"]');

        $('#standard').change(function() {
            var content = $(this).find(':selected').data('content-name');
            $('#pp_measure').val(content);
        })
	})
</script>