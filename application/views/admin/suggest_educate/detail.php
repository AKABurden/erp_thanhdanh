<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/suggest_educate/detail/' . $id . '',
    array('id' => 'suggest_educate')); ?>
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
                                <td><?= lang('Người đánh giá', 'staff_evaluate') ?></td>
                                <td>
                                    <select name="staff_evaluate" id="staff_evaluate"
                                            data-placeholder="<?= lang('Người đánh giá') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ($dtData['staff_evaluate'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                        <?php endforeach ?>
                                    </select>
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
                <div>
                    <label for="items_search"><?= lang('Mã đào tạo') ?></label>
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;"
                           data-placeholder="<?= lang('Mã đào tạo') ?>" value="">
                </div>
                <div class="table-responsive mtop10">
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 2100px;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('Mã đào tạo') ?></th>
                            <th style="width: 100px;"><?= lang('Tên đào tạo') ?></th>
                            <th style="width: 150px;"><?= lang('Vị trí đào tạo') ?></th>
                            <th style="width: 200px;"><?= lang('Chi tiết đào tạo') ?></th>
                            <th style="width: 150px;"><?= lang('Số lượng người tham gia') ?></th>
                            <th style="width: 150px;"><?= lang('Người phụ trách đào tạo') ?></th>
                            <th style="width: 150px;"><?= lang('Đơn vị đào tạo') ?></th>
                            <th style="width: 150px;"><?= lang('Chi phí đào tạo') ?></th>
                            <th style="width: 150px;"><?= lang('Thuế vat') ?></th>
                            <th style="width: 150px;"><?= lang('Thành tiền') ?></th>
                            <!-- <th style="width: 100px;"><?= lang('Kết quả') ?></th> -->
                            <th style="width: 100px;"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $key => $value) { ?>
                                <?php
                                $optionResult = '<option></option>';
                                if (!empty($dtResult)) {
                                    foreach ($dtResult as $kk => $vv) {
                                        $optionResult .= '<option ' . ($vv['id'] == $value['result_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                    }
                                }
                                $optionStaff = '<option></option>';
                                if (!empty($employees)) {
                                    foreach ($employees as $kk => $vv) {
                                        $optionStaff .= '<option ' . ($vv['staffid'] == $value['staff_educate'] ? 'selected' : '') . ' value="' . $vv['staffid'] . '">' . $vv['fullname'] . '</option>';
                                    }
                                }
                                $optionTax = '<option></option>';
                                if (!empty($taxs)) {
                                    foreach ($taxs as $kk => $vv) {
                                        $optionTax .= '<option data-rate="'.$vv['taxrate'].'" ' . ($vv['id'] == $value['tax_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                    }
                                }
                                $dtEvaluate = get_table_where('tbl_evaluate', ['id' => $value['evaluate_id']], '',
                                    'row_array');
                                ?>
                                <tr>
                                    <td class="text-center"><?= (++$key) ?></td>
                                    <td>
                                        <div class="code_item">
                                            <input type="hidden" name="counter[]" class="counter"
                                                   value="<?= $counter ?>">
                                            <input type="hidden" name="evaluate_id[<?= $counter ?>]" class="evaluate_id"
                                                   value="<?= $value['evaluate_id'] ?>">
                                            <input type="hidden" name="suggest_plan_educate_item_id[<?= $counter ?>]"
                                                   class="suggest_plan_educate_item_id" value="<?= $value['id'] ?>">
                                            <?= $dtEvaluate['code_evaluate'] ?>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <div class="name_item"><?= $dtEvaluate['name_evaluate'] ?></div>
                                    </td>
                                    <td>
                                        <div class="td-position_educate"><input type="text"
                                                                                name="position_educate[<?= $counter ?>]"
                                                                                class="position_educate form-control"
                                                                                value="<?= $value['position_educate'] ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-detail"><input type="text" name="detail[<?= $counter ?>]"
                                                                      class="detail form-control" value="<?= $value['detail'] ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-quantity"><input type="text" name="quantity[<?= $counter ?>]"
                                                                        class="quantity number-format form-control"
                                                                        value="<?= formatNumber($value['quantity']) ?>"></div>
                                    </td>
                                    <td>
                                        <div>
                                            <select class="staff_educate" id="staff_educate_<?= $counter ?>"
                                                    name="staff_educate[<?= $counter ?>]" style="width: 100%;"
                                                    data-placeholder="<?= lang('Nhân viên') ?>">
                                                <?= $optionStaff ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td-unit_educate"><input type="text" name="unit_educate[<?= $counter ?>]" class="unit_educate form-control" value="<?= $value['unit_educate'] ?>"></div>
                                    </td>
                                    <td>
                                        <div class="td-cost_money"><input type="text" name="cost_money[<?= $counter ?>]" class="cost_money number-format form-control" value="<?= formatMoney($value['cost_money']) ?>"></div>
                                    </td>
                                    <td>
                                        <div>
                                            <select class="tax_id" id="tax_id_<?= $counter ?>" name="tax_id[<?= $counter ?>]" style="width: 100%;"  data-placeholder="<?= lang('Thuế') ?>">
                                                <?= $optionTax ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td-total text-right"><?= formatMoney($value['total']) ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <select class="result_id" id="result_id_<?= $counter ?>"
                                                    name="result_id[<?= $counter ?>]" style="width: 100%;"
                                                    data-placeholder="<?= lang('Kết quả') ?>">
                                                <?= $optionResult ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="standard_item"><input type="text" name="standard[<?= $counter ?>]"
                                                                          class="standard form-control"
                                                                          value="<?= $value['standard'] ?>"></div>
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
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    var dtStaff = <?= !empty($employees) ? json_encode($employees) : '{}' ?>;
    var taxs = <?= !empty($taxs) ? json_encode($taxs) : '{}' ?>;
</script>
<?php $this->load->view('admin/suggest_educate/script_js.php') ?>
