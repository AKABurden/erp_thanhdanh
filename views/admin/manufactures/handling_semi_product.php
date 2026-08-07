<style>
    .modal-semi .bootstrap-select {
        width: 100% !important;
    }

    .modal-semi .select2-container {
        width: 100% !important;
    }

    #tb-handling-semi-products .select2-chosen {
        word-wrap: break-word !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        width: none !important;
    }
</style>
<?php echo form_open('admin/manufactures/handlingSemiProduct/', array('id' => 'handling_semi_products')); ?>
<div class="modal-dialog modal-lg modal-semi" style="width: 70%;">
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
                            <span><?= lang('Tại công đoạn <b>'.$dtPois['stage_name'].'</b> có phát sinh bán thành phẩm nhập kho. Vui lòng xác nhận thông tin bán thành phẩm phía dưới!') ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-md-4 hide">
                        <div class="form-group">
                            <?= lang('tnh_warehouses_semi_product', 'warehouses_semi_product') ?>
                            <select name="warehouses_semi_product" data-placeholder="<?= lang('tnh_warehouses_semi_product') ?>" id="warehouses_semi_product" class="modal-select2" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($warehouses)) : ?>
                                    <?php foreach ($warehouses as $key => $value) : ?>
                                        <option <?= $value['id'] == WAREHOUSES_CAPACITY ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group hide">
                            <div class="checkbox checkbox-info m-top-0 m-bottom-0">
                                <input type="checkbox" checked onchange="loadLocationWarehouse(this)" name="use_productions_plan" id="use_productions_plan" value="1">
                                <label for="use_productions_plan"><?= lang('tnh_use_plan_materials') ?> <?= $productions_plan['reference_no'] ?></label>
                            </div>
                        </div>
                        <div class="form-group hide">
                            <div class="checkbox checkbox-info m-top-0" style="margin-bottom: 5px;">
                                <input type="checkbox" id="additional" onchange="changeAdditional(this)" value="1">
                                <label for="additional"><?= lang('tnh_additional_semi_products') ?></label>
                            </div>
                            <div class="show-additional" style="display: none;">
                                <input type="text" name="semi_products_in_order" onchange="addRowSemiProduct(this)" id="semi_products_in_order" class="semi_products_in_order modal-select2" style="width: 100%;" data-placeholder="<?= lang('tnh_additional_semi_products') ?>" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="tb-handling-semi-products" class="table dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 260px;"><?= lang('tnh_btp') ?></th>
                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('quantity') ?></th>
                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_compensation') ?></th>
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
            <input type="hidden" name="pp_id" id="pp_id" class="form-control" value="<?= $productions_plan['id'] ?>">
            <input type="hidden" name="sp_cqis_id" id="sp_cqis_id" class="form-control" value="<?= $cqis_id ?>">
            <input type="hidden" name="isWarehouses" id="isWarehouses" class="form-control" value="0">
            <input type="hidden" name="sp_actions" id="sp_actions" class="form-control" value="<?= $actions ?>">
            <input type="hidden" name="sp_type" id="sp_type" class="form-control" value="<?= $type ?>">
            <input type="hidden" name="sp_pod_id" id="sp_pod_id" class="form-control" value="<?= $pod_id ?>">
            <input type="hidden" name="sp_pois_id" id="sp_pois_id" class="form-control" value="<?= $pois_id ?>">

            <input type="hidden" name="sp_cqi_id" id="sp_cqi_id" class="form-control" value="<?= $cqi_id ?>">
            <input type="hidden" name="sp_quantity" id="sp_quantity" class="form-control" value="<?= $quantity ?>">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <?php if($actions == "enter_semi_products" || $actions == "qc"): ?>
                <div class="checkbox checkbox-info">
                    <input type="checkbox" checked name="finished_productions" id="finished_productions" value="1">
                    <label for="finished_productions" class="text-primary"><?= lang('tnh_finished_productions') ?></label>
                </div>
            <?php endif; ?>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <?php if($actions != "view"): ?>
            <button type="submit" onclick="checkData()" class="btn btn-primary add"><?= _l('save') ?></button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var counterSemiProduct = 0;
    var counterWarehouses = 0;

    function checkData() {
        if(!$('#warehouses_semi_product').val()) {
            alert_float('danger', '<?= lang('Vui lòng chọn kho hàng') ?>');
        }
    }

    function totalSemiProducts(is_change_quantity = false) {
        tbSemiProducts = '#tb-handling-semi-products tbody tr:not("[class^=not-tr]")';
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
                $.each(subMaterials, function(iSub, vSub) {
                    quantity_single = intVal($(vSub).find('.quantity_single').val());
                    number_children_size = intVal($(vSub).find('.number_children_size').val());
                    is_single_use = intVal($(vSub).find('.is_single_use').val());

                    quota_material_replace_t = intVal($(vSub).find('.quota_material_replace_t').val());
                    if (is_single_use > 0) {
                        quantity_material = quantity_semi_product/quota_material_replace_t * quantity_single;
                    } else {
                        // quantity_material = quantity_single * quantity_semi_product;
                        quantity_material = quantity_semi_product/number_children_size *quantity_single;
                    }

                    $(vSub).find('.quantity_materials').val(formatDecimal(quantity_material));
                    $(vSub).find('.txt-quantity').html(tnhFormatNumber(quantity_material));

                    if (is_change_quantity) {
                        $(vSub).find('.quantity_items').val(0);
                        $($($(vSub).find('.quantity_items'))[0]).val(tnhFormatNumber(quantity_material));
                    }
                });
            }
        }

        isWarehouses = $('#isWarehouses').val();
        if (sttSemiProducts == 0 && isWarehouses == 0) {
            trNoti = `
                <tr class="not-tr td-notification">
                    <td colspan="4" style="padding: 10px;">
                        <div class="text-center"><div class="label label-danger border-radius-10px" style="padding: 5px 70px;"><?= lang('Không có bán thành phẩm để hoàn thành') ?></div></div>
                    </td>
                </tr>
            `;
            $('#tb-handling-semi-products tbody').html(trNoti);
        } else if (sttSemiProducts == 0 && isWarehouses == 1) {
            trNoti = `
                <tr class="not-tr td-notification">
                    <td colspan="4" style="padding: 10px;">
                        <div class="text-center"><div class="text-danger text-left italic tag-not-stage" style="padding: 5px 70px;"><?= lang('tnh_enough_semi_product_plan_materials') ?></div></div>
                    </td>
                </tr>
            `;
            $('#tb-handling-semi-products tbody').html(trNoti);
        } else {
            $('#tb-handling-semi-products tbody tr.td-notification').remove();
        }
    }

    function removeTrSemiProduct(_this, c_counterSemiProduct) {
        cTable = $(_this).closest('table');
        cTable.find('tr[tr-countersemiproduct="' + c_counterSemiProduct + '"]').remove();
        totalSemiProducts();
    }

    function totalSelect2Warehouses() {
        tbSemiProductsW = '#tb-handling-semi-products tbody tr:not("[class^=not-tr]")';
        nSemiProductsW = $(tbSemiProductsW).length;
        use_productions_plan = $('#use_productions_plan').prop('checked');
        for (iSemiProductsW = 0; iSemiProductsW < nSemiProductsW; iSemiProductsW++) {
            elSemiProducts = $(tbSemiProductsW)[iSemiProductsW];
            c_counterSemiProduct = $(elSemiProducts).find('.counterSemiProduct').val();
            subMaterials = $('tr[tr-sub-materials="' + c_counterSemiProduct + '"]');
            if (subMaterials.length > 0) {
                $.each(subMaterials, function(iSub, vSub) {

                    // subW = $('.view-add-warehouses .flex-center');
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
                                    production_plan_id: '<?= $productions_plan['id'] ?>'
                                }, false, json_w_default);
                            } else {
                                $('#warehouses_items__' + c_counterWarehouses).val('');
                                ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures/searchWarehousers', 0, {
                                    item_cs_id: item_cs_id,
                                    production_plan_id: '<?= $productions_plan['id'] ?>'
                                });
                            }
                        });
                    }

                    // c_counterWarehouses = $(vSub).find('.counterWarehouses').val();
                    // item_cs_id = $(vSub).find('.item_cs_id').val();
                    // json_w_default = $(vSub).find('.data-json-w-default').val();
                    // if (use_productions_plan && json_w_default != null && json_w_default) {
                    //     json_w_default = JSON.parse(json_w_default);
                    //     location_id = 0;
                    //     if (json_w_default != null) {
                    //         location_id = json_w_default.id;
                    //     }
                    //     ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures/searchWarehousers', location_id, {
                    //         item_cs_id: item_cs_id,
                    //         production_plan_id: '<?= $productions_plan['id'] ?>'
                    //     }, false, json_w_default);
                    // } else {
                    //     $('#warehouses_items__' + c_counterWarehouses).val(0);
                    //     ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures/searchWarehousers', 0, {
                    //         item_cs_id: item_cs_id,
                    //         production_plan_id: '<?= $productions_plan['id'] ?>'
                    //     });
                    // }
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
                    <input name="items[${tempCounterSemiProduct}][materials][${tempCounterSub}][warehouses_items][]" id="warehouses_items__${tempCounterWarehouses}" class="warehouses_items modal-select2" style="max-width: 200px;" data-placeholder="<?= lang('tblwarehouse') ?>">
                </div>
                <div style="width: 35%; padding: 3px;">
                    <input type="text" name="items[${tempCounterSemiProduct}][materials][${tempCounterSub}][quantity_items][]" class="form-control quantity_items" value="${tnhFormatNumber(quantity_sub_warehouses)}" placeholder="<?= lang('quantity') ?>">
                </div>
                <div style="width: 5%;">
                    <span onclick="removeSubWarehouses(this)" class="fa fa-remove text-danger pointer"></span>
                </div>
            </div>
        `;
    }

    function removeMaterial(_this) {
        $(_this).closest('tr').remove();
        totalSemiProducts();
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
                            <input type="text" name="items[${tempCounterSemiProduct}][materials][${tempCounterSubAuto}][quantity_single]" onchange="totalSemiProducts()" class="form-control number-format quantity_single" value="${(value.quantity_single)}">
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

    function rowDataSemiProduct(dtData) {
        if (dtData) {
            semi_product_code = dtData.code;
            semi_product_name = dtData.name;
            quantity_primary = dtData.quantity_primary;
            quantity = dtData.quantity;
            isBom = true;
            images = dtData.images;
            if (!images) {
                images = site.base_url + 'assets/images/tnh/no_image.png';
            }
            tdNumber = `<div class="td-numbers"></div>`;
            tdImages = `<div class="td-images">
                <div class="td-image">
                    <div class="preview_image" style="width: auto;">
                        <div class="display-block contract-attachment-wrapper img">
                            <div style="width:45px;">
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
            tdName = `<div class="td-name bold text-primary" style="font-size: 14px;">${semi_product_name}(${semi_product_code})</div>`;
            tdUnit = `<div class="td-unit"></div>`;
            tdQuantity = `<div class="td-quantity text-center">
                <input type="hidden" name="counterSemiProduct" class="form-control counterSemiProduct" value="${counterSemiProduct}">
                <input type="hidden" name="items[${counterSemiProduct}][id]" class="form-control" value="${dtData.id}">
                <input type="hidden" name="items[${counterSemiProduct}][poisub_id]" class="form-control" value="${dtData.poisub_id}">
                <input type="hidden" name="items[${counterSemiProduct}][quantity_exchange]" class="form-control" value="${dtData.quantity_exchange}">
                <input type="hidden" name="items[${counterSemiProduct}][quantity_single]" class="form-control" value="${dtData.quantity_single}">
                <input type="hidden" name="items[${counterSemiProduct}][unit_id]" class="form-control" value="${dtData.unit_id}">
                <input type="hidden" name="items[${counterSemiProduct}][unit_parent_id]" class="form-control" value="${dtData.unit_parent_id}">
                <input type="text" name="items[${counterSemiProduct}][quantity_semi_product]" class="form-control quantity_semi_product text-center" onchange="totalSemiProducts(true)" value="${tnhFormatNumber(quantity)}">
            </div>`;
            tdActions = `<div class="td-quantity text-center"><i onclick="removeTrSemiProduct(this, '${counterSemiProduct}')" class="pointer fa fa-remove text-danger"></i></div>`;

            trItems = `<tr data-semiproduct="${dtData.product_id}" class="group-tr" tr-counterSemiProduct="${counterSemiProduct}">
                <td class="" style="border-top: 1px solid #cedae6 !important; border-right: 0 !important; border-bottom: 0 !important;"><div class="flex-center">${tdImages} ${tdName}</div></td>
                <td class="text-center" style="border-top: 1px solid #cedae6 !important; border-left: 0 !important; border-right: 0 !important;  border-bottom: 0 !important;">${tdQuantity}</td>
                <td class="text-center" style="border-top: 1px solid #cedae6 !important; border-left: 0 !important; border-right: 0 !important;  border-bottom: 0 !important;"></td>
                <td class="text-center" style="border-top: 1px solid #cedae6 !important; border-left: 0 !important;  border-bottom: 0 !important;">${tdActions}</td>
            </tr>`;

            if (dtData.subItems && dtData.subItems.length > 0) {
                subItems = dtData.subItems;
                trSub = '';
                counterSub = 0;
                $.each(subItems, function(index, value) {
                    images = value.images;
                    if (!images) {
                        images = site.base_url + 'assets/images/tnh/no_image.png';
                    }
                    jsonWarehouses = (value.warehousePlan);
                    isWarehouses = (value.isWarehouses);
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
                        <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][quantity_materials]" class="form-control quantity_materials" readonly value="${(value.quantity)}">
                        <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][unit_id]" class="form-control" readonly value="${(value.unit_id)}">
                        <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][unit_parent_id]" class="form-control" readonly value="${(value.unit_parent_id)}">
                        <div class="txt-quantity italic">${tnhFormatNumber(value.quantity)}</div>
                    </div>`;

                    tdCompensation = `<div class="text-center">
                        ${tnhFormatNumber(value.quantity_compensation)}
                    </div>`;

                    // ${jsonWarehouses}
                    // ${rowSubWarehouses(counterWarehouses, counterSemiProduct, value.quantity, counterSub)}
                    cQuantityNeedHold = value.quantity;
                    var trWarehouseItems = '';
                    quantityW = 0;
                    if (jsonWarehouses) {
                        dataJsonWarehouses = JSON.parse(jsonWarehouses);
                        $.each(dataJsonWarehouses, function (iW, vW) { 
                            vW.product_quantity = formatDecimal(vW.product_quantity * value.quantity_exchange);
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
                        <tr class="not-tr not-border" tr-sub-materials="${counterSemiProduct}" tr-counterSemiProduct="${counterSemiProduct}">
                            <td class="" style="width: 200px; border-left: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                                <div class="flex-center" style="padding-left: 40px;">
                                    ${tdImages} ${tdName}
                                </div>
                                <div class="text-danger" style="padding-left: 40px;"><?= lang('tnh_unit_manufactures') ?>: ${value.unit_name_manufactures}</div>
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][quantity_exchange]" class="form-control" value="${(value.quantity_exchange)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][quantity_single]" class="form-control" value="${(value.quantity_single)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][item_cs_id]" class="form-control" value="${(value.item_cs_id)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][is_single_use]" class="form-control is_single_use" value="${(value.is_single_use)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][quantity_order]" class="form-control quantity_order" value="${(value.quantity_order)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][landscape_print_size]" class="form-control landscape_print_size" value="${(value.landscape_print_size)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][vertical_print_size]" class="form-control vertical_print_size" value="${(value.vertical_print_size)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][number_children_size]" class="form-control number_children_size" value="${(value.number_children_size)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][paper_exchange]" class="form-control paper_exchange" value="${(value.paper_exchange)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][quota_material_replace_t]" class="form-control quota_material_replace_t" value="${(value.quota_material_replace_t)}">
                            </td>
                            <td style="width: 100px; border-bottom: 0px !important;">${tdQuantity}</td>
                            <td style="width: 100px; border-bottom: 0px !important;">${tdCompensation}</td>
                            <td style="border-right: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                                <div class="">
                                    <input type="hidden" class="form-control counter-sub" value='${counterSub}'>
                                    <input type="hidden" class="form-control data-json-w-default" value=''>
                                    <a href="javascript:void(0)" ${isWarehouses == 0 ? 'style="display:none;"' : ''} onclick="addRowSubWarehouses(this, ${counterWarehouses}, ${counterSemiProduct}, ${value.quantity}, '${value.item_cs_id}', ${counterSub})">
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
                });
                // contains
                trItems += trSub;
            } else {
                isBom = false;
                counterSubAuto = 0;
                trItems+= `
                    <tr class="not-tr not-border" tr-sub-materials="${counterSemiProduct}" tr-counterSemiProduct="${counterSemiProduct}">
                        <td colspan="4" style="border-left: 1px solid #cedae6 !important; border-right: 1px solid #cedae6 !important; border-top: 0px !important">
                            <div class="flex-center">
                                <input type="text" id="search_materials_${counterSemiProduct}" class="modal-select2 search_materials_" style="width: 300px;" value="" data-placeholder="<?= lang('materials') ?>">
                                <a href="javascript:void(0)" style="margin-left: 5px;" class="mright5" onclick="addMaterials(this, ${counterSemiProduct}, ${counterSubAuto})">
                                    <div class="panel-comment" style="padding: 5px; width: 200px;">
                                            <div class="div-content">
                                            <span class="fa fa-plus"></span>
                                            &nbsp;<span class="bold text-primary"><?= lang('tnh_add_material') ?></span>
                                        </div>
                                    </div>
                                </a>
                                <div class="checkbox m-bottom-0 checkbox-success">
                                    <input type="checkbox" name="name="items[${counterSemiProduct}][is_save_dm]" id="save_dm_${counterSemiProduct}" value="1">
                                    <label for="save_dm_${counterSemiProduct}"><?= lang('tnh_save_dm') ?></label>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            }

            $('#tb-handling-semi-products').prepend(trItems);
            if (isBom == false) {
                ajaxSelectParamsCallback('#search_materials_'+counterSemiProduct, 'admin/manufactures/searchMaterials', 0, {production_plan_id: '<?= $productions_plan['id'] ?>'});
            }
            totalSemiProducts();
            counterSemiProduct++;
        }
    }

    function loadALLSemiProduct() {
        sp_pod_id = $('#sp_pod_id').val();
        sp_pois_id = $('#sp_pois_id').val();
        sp_type = $('#sp_type').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/loadALLSemiProduct',
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
                        rowDataSemiProduct(value);
                    });
                    $('#isWarehouses').val(response.isWarehouses);
                    totalSelect2Warehouses();
                }
                totalSemiProducts();
            }
        });
    }

    function addRowSemiProduct(_this) {
        c_product_id = $(_this).val();
        sp_pod_id = $('#sp_pod_id').val();
        sp_pois_id = $('#sp_pois_id').val();
        sp_type = $('#sp_type').val();
        $(_this).val(0);
        if (c_product_id) {
            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/manufactures/rowSemiProductPOD',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    pod_id: sp_pod_id,
                    product_id: c_product_id,
                    pois_id: sp_pois_id,
                    type: sp_type,
                    production_plan_id: '<?= $productions_plan['id'] ?>'
                },
                dataType: "json",
                success: function(response) {
                    if ($('tr[data-semiproduct="'+response.items.product_id+'"]').length > 0) {
                        $(_this).select2('val', null);
                        alert_float('danger', '<?= lang('Bán thành phẩm này đã được chọn, vui lòng kiểm tra lại.') ?>');
                        return;
                    } else {
                        rowDataSemiProduct(response.items);
                        totalSelect2Warehouses();
                    }
                }
            });
        }
    }

    $(function() {
        $('#warehouses_semi_product').select2();
        loadALLSemiProduct();
        sp_pod_id = $('#sp_pod_id').val();
        ajaxSelectParamsCallback('#semi_products_in_order', 'admin/manufactures/searchSemiProductsPOD', 0, {
            pod_id: sp_pod_id
        });

        appValidateForm($('#handling_semi_products'), {
            warehouses_semi_product: 'required'
        }, savehandlingSemiProducts);

        function savehandlingSemiProducts(form) {
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
            //
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