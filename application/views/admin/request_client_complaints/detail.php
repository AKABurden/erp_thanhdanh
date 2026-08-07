<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('request_client_complaints/detail/' . $id),
        ['id' => 'request_client_complaints']
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
                        <td><?= lang('Tên Brand', 'brand_id') ?></td>
                        <td colspan="1">
                            <select name="brand_id" id="brand_id" class="brand_id" data-placeholder="<?= lang('Tên Brand') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($brand)) { ?>
                                    <?php foreach ($brand as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['brand_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Khách hàng', 'client_id') ?></td>
                        <td colspan="1">
                            <input type="text" name="client_id" id="client_id" class="client_id" data-placeholder="<?= lang('Mã Khách hàng') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['client_id'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Người khiếu nại', 'employees') ?></td>
                        <td colspan="1">
                            <select name="employees" id="employees" class="employees" data-placeholder="<?= lang('Người khiếu nại') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['employees'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Nhóm khiếu nại', 'category_complaints') ?></td>
                        <td colspan="1">
                            <select name="category_complaints" id="category_complaints" class="category_complaints" data-placeholder="<?= lang('Nhóm khiếu nại') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtCategoryComplaints)) { ?>
                                    <?php foreach ($dtCategoryComplaints as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['category_complaints'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Chi tiết khiếu nại', 'detail_complaints') ?></td>
                        <td colspan="1">
                            <textarea name="detail_complaints" id="detail_complaints" class="form-control detail_complaints" cols="3" rows="4"><?= !empty($dtData) ? $dtData['detail_complaints'] : '' ?></textarea>
                        </td>
                        <td><?= lang('Người tiếp nhận (TD)', 'staff_tn') ?></td>
                        <td colspan="1">
                            <input type="text" name="staff_tn" id="staff_tn" class="staff_tn form-control" data-placeholder="<?= lang('Người tiếp nhận (TD)') ?>" value="<?= !empty($dtData) ? $dtData['staff_tn'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Định Mức Thời Gian', 'timequota') ?></td>
                        <td colspan="1">
                            <input type="text" name="timequota" id="timequota" class="timequota form-control" data-placeholder="<?= lang('Định Mức Thời Gian') ?>" value="<?= !empty($dtData) ? $dtData['timequota'] : '' ?>" title="">
                        </td>

                        <td><?= lang('Nguyên Nhân', 'causal') ?></td>
                        <td colspan="1">
                            <input type="text" name="causal" id="causal" class="causal form-control" data-placeholder="<?= lang('Nguyên Nhân') ?>" value="<?= !empty($dtData) ? $dtData['causal'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Quy Trình Xử Lý', 'processing_procedures') ?></td>
                        <td colspan="1">
                            <input type="text" name="processing_procedures" id="processing_procedures" class="processing_procedures form-control" data-placeholder="<?= lang('Quy Trình Xử Lý') ?>" value="<?= !empty($dtData) ? $dtData['processing_procedures'] : '' ?>" title="">
                        </td>

                        <td><?= lang('Quy Trình Phòng Ngừa', 'prevention_procedures') ?></td>
                        <td colspan="1">
                            <input type="text" name="prevention_procedures" id="prevention_procedures" class="prevention_procedures form-control" data-placeholder="<?= lang('Quy Trình Phòng Ngừa') ?>" value="<?= !empty($dtData) ? $dtData['prevention_procedures'] : '' ?>" title="">
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
    ajaxSelectParams('#client_id', 'admin/request_client_complaints/searchClients', $("#client_id").val(), true, true);
    $("#branch_id").select2();
    $("#result_id").select2();
    $("#category_complaints").select2();
    $("#employees").select2();
    $("#brand_id").select2();
    appValidateForm($('#request_client_complaints'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        result_id: 'required',
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