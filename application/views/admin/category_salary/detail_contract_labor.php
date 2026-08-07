<?php echo form_open('admin/category_salary/detail_contract_labor/'.$id, array('id'=>'detail_contract_labor')); ?>
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
                        <?= lang('Nhân viên', 'staff_id') ?>
                        <select name="staff_id" id="staff_id" class="staff_id" required="required" style="width: 100%;"  data-placeholder="<?= lang('Nhân viên') ?>">
                            <option value=""></option>
                            <?php if(!empty($dtStaff)): ?>
                                <?php foreach($dtStaff as $key => $value): ?>
                                    <option <?= (!empty($dtData) && $dtData['staff_id'] == $value['staffid'] ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['firstname'].' '.$value['lastname'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Loại hợp đồng', 'type_contract_id') ?>
                        <select name="type_contract_id" id="type_contract_id" class="type_contract_id" required="required" style="width: 100%;"  data-placeholder="<?= lang('Loại hợp đồng') ?>">
                            <option value=""></option>
                            <?php if(!empty($dtTypeContract)): ?>
                                <?php foreach($dtTypeContract as $key => $value): ?>
                                    <option <?= (!empty($dtData) && $dtData['type_contract_id'] == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Lương cơ bản', 'salary_basic') ?>
                        <?php echo form_input('salary_basic', (isset($_POST['salary_basic']) ? $_POST['salary_basic'] : (!empty($dtData) ? formatMoney($dtData['salary_basic']) : 0)), ' id="salary_basic" class="form-control input-tip number-format"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Lương vị trí', 'salary_position') ?>
                        <?php echo form_input('salary_position', (isset($_POST['salary_position']) ? $_POST['salary_position'] : (!empty($dtData) ? formatMoney($dtData['salary_position']) : 0)), ' id="salary_position" class="form-control number-format input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày thử việc', 'date_probation') ?>
                        <input type="text" name="date_probation" class="date_probation datepicker form-control" autocomplete="off" id="date_probation" value="<?= (!empty($dtData['date_probation']) ? _dhau($dtData['date_probation']) : '') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày ký HĐ', 'date_sign_contract') ?>
                        <input type="text" name="date_sign_contract" class="date_sign_contract datepicker form-control" autocomplete="off" id="date_sign_contract" value="<?= (!empty($dtData['date_sign_contract']) ? _dhau($dtData['date_sign_contract']) : '') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày hiệu lực', 'date_start') ?>
                        <input type="text" name="date_start" class="date_start datepicker form-control" autocomplete="off" id="date_start" value="<?= (!empty($dtData['date_start']) ? _dhau($dtData['date_start']) : '') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày hết hiệu lực', 'date_end') ?>
                        <input type="text" name="date_end" class="date_end datepicker form-control" autocomplete="off" id="date_end" value="<?= (!empty($dtData['date_end']) ? _dhau($dtData['date_end']) : '') ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày tái ký', 'date_sign') ?>
                        <input type="text" name="date_sign" class="date_sign datepicker form-control" autocomplete="off" id="date_sign" value="<?= (!empty($dtData['date_sign']) ? _dhau($dtData['date_sign']) : '') ?>">
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
        $("#staff_id").select2();
        $("#type_contract_id").select2();
        appValidateForm($('#detail_contract_labor'), {
            code: 'required',
            staff_id: 'required',
            type_contract_id: 'required',
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