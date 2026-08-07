<?php echo form_open('admin/category_regulations/detail_salary_grade/'.$id, array('id'=>'step-salary')); ?>
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
                        <?= lang('Tên ngạch lương', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtData) ? $dtData['name'] : '')), 'placeholder="'.lang('Tên ngạch lương').'" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tổng điểm vượt qua', 'pass_total_score') ?>
                        <?php echo form_input('pass_total_score', (isset($_POST['pass_total_score']) ? $_POST['pass_total_score'] : (!empty($dtData) ? $dtData['pass_total_score'] : 0)), 'placeholder="'.lang('Tổng điểm vượt qua').'" id="pass_total_score" class="form-control number-format input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Hưởng P1', 'p1') ?>
                        <?php echo form_input('p1', (isset($_POST['p1']) ? $_POST['p1'] : (!empty($dtData) ? $dtData['p1'] : 0)), 'placeholder="'.lang('Hưởng P1').'" id="p1" class="form-control number-format  input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Hưởng P2', 'p2') ?>
                        <?php echo form_input('p2', (isset($_POST['p2']) ? $_POST['p2'] : (!empty($dtData) ? ($dtData['p2']) : 0)), 'placeholder="'.lang('Hưởng P2').'" id="p2" class="form-control number-format  p2 input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Hưởng P3', 'p3') ?>
                        <?php echo form_input('p3', (isset($_POST['p3']) ? $_POST['p3'] : (!empty($dtData) ? ($dtData['p3']) : 0)), 'placeholder="'.lang('Hưởng P3').'" id="p3" class="form-control p3 number-format input-tip"'); ?>
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
        $("#is_ceo_required").select2({
        });
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        init_datepicker();
        appValidateForm($('#step-salary'), {
            name: 'required',
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
                    $('.add').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }
    })

</script>