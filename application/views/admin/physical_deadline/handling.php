<div class="modal-dialog modal-lg" style="min-width: 30%;">
    <?php echo form_open(
        admin_url('physical_deadline/handling/' . $id . '/' . $type),
        ['id' => 'physical_deadline']
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
                            <?= lang('Vị trí', 'location') ?>
                        </td>
                        <td colspan="3">
                            <?php
                            $this->db->where('tblroles.type', 0);
                            $this->db->where('tblroles.active_role', 1);
                            $data_roles = $this->db->get('tblroles')->result_array();
                            ?>
                            <div class="form-group">
                                <select id="location" class="selectpicker location" data-width="100%" name="location" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <option></option>
                                    <?php if (!empty($data_roles)) {
                                        foreach ($data_roles as $key => $value) { ?>
                                            <option <?= !empty($dtData) && ($dtData['location'] == $value['roleid']) ? 'selected' : '' ?> data-subtext="<?= $value['code_role'] ?>" value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= lang('Mã', 'code') ?>
                        </td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="text" name="code" class="form-control code" id="code" value="<?= !empty($dtData) ? ($dtData['code']) : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= lang('Thời Gian Khám Sức Khỏe', 'time') ?>
                        </td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="text" name="time" class="form-control time number-format" id="time" value="<?= !empty($dtData) ? ($dtData['time']) : '' ?>">
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
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;
    appValidateForm($('#physical_deadline'), {
        code: 'required',
        time: 'required',
        location: 'required',
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