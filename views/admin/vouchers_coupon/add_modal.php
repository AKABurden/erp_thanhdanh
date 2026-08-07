<div class="modal fade" id="payment_vouchers_coupon" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open(admin_url('vouchers_coupon/add'), array('id' => 'form-vouchers_coupon')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('coupon'); ?> </span>
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
                                <label for="customer" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('client'); ?>
                                </label>
                            </td>
                            <td>
                                <input type="text" name="customer" data-placeholder="<?= _l('client') ?>" id="customer" class="customer" style="width: 100%;" value="">
                            </td>
                            <td>
                                <label for="code_orders" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('ch_code_orders'); ?>
                                </label>
                            </td>
                            <td>
                                <?php echo render_select('code_orders[]', array(), array('id', 'reference_no'), '', '', array('multiple' => true, 'data-actions-box' => true, 'required' => true), array(), '', '', false); ?>
                            </td>
                        </tr>
                        <!-- yct start -->
                        <tr>
                            <td style="width: 17%;">
                                <label for="colcat" class="control-label">
                                    <?php echo _l('colcat_parent_code'); ?>
                                </label>
                            </td>
                            <td>
                                <div class="colcat_parent_code"></div>
                            </td>
                            <td>
                                <label for="colcat" class="control-label">
                                    <?php echo _l('colcat_parent_name'); ?>
                                </label>
                            </td>
                            <td>
                                <div class="colcat_parent_name"></div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 17%;">
                                <label for="colcat" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('collect_categories'); ?>
                                </label>
                            </td>
                            <td>
                                <?php $value = (isset($items) ? $items->objects : ''); ?>
                                <div class="form-group">
                                    <select id="id_costs" name="id_costs" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                        <option value=""></option>
                                        <?php
                                        $id_costs = (isset($items) ? $items->id_costs : '');
                                        if (!empty($colcat_list)) {
                                            foreach ($colcat_list as $key => $value) { ?>
                                                <?php if (!empty($value['name']) && !empty($value['data'])) { ?>
                                                    <optgroup label="<?= $value['name'] ?>">
                                                        <?php foreach ($value['data'] as $k => $v) { ?>
                                                            <option value="<?= $v['id'] ?>" <?= $id_costs == $v['id'] ? 'selected' : '' ?> data-subtext="<?= $v['name'] ?>" data-name_parent="<?= !empty($colcat_parent[$v['costs_parent']]) ? $colcat_parent[$v['costs_parent']]['name'] : '' ?>" data-code_parent="<?= !empty($colcat_parent[$v['costs_parent']]) ? $colcat_parent[$v['costs_parent']]['code'] : '' ?>">
                                                                <?= $v['code'] ?>
                                                            </option>
                                                        <?php } ?>
                                                    </optgroup>
                                                <?php } ?>
                                        <?php }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td style="width: 17%;">
                                <label for="colcat" class="control-label">
                                    <small class="req text-danger hide ch_list_objects">* </small>
                                    <?php echo _l('colcat_name'); ?>
                                </label>
                            </td>
                            <td>
                                <div class="colcat_name"></div>
                            </td>
                        </tr>
                        <!-- yct end -->
                        <tr>
                            <td>
                                <label for="staff" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('staff_coupon'); ?>
                                </label>
                            </td>
                            <td>
                                <?php echo render_select('staff', $staff, array('staffid', 'fullname'), ''); ?>
                            </td>
                            <td>
                                <label for="payment_mode" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('acs_sales_payment_modes_submenu'); ?>
                                </label>
                            </td>
                            <td>
                                <?php echo render_select('payment_mode', $payment_modes, array('id', 'name'), ''); ?>
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
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="note" class="control-label">
                                    <?php echo _l('note'); ?>
                                </label>
                            </td>
                            <td colspan="3">
                                <textarea rows="5" id="note" name="note" class="form-control " value=""></textarea>
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
        _validate_form($('#form-vouchers_coupon'), {
            date_vouchers: "required",
            code_vouchers: "required",
            customer: "required",
            staff: "required",
            payment_mode: "required",
            id_costs: "required",
        }, add_form);
        ajaxSelectCustomerCallBack('#customer', 'admin/vouchers_coupon/searchCustomers', $('#customer').val());
        init_selectpicker();
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
                        url: base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: base_url + url,
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
                    url: base_url + url,
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

    $(document).on('change', '#customer', function(e) {
        var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
        var data = {};
        data['customer'] = $(this).val();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'vouchers_coupon/getData_order', data).done(function(response) {
            response = JSON.parse(response);
            $('select[name="code_orders[]"]').html('');
            var option = '';
            $.each(response, function(i, v) {
                let total = v.grand_total - v.total_payment;
                if (total > 0) {
                    option += '<option value="' + v.id + '" data-subtext="' + formatNumber(v.grand_total - v.total_payment) + '">' + v.reference_no + '</option>';
                }
            });
            $('select[name="code_orders[]"]').append(option);
            $('select[name="code_orders[]"]').selectpicker('refresh');
        });
    });

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };


    //load trang voi action
    function add_form(form) {
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
    //end

    // yct start
    $('#id_costs').change(function() {
        var colcat_name = $(this).find('option:selected').data('subtext');
        var colcat_parent_name = $(this).find('option:selected').data('name_parent');
        var colcat_parent_code = $(this).find('option:selected').data('code_parent');
        // console.log($(this).find('option:selected'));
        $('.colcat_name').text(colcat_name);
        $('.colcat_parent_name').text(colcat_parent_name);
        $('.colcat_parent_code').text(colcat_parent_code);
    })
    $('#id_costs').trigger('change');
    // yct end
</script>