<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('request_bussiness/detail/' . $id),
        ['id' => 'request_bussiness']
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
                        <td><?= lang('Nội dung đánh giá', 'content') ?></td>
                        <td colspan="3">
                            <textarea name="content" id="content" class="form-control content" cols="3" rows="4"><?= !empty($dtData) ? $dtData['content'] : '' ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Người đề xuất', 'employees') ?></td>
                        <td colspan="1">
                            <select onchange="changeemployees_localtion()" name="employees" id="employees" class="employees" data-placeholder="<?= lang('Người đề xuất') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option data-role="<?= $value['role'] ?>" data-name_role="<?= $value['name_role'] ?>" <?= !empty($dtData) ? ($dtData['employees'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td><?= lang('Mã vị trí người đề xuất', 'employees_localtion') ?></td>
                        <td colspan="1">
                            <label class="name_employees_localtion"><?=!empty($dtData['name_employees_localtion']) ? $dtData['name_employees_localtion'] : ''?></label>
                            <input type="hidden" name="employees_localtion" id="employees_localtion" class="employees_localtion" value="<?=!empty($dtData) ? $dtData['employees_localtion'] : ''?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Số Lượng Người Đi', 'quantity') ?></td>
                        <td colspan="1">
                            <input type="text" name="quantity" id="quantity" onchange="total();" class="quantity form-control number-format" data-placeholder="<?= lang('Số Lượng Người Đi') ?>" value="<?= !empty($dtData) ? formatNumber($dtData['quantity']) : '' ?>" title="">
                        </td>
                        <td><?= lang('Mã vị trí người đi', 'staff_localtion') ?></td>
                        <td colspan="1">
                            <select name="staff_localtion" id="staff_localtion" class="staff_localtion" data-placeholder="<?= lang('Mã vị trí người đi') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($dtRoles)) { ?>
                                    <?php foreach ($dtRoles as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['staff_localtion'] == $value['roleid'] ? 'selected' : '') : '' ?> value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Điểm đến', 'object_type') ?></td>
                        <td>
                            <select name="object_type" id="object_type" class="object_type" data-placeholder="<?= lang('Điểm đến') ?>" style="width: 100%;">
                                <option value=""></option>
                                <option value="customer" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'customer') ? 'selected' : '' ?>>Khách Hàng</option>
                                <option value="supplier" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'supplier') ? 'selected' : '' ?>>Nhà cung cấp</option>
                                <option value="other" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'other') ? 'selected' : '' ?>>Khác</option>
                            </select>
                        </td>
                        <td><?= lang('', 'object_id') ?></td>
                        <td>
                            <input type="text" name="object_id" id="object_id" class="object_id form-control" data-placeholder="<?= lang('Mã Khách hàng') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['object_id'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Địa chỉ đến', 'address') ?></td>
                        <td colspan="1">
                            <textarea name="address" id="address" class="form-control address" cols="3" rows="4"><?= !empty($dtData) ? $dtData['address'] : '' ?></textarea>
                        </td>
                        <td><?= lang('Số Điện Thoại Liên Hệ', 'phone') ?></td>
                        <td colspan="1">
                            <input type="text" name="phone" id="phone" class="phone form-control" data-placeholder="<?= lang('Số Điện Thoại Liên Hệ') ?>" value="<?= !empty($dtData) ? $dtData['phone'] : '' ?>" title="">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Thời Gian Bắt Đầu Đi Công Tác', 'time_start') ?></td>
                        <td colspan="1">
                            <?php echo render_datetime_input('time_start', '', (!empty($dtData) ? _dt($dtData['time_start']) : '')); ?>
                        </td>
                        <td><?= lang('Thời Gian Kết Thúc Đi Công Tác', 'time_end') ?></td>
                        <td colspan="1">
                            <?php echo render_datetime_input('time_end', '', (!empty($dtData) ? _dt($dtData['time_end']) : '')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Chi phí công tác', 'amount') ?></td>
                        <td colspan="1">
                            <input type="text" name="amount" id="amount" onchange="total();" class="amount form-control number-format" data-placeholder="<?= lang('Chi phí công tác') ?>" value="<?= !empty($dtData) ? formatNumber($dtData['amount']) : '' ?>" title="">
                        </td>
                        <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                        <td colspan="1">
                            <?php $branchs = getListBranch(); ?>
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
    
    
    $('#object_type').change(function(){
        var objectType = $(this).val();
        $('#object_id').val('');
        $("#object_id").removeClass('form-control');
        $('.address').val('');
        $('.phone').val('');
        if(objectType == 'customer') {
            $('#object_id').attr('data-placeholder', 'Khách hàng');
            $('#request_bussiness').find('label[for="object_id"]').text('Khách hàng');
            ajaxSelectParamsGet('#object_id', 'admin/request_bussiness/searchObject', '', true, true, {'type' : 'customer'});
        }
        else if(objectType == 'supplier') {
            $('#object_id').attr('data-placeholder', 'Nhà Cung Cấp');
            $('#request_bussiness').find('label[for="object_id"]').text('Nhà Cung Cấp');
            ajaxSelectParamsGet('#object_id', 'admin/request_bussiness/searchObject', '', true, true, {'type' : 'supplier'});
        }
        else if(objectType == 'other') {
            $('#object_id').attr('data-placeholder', 'Khác');
            $('#request_bussiness').find('label[for="object_id"]').text('Khác');
            $("#object_id").addClass('form-control');
            $("#object_id").select2('destroy')
            
        }
    })
    $(function(){
        ReloadFunctionDefault();
    })

    function ReloadFunctionDefault()
    {
        $("#object_id").removeClass('form-control');
        if ($('#object_type').val() == 'customer') {
            $('#object_id').attr('data-placeholder', 'Khách Hàng');
            $('#request_bussiness').find('label[for="object_id"]').text('Khách Hàng');
            ajaxSelectParamsGet('#object_id', 'admin/request_bussiness/searchObject', $('#object_id').val(), true, true, {'type': 'customer'});
        } else if ($('#object_type').val() == 'supplier') {
            $('#object_id').attr('data-placeholder', 'Nhà Cung Cấp');
            $('#request_bussiness').find('label[for="object_id"]').text('Nhà Cung Cấp');
            ajaxSelectParamsGet('#object_id', 'admin/request_bussiness/searchObject', $('#object_id').val(), true, true, {'type': 'supplier'});
        } else if ($('#object_type').val() == 'other') {
            $('#object_id').attr('data-placeholder', 'Khác');
            $('#request_bussiness').find('label[for="object_id"]').text('Khác');
            $("#object_id").addClass('form-control');
            $("#object_id").select2('destroy');
        }
    }
    
    
    $('#object_id').change(function() {
        if($('#object_type').val() != 'other') {
            var address = '';
            var phonenumber = '';
            var object_id = $('#object_id');
            address = object_id.select2('data').address;
            phonenumber = object_id.select2('data').phonenumber;
            $('.address').val(address);
            $('.phone').val(phonenumber);
        }
    })
    
    
    $("#object_type").select2();
    $("#branch_id").select2();
    $("#employees").select2();
    $("#staff_localtion").select2();
    appValidateForm($('#request_bussiness'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        employees: 'required',
        staff_localtion: 'required'
    }, detail);

    function changeemployees_localtion() {
        var employees = $('.employees');
        var role = employees.find('option:selected').attr('data-role');
        var name_role = employees.find('option:selected').attr('data-name_role');
        $('.employees_localtion').val(role);
        $('.name_employees_localtion').text(name_role);
    }

    function changeclient() {
        client_id = $('.client_id');
        address = client_id.select2('data').address;
        phonenumber = client_id.select2('data').phonenumber;
        $('.address').val(address);
        $('.phone').val(phonenumber);
    }

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