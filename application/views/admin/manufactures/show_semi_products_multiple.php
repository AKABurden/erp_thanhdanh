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
<?php echo form_open('admin/manufactures/handlingMultipleSemiProduct/', array('id' => 'handling_semi_products_mul')); ?>
<div class="modal-dialog modal-lg modal-semi" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title; ?> <?= $stage['name'] ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <span><?= lang('Tại công đoạn <b>' . $stage['name'] . '</b> có phát sinh bán thành phẩm nhập kho. Vui lòng xác nhận thông tin bán thành phẩm phía dưới!') ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="show-errors-v">

                        </div>
                    </div>
                    <div class="col-md-8 hide">
                        <div class="form-group">
                            <div class="checkbox checkbox-info m-top-0 m-bottom-0">
                                <input type="checkbox" checked onchange="loadLocationWarehouse(this)" id="use_productions_plan" value="1">
                                <label for="use_productions_plan"><?= lang('tnh_use_plan_materials') ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-info m-top-0" style="margin-bottom: 5px;">
                                <input type="checkbox" id="additional" onchange="changeAdditional(this)" value="1">
                                <label for="additional"><?= lang('tnh_additional_semi_products') ?></label>
                            </div>
                            <div class="show-additional" style="display: none;">
                                <input type="text" onchange="addRowSemiProduct(this)" id="semi_products_in_order" class="semi_products_in_order modal-select2" style="width: 100%;" data-placeholder="<?= lang('tnh_additional_semi_products') ?>" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
            </div>
            <?php
                $arrPOIId = [];
                $arr_pp_id = [];
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $arr_pp_id[] = $value['pp_id'];
                        $arrPOIId[] = $value['poi_id'];
                    }
                }

                $dtWarehouses = [];
                $counterWarehouses = 0;
                if (!empty($arrPOIId)) {
                    $this->db->select('
                        tbl_productions_orders_items_sub.item_id as item_id
                    ', false);
                    $this->db->from('tbl_productions_orders_items_sub');
                    $this->db->where_in('tbl_productions_orders_items_sub.productions_orders_items_id', $arrPOIId);
                    $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
                    $this->db->group_by('tbl_productions_orders_items_sub.item_id');
                    $semi_products = $this->db->get()->result_array();
                    if (!empty($semi_products)) {
                        $arr_pp_id = array_unique($arr_pp_id);
                        if (!empty($arr_pp_id)) {
                            foreach ($arr_pp_id as $kPP => $vPP) {
                                foreach ($semi_products as $key => $value) {
                                    $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouses');
                                    $this->db->from('tblwarehouse_items');
                                    $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
                                    $this->db->where('tblwarehouse_items.warehouse_id', WAREHOUSES_CAPACITY);
                                    $this->db->where('tbllocaltion_warehouses.productions_plan_id', $vPP);
                                    $this->db->where('tblwarehouse_items.id_items', $value['item_id']);
                                    $this->db->where('tblwarehouse_items.type_items', 'product');
                                    $quantity_warehouses = $this->db->get()->row_array()['quantity_warehouses'];
                                    if (!empty($quantity_warehouses)) {
                                        $dtWarehouses['semi_products_' . $value['item_id'] . '_' . $vPP] = $quantity_warehouses;
                                    }
                                }
                            }
                        }
                    }
                }

                foreach ($items as $key => $value) {
                    $pod_id = $value['pod_id'];
                    $production_plan_id = $value['pp_id'];
                    $productions_orders_subs = $this->manufactures_model->loadDataSemiProducts($pod_id, $production_plan_id, 0, '', 0)['productions_orders_subs'];
                    $items[$key]['productions_orders_subs'] = null;
                    if (!empty($productions_orders_subs)) {
                        foreach ($productions_orders_subs as $k => $dtData) {
                            $product_id = $dtData['product_id'];
                            $semi_product_code = $dtData['code'];
                            $semi_product_name = $dtData['name'];
                            $quantity_primary = $dtData['quantity_primary'];
                            $quantity = $dtData['quantity'];
                            if (!empty($dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id])) {
                                $quantity_warehouse = $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id];
                                if ($quantity_warehouse > 0) {
                                    $tempQuantity = $quantity_warehouse - $quantity;
                                    if ($tempQuantity >= 0) {
                                        $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id] = $tempQuantity;
                                        continue;
                                    } else {
                                        $quantity = abs($tempQuantity);

                                    }
                                    $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id] = $tempQuantity;
                                }
                            }
                            $productions_orders_subs[$k]['quantity'] = $quantity;
                            $isSemi = 1;
                            $items[$key]['productions_orders_subs'] = $productions_orders_subs;
                        }
                    }
                }

                $productions_orders_subs = array_column($items, 'productions_orders_subs');
                array_multisort($productions_orders_subs, SORT_DESC, $items);
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="horizontal-scrollable-tabs">
                        <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                        <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                        <div class="horizontal-tabs">
                            <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                <?php if (!empty($items)) : ?>
                                    <?php foreach ($items as $key => $value) : ?>
                                        <?php
                                        $images = $value['images'];
                                        if (!$images) {
                                            $images = base_url('assets/images/tnh/no_image.png');
                                        } else {
                                            $images = base_url('uploads/products/' . $images);
                                        }
                                        //$arr_pp_id[] = $value['pp_id'];
                                        ?>
                                        <?php //$arrPOIId[] = $value['poi_id']; ?>
                                        <li role="presentation" class="<?= $key == 0 ? 'active' : '' ?>">
                                            <input type="hidden" name="arrPOIS[]" class="form-control" value="<?= $value['id'] ?>">
                                            <a href="#pod_id<?= $value['pod_id'] ?>" aria-controls="#pod_id<?= $value['pod_id'] ?>" role="tab" value="#pod_id<?= $value['pod_id'] ?>" data-toggle="tab">
                                                <img src="<?= $images ?>" style="width: 30px; height: 30px; border-radius: 10%;">
                                                <?= $value['item_name'] ?>(<?= $value['item_code'] ?>)
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                
                ?>
                <div class="col-md-12">
                    <div class="tab-content">
                        <?php if (!empty($items)) : ?>
                            <?php foreach ($items as $key => $value) : ?>
                                <?php
                                $pod_id = $value['pod_id'];
                                $production_plan_id = $value['pp_id'];
                                // $productions_orders_subs = $this->manufactures_model->loadDataSemiProducts($pod_id, $production_plan_id);
                                $productions_orders_subs = $value['productions_orders_subs'];
                                ?>
                                <div role="tabpanel" class="tab-pane <?= $key == 0 ? 'active' : '' ?>" id="pod_id<?= $value['pod_id'] ?>">
                                    <table class="table dataTable tb-handling-semi-products">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 260px;"><?= lang('tnh_btp') ?></th>
                                                <th class="text-center" style="width: 100px;" class="text-center"><?= lang('quantity') ?></th>
                                                <th class="text-center" style="width: 250px;" class="text-center"><?= lang('tblwarehouse') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $isSemi = 0; ?>
                                            <?php if ($productions_orders_subs) : ?>
                                                <?php $counterSemiProduct = 0; ?>
                                                <?php foreach ($productions_orders_subs as $k => $dtData) : ?>
                                                    <?php
                                                    $product_id = $dtData['product_id'];
                                                    $semi_product_code = $dtData['code'];
                                                    $semi_product_name = $dtData['name'];
                                                    $quantity_primary = $dtData['quantity_primary'];
                                                    $quantity = $dtData['quantity'];

                                                    // if (!empty($dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id])) {
                                                    //     $quantity_warehouse = $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id];
                                                    //     if ($quantity_warehouse > 0) {
                                                    //         $tempQuantity = $quantity_warehouse - $quantity;
                                                    //         if ($tempQuantity >= 0) {
                                                    //             continue;
                                                    //         } else {
                                                    //             $quantity = abs($tempQuantity);
                                                    //         }
                                                    //         $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id] = $tempQuantity;
                                                    //     }
                                                    // }
                                                    $isSemi = 1;
                                                    $isBom = true;
                                                    $images = $dtData['images'];
                                                    if (!$images) {
                                                        $images = base_url('assets/images/tnh/no_image.png');
                                                    }

                                                    $tdNumber = '<div class="td-numbers"></div>';
                                                    $tdImages = '<div class="td-images">
                                                            <div class="td-image">
                                                                <div class="preview_image" style="width: auto;">
                                                                    <div class="display-block contract-attachment-wrapper img">
                                                                        <div style="width:45px;">
                                                                            <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                                                                <div class="">
                                                                                    <img src="' . $images . '" style="border-radius: 50%">
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                    $tdName = '<div class="td-name bold text-primary" style="font-size: 14px;">' . $semi_product_name . '(' .   $semi_product_code . ')</div>';

                                                    $tdQuantity = '<div class="td-quantity text-center">
                                                            <input type="hidden" class="form-control counterSemiProduct" value="' . $counterSemiProduct . '">
                                                            <input type="hidden" class="form-control pod_id" value="' . $pod_id . '">
                                                            <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][id]" class="form-control" value="' . $dtData['id'] . '">
                                                            <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][poisub_id]" class="form-control" value="' . $dtData['poisub_id'] . '">
                                                            <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][quantity_single]" class="form-control" value="' . $dtData['quantity_single'] . '">

                                                            <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][unit_id]" class="form-control" value="' . $dtData['unit_id'] . '">
                                                            <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][unit_parent_id]" class="form-control" value="' . $dtData['unit_parent_id'] . '">
                                                            <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][quantity_exchange]" class="form-control" value="' . $dtData['quantity_exchange'] . '">
                                                            
                                                            <input type="text" name="items[' . $pod_id . '][' . $counterSemiProduct . '][quantity_semi_product]" class="form-control quantity_semi_product text-center" onchange="totalSemiProducts()" value="' . formatNumber($quantity) . '">
                                                        </div>';
                                                    $tdActions = '<div class="td-quantity text-center"><i onclick="removeTrSemiProduct(this, \'' . $counterSemiProduct . '\')" class="pointer fa fa-remove text-danger"></i></div>';

                                                    $trItems = '<tr data-semiproduct="' . $dtData['product_id'] . '" class="group-tr" tr-counterSemiProduct="' . $counterSemiProduct . '">
                                                        <td class="" style="border-top: 1px solid #cedae6 !important; border-right: 0 !important; border-bottom: 0 !important;"><div class="flex-center">' . $tdImages . ' ' . $tdName . '</div></td>
                                                        <td class="text-center" style="border-top: 1px solid #cedae6 !important; border-left: 0 !important; border-right: 0 !important;  border-bottom: 0 !important;">' . $tdQuantity . '</td>
                                                        <td class="text-center" style="border-top: 1px solid #cedae6 !important; border-left: 0 !important;  border-bottom: 0 !important;">' . $tdActions . '</td>
                                                    </tr>';

                                                    if (!empty($dtData['subItems'])) {
                                                        $subItems = $dtData['subItems'];
                                                        $trSub = '';
                                                        $counterSub = 0;
                                                        foreach ($subItems as $kSub => $vSub) {
                                                            $images = $vSub['images'];
                                                            if (!$images) {
                                                                $images = base_url('assets/images/tnh/no_image.png');
                                                            }
                                                            $jsonWarehouses = $vSub['warehousePlan'];
                                                            $isWarehouses = $vSub['isWarehouses'];
                                                            $counterWarehouses++;
                                                            $quantitySub = $quantity * $vSub['quantity_single'];

                                                            $tdNumber = '<div class="td-numbers"></div>';
                                                            $tdImages = '<div class="td-images">
                                                                <div class="td-image" style="">
                                                                    <div class="preview_image" style="width: auto;">
                                                                        <div class="display-block contract-attachment-wrapper img">
                                                                            <div style="width:40px;">
                                                                                <a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">
                                                                                    <div class="">
                                                                                        <img src="' . $images . '" style="border-radius: 50%">
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';

                                                            $tdName = '
                                                                <div class="td-name text-left italic">' . $vSub['item_name'] . '(' . $vSub['item_code'] . ')</div>
                                                                <input type="hidden" class="form-control item_cs_id" value="' . $vSub['item_cs_id'] . '">
                                                            ';
                                                            $tdQuantity = '<div class="td-quantity text-center">
                                                                <input type="hidden" name="" class="form-control quantity_single" value="' . $vSub['quantity_single'] . '">
                                                                <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][quantity_materials]" class="form-control quantity_materials" readonly value=' . $vSub['quantity'] . '">
                                                                <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][unit_id]" class="form-control" readonly value="' . $vSub['unit_id'] . '">
                                                                <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][unit_parent_id]" class="form-control" readonly value="' . $vSub['unit_parent_id'] . '">
                                                                <div class="txt-quantity italic">' . formatNumber($quantitySub) . '</div>
                                                            </div>';

                                                            $trSub .= '
                                                                <tr class="not-tr not-border" tr-sub-materials="' . $counterSemiProduct . '" tr-counterSemiProduct="' . $counterSemiProduct . '" tr-counterSemiProduct-pod="' . $counterSemiProduct . '-' . $pod_id . '">
                                                                    <td class="" style="width: 200px; border-left: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                                                                        <div class="flex-center" style="padding-left: 40px;">
                                                                        ' . $tdImages . ' ' . $tdName . '
                                                                        </div>
                                                                        <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][quantity_exchange]" class="form-control" value="' . $vSub['quantity_exchange'] . '">
                                                                        <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][quantity_single]" class="form-control" value="' . $vSub['quantity_single'] . '">
                                                                        <input type="hidden" name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][item_cs_id]" class="form-control" value="' . $vSub['item_cs_id'] . '">
                                                                    </td>
                                                                    <td style="width: 100px; border-bottom: 0px !important;">' . $tdQuantity . '</td>
                                                                    <td style="border-right: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                                                                        <div class="">
                                                                            <input type="hidden" class="form-control counter-sub" value="' . $counterSub . '">
                                                                            <input type="hidden" class="form-control data-json-w-default" value="' . tnh_htmlentities($jsonWarehouses) . '">
                                                                            <a href="javascript:void(0)" ' . ($isWarehouses == 0 ? 'style="display:none;"' : '') . ' onclick="addRowSubWarehouses(this, \'' . $counterWarehouses . '\', \'' . $counterSemiProduct . '\', \'' . $vSub['quantity'] . '\', \'' . $vSub['item_cs_id'] . '\', \'' . $counterSub . '\',  \'' . $pod_id . '\')">
                                                                                <div class="panel-comment" style="padding: 5px;">
                                                                                        <div class="div-content">
                                                                                        <span class="fa fa-plus"></span>
                                                                                        &nbsp;<span class="bold text-primary">' . lang('tnh_add_warehouses') . '</span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                            <div class="view-add-warehouses">
                                                                                <div ' . ($isWarehouses == 0 ? 'style="display:none;"' : '') . '>
                                                                                <div class="flex-center">
                                                                                    <div style="width: 60%;">
                                                                                        <input type="hidden" class="form-control counterWarehouses" value="' . $counterWarehouses . '">
                                                                                        <input name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][warehouses_items][]" id="warehouses_items__' . $counterWarehouses . '" class="warehouses_items modal-select2" style="max-width: 200px;" data-placeholder="' . lang('tblwarehouse') . '">
                                                                                    </div>
                                                                                    <div style="width: 35%; padding: 3px;">
                                                                                        <input type="text" name="items[' . $pod_id . '][' . $counterSemiProduct . '][materials][' . $counterSub . '][quantity_items][]" class="form-control" value="' . formatNumber($quantitySub) . '" placeholder="' . lang('quantity') . '">
                                                                                    </div>
                                                                                    <div style="width: 5%;">
                                                                                        <span onclick="removeSubWarehouses(this)" class="fa fa-remove text-danger pointer"></span>
                                                                                    </div>
                                                                                </div>
                                                                                </div>
                                                                                <div>
                                                                                    ' . ($isWarehouses == 0 ? '<div class="mtop5 text-center"><div class="label label-danger border-radius-10px" style="padding: 5px 70px;">' . lang('tnh_out_of_stock') . '</div></div>' : '') . '
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            ';
                                                            $trItems .= $trSub;
                                                            $counterSub++;
                                                        }
                                                    }
                                                    $counterSemiProduct++;
                                                    ?>
                                                    <?= $trItems ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr data-semiproduct="${dtData.product_id}" tr-counterSemiProduct="${counterSemiProduct}">
                                                <td colspan="3">
                                                    <div class="text-danger text-left italic tag-not-stage"><?= lang('tnh_enough_semi_product_plan_materials') ?></div>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="v_warehouse_import" id="v_warehouse_import" class="form-control" value="<?= $warehouse_import ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" onclick="checkData()" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>

</div>
</div>
<?php echo form_close(); ?>
<script>
    var counterSemiProduct = 0;
    var counterWarehouses = <?= $counterWarehouses ?>;

    function checkData() {
        // if (!$('#warehouses_semi_product').val()) {
        //     alert_float('danger', '<?= lang('Vui lòng chọn kho hàng') ?>');
        // }
    }

    function totalSemiProducts() {
        tbSemiProducts = '.tb-handling-semi-products tbody tr:not("[class^=not-tr]")';
        nSemiProducts = $(tbSemiProducts).length;
        sttSemiProducts = 0;
        for (iSemiProducts = 0; iSemiProducts < nSemiProducts; iSemiProducts++) {
            sttSemiProducts++;
            elSemiProducts = $(tbSemiProducts)[iSemiProducts];
            $(elSemiProducts).find('.td-numbers').html(sttSemiProducts);
            quantity_semi_product = intVal($(elSemiProducts).find('.quantity_semi_product').val());
            c_counterSemiProduct = $(elSemiProducts).find('.counterSemiProduct').val();
            c_pod_id = $(elSemiProducts).find('.pod_id').val();
            // subMaterials = $('tr[tr-sub-materials="' + c_counterSemiProduct + '"]');
            subMaterials = $('tr[tr-countersemiproduct-pod="' + c_counterSemiProduct + '-' + c_pod_id + '"]');
            if (subMaterials.length > 0) {
                $.each(subMaterials, function(iSub, vSub) {
                    quantity_single = intVal($(vSub).find('.quantity_single').val());
                    quantity_material = quantity_single * quantity_semi_product;
                    $(vSub).find('.quantity_materials').val(formatDecimal(quantity_material));
                    $(vSub).find('.txt-quantity').html(tnhFormatNumber(quantity_material));
                });
            }
        }

        if (sttSemiProducts == 0) {
            trNoti = `
                <tr class="not-tr td-notification">
                    <td colspan="3" style="padding: 10px;">
                        <div class="text-center"><div class="label label-danger border-radius-10px" style="padding: 5px 70px;"><?= lang('Không có bán thành phẩm để hoàn thành') ?></div></div>
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
        tbSemiProductsW = '.tb-handling-semi-products tbody tr:not("[class^=not-tr]")';
        nSemiProductsW = $(tbSemiProductsW).length;
        use_productions_plan = $('#use_productions_plan').prop('checked');
        for (iSemiProductsW = 0; iSemiProductsW < nSemiProductsW; iSemiProductsW++) {
            elSemiProducts = $(tbSemiProductsW)[iSemiProductsW];
            c_counterSemiProduct = $(elSemiProducts).find('.counterSemiProduct').val();
            subMaterials = $('tr[tr-sub-materials="' + c_counterSemiProduct + '"]');
            if (subMaterials.length > 0) {
                $.each(subMaterials, function(iSub, vSub) {
                    c_counterWarehouses = $(vSub).find('.counterWarehouses').val();
                    item_cs_id = $(vSub).find('.item_cs_id').val();
                    json_w_default = $(vSub).find('.data-json-w-default').val();
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
                        $('#warehouses_items__' + c_counterWarehouses).val(0);
                        ajaxSelectParamsCallback('#warehouses_items__' + c_counterWarehouses, 'admin/manufactures/searchWarehousers', 0, {
                            item_cs_id: item_cs_id,
                            production_plan_id: '<?= $productions_plan['id'] ?>'
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

    function addRowSubWarehouses(_this, tempCounterWarehouses, tempCounterSemiProduct, quantity_sub_warehouses = 0, temItemId, tempCounterSub, tempPod_id) {
        cDiv = $(_this).closest('div');
        htmlSubWarehouses = rowSubWarehouses(tempCounterWarehouses, tempCounterSemiProduct, 0, tempCounterSub, tempPod_id);
        cDiv.find('.view-add-warehouses').prepend(htmlSubWarehouses);
        ajaxSelectParamsCallback('#warehouses_items__' + tempCounterWarehouses, 'admin/manufactures/searchWarehousers', 0, {
            item_cs_id: temItemId
        });
    }

    function rowSubWarehouses(tempCounterWarehouses, tempCounterSemiProduct, quantity_sub_warehouses = 0, tempCounterSub, tempPod_id) {
        return `
            <div class="flex-center">
                <div style="width: 60%;">
                    <input type="hidden" class="form-control counterWarehouses" value="${tempCounterWarehouses}">
                    <input name="items[${tempPod_id}][${tempCounterSemiProduct}][materials][${tempCounterSub}][warehouses_items][]" id="warehouses_items__${tempCounterWarehouses}" class="warehouses_items modal-select2" style="max-width: 200px;" data-placeholder="<?= lang('tblwarehouse') ?>">
                </div>
                <div style="width: 35%; padding: 3px;">
                    <input type="text" name="items[${tempPod_id}][${tempCounterSemiProduct}][materials][${tempCounterSub}][quantity_items][]" class="form-control" value="${tnhFormatNumber(quantity_sub_warehouses)}" placeholder="<?= lang('quantity') ?>">
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
                <input type="text" name="items[${counterSemiProduct}][quantity_semi_product]" class="form-control quantity_semi_product text-center" onchange="totalSemiProducts()" value="${tnhFormatNumber(quantity)}">
            </div>`;
            tdActions = `<div class="td-quantity text-center"><i onclick="removeTrSemiProduct(this, '${counterSemiProduct}')" class="pointer fa fa-remove text-danger"></i></div>`;

            trItems = `<tr data-semiproduct="${dtData.product_id}" class="group-tr" tr-counterSemiProduct="${counterSemiProduct}">
                <td class="" style="border-top: 1px solid #cedae6 !important; border-right: 0 !important; border-bottom: 0 !important;"><div class="flex-center">${tdImages} ${tdName}</div></td>
                <td class="text-center" style="border-top: 1px solid #cedae6 !important; border-left: 0 !important; border-right: 0 !important;  border-bottom: 0 !important;">${tdQuantity}</td>
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
                    trSub += `
                        <tr class="not-tr not-border" tr-sub-materials="${counterSemiProduct}" tr-counterSemiProduct="${counterSemiProduct}">
                            <td class="" style="width: 200px; border-left: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                                <div class="flex-center" style="padding-left: 40px;">
                                ${tdImages} ${tdName}
                                </div>
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][quantity_exchange]" class="form-control" value="${(value.quantity_exchange)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][quantity_single]" class="form-control" value="${(value.quantity_single)}">
                                <input type="hidden" name="items[${counterSemiProduct}][materials][${counterSub}][item_cs_id]" class="form-control" value="${(value.item_cs_id)}">
                            </td>
                            <td style="width: 100px; border-bottom: 0px !important;">${tdQuantity}</td>
                            <td style="border-right: 1px solid #cedae6 !important; border-bottom: 0px !important;">
                                <div class="">
                                    <input type="hidden" class="form-control counter-sub" value='${counterSub}'>
                                    <input type="hidden" class="form-control data-json-w-default" value='${jsonWarehouses}'>
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
                                            ${rowSubWarehouses(counterWarehouses, counterSemiProduct, value.quantity, counterSub)}
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
                trItems += `
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
                ajaxSelectParamsCallback('#search_materials_' + counterSemiProduct, 'admin/manufactures/searchMaterials', 0, {
                    production_plan_id: '<?= $productions_plan['id'] ?>'
                });
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
                    if ($('tr[data-semiproduct="' + response.items.product_id + '"]').length > 0) {
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
        $(document).ready(function() {
            totalSelect2Warehouses();
            totalSemiProducts();
        });

        init_tabs_scrollable();
        $('#warehouses_semi_product').select2();
        // loadALLSemiProduct();
        sp_pod_id = $('#sp_pod_id').val();
        ajaxSelectParamsCallback('#semi_products_in_order', 'admin/manufactures/searchSemiProductsPOD', 0, {
            pod_id: sp_pod_id
        });

        appValidateForm($('#handling_semi_products_mul'), {
            warehouses_semi_product: 'required'
        }, savehandlingSemiProducts);

        function savehandlingSemiProducts(form) {
            $('.add').attr('disabled', 'disabled');
            // var data = $(form).serialize();
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    // cache: false,
                    // contentType: false,
                    // processData: false,
                    data: {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        formData: form.serialize()
                        // formData: formData
                    },
                })
                .done(function(response) {
                    if (response.result == 1) {
                        alert_float('success', response.message);
                        $('.task-info').find('.active').click();
                        $('.modal-dialog .close').trigger('click');
                    } else if (response.result == 2) {
                        if (response.errorsWarehouses) {
                            divShowWarehousesErrors = '<div class="bold text-danger"><?= lang('NVL hoặc BTP không đủ để xuất kho') ?></div>';
                            $.each(response.errorsWarehouses, function(i, v) {
                                divShowWarehousesErrors += `
                                <div>
                                    <div style="float: left; padding-right: 10px;">
                                        <div class="td-image">
                                            <div class="preview_image" style="width: auto;">
                                                <div class="display-block contract-attachment-wrapper img">
                                                    <div style="width:30px;"><a href="${v.images}" data-lightbox="customer-profile" class="display-block mbot5">
                                                            <div class=""><img src="${v.images}" style="border-radius: 50%"></div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bold" style="position: relative; font-size: 11px; padding-left: 40px;">
                                        ${v.item_name}(${v.item_code})
                                    </div>
                                    <div style="font-size: 11px; padding-left: 40px;"><?= lang('tnh_warehouses') ?>: ${v.warehouse_name} - ${v.location_name}</div>
                                    <div style="font-size: 11px; padding-left: 40px;"><?= lang('tnh_quantity_lack') ?>: ${tnhFormatNumber(intVal(v.quantity_primary) - intVal(v.product_quantity))}</div>
                                </div>
                                `;
                            });
                            $('.show-errors-v').css({
                                'border': '1px dashed #ea152b',
                                'padding': '5px 2px'
                            });
                            $('.show-errors-v').html(divShowWarehousesErrors);
                        }
                        alert_float('danger', response.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    } else {
                        alert_float('danger', response.message);
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