<div class="modal fade" id="other_payslips_coupon" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <?php
            $disabled = array();
            if (isset($items)) {
                $disabled = array('disabled' => true);
            }
            echo form_open(admin_url('other_payslips_coupon/pay_slip/'), array('id' => 'payment-form'));
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('other_payslips_coupon'); ?> </span>
                </h4>
            </div>
            <div class="modal-body" style="height:auto">
                <?php
                if (isset($items)) {
                ?>
                    <input type="text" name="id_orther" class="hide" value="<?= $items->id ?>">
                <?php
                }
                ?>
                <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                    <tbody>
                        <tr>
                            <td style="width: 17%;">
                                <label for="number" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('ch_code_p'); ?>
                                </label>
                            </td>
                            <td>
                                <div class="form-group">
                                    <?php $value = (isset($items) ? $items->prefix . '-' . $items->code : $code); ?>
                                    <input type="text" id="code_vouchers" name="" class="form-control " readonly value="<?= $value ?>">
                                </div>
                            </td>
                            <td style="width: 17%;">
                                <label for="date" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('ch_date_p'); ?>
                                </label>
                            </td>
                            <td>
                                <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                                <?php echo render_date_input('date', '', $value); ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 17%;">
                                <label for="number" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('ch_objects'); ?>
                                </label>
                            </td>
                            <td>
                                <?php $list_objects = array(
                                    array(
                                        'id' => 1,
                                        'name' => _l('ch_IN_client')
                                    ),
                                    array(
                                        'id' => 2,
                                        'name' => _l('ch_IN_suppliers')
                                    ),
                                    array(
                                        'id' => 3,
                                        'name' => _l('ch_IN_staff')
                                    ),
                                    array(
                                        'id' => 4,
                                        'name' => _l('ch_IN_other')
                                    ),
                                ); ?>
                                <?php $value = (isset($items) ? $items->objects : ''); ?>
                                <?php echo render_select('objects', $list_objects, array('id', 'name'), '', $value, $disabled); ?>
                            </td>
                            <td style="width: 17%;">
                                <label for="date" class="control-label">
                                    <small class="req text-danger hide ch_list_objects">* </small>
                                    <?php echo _l('ch_list_objects'); ?>
                                </label>
                            </td>
                            <td>
                                <div class="append_id_objects">
                                    <input data-placeholder="<?= _l('ch_list_objects') ?>" name="objects_id" style="width: 100%" id="objects_id">
                                </div>
                            </td>
                        </tr>
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
                        <tr>
                            <td style="width: 17%;">
                                <label for="number" class="control-label">
                                    <?php echo _l('Loại chứng từ'); ?>
                                </label>
                            </td>
                            <td>
                                <?php
                                $type_vouchers = array(
                                    // array(
                                    //     'id' => 1,
                                    //     'name' => 'Đơn đặt hàng mua',
                                    // ),
                                    array(
                                        'id' => 2,
                                        'name' => 'Xuất kho khác',
                                    ),
                                    // array(
                                    //     'id' => 3,
                                    //     'name' => 'Báo giá NCC',
                                    // ),
                                    // array(
                                    //     'id' => 4,
                                    //     'name' => 'Hỏi giá NCC',
                                    // ),
                                    array(
                                        'id' => 5,
                                        'name' => 'Đơn đặt hàng bán',
                                    ),
                                    // array(
                                    //     'id' => 6,
                                    //     'name' => 'Báo giá',
                                    // ),
                                    // array(
                                    //     'id' => 7,
                                    //     'name' => 'Hợp đồng bán',
                                    // ),
                                    // array(
                                    //     'id' => 8,
                                    //     'name' => 'Trả hàng',
                                    // ),
                                );
                                ?>
                                <select class="selectpicker no-margin" data-width="100%" id="type_vouchers" data-none-selected-text="<?php echo _l('Loại chứng từ'); ?>" name="type_vouchers" data-live-search="true" <?= (isset($items) ? 'disabled' : '') ?>>
                                    <option value=""></option>

                                    <?php foreach ($type_vouchers as $product) { ?>
                                        <option <?= (isset($items) ? (($product['id'] == $items->type_vouchers) ? 'selected' : '') : '') ?> value="<?php echo $product['id']; ?>" data-subtext=""><?php echo $product['name']; ?></option>
                                    <?php
                                    } ?>
                                </select>
                            </td>
                            <td style="width: 17%;">
                                <label for="number" class="control-label">
                                    <?php echo _l('ch_list_code'); ?>
                                </label>
                            </td>
                            <td>
                                <select class="selectpicker no-margin" data-width="100%" id="vouchers_id" data-none-selected-text="<?php echo _l('ch_list_code'); ?>" name="vouchers_id" data-live-search="true" <?= (isset($items) ? 'disabled' : '') ?>>
                                    <option value=""></option>

                                    <?php foreach ($vouchers_id as $product) { ?>
                                        <option <?= (isset($items) ? (($product['id'] == $items->vouchers_id) ? 'selected' : '') : '') ?> value="<?php echo $product['id']; ?>" total-id="<?= $product['total_import'] ?>" data-subtext=""><?php echo $product['name']; ?> ( <?php echo number_format($product['total_import']) ?> )</option>
                                    <?php
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 17%;">
                                <label for="number" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('expense_add_edit_amount'); ?>
                                </label>
                            </td>
                            <td>
                                <?php $total = (isset($items) ? number_format($items->total) : 0); ?>
                                <input type="text" id="votes_total" onkeyup="formatNumBerKeyUpCus(this)" name="total" class="form-control " value="<?= $total ?>">
                            </td>
                            <td style="width: 17%;">
                                <label for="date" class="control-label">
                                    <small class="req text-danger">* </small>
                                    <?php echo _l('acs_sales_payment_modes_submenu'); ?>
                                </label>
                            </td>
                            <td>
                                <?php $value_payment_modes = (isset($items) ? $items->payment_modes : ''); ?>
                                <?php echo render_select('payment_modes', $payment_modes, array('id', 'name'), '', $value_payment_modes); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="number" class="control-label">
                                    <?php echo _l('note'); ?>
                                </label>
                            </td>
                            <td colspan="3">
                                <?php $notes = (isset($items) ? $items->note : ''); ?>
                                <textarea rows="3" id="note" name="note" class="form-control " value=""><?= $notes ?></textarea>
                            </td>

                        </tr>
                    </tbody>
                </table>
                <div class="clearfix"> </div>
            </div>
            <div class="modal-footer">
                <!-- data-loading-text="<?= _l('wait_text') ?>" -->
                <button type="submit" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                <!--  -->
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function validate_form() {
        _validate_form($('#payment-form'), {
            code_vouchers: "required",
            date: "required",
            objects: "required",
            payment_modes: "required",
            payment: "required",
            objects_id: "required",
            id_costs: "required",
            total: "required",
            objects_text: "required",
        }, add_payment);
    }
    $(function() {
        validate_form();
    });
    $("#objects").change(function() {
        $('#objects_id').selectpicker('refresh');
        $('#objects_id').attr('required', true);
        $('#vouchers_id').attr('disabled', false);
        var id = $('#objects').val();
        var id_objects_id = 0;
        <?php
        if (!empty($items)) { ?>
            id_objects_id = <?= $items->objects_id; ?>;
        <?php
        }
        ?>
        if (id == 1) {
            var html = '<div class="form-group id ">\
                    <input data-placeholder="Khách hàng" name="objects_id" style="width: 100%" value="' + id_objects_id + '" id="objects_id">\
                </div>';
            $('.append_id_objects').html(html);
            $('#objects_id').attr('required', true);
            $('.ch_list_objects').addClass('hide');
            ajaxSelectCallBack($('#objects_id'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            validate_form();
        } else if (id == 2) {
            var html = '<div class="form-group id ">\
                    <input data-placeholder="Nhà cung cấp" name="objects_id" style="width: 100%" value="' + id_objects_id + '" id="objects_id">\
                </div>';
            $('.append_id_objects').html(html);
            $('#objects_id').attr('required', true);
            $('.ch_list_objects').addClass('hide');
            ajaxSelectCallBack($('#objects_id'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            validate_form();
        } else if (id == 3) {
            var html = '<div class="form-group id ">\
                    <input data-placeholder="Nhân viên" name="objects_id" style="width: 100%" value="' + id_objects_id + '" id="objects_id">\
                </div>';
            $('.append_id_objects').html(html);
            $('#objects_id').attr('required', true);
            $('.ch_list_objects').addClass('hide');
            ajaxSelectCallBack($('#objects_id'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            $('#vouchers_id').attr('disabled', true);
            validate_form();
        } else if (id == 4) {

            var html1 = '<div class="form-group id">\
                    <input type="text" id="objects_text" name="objects_text" class="form-control objects_text" value="<?= (!empty($items) ? $items->objects_text : '') ?>">\
            </div>';
            $('.append_id_objects').html(html1);
            $('.vouchers_ids').find('button').addClass('no-drop-v2');
            $('#vouchers_id').attr('disabled', true);
            $('.ch_list_objects').removeClass('hide');

            validate_form();
        }
        <?php
        if (!empty($items)) { ?>
            $('#objects_id').attr('disabled', true);
        <?php
        }
        ?>
    });
    <?php
    if (!empty($items)) { ?>
        $('#objects').change();
    <?php
    }
    ?>

    $(document).on('change', '#objects_id', function(event) {
        var objects_id = $('#objects_id').val();
        var objects = $('#objects').val();
        var type_vouchers = $('#type_vouchers').val();
        dataString = {
            type_vouchers: type_vouchers,
            objects_id: objects_id,
            objects: objects,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>other_payslips_coupon/vouchers_id/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('#vouchers_id').find('option:gt(0)').remove();
                $.each(data, function(key, value) {
                    if (type_vouchers == 1) {
                        $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses - value.total_payment) + ' value="' + value.id + '">' + value.reference_no + ' (' + formatNumber((value.total - value.price_other_expenses - value.total_payment)) + ')</option>');
                    } else if (type_vouchers == 2) {
                        $('#vouchers_id').append('<option total-id=' + (value.total) + ' value="' + value.id + '">' + value.prefix + '-' + value.code + ' (' + formatNumber((value.total)) + ')</option>');
                    } else if (type_vouchers == 5) {
                        $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses_delivery) + ' value="' + value.id + '">' + value.reference_no + ' (' + formatNumber((value.total - value.price_other_expenses_delivery)) + ')</option>');
                    }
                });
                $('#vouchers_id').selectpicker('refresh');
            }
        });
    });
    $(document).on('change', '#type_vouchers', function(event) {
        var objects_id = $('#objects_id').val();
        var objects = $('#objects').val();
        var type_vouchers = $('#type_vouchers').val();
        dataString = {
            type_vouchers: type_vouchers,
            objects_id: objects_id,
            objects: objects,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>other_payslips_coupon/vouchers_id/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('#vouchers_id').find('option:gt(0)').remove();
                $.each(data, function(key, value) {
                    if (type_vouchers == 1) {
                        $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses - value.total_payment) + ' value="' + value.id + '">' + value.reference_no + ' (' + formatNumber((value.total - value.price_other_expenses - value.total_payment)) + ')</option>');
                    } else if (type_vouchers == 2) {
                        $('#vouchers_id').append('<option total-id=' + (value.total) + ' value="' + value.id + '">' + value.prefix + '-' + value.code + ' (' + formatNumber((value.total)) + ')</option>');
                    } else if (type_vouchers == 5) {
                        let totals = value.total - value.price_other_expenses - value.total_payment - value.total_return;
                        if (totals > 0) {
                            $('#vouchers_id').append('<option total-id=' + (value.total - value.price_other_expenses - value.total_payment - value.total_return) + ' value="' + value.id + '">' + value.reference_no + ' (' + formatNumber((value.total - value.price_other_expenses - value.total_payment - value.total_return)) + ')</option>');
                        }
                    }
                });
                if (type_vouchers == 2) {
                    $('#vouchers_id').attr('disabled', false);
                }
                $('#vouchers_id').selectpicker('refresh');
            }
        });
    });

    function add_payment(form) {
        var objects_id = $('#objects_id').val();
        var objects = $('#objects').val();
        if (objects == 1 && !empty(objects_id)) {
            var total_limit = $('option:selected', $('#vouchers_id')).attr('total-id');
            var total = unformat_number($('#votes_total').val());
            if (Number(total) < 0) {
                alert('<?= _l('Giá trị không hợp lệ') ?>');
                return;
            }
            if (Number(total) > Number(total_limit)) {
                alert('<?= _l('Giá trị không hợp lệ') ?>' + ' Bạn phải nhập nhỏ hơn hoặc bằng: ' + formatNumber(total_limit));
                return;
            }
        }
        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form),
                alert_float(form.alert_type, form.message);
            if (form.success) {
                tAPI.draw('page');
                $('#other_payslips_coupon').modal('hide');
            }

        }), !1
    }

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

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + $('#objects').val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#objects').val(),
                            types: types,
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
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#objects').val(),
                            types: types,
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
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    $(function(e) {
        <?php
        if (empty($items)) { ?>
            ajaxSelectCallBack($('#objects_id'), "<?= admin_url('other_payslips/SearchClient') ?>", 0);
        <?php
        }
        ?>
    })

    function repoFormatSelection(state) {
        var id = $('#objects').val();
        if (id == 3) {
            return state.text;
        }
        return '[' + state.code_client + '] ' + state.text;
    }

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