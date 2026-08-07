<div id="modal_step_three_two" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 60%;">
        <?php echo form_open(
            admin_url('request_repair/detail_step/' . $id . '/'. $id_request_repair),
            ['id' => 'request_repair']
        ); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?= $title ?? '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="type_processing" value="2">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td>
                            <?= lang('tnh_suppliers', 'supplier_id') ?>
                        </td>
                        <td>
                            <input type="text" name="supplier_id" id="supplier_id" class="modal-select2" data-placeholder="<?= lang('tnh_suppliers') ?>" style="width: 100%;" value="<?= !empty($request_repair) ? $request_repair['supplier_id'] : '' ?>">
                        </td>
                        <td>
                            <?= lang('Số Hợp Đồng/PO', 'code_purchase_order') ?>
                        </td>
                        <td>
                            <input type="text" name="code_purchase_order"  class="form-control" id="code_purchase_order" style="width: 100%;" value="<?= !empty($request_repair) ? $request_repair['code_purchase_order'] : '' ?>">
                        </td>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Ngày ký hợp đồng', 'date_contract') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_contract',
                                (set_value('date_contract') ? set_value('date_contract') : (!empty($request_repair) ? _dC($request_repair['date_contract']) : date('d/m/Y'))),
                                'id="date" class="form-control datepicker" placeholder="' . lang('Ngày ký hợp đồng') . '" required '
                            ) ?>
                        </td>
                        <td><?= lang('Chi Tiết Thực Hiện', 'category_tasks') ?></td>
                        <td colspan="1">
                            <select name="category_tasks" id="category_tasks" class="category_tasks select2" data-placeholder="<?= lang('Chi tiết thực hiện') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($category_tasks)) { ?>
                                    <?php foreach ($category_tasks as $key => $value) { ?>
                                        <option <?= !empty($request_repair) ? (!empty($request_repair['category_tasks']) && $request_repair['category_tasks'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['code'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Đơn vị sửa chữa', 'unit_repair') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text" name="unit_repair" id="unit_repair" class="form-control" data-placeholder="Đơn vị sửa chữa" value="<?= !empty($request_repair) ? $request_repair['unit_repair'] : '' ?>">
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Đơn giá sữa chữa', 'price') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text" name="price" id="price" class="form-control" data-type="currency" data-placeholder="Đơn giá sửa chữa" value="<?= !empty($request_repair) ? number_format_data($request_repair['price']) : '' ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Số lượng', 'quantity') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text" name="quantity" id="quantity" class="form-control" data-type="currency" data-placeholder="Số lượng" value="<?= !empty($request_repair) ? number_format_data($request_repair['quantity']) : '' ?>">
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Thành tiền', 'grand_total') ?>
                        </td>
                        <td style="width: 35%;">
                            <div id="grand_total"><?= !empty($request_repair) ? number_format_data($request_repair['amount']) : '' ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Ngày bắt đầu', 'date_performing') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_performing',
                                (set_value('date_performing') ? set_value('date_performing') : (!empty($request_repair) ? _dt($request_repair['date_performing']) : date('d/m/Y'))),
                                'id="date_performing" class="form-control datepicker" placeholder="' . lang('Ngày bắt đầu') . '" required '
                            ) ?>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Ngày hoàn thành dự kiến', 'date_expected') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_expected',
                                (set_value('date_expected') ? set_value('date_expected') : (!empty($request_repair) ? _dt($request_repair['date_expected']) : date('d/m/Y'))),
                                'id="date_expected" class="form-control datepicker" placeholder="' . lang('Ngày hoàn thành dự kiến') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Ngày hoàn thành thực tế', 'date_success') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_success',
                                (set_value('date_success') ? set_value('date_success') : (!empty($request_repair) ? _dt($request_repair['date_success']) : date('d/m/Y'))),
                                'id="date_success" class="form-control datepicker" placeholder="' . lang('Ngày hoàn thành thực tế') . '" '
                            ) ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    $('#modal_step_three_two').modal('show');
    init_datepicker();
    init_selectpicker('refresh');
    // ajaxSelectParams('#machines_id', 'admin/request_repair/searchMachines', $("#machines_id").val(), true, true);
    ajaxSelectParams('#supplier_id', 'admin/orders/searchSuppliers', $("#supplier_id").val(), {type: 0}, true);
    // ajaxSelectParams('#production_report_id', 'admin/request_repair/searchProductionReport', $("#production_report_id").val(), {type: 0}, true);

    $("select.select2").select2();
    appValidateForm($('#request_repair'), {
        supplier_id: 'required',
        code_purchase_order: 'required',
        date_contract: 'required',
        category_tasks: 'required',
        unit_repair: 'required',
        price: 'required',
        quantity: 'required',
        date_performing: 'required',
        date_expected: 'required',
        date_success: 'required',
    }, detail);



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

                if(data.id_tasks) {
                    init_task_modal(data.id_tasks);
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
    function total() {
        quantity = intVal($('.quantity').val());
        price = intVal($('.price').val());
        total_amount = quantity * price;
        $('.total_amount').html(tnhFormatMoney(total_amount));
    }

    // $('#supplier_id').change(function() {
    //     var supplier_id = $(this).val();
    //     $.get(admin_url + 'request_repair/get_purchases', {supplier_id : supplier_id}, function(result) {
    //         result = JSON.parse(result);
    //         $('#id_purchase_order').html('<option></option>');
    //         $.each(result.data, function(index, value) {
    //             $('#id_purchase_order').append(`<option value="${value.id}">${value.fullcode}</option>`);
    //         })
    //         $('#id_purchase_order').select2();
    //     })
    // })

    $('#price_repair, #quantity').change(function() {
        var quantity = intVal($('#quantity').val());
        var price_repair = intVal($('#price_repair').val());
        var grand_total = quantity * price_repair;
        $('#grand_total').text(formatCurrency(grand_total));
    })
    // Format tiền tệ khi nhập liệu
    $('input[data-type="currency"]').on({
        keyup: function() {
            formatCurrency($(this));
        },
        blur: function() {
            formatCurrency($(this), "blur");
        }
    });
</script>