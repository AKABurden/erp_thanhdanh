<?php echo form_open('admin/quota_bonus_discipline/detail', array('id'=>'handling-quota')); ?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('name', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ''), 'placeholder="'.lang('name').'" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <?php foreach ($dtPrecious as $key => $value){ ?>
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="margin-bottom: 20px">
                            <div class="text-center uppercase bold panel-title" style="font-weight: bold !important;"><?= $value['name'] ?></div>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('Hình Thức Thưởng-Kỷ Luật', 'form_q1') ?>
                                    <input type="text" class="form form-control" name="form_<?= $value['id'] ?>" id="form_<?= $value['id'] ?>" autocomplete="off" value="" style="width: 100%">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('Giá Trị', 'value_q1') ?>
                                    <input type="text" class="value form-control number-format" name="value_<?= $value['id'] ?>" id="value_<?= $value['id'] ?>" autocomplete="off" value="" style="width: 100%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-12">
                    <div class="form-group">
                        <select class="form-control type" name="type">
                            <?php foreach ($dtType as $key => $value){ ?>
                                 <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('add') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        appValidateForm($('#handling-quota'), {
            name: 'required'
        }, handlingQuota);

        function handlingQuota(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var data = $(form).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function(data) {
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
                })
                .fail(function() {
                    console.log("error");
                });
            return false;
        }
    })
</script>