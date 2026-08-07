<?php echo form_open('admin/category_maintenance_calibration/detail_maintenance_calibration/' . $id,
    array('id' => 'maintenance_calibration')); ?>
<div class="modal-dialog" style="width: 45%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="hidden" name="type" id="type" value="<?= $type ?>">
                        <?= lang('dt_code_maintenance_calibration', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('dt_code_maintenance_calibration').'" id="code" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Thiết bị', 'machines_id') ?>
                        <input type="text" name="machines_id" id="machines_id" class="machines_id"
                               data-placeholder="<?= lang('Thiết bị') ?>" style="width: 100%;"
                               value="<?= !empty($dtData) ? $dtData['machines_id'] : '' ?>"
                               title="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_department_maintenance_calibration', 'department') ?>
                        <?php echo form_input('department',
                            (isset($_POST['department']) ? $_POST['department'] : (!empty($dtData) ? $dtData['department'] : '')),
                            'placeholder="' . lang('dt_department_maintenance_calibration') . '" id="department" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Phiếu Yêu Cầu Bảo Dưỡng', 'suggest_maintenance_id') ?>
                        <select name="suggest_maintenance_id" id="suggest_maintenance_id" class="suggest_maintenance_id c_select2" data-placeholder="Phiếu yêu cầu bảo dưỡng" style="width: 100%;" title="Phiếu yêu cầu bảo dưỡng">
                            <option value=""></option>
                            <?php if(!empty($suggest_maintenance_id)) {
                                foreach($suggest_maintenance_id as $key => $value) {?>
                                    <option <?= !empty($dtData) ? ($dtData['suggest_maintenance_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                <?php }?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Số lượng', 'quantity') ?>
                        <?php echo form_input('quantity', (isset($_POST['quantity']) ? $_POST['quantity'] : (!empty($dtData) ? $dtData['quantity'] : '')), 'placeholder="'.lang('Số lượng').'" id="quantity" onchange="getTotal()" class="form-control number-format input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Đơn giá', 'price') ?>
                        <?php echo form_input('price', (isset($_POST['price']) ? $_POST['price'] : (!empty($dtData) ? $dtData['price'] : '')), 'placeholder="'.lang('Đơn giá').'" id="price" onchange="getTotal()" class="form-control number-format input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Thuế Vat', 'tax_id') ?>
                        <select class="tax_id" id="tax_id" name="tax_id" style="width: 100%;" onchange="getTotal()" data-placeholder="<?= lang('Thuế') ?>">
                            <option></option>
                            <?php if (!empty($taxs)){ ?>
                                <?php foreach ($taxs as $key => $value){ ?>
                                    <option <?= !empty($dtData) && $dtData['tax_id'] == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>" data-rate="<?= $value['taxrate'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Thành tiền', 'grand_total') ?>
                        <input type="text" class="grand_total form-control" id="grand_total" readonly value="<?= !empty($dtData) ? formatMoney($dtData['grand_total']) : 0 ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Thời hạn quy định', 'deadline') ?>
                        <?php echo form_input('deadline', (isset($_POST['deadline']) ? $_POST['deadline'] : (!empty($dtData) ? $dtData['deadline'] : '')), 'placeholder="'.lang('Thời hạn quy định').'" id="deadline" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Thời Gian Bắt Đầu', 'date_start') ?>
                        <input type="text" class="date_start datepicker form-control" id="date_start" name="date_start" autocomplete="off" value="<?= !empty($dtData) && !empty($dtData['date_start']) ? _dhau($dtData['date_start']) : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Thời Gian Tái Tục', 'date_end') ?>
                        <input type="text" class="date_end datepicker form-control" id="date_end" name="date_end" autocomplete="off" value="<?= !empty($dtData) && !empty($dtData['date_end']) ? _dhau($dtData['date_end']) : '' ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('dt_detail_maintenance_calibration', 'detail') ?>
                        <?php echo form_textarea('detail',
                            (isset($_POST['detail']) ? $_POST['detail'] : (!empty($dtData) ? $dtData['detail'] : '')),
                            'placeholder="' . lang('dt_detail_maintenance_calibration') . '" id="detail" class="form-control input-tip tinymce"'); ?>
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
    $(function () {
        init_datepicker();
        $("#tax_id").select2();
        $("select.suggest_maintenance_id").select2();
        ajaxSelectParams('#machines_id', 'admin/suggest_repalce/searchMachines', $("#machines_id").val(), true, true);
        appValidateForm($('#maintenance_calibration'), {
            code: 'required',
            machines_id: 'required',
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
                .done(function (data) {
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
                .fail(function () {
                    console.log("error");
                });
            return false;
        }
    })

    function getTotal(){
        quantity = intVal($("#quantity").val());
        price = intVal($("#price").val());
        tax_rate = intVal($('select.tax_id').select2().find(":selected").data('rate'));
        amount = quantity * price;
        total_tax = amount * tax_rate / 100;
        grand_total = amount + total_tax;
        $("#grand_total").val(tnhFormatMoney(grand_total));
    }
</script>