<style>
.table-image {
    height: 120px !important;
    width: 250px;
}
</style>
<?php echo form_open('admin/salary/viewEditHour/', array('id' => 'view-edit-hour')); ?>
<div class="modal-dialog modal-check_hour">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Sửa chấm công') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <?php 
                    $hour = date("H:i");
                    $value = _dhau($date_check);
                ?>
                <div class="col-md-12" style="margin-bottom:10px">
                    <?php echo render_date_input('date', 'Chọn ngày', $value) ?>
                </div>
                <div class="col-md-12" style="margin-bottom:10px">
                    <label for="hour">Chọn giờ công</label>
                    <input type="time" id="hour" name="hour" class="form-control" value="<?= $hour ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="save" name="save" value="1">
                <input type="hidden" id="date_check" name="date_check" value="<?= $date_check ?>">
                <input type="hidden" id="staff_id" name="staff_id" value="<?= $staff_id ?>">
                <input type="hidden" id="type_hour" name="type_hour" value="<?= $type_hour ?>">
                <input type="hidden" id="type_check" name="type_check" value="<?= $type_check ?>">
                <input type="hidden" id="newdate" name="newdate" value="<?= $newdate ?>">
                <input type="hidden" id="id_timekeeping_detail_hour" name="id_timekeeping_detail_hour"
                    value="<?= $id_timekeeping_detail_hour ?>">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
$(function() {

    var startdate = $('#date_check').val();
    var newdate = $('#newdate').val();
    $('#date').datetimepicker({
        timepicker: false,
        format: 'd/m/Y',
        minDate: startdate,
        maxDate: newdate
    })

    appValidateForm($('#view-edit-hour'), {
        'hour': 'required',
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
                    $('.modal-dialog .close').trigger('click');
                    loadPersonnelTimekeeping();
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled', 15000);
                    loadPersonnelTimekeeping();
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