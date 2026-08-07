<?php echo form_open('admin/manufactures/keep_stock_material/' . $id, array('id' => 'keep_stock_material')); ?>
<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_keep_stock_material'); ?></h4>
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
                <div class="col-md-12 hide">
                    <div class="text-right">
                        <a class="btn btn-success btn-xs" href="javascript:void(0)" onclick="loadAllKeepStockMaterial(this)"><?= lang('tnh_load_all_lack') ?></a>
                        <a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="removeAllKeepStockMaterial(this)"><?= lang('tnh_delete_all') ?></a>
                    </div>
                </div>
                <div class="col-md-12">
                    <table id="tb-item-purchases" class="dt-tnh table table-bordered table-hover dont-responsive-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;"><a class="btn btn-info btn-icon add-row hide"><i class="fa fa-plus"></i></a></th>
                                <th class="text-center" style="width: 200px;"><?= lang('tnh_items') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('type') ?></th>
                                <th class="text-center" style="width: 150px;" class="text-center"><?= lang('tnh_standard_unit') ?></th>
                                <th class="text-center" style="width: 150px;" class="text-center"><?= lang('tnh_quantity_hold') ?></th>
                                <th class="text-center" style="width: 150px;" class="text-center"><?= lang('tnh_quantity_need_keep') ?></th>
                                <th class="text-center" style="width: 450px;" class="text-center"><?= lang('tnh_quantity_w') ?></th>
                                <th class="text-center" style="width: 70px;" class="text-center"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
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
    var c_productions_plan_id = '<?= $id ?>';

    function totalPurchases() {
        tb = '#tb-item-purchases tbody tr';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        arr_id = [];
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            item_current_id = $(element).find('input.items_id').val();
            warehouses_id = $(element).find('select.warehouses_id').val();
            // str_item_id = item_current_id+'__'+warehouses_id;
            str_item_id = item_current_id;
            if (str_item_id) {
                index = jQuery.inArray(str_item_id, arr_id);
                if (index !== -1) {
                    // alert('Mặt hàng và vị trí kho chuyển này đã được chọn vui lòng không chọn lại');
                    alert('Mặt hàng này đã được chọn');
                    dtItemPurchases.row($(element)).remove().draw();
                    continue;
                } else {
                    arr_id.push(str_item_id);
                }
            }

            quantity_warehouse = intVal($(element).find('.td-quantity-warehouses').html());
            quantity = intVal($(element).find('.quantity').val());
            if (quantity > quantity_warehouse && item_current_id) {
                $(element).find('.quantity').val(tnhFormatNumber(quantity_warehouse));
                // $(element).find('.show-errors').html('SL giữ nhỏ hơn '+tnhFormatNumber(quantity_warehouse));
                count_errors++;
            } else {
                $(element).find('.show-errors').html('');
            }
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
        dtItemPurchases.row($(el).parents('tr')).remove().draw();
        totalPurchases();
    }

    function removeAllKeepStockMaterial(_this) {
        dtItemPurchases.rows().remove().draw();
        totalPurchases();
    }

    function loadAllKeepStockMaterial() {
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufactures/loadAllKeepStockMaterial',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                productions_plan_id: c_productions_plan_id,
            },
            dataType: "json",
            success: function (response) {
                if (response) {
                    dtItemPurchases.rows().remove().draw();
                    $.each(response.items, function (index, item) { 
                        createRow(item);
                    });
                }
            }
        });
    }

    function createRow(dataItem = false) {

        dtItems_id = '';
        dtType_item = '';
        dtWarehouses = '';
        dtQuantityHold = '';
        dtQuantity = 1;
        txtJsonItemsId = null;
        idDefault = 0;
        quantityDefault = 0;
        isWarehouses = 0;
        htmlQuantityWarehouse = '';
        hideWarehouses = '';
        txtWarehouses = '';
        unit_name_stock = '';
        conversion_quantity_unit = 1;
        if (dataItem) {
            dtItems_id = dataItem.id;
            dtType_item = lang_core[dataItem.item_type_root];
            quantity_net = intVal(dataItem.quantity_net);
            quantity_primary = intVal(dataItem.quantity_primary);
            exchange_standard_unit = intVal(dataItem.exchange_standard_unit);
            exchange_unit = intVal(dataItem.exchange_unit);
            dtQuantity = (quantity_primary/exchange_standard_unit * exchange_unit) - quantity_net;
            if (dtQuantity < 0) dtQuantity = 0;
            dtQuantity = Math.ceil(dtQuantity);

            // if (dtQuantity == 0) return ''; 
            dtQuantityHold = quantity_net;
            txtJsonItemsId = {'id': dtItems_id, 'text': dataItem.text};
            dtWarehouses = dataItem.warehouses;
            idDefault = dataItem.idDefault;
            quantityDefault = dataItem.quantityDefault;
            isWarehouses = dataItem.isWarehouses;
            cQuantityNeedHold = dtQuantity;
            unit_name_stock = dataItem.unit_name_stock;
            conversion_quantity_unit = dataItem.conversion_quantity_unit;
            if (tnhToFixedNumber(cQuantityNeedHold, 0) <= 0) {
                return '';
            }

            if (typeof dataItem.arrWarehouses !== 'undefined' && dataItem.arrWarehouses.length > 0)
            {
                $.each(dataItem.arrWarehouses, function(index, el) {
                    isChecked = '';
                    quantityW = 0;
                    el.quantity_warehouse = tnhToFixedNumber(el.quantity_warehouse/conversion_quantity_unit);
                    tempQuantity = formatDecimal(cQuantityNeedHold);
                    if (cQuantityNeedHold > 0) {
                        cQuantityNeedHold = cQuantityNeedHold - el.quantity_warehouse;
                        if (cQuantityNeedHold > 0) {
                            quantityW = el.quantity_warehouse;
                        } else {
                            quantityW = tempQuantity;
                        }
                    }

                    if (quantityW > 0) {
                        isChecked = 'checked';
                    }

                    tdTick = '<div class="checkbox checkbox-info" style="margin-bottom: 0;">'+
                        '<input type="checkbox" onChange="totalMovingCoordinator()" class="tick" name="tick['+counter+']['+index+']" value="'+el.id+'" '+isChecked+' id="tick-'+counter+'-'+index+'">'+
                        '<label for="tick-'+counter+'-'+index+'"></label>'+
                    '</div>';

                    tdWarehouseNoti = `<div>
                        <div><span class="bold">Kho hàng:</span> ${el.name_warehouse}</div>
                        <div><span class="bold">Vị trí:</span> ${el.name_location}</div>
                        <div><span class="bold">Lot:</span> ${(el.lot_code && el.lot_code != null) ? el.lot_code : ''}</div>
                        <div><span class="bold">Ngày SX:</span> ${(el.date_sx && el.date_sx != null) ? fsd(el.date_sx) : ''}</div>
                        <div><span class="bold">Ngày SD:</span> ${(el.date_sd && el.date_sd != null) ? fsd(el.date_sd) : ''}</div>
                        <div class="text-primary"><span class="bold">Số lượng:</span> ${(el.quantity_warehouse && el.quantity_warehouse != null) ? tnhFormatNumber(el.quantity_warehouse) : ''}</div>
                    </div>`;

                    tdQuantityCoordinator = '<input type="text" quantity-warehouse="" name="quantity_coordinator['+counter+']['+index+']" class="form-control quantity-coordinator" style="width: 100px;" value="'+tnhFormatNumber(quantityW, 0)+'">';

                    htmlQuantityWarehouse+= `<tr class="not-tr">
                        <td style="width: 50px;" class="text-center">${tdTick}</td>
                        <td style="width: 350px;">${tdWarehouseNoti}</td>
                        <td style="width: 100px;">${tdQuantityCoordinator}</td>
                    </tr>`;
                });
            } else {
                txtWarehouses = '<span class="label label-danger border-radius-10px"><?= lang('tnh_out_of_stock') ?></span>';
                return;
            }
        }

        tdNumber = '<div class="stt text-center"></div>';
        tdCode = '<div class="td-code mbot10" style="pointer-events: none; opacity: 0.9;"><input type="hidden" name="items[' + counter + '][counter]" class="form-control counter" value="' + counter + '">\
            <input type="text" name="items[' + counter + '][items_id]" id="items_' + counter + '" class="items_id modal-select2" style="width: 100%;" onchange="chonseItem(this, \'items_' + counter + '\')" data-placeholder="' + lang_core['choose'] + '" value="'+dtItems_id+'"></div>' +
            '<div class="type-item">' +
            '</div>';
        tdTypeItem = `<div class="td-type-item text-center">${dtType_item}</div>`;
        tdUnit = `<div class="text-center td-unit">${unit_name_stock}</div>`;
        // if (!isWarehouses) {
        //     hideWarehouses = 'hide'
        //     txtWarehouses = '<span class="label label-danger border-radius-10px"><?= lang('tnh_out_of_stock') ?></span>';
        // }
        // tdWarehouses = `<div class="hide-show ${hideWarehouses}">
        //         <select name="items[${counter}][warehouses_id]" onchange="changeWarehouses(this)" data-placeholder="${lang_core['choose']}" class="modal-select2 warehouses_id" id="warehouses_id_${counter}" style="width: 100%;">
        //             <option value=""></option>
        //             ${dtWarehouses}
        //         </select>
        //     </div>
        //     <div class="text-danger show-errors-warehouses text-center">${txtWarehouses}</div>
        // `;
        
        tdQuantityWarehouses = '<div class="td-quantity-warehouses text-center"></div>';
        tdQuantityHold = `<div class="td-quantity-hold text-center">
            <input type="hidden" name="items[${counter}][conversion_quantity_unit]" class="form-control" value="${conversion_quantity_unit}">
            ${tnhFormatNumber(dtQuantityHold)}
        </div>`;
        tdQuantityNeedHold = `<div class="td-need-hold text-center">${tnhFormatNumber(dtQuantity, 0)}</div>`;
        // tdQuantity = '<div class="td-quantity"><input type="text" onchange="totalPurchases()" name="items[' + counter + '][quantity]" class="form-control quantity number-format" style="width: 100%;" value="'+tnhFormatNumber(dtQuantity)+'"></div><div class="text-danger mtop5 show-errors"></div>';

        
        tdQuantity = `<div class="td-quantity">
            <table class="tnh-table table-warehouse" style="margin: 0;">${htmlQuantityWarehouse}</table>
            ${txtWarehouses}
        </div>`;
        tdActions = '<div class="text-center"><i onclick="removeRow(this)" class="fa fa-remove btn btn-danger remove-row"></i></div>';

        rowNode = dtItemPurchases.row.add([
            tdNumber,
            tdCode,
            tdTypeItem,
            tdUnit,
            tdQuantityHold,
            tdQuantityNeedHold,
            tdQuantity,
            tdActions
        ]).draw(false).node();

        if (txtJsonItemsId) {
            ajaxSelectParamsCallback($('#items_' + counter + ''), 'admin/manufactures/getItemsKeepStockMaterial', dtItems_id, {productions_plan_id: c_productions_plan_id}, false, txtJsonItemsId);
        } else {
            ajaxSelectParamsCallback($('#items_' + counter + ''), 'admin/manufactures/getItemsKeepStockMaterial', 0, {productions_plan_id: c_productions_plan_id});
        }
        
        
        $('select.warehouses_id').select2();
        if (idDefault) {
            $(`#warehouses_id_${counter}`).val(idDefault).trigger('change');
        }
        counter++;
        totalPurchases();
    }

    $(function() {
        init_datepicker();
        dtItemPurchases = $('#tb-item-purchases').DataTable({
            "language": app.lang.datatables,
            "pageLength": intVal(app.options.tables_pagination_limit),
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });

        loadAllKeepStockMaterial();

        $('.add-row').on('click', function(event) {
            event.preventDefault();
            createRow();
        });

        $(document).ready(function() {
            $('.add-row').click();
        });

        appValidateForm($('#keep_stock_material'), {
            'date': 'required'
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