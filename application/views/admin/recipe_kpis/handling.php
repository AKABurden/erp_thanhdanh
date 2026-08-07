<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('recipe_kpis/handling/' . $id.'/'.$type),
        ['id' => 'recipe_kpis']
    ); ?>
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
                        <td><?= lang('Nhóm Kpis', 'category_kpi') ?></td>
                        <td>
                            <div class="form-group">
                                <select name="category_kpi" style="width: 100%;" id="category_kpi" data-placeholder="<?= lang('Nhóm Kpis') ?>" class="category_kpi">
                                    <option value=""></option>
                                    <?php if (!empty($dtCategoryKpis)) { ?>
                                        <?php foreach ($dtCategoryKpis as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ($dtData['category_kpi'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>" data-content-name="<?= $value['name'] ?>"><?= $value['code'] ?></option>
                                        <?php endforeach ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </td>
                        <td><?= lang('KPIs', 'kpis') ?></td>
                        <td colspan="1">
                            <input type="text" name="kpis" id="kpis" class="kpis" data-placeholder="<?= lang('KPIs') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['kpis'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Target', 'target') ?></td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="target" class="form-control target number-format" id="target" value="<?= !empty($dtData) ? formatNumber($dtData['target']) : 0 ?>">
                            </div>
                        </td>
                        <td><?= lang('Trọng số (%)', 'weight') ?></td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="weight" class="form-control weight number-format" id="weight" value="<?= !empty($dtData) ? formatNumber($dtData['weight']) : 0 ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Công Thức Quy Đổi', 'conversion_formula') ?></td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="conversion_formula" class="form-control conversion_formula" id="conversion_formula" value="<?= !empty($dtData) ? ($dtData['conversion_formula']) : '' ?>">
                            </div>
                        </td>
                        <td><?= lang('Số điểm', 'point') ?></td>
                        <td>
                            <div class="form-group">
                                <input type="text" name="point" class="form-control point number-format" id="point" value="<?= !empty($dtData) ? formatNumber($dtData['point']) : 0 ?>">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
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
    ajaxSelectParams('#kpis', 'admin/recipe_kpis/searchKpis', $("#kpis").val(), $("#category_kpi").val(), true);
    $("#branch_id").select2();
    $("#maintenance_department").select2();
    $("#category_kpi").select2();
    $("#category_maintenance").select2();
    $("#department_id").select2();
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;
    $("#category_kpi").change(function() {
        ajaxSelectParams('#kpis', 'admin/recipe_kpis/searchKpis', 0, $("#category_kpi").val(), true);
    });
    $("#kpis").change(function() {
        kpis = $(this).val();
        $('#maintenance_department').find('option:gt(0)').remove();
        if (kpis) {
            $.ajax({
                    url: site.base_url + 'admin/recipe_kpis/getMaintenaceKpis',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        csrf_token_name: hash,
                        kpis: kpis,
                    },
                })
                .done(function(data) {
                    $('#maintenance_department').html(data)
                    // if (data.dtMaintenaceKpis.length > 0) {
                    //     $.each(data.dtMaintenaceKpis, function(k, v) {
                    //         loadItem(v)
                    //     });
                    // }
                })
                .fail(function() {
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
        $.each(dtResult, function(index, el) {
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

    appValidateForm($('#recipe_kpis'), {
        category_kpi: 'required',
        maintenance_department: 'required',
        detail: 'required',
        date_start: 'required',
        date_renewals: 'required',
        category_maintenance: 'required',
        department_id: 'required',
        kpis: 'required'
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
        }).done(function(data) {
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
        }).fail(function() {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
</script>