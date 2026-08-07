<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('suggest_bonus_discipline/detail/' . $id),
        ['id' => 'suggest_bonus_discipline']
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
                        <td style="width: 15%;">
                            <?= lang('dt_reference_suggest', 'reference_no') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('date', 'date') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date',
                                set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Loại định mức', 'type_quota_bonus_disciplines_id') ?></td>
                        <td>
                            <select name="type_quota_bonus_disciplines_id" id="type_quota_bonus_disciplines_id" data-placeholder="<?= lang('Loại định mức') ?>" style="width: 100%;" onchange="changeData(this)" class="">
                                <option value=""></option>
                                <?php foreach ($typeBounsDiscipline as $key => $value) : ?>
                                    <option <?= !empty($dtData) ? ($dtData['type_quota_bonus_disciplines_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                        <td colspan="1">
                            <?php
                            $branchs = getListBranch();
                            ?>
                            <select name="branch_id" id="branch_id" class="branch_id" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($branchs)) { ?>
                                    <?php foreach ($branchs as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2"><?= lang('Đối tượng', 'object_type') ?></td>
                        <td rowspan="2">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6 hide">
                                        <div class="radio radio-primary">
                                            <input type="radio" value="staff" <?= !empty($dtData) && $dtData['object_type'] == 'staff' ? 'checked' : 'checked' ?> id="object_type1" name="object_type">
                                            <label for="object_type1">Nhân viên</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 hide">
                                        <div class="radio radio-primary">
                                            <input type="radio" value="department" <?= !empty($dtData) && $dtData['object_type'] == 'department' ? 'checked' : '' ?> id="object_type2" name="object_type">
                                            <label for="object_type2">Bộ phận-Phòng ban</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 show_staff <?= !empty($dtData) && $dtData['object_type'] == 'department' ? 'hide' : '' ?>">
                                        <select name="object_id" id="object_id" <?= !empty($dtData) && $dtData['object_type'] == 'staff' ? 'required' : 'required' ?> data-placeholder="<?= lang('Nhân viên') ?>" style="width: 100%;" class="">
                                            <option value=""></option>
                                            <?php foreach ($employees as $key => $value) : ?>
                                                <option <?= !empty($dtData) ? ($dtData['object_id'] == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>"><?= $value['firstname'] . ' ' . $value['lastname'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12 show_department <?= !empty($dtData) ? ($dtData['object_type'] == 'staff' ? 'hide' : '') : 'hide' ?>">
                                        <select name="object_id_new" id="object_id_new" <?= !empty($dtData) && $dtData['object_type'] == 'department' ? 'required' : '' ?> data-placeholder="<?= lang('Bộ phận') ?>" style="width: 100%;" class="">
                                            <option value=""></option>
                                            <?php foreach ($deparment as $key => $value) : ?>
                                                <option <?= !empty($dtData) ? ($dtData['object_id'] == $value['departmentid'] ? 'selected' : '') : '' ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Định mức khen thưởng-kỷ luật', 'quota_bonus_disciplines_id') ?></td>
                        <td colspan="1">
                            <select class="hide" name="precious_id" id="precious_id" data-placeholder="<?= lang('Quí') ?>" style="width: 100%;" class="">
                                <option value=""></option>
                                <?php foreach ($dtPrecious as $key => $value) : ?>
                                    <option <?= ($value['id'] == 1 ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <input type="text" name="quota_bonus_disciplines_id" id="quota_bonus_disciplines_id" class="quota_bonus_disciplines_id" style="width: 100%" data-placeholder="<?= lang('Định mức khen thưởng-kỷ luật') ?>" value="<?= !empty($dtData) ? $dtData['quota_bonus_disciplines_id'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Phiếu đề xuất đánh giá KPI', 'propose_kpi') ?></td>
                        <td colspan="1">
                            <input type="text" name="propose_kpi" id="propose_kpi" class="propose_kpi" style="width: 100%" data-placeholder="<?= lang('Phiếu đề xuất đánh giá KPI') ?>" value="<?= !empty($dtData) ? $dtData['propose_kpi'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Báo cáo không phù hợp', 'production_report_id') ?></td>
                        <td>
                            <input type="text" name="production_report_id" id="production_report_id" class="production_report_id"
                                   data-placeholder="<?= lang('Báo cáo không phù hợp') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['production_report_id'] : '' ?>"
                                   title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Số tiền', 'grand_total') ?></td>
                        <td colspan="3">
                            <input type="text" name="grand_total" class="grand_total form-control format-number" readonly id="grand_total" value="<?= !empty($dtData) ? formatMoney($dtData['grand_total']) : 0 ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Lý do', 'note') ?></td>
                        <td colspan="3">
                            <textarea name="note" id="note" class="form-control note" cols="3" rows="4"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
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
    ajaxSelectParams('#production_report_id', 'admin/suggest_repalce/searchProductionReports', $("#production_report_id").val(), true, true);
    init_datepicker();
    init_selectpicker('refresh');
    ajaxSelectParams('#quota_bonus_disciplines_id', 'admin/suggest_bonus_discipline/searchQuotaBonusDisciplines', $("#quota_bonus_disciplines_id").val(), {
        type_bonus_disciplines_id: <?= !empty($dtData) ? $dtData['type_quota_bonus_disciplines_id'] : 0 ?>
    }, true);
    ajaxSelectParams('#propose_kpi', 'admin/suggest_bonus_discipline/searchProposeKpi', $("#propose_kpi").val(), {}, true);
    $("#branch_id").select2();
    $("#object_id").select2();
    $("#precious_id").select2();
    $("#object_id_new").select2();
    $("#type_quota_bonus_disciplines_id").select2();

    function changeData(_this) {
        ajaxSelectParams('#quota_bonus_disciplines_id', 'admin/suggest_bonus_discipline/searchQuotaBonusDisciplines', $("#quota_bonus_disciplines_id").val(), {
            type_bonus_disciplines_id: $(_this).val()
        }, true);
    }
    $(document).on('change', '#object_id', function(event) {
        var quota_bonus_disciplines_id = $('#quota_bonus_disciplines_id').val();
        var object_id = $('#object_id').val();
        dataString = {
            object_id: object_id,
            quota_bonus_disciplines_id: quota_bonus_disciplines_id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>suggest_bonus_discipline/get_price/",
            data: dataString,
            cache: false,
            success: function(data) {
                $("#grand_total").val(tnhFormatMoney(data));
            }
        })
    });
    $(document).on('change', '#quota_bonus_disciplines_id', function(event) {
        var quota_bonus_disciplines_id = $('#quota_bonus_disciplines_id').val();
        var object_id = $('#object_id').val();
        if(object_id == ''){
            alert('Vui lòng chọn nhân viên');
            $('#quota_bonus_disciplines_id').val('').selectpicker('refresh');
            return false;
        }
        dataString = {
            object_id: object_id,
            quota_bonus_disciplines_id: quota_bonus_disciplines_id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>suggest_bonus_discipline/get_price/",
            data: dataString,
            cache: false,
            success: function(data) {
                $("#grand_total").val(tnhFormatMoney(data));
            }
        })
    });
    $("#precious_id").change(function() {
        $("#quota_bonus_disciplines_id").select2("val", "");
    });

    // $("#quota_bonus_disciplines_id").change(function() {
    //     dataSelect2 = $(this).select2('data');
    //     grand_total = dataSelect2.grand_total;
    //     $("#grand_total").val(tnhFormatMoney(grand_total));
    // })

    function ajaxSelectParams(element, url, id, params = false, clearSl2 = false) {
        console.log(clearSl2);
        if (id) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: clearSl2,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val() + '/' + <?= !empty($dtData['precious_id']) ? $dtData['precious_id'] : 0 ?>,
                        dataType: "json",
                        success: function(data) {
                            callback(data.row);
                            if (data.row) {
                                if (data.row.id === 0) {
                                    $(element).val(0);
                                }
                            }
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            params: params,
                            term: term,
                            precious_id: $("#precious_id").val(),
                            object_id: $("#object_id").val(),
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
                allowClear: clearSl2,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            params: params,
                            term: term,
                            precious_id: $("#precious_id").val(),
                            object_id: $("#object_id").val(),
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

    $(document).on('change', 'input[name="object_type"]', function(e) {
        var value = $(this).val();
        $("select#object_id_new").select2('val', '');
        $("select#object_id").select2('val', '');
        if (value == 'staff') {
            $(".show_staff").removeClass('hide');
            $(".show_department").addClass('hide');
            $("select#object_id").attr('required', true);
            $("select#object_id_new").attr('required', false);

        } else if (value == 'department') {
            $(".show_staff").addClass('hide');
            $(".show_department").removeClass('hide');
            $("select#object_id_new").attr('required', true);
            $("select#object_id").attr('required', false);
        }
    });

    $("#object_id").change(function() {
        $("#propose_kpi").select2("val", "");
    })

    appValidateForm($('#suggest_bonus_discipline'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        type_quota_bonus_disciplines_id: 'required',
        quota_bonus_disciplines_id: 'required',
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