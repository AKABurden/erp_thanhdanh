<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(
    'admin/request_export_products/detail/' . $id . '',
    array('id' => 'request_export_products')
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
                                    <td><?= lang('Đơn hàng', 'order_id') ?></td>
                                    <td>
                                        <input type="text" name="order_id" id="order_id" class="order_id" data-placeholder="<?= lang('Đơn hàng') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['order_id'] : '' ?>" title="">
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
                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                <th style="width: 100px;"><?= lang('Tên Brand') ?></th>
                                <th style="width: 100px;"><?= lang('Mã TP') ?></th>
                                <th style="width: 150px;"><?= lang('Tên TP') ?></th>
                                <th style="width: 100px;"><?= lang('Tên nhóm SP') ?></th>
                                <th style="width: 50px;"><?= lang('Tên Mã Chủng Loại SP') ?></th>
                                <th style="width: 100px;"><?= lang('Tổng Số Lượng SX') ?></th>
                                <th style="width: 150px;"><?= lang('Số Lượng Tồn Kho ') ?></th>
                                <th style="width: 100px;"><?= lang('Số Lượng Cần SX') ?></th>
                                <th style="width: 100px;"><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                <th style="width: 100px;"><?= lang('Số Lượng Cần Mua') ?></th>
                                <th style="width: 100px;"><?= lang('Height TP') ?></th>
                                <th style="width: 100px;"><?= lang('Width TP') ?></th>
                                <th style="width: 100px;"><?= lang('Số Con') ?></th>
                                <th style="width: 100px;"><?= lang('Số Kiện') ?></th>
                                <th style="width: 100px;"><?= lang('Số Kg') ?></th>
                                <th style="width: 100px;"><?= lang('Tổng Số Kiện') ?></th>
                                <th style="width: 100px;"><?= lang('Định Mức Thời Gian') ?></th>
                                <th style="width: 100px;"><?= lang('Thời Gian Quy Định') ?></th>
                                <th style="width: 100px;"><?= lang('Hình Ảnh SP') ?></th>
                                <th style="width: 50px;"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0;
                            if (!empty($dtItems)) { ?>
                                <?php foreach ($dtItems as $key => $value) { ?>
                                    <?php
                                    $item_id = $value['item_id'];
                                    $type_item = $value['type_item'];
                                    $info = null;
                                    $images = base_url('assets/images/tnh/no_image.png');
                                    if ($type_item == "products") {
                                        $info = $this->products_model->rowProductALL($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                        if (!empty($info['images'])) {
                                            $images = base_url('uploads/products/' . $info['images']);
                                        }
                                    }
                                    $warehouses = '
                                                    (Select
                                                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                                                    FROM tblwarehouse_items
                                                    WHERE tblwarehouse_items.id_items = ' . $item_id . '
                                                        AND tblwarehouse_items.type_items = "product" 
                                                        AND tblwarehouse_items.product_quantity > 0
                                                        AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
                                                    ';
                                    $productquantity = $this->db->query($warehouses)->row_array();
                                    if (!empty($productquantity)) {
                                        $product_quantity = $productquantity['product_quantity'];
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td>
                                            <div class="code_item">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="order_item_id[<?= $counter ?>]" class="order_item_id" value="<?= $value['order_item_id'] ?>">
                                                <input type="hidden" name="pod_id[<?= $counter ?>]" class="pod_id" value="<?= $value['pod_id'] ?>">
                                                <input type="hidden" name="quantity[<?= $counter ?>]" class="quantity" value="<?= $value['quantity'] ?>">
                                                <input type="hidden" name="request_export_products_id[<?= $counter ?>]" class="request_export_products_id" value="<?= $value['id'] ?>">
                                                <?= $info['brand_name'] ?>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div class="code_item"><?= $info['code'] ?></div>
                                        </td>
                                        <td class="text-left">
                                            <div class="name_item"><?= $info['name'] ?></div>
                                        </td>
                                        <td class="text-left">
                                            <div class="td_mode"><?= $info['category_name'] ?></div>
                                        </td>
                                        <td class="text-left">
                                            <div class="td_unit"><?= $info['species_name'] ?></div>
                                        </td>
                                        <td class="text-left">
                                            <div class="td_quantity text-center"><?= formatNumber($value['quantity']) ?></div>
                                        </td>
                                        <td class="text-left">
                                            <div class="td_quantity text-center"><?= formatNumber($product_quantity) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-quabtity_manufactures"><input type="text" name="quabtity_manufactures[<?= $counter ?>]" class="quabtity_manufactures form-control number-format" value="<?= formatNumber($value['quabtity_manufactures']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-quabtity_allow"><input type="text" name="quabtity_allow[<?= $counter ?>]" class="number-format quabtity_allow form-control" value="<?= formatNumber($value['quabtity_allow']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-quabtity_purchase"><input onchange="getTotal()" type="text" name="quabtity_purchase[<?= $counter ?>]" class="number-format quabtity_purchase form-control" value="<?= formatNumber($value['quabtity_purchase']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="height_item"><?= $info['height'] ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="wide_item"><?= $info['wide'] ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-totalcon"><input type="text" name="totalcon[<?= $counter ?>]" class="number-format totalcon form-control" value="<?= formatNumber($value['totalcon']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-totalkien"><input type="text" name="totalkien[<?= $counter ?>]" class="number-format totalkien form-control" value="<?= formatNumber($value['totalkien']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-totalkg"><input type="text" name="totalkg[<?= $counter ?>]" class="number-format totalkg form-control" value="<?= formatNumber($value['totalkg']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-totalallkien"><input type="text" name="totalallkien[<?= $counter ?>]" class="number-format totalallkien form-control" value="<?= formatNumber($value['totalallkien']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-timequota"><input type="text" name="timequota[<?= $counter ?>]" class="number-format timequota form-control" value="<?= formatNumber($value['timequota']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-timeregulations"><input type="text" name="timeregulations[<?= $counter ?>]" class="number-format timeregulations form-control" value="<?= formatNumber($value['timeregulations']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="td-image">
                                                <div class="preview_image" style="width: auto;">
                                                    <div class="display-block contract-attachment-wrapper img">
                                                        <div style="width:45px; margin: auto;"><a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                <div class=""><img src="<?= $images ?>" style="border-radius: 50%"></div>
                                                            </a></div>
                                                    </div>
                                                </div>
                                            </div>
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
    var table_price = {};
    $('.tax_id').select2();
</script>
<?php $this->load->view('admin/request_export_products/script_js.php') ?>