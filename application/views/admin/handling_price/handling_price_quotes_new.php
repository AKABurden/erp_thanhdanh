<?php echo form_open('admin/Handling_price/handlingPriceQuotes/', array('id' => 'handling-price')); ?>
<style>
    .td-white-red {
        background: white !important;
        color: red !important;
        border: 1px solid #cedae6 !important;
    }

    .modal-price-list table tr th {
        border: 1px solid #dcdcdc !important;
    }
</style>
<div class="modal-dialog modal-price-list" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_list_price') ?></h4>
        </div>
        <div class="modal-body">
            <?php
            $dtcItemsId = explode('__', $cItemsId);
            $products = $this->products_model->rowProduct($dtcItemsId[0]);
            $item_code = $products['code'];
            $item_name = $products['name'];
            $arrDataJson = !empty($cdataJson) ? json_decode($cdataJson, true) : null;
            $customers = str_replace('customers__', '', $customers);
            $dtGroupCustomer = $this->handling_price_model->getCustomersGroups($customers);
            $dtCategoryStages = get_table_where('tbl_category_stages', ['type_use' => 0], '');

            $machines = $this->handling_price_model->getMachines();
            $stages = $this->products_model->getStages();

            $quotes_stages = $this->db->get_where('tbl_stage_quote', ['id' => $quote_stage_id])->row_array();
            // echo '<pre>';
            // print_r($arrDataJson);
            // echo '</pre>';
            ?>
            <div class="row">
                <span id="id_div_customer" data-id="<?= $customers ?>"></span>
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
                        <div class="row-contro">
                            <div><?= lang('Nhóm khách hàng') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtGroupCustomer) ? $dtGroupCustomer['group_name'] : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Bảng giá công đoạn áp dụng') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($quotes_stages) ? $quotes_stages['name'] . ' (' . $quotes_stages['code'] . ')' : '' ?></div>
                        </div>
                        <div class="row-contro hide">
                            <div><?= lang('quantity') ?>: </div>
                            <div class="ml-at t-bold"><?= $cQuantity ?></div>
                            <input type="hidden" name="cQuantity" id="cQuantity" class="form-control" value="<?= $cQuantity ?>">
                            <input type="hidden" name="cItemsId" id="cItemsId" class="form-control" value="<?= $cItemsId ?>">
                            <input type="hidden" name="group_id" id="group_id" class="form-control" value="<?= !empty($dtGroupCustomer) ? $dtGroupCustomer['group_id'] : '' ?>">
                            <input type="hidden" name="quote_stage_id" class="form-control" value="<?= $quote_stage_id ?>">
                            <input type="hidden" name="customers" class="form-control" value="<?= $customers ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-8 hide">
                    <?= lang('Lịch sử báo giá', 'product_quote_reference') ?>
                    <input type="text" name="product_quote_reference" id="product_quote_reference" data-placeholder="<?= lang('Lịch sử báo giá') ?>" class="modal-select2" value="<?= !empty($arrDataJson['product_quote_reference']) ? $arrDataJson['product_quote_reference'] : '' ?>" style="width: 100%;" pattern="" title="">
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
                                <td>
                                    <input type="text" name="height" onchange="productCalculation()" placeholder="<?= lang('Height') ?>" class="form-control height" value="<?= !empty($arrDataJson['height']) ? $arrDataJson['height'] : $products['longs'] / 10 ?>">
                                </td>
                                <td>
                                    <input type="text" name="corner_boundary_height" onchange="productCalculation()" placeholder="<?= lang('Chừa Biên Bo Góc') ?>" class="form-control corner_boundary_height" value="<?= !empty($arrDataJson['corner_boundary_height']) ? $arrDataJson['corner_boundary_height'] : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="perpendicular_border_height" onchange="productCalculation()" placeholder="<?= lang('Chừa Biên Vuông Góc') ?>" class="form-control perpendicular_border_height" value="<?= !empty($arrDataJson['perpendicular_border_height']) ? $arrDataJson['perpendicular_border_height'] : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="round_square_border_height" onchange="productCalculation()" placeholder="<?= lang('Chừa Biên Vuông Tròn') ?>" class="form-control round_square_border_height" value="<?= !empty($arrDataJson['round_square_border_height']) ? $arrDataJson['round_square_border_height'] : '' ?>">
                                </td>
                                <td class="td-product-calculation-height text-center"><?= !empty($arrDataJson['product_calculation_height']) ? $arrDataJson['product_calculation_height'] : '' ?></td>
                                <td class="td-product-calculation-height-width text-center" rowspan="2"><?= !empty($arrDataJson['product_calculation_height_width']) ? $arrDataJson['product_calculation_height_width'] : '' ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('Width') ?></td>
                                <td>
                                    <input type="text" name="width" onchange="productCalculation()" placeholder="<?= lang('Width') ?>" class="form-control width" value="<?= !empty($arrDataJson['width']) ? $arrDataJson['width'] : $products['wide'] / 10 ?>">
                                </td>
                                <td>
                                    <input type="text" name="corner_boundary_width" onchange="productCalculation()" placeholder="<?= lang('Chừa Biên Bo Góc') ?>" class="form-control corner_boundary_width" value="<?= !empty($arrDataJson['corner_boundary_width']) ? $arrDataJson['corner_boundary_width'] : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="perpendicular_border_width" onchange="productCalculation()" placeholder="<?= lang('Chừa Biên Vuông Góc') ?>" class="form-control perpendicular_border_width" value="<?= !empty($arrDataJson['perpendicular_border_width']) ? $arrDataJson['perpendicular_border_width'] : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="round_square_border_width" onchange="productCalculation()" placeholder="<?= lang('Chừa Biên Vuông Tròn') ?>" class="form-control round_square_border_width" value="<?= !empty($arrDataJson['round_square_border_width']) ? $arrDataJson['round_square_border_width'] : '' ?>">
                                </td>
                                <td class="td-product-calculation-width text-center"><?= !empty($arrDataJson['product_calculation_width']) ? $arrDataJson['product_calculation_width'] : '' ?></td>
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
                                <td>
                                    <input type="text" name="height_layout" onchange="layoutCalculation()" placeholder="<?= lang('Height') ?>" class="form-control height_layout" value="<?= !empty($arrDataJson['height_layout']) ? $arrDataJson['height_layout'] : 0 ?>">
                                </td>
                                <td>
                                    <input type="text" name="height_layout_print_tweezers" onchange="layoutCalculation()" placeholder="<?= lang('Chừa Nhíp In') ?>" class="form-control height_layout_print_tweezers" value="<?= !empty($arrDataJson['height_layout_print_tweezers']) ? $arrDataJson['height_layout_print_tweezers'] : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="height_layout_boong_cut" onchange="layoutCalculation()" placeholder="<?= lang('Chừa Boong Cắt Bế') ?>" class="form-control height_layout_boong_cut" value="<?= !empty($arrDataJson['height_layout_boong_cut']) ? $arrDataJson['height_layout_boong_cut'] : '' ?>">
                                </td>
                                <td class="td-height_layout_material_size text-center">

                                </td>
                                <td class="td-height_layout_mode text-center">

                                </td>
                                <td class="td-height_layout_quantity text-center">

                                </td>
                                <td class="height_layout_total_quantity text-center" rowspan="2">

                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Width') ?></td>
                                <td>
                                    <input type="text" name="width_layout" onchange="layoutCalculation()" placeholder="<?= lang('Width') ?>" class="form-control width_layout" value="<?= !empty($arrDataJson['width_layout']) ? $arrDataJson['width_layout'] : 0 ?>">
                                </td>
                                <td>
                                    <input type="text" name="width_layout_print_tweezers" onchange="layoutCalculation()" placeholder="<?= lang('Chừa Nhíp In') ?>" class="form-control width_layout_print_tweezers" value="<?= !empty($arrDataJson['width_layout_print_tweezers']) ? $arrDataJson['width_layout_print_tweezers'] : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="width_layout_boong_cut" onchange="layoutCalculation()" placeholder="<?= lang('Chừa Boong Cắt Bế') ?>" class="form-control width_layout_boong_cut" value="<?= !empty($arrDataJson['width_layout_boong_cut']) ? $arrDataJson['width_layout_boong_cut'] : '' ?>">
                                </td>
                                <td class="td-width_layout_material_size text-center">

                                </td>
                                <td class="td-width_layout_mode text-center">

                                </td>
                                <td class="td-width_layout_quantity text-center">

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-6">
                    <!-- <li role="presentation" class="active"> -->
                    <a href="#facade" aria-controls="facade" role="tab" data-toggle="tab"><?= lang('tnh_facade') ?></a>
                    <!-- </li> -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_materials', 'material_price_quotes') ?>
                                <input type="text" name="material_price_quotes" id="material_price_quotes" data-placeholder="<?= lang('tnh_materials') ?>" class="modal-select2" style="width: 100%;" value="<?= !empty($arrDataJson['material_price_quotes']) ? $arrDataJson['material_price_quotes'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_stage', 'stages_price_quotes') ?>
                                <select name="stages_price_quotes" data-placeholder="<?= lang('tnh_stage') ?>" id="stages_price_quotes" class="stages_price_quotes modal-select2" style="width: 100%;">
                                    <option value=""></option>
                                    <?php if (!empty($stages)) : ?>
                                        <?php foreach ($stages as $key => $value) : ?>
                                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <a href="javascript:void(0)" onclick="addItemPriceQuotes()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div id="top_search_purchase" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="Quét QR..." style="width: 420px;">
                                <input style="width: 200px;" type="search" id="SearchQR_meterial" class="form-control" placeholder="<?php echo _l('Quét QR Nguyên Phụ Liệu'); ?>">
                                <input type="hidden" id="SearchQR_meterial_id" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="top_search_purchase" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="Quét QR..." style="width: 420px;">
                                <input style="width: 200px;" type="search" id="SearchQR_stages" class="form-control" placeholder="<?php echo _l('Quét QR Công Đoạn'); ?>">
                                <input type="hidden" id="SearchQR_stages_id" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="top_search_purchase" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="Quét QR..." style="width: 420px;">
                                <input style="width: 200px;" type="search" id="SearchQR_machines" class="form-control" placeholder="<?php echo _l('Quét QR Thiết Bị'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table-items-stages" class="table table-hover" style="min-width: 1100px;">
                                    <thead>
                                        <tr>
                                            <th colspan="99" class="text-center">Công đoạn trước in</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="width: 50px;"></th>
                                            <th class="text-center" style="width: 120px;"><?= lang('tnh_materials') ?></th>
                                            <th class="text-center" style="width: 70px;"><?= lang('tnh_stage') ?></th>
                                            <th class="text-center" style="width: 70px;"><?= lang('Đơn vị tính') ?></th>
                                            <th class="text-center" style="width: 70px;"><?= lang('Qui cách') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('SL màu in') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('Thiết bị') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Loại NPL') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Định mức BOM') ?></th>
                                            <th class="text-center hide" style="width: 150px;"><?= lang('Số lần vận hành') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Đơn giá NVL') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Thành tiền/Tờ') ?></th>
                                            <th class="text-center" style="width: 80px;"><?= lang('actions') ?></th>
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
                                                        ' . (!empty($dtItem['is_single_use']) ? '<div class="text-danger">Duy nhất</div>' : '') . '
                                                    </td>';
                                                    $tdStages = '<td class="text-center">' . $dtStages['name'] . '</td>';
                                                    $tdUnits = '<td><div class="text-center">' . $dtItem['unit_name'] . '</div></td>';
                                                    $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                                                    $tdColor = '<td>
                                                        <input type="text" name="quantity_color[]" placeholder="' . lang('SL màu in') . '" onchange="totalItemsPrice()" place class="form-control quantity_color number-format" value="' . formatNumber($value['quantity_color']) . '">
                                                    </td>';
                                                    $tdNumberOperate = '<td class="hide">
                                                        <input type="text" name="number_operate[]" placeholder="' . lang('Số lần vận hành') . '" onchange="totalItemsPrice()" place class="form-control number_operate number-format" value="' . formatNumber($value['number_operate']) . '">
                                                    </td>';

                                                    // $tdMachine = '<td>
                                                    //     <input type="text" name="machine[]" onchange="totalItemsPrice()" placeholder="'.lang('Thiết bị').'" class="form-control machine" value="'.(!empty($value['machine']) ? $value['machine'] : '').'">
                                                    // </td>';

                                                    $optionsMachine = '<option></option>';
                                                    foreach ($machines as $kM => $vM) {
                                                        $_selected = !empty($value['machine']) && $value['machine'] == $vM['id'] ? 'selected' : '';
                                                        $optionsMachine .= '<option ' . $_selected . ' value="' . $vM['id'] . '">' . $vM['name'] . '</option>';
                                                    }

                                                    $tdMachine = '<td>
                                                        <select name="machine[]" onchange="totalItemsPrice()" data-placeholder="' . lang('Thiết bị') . '" style="width: 100%;" class="machine">
                                                            ' . $optionsMachine . '
                                                        </select>
                                                    </td>';

                                                    $tdTypeNPL = '<td>
                                                        <input type="text" name="type_npl[]" onchange="totalItemsPrice()" placeholder="' . lang('Loại NPL') . '" class="form-control type_npl" value="' . (!empty($value['type_npl']) ? $value['type_npl'] : '') . '">
                                                    </td>';
                                                    $tdQuotaBOM = '<td>
                                                        <input type="text" name="quota_bom[]" onchange="totalItemsPrice()" placeholder="' . lang('Định mức BOM') . '" class="form-control quota_bom" value="' . (!empty($value['quota_bom']) ? formatNumber($value['quota_bom']) : '') . '">
                                                    </td>';

                                                    $tdPriceAbout = '<td>
                                                        <input type="text" name="price_about[]" placeholder="' . lang('Đơn giá/lần') . '" onchange="totalItemsPrice()" class="form-control price_about money-format" value="' . formatMoney($value['price_about']) . '">
                                                    </td>';
                                                    $tdTotalSheet = '<td class="td-total-sheet text-right">
                                                    </td>';
                                                    $tdActions = '<td class="text-center"><i onclick="removeItemsPrice(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    echo '<tr>
                                                        <td></td>
                                                        ' . $tdCategoryStages . '
                                                        ' . $tdStages . '
                                                        ' . $tdUnits . '
                                                        ' . $tdMode . '
                                                        ' . $tdColor . '
                                                        ' . $tdNumberOperate . '
                                                        ' . $tdMachine . '
                                                        ' . $tdTypeNPL . '
                                                        ' . $tdQuotaBOM . '
                                                        ' . $tdPriceAbout . '
                                                        ' . $tdTotalSheet . '
                                                        ' . $tdActions . '
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
                                            <td class="text-center text-danger" colspan="8"><?= lang('GT=sum tổng') ?></td>
                                            <td class="text-right grand-total-sheet text-danger"></td>
                                            <td></td>
                                        </tr>
                                        <tr class="text-danger bold hide">
                                            <td class="text-center"><?= lang('') ?></td>
                                            <td class="text-center"><?= lang('GSP1') ?></td>
                                            <td class="text-center" colspan="4"><?= lang('GSP1=GT/S') ?></td>
                                            <td class="td-gsp1 text-right"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- <li role="presentation"> -->
                    <a href="#backside" aria-controls="backside" role="tab" data-toggle="tab"><?= lang('tnh_backside') ?></a>
                    <!-- </li> -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_materials', 'material_price_quotes_backside') ?>
                                <input type="text" name="material_price_quotes_backside" id="material_price_quotes_backside" data-placeholder="<?= lang('tnh_materials') ?>" class="modal-select2" style="width: 100%;" value="<?= !empty($arrDataJson['material_price_quotes_backside']) ? $arrDataJson['material_price_quotes_backside'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('tnh_stage', 'stages_price_quotes_backside') ?>
                                <select name="stages_price_quotes_backside" data-placeholder="<?= lang('tnh_stage') ?>" id="stages_price_quotes_backside" class="stages_price_quotes_backside modal-select2" style="width: 100%;">
                                    <option value=""></option>
                                    <?php if (!empty($stages)) : ?>
                                        <?php foreach ($stages as $key => $value) : ?>
                                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <a href="javascript:void(0)" onclick="addItemPriceQuotesBackside()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table-items-stages-backside" class="table table-hover" style="min-width: 1150px;">
                                    <thead>
                                        <tr>
                                            <th colspan="99" class="text-center">Công đoạn trước in (mặt sau)</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="width: 120px;"><?= lang('tnh_materials') ?></th>
                                            <th class="text-center" style="width: 70px;"><?= lang('tnh_stage') ?></th>
                                            <th class="text-center" style="width: 70px;"><?= lang('Đơn vị tính') ?></th>
                                            <th class="text-center" style="width: 70px;"><?= lang('Qui cách') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('SL màu in') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('Thiết bị') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Loại NPL') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Định mức BOM') ?></th>
                                            <th class="text-center hide" style="width: 150px;"><?= lang('Số lần vận hành') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Đơn giá NVL') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('Thành tiền/Tờ') ?></th>
                                            <th class="text-center" style="width: 80px;"><?= lang('actions') ?></th>
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
                                                        <input type="hidden" name="type_price_backside[]" class="form-control type_price_backside" value="' . $type_price . '">
                                                        <input type="hidden" name="item_id_price_backside[]" class="form-control item_id_price" value="' . $item_id_price . '">
                                                        <input type="hidden" name="stage_id_price_backside[]" class="form-control stage_id_price_backside" value="' . $stage_id_price . '">
                                                        <div>' . ($type_price == 'materials' ? $dtItem['name'] . '(' . $dtItem['code'] . ')' : '') . '</div>
                                                        ' . (!empty($dtItem['is_single_use']) ? '<div class="text-danger">Duy nhất</div>' : '') . '
                                                    </td>';
                                                    $tdStages = '<td class="text-center">' . $dtStages['name'] . '</td>';
                                                    $tdUnits = '<td><div class="text-center">' . $dtItem['unit_name'] . '</div></td>';
                                                    $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                                                    $tdColor = '<td>
                                                        <input type="text" name="quantity_color_backside[]" placeholder="' . lang('SL màu in') . '" onchange="totalItemsPrice()" place class="form-control quantity_color_backside number-format" value="' . formatNumber($value['quantity_color_backside']) . '">
                                                    </td>';
                                                    $tdNumberOperate = '<td class="hide">
                                                        <input type="text" name="number_operate_backside[]" placeholder="' . lang('Số lần vận hành') . '" onchange="totalItemsPrice()" place class="form-control number_operate_backside number-format" value="' . formatNumber($value['number_operate_backside']) . '">
                                                    </td>';

                                                    // $tdMachine = '<td>
                                                    //     <input type="text" name="machine_backside[]" onchange="totalItemsPrice()" placeholder="'.lang('Thiết bị').'" class="form-control machine_backside" value="'.(!empty($value['machine_backside']) ? $value['machine_backside'] : '').'">
                                                    // </td>';

                                                    $optionsMachine = '<option></option>';
                                                    foreach ($machines as $kM => $vM) {
                                                        $_selected = !empty($value['machine_backside']) && $value['machine_backside'] == $vM['id'] ? 'selected' : '';
                                                        $optionsMachine .= '<option ' . $_selected . ' value="' . $vM['id'] . '">' . $vM['name'] . '</option>';
                                                    }

                                                    $tdMachine = '<td>
                                                        <select name="machine_backside[]" onchange="totalItemsPrice()" data-placeholder="' . lang('Thiết bị') . '" style="width: 100%;" class="machine_backside">
                                                            ' . $optionsMachine . '
                                                        </select>
                                                    </td>';

                                                    $tdTypeNPL = '<td>
                                                        <input type="text" name="type_npl_backside[]" onchange="totalItemsPrice()" placeholder="' . lang('Loại NPL') . '" class="form-control type_npl_backside" value="' . (!empty($value['type_npl_backside']) ? $value['type_npl_backside'] : '') . '">
                                                    </td>';
                                                    $tdQuotaBOM = '<td>
                                                        <input type="text" name="quota_bom_backside[]" onchange="totalItemsPrice()" placeholder="' . lang('Định mức BOM') . '" class="form-control quota_bom_backside" value="' . (!empty($value['quota_bom_backside']) ? formatNumber($value['quota_bom_backside']) : '') . '">
                                                    </td>';

                                                    $tdPriceAbout = '<td>
                                                        <input type="text" name="price_about_backside[]" placeholder="' . lang('Đơn giá/lần') . '" onchange="totalItemsPrice()" class="form-control price_about_backside money-format" value="' . formatMoney($value['price_about_backside']) . '">
                                                    </td>';
                                                    $tdTotalSheet = '<td class="td-total-sheet-backside td-total-sheet text-right">
                                                    </td>';
                                                    $tdActions = '<td class="text-center"><i onclick="removeItemsPrice(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    echo '<tr>
                                                        ' . $tdCategoryStages . '
                                                        ' . $tdStages . '
                                                        ' . $tdUnits . '
                                                        ' . $tdMode . '
                                                        ' . $tdColor . '
                                                        ' . $tdNumberOperate . '
                                                        ' . $tdMachine . '
                                                        ' . $tdTypeNPL . '
                                                        ' . $tdQuotaBOM . '
                                                        ' . $tdPriceAbout . '
                                                        ' . $tdTotalSheet . '
                                                        ' . $tdActions . '
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
                                            <td class="text-right grand-total-sheet-backside text-danger"></td>
                                            <td></td>
                                        </tr>
                                        <tr class="text-danger bold hide">
                                            <td class="text-center"><?= lang('') ?></td>
                                            <td class="text-center"><?= lang('GSP1') ?></td>
                                            <td class="text-center" colspan="4"><?= lang('GSP1=GT/S') ?></td>
                                            <td class="td-gsp1-backside text-right"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Công đoạn sau in', 'stages_price_quotes_products') ?>
                        <select name="stages_price_quotes_products" data-placeholder="<?= lang('Công đoạn thành phẩm') ?>" id="stages_price_quotes_products" class="stages_price_quotes_products modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($stages)) : ?>
                                <?php foreach ($stages as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0)" onclick="addItemPriceQuotesProducts()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div id="top_search_purchase" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="Quét QR..." style="width: 420px;">
                        <input style="width: 200px;" type="search" id="SearchQR_stages_products" class="form-control" placeholder="<?php echo _l('Quét QR Công Đoạn'); ?>">
                    </div>
                </div>

                <div class="col-md-12">
                    <table id="table-items-products" class="table table-hover">
                        <thead>
                            <tr>
                                <th colspan="99" class="text-center">Công đoạn sau in</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 150px;"><?= lang('Hạng mục tính giá') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Dài/Cao') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Rộng/Ngang') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('Qui cách') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('QC Vận Hành') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Số Lần Xả/Vận Hành') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Số mặt in') ?><div class="text-danger">(<?= lang('1: mặt trước, 2: mặt sau, 3: mặt trước và sau') ?>)</div>
                                </th>
                                <th class="text-center" style="width: 150px;"><?= lang('Đơn Giá CĐ') ?></th>
                                <th class="text-center" style="width: 150px;"><?= lang('Thành tiền') ?></th>
                                <th class="text-center" style="width: 80px;"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counterItemsProducts = 0; ?>
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

                                        // Code mới truyền dài, rộng riêng biệt
                                        $price_sell = $this->handling_price_model->getStagePrice(
                                            $stage_id_price,
                                            $quote_stage_id,
                                            $customers,
                                            (!empty($arrDataJson['height_layout']) ? $arrDataJson['height_layout'] : 0),
                                            (!empty($arrDataJson['width_layout']) ? $arrDataJson['width_layout'] : 0),
                                            (!empty($value['long_height']) ? $value['long_height'] : null),
                                            (!empty($value['width_horizontal']) ? $value['width_horizontal'] : null)
                                        );

                                        $tdCategoryStages = '<td>
                                            <input type="hidden" name="type_price_products[' . $counterItemsProducts . ']" class="form-control type_price_products" value="' . $value['type_price_products'] . '">
                                            <input type="hidden" name="item_id_price_products[' . $counterItemsProducts . ']" class="form-control item_id_price_products" value="' . $value['item_id_price_products'] . '">
                                            <input type="hidden" name="stage_id_price_products[' . $counterItemsProducts . ']" class="form-control stage_id_price_products" value="' . $value['stage_id_price_products'] . '">
                                            <div>' . $dtItem['name'] . '</div>
                                            <div class="checkbox checkbox-danger">
                                                <input type="checkbox" onchange="totalAll()" ' . ($value['not_cpln'] ? 'checked' : '') . ' class="not_cpln" name="not_cpln[' . $counterItemsProducts . ']" id="not_cpln_' . $counterItemsProducts . '" value="1">
                                                <label for="not_cpln_' . $counterItemsProducts . '">Không tính CPLN</label>
                                            </div>
                                        </td>';

                                        $tdLongHeight = '<td>
                                            <input type="text" name="long_height[' . $counterItemsProducts . ']" placeholder="' . lang('Dài/Cao') . '" onchange="totalItemsPrice()" place class="form-control long_height" value="' . (!empty($value['long_height']) ? $value['long_height'] : '') . '">
                                        </td>';
                                        $tdWidthHorizontal = '<td>
                                            <input type="text" name="width_horizontal[' . $counterItemsProducts . ']" placeholder="' . lang('Rộng/Ngang') . '" onchange="totalItemsPrice()" place class="form-control width_horizontal" value="' . (!empty($value['width_horizontal']) ? $value['width_horizontal'] : '') . '">
                                        </td>';

                                        $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                                        $tdQC = '<td><div class="text-center"></div></td>';
                                        $tdNumberOperate = '<td>
                                            <input type="text" name="number_operate_products[' . $counterItemsProducts . ']" placeholder="' . lang('Số Lần Xả/Vận Hành') . '" onchange="totalItemsPrice()" place class="form-control number_operate_products number-format" value="' . formatNumber($value['number_operate_products']) . '">
                                        </td>';

                                        $tdFaceProducts = '<td>
                                            <input type="text" name="face_products[' . $counterItemsProducts . ']" placeholder="' . lang('Mặt in') . '" onchange="totalItemsPrice()" class="form-control face_products number-format" value="' . (!empty($value['face_products']) ? formatNumber($value['face_products']) : '') . '">
                                        </td>';

                                        // ' . formatNumber($value['price_about_products']) . '
                                        $tdPriceAbout = '<td>
                                            <input type="text" name="price_about_products[' . $counterItemsProducts . ']" placeholder="' . lang('Đơn giá/tờ') . '" onchange="totalItemsPrice()" readonly class="form-control price_about_products money-format" value="' . formatNumber($price_sell) . '">
                                        </td>';

                                        $tdTotalSheet = '<td class="td-total-sheet-products text-right">
                                            ' . formatNumber($value['total_sheet_products']) . '
                                        </td>';
                                        $tdActions = '<td class="text-center"><i onclick="removeItemsPriceProducts(this)" class="fa fa-remove text-danger pointer"></i></td>';

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
                                            ' . $tdActions . '
                                        ';

                                        $counterItemsProducts++;
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
                                <td class="sum1 text-right" style="border-top: 1px solid #cedae6;"></td>
                            </tr>
                            <tr>
                                <td class="text-danger text-center" style="border-top: 1px solid #cedae6; width: 135px;"><?= lang('SUM2') ?></td>
                                <td class="sum2 text-right" style="border-top: 1px solid #cedae6;">0</td>
                            </tr>
                            <tr>
                                <td class="text-danger text-center" style="border-top: 1px solid #cedae6; width: 135px;"><?= lang('G1') ?></td>
                                <td class="text-right" style="border-top: 1px solid #cedae6;">
                                    <div class="g1">0</div>
                                    <div class="text-danger">Sum 1 + Sum 2 : Số Con /Tờ QC </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row hide">
                <div class="col-md-12">
                    <table id="table-g2" class="table table-hover" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td class="text-danger text-center" style="border-top: 1px solid #cedae6;"><?= lang('GGC') ?></td>
                                <td class="" style="border-top: 1px solid #cedae6;"><?= lang('Giá gia công cấp số') ?></td>
                                <td class="text-danger text-center" style="border-top: 1px solid #cedae6;"><?= lang('Con') ?></td>
                                <td class="" style="border-top: 1px solid #cedae6;">
                                    <input type="text" name="processing_price" id="processing_price" onchange="gvcCalculation()" class="form-control" placeholder="<?= lang('Giá gia công cấp số') ?>" value="<?= !empty($arrDataJson['ggc']) ? $arrDataJson['ggc'] : '' ?>">
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
                                <th class="text-center" style="width: 30px;">
                                    <a class="hover-svg dropdown-toggle" onclick="addOutTransport()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                            <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                        </svg>
                                    </a>
                                </th>
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
                                    $tdNumber = '<td class="text-center"><a href="javascript:void(0)" onclick="removeOutTransport(this)" class="text-danger fa fa-remove"></a></td>';

                                    $tdTypeVC = '<td>
                                        <input type="text" onchange="gvcCalculation()" placeholder="' . lang('Tên gia công - Vận chuyển') . '" name="type_vc[]" class="form-control" value="' . $value['type_vc'] . '">
                                    </td>';

                                    $tdUnitKg = '<td>
                                        <input type="text" onchange="gvcCalculation()" placeholder="' . lang('ĐVT') . '" name="unit_kg[]" class="form-control" value="' . $value['unit_kg'] . '">
                                    </td>';

                                    $tdPriceGvc = '<td>
                                        <input type="text" placeholder="' . lang('Đơn giá') . '" onchange="gvcCalculation()" name="price_gvc[]" id="price_gvc" class="form-control price_gvc money-format" value="' . formatMoney($value['price_gvc']) . '">
                                    </td>';

                                    $tdKgChildGvc = '<td>
                                        <input type="text" placeholder="' . lang('KG/Con') . '" onchange="gvcCalculation()" name="kg_child_gvc[]" class="form-control kg_child_gvc number-format" value="' . formatNumber($value['kg_child_gvc']) . '">
                                    </td>';

                                    $tdTotalPriceGvc = '<td class="td-price-child-gvc text-right text-danger">
                                        ' . formatMoney($value['price_child_gvc']) . '
                                    </td>';

                                    echo '<tr>
                                        ' . $tdNumber . '
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
                                <td class="text-center text-danger" colspan="4"></td>
                                <td class="text-danger text-right td-g2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- More -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Thêm công đoạn', 'stage_add_plus') ?>
                        <select name="stage_add_plus" data-placeholder="<?= lang('Công đoạn') ?>" id="stage_add_plus" class="stage_add_plus modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($stages)) : ?>
                                <?php foreach ($stages as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0)" onclick="addPlusStage()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                </div>
                <div class="col-md-12">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab-info-npl" aria-controls="home" role="tab" data-toggle="tab"><?= lang('Thông tin NPL') ?></a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#tab-layout" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('Dàn trang') ?></a>
                            </li>
                            <?php if (!empty($arrDataJson['arrItemsPsStage'])) : ?>
                                <?php foreach ($arrDataJson['arrItemsPsStage'] as $key => $value) : ?>
                                    <?php
                                    $dtInfo = $this->products_model->rowStages($value['stage_id']);
                                    ?>
                                    <li role="presentation">
                                        <a href="#tab-cd-<?= $value['stage_id'] ?>" aria-controls="tab" role="tab" data-toggle="tab"><?= $dtInfo['name'] ?> <span class="fa fa-remove text-danger" onclick="rvPlusStage(this, <?= $value['stage_id'] ?>)" data-toggle="tooltip" title="Xóa"></span></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li role="presentation" class="span-tab-plus">
                                <a href="#tab-cd-kiem" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('Công đoạn kiểm') ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-cd-tem" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('Công đoạn phân đơn - dán tem') ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-cd-delivery" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('Công đoạn mở phiếu giao hàng') ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-cd-car" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('Công đoạn điều xe') ?></a>
                            </li>
                        </ul>

                        <div class="tab-content div-tab-plus">
                            <div role="tabpanel" class="tab-pane active" id="tab-info-npl">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('tnh_materials', 'material_info_npl') ?>
                                            <input type="text" name="material_info_npl" id="material_info_npl" data-placeholder="<?= lang('tnh_materials') ?>" class="modal-select2" style="width: 100%;" value="<?= !empty($arrDataJson['material_info_npl']) ? $arrDataJson['material_info_npl'] : '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)" onclick="addItemMaterialInfoNPL()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tb-item-info-npl" class="table table-hover" style="min-width: 2500px; margin-top: 0px;">
                                        <thead>
                                            <tr>
                                                <th colspan="99"><?= lang('Thông Tin NPL') ?></th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="width: 150px;"><?= lang('Mã NPL') ?></th>
                                                <th class="text-center" style="width: 150px;"><?= lang('Tên NPL') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Height') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Width') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('ĐV Đo SP') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('ĐV Tính SP') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Height') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Chừa Biên') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Width') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Chừa Biên') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('ĐV Đo SP') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('ĐV Tính SP') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Chừa Nhíp') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Chừa Xả Width') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Chừa Xả Height') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Số Con Width') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Số Con Height') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Số Con/Tờ') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Giá/Tờ (VNĐ)') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Giá/XLT In/Tờ') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Tiền/Tờ') ?></th>
                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ctItemsInfoNPL = 0;
                                            ?>
                                            <?php if (!empty($arrDataJson['arrItemsNPL'])) : ?>
                                                <?php foreach ($arrDataJson['arrItemsNPL'] as $key => $value) : ?>
                                                    <?php
                                                    $dtMaterial = $this->items_model->rowMaterial($value['item_id']);
                                                    $tdItemCode = '<td>
                                                            <input type="hidden" name="items_npl[' . $ctItemsInfoNPL . '][item_id]" class="form-control" value="' . $value['item_id'] . '">
                                                            ' . $dtMaterial['code'] . '
                                                        </td>';

                                                    $tdItemName = '<td>
                                                            ' . $dtMaterial['name'] . '
                                                        </td>';

                                                    $tdHeight = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="' . formatNumber($value['height']) . '" title="">
                                                        </td>';

                                                    $tdWidth = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="' . formatNumber($value['width']) . '" title="">
                                                        </td>';

                                                    $tdUnitMeasureSP = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][unit_measure_sp]" onchange="handlingReferencePrice()" placeholder="ĐV đo SP" class="form-control unit_measure_sp" value="' . $value['unit_measure_sp'] . '" title="">
                                                        </td>';

                                                    $tdUnitCalculationSP = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][unit_calculation_sp]" onchange="handlingReferencePrice()" placeholder="ĐV tính SP" class="form-control unit_calculation_sp" value="' . $value['unit_calculation_sp'] . '" title="">
                                                        </td>';

                                                    $tdHeight1 = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][height1]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height1" value="' . formatNumber($value['height1']) . '" title="">
                                                        </td>';

                                                    $tdLeaveMargin = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][leave_margin]" onchange="handlingReferencePrice()" placeholder="Chừa biên" class="form-control number-format leave_margin" value="' . formatNumber($value['leave_margin']) . '" title="">
                                                        </td>';

                                                    $tdWidth1 = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][width1]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width1" value="' . formatNumber($value['width1']) . '" title="">
                                                        </td>';

                                                    $tdLeaveMargin1 = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][leave_margin1]" onchange="handlingReferencePrice()" placeholder="Chừa biên" class="form-control number-format leave_margin1" value="' . formatNumber($value['leave_margin1']) . '" title="">
                                                        </td>';

                                                    $tdUnitMeasureSP1 = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][unit_measure_sp1]" onchange="handlingReferencePrice()" placeholder="ĐV đo SP" class="form-control unit_measure_sp1" value="' . $value['unit_measure_sp1'] . '" title="">
                                                        </td>';

                                                    $tdUnitCalculationSP1 = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][unit_calculation_sp1]" onchange="handlingReferencePrice()" placeholder="ĐV tính SP" class="form-control unit_calculation_sp1" value="' . $value['unit_calculation_sp1'] . '" title="">
                                                        </td>';

                                                    $tdLeaveTweezers = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][leave_tweezers]" onchange="handlingReferencePrice()" placeholder="Chừa nhíp" class="form-control number-format leave_tweezers" value="' . formatNumber($value['leave_tweezers']) . '" title="">
                                                        </td>';

                                                    $tdLeaveDischargeW = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][leave_discharge_w]" onchange="handlingReferencePrice()" placeholder="Chừa xả width" class="form-control number-format leave_discharge_w" value="' . formatNumber($value['leave_discharge_w']) . '" title="">
                                                        </td>';

                                                    $tdLeaveDischargeH = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][leave_discharge_h]" onchange="handlingReferencePrice()" placeholder="Chừa xả height" class="form-control number-format leave_discharge_h" value="' . formatNumber($value['leave_discharge_h']) . '" title="">
                                                        </td>';

                                                    $tdTotalChildW = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][total_child_w]" onchange="handlingReferencePrice()" placeholder="Tổng số con width" class="form-control number-format total_child_w" value="' . formatNumber($value['total_child_w']) . '" title="">
                                                        </td>';

                                                    $tdTotalChildH = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][total_child_h]" onchange="handlingReferencePrice()" placeholder="Tổng số con height" class="form-control number-format total_child_h" value="' . formatNumber($value['total_child_h']) . '" title="">
                                                        </td>';

                                                    $tdTotalChildPage = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][total_child_page]" onchange="handlingReferencePrice()" placeholder="Tổng số con/Tờ" class="form-control number-format total_child_page" value="' . formatNumber($value['total_child_page']) . '" title="">
                                                        </td>';

                                                    $tdPricePage = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][price_page]" onchange="totalAll()" placeholder="Giá/Tờ(VNĐ)" class="form-control money-format price_page" value="' . formatMoney($value['price_page']) . '" title="">
                                                        </td>';

                                                    $tdPriceXLT = '<td>
                                                            <input type="text" name="items_npl[' . $ctItemsInfoNPL . '][price_xlt]" onchange="totalAll()" placeholder="Giá/XLT In/Tờ" class="form-control money-format price_xlt" value="' . formatMoney($value['price_xlt']) . '" title="">
                                                        </td>';

                                                    $tdTotalMoney = '<td class="total_money text-right">
                                                            ' . formatMoney($value['total_money']) . '
                                                        </td>';

                                                    $tdActions = '<td class="text-center"><i onclick="removeItemMaterialInfoNPL(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    $_cTrItem = '<tr>
                                                            ' . $tdItemCode . '
                                                            ' . $tdItemName . '
                                                            ' . $tdHeight . '
                                                            ' . $tdWidth . '
                                                            ' . $tdUnitMeasureSP . '
                                                            ' . $tdUnitCalculationSP . '
                                                            ' . $tdHeight1 . '
                                                            ' . $tdLeaveMargin . '
                                                            ' . $tdWidth1 . '
                                                            ' . $tdLeaveMargin1 . '
                                                            ' . $tdUnitMeasureSP1 . '
                                                            ' . $tdUnitCalculationSP1 . '
                                                            ' . $tdLeaveTweezers . '
                                                            ' . $tdLeaveDischargeW . '
                                                            ' . $tdLeaveDischargeH . '
                                                            ' . $tdTotalChildW . '
                                                            ' . $tdTotalChildH . '
                                                            ' . $tdTotalChildPage . '
                                                            ' . $tdPricePage . '
                                                            ' . $tdPriceXLT . '
                                                            ' . $tdTotalMoney . '
                                                            ' . $tdActions . '
                                                        </tr>';
                                                    echo $_cTrItem;
                                                    $ctItemsInfoNPL++;
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-layout">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="stage_layout">Công đoạn</label>
                                            <select data-placeholder="Công đoạn" id="stage_layout" class="stage_sub modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if (!empty($stages)) : ?>
                                                    <?php foreach ($stages as $key => $value) : ?>
                                                        <option data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="machine_layout">Thiết bị</label>
                                            <select data-placeholder="Thiết bị" id="machine_layout" class="machine_sub modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if (!empty($machines)) : ?>
                                                    <?php foreach ($machines as $key => $value) : ?>
                                                        <option data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)" onclick="addItemLayoutStage(this)" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tb-item-layout" class="table table-hover" style="min-width: 2700px; margin-top: 0px;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Mã Thiết Bị - Công Đoạn') ?></th>
                                                <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Tên Thiết Bị - Công Đoạn') ?></th>
                                                <th class="text-center" rowspan="1" colspan="2" style="width: 200px;"><?= lang('Kích Thước Vận Hành') ?></th>
                                                <th class="text-center" rowspan="1" colspan="7" style="width: 800px;"><?= lang('Mặt 1') ?></th>
                                                <th class="text-center" rowspan="1" colspan="7" style="width: 800px;"><?= lang('Mặt 2') ?></th>
                                                <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Tổng Số Lần Vận Hành/Tờ') ?></th>
                                                <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Tổng Số NPL/Tờ') ?></th>
                                                <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Giá/Tờ (VNĐ)') ?></th>
                                                <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Tổng Giá/Tờ (VNĐ)') ?></th>
                                                <th class="text-center" rowspan="2" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                            </tr>
                                            <tr>
                                                <th class="text-center"><?= lang('Height') ?></th>
                                                <th class="text-center"><?= lang('Width') ?></th>
                                                <th class="text-center"><?= lang('Số Con/Tờ In') ?></th>
                                                <th class="text-center"><?= lang('Số Lượng Màu In') ?></th>
                                                <th class="text-center"><?= lang('Loại NPL') ?></th>
                                                <th class="text-center"><?= lang('Số Lượng Kẽm') ?></th>
                                                <th class="text-center"><?= lang('Số Lần Vận Hành/Tờ') ?></th>
                                                <th class="text-center"><?= lang('Định Mức Kẽm Sử Dụng') ?></th>
                                                <th class="text-center"><?= lang('Định Mức Năng Suất /CTP') ?></th>
                                                <th class="text-center"><?= lang('Số Con/Tờ In') ?></th>
                                                <th class="text-center"><?= lang('Số Lượng Màu In') ?></th>
                                                <th class="text-center"><?= lang('Loại NPL') ?></th>
                                                <th class="text-center"><?= lang('Số Lượng Kẽm') ?></th>
                                                <th class="text-center"><?= lang('Số Lần Vận Hành/Tờ') ?></th>
                                                <th class="text-center"><?= lang('Định Mức Kẽm Sử Dụng') ?></th>
                                                <th class="text-center"><?= lang('Định Mức Năng Suất /CTP') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ctLayout = 0;
                                            ?>
                                            <?php if (!empty($arrDataJson['arrItemsLStage'])) : ?>
                                                <?php foreach ($arrDataJson['arrItemsLStage'] as $key => $value) : ?>
                                                    <?php
                                                    if ($value['type'] == 1) {
                                                        $dtInfo = $this->products_model->rowStages($value['item_id']);
                                                    } else {
                                                        $dtInfo = $this->category_model->rowMachines($value['item_id']);
                                                    }

                                                    $tdItemCode = '<td>
                                                            <input type="hidden" name="items_lstage[' . $ctLayout . '][item_id]" class="form-control" value="' . $value['item_id'] . '">
                                                            <input type="hidden" name="items_lstage[' . $ctLayout . '][type]" class="form-control" value="' . $value['type'] . '">
                                                            ' . $dtInfo['code'] . '
                                                        </td>';

                                                    $tdItemName = '<td>
                                                            ' . $dtInfo['name'] . '
                                                        </td>';

                                                    $tdHeight = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="' . formatNumber($value['height']) . '" title="">
                                                        </td>';

                                                    $tdWidth = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="' . formatNumber($value['width']) . '" title="">
                                                        </td>';

                                                    $tdNumberChildPrintF1 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_child_print_f1]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ In" class="form-control number-format number_child_print_f1" value="' . formatNumber($value['number_child_print_f1']) . '" title="">
                                                        </td>';

                                                    $tdNumberColorPrintF1 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_color_print_f1]" onchange="handlingReferencePrice()" placeholder="Số Lượng Màu In" class="form-control number-format number_color_print_f1" value="' . formatNumber($value['number_color_print_f1']) . '" title="">
                                                        </td>';

                                                    $tdTypeNPLF1 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][type_npl_f1]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f1" value="' . ($value['type_npl_f1']) . '" title="">
                                                        </td>';

                                                    $tdNumberZnF1 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_zn_f1]" onchange="handlingReferencePrice()" placeholder="Số Lượng Kẽm" class="form-control number-format number_zn_f1" value="' . formatNumber($value['number_zn_f1']) . '" title="">
                                                        </td>';

                                                    $tdNumberOperationsPageF1 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_operations_page_f1]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Tờ" class="form-control number-format number_operations_page_f1" value="' . formatNumber($value['number_operations_page_f1']) . '" title="">
                                                        </td>';

                                                    $tdQuotaZnUseF1 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][quota_zn_use_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức Kẽm Sử Dụng" class="form-control number-format quota_zn_use_f1" value="' . formatNumber($value['quota_zn_use_f1']) . '" title="">
                                                        </td>';

                                                    $tdQuotaCTPF1 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][quota_ctp_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất /CTP" class="form-control number-format quota_ctp_f1" value="' . formatNumber($value['quota_ctp_f1']) . '" title="">
                                                        </td>';


                                                    $tdNumberChildPrintF2 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_child_print_f2]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ In" class="form-control number-format number_child_print_f2" value="' . formatNumber($value['number_child_print_f2']) . '" title="">
                                                        </td>';

                                                    $tdNumberColorPrintF2 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_color_print_f2]" onchange="handlingReferencePrice()" placeholder="Số Lượng Màu In" class="form-control number-format number_color_print_f2" value="' . formatNumber($value['number_color_print_f2']) . '" title="">
                                                        </td>';

                                                    $tdTypeNPLF2 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][type_npl_f2]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f2" value="' . ($value['type_npl_f2']) . '" title="">
                                                        </td>';

                                                    $tdNumberZnF2 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_zn_f2]" onchange="handlingReferencePrice()" placeholder="Số Lượng Kẽm" class="form-control number-format number_zn_f2" value="' . formatNumber($value['number_zn_f2']) . '" title="">
                                                        </td>';

                                                    $tdNumberOperationsPageF2 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][number_operations_page_f2]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Tờ" class="form-control number-format number_operations_page_f2" value="' . formatNumber($value['number_operations_page_f2']) . '" title="">
                                                        </td>';

                                                    $tdQuotaZnUseF2 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][quota_zn_use_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức Kẽm Sử Dụng" class="form-control number-format quota_zn_use_f2" value="' . formatNumber($value['quota_zn_use_f2']) . '" title="">
                                                        </td>';

                                                    $tdQuotaCTPF2 = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][quota_ctp_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất /CTP" class="form-control number-format quota_ctp_f2" value="' . formatNumber($value['quota_ctp_f2']) . '" title="">
                                                        </td>';

                                                    $tdTotalOperationsPage = '<td class="text-center total_operations_page">
                                                            ' . formatNumber($value['total_operations_page']) . '
                                                        </td>';

                                                    $tdTotalNPL = '<td class="text-center">
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][total_npl]" onchange="handlingReferencePrice()" placeholder="Tổng Số NPL/Tờ" class="form-control number-format total_npl" value="' . formatNumber($value['total_npl']) . '" title="">
                                                        </td>';

                                                    $tdPrice = '<td>
                                                            <input type="text" name="items_lstage[' . $ctLayout . '][price]" onchange="totalAll()" placeholder="Giá/Tờ (VNĐ)" class="form-control number-format price" value="' . formatMoney($value['price']) . '" title="">
                                                        </td>';

                                                    $tdTotalPrice = '<td class="text-center total_price">
                                                            ' . formatMoney($value['total_price']) . '
                                                        </td>';

                                                    $tdActions = '<td class="text-center"><i onclick="removeItemSubStage(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    $trItem = '<tr>
                                                            ' . $tdItemCode . '
                                                            ' . $tdItemName . '
                                                            ' . $tdHeight . '
                                                            ' . $tdWidth . '
                                                            ' . $tdNumberChildPrintF1 . '
                                                            ' . $tdNumberColorPrintF1 . '
                                                            ' . $tdTypeNPLF1 . '
                                                            ' . $tdNumberZnF1 . '
                                                            ' . $tdNumberOperationsPageF1 . '
                                                            ' . $tdQuotaZnUseF1 . '
                                                            ' . $tdQuotaCTPF1 . '
                                                            ' . $tdNumberChildPrintF2 . '
                                                            ' . $tdNumberColorPrintF2 . '
                                                            ' . $tdTypeNPLF2 . '
                                                            ' . $tdNumberZnF2 . '
                                                            ' . $tdNumberOperationsPageF2 . '
                                                            ' . $tdQuotaZnUseF2 . '
                                                            ' . $tdQuotaCTPF2 . '
                                                            ' . $tdTotalOperationsPage . '
                                                            ' . $tdTotalNPL . '
                                                            ' . $tdPrice . '
                                                            ' . $tdTotalPrice . '
                                                            ' . $tdActions . '
                                                        </tr>';
                                                    echo $trItem;
                                                    $ctLayout++;
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-cd-kiem">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('Nhóm công đoạn kiểm', 'inspection_stage') ?>
                                            <select name="inspection_stage" id="inspection_stage" data-placeholder="<?= lang('Nhóm công đoạn kiểm') ?>" class="modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if (!empty($dtCategoryStages)) : ?>
                                                    <?php foreach ($dtCategoryStages as $key => $value) : ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)" onclick="addItemInspectionStage()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tb-item-inspection-stage" class="table table-hover" style="min-width: 1100px; margin-top: 0px;">
                                        <thead>
                                            <tr>
                                                <th colspan="99"><?= lang('Công Đoạn Kiểm') ?></th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th class="text-center" colspan="2"><?= lang('Kích Thước Vận Hành') ?></th>
                                                <th></th>
                                                <th class="text-center" colspan="3"><?= lang('Mặt 1') ?></th>
                                                <th class="text-center" colspan="3"><?= lang('Mặt 2') ?></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="width: 150px;"><?= lang('Nhóm Công Đoạn Kiểm') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Height') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Width') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Đơn Vị Tính') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Loại Kiểm') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Số Lần Vận Hành/Mặt') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Định Mức Năng Suất') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Loại Kiểm') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Số Lần Vận Hành/Mặt') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Định Mức Năng Suất ') ?></th>
                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ctItemsInspectionStage = 0;
                                            ?>
                                            <?php if (!empty($arrDataJson['arrItemsIStage'])) : ?>
                                                <?php foreach ($arrDataJson['arrItemsIStage'] as $key => $value) : ?>
                                                    <?php
                                                    $dtInfo = $this->products_model->rowCategoryStages($value['category_stage_id']);
                                                    $tdItemCode = '<td>
                                                            <input type="hidden" name="items_istage[' . $ctItemsInspectionStage . '][category_stage_id]" class="form-control" value="' . $value['category_stage_id'] . '">
                                                            ' . $dtInfo['code'] . '(' . $dtInfo['name'] . ')
                                                        </td>';

                                                    $tdHeight = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="' . formatNumber($value['height']) . '" title="">
                                                        </td>';

                                                    $tdWidth = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="' . formatNumber($value['width']) . '" title="">
                                                        </td>';

                                                    $tdUnitF1 = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][unit_f1]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit_f1" value="' . $value['unit_f1'] . '" title="">
                                                        </td>';

                                                    $tdTypeCheckF1 = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][type_check_f1]" onchange="handlingReferencePrice()" placeholder="Loại Kiểm" class="form-control type_check_f1" value="' . $value['type_check_f1'] . '" title="">
                                                        </td>';

                                                    $tdNumberOSideF1 = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][number_o_side_f1]" onchange="handlingReferencePrice()" placeholder="Số lần vận hành/mặt" class="form-control number_o_side_f1 number-format" value="' . formatNumber($value['number_o_side_f1']) . '" title="">
                                                        </td>';

                                                    $tdProductivityNormsF1 = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][productivity_norms_f1]" onchange="handlingReferencePrice()" placeholder="định mức năng suất" class="form-control productivity_norms_f1 number-format" value="' . formatNumber($value['productivity_norms_f1']) . '" title="">
                                                        </td>';

                                                    $tdTypeCheckF2 = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][type_check_f2]" onchange="handlingReferencePrice()" placeholder="Loại Kiểm" class="form-control type_check_f2" value="' . $value['type_check_f2'] . '" title="">
                                                        </td>';

                                                    $tdNumberOSideF2 = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][number_o_side_f2]" onchange="handlingReferencePrice()" placeholder="Số lần vận hành/mặt" class="form-control number_o_side_f2 number-format" value="' . formatNumber($value['number_o_side_f2']) . '" title="">
                                                        </td>';

                                                    $tdProductivityNormsF2 = '<td>
                                                            <input type="text" name="items_istage[' . $ctItemsInspectionStage . '][productivity_norms_f2]" onchange="handlingReferencePrice()" placeholder="định mức năng suất" class="form-control productivity_norms_f2 number-format" value="' . formatNumber($value['productivity_norms_f2']) . '" title="">
                                                        </td>';

                                                    $tdActions = '<td class="text-center"><i onclick="removeItemInspectionStage(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    $_cTrItem = '<tr>
                                                            ' . $tdItemCode . '
                                                            ' . $tdHeight . '
                                                            ' . $tdWidth . '
                                                            ' . $tdUnitF1 . '
                                                            ' . $tdTypeCheckF1 . '
                                                            ' . $tdNumberOSideF1 . '
                                                            ' . $tdProductivityNormsF1 . '
                                                            ' . $tdTypeCheckF2 . '
                                                            ' . $tdNumberOSideF2 . '
                                                            ' . $tdProductivityNormsF2 . '
                                                            ' . $tdActions . '
                                                        </tr>';

                                                    echo $_cTrItem;
                                                    $ctItemsInspectionStage++;
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-cd-tem">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('Nhóm công đoạn đóng gói', 'package_stage') ?>
                                            <select name="package_stage" id="package_stage" data-placeholder="<?= lang('Nhóm công đoạn đóng gói') ?>" class="modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if (!empty($dtCategoryStages)) : ?>
                                                    <?php foreach ($dtCategoryStages as $key => $value) : ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)" onclick="addItemPackageStage()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tb-item-package-stage" class="table table-hover" style="min-width: 1100px; margin-top: 0px;">
                                        <thead>
                                            <tr>
                                                <th colspan="99"><?= lang('Công Đoạn Phân Đơn - Dán Tem ') ?></th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="width: 150px;"><?= lang('Nhóm Công Đoạn Đóng Gói') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Height') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Width') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Cao/Đáy') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Đơn Vị Tính') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Số Con/Kiện') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Định Mức Kiện') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Định Mức Năng Suất') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Loại Bao Bì Đóng Gói') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Loại Tem Dán') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Số Tem Dán') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Số Kiện Dán') ?></th>
                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ctItemsPackageStage = 0;
                                            ?>
                                            <?php if (!empty($arrDataJson['arrItemsPStage'])) : ?>
                                                <?php foreach ($arrDataJson['arrItemsPStage'] as $key => $value) : ?>
                                                    <?php
                                                    $dtInfo = $this->products_model->rowCategoryStages($value['category_stage_id']);

                                                    $tdItemCode = '<td>
                                                            <input type="hidden" name="items_pstage[' . $ctItemsPackageStage . '][category_stage_id]" class="form-control" value="' . $value['category_stage_id'] . '">
                                                            ' . $dtInfo['code'] . '(' . $dtInfo['name'] . ')
                                                        </td>';

                                                    $tdHeight = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="' . formatNumber($value['height']) . '" title="">
                                                        </td>';

                                                    $tdWidth = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="' . formatNumber($value['width']) . '" title="">
                                                        </td>';

                                                    $tdHightBottom = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][hight_bottom]" onchange="handlingReferencePrice()" placeholder="Cao/Đáy" class="form-control number-format hight_bottom" value="' . formatNumber($value['hight_bottom']) . '" title="">
                                                        </td>';

                                                    $tdUnit = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][unit]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit" value="' . ($value['unit']) . '" title="">
                                                        </td>';

                                                    $tdNumberBales = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][number_bales]" onchange="handlingReferencePrice()" placeholder="Số Con/Kiện" class="form-control number-format number_bales" value="' . formatNumber($value['number_bales']) . '" title="">
                                                        </td>';

                                                    $tdBaleNorms = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][bale_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Kiện" class="form-control number-format bale_norms" value="' . formatNumber($value['bale_norms']) . '" title="">
                                                        </td>';

                                                    $tdProductivityNorms = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][productivity_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất" class="form-control number-format productivity_norms" value="' . formatNumber($value['productivity_norms']) . '" title="">
                                                        </td>';

                                                    $tdTypePackaging = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][type_packaging]" onchange="handlingReferencePrice()" placeholder="Loại bao bì đóng gói" class="form-control type_packaging" value="' . ($value['type_packaging']) . '" title="">
                                                        </td>';

                                                    $tdTypeTem = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][type_tem]" onchange="handlingReferencePrice()" placeholder="Loại Tem Dán" class="form-control type_tem" value="' . ($value['type_tem']) . '" title="">
                                                        </td>';

                                                    $tdTotalTem = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][total_tem]" onchange="handlingReferencePrice()" placeholder="Tổng Số Tem Dán" class="form-control number-format total_tem" value="' . formatNumber($value['total_tem']) . '" title="">
                                                        </td>';

                                                    $tdTotalBale = '<td>
                                                            <input type="text" name="items_pstage[' . $ctItemsPackageStage . '][total_bale]" onchange="handlingReferencePrice()" placeholder="Tổng Số Kiện Dán" class="form-control number-format total_bale" value="' . formatNumber($value['total_bale']) . '" title="">
                                                        </td>';

                                                    $tdActions = '<td class="text-center"><i onclick="removeItemPackageStage(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    $_cTrItem = '<tr>
                                                            ' . $tdItemCode . '
                                                            ' . $tdHeight . '
                                                            ' . $tdWidth . '
                                                            ' . $tdHightBottom . '
                                                            ' . $tdUnit . '
                                                            ' . $tdNumberBales . '
                                                            ' . $tdBaleNorms . '
                                                            ' . $tdProductivityNorms . '
                                                            ' . $tdTypePackaging . '
                                                            ' . $tdTypeTem . '
                                                            ' . $tdTotalTem . '
                                                            ' . $tdTotalBale . '
                                                            ' . $tdActions . '
                                                        </tr>';

                                                    echo $_cTrItem;
                                                    $ctItemsPackageStage++;
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-cd-delivery">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('Nhóm công đoạn mở phiếu giao hàng', 'delivery_stage') ?>
                                            <select name="delivery_stage" id="delivery_stage" data-placeholder="<?= lang('Nhóm công đoạn mở phiếu giao hàng') ?>" class="modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if (!empty($dtCategoryStages)) : ?>
                                                    <?php foreach ($dtCategoryStages as $key => $value) : ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)" onclick="addItemDeliveryStage()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tb-item-delivery-stage" class="table table-hover" style="min-width: 1100px; margin-top: 0px;">
                                        <thead>
                                            <tr>
                                                <th colspan="99"><?= lang('Công Đoạn Mở Phiếu Giao Hàng') ?></th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="width: 150px;"><?= lang('Nhóm Công Đoạn Đóng Gói') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Height') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Width') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Cao/Đáy') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Đơn Vị Tính') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Số Con/Kiện') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Định Mức Kiện') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Định Mức Năng Suất') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Loại Bao Bì Đóng Gói') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Loại Tem Dán') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Số Tem Dán') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Số Kiện Dán') ?></th>
                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ctItemsDeliveryStage = 0;
                                            ?>
                                            <?php if (!empty($arrDataJson['arrItemsDStage'])) : ?>
                                                <?php foreach ($arrDataJson['arrItemsDStage'] as $key => $value) : ?>
                                                    <?php
                                                    $dtInfo = $this->products_model->rowCategoryStages($value['category_stage_id']);

                                                    $tdItemCode = '<td>
                                                            <input type="hidden" name="items_dstage[' . $ctItemsDeliveryStage . '][category_stage_id]" class="form-control" value="' . $value['category_stage_id'] . '">
                                                            ' . $dtInfo['code'] . '(' . $dtInfo['name'] . ')
                                                        </td>';

                                                    $tdHeight = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="' . formatNumber($value['height']) . '" title="">
                                                        </td>';

                                                    $tdWidth = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="' . formatNumber($value['width']) . '" title="">
                                                        </td>';

                                                    $tdHightBottom = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][hight_bottom]" onchange="handlingReferencePrice()" placeholder="Cao/Đáy" class="form-control number-format hight_bottom" value="' . formatNumber($value['hight_bottom']) . '" title="">
                                                        </td>';

                                                    $tdUnit = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][unit]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit" value="' . ($value['unit']) . '" title="">
                                                        </td>';

                                                    $tdNumberBales = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][number_bales]" onchange="handlingReferencePrice()" placeholder="Số Con/Kiện" class="form-control number-format number_bales" value="' . formatNumber($value['number_bales']) . '" title="">
                                                        </td>';

                                                    $tdBaleNorms = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][bale_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Kiện" class="form-control number-format bale_norms" value="' . formatNumber($value['bale_norms']) . '" title="">
                                                        </td>';

                                                    $tdProductivityNorms = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][productivity_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất" class="form-control number-format productivity_norms" value="' . formatNumber($value['productivity_norms']) . '" title="">
                                                        </td>';

                                                    $tdTypePackaging = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][type_packaging]" onchange="handlingReferencePrice()" placeholder="Loại bao bì đóng gói" class="form-control type_packaging" value="' . ($value['type_packaging']) . '" title="">
                                                        </td>';

                                                    $tdTypeTem = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][type_tem]" onchange="handlingReferencePrice()" placeholder="Loại Tem Dán" class="form-control type_tem" value="' . ($value['type_tem']) . '" title="">
                                                        </td>';

                                                    $tdTotalTem = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][total_tem]" onchange="handlingReferencePrice()" placeholder="Tổng Số Tem Dán" class="form-control number-format total_tem" value="' . formatNumber($value['total_tem']) . '" title="">
                                                        </td>';

                                                    $tdTotalBale = '<td>
                                                            <input type="text" name="items_dstage[' . $ctItemsDeliveryStage . '][total_bale]" onchange="handlingReferencePrice()" placeholder="Tổng Số Kiện Dán" class="form-control number-format total_bale" value="' . formatNumber($value['total_bale']) . '" title="">
                                                        </td>';

                                                    $tdActions = '<td class="text-center"><i onclick="removeItemDeliveryStage(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    $_cTrItem = '<tr>
                                                            ' . $tdItemCode . '
                                                            ' . $tdHeight . '
                                                            ' . $tdWidth . '
                                                            ' . $tdHightBottom . '
                                                            ' . $tdUnit . '
                                                            ' . $tdNumberBales . '
                                                            ' . $tdBaleNorms . '
                                                            ' . $tdProductivityNorms . '
                                                            ' . $tdTypePackaging . '
                                                            ' . $tdTypeTem . '
                                                            ' . $tdTotalTem . '
                                                            ' . $tdTotalBale . '
                                                            ' . $tdActions . '
                                                        </tr>';

                                                    echo $_cTrItem;
                                                    $ctItemsDeliveryStage++;
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-cd-car">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('Nhóm công đoạn giao hàng', 'car_stage') ?>
                                            <select name="car_stage" id="car_stage" data-placeholder="<?= lang('Nhóm công đoạn giao hàng') ?>" class="modal-select2" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if (!empty($dtCategoryStages)) : ?>
                                                    <?php foreach ($dtCategoryStages as $key => $value) : ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="javascript:void(0)" onclick="addItemCarStage()" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tb-item-car-stage" class="table table-hover" style="min-width: 1100px; margin-top: 0px;">
                                        <thead>
                                            <tr>
                                                <th colspan="99"><?= lang('Công Đoạn Điều Xe') ?></th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="width: 150px;"><?= lang('Nhóm Công Đoạn Giao Hàng') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Loại Phương Tiện') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Đơn Vị Tính') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Đơn Giá Giao Hàng') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Tổng Kiện') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Thành Tiền') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Nhà Cung Cấp') ?></th>
                                                <th class="text-center" style="width: 100px;"><?= lang('Địa Chỉ Giao Hàng') ?></th>
                                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ctItemsCarStage = 0;
                                            ?>
                                            <?php if (!empty($arrDataJson['arrItemsCStage'])) : ?>
                                                <?php foreach ($arrDataJson['arrItemsCStage'] as $key => $value) : ?>
                                                    <?php
                                                    $dtInfo = $this->products_model->rowCategoryStages($value['category_stage_id']);

                                                    $tdItemCode = '<td>
                                                            <input type="hidden" name="items_cstage[' . $ctItemsCarStage . '][category_stage_id]" class="form-control" value="' . $value['category_stage_id'] . '">
                                                            ' . $dtInfo['code'] . '(' . $dtInfo['name'] . ')
                                                        </td>';

                                                    $tdTransportation = '<td>
                                                            <input type="text" name="items_cstage[' . $ctItemsCarStage . '][transportation]" onchange="handlingReferencePrice()" placeholder="Loại Phương Tiện" class="form-control transportation" value="' . $value['transportation'] . '" title="">
                                                        </td>';

                                                    $tdUnit = '<td>
                                                            <input type="text" name="items_cstage[' . $ctItemsCarStage . '][unit]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit" value="' . $value['unit'] . '" title="">
                                                        </td>';

                                                    $tdPriceDelivery = '<td>
                                                            <input type="text" name="items_cstage[' . $ctItemsCarStage . '][price_delivery]" onchange="handlingReferencePrice()" placeholder="Đơn Giá Giao Hàng" class="form-control money-format price_delivery" value="' . formatMoney($value['price_delivery']) . '" title="">
                                                        </td>';

                                                    $tdTotalBale = '<td>
                                                            <input type="text" name="items_cstage[' . $ctItemsCarStage . '][total_bale]" onchange="handlingReferencePrice()" placeholder="Tổng Kiện" class="form-control number-format total_bale" value="' . formatNumber($value['total_bale']) . '" title="">
                                                        </td>';

                                                    $tdSubtotal = '<td>
                                                            <input type="text" name="items_cstage[' . $ctItemsCarStage . '][subtotal]" onchange="handlingReferencePrice()" placeholder="Thành Tiền" class="form-control money-format subtotal" value="' . formatMoney($value['subtotal']) . '" title="">
                                                        </td>';

                                                    $tdSupplier = '<td>
                                                            <input type="text" name="items_cstage[' . $ctItemsCarStage . '][supplier]" onchange="handlingReferencePrice()" placeholder="Nhà Cung Cấp" class="form-control supplier" value="' . $value['supplier'] . '" title="">
                                                        </td>';

                                                    $tdAddressDelivery = '<td>
                                                            <input type="text" name="items_cstage[' . $ctItemsCarStage . '][address_delivery]" onchange="handlingReferencePrice()" placeholder="Địa Chỉ Giao Hàng" class="form-control address_delivery" value="' . $value['address_delivery'] . '" title="">
                                                        </td>';

                                                    $tdActions = '<td class="text-center"><i onclick="removeItemCarStage(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                    $_cTrItem = '<tr>
                                                            ' . $tdItemCode . '
                                                            ' . $tdTransportation . '
                                                            ' . $tdUnit . '
                                                            ' . $tdPriceDelivery . '
                                                            ' . $tdTotalBale . '
                                                            ' . $tdSubtotal . '
                                                            ' . $tdSupplier . '
                                                            ' . $tdAddressDelivery . '
                                                            ' . $tdActions . '
                                                        </tr>';

                                                    echo $_cTrItem;
                                                    $ctItemsCarStage++;
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                            $ctPlusStage = 0;
                            ?>
                            <?php if (!empty($arrDataJson['arrItemsPsStage'])) : ?>
                                <?php
                                $optionStages = '<option value=""></option>';
                                foreach ($stages as $key => $value) {
                                    $optionStages .= '<option data-code="' . $value['code'] . '" value="' . $value['id'] . '">' . $value['name'] . '</option>';
                                }

                                $optionMachines = '<option value=""></option>';
                                foreach ($machines as $key => $value) {
                                    $optionMachines .= '<option data-code="' . $value['code'] . '" value="' . $value['id'] . '">' . $value['name'] . '</option>';
                                }
                                ?>

                                <?php foreach ($arrDataJson['arrItemsPsStage'] as $key => $value) : ?>
                                    <?php
                                    $stageAddPlusId = $value['stage_id'];

                                    ?>
                                    <div role="tabpanel" class="tab-pane" id="tab-cd-<?= $stageAddPlusId ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="stage_sub_<?= $stageAddPlusId ?>">Công đoạn</label>
                                                    <select data-placeholder="Công đoạn" id="stage_sub_<?= $stageAddPlusId ?>" class="stage_sub modal-select2" style="width: 100%;">
                                                        <?= $optionStages ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="machine_sub_<?= $stageAddPlusId ?>">Thiết bị</label>
                                                    <select data-placeholder="Thiết bị" id="machine_sub_<?= $stageAddPlusId ?>" class="machine_sub modal-select2" style="width: 100%;">
                                                        <?= $optionMachines ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="javascript:void(0)" onclick="addItemPlusStage(this, <?= $stageAddPlusId ?>)" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="tb-plus-stage-<?= $stageAddPlusId ?>" class="table table-hover tb-plus-stage" style="min-width: 3100px; margin-top: 0px;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Mã Thiết Bị - Công Đoạn') ?></th>
                                                        <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Tên Thiết Bị - Công Đoạn') ?></th>
                                                        <th class="text-center" rowspan="1" colspan="2" style="width: 200px;"><?= lang('Kích Thước Vận Hành') ?></th>
                                                        <th class="text-center" rowspan="1" colspan="8" style="width: 800px;"><?= lang('Mặt 1') ?></th>
                                                        <th class="text-center" rowspan="1" colspan="8" style="width: 800px;"><?= lang('Mặt 2') ?></th>
                                                        <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Tổng NPL Mặt 1+Mặt 2') ?></th>
                                                        <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Tổng TG Canh Bài Mặt 1+Mặt 2') ?></th>
                                                        <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Đơn Giá/lần In') ?></th>
                                                        <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Đơn Giá/tờ') ?></th>
                                                        <th class="text-center" rowspan="2" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Height</th>
                                                        <th class="text-center">Width</th>
                                                        <th class="text-center">Số Con/Tờ Vận Hành</th>
                                                        <th class="text-center">Loại NPL</th>
                                                        <th class="text-center">Số Lần Vận Hành/Mặt</th>
                                                        <th class="text-center">Định Mức Mực In/Lần In</th>
                                                        <th class="text-center">Định Mức TG Canh Bài</th>
                                                        <th class="text-center">Định Mức NPL Canh Bài</th>
                                                        <th class="text-center">Tổng NPL</th>
                                                        <th class="text-center">Tổng TG Canh Bài</th>
                                                        <th class="text-center">Số Con/Tờ Vận Hành</th>
                                                        <th class="text-center">Loại NPL</th>
                                                        <th class="text-center">Số Lần Vận Hành/Mặt</th>
                                                        <th class="text-center">Định Mức Mực In/Lần In</th>
                                                        <th class="text-center">Định Mức TG Canh Bài</th>
                                                        <th class="text-center">Định Mức NPL Canh Bài</th>
                                                        <th class="text-center">Tổng NPL</th>
                                                        <th class="text-center">Tổng TG Canh Bài</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($value['itemsS'])) : ?>
                                                        <?php foreach ($value['itemsS'] as $k => $v) : ?>
                                                            <?php
                                                            if ($v['type'] == 1) {
                                                                $dtInfo = $this->products_model->rowStages($v['item_id']);
                                                            } else {
                                                                $dtInfo = $this->category_model->rowMachines($v['item_id']);
                                                            }

                                                            $tdItemCode = '<td>
                                                                    <input type="hidden" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][item_id]" class="form-control" value="' . $v['item_id'] . '">
                                                                    <input type="hidden" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][type]" class="form-control" value="' . $v['type'] . '">
                                                                    ' . $dtInfo['code'] . '
                                                                </td>';

                                                            $tdItemName = '<td>
                                                                    ' . $dtInfo['name'] . '
                                                                </td>';

                                                            $tdHeight = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="' . formatNumber($v['height']) . '" title="">
                                                                </td>';

                                                            $tdWidth = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="' . formatNumber($v['width']) . '" title="">
                                                                </td>';

                                                            $tdNumberOperatingF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][number_operating_f1]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ Vận Hành" class="form-control number-format number_operating_f1" value="' . formatNumber($v['number_operating_f1']) . '" title="">
                                                                </td>';

                                                            $tdTypeNPLF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][type_npl_f1]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f1" value="' . ($v['type_npl_f1']) . '" title="">
                                                                </td>';

                                                            $tdNumberOperatingSideF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][number_operating_side_f1]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Mặt" class="form-control number-format number_operating_side_f1" value="' . formatNumber($v['number_operating_side_f1']) . '" title="">
                                                                </td>';

                                                            $tdInkF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][ink_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức Mực In/Lần In" class="form-control number-format ink_f1" value="' . ($v['ink_f1']) . '" title="">
                                                                </td>';

                                                            $tdQuotaTimeF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][quota_time_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức TG Canh Bài" class="form-control number-format quota_time_f1" value="' . formatNumber($v['quota_time_f1']) . '" title="">
                                                                </td>';

                                                            $tdQuotaNPLF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][quota_npl_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức NPL Canh Bài" class="form-control number-format quota_npl_f1" value="' . formatNumber($v['quota_npl_f1']) . '" title="">
                                                                </td>';

                                                            $tdTotalNPLF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][total_npl_f1]" onchange="totalAll()" placeholder="Tổng NPL" class="form-control number-format total_npl_f1" value="' . formatNumber($v['total_npl_f1']) . '" title="">
                                                                </td>';

                                                            $tdTotalTimeF1 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][total_time_npl_f1]" onchange="totalAll()" placeholder="Tổng TG Canh Bài" class="form-control number-format total_time_npl_f1" value="' . formatNumber($v['total_time_npl_f1']) . '" title="">
                                                                </td>';

                                                            //
                                                            $tdNumberOperatingF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][number_operating_f2]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ Vận Hành" class="form-control number-format number_operating_f2" value="' . formatNumber($v['number_operating_f2']) . '" title="">
                                                                </td>';

                                                            $tdTypeNPLF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][type_npl_f2]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f2" value="' . ($v['type_npl_f2']) . '" title="">
                                                                </td>';

                                                            $tdNumberOperatingSideF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][number_operating_side_f2]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Mặt" class="form-control number-format number_operating_side_f2" value="' . formatNumber($v['number_operating_side_f2']) . '" title="">
                                                                </td>';

                                                            $tdInkF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][ink_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức Mực In/Lần In" class="form-control number-format ink_f2" value="' . formatNumber($v['ink_f2']) . '" title="">
                                                                </td>';

                                                            $tdQuotaTimeF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][quota_time_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức TG Canh Bài" class="form-control number-format quota_time_f2" value="' . formatNumber($v['quota_time_f2']) . '" title="">
                                                                </td>';

                                                            $tdQuotaNPLF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][quota_npl_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức NPL Canh Bài" class="form-control number-format quota_npl_f2" value="' . formatNumber($v['quota_npl_f2']) . '" title="">
                                                                </td>';

                                                            $tdTotalNPLF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][total_npl_f2]" onchange="totalAll()" placeholder="Tổng NPL" class="form-control number-format total_npl_f2" value="' . formatNumber($v['total_npl_f2']) . '" title="">
                                                                </td>';

                                                            $tdTotalTimeF2 = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][total_time_npl_f2]" onchange="totalAll()" placeholder="Tổng TG Canh Bài" class="form-control number-format total_time_npl_f2" value="' . formatNumber($v['total_time_npl_f2']) . '" title="">
                                                                </td>';

                                                            $tdTotalNPLF12 = '<td class="td-total-npl-f12 text-center">
                                                                    ' . formatNumber($v['total_npl_f12']) . '
                                                                </td>';

                                                            $tdTotalTimeF12 = '<td class="td-total-time-f12 text-center">
                                                                    ' . formatNumber($v['total_time_f12']) . '
                                                                </td>';

                                                            $tdPrice = '<td>
                                                                    <input type="text" name="items_psstage[' . $stageAddPlusId . '][' . $ctPlusStage . '][price]" onchange="totalAll()" placeholder="Đơn Giá/lần In" class="form-control number-format price" value="' . formatMoney($v['price']) . '" title="">
                                                                </td>';

                                                            $tdPricePage = '<td class="td-price-page text-right">
                                                                    ' . formatMoney($v['price_page']) . '
                                                                </td>';

                                                            $tdActions = '<td class="text-center"><i onclick="removeItemSubStage(this)" class="fa fa-remove text-danger pointer"></i></td>';

                                                            $trItem = '<tr>
                                                                    ' . $tdItemCode . '
                                                                    ' . $tdItemName . '
                                                                    ' . $tdHeight . '
                                                                    ' . $tdWidth . '
                                                                    ' . $tdNumberOperatingF1 . '
                                                                    ' . $tdTypeNPLF1 . '
                                                                    ' . $tdNumberOperatingSideF1 . '
                                                                    ' . $tdInkF1 . '
                                                                    ' . $tdQuotaTimeF1 . '
                                                                    ' . $tdQuotaNPLF1 . '
                                                                    ' . $tdTotalNPLF1 . '
                                                                    ' . $tdTotalTimeF1 . '
                                                                    ' . $tdNumberOperatingF2 . '
                                                                    ' . $tdTypeNPLF2 . '
                                                                    ' . $tdNumberOperatingSideF2 . '
                                                                    ' . $tdInkF2 . '
                                                                    ' . $tdQuotaTimeF2 . '
                                                                    ' . $tdQuotaNPLF2 . '
                                                                    ' . $tdTotalNPLF2 . '
                                                                    ' . $tdTotalTimeF2 . '
                                                                    ' . $tdTotalNPLF12 . '
                                                                    ' . $tdTotalTimeF12 . '
                                                                    ' . $tdPrice . '
                                                                    ' . $tdPricePage . '
                                                                    ' . $tdActions . '
                                                                </tr>';
                                                            echo $trItem;
                                                            $ctPlusStage++;
                                                            ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <script>
                                    $(document).ready(function() {
                                        $('select.stage_sub').select2({
                                            allowClear: true
                                        });
                                        $('select.machine_sub').select2({
                                            allowClear: true
                                        });
                                    });
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- More -->
            <div class="row mtop5">
                <div class="col-md-12">
                    <button type="button" onclick="loadExpenseQuote(this)" class="btn btn-warning"><?= lang('Load lại % chi phí') ?></button>
                </div>
                <div class="col-md-12">
                    <table id="table-gcpln" class="table table-hover">
                        <tbody>
                            <tr>
                                <td class="" style="border-top: 1px solid #cedae6;"><?= lang('Cost of Brand') ?></td>
                                <td class="" style="border-top: 1px solid #cedae6;"><?= lang('Chi Phí Brand') ?></td>
                                <td class="" style="border-top: 1px solid #cedae6;" colspan="2">
                                    <input type="text" onchange="g3Calculation()" name="cost_of_brand" id="cost_of_brand" class="form-control cost_of_brand" readonly value="<?= !empty($arrDataJson['cost_of_brand']) ? ($arrDataJson['cost_of_brand']) : '' ?>">
                                </td>
                                </td>
                            </tr>
                            <tr>
                                <td class=""><?= lang('Labor cost + Management Cost') ?></td>
                                <td class=""><?= lang('Chi Phí QL- Nhân Công') ?></td>
                                <td class="" colspan="2">
                                    <input type="text" onchange="g3Calculation()" name="labor_cost" id="labor_cost" class="form-control labor_cost" readonly value="<?= !empty($arrDataJson['labor_cost']) ? ($arrDataJson['labor_cost']) : '' ?>">
                                </td>
                            </tr>
                            <tr>
                                <td class=""><?= lang('Loss Cost') ?></td>
                                <td class=""><?= lang('Chi Phí Hao Phế các Công Đoạn') ?></td>
                                <td class="" colspan="2">
                                    <input type="text" onchange="g3Calculation()" name="loss_cost" id="loss_cost" class="form-control loss_cost" readonly value="<?= !empty($arrDataJson['loss_cost']) ? ($arrDataJson['loss_cost']) : '' ?>">
                                </td>
                            </tr>
                            <tr>
                                <td class=""><?= lang('Profit') ?></td>
                                <td class=""><?= lang('Lợi Nhuận') ?></td>
                                <td class="" colspan="2">
                                    <input type="text" onchange="g3Calculation()" readonly name="profit" id="profit" class="form-control profit" value="<?= !empty($arrDataJson['profit']) ? ($arrDataJson['profit']) : '' ?>">
                                </td>
                            </tr>
                            <tr class="text-danger">
                                <td class=""><?= lang('G3') ?></td>
                                <td class=""><?= lang('GCPLN') ?></td>
                                <td class="td-percent-g3 text-center">
                                </td>
                                <td class="td-g3 text-right">
                                </td>
                            </tr>
                            <tr class="text-danger">
                                <td class=""><?= lang('G') ?></td>
                                <td class=""><?= lang('G=SUM Tổng=G1+G2+G3') ?></td>
                                <td></td>
                                <td class="td-g text-right">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="99" class="text-right">
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="is_bom" id="is_bom" value="1">
                                        <label for="is_bom"><?= lang('Tạo BOM và giai đoạn cho thành phẩm') ?></label>
                                    </div>
                                    <span class="btn btn-primary" onclick="chonsePrice(this)"><?= lang('Chọn') ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <!-- <button type="submit" class="btn btn-primary add"><?= _l('choose') ?></button> -->
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var dataJson = null;
    var machines = <?= !empty($machines) ? json_encode($machines) : '{}' ?>;

    var counterItemsProducts = <?= $counterItemsProducts ? $counterItemsProducts : 0 ?>;

    function getMachines(_id_selected = 0) {
        optionsMachines = `<option></option>`;
        $.each(machines, function(index, value) {
            is_selected = _id_selected == value.id ? 'selected' : '';
            optionsMachines += `<option ${is_selected} value="${value.id}">"${value.name}</option>`;
        });
        return optionsMachines;
    }

    function productCalculation() {
        layoutCalculation();
    }

    function layoutCalculation() {
        totalItemsPrice();
    }

    function gvcCalculation() {
        g3Calculation();
    }

    function g3Calculation() {
        totalAll();
    }

    function layoutCalculationBackside() {
        totalAll();
    }

    function removeItemsPrice(_this) {
        cTrItemsPrice = $(_this).closest('tr');
        cTrItemsPrice.remove();
        totalItemsPrice();
    }

    function removeItemsPriceProducts(_this) {
        cTrItemsPrice = $(_this).closest('tr');
        cTrItemsPrice.remove();
        totalItemsPrice();
    }

    function totalItemsPrice() {
        gvcCalculation();
    }

    function totalAll() {
        //productCalculation
        height = intVal($('.height').val());
        corner_boundary_height = intVal($('.corner_boundary_height').val());
        perpendicular_border_height = intVal($('.perpendicular_border_height').val());
        round_square_border_height = intVal($('.round_square_border_height').val());
        product_calculation_height = height + corner_boundary_height + perpendicular_border_height + round_square_border_height;
        $('.td-product-calculation-height').html(product_calculation_height);
        $('.td-height_layout_mode').html(product_calculation_height);
        $('.td-height_layout_mode_backside').html(product_calculation_height);

        width = intVal($('.width').val());
        corner_boundary_width = intVal($('.corner_boundary_width').val());
        perpendicular_border_width = intVal($('.perpendicular_border_width').val());
        round_square_border_width = intVal($('.round_square_border_width').val());
        product_calculation_width = width + corner_boundary_width + perpendicular_border_width + round_square_border_width;
        $('.td-product-calculation-width').html(product_calculation_width);
        $('.td-width_layout_mode').html(product_calculation_width);
        $('.td-width_layout_mode_backside').html(product_calculation_width);

        product_calculation_height_width = product_calculation_height + 'x' + product_calculation_width + ' cm';
        $('.td-product-calculation-height-width').html(product_calculation_height_width);

        // layoutCalculation
        //height
        height_layout = intVal($('.height_layout').val());
        height_layout_print_tweezers = intVal($('.height_layout_print_tweezers').val());
        height_layout_boong_cut = intVal($('.height_layout_boong_cut').val());
        height_layout_material_size = height_layout - height_layout_print_tweezers - height_layout_boong_cut;
        $('.td-height_layout_material_size').html(tnhFormatNumber(height_layout_material_size));
        // height_layout_mode = intVal($('.td-height_layout_mode').html());
        height_layout_mode = product_calculation_height;
        height_layout_quantity = 0;
        if (height_layout_material_size != 0) {
            height_layout_quantity = Math.floor(height_layout_material_size / height_layout_mode);
        }
        // $('.td-height_layout_quantity').html(Math.round(height_layout_quantity));
        // $('.td-height_layout_quantity').html(height_layout_quantity.toFixed(2));
        $('.td-height_layout_quantity').html(height_layout_quantity);

        //width
        width_layout = intVal($('.width_layout').val());
        width_layout_print_tweezers = intVal($('.width_layout_print_tweezers').val());
        width_layout_boong_cut = intVal($('.width_layout_boong_cut').val());
        width_layout_material_size = width_layout - width_layout_print_tweezers - width_layout_boong_cut;
        $('.td-width_layout_material_size').html(tnhFormatNumber(width_layout_material_size));
        // width_layout_mode = intVal($('.td-width_layout_mode').html());
        width_layout_mode = product_calculation_width;
        width_layout_quantity = 0;
        if (width_layout_material_size != 0) {
            width_layout_quantity = Math.floor(width_layout_material_size / width_layout_mode);
        }
        // $('.td-width_layout_quantity').html(Math.round(width_layout_quantity));
        // $('.td-width_layout_quantity').html(width_layout_quantity.toFixed(2));
        $('.td-width_layout_quantity').html(width_layout_quantity);

        height_layout_total_quantity = Math.floor(height_layout_quantity * width_layout_quantity);
        // $('.height_layout_total_quantity').html(Math.round(height_layout_total_quantity));
        // $('.height_layout_total_quantity').html(height_layout_total_quantity.toFixed(2));
        $('.height_layout_total_quantity').html(height_layout_total_quantity);

        //backside
        // height_layout_backside = intVal($('.height_layout_backside').val());
        // height_layout_print_tweezers_backside = intVal($('.height_layout_print_tweezers_backside').val());
        // height_layout_boong_cut_backside = intVal($('.height_layout_boong_cut_backside').val());
        // height_layout_material_size_backside = height_layout_backside - height_layout_print_tweezers_backside - height_layout_boong_cut_backside;
        // $('.td-height_layout_material_size_backside').html(height_layout_material_size_backside);
        // height_layout_mode_backside = product_calculation_height;
        // height_layout_quantity_backside = 0;
        // if (height_layout_material_size_backside != 0) {
        //     height_layout_quantity_backside = (height_layout_material_size_backside / height_layout_mode_backside);
        // }
        // $('.td-height_layout_quantity_backside').html(height_layout_quantity_backside.toFixed(2));

        // width_layout_backside = intVal($('.width_layout_backside').val());
        // width_layout_print_tweezers_backside = intVal($('.width_layout_print_tweezers_backside').val());
        // width_layout_boong_cut_backside = intVal($('.width_layout_boong_cut_backside').val());
        // width_layout_material_size_backside = width_layout_backside - width_layout_print_tweezers_backside - width_layout_boong_cut_backside;
        // $('.td-width_layout_material_size_backside').html(width_layout_material_size_backside);

        // width_layout_mode_backside = product_calculation_width;
        // width_layout_quantity_backside = 0;
        // if (width_layout_material_size_backside != 0) {
        //     width_layout_quantity_backside = (width_layout_material_size_backside / width_layout_mode_backside);
        // }
        // $('.td-width_layout_quantity_backside').html(width_layout_quantity_backside.toFixed(2));
        // height_layout_total_quantity_backside = height_layout_quantity_backside * width_layout_quantity_backside;
        // $('.height_layout_total_quantity_backside').html(height_layout_total_quantity_backside.toFixed(2));

        tbItemsPriceBackside = '#table-items-stages-backside tbody tr:not("[class^=not-tr]")';
        var nItemsPriceBackside = $(tbItemsPriceBackside).length;
        var grandTotalSheetBackside = 0;
        for (iI = 0; iI < nItemsPriceBackside; iI++) {
            eItemsPriceBackside = $(tbItemsPriceBackside)[iI];
            number_operate_backside = intVal($(eItemsPriceBackside).find('.number_operate_backside').val());
            price_about_backside = intVal($(eItemsPriceBackside).find('.price_about_backside').val());
            total_sheet_backside = number_operate_backside * price_about_backside;
            $(eItemsPriceBackside).find('.td-total-sheet').html(tnhFormatMoney(total_sheet_backside));
            grandTotalSheetBackside += total_sheet_backside;
        }
        $('.grand-total-sheet-backside').html(tnhFormatMoney(grandTotalSheetBackside));

        // gsp1_backside = 0;
        // if (height_layout_total_quantity_backside > 0) {
        //     gsp1_backside = grandTotalSheetBackside / height_layout_total_quantity;
        // }
        // $('.td-gsp1-backside').html(tnhFormatMoney(gsp1_backside));
        //

        //totalItemsPrice
        tbItemsPrice = '#table-items-stages tbody tr:not("[class^=not-tr]")';
        var nItemsPrice = $(tbItemsPrice).length;
        var grandTotalSheet = 0;
        for (iI = 0; iI < nItemsPrice; iI++) {
            eItemsPrice = $(tbItemsPrice)[iI];
            number_operate = intVal($(eItemsPrice).find('.number_operate').val());
            price_about = intVal($(eItemsPrice).find('.price_about').val());
            total_sheet = number_operate * price_about;
            $(eItemsPrice).find('.td-total-sheet').html(tnhFormatMoney(total_sheet));
            grandTotalSheet += total_sheet;
        }
        $('.grand-total-sheet').html(tnhFormatMoney(grandTotalSheet));

        sum1 = grandTotalSheetBackside + grandTotalSheet;
        $('.sum1').html(tnhFormatMoney(sum1));

        gsp1 = 0;
        // if (height_layout_total_quantity > 0) {
        //     gsp1 = grandTotalSheet / height_layout_total_quantity;
        // }
        // $('.td-gsp1').html(tnhFormatMoney(gsp1));

        tbItemsPriceProducts = '#table-items-products tbody tr:not("[class^=not-tr]")';
        var nItemsProducts = $(tbItemsPriceProducts).length;
        var grandTotalProduct = 0;
        var grandTotalProductCal = 0;
        for (iI = 0; iI < nItemsProducts; iI++) {
            eItemsPriceProducts = $(tbItemsPriceProducts)[iI];
            number_operate_products = intVal($(eItemsPriceProducts).find('.number_operate_products').val());
            price_about_products = intVal($(eItemsPriceProducts).find('.price_about_products').val());
            total_sheet_products = number_operate_products * price_about_products;
            $(eItemsPriceProducts).find('.td-total-sheet-products').html(tnhFormatMoney(total_sheet_products));
            not_cpln = $(eItemsPriceProducts).find('.not_cpln').prop('checked');

            if (!not_cpln) {
                grandTotalProductCal += total_sheet_products;
            }

            grandTotalProduct += total_sheet_products;
        }

        sum2 = grandTotalProduct;
        $('.sum2').html(tnhFormatMoney(sum2));
        g1 = 0;
        g1_cal = 0;
        if (height_layout_total_quantity > 0) {
            g1 = (sum1 + sum2) / height_layout_total_quantity;
            g1_cal = (sum1 + grandTotalProductCal) / height_layout_total_quantity;
        }
        $('.g1').html(tnhFormatMoney(g1));


        // gvcCalculation
        // price_gvc = intVal($('#price_gvc').val());
        // kg_child_gvc = intVal($('#kg_child_gvc').val());
        // price_child_gvc = price_gvc * kg_child_gvc;

        // $('.td-price-child-gvc').html(tnhFormatMoney(price_child_gvc));
        // $('.td-gvc').html(tnhFormatMoney(price_child_gvc));

        total_price_child_gvc = 0;
        tbItemsGVC = '#table-gvc tbody tr:not("[class^=not-tr]")';
        var nItemsGVC = $(tbItemsGVC).length;
        for (iI = 0; iI < nItemsGVC; iI++) {
            eItemsGVC = $(tbItemsGVC)[iI];
            price_gvc = intVal($(eItemsGVC).find('.price_gvc').val());
            kg_child_gvc = intVal($(eItemsGVC).find('.kg_child_gvc').val());
            price_child_gvc = price_gvc * kg_child_gvc;
            $(eItemsGVC).find('.td-price-child-gvc').html(tnhFormatMoney(price_child_gvc));
            total_price_child_gvc += price_child_gvc;
        }
        $('.td-g2').html(tnhFormatMoney(total_price_child_gvc));
        g2 = total_price_child_gvc;

        // gsp1 = intVal($('.td-gsp1').html());
        // ggc = intVal($('#processing_price').val());
        // gsp2 = gsp1 + ggc + price_child_gvc;
        // $('.td-gsp2').html(tnhFormatMoney(gsp2));

        cost_of_brand = intVal($('.cost_of_brand').val());
        labor_cost = intVal($('.labor_cost').val());
        loss_cost = intVal($('.loss_cost').val());
        profit = intVal($('.profit').val());

        total_precent = cost_of_brand + labor_cost + loss_cost + profit;
        // gsp2 = intVal($('.td-gsp2').html());

        $('.td-percent-g3').html(total_precent + '%');
        // g3 = (g1 + g2) * total_precent / 100;
        g3 = (g1_cal + g2) * total_precent / 100;

        $('.td-g3').html(tnhFormatMoney(g3));

        g = g1 + g2 + g3;
        $('.td-g').html(tnhFormatMoney(g));

        //more
        tbItemsInfoNPL = '#tb-item-info-npl tbody tr:not("[class^=not-tr]")';
        var nItemsInfoNPL = $(tbItemsInfoNPL).length;
        for (iI = 0; iI < nItemsInfoNPL; iI++) {
            var eItemsInfoNPL = $(tbItemsInfoNPL)[iI];
            var price_page = intVal($(eItemsInfoNPL).find('.price_page').val());
            var price_xlt = intVal($(eItemsInfoNPL).find('.price_xlt').val());

            var total_money = price_page + price_xlt;
            $(eItemsInfoNPL).find('.total_money').html(tnhFormatMoney(total_money));
        }

        //Dàn trang
        var tbItemsLayout = '#tb-item-layout tbody tr:not("[class^=not-tr]")';
        var nItemsLayout = $(tbItemsLayout).length;
        for (iI = 0; iI < nItemsLayout; iI++) {
            var eI = $(tbItemsLayout)[iI];
            var number_operations_page_f1 = intVal($(eI).find('.number_operations_page_f1').val());
            var number_operations_page_f2 = intVal($(eI).find('.number_operations_page_f2').val());
            var total_operations_page = number_operations_page_f1 + number_operations_page_f2;
            var price = intVal($(eI).find('.price').val());
            var total_price = total_operations_page * price;

            $(eI).find('.total_operations_page').html(tnhFormatNumber(total_operations_page));
            $(eI).find('.total_price').html(tnhFormatMoney(total_price));
        }

        //plus stage
        $.each($('.tb-plus-stage tbody tr'), function(index, value) {
            var total_npl_f1 = intVal($(value).find('.total_npl_f1').val());
            var total_npl_f2 = intVal($(value).find('.total_npl_f2').val());
            var total_time_npl_f1 = intVal($(value).find('.total_time_npl_f1').val());
            var total_time_npl_f2 = intVal($(value).find('.total_time_npl_f2').val());
            var total_npl_f12 = total_npl_f1 + total_npl_f2;
            var total_time_f12 = total_time_npl_f1 + total_time_npl_f2;
            var price = intVal($(value).find('.price').val());

            var number_operating_side_f1 = intVal($(value).find('.number_operating_side_f1').val());
            var number_operating_side_f2 = intVal($(value).find('.number_operating_side_f2').val());
            var total_number_operating_side = number_operating_side_f1 + number_operating_side_f2;
            var price_page = total_number_operating_side * price;
            $(value).find('.td-total-npl-f12').html(tnhFormatNumber(total_npl_f12));
            $(value).find('.td-total-time-f12').html(tnhFormatNumber(total_time_f12));
            $(value).find('.td-price-page').html(tnhFormatMoney(price_page));
        });
        //

        handlingReferencePrice();
    }
    var data_count = 0;

    function createItemsPrice(dataPrice, dataStages) {
        if (!dataPrice) return '';

        checkbox = `<td>
                        <div class="checkbox"><input onchange="changecheckbox()" type="checkbox" class="check-itemprice" value="${data_count}"><label for="check-item"></label></div>
                    </td>`;
        tdCategoryStages = `<td>
            <input type="hidden"  class="form-control data_count" value="${data_count}">
            <input type="hidden" name="type_price[]" class="form-control type_price" value="${dataPrice.type}">
            <input type="hidden" name="item_id_price[]" class="form-control item_id_price" value="${dataPrice.id}">
            <input type="hidden" name="stage_id_price[]" class="form-control item_id_price" value="${dataStages.id}">
            <div>${dataPrice.type == 'materials' ?  dataPrice.name+'('+dataPrice.code+')' : ''}</div>
            ${dataPrice.is_single_use > 0 ? '<div class="text-danger">Duy nhất</div>' : ''}
        </td>`;

        tdStages = `<td class="text-center">
            <div>${dataStages.name}</div>
        </td>`;

        tdUnits = `<td><div class="text-center">${dataPrice.unit_name}</div></td>`;
        tdMode = `<td><div class="text-center">${dataPrice.mode}</div></td>`;
        tdColor = `<td>
            <input type="text" name="quantity_color[]" placeholder="<?= lang('SL màu in') ?>" onchange="totalItemsPrice()" place class="form-control quantity_color number-format" value="0">
        </td>`;
        tdNumberOperate = `<td class="hide"> 
            <input type="text" name="number_operate[]" placeholder="<?= lang('Số lần vận hành') ?>" onchange="totalItemsPrice()" place class="form-control number_operate number-format" value="1">
        </td>`;

        // tdMachine = `<td>
        //     <input type="text" name="machine[]" onchange="totalItemsPrice()" placeholder="<?= lang('Thiết bị') ?>" class="form-control machine" value="">
        // </td>`;

        tdMachine = `<td>
            <select name="machine[]" onchange="totalItemsPrice()" id="machine_${data_count}" data-placeholder="<?= lang('Thiết bị') ?>" style="width: 100%;" class="machine">
                ${getMachines()}
            </select>
        </td>`;

        tdTypeNPL = `<td>
            <input type="text" name="type_npl[]" onchange="totalItemsPrice()" placeholder="<?= lang('Loại NPL') ?>" class="form-control type_npl" value="">
        </td>`;
        tdQuotaBOM = `<td>
            <input type="text" name="quota_bom[]" onchange="totalItemsPrice()" placeholder="<?= lang('Định mức BOM') ?>" class="form-control quota_bom number-format" value="">
        </td>`;

        tdPriceAbout = `<td>
            <input type="text" name="price_about[]" placeholder="<?= lang('Đơn giá NPL') ?>" onchange="totalItemsPrice()" class="form-control price_about money-format" value="${tnhFormatMoney(dataPrice.price_sell)}">
        </td>`;
        // ${tnhFormatMoney(dataPrice.price_sell)}
        tdTotalSheet = `<td class="td-total-sheet text-right">
            ${tnhFormatMoney(dataPrice.price_sell)}
        </td>`;
        tdActions = `<td class="text-center"><i onclick="removeItemsPrice(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        cTrPrice = `<tr>
            ${checkbox}
            ${tdCategoryStages}
            ${tdStages}
            ${tdUnits}
            ${tdMode}
            ${tdColor}
            ${tdNumberOperate}
            ${tdMachine}
            ${tdTypeNPL}
            ${tdQuotaBOM}
            ${tdPriceAbout}
            ${tdTotalSheet}
            ${tdActions}
        </tr>`;
        data_count++;

        return cTrPrice;
    }
    var quote_stage_id = "<?= $quote_stage_id ?>";

    function addItemPriceQuotes() {
        material_price_quotes = $('#material_price_quotes').val();
        stages_price_quotes = $('#stages_price_quotes').val();


        if (!material_price_quotes || !stages_price_quotes) {
            alert_float('danger', 'Vui lòng chọn nguyên phụ liệu và công đoạn');
            return;
        }

        if (material_price_quotes && !stages_price_quotes) {
            alert_float('danger', 'Vui lòng chọn nguyên phụ liệu và công đoạn');
            return;
        }

        if (!stages_price_quotes) {
            alert_float('danger', 'Vui lòng chọn công đoạn');
            return;
        }



        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['material_price_quotes'] = material_price_quotes;
        dataPOST['stages_price_quotes'] = stages_price_quotes;
        dataPOST['height_layout'] = intVal($('.height_layout').val());
        dataPOST['width_layout'] = intVal($('.width_layout').val());


        dataPOST['quote_stage_id'] = quote_stage_id;



        dataPOST['group_id'] = $('#group_id').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemPriceQuotes',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                trItemsQuotes = '';
                if (response.items_material_price_quotes && response.items_material_price_quotes.length > 0) {
                    $.each(response.items_material_price_quotes, function(index, value) {
                        trItemsQuotes += createItemsPrice(value, response.items_stages_price_quotes[index]);
                    });
                }

                if (response.items_material_price_quotes.length == 0) {
                    if (response.items_stages_price_quotes && response.items_stages_price_quotes.length > 0) {
                        $.each(response.items_stages_price_quotes, function(index, value) {
                            trItemsQuotes += createItemsPrice(value, value);
                        });
                    }
                }

                $('#table-items-stages tbody').append(trItemsQuotes);
                $('select.machine').select2({
                    'allowClear': true
                });
                totalItemsPrice();
                // init_selectpicker();
            }
        });
    }

    function addItemPriceQuotesProducts() {
        stages_price_quotes_products = $('#stages_price_quotes_products').val();
        if (!stages_price_quotes_products) {
            alert_float('danger', 'Vui lòng chọn công đoạn thành phẩm');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['stages_price_quotes'] = stages_price_quotes_products;
        dataPOST['height_layout'] = intVal($('.height_layout').val());
        dataPOST['width_layout'] = intVal($('.width_layout').val());
        dataPOST['group_id'] = $('#group_id').val();
        dataPOST['id_customer'] = $('#id_div_customer').data('id');
        dataPOST['height'] = $('.height').val();
        dataPOST['width'] = $('.width').val();

        dataPOST['quote_stage_id'] = quote_stage_id;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemPriceQuotes',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                trItemsQuotes = '';
                if (response.items_material_price_quotes.length == 0) {
                    if (response.items_stages_price_quotes && response.items_stages_price_quotes.length > 0) {
                        $.each(response.items_stages_price_quotes, function(index, value) {
                            dataPrice = value;
                            tdCategoryStages = `<td>
                                <input type="hidden" name="type_price_products[${counterItemsProducts}]" class="form-control type_price_products" value="${dataPrice.type}">
                                <input type="hidden" name="item_id_price_products[${counterItemsProducts}]" class="form-control item_id_price_products" value="${dataPrice.id}">
                                <input type="hidden" name="stage_id_price_products[${counterItemsProducts}]" class="form-control stage_id_price_products" value="${dataPrice.id}">
                                <div>${dataPrice.name}</div>
                                <div class="checkbox checkbox-danger">
                                    <input type="checkbox" onchange="totalAll()"  class="not_cpln" name="not_cpln[${counterItemsProducts}]" id="not_cpln_${counterItemsProducts}" value="1">
                                    <label for="not_cpln_${counterItemsProducts}">Không tính CPLN</label>
                                </div>
                            </td>`;

                            tdLongHeight = `<td>
                                <input type="text" name="long_height[${counterItemsProducts}]" placeholder="<?= lang('Dài/Cao') ?>" onchange="totalItemsPrice()" place class="form-control long_height" value="">
                            </td>`;

                            tdWidthHorizontal = `<td>
                                <input type="text" name="width_horizontal[${counterItemsProducts}]" placeholder="<?= lang('Rộng/Ngang') ?>" onchange="totalItemsPrice()" place class="form-control width_horizontal" value="">
                            </td>`;

                            tdMode = `<td><div class="text-center">${dataPrice.mode}</div></td>`;
                            tdQC = `<td><div class="text-center"></div></td>`;
                            tdNumberOperate = `<td>
                                <input type="text" name="number_operate_products[${counterItemsProducts}]" placeholder="<?= lang('Số Lần Xả/Vận Hành') ?>" onchange="totalItemsPrice()" place class="form-control number_operate_products number-format" value="1">
                            </td>`;

                            tdFaceProducts = `<td>
                                <input type="text" name="face_products[${counterItemsProducts}]" placeholder="<?= lang('Mặt in') ?>" onchange="totalItemsPrice()" class="form-control face_products number-format" value="">
                            </td>`;

                            tdPriceAbout = `<td>
                                <input type="text" name="price_about_products[${counterItemsProducts}]" placeholder="<?= lang('Đơn giá CĐ') ?>" onchange="totalItemsPrice()" readonly class="form-control price_about_products money-format" value="${tnhFormatMoney(dataPrice.price_sell)}">
                            </td>`;

                            tdTotalSheet = `<td class="td-total-sheet-products text-right">
                                ${tnhFormatMoney(dataPrice.price_sell)}
                            </td>`;
                            tdActions = `<td class="text-center"><i onclick="removeItemsPriceProducts(this)" class="fa fa-remove text-danger pointer"></i></td>`;

                            trItemsQuotes = `<tr>
                                ${tdCategoryStages}
                                ${tdLongHeight}
                                ${tdWidthHorizontal}
                                ${tdMode}
                                ${tdQC}
                                ${tdNumberOperate}
                                ${tdFaceProducts}
                                ${tdPriceAbout}
                                ${tdTotalSheet}
                                ${tdActions}
                            </tr>`;

                            counterItemsProducts++;
                        });
                    }
                }

                $('#table-items-products tbody').append(trItemsQuotes);
                totalItemsPrice();
            }
        });

    }

    function removeOutTransport(_this) {
        $(_this).closest('tr').remove();
        totalItemsPrice();
    }

    function addOutTransport() {

        tdNumber = `<td class="text-center"><a href="javascript:void(0)" onclick="removeOutTransport(this)" class="text-danger fa fa-remove"></a></td>`;
        tdTypeVC = `<td>
            <input type="text" placeholder="<?= lang('Tên gia công - Vận chuyển') ?>" onchange="gvcCalculation()" name="type_vc[]" class="form-control" value="">
        </td>`;
        tdUnitKg = `<td>
            <input type="text" placeholder="<?= lang('ĐVT') ?>" name="unit_kg[]" onchange="gvcCalculation()" class="form-control" value="">
        </td>`;
        tdPriceGvc = `<td>
            <input type="text" placeholder="<?= lang('Đơn giá') ?>" onchange="gvcCalculation()" name="price_gvc[]" id="price_gvc" class="form-control price_gvc money-format" value="">
        </td>`;
        tdKgChildGvc = `<td>
            <input type="text" placeholder="<?= lang('KG/Con') ?>" onchange="gvcCalculation()" name="kg_child_gvc[]" class="form-control kg_child_gvc number-format" value="">
        </td>`;
        tdTotalPriceGvc = `<td class="td-price-child-gvc text-right text-danger">
        </td>`;

        trOutTransport = `<tr>
            ${tdNumber}
            ${tdTypeVC}
            ${tdUnitKg}
            ${tdPriceGvc}
            ${tdKgChildGvc}
            ${tdTotalPriceGvc}
        </tr>`;
        $('#table-gvc tbody').append(trOutTransport);
        totalItemsPrice();
    }

    function createItemsPriceBackside(dataPrice, dataStages) {
        if (!dataPrice) return '';

        tdCategoryStages = `<td>
            <input type="hidden" name="type_price_backside[]" class="form-control type_price_backside" value="${dataPrice.type}">
            <input type="hidden" name="item_id_price_backside[]" class="form-control item_id_price_backside" value="${dataPrice.id}">
            <input type="hidden" name="stage_id_price_backside[]" class="form-control item_id_price_backside" value="${dataStages.id}">
            <div>${dataPrice.type == 'materials' ?  dataPrice.name+'('+dataPrice.code+')' : ''}</div>
            ${dataPrice.is_single_use > 0 ? '<div class="text-danger">Duy nhất</div>' : ''}
        </td>`;

        tdStages = `<td class="text-center">
            <div>${dataStages.name}</div>
        </td>`;

        tdUnits = `<td><div class="text-center">${dataPrice.unit_name}</div></td>`;
        tdMode = `<td><div class="text-center">${dataPrice.mode}</div></td>`;
        tdColor = `<td>
            <input type="text" name="quantity_color_backside[]" placeholder="<?= lang('SL màu in') ?>" onchange="totalItemsPrice()" place class="form-control quantity_color_backside number-format" value="0">
        </td>`;
        tdNumberOperate = `<td class="hide">
            <input type="text" name="number_operate_backside[]" placeholder="<?= lang('Số lần vận hành') ?>" onchange="totalItemsPrice()" place class="form-control number_operate_backside number-format" value="1">
        </td>`;

        // tdMachine = `<td>
        //     <input type="text" name="machine_backside[]" onchange="totalItemsPrice()" placeholder="<?= lang('Thiết bị') ?>" class="form-control machine_backside" value="">
        // </td>`;

        tdMachine = `<td>
            <select name="machine_backside[]" onchange="totalItemsPrice()" data-placeholder="<?= lang('Thiết bị') ?>" class="machine_backside modal-select2" style="width: 100%;">
                ${getMachines()}
            </select>
        </td>`;

        tdTypeNPL = `<td>
            <input type="text" name="type_npl_backside[]" onchange="totalItemsPrice()" placeholder="<?= lang('Loại NPL') ?>" class="form-control type_npl_backside" value="">
        </td>`;
        tdQuotaBOM = `<td>
            <input type="text" name="quota_bom_backside[]" onchange="totalItemsPrice()" placeholder="<?= lang('Định mức BOM') ?>" class="form-control quota_bom_backside number-format" value="">
        </td>`;

        tdPriceAbout = `<td>
            <input type="text" name="price_about_backside[]" placeholder="<?= lang('Đơn giá NPL') ?>" onchange="totalItemsPrice()" class="form-control price_about_backside money-format" value="${tnhFormatMoney(dataPrice.price_sell)}">
        </td>`;
        // ${tnhFormatMoney(dataPrice.price_sell)}
        tdTotalSheet = `<td class="td-total-sheet text-right">
            ${tnhFormatMoney(dataPrice.price_sell)}
        </td>`;
        tdActions = `<td class="text-center"><i onclick="removeItemsPrice(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        cTrPrice = `<tr>
            ${tdCategoryStages}
            ${tdStages}
            ${tdUnits}
            ${tdMode}
            ${tdColor}
            ${tdNumberOperate}
            ${tdMachine}
            ${tdTypeNPL}
            ${tdQuotaBOM}
            ${tdPriceAbout}
            ${tdTotalSheet}
            ${tdActions}
        </tr>`;

        return cTrPrice;
    }

    function addItemPriceQuotesBackside() {
        material_price_quotes_backside = $('#material_price_quotes_backside').val();
        stages_price_quotes_backside = $('#stages_price_quotes_backside').val();

        if (!material_price_quotes_backside || !stages_price_quotes_backside) {
            alert_float('danger', 'Vui lòng chọn nguyên phụ liệu và công đoạn');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['material_price_quotes'] = material_price_quotes_backside;
        dataPOST['stages_price_quotes'] = stages_price_quotes_backside;

        dataPOST['quote_stage_id'] = quote_stage_id;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemPriceQuotes',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                trItemsQuotes = '';
                if (response.items_material_price_quotes && response.items_material_price_quotes.length > 0) {
                    $.each(response.items_material_price_quotes, function(index, value) {
                        trItemsQuotes += createItemsPriceBackside(value, response.items_stages_price_quotes[index]);
                    });
                }

                if (response.items_material_price_quotes.length == 0) {
                    if (response.items_stages_price_quotes && response.items_stages_price_quotes.length > 0) {
                        $.each(response.items_stages_price_quotes, function(index, value) {
                            trItemsQuotes += createItemsPriceBackside(value, value);
                        });
                    }
                }

                $('#table-items-stages-backside tbody').append(trItemsQuotes);
                // init_selectpicker();
                $('select.machine_backside').select2({
                    'allowClear': true
                });
                totalItemsPrice();
            }
        });
    }

    function handlingReferencePrice() {
        var form = $('#handling-price'),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });


        $.ajax({
                url: site.base_url + 'admin/handling_price/handlingReferencePrice',
                type: "POST",
                dataType: "JSON",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    dataJson = data.dataJson;
                    var parsedData = JSON.parse(dataJson);
                    if (parsedData.itemsStagesProducts) {
                        $.each(parsedData.itemsStagesProducts, function(index, item) {
                            var row = $('#table-items-products tbody tr').eq(index);
                            if (row.length) {
                                row.find('.price_about_products').val(tnhFormatNumber(item.price_about_products));
                                row.find('.td-total-sheet-products').html(tnhFormatMoney(item.total_sheet_products));
                            }
                        });
                    }
                    if (parsedData.sum2 !== undefined) {
                        $('.sum2').html(tnhFormatMoney(parsedData.sum2));
                    }
                    if (parsedData.g1 !== undefined) {
                        $('.g1').html(tnhFormatMoney(parsedData.g1));
                    }
                    if (parsedData.g2 !== undefined) {
                        $('.td-g2').html(tnhFormatMoney(parsedData.g2));
                    }
                    if (parsedData.g3 !== undefined) {
                        $('.td-g3').html(tnhFormatMoney(parsedData.g3));
                    }
                    if (parsedData.g !== undefined) {
                        $('.td-g').html(tnhFormatMoney(parsedData.g));
                    }
                } else {}
            })
            .fail(function() {});
        return false;
    }

    function chonsePrice(_this) {
        var form = $('#handling-price'),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        $(_this).attr('disabled', 'disabled');


        $.ajax({
                url: site.base_url + 'admin/handling_price/saveBOM',
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(response) {
                if (response.result == 1) {
                    cTrPrice = $(_this).closest('tr');
                    cPrice = intVal($('.td-g').html());
                    cTrChonse.find('.price').val(tnhFormatMoney(cPrice)).trigger('change');
                    alert_float('success', response.message);
                    cTrChonse.find('.data_json').val(dataJson);
                    $('.modal-price-list .close').trigger('click');
                } else {
                    alert_float('danger', response.message);
                }
                $(_this).removeAttr('disabled');
            })
            .fail(function() {
                alert_float('danger', 'Lỗi');
                $(_this).removeAttr('disabled');
            });
    }

    $(function() {
        customer_id_price = $('#customers').val();
        ajaxSelectParamsCallback('#product_quote_reference', 'admin/handling_price/getQuotesProductsPrice', $('#product_quote_reference').val(), {
            customer_id_price: customer_id_price
        }, true);
        $('#stages_price_quotes').select2();
        ajaxSelectParamsCallback('#material_price_quotes', 'admin/items/searchSelect2Materials', 0, false, true);

        $('#stages_price_quotes_backside').select2();
        ajaxSelectParamsCallback('#material_price_quotes_backside', 'admin/items/searchSelect2Materials', 0, false, true);

        $('#stages_price_quotes_products').select2();
        totalAll();

        $('#product_quote_reference').change(function(event) {
            product_quote_reference = $(this).val();
            if (product_quote_reference) {
                $.ajax({
                    type: "GET",
                    url: site.base_url + 'admin/handling_price/getReferenceProductQuota',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        product_quote_reference: product_quote_reference,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (typeof response.arrDataJson !== 'undefined' && response.arrDataJson != null) {
                            arrDataJsonRe = response.arrDataJson;

                            if (typeof arrDataJsonRe.height != "undefined") {
                                $('.height').val(arrDataJsonRe.height);
                            }

                            if (typeof arrDataJsonRe.corner_boundary_height != "undefined") {
                                $('.corner_boundary_height').val(arrDataJsonRe.corner_boundary_height);
                            }

                            if (typeof arrDataJsonRe.perpendicular_border_height != "undefined") {
                                $('.perpendicular_border_height').val(arrDataJsonRe.perpendicular_border_height);
                            }

                            if (typeof arrDataJsonRe.round_square_border_height != "undefined") {
                                $('.round_square_border_height').val(arrDataJsonRe.round_square_border_height);
                            }

                            if (typeof arrDataJsonRe.product_calculation_height != "undefined") {
                                $('.product_calculation_height').val(arrDataJsonRe.product_calculation_height);
                            }

                            if (typeof arrDataJsonRe.width != "undefined") {
                                $('.width').val(arrDataJsonRe.width);
                            }

                            if (typeof arrDataJsonRe.corner_boundary_width != "undefined") {
                                $('.corner_boundary_width').val(arrDataJsonRe.corner_boundary_width);
                            }

                            if (typeof arrDataJsonRe.perpendicular_border_width != "undefined") {
                                $('.perpendicular_border_width').val(arrDataJsonRe.perpendicular_border_width);
                            }

                            if (typeof arrDataJsonRe.round_square_border_width != "undefined") {
                                $('.round_square_border_width').val(arrDataJsonRe.round_square_border_width);
                            }

                            if (typeof arrDataJsonRe.product_calculation_width != "undefined") {
                                $('.product_calculation_width').val(arrDataJsonRe.product_calculation_width);
                            }

                            if (typeof arrDataJsonRe.product_calculation_width != "undefined") {
                                $('.height_layout').val(arrDataJsonRe.height_layout);
                            }

                            if (typeof arrDataJsonRe.height_layout_print_tweezers != "undefined") {
                                $('.height_layout_print_tweezers').val(arrDataJsonRe.height_layout_print_tweezers);
                            }

                            if (typeof arrDataJsonRe.height_layout_boong_cut != "undefined") {
                                $('.height_layout_boong_cut').val(arrDataJsonRe.height_layout_boong_cut);
                            }

                            if (typeof arrDataJsonRe.height_layout_material_size != "undefined") {
                                $('.height_layout_material_size').val(arrDataJsonRe.height_layout_material_size);
                            }

                            if (typeof arrDataJsonRe.height_layout_mode != "undefined") {
                                $('.height_layout_mode').val(arrDataJsonRe.height_layout_mode);
                            }

                            if (typeof arrDataJsonRe.height_layout_quantity != "undefined") {
                                $('.height_layout_quantity').val(Math.round(arrDataJsonRe.height_layout_quantity));
                            }

                            if (typeof arrDataJsonRe.height_layout_total_quantity != "undefined") {
                                $('.height_layout_total_quantity').val(Math.round(arrDataJsonRe.height_layout_total_quantity));
                            }

                            if (typeof arrDataJsonRe.width_layout != "undefined") {
                                $('.width_layout').val(arrDataJsonRe.width_layout);
                            }

                            if (typeof arrDataJsonRe.width_layout_print_tweezers != "undefined") {
                                $('.width_layout_print_tweezers').val(arrDataJsonRe.width_layout_print_tweezers);
                            }

                            if (typeof arrDataJsonRe.width_layout_boong_cut != "undefined") {
                                $('.width_layout_boong_cut').val(arrDataJsonRe.width_layout_boong_cut);
                            }

                            if (typeof arrDataJsonRe.width_layout_material_size != "undefined") {
                                $('.width_layout_material_size').val(arrDataJsonRe.width_layout_material_size);
                            }

                            if (typeof arrDataJsonRe.width_layout_mode != "undefined") {
                                $('.width_layout_mode').val(arrDataJsonRe.width_layout_mode);
                            }

                            if (typeof arrDataJsonRe.width_layout_quantity != "undefined") {
                                $('.width_layout_quantity').val(Math.round(arrDataJsonRe.width_layout_quantity));
                            }

                            if (typeof response.trItemPrice != "undefined") {
                                $('#table-items-stages tbody').html(response.trItemPrice);
                            }

                            // if (typeof arrDataJsonRe.grandTotalSheet != "undefined") {
                            //     $('.grand-total-sheet').val(tnhFormatMoney(arrDataJsonRe.grandTotalSheet));
                            // }

                            if (typeof arrDataJsonRe.ggc != "undefined") {
                                $('#processing_price').val(arrDataJsonRe.ggc)
                            }

                            if (typeof arrDataJsonRe.type_vc != "undefined") {
                                $('#type_vc').val(arrDataJsonRe.type_vc);
                            }

                            if (typeof arrDataJsonRe.unit_kg != "undefined") {
                                $('#unit_kg').val(arrDataJsonRe.unit_kg);
                            }

                            if (typeof arrDataJsonRe.price_gvc != "undefined") {
                                $('.price_gvc').val(tnhFormatMoney(arrDataJsonRe.price_gvc));
                            }

                            if (typeof arrDataJsonRe.kg_child_gvc != "undefined") {
                                $('.kg_child_gvc').val(tnhFormatNumber(arrDataJsonRe.kg_child_gvc));
                            }

                            if (typeof arrDataJsonRe.price_child_gvc != "undefined") {
                                $('.price_child_gvc').val(tnhFormatMoney(arrDataJsonRe.price_child_gvc));
                            }

                            if (typeof arrDataJsonRe.labor_cost != "undefined") {
                                $('.labor_cost').val((arrDataJsonRe.labor_cost));
                            }

                            if (typeof arrDataJsonRe.loss_cost != "undefined") {
                                $('.loss_cost').val((arrDataJsonRe.loss_cost));
                            }

                            if (typeof arrDataJsonRe.profit != "undefined") {
                                $('.profit').val((arrDataJsonRe.profit));
                            }
                        }
                        totalAll();
                    }
                });
            }
        });
    })
</script>

<script>
    function loadExpenseQuote(_this) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['customer_id'] = $('#id_div_customer').data('id');
        dataPOST['quote_stage_id'] = quote_stage_id;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/loadExpenseQuote',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                $('#cost_of_brand').val(response?.stage_quote_client?.cost_of_brand ?? 0);
                $('#labor_cost').val(response?.stage_quote_client?.labor_cost ?? 0);
                $('#loss_cost').val(response?.stage_quote_client?.loss_cost ?? 0);
                $('#profit').val(response?.stage_quote_client?.profit ?? 0);
                totalAll();
            }
        });
    }

    $(document).ready(function() {
        // if (counterItemsProducts == 0) {
        loadExpenseQuote();
        // }

        $('select.machine').select2({
            'allowClear': true
        });
        $('select.machine_backside').select2({
            'allowClear': true
        });
    });
</script>

<script>
    var ctItemsInfoNPL = <?= $ctItemsInfoNPL ? $ctItemsInfoNPL : 0 ?>;
    $(document).ready(function() {
        ajaxSelectParamsCallback('#material_info_npl', 'admin/items/searchSelect2Materials', 0, false, true);
    });

    function createItemMaterialInfoNPL(dataItem) {
        if (!dataItem) return '';

        tdItemCode = `<td>
            <input type="hidden" name="items_npl[${ctItemsInfoNPL}][item_id]" class="form-control" value="${dataItem.id}">
            ${dataItem.code}
        </td>`;

        tdItemName = `<td>
            ${dataItem.name}
        </td>`;

        tdHeight = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="" title="">
        </td>`;

        tdWidth = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="" title="">
        </td>`;

        tdUnitMeasureSP = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][unit_measure_sp]" onchange="handlingReferencePrice()" placeholder="ĐV đo SP" class="form-control unit_measure_sp" value="" title="">
        </td>`;

        tdUnitCalculationSP = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][unit_calculation_sp]" onchange="handlingReferencePrice()" placeholder="ĐV tính SP" class="form-control unit_calculation_sp" value="" title="">
        </td>`;

        tdHeight1 = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][height1]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height1" value="" title="">
        </td>`;

        tdLeaveMargin = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][leave_margin]" onchange="handlingReferencePrice()" placeholder="Chừa biên" class="form-control number-format leave_margin" value="" title="">
        </td>`;

        tdWidth1 = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][width1]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width1" value="" title="">
        </td>`;

        tdLeaveMargin1 = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][leave_margin1]" onchange="handlingReferencePrice()" placeholder="Chừa biên" class="form-control number-format leave_margin1" value="" title="">
        </td>`;

        tdUnitMeasureSP1 = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][unit_measure_sp1]" onchange="handlingReferencePrice()" placeholder="ĐV đo SP" class="form-control unit_measure_sp1" value="" title="">
        </td>`;

        tdUnitCalculationSP1 = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][unit_calculation_sp1]" onchange="handlingReferencePrice()" placeholder="ĐV tính SP" class="form-control unit_calculation_sp1" value="" title="">
        </td>`;

        tdLeaveTweezers = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][leave_tweezers]" onchange="handlingReferencePrice()" placeholder="Chừa nhíp" class="form-control number-format leave_tweezers" value="" title="">
        </td>`;

        tdLeaveDischargeW = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][leave_discharge_w]" onchange="handlingReferencePrice()" placeholder="Chừa xả width" class="form-control number-format leave_discharge_w" value="" title="">
        </td>`;

        tdLeaveDischargeH = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][leave_discharge_h]" onchange="handlingReferencePrice()" placeholder="Chừa xả height" class="form-control number-format leave_discharge_h" value="" title="">
        </td>`;

        tdTotalChildW = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][total_child_w]" onchange="handlingReferencePrice()" placeholder="Tổng số con width" class="form-control number-format total_child_w" value="" title="">
        </td>`;

        tdTotalChildH = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][total_child_h]" onchange="handlingReferencePrice()" placeholder="Tổng số con height" class="form-control number-format total_child_h" value="" title="">
        </td>`;

        tdTotalChildPage = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][total_child_page]" onchange="handlingReferencePrice()" placeholder="Tổng số con/Tờ" class="form-control number-format total_child_page" value="" title="">
        </td>`;

        tdPricePage = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][price_page]" onchange="totalAll()" placeholder="Giá/Tờ(VNĐ)" class="form-control money-format price_page" value="" title="">
        </td>`;

        tdPriceXLT = `<td>
            <input type="text" name="items_npl[${ctItemsInfoNPL}][price_xlt]" onchange="totalAll()" placeholder="Giá/XLT In/Tờ" class="form-control money-format price_xlt" value="" title="">
        </td>`;

        tdTotalMoney = `<td class="total_money text-right">
            
        </td>`;

        tdActions = `<td class="text-center"><i onclick="removeItemMaterialInfoNPL(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        var _cTrItem = `<tr>
            ${tdItemCode}
            ${tdItemName}
            ${tdHeight}
            ${tdWidth}
            ${tdUnitMeasureSP}
            ${tdUnitCalculationSP}
            ${tdHeight1}
            ${tdLeaveMargin}
            ${tdWidth1}
            ${tdLeaveMargin1}
            ${tdUnitMeasureSP1}
            ${tdUnitCalculationSP1}
            ${tdLeaveTweezers}
            ${tdLeaveDischargeW}
            ${tdLeaveDischargeH}
            ${tdTotalChildW}
            ${tdTotalChildH}
            ${tdTotalChildPage}
            ${tdPricePage}
            ${tdPriceXLT}
            ${tdTotalMoney}
            ${tdActions}
        </tr>`;

        return _cTrItem;
    }

    function removeItemMaterialInfoNPL(_this) {
        var cTrItem = $(_this).closest('tr');
        cTrItem.remove();
        totalAll();
    }

    function addItemMaterialInfoNPL() {
        material_info_npl = $('#material_info_npl').val();
        if (!material_info_npl) {
            alert_float('danger', 'Vui lòng chọn nguyên phụ liệu');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['material_info_npl'] = material_info_npl;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemMaterialInfoNPL',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                var cTrItemNPL = '';
                if (response.items_material_info_npl && response.items_material_info_npl.length > 0) {
                    $.each(response.items_material_info_npl, function(index, value) {
                        cTrItemNPL += createItemMaterialInfoNPL(value);
                    });
                }

                $('#tb-item-info-npl tbody').append(cTrItemNPL);
                totalAll();
            }
        });
    }
</script>
<script>
    var ctItemsInspectionStage = <?= $ctItemsInspectionStage ? $ctItemsInspectionStage : 0 ?>;
    $(document).ready(function() {
        $('#inspection_stage').select2({
            'allowClear': true
        });
    });

    function createItemInspectionStage(dataItem) {
        if (!dataItem) return '';

        tdItemCode = `<td>
            <input type="hidden" name="items_istage[${ctItemsInspectionStage}][category_stage_id]" class="form-control" value="${dataItem.id}">
            ${dataItem.code}(${dataItem.name})
        </td>`;

        tdHeight = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="" title="">
        </td>`;

        tdWidth = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="" title="">
        </td>`;

        tdUnitF1 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][unit_f1]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit_f1" value="" title="">
        </td>`;

        tdTypeCheckF1 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][type_check_f1]" onchange="handlingReferencePrice()" placeholder="Loại Kiểm" class="form-control type_check_f1" value="" title="">
        </td>`;

        tdNumberOSideF1 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][number_o_side_f1]" onchange="handlingReferencePrice()" placeholder="Số lần vận hành/mặt" class="form-control number_o_side_f1 number-format" value="" title="">
        </td>`;

        tdProductivityNormsF1 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][productivity_norms_f1]" onchange="handlingReferencePrice()" placeholder="định mức năng suất" class="form-control productivity_norms_f1 number-format" value="" title="">
        </td>`;

        tdUnitF2 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][unit_f2]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit_f2" value="" title="">
        </td>`;

        tdTypeCheckF2 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][type_check_f2]" onchange="handlingReferencePrice()" placeholder="Loại Kiểm" class="form-control type_check_f2" value="" title="">
        </td>`;

        tdNumberOSideF2 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][number_o_side_f2]" onchange="handlingReferencePrice()" placeholder="Số lần vận hành/mặt" class="form-control number_o_side_f2 number-format" value="" title="">
        </td>`;

        tdProductivityNormsF2 = `<td>
            <input type="text" name="items_istage[${ctItemsInspectionStage}][productivity_norms_f2]" onchange="handlingReferencePrice()" placeholder="định mức năng suất" class="form-control productivity_norms_f2 number-format" value="" title="">
        </td>`;

        tdActions = `<td class="text-center"><i onclick="removeItemInspectionStage(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        var _cTrItem = `<tr>
            ${tdItemCode}
            ${tdHeight}
            ${tdWidth}
            ${tdUnitF1}
            ${tdTypeCheckF1}
            ${tdNumberOSideF1}
            ${tdProductivityNormsF1}
            ${tdTypeCheckF2}
            ${tdNumberOSideF2}
            ${tdProductivityNormsF2}
            ${tdActions}
        </tr>`;

        return _cTrItem;
    }

    function removeItemInspectionStage(_this) {
        var cTrItem = $(_this).closest('tr');
        cTrItem.remove();
        totalAll();
    }

    function addItemInspectionStage() {
        var inspection_stage = $('#inspection_stage').val();
        if (!inspection_stage) {
            alert_float('danger', 'Vui lòng chọn nhóm công đoạn kiểm');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['inspection_stage'] = inspection_stage;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemInspectionStage',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                var cTrItemIS = '';
                if (response.items_inspection_stage && response.items_inspection_stage.length > 0) {
                    $.each(response.items_inspection_stage, function(index, value) {
                        cTrItemIS += createItemInspectionStage(value);
                    });
                }

                $('#tb-item-inspection-stage tbody').append(cTrItemIS);
                totalAll();
            }
        });
    }
</script>
<script>
    var ctItemsPackageStage = <?= $ctItemsPackageStage ? $ctItemsPackageStage : 0 ?>;
    $(document).ready(function() {
        $('#package_stage').select2({
            'allowClear': true
        });
    });

    function createItemPackageStage(dataItem) {
        if (!dataItem) return '';

        tdItemCode = `<td>
            <input type="hidden" name="items_pstage[${ctItemsPackageStage}][category_stage_id]" class="form-control" value="${dataItem.id}">
            ${dataItem.code}(${dataItem.name})
        </td>`;

        tdHeight = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="" title="">
        </td>`;

        tdWidth = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="" title="">
        </td>`;

        tdHightBottom = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][hight_bottom]" onchange="handlingReferencePrice()" placeholder="Cao/Đáy" class="form-control number-format hight_bottom" value="" title="">
        </td>`;

        tdUnit = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][unit]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit" value="" title="">
        </td>`;

        tdNumberBales = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][number_bales]" onchange="handlingReferencePrice()" placeholder="Số Con/Kiện" class="form-control number-format number_bales" value="" title="">
        </td>`;

        tdBaleNorms = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][bale_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Kiện" class="form-control number-format bale_norms" value="" title="">
        </td>`;

        tdProductivityNorms = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][productivity_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất" class="form-control number-format productivity_norms" value="" title="">
        </td>`;

        tdTypePackaging = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][type_packaging]" onchange="handlingReferencePrice()" placeholder="Loại bao bì đóng gói" class="form-control type_packaging" value="" title="">
        </td>`;

        tdTypeTem = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][type_tem]" onchange="handlingReferencePrice()" placeholder="Loại Tem Dán" class="form-control type_tem" value="" title="">
        </td>`;

        tdTotalTem = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][total_tem]" onchange="handlingReferencePrice()" placeholder="Tổng Số Tem Dán" class="form-control number-format total_tem" value="" title="">
        </td>`;

        tdTotalBale = `<td>
            <input type="text" name="items_pstage[${ctItemsPackageStage}][total_bale]" onchange="handlingReferencePrice()" placeholder="Tổng Số Kiện Dán" class="form-control number-format total_bale" value="" title="">
        </td>`;

        tdActions = `<td class="text-center"><i onclick="removeItemPackageStage(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        var _cTrItem = `<tr>
            ${tdItemCode}
            ${tdHeight}
            ${tdWidth}
            ${tdHightBottom}
            ${tdUnit}
            ${tdNumberBales}
            ${tdBaleNorms}
            ${tdProductivityNorms}
            ${tdTypePackaging}
            ${tdTypeTem}
            ${tdTotalTem}
            ${tdTotalBale}
            ${tdActions}
        </tr>`;

        return _cTrItem;
    }

    function removeItemPackageStage(_this) {
        var cTrItem = $(_this).closest('tr');
        cTrItem.remove();
        totalAll();
    }

    function addItemPackageStage() {
        var package_stage = $('#package_stage').val();
        if (!package_stage) {
            alert_float('danger', 'Vui lòng chọn nhóm công đoạn phân đơn - dán tem');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['package_stage'] = package_stage;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemPackageStage',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                var cTrItemPS = '';
                if (response.items_package_stage && response.items_package_stage.length > 0) {
                    $.each(response.items_package_stage, function(index, value) {
                        cTrItemPS += createItemPackageStage(value);
                    });
                }

                $('#tb-item-package-stage tbody').append(cTrItemPS);
                totalAll();
            }
        });
    }
</script>
<script>
    var ctItemsDeliveryStage = <?= $ctItemsDeliveryStage ? $ctItemsDeliveryStage : 0 ?>;
    $(document).ready(function() {
        $('#delivery_stage').select2({
            'allowClear': true
        });
    });

    function createItemDeliveryStage(dataItem) {
        if (!dataItem) return '';

        tdItemCode = `<td>
            <input type="hidden" name="items_dstage[${ctItemsDeliveryStage}][category_stage_id]" class="form-control" value="${dataItem.id}">
            ${dataItem.code}(${dataItem.name})
        </td>`;

        tdHeight = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="" title="">
        </td>`;

        tdWidth = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="" title="">
        </td>`;

        tdHightBottom = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][hight_bottom]" onchange="handlingReferencePrice()" placeholder="Cao/Đáy" class="form-control number-format hight_bottom" value="" title="">
        </td>`;

        tdUnit = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][unit]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit" value="" title="">
        </td>`;

        tdNumberBales = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][number_bales]" onchange="handlingReferencePrice()" placeholder="Số Con/Kiện" class="form-control number-format number_bales" value="" title="">
        </td>`;

        tdBaleNorms = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][bale_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Kiện" class="form-control number-format bale_norms" value="" title="">
        </td>`;

        tdProductivityNorms = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][productivity_norms]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất" class="form-control number-format productivity_norms" value="" title="">
        </td>`;

        tdTypePackaging = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][type_packaging]" onchange="handlingReferencePrice()" placeholder="Loại bao bì đóng gói" class="form-control type_packaging" value="" title="">
        </td>`;

        tdTypeTem = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][type_tem]" onchange="handlingReferencePrice()" placeholder="Loại Tem Dán" class="form-control type_tem" value="" title="">
        </td>`;

        tdTotalTem = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][total_tem]" onchange="handlingReferencePrice()" placeholder="Tổng Số Tem Dán" class="form-control number-format total_tem" value="" title="">
        </td>`;

        tdTotalBale = `<td>
            <input type="text" name="items_dstage[${ctItemsDeliveryStage}][total_bale]" onchange="handlingReferencePrice()" placeholder="Tổng Số Kiện Dán" class="form-control number-format total_bale" value="" title="">
        </td>`;

        tdActions = `<td class="text-center"><i onclick="removeItemDeliveryStage(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        var _cTrItem = `<tr>
            ${tdItemCode}
            ${tdHeight}
            ${tdWidth}
            ${tdHightBottom}
            ${tdUnit}
            ${tdNumberBales}
            ${tdBaleNorms}
            ${tdProductivityNorms}
            ${tdTypePackaging}
            ${tdTypeTem}
            ${tdTotalTem}
            ${tdTotalBale}
            ${tdActions}
        </tr>`;

        return _cTrItem;
    }

    function removeItemDeliveryStage(_this) {
        var cTrItem = $(_this).closest('tr');
        cTrItem.remove();
        totalAll();
    }

    function addItemDeliveryStage() {
        var delivery_stage = $('#delivery_stage').val();
        if (!delivery_stage) {
            alert_float('danger', 'Vui lòng chọn nhóm công đoạn mở phiếu giao hàng');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['package_stage'] = delivery_stage;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemPackageStage',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                var cTrItemDS = '';
                if (response.items_package_stage && response.items_package_stage.length > 0) {
                    $.each(response.items_package_stage, function(index, value) {
                        cTrItemDS += createItemDeliveryStage(value);
                    });
                }

                $('#tb-item-delivery-stage tbody').append(cTrItemDS);
                totalAll();
            }
        });
    }
</script>
<script>
    //stage car
    var ctItemsCarStage = <?= $ctItemsCarStage ? $ctItemsCarStage : 0 ?>;
    $(document).ready(function() {
        $('#car_stage').select2({
            'allowClear': true
        });
    });

    function createItemCarStage(dataItem) {
        if (!dataItem) return '';

        tdItemCode = `<td>
            <input type="hidden" name="items_cstage[${ctItemsCarStage}][category_stage_id]" class="form-control" value="${dataItem.id}">
            ${dataItem.code}(${dataItem.name})
        </td>`;

        tdTransportation = `<td>
            <input type="text" name="items_cstage[${ctItemsCarStage}][transportation]" onchange="handlingReferencePrice()" placeholder="Loại Phương Tiện" class="form-control transportation" value="" title="">
        </td>`;

        tdUnit = `<td>
            <input type="text" name="items_cstage[${ctItemsCarStage}][unit]" onchange="handlingReferencePrice()" placeholder="Đơn Vị Tính" class="form-control unit" value="" title="">
        </td>`;

        tdPriceDelivery = `<td>
            <input type="text" name="items_cstage[${ctItemsCarStage}][price_delivery]" onchange="handlingReferencePrice()" placeholder="Đơn Giá Giao Hàng" class="form-control money-format price_delivery" value="" title="">
        </td>`;

        tdTotalBale = `<td>
            <input type="text" name="items_cstage[${ctItemsCarStage}][total_bale]" onchange="handlingReferencePrice()" placeholder="Tổng Kiện" class="form-control number-format total_bale" value="" title="">
        </td>`;

        tdSubtotal = `<td>
            <input type="text" name="items_cstage[${ctItemsCarStage}][subtotal]" onchange="handlingReferencePrice()" placeholder="Thành Tiền" class="form-control money-format subtotal" value="" title="">
        </td>`;

        tdSupplier = `<td>
            <input type="text" name="items_cstage[${ctItemsCarStage}][supplier]" onchange="handlingReferencePrice()" placeholder="Nhà Cung Cấp" class="form-control supplier" value="" title="">
        </td>`;

        tdAddressDelivery = `<td>
            <input type="text" name="items_cstage[${ctItemsCarStage}][address_delivery]" onchange="handlingReferencePrice()" placeholder="Địa Chỉ Giao Hàng" class="form-control address_delivery" value="" title="">
        </td>`;

        tdActions = `<td class="text-center"><i onclick="removeItemCarStage(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        var _cTrItem = `<tr>
            ${tdItemCode}
            ${tdTransportation}
            ${tdUnit}
            ${tdPriceDelivery}
            ${tdTotalBale}
            ${tdSubtotal}
            ${tdSupplier}
            ${tdAddressDelivery}
            ${tdActions}
        </tr>`;

        return _cTrItem;
    }

    function removeItemCarStage(_this) {
        var cTrItem = $(_this).closest('tr');
        cTrItem.remove();
        totalAll();
    }

    function addItemCarStage() {
        var car_stage = $('#car_stage').val();
        if (!car_stage) {
            alert_float('danger', 'Vui lòng chọn nhóm công đoạn điều xe');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['package_stage'] = car_stage;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/handling_price/addItemPackageStage',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                var cTrItemCS = '';
                if (response.items_package_stage && response.items_package_stage.length > 0) {
                    $.each(response.items_package_stage, function(index, value) {
                        cTrItemCS += createItemCarStage(value);
                    });
                }

                $('#tb-item-car-stage tbody').append(cTrItemCS);
                totalAll();
            }
        });
    }
</script>
<?php
$jsonStage = json_encode($stages);
$jsonMachines = json_encode($machines);
?>
<script>
    //add plus stage
    var ctPlusStage = <?= $ctPlusStage ? $ctPlusStage : 0 ?>;
    $(document).ready(function() {
        $('#stage_add_plus').select2({
            'allowClear': true
        });
    });

    function rvPlusStage(_this, _stageAddPlusId) {
        $(_this).closest('li').remove();
        $('#tab-cd-' + _stageAddPlusId).remove();
        totalAll();
    }

    function removeItemSubStage(_this) {
        $(_this).closest('tr').remove();
        totalAll();
    }

    function createItemPlusStage(_item_id, _item_name, _item_code, _type, _stageAddPlusId) {
        if (!_item_id) return '';

        tdItemCode = `<td>
            <input type="hidden" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][item_id]" class="form-control" value="${_item_id}">
            <input type="hidden" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][type]" class="form-control" value="${_type}">
            ${_item_code}
        </td>`;

        tdItemName = `<td>
            ${_item_name}
        </td>`;

        tdHeight = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="" title="">
        </td>`;

        tdWidth = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="" title="">
        </td>`;

        tdNumberOperatingF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][number_operating_f1]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ Vận Hành" class="form-control number-format number_operating_f1" value="" title="">
        </td>`;

        tdTypeNPLF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][type_npl_f1]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f1" value="" title="">
        </td>`;

        tdNumberOperatingSideF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][number_operating_side_f1]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Mặt" class="form-control number-format number_operating_side_f1" value="" title="">
        </td>`;

        tdInkF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][ink_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức Mực In/Lần In" class="form-control number-format ink_f1" value="" title="">
        </td>`;

        tdQuotaTimeF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][quota_time_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức TG Canh Bài" class="form-control number-format quota_time_f1" value="" title="">
        </td>`;

        tdQuotaNPLF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][quota_npl_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức NPL Canh Bài" class="form-control number-format quota_npl_f1" value="" title="">
        </td>`;

        tdTotalNPLF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][total_npl_f1]" onchange="totalAll()" placeholder="Tổng NPL" class="form-control number-format total_npl_f1" value="" title="">
        </td>`;

        tdTotalTimeF1 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][total_time_npl_f1]" onchange="totalAll()" placeholder="Tổng TG Canh Bài" class="form-control number-format total_time_npl_f1" value="" title="">
        </td>`;

        //
        tdNumberOperatingF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][number_operating_f2]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ Vận Hành" class="form-control number-format number_operating_f2" value="" title="">
        </td>`;

        tdTypeNPLF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][type_npl_f2]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f2" value="" title="">
        </td>`;

        tdNumberOperatingSideF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][number_operating_side_f2]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Mặt" class="form-control number-format number_operating_side_f2" value="" title="">
        </td>`;

        tdInkF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][ink_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức Mực In/Lần In" class="form-control number-format ink_f2" value="" title="">
        </td>`;

        tdQuotaTimeF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][quota_time_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức TG Canh Bài" class="form-control number-format quota_time_f2" value="" title="">
        </td>`;

        tdQuotaNPLF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][quota_npl_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức NPL Canh Bài" class="form-control number-format quota_npl_f2" value="" title="">
        </td>`;

        tdTotalNPLF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][total_npl_f2]" onchange="totalAll()" placeholder="Tổng NPL" class="form-control number-format total_npl_f2" value="" title="">
        </td>`;

        tdTotalTimeF2 = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][total_time_npl_f2]" onchange="totalAll()" placeholder="Tổng TG Canh Bài" class="form-control number-format total_time_npl_f2" value="" title="">
        </td>`;

        tdTotalNPLF12 = `<td class="td-total-npl-f12 text-center">
            
        </td>`;

        tdTotalTimeF12 = `<td class="td-total-time-f12 text-center">
            
        </td>`;

        tdPrice = `<td>
            <input type="text" name="items_psstage[${_stageAddPlusId}][${ctPlusStage}][price]" onchange="totalAll()" placeholder="Đơn Giá/lần In" class="form-control number-format price" value="" title="">
        </td>`;

        tdPricePage = `<td class="td-price-page text-right">
        </td>`;

        tdActions = `<td class="text-center"><i onclick="removeItemSubStage(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        var trItem = `<tr>
            ${tdItemCode}
            ${tdItemName}
            ${tdHeight}
            ${tdWidth}
            ${tdNumberOperatingF1}
            ${tdTypeNPLF1}
            ${tdNumberOperatingSideF1}
            ${tdInkF1}
            ${tdQuotaTimeF1}
            ${tdQuotaNPLF1}
            ${tdTotalNPLF1}
            ${tdTotalTimeF1}
            ${tdNumberOperatingF2}
            ${tdTypeNPLF2}
            ${tdNumberOperatingSideF2}
            ${tdInkF2}
            ${tdQuotaTimeF2}
            ${tdQuotaNPLF2}
            ${tdTotalNPLF2}
            ${tdTotalTimeF2}
            ${tdTotalNPLF12}
            ${tdTotalTimeF12}
            ${tdPrice}
            ${tdPricePage}
            ${tdActions}
        </tr>`;
        return trItem;
    }

    function addItemPlusStage(_this, _stageAddPlusId) {
        var stageSub = $('#stage_sub_' + _stageAddPlusId).val();
        var stageSubText = $('#stage_sub_' + _stageAddPlusId + ' option:selected').text();
        var dtStageSub = $('#stage_sub_' + _stageAddPlusId).select2('data');

        var machineSub = $('#machine_sub_' + _stageAddPlusId).val();
        var machineSubText = $('#machine_sub_' + _stageAddPlusId + ' option:selected').text();
        var dtMachineSub = $('#machine_sub_' + _stageAddPlusId).select2('data');

        if (!stageSub && !machineSub) {
            alert_float('danger', 'Vui lòng chọn 1 công đoạn hoặc 1 thiết bị.');
            return;
        }

        var selectedOption = $('#stage_sub_' + _stageAddPlusId).find('option[value="' + stageSub + '"]');
        var stageSubCode = selectedOption.data('code');

        var selectedOption = $('#machine_sub_' + _stageAddPlusId).find('option[value="' + machineSub + '"]');
        var machineSubCode = selectedOption.data('code');

        var trPlusStage = '';
        if (stageSub > 0) {
            trPlusStage += createItemPlusStage(stageSub, stageSubText, stageSubCode, 1, _stageAddPlusId);
            ctPlusStage++;
        }

        if (machineSub > 0) {
            trPlusStage += createItemPlusStage(machineSub, machineSubText, machineSubCode, 2, _stageAddPlusId);
            ctPlusStage++;
        }

        $('#tb-plus-stage-' + _stageAddPlusId).append(trPlusStage);
        totalAll();
    }

    function addPlusStage() {
        var stageAddPlusId = $('#stage_add_plus').val();
        if (!stageAddPlusId) {
            alert_float('danger', 'Vui lòng chọn thêm công đoạn');
            return;
        }

        if ($('#tab-cd-' + stageAddPlusId + '').length > 0) {
            return;
        }

        var stageAddPlusText = $('#stage_add_plus option:selected').text();

        var tabStage = `<li role="presentation">
            <a href="#tab-cd-${stageAddPlusId}" aria-controls="tab" role="tab" data-toggle="tab">${stageAddPlusText} <span class="fa fa-remove text-danger" onclick="rvPlusStage(this, ${stageAddPlusId})" data-toggle="tooltip" title="Xóa"></span></a> 
        </li>`;
        $('.span-tab-plus').before(tabStage);

        var optionStages = '<option value=""></option>';
        $.each(<?= $jsonStage ?>, function(index, value) {
            optionStages += `<option data-code="${value.code}" value="${value.id}">${value.name}</option>`;
        });

        var optionMachines = '<option value=""></option>';
        $.each(<?= $jsonMachines ?>, function(index, value) {
            optionMachines += `<option data-code="${value.code}" value="${value.id}">${value.name}</option>`;
        });

        $('.div-tab-plus').append(`<div role="tabpanel" class="tab-pane" id="tab-cd-${stageAddPlusId}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="stage_sub_${stageAddPlusId}">Công đoạn</label>
                        <select data-placeholder="Công đoạn" id="stage_sub_${stageAddPlusId}" class="stage_sub modal-select2" style="width: 100%;">
                            ${optionStages}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                            <label for="machine_sub_${stageAddPlusId}">Thiết bị</label>
                            <select data-placeholder="Thiết bị" id="machine_sub_${stageAddPlusId}" class="machine_sub modal-select2" style="width: 100%;">
                                ${optionMachines}
                            </select>
                        </div>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0)" onclick="addItemPlusStage(this, ${stageAddPlusId})" data-toggle="tooltip" title="<?= lang('tnh_plus') ?>" class="fa fa-plus btn btn-success mtop30"> <?= lang('tnh_plus') ?></a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tb-plus-stage-${stageAddPlusId}" class="table table-hover tb-plus-stage" style="min-width: 3100px; margin-top: 0px;">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Mã Thiết Bị - Công Đoạn') ?></th>
                            <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Tên Thiết Bị - Công Đoạn') ?></th>
                            <th class="text-center" rowspan="1" colspan="2" style="width: 200px;"><?= lang('Kích Thước Vận Hành') ?></th>
                            <th class="text-center" rowspan="1" colspan="8" style="width: 800px;"><?= lang('Mặt 1') ?></th>
                            <th class="text-center" rowspan="1" colspan="8" style="width: 800px;"><?= lang('Mặt 2') ?></th>
                            <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Tổng NPL Mặt 1+Mặt 2') ?></th>
                            <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Tổng TG Canh Bài Mặt 1+Mặt 2') ?></th>
                            <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Đơn Giá/lần In') ?></th>
                            <th class="text-center" rowspan="2" style="width: 100px;"><?= lang('Đơn Giá/tờ') ?></th>
                            <th class="text-center" rowspan="2" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                        </tr>
                        <tr>
                            <th class="text-center">Height</th>
                            <th class="text-center">Width</th>
                            <th class="text-center">Số Con/Tờ Vận Hành</th>
                            <th class="text-center">Loại NPL</th>
                            <th class="text-center">Số Lần Vận Hành/Mặt</th>
                            <th class="text-center">Định Mức Mực In/Lần In</th>
                            <th class="text-center">Định Mức TG Canh Bài</th>
                            <th class="text-center">Định Mức NPL Canh Bài</th>
                            <th class="text-center">Tổng NPL</th>
                            <th class="text-center">Tổng TG Canh Bài</th>
                            <th class="text-center">Số Con/Tờ Vận Hành</th>
                            <th class="text-center">Loại NPL</th>
                            <th class="text-center">Số Lần Vận Hành/Mặt</th>
                            <th class="text-center">Định Mức Mực In/Lần In</th>
                            <th class="text-center">Định Mức TG Canh Bài</th>
                            <th class="text-center">Định Mức NPL Canh Bài</th>
                            <th class="text-center">Tổng NPL</th>
                            <th class="text-center">Tổng TG Canh Bài</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>`);

        $('#stage_sub_' + stageAddPlusId).select2({
            allowClear: true
        });
        $('#machine_sub_' + stageAddPlusId).select2({
            allowClear: true
        });
        totalAll();
    }
</script>
<script>
    var ctLayout = <?= $ctLayout ? $ctLayout : 0 ?>;
    $(document).ready(function() {
        $('#stage_layout').select2({
            allowClear: true
        });
        $('#machine_layout').select2({
            allowClear: true
        });
    });

    function removeItemLayoutStage(_this) {
        $(_this).closest('tr').remove();
        totalAll();
    }

    function createItemLayoutStage(_item_id, _item_name, _item_code, _type) {
        if (!_item_id) return '';

        tdItemCode = `<td>
            <input type="hidden" name="items_lstage[${ctLayout}][item_id]" class="form-control" value="${_item_id}">
            <input type="hidden" name="items_lstage[${ctLayout}][type]" class="form-control" value="${_type}">
            ${_item_code}
        </td>`;

        tdItemName = `<td>
            ${_item_name}
        </td>`;

        tdHeight = `<td>
            <input type="text" name="items_lstage[${ctLayout}][height]" onchange="handlingReferencePrice()" placeholder="Height" class="form-control number-format height" value="" title="">
        </td>`;

        tdWidth = `<td>
            <input type="text" name="items_lstage[${ctLayout}][width]" onchange="handlingReferencePrice()" placeholder="Width" class="form-control number-format width" value="" title="">
        </td>`;

        tdNumberChildPrintF1 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_child_print_f1]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ In" class="form-control number-format number_child_print_f1" value="" title="">
        </td>`;

        tdNumberColorPrintF1 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_color_print_f1]" onchange="handlingReferencePrice()" placeholder="Số Lượng Màu In" class="form-control number-format number_color_print_f1" value="" title="">
        </td>`;

        tdTypeNPLF1 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][type_npl_f1]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f1" value="" title="">
        </td>`;

        tdNumberZnF1 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_zn_f1]" onchange="handlingReferencePrice()" placeholder="Số Lượng Kẽm" class="form-control number-format number_zn_f1" value="" title="">
        </td>`;

        tdNumberOperationsPageF1 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_operations_page_f1]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Tờ" class="form-control number-format number_operations_page_f1" value="" title="">
        </td>`;

        tdQuotaZnUseF1 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][quota_zn_use_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức Kẽm Sử Dụng" class="form-control number-format quota_zn_use_f1" value="" title="">
        </td>`;

        tdQuotaCTPF1 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][quota_ctp_f1]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất /CTP" class="form-control number-format quota_ctp_f1" value="" title="">
        </td>`;

        //
        tdNumberChildPrintF2 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_child_print_f2]" onchange="handlingReferencePrice()" placeholder="Số Con/Tờ In" class="form-control number-format number_child_print_f2" value="" title="">
        </td>`;

        tdNumberColorPrintF2 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_color_print_f2]" onchange="handlingReferencePrice()" placeholder="Số Lượng Màu In" class="form-control number-format number_color_print_f2" value="" title="">
        </td>`;

        tdTypeNPLF2 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][type_npl_f2]" onchange="handlingReferencePrice()" placeholder="Loại NPL" class="form-control type_npl_f2" value="" title="">
        </td>`;

        tdNumberZnF2 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_zn_f2]" onchange="handlingReferencePrice()" placeholder="Số Lượng Kẽm" class="form-control number-format number_zn_f2" value="" title="">
        </td>`;

        tdNumberOperationsPageF2 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][number_operations_page_f2]" onchange="totalAll()" placeholder="Số Lần Vận Hành/Tờ" class="form-control number-format number_operations_page_f2" value="" title="">
        </td>`;

        tdQuotaZnUseF2 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][quota_zn_use_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức Kẽm Sử Dụng" class="form-control number-format quota_zn_use_f2" value="" title="">
        </td>`;

        tdQuotaCTPF2 = `<td>
            <input type="text" name="items_lstage[${ctLayout}][quota_ctp_f2]" onchange="handlingReferencePrice()" placeholder="Định Mức Năng Suất /CTP" class="form-control number-format quota_ctp_f2" value="" title="">
        </td>`;

        tdTotalOperationsPage = `<td class="text-center total_operations_page">
        </td>`;

        tdTotalNPL = `<td class="text-center">
            <input type="text" name="items_lstage[${ctLayout}][total_npl]" onchange="handlingReferencePrice()" placeholder="Tổng Số NPL/Tờ" class="form-control number-format total_npl" value="" title="">
        </td>`;

        tdPrice = `<td>
            <input type="text" name="items_lstage[${ctLayout}][price]" onchange="totalAll()" placeholder="Giá/Tờ (VNĐ)" class="form-control number-format price" value="" title="">
        </td>`;

        tdTotalPrice = `<td class="text-center total_price">
        </td>`;

        tdActions = `<td class="text-center"><i onclick="removeItemSubStage(this)" class="fa fa-remove text-danger pointer"></i></td>`;

        var trItem = `<tr>
            ${tdItemCode}
            ${tdItemName}
            ${tdHeight}
            ${tdWidth}
            ${tdNumberChildPrintF1}
            ${tdNumberColorPrintF1}
            ${tdTypeNPLF1}
            ${tdNumberZnF1}
            ${tdNumberOperationsPageF1}
            ${tdQuotaZnUseF1}
            ${tdQuotaCTPF1}
            ${tdNumberChildPrintF2}
            ${tdNumberColorPrintF2}
            ${tdTypeNPLF2}
            ${tdNumberZnF2}
            ${tdNumberOperationsPageF2}
            ${tdQuotaZnUseF2}
            ${tdQuotaCTPF2}
            ${tdTotalOperationsPage}
            ${tdTotalNPL}
            ${tdPrice}
            ${tdTotalPrice}
            ${tdActions}
        </tr>`;
        return trItem;
    }

    function addItemLayoutStage(_this) {
        var stageLayout = $('#stage_layout').val();
        var stageLayoutText = $('#stage_layout option:selected').text();
        var dtStageLayout = $('#stage_layout').select2('data');

        var machineLayout = $('#machine_layout').val();
        var machineLayoutText = $('#machine_layout option:selected').text();
        var dtMachineLayout = $('#machine_layout').select2('data');

        if (!stageLayout && !machineLayout) {
            alert_float('danger', 'Vui lòng chọn 1 công đoạn hoặc 1 thiết bị.');
            return;
        }

        var selectedOption = $('#stage_layout').find('option[value="' + stageLayout + '"]');
        var stageLayoutCode = selectedOption.data('code');

        var selectedOption = $('#machine_layout').find('option[value="' + machineLayout + '"]');
        var machineLayoutCode = selectedOption.data('code');

        var trLayoutStage = '';
        if (stageLayout > 0) {
            trLayoutStage += createItemLayoutStage(stageLayout, stageLayoutText, stageLayoutCode, 1);
            ctLayout++;
        }

        if (machineLayout > 0) {
            trLayoutStage += createItemLayoutStage(machineLayout, machineLayoutText, machineLayoutCode, 2);
            ctLayout++;
        }

        $('#tb-item-layout').append(trLayoutStage);
        totalAll();
    }
    $('#SearchQR_machines').on('keydown', function(event) {
        var code = $(this).val();
        if (event.keyCode === 13) {
            event.preventDefault();
            $('#SearchQR_machines').change();
        }
    });
    $('#SearchQR_machines').change(function() {
        var code = $(this).val();
        link = '';
        if (code) {
            var data = {
                'code': code,
                'type': 1
            };
            $.get(admin_url + 'Handling_price/check_qr', data, function(data) {
                data = JSON.parse(data);
                if (data.result) {
                    alert_float('success', data.message)
                    if (data.id) {
                        var checkboxitemprice = 0;
                        var checkboxes = document.querySelectorAll('.check-itemprice');
                        checkboxes.forEach(function(checkbox) {
                            if (checkbox.checked) {
                                // Nếu checkbox đã được chọn, thêm giá trị của nó vào mảng
                                checkboxitemprice = checkbox.value;
                            }
                        });
                        $('#machine_' + checkboxitemprice).select2('val', data.id);
                        $('#machine_' + checkboxitemprice).change();
                        $('#SearchQR_machines').val('');
                    }
                } else {
                    alert_float('danger', data.message)
                    Soundhau('<?= base_url('uploads/error.mp3') ?>');
                    $('#SearchQR_machines').val('');
                }
            })
        }
        // $('#SearchQR_purchase').val('');
    })
    $('#SearchQR_meterial').on('keydown', function(event) {
        var code = $(this).val();
        if (event.keyCode === 13) {
            event.preventDefault();
            $('#SearchQR_meterial').change();
        }
    });
    $('#SearchQR_meterial').change(function() {
        var code = $(this).val();
        link = '';
        if (code) {
            var data = {
                'code': code,
                'type': 2
            };
            $.get(admin_url + 'Handling_price/check_qr', data, function(data) {
                data = JSON.parse(data);
                if (data.result) {
                    alert_float('success', data.message)
                    if (data.id) {
                        $('#SearchQR_meterial_id').val(data.id);
                        $('#SearchQR_stages').focus();
                        additemsPrice();
                    }
                } else {
                    alert_float('danger', data.message)
                    Soundhau('<?= base_url('uploads/error.mp3') ?>');
                    $('#SearchQR_meterial_id').val(0);
                    $('#SearchQR_meterial').val('');
                }
            })
        }
        // $('#SearchQR_purchase').val('');
    })
    $('#SearchQR_stages_products').on('keydown', function(event) {
        var code = $(this).val();
        if (event.keyCode === 13) {
            event.preventDefault();
            $('#SearchQR_stages_products').change();
        }
    });
    $('#SearchQR_stages_products').change(function() {
        var code = $(this).val();
        link = '';
        if (code) {
            var data = {
                'code': code,
            };
            $.get(admin_url + 'Handling_price/check_qr_new', data, function(data) {
                data = JSON.parse(data);
                if (data.result) {
                    alert_float('success', data.message)
                    if (data.id) {
                        var dataPOST = {};
                        dataPOST[csrfData['token_name']] = csrfData['hash'];
                        dataPOST['stages_price_quotes'] = data.id;
                        dataPOST['height_layout'] = intVal($('.height_layout').val());
                        dataPOST['width_layout'] = intVal($('.width_layout').val());
                        dataPOST['group_id'] = $('#group_id').val();
                        dataPOST['id_customer'] = $('#id_div_customer').data('id');
                        dataPOST['height'] = $('.height').val();
                        dataPOST['width'] = $('.width').val();

                        dataPOST['quote_stage_id'] = quote_stage_id;

                        $.ajax({
                            type: "POST",
                            url: site.base_url + 'admin/handling_price/addItemPriceQuotes',
                            data: dataPOST,
                            dataType: "json",
                            success: function(response) {
                                trItemsQuotes = '';
                                if (response.items_material_price_quotes.length == 0) {
                                    if (response.items_stages_price_quotes && response.items_stages_price_quotes.length > 0) {
                                        $.each(response.items_stages_price_quotes, function(index, value) {
                                            dataPrice = value;
                                            tdCategoryStages = `<td>
                                <input type="hidden" name="type_price_products[${counterItemsProducts}]" class="form-control type_price_products" value="${dataPrice.type}">
                                <input type="hidden" name="item_id_price_products[${counterItemsProducts}]" class="form-control item_id_price_products" value="${dataPrice.id}">
                                <input type="hidden" name="stage_id_price_products[${counterItemsProducts}]" class="form-control stage_id_price_products" value="${dataPrice.id}">
                                <div>${dataPrice.name}</div>
                                <div class="checkbox checkbox-danger">
                                    <input type="checkbox" onchange="totalAll()"  class="not_cpln" name="not_cpln[${counterItemsProducts}]" id="not_cpln_${counterItemsProducts}" value="1">
                                    <label for="not_cpln_${counterItemsProducts}">Không tính CPLN</label>
                                </div>
                            </td>`;

                                            tdLongHeight = `<td>
                                <input type="text" name="long_height[${counterItemsProducts}]" placeholder="<?= lang('Dài/Cao') ?>" onchange="totalItemsPrice()" place class="form-control long_height" value="">
                            </td>`;

                                            tdWidthHorizontal = `<td>
                                <input type="text" name="width_horizontal[${counterItemsProducts}]" placeholder="<?= lang('Rộng/Ngang') ?>" onchange="totalItemsPrice()" place class="form-control width_horizontal" value="">
                            </td>`;

                                            tdMode = `<td><div class="text-center">${dataPrice.mode}</div></td>`;
                                            tdQC = `<td><div class="text-center"></div></td>`;
                                            tdNumberOperate = `<td>
                                <input type="text" name="number_operate_products[${counterItemsProducts}]" placeholder="<?= lang('Số Lần Xả/Vận Hành') ?>" onchange="totalItemsPrice()" place class="form-control number_operate_products number-format" value="1">
                            </td>`;

                                            tdFaceProducts = `<td>
                                <input type="text" name="face_products[${counterItemsProducts}]" placeholder="<?= lang('Mặt in') ?>" onchange="totalItemsPrice()" class="form-control face_products number-format" value="">
                            </td>`;

                                            tdPriceAbout = `<td>
                                <input type="text" name="price_about_products[${counterItemsProducts}]" placeholder="<?= lang('Đơn giá CĐ') ?>" onchange="totalItemsPrice()" readonly class="form-control price_about_products money-format" value="${tnhFormatMoney(dataPrice.price_sell)}">
                            </td>`;

                                            tdTotalSheet = `<td class="td-total-sheet-products text-right">
                                ${tnhFormatMoney(dataPrice.price_sell)}
                            </td>`;
                                            tdActions = `<td class="text-center"><i onclick="removeItemsPriceProducts(this)" class="fa fa-remove text-danger pointer"></i></td>`;

                                            trItemsQuotes = `<tr>
                                ${tdCategoryStages}
                                ${tdLongHeight}
                                ${tdWidthHorizontal}
                                ${tdMode}
                                ${tdQC}
                                ${tdNumberOperate}
                                ${tdFaceProducts}
                                ${tdPriceAbout}
                                ${tdTotalSheet}
                                ${tdActions}
                            </tr>`;

                                            counterItemsProducts++;
                                        });
                                    }
                                }

                                $('#table-items-products tbody').append(trItemsQuotes);
                                totalItemsPrice();
                            }
                        });
                    }
                } else {
                    alert_float('danger', data.message)
                    Soundhau('<?= base_url('uploads/error.mp3') ?>');
                }
            })
        }
        $('#SearchQR_stages_products').val('');
        // $('#SearchQR_purchase').val('');
    })
    $('#SearchQR_stages').on('keydown', function(event) {
        var code = $(this).val();
        if (event.keyCode === 13) {
            event.preventDefault();
            $('#SearchQR_stages').change();
        }
    });
    $('#SearchQR_stages').change(function() {
        var code = $(this).val();
        link = '';
        if (code) {
            var data = {
                'code': code,
                'type': 3
            };
            $.get(admin_url + 'Handling_price/check_qr', data, function(data) {
                data = JSON.parse(data);
                if (data.result) {
                    alert_float('success', data.message)
                    if (data.id) {
                        $('#SearchQR_stages_id').val(data.id);
                        additemsPrice();
                    }
                } else {
                    alert_float('danger', data.message)
                    Soundhau('<?= base_url('uploads/error.mp3') ?>');
                    $('#SearchQR_stages_id').val(0);
                    $('#SearchQR_stages').val('');
                }
            })
        }
        // $('#SearchQR_purchase').val('');
    })

    function additemsPrice() {
        var SearchQR_meterial = $('#SearchQR_meterial_id').val();
        var SearchQR_stages = $('#SearchQR_stages_id').val();
        if (SearchQR_stages > 0 && SearchQR_stages > 0) {
            $('#SearchQR_meterial_id').val(0);
            $('#SearchQR_stages_id').val(0);
            $('#SearchQR_stages').val('');
            $('#SearchQR_meterial').val('');
            var dataPOST = {};
            dataPOST[csrfData['token_name']] = csrfData['hash'];
            dataPOST['material_price_quotes'] = SearchQR_meterial;
            dataPOST['stages_price_quotes'] = SearchQR_stages;
            dataPOST['height_layout'] = intVal($('.height_layout').val());
            dataPOST['width_layout'] = intVal($('.width_layout').val());
            dataPOST['quote_stage_id'] = quote_stage_id;
            dataPOST['group_id'] = $('#group_id').val();
            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/handling_price/addItemPriceQuotes',
                data: dataPOST,
                dataType: "json",
                success: function(response) {
                    trItemsQuotes = '';
                    if (response.items_material_price_quotes && response.items_material_price_quotes.length > 0) {
                        $.each(response.items_material_price_quotes, function(index, value) {
                            trItemsQuotes += createItemsPrice(value, response.items_stages_price_quotes[index]);
                        });
                    }

                    if (response.items_material_price_quotes.length == 0) {
                        if (response.items_stages_price_quotes && response.items_stages_price_quotes.length > 0) {
                            $.each(response.items_stages_price_quotes, function(index, value) {
                                trItemsQuotes += createItemsPrice(value, value);
                            });
                        }
                    }
                    $('#table-items-stages tbody').append(trItemsQuotes);
                    $('select.machine').select2({
                        'allowClear': true
                    });
                    totalItemsPrice();
                    // init_selectpicker();
                }
            });
        }
    }

    function changecheckbox() {
        var checkboxes = document.querySelectorAll('.check-itemprice');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Nếu checkbox này được chọn, vô hiệu hóa tất cả các checkbox khác
                if (this.checked) {
                    checkboxes.forEach(function(otherCheckbox) {
                        if (otherCheckbox !== checkbox) {
                            otherCheckbox.disabled = true;
                        }
                    });
                } else {
                    // Nếu checkbox này không được chọn, kích hoạt lại tất cả các checkbox khác
                    checkboxes.forEach(function(otherCheckbox) {
                        otherCheckbox.disabled = false;
                        otherCheckbox.checked = false;
                    });
                }
            });
        });
        $('#SearchQR_machines').focus();
    }
</script>