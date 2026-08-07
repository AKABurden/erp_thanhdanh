<div id="list_other_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <?php echo form_open(admin_url('list_other/detail_other/' . $type . '/' . (!empty($list_other) ? $list_other->id : '')),
            ['id' => 'from_list_other']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?= !empty($title) ? $title : '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                        
                            <?php foreach($name_colums  as $k => $v) {?>
                                <?php if($k == 'id') continue?>
                                <tr>
                                    <td style="width: 30%;">
                                        <?= lang($v, $k) ?>
                                    </td>
                                    <td style="width: 70%;">
                                        <div class="form-group">
                                            <input type="text" name="<?=$k?>" class="form-control" id="<?=$k?>"
                                                   value="<?= !empty($list_other) ? $list_other->{$k} : '' ?>">
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
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
</div>
<script type="text/javascript">
    $('#list_other_modal').modal('show');

    appValidateForm($('#from_list_other'), {
        code:'required',
        name: 'required',
    }, addFrom);

    function addFrom(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serializeArray();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        })
        .done(function(data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                if (typeof oTable != 'undefined') {
                    oTable.draw();
                }
                $('#list_other_modal').modal('hide');
                $('.add').removeAttr('disabled', 'disabled');
            } else {
                alert_float('danger', data.message);
                $('.add').removeAttr('disabled', 'disabled');
            }
        })
        .fail(function() {
            console.log("error");
        });
        return false;
    }
</script>