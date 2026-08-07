<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Chọn ca làm việc'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ca làm việc', 'shift_id') ?>
                        <select name="shift_id" id="shift_id" class="" required data-placeholder="<?= lang('Ca làm việc') ?>"
                                style="width: 100%;" style="width: 100%;">
                                <option></option>
                            <?php if (!empty($dtshift)) : ?>
                                <?php foreach ($dtshift as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="hidden" name="staff_id" class="staff_id" id="staff_id" value="<?= $ids ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="button" class="btn btn-primary update_shift"><?= _l('Cập nhập') ?></button>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#shift_id').select2();
        $(".update_shift").click(function() {
            shift_id = $("#shift_id").val();
            staff_id = $("#staff_id").val();
            if (!shift_id) {
                bootbox.alert('Xin vui lòng chọn ca làm việc');
                return;
            }
            if (shift_id) {
                $.ajax({
                    type: "POST",
                    url: site.base_url + 'admin/staff/updateShift',
                    data: {
                        csrf_token_name: hash,
                        shift_id: shift_id,
                        staff_id: staff_id,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.result){
                            alert_float('success',response.message);
                        } else {
                            alert_float('danger',response.message);
                        }
                        if (typeof (tAPI) != "undefined"){
                            tAPI.draw(false);
                        }
                        $('#tnhModal').modal('hide');
                    }
                });
            }
        });
    })
</script>