<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/suggest_purchase_npl/detail/' . $id . '',
    array('id' => 'suggest_purchase_npl')); ?>
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
                                               value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>"
                                               readonly="" aria-invalid="false">
                                    </div>
                                </td>
                                <td style="width: 15%;">
                                    <?= lang('date', 'date') ?>
                                </td>
                                <td style="width: 35%;">
                                    <?= form_input('date',
                                        set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                        'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Nhà cung cấp', 'supplier_id') ?></td>
                                <td>
                                    <select name="supplier_id" id="supplier_id"
                                            data-placeholder="<?= lang('Nhà cung cấp') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($dtSuppliers as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ($dtData['supplier_id'] == $value['id'] ? 'selected' : '') : '' ?>
                                                    value="<?= $value['id'] ?>"><?= $value['company'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td><?= lang('Phiếu yêu cầu mua', 'purchase_request_material_id') ?></td>
                                <td colspan="1">
                                    <input type="text" name="purchase_request_material_id" id="purchase_request_material_id" class="purchase_request_material_id"
                                           data-placeholder="<?= lang('Phiếu yêu cầu mua') ?>" style="width: 100%;"
                                           value="<?= !empty($dtData) ? $dtData['purchase_request_material_id'] : '' ?>"
                                           title="">
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Ngày nhập', 'date_import') ?></td>
                                <td colspan="1">
                                    <input type="text" name="date_import" id="date_import" autocomplete="off" class="date_import form-control datepicker"
                                           style="width: 100%;"
                                           value="<?= !empty($dtData) ? _dhau($dtData['date_import']) : '' ?>"
                                           title="">
                                <td><?= lang('note', 'note') ?></td>
                                <td colspan="1">
                                    <textarea name="note" id="note" class="form-control note"
                                              rows="3"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
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
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;"
                           data-placeholder="<?= lang('Mặt hàng') ?>" value="">
                </div>
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('Mã NPL') ?></th>
                            <th style="width: 150px;"><?= lang('Tên NPL') ?></th>
                            <th style="width: 100px;"><?= lang('Số Lượng Thực Mua') ?></th>
                            <th style="width: 100px;"><?= lang('Số Lượng Nhập') ?></th>
                            <th style="width: 100px;"><?= lang('Chi Tiết') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu Chuẩn/Quy Định') ?></th>
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
                                if ($type_item == "products") {
                                    $info = $this->products_model->rowProduct($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                } elseif ($type_item == 'materials'){
                                    $info = $this->items_model->rowMaterial($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                }
                                $arrId[] = $value['purchase_request_material_item_id'];
                                ?>
                                <tr>
                                    <td class="text-center"><?= (++$key) ?></td>
                                    <td>
                                        <div class="code_item">
                                            <input type="hidden" name="counter[]" class="counter"
                                                   value="<?= $counter ?>">
                                            <input type="hidden" name="purchase_request_material_item_id[<?= $counter ?>]"
                                                   class="purchase_request_material_item_id"
                                                   value="<?= $value['purchase_request_material_item_id'] ?>">
                                            <input type="hidden" name="suggest_purchase_npl_item_id[<?= $counter ?>]"
                                                   class="suggest_purchase_npl_item_id" value="<?= $value['id'] ?>">
                                            <?= $info['code'] ?>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <div class="name_item"><?= $info['name'] ?></div>
                                    </td>
                                    <td>
                                        <div class="td-quantity"><input type="text" name="quantity[<?= $counter ?>]" onchange="getTotal()" class="quantity form-control" readonly value="<?= formatMoney($value['quantity']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-quantity-import"><input type="text" name="quantity_import[<?= $counter ?>]" onchange="getTotal()" class="quantity_import form-control number-format" value="<?= formatMoney($value['quantity_import']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-detail"><textarea cols="2" rows="3" name="detail[<?= $counter ?>]" class="detail form-control"><?= $value['detail'] ?>
                                            </textarea>
                                    </td>
                                    <td>
                                        <div class="td-standard"><input type="text" name="standard[<?= $counter ?>]" class="standard form-control"  value="<?= $value['standard'] ?>"></div>
                                    </td>
                                    <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)"
                                                               class="fa fa-remove remove-row"></a></td>
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
    var taxs = <?= !empty($taxs) ? json_encode($taxs) : '{}' ?>;
    var arrId = <?= !empty($arrId) ? json_encode($arrId) : '[]' ?>;
</script>
<?php $this->load->view('admin/suggest_purchase_npl/script_js.php') ?>
