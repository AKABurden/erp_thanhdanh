<style>
    .modal-semi .bootstrap-select {
        width: 100% !important;
    }

    .modal-semi .select2-container {
        width: 100% !important;
    }

    #tb-handling-prepare .select2-chosen {
        word-wrap: break-word !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        width: none !important;
    }
</style>
<?php echo form_open('admin/manufactures_temp/prepare_materials/'.$id, array('id' => 'handlingPrepareMaterials')); ?>
<div class="modal-dialog modal-lg modal-semi" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title; ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= lang('tnh_reference_productions_orders', 'reference_productions_orders') ?>
                    <?= $productions_orders['reference_no'] ?>
                </div>
                <div class="col-md-12">
                    <table id="tb-handling-prepare" class="table dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 150px;"><?= lang('tnh_material_code') ?>/BTP</th>
                                <th class="text-center" style="width: 150px;"><?= lang('tnh_material_name') ?>/BTP</th>
                                <th class="text-center" style="width: 100px;"><?= lang('type') ?></th>
                                <th class="text-center" style="width: 80px;" class="text-center"><?= lang('tnh_unit_manufactures') ?></th>
                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_exported') ?></th>
                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_shipped') ?></th>
                                <th class="text-center" style="width: 350px;" class="text-center"><?= lang('tblwarehouse') ?></th>
                                <th class="text-center" style="width: 50px;" class="text-center"><span class="fa fa-trash-o"></span></th>
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
            <button type="submit" onclick="checkData()" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var counterSemiProduct = 0;
    var counterWarehouses = 0;

    function totalProducts() {

    }

    function totalSelect2Warehouses() {
        tbSemiProductsW = '#tb-handling-prepare tbody tr:not("[class^=not-tr]")';
        nSemiProductsW = $(tbSemiProductsW).length;
        // use_productions_plan = $('#use_productions_plan').prop('checked');
        use_productions_plan = true;
        for (iSemiProductsW = 0; iSemiProductsW < nSemiProductsW; iSemiProductsW++) {
            elSemiProducts = $(tbSemiProductsW)[iSemiProductsW];
            c_counterSemiProduct = $(elSemiProducts).find('.counterSemiProduct').val();
            // console.log(c_counterSemiProduct);
            subMaterials = $('tr[tr-sub-materials="' + c_counterSemiProduct + '"]');
            if (subMaterials.length > 0) {
                $.each(subMaterials, function(iSub, vSub) {

                    subW = $(vSub).find('.view-add-warehouses .flex-center');
                    item_cs_id = $(vSub).find('.item_cs_id').val();
                    if (subW.length > 0) {
                        $.each(subW, function (iSW, vSW) { 
                            c_counterWarehouses = $(vSW).find('.counterWarehouses').val();
                            json_w_default = $(vSW).find('.json_item_w').val();
                            if (use_productions_plan && json_w_default != null && json_w_default) {
                                json_w_default = JSON.parse(json_w_default);
                                location_id = 0;
                                if (json_w_default != null) {
                                    location_id = json_w_default.id;
                                }
                                ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures_temp/searchWarehousers', location_id, {
                                    item_cs_id: item_cs_id,
                                    production_plan_id: '<?= $productions_plan['id'] ?>'
                                }, false, json_w_default);
                            } else {
                                $('#warehouses_items__' + c_counterWarehouses).val('');
                                ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures_temp/searchWarehousers', 0, {
                                    item_cs_id: item_cs_id,
                                    production_plan_id: '<?= $productions_plan['id'] ?>'
                                });
                            }
                        });
                    }
                });
            }
        }
    }

    function loadLocationWarehouse(_this) {
        totalSelect2Warehouses();
    }

    function removeSubWarehouses(_this) {
        flex_center = $(_this).closest('.flex-center');
        flex_center.remove();
    }

    function addRowSubWarehouses(_this, tempCounterWarehouses, tempCounterSemiProduct, quantity_sub_warehouses = 0, temItemId, tempCounterSub) {
        cDiv = $(_this).closest('div');
        htmlSubWarehouses = rowSubWarehouses(tempCounterWarehouses, tempCounterSemiProduct, 0, tempCounterSub);
        cDiv.find('.view-add-warehouses').prepend(htmlSubWarehouses);
        ajaxSelectParamsCallback('#warehouses_items__' + tempCounterWarehouses, 'admin/manufactures_temp/searchWarehousers', 0, {
            item_cs_id: temItemId
        });
    }

    function rowSubWarehouses(tempCounterWarehouses, tempCounterSemiProduct, quantity_sub_warehouses = 0, tempCounterSub, json_item_w = null) {
        return `
            <div class="flex-center">
                <div style="width: 60%;">
                    <input type="hidden" id="json_item_w_${tempCounterWarehouses}" class="form-control json_item_w" value='${(json_item_w ? JSON.stringify(json_item_w) : '')}'>
                    <input type="hidden" class="form-control counterWarehouses" value="${tempCounterWarehouses}">
                    <input name="items[${tempCounterSemiProduct}][materials][${tempCounterSub}][warehouses_items][]" id="warehouses_items__${tempCounterWarehouses}" class="warehouses_items modal-select2" style="width: 180px;" data-placeholder="<?= lang('tblwarehouse') ?>">
                </div>
                <div style="width: 35%; padding: 3px;">
                    <input type="text" name="items[${tempCounterSemiProduct}][materials][${tempCounterSub}][quantity_items][]" class="form-control" value="${tnhFormatNumber(quantity_sub_warehouses, 0)}" placeholder="<?= lang('quantity') ?>">
                </div>
                <div style="width: 5%;">
                    <span onclick="removeSubWarehouses(this)" class="fa fa-remove text-danger pointer"></span>
                </div>
            </div>
        `;
    }

    function removeItemPMT(_this) {
        $(_this).closest('tr').remove();
    }

    function rowDataProducts(dtData) {
        trSub = '';
        counterSub = 0;
        if (dtData) {
            images = dtData.images;
            if (!images) {
                images = site.base_url + 'assets/images/tnh/no_image.png';
            } 

            isWarehouses = dtData.isWarehouses;
            counterWarehouses++;

            tdNumber = `<div class="td-numbers"></div>`;
            tdImages = `<div class="td-images">
                <div class="td-image" style="">
                    <div class="preview_image" style="width: auto;">
                        <div class="display-block contract-attachment-wrapper img">
                            <div style="width:40px;">
                                <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                    <div class="">
                                        <img src="${images}" style="border-radius: 50%">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

            tdCode = `
                <div class="td-name text-left">${dtData.item_code}</div>
                <input type="hidden" class="form-control item_cs_id" value="${dtData.type+'__'+dtData.item_id}">
            `;

            tdName = `<div class="text-center">${dtData.item_name}</div>`;
            tdType = `<div class="text-center">${dtData.item_type_name}</div>`;

            tdUnitManufactures = `
                <div class="text-center">${dtData.unit_name_manufactures}</div>
            `;

            dtData.quantity = Math.ceil(dtData.quantity);
            quantity = dtData.quantity;
            tdQuantity = `
                <div class="text-center">
                    ${tnhFormatNumber(quantity, 0)}
                </div>
            `;

            tdQuantityExport = `
                <div class="text-center">
                    ${tnhFormatNumber(dtData.quantity_export, 0)}
                </div>
            `;

            cQuantityNeedHold = intVal(dtData.quantity) - intVal(dtData.quantity_export);
            if (cQuantityNeedHold <= 0) return '';
            var trWarehouseItems = '';
            quantityW = 0;

            jsonWarehouses = dtData.warehousePlan;
            if (jsonWarehouses) {
                dataJsonWarehouses = JSON.parse(jsonWarehouses);
                $.each(dataJsonWarehouses, function (iW, vW) { 
                    vW.product_quantity = formatDecimal(vW.product_quantity/dtData.conversion_quantity_unit);
                    tempQuantity = cQuantityNeedHold;
                    if (cQuantityNeedHold > 0) {
                        cQuantityNeedHold = cQuantityNeedHold - vW.product_quantity;
                        if (cQuantityNeedHold > 0) {
                            quantityW = vW.product_quantity;
                        } else {
                            quantityW = tempQuantity;
                        }

                        if (quantityW >= 0) {
                            trWarehouseItems+= rowSubWarehouses(counterWarehouses, counterSemiProduct, quantityW, counterSub, vW);
                            counterWarehouses++;
                        }
                    }
                });
            }

            if (cQuantityNeedHold > 0) {
                trWarehouseItems+= rowSubWarehouses(counterWarehouses, counterSemiProduct, cQuantityNeedHold, counterSub, '');
                counterWarehouses++;
            }

            trSub += `
                <tr class="" tr-sub-materials="${counterSemiProduct}" tr-counterSemiProduct="${counterSemiProduct}">
                    <td class="" style="width: 200px;">
                        <input type="hidden" name="counterSemiProduct[]"  class="form-control counterSemiProduct" value="${counterSemiProduct}">
                        <input type="hidden" name="items[${counterSemiProduct}][quantity_exchange]" class="form-control" value="${dtData.quantity_exchange}">
                        <input type="hidden" name="items[${counterSemiProduct}][quantity_single]" class="form-control" value="${dtData.quantity_single}">
                        <input type="hidden" name="items[${counterSemiProduct}][unit_id]" class="form-control" value="${dtData.unit_id}">
                        <input type="hidden" name="items[${counterSemiProduct}][unit_parent_id]" class="form-control" value="${dtData.unit_parent_id}">
                        <input type="hidden" name="items[${counterSemiProduct}][item_cs_id]" class="form-control" value="${dtData.item_cs_id}">
                        <input type="hidden" name="items[${counterSemiProduct}][is_single_use]" class="form-control" value="${dtData.is_single_use}">
                        <div class="flex-center">
                        ${tdImages} ${tdCode}
                        </div>
                    </td>
                    <td>${tdName}</td>
                    <td>${tdType}</td>
                    <td style="">${tdUnitManufactures}</td>
                    <td style="">${tdQuantity}</td>
                    <td style="">${tdQuantityExport}</td>
                    <td style="max-width: 300px;">
                        <div class="">
                            <input type="hidden" class="form-control counter-sub" value='${counterSub}'>
                            <input type="hidden" class="form-control data-json-w-default" value=''>
                            <a href="javascript:void(0)" ${isWarehouses == 0 ? 'style="display:none;"' : ''} onclick="addRowSubWarehouses(this, ${counterWarehouses}, ${counterSemiProduct}, ${dtData.quantity}, '${dtData.item_cs_id}', ${counterSub})">
                                <div class="panel-comment" style="padding: 5px;">
                                        <div class="div-content">
                                        <span class="fa fa-plus"></span>
                                        &nbsp;<span class="bold text-primary"><?= lang('tnh_add_warehouses') ?></span>
                                    </div>
                                </div>
                            </a>
                            <div class="view-add-warehouses">
                                <div ${isWarehouses == 0 ? 'style="display:none;"' : ''}>
                                    ${trWarehouseItems}
                                </div>
                                <div>
                                    ${isWarehouses == 0 ? '<div class="mtop5 text-center"><div class="label label-danger border-radius-10px" style="padding: 5px 70px;"><?= lang('tnh_out_of_stock') ?></div></div>' : ''}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center"><i onclick="removeItemPMT(this)" class="fa fa-remove text-danger pointer"></i></td>
                </tr>
            `;
            counterSub++;

            $('#tb-handling-prepare tbody').prepend(trSub);
            totalProducts();
            counterSemiProduct++;
        }
    }

    function loadALLPrepareMaterialsTotal() {
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures_temp/loadALLPrepareMaterialsTotal',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                po_id: <?= $id ?>,
                production_plan_id: '<?= $productions_plan['id'] ?>',
            },
            dataType: "json",
            success: function(response) {
                if (response.items) {
                    $.each(response.items, function(index, value) {
                        rowDataProducts(value);
                    });
                    totalSelect2Warehouses();
                }
            }
        });
    }

    $(function() {
        appValidateForm($('#handlingPrepareMaterials'), {
            warehouses_products: 'required'
        }, saveHandlingPrepareMaterialsTotal);

        loadALLPrepareMaterialsTotal();

        function saveHandlingPrepareMaterialsTotal(form) {
            $('.add').attr('disabled', 'disabled');
            // var data = $(form).serialize();
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
                    $('.td-process').html(data.process);
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
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