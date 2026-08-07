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

            $machines = $this->handling_price_model->getMachines();
            $stages = $this->products_model->getStages();
            
            $quotes_stages = $this->db->get_where('tbl_stage_quote', ['id' => $quote_stage_id])->row_array();
            ?>
            <div class="row">
                <span id="id_div_customer" data-id="<?=$customers?>"></span>
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
                            <div class="ml-at t-bold"><?= !empty($quotes_stages) ? $quotes_stages['name'].' ('.$quotes_stages['code'].')' : '' ?></div>
                        </div>
                        <div class="row-contro hide">
                            <div><?= lang('quantity') ?>: </div>
                            <div class="ml-at t-bold"><?= $cQuantity ?></div>
                            <input type="hidden" name="cQuantity" id="cQuantity" class="form-control" value="<?= $cQuantity ?>">
                            <input type="hidden" name="cItemsId" id="cItemsId" class="form-control" value="<?= $cItemsId ?>">
                            <input type="hidden" name="group_id" id="group_id" class="form-control" value="<?= !empty($dtGroupCustomer) ? $dtGroupCustomer['group_id'] : '' ?>">
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
                                    <input type="text" name="height" onchange="productCalculation()" placeholder="<?= lang('Height') ?>" class="form-control height" value="<?= !empty($arrDataJson['height']) ? $arrDataJson['height'] : $products['longs']/10 ?>">
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
                                    <input type="text" name="width" onchange="productCalculation()" placeholder="<?= lang('Width') ?>" class="form-control width" value="<?= !empty($arrDataJson['width']) ? $arrDataJson['width'] : $products['wide']/10 ?>">
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
                                                        '.(!empty($dtItem['is_single_use']) ? '<div class="text-danger">Duy nhất</div>' : '').'
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
                                                        $optionsMachine.= '<option '.$_selected.' value="'.$vM['id'].'">'.$vM['name'].'</option>';
                                                    }

                                                    $tdMachine = '<td>
                                                        <select name="machine[]" onchange="totalItemsPrice()" data-placeholder="'.lang('Thiết bị').'" style="width: 100%;" class="machine">
                                                            '.$optionsMachine.'
                                                        </select>
                                                    </td>';

                                                    $tdTypeNPL = '<td>
                                                        <input type="text" name="type_npl[]" onchange="totalItemsPrice()" placeholder="'.lang('Loại NPL').'" class="form-control type_npl" value="'.(!empty($value['type_npl']) ? $value['type_npl'] : '').'">
                                                    </td>';
                                                    $tdQuotaBOM = '<td>
                                                        <input type="text" name="quota_bom[]" onchange="totalItemsPrice()" placeholder="'.lang('Định mức BOM').'" class="form-control quota_bom" value="'.(!empty($value['quota_bom']) ? formatNumber($value['quota_bom']) : '').'">
                                                    </td>';

                                                    $tdPriceAbout = '<td>
                                                        <input type="text" name="price_about[]" placeholder="' . lang('Đơn giá/lần') . '" onchange="totalItemsPrice()" class="form-control price_about money-format" value="' . formatMoney($value['price_about']) . '">
                                                    </td>';
                                                    $tdTotalSheet = '<td class="td-total-sheet text-right">
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
                                                        '.(!empty($dtItem['is_single_use']) ? '<div class="text-danger">Duy nhất</div>' : '').'
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
                                                        $optionsMachine.= '<option '.$_selected.' value="'.$vM['id'].'">'.$vM['name'].'</option>';
                                                    }

                                                    $tdMachine = '<td>
                                                        <select name="machine_backside[]" onchange="totalItemsPrice()" data-placeholder="'.lang('Thiết bị').'" style="width: 100%;" class="machine_backside">
                                                            '.$optionsMachine.'
                                                        </select>
                                                    </td>';

                                                    $tdTypeNPL = '<td>
                                                        <input type="text" name="type_npl_backside[]" onchange="totalItemsPrice()" placeholder="'.lang('Loại NPL').'" class="form-control type_npl_backside" value="'.(!empty($value['type_npl_backside']) ? $value['type_npl_backside'] : '').'">
                                                    </td>';
                                                    $tdQuotaBOM = '<td>
                                                        <input type="text" name="quota_bom_backside[]" onchange="totalItemsPrice()" placeholder="'.lang('Định mức BOM').'" class="form-control quota_bom_backside" value="'.(!empty($value['quota_bom_backside']) ? formatNumber($value['quota_bom_backside']) : '').'">
                                                    </td>';

                                                    $tdPriceAbout = '<td>
                                                        <input type="text" name="price_about_backside[]" placeholder="' . lang('Đơn giá/lần') . '" onchange="totalItemsPrice()" class="form-control price_about_backside money-format" value="' . formatMoney($value['price_about_backside']) . '">
                                                    </td>';
                                                    $tdTotalSheet = '<td class="td-total-sheet-backside text-right">
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
                                <th class="text-center" style="width: 150px;"><?= lang('Số mặt in') ?><div class="text-danger">(<?= lang('1: mặt trước, 2: mặt sau, 3: mặt trước và sau') ?>)</div></th>
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

                                        $tdCategoryStages = '<td>
                                            <input type="hidden" name="type_price_products['.$counterItemsProducts.']" class="form-control type_price_products" value="' . $value['type_price_products'] . '">
                                            <input type="hidden" name="item_id_price_products['.$counterItemsProducts.']" class="form-control item_id_price_products" value="' . $value['item_id_price_products'] . '">
                                            <input type="hidden" name="stage_id_price_products['.$counterItemsProducts.']" class="form-control stage_id_price_products" value="' . $value['stage_id_price_products'] . '">
                                            <div>' . $dtItem['name'] . '</div>
                                            <div class="checkbox checkbox-danger">
                                                <input type="checkbox" onchange="totalAll()" '.($value['not_cpln'] ? 'checked' : '').' class="not_cpln" name="not_cpln['.$counterItemsProducts.']" id="not_cpln_'.$counterItemsProducts.'" value="1">
                                                <label for="not_cpln_'.$counterItemsProducts.'">Không tính CPLN</label>
                                            </div>
                                        </td>';

                                        $tdLongHeight = '<td>
                                            <input type="text" name="long_height['.$counterItemsProducts.']" placeholder="'.lang('Dài/Cao').'" onchange="totalItemsPrice()" place class="form-control long_height" value="'.(!empty($value['long_height']) ? $value['long_height'] : '').'">
                                        </td>';
                                        $tdWidthHorizontal = '<td>
                                            <input type="text" name="width_horizontal['.$counterItemsProducts.']" placeholder="'.lang('Rộng/Ngang').'" onchange="totalItemsPrice()" place class="form-control width_horizontal" value="'.(!empty($value['width_horizontal']) ? $value['width_horizontal'] : '').'">
                                        </td>';

                                        $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                                        $tdQC = '<td><div class="text-center"></div></td>';
                                        $tdNumberOperate = '<td>
                                            <input type="text" name="number_operate_products['.$counterItemsProducts.']" placeholder="' . lang('Số Lần Xả/Vận Hành') . '" onchange="totalItemsPrice()" place class="form-control number_operate_products number-format" value="' . formatNumber($value['number_operate_products']) . '">
                                        </td>';

                                        $tdFaceProducts = '<td>
                                            <input type="text" name="face_products['.$counterItemsProducts.']" placeholder="' . lang('Mặt in') . '" onchange="totalItemsPrice()" class="form-control face_products number-format" value="'.(!empty($value['face_products']) ? formatNumber($value['face_products']) : '') . '">
                                        </td>';

                                        $tdPriceAbout = '<td>
                                            <input type="text" name="price_about_products['.$counterItemsProducts.']" placeholder="' . lang('Đơn giá/tờ') . '" onchange="totalItemsPrice()" readonly class="form-control price_about_products money-format" value="' . formatNumber($value['price_about_products']) . '">
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
                                        <input type="text" onchange="gvcCalculation()" placeholder="'.lang('Tên gia công - Vận chuyển').'" name="type_vc[]" class="form-control" value="'.$value['type_vc'].'">
                                    </td>';

                                    $tdUnitKg = '<td>
                                        <input type="text" onchange="gvcCalculation()" placeholder="'.lang('ĐVT').'" name="unit_kg[]" class="form-control" value="'.$value['unit_kg'].'">
                                    </td>';

                                    $tdPriceGvc = '<td>
                                        <input type="text" placeholder="'.lang('Đơn giá').'" onchange="gvcCalculation()" name="price_gvc[]" id="price_gvc" class="form-control price_gvc money-format" value="'.formatMoney($value['price_gvc']).'">
                                    </td>';

                                    $tdKgChildGvc = '<td>
                                        <input type="text" placeholder="'.lang('KG/Con').'" onchange="gvcCalculation()" name="kg_child_gvc[]" class="form-control kg_child_gvc number-format" value="'.formatNumber($value['kg_child_gvc']).'">
                                    </td>';

                                    $tdTotalPriceGvc = '<td class="td-price-child-gvc text-right text-danger">
                                        '.formatMoney($value['price_child_gvc']).'
                                    </td>';

                                    echo '<tr>
                                        '.$tdNumber.'
                                        '.$tdTypeVC.'
                                        '.$tdUnitKg.'
                                        '.$tdPriceGvc.'
                                        '.$tdKgChildGvc.'
                                        '.$tdTotalPriceGvc.'
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
            <div class="row">
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

    var counterItemsProducts = <?=  $counterItemsProducts ? $counterItemsProducts : 0 ?>;

    function getMachines(_id_selected = 0) {
        optionsMachines = `<option></option>`;
        $.each(machines, function (index, value) {
            is_selected = _id_selected == value.id ? 'selected' : '';
            optionsMachines+= `<option ${is_selected} value="${value.id}">"${value.name}</option>`;
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
                grandTotalProductCal+= total_sheet_products;
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

        handlingReferencePrice();
    }

    function createItemsPrice(dataPrice, dataStages) {
        if (!dataPrice) return '';

        tdCategoryStages = `<td>
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
            <select name="machine[]" onchange="totalItemsPrice()" data-placeholder="<?= lang('Thiết bị') ?>" style="width: 100%;" class="machine">
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
    var quote_stage_id = "<?=$quote_stage_id?>";
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
    
        
        dataPOST['quote_stage_id'] =  quote_stage_id;
    
        

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
                $('select.machine').select2({'allowClear': true});
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
    
        dataPOST['quote_stage_id'] =  quote_stage_id;
    
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
    
        dataPOST['quote_stage_id'] =  quote_stage_id;
    
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
                $('select.machine_backside').select2({'allowClear': true});
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
        	url: site.base_url+'admin/handling_price/saveBOM',
        	type : 'POST',
        	dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
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

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/handling_price/loadExpenseQuote',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
               $('#cost_of_brand').val(response.stage_quote_client.cost_of_brand); 
               $('#labor_cost').val(response.stage_quote_client.labor_cost); 
               $('#loss_cost').val(response.stage_quote_client.loss_cost); 
               $('#profit').val(response.stage_quote_client.profit); 
               totalAll();
            }
        });
    }

    $(document).ready(function () {
        if (counterItemsProducts == 0) {
            loadExpenseQuote();
        }

        $('select.machine').select2({'allowClear': true});
        $('select.machine_backside').select2({'allowClear': true});
    });
</script>