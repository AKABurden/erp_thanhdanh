<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(admin_url('decision_bonus_discipline/detail/' . $id),
        ['id' => 'decision_bonus_discipline']); ?>
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
                        <?= lang('Số phiếu quyết định', 'reference_no') ?>
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
                    <td><?= lang('Loại định mức', 'type_quota_bonus_discipline_id') ?></td>
                    <td>
                        <select name="type_quota_bonus_discipline_id" id="type_quota_bonus_discipline_id"
                                data-placeholder="<?= lang('Loại định mức') ?>" style="width: 100%;"
                                onchange="changeData(this)"
                                class="">
                            <option value=""></option>
                            <?php foreach ($typeBounsDiscipline as $key => $value) : ?>
                                <option <?= !empty($dtData) ? ($dtData['type_quota_bonus_discipline_id'] == $value['id'] ? 'selected' : '') : '' ?>
                                        value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
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
                    <td><?= lang('Phiếu Yêu Cầu của phòng HCNS', 'suggest_bonus_discipline_id') ?></td>
                    <td colspan="3">
                        <input type="text" name="suggest_bonus_discipline_id" id="suggest_bonus_discipline_id"
                               class="suggest_bonus_discipline_id" style="width: 100%"
                               onchange="changDataSuggest(this)"
                               data-placeholder="<?= lang('Phiếu Yêu Cầu của phòng HCNS') ?>"
                               value="<?= !empty($dtData) ? $dtData['suggest_bonus_discipline_id'] : '' ?>"
                               title="">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Loại đối tượng', 'object_type') ?></td>
                    <td class="html_object_type"
                        colspan="3"><?= !empty($dtData) ? ($dtData['object_type'] == 'staff' ? 'Cá nhân' : 'Bộ phận - Phòng ban') : '' ?></td>
                </tr>
                <tr>
                    <td><?= lang('Đối tượng', 'object_id') ?></td>
                    <td colspan="3" class="html_object_name"><?= !empty($dtData) ? $dtData['object_name'] : '' ?></td>
                </tr>
                <tr>
                    <td><?= lang('Số tiền', 'grand_total') ?></td>
                    <td colspan="3">
                        <input type="text" name="grand_total" class="grand_total form-control format-number" readonly
                               id="grand_total"
                               value="<?= !empty($dtData) ? formatMoney($dtData['grand_total']) : 0 ?>">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Lý do', 'note') ?></td>
                    <td colspan="3">
                        <textarea name="note" id="note" class="form-control note tinymce" cols="3"
                                  rows="4"><?= !empty($dtData) ? $dtData['note'] : get_option('decision_bnous') ?></textarea>
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
                        <div class="col-md-12">
                            <?php if (!empty($dtFile)) { ?>
                                <div class="preview_image" id="avatar_view" style="width:auto">
                                    <div class="display-block contract-attachment-wrapper img-1">
                                        <?php foreach ($dtFile as $kk => $vv) { ?>
                                            <?php $file_name_new = $vv['file_name']; ?>
                                            <?php if (explode('/', $vv['filetype'])[0] == 'image') { ?>
                                                <div class="col-md-12 row" style="display: flex;">
                                                    <input type="hidden" name="file_old[]" id="file_old"
                                                           class="form-control file_old"
                                                           value="<?= $vv['file_name'] ?>">
                                                    <?= ViewHtmlImagesDt(base_url('uploads/decision_bonus_discipline/' . $dtData['id'] . '/' . $file_name_new))?>
                                                    <button type="button" class="close remove-image" data-id="50"
                                                            data-src="uploads/items/50/tru_ringlock.jpg"
                                                            style="color:red;"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                            <?php } else { ?>
                                                <div class="col-md-12 row" style="display: flex;">
                                                    <input type="hidden" name="file_old[]" id="file_old"
                                                           class="form-control file_old"
                                                           value="<?= $vv['file_name'] ?>">
                                                    <div style="margin-right: 5px"><?= trim($file_name_new, '_') ?></div>
                                                    <button type="button" class="close remove-image" data-id="50"
                                                            data-src="uploads/items/50/tru_ringlock.jpg"
                                                            style="color:red;"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
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
    ajaxSelectParams('#suggest_bonus_discipline_id', 'admin/decision_bonus_discipline/searchSuggestBonusDisciplines', $("#suggest_bonus_discipline_id").val(), true, true);
    ajaxSelectParams('#kpi_id', 'admin/decision_bonus_discipline/searchKPI', $("#kpi_id").val(), true, true);
    $("#branch_id").select2();
    $("#object_id").select2();
    $("#object_id_new").select2();
    $("#type_quota_bonus_discipline_id").select2();
    Dropzone.options.expenseForm = false;
    var expenseDropzone;

    function changeData() {
        $("#suggest_bonus_discipline_id").select2("val", "");
    }

    $('.remove-image').click(function (event) {
        $(this).closest('.col-md-12').remove();
    });

    function changDataSuggest(_this) {
        $.ajax({
            url: site.base_url + 'admin/decision_bonus_discipline/getDataSuggestBonus',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token_name: "<?= $this->security->get_csrf_hash() ?>",
                suggest_bonus_discipline_id: $(_this).val()
            },
        })
            .done(function (data) {
                if (data) {
                    html_object_type = '';
                    if (data.dtData.object_type == 'staff') {
                        html_object_type = 'Cá nhân';
                    } else if (data.dtData.object_type == 'department') {
                        html_object_type = 'Bộ phận-Phòng ban';
                    }
                    $(".html_object_type").html(html_object_type);
                    $(".html_object_name").html(data.dtData.object_name);
                    $("#grand_total").val(tnhFormatMoney(data.dtData.grand_total));
                }
            })
            .fail(function () {
                console.log("error");
            });
    }

    function ajaxSelectParams(element, url, id, params = false, clearSl2 = false) {
        console.log(clearSl2);
        if (id) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: clearSl2,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function (data) {
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
                    data: function (term, page) {
                        return {
                            params: params,
                            term: term,
                            type_quota_bonus_disciplines_id: $("#type_quota_bonus_discipline_id").val(),
                            suggest_bonus_discipline_id: $("#suggest_bonus_discipline_id").val(),
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
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
                    data: function (term, page) {
                        return {
                            params: params,
                            term: term,
                            type_quota_bonus_disciplines_id: $("#type_quota_bonus_discipline_id").val(),
                            suggest_bonus_discipline_id: $("#suggest_bonus_discipline_id").val(),
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        }
    }

    $(document).on('change', 'input[name="object_type"]', function (e) {
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

    appValidateForm($('#decision_bonus_discipline'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        type_quota_bonus_discipline_id: 'required',
        suggest_bonus_discipline_id: 'required',
    }, detail);

    function detail(form) {
        $('.add').attr('disabled', 'disabled');
        tinymce.get('note').save();
        var url = form.action;
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();
        $.each(expenseDropzone.files, function (index, value) {
            formData.append('file[]', value);
        })
        $.each(formParams, function (i, val) {
            formData.append(val.name, val.value);
        });
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
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

    $(function () {
        tinyMCE.remove('#note');
        init_editor('textarea[name="note"]');
    })

    // file upload

    if ($('#dropzoneTaskComment').length > 0) {
        expenseDropzone = new Dropzone('#decision_bonus_discipline', appCreateDropzoneOptions({
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
</script>