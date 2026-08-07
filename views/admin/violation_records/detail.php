<?php init_head(); ?>
<style>
    #content {
        height: 600px;
    }

    .mce-edit-area {
        min-height: 230px !important;
    }

    .object_id button {
        height: auto !important;
    }
    .select2-choice {
        height: 100%!important;
    }
    .select2-chosen {
        word-break: break-word!important;
        height: auto!important;
        white-space: break-spaces!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
        </div>
    </div>
	<?php echo form_open('admin/violation_records/detail/' . (!empty($id) ? $id : ''), array('id' => 'form_detail_violation_records')); ?>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                            <tbody>
                            <tr>
                                <td width="25%">
                                    <label for="date" class="control-label"><?= _l('date') ?></label>
                                </td>
                                <td width="75%">
									<?php $value = !empty($violation_records) ? _dt($violation_records->date) : _dt(date('Y-m-d H:i:s')) ?>
									<?= render_date_input('date', '', $value) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="staff_id" class="control-label"><?= _l('Nhân viên') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($violation_records) ? $violation_records->staff_id : '' ?>
									<?= render_select('staff_id', !empty($staff) ? $staff : [], ['staffid', 'fullname'], '', $value) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="date" class="control-label"><?= _l('Loại vi phạm') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($violation_records) ? $violation_records->id_list_protocol : '' ?>
									<?= render_select('id_list_protocol', (!empty($list_protocol) ? $list_protocol : []), ['id', 'name', 'code'], '', $value) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="object_type" class="control-label"><?= _l('Loại phiếu liên quan') ?></label>
                                </td>
                                <td>
									<?php $object_type = [
										['id' => 'orders', 'name' => 'Đơn hàng'],
										['id' => 'productions_orders_detail', 'name' => 'Lệnh sản xuất chi tiết'],
										['id' => 'purchase_order', 'name' => 'Đơn hàng mua'],
										['id' => 'tasks', 'name' => 'Công việc'],
										['id' => 'qc', 'name' => 'QC'],
										['id' => 'production_report', 'name' => 'Phiếu báo cáo'],
									]; ?>
									<?php $value_object_type = !empty($violation_records) ? $violation_records->object_type : '' ?>
									<?= render_select('object_type', (!empty($object_type) ? $object_type : []), ['id', 'name'], '', $value_object_type) ?>
                                </td>
                            </tr>
							<?php $value_object_id = !empty($violation_records) ? $violation_records->object_id : '' ?>
							<?php if ($value_object_type == 'productions_orders_detail') { ?>
                                <tr class="object_id">
                                    <td>
                                        <label for="object_id" class="control-label"><?= _l('Phiếu chứng từ') ?></label>
                                    </td>
                                    <td>
                                        <div class="form-group" app-field-wrapper="object_id">
                                            <select id="object_id" name="object_id" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                <option></option>
                                                <?php if(!empty($object_id)) {
                                                   foreach($object_id as $key => $value) {?>
                                                        <optgroup label="<?=$value[0]['code']?>">
                                                            <?php foreach($value as $k => $v) {
																$subtext = '<b>Thành phẩm:</b> ' . $v['items_code'] . ' (' . $v['items_name'] . ')';
																if (!empty($v['code_orders'])) {
																	$subtext = '<b>Mã đơn hàng:</b> ' . $v['code_orders'] . '<br/>' . $subtext;
																}
																$subtext = '<b>Lệnh SXCT: </b>' . $v['code_detail'] . '<br/>' . $subtext;
																$selected = $v['id_detail'] == $value_object_id ? 'selected' : '';
																echo '<option value="' . $v['id_detail'] . '" ' . $selected . ' data-content="' . $subtext . '"></option>';
															}?>

                                                        </optgroup>
												   <?php }
												}?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="tr_stages">
                                    <td><label for="stages" class="control-label">Công đoạn</label></td>
                                    <td>
										<?php $value = !empty($violation_records) ? $violation_records->stages : '' ?>
										<?= render_select('stages', (!empty($stages) ? $stages : []), ['id', 'name'], '', $value) ?>
                                    </td>
                                </tr>
							<?php } else { ?>
                                <tr class="object_id">
                                    <td>
                                        <label for="object_id" class="control-label"><?= _l('Phiếu chứng từ') ?></label>
                                    </td>
                                    <td>
										<?= render_select('object_id', (!empty($object_id) ? $object_id : []), ['id', 'code'], '', $value_object_id) ?>
                                    </td>
                                </tr>
							<?php } ?>
                            <tr>
                                <td><?= lang('tnh_cal_kpi', 'cal_kpi') ?></td>
                                <td>
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="cal_kpi" id="is_cal_kpi" <?= !empty($violation_records) && $violation_records->cal_kpi ? 'checked' : '' ?> value="1">
                                        <label for="is_cal_kpi"><?= lang('choose') ?></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('tnh_kpi_criteria', 'kpi_criteria') ?></td>
                                <td>
                                    <select name="kpi_criteria" id="kpi_criteria" data-placeholder="<?= lang('tnh_kpi_criteria') ?>" class="" style="width: 100%;">
                                        <option value=""></option>
                                        <?php if(!empty($kpi_criteria)): ?>
                                            <?php foreach($kpi_criteria as $key => $value): ?>
                                                <option <?= !empty($violation_records) && $violation_records->kpi_criteria == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['criteria'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="note" class="control-label"><?= _l('note') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($violation_records) ? $violation_records->note : '' ?>
									<?php echo render_textarea('note', '', $value); ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('Nội dung vi phạm') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                            <tbody>
                            <tr>
                                <td style="width: 80%;">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" value="1" id="detail_data" <?= !empty($violation_records) ? 'checked' : '' ?>">
                                        <label for="detail_data" data-toggle="tooltip" data-original-title="" title="">Chỉnh sửa</label>
                                    </div>
                                    <div style="border: 1px solid black; padding: 10px" class="divContent <?= empty($violation_records) ? 'hide' : '' ?>"><?= !empty($violation_records) ? $violation_records->data_content : '' ?></div>
                                    <div class="dataContent <?= !empty($violation_records) ? 'hide' : '' ?>">
										<?php $value = !empty($violation_records) ? $violation_records->content : '' ?>
										<?php echo render_textarea('content', '', $value, array('rows' => 10, 'data-entities-encode' => 'true'), array(), '', 'tinymce'); ?>
                                    </div>
                                </td>
                                <td>
                                    <p>Tên khách hàng<span class="pull-right"><a class="add_merge_field pointer">{fullname}</a></span>
                                    </p>
                                    <p>Chức vụ<span class="pull-right"><a class="add_merge_field pointer">{name_role}</a></span>
                                    </p>
                                    <p>Phòng ban<span class="pull-right"><a class="add_merge_field pointer">{name_departments}</a></span>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel panel-primary" style="padding-bottom: 30px;">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('Hình thức xử lý') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                            <tbody>
                            <tr>
                                <td>
									<?php $value = !empty($violation_records) ? $violation_records->forms_processing : '' ?>
									<?php echo render_textarea('forms_processing', '', $value, array('rows' => 10, 'data-entities-encode' => 'true'), array(), '', 'tinymce'); ?>
                                </td>
                            </tr>
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
            <button type="submit" class="btn btn-info add-order">
				<?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
	<?php echo form_close(); ?>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script>
    var stages = <?=!empty($stages) ? json_encode($stages) : '{}'?>;
    appValidateForm($('#form_detail_violation_records'), {
        date: 'required',
        staff_id: 'required',
        id_list_protocol: 'required',
        object_type: 'required',
        object_id: 'required',
    });
    $('.add_merge_field').on('click', function (e) {
        e.preventDefault();
        tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).text());
    });
    $('body').on('change', '#id_list_protocol', function () {
        var id_list_protocol = $('#id_list_protocol').val();
        $.get(admin_url + 'violation_records/get_info_list_protocol/' + id_list_protocol, function (data) {
            var data = JSON.parse(data);
            if(data.content) {
                tinymce.get("content").setContent(data.content);
                $('#detail_data').trigger('change');
            }
            else {
                tinymce.get("content").setContent('');
                $('#detail_data').trigger('change');
            }
            // var parentEditor = parent.tinyMCE.activeEditor;
            // parentEditor.execCommand('mceInsertRawHTML', false, data.content);


        })
    })
    $('body').on('change', '#object_type', function () {
        var object_type = $('#object_type').val();
        var staff_id = $('#staff_id').val();
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['object_type'] = object_type;
        data['staff_id'] = staff_id;
        if (object_type != 'productions_orders_detail') {
            $.post(admin_url + 'violation_records/get_info_object_type/' + object_type, data, function (data) {
                var data = JSON.parse(data);
                $('#object_id').html('<option></option>');
                $.each(data, function (index, value) {
                    $('#object_id').append(`<option value="${value.id}">${value.code}</option>`);
                })
                $('#object_id').selectpicker('refresh');
            })
            $('.tr_stages').remove();
        } else {
            $.post(admin_url + 'violation_records/get_info_productions_orders', data, function (data) {
                var data = JSON.parse(data);
                $('#object_id').html('<option></option>');
                $.each(data, function (index, value) {
                    var optgroup = $(`<optgroup label="${value[0].code}"></optgroup>`);
                    $.each(value, function (i, v) {
                        var subtext = '<b>Thành phẩm:</b> ' + v.items_code + ' (' + v.items_name + ')';
                        if (v.code_orders) {
                            subtext = '<b>Mã đơn hàng:</b> ' + v.code_orders + '<br/>' + subtext;
                        }
                        subtext = '<b>Lệnh SXCT: </b>' + v.code_detail + '<br/>' + subtext;
                        optgroup.append(`<option value="${v.id_detail}" data-content="${subtext}">${v.code_detail}</option>`);
                    })
                    $('#object_id').append(optgroup);
                })
                $('#object_id').selectpicker('refresh');
                var tr_stages = $(`<tr class="tr_stages"></tr>`);
                var select_stages = $(`<select name="stages" id="stages" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" ></select>`);
                select_stages.append(`<option></option>`);
                $.each(stages, function (index, value) {
                    select_stages.append(`<option value="${value.id}">${value.name}</option>`);
                })
                tr_stages.append('<td><label for="stages" class="control-label">Công đoạn</label></td>');
                var tdStages = $(`<td></td>`);
                tdStages.append(select_stages);
                tr_stages.append(tdStages);
                $('tr.object_id').after(tr_stages);
                init_selectpicker();
            })
        }
    })

    $('#detail_data').change(function () {
        if ($(this).prop('checked')) {
            $('.divContent').addClass('hide');
            $('.dataContent').removeClass('hide');
        } else {
            $('.dataContent').addClass('hide');
            $('.divContent').removeClass('hide');
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['content'] = tinymce.get("content").getContent();
            data['staff_id'] = $('#staff_id').val();
            $.post(admin_url + 'violation_records/get_data_html_content', data, function (data_content) {
                $('.divContent').html(data_content);
            })
        }
    })

    $(document).ready(function () {
        $('#kpi_criteria').select2({allowClear: true});
    });
</script>
