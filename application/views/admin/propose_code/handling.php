<div class="modal-dialog modal-lg" style="min-width: 30%;">
    <?php echo form_open(
        admin_url('propose_code/handling/' . $id . '/' . $type),
        ['id' => 'propose_code']
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
                            <?= lang('Mã đề xuất', 'code') ?>
                        </td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="text" name="code" class="form-control code" id="code" value="<?= !empty($dtData) ? ($dtData['code']) : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= lang('Tên đề xuất', 'name') ?>
                        </td>
                        <td colspan="3">
                            <div class="form-group">
                                <input type="name" name="name" class="form-control name" id="name" value="<?= !empty($dtData) ? ($dtData['name']) : '' ?>">
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
    appValidateForm($('#propose_code'), {
        code: 'required',
        name: 'required',
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