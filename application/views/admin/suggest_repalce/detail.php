<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/suggest_repalce/detail/' . $id . '',
    array('id' => 'suggest_replace')); ?>
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
                                        <input type="text" name="reference_no" class="form-control" id="reference_no"
                                               value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                                    </div>
                                </td>
                                <td style="width: 15%;">
                                    <?= lang('date', 'date') ?>
                                </td>
                                <td style="width: 35%;">
                                    <?= form_input('date', set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) :date('d/m/Y H:i'),
                                        'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Thiết bị', 'machines_id') ?></td>
                                <td colspan="1">
                                    <input type="text" name="machines_id" id="machines_id" class="machines_id"
                                           data-placeholder="<?= lang('Mã thiết bị') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['machines_id'] : '' ?>"
                                           title="">
                                </td>
                                <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                                <td colspan="1">
                                    <?php
                                    $branchs = getListBranch();
                                    ?>
                                    <select name="branch_id" id="branch_id" class="branch_id" required="required"
                                            data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
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
                                <td><?= lang('Người đề xuất', 'staff_suggest') ?></td>
                                <td>
                                    <select name="staff_suggest" id="staff_suggest"
                                            data-placeholder="<?= lang('Người đề xuất') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ( $dtData['staff_suggest'] == $value['staffid'] ? 'selected' : '' ) : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td><?= lang('Người duyệt', 'staff_agree') ?></td>
                                <td>
                                    <select name="staff_agree" id="staff_agree"
                                            data-placeholder="<?= lang('Người duyệt') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ( $dtData['staff_agree'] == $value['staffid'] ? 'selected' : '' ) : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Hoành thành thanh toán', 'finish_payment') ?></td>
                                <td colspan="1">
                                    <input type="text" name="finish_payment" class="finish_payment form-control" id="finish_payment">
                                </td>
                                <td><?= lang('note', 'note') ?></td>
                                <td colspan="1">
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
                <div>
                    <label for="items_search"><?= lang('Vật tư') ?></label>
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;"
                           data-placeholder="<?= lang('Vật tư') ?>" value="">
                </div>
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table table-hover" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('Mã vật tư') ?></th>
                            <th style="width: 100px;"><?= lang('Tên vật tư') ?></th>
                            <th style="width: 150px;"><?= lang('Nhà cung cấp') ?></th>
                            <th style="width: 50px;"><?= lang('ĐVT') ?></th>
                            <th style="width: 100px;"><?= lang('quantity') ?></th>
                            <th style="width: 100px;" class="hide"><?= lang('Kết quả') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)){ ?>
                            <?php foreach ($dtItems as $key => $value){ ?>
                                <?php
                                $optionResult = '<option></option>';
                                if (!empty($dtResult)){
                                    foreach ($dtResult as $kk => $vv){
                                        $optionResult .= '<option '.($vv['id'] == $value['result'] ? 'selected' : '').' value="'.$vv['id'].'">'.$vv['name'].'</option>';
                                    }
                                }
                                $item_id = $value['item_id'];
                                $type_item = $value['type_item'];
                                $info = null;
                                if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                                    $info = $this->products_model->rowProduct($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                } else if ($type_item == "materials") {
                                    $info = $this->items_model->rowMaterial($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                                    $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                }

                                ?>
                                <tr>
                                    <td  class="text-center"><?= (++$key) ?></td>
                                    <td><div class="code_item">
                                            <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                            <input type="hidden" name="item_id[<?= $counter ?>]" class="item_id" value="<?= $value['type_item'].'__'.$value['item_id'] ?>">
                                            <input type="hidden" name="suggest_replace_item_id[<?= $counter ?>]" class="suggest_replace_item_id" value="<?= $value['id'] ?>">
                                            <?= $info['code'] ?>
                                        </div>
                                    </td>
                                    <td><div class="name_item"><?= $info['name'] ?></div></td>
                                    <td>
                                        <div class="supplier">
                                            <input type="text" name="suppliers_id[<?= $counter ?>]" id="suppliers_id_<?= $counter ?>" class="suppliers_id"
                                                   data-placeholder="<?= lang('Nhà cung cấp') ?>" style="width: 100%;" value="<?= $value['suppliers_id'] ?>">
                                        </div>
                                    </td>
                                    <td><div class="unit_item"><?= $unit['unit'] ?></div></td>
                                    <td><div class="td-quantity"><input type="text" name="quantity[<?= $counter ?>]" class="quantity form-control number-format" value="<?= formatNumber($value['quantity']) ?>"></div></td>
                                    <td class="hide">
                                        <div>
                                            <select class="result" id="result_<?= $counter ?>" name="result[<?= $counter ?>]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                                                <?= $optionResult ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td><div class="standard_item"><input type="text" name="standard[<?= $counter ?>]" class="standard form-control" value="<?= $value['standard'] ?>"></div></td>
                                    <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                </tr>
                                <?php $counter++;} ?>
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
</script>
<?php $this->load->view('admin/suggest_repalce/script_js.php') ?>
