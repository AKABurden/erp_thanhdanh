<?php echo form_open('admin/salary/createdNoteTimekeeping/', array('id' => 'created_note_timekeeping')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Lý do', 'reason_id') ?>
                        <select name="reason_id" id="reason_id" class="modal-select2"
                            data-placeholder="<?= lang('Lý do') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if(!empty($reasons)){?>
                            <?php foreach($reasons as $key => $value){?>
                            <option <?= $value['id'] == $timeKeepingDetailNote['reason_id'] ? 'selected' :'' ?>
                                value="<?= $value['id'] ?>" data-code="<?= $value['code']?>"
                                data-type="<?= $value['type']?>"><?= $value['name'] ?>
                            </option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Số ngày', 'value') ?> <span style="color:red" class="show_error"></span>
                        <input type="text" name="value" class="form-control value" onkeyup="formatNumBerKeyUpCus(this)"
                            placeholder="số ngày" id="value" value="<?= $timeKeepingDetailNote['value'] ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_note', 'note') ?>
                        <textarea name="mnote" id="note" class="form-control note"
                            rows="3"><?= $timeKeepingDetailNote['note'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="timekeepingId" class="form-control" value="<?= $timekeepingId ?>">
            <input type="hidden" name="staffId" class="form-control" value="<?= $personnel_id ?>">
            <input type="hidden" name="timekeeping_detail_id" class="form-control" value="<?= $idTimekeepingDetail ?>">
            <input type="hidden" name="typeTimeKeeping" class="form-control" value="<?= $typeTimeKeeping ?>">

            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default closes" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
$(function() {

    // $("#reason_id").change(function() {
    //     var code = $('option:selected', $(this)).attr('data-code');
    //     var type = $('option:selected', $(this)).attr('data-type');
    //     if (type == 'R_HT_50') {
    //         if (code == 'Nuôi người thân nằm viện - chồng, vợ') {
    //             $(".show_error").html('( Tối đa 3 ngày)');
    //         } else if (code == 'Nuôi người thân nằm viện - cha mẹ ruột, cha mẹ chồng/vợ') {
    //             $(".show_error").html('( Tối đa 2 ngày)');
    //         } else {
    //             $(".show_error").html('');
    //         }
    //     } else {
    //         $(".show_error").html('');
    //     }
    // })
    $('#reason_id').select2({
        allowClear: true
    });
    $(".closes").click(function() {
        type = "<?= $typeTimeKeeping ?>";
        type_now = "<?= $type_now ?>";
        if (type_now == 'X') {
            $('.<?= $timekeepingId ?>__<?= $personnel_id ?>__<?= $day ?>__<?= $idTimekeepingDetail ?>')
                .find('option[value="' + type + '"]').prop('selected', false);
        }
    });

    appValidateForm($('#created_note_timekeeping'), {
        'value': 'required',
        'reason_id': 'required',
    }, custom);

    function custom(form) {
        $('.add').attr('disabled', 'disabled');
        var url = form.action;
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });

        $.each(formParams, function(i, val) {
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
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    $('.<?= $timekeepingId ?>__<?= $personnel_id ?>__<?= $day ?>__<?= $idTimekeepingDetail ?>')
                        .find('option[value="' + data.type + '"]').prop('selected', 'selected');
                    elementSelect = $(
                        '.<?= $timekeepingId ?>__<?= $personnel_id ?>__<?= $day ?>__<?= $idTimekeepingDetail ?>'
                    );
                    cTdModal = elementSelect.closest('td');
                    console.log(cTdModal.find('.edit-note'));
                    if (data.type == 'R' || data.type == 'O_K_BHXH' || data.type == 'RO_HT_50') {
                        cTdModal.find('.edit-note').show();
                    } else {
                        cTdModal.find('.edit-note').hide();
                    }
                    cTdModal.find('.edit-note').attr('onclick',
                        "loadModalTimekeepingDetailNote(<?= $timekeepingId ?>, <?= $personnel_id ?>, '<?= $day ?>','<?= $idTimekeepingDetail ?>', '" +
                        data.type + "')");
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled', 15000);
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
})
</script>