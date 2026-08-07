<style>
    #tb-item-purchases th {
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }
</style>
<?php echo form_open('admin/manufactures_temp/keep_stock_products/' . $id, array('id' => 'keep_stock_material')); ?>
<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Giữ kho TP (Trên truyền)'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y')), 'placeholder="' . lang('date') . '" id="date" required class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('ch_note_t', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="' . lang('ch_note_t') . '" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Chi nhánh', 'branch_id') ?>
                        <?php
                        $branchs = getListBranch();
                        ?>
                        <select name="branch_id" id="branch_id" class="branch_id" required="required" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($branchs)) { ?>
                                <?php foreach ($branchs as $key => $value) { ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 hide">
                    <div class="text-right">
                        <a class="btn btn-success btn-xs" href="javascript:void(0)" onclick="loadAllKeepStockMaterial(this)"><?= lang('tnh_load_all_lack') ?></a>
                        <a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="removeAllKeepStockMaterial(this)"><?= lang('tnh_delete_all') ?></a>
                    </div>
                </div>
                <div class="col-md-12">
                    <table id="tb-item-purchases" class="dt-tnh table dont-responsive-table">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th class="text-center" style="width: 150px;"><?= lang('Mã TP') ?></th>
                            <th class="text-center" style="width: 150px;"><?= lang('Tên TP') ?></th>
                            <th class="text-center" style="width: 100px;" class="text-center"><?= lang('Số lượng') ?></th>
                            <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_hold') ?></th>
                            <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_need_keep') ?></th>
                            <th class="text-center" style="width: 450px;" class="text-center"><?= lang('Chi tiết KHKD') ?></th>
                            <th class="text-center" style="width: 70px;" class="text-center"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody class="body_data">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var dtItemPurchases = '';
    var counter = 0;
    var arr_id = [];
    var arrNew = [];
    var arr_info_new = [];
    var c_productions_plan_id = '<?= $id ?>';

    function totalPurchases() {
        tb = '#tb-item-purchases tbody tr.tr-parent';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        arr_id = [];
        arr_info = [];
        $('.show-errors').html('');
        $('tr').removeClass('bg-warning');


        arrTotal = [];
        arrCheck = [];
        if (arr_info_new.length > 0){
            $.each(arr_info_new,function (k,v){
                object = {"key":v.key,"qty" :0}
                arrTotal.push(object);
                arrCheck.push(v.key);
            });
        }

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
                        cTrSub.find('.show-errors').html('Đã vượt qua số lượng KHKD');
                        $(cTrSub).addClass('bg-warning');
                        count_errors++;
                    }

                    business_plan_item_id = cTrSub.find('.business_plan_item_id').val();
                    id_business_plan = cTrSub.find('.id_business_plan').val();
                    check_exists_new = `${business_plan_item_id}__${id_business_plan}`;
                    quantity_check = intVal($(element).find(`.quantity_${check_exists_new}`).val());

                    index = jQuery.inArray(check_exists_new, arrCheck);
                    if (index !== -1)
                    {
                        arrTotal[index].qty = parseFloat(arrTotal[index].qty) + parseFloat(quantity_check);
                    } else {
                        arrCheck.push(check_exists_new);
                        object = {"key":check_exists_new,"qty" :0}
                        arrTotal.push(object);
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

        arrToTalNew = [];
        arrTotal.forEach(function (v,k){
            object = {"qty": v.qty};
            arrToTalNew[v.key] = (object);
        });
        if (arr_info_new.length > 0){
            $.each(arr_info_new,function (k,v){
                quantity_check = v.quantity;
                key_check = v.key;
                if (arrToTalNew[key_check].qty != undefined) {
                    if (arrToTalNew[key_check].qty > quantity_check) {
                        $(`.text_error_child_${key_check}`).html('Số lương tổng chi tiết phải nhỏ hơn hoặc bằng ' + tnhFormatNumber(quantity_check));
                        count_errors ++;
                    } else {
                        $(`.text_error_child_${key_check}`).html('');
                    }
                } else {
                    $(`.text_error_child_${key_check}`).html('');
                }
            });
        }

    }


    function chonseItem(el, idEl) {
        trCurItem = $(el).closest('tr');
        dataItem = $('#' + idEl).select2("data");
        temp_counter = trCurItem.find('.counter').val();
        if (dataItem) {
            $.ajax({
                type: "POST",
                url: site.base_url+'admin/manufactures/getWarehousesLocationPlanNew',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    item_id: dataItem.item_id,
                    item_type: dataItem.item_type
                },
                dataType: "json",
                success: function(response) {
                    itemId = dataItem.id
                    itemType = itemId.split('__')[0];
                    trCurItem.find('.td-type-item').html(lang_core[dataItem.item_type_root]);

                    // if (jQuery.inArray(itemId, arr_id) !== -1) {
                    //     alert('Mặt hàng này đã được chọn vui lòng không chọn lại');
                    //     dtItemPurchases.row(trCurItem).remove().draw();
                    //     return;
                    // }

                    quantity_primary = intVal(dataItem.quantity_primary/dataItem.exchange_standard_unit * dataItem.exchange_unit) - intVal(dataItem.quantity_net);
                    quantity_primary = tnhToFixedNumber(quantity_primary, 0);
                    if (quantity_primary < 0) quantity_primary = 0;
                    trCurItem.find('input.quantity').val(tnhFormatNumber(quantity_primary));
                    trCurItem.find('.td-quantity-hold').html(tnhFormatNumber(dataItem.quantity_net));
                    trCurItem.find('.td-need-hold').html(tnhFormatNumber(quantity_primary));

                    cQuantityNeedHold = quantity_primary;
                    htmlQuantityWarehouse = '';
                    dataItem.arrWarehouses = response.arrWarehouses;
                    if (typeof dataItem.arrWarehouses !== 'undefined' && dataItem.arrWarehouses.length > 0)
                    {
                        $.each(dataItem.arrWarehouses, function(index, el) {
                            isChecked = '';
                            quantityW = 0;
                            tempQuantity = cQuantityNeedHold;
                            if (cQuantityNeedHold > 0) {
                                cQuantityNeedHold = cQuantityNeedHold - el.quantity_warehouse;
                                if (cQuantityNeedHold > 0) {
                                    quantityW = (el.quantity_warehouse);
                                } else {
                                    quantityW = (tempQuantity);
                                }
                            }

                            if (quantityW > 0) {
                                isChecked = 'checked';
                            }

                            tdTick = '<div class="checkbox checkbox-info" style="margin-bottom: 0;">'+
                                '<input type="checkbox" onChange="totalMovingCoordinator()" class="tick" name="tick['+temp_counter+']['+index+']" value="'+el.id+'" '+isChecked+' id="tick-'+temp_counter+'-'+index+'">'+
                                '<label for="tick-'+temp_counter+'-'+index+'"></label>'+
                                '</div>';

                            tdWarehouseNoti = `<div>
                                <div><span class="bold">Kho hàng:</span> ${el.name_warehouse}</div>
                                <div><span class="bold">Vị trí:</span> ${el.name_location}</div>
                                <div><span class="bold">Lot:</span> ${(el.lot_code && el.lot_code != null) ? el.lot_code : ''}</div>
                                <div><span class="bold">Ngày SX:</span> ${(el.date_sx && el.date_sx != null) ? fsd(el.date_sx) : ''}</div>
                                <div><span class="bold">Ngày SD:</span> ${(el.date_sd && el.date_sd != null) ? fsd(el.date_sd) : ''}</div>
                                <div class="text-primary"><span class="bold">Số lượng:</span> ${(el.quantity_warehouse && el.quantity_warehouse != null) ? tnhFormatNumber(el.quantity_warehouse, 0) : ''}</div>
                            </div>`;

                            tdQuantityCoordinator = '<input type="text" quantity-warehouse="" name="quantity_coordinator['+temp_counter+']['+index+']" class="form-control quantity-coordinator" style="width: 100px;" value="'+quantityW+'">';

                            htmlQuantityWarehouse+= `<tr class="not-tr">
                                <td style="width: 50px;" class="text-center">${tdTick}</td>
                                <td style="width: 350px;">${tdWarehouseNoti}</td>
                                <td style="width: 100px;">${tdQuantityCoordinator}</td>
                            </tr>`;
                        });

                        htmlQuantityWarehouse = `<table class="tnh-table table-warehouse" style="margin: 0;">${htmlQuantityWarehouse}</table>`;
                    } else {
                        htmlQuantityWarehouse = '<span class="label label-danger border-radius-10px"><?= lang('tnh_out_of_stock') ?></span>';
                    }
                    trCurItem.find('.td-quantity').html(htmlQuantityWarehouse);
                    trCurItem.find('.td-unit').html(dataItem.unit_name_stock);
                    // trCurItem.find('select.warehouses_id').html(response.option);
                    // trCurItem.find('select.warehouses_id').val(0).trigger('change');

                    if (response.isWarehouses) {
                        trCurItem.find('.show-errors-warehouses').html('');
                        trCurItem.find('.hide-show').removeClass('hide');
                    }

                    lastrow = $('#tb-item-purchases tbody tr')[$('#tb-item-purchases tbody tr').length - 1];
                    if ($(lastrow).find('input.items_id').select2('val')) {
                        $('.add-row').click();
                    }
                }
            });
        } else {

        }
    }

    function changeWarehouses(_this) {
        trCurrent = $(_this).closest('tr');
        data = $(_this).select2().find(":selected");
        quantity_warehouse = 0;
        if (data) {
            quantity_warehouse = data.data("quantity");
        }
        trCurrent.find('.td-quantity-warehouses').html(tnhFormatNumber(quantity_warehouse));
        totalPurchases();
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        totalPurchases();
    }

    function removeAllKeepStockMaterial(_this) {
        dtItemPurchases.rows().remove().draw();
        totalPurchases();
    }

    function loadAllKeepStockMaterial() {
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufactures_temp/loadAllKeepStockProducts',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                productions_plan_id: c_productions_plan_id,
                item_id: '<?= $id ?>',
            },
            dataType: "json",
            success: function (response) {
                if (response) {
                    $('#tb-item-purchases tbody').html('');
                    if (response.dtOrder.length > 0) {
                        $.each(response.dtOrder, function (index, item) {
                            createRow(item);
                        });
                        $(".add").removeClass('hide');
                    } else {
                        $('#tb-item-purchases tbody').html(`<tr>
                            <td colspan="8"><div style="color: green;text-align: center">Không có hàng giữ hoặc đã giữ hết hàng !</div></td>
                        </tr>`);
                        $(".add").addClass('hide');
                    }
                }
            }
        });
    }

    function createRow(dataItem = false) {

        dtItems_id = '';
        reference_no = '';
        if (dataItem) {
            dtItems_id = dataItem.id;
            reference_no = dataItem.reference_no;

            html_reference_no = `<div style="color: green">${reference_no}</div>`;

            tdNumber = '<td><div class="stt text-center"></div></td>';
            tdCode = `<td><div class="text-left td-code">${dataItem.code}${html_reference_no}
                <input type="hidden" name="counter[]" class="counter" value="${counter}">
                <input type="hidden" name="order_item_id[${counter}]" class="order_item_id" value="${dataItem.order_item_id}">
                <input type="hidden" name="item_id[${counter}]" class="item_id" value="${dataItem.item_id}">
                <input type="hidden" name="plan_id_item[${counter}]" class="plan_id_item" value="${dataItem.plan_id_item}">
                <input type="hidden" name="plan_id[${counter}]" class="plan_id" value="${dataItem.plan_id}">
                <input type="hidden" name="order_id[${counter}]" class="order_id" value="${dataItem.order_id}">
            </div></td>`;
            tdName = `<td class="td-name">${dataItem.name}</td>`;
            tdQuantity = `<td class="td-quantity text-center">${tnhFormatNumber(dataItem.quantity)}
                <input type="hidden" name="quantity[${counter}]" class="quantity" value="${dataItem.quantity}">
            </td>`;
            tdQuantityHold = `<td class="td-quantity-hold text-center">${tnhFormatNumber(dataItem.quantity_hold)}
            </td>`;
            tdQuantityNeedHold = `<td class="td-quantity-need-hold quantity-need-hold text-center">${tnhFormatNumber(dataItem.quantity - dataItem.quantity_hold)}
            </td>`;
            cQuantityNeedHold = dataItem.quantity - dataItem.quantity_hold;
            htmlWarehouseNew = '';
            if (dataItem.warehouse.length > 0) {
                $.each(dataItem.warehouse, function (index, el) {
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

                    check_exists = `${el.business_plan_item_id}__${el.id_business_plan}`;
                    index_check = jQuery.inArray(check_exists, arrNew);
                    if (index_check !== -1) {} else {
                        arrNew.push(check_exists);
                        object = {"key":check_exists,"quantity": el.product_quantity};
                        arr_info_new.push(object);
                    }

                    tdTick = `<div class="checkbox checkbox-info" style="margin-bottom: 0;">
                    <input type="checkbox" ${isChecked} onChange="totalPurchases()" class="tick" name="tick[${counter}][${index}]"
                    value="${el.id_business_plan}__${el.business_plan_item_id}" id="tick-${counter}-${index}">
                    <label for="tick-${counter}-${index}"></label>
                    </div>`;

                    tdWarehouseNew = `<div>${el.reference_no_production != null ? el.reference_no_production : ''} - ${el.reference_no} - ${el.items_name} - ${tnhFormatNumber(el.product_quantity)}
                    <div class="show-errors text-danger"></div>`;
                    tdQuantityCoordinator = `
                    <input type="hidden" class="id_business_plan" name="id_business_plan[${counter}][${index}]" value="${el.id_business_plan}">
                    <input type="hidden" class="business_plan_item_id" name="business_plan_item_id[${counter}][${index}]" value="${el.business_plan_item_id}">
                    <input type="text"  quantity-warehouse="${el.product_quantity}"
                    onChange="totalPurchases()" name="quantity_coordinator[${counter}][${index}]" class="form-control quantity_${check_exists} quantity-coordinator" value="${tnhFormatNumber(quantityW)}">
                    <div class="text_error_child_${check_exists} text-danger"></div>
                    `;
                    htmlWarehouseNew += `<tr class="not-tr">
                    <td style="width: 50px;" class="text-center">${tdTick}</td>
                    <td style="width: 350px;">${tdWarehouseNew}</td>
                    <td style="width: 100px;">${tdQuantityCoordinator}</td>
                    </tr>`;
                });
            } else {
                htmlWarehouseNew = '<div class="label label-danger" style="border-radius:15px;padding:5px 10px;">Không có KHKD</div>';
            }
            tdDetail = `<td class="td-detail"><table class="tnh-table table-warehouse" style="margin: 0;">${htmlWarehouseNew}</table></td>`;
            tdActions = '<td><div class="text-center"><i onclick="removeRow(this)" class="fa fa-remove btn btn-danger remove-row"></i></div></td>';
            trHtml = `<tr class="tr-parent">
                ${tdNumber}
                ${tdCode}
                ${tdName}
                ${tdQuantity}
                ${tdQuantityHold}
                ${tdQuantityNeedHold}
                ${tdDetail}
                ${tdActions}
            </tr>`;
            $('#tb-item-purchases tbody.body_data').append(trHtml);
            counter ++;
            totalPurchases();
        }
    }

    $(function() {
        $("#branch_id").select2();
        init_datepicker();

        loadAllKeepStockMaterial();

        $('.add-row').on('click', function(event) {
            event.preventDefault();
            createRow();
        });

        $(document).ready(function() {
            $('.add-row').click();
        });

        appValidateForm($('#keep_stock_material'), {
            'date': 'required',
            'branch_id': 'required',
        }, convert);

        function convert(form) {
            if (count_errors > 0) {
                alert_float('danger', '<?= lang('Kiểm tra lại số lượng giữ') ?>');
                return;
            }
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message, 10000);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>