<div class="modal fade" id="add_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 95%;">
        <?php echo form_open(admin_url('plan_propose/add/'), array('id' => 'add-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input class="hide" id="id_plan_propose" value="<?= !empty($plan_propose) ? $plan_propose->id : '' ?>">
                        <div class="tabset">
                            <!-- Tab 1 -->
                            <input type="radio" name="tabset" id="tab1" aria-controls="info" checked>
                            <label for="tab1"><?= lang('Thông tin chung') ?></label>
                            <!-- Tab 2 -->
                            <input class="infodetail_items hide" type="radio" name="tabset" id="tab2" aria-controls="detail_items">
                            <label class="infodetail_items hide" for="tab2"><?= lang('Thông tin chi tiết') ?></label>
                            <!-- Tab 3 -->
                            <input class="infodetail_time hide" type="radio" name="tabset" id="tab3" aria-controls="detail_time">
                            <label class="infodetail_time hide" for="tab3"><?= lang('Thông tin thời gian') ?></label>
                            <div class="tab-panels">
                                <section id="info" class="tab-panel">
                                    <div class="hide">
                                        <input type="" id="id" name="id" class="form-control" autocomplete="off" value="<?= !empty($plan_propose) ? $plan_propose->id : '' ?>">
                                    </div>
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                        <tbody>
                                            <tr>
                                                <!-- Mã đề xuất -->
                                                <td style="width: 17%;">
                                                    <label for="code" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_code_plan_propose'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $code = !empty($plan_propose) ? ($plan_propose->code) : '' ?>
                                                    <?php echo form_input('code', $code, 'placeholder="' . lang('Mã tự động') . '" id="code" class="form-control input-tip" readonly'); ?>
                                                </td>
                                                <!-- Ngày đề xuất -->
                                                <td style="width: 17%;">
                                                    <label for="date" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_date_plan_propose'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value = !empty($plan_propose) ? _dhau($plan_propose->date) : '' ?>
                                                    <?php echo render_date_input('date', '', $value); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <!-- Người đề xuất -->
                                                <td style="width: 17%;">
                                                    <label for="staff" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_staff_plan_propose'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value_staff = !empty($plan_propose) ? ($plan_propose->staff) : '' ?>
                                                    <select name="staff" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('staff') ?>" id="staff" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($staff_list_all)) : ?>
                                                            <?php foreach ($staff_list_all as $key => $value) : ?>
                                                                <option <?= ($value_staff == $value['staffid'] ? 'selected' : '') ?> data-department="<?= $value['name_department'] ?>" value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                                <!-- Người đề xuất -->
                                                <td style="width: 17%;">
                                                    <label for="staff" class="control-label">
                                                        <?php echo _l('Người duyệt'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value = !empty($plan_propose) ? $plan_propose->staff_assigned : [] ?>
                                                    <?php echo render_select('staff_assigned[]', (!empty($staff_list) ? $staff_list : []), ['staffid', ['firstname', 'lastname']], '', $value, ['multiple' => true]) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 17%;">
                                                    <label for="category_tasks" class="control-label">
                                                        <?php echo _l('Mã công việc'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value_category_tasks = !empty($plan_propose) ? ($plan_propose->category_tasks) : '' ?>
                                                    <select name="category_tasks" id="category_tasks" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Mã công việc') ?>" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($category_tasks)) : ?>
                                                            <?php foreach ($category_tasks as $key => $value) : ?>
                                                                <option <?= ($value_category_tasks == $value['id'] ? 'selected' : '') ?> data-subtext="<?= $value['content'] ?>" data-departments="<?= $value['departments'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                                <td style="width: 17%;">
                                                    <label for="category_tasks" class="control-label">
                                                        <?php echo _l('Phòng ban công việc'); ?>
                                                    </label>
                                                </td>
                                                <td class="txt-type_name">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="id_branch" class="control-label">
                                                        <?php echo _l('id_branch'); ?>
                                                    </label>
                                                </td>
                                                <?php
                                                if (empty($branch)) {
                                                    $branch = get_table_where('tblbranch');
                                                }
                                                ?>
                                                <?php $value = !empty($plan_propose) ? $plan_propose->id_branch : '0'; ?>
                                                <td><?php echo render_select('id_branch', (!empty($branch) ? $branch : []), ['id', 'name'], '', $value) ?></td>
                                                <td style="width: 17%;">
                                                    <label for="type_plan_propose" class="control-label">
                                                        <?php echo _l('ch_type_plan_propose'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $value_type_plan_propose = !empty($plan_propose) ? ($plan_propose->type_plan_propose) : '' ?>
                                                    <select name="type_plan_propose" id="type_plan_propose" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('ch_type_plan_propose') ?>" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($type_plan_propose)) : ?>
                                                            <?php foreach ($type_plan_propose as $key => $value) : ?>
                                                                <option <?= ($value_type_plan_propose == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 17%;">
                                                    <label for="money" class="control-label">
                                                        <?php echo _l('Tổng ngân sách'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $money = !empty($plan_propose) ? ($plan_propose->money) : 0 ?>
                                                    <input type="text" id="money" onkeyup="formatNumBerKeyUp(this)" name="money" class="form-control " value="<?= number_format_data($money) ?>">
                                                </td>
                                                <td class="type_plan_propose_train hide loai" style="width: 17%;">
                                                    <label for="type_train" class="control-label">
                                                        <?php echo _l('Loại đào tạo'); ?>
                                                    </label>
                                                </td>
                                                <td class="type_plan_propose_train hide loai">
                                                    <?php
                                                    $type_train = array(
                                                        array(
                                                            'id' => 1,
                                                            'name' => 'Đào Tạo Nhân Viên Mới'
                                                        ),
                                                        array(
                                                            'id' => 2,
                                                            'name' => 'Đào Tạo Lại Nhân Viên Cũ'
                                                        ),
                                                        array(
                                                            'id' => 3,
                                                            'name' => 'Đào Tạo Nâng Cấp Nhân Viên Cũ'
                                                        ),
                                                        array(
                                                            'id' => 4,
                                                            'name' => 'Đào Tạo Định Kỳ'
                                                        )
                                                    );
                                                    ?>
                                                    <?php $value_type_train = !empty($plan_propose) ? ($plan_propose->type_train) : '' ?>
                                                    <select name="type_train" id="type_train" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Loại đào tạo') ?>" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($type_train)) : ?>
                                                            <?php foreach ($type_train as $key => $value) : ?>
                                                                <option <?= ($value_type_train == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                                <td class="type_plan_propose_repair hide loai" style="width: 17%;">
                                                    <label for="type_repair" class="control-label">
                                                        <?php echo _l('Loại sửa chữa'); ?>
                                                    </label>
                                                </td>
                                                <td class="type_plan_propose_repair hide loai">
                                                    <?php
                                                    $type_repair = array(
                                                        array(
                                                            'id' => 1,
                                                            'name' => 'Thiết Bị Bảo Dưỡng Định Kỳ'
                                                        ),
                                                        array(
                                                            'id' => 2,
                                                            'name' => 'Thiết Bị Hiệu Chuẩn Định Kỳ'
                                                        ),
                                                        array(
                                                            'id' => 3,
                                                            'name' => 'Thiết Bị Thay Thế Vật Tư Định Kỳ'
                                                        ),
                                                        array(
                                                            'id' => 4,
                                                            'name' => 'Thiết Bị Hư Đột Xuất'
                                                        )
                                                    );
                                                    ?>
                                                    <?php $value_type_repair = !empty($plan_propose) ? ($plan_propose->type_repair) : '' ?>
                                                    <select name="type_repair" id="type_repair" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Loại đào tạo') ?>" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($type_repair)) : ?>
                                                            <?php foreach ($type_repair as $key => $value) : ?>
                                                                <option <?= ($value_type_repair == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>

                                                <td class="type_plan_propose_items hide loai" style="width: 17%;">
                                                    <label for="type_items" class="control-label">
                                                        <?php echo _l('Nhóm mặt hàng'); ?>
                                                    </label>
                                                </td>
                                                <td class="type_plan_propose_items hide loai">
                                                    <?php $value_type_items = !empty($plan_propose) ? ($plan_propose->type_items) : 0 ?>
                                                    <input type="text" name="type_items" data-id="<?= $value_type_items ?>" id="type_items" class="type_items" style="width: 100%;" data-placeholder="<?= _l('choose') ?>" value="<?= $value_type_items ?>">
                                                </td>


                                                <td class="type_plan_propose_recruit hide loai" style="width: 17%;">
                                                    <label for="type_recruit" class="control-label">
                                                        <?php echo _l('Loại tuyển dụng'); ?>
                                                    </label>
                                                </td>
                                                <td class="type_plan_propose_recruit hide loai">
                                                    <?php
                                                    $type_recruit = array(
                                                        array(
                                                            'id' => 1,
                                                            'name' => 'Mới'
                                                        ),
                                                        array(
                                                            'id' => 2,
                                                            'name' => 'Thay Thế Tạm'
                                                        ),
                                                        array(
                                                            'id' => 3,
                                                            'name' => 'Thay Thế Nghỉ Việc'
                                                        ),
                                                        array(
                                                            'id' => 4,
                                                            'name' => 'Thay Thế Thuyên Chuyển'
                                                        )
                                                    );
                                                    ?>
                                                    <?php $value_type_recruit = !empty($plan_propose) ? ($plan_propose->type_recruit) : '' ?>
                                                    <select name="type_recruit" id="type_recruit" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Loại đào tạo') ?>" class="selectpicker">
                                                        <option value=""></option>
                                                        <?php if (!empty($type_recruit)) : ?>
                                                            <?php foreach ($type_recruit as $key => $value) : ?>
                                                                <option <?= ($value_type_recruit == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>

                                            </tr>
                                            <!-- Nội dung -->
                                            <tr>
                                                <td colspan="4">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <?php $content = !empty($plan_propose) ? ($plan_propose->content) : '' ?>
                                                            <?= lang('internal_proposal_content') ?>
                                                            <?php echo form_textarea('content', $content, 'placeholder="' . lang('internal_proposal_content') . '" id="content" class="form-control input-tip tinymce"'); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <div class="col-md-12">
                                                        <div class="dropzone dropzone-manual">
                                                            <div id="dropzoneTaskComment" class="dropzoneDragArea dz-default dz-message task-comment-dropzone">
                                                                <span><?php echo _l('drop_files_here_to_upload'); ?></span>
                                                            </div>
                                                            <div class="dropzone-task-comment-previews dropzone-previews"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </section>
                                <section id="detail_items" class="tab-panel">
                                    <div class="col-md-12 mtop5">
                                        <div id="detail_items"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </section>
                                <section id="detail_time" class="tab-panel">
                                    <div class="col-md-12 mtop5">
                                        <div id="detail_time"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    $('#add_modal').modal({
        show: true,
        backdrop: 'static'
    });

    function ajaxSelectCallBack_type_items(element, url, id, types = '') {
        if (id != 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types:  $('#type_plan_propose').val(),
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types:  $('#type_plan_propose').val(),
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        }
    }
    ajaxSelectCallBack_type_items($('input#type_items'), 'admin/plan_propose/searchTypeItems', $('#type_items').val());

    init_selectpicker();
    init_datepicker();
    init_editor('textarea[name="content"]');
    var key_departments = <?= !empty($key_departments) ? json_encode($key_departments) : '[]' ?>;
    // file upload
    Dropzone.options.expenseForm = false;
    var expenseDropzone;
    if ($('#dropzoneTaskComment').length > 0) {
        expenseDropzone = new Dropzone('#add-form', appCreateDropzoneOptions({
            paramName: "file",
            autoProcessQueue: false,
            previewsContainer: '.dropzone-previews',
            addRemoveLinks: true,
            maxFiles: 10,
            clickable: '#dropzoneTaskComment',
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // window.location.reload();
                }
            }
        }));
    }
    $('#id_other_payslips').change(function() {
        var total = $(this).find('option:selected').data('subtext');
        $('#money').val(tnhFormatNumber(total));
    })
    $('#id_service, #id_purchase_order').change(function() {
        var total_service = 0;
        if ($('#id_service').find('option:selected').data('subtext')) {
            var total_service = $('#id_service').find('option:selected').data('subtext');
        }
        var total_purchase_order = 0;
        if ($('#id_purchase_order').find('option:selected').data('total')) {
            total_purchase_order = $('#id_purchase_order').find('option:selected').data('total');
        }
        $('#money').val(tnhFormatNumber(total_service + total_purchase_order));
    })
    // Chọn người đề xuất
    $('#staff').change(function(event) {
        department = $("#staff").select().find(":selected").data("department");
        // alert(department);
        $('.txt-department').html(department);
    })
    // Chọn Loại đề xuất
    $('#category_tasks').change(function(event) {
        departments = $("#category_tasks").find("option:selected").data("departments");
        var list = [];
        if (departments) {
            departments = departments + '';
            var list = departments ? departments.split(",") : '';
        }
        var subtext = "";
        $.each(list, function(index, value) {
            if (key_departments[value]) {
                subtext += key_departments[value] + ',';
            }
        })
        $('.txt-type_name').html(subtext);
    })

    $('#category_tasks').trigger('change');
    $(function() {
        $('#type_plan_propose').change();
        appValidateForm($('#add-form'), {
            date: 'required',
            staff: 'required',
            category_tasks: 'required',
            type_plan_propose: 'required',
            id_branch: 'required',
        }, manage);

        function manage(form) {
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();
            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(expenseDropzone.files, function(index, value) {
                formData.append('file[]', value);
            })
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });
            var button = $(form).find('button[type="submit"]');
            button.button({
                loadingText: 'please wait...'
            });
            button.button('loading');

            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(response) {
                    if (response.success == true) {
                        alert_float('success', response.message);
                    } else {
                        alert_float('danger', response.message);
                    }
                    oTable.draw();
                    $('#add_modal').modal('hide');
                })
                .always(function() {
                    button.button('reset');
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                    button.button('reset');
                });
            return false;
        }
    });

    $(document).on('hide.bs.modal', '#add_modal', function() {
        tinyMCE.remove();
    });
</script>