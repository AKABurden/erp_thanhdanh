<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<div class="modal-dialog modal-lg" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_productions_plan') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('date') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($productions_plan['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_productions_plan') ?>: </div>
                            <div class="ml-at t-bold"><?= $productions_plan['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_planning_cycle') ?> </div>
                            <div class="ml-at t-bold"><?= _d($productions_plan['date_start']) ?> - <?= _d($productions_plan['date_end']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_options') ?> </div>
                            <div class="ml-at t-bold">
                                <?php if ($productions_plan['options1']) : ?>
                                    <span class="label label-primary"><?= lang('tnh_sales_orders') ?></span>
                                <?php endif ?>
                                <?php if ($productions_plan['options2']) : ?>
                                    <span class="label label-warning"><?= lang('tnh_business_plan') ?></span>
                                <?php endif ?>
                            </div>
                        </div>
                        <?php if ($productions_plan['options1']) : ?>
                            <div class="row-contro">
                                <div class="min-w-100"><?= lang('tnh_sales_orders') ?> </div>
                                <div class="ml-at t-bold pull-right text-right">
                                    <?php 
                                        $orders = explode('|||', $productions_plan['options1_reference_no']);
                                        $ctOrders = count($orders); 
                                    ?>
                                    <?php if (!empty($orders)) : ?>
                                        <?php foreach ($orders as $key => $value) : ?>
                                            <?php
                                                if ($key == 7) {
                                                    echo '<a class="accordion-toggle collapsed" data-toggle="collapse" style="position: absolute;
                                                    right: 10px;" href="#collapseOrders" role="button" aria-controls="collapseOrders"></a>';
                                                    echo '<div id="collapseOrders" class="collapse">';
                                                }    
                                            ?>
                                            <span><?= $value ?></span>,
                                            <?php
                                                if ($ctOrders - 1 == $key && $key > 6) {
                                                    echo '</div>';
                                                }
                                            ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if ($productions_plan['options2']) : ?>
                            <div class="row-contro">
                                <div><?= lang('tnh_business_plan') ?> </div>
                                <div class="ml-at t-bold pull-right text-right">
                                    <?php 
                                        $business_plan = explode('|||', $productions_plan['options2_reference_no']);
                                        $ctBusinessPlan = count($business_plan); 
                                    ?>
                                    <?php if (!empty($business_plan)) : ?>
                                        <?php foreach ($business_plan as $key => $value) : ?>
                                            <?php
                                                if ($key == 7) {
                                                    echo '<a class="accordion-toggle collapsed" data-toggle="collapse" style="position: absolute;
                                                    right: 10px;" href="#collapseBusinessPlan" role="button" aria-controls="collapseBusinessPlan"></a>';
                                                    echo '<div id="collapseBusinessPlan" class="collapse">';
                                                }    
                                            ?>
                                            <span><?= $value ?></span>,
                                            <?php
                                                if ($ctBusinessPlan - 1 == $key && $key > 6) {
                                                    echo '</div>';
                                                }
                                            ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="row-contro">
                            <div><?= lang('note') ?> </div>
                            <div class="ml-at t-bold"><?= $productions_plan['note'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>

                        <input type="radio" name="tabset" id="tab3" aria-controls="view-semi-products-ouside">
                        <label for="tab3"><?= lang('tnh_plan_semi_product') ?></label>

                        <input type="radio" name="tabset" id="tab4" aria-controls="view-materials">
                        <label for="tab4"><?= lang('tnh_plan_materials') ?></label>

                        <input type="radio" class="hide" name="tabset" id="tab6" aria-controls="view-general-purchasing">
                        <label for="tab6" class="hide"><?= lang('tnh_general_purchasing') ?></label>

                        <?php if ($this->input->post('view') != 'seen') : ?>
                            <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                            <label for="tab5"><?= lang('activity_log_puchases') ?></label>
                        <?php endif ?>
                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-view-plan" class="table table-hover dataTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                                <th class="text-center"><?= lang('Số đơn hàng') ?></th>
                                                <th class="text-center"><?= lang('image') ?></th>
                                                <th class="text-center"><?= lang('tnh_product_code') ?></th>
                                                <th class="text-center"><?= lang('tnh_product_name') ?></th>
                                                <th class="text-center"><?= lang('tnh_sample_cover_code') ?></th>
                                                <th class="text-center"><?= lang('unit') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_warehouses') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_need') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_reserve') ?></th>
                                                <th class="text-center"><?= lang('total_quantity') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bold">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                            <section id="view-semi-products-ouside" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-view-semi-products" class="table dataTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                                <th class="text-center" style="width: 80px;"><?= lang('image') ?></th>
                                                <th class="text-center" style="width: 200px;"><?= lang('tnh_semi_product_code') ?></th>
                                                <th class="text-center" style="width: 200px;"><?= lang('tnh_semi_product_name') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('unit') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_bu_hao') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_use') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_convert_w') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_inventory') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_lack') ?></th>
                                                <th class="text-center hide" style="width: 100px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $this->db->select('
                                                    tbl_products.images as images,
                                                    tbl_products.code as item_code,
                                                    tbl_products.name as item_name,
                                                    tbl_productions_plan_bom.item_id,
                                                    SUM(tbl_productions_plan_bom.quantity_primary) as quantity_primary,
                                                    tblunits.unit as unit_name,
                                                    tbl_productions_plan_bom.item_type as item_type,
                                                    tbl_products.conversion_quantity_unit as conversion_quantity_unit,
                                                    unit_manufactures.unit as unit_name_manufactures
                                                ', false);
                                                $this->db->from('tbl_productions_plan_bom');
                                                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_bom.item_id');
                                                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
                                                $this->db->join('tblunits unit_manufactures', 'unit_manufactures.unitid = tbl_products.conversion_unit', 'left');
                                                $this->db->where_in('tbl_productions_plan_bom.item_type', ['semi_products', 'semi_products_outside']);
                                                $this->db->where('tbl_productions_plan_bom.productions_plan_id', $id);
                                                $this->db->group_by('tbl_productions_plan_bom.item_id');
                                                $productions_plan_bom = $this->db->get()->result_array();
                                            ?>
                                            <?php if(!empty($productions_plan_bom)): ?>
                                                <?php foreach($productions_plan_bom as $key => $value): ?>
                                                    <?php
                                                        $images = $value['images'];
                                                        if (!empty($images)) {
                                                            $images = base_url('uploads/products/'.$images);
                                                        } else {
                                                            $images = base_url('assets/images/tnh/no_image.png');
                                                        }
                                                        $tdImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images.'" style="border-radius: 50%"></div></a></div></div></div></div>';
                                                        
                                                        $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($id, $value['item_id'], $value['item_type']);

                                                        $dtWarehouse = $this->manufactures_model->getWarehousesPlanBomNew($id, $value['item_id'], $value['item_type'], 'product');
                                                        $quantityW = (float)$dtWarehouse['product_quantity'];
                                                        $quantityNeed = number_unformat($value['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary']);
                                                        $quantityCW =  $quantityNeed *  $value['conversion_quantity_unit'];
                                                        // $quantityRest = $quantityNeed - $quantityW;
                                                        $quantityRest = $quantityCW - $quantityW;
                                                        if ($quantityRest < 0) $quantityRest = 0;
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= ++$key ?>
                                                        <div style="position: relative;"><a style="font-size: 25px; color: #0e3063; position: absolute;top: -22px; right: 0;" onclick="rowChildsPPM(this)" href="javascript:void(0)" class="fa fa-caret-right"></a></div>
                                                        </td>
                                                        <td>
                                                            <?= $tdImages ?>
                                                        </td>
                                                        <td><?= $value['item_code'] ?></td>
                                                        <td><?= $value['item_name'] ?></td>
                                                        <td class="text-center" ><?= $value['unit_name'] ?></td>
                                                        <td class="text-center" >
                                                            <?= ''//formatNumber($productionsPlanCompensation['quantity_primary']) ?>
                                                            <?= ceil($productionsPlanCompensation['quantity_primary']) ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= ''//formatNumber($quantityNeed, 0) ?>
                                                            <?= ceil($quantityNeed) ?>
                                                        </td>
                                                        <td class="text-center"><?= formatNumber($quantityNeed * $value['conversion_quantity_unit']) ?> <?= $value['unit_name_manufactures'] ?></td>
                                                        <td class="text-center"><?= formatNumber($quantityW) ?></td>
                                                        <td class="text-center">
                                                            <?= ''//formatNumber($quantityRest) ?>
                                                            <?= ceil($quantityRest) ?>
                                                        </td>
                                                        <td class="hide">
                                                        <table class="table" style="width: 100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_product_code') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_product_name') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_landscape_print_size') ?></th>
                                                                        <!-- <th class="text-center" style="background: #ddd !important;"><?= ''//lang('tnh_vertical_print_size') ?></th> -->
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_number_children_size') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_exchange_value') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_paper_exchange') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_quantity_npl_need') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_quantity_compensation') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_stage') ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $this->db->select('
                                                                            tbl_products.code as product_code,
                                                                            tbl_products.name as product_name,
                                                                            (ppb_materials.landscape_print_size) as landscape_print_size,
                                                                            (ppb_materials.vertical_print_size) as vertical_print_size,
                                                                            (ppb_materials.number_children_size) as number_children_size,
                                                                            (ppb_materials.paper_exchange) as paper_exchange,
                                                                            (ppb_materials.quantity_single) as quantity_single,
                                                                            (ppb_materials.quantity_primary) as quantity_primary,
                                                                            tbl_stages.name as stage_name,
                                                                            (ppb_materials.quantity_compensation_primary) as quantity_compensation_primary,
                                                                            (ppb_materials.quantity_compensation_sm_primary) as quantity_compensation_sm_primary,
                                                                        ', false);
                                                                        $this->db->from('tbl_productions_plan_bom ppb_primary');
                                                                        $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');

                                                                        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
                                                                        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
                                                                        $this->db->join('tbl_stages', 'tbl_stages.id = (ppb_materials.stage_item_id)', 'left');
                                                                        $this->db->where('ppb_primary.productions_plan_id', $productions_plan['id']);
                                                                        $this->db->where('ppb_primary.parent_id', 0);
                                                                        $this->db->where_in('(ppb_materials.item_type)', ['semi_products', 'semi_products_outside']);
                                                                        $this->db->where('(ppb_materials.item_id)', $value['item_id']);
                                                                        $productions_plan_bom = $this->db->get()->result_array();
                                                                    ?>
                                                                    <?php if(!empty($productions_plan_bom)): ?>
                                                                        <?php foreach($productions_plan_bom as $k => $val): ?>
                                                                            <tr>
                                                                                <td><?= $val['product_code'] ?></td>
                                                                                <td><?= $val['product_name'] ?></td>
                                                                                <td class="text-center"><?= ($val['landscape_print_size']) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['number_children_size']) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['quantity_single']) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['paper_exchange'], 0) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['quantity_primary'], 0) ?></td>
                                                                                <td class="text-center"><?= formatNumber(($val['quantity_compensation_primary'] + $val['quantity_compensation_sm_primary'])) ?></td>
                                                                                <td class="text-center"><?= $val['stage_name'] ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                            <?php
                                               
                                                $ppi = $this->manufactures_model->getProductionsPlanItemsView($id);
                                                $arrSemiProduct = [];
                                            ?>
                                            <?php if(!empty($ppi)): ?>
                                                <?php foreach($ppi as $key => $value): ?>
                                                    <?php
                                                        continue;
                                                        $images = $value['images'];
                                                        if (!empty($images)) {
                                                            $images = base_url('uploads/products/'.$images);
                                                        } else {
                                                            $images = base_url('assets/images/tnh/no_image.png');
                                                        }
                                                        $tdImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images.'" style="border-radius: 50%"></div></a></div></div></div></div>';
                                                        $quantity = $value['quantity_total_details'] + $value['quantity_reserve'];
                                                    ?>
                                                    <?php
                                                        $ppi_id = $value['id'];
                                                        $semi_products = $this->manufactures_model->getProductionsPlanBom($ppi_id, ['semi_products']);
                                                        if (empty($semi_products)) continue;

                                                        $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($id, $v['item_id'], 'semi_products');
                                                    ?>
                                                    <tr class="bg-group bold">
                                                        <td class="text-center"><?= ++$key ?></td>
                                                        <td>
                                                            <?= $tdImages ?>
                                                        </td>
                                                        <td><?= $value['item_code'] ?></td>
                                                        <td><?= $value['item_name'] ?></td>
                                                        <td class="text-center">
                                                            <?= $value['unit_name'] ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= formatNumber($quantity)  ?>
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    
                                                    <?php if(!empty($semi_products)): ?>
                                                        <?php foreach($semi_products as $k => $v): ?>
                                                            <?php
                                                                $info = $this->products_model->rowProduct($v['item_id']);
                                                                $images = $info['images'];
                                                                if (!empty($images)) {
                                                                    $images = base_url('uploads/products/'.$images);
                                                                } else {
                                                                    $images = base_url('assets/images/tnh/no_image.png');
                                                                }
                                                                $tdImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images.'" style="border-radius: 50%"></div></a></div></div></div></div>';
                                                                $tempW = null;
                                                                $quantityCurrent = 0;

                                                                $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($id, $v['item_id'], 'semi_products');

                                                                // $quantityPrimary = $v['quantity_primary'] + $v['quantity_compensation_primary'];
                                                                // $quantityRest = $v['quantity_primary'] + $v['quantity_compensation_primary'];

                                                                $quantityPrimary = $v['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary'];
                                                                $quantityRest = $v['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary'];



                                                                $cIndex = 'semi_products__'.$v['item_id'];
                                                                if (!empty($warehousePlan[$cIndex])) {
                                                                    $tempW = $warehousePlan[$cIndex];
                                                                    $quantityCurrent = $tempW['quantity'];
                                                                    if ($quantityCurrent > 0) {
                                                                        $tempQuantity = $quantityCurrent - $quantityPrimary;
                                                                        if ($tempQuantity > 0) {
                                                                            $quantityRest = 0;
                                                                        } else {
                                                                            $quantityRest = abs($tempQuantity);
                                                                            $tempQuantity = 0;
                                                                        }
                                                                        $warehousePlan[$cIndex]['quantity'] = $tempQuantity;
                                                                    } else {
                                                                        $quantityCurrent = 0;
                                                                    }

                                                                }
                                                            ?>
                                                            <tr>
                                                                <td style="border-right: 0px; border-top: 0px; border-bottom: 0px;"></td>
                                                                <td style="border: 0px;">
                                                                    <?= $tdImages ?>
                                                                </td>
                                                                <td style="border: 0px;"><?= $info['code'] ?></td>
                                                                <td style="border: 0px;"><?= $info['name'] ?></td>
                                                                <td class="text-center" style="border: 0px;"><?= $v['unit_name'] ?></td>
                                                                <td class="text-center" style="border: 0px;"><?= formatNumber($v['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary']) ?></td>
                                                                <td class="text-center" style="border: 0px;"><?= formatNumber($quantityCurrent) ?></td>
                                                                <td class="text-center" style="border-left: 0px; border-top: 0px; border-bottom: 0px;">
                                                                    <?= $quantityRest == 0 ? '<span class="label label-warning">'.lang('Đủ kho').'</span>' : formatNumber($quantityRest) ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                                if (!empty($quantityCurrent)) {
                                                                    $quantityUse = $v['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary'] - $quantityCurrent;
                                                                    if ($quantityUse >= 0) {
                                                                        $quantityUse = $quantityCurrent;
                                                                    } else {
                                                                        $quantityUse = $v['quantity_primary'] + $v['quantity_compensation_primary'];
                                                                    }
                                                                    $arrSemiProduct[$v['id']] = $quantityUse;
                                                                }
                                                            ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                            <section id="view-materials" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-view-materials" class="table table-hover dataTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                                <th class="text-center" style="width: 80px;"><?= lang('image') ?></th>
                                                <th class="text-center" style="width: 200px;"><?= lang('tnh_materials_code') ?></th>
                                                <th class="text-center" style="width: 200px;"><?= lang('tnh_materials_name') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_standard_unit') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_bu_hao') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_use') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_hold') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_inventory') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_lack') ?></th>
                                                <th class="text-center hide" style="width: 100px;"><?= lang('info') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $materialBomSemiProduct = $this->manufactures_model->getMaterialBomSemiProduct($arrSemiProduct);
                                            ?>
                                            <?php if (!empty($productionsPlanMaterials)) : ?>
                                                <?php foreach ($productionsPlanMaterials as $key => $value) : ?>
                                                    <?php
                                                        $images = $value['images'];
                                                        if (!empty($images)) {
                                                            $images = base_url('uploads/materials/'.$images);
                                                        } else {
                                                            $images = base_url('assets/images/tnh/no_image.png');
                                                        }
                                                        $tdImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images.'" style="border-radius: 50%"></div></a></div></div></div></div>';
                                                        $tIndex = 'materials__'.$value['item_id'];
                                                        $quantityWMaterial = !empty($materialBomSemiProduct[$tIndex]) ? $materialBomSemiProduct[$tIndex]['quantity'] : 0;

                                                        $tbTransferWarehouse = "(
                                                            SELECT
                                                                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
                                                            FROM tbltransfer_warehouse
                                                            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
                                                            WHERE tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse.productions_capacity_id = ".$id." AND tbltransfer_warehouse_detail.id_items = ".$value['item_id']."
                                                        )";
                                                        $dtKeep = $this->db->query($tbTransferWarehouse)->row_array();
                                                        $quantityKeep = (float)$dtKeep['quantity_net'];

                                                        // $value['quantity_primary'] = $value['quantity_primary'] + $value['quantity_compensation_primary'] + $value['quantity_compensation_sm_primary'] - $quantityWMaterial;
                                                        $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($id, $value['item_id'], 'materials');

                                                        // $value['quantity_primary'] = $value['quantity_primary'] + (float)$productionsPlanCompensation['quantity_primary'];
                                                        
                                                        $quantityPrimary = $value['quantity_primary'] + $productionsPlanCompensation['quantity_primary'];
                                                        $quantityW = number_unformat($quantityPrimary/$value['exchange_standard_unit'] * $value['exchange_unit']);
                                                    ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?= ++$key ?>
                                                            <div style="position: relative;"><a style="font-size: 25px; color: #0e3063; position: absolute;top: -22px; right: 0;" onclick="rowChildsPP(this)" href="javascript:void(0)" class="fa fa-caret-right"></a></div>
                                                        </td>
                                                        <td><?= $tdImages ?></td>
                                                        <td><?= $value['item_code'] ?></td>
                                                        <td><?= $value['item_name'] ?></td>
                                                        <td class="text-center"><?= $value['unit_name_stock'] ?></td>
                                                        <td class="text-center">
                                                            <?= 
                                                                //formatNumber($productionsPlanCompensation['quantity_primary']/$value['exchange_standard_unit'] * $value['exchange_unit'], 0) 
                                                                ceil($productionsPlanCompensation['quantity_primary']/$value['exchange_standard_unit'] * $value['exchange_unit']) 
                                                                // ceil($productionsPlanCompensation['quantity_compensation'])
                                                            ?>
                                                        </td>
                                                        <td class="text-center"><?= 
                                                            // formatNumber($quantityW, 0) 
                                                            ceil($quantityW) 
                                                        ?><div><?//= $quantityPrimary ?></div></td>
                                                        <td class="text-center"><?= formatNumber($quantityKeep) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['quantity_inventory']) ?></td>
                                                        <?php
                                                        $quantity_lack = (float)$quantityW - $quantityKeep - (float)$value['quantity_inventory'];
                                                        if ($quantity_lack < 0) $quantity_lack = 0;
                                                        ?>
                                                        <td class="text-center">
                                                            <?= 
                                                                // formatNumber($quantity_lack, 0) 
                                                                ceil($quantity_lack) 
                                                            ?>
                                                        </td>
                                                        <td class="hide">
                                                            <table class="table" style="width: 100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_product_code') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_product_name') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_landscape_print_size') ?></th>
                                                                        <!-- <th class="text-center" style="background: #ddd !important;"><?= ''//lang('tnh_vertical_print_size') ?></th> -->
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_number_children_size') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_exchange_value') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_paper_exchange') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_quantity_npl_need') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_quantity_compensation') ?></th>
                                                                        <th class="text-center" style="background: #ddd !important;"><?= lang('tnh_stage') ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        // $this->db->select('
                                                                        //     tbl_products.code as product_code,
                                                                        //     tbl_products.name as product_name,
                                                                        //     tbl_productions_plan_bom.landscape_print_size as landscape_print_size,
                                                                        //     tbl_productions_plan_bom.vertical_print_size as vertical_print_size,
                                                                        //     tbl_productions_plan_bom.number_children_size as number_children_size,
                                                                        //     tbl_productions_plan_bom.paper_exchange as paper_exchange,
                                                                        //     tbl_productions_plan_bom.quantity_single as quantity_single,
                                                                        //     tbl_productions_plan_bom.quantity_primary as quantity_primary,
                                                                        //     tbl_stages.name as stage_name,
                                                                        //     tbl_productions_plan_bom.quantity_compensation_primary as quantity_compensation_primary,
                                                                        //     tbl_productions_plan_bom.quantity_compensation_sm_primary as quantity_compensation_sm_primary,
                                                                        // ', false);
                                                                        // $this->db->from('tbl_productions_plan_bom');
                                                                        // $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_plan_bom.productions_plan_items_id', 'inner');
                                                                        // $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
                                                                        // $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_plan_bom.stage_item_id', 'left');
                                                                        // $this->db->where('tbl_productions_plan_bom.productions_plan_id', $productions_plan['id']);
                                                                        // $this->db->where('tbl_productions_plan_bom.item_type', 'materials');
                                                                        // $this->db->where('tbl_productions_plan_bom.item_id', $value['item_id']);
                                                                        // $productions_plan_bom = $this->db->get()->result_array();

                                                                        $this->db->select('
                                                                            tbl_products.code as product_code,
                                                                            tbl_products.name as product_name,
                                                                            (ppb_materials.landscape_print_size) as landscape_print_size,
                                                                            (ppb_materials.vertical_print_size) as vertical_print_size,
                                                                            (ppb_materials.number_children_size) as number_children_size,
                                                                            (ppb_materials.paper_exchange) as paper_exchange,
                                                                            (ppb_materials.quantity_single) as quantity_single,
                                                                            (ppb_materials.quantity_primary) as quantity_primary,
                                                                            tbl_stages.name as stage_name,
                                                                            (ppb_materials.quantity_compensation_primary) as quantity_compensation_primary,
                                                                            (ppb_materials.quantity_compensation_sm_primary) as quantity_compensation_sm_primary,
                                                                        ', false);
                                                                        $this->db->from('tbl_productions_plan_bom ppb_primary');
                                                                        $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');

                                                                        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
                                                                        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
                                                                        $this->db->join('tbl_stages', 'tbl_stages.id = (ppb_materials.stage_item_id)', 'left');
                                                                        $this->db->where('ppb_primary.productions_plan_id', $productions_plan['id']);
                                                                        $this->db->where('ppb_primary.parent_id', 0);
                                                                        $this->db->where('(ppb_materials.item_type)', 'materials');
                                                                        $this->db->where('(ppb_materials.item_id)', $value['item_id']);
                                                                        $productions_plan_bom = $this->db->get()->result_array();
                                                                    ?>
                                                                    <?php if(!empty($productions_plan_bom)): ?>
                                                                        <?php foreach($productions_plan_bom as $k => $val): ?>
                                                                            <tr>
                                                                                <td><?= $val['product_code'] ?></td>
                                                                                <td><?= $val['product_name'] ?></td>
                                                                                <td class="text-center"><?= ($val['landscape_print_size']) ?></td>
                                                                                <!-- <td class="text-center"><?= ''//formatNumber($val['vertical_print_size']) ?></td> -->
                                                                                <td class="text-center"><?= formatNumber($val['number_children_size']) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['quantity_single']) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['paper_exchange'], 0) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['quantity_primary']/$value['exchange_standard_unit'] * $value['exchange_unit'], 0) ?></td>
                                                                                <td class="text-center"><?= formatNumber(($val['quantity_compensation_primary'] + $val['quantity_compensation_sm_primary'])/$value['exchange_standard_unit'] * $value['exchange_unit'], 0) ?></td>
                                                                                <td class="text-center"><?= $val['stage_name'] ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php
                                                                        // $this->db->select('
                                                                        //     tbl_products.code as product_code,
                                                                        //     tbl_products.name as product_name,
                                                                        //     sm_material.landscape_print_size as landscape_print_size,
                                                                        //     sm_material.vertical_print_size as vertical_print_size,
                                                                        //     sm_material.number_children_size as number_children_size,
                                                                        //     sm_material.paper_exchange as paper_exchange,
                                                                        //     sm_material.quantity_single as quantity_single,
                                                                        //     sm_material.quantity_primary as quantity_primary,
                                                                        //     tbl_stages.name as stage_name,
                                                                        //     sm_material.quantity_compensation_primary as quantity_compensation_primary,
                                                                        //     sm_material.quantity_compensation_sm_primary as quantity_compensation_sm_primary,
                                                                        // ');
                                                                        // $this->db->from('tbl_productions_plan_bom smp');
                                                                        // $this->db->join('tbl_productions_plan_bom sm_material', 'sm_material.parent_id = smp.id');
                                                                        // $this->db->join('tbl_products', 'tbl_products.id = smp.item_id', 'inner');
                                                                        // $this->db->where('smp.productions_plan_id', $productions_plan['id']);
                                                                        // $this->db->where('smp.item_type', 'semi_products');
                                                                        // $this->db->where('sm_material.item_type', '	
                                                                        // materials');
                                                                        // $this->db->where('sm_material.item_id', $value['item_id']);
                                                                        // $this->db->join('tbl_stages', 'tbl_stages.id = sm_material.stage_item_id', 'left');
                                                                        // $productions_plan_bom_semi = $this->db->get()->result_array();

                                                                        $this->db->select('
                                                                            tbl_products.code as product_code,
                                                                            tbl_products.name as product_name,
                                                                            (ppb_materials.landscape_print_size) as landscape_print_size,
                                                                            (ppb_materials.vertical_print_size) as vertical_print_size,
                                                                            (ppb_materials.number_children_size) as number_children_size,
                                                                            (ppb_materials.paper_exchange) as paper_exchange,
                                                                            (ppb_materials.quantity_single) as quantity_single,
                                                                            (ppb_materials.quantity_primary) as quantity_primary,
                                                                            tbl_stages.name as stage_name,
                                                                            (ppb_materials.quantity_compensation_primary) as quantity_compensation_primary,
                                                                            (ppb_materials.quantity_compensation_sm_primary) as quantity_compensation_sm_primary,
                                                                        ', false);
                                                                        $this->db->from('tbl_productions_plan_bom ppb_semi');
                                                                        $this->db->join('tbl_productions_plan_bom ppb_ele', 'ppb_semi.id = (ppb_ele.parent_id)', 'inner');
                                                                        $this->db->join('tbl_productions_plan_bom ppb_materials ', '(ppb_ele.id) = (ppb_materials.parent_id)', 'inner');
                                                                        $this->db->join('tbl_products', 'tbl_products.id = (ppb_semi.item_id)', 'inner');
                                                                        $this->db->join('tbl_stages', 'tbl_stages.id = (ppb_materials.stage_item_id)', 'left');

                                                                        $this->db->where('(ppb_semi.productions_plan_id)', $productions_plan['id']);
                                                                        $this->db->where('(ppb_semi.item_type)', 'semi_products');
                                                                        $this->db->where('(ppb_materials.item_type)', 'materials');
                                                                        $this->db->where('(ppb_materials.item_id)', $value['item_id']);
                                                                        $productions_plan_bom_semi = $this->db->get()->result_array();
                                                                    ?>
                                                                    <?php if(!empty($productions_plan_bom_semi)): ?>
                                                                        <?php foreach($productions_plan_bom_semi as $k => $val): ?>
                                                                            <tr>
                                                                                <td><?= $val['product_code'] ?></td>
                                                                                <td><?= $val['product_name'] ?></td>
                                                                                <td class="text-center"><?= ($val['landscape_print_size']) ?></td>
                                                                                <!-- <td class="text-center"><?= ''//formatNumber($val['vertical_print_size']) ?></td> -->
                                                                                <td class="text-center"><?= formatNumber($val['number_children_size']) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['quantity_single']) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['paper_exchange'], 0) ?></td>
                                                                                <td class="text-center"><?= formatNumber($val['quantity_primary']/$value['exchange_standard_unit'] * $value['exchange_unit'], 0) ?></td>
                                                                                <td class="text-center"><?= formatNumber(($val['quantity_compensation_primary'] + $val['quantity_compensation_sm_primary'])/$value['exchange_standard_unit'] * $value['exchange_unit']) ?></td>
                                                                                <td class="text-center"><?= $val['stage_name'] ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="bold">
                                            <tr>
                                                <td></td>
                                                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="hide"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                            <section id="view-general-purchasing" class="tab-panel hide">
                                <div class="table-responsive">
                                    <table id="table-view-general-purchasing" class="table table-hover dataTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                                <th class="text-center"><?= lang('tnh_item_code') ?></th>
                                                <th class="text-center"><?= lang('tnh_item_name') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_use') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_exchange') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_hold') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_import') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_need_purchase') ?></th>
                                                <th style="width: 250px;" class="text-center"><?= lang('tnh_purchasing_process') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="bold">
                                            <tr>
                                                <td></td>
                                                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                            <?php if ($this->input->post('view') != 'seen') : ?>
                                <section id="view-activity-log" class="tab-panel">
                                    <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                        <?php
                                        $history = getActivityLogByObjId($productions_plan['id'], 'productions_plan');
                                        ?>
                                        <?php if (!empty($history)) : ?>
                                            <?php foreach ($history as $key => $value) : ?>
                                                <?php
                                                echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        ' . staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small') . '' . $value['staff_name'] . '
                                                    </div>
                                                    <div class="activity-time">
                                                        ' . time_ago($value['date']) . '<span class="activity-module">' . _l($value['type_parent_obj']) . '</span>
                                                    </div>
                                                    <div>
                                                        ' . $value['content'] . '
                                                    </div>
                                                </div>';
                                                ?>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </div>
                                </section>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($productions_plan['date_created']) ?></div>
                            </div>
                            <?php if($productions_plan['updated_by']): ?>
                                <div><?= lang('tnh_updated_by') ?>: <?= get_staff_full_name($productions_plan['updated_by']) ?></div>
                                <div><?= lang('tnh_date_updated') ?>: <?= _dt($productions_plan['date_updated']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="filter_productions_plan_id" id="filter_productions_plan_id" class="form-control" value="<?= $productions_plan['id'] ?>">
            <input type="hidden" name="filter_date_start" id="filter_date_start" class="form-control" value="<?= $productions_plan['date_start'] ?>">
            <input type="hidden" name="filter_date_end" id="filter_date_end" class="form-control" value="<?= $productions_plan['date_end'] ?>">
        </div>
        <div class="modal-footer">

            <input type="hidden" name="view_productions_plan_id" id="view_productions_plan_id" class="form-control" value="<?= $id ?>">

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    var oTableProductionsPlan = '';
    var paramsProductionsPlan = {
        'filter_productions_plan_id': '#filter_productions_plan_id',
        'filter_date_start': '#filter_date_start',
        'filter_date_end': '#filter_date_end'
    };

    var oTableProductionsPlanProceduce = '';
    var paramsProductionsPlanProceduce = {
        'filter_productions_plan_id': '#filter_productions_plan_id'
    };
    $(document).ready(function() {
        oTableProductionsPlan = tnhDatatable(
            '#table-view-plan', {
                // 'order': [[1, 'asc']],
                'ordering': false,
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [1, 10, 25, 50, 100, -1],
                    [1, 10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                // 'fixedHeader': {
                //     header: true,
                // },
                // scrollY: true,
                // // scrollY: "300px",
                // scrollX: true,
                // fixedColumns: {
                //     leftColumns: 3,
                // },
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getViewProductionsPlan') ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionsPlan) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionsPlan[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    // mainWrapperHeightFix();
                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '50px',
                        'className': 'text-center'
                    },
                    {
                        "targets": 1,
                        "name": 'reference_order',
                        'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            images = (data != null) ? site.base_url + "uploads/products/" + data + '?' : site.base_url + "assets/images/tnh/no_image.png";
                            return '<div class="preview_image" style="width: auto;">\
		                        <div class="display-block contract-attachment-wrapper img">\
		                            <div style="width:50px; margin: auto;">\
		                                <a href="' + images + '" data-lightbox="customer-profile" class="display-block mbot5">\
		                                    <div class="">\
		                                        <img src="' + images + '" style="border-radius: 50%" />\
		                                    </div>\
		                                </a>\
		                            </div>\
		                        </div>\
		                    </div>';
                        },
                        "targets": 2,
                        "name": 'images',
                        'width': '80px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data) return '';
                            data = data.split('|||');
                            return `<div style="word-break: break-all;">${data[0]}</div>${data[1] > 0 ? '<div class="text-danger">Dự phòng</div>' : ''}`;
                        },
                        "targets": 3,
                        "name": 'product_code',
                        'width': '150px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data) return '';
                            data = data.split('|||');
                            return `<div style="word-break: break-all;">${data[0]}</div>${data[1] > 0 ? '<div class="text-danger">Dự phòng</div>' : ''}`;
                        },
                        "targets": 4,
                        "name": 'product_name',
                        'width': '150px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data) return '';
                            return `<div style="word-break: break-all;">${data}</div>`;
                        },
                        "targets": 5,
                        "name": 'sample_cover_code',
                        'width': '100px'
                    },
                    {
                        "targets": 6,
                        "name": 'unit',
                        'width': '50px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 7,
                        "name": 'quantity_warehouses',
                        'width': '100px',
                        'visible': false,
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 8,
                        "name": 'quantity_total_details',
                        'width': '70px',
                        'className': 'text-center',
                        'visible': false,
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 9,
                        "name": 'quantity_reserve',
                        'width': '70px',
                        'className': 'text-center',
                        'visible': false
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 10,
                        "name": 'total_quantity',
                        'width': '70px',
                        'className': 'text-center',
                        'visible': true
                    },
                ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                    var grand_total = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        grand_total += intVal(aaData[i][10]);
                    }
                    var nCells = nRow.getElementsByTagName('td');
                    nCells[7].innerHTML = '<div class="text-center bold">' + tnhFormatMoney(grand_total) + '</div>';
                }
            }
        );

        oTableProductionsPlanProceduce = tnhDatatable(
            '#table-view-procedure', {
                'order': [
                    [1, 'asc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [1, 10, 25, 50, 100, -1],
                    [1, 10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getViewProductionsPlanProceduce') ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionsPlanProceduce) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionsPlanProceduce[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    // mainWrapperHeightFix();
                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '50px',
                        'className': 'text-center',
                        'sortable': false
                    },
                    {
                        "targets": 1,
                        "name": 'object_id',
                        'width': '230px'
                    },
                    {
                        "targets": 2,
                        "name": 'product_code',
                        'width': '150px'
                    },
                    {
                        "targets": 3,
                        "name": 'product_name',
                        'width': '150px'
                    },
                    {
                        "targets": 4,
                        "name": 'unit',
                        'width': '50px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 5,
                        "name": 'quantity_total_details',
                        'width': '100px'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 6,
                        "name": 'quantity_has_produced',
                        'width': '100px',
                        'visible': false
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 7,
                        "name": 'quantity_rest',
                        'width': '100px',
                        'visible': false
                    },
                ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity_total_details = 0,
                        quantity_has_produced = 0,
                        quantity_rest = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity_total_details += intVal(aaData[i][5]);
                        quantity_has_produced += intVal(aaData[i][6]);
                        quantity_rest += intVal(aaData[i][7]);
                    }
                    var nCells = nRow.getElementsByTagName('td');
                    nCells[5].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(quantity_total_details) + '</div>';
                }
            }
        );
    });
</script>

<script>
    var oTableViewSemiProducts = '';

    function loadInfoSemiProducts(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        return cData[10];
    }

    function rowChildsPPM(_this) {
        var tr = $(_this).closest('tr');
        var row = oTableViewSemiProducts.row(tr);
        if (row.child.isShown()) {
            $(_this).removeClass('fa-caret-down');
            $(_this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(_this).removeClass('fa-caret-right');
            $(_this).addClass('fa-caret-down');
            row.child(loadInfoSemiProducts(row.data())).show();
            tr.addClass('shown');
        }
    }

    $(document).ready(function() {
        oTableViewSemiProducts = $('#table-view-semi-products').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            'ordering': false,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                // var api = this.api(),
                //     data;
                // pageTotalQuantity = api
                //     .column(4, {
                //         page: 'current'
                //     })
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);

                // $(api.column(4).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');

                // pageTotalQuantity = api
                //     .column(5, {
                //         page: 'current'
                //     })
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);

                // $(api.column(5).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');

                // pageTotalQuantity = api
                //     .column(6, {
                //         page: 'current'
                //     })
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);

                // $(api.column(6).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');
            }
        });
    });

    var oTableViewMaterials = '';

    function loadInfoMaterials(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        return cData[10];
    }

    function rowChildsPP(_this) {
        var tr = $(_this).closest('tr');
        var row = oTableViewMaterials.row(tr);
        if (row.child.isShown()) {
            $(_this).removeClass('fa-caret-down');
            $(_this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(_this).removeClass('fa-caret-right');
            $(_this).addClass('fa-caret-down');
            row.child(loadInfoMaterials(row.data())).show();
            tr.addClass('shown');
        }
    }

    $(document).ready(function() {
        oTableViewMaterials = $('#table-view-materials').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageTotalQuantity = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(6).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');

                pageTotalQuantity = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(7).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');

                pageTotalQuantity = api
                    .column(8, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(8).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');

                pageTotalQuantityT = api
                    .column(9, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(9).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantityT) + '</div>');
            }
        });
    });
</script>

<script>
    var oTablePurchaseGeneral = '';
    var fnserverparamsPurchaseGeneral = {
        view_productions_plan_id: "#view_productions_plan_id",
    };

    $(document).ready(function() {
        oTablePurchaseGeneral = tnhInitDataTable('#table-view-general-purchasing', '<?= site_url('admin/manufactures/getPurchasesGeneral') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getPurchasesGeneral') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsPurchaseGeneral) {
                        d[key] = $(fnserverparamsPurchaseGeneral[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    $('#table-view-general-purchasing tfoot tr td:nth-child(4)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantity)}</div>`);
                    $('#table-view-general-purchasing tfoot tr td:nth-child(5)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityPrimary)}</div>`);
                    $('#table-view-general-purchasing tfoot tr td:nth-child(6)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityTransfer)}</div>`);
                    $('#table-view-general-purchasing tfoot tr td:nth-child(7)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityImport)}</div>`);
                    $('#table-view-general-purchasing tfoot tr td:nth-child(8)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityNeed)}</div>`);
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "targets": 6,
                    'searchable': false,
                    'sortable': false
                },
                {
                    "targets": 8,
                    'searchable': false,
                    'sortable': false
                },
            ]
        })
    });
</script>