<div class="modal fade" id="modal_trouble" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg"  style="min-width: 80%;">
		<?php echo form_open(admin_url('trouble/modal/' . (!empty($trouble->id) ? $trouble->id : '')), ['id' => 'form_trouble']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? _l($title) : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
					<?php $value = !empty($trouble) ? $trouble->code : '' ?>
					<?php echo render_input('code', 'c_trouble_code', $value) ?>
                </div>
                <div class="col-md-12">
					<?php $value = !empty($trouble) ? $trouble->name : '' ?>
					<?php echo render_input('name', 'c_trouble_name', $value) ?>
                </div>
                <div class="col-md-12">
                    <?php $value = !empty($trouble) ? $trouble->id_criteria : '' ?>
                    <?php echo render_select('id_criteria', $criteria, ['id', 'code_criteria', 'criteria'], 'Mã tiêu chí KPI', $value) ?>
                </div>
                <div class="col-md-12">
                    <?php $value = !empty($trouble) ? $trouble->id_departments : '' ?>
                    <?php echo render_select('id_departments', $departments, ['departmentid', 'name'],'Phòng ban', $value) ?>
                </div>
                <div class="col-md-12">
                    <?php $value = !empty($trouble) ? $trouble->name_stage : '' ?>
                    <?php echo render_input('name_stage', 'Tên tổ - công đoạn', $value) ?>
                </div>
                <div class="col-md-12">
                    <?php $value = !empty($trouble) ? $trouble->name_task : '' ?>
                    <?php echo render_input('name_task', 'Tên công việc', $value) ?>
                </div>
                <div class="col-md-12">
                    <?php $arrViolationPoint = get_table_where('tbltrouble_violation_point', [], 'tbltrouble_violation_point.point ASC', 'result_array', '', '*, CONCAT( "trừ ", point, " điểm") as point') ?>
                    <?php $value = !empty($trouble) ? $trouble->trouble_violation_point_id : '' ?>
                    <?php echo render_select('trouble_violation', $arrViolationPoint, ['id', 'name', 'point'], 'trouble_violation', $value) ?>
                </div>
                <div class="col-md-12 mbot20">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab_reason">PHÂN TÍCH NGUYÊN NHÂN</a></li>
                        <li><a data-toggle="tab" href="#tab_procedure">QUY TRÌNH KHẮC PHỤC</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab_reason" class="tab-pane fade in active">
                            <table id="tb-material" class="table table-hover table-cs dataTable">
                                <thead>
                                <tr>
                                    <th class="text-center open" style="width: 50px;">
                                        <a class="hover-svg add-row" onclick="addRow('material')">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th>Nguyên phụ liệu (Material)</th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php if (!empty($trouble)) {
									foreach ($trouble->material as $key => $value) { ?>
                                        <tr>
                                            <td class="stt text-center"><?= ($key + 1) ?></td>
                                            <td>
                                                <input type="text" name="items[material][]" class="form-control material" value="<?= $value['name'] ?>" required>
                                            </td>
                                            <td class="text-center text-danger">
                                                <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'material')"></i>
                                            </td>
                                        </tr>
									<?php }
								} else { ?>
                                    <tr>
                                        <td class="stt text-center">1</td>
                                        <td>
                                            <input type="text" name="items[material][]" class="form-control material" placeholder="Nguyên phụ liệu (Material)" value="" required>
                                        </td>
                                        <td class="text-center text-danger">
                                            <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'material')"></i>
                                        </td>
                                    </tr>
								<?php } ?>
                                </tbody>
                            </table>
                            <table id="tb-man" class="table table-hover table-cs dataTable" style="margin-top: 15px!important;">
                                <thead>
                                <tr>
                                    <th class="text-center open" style="width: 50px;">
                                        <a class="hover-svg add-row" onclick="addRow('man')">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th>Nhân lực (Man)</th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php if (!empty($trouble)) {
									foreach ($trouble->man as $key => $value) { ?>
                                        <tr>
                                            <td class="stt text-center"><?= ($key + 1) ?></td>
                                            <td>
                                                <input type="text" name="items[man][]" class="form-control man" value="<?= $value['name'] ?>" required>
                                            </td>
                                            <td class="text-center text-danger">
                                                <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'man')"></i>
                                            </td>
                                        </tr>
									<?php }
								} else { ?>
                                    <tr>
                                        <td class="stt text-center">1</td>
                                        <td>
                                            <input type="text" name="items[man][]" class="form-control man" value="" placeholder="Nhân lực (Man)" required>
                                        </td>
                                        <td class="text-center text-danger">
                                            <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'man')"></i>
                                        </td>
                                    </tr>
								<?php } ?>
                                </tbody>
                            </table>
                            <table id="tb-machine" class="table table-hover table-cs dataTable" style="margin-top: 15px!important;">
                                <thead>
                                <tr>
                                    <th class="text-center open" style="width: 50px;">
                                        <a class="hover-svg add-row" onclick="addRow('machine')">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th>Máy móc (Machine)</th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php if (!empty($trouble)) {
									foreach ($trouble->machine as $key => $value) { ?>
                                        <tr>
                                            <td class="stt text-center"><?= ($key + 1) ?></td>
                                            <td>
                                                <input type="text" name="items[machine][]" class="form-control machine" value="<?= $value['name'] ?>" required>
                                            </td>
                                            <td class="text-center text-danger">
                                                <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'machine')"></i>
                                            </td>
                                        </tr>
									<?php }
								} else { ?>
                                    <tr>
                                        <td class="stt text-center">1</td>
                                        <td>
                                            <input type="text" name="items[machine][]" class="form-control machine" value="" placeholder="Máy móc (Machine)" required>
                                        </td>
                                        <td class="text-center text-danger">
                                            <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'machine')"></i>
                                        </td>
                                    </tr>
								<?php } ?>
                                </tbody>
                            </table>
                            <table id="tb-method" class="table table-hover table-cs dataTable" style="margin-top: 15px!important;">
                                <thead>
                                <tr>
                                    <th class="text-center open" style="width: 50px;">
                                        <a class="hover-svg add-row" onclick="addRow('method')">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th>Phương pháp (Method)</th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php if (!empty($trouble)) {
									foreach ($trouble->method as $key => $value) { ?>
                                        <tr>
                                            <td class="stt text-center"><?= ($key + 1) ?></td>
                                            <td>
                                                <input type="text" name="items[method][]" class="form-control method" value="<?= $value['name'] ?>" required>
                                            </td>
                                            <td class="text-center text-danger">
                                                <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'method')"></i>
                                            </td>
                                        </tr>
									<?php }
								} else { ?>
                                    <tr>
                                        <td class="stt text-center">1</td>
                                        <td>
                                            <input type="text" name="items[method][]" class="form-control method" value="" placeholder="Phương pháp (Method)" required>
                                        </td>
                                        <td class="text-center text-danger">
                                            <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'method')"></i>
                                        </td>
                                    </tr>
								<?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_procedure" class="tab-pane fade in">
                            <table id="tb-procedure" class="table table-hover table-cs dataTable">
                                <thead>
                                <tr>
                                    <th class="text-center open" style="width: 50px;">
                                        <a class="hover-svg add-row" onclick="addProcess('procedure')">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th>Quy trình khắc phục</th>
                                    <th>File đính kèm</th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php if (!empty($trouble)) {
									foreach ($trouble->procedure as $key => $value) { ?>
                                        <tr>
                                            <td class="stt text-center"><?= ($key + 1) ?></td>
                                            <td>
                                                <input type="hidden" name="id_procedure[<?= $key ?>]" class="form-control id_procedure" value="<?= $value['id'] ?>">
                                                <input type="text" name="items[procedure][<?= $key ?>]" class="form-control procedure" value="<?= $value['name'] ?>" required>
                                            </td>
                                            <td>
												<?php
												$getFile = get_table_where('tblfiles', ['rel_type' => 'trouble', 'rel_id' => $value['id']]);
												$viewFile = '';
												foreach ($getFile as $k => $v) {
													if (!empty($getFile)) {
														if (explode('/', $v['filetype'])[0] == 'image') {
															$viewFile .= '<div class="url_file" style="margin-bottom:5px; margin-top:5px;">
                                                                                    <div class="preview_image" style="width: auto;margin-bottom: 5px; margin-top: 5px;">		
                                                                                        <div class="display-block contract-attachment-wrapper img">
                                                                                            <span class="float-left">
                                                                                                <a href="' . base_url('uploads/trouble/' . $value['id'] . '/' . $v['file_name']) . '" data-lightbox="customer-profile" class="display-block mbot5 col-md-9">	
                                                                                                    <div class="">		                     
                                                                                                       <i class="fa fa-file-image-o" aria-hidden="true"></i> ' . $v['file_name'] . '
                                                                                                    </div>		                             
                                                                                                </a>
                                                                                                <a class="btn-icon col-md-2 text-center">
                                                                                                    <i class="fa fa-remove tnh-icon-remove pointer text-danger" onclick="removeFileProcess(' . $v['id'] . ', this)"></i>
                                                                                                </a>		          
                                                                                            </span>	
                                                                                        </div>		           
                                                                                    </div>
                                                                                    <hr class="mtop5 mbot5"/>
                                                                                </div>';
														} else {
															$viewFile .= '<div class="url_file">
                                                                                        <a class="col-md-9" target="_blank" href="' . base_url('uploads/trouble/' . $value['id'] . '/' . $v['file_name']) . '"><i class="fa fa-file-archive-o" aria-hidden="true"></i> ' . $v['file_name'] . '</a> 
                                                                                        <a class="btn btn-icon col-md-2">
                                                                                            <i class="fa fa-remove tnh-icon-remove pointer text-danger" onclick="removeFileProcess(' . $v['id'] . ', this)"></i>
                                                                                        </a>
                                                                                        <div class="clearfix"></div>
                                                                                        <hr class="mtop5 mbot5"/>
                                                                                 </div>';
														}
													}
												}
												?>
												<?= $viewFile ?>
                                                <input type="file" name="file[<?= $key ?>]" class="form-control" value="">
                                            </td>
                                            <td class="text-center text-danger">
                                                <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'procedure')"></i>
                                            </td>
                                        </tr>
									<?php }
								} else { ?>
                                    <tr>
                                        <td class="stt text-center">1</td>
                                        <td>
                                            <input type="text" name="items[procedure][0]" class="form-control procedure" placeholder="Quy trình khắc phục" value="" required>
                                        </td>
                                        <td>
                                            <input type="file" name="file[0]" class="form-control file" multiple="" value="" placeholder="File">
                                        </td>
                                        <td class="text-center text-danger">
                                            <i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, 'procedure')"></i>
                                        </td>
                                    </tr>
								<?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    init_selectpicker('refresh');
    $('#modal_trouble').modal('show');
    appValidateForm($('#form_trouble'), {
        code: 'required',
        name: 'required',
        trouble_violation: 'required',
    }, manage_trouble);

    function manage_trouble(form) {
        var formParams = $(form).serializeArray();
        var formData = new FormData();
        var data = $(form).serialize();
        var url = form.action;
        $.each($(form).find('input[type="file"]'), function (i, tag) {
            $.each($(tag)[0].files, function (i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function (i, val) {
            formData.append(val.name, val.value);
        });
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
        }).done(function (response) {
            if (response.success == true) {
                $('#modal_trouble').modal('hide');
            }
            alert_float(response.alert_type, response.message);
            $('.table-trouble').DataTable().ajax.reload();
        }).fail(function () {
            var error = JSON.parse(data.responseText);
            alert_float('danger', error.message);
        });
        return false;
    }

    function addRow(type) {
        var Tr = $(`<tr></tr>`);
        var tdSTT = $(`<td class="stt text-center"></td>`);
        var tdProcess = $(`<td><input type="text" name="items[${type}][]" class="form-control ${type}" value="" required></td>`);
        var tdRemove = $(`<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this, '${type}')"></i></td>`);
        Tr.append(tdSTT);
        Tr.append(tdProcess);
        Tr.append(tdRemove);
        $(`#tb-${type}`).find('tbody').append(Tr);
        orderStt($(`#tb-${type}`));
    }

    var countItems = <?=!empty($trouble->procedure) ? count($trouble->procedure) : 1?>;
    function addProcess(type) {
        tdNumbers = `<td class="stt text-center"></td>`;
        tdProcess = `<td>
                        <input type="text" name="items[${type}][${countItems}]" class="form-control form-control ${type}" value="">
                    </td>`;
        tdFile = `<td>
                    <input type="file" name="file[${countItems}]" class="form-control file" multiple value="" placeholder="<?= lang('File') ?>">
                </td>`;
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this)"></i></td>`;
        trProcess = `<tr>
            ${tdNumbers}
            ${tdProcess}
            ${tdFile}
            ${tdActions}
        </tr>`;
        $(`#tb-${type}`).find('tbody').append(trProcess);
        countItems++;
        orderStt($(`#tb-${type}`));
    }

    function orderStt(table) {
        var list_stt = $(table).find('tr').find('.stt');
        $.each(list_stt, function (index, value) {
            $(value).html((index + 1));
        })
    }

    function removeProcess(_this, type) {
        $(_this).parents('tr').remove();
        orderStt($(`#tb-${type}`));
    }

    function removeFileProcess(id, _this) {
        if (confirm('Bạn có muốn xóa file này?')) {
            $.get(admin_url + 'trouble/remove_file/' + id, function (result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                if (result.success) {
                    $(_this).parents('.url_file').remove();
                }
            })
        }
    }
</script>