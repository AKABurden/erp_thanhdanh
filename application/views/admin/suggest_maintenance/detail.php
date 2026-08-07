<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(admin_url('suggest_maintenance/detail/' . $id),
        ['id' => 'suggest_maintenance']); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="title"><?= $title ?></span>
            </h4>
        </div>
        <div class="modal-body">
            <table class="tnh-tb table-bordered table-hover">
                <tbody>
                <tr>
                    <td style="width: 15%;">
                        <?= lang('dt_reference_suggest', 'reference_no') ?>
                    </td>
                    <td style="width: 35%;">
                        <div class="form-group">
                            <input type="text" name="reference_no" class="form-control" id="reference_no"
                                   value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly=""
                                   aria-invalid="false">
                        </div>
                    </td>
                    <td style="width: 15%;">
                        <?= lang('date', 'date') ?>
                    </td>
                    <td style="width: 35%;">
                        <?= form_input('date',
                            set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Loại bảo dưỡng', 'type_maintenance') ?></td>
                    <td colspan="1">
                        <select name="type_maintenance" id="type_maintenance" class="type_maintenance"
                                data-placeholder="<?= lang('Loại bảo dưỡng') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($dtTypeMaintenance)) { ?>
                                <?php foreach ($dtTypeMaintenance as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['type_maintenance'] == $value['id'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><?= lang('Nhóm bảo dưỡng', 'category_maintenance') ?></td>
                    <td colspan="1">
                        <select name="category_maintenance" id="category_maintenance" class="category_maintenance"
                                data-placeholder="<?= lang('Nhóm bảo dưỡng') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($dtCategoryMaintenance)) { ?>
                                <?php foreach ($dtCategoryMaintenance as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['category_maintenance'] == $value['id'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Thiết bị', 'machines_id') ?></td>
                    <td colspan="1">
                        <input type="text" name="machines_id" id="machines_id" class="machines_id"
                               data-placeholder="<?= lang('Mã thiết bị') ?>" style="width: 100%;"
                               value="<?= !empty($dtData) ? $dtData['machines_id'] : '' ?>"
                               title="">
                    </td>
                    <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                    <td colspan="1">
                        <?php
                        $branchs = getListBranch();
                        ?>
                        <select name="branch_id" id="branch_id" class="branch_id"
                                data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($branchs)) { ?>
                                <?php foreach ($branchs as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Số lượng', 'quantity') ?></td>
                    <td>
                        <div class="form-group">
                            <input type="text" name="quantity" class="form-control quantity number-format" id="quantity"
                                   value="<?= !empty($dtData) ? formatNumber($dtData['quantity']) : 0 ?>">
                        </div>
                    </td>
                    <td><?= lang('Khu vực bảo dưỡng', 'department_id') ?></td>
                    <td>
                        <select name="department_id" id="department_id" class="department_id"
                                data-placeholder="<?= lang('Khu vực bảo dưỡng') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($dtDepartment)) { ?>
                                <?php foreach ($dtDepartment as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['department_id'] == $value['departmentid'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Giờ dừng máy', 'downtime') ?></td>
                    <td>
                        <div class="form-group">
                            <input type="text" name="downtime" class="form-control downtime" id="downtime"
                                   value="<?= !empty($dtData) ? $dtData['downtime'] : '' ?>">
                        </div>
                    </td>
                    <td><?= lang('Chi tiết bảo dưỡng', 'detail') ?></td>
                    <td colspan="1">
                        <textarea name="detail" id="detail" class="form-control detail" cols="3"
                                  rows="4"><?= !empty($dtData) ? $dtData['detail'] : '' ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row mtop10">
                <div class="col-md-12">
                    <table id="tb-maintenance-machines" class="table dataTable">
                        <thead>
                        <tr>
                            <th class="text-center"><?= lang('STT') ?></th>
                            <th class="text-center"><?= lang('Bộ phận thiết bị') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Kết quả') ?></th>
                            <th class="text-center"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $key => $value) { ?>
                                <?php
                                $optionResult = '<option></option>';
                                if (!empty($dtResult)) {
                                    foreach ($dtResult as $kk => $vv) {
                                        $optionResult .= '<option ' . ($vv['id'] == $value['result_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="text-center"><?= (++$key) ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="hidden" class="counter" name="counter[]"
                                                   value="<?= $counter ?>">
                                            <input type="hidden" class="machines_maintenance_id"
                                                   name="machines_maintenance_id[<?= $counter ?>]"
                                                   value="<?= $value['machines_maintenance_id'] ?>">
                                            <input type="hidden" class="suggest_maintenance_item_id"
                                                   name="suggest_maintenance_item_id[<?= $counter ?>]"
                                                   value="<?= $value['id'] ?>">
                                            <?= $value['name_machines_maintenance'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <select class="result_id" id="result_id_<?= $counter ?>"
                                                    name="result_id[<?= $counter ?>]" style="width: 100%;"
                                                    data-placeholder="<?= lang('Kết quả') ?>">
                                                <?= $optionResult ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" name="standard[<?= $counter ?>]"
                                                   class="standard form-control" value="<?= $value['standard'] ?>">
                                        </div>
                                    </td>
                                </tr>
                                <?php $counter++;
                            } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    init_datepicker();
    init_selectpicker('refresh');
    ajaxSelectParams('#machines_id', 'admin/suggest_repalce/searchMachines', $("#machines_id").val(), true, true);
    $("#branch_id").select2();
    $("#type_maintenance").select2();
    $("#category_maintenance").select2();
    $("#department_id").select2();
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;

    $("#machines_id").change(function () {
        machines_id = $(this).val();
        $("#tb-maintenance-machines").find('tbody').html('');
        if (machines_id) {
            $.ajax({
                url: site.base_url + 'admin/suggest_maintenance/getMaintenaceMachines',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    machines_id: machines_id,
                },
            })
                .done(function (data) {
                    if (data.dtMaintenaceMachines.length > 0) {
                        $.each(data.dtMaintenaceMachines, function (k, v) {
                            loadItem(v)
                        });
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    })

    if (edit == 1) {
        for (i = 0; i < counter; i++) {
            $(`#result_id_${i}`).select2();
        }
    }

    function optionResult(selected_id = 0) {
        option = `<option></option>`;
        $.each(dtResult, function (index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function loadItem(item) {
        tdStt = `<div class="text-center"></div>`;
        tdName = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="hidden" class="machines_maintenance_id" name="machines_maintenance_id[${counter}]" value="${item.id}">
            ${item.name}
        </div>`;
        tdResult = `<div>
             <select class="result_id" id="result_id_${counter}" name="result_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult()}
            </select>
        </div>`;
        tdStandard = `<div>
            <input type="text" name="standard[${counter}]" class="standard form-control" value="">
        </div>`;

        trItem = `<tr>
            <td class="text-center stt">${tdStt}</td>
            <td>${tdName}</td>
            <td>${tdResult}</td>
            <td>${tdStandard}</td>
        </tr>`;

        $("#tb-maintenance-machines").find('tbody').append(trItem);
        $(`#result_id_${counter}`).select2();
        $(`#result_id_${counter}`).attr('required', 'required');
        counter++;
        getTotal();
    }

    function getTotal() {
        tb = '#tb-maintenance-machines tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
        }
    }

    appValidateForm($('#suggest_maintenance'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        type_maintenance: 'required',
        category_maintenance: 'required',
        department_id: 'required',
        machines_id: 'required'
    }, detail);

    function detail(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
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
        }).fail(function () {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
</script>