<div class="modal-dialog modal-lg" style="min-width: 30%;">
    <?php echo form_open(
        admin_url('email/handling/' . $id . '/' . $type),
        ['id' => 'email']
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
                        <td>
                            <?= lang('Mã email', 'code') ?>
                        </td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="text" name="code" class="form-control code" id="code" value="<?= !empty($dtData) ? ($dtData['code']) : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= lang('Email 1', 'email') ?>
                        </td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="email" name="email" class="form-control email" id="email" value="<?= !empty($dtData) ? ($dtData['email']) : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= lang('Email 2', 'email2') ?>
                        </td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="email" name="email2" class="form-control email2" id="email2" value="<?= !empty($dtData) ? ($dtData['email2']) : '' ?>">
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
    ajaxSelectParams('#machines_id', 'admin/email/searchMachines', $("#machines_id").val(), $("#category_machine_id").val(), true);
    $("#branch_id").select2();
    $("#maintenance_department").select2();
    $("#category_machine_id").select2();
    $("#category_maintenance").select2();
    $("#department_id").select2();
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;
    $("#category_machine_id").change(function() {
        ajaxSelectParams('#machines_id', 'admin/email/searchMachines', 0, $("#category_machine_id").val(), true);
    });
    $("#machines_id").change(function() {
        machines_id = $(this).val();
        $('#maintenance_department').find('option:gt(0)').remove();
        if (machines_id) {
            $.ajax({
                    url: site.base_url + 'admin/email/getMaintenaceMachines',
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

    appValidateForm($('#email'), {
        code: 'required',
        email: 'required',
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