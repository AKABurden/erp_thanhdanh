<div class="modal-dialog modal-lg" style="min-width: 75%;">
    <?php echo form_open(admin_url('kpi/detail_suggest_kpi/' . $id),
        ['id' => 'detail_suggest_kpi']); ?>
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
                    <td><?= lang('Người yêu cầu đánh giá', 'staff_suggest') ?></td>
                    <td>
                        <select name="staff_suggest" id="staff_suggest"
                                data-placeholder="<?= lang('Nhân viên đề xuất đánh giá') ?>" style="width: 100%;"
                                class="">
                            <option value=""></option>
                            <?php foreach ($employees as $key => $value) : ?>
                                <option <?= !empty($dtData) ? ($dtData['staff_suggest'] == $value['staffid'] ? 'selected' : '') : '' ?>
                                        value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                            <?php endforeach ?>
                        </select>
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
                    <td><?= lang('Tháng','month') ?></td>
                    <td>
                        <select onchange="changeData(this)" name="month" id="month" class="" data-placeholder="<?= lang('month') ?>"
                                style="width: 100%;" style="width: 100%;">
                            <?php if (!empty(getMonth())) : ?>
                                <?php foreach (getMonth() as $key => $value) : ?>
                                    <option <?= date('m') == $key ? 'selected' : '' ?>
                                            value="<?= $key ?>"><?= $value ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </td>
                    <td><?= lang('Năm','year') ?></td>
                    <td>
                        <select onchange="changeData(this)" name="year" id="year" class="" data-placeholder="<?= lang('year') ?>"
                                style="width: 100%;" style="width: 100%;">
                            <?php if (!empty(getYear())) : ?>
                                <?php foreach (getYear() as $key => $value) : ?>
                                    <option <?= date('Y') == $key ? 'selected' : '' ?>
                                            value="<?= $key ?>"><?= $value ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Mã vị trí đánh giá', 'role_id') ?></td>
                    <td colspan="1">
                        <select onchange="changeData(this)" name="role_id" id="role_id" class="role_id"
                                data-placeholder="<?= lang('Mã vị trí đánh giá') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($dtData)){ ?>
                            <?php if (!empty($roles)) { ?>
                                <?php foreach ($roles as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['role_id'] == $value['roleid'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><?= lang('Ghi chú', 'note') ?></td>
                    <td colspan="1">
                        <textarea name="note" id="note" class="form-control note" cols="3"
                                  rows="4"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row mtop10">
                <div class="col-md-12">
                    <table id="tb-suggest-kpi" class="table dataTable">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px"><?= lang('STT') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Nhóm KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Loại KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chi tiết KPIS') ?></th>
                            <th class="text-center" style="width: 100px"><?= lang('Mã KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chỉ Số Đo Lường KPIs') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Target') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Trọng số (%)') ?></th>
                            <th class="text-center" style="width: 200px"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Báo Cáo Không Phù Hợp') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Kết quả') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $stt = 0; $counter = 0; $total_weight = 0;$total_weight1 = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $key => $value) { ?>
                                <?php
                                if ($value['type'] == 2){
                                    continue;
                                } else {
                                    $stt ++;
                                }
                                $weight = $value['weight'];
                                if ($value['report'] > $value['time']){
                                    $weight = 0;
                                    $selectedResult = 2;
                                } else {
                                    $selectedResult = 1;
                                }
                                $optionResult = '<option></option>';
                                if (!empty($dtResult)) {
                                    foreach ($dtResult as $kk => $vv) {
                                        $optionResult .= '<option ' . ($vv['id'] == $selectedResult ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="text-center"><?= ($stt) ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="hidden" class="counter" name="counter[]"
                                                   value="<?= $counter ?>">
                                            <input type="hidden" class="category_kpi_id"
                                                   name="category_kpi_id[<?= $counter ?>]"
                                                   value="<?= $value['category_kpi_id'] ?>">
                                            <input type="hidden" class="category_kpi_criteria_id"
                                                   name="category_kpi_criteria_id[<?= $counter ?>]"
                                                   value="<?= $value['category_kpi_criteria_id'] ?>">
                                            <input type="hidden" class="suggest_kpi_item_id"
                                                   name="suggest_kpi_item_id[<?= $counter ?>]"
                                                   value="<?= $value['id'] ?>">
                                            <?= $value['name_category'] ?>
                                        </div>
                                    </td>
                                    <td><div class="td_type"><?= $value['type'] == 1 ? "Năng Lực" : "Tuân Thủ" ?></div></td>
                                    <td><div class="td_name_kpi"><?= $value['name_kpi'] ?></div></td>
                                    <td><div class="td_code_kpi"><?= $value['code_kpi'] ?></div></td>
                                    <td><div class="td_measure"><?= $value['measure'] ?></div></td>
                                    <td><div class="td_target text-center"><?= $value['time'] ?></div></td>
                                    <td><div class="td_weight text-center"><input type="hidden" class="weight" name="weight[<?= $counter ?>]" value="<?= $weight ?>"><?= $weight ?></div></td>
                                    <td><div class="td_regulations"><?= $value['regulations'] ?></div></td>
                                    <td class="text-center"><?= $value['report'] ?> Phiếu</td>
                                    <td>
                                        <div>
                                            <select class="result_id none-event" id="result_id_<?= $counter ?>"
                                                    name="result_id[<?= $counter ?>]" style="width: 100%;"
                                                    data-placeholder="<?= lang('Kết quả') ?>">
                                                <?= $optionResult ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <?php $counter++;
                                $total_weight += $value['weight'];
                            } ?>
                        <?php } $total_weight1 = $total_weight; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="total_weight text-center bold"><?= $total_weight ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bold uppercase">% KPI</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center total_kpi" style="color: red"><?= 80 * $total_weight / 100 ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>

                    </table>
                    <table id="tb-suggest-kpi-new" class="table dataTable" style="margin-top: 20px !important;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px"><?= lang('STT') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Nhóm KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Loại KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chi tiết KPIS') ?></th>
                            <th class="text-center" style="width: 100px"><?= lang('Mã KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chỉ Số Đo Lường KPIs') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Target') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Trọng số (%)') ?></th>
                            <th class="text-center" style="width: 200px"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Báo Cáo Không Phù Hợp') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Kết quả') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $stt = 0;$total_weight = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $kkk => $value) { ?>
                                <?php
                                if ($value['type'] == 1){
                                    continue;
                                } else {
                                    $stt ++;
                                }

                                $weight = $value['weight'];
                                if ($value['report'] > $value['time']){
                                    $weight = 0;
                                    $selectedResult = 2;
                                } else {
                                    $selectedResult = 1;
                                }

                                $optionResult = '<option></option>';
                                if (!empty($dtResult)) {
                                    foreach ($dtResult as $kk => $vv) {
                                        $optionResult .= '<option ' . ($vv['id'] == $selectedResult ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="text-center"><?= ($stt) ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="hidden" class="counter" name="counter[]"
                                                   value="<?= $counter ?>">
                                            <input type="hidden" class="category_kpi_id"
                                                   name="category_kpi_id[<?= $counter ?>]"
                                                   value="<?= $value['category_kpi_id'] ?>">
                                            <input type="hidden" class="category_kpi_criteria_id"
                                                   name="category_kpi_criteria_id[<?= $counter ?>]"
                                                   value="<?= $value['category_kpi_criteria_id'] ?>">
                                            <input type="hidden" class="suggest_kpi_item_id"
                                                   name="suggest_kpi_item_id[<?= $counter ?>]"
                                                   value="<?= $value['id'] ?>">
                                            <?= $value['name_category'] ?>
                                        </div>
                                    </td>
                                    <td><div class="td_type"><?= $value['type'] == 1 ? "Năng Lực" : "Tuân Thủ" ?></div></td>
                                    <td><div class="td_name_kpi"><?= $value['name_kpi'] ?></div></td>
                                    <td><div class="td_code_kpi"><?= $value['code_kpi'] ?></div></td>
                                    <td><div class="td_measure"><?= $value['measure'] ?></div></td>
                                    <td><div class="td_target text-center"><?= $value['time'] ?></div></td>
                                    <td><div class="td_weight text-center"><input type="hidden" class="weight" name="weight[<?= $counter ?>]" value="<?= $weight ?>"><?= $weight ?></div></td>
                                    <td><div class="td_regulations"><?= $value['regulations'] ?></div></td>
                                    <td class="text-center"><?= $value['report'] ?> Phiếu</td>
                                    <td>
                                        <div>
                                            <select class="result_id none-event" id="result_id_<?= $counter ?>"
                                                    name="result_id[<?= $counter ?>]" style="width: 100%;"
                                                    data-placeholder="<?= lang('Kết quả') ?>">
                                                <?= $optionResult ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <?php $counter++;
                                $total_weight += $value['weight'];
                            } ?>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="total_weight text-center bold"><?= $total_weight ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bold uppercase">% KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center total_kpi" style="color: red"><?= 20 * $total_weight / 100 ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bold uppercase">Tổng KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center grand_kpi" style="color: red"><?= (80 * $total_weight1 / 100) +  (20 * $total_weight / 100) ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>
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
    $("#branch_id").select2();
    $("#year").select2();
    $("#month").select2();
    $("#role_id").select2();
    $("#staff_suggest").select2();
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;

    $("#staff_suggest").change(function () {
        staff_suggest = $(this).val();
        $("#role_id").select2("val","");
        $("#tb-suggest-kpi").find('tbody').html('');
        $("#tb-suggest-kpi-new").find('tbody').html('');
        getTotal();
        getTotalNew();
        if (staff_suggest) {
            $.ajax({
                url: site.base_url + 'admin/kpi/getRoleByStaff',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    staff_suggest: staff_suggest,
                },
            })
                .done(function (data) {
                    option = '<option></option>';
                    if (data.dtRole.length > 0){
                        $.each(data.dtRole,function (k,v){
                            option += `<option value="${v.roleid}">${v.name}</option>`;
                        })
                    }
                    $("#role_id").html(option);
                })
                .fail(function () {
                    console.log("error");
                });
        }
    })

    function changeData(_this){
        staff_suggest = $("#staff_suggest").val();
        role_id = $("#role_id").val();
        month = $("#month").val();
        year = $("#year").val();
        $("#tb-suggest-kpi").find('tbody').html('');
        $("#tb-suggest-kpi-new").find('tbody').html('');
        getTotal();
        getTotalNew();
        if (role_id) {
            $.ajax({
                url: site.base_url + 'admin/kpi/getDataRole',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    staff_suggest: staff_suggest,
                    role_id: role_id,
                    month: month,
                    year: year,
                },
            })
                .done(function (data) {
                    if (data.dtCategoryKpi.length > 0) {
                        $.each(data.dtCategoryKpi, function (k, v) {
                            if (v.type == 1){
                                loadItem(v)
                            } else {
                                loadItemNew(v)
                            }
                        });
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    }


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
        tdNameCategory = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="hidden" class="category_kpi_id" name="category_kpi_id[${counter}]" value="${item.category_kpi_id}">
            <input type="hidden" class="category_kpi_id" name="category_kpi_criteria_id[${counter}]" value="${item.category_kpi_criteria_id}">
            ${item.name_category}
        </div>`;
        tdType = `<div class="td_type">${item.type == 1 ? "Năng Lực" : "Tuân Thủ"}</div>`;
        tdNameKpi = `<div class="td_name_kpi">${item.name_kpi}</div>`;
        tdCodeKpi = `<div class="td_code_kpi">${item.code_kpi}</div>`;
        tdTMeasure = `<div class="td_measure">${item.measure}</div>`;
        tdTarget = `<div class="td_target text-center">${item.time}</div>`;
        selectedResult = 0;
        weight = item.weight;
        if (item.report > item.time){
            selectedResult = 2;
            weight = 0;
        } else {
            selectedResult = 1;
        }
        tdWeight = `<div class="td_weight text-center"><input type="hidden" class="weight" name="weight[${counter}]" value="${weight}">${weight}</div>`;
        tdStandard = `<div>${item.regulations != null ? item.regulations : ''}</div>`;
        tdReport = `<div class="text-center">${item.report} Phiếu</div>`;

        tdResult = `<div>
             <select class="result_id none-event" id="result_id_${counter}" name="result_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult(selectedResult)}
            </select>
        </div>`;

        trItem = `<tr>
            <td class="text-center stt">${tdStt}</td>
            <td>${tdNameCategory}</td>
            <td>${tdType}</td>
            <td>${tdNameKpi}</td>
            <td>${tdCodeKpi}</td>
            <td>${tdTMeasure}</td>
            <td>${tdTarget}</td>
            <td>${tdWeight}</td>
            <td>${tdStandard}</td>
            <td>${tdReport}</td>
            <td>${tdResult}</td>
        </tr>`;

        $("#tb-suggest-kpi").find('tbody').append(trItem);
        $(`#result_id_${counter}`).select2();
        $(`#result_id_${counter}`).attr('required', 'required');
        counter++;
        getTotal();
    }

    function loadItemNew(item) {
        tdStt = `<div class="text-center"></div>`;
        tdNameCategory = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="hidden" class="category_kpi_id" name="category_kpi_id[${counter}]" value="${item.category_kpi_id}">
            <input type="hidden" class="category_kpi_criteria_id" name="category_kpi_criteria_id[${counter}]" value="${item.category_kpi_criteria_id}">
            ${item.name_category}
        </div>`;
        tdType = `<div class="td_type">${item.type == 1 ? "Năng Lực" : "Tuân Thủ"}</div>`;
        tdNameKpi = `<div class="td_name_kpi">${item.name_kpi}</div>`;
        tdCodeKpi = `<div class="td_code_kpi">${item.code_kpi}</div>`;
        tdTMeasure = `<div class="td_measure">${item.measure}</div>`;
        tdTarget = `<div class="td_target text-center">${item.time}</div>`;

        selectedResult = 0;
        weight = item.weight;
        if (item.report > item.time){
            selectedResult = 2;
            weight = 0;
        } else {
            selectedResult = 1;
        }

        tdWeight = `<div class="td_weight text-center"><input type="hidden" class="weight" name="weight[${counter}]" value="${weight}">${weight}</div>`;
        tdStandard = `<div>${item.regulations != null ? item.regulations : ''}</div>`;
        tdReport = `<div class="text-center">${item.report} Phiếu</div>`;

        tdResult = `<div>
             <select class="result_id none-event" id="result_id_${counter}" name="result_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult(selectedResult)}
            </select>
        </div>`;

        trItem = `<tr>
            <td class="text-center stt">${tdStt}</td>
            <td>${tdNameCategory}</td>
            <td>${tdType}</td>
            <td>${tdNameKpi}</td>
            <td>${tdCodeKpi}</td>
            <td>${tdTMeasure}</td>
            <td>${tdTarget}</td>
            <td>${tdWeight}</td>
            <td>${tdStandard}</td>
            <td>${tdReport}</td>
            <td>${tdResult}</td>
        </tr>`;

        $("#tb-suggest-kpi-new").find('tbody').append(trItem);
        $(`#result_id_${counter}`).select2();
        $(`#result_id_${counter}`).attr('required', 'required');
        counter++;
        getTotalNew();
    }

    function getTotal() {
        tb = '#tb-suggest-kpi tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        total_weight = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            weight = intVal($(element).find('.weight').val());
            total_weight += weight;
        }
        total_kpi = 80 * total_weight / 100;
        $("#tb-suggest-kpi tfoot").find('.total_weight').html(total_weight);
        $("#tb-suggest-kpi tfoot").find('.total_kpi').html(total_kpi);
    }

    function getTotalNew() {
        tb = '#tb-suggest-kpi-new tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        total_weight = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            weight = intVal($(element).find('.weight').val());
            total_weight += weight;
        }
        total_kpi = 20 * total_weight / 100;
        $("#tb-suggest-kpi-new tfoot").find('.total_weight').html(total_weight);
        $("#tb-suggest-kpi-new tfoot").find('.total_kpi').html(total_kpi);

        total_kpi1 = intVal($("#tb-suggest-kpi tfoot").find('.total_kpi').html());

        grand_kpi = total_kpi + total_kpi1;

        $("#tb-suggest-kpi-new tfoot").find('.grand_kpi').html(grand_kpi);

    }

    appValidateForm($('#detail_suggest_kpi'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        staff_suggest: 'required',
        month: 'required',
        year: 'required',
        role_id: 'required'
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