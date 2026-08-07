<style>
    .select2-choice {
        height: auto !important;
    }
</style>
<div class="modal fade" id="add_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 85%;">
        <?php echo form_open(admin_url('internal_proposal/add'), array('id' => 'add-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo $title; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="hide">
                            <input type="" id="id" name="id" class="form-control" autocomplete="off" value="<?php echo $object->id ?>">
                        </div>
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                            <tbody>
                                <tr class="text-center bold uppercase">
                                    <td colspan="4"><?= lang('tnh_info_general') ?></td>
                                </tr>
                                <tr>
                                    <!-- Mã đề xuất -->
                                    <td style="width: 17%;">
                                        <label for="code" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('intpro_code'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo form_input('code', $object->code, 'placeholder="' . lang('intpro_code') . '" id="code" class="form-control input-tip"'); ?>
                                    </td>
                                    <!-- Ngày đề xuất -->
                                    <td style="width: 17%;">
                                        <label for="date" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('intpro_date'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo render_datetime_input('date', '', $object->date); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- Người đề xuất -->
                                    <td style="width: 17%;">
                                        <label for="staff" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('intpro_staff'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <select name="staff" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('staff') ?>" id="staff" class="selectpicker">
                                            <option value=""></option>
                                            <?php if (!empty($staff_list_all)) : ?>
                                                <?php foreach ($staff_list_all as $key => $value) : ?>
                                                    <option <?= ($object->staff == $value['staffid'] ? 'selected' : '') ?> data-department="<?= $value['name_department'] ?>" value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
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
                                        <?php $value = !empty($object->staff_assigned) ? $object->staff_assigned : [] ?>
                                        <?php echo render_select('staff_assigned[]', (!empty($staff_list) ? $staff_list : []), ['staffid', ['firstname', 'lastname']], '', $value, ['multiple' => true]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= lang('Bộ phận', 'id_departments') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $departmentsN = $this->site_model->getDepartmentsActive([1]);
                                        $value = !empty($object->id_departments) ? $object->id_departments : ''
                                        ?>
                                        <?= render_select('id_departments', (!empty($departmentsN) ? $departmentsN : []), ['departmentid', 'name'], '', $value) ?>
                                    </td>
                                    <td>
                                        <?= lang('Lọc mã công việc theo chức vụ', 'search_role') ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($object->id_departments)) {
                                            $this->db->where('tblroles.departments_id', $object->id_departments);
                                            $data_roles = $this->db->get('tblroles')->result_array();
                                        }
                                        ?>
                                        <select id="role_id" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option></option>
                                            <?php if (!empty($data_roles)) {
                                                foreach ($data_roles as $key => $value) { ?>
                                                    <option value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 17%;">
                                        <label for="category_tasks" class="control-label">
                                            <?php echo _l('Mã công việc'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <select name="category_tasks" id="category_tasks" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Mã công việc') ?>" class="selectpicker">
                                            <option value=""></option>
                                            <?php if (!empty($category_tasks)) : ?>
                                                <?php foreach ($category_tasks as $key => $value) : ?>
                                                    <option <?= ((!empty($object->category_tasks) && $object->category_tasks == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['content'] ?>" data-departments="<?= $value['departments'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
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
                                    <td style="width: 17%;">
                                        <label for="type_plan_propose" class="control-label">
                                            <?php echo _l('Loại kế hoạch'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <select name="type_plan_propose" id="type_plan_propose" data-width="100%" data-none-selected-text="Không có mục nào được chọn" require="true" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Mã công việc') ?>" class="selectpicker">
                                            <option></option>
                                            <?php if (!empty($type_plan_propose)) : ?>
                                                <?php foreach ($type_plan_propose as $key => $value) : ?>
                                                    <option <?= ((!empty($object->type_plan_propose) && $object->type_plan_propose == $value['id']) ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= lang('Nhóm đề xuất', 'recommended_list_group_id') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $dtRecommendedListG = $this->recommended_list_model->getRecommendedListParent([0]);
                                        ?>
                                        <select name="recommended_list_group_id" id="recommended_list_group_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Nhóm đề xuất') ?>" class="selectpicker">
                                            <option value=""></option>
                                            <?php if (!empty($dtRecommendedListG)) : ?>
                                                <?php foreach ($dtRecommendedListG as $key => $value) : ?>
                                                    <option <?= ((!empty($object->recommended_list_group_id) && $object->recommended_list_group_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?= lang('Chi tiết đề xuất', 'recommended_list_id') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $dtRecommendedList = null;
                                        if (!empty($object->recommended_list_group_id)) {
                                            $dtRecommendedList = $this->recommended_list_model->getRecommendedListParent([$object->recommended_list_group_id]);
                                        }
                                        ?>
                                        <select name="recommended_list_id" id="recommended_list_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Chi tiết đề xuất') ?>" class="selectpicker" multiple>
                                            <option value=""></option>
                                            <?php if (!empty($dtRecommendedList)) : ?>
                                                <?php foreach ($dtRecommendedList as $key => $value) : ?>
                                                    <option <?= ((!empty($object->recommended_list_id) && $object->recommended_list_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 17%;">
                                        <label for="id_purchases" class="control-label">
                                            <?php echo _l('Phiếu yêu cầu mua hàng'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = (!empty($object->id_purchases) && $object->id_purchases == -1) ? $internal_proposal_purchase : (!empty($object->id_purchases) ? [$object->id_purchases] : [])  ?>
                                        <?php echo render_select('id_purchases[]', (!empty($purchases) ? $purchases : []), ['id', ['prefix', 'code'], 'explanation'], '', $value, ['onchange' => 'loadItemsPurchase()', 'multiple' => true, 'data-actions-box' => true], array(), '', '', false) ?>
                                    </td>
                                    <td class="hide" style="width: 17%;">
                                        <label for="id_purchase_order" class="control-label">
                                            <?php echo _l('Phiếu mua hàng (PO)'); ?>
                                        </label>
                                    </td>
                                    <td class="hide">
                                        <?php $value = !empty($object->id_purchase_order) ? $object->id_purchase_order : '' ?>
                                        <select id="id_purchase_order" name="id_purchase_order" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option value=""></option>
                                            <?php if (!empty($purchase_order)) { ?>
                                                <?php foreach ($purchase_order as $key => $value_purchase_order) { ?>
                                                    <option value="<?= $value_purchase_order['id'] ?>" <?= $value_purchase_order['id'] == $value ? 'selected' : '' ?> data-subtext="<?= $value_purchase_order['company'] ?> - <?= number_format_data($value_purchase_order['total_dqd']) ?>" data-total="<?= $value_purchase_order['total_dqd'] ?>"><?= $value_purchase_order['fullcode'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td style="width: 17%;">
                                        <label for="id_service" class="control-label">
                                            <?php echo _l('Phiếu dịch vụ'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->id_service) ? $object->id_service : '' ?>
                                        <?php echo render_select('id_service', (!empty($services) ? $services : []), ['id', ['prefix', 'code'], 'subtotal'], '', $value) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- Số tiền -->
                                    <td style="width: 17%;">
                                        <label for="money" class="control-label">
                                            <?php echo _l('intpro_money'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" id="money" onkeyup="formatNumBerKeyUp(this)" name="money" class="form-control " value="<?= number_format_data($object->money) ?>">
                                    </td>
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
                                    <td><?php echo render_select('id_branch', (!empty($branch) ? $branch : []), ['id', 'name'], '', (!empty($object->id_branch) ? $object->id_branch : 0)) ?></td>
                                </tr>
                                <tr class="hide">
                                    <td style="width: 17%;">
                                        <label for="id_other_payslips" class="control-label">
                                            <?php echo _l('Phiếu chi khác'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->id_other_payslips) ? $object->id_other_payslips : '' ?>
                                        <?php echo render_select('id_other_payslips', (!empty($other_payslips) ? $other_payslips : []), ['id', 'fullcode', 'total'], '', $value) ?>
                                    </td>
                                    <td style="width: 17%;">
                                        <label for="type_object" class="control-label">
                                            <?php echo _l('Liên quan đến'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php $value = !empty($object->type_object) ? $object->type_object : '' ?>
                                        <select id="type_object" name="type_object" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option value=""></option>
                                            <?php if (!empty($type_object)) { ?>
                                                <?php foreach ($type_object as $id_type => $name_type) { ?>
                                                    <option value="<?= $id_type ?>" <?= $id_type == $value ? 'selected' : '' ?>><?= $name_type ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <!-- Chi tiết ycmh -->
                                <tr>
                                    <td colspan="4">
                                        <div class="col-md-12">
                                            <table style="width: 50%;float: right;table-layout: fixed;" class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 25%">
                                                            <a type="submit" href="<?= base_url('uploads/import_inventory.xlsx?vs=1.0') ?>" class="btn btn-success">Tải mẫu import</a>
                                                        </td>
                                                        <td style="width: 50%">
                                                            <?php echo render_input('file_csv', '', '', 'file'); ?>
                                                        </td>
                                                        <td style="width: 25%">
                                                            <a href="#" id="import_export_client" class="btn btn-warning btn-icon" style="float: right;"><?= _l('import mặt hàng') ?></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <br>
                                            <div class="form-group">
                                                <?= lang('Chi tiết yêu cầu') ?>
                                                <table class="dt-tnh table item-purchases table-bordered table-hover" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="200" class="text-left"></i> <?php echo _l('ch_items_name_t'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng đề xuất'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="110" class="text-center"><?php echo _l('Số lượng PO ĐV chuẩn'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="110" class="text-center"><?php echo _l('Số lượng PO ĐV kho'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV thanh toán'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Đơn giá'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thuế'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thành tiền'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="150" class="text-center"><?php echo _l('Nhà cung cấp'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Ghi chú đề xuất'); ?></th>
                                                            <th style="border-top: 1px solid #b4b9bf!important" width="50" class="text-center"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table_purchase">
                                                        <?php echo $tbody ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <td>Tổng</td>
                                                        <td class="tfood_sldx text-center"></td>
                                                        <td class="tfood_slc text-center"></td>
                                                        <td class="tfood_slk text-center"></td>
                                                        <td class="tfood_slp text-center"></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="tfood_total text-right"></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Nội dung -->
                                <tr>
                                    <td colspan="4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <?= lang('internal_proposal_content') ?>
                                                <?php echo form_textarea('content', $object->content, 'placeholder="' . lang('internal_proposal_content') . '" id="content" class="form-control input-tip tinymce"'); ?>
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

    function addtotal() {
        var total_service = 0;
        if ($('#id_service').find('option:selected').data('subtext')) {
            var total_service = $('#id_service').find('option:selected').data('subtext');
        }
        var tfood_total = $('#add_modal').find('.tfood_total').text().replace(/\,/g, '');
        // tfood_total 
        $('#money').val(tnhFormatNumber(total_service + tfood_total));
    }
    $('#id_service, #id_purchase_order').change(function() {
        var total_service = 0;
        if ($('#id_service').find('option:selected').data('subtext')) {
            var total_service = $('#id_service').find('option:selected').data('subtext');
        }
        var total_purchase_order = 0;
        if ($('#id_purchase_order').find('option:selected').data('total')) {
            total_purchase_order = $('#id_purchase_order').find('option:selected').data('total');
        }
        // $('#money').val(tnhFormatNumber(total_service + total_purchase_order));
        addtotal();
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
        appValidateForm($('#add-form'), {
            code: 'required',
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

    // $(document).on('change', 'select[name="id_purchases"]', function(e) {

    // });
    function loadItemsPurchase(type) {
        var id_purchases = $('select[name="id_purchases[]"]').val();

        // custom_item_select.find('option:gt(0)').remove();
        // custom_item_select.selectpicker('refresh');
        id_purchases_text = '';
        if (id_purchases.length) {
            $.each(id_purchases, function(i, v) {
                id_purchases_text += v + '_';
            });
            $.ajax({
                    url: admin_url + 'internal_proposal/items_purchases/' + id_purchases_text,
                    dataType: 'json',
                })
                .done(function(data) {
                    $('.table_purchase').html(data.tbody);
                    for (let index = 0; index < data.dem; index++) {
                        var id_supp = $('#suppliers_id_' + index).attr('data-id_supp');
                        var company_supp = $('#suppliers_id_' + index).attr('data-company_supp');
                        if (id_supp == 0) {
                            ajaxSelectCallBack($('#suppliers_id_' + index), "<?= admin_url('suppliers/SearchSupplierss') ?>", 0);
                        } else {
                            var txtJson = {
                                id: id_supp,
                                text: company_supp
                            };
                            ajaxSelectCallBack($('#suppliers_id_' + index), "<?= admin_url('suppliers/SearchSupplierss') ?>", id_supp, 0, txtJson);
                            $('#suppliers_id_' + index).change();
                        }
                    }
                    init_selectpicker();
                    getTotalPrice();
                });
        } else {
            $('.table_purchase').html('');
            getTotalPrice();
        }
    }
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };

    function ajaxSelectCallBack(element, url, id, types = '', txtJson = false) {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function(element, callback) {
                    if (txtJson) {
                        callback(txtJson);
                    } else {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: url + '/' + id + '/' + types,
                            dataType: "json",
                            success: function(data) {
                                callback(data.results[0].children[0]);
                            }
                        });
                    }
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
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
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
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
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    var base_url = '<?= base_url() ?>';

    function repoFormatSelection_ch(state) {
        if (!state.id) return state.text;

        return state.text;
    }

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);
        var current_row = currentInput.parent().parent();
        let recipe = (current_row.find('.recipe').val());
        let paper = (current_row.find('.paper').val());
        let longs = (current_row.find('.longs').val());
        let wide = (current_row.find('.wide').val());
        let mainQuantity = unformat_number(current_row.find('.mainQuantity').val());
        let mainQuantity_suppliers = unformat_number(current_row.find('.mainQuantity_suppliers').val());
        let exchange_standard_unit = unformat_number(current_row.find('.exchange_standard_unit').val());
        let exchange_stock = unformat_number(current_row.find('.exchange_stock').val());
        let exchange_payment = unformat_number(current_row.find('.exchange_payment').val());
        var quantity_stock = Math.round((mainQuantity_suppliers / exchange_stock) * exchange_standard_unit)

        current_row.find('.text_mainquantity_stock').text(tnhFormatNumber(quantity_stock, 0));
        current_row.find('.mainquantity_stock').val((quantity_stock));

        if (recipe == 1) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * exchange_standard_unit)
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 2) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * paper / 100)
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        } else if (recipe == 3) {
            var quantity_payment = ((mainQuantity_suppliers / exchange_payment) * (longs * wide) / 10000)
            current_row.find('.text_mainquantity_payment').text(tnhFormatNumber(quantity_payment));
            current_row.find('.mainquantity_payment').val((quantity_payment));
        }
        let price_suppliers = unformat_number(current_row.find('.price_suppliers').val());
        let tax = unformat_number(current_row.find('.tax_rate').val());
        var total_suppliers = (quantity_payment * price_suppliers) * (1 + tax / 100);

        // current_row.find('.total_expected').text(tnhFormatNumber(total_expected));
        current_row.find('.total_suppliers').text(tnhFormatMoney(total_suppliers));
        getTotalPrice();
    };


    function getTotalPrice() {
        var items = $('table.item-purchases tbody').find('tr');
        var sldx = 0;
        var slc = 0;
        var slk = 0;
        var skp = 0;
        var total = 0;
        $.each(items, (index, value) => {
            // sldx += parseFloat($(value).find('.sldx').text().replace(/\,/g, ''));
            // slc += parseFloat($(value).find('.mainQuantity_suppliers').val().replace(/\,/g, ''));
            // slk += parseFloat($(value).find('.mainquantity_stock').val().replace(/\,/g, ''));
            // skp += parseFloat($(value).find('.mainquantity_payment').val().replace(/\,/g, ''));
            total += parseFloat($(value).find('.total_suppliers').text().replace(/\,/g, ''));
        });
        // $('.tfood_sldx').text(tnhFormatNumber(sldx));
        // $('.tfood_slc').text(tnhFormatNumber(slc));
        // $('.tfood_slk').text(tnhFormatNumber(slk));
        // $('.tfood_skp').text(tnhFormatNumber(skp));
        console.log(total)
        $('.tfood_total').text(tnhFormatNumber(total));
        addtotal();
    }

    function countrow() {

        var items = $('table.item-purchases tbody').find('tr');
        $.each(items, (index, value) => {
            var count = $(value).find('td').find('input.count').val();
            var suppliers_id = $(value).find('td').find('input#suppliers_id_' + count).val();
            $('#price_suppliers_' + count).change();
            // ajaxSelectCallBack($('#suppliers_id_' + count), "<?= admin_url('suppliers/SearchSupplierss') ?>", suppliers_id);
            var id_supp = $('#suppliers_id_' + index).attr('data-id_supp');
            var company_supp = $('#suppliers_id_' + index).attr('data-company_supp');
            if (id_supp == 0) {
                ajaxSelectCallBack($('#suppliers_id_' + count), "<?= admin_url('suppliers/SearchSupplierss') ?>", suppliers_id);
            } else {
                var txtJson = {
                    id: id_supp,
                    text: company_supp
                };
                ajaxSelectCallBack($('#suppliers_id_' + count), "<?= admin_url('suppliers/SearchSupplierss') ?>", suppliers_id, 0, txtJson);
            }
        });
    }
    <?php if (!empty($id)) { ?>
        countrow();
    <?php } ?>
</script>

<script>
    $('#id_departments').change(function() {
        var id_departments = $(this).val();
        $.get(admin_url + 'production_report/get_list_role/' + id_departments, function(data) {
            data = JSON.parse(data);
            $('#role_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#role_id').append(`<option value="${value.roleid}">${value.name}</option>`);
            })
            $('#role_id').selectpicker('refresh');
            $('#role_id').trigger('change');
        })
    });

    $('#role_id').change(function() {
        var role_id = $('#role_id').val();
        var id_departments = $('#id_departments').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['role_id'] = role_id;
        data['id_departments'] = id_departments;
        data['internal_proposal'] = 1;
        $.post(admin_url + 'production_report/get_list_category_tasks', data, function(data) {
            data = JSON.parse(data);
            $('#category_tasks').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#category_tasks').append(`<option value="${value.id}" data-departments="${value.departments}" data-subtext="${value.content}">${value.code}</option>`);
            })
            $('#category_tasks').selectpicker('refresh');
            $('#category_tasks').trigger('change');
        });
    });

    $('#recommended_list_group_id').change(function() {
        var recommended_list_group_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_group_id;

        $.post(admin_url + 'production_report/getRecommendedListByParent', data, function(data) {
            data = JSON.parse(data);
            $('#recommended_list_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#recommended_list_id').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
            })
            $('#recommended_list_id').selectpicker('refresh');
        });
    });
</script>