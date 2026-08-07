<style>
    .modal-semi .bootstrap-select {
        width: 100% !important;
    }

    .modal-semi .select2-container {
        width: 100% !important;
    }

    #tb-handling-products .select2-chosen {
        word-wrap: break-word !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        width: none !important;
    }
</style>
<?php echo form_open('admin/manufactures_temp/handlingPrepareMaterials/', array('id' => 'handlingPrepareMaterials')); ?>
<div class="modal-dialog modal-lg modal-semi" style="width: 65%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title; ?> <?= $dtPois['stage_name'] ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <span><?= lang('Tại công đoạn <b>'.$dtPois['stage_name'].'</b> có phát sinh chuẩn bị NPL. Vui lòng xác nhận thông tin thành phẩm phía dưới!') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="tb-handling-products" class="table dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 260px;"><?= lang('materials') ?></th>
                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_unit_manufactures') ?></th>
                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('quantity') ?></th>
                                <th class="text-center" style="width: 250px;" class="text-center"><?= lang('tblwarehouse') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="sp_actions" id="sp_actions" class="form-control" value="<?= $actions ?>">
            <input type="hidden" name="sp_cqis_id" id="sp_cqis_id" class="form-control" value="<?= $cqis_id ?>">
            <input type="hidden" name="sp_type" id="sp_type" class="form-control" value="<?= $type ?>">
            <input type="hidden" name="sp_pod_id" id="sp_pod_id" class="form-control" value="<?= $pod_id ?>">
            <input type="hidden" name="sp_pois_id" id="sp_pois_id" class="form-control" value="<?= $pois_id ?>">

            <input type="hidden" name="sp_cqi_id" id="sp_cqi_id" class="form-control" value="<?= $cqi_id ?>">
            <input type="hidden" name="sp_quantity" id="sp_quantity" class="form-control" value="<?= $quantity ?>">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <?php if($actions == "products" || $actions == "qc"): ?>
                <div class="checkbox checkbox-info">
                    <input type="checkbox" checked name="finished_productions" id="finished_productions" value="1">
                    <label for="finished_productions" class="text-primary"><?= lang('tnh_finished_productions') ?></label>
                </div>
            <?php endif; ?>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" onclick="checkData()" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var counterSemiProduct = 0;
    var counterWarehouses = 0;

    function checkData() {
        // if(!$('#warehouses_products').val()) {
        //     alert_float('danger', '<?= lang('Vui lòng chọn kho hàng') ?>');
        // }
    }

    function totalProducts() {
        tbSemiProducts = '#tb-handling-products tbody tr:not("[class^=not-tr]")';
        nSemiProducts = $(tbSemiProducts).length;
        sttSemiProducts = 0;
        for (iSemiProducts = 0; iSemiProducts < nSemiProducts; iSemiProducts++) {
            sttSemiProducts++;
            elSemiProducts = $(tbSemiProducts)[iSemiProducts];
            $(elSemiProducts).find('.td-numbers').html(sttSemiProducts);
            quantity_semi_product = intVal($(elSemiProducts).find('.quantity_semi_product').val());
            c_counterSemiProduct = $(elSemiProducts).find('.counterSemiProduct').val();
            subMaterials = $('tr[tr-sub-materials="' + c_counterSemiProduct + '"]');
            if (subMaterials.length > 0) {
                
                // $.each(subMaterials, function(iSub, vSub) {
                //     quantity_single = intVal($(vSub).find('.quantity_single').val());
                //     is_single_use = intVal($(vSub).find('.is_single_use').val());
                //     quota_material_replace_t = intVal($(vSub).find('.quota_material_replace_t').val());
                //     if (is_single_use > 0) {
                //         quantity_material = quantity_semi_product/quota_material_replace_t * quantity_single;
                //     } else {
                //         quantity_material = quantity_single * quantity_semi_product;
                //     }
                //     $(vSub).find('.quantity_materials').val(formatDecimal(quantity_material));
                //     $(vSub).find('.txt-quantity').html(tnhFormatNumber(quantity_material));
                // });
            }
        }
    }

    function removeTrProducts(_this, c_counterSemiProduct) {
        cTable = $(_this).closest('table');
        cTable.find('tr[tr-countersemiproduct="' + c_counterSemiProduct + '"]').remove();
        totalProducts();
    }

    function totalSelect2Warehouses() {
        tbSemiProductsW = '#tb-handling-products tbody tr:not("[class^=not-tr]")';
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
                                ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures/searchWarehousers', location_id, {
                                    item_cs_id: item_cs_id,
                                    production_plan_id: '<?= 0 ?>'
                                }, false, json_w_default);
                            } else {
                                $('#warehouses_items__' + c_counterWarehouses).val('');
                                ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures/searchWarehousers', 0, {
                                    item_cs_id: item_cs_id,
                                    production_plan_id: '<?= 0 ?>'
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

    function changeAdditional(_this) {
        isCheckAdditional = $(_this).prop('checked');
        if (isCheckAdditional) {
            $('.show-additional').show("slow");
        } else {
            $('.show-additional').slideUp();
        }
    }

    function removeSubWarehouses(_this) {
        flex_center = $(_this).closest('.flex-center');
        flex_center.remove();
    }

    function addRowSubWarehouses(_this, tempCounterWarehouses, tempCounterSemiProduct, quantity_sub_warehouses = 0, temItemId, tempCounterSub) {
        cDiv = $(_this).closest('div');
        htmlSubWarehouses = rowSubWarehouses(tempCounterWarehouses, tempCounterSemiProduct, 0, tempCounterSub);
        cDiv.find('.view-add-warehouses').prepend(htmlSubWarehouses);
        ajaxSelectParamsCallback('#warehouses_items__' + tempCounterWarehouses, 'admin/manufactures/searchWarehousers', 0, {
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
                    <input type="text" name="items[${tempCounterSemiProduct}][materials][${tempCounterSub}][quantity_items][]" class="form-control" value="${tnhFormatNumber(quantity_sub_warehouses)}" placeholder="<?= lang('quantity') ?>">
                </div>
                <div style="width: 5%;">
                    <span onclick="removeSubWarehouses(this)" class="fa fa-remove text-danger pointer"></span>
                </div>
            </div>
        `;
    }

    function removeMaterial(_this) {
        $(_this).closest('tr').remove();
        totalProducts();
    }

    function addMaterials(_this, tempCounterSemiProduct, tempCounterSubAuto) {
        cTr = $(_this).closest('tr');
        dataMaterial = cTr.find('input.search_materials_').select2('data');
        if (dataMaterial) {
            value = dataMaterial;
            console.log(value);
            images = dataMaterial.images;
            if (!images) {
                images = site.base_url + 'assets/images/tnh/no_image.png';
            }
            jsonWarehouses = value.warehousePlan;
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
            tdName = `
                <div class="td-name text-left italic">${value.item_name}(${value.item_code})</div>
                <input type="hidden" class="form-control item_cs_id" value="${value.item_cs_id}">
            `;
            tdQuantity = `<div class="td-quantity text-center">
                <input type="hidden" name="" class="form-control quantity_single" value="${value.quantity_single}">
                <input type="hidden" name="items[${tempCounterSemiProduct}][materials][${tempCounterSubAuto}][quantity_materials]" class="form-control quantity_materials" readonly value="${(value.quantity)}">
                <input type="hidden" name="items[${tempCounterSemiProduct}][materials][${tempCounterSubAuto}][unit_id]" class="form-control" readonly value="${(value.unit_id)}">
                <input type="hidden" name="items[${tempCounterSemiProduct}][materials][${tempCounterSubAuto}][unit_parent_id]" class="form-control" readonly value="${(value.unit_parent_id)}">
                <div class="txt-quantity italic">${tnhFormatNumber(value.quantity)}</div>
            </div>`;
            trMaterial = `
                <tr class="not-tr not-border" tr-sub-materials="${tempCounterSemiProduct}" tr-counterSemiProduct="${tempCounterSemiProduct}">
                    <td class="" style="width: 200px; border-left: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                        <div class="flex-center" style="padding-left: 40px;">
                        <i class="fa fa-remove text-danger pointer" onclick="removeMaterial(this)"></i>
                        ${tdImages} ${tdName}
                        </div>
                        <div style="padding-left: 40px;">
                            <input type="text" name="items[${tempCounterSemiProduct}][materials][${tempCounterSubAuto}][quantity_single]" onchange="totalProducts()" class="form-control number-format quantity_single" value="${(value.quantity_single)}">
                        </div>
                        <input type="hidden" name="items[${tempCounterSemiProduct}][materials][${tempCounterSubAuto}][quantity_exchange]" class="form-control quantity_exchange" value="${(value.quantity_exchange)}">
                        <input type="hidden" name="items[${tempCounterSemiProduct}][materials][${tempCounterSubAuto}][item_cs_id]" class="form-control" value="${(value.item_cs_id)}">
                    </td>
                    <td style="width: 100px; border-bottom: 0px !important;">${tdQuantity}</td>
                    <td style="border-right: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                        <div class="">
                            <input type="hidden" class="form-control counter-sub" value='${tempCounterSubAuto}'>
                            <input type="hidden" class="form-control data-json-w-default" value='${jsonWarehouses}'>
                            <a href="javascript:void(0)" onclick="addRowSubWarehouses(this, ${counterWarehouses}, ${tempCounterSemiProduct}, ${value.quantity}, '${value.item_cs_id}', ${tempCounterSubAuto})">
                                <div class="panel-comment" style="padding: 5px;">
                                        <div class="div-content">
                                        <span class="fa fa-plus"></span>
                                        &nbsp;<span class="bold text-primary"><?= lang('tnh_add_warehouses') ?></span>
                                    </div>
                                </div>
                            </a>
                            <div class="view-add-warehouses">
                                ${rowSubWarehouses(counterWarehouses, tempCounterSemiProduct, value.quantity, tempCounterSubAuto)}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            cTr.before(trMaterial);
            json_w_default = null;
            if (jsonWarehouses != null && typeof jsonWarehouses != "undefined") {
                json_w_default = JSON.parse(jsonWarehouses);
            }
            location_id = 0;
            if (json_w_default != null) {
                location_id = json_w_default.id;
            }
            ajaxSelectParamsCallback('#warehouses_items__' + counterWarehouses, 'admin/manufactures/searchWarehousers', location_id, {
                item_cs_id: value.item_cs_id
            }, false, json_w_default);
        }
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

            tdName = `
                <div class="td-name text-left italic">${dtData.item_name}(${dtData.item_code})</div>
                <input type="hidden" class="form-control item_cs_id" value="${dtData.item_cs_id}">
            `;

            tdUnitManufactures = `
                <div class="text-center">${dtData.unit_name_manufactures}</div>
            `;

            quantity = dtData.quantity;
            tdQuantity = `
                <div class="text-center">
                    ${tnhFormatNumber(quantity)}
                </div>
            `;

            cQuantityNeedHold = dtData.quantity;
            var trWarehouseItems = '';
            quantityW = 0;

            jsonWarehouses = dtData.warehousePlan;
            if (jsonWarehouses) {
                dataJsonWarehouses = JSON.parse(jsonWarehouses);
                $.each(dataJsonWarehouses, function (iW, vW) { 
                    vW.product_quantity = formatDecimal(vW.product_quantity * dtData.quantity_exchange);
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
                        ${tdImages} ${tdName}
                        </div>
                    </td>
                    <td style="">${tdUnitManufactures}</td>
                    <td style="">${tdQuantity}</td>
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
                </tr>
            `;
            counterSub++;

            $('#tb-handling-products tbody').prepend(trSub);
            totalProducts();
            counterSemiProduct++;
        }
    }

    function loadALLPrepareMaterials() {
        sp_pod_id = $('#sp_pod_id').val();
        sp_pois_id = $('#sp_pois_id').val();
        sp_type = $('#sp_type').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures_temp/loadALLPrepareMaterials',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                pod_id: sp_pod_id,
                pois_id: sp_pois_id,
                type: sp_type,
                production_plan_id: '<?= $productions_plan['id'] ?>',
                quantity: $('#sp_quantity').val(),
                actions: $('#sp_actions').val(),
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
        $('#warehouses_products').select2();
        loadALLPrepareMaterials();
        sp_pod_id = $('#sp_pod_id').val();

        appValidateForm($('#handlingPrepareMaterials'), {
            warehouses_products: 'required'
        }, saveHandlingProducts);

        function saveHandlingProducts(form) {
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