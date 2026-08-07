<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/suggest_overcome_products/detail/' . $id . '',
    array('id' => 'suggest_overcome_product')); ?>
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
                                               value="<?= !empty($dtSuggestPurchase) ? $dtSuggestPurchase['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                                    </div>
                                </td>
                                <td style="width: 15%;">
                                    <?= lang('date', 'date') ?>
                                </td>
                                <td style="width: 35%;">
                                    <?= form_input('date', set_value('date') ? set_value('date') : !empty($dtSuggestPurchase) ? _dt($dtSuggestPurchase['date']) :date('d/m/Y H:i'),
                                        'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Số LCTSX', 'pod_id') ?></td>
                                <td colspan="1">
                                    <input type="text" name="pod_id" id="pod_id" class="pod_id"
                                           data-placeholder="<?= lang('Số LCTSX') ?>" style="width: 100%;" value="<?= !empty($dtSuggestPurchase) ? $dtSuggestPurchase['pod_id'] : 0 ?>"
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
                                                <option <?= !empty($dtSuggestPurchase) ? ($dtSuggestPurchase['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Ngày nhập', 'date_import') ?></td>
                                <td>
                                    <input type="text" name="date_import" id="date_import"
                                           class="date_import form-control datepicker" autocomplete="off" style="width: 100%;" value="<?= !empty($dtSuggestPurchase) ? _dhau($dtSuggestPurchase['date_import']) : '' ?>"
                                           title="">
                                </td>
                                <td><?= lang('Người phụ trách kiểm', 'employee_id') ?></td>
                                <td>
                                    <select name="employee_id" id="employee_id"
                                            data-placeholder="<?= lang('Người phụ trách kiểm') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtSuggestPurchase) ? ( $dtSuggestPurchase['employee_id'] == $value['staffid'] ? 'selected' : '' ) : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('note', 'note') ?></td>
                                <td colspan="3">
                                    <textarea name="note" id="note" class="form-control note" rows="3"><?= !empty($dtSuggestPurchase) ? $dtSuggestPurchase['note'] : '' ?></textarea>
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
                    <label for="items_search"><?= lang('Mặt hàng') ?></label>
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;"
                           data-placeholder="<?= lang('Mặt hàng') ?>" value="">
                </div>
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table table-hover" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
                            <th style="width: 50px"><?= lang('tnh_images') ?></th>
                            <th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
                            <th style="width: 50px;"><?= lang('ĐVT') ?></th>
                            <th style="width: 100px;"><?= lang('quantity') ?></th>
                            <th style="width: 100px;"><?= lang('Số kiện') ?></th>
                            <th style="width: 100px;"><?= lang('Số Kg') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0; if (!empty($dtSuggestPurchaseItems)){ ?>
                            <?php foreach ($dtSuggestPurchaseItems as $key => $value){ ?>
                                <?php
                                    $item_id = $value['item_id'];
                                    $type_item = $value['type_item'];
                                    $info = null;
                                    if ($type_item == "products") {
                                        $info = $this->products_model->rowProduct($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                        if (!empty($info['images'])) {
                                            $images = base_url('uploads/products/' . $info['images']);
                                        }
                                    }
                                    if (empty($images)) {
                                        $images = base_url('assets/images/tnh/no_image.png');
                                    }

                                ?>
                                <tr>
                                    <td  class="text-center"><?= (++$key) ?></td>
                                    <td><div class="code_item">
                                        <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                        <input type="hidden" name="item_id[<?= $counter ?>]" class="item_id" value="<?= $value['item_id'].'__'.$value['type_item'] ?>">
                                        <input type="hidden" name="suggest_purchase_product_item_id[<?= $counter ?>]" class="suggest_purchase_product_item_id" value="<?= $value['id'] ?>">
                                            <?= $info['code'] ?>
                                    </div>
                                    </td>
                                    <td><div class="td-image">
                                        <div class="preview_image" style="width: auto;">
                                            <div class="display-block contract-attachment-wrapper img">
                                                <div style="width:45px;">
                                                    <a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                        <div class="">
                                                            <img src="<?= $images ?>" style="border-radius: 50%">
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </td>
                                    <td><div class="name_item"><?= $info['name'] ?></div></td>
                                    <td><div class="unit_item"><?= $unit['unit'] ?></div></td>
                                    <td><div class="td-quantity"><input type="text" name="quantity[<?= $counter ?>]" class="quantity form-control number-format" value="<?= formatNumber($value['quantity']) ?>"></div></td>
                                    <td><div class="td-quantity-kien"><input type="text" name="quantity_kien[<?= $counter ?>]" class="quantity_kien number-format form-control" value="<?= formatNumber($value['quantity_kien']) ?>"></div></td>
                                    <td><div class="td-quantity-kg"><input type="text" name="quantity_kg[<?= $counter ?>]" class="quantity_kg form-control number-format" value="<?= formatNumber($value['quantity_kg']) ?>"></div></td>
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
    var edit = 0;
    var counter = <?= $counter ?>;
    var count_errors = 0;
</script>
<?php $this->load->view('admin/suggest_overcome_products/script_js.php') ?>
