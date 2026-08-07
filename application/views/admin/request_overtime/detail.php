<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/request_overtime/detail/' . $id . '',
    array('id' => 'request_overtime')); ?>
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
                                <td><?= lang('Loại Phiếu', 'type_object') ?></td>
                                <td>
                                    <select class="c_select2 select2" name="type_object" id="type_object" data-placeholder="<?= lang('Loại Phiếu') ?>" style="width: 100%;">
                                        <option value="productions_orders" <?=!empty($dtData) && $dtData['type_object'] == 'productions_orders' ? 'selected' : ''?>>Lệnh Sản Xuất</option>
                                        <option value="orders" <?=!empty($dtData) && $dtData['type_object'] == 'orders' ? 'selected' : ''?>>Đơn hàng bán</option>
                                    </select>
                                </td>
                                
                                <td>
                                    <label for="po_id"><span class="name_type"><?=(!empty($dtData) && $dtData['type_object'] == 'orders') ? 'Đơn hàng' : 'Lệnh sản xuất'?></span></label>
                                </td>
                                <td>
                                    <input type="text" name="po_id" id="po_id" class="po_id"
                                           data-placeholder="<?= lang('Lệnh sản xuất') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['po_id'] : '' ?>"
                                           title="">
                                </td>
                                
                            </tr>
                            <tr>
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
                                <td><?= lang('Người lập kế hoạch', 'staff_plan') ?></td>
                                <td>
                                    <select name="staff_plan" id="staff_plan"
                                            data-placeholder="<?= lang('Người lập kế hoạch') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
										<?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ($dtData['staff_plan'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
										<?php endforeach ?>
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
                            <th style="width: 100px;"><?= lang('Tổng SL') ?></th>
                            <th style="width: 100px;"><?= lang('Định mức năng suất') ?></th>
                            <th style="width: 100px;"><?= lang('Nhóm tăng ca') ?></th>
                            <th style="width: 100px;"><?= lang('Số Lượng Người Tăng Ca') ?></th>
                            <th style="width: 100px;"><?= lang('Hệ Số Lương Ca') ?></th>
                            <th style="width: 100px;"><?= lang('Tổng Số Lương Tăng Ca') ?></th>
                            <th style="width: 100px;"><?= lang('Nhân Viên Điều Độ Tăng Ca') ?></th>
                            <th style="width: 100px;"><?= lang('Kết quả') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu Chuẩn/ Quy Định') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)){ ?>
                            <?php foreach ($dtItems as $key => $value){ ?>
                                <?php
                                $optionStaff = '<option></option>';
                                if (!empty($dtStaff)){
                                    foreach ($dtStaff as $kk => $vv){
                                        $optionStaff .= '<option '.($vv['staffid'] == $value['staff_id'] ? 'selected' : '').' value="'.$vv['staffid'].'">'.$vv['firstname'].' '.$vv['lastname'].'</option>';
                                    }
                                }
                                $optionResult = '<option></option>';
                                if (!empty($dtResult)){
                                    foreach ($dtResult as $kk => $vv){
                                        $optionResult .= '<option '.($vv['id'] == $value['result_id'] ? 'selected' : '').' value="'.$vv['id'].'">'.$vv['name'].'</option>';
                                    }
                                }
                                $item_id = $value['item_id'];
                                $type_item = $value['type_item'];
                                $info = null;
                                if ($type_item == "products") {
                                    $info = $this->products_model->rowProduct($item_id);
                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                }
                                $arrId[] = $value['pod_id'];
                                ?>
                                <tr class="tr_<?= $value['order_id'] ?>">
                                    <td  class="text-center"><?= (++$key) ?></td>
                                    <td>
                                        <div class="code_item">
                                            <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                            <input type="hidden" name="order_item_id[<?= $counter ?>]" class="order_item_id" value="<?= $value['order_item_id'] ?>">
                                            <input type="hidden" name="order_id[<?= $counter ?>]" class="order_id" value="<?= $value['order_id'] ?>">
                                            <input type="hidden" name="pod_id[<?= $counter ?>]" class="pod_id" value="<?= $value['pod_id'] ?>">
                                            <input type="hidden" name="quantity[<?= $counter ?>]" class="quantity" value="<?= $value['quantity'] ?>">
                                            <input type="hidden" name="suggest_plan_overtime_item_id[<?= $counter ?>]" class="suggest_plan_overtime_item_id" value="<?= $value['id'] ?>">
                                            <?= $info['code'] ?>
                                            <div style="color: green"><?=!empty($value['code_object_type']) ? $value['code_object_type'] : ''?></div>
                                        </div>
                                    </td>
                                    <td class="text-left"><div class="name_item"><?= $info['name'] ?></div></td>
                                    <td class="text-left"><div class="td_quantity text-center"><?= formatNumber($value['quantity']) ?></div></td>
                                    <td>
                                        <div class="td-capacity_level"><input type="text" name="quota_productivity[<?= $counter ?>]" class="capacity_level number-format form-control" value="<?= ($value['quota_productivity']) ?>"></div></div>
                                    </td>
                                    <td><div class="td-category_overtime"><input type="text" name="category_overtime[<?= $counter ?>]" class="category_overtime form-control" value="<?= $value['category_overtime'] ?>"></div></td>
                                    <td>
                                        <div class="td-quantity_overtime"><input type="text" name="quantity_overtime[<?= $counter ?>]" class="quantity_overtime number-format form-control" value="<?= formatNumber($value['quantity_overtime']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-coefficient"><input type="text" name="coefficient[<?= $counter ?>]" class="coefficient number-format form-control" value="<?= ($value['coefficient']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-salary"><input type="text" name="salary[<?= $counter ?>]" class="salary number-format form-control" value="<?= formatMoney($value['salary']) ?>"></div>
                                    </td>
                                    <td>
                                        <div>
                                            <select class="staff_id" id="staff_id_<?= $counter ?>" name="staff_id[<?= $counter ?>]" style="width: 100%;"  data-placeholder="<?= lang('Nhân viên') ?>">
                                                <?= $optionStaff ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <select class="result_id" id="result_id_<?= $counter ?>" name="result_id[<?= $counter ?>]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                                                <?= $optionResult ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td-standard"><input type="text" name="standard[<?= $counter ?>]" class="standard form-control" value="<?= $value['standard'] ?>"></div>
                                    </td>
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
    $('select.c_select2').select2();
    var dt = '';
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var edit = <?= !empty($dtData) ? 1 : 0 ?>;
    var counter = <?= $counter ?>;
    var count_errors = 0;
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    var dtCategoryStage = <?= !empty($dtCategoryStage) ? json_encode($dtCategoryStage) : '{}' ?>;
    var dtStaff = <?= !empty($dtStaff) ? json_encode($dtStaff) : '{}' ?>;
    var arrId =  <?= !empty($arrId) ? json_encode($arrId) : '[]' ?>;
</script>
<?php $this->load->view('admin/request_overtime/script_js.php') ?>
