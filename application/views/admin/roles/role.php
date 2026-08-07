<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin in-title" style="text-transform: uppercase;">
                            <?php echo !empty($title) ? $title : ''; ?>
                        </h4>
                        <hr class="hr-panel-heading"/>
                        <?php if (isset($role)) { ?>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <?php if (isset($role)) { ?>
                            <?php if (total_rows(db_prefix() . 'staff', array('role' => $role->roleid)) > 0) { ?>
                                <div class="alert alert-warning bold">
                                    <?php echo _l('change_role_permission_warning'); ?>
                                    <div class="checkbox">
                                        <input type="checkbox" name="update_staff_permissions"
                                               id="update_staff_permissions">
                                        <label for="update_staff_permissions"><?php echo _l('role_update_staff_permissions'); ?></label>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab_info">Thông tin chung</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other1">Nội dung JD và yêu cầu tuyển dụng</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other2">Tiêu chí loại hồ sơ ứng viên</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other3">Tiêu chuẩn PVV1</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other4">Tiêu chuẩn PVV2</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other7">Tiêu chuẩn 5 giá trị cốt lõi</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other5">Tiêu chuẩn CEO PV</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other6">Các chỉ số tính thưởng P3</a></li>
                            <li><a data-toggle="tab" href="#tab_info_other8">Cấp bậc vị trí vai trò</a></li>
                            <li><a data-toggle="tab" href="#tab_has_per">Phân Quyền (default)</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab_info" class="tab-pane fade in active">
                                <?php $attrs = (isset($role) ? array() : array('autofocus' => true)); ?>

                                <?php $value = (isset($role) ? $role->code_role : ''); ?>
                                <?php echo render_input('code_role', 'Mã vị trí', $value, 'text', $attrs); ?>

                                <?php $value = (isset($role) ? $role->name : ''); ?>
                                <?php echo render_input('name', 'Tên vị trí', $value, 'text', $attrs); ?>

                                <?php $value = (isset($role) ? $role->id_room : ''); ?>
                                <div>
                                    <?php echo render_select('id_room', (!empty($list_room) ? $list_room : []),
                                        array('id', 'name'), 'Phòng ban', $value); ?>
                                </div>

                                <?php $value = (isset($role) ? $role->name_position : ''); ?>
                                <?php echo render_input('name_position', 'Tên chức vụ', $value, 'text', $attrs); ?>

                                <?php $value = (isset($role) ? $role->email : ''); ?>
                                <?php echo render_input('email', 'Email', $value, 'text', $attrs); ?>

                                <div>
                                    <div class="form-group ">
                                        <?php $day_evaluate = (isset($role) ? ($role->day_evaluate ?? 0) : 0); ?>
                                        <?= lang('Vòng đời đánh giá (số ngày)', 'day_evaluate') ?>
                                        <input type="text" name="day_evaluate"
                                               class="form-control day_evaluate number-format"
                                               value="<?= formatMoney($day_evaluate) ?>">
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group ">
                                        <?php $budget_role = (isset($role) ? ($role->budget_role ?? 0) : 0); ?>
                                        <?= lang('Ngân sách (VND/Năm)', 'budget_role') ?>
                                        <input type="text" name="budget_role"
                                               class="form-control budget_role number-format"
                                               value="<?= formatMoney($budget_role) ?>">
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group ">
                                        <?php $headcount = (isset($role) ? ($role->headcount ?? 0) : 0); ?>
                                        <?= lang('Số lượng người cho vị trí', 'headcount') ?>
                                        <input type="text" name="headcount"
                                               class="form-control headcount number-format"
                                               value="<?= formatMoney($headcount) ?>">
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group ">
                                        <?php $asset_link = (isset($role) ? $role->asset_link : ''); ?>
                                        <?= lang('Link bảng tài sản', 'asset_link') ?>
                                        <input type="text" name="asset_link" class="form-control asset_link"
                                               value="<?= $asset_link ?>">
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group ">
                                        <?php $workspace_link = (isset($role) ? $role->workspace_link : ''); ?>
                                        <?= lang('Link Đường dẫn/Workspace', 'workspace_link') ?>
                                        <input type="text" name="workspace_link" class="form-control workspace_link "
                                               value="<?= $workspace_link ?>">
                                    </div>
                                </div>
                                <?php $value = (!empty($role->salary_id) ? $role->salary_id : ''); ?>
                                <?php echo render_select('salary_id',
                                    !empty($list_step_salary) ? $list_step_salary : [],
                                    array('id', 'name', 'coefficient'), 'Bậc lương', $value); ?>

                                <?php $value = (!empty($role->coefficient_salary_id) ? $role->coefficient_salary_id : ''); ?>
                                <?php echo render_select('coefficient_salary_id',
                                    !empty($list_coefficient_salary) ? $list_coefficient_salary : [],
                                    array('id', 'name', 'coefficient'), 'Hệ số lương', $value); ?>
                                <div>
                                    <div class="form-group ">
                                        <?php $login_convention = (isset($role) ? $role->login_convention : ''); ?>
                                        <?= lang('Pass/Quy ước đăng nhập', 'login_convention') ?>
                                        <input type="text" name="login_convention"
                                               class="form-control login_convention " value="<?= $login_convention ?>">
                                    </div>
                                </div>
                            </div>
                            <div id="tab_info_other1" class="tab-pane fade in">
                                <table id="tb-other1"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other1"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Tiêu chí') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 100px;"><?= lang('Hệ số từ') ?></th>
                                        <th style="width: 100px;"><?= lang('Hệ số đến') ?></th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther1 = 0;
                                    $dtDataOther1 = !empty($role) ? get_table_where(
                                        'tbl_criteria_matrix_role',
                                        ['role_id' => $role->roleid ?? 0, 'type' => 1]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther1)) : ?>
                                        <?php foreach ($dtDataOther1 as $key => $value) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other1 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other1[]"
                                                           class="form-control id_other1"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther1[]"
                                                           id="counterOther1" class="form-control"
                                                           value="<?= $counterOther1 ?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other1"
                                                           name="type_other1[]"
                                                           value="<?= $value['type'] ?>">
                                                    <input type="text" name="name_other1[]"
                                                           class="name_other1 form-control"
                                                           value="<?= $value['name'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_from_other1[]"
                                                           class="coefficient_from_other1 form-control number-format"
                                                           value="<?= $value['coefficient_from'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_to_other1[]"
                                                           class="coefficient_to_other1 form-control number-format"
                                                           value="<?= $value['coefficient_to'] ?>">
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other1"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther1++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_info_other2" class="tab-pane fade in">
                                <table id="tb-other2"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other2"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Tiêu chí') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 100px;"><?= lang('Hệ số từ') ?></th>
                                        <th style="width: 100px;"><?= lang('Hệ số đến') ?></th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther2 = 0;
                                    $dtDataOther2 = !empty($role) ? get_table_where(
                                        'tbl_criteria_matrix_role',
                                        ['role_id' => $role->roleid ?? 0, 'type' => 2]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther2)) : ?>
                                        <?php foreach ($dtDataOther2 as $key => $value) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other1 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other2[]"
                                                           class="form-control id_other2"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther2[]"
                                                           id="counterOther2" class="form-control"
                                                           value="<?= $counterOther2 ?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other2"
                                                           name="type_other2[]"
                                                           value="<?= $value['type'] ?>">
                                                    <input type="text" name="name_other2[]"
                                                           class="name_other2 form-control"
                                                           value="<?= $value['name'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_from_other2[]"
                                                           class="coefficient_from_other2 form-control number-format"
                                                           value="<?= $value['coefficient_from'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_to_other2[]"
                                                           class="coefficient_to_other2 form-control number-format"
                                                           value="<?= $value['coefficient_to'] ?>">
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other2"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther2++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_info_other3" class="tab-pane fade in">
                                <table id="tb-other3"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other3"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Tiêu chí') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 100px;"><?= lang('Hệ số từ') ?></th>
                                        <th style="width: 100px;"><?= lang('Hệ số đến') ?></th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther3 = 0;
                                    $dtDataOther3 = !empty($role) ? get_table_where(
                                        'tbl_criteria_matrix_role',
                                        ['role_id' => $role->roleid ?? 0, 'type' => 3]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther3)) : ?>
                                        <?php foreach ($dtDataOther3 as $key => $value) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other3 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other3[]"
                                                           class="form-control id_other3"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther3[]"
                                                           id="counterOther3" class="form-control"
                                                           value="<?= $counterOther3 ?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other3"
                                                           name="type_other3[]"
                                                           value="<?= $value['type'] ?>">
                                                    <input type="text" name="name_other3[]"
                                                           class="name_other3 form-control"
                                                           value="<?= $value['name'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_from_other3[]"
                                                           class="coefficient_from_other3 form-control number-format"
                                                           value="<?= $value['coefficient_from'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_to_other3[]"
                                                           class="coefficient_to_other3 form-control number-format"
                                                           value="<?= $value['coefficient_to'] ?>">
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other3"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther3++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_info_other4" class="tab-pane fade in">
                                <table id="tb-other4"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other4"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Tiêu chí') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 100px;"><?= lang('Hệ số từ') ?></th>
                                        <th style="width: 100px;"><?= lang('Hệ số đến') ?></th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther4 = 0;
                                    $dtDataOther4 = !empty($role) ? get_table_where(
                                        'tbl_criteria_matrix_role',
                                        ['role_id' => $role->roleid ?? 0, 'type' => 4]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther4)) : ?>
                                        <?php foreach ($dtDataOther4 as $key => $value) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other4 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other4[]"
                                                           class="form-control id_other4"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther4[]"
                                                           id="counterOther4" class="form-control"
                                                           value="<?= $counterOther4 ?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other4"
                                                           name="type_other4[]"
                                                           value="<?= $value['type'] ?>">
                                                    <input type="text" name="name_other4[]"
                                                           class="name_other4 form-control"
                                                           value="<?= $value['name'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_from_other4[]"
                                                           class="coefficient_from_other4 form-control number-format"
                                                           value="<?= $value['coefficient_from'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_to_other4[]"
                                                           class="coefficient_to_other4 form-control number-format"
                                                           value="<?= $value['coefficient_to'] ?>">
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other4"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther4++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_info_other5" class="tab-pane fade in">
                                <table id="tb-other5"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other5"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Tiêu chí') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 100px;"><?= lang('Hệ số từ') ?></th>
                                        <th style="width: 100px;"><?= lang('Hệ số đến') ?></th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther5 = 0;
                                    $dtDataOther5 = !empty($role) ? get_table_where(
                                        'tbl_criteria_matrix_role',
                                        ['role_id' => $role->roleid ?? 0, 'type' => 5]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther5)) : ?>
                                        <?php foreach ($dtDataOther5 as $key => $value) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other5 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other5[]"
                                                           class="form-control id_other5"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther5[]"
                                                           id="counterOther5" class="form-control"
                                                           value="<?= $counterOther5?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other5"
                                                           name="type_other5[]"
                                                           value="<?= $value['type'] ?>">
                                                    <input type="text" name="name_other5[]"
                                                           class="name_other5 form-control"
                                                           value="<?= $value['name'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_from_other5[]"
                                                           class="coefficient_from_other5 form-control number-format"
                                                           value="<?= $value['coefficient_from'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_to_other5[]"
                                                           class="coefficient_to_other5 form-control number-format"
                                                           value="<?= $value['coefficient_to'] ?>">
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other5"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther5++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_info_other6" class="tab-pane fade in">
                                <table id="tb-other6"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other6"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Tiêu chí') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 100px;"><?= lang('Hệ số từ') ?></th>
                                        <th style="width: 100px;"><?= lang('Hệ số đến') ?></th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther6 = 0;
                                    $dtDataOther6 = !empty($role) ? get_table_where(
                                        'tbl_criteria_matrix_role',
                                        ['role_id' => $role->roleid ?? 0, 'type' => 6]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther6)) : ?>
                                        <?php foreach ($dtDataOther6 as $key => $value) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other6 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other6[]"
                                                           class="form-control id_other6"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther6[]"
                                                           id="counterOther6" class="form-control"
                                                           value="<?= $counterOther6?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other6"
                                                           name="type_other6[]"
                                                           value="<?= $value['type'] ?>">
                                                    <input type="text" name="name_other6[]"
                                                           class="name_other6 form-control"
                                                           value="<?= $value['name'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_from_other6[]"
                                                           class="coefficient_from_other6 form-control number-format"
                                                           value="<?= $value['coefficient_from'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_to_other6[]"
                                                           class="coefficient_to_other6 form-control number-format"
                                                           value="<?= $value['coefficient_to'] ?>">
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other6"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther6++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_info_other7" class="tab-pane fade in">
                                <table id="tb-other7"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other7"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Tiêu chí') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 100px;"><?= lang('Hệ số từ') ?></th>
                                        <th style="width: 100px;"><?= lang('Hệ số đến') ?></th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther7 = 0;
                                    $dtDataOther7 = !empty($role) ? get_table_where(
                                        'tbl_criteria_matrix_role',
                                        ['role_id' => $role->roleid ?? 0, 'type' => 7]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther7)) : ?>
                                        <?php foreach ($dtDataOther7 as $key => $value) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other7 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other7[]"
                                                           class="form-control id_other7"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther7[]"
                                                           id="counterOther7" class="form-control"
                                                           value="<?= $counterOther7?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other7"
                                                           name="type_other7[]"
                                                           value="<?= $value['type'] ?>">
                                                    <input type="text" name="name_other7[]"
                                                           class="name_other7 form-control"
                                                           value="<?= $value['name'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_from_other7[]"
                                                           class="coefficient_from_other7 form-control number-format"
                                                           value="<?= $value['coefficient_from'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="coefficient_to_other7[]"
                                                           class="coefficient_to_other7 form-control number-format"
                                                           value="<?= $value['coefficient_to'] ?>">
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other7"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther7++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_info_other8" class="tab-pane fade in">
                                <table id="tb-other8"
                                       class="dt-tnh table tnh-table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <a class="btn btn-info btn-icon add-other8"><i
                                                        class="fa fa-plus"></i></a>
                                        </th>
                                        <th style="width: 200px;"><?= lang('Cấp bậc') ?><span class="red">*</span>
                                        </th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $counterOther8 = 0;
                                    $dtDataOther8 = !empty($role) ? get_table_where(
                                        'tbl_role_role_level',
                                        ['role_id' => $role->roleid ?? 0]
                                    ) : [];
                                    ?>
                                    <?php if (!empty($dtDataOther8)) : ?>
                                        <?php foreach ($dtDataOther8 as $key => $value) : ?>
                                            <?php
                                                $option = '';
                                                if(!empty($dtRoleLevel)){
                                                    foreach ($dtRoleLevel as $k => $v) {
                                                        $option .= '<option '.($v['id'] == $value['role_level_id'] ? 'selected' : '').' value="'.$v['id'].'">'.$v['name'].'</option>';
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other8 text-center"><?= ++$key ?></div>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="id_other8[]"
                                                           class="form-control id_other8"
                                                           value="<?= $value['id'] ?>">
                                                    <input type="hidden" name="counterOther8[]"
                                                           id="counterOther8" class="form-control"
                                                           value="<?= $counterOther8?>">
                                                    <input type="hidden" class="form-control role_id"
                                                           value="<?= $value['role_id'] ?>">
                                                    <input type="hidden" class="form-control type_other8"
                                                           name="type_other8[]"
                                                           value="<?= $value['type'] ?>">
                                                    <select name="role_level_id[]" style="width:100%" id="role_level_id_<?= $counterOther8 ?>" class="role_level_id">
                                                        <?= $option; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="td-actions text-center"><span
                                                                class="fa fa-remove btn btn-danger remove-row-other8"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $counterOther8++; ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="tab_has_per" class="tab-pane fade in">
                                <?php
                                if (isset($arr_parent)) {
                                    $data_permission['role'] = $arr_parent;
                                    $data_permission['roleid'] = $role->roleid;
                                } else {
                                    $data_permission['role'] = array();
                                }
                                $this->load->view('admin/staff/permissions', $data_permission);
                                ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <?php if (isset($role_staff)) { ?>
                <div class="col-md-5">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin">
                                <?php echo _l('staff_which_are_using_role'); ?>
                            </h4>
                            <hr class="hr-panel-heading"/>
                            <div class="table-responsive">
                                <table class="table dt-table">
                                    <thead>
                                    <tr>
                                        <th><?php echo _l('staff_dt_name'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($role_staff as $staff) { ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo '<a href="' . admin_url('staff/profile/' . $staff['staffid']) . '">' . staff_profile_image($staff['staffid'],
                                                        [
                                                            'staff-profile-image-small',
                                                        ]) . '</a>';
                                                echo ' <a href="' . admin_url('staff/member/' . $staff['staffid']) . '">' . $staff['firstname'] . ' ' . $staff['lastname'] . '</a>';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        appValidateForm($('form'), {
            code_role: 'required',
            name: 'required',
        });
        show_permission();
    });
</script>
</body>

</html>

<script>

    function totalCapacity() {
        tb = '#tb-capacity tbody tr.tr-capacity';
        var n = $(tb).length;
        var grand_total_point_standard = 0;
        var grand_total_point_evaluate = 0;

        for (ii = 0; ii < n; ii++) {
            element = $(tb)[ii];
            point_weight = intVal($(element).find('.point_weight').val());
            point_standard = intVal($(element).find('.point_standard').val());
            point_evaluate = intVal($(element).find('.point_evaluate').val());

            point_standard = point_weight * point_standard;
            point_evaluate = point_weight * point_evaluate;
            $(element).find('.td-point-standard').html(tnhFormatNumber(point_standard));
            $(element).find('.td-point-evaluate').html(tnhFormatNumber(point_evaluate));

            grand_total_point_standard += point_standard;
            grand_total_point_evaluate += point_evaluate;
        }

        $('.grand_total_point_standard').html(tnhFormatNumber(grand_total_point_standard));
        $('.grand_total_point_evaluate').html(tnhFormatNumber(grand_total_point_evaluate));
        grand_total_ratio = 0;
        if (grand_total_point_standard > 0) {
            grand_total_ratio = grand_total_point_evaluate / grand_total_point_standard;
        }
        $('.grand_total_ratio').html(tnhFormatNumber(grand_total_ratio));
    }

    function removeCapacity(_this) {
        $(_this).closest('tr').remove();
    }

    function addPlusCapacity(capacity, type) {

        tdName = `<td style="display: flex; align-items: center;">
				<a href="javascript:void(0)" onclick="removeCapacity(this)" class="text-danger" style="margin-right: 5px;"><span class="fa fa-remove"></span></a>
				<input type="hidden" name="capacity[]" class="form-control capacity" value="${capacity}">
				<input type="hidden" name="type[]" class="form-control type" value="${type}">
				<input type="text" name="name[]" placeholder="<?= lang('name') ?>" class="form-control" value="">
			</td>`;
        tdPointWeight = `<td>
				<input type="text" name="point_weight[]" onchange="totalCapacity()" class="form-control point_weight number-format" value="0">
			</td>`;
        tdPointStandard = `<td>
				<input type="text" name="point_standard[]" onchange="totalCapacity()" class="form-control point_standard number-format" value="0">
			</td>`;
        tdPointEvaluate = `<td>
				<input type="text" name="point_evaluate[]" onchange="totalCapacity()" class="form-control point_evaluate number-format" value="0">
			</td>`;

        tdTotalPointStandard = `<td class="td-point-standard" style="vertical-align: middle; text-align: center;">0</td>`;
        tdTotalPointEvaluate = `<td class="td-point-evaluate" style="vertical-align: middle; text-align: center;">0</td>`;

        trHtmlCapacity = `<tr class="tr-capacity">
				${tdName}
				${tdPointWeight}
				${tdPointStandard}
				${tdPointEvaluate}
				${tdTotalPointStandard}
				${tdTotalPointEvaluate}
			</tr>`;
        $('.tr-child-' + capacity + '-' + type).after(trHtmlCapacity);
    }
    var counterOther1 = "<?= $counterOther1 ?? 0 ?>";
    $('.add-other1').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other1 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther1[]"
                       id="counterOther1" class="form-control"
                       value="${counterOther1}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other1"
                       name="type_other1[]"
                       value="1">
                <input type="text" name="name_other1[]"
                       class="name_other1 form-control"
                       value="">
                </td>
                 <td>
                    <input type="text" name="coefficient_from_other1[]"
                           class="coefficient_from_other1 form-control number-format"
                           value="">
                </td>
                <td>
                    <input type="text" name="coefficient_to_other1[]"
                           class="coefficient_to_other1 form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other1"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other1 tbody").append(trItem);
        counterOther1++;
        totalOther1();
    });
    $(document).on('click', '.remove-row-other1', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther1();
    });
    function totalOther1()
    {
        tbOther1 = '#tb-other1 tbody tr:not("[class^=not-tr]")';
        var nOther1 = $(tbOther1).length;
        var sttOther1 = 0;

        for (i = 0; i < nOther1; i++)
        {
            sttOther1++;
            element = $(tbOther1)[i];
            $(element).find('.stt-other1').html(sttOther1);
        }
    }
    var counterOther2 = "<?= $counterOther2 ?? 0 ?>";
    $('.add-other2').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other2 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther2[]"
                       id="counterOther2" class="form-control"
                       value="${counterOther2}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other2"
                       name="type_other2[]"
                       value="2">
                <input type="text" name="name_other2[]"
                       class="name_other2 form-control"
                       value="">
                </td>
                 <td>
                    <input type="text" name="coefficient_from_other2[]"
                           class="coefficient_from_other2 form-control number-format"
                           value="">
                </td>
                <td>
                    <input type="text" name="coefficient_to_other2[]"
                           class="coefficient_to_other2 form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other2"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other2 tbody").append(trItem);
        counterOther2++;
        totalOther2();
    });
    $(document).on('click', '.remove-row-other2', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther2();
    });
    function totalOther2()
    {
        tbOther2 = '#tb-other2 tbody tr:not("[class^=not-tr]")';
        var nOther2 = $(tbOther2).length;
        var sttOther2 = 0;

        for (i = 0; i < nOther2; i++)
        {
            sttOther2++;
            element = $(tbOther2)[i];
            $(element).find('.stt-other2').html(sttOther2);
        }
    }
    var counterOther3 = "<?= $counterOther3 ?? 0 ?>";
    $('.add-other3').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other3 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther3[]"
                       id="counterOther3" class="form-control"
                       value="${counterOther3}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other3"
                       name="type_other3[]"
                       value="3">
                <input type="text" name="name_other3[]"
                       class="name_other3 form-control"
                       value="">
                </td>
                 <td>
                    <input type="text" name="coefficient_from_other3[]"
                           class="coefficient_from_other3 form-control number-format"
                           value="">
                </td>
                <td>
                    <input type="text" name="coefficient_to_other3[]"
                           class="coefficient_to_other3 form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other3"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other3 tbody").append(trItem);
        counterOther3++;
        totalOther3();
    });
    $(document).on('click', '.remove-row-other3', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther3();
    });
    function totalOther3()
    {
        tbOther3 = '#tb-other3 tbody tr:not("[class^=not-tr]")';
        var nOther3 = $(tbOther3).length;
        var sttOther3 = 0;

        for (i = 0; i < nOther3; i++)
        {
            sttOther3++;
            element = $(tbOther3)[i];
            $(element).find('.stt-other3').html(sttOther3);
        }
    }
    var counterOther4 = "<?= $counterOther4 ?? 0 ?>";
    $('.add-other4').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other4 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther4[]"
                       id="counterOther4" class="form-control"
                       value="${counterOther4}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other4"
                       name="type_other4[]"
                       value="4">
                <input type="text" name="name_other4[]"
                       class="name_other4 form-control"
                       value="">
                </td>
                 <td>
                    <input type="text" name="coefficient_from_other4[]"
                           class="coefficient_from_other4 form-control number-format"
                           value="">
                </td>
                <td>
                    <input type="text" name="coefficient_to_other4[]"
                           class="coefficient_to_other4 form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other4"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other4 tbody").append(trItem);
        counterOther4++;
        totalOther4();
    });
    $(document).on('click', '.remove-row-other4', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther4();
    });
    function totalOther4()
    {
        tbOther4 = '#tb-other4 tbody tr:not("[class^=not-tr]")';
        var nOther4 = $(tbOther4).length;
        var sttOther4 = 0;

        for (i = 0; i < nOther4; i++)
        {
            sttOther4++;
            element = $(tbOther4)[i];
            $(element).find('.stt-other4').html(sttOther4);
        }
    }
    var counterOther5 = "<?= $counterOther5 ?? 0 ?>";
    $('.add-other5').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other5 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther5[]"
                       id="counterOther5" class="form-control"
                       value="${counterOther5}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other5"
                       name="type_other5[]"
                       value="5">
                <input type="text" name="name_other5[]"
                       class="name_other5 form-control"
                       value="">
                </td>
                 <td>
                    <input type="text" name="coefficient_from_other5[]"
                           class="coefficient_from_other5 form-control number-format"
                           value="">
                </td>
                <td>
                    <input type="text" name="coefficient_to_other5[]"
                           class="coefficient_to_other5 form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other5"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other5 tbody").append(trItem);
        counterOther5++;
        totalOther5();
    });
    $(document).on('click', '.remove-row-other5', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther5();
    });
    function totalOther5()
    {
        tbOther5 = '#tb-other5 tbody tr:not("[class^=not-tr]")';
        var nOther5 = $(tbOther5).length;
        var sttOther5 = 0;

        for (i = 0; i < nOther5; i++)
        {
            sttOther5++;
            element = $(tbOther5)[i];
            $(element).find('.stt-other5').html(sttOther5);
        }
    }
    var counterOther6 = "<?= $counterOther6 ?? 0 ?>";
    $('.add-other6').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other6 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther6[]"
                       id="counterOther6" class="form-control"
                       value="${counterOther6}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other6"
                       name="type_other6[]"
                       value="6">
                <input type="text" name="name_other6[]"
                       class="name_other6 form-control"
                       value="">
                </td>
                 <td>
                    <input type="text" name="coefficient_from_other6[]"
                           class="coefficient_from_other6 form-control number-format"
                           value="">
                </td>
                <td>
                    <input type="text" name="coefficient_to_other6[]"
                           class="coefficient_to_other6 form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other6"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other6 tbody").append(trItem);
        counterOther6++;
        totalOther6();
    });
    $(document).on('click', '.remove-row-other6', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther6();
    });
    function totalOther6()
    {
        tbOther6 = '#tb-other6 tbody tr:not("[class^=not-tr]")';
        var nOther6 = $(tbOther6).length;
        var sttOther6 = 0;

        for (i = 0; i < nOther6; i++)
        {
            sttOther6++;
            element = $(tbOther6)[i];
            $(element).find('.stt-other6').html(sttOther6);
        }
    }
    var counterOther7 = "<?= $counterOther7 ?? 0 ?>";
    $('.add-other7').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other7 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther7[]"
                       id="counterOther7" class="form-control"
                       value="${counterOther7}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other7"
                       name="type_other7[]"
                       value="7">
                <input type="text" name="name_other7[]"
                       class="name_other7 form-control"
                       value="">
                </td>
                 <td>
                    <input type="text" name="coefficient_from_other7[]"
                           class="coefficient_from_other7 form-control number-format"
                           value="">
                </td>
                <td>
                    <input type="text" name="coefficient_to_other7[]"
                           class="coefficient_to_other7 form-control number-format"
                           value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other7"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other7 tbody").append(trItem);
        counterOther7++;
        totalOther7();
    });
    $(document).on('click', '.remove-row-other7', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther7();
    });
    function totalOther7()
    {
        tbOther7 = '#tb-other7 tbody tr:not("[class^=not-tr]")';
        var nOther7 = $(tbOther7).length;
        var sttOther7 = 0;

        for (i = 0; i < nOther7; i++)
        {
            sttOther7++;
            element = $(tbOther7)[i];
            $(element).find('.stt-other7').html(sttOther7);
        }
    }
    var dtRoleLevel = <?= !empty($dtRoleLevel) ? json_encode($dtRoleLevel) : '[]' ?>;
    var counterOther8 = "<?= $counterOther8 ?? 0 ?>";
    for (var i = 0; i <= counterOther8; i++) {
        $(`#role_level_id_${i}`).select2();
    }
    function getRoleLevel(select_id)
    {
        var option = '<option value=""></option>';
        option+= '<option value="0"></option>';
        $.each(dtRoleLevel, function(index, el) {
            selected = select_id == el.id ? 'selected' : '';
            option+= '<option value="'+el.id+'">'+el.name+'</option>';
        });
        return option;
    }
    $('.add-other8').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other8 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther8[]"
                       id="counterOther8" class="form-control"
                       value="${counterOther8}">
                <input type="hidden" class="form-control role_id"
                       value="<?= $role->roleid ?? 0 ?>">
                <input type="hidden" class="form-control type_other7"
                       name="type_other8[]"
                       value="8">
                <select name="role_level_id[]" id="role_level_id_${counterOther8}" class="role_level_id" style="width:100%">
                    ${getRoleLevel()}
                </select>
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other7"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other8 tbody").append(trItem);
        $(`#role_level_id_${counterOther8}`).select2();
        counterOther8++;
        totalOther8();
    });
    $(document).on('click', '.remove-row-other8', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther8();
    });
    function totalOther8()
    {
        tbOther8 = '#tb-other8 tbody tr:not("[class^=not-tr]")';
        var nOther8 = $(tbOther8).length;
        var sttOther8 = 0;

        for (i = 0; i < nOther8; i++)
        {
            sttOther8++;
            element = $(tbOther8)[i];
            $(element).find('.stt-other8').html(sttOther8);
        }
    }
</script>