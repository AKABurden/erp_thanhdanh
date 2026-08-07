<?php echo form_open('admin/salary3P/detail_category_grade/'.$id, array('id'=>'step-salary')); ?>
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
                        <?= lang('Thâm niên từ (Tháng)', 'seniority_from_month') ?>
                        <?php echo form_input('seniority_from_month', (isset($_POST['seniority_from_month']) ? $_POST['seniority_from_month'] : (!empty($dtData) ? $dtData['seniority_from_month'] : 0)), 'id="seniority_from_month" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Thâm niên đến (Tháng)', 'seniority_to_month') ?>
                        <?php echo form_input('seniority_to_month', (isset($_POST['seniority_to_month']) ? $_POST['seniority_to_month'] : (!empty($dtData) ? $dtData['seniority_to_month'] : 0)), 'placeholder="'.lang('seniority_to_month').'" id="seniority_to_month" class="form-control input-tip"'); ?>
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
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        init_datepicker();
        appValidateForm($('#step-salary'), {
            code: 'required',
            seniority_from_month: 'required',
            seniority_to_month: 'required',
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