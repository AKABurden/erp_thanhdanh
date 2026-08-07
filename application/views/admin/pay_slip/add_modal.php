<div class="modal fade" id="payment_vouchers_coupon" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open(admin_url('pay_slip/add'), array('id' => 'form-pay_slip')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_vouchers_for_purchase'); ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <table style="table-layout: fixed;" class="dt-tnh table table-bordered table-hover mtop0 mbot0">
                    <tbody>
                    <tr>
                        <td style="width: 20%;">
                            <label for="date_vouchers" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('ch_date_p'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <div class="input-group date">
                                <input type="text" id="date_vouchers" name="date_vouchers" class="date_vouchers form-control datepicker" value="<?= _d(date('Y-m-d')) ?>">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </td>
                        <td style="width: 20%;">
                            <label for="code_vouchers" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('ch_code_p'); ?>
                            </label>
                        </td>
                        <td style="width: 40%;">
                            <div class="form-group">
                                <input type="text" id="code_vouchers" name="code_vouchers" class="form-control " readonly value="<?= $code ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="id_supplierss" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('supplier'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="text" name="id_supplierss" onchange="changeSupplies(this)" data-placeholder="<?= _l('supplier') ?>" id="id_supplierss" class="id_supplierss" style="width: 100%;" value="">
                        </td>
                        <td class="hide">
                            <label for="id_supplierss" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('ch_type_of_document'); ?>
                            </label>
                        </td>
                        <td class="hide">
                            <?php $list_objects = array(
                                array(
                                    'id' => 2,
                                    'name' => _l('tnh_retail_bill')
                                ),
                                array(
                                    'id' => 1,
                                    'name' => _l('tnh_tax_bill')
                                ),
                                // array('id'=>5,
                                //       'name'=>_l('Phiếu gia công')),
                            ); ?>
                            <?php echo render_select('type', $list_objects, array('id', 'name'),'',2,['onchange' => 'changeType(this)']); ?>
                        </td>
                        <td rowspan="2" style="vertical-align:middle">
                            <label for="code_orders" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('cong_code_orders'); ?>
                            </label>
                        </td>
                        <td rowspan="2" style="vertical-align:middle">
                            <?php echo render_select('code_orders[]', array(), array('id', 'reference_no'), '', '', array('multiple' => true, 'data-actions-box' => true, 'required' => true), array(), '', '', false); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo _l('Nhóm nhà cung cấp'); ?>
                        </td>
                        <td class="category_supplier">

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>
                                <?php echo _l('ch_receiver'); ?>
                            </label>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" id="receiver" name="receiver" class="form-control " value="">
                            </div>
                        </td>
                        <td>
                            <label for="note" class="control-label">
                                <?php echo _l('ch_costs'); ?>
                            </label>
                        </td>
                        <td>
                            <select data-live-search="true" class="id_costs selectpicker"  id="id_costs" style="width: 200px;" name="id_costs" data-width="100%" data-none-selected-text="<?php echo _l('ch_costs'); ?>">
                                <option></option>
                                <?php foreach ($costs as $k => $v) { ?>
                                    <option value="<?= $v['id'] ?>"> <?= $v['name'] ?> </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="payment_mode" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('acs_sales_payment_modes_submenu'); ?>
                            </label>
                        </td>
                        <td>
                            <?php echo render_select('payment_mode', $payment_modes, array('id', 'name'), ''); ?>
                        </td>
                        <td>
                            <label for="payment_mode" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('currency_lowercase'); ?>
                            </label>
                        </td>
                        <td>
                            <select required="true" class="currency selectpicker" disabled id="currency" style="width: 200px;" name="currency" data-width="100%" data-none-selected-text="<?php echo _l('currency_lowercase'); ?>">
                                <option></option>
                                <?php foreach ($currency as $k => $v) { ?>
                                    <option data-name="<?= $v['name'] ?>" data-total="<?= $v['amount_to_vnd'] ?>" data-subtext="<?= formatNumber($v['amount_to_vnd']) ?><?= $v['symbol'] ?>" <?= (($v['id'] == 3) ? 'selected' : '') ?> value="<?= $v['id'] ?>"><?= $v['name'] ?> </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="total" class="control-label">
                                <?php echo _l('ch_total_total'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="text" id="total" name="total" class="form-control " value="0" readonly>
                        </td>
                        <td>
                            <label for="payment" class="control-label">
                                <?php echo _l('ch_total_payment'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="text" id="payment" onkeyup="formatNumBerKeyUpCus(this)" name="payment" class="form-control " value="0">
                            <!-- <span id="total_fund_text" style="color: red;"><?= _l('ch_kq_cl') ?>: <span id="total_fund"><?= number_format($fund) ?></span></span> -->
                        </td>

                    </tr>
                    <tr>
                        
                        <td>
                            <label for="note" class="control-label">
                                <?php echo _l('note'); ?>
                            </label>
                        </td>
                        <td colspan="3">
                            <textarea rows="3" id="note" name="note" class="form-control " value=""></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("#branch_id").attr('required',true);
        _validate_form($('#form-pay_slip'), {
            date_vouchers: "required",
            code_vouchers: "required",
            id_supplierss: "required",
            type: "required",
            staff: "required",
            payment_mode: "required",
            payment: "required",
        }, add_form);
        ajaxSelectCustomerCallBack('#id_supplierss', '<?= admin_url('pay_slip/searchSuppliers') ?>', $('#id_supplierss').val());
    });

    function ajaxSelectCustomerCallBack(element, url, id) {
        if (id) {
            $(element).val(id).select2({
                width: 'resolve',
                allowClear: true,
                escapeMarkup: function(m) {
                    return m;
                },
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + $(element).val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        } else {
            $(element).select2({
                width: 'resolve',
                allowClear: true,
                escapeMarkup: function(m) {
                    return m;
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        }
    }
    function changeSupplies(_this) {
        var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
        var data = {};
        data['id_supplierss'] = $(_this).val();
        data['type'] = $('#type').val();
        dataSelect = $(_this).select2('data');
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'pay_slip/getData_order', data).done(function(response) {
            $('select[name="code_orders[]"]').html(response);
            $('select[name="code_orders[]"]').selectpicker('refresh');
        });
        $.post(admin_url + 'suppliers/get_debt/' + data['id_supplierss'], {
            [csrfData['token_name']]: csrfData['hash']
        }, function(datas) {
            datas = JSON.parse(datas);
            if (datas.default_currency != "0") {
                $('#currency').val(datas.default_currency).selectpicker('refresh');
            } else {
                $('#currency').val(3).selectpicker('refresh');
            }
            $('#currency').trigger('change');
            $('#total_fund').html(datas.fund);
        });
        $('#payment_qd').val(formatNumber(0));
        $('#total').val(formatNumber(0));

        $(".category_supplier").html(dataSelect.name_category);
    };
    $('#total_fund_text').addClass('hide');
    $(document).on('change', '#payment_mode', function(e) {
        var payment_mode = $('#payment_mode').val();
        if (payment_mode == 5) {
            $('#total_fund_text').removeClass('hide');
        } else {
            $('#total_fund_text').addClass('hide');
        }
    });

    function changeType(_this) {
        var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
        var data = {};
        data['id_supplierss'] = $('#id_supplierss').val();
        data['type'] = $(_this).val();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'pay_slip/getData_order', data).done(function(response) {
            $('select[name="code_orders[]"]').html(response);
            $('select[name="code_orders[]"]').selectpicker('refresh');
        });
        $('#payment_qd').val(formatNumber(0));
        $('#total').val(formatNumber(0));
    };

    $(document).on('change', 'select[name="code_orders[]"]', function(e) {
        var total = 0;
        if ($(this).val().length > 0) {
            $.each($(this).val(), function(i, v) {
                if ($('select[name="code_orders[]').find('option[value=' + v + ']').length > 0) {
                    total += Number(unformat_number($('select[name="code_orders[]').find('option[value=' + v + ']').attr('data-subtext')));
                }
            });

            var currents = Number($('#currency').find('option:selected').attr('data-total'));
            $('#payment_qd').val(formatNumber(total * currents));
            $('#total').val(formatNumber(total));
        } else {
            $('#total').val(formatNumber(total));
        }
    });

    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2 = x2.substr(0, 2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };
    //load trang voi action
    function add_form(form) {
        var total = unformat_number($('#total').val());
        var payment = unformat_number($('#payment').val());
        var payment_mode = unformat_number($('#payment_mode').val());
        var total_currency = $(".currency").find('option:selected').attr('data-total');
        var id_costs = $("#id_costs").val();
        if (id_costs == ''){
            alert('<?= _l('Vui lòng chọn loại chi phí') ?>');
            return;
        }

        //if (payment_mode == 5) {
        //    var fund = Number(unformat_number($('#total_fund').text()));
        //    if (payment > fund) {
        //        var currencys = $(".currency").find('option:selected').attr('data-name');
        //        alert('<?//= _l('expense_amount') ?>// ' + fund + ' ' + currencys + ' <?//= _l('ch_not_ct') ?>//');
        //        return;
        //    }
        //}
        // payment = payment * total_currency;
        if (Number(payment) < 0) {
            alert('<?= _l('ch_false_pay_slip') ?>');
            return;
        }
        // if (Number(payment) > Number(total)) {
        //     alert('<?= _l('ch_false_pay_slip') ?>');
        //     return;
        // }
        if ($('#payment-form input.error').length == 0) {
            $('#submit').button('loading');
        }

        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form),
                alert_float(form.alert_type, form.message);
            if (form.success) {
                tAPI.draw('page');
                $('#payment_vouchers_coupon').modal('hide');
            }
        }), !1
    }
    $(document).on('change', '.type_branch', function (e) {
        var value = $(this).val();
        if (value == 0){
            $('.show-branch').removeClass('hide');
            $("#branch_id").attr('required',true);
        } else {
            $('.show-branch').addClass('hide');
            $("#branch_id").attr('required',false);
            $("#branch_id").val('').selectpicker('refresh');
        }
    });
    //end
</script>