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
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
        </div>
    </div>
	<?php echo form_open('admin/decision/detail_list/' . (!empty($id) ? $id : ''), array('id' => 'form_detail_decision')); ?>
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
									<?php $value = !empty($decision) ? _dt($decision->date) : _dt(date('Y-m-d H:i:s')) ?>
									<?= render_date_input('date', '', $value) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="staff_id" class="control-label"><?= _l('Nhân viên') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($decision) ? $decision->staff_id : '' ?>
									<?= render_select('staff_id', !empty($staff) ? $staff : [], ['staffid', 'fullname'], '', $value) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="date" class="control-label"><?= _l('Loại quyết định') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($decision) ? $decision->id_category : '' ?>
									<?= render_select('id_category', (!empty($category) ? $category : []), ['id', 'name', 'code'], '', $value) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="note" class="control-label"><?= _l('note') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($decision) ? $decision->note : '' ?>
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
                        <h3 class="panel-title"><?= lang('Nội dung quyết định') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                            <tbody>
                            <tr>
                                <td style="width: 80%;">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" value="1" id="detail_data" <?= !empty($decision) ? '' : 'checked' ?>>
                                        <label for="detail_data" data-toggle="tooltip" data-original-title="" title="">Chỉnh sửa</label>
                                    </div>
                                    <div style="border: 1px solid black; padding: 10px" class="divContent <?= empty($decision) ? 'hide' : '' ?>"><?= !empty($decision) ? $decision->data_content : '' ?></div>
                                    <div class="dataContent <?= !empty($decision) ? 'hide' : '' ?>">
										<?php $value = !empty($decision) ? $decision->content : '' ?>
										<?php echo render_textarea('content', '', $value, array('rows' => 10, 'data-entities-encode' => 'true'), array(), '', 'tinymce'); ?>
                                    </div>
                                </td>
                                <td>
                                    <p>Tên khách hàng<span class="pull-right"><a class="add_merge_field pointer">{fullname}</a></span></p>
                                    <p>Sinh nhật<span class="pull-right"><a class="add_merge_field pointer">{birthday}</a></span></p>
                                    <p>Nơi sinh<span class="pull-right"><a class="add_merge_field pointer">{birthplace}</a></span></p>
                                    <p>Quê quán<span class="pull-right"><a class="add_merge_field pointer">{domicile}</a></span></p>
                                    <p>CMND<span class="pull-right"><a class="add_merge_field pointer">{cmnd_id_passport}</a></span></p>
                                    <p>Ngày cấp<span class="pull-right"><a class="add_merge_field pointer">{date_range}</a></span></p>
                                    <p>Nơi cấp<span class="pull-right"><a class="add_merge_field pointer">{issued_by}</a></span></p>
                                    <p>Quốc tịch<span class="pull-right"><a class="add_merge_field pointer">{nationality}</a></span></p>
                                    <p>Mã số thuế<span class="pull-right"><a class="add_merge_field pointer">{personal_tax_code}</a></span></p>
                                    <p>Thường trú<span class="pull-right"><a class="add_merge_field pointer">{resident}</a></span></p>
                                    <p>Chổ ở hiện nay<span class="pull-right"><a class="add_merge_field pointer">{current_accommodation}</a></span></p>
                                    <p>Chức vụ<span class="pull-right"><a class="add_merge_field pointer">{name_role}</a></span></p>
                                    <p>Phòng ban<span class="pull-right"><a class="add_merge_field pointer">{name_departments}</a></span></p>
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
    appValidateForm($('#form_detail_decision'), {
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
    $('body').on('change', '#id_category', function () {
        var id_category = $('#id_category').val();
        $.get(admin_url + 'decision/get_info_category/' + id_category, function (data) {
            var data = JSON.parse(data);
            if(data.content) {
                tinymce.get("content").setContent(data.content);
                $('#detail_data').trigger('change');
            }
            else {
                tinymce.get("content").setContent('');
                $('#detail_data').trigger('change');
            }
        })
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
            $.post(admin_url + 'decision/get_data_html_content', data, function (data_content) {
                $('.divContent').html(data_content);
            })
        }
    })
</script>
