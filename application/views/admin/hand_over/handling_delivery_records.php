<?php echo form_open_multipart('admin/hand_over/handling_delivery_records/' . $id . '/' . $id_import, array('id' => 'handling-delivery_records', 'class' => '', 'enctype' => 'multipart/form-data', )); ?>
<style>
    .tb-view tbody tr td {
        border-top: 1px solid #cedae6 !important;
    }

    .checkTab {
        display: none !important;
    }
</style>
<div class="modal-dialog modal-lg" style="min-width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="tb-view table table-hover dataTable">
                        <tbody>
                            <tr class="text-center bold uppercase">
                                <td colspan="4"><?= lang('tnh_info_general') ?></td>
                            </tr>
                            <tr>
                                <td style="width: 15%;">
                                    <?= lang('tnh_reference_no_delivery_records', 'reference_no_delivery_records') ?>
                                </td>
                                <td style="width: 35%;">
                                    <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : (!empty($delivery_records) ? $delivery_records['reference_no'] : $reference_no)), 'placeholder="' . lang('tnh_reference_no_delivery_records') . '" id="reference_no_delivery_records" required class="form-control input-tip"'); ?>
                                </td>
                                <td style="width: 15%;">
                                    <?= lang('tnh_date_delivery_records', 'date_delivery_records') ?>
                                </td>
                                <td style="width: 35%;">
                                    <input type="text" name="date" id="date_delivery_records"
                                        class="form-control datetimepicker"
                                        value="<?= !empty($delivery_records) ? _d($delivery_records['date']) : date('d/m/Y H:i:s') ?>"
                                        required="required">
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Hạng mục bàn giao', 'type_object') ?></td>
                                <td>
                                    <select name="type_object" data-placeholder="<?= lang('Liên quan đến') ?>"
                                        data-live-search="true" id="type_object" class="selectpicker form-control "
                                        data-none-selected-text="Không có mục nào được chọn" tabindex="-98">
                                        <option value=""></option>
                                        <?php if (!empty($type_object_internal_proposal)): ?>
                                            <?php foreach ($type_object_internal_proposal as $key => $value): ?>
                                                <?php
                                                if (!empty($delivery_records) && $delivery_records['type_object'] == $value['key_object']) {
                                                    $type = $value['type'];
                                                }
                                                ?>
                                                <option <?= !empty($delivery_records) && $delivery_records['type_object'] == $value['key_object'] ? 'selected' : '' ?>
                                                    data-type="<?= $value['type'] ?>" value="<?= $value['key_object'] ?>">
                                                    <?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td class="type_id_object <?= empty($type) ? 'hide' : '' ?>"><label
                                        for="id_object"><?= !empty($delivery_records['name_type_object']) ? $delivery_records['name_type_object'] : '' ?></label>
                                </td>
                                <td class="type_id_object <?= empty($type) ? 'hide' : '' ?>" id="rel_id_select">
                                    <select name="id_object[]" data-live-search="true" id="id_object"
                                        class="selectpicker form-control" data-actions-box="true" multiple
                                        data-none-selected-text="Không có mục nào được chọn" tabindex="-98">
                                        <?php if (!empty($id_object)): ?>
                                            <?php foreach ($id_object as $key => $value): ?>
                                                <option <?= !empty($delivery_records) && !empty($delivery_records['id_object_' . $value['id']]) ? 'selected' : '' ?> value="<?= $value['id'] ?>"
                                                    data-subtext="<?= $value['subtext'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr class="text-center bold uppercase">
                                <td colspan="4"><?= lang('tnh_info_staff') ?></td>
                            </tr>
                            <tr>
                                <?php
                                $staffc = 0;
                                $disabled = '';
                                $module_category_hand_overc = '';
                                $dtStaff = [];
                                if ($delivery_records) {
                                    $dtStaff = $this->site_model->getStaffByStaffId($delivery_records['staff']);
                                } else {
                                    if ($id_import) {
                                        $staffc = get_staff_user_id();
                                        $disabled = 'none-event';
                                        $module_category_hand_overc = 3;
                                    }
                                }
                                ?>
                                <td><?= lang('Người bàn giao', 'staff') ?></td>
                                <td>
                                    <select name="staff" data-placeholder="<?= lang('staff') ?>"
                                        id="staff_delivery_records" class="modal-select2 <?= $disabled ?>"
                                        style="width: 100%;" required="required">
                                        <option value=""></option>
                                        <?php if (!empty($staffs)): ?>
                                            <?php foreach ($staffs as $key => $value): ?>
                                                <option <?= !empty($delivery_records) && $delivery_records['staff'] == $value['staffid'] ? 'selected' : (!empty($id_import) && ($staffc == $value['staffid']) ? 'selected' : '') ?>
                                                    data-department="<?= !empty($value['name_department']) ? $value['name_department'] : '' ?>"
                                                    data-role="<?= !empty($value['name_role']) ? $value['name_role'] : '' ?>"
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td><?= lang('Người nhận bàn giao', 'receiver') ?></td>
                                <td>
                                    <select name="receiver" data-placeholder="<?= lang('Người nhận bàn giao') ?>"
                                        id="receiver" class="modal-select2 <?= $disabled ?>" style="width: 100%;">
                                        <option value=""></option>
                                        <?php if (!empty($staffs)): ?>
                                            <?php foreach ($staffs as $key => $value): ?>
                                                <option <?= !empty($delivery_records['receiver']) && $delivery_records['receiver'] == $value['staffid'] ? 'selected' : '' ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('id_branch', 'id_branch') ?></td>
                                <td>
                                    <?php echo render_select('id_branch', (!empty($branch) ? $branch : []), ['id', 'name'], '', (!empty($delivery_records) ? $delivery_records['id_branch'] : '')) ?>
                                </td>
                                <td>Ghi chú</td>
                                <td>
                                    <?php $value = (!empty($delivery_records) ? $delivery_records['note'] : ''); ?>
                                    <?php echo render_textarea('note', '', $value); ?>
                                </td>
                            </tr>
                            <tr class="text-center bold uppercase">
                                <td colspan="4"><?= lang('tnh_content_delivery_records') ?></td>
                            </tr>
                            <tr>
                                <td colspan="1">
                                    <?= lang('Loại bàn giao', 'category_hand') ?>
                                </td>
                                <td colspan="3">
                                    <?php
                                    if (!empty($delivery_records) && $delivery_records['category_hand']) {
                                        $this->db->where('tbl_delivery_records_task.delivery_records_id', $delivery_records['id']);
                                        $this->db->where('task_hand_over_qualified != 0', false, false);
                                        $kt_active_delivery_records_task = $this->db->get('tbl_delivery_records_task')->row();
                                    }
                                    ?>
                                    <select name="category_hand" id="category_hand" data-live-search="true"
                                        <?= !empty($kt_active_delivery_records_task) ? 'disabled' : '' ?>
                                        data-none-selected-text="<?= lang('Loại bàn giao') ?>"
                                        class="form-control selectpicker" required="required">
                                        <option></option>
                                        <?php if (!empty($category_hand)): ?>
                                            <?php foreach ($category_hand as $key => $value): ?>
                                                <option <?= (!empty($delivery_records) && $delivery_records['category_hand'] == $value['id'] ? 'selected' : '') ?>
                                                    value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="view-hand_over">
                                    <table id="tb-task" class="table tb-view dataTable">
                                        <thead>
                                            <tr style="background: #cedae6;">
                                                <td style="width: 15%;" class="text-center"><?= lang('Công đoạn') ?>
                                                </td>
                                                <td style="width: 25%;" class="text-center">
                                                    <?= lang('Nội dung bàn giao') ?></td>
                                                <td style="width: 15%;" class="text-center"><?= lang('Tiêu chuẩn') ?>
                                                </td>
                                                <td style="width: 25%;" class="text-center"><?= lang('Phương thức') ?>
                                                </td>
                                                <td style="width: 10%;" class="text-center checkTab"><?= lang('Đạt') ?>
                                                </td>
                                                <td style="width: 10%;" class="text-center checkTab">
                                                    <?= lang('Không đạt') ?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $arrHandOver = [];
                                            if (!empty($delivery_records)) {
                                                //                                                $this->db->select('
//                                                        tbl_module_hand_over.*
//                                                    ', false);
//                                                $this->db->from('tbl_module_hand_over');
//                                                $this->db->where_in('tbl_module_hand_over.id', $arrModule);
//                                                $module_hand_over = $this->db->get()->result_array();
//                                                if (!empty($module_hand_over)) {
//                                                    foreach ($module_hand_over as $key => $value) {
                                                $this->db->select('tbl_category_hand_over.*');
                                                $this->db->from('tbl_category_hand_over');
                                                //                                                        $this->db->where('tbl_category_hand_over.type', $value['id']);
                                                $this->db->where('tbl_category_hand_over.id', $delivery_records['category_hand']);
                                                $category_hand_over = $this->db->get()->result_array();

                                                if (!empty($category_hand_over)) {
                                                    foreach ($category_hand_over as $k => $val) {

                                                        $this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
                                                        $this->db->from('tbl_hand_over_task');
                                                        $this->db->where('tbl_hand_over_task.category_hand_over_id', $val['id']);
                                                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
                                                        $this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
                                                        $hand_over_task = $this->db->get()->result_array();
                                                        $category_hand_over[$k]['task'] = $hand_over_task;
                                                    }
                                                }

                                                $arrHandOver[] = [
                                                    //                                                            'name_category_hand_over' => $value['name'],
                                                    'category_hand_over' => $category_hand_over
                                                ];
                                                //                                                    }
//                                                }
                                            
                                                $counterDR = 0;
                                                if (!empty($arrHandOver)) {
                                                    $trHandOver = '';
                                                    foreach ($arrHandOver as $key => $value) {
                                                        $trTask = '';
                                                        if (!empty($value['category_hand_over'])) {
                                                            foreach ($value['category_hand_over'] as $iC => $vC) {
                                                                if (!empty($vC['task'])) {
                                                                    foreach ($vC['task'] as $iT => $vT) {
                                                                        $deliveryRecordsTask = $this->hand_over_model->getDeliveryRecordsTaskById($id, $vT['id']);

                                                                        $arrTask = [];
                                                                        $delivery_records_task_id = 0;
                                                                        if (!empty($deliveryRecordsTask)) {
                                                                            foreach ($deliveryRecordsTask as $kTT => $vTT) {
                                                                                $arrTask[] = $vTT['hand_over_task_id'] . '__' . $vTT['task_hand_over_qualified'];
                                                                                $delivery_records_task_id = $vTT['id'];
                                                                            }
                                                                        }
                                                                        if (!empty($deliveryRecordsTask)) {
                                                                            $trTask .= '<tr>
                                                                                <td>' . $vT['code_stage'] . '</td>
                                                                                <td>' . $vT['name'] . '</td>
                                                                                <td>' . $vT['standard'] . '</td>
                                                                                <td>' . $vT['method'] . '</td>
                                                                                <td class="text-center checkTab">
                                                                                    <input type="hidden" name="delivery_records_task[' . $counterDR . ']" class="form-control" value="' . $delivery_records_task_id . '">
                                                                                    <input type="radio" name="task_hand_over[' . $counterDR . ']" ' . (in_array($vT['id'] . '__0', $arrTask) ? 'checked' : '') . ' id="task_hand_over_qualified_' . $counterDR . '" value="' . $vT['id'] . '__0">
                                                                                    <div class="radio radio-info">
                                                                                        <input type="radio" name="task_hand_over[' . $counterDR . ']" ' . (in_array($vT['id'] . '__1', $arrTask) ? 'checked' : '') . ' id="task_hand_over_qualified_' . $counterDR . '" value="' . $vT['id'] . '__1">
                                                                                        <label for="task_hand_over_qualified_' . $counterDR . '"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-center checkTab">
                                                                                    <div class="radio radio-info">
                                                                                        <input type="radio" name="task_hand_over[' . $counterDR . ']" ' . (in_array($vT['id'] . '__2', $arrTask) ? 'checked' : '') . ' id="task_hand_over_un_qualified_' . $counterDR . '" value="' . $vT['id'] . '__2">
                                                                                        <label for="task_hand_over_un_qualified_' . $counterDR . '"></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>';
                                                                        }
                                                                        $counterDR++;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        if (empty($trTask))
                                                            continue;
                                                        $trHandOver .= $trTask;
                                                    }
                                                    echo $trHandOver;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="col-md-12">
                                        <div class="dropzone dropzone-manual">
                                            <div id="dropzoneTaskComment"
                                                class="dropzoneDragArea dz-default dz-message task-comment-dropzone">
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
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>

    Dropzone.options.expenseForm = false;
    var expenseDropzone;
    if ($('#dropzoneTaskComment').length > 0) {
        expenseDropzone = new Dropzone('#handling-delivery_records', appCreateDropzoneOptions({
            paramName: "file",
            autoProcessQueue: false,
            previewsContainer: '.dropzone-previews',
            addRemoveLinks: true,
            maxFiles: 10,
            clickable: '#dropzoneTaskComment',
            accept: function (file, done) {
                done();
            },
            success: function (file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // window.location.reload();
                }
            }
        }));
    }

    var counterDR = 0;
    $(function () {
        $('#staff_delivery_records').select2();
        $('#receiver').select2();
        init_datepicker();
        init_selectpicker();

        $('#staff_delivery_records').change(function (event) {
            department = $("#staff_delivery_records").select2().find(":selected").data("department");
            role = $("#staff_delivery_records").select2().find(":selected").data("role");

            $('.txt-department').html(department);
            $('.txt-role').html(role);
        });

        $('#category_hand').change(function (event) {
            category_hand = $(this).val();
            var dataPOST = {};
            dataPOST[csrfData['token_name']] = csrfData['hash'];
            dataPOST['category_hand'] = category_hand;

            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/hand_over/getDataHandOverToCategory',
                data: dataPOST,
                dataType: "json",
                success: function (response) {
                    $('#tb-task tbody').html();
                    if (response) {
                        trHandOver = '';
                        $.each(response.arrHandOver, function (index, value) {
                            trTask = '';
                            if (typeof value.category_hand_over !== 'undefined' && value.category_hand_over.length > 0) {
                                $.each(value.category_hand_over, function (iC, vC) {
                                    if (typeof vC.task !== 'undefined' && vC.task.length > 0) {
                                        $.each(vC.task, function (iT, vT) {
                                            trTask += `<tr>
                                                <td>${vT.code_stage ? vT.code_stage : ''}</td>
                                                <td>${vT.name}</td>
                                                <td>${vT.standard ? vT.standard : ''}</td>
                                                <td>${vT.method ? vT.method : ''}</td>
                                                <td class="text-center checkTab">
                                                    <input class="hide" type="radio" name="task_hand_over[${counterDR}]" id="task_hand_over_qualified_${counterDR}" value="${vT.id}__0" checked>
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="task_hand_over[${counterDR}]" id="task_hand_over_qualified_${counterDR}" value="${vT.id}__1">
                                                        <label for="task_hand_over_qualified_${counterDR}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center checkTab">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="task_hand_over[${counterDR}]" id="task_hand_over_un_qualified_${counterDR}" value="${vT.id}__2">
                                                        <label for="task_hand_over_un_qualified_${counterDR}"></label>
                                                    </div>
                                                </td>
                                            </tr>`;
                                            counterDR++;
                                        });
                                    }
                                });
                            }

                            if (!trTask) return;

                            trHandOver += `${trTask}`;
                        });
                        $('#tb-task tbody').html(trHandOver);
                    }
                }
            });
        });


        $('#module_category_hand_over').change(function (event) {
            module_category_hand_over = $(this).val();
            var dataPOST = {};
            dataPOST[csrfData['token_name']] = csrfData['hash'];
            dataPOST['module_category_hand_over'] = module_category_hand_over;

            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/hand_over/getDataHandOver',
                data: dataPOST,
                dataType: "json",
                success: function (response) {
                    $('#tb-task tbody').html();
                    if (response) {
                        trHandOver = '';
                        $.each(response.arrHandOver, function (index, value) {
                            trTask = '';
                            if (typeof value.category_hand_over !== 'undefined' && value.category_hand_over.length > 0) {
                                $.each(value.category_hand_over, function (iC, vC) {
                                    if (typeof vC.task !== 'undefined' && vC.task.length > 0) {
                                        $.each(vC.task, function (iT, vT) {
                                            trTask += `<tr>
                                                <td>${vC.name}</td>
                                                <td>${vT.name}</td>
                                                <td class="text-center <?= empty($delivery_records) ? 'checkTab' : '' ?>">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="task_hand_over[${counterDR}]" id="task_hand_over_qualified_${counterDR}" value="${vT.id}__1">
                                                        <label for="task_hand_over_qualified_${counterDR}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center <?= empty($delivery_records) ? 'checkTab' : '' ?>">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="task_hand_over[${counterDR}]" id="task_hand_over_un_qualified_${counterDR}" value="${vT.id}__2">
                                                        <label for="task_hand_over_un_qualified_${counterDR}"></label>
                                                    </div>
                                                </td>
                                            </tr>`;
                                            counterDR++;
                                        });
                                    }
                                });
                            }

                            if (!trTask) return;

                            trHandOver += `<tr>
                                <td colspan="4" class="text-center bold">${value.name_category_hand_over}</td>;
                            </tr>${trTask}`;
                        });
                        $('#tb-task tbody').html(trHandOver);
                    }
                }
            });
        });

        appValidateForm($('#handling-delivery_records'), {
            reference_no_delivery_records: 'required',
            date_delivery_records: 'required',
            staff_delivery_records: 'required',
            module_category_hand_over: 'required',
            id_branch: 'required',
        }, handlingDeliveryRecords);

        function handlingDeliveryRecords(form) {
            $('.add').attr('disabled', 'disabled');
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(expenseDropzone.files, function (index, value) {
                formData.append('file[]', value);
            })

            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });

            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/hand_over/handling_delivery_records/<?= $id ?>/<?= $id_import ?>',
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
                .done(function (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw();
                        }
                        if (typeof tAPI != 'undefined' && tAPI != '') {
                            tAPI.draw('page');
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
        init_selectpicker();
        init_datepicker();
    })

    setTimeout(function () {
        $('#staff_delivery_records').trigger('change');
        $('#module_category_hand_over').trigger('change');
    }, 450);

    $('#type_object').change(function () {
        var type_object = $(this).val();
        var type = $(this).find('option:selected').data('type')
        $('#id_object').html('').selectpicker('refresh');
        $('label[for="id_object"]').text($(this).find('option:selected').text());
        if (type == 1) {
            $('.type_id_object').removeClass('hide');
            $.get(admin_url + 'hand_over/get_data_object/' + type_object, function (result) {
                result = JSON.parse(result);
                $.each(result, function (index, value) {
                    $('#id_object').append(`<option value="${value.id}" data-subtext="${value.subtext}">${value.name}</option>`);
                })
                $('#id_object').selectpicker('refresh');
            })
        }
        else {
            $('.type_id_object').addClass('hide');
        }
    })

    // var _data = {};
    // var _rel_id = $('#id_object');
    // var _rel_type = $('#type_object');
    // _rel_type.on('change', function () {
    //     var clonedSelect = $('#id_object').html('').clone();
    //     _rel_id.selectpicker('destroy').remove();
    //     _rel_id = clonedSelect;
    //     $('#rel_id_select').append(clonedSelect);
    //     $('label[for="id_object"]').text($(this).find('option:selected').text());
    //     // $('.rel_id_label').html($('#type_object').find('option:selected').text());
    //     task_rel_select();
    //     if ($(this).val() != '') {
    //         $('#id_object').removeClass('hide');
    //     } else {
    //         $('#id_object').addClass('hide');
    //     }
    // });
    // task_rel_select()
    //
    // function task_rel_select() {
    //     var serverData = {};
    //     serverData.rel_id = _rel_id.val();
    //     _data.type = _rel_type.val();
    //     console.log(serverData.rel_id);
    //     if ($(serverData.rel_id).length == 0) {
    //         // ajaxReloadFrist(_data.type, _rel_id);
    //     }
    //     init_ajax_search(_rel_type.val(), _rel_id, serverData);
    // }
    //
    // function ajaxReloadFrist(type, _rel_id) {
    //     var data = {};
    //     if (typeof (csrfData) !== 'undefined') {
    //         data[csrfData['token_name']] = csrfData['hash'];
    //     }
    //     data['type'] = type;
    //     data['limit_search'] = 5;
    //     $.ajax({
    //         url: admin_url + "misc/get_relation_data",
    //         type: "POST",
    //         dataType: "JSON",
    //         data: data,
    //         success: function (result) {
    //             $.each(result, function (index, value) {
    //                 subtext = '';
    //                 if (value.subtext) {
    //                     subtext = ' data-subtext="' + value.subtext + '" ';
    //                 }
    //                 content = '';
    //                 if (value.content) {
    //                     content = ' data-content="' + value.content + '" ';
    //                 }
    //                 $(_rel_id).append(`<option ${content} ${subtext} value="${value.id}">${value.name}</option>`);
    //             })
    //             $(_rel_id).selectpicker('refresh');
    //         }
    //     });
    // }

</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>