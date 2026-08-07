<?php echo form_open('admin/Handling_price/handlingPriceQuotes/', array('id' => 'handling-price')); ?>
<style>
    .td-white-red {
        background: white !important;
        color: red !important;
        border: 1px solid #cedae6 !important;
    }
</style>
<?php
$item_code = '';
$item_name = '';
if ($type == "quotes") {
    $this->db->select('
            tbl_quote_items.type_item as type_item,
            tbl_quote_items.item_id as item_id,
            tbl_quote_items.quantity as quantity,
            tbl_quote_items.unit_price as unit_price,
            tbl_quote_items.data_price_json as data_price_json
        ', false);
    $this->db->from('tbl_quote_items');
    $this->db->where('tbl_quote_items.id', $object_item_id);
    $item = $this->db->get()->row_array();
    if (!empty($item)) {
        $type_item = $item['type_item'];
        $item_id = $item['item_id'];
        $products = $this->products_model->rowProduct($item_id);
        $item_code = $products['code'];
        $item_name = $products['name'];
    }
}
$arrDataJson = !empty($item['data_price_json']) ? json_decode($item['data_price_json'], true) : null;

?>
<style>
    .modal-price-list table tr th {
        border: 1px solid #dcdcdc !important;
    }
</style>
<div class="modal-dialog modal-lg modal-price-list" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_list_price') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_product_code') ?>: </div>
                            <div class="ml-at t-bold"><?= $item_code ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_product_name') ?>: </div>
                            <div class="ml-at t-bold"><?= $item_name ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="2"><?= lang('Qui Cách SP In Cm') ?></th>
                                <th class="text-center"><?= lang('Chừa Biên Bo Góc') ?></th>
                                <th class="text-center"><?= lang('Chừa Biên Vuông Góc') ?></th>
                                <th class="text-center"><?= lang('Chừa Biên Vuông Tròn') ?></th>
                                <th class="text-center" colspan="2" style="width: 250px;"><?= lang('Qui Cách SP Tính Giá') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= lang('Height') ?></td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['height']) ? $arrDataJson['height'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['corner_boundary_height']) ? $arrDataJson['corner_boundary_height'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['perpendicular_border_height']) ? $arrDataJson['perpendicular_border_height'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['round_square_border_height']) ? $arrDataJson['round_square_border_height'] : '' ?>
                                </td>
                                <td class="td-product-calculation-height text-center">
                                    <?= !empty($arrDataJson['product_calculation_height']) ? $arrDataJson['product_calculation_height'] : '' ?>
                                </td>
                                <td class="td-product-calculation-height-width text-center" rowspan="2"><?= !empty($arrDataJson['product_calculation_height_width']) ? $arrDataJson['product_calculation_height_width'] : '' ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('Width') ?></td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['width']) ? $arrDataJson['width'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['corner_boundary_width']) ? $arrDataJson['corner_boundary_width'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['perpendicular_border_width']) ? $arrDataJson['perpendicular_border_width'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['round_square_border_width']) ? $arrDataJson['round_square_border_width'] : '' ?>
                                </td>
                                <td class="td-product-calculation-width text-center">
                                    <?= !empty($arrDataJson['product_calculation_width']) ? $arrDataJson['product_calculation_width'] : '' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="99" class="text-center"><?= lang('Dàn Trang') ?></th>
                            </tr>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center"><?= lang('Chừa Nhíp In') ?></th>
                                <th class="text-center"><?= lang('Chừa Boong Cắt Bế') ?></th>
                                <th class="text-center"><?= lang('Khổ NVL Tính Giá') ?></th>
                                <th class="text-center"><?= lang('Qui Cách SP Tính Giá') ?></th>
                                <th class="text-center"><?= lang('Số Sản Phẩm Ngang-Dọc') ?></th>
                                <th class="text-center"><?= lang('Tổng Số Con /Tờ') ?></th>
                            </tr>
                            <tr>
                                <th class="text-center td-white-red" style="width: 100px;"></th>
                                <th class="text-center td-white-red" style="width: 130px;"><?= lang('A1') ?></th>
                                <th class="text-center td-white-red"><?= lang('A2') ?></th>
                                <th class="text-center td-white-red"><?= lang('A3') ?></th>
                                <th class="text-center td-white-red"><?= lang('A') ?></th>
                                <th class="text-center td-white-red"><?= lang('B') ?></th>
                                <th class="text-center td-white-red"><?= lang('SPN-SPD') ?></th>
                                <th class="text-center td-white-red"><?= lang('S=SPNxSPD') ?></th>
                            </tr>
                            <tr>
                                <th class="text-center td-white-red"></th>
                                <th class="text-center td-white-red" colspan="4"><?= lang('A=A1-(A2+A3)') ?></th>
                                <th class="text-center td-white-red" colspan="2"><?= lang('SP=A/B Làm Tròn') ?></th>
                                <th class="text-center td-white-red"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= lang('Height') ?></td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['height_layout']) ? $arrDataJson['height_layout'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['height_layout_print_tweezers']) ? $arrDataJson['height_layout_print_tweezers'] : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['height_layout_boong_cut']) ? $arrDataJson['height_layout_boong_cut'] : '' ?>
                                </td>
                                <td class="td-height_layout_material_size text-center">
                                    <?= !empty($arrDataJson['height_layout_material_size']) ? $arrDataJson['height_layout_material_size'] : '' ?>
                                </td>
                                <td class="td-height_layout_mode text-center">
                                    <?= !empty($arrDataJson['height_layout_mode']) ? $arrDataJson['height_layout_mode'] : '' ?>
                                </td>
                                <td class="td-height_layout_quantity text-center">
                                    <?= !empty($arrDataJson['height_layout_quantity']) ? round($arrDataJson['height_layout_quantity']) : '' ?>
                                </td>
                                <td class="height_layout_total_quantity text-center" rowspan="2">
                                    <?= !empty($arrDataJson['height_layout_total_quantity']) ? round($arrDataJson['height_layout_total_quantity']) : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Width') ?></td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['width_layout']) ? ($arrDataJson['width_layout']) : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['width_layout_print_tweezers']) ? ($arrDataJson['width_layout_print_tweezers']) : '' ?>
                                </td>
                                <td class="text-center">
                                    <?= !empty($arrDataJson['width_layout_boong_cut']) ? ($arrDataJson['width_layout_boong_cut']) : '' ?>
                                </td>
                                <td class="td-width_layout_material_size text-center">
                                    <?= !empty($arrDataJson['width_layout_material_size']) ? ($arrDataJson['width_layout_material_size']) : '' ?>
                                </td>
                                <td class="td-width_layout_mode text-center">
                                    <?= !empty($arrDataJson['width_layout_mode']) ? ($arrDataJson['width_layout_mode']) : '' ?>
                                </td>
                                <td class="td-width_layout_quantity text-center">
                                    <?= !empty($arrDataJson['width_layout_quantity']) ? round($arrDataJson['width_layout_quantity']) : '' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <a href="#facade" aria-controls="facade" role="tab" data-toggle="tab"><?= lang('tnh_facade') ?></a>
                    <table id="table-items-stages" class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="99" class="text-center">Công đoạn trước in</th>
                            </tr>
                            <tr>
                                <th class="text-center"><?= lang('tnh_materials') ?></th>
                                <th class="text-center"><?= lang('tnh_stage') ?></th>
                                <th class="text-center"><?= lang('Đơn vị tính') ?></th>
                                <th class="text-center"><?= lang('Qui cách') ?></th>
                                <th class="text-center" style="width: 120px;"><?= lang('SL màu in') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Thiết bị') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Loại NPL') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Định mức BOM') ?></th>
                                <!-- <th class="text-center" style="width: 150px;"><?= lang('Số lần vận hành') ?></th> -->
                                <th class="text-center" style="width: 150px;"><?= lang('Đơn giá NVL') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Thành tiền/Tờ') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($arrDataJson['ItemsPrice'])) : ?>
                                <?php foreach ($arrDataJson['ItemsPrice'] as $key => $value) : ?>
                                    <tr>
                                        <?php
                                        $type_price = $value['type_price'];
                                        $item_id_price = $value['item_id_price'];
                                        $stage_id_price = $value['stage_id_price'];
                                        $dtItem = [];
                                        $dtStages = $this->handling_price_model->getItemsStagesPriceQuotes($stage_id_price);
                                        if ($type_price == "materials") {
                                            $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                                        } else if ($type_price == "stages") {
                                            $dtItem = $this->handling_price_model->getItemsStagesPriceQuotes($item_id_price);
                                        }
                                        $tdCategoryStages = '<td>
                                                            <input type="hidden" name="type_price[]" class="form-control type_price" value="' . $type_price . '">
                                                            <input type="hidden" name="item_id_price[]" class="form-control item_id_price" value="' . $item_id_price . '">
                                                            <input type="hidden" name="stage_id_price[]" class="form-control stage_id_price" value="' . $stage_id_price . '">
                                                            <div>' . ($type_price == 'materials' ? $dtItem['name'] . '(' . $dtItem['code'] . ')' : '') . '</div>
                                                        </td>';

                                        $tdColor = '<td class="text-center">
                                                            ' . formatNumber($value['quantity_color']) . '
                                                        </td>';
                                        $tdStages = '<td class="text-center">' . $dtStages['code'] . '</td>';
                                        $tdUnits = '<td><div class="text-center">' . $dtItem['unit_name'] . '</div></td>';
                                        $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                                        $tdNumberOperate = '<td class="text-center">
                                                            ' . formatNumber($value['number_operate']) . '
                                                        </td>';

                                        $dtMachine = $this->handling_price_model->rowMachines((!empty($value['machine']) ? $value['machine'] : ''));
                                        $tdMachine = '<td class="text-center">
                                            ' . (!empty($dtMachine['name']) ? $dtMachine['name'] : '') . '
                                        </td>';
                                        $tdTypeNPL = '<td class="text-center">
                                            ' . (!empty($value['type_npl']) ? $value['type_npl'] : '') . '
                                        </td>';
                                        $tdQuotaBOM = '<td class="text-center">
                                            ' . (!empty($value['quota_bom']) ? formatNumber($value['quota_bom']) : '') . '
                                        </td>';

                                        $tdPriceAbout = '<td class="text-center">
                                                            ' . formatMoney($value['price_about']) . '
                                                        </td>';
                                        $tdTotalSheet = '<td class="td-total-sheet text-right">
                                                            ' . formatMoney($value['total_sheet']) . '
                                                        </td>';

                                        echo '<tr>
                                            ' . $tdCategoryStages . '
                                            ' . $tdStages . '
                                            ' . $tdUnits . '
                                            ' . $tdMode . '
                                            ' . $tdColor . '
                                            ' . $tdMachine . '
                                            ' . $tdTypeNPL . '
                                            ' . $tdQuotaBOM . '
                                            ' . $tdPriceAbout . '
                                            ' . $tdTotalSheet . '
                                        </tr>';
                                        ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bold">
                                <td class="text-center"></td>
                                <td class="text-center"><?= lang('tnh_total') ?></td>
                                <td class="text-center text-danger" colspan="7"><?= lang('GT=sum tổng') ?></td>
                                <td class="text-right grand-total-sheet text-danger"><?= !empty($arrDataJson['grandTotalSheet']) ? formatMoney($arrDataJson['grandTotalSheet']) : '' ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-6">
                    <a href="#backside" aria-controls="backside" role="tab" data-toggle="tab"><?= lang('tnh_backside') ?></a>
                    <table id="table-items-stages" class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="99" class="text-center">Công đoạn trước in (sau)</th>
                            </tr>
                            <tr>
                                <th class="text-center"><?= lang('tnh_materials') ?></th>
                                <th class="text-center"><?= lang('tnh_stage') ?></th>
                                <th class="text-center"><?= lang('Đơn vị tính') ?></th>
                                <th class="text-center"><?= lang('Qui cách') ?></th>
                                <th class="text-center" style="width: 120px;"><?= lang('SL màu in') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Thiết bị') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Loại NPL') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Định mức BOM') ?></th>
                                <!-- <th class="text-center" style="width: 150px;"><?= lang('Số lần vận hành') ?></th> -->
                                <th class="text-center" style="width: 150px;"><?= lang('Đơn giá NVL') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Thành tiền/Tờ') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($arrDataJson['itemsPriceBackside'])) : ?>
                                <?php foreach ($arrDataJson['itemsPriceBackside'] as $key => $value) : ?>
                                    <tr>
                                        <?php
                                        $type_price = $value['type_price_backside'];
                                        $item_id_price = $value['item_id_price_backside'];
                                        $stage_id_price = $value['stage_id_price_backside'];
                                        $dtItem = [];
                                        $dtStages = $this->handling_price_model->getItemsStagesPriceQuotes($stage_id_price);
                                        if ($type_price == "materials") {
                                            $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                                        } else if ($type_price == "stages") {
                                            $dtItem = $this->handling_price_model->getItemsStagesPriceQuotes($item_id_price);
                                        }
                                        $tdCategoryStages = '<td>
                                                            <input type="hidden" name="type_price[]" class="form-control type_price" value="' . $type_price . '">
                                                            <input type="hidden" name="item_id_price[]" class="form-control item_id_price" value="' . $item_id_price . '">
                                                            <input type="hidden" name="stage_id_price[]" class="form-control stage_id_price" value="' . $stage_id_price . '">
                                                            <div>' . ($type_price == 'materials' ? $dtItem['name'] . '(' . $dtItem['code'] . ')' : '') . '</div>
                                                        </td>';

                                        $tdStages = '<td class="text-center">' . $dtStages['code'] . '</td>';
                                        $tdUnits = '<td><div class="text-center">' . $dtItem['unit_name'] . '</div></td>';
                                        $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                                        $tdColor = '<td class="text-center">
                                                            ' . formatNumber($value['quantity_color_backside']) . '
                                                        </td>';
                                        $tdNumberOperate = '<td class="text-center">
                                                            ' . formatNumber($value['number_operate_backside']) . '
                                                        </td>';

                                        $dtMachine = $this->handling_price_model->rowMachines((!empty($value['machine_backside']) ? $value['machine_backside'] : ''));
                                        $tdMachine = '<td class="text-center">
                                            ' . (!empty($dtMachine['name']) ? $dtMachine['name'] : '') . '
                                        </td>';
                                        $tdMachine = '<td class="text-center">
                                            ' . (!empty($value['machine_backside']) ? $value['machine_backside'] : '') . '
                                        </td>';
                                        $tdTypeNPL = '<td class="text-center">
                                            ' . (!empty($value['type_npl_backside']) ? $value['type_npl_backside'] : '') . '
                                        </td>';
                                        $tdQuotaBOM = '<td class="text-center">
                                            ' . (!empty($value['quota_bom_backside']) ? formatNumber($value['quota_bom_backside']) : '') . '
                                        </td>';

                                        $tdPriceAbout = '<td class="text-center">
                                                            ' . formatMoney($value['price_about_backside']) . '
                                                        </td>';
                                        $tdTotalSheet = '<td class="td-total-sheet text-right">
                                                            ' . formatMoney($value['total_sheet_backside']) . '
                                                        </td>';

                                        echo '<tr>
                                            ' . $tdCategoryStages . '
                                            ' . $tdStages . '
                                            ' . $tdUnits . '
                                            ' . $tdMode . '
                                            ' . $tdColor . '
                                            ' . $tdMachine . '
                                            ' . $tdTypeNPL . '
                                            ' . $tdQuotaBOM . '
                                            ' . $tdPriceAbout . '
                                            ' . $tdTotalSheet . '
                                        </tr>';
                                        ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bold">
                                <td class="text-center"></td>
                                <td class="text-center"><?= lang('tnh_total') ?></td>
                                <td class="text-center text-danger" colspan="7"><?= lang('GT=sum tổng') ?></td>
                                <td class="text-right grand-total-sheet text-danger"><?= !empty($arrDataJson['grandTotalSheet']) ? formatMoney($arrDataJson['grandTotalSheet']) : '' ?></td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
            <div class="row hide">
                <div class="col-md-12">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#facade" aria-controls="facade" role="tab" data-toggle="tab"><?= lang('tnh_facade') ?></a>
                            </li>
                            <li role="presentation">
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-content" style="border-bottom: 1px solid red; border-top: 1px solid red; padding: 15px;">
                                <div role="tabpanel" class="tab-pane active" id="facade">

                                </div>
                                <div role="tabpanel" class="tab-pane" id="backside">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="table-items-products" class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="99" class="text-center">Công đoạn sau in</th>
                            </tr>
                            <tr>
                                <th class="text-center"><?= lang('Hạng mục tính giá') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Dài/Cao') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Rộng/Ngang') ?></th>
                                <th class="text-center"><?= lang('Qui cách') ?></th>
                                <th class="text-center"><?= lang('QC Vận Hành') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Số Lần Xả/Vận Hành') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Số mặt in') ?><div class="text-danger">(<?= lang('1: mặt trước, 2: mặt sau, 3: mặt trước và sau') ?>)</div>
                                </th>
                                <th class="text-center" style="width: 150px;"><?= lang('Đơn Giá CĐ') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Thành tiền') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counterItemsProducts = 0;
                            ?>
                            <?php if (!empty($arrDataJson['itemsStagesProducts'])) : ?>
                                <?php foreach ($arrDataJson['itemsStagesProducts'] as $key => $value) : ?>
                                    <tr>
                                        <?php
                                        $type_price = $value['type_price_products'];
                                        $item_id_price = $value['item_id_price_products'];
                                        $stage_id_price = $value['stage_id_price_products'];
                                        $dtItem = [];
                                        $dtStages = $this->handling_price_model->getItemsStagesPriceQuotes($stage_id_price);
                                        if ($type_price == "materials") {
                                            $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                                        } else if ($type_price == "stages") {
                                            $dtItem = $this->handling_price_model->getItemsStagesPriceQuotes($item_id_price);
                                        }

                                        $tdCategoryStages = '<td>
                                            <input type="hidden" name="type_price_products[]" class="form-control type_price_products" value="' . $value['type_price_products'] . '">
                                            <input type="hidden" name="item_id_price_products[]" class="form-control item_id_price_products" value="' . $value['item_id_price_products'] . '">
                                            <input type="hidden" name="stage_id_price_products[]" class="form-control stage_id_price_products" value="' . $value['stage_id_price_products'] . '">
                                            <div>' . $dtItem['name'] . '</div>
                                            <div class="checkbox checkbox-danger" style="pointer-events: none;">
                                                <input type="checkbox" onchange="totalAll()" ' . ($value['not_cpln'] ? 'checked' : '') . ' class="not_cpln" name="not_cpln[' . $counterItemsProducts . ']" id="not_cpln_' . $counterItemsProducts . '" value="1">
                                                <label for="not_cpln_' . $counterItemsProducts . '">Không tính CPLN</label>
                                            </div>
                                        </td>';

                                        $tdLongHeight = '<td class="text-center">
                                            ' . (!empty($value['long_height']) ? $value['long_height'] : '') . '
                                        </td>';
                                        $tdWidthHorizontal = '<td class="text-center">
                                            ' . (!empty($value['width_horizontal']) ? $value['width_horizontal'] : '') . '
                                        </td>';

                                        $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                                        $tdQC = '<td><div class="text-center"></div></td>';
                                        $tdNumberOperate = '<td class="text-center">
                                            ' . formatNumber($value['number_operate_products']) . '
                                        </td>';

                                        $tdFaceProducts = '<td class="text-center">
                                            ' . (!empty($value['face_products']) ? formatNumber($value['face_products']) : '') . '
                                        </td>';

                                        $tdPriceAbout = '<td class="text-right">
                                            ' . formatMoney($value['price_about_products']) . '
                                        </td>';

                                        $tdTotalSheet = '<td class="td-total-sheet-products text-right">
                                            ' . formatMoney($value['total_sheet_products']) . '
                                        </td>';

                                        echo '
                                            ' . $tdCategoryStages . '
                                            ' . $tdLongHeight . '
                                            ' . $tdWidthHorizontal . '
                                            ' . $tdMode . '
                                            ' . $tdQC . '
                                            ' . $tdNumberOperate . '
                                            ' . $tdFaceProducts . '
                                            ' . $tdPriceAbout . '
                                            ' . $tdTotalSheet . '
                                        ';
                                        ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="table-sub" class="table table-hover" style="margin: 0;">
                        <tbody>
                            <tr>
                                <td class="text-danger text-center" style="border-top: 1px solid #cedae6; width: 135px;"><?= lang('SUM1') ?></td>
                                <td class="sum1 text-right" style="border-top: 1px solid #cedae6;"><?= !empty($arrDataJson['sum1']) ? formatMoney($arrDataJson['sum1']) : '' ?></td>
                            </tr>
                            <tr>
                                <td class="text-danger text-center" style="border-top: 1px solid #cedae6; width: 135px;"><?= lang('SUM2') ?></td>
                                <td class="sum2 text-right" style="border-top: 1px solid #cedae6;"><?= !empty($arrDataJson['sum2']) ? formatMoney($arrDataJson['sum2']) : '' ?></td>
                            </tr>
                            <tr>
                                <td class="text-danger text-center" style="border-top: 1px solid #cedae6; width: 135px;"><?= lang('G1') ?></td>
                                <td class="text-right" style="border-top: 1px solid #cedae6;">
                                    <div class="g1"><?= !empty($arrDataJson['g1']) ? formatMoney($arrDataJson['g1']) : '' ?></div>
                                    <div class="text-danger">Sum 1 + Sum 2 : Số Con /Tờ QC </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="table-gvc" class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 350px;"><?= lang('Tên gia công - Vận chuyển') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('ĐVT') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Đơn giá') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('KG/Con') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Thành tiền') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($arrDataJson['itemsGVC'])) : ?>
                                <?php foreach ($arrDataJson['itemsGVC'] as $key => $value) : ?>
                                    <?php
                                    $tdNumber = '<td></td>';

                                    $tdTypeVC = '<td>
                                        ' . $value['type_vc'] . '
                                    </td>';

                                    $tdUnitKg = '<td class="text-center">
                                        ' . $value['unit_kg'] . '
                                    </td>';

                                    $tdPriceGvc = '<td class="text-right">
                                        ' . formatMoney($value['price_gvc']) . '
                                    </td>';

                                    $tdKgChildGvc = '<td class="text-center">
                                        ' . formatNumber($value['kg_child_gvc']) . '
                                    </td>';

                                    $tdTotalPriceGvc = '<td class="td-price-child-gvc text-right text-danger">
                                        ' . formatMoney($value['price_child_gvc']) . '
                                    </td>';

                                    echo '<tr>
                                        ' . $tdTypeVC . '
                                        ' . $tdUnitKg . '
                                        ' . $tdPriceGvc . '
                                        ' . $tdKgChildGvc . '
                                        ' . $tdTotalPriceGvc . '
                                    </tr>';
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-danger"><?= lang('G2') ?></td>
                                <td class="text-center text-danger" colspan="3"></td>
                                <td class="text-danger text-right td-g2"><?= !empty($arrDataJson['g2']) ? formatMoney($arrDataJson['g2']) : '' ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="table-gcpln" class="table table-hover">
                        <tbody>
                            <tr>
                                <td class="" style="border-top: 1px solid #cedae6;"><?= lang('Cost of Brand') ?></td>
                                <td class="" style="border-top: 1px solid #cedae6;"><?= lang('Chi Phí Brand') ?></td>
                                <td class="text-right" style="border-top: 1px solid #cedae6;" colspan="2"><?= !empty($arrDataJson['cost_of_brand']) ? ($arrDataJson['cost_of_brand']) : '' ?></td>
                            </tr>
                            <tr>
                                <td class=""><?= lang('Labor cost + Management Cost') ?></td>
                                <td class=""><?= lang('Chi Phí QL- Nhân Công') ?></td>
                                <td class="text-right" colspan="2">
                                    <?= !empty($arrDataJson['labor_cost']) ? ($arrDataJson['labor_cost']) : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td class=""><?= lang('Loss Cost') ?></td>
                                <td class=""><?= lang('Chi Phí Hao Phế các Công Đoạn') ?></td>
                                <td class="text-right" colspan="2">
                                    <?= !empty($arrDataJson['loss_cost']) ? ($arrDataJson['loss_cost']) : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td class=""><?= lang('Profit') ?></td>
                                <td class=""><?= lang('Lợi Nhuận') ?></td>
                                <td class="text-right" colspan="2">
                                    <?= !empty($arrDataJson['profit']) ? ($arrDataJson['profit']) : '' ?>
                                </td>
                            </tr>
                            <tr class="text-danger">
                                <td class=""><?= lang('G3') ?></td>
                                <td class=""><?= lang('GCPLN') ?></td>
                                <td class="td-percent-g3 text-center">
                                    <?= !empty($arrDataJson['total_precent']) ? ($arrDataJson['total_precent']) : '' ?>
                                </td>
                                <td class="td-g3 text-right">
                                    <?= !empty($arrDataJson['g3']) ? formatMoney($arrDataJson['g3']) : '' ?>
                                </td>
                            </tr>
                            <tr class="text-danger">
                                <td class=""><?= lang('G') ?></td>
                                <td class=""><?= lang('G=SUM Tổng=G1+G2+G3') ?></td>
                                <td></td>
                                <td class="td-g text-right">
                                    <?= !empty($arrDataJson['g']) ? formatMoney($arrDataJson['g']) : '' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>