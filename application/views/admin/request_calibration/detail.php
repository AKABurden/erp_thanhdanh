<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('request_calibration/detail/' . $id),
        ['id' => 'request_calibration']
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
                        <td style="width: 15%;">
                            <?= lang('dt_reference_suggest', 'reference_no') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('date', 'date') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date',
                                set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Nhóm hiệu chỉnh', 'category_calibration') ?></td>
                        <td colspan="1">
                            <select name="category_calibration" id="category_calibration" class="category_calibration" data-placeholder="<?= lang('Nhóm hiệu chỉnh') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtCategoryCalibration)) { ?>
                                    <?php foreach ($dtCategoryCalibration as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['category_calibration'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Bộ Phận Hiệu Chuẩn', 'bp_calibration') ?></td>
                        <td colspan="1">
                            <input type="text" name="bp_calibration" id="bp_calibration" class="bp_calibration form-control" data-placeholder="<?= lang('Bộ Phận Hiệu Chuẩn') ?>" value="<?= !empty($dtData) ? $dtData['bp_calibration'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Chi Tiết Hiệu Chuẩn', 'detail_calibration') ?></td>
                        <td colspan="1">
                            <input type="text" name="detail_calibration" id="detail_calibration" class="detail_calibration form-control" data-placeholder="<?= lang('Chi Tiết Hiệu Chuẩn') ?>" value="<?= !empty($dtData) ? $dtData['detail_calibration'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Số lượng', 'quantity') ?></td>
                        <td colspan="1">
                            <input type="text" name="quantity" id="quantity" onchange="total();" class="quantity form-control number-format" data-placeholder="<?= lang('Số lượng') ?>" value="<?= !empty($dtData) ? formatNumber($dtData['quantity']) : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Đơn Giá', 'price') ?></td>
                        <td colspan="1">
                            <input type="text" name="price" id="price" onchange="total();" class="price form-control number-format" data-placeholder="<?= lang('Đơn Giá') ?>" value="<?= !empty($dtData) ? formatMoney($dtData['price']) : '' ?>" title="">
                        </td>
                        <td><?= lang('Thành tiền', 'total_amount') ?></td>
                        <td colspan="1" class="total_amount"><?= !empty($dtData) ? formatMoney($dtData['price'] * $dtData['quantity']) : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Thiết bị', 'machines_id') ?></td>
                        <td colspan="1">
                            <input type="text" name="machines_id" id="machines_id" class="machines_id" data-placeholder="<?= lang('Mã thiết bị') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['machines_id'] : '' ?>" title="">
                        </td>
                        <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                        <td colspan="1">
                            <?php
                            $branchs = getListBranch();
                            ?>
                            <select name="branch_id" id="branch_id" class="branch_id" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($branchs)) { ?>
                                    <?php foreach ($branchs as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Kết quả', 'result_id') ?></td>
                        <td colspan="1">
                            <select name="result_id" id="result_id" class="result_id" data-placeholder="<?= lang('Kết quả') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtResult)) { ?>
                                    <?php foreach ($dtResult as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['result_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Tiêu Chuẩn/ Quy Định', 'standard') ?></td>
                        <td colspan="1">
                            <input type="text" name="standard" id="standard" class="standard form-control" data-placeholder="<?= lang('Tiêu Chuẩn/ Quy Định') ?>" value="<?= !empty($dtData) ? $dtData['standard'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Quy trình', 'procedure') ?></td>
                        <td colspan="1">
                            <input type="text" name="procedure" id="procedure" class="procedure form-control" data-placeholder="<?= lang('Quy trình') ?>" value="<?= !empty($dtData) ? $dtData['procedure'] : '' ?>" title="">
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
    ajaxSelectParams('#machines_id', 'admin/suggest_repalce/searchMachines', $("#machines_id").val(), true, true);
    $("#branch_id").select2();
    $("#category_calibration").select2();
    $("#result_id").select2();
    appValidateForm($('#request_calibration'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        result_id: 'required',
        category_calibration: 'required',
        machines_id: 'required'
    }, detail);

    function total() {
        quantity = intVal($('.quantity').val());
        price = intVal($('.price').val());
        total_amount = quantity * price;
        $('.total_amount').html(tnhFormatMoney(total_amount));
    }

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