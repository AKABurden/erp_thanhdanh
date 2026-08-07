<?php echo form_open('admin/orders/add_hold_orders/' . $id.'/'.$add_more, array('id' => 'handling-keep-stock')); ?>
<style>
    .table-keep tr td {
        padding: 5px;
    }

    .bg-warning {
        background: #f9dac3 !important;
    }

    #tb-keep > thead > tr > th {
        border-top: 1px solid #93b4d6 !important;
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $add_more ? lang('Giữ kho thêm') : lang('Giữ kho') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <b><?= lang('Đơn hàng') ?>:</b>
                    <span><?= $order['reference_no'] ?></span>
                </div>
                <div class="col-md-12">
                    <b><?= lang('note') ?>:</b>
                    <textarea name="note" id="note" placeholder="<?= lang('note') ?>" class="form-control note" rows="3"></textarea>
                </div>
                <?php if (!empty($items)) { ?>
                    <div class="col-md-12">
                        <table class="table" id="tb-keep">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                <th class="text-center"><?= lang('items') ?></th>
                                <th class="text-center"><?= lang('unit') ?></th>
                                <th class="text-center"><?= lang('quantity') ?></th>
                                <th class="text-center"><?= lang('Số lượng đã giao') ?></th>
                                <th class="text-center"><?= lang('tnh_quantity_manufactures') ?></th>
                                <th class="text-center"><?= lang('Số lượng đã giữ') ?></th>
                                <th class="text-center"><?= lang('Số lượng cần giữ') ?></th>
                                <th class="text-center"><?= lang('Kho hàng') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('actions') ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="col-md-12">
                        <div class="text-center"
                             style="padding: 5px;border: 1px solid red;border-radius: 25px;margin-top: 10px;color: red;font-style: italic;font-size: 20px">
                            Đã giữ đủ hàng
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <?php if (!empty($items)) { ?>
                <button type="submit" class="btn btn-primary add-keep-stock"><?= _l('save') ?></button>
            <?php } ?>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var counter = 0;
    var count_erroes = 0;
    var order_id = <?= $id ?>;
    var items = <?= (isset($items)) ? json_encode($items) : [] ?>;
    var arr_id = [];

    function totalKeepSupplies() {
        tb = '#tb-keep tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        arr_id = [];
        arr_info = [];
        $('.show-errors').html('');
        $('tr').removeClass('bg-warning');
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            quantityNeedHold = intVal($(element).find('.quantity-need-hold').html());

            arrTick = $(element).find('.tick');
            totalQuantityCorrdinator = 0;
            $.each(arrTick, function (index, el) {
                checked = $(el).prop('checked');
                if (checked) {
                    cTrSub = $(el).closest('tr');
                    cQuantityCoordinator = intVal(cTrSub.find('.quantity-coordinator').val());
                    cQuantityWarehouse = intVal(cTrSub.find('.quantity-coordinator').attr('quantity-warehouse'));

                    totalQuantityCorrdinator += cQuantityCoordinator;
                    if (cQuantityCoordinator > cQuantityWarehouse) {
                        cTrSub.find('.show-errors').html('Đã vượt qua số lượng trong kho');
                        $(cTrSub).addClass('bg-warning');
                        count_errors++;
                    }
                }
            });

            if (totalQuantityCorrdinator > quantityNeedHold) {
                $(element).addClass('bg-danger');
                count_errors++;
            } else {
                $(element).removeClass('bg-danger');
            }
        }
    }

    function removeKeep(c_order_item_id, _this) {
        $('tr[data-order_item_id="' + c_order_item_id + '"]').closest('tr').remove();
        totalKeepSupplies();
    }

    function addRowItem(data, counter, _this = null) {
        trCurItem = $(_this).closest('tr');
        item_id = data.item_id;
        images = data.images;
        code_item = data.code_item;
        name_item = data.name_item;
        type_item = data.type_item;
        quantity = data.quantity;
        quantity_hold = data.quantity_condition;
        quantity_delivery = data.quantity_delivery;
        order_item_id = data.order_item_id;
        order_id = data.order_id;
        images = images ? site.base_url + images : site.base_url + 'assets/images/tnh/no_image.png';
        console.log(data);

        if (jQuery.inArray(order_item_id, arr_id) !== -1) {
            alert_float('danger', 'Thành phẩm này đã được thêm');
            return;
        }

        tdNumber = `<div class="stt text-center"></div>`;
        tdCode = `<div class="td-code mbot10">
            <input type="hidden" name="counter[${counter}]" class="form-control counter" value="${counter}">
            <input type="hidden" name="order_item_id[${counter}]" id="order_item_id${counter}" class="order_item_id" style="width: 100%;" value="${order_item_id}">
            <input type="hidden" name="order_id[${counter}]" id="order_id${counter}" class="order_id" style="width: 100%;" value="${order_id}">
            <input type="hidden" name="item_id[${counter}]" id="item_id${counter}" class="item_id" style="width: 100%;" value="${item_id}">
            <input type="hidden" name="type_item[${counter}]" id="type_item${counter}" class="type_item" style="width: 100%;" value="${type_item}">
            <div class="bold">${name_item} (${code_item})</div>
        </div>`;

        tdImage = `<div class="td-image">
            <div class="preview_image" style="width: auto;">
            <div class="display-block contract-attachment-wrapper img">
            <div style="width:45px; margin: auto;">
            <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
            <div class="">
            <img src="${images}" style="border-radius: 50%">
            </div>
            </a>
            </div>
            </div>
            </div>
            </div>`;

        tdUnit = `<div class="text-center td-unit">${data.dt_unit.unit}</div>`;

        tdQuantity = `<div class="td-quantity text-center">${tnhFormatNumber(quantity)}</div>`;
        tdQuantityDelivery = `<div class="td-quantity text-center">${tnhFormatNumber(quantity_delivery)}</div>`;

        // quantity_manufactures = (quantity - quantity_delivery) * data.conversion_quantity_unit;
        quantity_manufactures = (quantity ) * data.conversion_quantity_unit - quantity_delivery;

        tdManufactures = `<div class="td-manufactures text-center">${tnhFormatNumber(quantity_manufactures)}</div>`;
        tdQuantityOldHold = `<div class="td-quantity text-center">${tnhFormatNumber(quantity_hold)}</div>`;
        tdQuantityHold =
            `<div class="td-quantity text-center quantity-need-hold">${tnhFormatNumber(quantity_manufactures - quantity_hold)}</div>`;

        // cQuantityNeedHold = quantity - quantity_delivery - quantity_hold;

        cQuantityNeedHold = quantity_manufactures - quantity_hold;
        htmlWarehouseNew = '';
        if (data.warehouse.length > 0) {
            $.each(data.warehouse, function (index, el) {
                // el.product_quantity = el.product_quantity / intVal(data.conversion_quantity_unit);
                // el.product_quantity = tnhToFixedNumber(el.product_quantity);

                isChecked = '';
                quantityW = 0;
                tempQuantity = cQuantityNeedHold;
                if (cQuantityNeedHold > 0) {
                    cQuantityNeedHold = cQuantityNeedHold - el.product_quantity;
                    if (cQuantityNeedHold > 0) {
                        quantityW = el.product_quantity;
                    } else {
                        quantityW = tempQuantity;
                    }
                }

                if (quantityW > 0) {
                    isChecked = 'checked';
                }

                tdTick = `<div class="checkbox checkbox-info" style="margin-bottom: 0;">
                    <input type="checkbox" ${isChecked} onChange="totalKeepSupplies()" class="tick" name="tick[${counter}][${index}]"
                    value="${el.warehouse_id}__${el.localtion}" id="tick-${counter}-${index}">
                    <label for="tick-${counter}-${index}"></label>
                    </div>`;

                date_sx = el.date_sx != null ? el.date_sx : '';
                date_sd = el.date_sd != null ? el.date_sd : '';
                tdWarehouseNew = `<div>${el.name_warehouse} - ${el.name_local}
                    - <b class="text-primary">${el.product_quantity}
                    </b></div>${el.bussiness ? '('+el.bussiness+')' : ''}<div>
                       <p>Lot: ${el.lot_code}</p>
                       <p>Ngày SX: ${date_sx}</p>
                       <p>Ngày SD: ${date_sd}</p>
                    </div><div class="show-errors text-danger"></div>`;
                tdQuantityCoordinator = `<input type="text"  quantity-warehouse="${el.product_quantity}"
                onChange="totalKeepSupplies()" name="quantity_coordinator[${counter}][${index}]" class="form-control quantity-coordinator" value="${tnhFormatNumber(quantityW)}">
                <input type="hidden" name="lot_code[${counter}][${index}]" class="form-control" value="${el.lot_code}">
                <input type="hidden" name="date_sx[${counter}][${index}]" class="form-control" value="${el.date_sx}">
                <input type="hidden" name="date_sd[${counter}][${index}]" class="form-control" value="${el.date_sd}">
                <input type="hidden" name="date_use[${counter}][${index}]" class="form-control" value="${el.date_use}">
                `;

                htmlWarehouseNew += `<tr class="not-tr">
                    <td style="width: 50px;" class="text-center">${tdTick}</td>
                    <td style="width: 350px;">${tdWarehouseNew}</td>
                    <td style="width: 100px;">${tdQuantityCoordinator}</td>
                    </tr>`;
            });
        } else {
            htmlWarehouseNew = '<div class="label label-danger" style="border-radius:15px;padding:5px 10px;">Tồn kho hết</div>';
        }
        tdWarehouses = `<table class="tnh-table table-warehouse" style="margin: 0;">${htmlWarehouseNew}</table>`;

        tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';


        cTR = `<tr>
            <td>${tdNumber}</td>
            <td>${tdCode}</td>
            <td>${tdUnit}</td>
            <td>${tdQuantity}</td>
            <td>${tdQuantityDelivery}</td>
            <td>${tdManufactures}</td>
            <td>${tdQuantityOldHold}</td>
            <td>${tdQuantityHold}</td>
            <td class="td-warehouse" style="text-align:center">${tdWarehouses}</td>
            <td>${tdActions}</td>
        </tr>`;
        return cTR;
    }

    $(document).on('click', '.remove-row', function (event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        order_id_item = tr.find('input.order_item_id').val();
        index = jQuery.inArray(order_id_item, arr_id);
        if (index !== -1) {
            arr_id.splice(index, 1);
        }
        counter_index = tr.find('.counter').val();
        tr.remove();
        totalKeepSupplies();
    });

    $(function () {
        if (items.length > 0) {
            htmlItem = '';
            $.each(items, function (k, v) {
                trItem = addRowItem(v, counter);
                htmlItem += trItem;
                counter++;
            })
            $('#tb-keep tbody').append(htmlItem);
            totalKeepSupplies();
        }
        init_selectpicker();
        appValidateForm($('#handling-keep-stock'), {}, handlingKeepStock);

        function handlingKeepStock(form) {
            if (count_errors > 0) {
                alert_float('danger', 'Dữ liệu nhập không hợp lệ');
                return;
            }
            $('.add-keep-stock').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw(false);
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add-keep-stock').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    $('.add-keep-stock').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }

        $('.status').selectpicker();
    })
</script>