<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(
    'admin/request_graft_size/detail/' . $id . '',
    array('id' => 'request_graft_size')
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
                                            set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('Lệnh sản xuất', 'po_id') ?></td>
                                    <td>
                                        <input type="text" name="po_id" id="po_id" class="po_id" data-placeholder="<?= lang('Lệnh sản xuất') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['po_id'] : '' ?>" title="">
                                    </td>
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
                                </tr>
                                <tr>
                                    <td><?= lang('note', 'note') ?></td>
                                    <td colspan="3">
                                        <textarea name="note" id="note" class="form-control note" rows="3"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
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
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 2000px;">
                        <thead>
                            <tr>
                                <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Tên Thiết Bị-Công Đoạn') ?></th>
                                <th class="text-center" colspan="2"><?= lang('Kích Thước Vận Hành') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Số Con/Tờ In') ?></th>
                                <th class="text-center" colspan="5"><?= lang('Mặt 1') ?></th>
                                <th class="text-center" colspan="5"><?= lang('Mặt 2') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Số Lượng Kẽm') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Size Ghép') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Tổng Số Lượng Từng Size') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Layout Ghép Theo Cột/Hàng') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Định Mức Thời Gian') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Hình Ảnh SP') ?></th>
                                <th class="text-center" rowspan="2"><?= lang('Tác vụ') ?></th>
                            </tr>
                            <tr>
                                <th class="text-center"><?= lang('Height') ?></th>
                                <th class="text-center"><?= lang('Width') ?></th>
                                <th class="text-center"><?= lang('Số Cột/Tờ In') ?></th>
                                <th class="text-center"><?= lang('Số Hàng/Tờ In') ?></th>
                                <th class="text-center"><?= lang('Số Lượng Màu In') ?></th>
                                <th class="text-center"><?= lang('Số Lượng Kẽm') ?></th>
                                <th class="text-center"><?= lang('Số Lần Vận Hành/Tờ') ?></th>
                                <th class="text-center"><?= lang('Số Cột/Tờ In') ?></th>
                                <th class="text-center"><?= lang('Số Hàng/Tờ In') ?></th>
                                <th class="text-center"><?= lang('Số Lượng Màu In') ?></th>
                                <th class="text-center"><?= lang('Số Lượng Kẽm') ?></th>
                                <th class="text-center"><?= lang('Số Lần Vận Hành/Tờ') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0;
                            if (!empty($dtItems)) { ?>
                                <?php foreach ($dtItems as $key => $value) { ?>
                                    <?php
                                    $item_id = $value['id_products'];
                                    $info = null;
                                    $machines = get_table_where('tbl_machines', array('id' => $value['machines']), '', 'row_array');
                                    $productions_orders_items_stages = get_table_where('tbl_productions_orders_items_stages', array('id' => $value['id_items_stages']), '', 'row_array');
                                    $readonly1 = 'readonly="readonly"';
                                    $readonly2 = 'readonly="readonly"';
                                    if ($productions_orders_items_stages['face'] == 1) {
                                        $readonly1 = '';
                                    }
                                    if ($productions_orders_items_stages['face_after'] == 2) {
                                        $readonly2 = '';
                                    }

                                    $images = base_url('assets/images/tnh/no_image.png');
                                    $info = $this->products_model->rowProductALL($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/materials/' . $info['images']);
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td>
                                            <div class="code_item">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="machines[<?= $counter ?>]" class="machines" value="<?= $value['machines'] ?>">
                                                <input type="hidden" name="id_products[<?= $counter ?>]" class="id_products" value="<?= $value['id_products'] ?>">
                                                <input type="hidden" name="id_items_stages[<?= $counter ?>]" class="id_items_stages" value="<?= $value['id_items_stages'] ?>">
                                                <input type="hidden" name="id_items_stages[<?= $counter ?>]" class="id_stages" value="<?= $value['id_stages'] ?>">
                                                <input type="hidden" name="request_graft_size_id[<?= $counter ?>]" class="request_graft_size_id" value="<?= $value['id'] ?>">
                                                <?= $machines['code'] ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="height_item"><?= $info['height'] ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="wide_item"><?= $info['wide'] ?></div>
                                        </td>
                                        <td>
                                            <div class="td-childsheet"><input type="text" name="childsheet[<?= $counter ?>]" class="childsheet form-control number-format" value="<?= formatNumber($value['childsheet']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-ColumnsSheets1"><input <?= $readonly1 ?> type="text" name="columnssheets1[<?= $counter ?>]" class="columnssheets1 form-control number-format" value="<?= formatNumber($value['childsheet']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-rowssheets1"><input <?= $readonly1 ?> type="text" name="rowssheets1[<?= $counter ?>]" class="rowssheets1 form-control number-format" value="<?= formatNumber($value['rowssheets1']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-quantity_print_color1"><input readonly="readonly" type="text" name="quantity_print_color1[<?= $counter ?>]" class="quantity_print_color1 form-control number-format" value="<?= formatNumber($value['quantity_print_color1']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-quantity_zinc1"><input readonly="readonly" type="text" name="quantity_zinc1[<?= $counter ?>]" class="quantity_zinc1 form-control number-format" value="<?= formatNumber($value['quantity_zinc1']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-number_operations1"><input readonly="readonly" type="text" name="number_operations1[<?= $counter ?>]" class="number_operations1 form-control number-format" value="<?= formatNumber($value['number_operations1']) ?>"></div>
                                        </td>

                                        <td>
                                            <div class="td-ColumnsSheets2"><input <?= $readonly2 ?> type="text" name="columnssheets2[<?= $counter ?>]" class="columnssheets2 form-control number-format" value="<?= formatNumber($value['childsheet']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-rowssheets2"><input <?= $readonly2 ?> type="text" name="rowssheets2[<?= $counter ?>]" class="rowssheets2 form-control number-format" value="<?= formatNumber($value['rowssheets2']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-quantity_print_color2"><input readonly="readonly" type="text" name="quantity_print_color2[<?= $counter ?>]" class="quantity_print_color2 form-control number-format" value="<?= formatNumber($value['quantity_print_color2']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-quantity_zinc2"><input readonly="readonly" type="text" name="quantity_zinc2[<?= $counter ?>]" class="quantity_zinc2 form-control number-format" value="<?= formatNumber($value['quantity_zinc2']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-number_operations2"><input readonly="readonly" type="text" name="number_operations2[<?= $counter ?>]" class="number_operations2 form-control number-format" value="<?= formatNumber($value['number_operations2']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-quantity_total_zinc"><input readonly="readonly" type="text" name="quantity_total_zinc[<?= $counter ?>]" class="quantity_total_zinc form-control number-format" value="<?= formatNumber($value['quantity_total_zinc']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-timequota"><input type="text" name="timequota[<?= $counter ?>]" class="number-format timequota form-control" value="<?= formatNumber($value['timequota']) ?>"></div>
                                        </td>
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
    var arrId = <?= !empty($arrId) ? json_encode($arrId) : '[]' ?>;
    var taxs = <?= !empty($taxs) ? json_encode($taxs) : '{}' ?>;
    $('.tax_id').select2();
</script>
<?php $this->load->view('admin/request_graft_size/script_js.php') ?>