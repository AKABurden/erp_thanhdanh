<div id="modal_step_four" class="modal fade" role="dialog">
    <style>
        .stars {
            display: flex;
            flex-direction: row-reverse; /* Đảo ngược để xử lý logic hover/checked */
            justify-content: flex-end;
        }

        .stars input {
            display: none; /* Ẩn các nút radio mặc định */
        }

        .stars label {
            font-size: 25px;
            color: #ccc; /* Màu sao trống */
            cursor: pointer;
            transition: color 0.2s;
        }

        /* Khi di chuột qua hoặc đã chọn: đổi màu sao và các sao đứng trước nó */
        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: #ffca08; /* Màu vàng cho sao đã chọn */
        }
    </style>
    <div class="modal-dialog modal-lg" style="min-width: 60%;">
        <?php echo form_open(
            admin_url('request_repair/detail_append/' . $id_request_repair),
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
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td><?= lang('Người nghiệm thu', 'staff_acceptance') ?></td>
                        <td colspan="1">
                            <select name="staff_acceptance" id="staff_acceptance" class="staff_acceptance select2" data-placeholder="<?= lang('Người nghiệm thu') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($request_repair) ? ($request_repair['employees'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Ngày nghiệm thu', 'date_acceptance') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date_acceptance',
                                (set_value('date_acceptance') ? set_value('date_acceptance') : (!empty($request_repair) ? _dt($request_repair['date_acceptance']) : date('d/m/Y H:i'))),
                                'id="date_acceptance" class="form-control datetimepicker" placeholder="' . lang('Ngày nghiệm thu') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Kết quả nghiệm thu', 'result_acceptance') ?></td>
                        <td colspan="1">
                            <select name="result_acceptance" id="result_acceptance" class="result_acceptance select2" data-placeholder="<?= lang('Kết quả nghiệm thu') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php foreach ($this->list_result_acceptance as $key => $value) { ?>
                                    <option <?= !empty($request_repair) ? (!empty($request_repair['result_acceptance']) && $request_repair['result_acceptance'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Đánh giá chất lượng', 'star') ?>
                        </td>
                        <td>
                            <div class="rating-container">
                                <div class="stars">
                                    <input type="radio" id="star5" name="star" value="5" <?= !empty($request_repair['star']) && $request_repair['star'] == 5 ? 'checked' : '' ?> /><label for="star5" title="5 sao">★</label>
                                    <input type="radio" id="star4" name="star" value="4" <?= !empty($request_repair['star']) && $request_repair['star'] == 4 ? 'checked' : '' ?>  /><label for="star4" title="4 sao">★</label>
                                    <input type="radio" id="star3" name="star" value="3" <?= !empty($request_repair['star']) && $request_repair['star'] == 3 ? 'checked' : '' ?>  /><label for="star3" title="3 sao">★</label>
                                    <input type="radio" id="star2" name="star" value="2" <?= !empty($request_repair['star']) && $request_repair['star'] == 2 ? 'checked' : '' ?>  /><label for="star2" title="2 sao">★</label>
                                    <input type="radio" id="star1" name="star" value="1" <?= !empty($request_repair['star']) && $request_repair['star'] == 1 ? 'checked' : '' ?>  /><label for="star1" title="1 sao">★</label>
                                </div>
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Đánh giá đơn vị sửa chữa', 'star_unit_repair') ?>
                        </td>
                        <td>
                            <textarea name="star_unit_repair" id="star_unit_repair" class="form-control" data-placeholder="Đánh giá đơn vị sửa chữa"><?= !empty($request_repair) ? $request_repair['star_unit_repair'] : '' ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Hoàn thành thanh toán', 'payment') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="text-right checkbox checkbox-info">
                                <input type="checkbox" name="payment" id="payment" class="checkbox" value="1" <?= !empty($request_repair['payment']) ? 'checked': '' ?>>
                                <label for="payment"></label>
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Biên bản nghiệm thu', 'file_acceptance') ?>
                        </td>
                        <td>
                            <input type="file"  name="file_acceptance" id="file_acceptance" class="form-control" data-placeholder="Biên bản nghiệm thu"><?= !empty($request_repair['file_acceptance']) ? $request_repair['file_acceptance'] : '' ?></input>
                        </td>
                    </tr>

                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Mã ngân sách', 'costs') ?>
                        </td>
                        <td style="width: 35%;">
                            <select name="costs" id="costs" class="result_acceptance select2" data-placeholder="<?= lang('Mã ngân sách') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($costs)) { ?>
                                    <?php foreach ($costs as $key => $value) { ?>
                                        <option <?= !empty($request_repair) ? (!empty($request_repair['costs']) && $request_repair['costs'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Chủ sở hữu ngân sách', 'employees_costs') ?>
                        </td>
                        <td style="width: 35%;">
                            <select name="employees_costs" id="employees_costs" class="employees_costs select2" data-placeholder="<?= lang('Chủ sở hữu ngân sách') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($employees)) { ?>
                                    <?php foreach ($employees as $key => $value) { ?>
                                        <option <?= !empty($request_repair) ? ($request_repair['employees'] == $value['staffid'] ? 'selected' : '') : ($value['staffid'] == get_staff_user_id() ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
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
</div>
<script type="text/javascript">
    $('#modal_step_four').modal('show');
    init_datepicker();
    init_selectpicker('refresh');
    // ajaxSelectParams('#machines_id', 'admin/request_repair/searchMachines', $("#machines_id").val(), true, true);
    // ajaxSelectParams('#supplier_id', 'admin/orders/searchSuppliers', $("#supplier_id").val(), {type: 0}, true);
    // ajaxSelectParams('#production_report_id', 'admin/request_repair/searchProductionReport', $("#production_report_id").val(), {type: 0}, true);

    $("select.select2").select2();
    appValidateForm($('#request_repair'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        result_id: 'required',
        category_maintenance: 'required',
        employees: 'required',
        machines_id: 'required',
        supplier_id: 'required',
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