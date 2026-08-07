<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/suggest_evaluate/detail/' . $id . '',
    array('id' => 'suggest_evaluate')); ?>
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
                                        <?php  foreach ($employees as $key => $value) : ?>
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
                                <td><?= lang('Loại đánh giá', 'type_evaluate_id') ?></td>
                                <td>
                                    <select name="type_evaluate_id" id="type_evaluate_id"
                                            data-placeholder="<?= lang('Loại đánh giá') ?>" style="width: 100%;"
                                            class="<?= !empty($type_evaluate_id) ? 'none-event' : '' ?>">
                                        <option value=""></option>
                                        <?php if(!empty($dtTypeEvaluate)){ ?>
                                            <?php foreach ($dtTypeEvaluate as $key => $value) { ?>
                                                <option <?= !empty($dtData) ? ($dtData['type_evaluate_id'] == $value['id'] ? 'selected' : '') : ($type_evaluate_id == $value['id'] ? 'selected' : '') ?>
                                                        value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td><?= lang('Đối tượng', 'object_id') ?></td>
                                <td colspan="1">
                                    <div class="form-group">
                                        <input type="text" name="object_id" data-placeholder="<?= lang('Đối tượng') ?>" id="object_id" class="object_id" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['object_id'].'__'.$dtData['object_type'] : '' ?>">
                                    </div>
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
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('Mã đánh giá') ?></th>
                            <th style="width: 150px;"><?= lang('Chi tiết đánh giá') ?></th>
                            <th style="width: 150px;"><?= lang('Hiện trạng thực tế') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)){ ?>
                            <?php foreach ($dtItems as $key => $value){ ?>
                                <?php
                                $child = $value['child'];
                                ?>
                                <tr  class="bg-danger" data-id="<?= $value['id'] ?>">
                                    <td  class="text-center"><?= (++$key) ?></td>
                                    <td><div class="code_item">
                                            <?= $value['name'] ?>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                </tr>
                                <?php if (!empty($child)){ ?>
                                    <?php foreach ($child as $k => $v){ ?>
                                        <?php
                                            $dtEvaluate = get_table_where('tbl_evaluate',['id' => $v['evaluate_id']],'','row_array');
                                        ?>
                                        <tr class="child_<?= $value['id'] ?>">
                                            <td  class="text-center"></td>
                                            <td><div class="code_item">
                                                    <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                    <input type="hidden" name="evaluate_id[<?= $counter ?>]" class="evaluate_id" value="<?= $v['evaluate_id'] ?>">
                                                    <input type="hidden" name="category_evaluate_id[<?= $counter ?>]" class="category_evaluate_id" value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="suggest_plan_evaluate_item_id[<?= $counter ?>]" class="suggest_plan_evaluate_item_id" value="<?= $v['id'] ?>">
                                                    <?= $dtEvaluate['code_evaluate'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="td-content" style="width: 200px"><textarea name="content[<?= $counter ?>]" class="content form-control" rows="3"><?= $v['content'] ?></textarea></div>
                                            </td>
                                            <td><div class="td-actual_situation"><input type="text" name="actual_situation[<?= $counter ?>]" class="actual_situation form-control" value="<?= $v['actual_situation'] ?>"></div></td>
                                            <td><div class="standard_item"><input type="text" name="standard[<?= $counter ?>]" class="standard form-control" value="<?= $v['standard'] ?>"></div></td>
                                            <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                        </tr>
                                        <?php $counter++;} ?>
                                    <?php }?>
                                <?php }?>
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
<?php $this->load->view('admin/suggest_evaluate/script_js.php') ?>
