<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(
    'admin/purchase_request_zinc/detail/' . $id . '',
    array('id' => 'purchase_request_zinc')
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
                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                <th style="width: 100px;"><?= lang('Mã NPL') ?></th>
                                <th style="width: 150px;"><?= lang('Tên NPL') ?></th>
                                <th style="width: 100px;"><?= lang('Height NPL') ?></th>
                                <th style="width: 100px;"><?= lang('Width NPL') ?></th>
                                <th style="width: 100px;"><?= lang('Đơn vị tính') ?></th>
                                <th style="width: 100px;"><?= lang('Tổng số lượng') ?></th>
                                <th style="width: 100px;"><?= lang('Mã file') ?></th>
                                <th style="width: 200px;"><?= lang('Đường link') ?></th>
                                <th style="width: 100px;"><?= lang('Mã thiết bị vận hành') ?></th>
                                <th style="width: 100px;"><?= lang('Định Mức Năng Suất/Giờ') ?></th>
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
                                    $images = '';
                                    if ($type_item == "materials") {
                                        $info = $this->items_model->rowMaterial($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                    }
                                    $arrId[] = $value['pod_id'];
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td>
                                            <div class="code_item">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="order_item_id[<?= $counter ?>]" class="order_item_id" value="<?= $value['order_item_id'] ?>">
                                                <input type="hidden" name="pod_id[<?= $counter ?>]" class="pod_id" value="<?= $value['pod_id'] ?>">
                                                <input type="hidden" name="quantity[<?= $counter ?>]" class="quantity" value="0">
                                                <input type="hidden" name="purchase_request_zinc_id[<?= $counter ?>]" class="purchase_request_zinc_id" value="<?= $value['id'] ?>">
                                                <?= $info['code'] ?>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div class="name_item"><?= $info['name'] ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="height_item"><?= $info['height'] ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="wide_item"><?= $info['wide'] ?></div>
                                        </td>
                                        <td class="text-left">
                                            <div class="td_mode"><?= $unit['unit'] ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-quabtity_total"><input type="text" name="quabtity_total[<?= $counter ?>]" class="quabtity_total form-control number-format" value="<?= formatNumber($value['quabtity_total']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-code_file"><input type="text" name="code_file[<?= $counter ?>]" class="code_file form-control" value="<?= ($value['code_file']) ?>"></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-link_file"><input type="text" name="link_file[<?= $counter ?>]" class="link_file form-control" value="<?= ($value['link_file']) ?>"></div>
                                        </td>
                                        <td>
                                            <div class="operating_equipments">
                                                <input type="text" name="operating_equipment[<?= $counter ?>]" id="operating_equipment_<?= $counter ?>" class="operating_equipment" data-placeholder="<?= lang('Mã Thiết Bị Vận Hành') ?>" value="<?= $value['operating_equipment'] ?>" style="width: 100%;">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="td-productivity_norms"><input type="text" name="productivity_norms[<?= $counter ?>]" class="productivity_norms form-control" value="<?= formatNumber($value['productivity_norms']) ?>"></div>
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
<?php $this->load->view('admin/purchase_request_zinc/script_js.php') ?>