<?php echo form_open('admin/depreciation/detail_depreciation/'.$id, array('id'=>'depreciation')); ?>
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
                        <?= lang('code', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('name', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtData) ? $dtData['name'] : '')), 'placeholder="'.lang('name').'" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Thời gian khấu hao', 'time_depreciation') ?>
                        <input type="text" name="time_depreciation" class="time_depreciation number-format form-control" id="time_depreciation" value="<?=  (!empty($dtData) ? $dtData['time_depreciation'] : '') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Thời gian bắt đầu', 'date_start') ?>
                        <input type="text" autocomplete="off" name="date_start" class="date_start form-control datepicker" id="date_start" value="<?=  (!empty($dtData) && !empty($dtData['date_start']) ? _dhau($dtData['date_start']) : '') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Thời gian kết thúc', 'date_end') ?>
                        <input type="text" autocomplete="off" name="date_end" class="date_end form-control datepicker" id="date_end" value="<?=  (!empty($dtData) && !empty($dtData['date_end']) ? _dhau($dtData['date_end']) : '') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Giá trị khấu hao', 'depreciation_value') ?>
                        <input type="text" name="depreciation_value" class="depreciation_value number-format form-control" id="depreciation_value" value="<?=  (!empty($dtData) ? formatMoney($dtData['depreciation_value']) : 0) ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Giá trị còn lại', 'residual_value') ?>
                        <input type="text" name="residual_value" class="residual_value number-format form-control" id="residual_value" value="<?=  (!empty($dtData) ? formatMoney($dtData['residual_value']) : 0) ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') : _l('edit'); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        init_datepicker();
        appValidateForm($('#depreciation'), {
            code: 'required',
            name: 'required'
        }, handling);

        function handling(form) {
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