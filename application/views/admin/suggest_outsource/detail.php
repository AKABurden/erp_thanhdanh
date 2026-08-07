<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(
    'admin/suggest_outsource/detail/' . $id . '',
    array('id' => 'suggest_outsource')
); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td style="width: 15%;">
                                        <?= lang('dt_reference_suggest', 'reference_no') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <div class="form-group">
                                            <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                                        </div>
                                    </td>
                                    <td style="width: 15%;">
                                        <?= lang('date', 'date') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input(
                                            'date',
                                            (set_value('date') ? set_value('date') : (!empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'))),
                                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('Loại', 'object_type') ?></td>
                                    <td>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="radio radio-primary">
                                                        <input type="radio" value="order" <?= !empty($dtData) && $dtData['object_type'] == 'order' ? 'checked' : 'checked'  ?> id="object_type1" name="object_type">
                                                        <label for="object_type1">Đơn hàng</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="radio radio-primary">
                                                        <input type="radio" value="po" <?= !empty($dtData) && $dtData['object_type'] == 'po' ? 'checked' : ''  ?> id="object_type2" name="object_type">
                                                        <label for="object_type2">Lệnh sản xuất</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="text" name="object_id" id="object_id" class="object_id" multiple data-placeholder="<?= lang('Đơn hàng/ Lệnh sản xuất') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['object_id'] : '' ?>" title="">
                                    </td>
                                    <td>
                                        <?= lang('Ngày gửi yêu cầu gia công', 'date_request') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input(
                                            'date_request',
                                            (set_value('date') ? set_value('date') : (!empty($dtData) ? _d($dtData['date_request']) : '')),
                                            'id="date" class="form-control datepicker" placeholder="' . lang('Ngày gửi yêu cầu gia công') . '" required '
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('Người lập kế hoạch', 'staff_plan') ?></td>
                                    <td>
                                        <select name="staff_plan" id="staff_plan" data-placeholder="<?= lang('Người lập kế hoạch') ?>" style="width: 100%;" class="">
                                            <option value=""></option>
                                            <?php foreach ($employees as $key => $value) : ?>
                                                <option <?= !empty($dtData) ? ($dtData['staff_plan'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?= lang('Ngày giao hàng', 'date_delivery') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input(
                                            'date_delivery',
                                            (set_value('date') ? set_value('date') : (!empty($dtData) ? _d($dtData['date_delivery']) : '')),
                                            'id="date" class="form-control datepicker" placeholder="' . lang('Ngày giao hàng') . '" required '
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                                    <td colspan="1">
                                        <?php
                                        $branchs = getListBranch();
                                        ?>
                                        <select name="branch_id" id="branch_id" class="branch_id" required="required" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                            <option value=""></option>
                                            <?php if (!empty($branchs)) { ?>
                                                <?php foreach ($branchs as $key => $value) { ?>
                                                    <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?= lang('Ngày đưa đi dự kiến', 'date_go_expected') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input(
                                            'date_go_expected',
                                            (set_value('date') ? set_value('date') : (!empty($dtData) ? _d($dtData['date_go_expected']) : '')),
                                            'id="date" class="form-control datepicker" placeholder="' . lang('Ngày đưa đi dự kiến') . '" required '
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('note', 'note') ?></td>
                                    <td colspan="1">
                                        <textarea name="note" id="note" class="form-control note" rows="3"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
                                    </td>
                                    <td>
                                        <?= lang('Ngày về dự kiến', 'date_satisfied_expected') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input(
                                            'date_satisfied_expected',
                                            (set_value('date') ? set_value('date') : (!empty($dtData) ? _d($dtData['date_satisfied_expected']) : '')),
                                            'id="date" class="form-control datepicker" placeholder="' . lang('Ngày về dự kiến') . '" required '
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td><?= lang('Deadline', 'deadline') ?></td>
                                    <td colspan="1">
                                        <div class="input-group">
                                            <div class="form-group">
                                                <div id="deadline" class="form-control"></div>
                                            </div>
                                            <div class="input-group-addon">Ngày</div>
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div style="margin-bottom: 20px">
                    <label for="items_search"><?= lang('Mặt hàng') ?></label>
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="<?= lang('Mặt hàng') ?>" value="">
                </div>
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 4500px;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                <th style="width: 150px;"><?= lang('Đơn Vị Gia Công(NCC)') ?></th>
                                <th style="width: 100px;"><?= lang('Mã thành phẩm') ?></th>
                                <th style="width: 150px;"><?= lang('Tên thành phẩm') ?></th>
                                <th style="width: 150px;">Quy Cách</th><!--mode-->
                                <th style="width: 150px;">Đơn Vị Tính</th><!--unit-->
                                <th style="width: 150px;">Chi Tiết Gia Công</th>
                                <th style="width: 150px;">Đơn Vị Vận chuyển Gia Công</th><!--shipping_unit_outsource-->
                                <th style="width: 150px;">Phương tiện vận chuyển gia công</th><!--transport_outsource-->
                                <th style="width: 150px;">Chi Phí Vận Chuyển</th><!--price_transport  amount_transport-->
                                <th style="width: 150px;">Đơn Giá Gia Công</th><!--pricee-->
                                <th style="width: 150px;">Số lượng gia công</th><!--pricee-->
                                <th style="width: 150px;">Thành tiền</th><!--pricee-->
                                <th style="width: 150px;">VAT (%)</th><!--radio_vat-->
                                <th style="width: 150px;">Tổng Sau VAT</th><!--total_vat-->
                                <th style="width: 150px;"><?= lang('Số lượng tờ in') ?></th>
                                <th style="width: 100px;"><?= lang('NVL in') ?></th>
                                <th style="width: 150px;"><?= lang('Số lượng bù hao') ?></th>
                                <th style="width: 100px;"><?= lang('Số lượng bù hao xuất thêm (tờ in)') ?></th>
                                <th style="width: 100px;"><?= lang('Khổ in(cm)') ?></th>
                                <th style="width: 100px;"><?= lang('Hình ảnh') ?></th>
                                <th style="width: 300px;"><?= lang('Loại hình phủ') ?></th>
                                <th style="width: 100px;"><?= lang('Cách in') ?></th>
                                <th style="width: 100px;"><?= lang('Số mặt in') ?></th>
                                <th style="width: 100px;"><?= lang('Số màu - Mặt A') ?></th>
                                <th style="width: 100px;"><?= lang('Số màu - Mặt B') ?></th>
                                <th style="width: 100px;"><?= lang('Số kẽm- Mặt A') ?></th>
                                <th style="width: 100px;"><?= lang('Số kẽm- Mặt B') ?></th>
                                <th style="width: 100px;"><?= lang('Nhíp kẽm') ?></th>
                                <th style="width: 100px;"><?= lang('Hình ảnh mực in') ?></th>
                                <th style="width: 100px;"><?= lang('Hình ảnh bóng phủ') ?></th>
                                <th style="width: 100px;"><?= lang('Ghi Chú') ?></th>
                                <th style="width: 50px;"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0;
                            if (!empty($dtItems)) { ?>
                                <?php foreach ($dtItems as $key => $value) { ?>
                                    <?php
                                    $optionStaff = '<option></option>';
                                    if (!empty($dtStaff)) {
                                        foreach ($dtStaff as $kk => $vv) {
                                            $optionStaff .= '<option ' . ($vv['staffid'] == $value['staff_id'] ? 'selected' : '') . ' value="' . $vv['staffid'] . '">' . $vv['firstname'] . ' ' . $vv['lastname'] . '</option>';
                                        }
                                    }
                                    $optionResult = '<option></option>';
                                    if (!empty($dtResult)) {
                                        foreach ($dtResult as $kk => $vv) {
                                            $optionResult .= '<option ' . ($vv['id'] == $value['result_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                        }
                                    }

                                    $optionTax = '<option></option>';
                                    if (!empty($taxs)) {
                                        foreach ($taxs as $kk => $vv) {
                                            $optionTax .= '<option data-rate="' . $vv['taxrate'] . '" ' . ($vv['id'] == $value['tax_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                        }
                                    }
                                    $optionPrint = '<option></option>';
                                    if (!empty($print)) {
                                        foreach ($print as $kk => $vv) {
                                            $optionPrint .= '<option ' . ($vv['id'] == $value['print'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                        }
                                    }
                                    $optionMaterial = '<option></option>';
                                    if (!empty($value['materials'])) {
                                        foreach ($value['materials'] as $kk => $vv) {
                                            $optionMaterial .= '<option  data-quantity_compensation="' . $vv['quantity_compensation'] . '" data-landscape_print_size="' . $vv['landscape_print_size'] . '" ' . ($vv['type'] . '__' . $vv['item_id'] == $value['type_material'] . '__' . $value['material'] ? 'selected' : '') . ' value="' . $vv['type'] . '__' . $vv['item_id'] . '">' . $vv['name_items'] . '</option>';
                                        }
                                    }
                                    $item_id = $value['item_id'];
                                    $type_item = $value['type_item'];
                                    $info = null;
                                    if ($type_item == "products") {
                                        $info = $this->products_model->rowProduct($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                    }
                                    $arrId[] = $value['order_item_id'];
                                    if ($value['object_type'] == 'po') {
                                        $dtObject = get_table_where('tbl_productions_orders', ['id' => $value['order_id']], '', 'row_array');
                                    } else {
                                        $dtObject = get_table_where('tbl_orders', ['id' => $value['order_id']], '', 'row_array');
                                    }
                                    ?>
                                    <tr class="tr_<?= $value['order_id'] ?>">
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td>
                                            <div class="supplier">
                                                <input type="text" name="suppliers_id[<?= $counter ?>]" id="suppliers_id_<?= $counter ?>" class="suppliers_id" data-placeholder="<?= lang('Nhà gia công') ?>" value="<?= $value['supplier_id'] ?>" style="width: 100%;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="code_item">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="order_item_id[<?= $counter ?>]" class="order_item_id" value="<?= $value['order_item_id'] ?>">
                                                <input type="hidden" name="productions_orders_id[<?= $counter ?>]" class="productions_orders_id" value="<?= $value['productions_orders_id'] ?>">
                                                <input type="hidden" name="plan_id[<?= $counter ?>]" class="plan_id" value="<?= $value['plan_id'] ?>">
                                                <input type="hidden" name="pod_id[<?= $counter ?>]" class="pod_id" id="pod_id_<?= $counter ?>" value="<?= $value['pod_id'] ?>">
<!--                                                <input type="hidden" name="quantity[--><?php //= $counter ?><!--]" class="quantity" value="--><?php //= $value['quantity'] ?><!--">-->
                                                <input type="hidden" name="order_id[<?= $counter ?>]" class="order_id" value="<?= $value['order_id'] ?>">
                                                <input type="hidden" name="suggest_plan_outsource_item_id[<?= $counter ?>]" class="suggest_plan_outsource_item_id" value="<?= $value['id'] ?>">
                                                <?= $info['code'] ?>
                                                <div style="color: green"><?= $dtObject['reference_no'] ?></div>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div class="name_item"><?= $info['name'] ?></div>
                                        </td>


                                        <td><div class="mode" style="width: 100px"><?= $info['mode'] ?></div></td>
                                        <td style="width: 150px" class="unit"><?=$unit['unit']?></td>
                                        <td style="width: 150px"><textarea style="width: 150px;" name="note_detail[<?= $counter ?>]" class="note_detail form-control"><?=$value['note_detail'] ?? ''?></textarea></td>
                                        <td style="width: 150px"><div style="width: 150px"><input type="text" name="shipping_unit_outsource[<?= $counter ?>]" class="shipping_unit_outsource form-control" value="<?=$value['shipping_unit_outsource'] ?? ''?>"></div></td>
                                        <td style="width: 150px"><div style="width: 150px"><input type="text" name="transport_outsource[<?= $counter ?>]" class="transport_outsource form-control" value="<?=$value['transport_outsource'] ?? ''?>"></div></td>
                                        <td style="width: 150px"><div style="width: 150px"><input type="text" name="price_transport[<?= $counter ?>]" class="price_transport form-control number-format" value="<?=number_format_data($value['price_transport']) ?? 0?>"></div></td>
                                        <td style="width: 150px"><div style="width: 150px"><input type="text" name="price[<?= $counter ?>]" class="price form-control number-format" value="<?=number_format_data($value['price']) ?? 0?>"></div></td>
                                        <td style="width: 150px">
                                            <div class="td-quantity" style="width: 150px">
                                                <input type="text" name="quantity[<?= $counter ?>]" class="quantity form-control number-format" value="<?=number_format_data($value['quantity']) ?? 0?>">
                                            </div>
                                        </td>
                                        <td style="width: 150px"><div style="width: 100px" class="td-amount"><?=number_format_data($value['amount']) ?? 0?></div></td>
                                        <td style="width: 150px"><div>
                                                <div class="select2-container tax_id" id="s2id_tax_id_0" style="width: 100%;">
                                                <select class="tax_id" id="tax_id_<?= $counter ?>" name="tax_id[<?= $counter ?>]" style="width: 100%;" data-placeholder="Thuế" tabindex="-1" title="">
                                                    <?php foreach ($taxs as $k => $v) { ?>
                                                        <?php
                                                            if($v['id'] == $value['tax_id']) {
                                                                $tax_rate = $v['taxrate'];
                                                            }
                                                        ?>
                                                        <option <?= ($v['id'] == $value['tax_id'] ? 'selected' : '') ?> data-rate="<?= $v['taxrate'] ?>" value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" class="tax_rate" name="tax_rate[<?= $counter ?>]" value="<?= $tax_rate ?? 0 ?>">
                                            </div></td>
                                        <td style="width: 150px">
                                            <div class="td-grand_total" style="width: 150px"><?=number_format_data($value['grand_total']) ?? 0?></div>
                                        </td>



                                        <td style="width: 150px" class="text-center">
                                            <input type="hidden" name="sltin[<?= $counter ?>]" class="sltin" value="<?= $value['sltin'] ?>"><?= formatNumber($value['sltin']) ?>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="material" id="material_<?= $counter ?>" name="material[<?= $counter ?>]" style="width: 100%;" data-placeholder="<?= lang('Nguyên vật liệu') ?>">
                                                    <?= $optionMaterial ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="width: 150px" class="text-center tdquantity_compensation"><input type="hidden" name="quantity_compensation[<?= $counter ?>]" class="quantity_compensation" value="<?= $value['quantity_compensation'] ?>"><?= formatNumber($value['quantity_compensation']) ?></td>
                                        <td>
                                            <div class="td-quantity_compensation_more"><input type="text" name="quantity_compensation_more[<?= $counter ?>]" class="quantity_compensation_more form-control number-format" value="<?= formatNumber($value['quantity_compensation_more']) ?>"></div>
                                        </td>
                                        <td style="width: 150px" class="text-center tdlandscape_print_size"><input type="hidden" name="landscape_print_size[<?= $counter ?>]" class="landscape_print_size" value="<?= ($value['landscape_print_size']) ?>"><?= ($value['landscape_print_size']) ?></td>
                                        <td>
                                            <div class="td-image" style="display: flex;justify-content: center;">
                                                <div class="preview_image" style="width: auto;">
                                                    <div class="display-block contract-attachment-wrapper img">
                                                        <div style="width:45px;">
                                                            <a href="<?= $value['images'] ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                <div class="">
                                                                    <img src="<?= $value['images'] ?>" style="border-radius: 50%">
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div class="td-stage">
                                                <input type="text" name="stage_id[<?= $counter ?>]" id="stage_id_<?= $counter ?>" class="stage_id" data-placeholder="<?= lang('Công đoạn') ?>" style="width: 100%;" value="<?= $value['stage_id'] ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="print" id="print_<?= $counter ?>" name="print[<?= $counter ?>]" style="width: 100%;" data-placeholder="<?= lang('Cách in') ?>">
                                                    <?= $optionPrint ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-number_of_printed_sides"><input type="text" name="number_of_printed_sides[<?= $counter ?>]" class="number_of_printed_sides form-control number-format" value="<?= formatNumber($value['number_of_printed_sides']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-color_number_a"><input type="text" name="color_number_a[<?= $counter ?>]" class="color_number_a form-control number-format" value="<?= formatNumber($value['color_number_a']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-color_number_b"><input type="text" name="color_number_b[<?= $counter ?>]" class="color_number_b form-control number-format" value="<?= formatNumber($value['color_number_b']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-zinc_number_a"><input type="text" name="zinc_number_a[<?= $counter ?>]" class="zinc_number_a form-control number-format" value="<?= formatNumber($value['zinc_number_a']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-zinc_number_b"><input type="text" name="zinc_number_b[<?= $counter ?>]" class="zinc_number_b form-control number-format" value="<?= formatNumber($value['zinc_number_b']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-grape"><input type="text" name="grape[<?= $counter ?>]" class="grape form-control number-format" value="<?= formatNumber($value['grape']) ?>"></div>
                                        </td>
                                        <td style="width: 450px"><input type="file" name="image_mucin[<?= $counter ?>]" class="form-control image" value="" title=""></td>
                                        <td style="width: 450px"><input type="file" name="image_bongmo[<?= $counter ?>]" class="form-control image" value="" title=""></td>
                                        <td style="width: 250px"><textarea style="width: 100%;" class="note_items" name="note_items[<?= $counter ?>]" value="<?= ($value['note']) ?>"></textarea></td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                                <?php $counter++;
                                } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <input type="hidden" name="add" id="" class="form-control" value="1">
            <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                <?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var dt = '';
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var edit = <?= !empty($dtData) ? 1 : 0 ?>;
    var counter = <?= $counter ?>;
    var count_errors = 0;
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    var dtCategoryStage = <?= !empty($dtCategoryStage) ? json_encode($dtCategoryStage) : '{}' ?>;
    var dtStaff = <?= !empty($dtStaff) ? json_encode($dtStaff) : '{}' ?>;
    var taxs = <?= !empty($taxs) ? json_encode($taxs) : '{}' ?>;
    var print = <?= !empty($print) ? json_encode($print) : '{}' ?>;
    var arrId = <?= !empty($arrId) ? json_encode($arrId) : '[]' ?>;
    var arrObjectId = <?= !empty($arrObjectId) ? json_encode($arrObjectId) : '{}' ?>;
</script>
<?php $this->load->view('admin/suggest_outsource/script_js.php') ?>
<script>
    function daysBetween(date1, date2) {
        const [d1, m1, y1] = date1.split('/').map(Number);
        const [d2, m2, y2] = date2.split('/').map(Number);

        const start = new Date(y1, m1 - 1, d1);
        const end   = new Date(y2, m2 - 1, d2);

        const diffTime = end - start;
        const diffDays = diffTime / (1000 * 60 * 60 * 24);

        return diffDays;
    }
    $('input[name="date_go_expected"], input[name="date_satisfied_expected"]').change(function() {
        if($('input[name="date_go_expected"]').val() != '' && $('input[name="date_satisfied_expected"]').val() != ''){
            var date_go_expected = $('input[name="date_go_expected"]').val();
            var date_satisfied_expected = $('input[name="date_satisfied_expected"]').val();
            var days = daysBetween(date_go_expected, date_satisfied_expected);
            $('#deadline').html(days);
        } else {
            $('#deadline').html('');
        }
    })

    $(function () {
        $('input[name="date_go_expected"]').trigger('change');
    })

</script>
