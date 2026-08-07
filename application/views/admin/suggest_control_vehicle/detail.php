<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/suggest_control_vehicle/detail/' . $id . '',
    array('id' => 'suggest_control_vehicle')); ?>
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
                                <td><?= lang('Khách hàng', 'customer_id') ?></td>
                                <td>
                                    <input type="text" name="customer_id" data-placeholder="<?= lang('Khách hàng') ?>"
                                           id="customer_id" class="customer_id" style="width: 100%;"
                                           value="<?= !empty($dtData) ? $dtData['customer_id'] : '' ?>">
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
                                                <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?>
                                                        value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('note', 'note') ?></td>
                                <td colspan="3">
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
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 2000px;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('Mã thành phẩm') ?></th>
                            <th style="width: 150px;"><?= lang('Tên thành phẩm') ?></th>
                            <th style="width: 50px;"><?= lang('Số con/Kiện') ?></th>
                            <th style="width: 100px;"><?= lang('Số ký/kiện') ?></th>
                            <th style="width: 100px;"><?= lang('Tổng số kiện') ?></th>
                            <th style="width: 150px;"><?= lang('Tổng số ký') ?></th>
                            <th style="width: 100px;"><?= lang('Định mức phương tiện') ?></th>
                            <th style="width: 100px;"><?= lang('Phương tiện') ?></th>
                            <th style="width: 100px;"><?= lang('Mã lộ trình') ?></th>
                            <th style="width: 100px;"><?= lang('Đơn vị vận chuyển') ?></th>
                            <th style="width: 100px;"><?= lang('Đơn giá') ?></th>
                            <th style="width: 100px;"><?= lang('Thành tiền') ?></th>
                            <th style="width: 150px;"><?= lang('Tiêu chuẩn/quy định') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $key => $value) { ?>
                                <?php
                                $optionTransport = '<option></option>';
                                if (!empty($dtTrans)) {
                                    foreach ($dtTrans as $kk => $vv) {
                                        $optionTransport .= '<option ' . ($vv['id'] == $value['transport_unit_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['company'] . '</option>';
                                    }
                                }
                                $dtDelivery = get_table_where('tbl_deliveries',['id' => $value['delivery_id']],'','row_array');
                                $item_id = $value['item_id'];
                                $type_item = $value['type_item'];
                                $info = null;
                                if ($type_item == "products") {
                                    $info = $this->products_model->rowProduct($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                }
                                $arrId[] = $value['delivery_item_id'];
                                ?>
                                <tr>
                                    <td class="text-center"><?= (++$key) ?></td>
                                    <td>
                                        <div class="code_item">
                                            <input type="hidden" name="counter[]" class="counter"
                                                   value="<?= $counter ?>">
                                            <input type="hidden" name="delivery_id[<?= $counter ?>]" class="delivery_id"
                                                   value="<?= $value['delivery_id'] ?>">
                                            <input type="hidden" name="delivery_item_id[<?= $counter ?>]"
                                                   class="delivery_item_id" value="<?= $value['delivery_item_id'] ?>">
                                            <input type="hidden" name="item_id[<?= $counter ?>]" class="item_id"
                                                   value="<?= $value['item_id'] ?>">
                                            <input type="hidden" name="suggest_control_vehicle_item_id[<?= $counter ?>]"
                                                   class="suggest_control_vehicle_item_id" value="<?= $value['id'] ?>">
                                            <?= $info['code'] ?>
                                            <div style="color: green"><?= !empty($dtDelivery) ? $dtDelivery['reference_no'] : '' ?></div>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <div class="name_item"><?= $info['name'] ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div><?= $info['quantity_sheet_bale'] ?></div>
                                    </td>
                                    <td>
                                        <div class="td_ky_kien">
                                            <input type="text" name="number_ky[<?= $counter ?>]" class="number_ky form-control number-format" value="<?= formatNumber($value['number_ky']) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td_total_kienn">
                                            <input type="text" name="total_kien[<?= $counter ?>]" class="total_kien form-control number-format" value="<?= formatNumber($value['total_kien']) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td_total_ky">
                                            <input type="text" name="total_ky[<?= $counter ?>]" class="total_ky form-control number-format" value="<?= formatNumber($value['total_ky']) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td_quota_vehicle">
                                            <input type="text" name="quota_vehicle[<?= $counter ?>]" class="quota_vehicle form-control number-format" value="<?= formatNumber($value['quota_vehicle']) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td_vehicle">
                                            <input type="text" name="vehicle[<?= $counter ?>]" class="vehicle form-control" value="<?= ($value['vehicle']) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td_route">
                                            <input type="text" name="route[<?= $counter ?>]" class="route form-control" value="<?= ($value['route']) ?>">
                                        </div>
                                    </td>
                                    <td style="width: 150px">
                                        <div>
                                            <select class="transport_unit_id" id="transport_unit_id_<?= $counter ?>"
                                                    name="transport_unit_id[<?= $counter ?>]" style="width: 100%;"
                                                    data-placeholder="<?= lang('Phương tiện vận chuyển') ?>">
                                                <?= $optionTransport ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td-price"><input type="text" onchange="getTotal()" name="price[<?= $counter ?>]" class="price form-control number-format" value="<?= formatMoney($value['price']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-amount text-right"><?= formatMoney($value['amount']) ?></div>`
                                    </td>
                                    <td>
                                        <div class="td-standard"><input type="text" name="standard[<?= $counter ?>]" class="standard form-control" value="<?= ($value['standard']) ?>"></div>
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
    var dtTrans = <?= !empty($dtTrans) ? json_encode($dtTrans) : '[]' ?>;
    var arrId = <?= !empty($arrId) ? json_encode($arrId) : '[]' ?>;
</script>
<?php $this->load->view('admin/suggest_control_vehicle/script_js.php') ?>
