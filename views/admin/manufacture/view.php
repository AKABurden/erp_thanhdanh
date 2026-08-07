<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_manufactures') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($manufactures['date']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Số phiếu xả') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= ($manufactures['reference_no']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Số LSX chi tiết') ?>: </span>
                            <span class="bold font-medium-xs lead-name">
                                <?php
                                    $dtPod = get_table_where('tbl_productions_orders_details', ['id' => $manufactures['id_production_detail']], '', 'row_array');
                                    if (!empty($dtPod)) {
                                        echo $dtPod['reference_no'];
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_product_code') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= ($pod['item_code']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_product_name') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= ($pod['item_name']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('quantity') ?> sản phẩm: </span>
                            <span class="bold font-medium-xs lead-name"><?= formatNumber($pod['quantity']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content second hide">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('status') ?>: </span>
                            <span class="bold font-medium-xs lead-name"></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $manufactures['note'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>
                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <table id="table-items" class="table table-hover dont-responsive-table table-tbitems dataTable " style="max-height: 400px !important;">
                                    <thead>
                                        <th class="text-center" style="width: 50px;">
                                            <?= lang('tnh_numbers') ?>
                                        </th>
                                        <th class="text-center" style="background: orange !important;"><?= lang('tnh_material_code') ?></th>
                                        <th class="text-center" style="background: orange !important;"><?= lang('tnh_material_name') ?></th>
                                        <th class="text-center" style="background: orange !important;"><?= lang('tnh_number_paper') ?></th>
                                        <th class="text-center" style="background: orange !important;"><?= lang('tnh_height_cm') ?></th>
                                        <th class="text-center" style="background: orange !important;"><?= lang('tnh_total_height_cm') ?></th>
                                        <th class="text-center"><?= lang('tnh_landscape_print_size') ?></th>
                                        <!-- <th class="text-center"><?= ''//lang('tnh_vertical_print_size') ?></th> -->
                                        <th class="text-center"><?= lang('tnh_exchange_value') ?></th>
                                        <th class="text-center"><?= lang('tnh_paper_exchange_unit_paper') ?></th>
                                        <!-- <th class="text-center"><?= ''//lang('tnh_quantity_compensation') ?></th> -->
                                        <th class="text-center" style="width: 100px;"><?= lang('note') ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $manufactures_items = $this->manufacture_model->getManufacturesItems($id);
                                        $counter = 0;
                                        $stt = 0;
                                        ?>
                                        <?php if (!empty($manufactures_items)) : ?>
                                            <?php foreach ($manufactures_items as $key => $value) : ?>
                                                <?php
                                                $warehouse = $this->site_model->rowWarehouseById($value['warehouse_id']);
                                                $location = $this->site_model->rowLocationWarehouseById($value['location_id']);
                                                $type_item = $value['type_items'];
                                                $items_id = $value['item_id'];
                                                // $info = $this->items_model->rowItems($value['item_id']);
                                                $images = base_url('assets/images/tnh/no_image.png');

                                                if ($type_item == "products") {
                                                    $info = $this->products_model->rowProduct($items_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    if (!empty($info['images'])) {
                                                        $images = base_url('uploads/products/' . $info['images']);
                                                    }
                                                    $model = $info['model'];
                                                } elseif ($type_item == "items") {
                                                    $info = $this->items_model->rowItems($items_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit']);
                                                    if (!empty($info['avatar'])) {
                                                        $images = base_url($info['avatar']);
                                                    }
                                                } elseif ($type_item == "materials") {
                                                    $info = $this->items_model->rowMaterial($items_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    if (!empty($info['images'])) {
                                                        $images = base_url('uploads/materials/' . $info['images']);
                                                    }
                                                } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                                                    $info = $this->tools_supplies_model->rowToolsSupplies($items_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    if (!empty($info['avatar'])) {
                                                        $images = base_url('uploads/tools_supplies/' . $info['images']);
                                                    }
                                                }

                                                $tdNumber = '<td class="text-center">
                                                    ' . ++$stt . '
                                                </td>';

                                                $tdCode = '<td><div class="td-code">' . $info['code'] . '</div></td>';
                                                $tdName = '<td><div class="td-item-name">' . $info['name'] . '</div></td>';
                                                
                                                $tdLandscapePrintSize = '<td class="tdLandscapePrintSize text-center">'.($value['landscape_print_size']).'</td>';
                                                $tdVerticalPrintSize = '<td class="tdVerticalPrintSize text-center">'.formatNumber($value['vertical_print_size']).'</td>';
                                                $tdNumberChildrenSize = '<td class="tdNumberChildrenSize text-center">'.formatNumber($value['number_children_size']).'</td>';
                                                $tdExchangeValue = '<td class="tdExchangeValue text-center">'.formatNumber($value['quantity_single']).'</td>';
                                                $tdPaperExchange = '<td class="tdPaperExchange text-center">'.formatNumber($value['paper_exchange']).'</td>';
                                                $quantity_compensation = $value['quantity_compensation'] + $value['quantity_compensation_sm'];
                                                $tdQuantityCompensation = '<td class="tdQuantityCompensation text-center">'.formatNumber($quantity_compensation).'</td>';

                                                $tdQuantityNeed = '<td class="tdQuantityNeed text-center">'.formatNumber($value['quantity'] + $quantity_compensation, 0).'</td>';
                                                $tdHeight = '<td class="tdHeight text-center">'.$value['height'].'</td>';
                                                $tdTotalHeight = '<td class="tdTotalHeight text-center">'.formatNumber($value['total_height']).'</td>';
										
                                                $tdNote = '<td>
                                                        ' . $value['note_item'] . '
                                                    </td>';
                                                ?>
                                                <tr>
                                                    <?= $tdNumber ?>
                                                    <?= $tdCode ?>
                                                    <?= $tdName ?>
                                                    <?= $tdQuantityNeed ?>
                                                    <?= $tdHeight ?>
                                                    <?= $tdTotalHeight ?>
                                                    <?= $tdLandscapePrintSize ?>
                                                    <?= ''//$tdVerticalPrintSize ?>
                                                    <?= ''//$tdNumberChildrenSize ?>
                                                    <?= $tdExchangeValue ?>
                                                    <?= $tdPaperExchange ?>
                                                    <?= ''//$tdQuantityCompensation ?>
                                                    <?= $tdNote ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </section>
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
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($manufactures['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($manufactures['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($manufactures['updated_by'])) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= get_staff_full_name($manufactures['updated_by']) ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($manufactures['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
</script>