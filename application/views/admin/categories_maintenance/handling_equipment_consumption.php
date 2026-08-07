<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('categories_maintenance/handling_equipment_consumption/' . $id . '/' . $type),
        ['id' => 'categories_maintenance']
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
                        <td><?= lang('dt_category_machines', 'category_machine_id') ?></td>
                        <td>
                            <div class="form-group">
                                <select name="category_machine_id" style="width: 100%;" id="category_machine_id" data-placeholder="<?= lang('dt_category_machines') ?>" class="category_machine_id">
                                    <option value=""></option>
                                    <?php if (!empty($dtCategoryMachines)) { ?>
                                        <?php foreach ($dtCategoryMachines as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ($dtData['id_category_machines'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>" data-content-name="<?= $value['name'] ?>"><?= $value['code'] ?></option>
                                        <?php endforeach ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </td>
                        <td><?= lang('Thiết bị', 'machines_id') ?></td>
                        <td colspan="1">
                            <input type="text" name="machines_id" id="machines_id" class="machines_id" data-placeholder="<?= lang('Mã thiết bị') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['id_machines'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Chi tiết bảo dưỡng', 'detail') ?></td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="text" name="detail" class="form-control detail" id="detail" value="<?= !empty($dtData) ? ($dtData['detail']) : 0 ?>">
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
    ajaxSelectParams('#machines_id', 'admin/categories_maintenance/searchMachines', $("#machines_id").val(), $("#category_machine_id").val(), true);
    $("#branch_id").select2();
    $("#maintenance_department").select2();
    $("#category_machine_id").select2();
    $("#category_maintenance").select2();
    $("#department_id").select2();
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;
    $("#category_machine_id").change(function() {
        ajaxSelectParams('#machines_id', 'admin/categories_maintenance/searchMachines', 0, $("#category_machine_id").val(), true);
    });
    $("#machines_id").change(function() {
        machines_id = $(this).val();
        $('#maintenance_department').find('option:gt(0)').remove();
        if (machines_id) {
            $.ajax({
                    url: site.base_url + 'admin/categories_maintenance/getMaintenaceMachines',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        csrf_token_name: hash,
                        machines_id: machines_id,
                    },
                })
                .done(function(data) {
                    $('#maintenance_department').html(data)
                    // if (data.dtMaintenaceMachines.length > 0) {
                    //     $.each(data.dtMaintenaceMachines, function(k, v) {
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

    appValidateForm($('#categories_maintenance'), {
        category_machine_id: 'required',
        maintenance_department: 'required',
        detail: 'required',
        date_start: 'required',
        date_renewals: 'required',
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