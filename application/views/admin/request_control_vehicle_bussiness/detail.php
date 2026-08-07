<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(
        admin_url('request_control_vehicle_bussiness/detail/' . $id),
        ['id' => 'request_control_vehicle_bussiness']
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
                            <input type="text" name="reference_no" class="form-control" id="reference_no"
                                   value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly=""
                                   aria-invalid="false">
                        </div>
                    </td>
                    <td style="width: 15%;">
                        <?= lang('Ngày hiệu lực', 'date') ?>
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
                    <td><?= lang('Lý do điều xe', 'object_type') ?></td>
                    <td>
                        <select name="object_type" id="object_type" class="object_type" data-placeholder="<?= lang('Lý do điều xe') ?>" style="width: 100%;">
                            <option value=""></option>
                            <option value="request_bussiness" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'request_bussiness') ? 'selected' : '' ?>>Công tác</option>
                            <option value="delivery" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'delivery') ? 'selected' : '' ?>>Giao hàng</option>
                            <option value="purchase_order" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'purchase_order') ? 'selected' : '' ?>>Mua hàng</option>
                            <option value="suggest_outsource" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'suggest_outsource') ? 'selected' : '' ?>>Gia công</option>
                            <option value="other" <?= (!empty($dtData['object_type']) && $dtData['object_type'] == 'other') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </td>
                    <td><?= lang('', 'object_id') ?></td>
                    <td>
                        <div class="select_object_main mbot10 <?=($dtData['object_type'] != 'delivery' && $dtData['object_type'] != 'purchase_order') ? 'hide' : ''?>">
                            <input type="text" name="object_main" id="object_main" class="object_main" data-placeholder="" style="width: 100%;" value="<?= !empty($dtData['object_main']) ? $dtData['object_main'] : '' ?>" title="">
                        </div>
                        <input type="text" name="object_id" id="object_id" class="object_id form-control" data-placeholder="" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['object_id'] : '' ?>" title="">
                    </td>
                </tr>
                <tr>
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
                    <td><?= lang('Người phụ trách', 'staff_id') ?></td>
                    <td colspan="1">
                        <select name="staff_id" id="staff_id" class="staff_id"
                                data-placeholder="<?= lang('Người phụ trách') ?>" style="width: 100%;">
                            <option value=""></option>
							<?php if (!empty($employees)) { ?>
								<?php foreach ($employees as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['staff_id'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?>
                                            value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
								<?php } ?>
							<?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Nhân viên', 'employees') ?></td>
                    <td colspan="1">
                        <select name="employees[]" id="employees" class="employees" multiple data-placeholder="<?= lang('Nhân viên được điều xe') ?>" style="width: 100%;">
                            <option value=""></option>
							<?php if (!empty($employees)) { ?>
								<?php foreach ($employees as $key => $value) { ?>
                                    <?php
                                        $selected = '';
                                        if(!empty($dtData['employees']) && is_numeric(array_search($value['staffid'], $dtData['employees']))) {
											$selected = 'selected';
                                        }
                                    ?>
                                    <option data-role="<?= $value['role'] ?>" data-name_role="<?= $value['name_role'] ?>"  <?=$selected?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
								<?php } ?>
							<?php } ?>
                        </select>
                    </td>
                    <td><?= lang('Người khác ngoài nhân viên', 'employees_other') ?></td>
                    <td colspan="1">
                        <input type="text" name="employees_other" id="employees_other" class="employees_other form-control" value="<?=!empty($dtData) ? $dtData['employees_other'] : ''?>">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Tên phương tiện', 'vehicle_name') ?></td>
                    <td colspan="1">
                        <select name="vehicle_name" id="vehicle_name" class="vehicle_name" data-placeholder="<?= lang('Phương tiện') ?>" style="width: 100%;">
                            <option value=""></option>
							<?php if (!empty($list_vehicle)) { ?>
								<?php foreach ($list_vehicle as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['vehicle_name'] == $value['id'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['id'] ?>"
                                            data-type="<?=$value['type_vehicle']?>"
                                            data-unit_name="<?=$value['unit_name']?>"
                                            data-address="<?=$value['destination']?>"
                                            data-number_km="<?=$value['number_km']?>"
                                            data-price="<?=$value['price']?>"
                                            data-currency_unit="<?=$value['currency_unit']?>"
                                    ><?= $value['code_vehicle'] ?>
                                    </option>
								<?php } ?>
							<?php } ?>
                        </select>
                    </td>
                    <td><?= lang('Loại phương tiện', 'type_vehicle') ?></td>
                    <td colspan="1">
                        <input type="text" name="type_vehicle" id="type_vehicle" class="type_vehicle form-control" readonly
                               value="<?= !empty($dtData) ? ($dtData['type_vehicle']) : '' ?>">
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
                    <td><?= lang('Số km đi', 'number_km') ?></td>
                    <td colspan="1">
                        <input type="text" name="number_km" id="number_km" class="number_km form-control number-format text-right" onchange="changePrice(this)"
                               value="<?= !empty($dtData) ? formatMoney($dtData['number_km']) : '' ?>">
                    </td>
                    <td><?= lang('Đơn giá', 'price') ?></td>
                    <td colspan="1">
                        <input type="text" name="price" onchange="changePrice(this)" id="price" class="price form-control number-format text-right"
                               value="<?= !empty($dtData) ? formatMoney($dtData['price']) : '' ?>">
                    </td>
                    
                </tr>
                <tr>
                    <td><?= lang('Phí cầu đường/phà', 'cost_tolls') ?></td>
                    <td colspan="1">
                        <input type="text" name="cost_tolls" id="cost_tolls"
                               class="cost_tolls form-control number-format text-right" onchange="changePrice(this)"
                               value="<?= !empty($dtData) ? formatMoney($dtData['cost_tolls']) : '' ?>">
                    </td>
                    <td><?= lang('Định mức xăng dầu', 'quota_gasoline') ?></td>
                    <td colspan="1">
                        <input type="text" name="quota_gasoline" id="quota_gasoline"
                               class="quota_gasoline form-control number-format text-right" onchange="changePrice(this)"
                               value="<?= !empty($dtData) ? formatMoney($dtData['quota_gasoline']) : '' ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?= lang('Thành tiền ((Số KM * Đơn Giá) + Chi Phí)', 'amount') ?></td>
                    <td colspan="2" class="text-right">
                        <b class="td_amount text-danger mright10"><?= !empty($dtData) ? formatMoney($dtData['amount']) : '' ?></b>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Thời Gian Bắt Đầu', 'time_start') ?></td>
                    <td>
						<?php echo render_datetime_input('time_start', '', (!empty($dtData['time_start']) ? _dt($dtData['time_start']) : '')); ?>
                    </td>
                    <td><?= lang('Thời Gian Kết Thúc', 'time_end') ?></td>
                    <td>
						<?php echo render_datetime_input('time_end', '', (!empty($dtData['time_end']) ? _dt($dtData['time_end']) : '')); ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Ghi chú', 'note') ?></td>
                    <td colspan="3">
						<?php echo render_textarea('note', '', (!empty($dtData['note']) ? _dt($dtData['note']) : '')); ?>
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
    ajaxSelectParams('#request_bussiness_id', 'admin/request_control_vehicle_bussiness/searchRequestBussiness', $("#request_bussiness_id").val(), true, true);

    $('#object_type').select2();
    $('#employees').select2();
    $('#branch_id').select2();
    $('#vehicle_name').select2();

    $('#object_type').change(function(){
        var objectType = $(this).val();
        $('#object_id').val('');
        $("#object_id").removeClass('form-control');
        $('#object_main').val('');
        $('.select_object_main').addClass('hide');
        $('.address').val('');
        $('.phone').val('');
        ReloadFunctionDefault();
        // if(objectType == 'request_bussiness') {
        //     $("#object_main").select2('destroy');
        //     $('#object_id').attr('data-placeholder', 'Phiếu yêu cầu công tác');
        //     $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Phiếu yêu cầu công tác');
        //     ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', '', true, true, {'type' : objectType});
        // }
        // else if(objectType == 'delivery') {
        //     $('.select_object_main').removeClass('hide');
        //     $('#object_main').attr('data-placeholder', 'Khách hàng');
        //     $('#object_id').attr('data-placeholder', 'Phiếu giao hàng');
        //     $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Giao hàng');
        //     ajaxSelectParamsGet('#object_main', 'admin/request_control_vehicle_bussiness/searchObjectMain', '', true, true, {'type' : objectType});
        //     ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', '', true, true, {'type' : objectType});
        //
        // }
        // else if(objectType == 'purchase_order') {
        //     $('.select_object_main').removeClass('hide');
        //     $('#object_main').attr('data-placeholder', 'Nhà cung cấp');
        //     $('#object_id').attr('data-placeholder', 'Phiếu mua hàng');
        //     $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Mua hàng');
        //     ajaxSelectParamsGet('#object_main', 'admin/request_control_vehicle_bussiness/searchObjectMain', '', true, true, {'type' : objectType});
        //     ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', '', true, true, {'type' : objectType});
        //
        // }
        // else if(objectType == 'other') {
        //     $("#object_main").select2('destroy');
        //     $('#object_id').attr('data-placeholder', 'Khác');
        //     $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Khác');
        //     $("#object_id").addClass('form-control');
        //     $("#object_id").select2('destroy');
        //
        // }
    })
    
    $('#object_main').change(function() {
        ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', '', true, true, {
            'type': $('#object_type').val(),
            'object_main' : $('#object_main').val()
        }, true);
    })
    $(function(){
        ReloadFunctionDefault();
    })

    function ReloadFunctionDefault()
    {
        $("#object_id").removeClass('form-control');
        var objectType = $('#object_type').val();
        if(objectType == 'request_bussiness') {
            $("#object_main").select2('destroy');
            $('#object_id').attr('data-placeholder', 'Phiếu yêu cầu công tác');
            $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Phiếu yêu cầu công tác');
            ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', $('#object_id').val(), true, true, {'type' : objectType});
        }
        else if(objectType == 'suggest_outsource') {
            $("#object_main").select2('destroy');
            $('#object_id').attr('data-placeholder', 'Phiếu yêu cầu gia công');
            $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Phiếu yêu cầu gia công');
            ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', $('#object_id').val(), true, true, {'type' : objectType});
        }
        else if(objectType == 'delivery') {
            $('.select_object_main').removeClass('hide');
            $('#object_main').attr('data-placeholder', 'Khách hàng');
            $('#object_id').attr('data-placeholder', 'Phiếu giao hàng');
            $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Giao hàng');
            ajaxSelectParamsGet('#object_main', 'admin/request_control_vehicle_bussiness/searchObjectMain', $('#object_main').val(), true, true, {'type' : objectType});
            ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', $('#object_id').val(), true, true, {
                'type' : objectType,
                'object_main' : $('#object_main').val()
            }, true);
        
        }
        else if(objectType == 'purchase_order') {
            $('.select_object_main').removeClass('hide');
            $('#object_main').attr('data-placeholder', 'Nhà cung cấp');
            $('#object_id').attr('data-placeholder', 'Phiếu mua hàng');
            $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Mua hàng');
            ajaxSelectParamsGet('#object_main', 'admin/request_control_vehicle_bussiness/searchObjectMain', $('#object_main').val(), true, true, {'type' : objectType});
            ajaxSelectParamsGet('#object_id', 'admin/request_control_vehicle_bussiness/searchObject', $('#object_id').val(), true, true, {
                'type' : objectType,
                'object_main' : $('#object_main').val()
            }, true);
        
        }
        else if(objectType == 'other') {
            $("#object_main").select2('destroy');
            $('#object_id').attr('data-placeholder', 'Khác');
            $('#request_control_vehicle_bussiness').find('label[for="object_id"]').text('Khác');
            $("#object_id").addClass('form-control');
            $("#object_id").select2('destroy');
        }
    }


    $('#object_id').change(function() {
        if($('#object_type').val() != 'other') {
            var address = '';
            var phonenumber = '';
            var object_id = $('#object_id');
            if(object_id.select2('data').length > 0) {
                address = object_id.select2('data')[0].address;
                phonenumber = object_id.select2('data')[0].phonenumber;
                branch_id = object_id.select2('data')[0].branch_id;
                $('.address').val(address);
                $('.phone').val(phonenumber);
                $('.branch_id').val(branch_id).select2();
            }
            else {
                address = object_id.select2('data').address;
                phonenumber = object_id.select2('data').phonenumber;
                branch_id = object_id.select2('data').branch_id;
                $('.address').val(address);
                $('.phone').val(phonenumber);
                $('.branch_id').val(branch_id).select2();
            }
        }
    })
    $('#vehicle_name').change(function() {
       var id = $(this).val();
       var type_vehicle = $(this).find('option:selected').attr('data-type');
       var unit_name = $(this).find('option:selected').attr('data-unit_name');
       var address = $(this).find('option:selected').attr('data-address');
       var number_km = $(this).find('option:selected').attr('data-number_km');
       var price = $(this).find('option:selected').attr('data-price');
       var currency_unit = $(this).find('option:selected').attr('data-currency_unit');
       
       $('#type_vehicle').val(type_vehicle);
       $('#number_km').val(number_km);
       $('#price').val(price).trigger('change');
    })
    
    
    $("#staff_id").select2();
    appValidateForm($('#request_control_vehicle_bussiness'), {
        object_type: 'required',
        date: 'required',
        reference_no: 'required',
        staff_id: 'required',
        branch_id: 'required',
    }, detail);

    function changePrice(_this){
        var price = intVal($('#price').val());
        var number_km = intVal($('#number_km').val());
        var quota_gasoline = intVal($('#quota_gasoline').val());
        var cost_tolls = intVal($('#cost_tolls').val());
        var amount = price * number_km;
        
        amount += quota_gasoline + cost_tolls;
        $(".td_amount").html(tnhFormatMoney(amount));
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
        }).done(function (data) {
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
        }).fail(function () {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
</script>