<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<style>
    .text_checklist {
        position: absolute;
        resize: none;
        overflow: hidden;
        left: 25px;
        top: 0;
        font-size: 14px;
        width: 90%;
        border-radius: 3px;
        border: 0;
        outline: 0;
        padding-left: 5px;
    }

    .font-medium-12 {
        font-size: 12px !important;
    }

    .tnh-tb td label,
    #tb-productions-orders th label {
        text-transform: capitalize;
    }

    .trouble-material,
    .trouble-man,
    .trouble-machine,
    .trouble-method,
    .trouble-environment,
    .trouble-procedure {
        display: none;
    }

    .checkbox input[type=checkbox],
    .checkbox-inline input[type=checkbox],
    .radio input[type=radio],
    .radio-inline input[type=radio] {
        position: unset;
    }

    .checkbox+.checkbox,
    .radio+.radio {
        margin-top: 10px;
    }
</style>
<?php echo form_open(
    $this->uri->uri_string(),
    array('id' => 'form-production_report', 'enctype' => 'multipart/form-data')
); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <?php if (!empty($id_delivery_records)) { ?>
                        <input type="hidden" name="id_delivery_records" value="<?= $id_delivery_records ?>">
                        <?php if (!empty($id_delivery_records_detail)) { ?>
                            <input type="hidden" name="id_delivery_records_detail" value="<?= $id_delivery_records_detail ?>">
                        <?php } ?>
                    <?php } ?>
                    <input type="hidden" name="id_internal_proposal" value="<?= (!empty($id_internal_proposal) ? $id_internal_proposal : NULL) ?>">
                    <input type="hidden" name="id_internal_proposal_process" value="<?= (!empty($id_internal_proposal_process) ? $id_internal_proposal_process : NULL) ?>">
                    <input type="hidden" name="id_internal_proposal_process_child" value="<?= (!empty($id_internal_proposal_process_child) ? $id_internal_proposal_process_child : NULL) ?>">
                    <input type="hidden" name="id_tasks_process" value="<?= (!empty($id_tasks_process) ? $id_tasks_process : NULL) ?>">
                    <input type="hidden" name="id_tasks_process_child" value="<?= (!empty($id_tasks_process_child) ? $id_tasks_process_child : NULL) ?>">
                    <input type="hidden" name="id_audit_item" value="<?= (!empty($id_audit_item) ? $id_audit_item : NULL) ?>">
                    <input type="hidden" name="audit_id" value="<?= (!empty($audit_id) ? $audit_id : NULL) ?>">
                    <input type="hidden" name="in_and_out_of_work_item" value="<?= (!empty($in_and_out_of_work_item) ? $in_and_out_of_work_item : NULL) ?>">
                    <input type="hidden" name="in_and_out_of_work" value="<?= (!empty($in_and_out_of_work) ? $in_and_out_of_work : NULL) ?>">

                    <input type="hidden" name="entrance_ticket_id" value="<?= (!empty($production_report) ? $production_report->entrance_ticket_id : ($_GET['entrance_ticket_id'] ?? 0)) ?>">
                    <input type="hidden" name="step" value="<?= (!empty($production_report) ? $production_report->step : ($_GET['step'] ?? 0)) ?>">

                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td style="width: 10%;"><label for="type_report">1.Loại</label></td>
                                    <td colspan="1">
                                        <div class="flex-center">
                                            <div class="radio radio-danger">
                                                <input type="radio" name="type_report" id="type_report_4" value="4" <?= empty($production_report) || $production_report->type_report == 4 ? 'checked="checked"' : '' ?>>
                                                <label for="type_report_4"><?= lang('Báo cáo vi phạm') ?></label>
                                            </div>
                                            <div class="radio radio-danger">
                                                <input type="radio" name="type_report" id="type_report_1" value="1" <?= isset($production_report->type_report) && $production_report->type_report == 1 ? 'checked="checked"' : '' ?>>
                                                <label for="type_report_1"><?= lang('Báo cáo không phù hợp') ?></label>
                                            </div>
                                            <div class="radio radio-danger mleft5" style="margin-top: 7px;">
                                                <input type="radio" name="type_report" id="type_report_2" value="2" <?= isset($production_report->type_report) && $production_report->type_report == 2 ? 'checked="checked"' : '' ?>>
                                                <label for="type_report_2"><?= lang('Báo cáo vượt') ?></label>
                                            </div>
                                            <div class="radio radio-danger mleft5" style="margin-top: 7px;">
                                                <input type="radio" name="type_report" id="type_report_3" value="3" <?= isset($production_report->type_report) && $production_report->type_report == 3 ? 'checked="checked"' : '' ?>>
                                                <label for="type_report_3"><?= lang('Báo cáo cải tiến') ?></label>
                                            </div>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="big_risk" name="big_risk" value="1" <?= !empty($production_report->big_risk) ? 'checked' : '' ?>>
                                            <label for="big_risk">Rủi ro lớn</label>
                                        </div>
                                    </td>
                                    <td>11.Số Phiếu</td>
                                    <td>
                                        <input type="text" name="reference_no" id="reference_no" class="form-control reference_no" placeholder="<?= lang('auto') ?>" readonly value="<?= $production_report->reference_no ?? '' ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 15%;">
                                        <label for="name">2.Tên Phiếu</label>
                                    </td>
                                    <td style="width: 35%;">
                                        <?php $value = !empty($production_report) ? $production_report->name_report : lang('Báo cáo vi phạm') ?>
                                        <?= form_input(
                                            'name_report',
                                            $value,
                                            'id="name_report" class="form-control" placeholder="' . lang('Tên phiếu') . '" required '
                                        ) ?>
                                    </td>
                                    <td>
                                        <div style="display: flex;">
                                            <?= lang('12.Chi Nhánh', 'id_branch') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php $value = !empty($production_report) ? $production_report->id_branch : (!empty($data_delivery_records->id_branch) ? $data_delivery_records->id_branch : '') ?>
                                        <?= render_select(
                                            'id_branch',
                                            (!empty($branch) ? $branch : []),
                                            ['id', 'name'],
                                            '',
                                            $value
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 15%;"><label for="date">3.Ngày</label></td>
                                    <td style="width: 35%;">
                                        <?php $value = !empty($production_report) ? _dt_new($production_report->date) : '' ?>
                                        <?= form_input(
                                            'date',
                                            !empty($value) ? $value : date('Y/m/d H:i:s'),
                                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                                        ) ?>
                                    </td>
                                    <td><?= lang('13.Khối-Phòng', 'id_departments') ?></td>
                                    <td>
                                        <?php $value = !empty($production_report) ? $production_report->id_departments : $department_audit_id ?>
                                        <?= render_select(
                                            'id_departments',
                                            (!empty($departments) ? $departments : []),
                                            ['id', 'name'],
                                            '',
                                            $value
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= lang('4.Liên Quan Đến', 'recommended_list_group_id') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $dtRecommendedListG = $this->recommended_list_model->getRelateParent([0], 1);
                                        // $dtRecommendedListG = $this->recommended_list_model->getRelate();
                                        ?>
                                        <div class="form-group">
                                            <select name="recommended_list_group_id" id="recommended_list_group_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Liên quan đến') ?>" class="selectpicker">
                                                <option value=""></option>
                                                <?php if (!empty($dtRecommendedListG)) : ?>
                                                    <?php foreach ($dtRecommendedListG as $key => $value) : ?>
                                                        <option <?= ((!empty($production_report->recommended_list_group_id) && $production_report->recommended_list_group_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td style="width: 15%;">
                                        <label for="category_tasks">14.Mã Công Việc-Vị Trí Chức Vụ</label>
                                    </td>
                                    <td style="width: 35%;">
                                        <?php if (!empty($data_tasks)) { ?>
                                            <input type="hidden" name="category_tasks" id="category_tasks" value="<?= $data_tasks->category_tasks ?>">
                                            <input type="hidden" name="id_tasks" id="id_tasks" value="<?= $data_tasks->id ?>">
                                            <div><b><?= $data_tasks->code_category_tasks ?></b></div>
                                            <div><i><?= $data_tasks->content_category_tasks ?></i></div>
                                            <div id="staff_responsible_container">
                                                <label for="staff_responsible">Nhân viên</label>
                                                <?php $valuestaff_responsible = !empty($production_report) ? $production_report->staff_responsible : '' ?>
                                                <select id="staff_responsible" name="staff_responsible" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                    <option></option>
                                                    <?php if (!empty($staff)) {
                                                        foreach ($staff as $key => $value) { ?>
                                                            <option <?= ($valuestaff_responsible == $value['staffid']) ? 'selected' : '' ?> data-role="<?= $value['role'] ?>" data-subtext="<?= $value['code'] ?>" value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                                <!-- <?= render_select('staff_responsible', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname'], 'code'], '', $value) ?> -->
                                            </div>
                                            <label for="violation_group">Nhóm vi phạm</label>
                                            <?php $value = !empty($production_report) ? $production_report->violation_group : ''; ?>
                                            <?= render_select(
                                                'violation_group',
                                                (!empty($dtViolationGroup) ? $dtViolationGroup : []),
                                                ['id', ['code', 'name'], 'detail'],
                                                '',
                                                $value
                                            ) ?>
                    </div>
                <?php } else { ?>
                    <!-- <a class="mbot10" data-toggle="collapse" data-target="#search_role">Lọc mã công việc theo chức vụ</a> -->

                    <?php $selectedVal = !empty($production_report) ? $production_report->role_id : '' ?>
                    <label for="role_id">Lọc mã công việc theo chức vụ</label>
                    <div id="search_role" class="collapse in form-group">
                        <div class="mbot20">
                            <select id="role_id" name="role_id" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                <option></option>
                                <?php if (!empty($data_roles)) {
                                                foreach ($data_roles as $key => $value) { ?>
                                        <option <?= ($selectedVal == $value['roleid']) ? 'selected' : '' ?> value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                <?php }
                                            } ?>
                            </select>
                        </div>
                    </div>
                    <div id="staff_responsible_container">
                        <label for="staff_responsible">Nhân viên</label>
                        <?php $valuestaff_responsible = !empty($production_report) ? $production_report->staff_responsible : '' ?>
                        <select id="staff_responsible" name="staff_responsible" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                            <option></option>
                            <?php if (!empty($staff)) {
                                                foreach ($staff as $key => $value) { ?>
                                    <option <?= ($valuestaff_responsible == $value['staffid']) ? 'selected' : '' ?> data-role="<?= $value['role'] ?>" data-subtext="<?= $value['code'] ?>" value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                            <?php }
                                            } ?>
                        </select>
                        <!-- <?= render_select('staff_responsible', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname'], 'code'], '', $value) ?> -->
                    </div>
                    <div class="wrap-category_tasks <?= !empty($production_report) ? ($production_report->type_report != 4 ? '' : 'hide') : 'hide' ?>">
                        <label for="category_tasks">Mã công việc</label>
                        <?php $value = !empty($production_report) ? $production_report->category_tasks : '' ?>
                        <?= render_select(
                                                'category_tasks',
                                                (!empty($category_tasks) ? $category_tasks : []),
                                                ['id', 'code', 'content'],
                                                '',
                                                $value
                                            ) ?>
                    </div>
                    <?php $value = !empty($production_report) ? $production_report->id_tasks : '' ?>
                    <input type="hidden" name="id_tasks" id="id_tasks" value="<?= $value ?>">

                    <div class="wrap-violation_group  <?= !empty($production_report) ? ($production_report->type_report == 4 ? '' : 'hide') : '' ?>">
                        <label for="violation_group">Nhóm vi phạm</label>
                        <?php $value = !empty($production_report) ? $production_report->violation_group : $violation_group_id; ?>
                        <?= render_select(
                                                'violation_group',
                                                (!empty($dtViolationGroup) ? $dtViolationGroup : []),
                                                ['id', ['code', 'name'], 'detail'],
                                                '',
                                                $value
                                            ) ?>
                    </div>
                <?php } ?>
                </td>
                </tr>
                <tr>
                    <td>
                        <?= lang('5.Chi Tiết Liên Quan', 'recommended_list_id') ?>
                    </td>
                    <td>
                        <?php
                        $dtRecommendedList = null;
                        if (!empty($production_report->recommended_list_group_id)) {
                            $dtRecommendedList = $this->recommended_list_model->getRelateParent([$production_report->recommended_list_group_id]);
                        }
                        ?>
                        <select name="recommended_list_id" id="recommended_list_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Chi tiết liên quan') ?>" class="selectpicker">
                            <option value=""></option>
                            <?php if (!empty($dtRecommendedList)) : ?>
                                <?php foreach ($dtRecommendedList as $key => $value) : ?>
                                    <option <?= ((!empty($production_report->recommended_list_id) && $production_report->recommended_list_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td>
                        <div style="display: flex;">
                            <?= lang('15.Số Lần Vi Phạm', 'violate') ?>
                        </div>
                    </td>
                    <td>
                        <div class="form-group mtop10">
                            <label class="hide">Điểm cộng KPI</label>
                            <input type="hidden" name="point_kpi" id="point_kpi" class="point_kpi form-control number-format" style="width: 100%;" value="<?= !empty($production_report) ? formatMoney($production_report->point_kpi) : 0 ?>" title="">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" name="violate" id="violate" <?= (empty($production_report) || $production_report->violate) == 1 ? 'checked' : '' ?> value="1">
                                <label for="violate"><?= lang('Vi phạm') ?></label>
                            </div>
                            <div id="countViolate_text" style="color:red;"></div>
                            <div class="show_kpi_department">
                                <label>Mục tiêu KPI phòng ban</label>
                                <select class="kpi_list_criteria_department form-control selectpicker" name="kpi_list_criteria_department" data-none-selected-text="Không có mục nào được chọn" id="kpi_list_criteria_department" data-live-search="true">
                                    <?php if (!empty($kpi_list_criteria_department)) { ?>
                                        <?php foreach ($kpi_list_criteria_department as $key => $value) { ?>
                                            <option <?= (!empty($production_report) && $production_report->kpi_list_criteria_department_id == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label>Chi tiết mục tiêu KPI phòng ban</label>
                                <select class="kpi_list_criteria_department_child form-control selectpicker" name="kpi_list_criteria_department_child" data-none-selected-text="Không có mục nào được chọn" id="kpi_list_criteria_department_child" data-live-search="true">
                                    <?php if (!empty($kpi_list_criteria_department_child)) { ?>
                                        <?php foreach ($kpi_list_criteria_department_child as $key => $value) { ?>
                                            <option <?= (!empty($production_report) && $production_report->kpi_list_criteria_department_id_child == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] . '-' . $value['id_child_kpi'] ?>"><?= $value['name'] . '-' . $value['evaluation_criteria'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label>Vi phạm</label>
                                <select class="kpi_list_criteria_department_violate form-control selectpicker" name="kpi_list_criteria_department_violate" data-none-selected-text="Không có mục nào được chọn" id="kpi_list_criteria_department_violate" data-live-search="true">
                                    <?php if (!empty($kpi_list_criteria_department_violate)) { ?>
                                        <?php foreach ($kpi_list_criteria_department_violate as $key => $value) { ?>
                                            <option <?= (!empty($production_report) && $production_report->kpi_list_criteria_department_violate == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <label>Chi phí thiệt hại</label>
                            <input type="text" name="damage_cost" id="damage_cost" class="damage_cost form-control number-format" style="width: 100%;" value="<?= !empty($production_report) ? formatMoney($production_report->damage_cost) : 0 ?>" title="">
                        </div>
                        <div class="form-group mtop10">
                            <div class="show_role_id">
                                <div class="form-group">
                                    <label>Mục tiêu vị trí theo (Mô tả công việc) JD</label>
                                    <input type="text" name="role_id_jd" id="role_id_jd" class="role_id_jd modal-select2"
                                        data-placeholder="<?= lang('Mã vị trí vi phạm') ?>" style="width: 100%;" value="<?= !empty($production_report) ? $production_report->role_id_jd : '' ?>"
                                        title="">
                                </div>
                                <div class="form-group">
                                    <label>Mô tả công việc JD</label>
                                    <select class="jd_tasks form-control selectpicker" name="jd_tasks" data-none-selected-text="Không có mục nào được chọn" id="jd_tasks" data-live-search="true">
                                        <?php if (!empty($jd_tasks)) { ?>
                                            <?php foreach ($jd_tasks as $key => $value) { ?>
                                                <option <?= (!empty($production_report) && $production_report->jd_tasks == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['code'] ?>-<?= $value['title'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Theo trách nhiệm</label>
                                    <select class="jd_responsibility form-control selectpicker" multiple="true" name="jd_responsibility[]" data-none-selected-text="Không có mục nào được chọn" id="jd_responsibility" data-live-search="true">
                                        <?php if (!empty($responsibility)) { ?>
                                            <?php foreach ($responsibility as $key => $value) { ?>
                                                <option <?= (!empty($data_responsibility) && in_array($value['id'], $data_responsibility)) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Theo phạm vi quyền hạn</label>
                                    <select class="jd_jurisdiction form-control selectpicker" multiple="true" name="jd_jurisdiction[]" data-none-selected-text="Không có mục nào được chọn" id="jd_jurisdiction" data-live-search="true">
                                        <?php if (!empty($jurisdiction)) { ?>
                                            <?php foreach ($jurisdiction as $key => $value) { ?>
                                                <option <?= (!empty($data_jurisdiction) && in_array($value['id'], $data_jurisdiction)) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Theo yêu cầu công việc</label>
                                    <select class="jd_requirement form-control selectpicker" multiple="true" name="jd_requirement[]" data-none-selected-text="Không có mục nào được chọn" id="jd_requirement" data-live-search="true">
                                        <?php if (!empty($requirement)) { ?>
                                            <?php foreach ($requirement as $key => $value) { ?>
                                                <option <?= (!empty($data_requirement) && in_array($value['id'], $data_requirement)) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Theo tiêu chuẩn năng lực</label>
                                    <select class="jd_competency_standard form-control selectpicker" multiple="true" name="jd_competency_standard[]" data-none-selected-text="Không có mục nào được chọn" id="jd_competency_standard" data-live-search="true">
                                        <?php if (!empty($competency_standard)) { ?>
                                            <?php foreach ($competency_standard as $key => $value) { ?>
                                                <option <?= (!empty($data_competency_standard) && in_array($value['id'], $data_competency_standard)) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display: flex;">
                            <?= lang('6.Đơn Đặt Hàng', 'id_orders') ?>
                        </div>
                    </td>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->id_orders : '' ?>
                        <?php $value = !empty($default_value) ? $default_value->id_orders : '' ?>
                        <input type="text" name="id_orders" id="id_orders" class="id_orders" data-placeholder="<?= lang('Đơn đặt hàng') ?>" style="width: 100%;" value="<?= $value ?>" title="">
                    </td>
                    <td><?= lang('16.Công Đoạn Phát Hiện', 'production_stage') ?></td>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->production_stage : (!empty($id_stage) ? $id_stage : '') ?>
                        <?= render_select(
                            'production_stage',
                            (!empty($stages) ? $stages : []),
                            ['id', 'name', 'code'],
                            '',
                            $value
                        ) ?>
                    </td>

                </tr>
                <tr>
                    <td>
                        <div style="display: flex;">
                            <?= lang('7.Lệnh Sản Xuất', 'id_production_orders') ?>
                        </div>
                    </td>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->id_production_orders : (!empty($id_production_orders) ? $id_production_orders : '') ?>
                        <input type="text" name="id_production_orders" id="id_production_orders" class="id_production_orders" data-placeholder="<?= lang('Lệnh SX') ?>" style="width: 100%;" value="<?= $value ?>" title="">
                    </td>

                    <td><?= lang('17.Chi Tiết Công Việc', 'detail_tasks') ?></td>
                    <?php $value = !empty($production_report) ? $production_report->detail_tasks : (!empty($detail_tasks) ? $detail_tasks : '') ?>
                    <td>
                        <span><?= $value ?></span>
                        <input class="detail_tasks hide" id="detail_tasks" name="detail_tasks" value="<?= $value ?>">
                    </td>

                </tr>
                <tr>
                    <td rowspan="6">
                        <div style="display: flex;">
                            <?= lang('8.Sản Phẩm', 'it_items') ?>
                        </div>
                    </td>
                    <td rowspan="6">
                        <div class="name_product"><?= !empty($data_product->list_name_items) ? $data_product->list_name_items : '' ?></div>
                    </td>
                    <td><?= lang('18.Người Lập Báo Cáo', 'staff_handover') ?></td>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->staff_handover : get_staff_user_id() ?>
                        <?php
                        $staff_handover = [];
                        foreach ($staff as $key => $vv) {
                            if ($vv['staffid'] != 42 && $vv['staffid'] != 67) {
                                if (has_permission('production_report', $vv['staffid'], 'approve_lbc')) {
                                    $staff_handover[] = $vv;
                                }
                            }
                        }
                        ?>
                        <?= render_select(
                            'staff_handover',
                            (!empty($staff_handover) ? $staff_handover : []),
                            ['staffid', ['firstname', 'lastname'], 'code'],
                            '',
                            $value,
                            [],
                            [],
                            '',
                            '',
                            true
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('19.Người Chứng Nhận Xử Lý', 'staff_handler') ?></td>
                    <?php
                    $staff_handler = [];
                    foreach ($staff as $key => $vv) {
                        if ($vv['staffid'] != 42 && $vv['staffid'] != 67) {
                            if (has_permission('production_report', $vv['staffid'], 'approve_ncnxl')) {
                                $staff_handler[] = $vv;
                            }
                        }
                    }
                    ?>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->staff_handler : [] ?>
                        <?= render_select(
                            'staff_handler[]',
                            (!empty($staff_handler) ? $staff_handler : []),
                            ['staffid', ['firstname', 'lastname'], 'code'],
                            '',
                            $value,
                            ['multiple' => true],
                            [],
                            '',
                            '',
                            false
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('20.Người Giám Sát Phòng Ngừa', 'staff_assigned') ?></td>
                    <?php
                    $staff_assigned = [];
                    foreach ($staff as $key => $vv) {
                        if ($vv['staffid'] != 42 && $vv['staffid'] != 67) {
                            if (has_permission('production_report', $vv['staffid'], 'approve_gspn')) {
                                $staff_assigned[] = $vv;
                            }
                        }
                    }
                    ?>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->staff_assigned : [] ?>
                        <?= render_select(
                            'staff_assigned[]',
                            (!empty($staff_assigned) ? $staff_assigned : []),
                            ['staffid', ['firstname', 'lastname'], 'code'],
                            '',
                            $value,
                            ['multiple' => true],
                            [],
                            '',
                            '',
                            false
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('21.Người Đánh Giá', 'staff_evaluate') ?></td>
                    <?php
                    $staff_evaluate = [];
                    foreach ($staff as $key => $vv) {
                        if ($vv['staffid'] != 42 && $vv['staffid'] != 67) {
                            if (has_permission('production_report', $vv['staffid'], 'approve_dg')) {
                                $staff_evaluate[] = $vv;
                            }
                        }
                    }
                    ?>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->staff_evaluate : 0 ?>
                        <?= render_select(
                            'staff_evaluate',
                            (!empty($staff_evaluate) ? $staff_evaluate : 0),
                            ['staffid', ['firstname', 'lastname'], 'code'],
                            '',
                            $value,
                            [],
                            [],
                            '',
                            '',
                            true
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display: flex;" class="show_kpi hide">
                            <?= lang('22.Nhóm KPI', 'quantity_kpi') ?>
                        </div>
                    </td>
                    <td>
                        <div class="show_kpi hide">
                            <?php $value = !empty($production_report) ? $production_report->quantity_kpi : 0 ?>
                            <select class="category_kpi_criteria_id form-control selectpicker" name="category_kpi_criteria_id[]" data-none-selected-text="Không có mục nào được chọn" id="category_kpi_criteria_id" multiple data-live-search="true">
                                <?php if (!empty($production_report)) { ?>
                                    <?php foreach ($dtcategoryKpi as $key => $value) { ?>
                                        <option <?= (!empty($arrSelectKpi) && in_array(
                                                    $value['id'],
                                                    $arrSelectKpi
                                                ) ? 'selected' : '') ?> data-subtext="<?= $value['type'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                            -<?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <td class="hide">
                        <div style="display: flex;">
                            <?= lang('Số lượng', 'quantity_pcs') ?>
                        </div>
                    </td>
                    <td class="hide">
                        <?php $value = !empty($production_report) ? $production_report->quantity_pcs : '' ?>
                        <?= form_input(
                            'quantity_pcs',
                            $value,
                            'id="quantity_pcs" class="form-control" placeholder="' . lang('Số lượng') . '"'
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td>23.Máy Móc Thiết Bị</td>
                    <td>
                        <input type="text" name="machines_id" id="machines_id" class="machines_id"
                            data-placeholder="<?= lang('Thiết bị') ?>" style="width: 100%;"
                            value="<?= !empty($production_report) ? $production_report->machines_id : 0 ?>"
                            title="">
                        <label for="downtime">Thời gian nghỉ máy</label>
                        <input type="text" name="downtime" id="downtime" class="form-control downtime number-format"
                            value="<?= !empty($production_report) ? $production_report->downtime : 0 ?>"
                            title="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display: flex;">
                            <?= lang('9.Sự Cố', 'id_trouble') ?>
                        </div>
                    </td>
                    <td>
                        <?php $value = !empty($production_report) ? $production_report->id_trouble : '' ?>
                        <?= render_select(
                            'id_trouble',
                            (!empty($trouble) ? $trouble : []),
                            ['id', 'code', 'name'],
                            '',
                            $value
                        ) ?>
                        <div id="countTrouble_text" style="color:red;"></div>
                        <div id="violation_point"></div>

                        <div class="wrap_salary_3p hide">
                            <label for="salary_3p">Khung lương 3P</label>
                            <input type="text" name="salary_3p" id="salary_3p" class="salary_3p" style="width:100%"
                                value="<?= !empty($production_report) ? $production_report->salary_3p : 0 ?>"
                                title="">
                        </div>
                    </td>
                    <td rowspan="4">
                        <div class="form-group">
                            <label for="departments"></label>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" id="type_stage_1" name="type_stage_1" value="1" <?= !empty($production_report->type_stage_1) ? 'checked' : '' ?>>
                                <label for="type_stage_1">Chạy mẫu</label>
                            </div>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" id="type_stage_2" name="type_stage_2" value="1" <?= !empty($production_report->type_stage_2) ? 'checked' : '' ?>>
                                <label for="type_stage_2">Chạy hàng + Mẫu</label>
                            </div>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" id="type_stage_3" name="type_stage_3" value="1" <?= !empty($production_report->type_stage_3) ? 'checked' : '' ?>>
                                <label for="type_stage_3">Chạy hàng</label>
                            </div>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" id="type_stage_4" name="type_stage_4" value="1" <?= !empty($production_report->type_stage_4) ? 'checked' : '' ?>>
                                <label for="type_stage_4">Chạy bù hàng</label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display: flex;">
                            <?= lang('10.Quản Lý Chịu Trách Nhiệm', 'staff_responsible') ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        $value = !empty($production_report) ? $production_report->responsible_type : '';
                        $staffChecked = 'checked';
                        $departmentChecked = '';
                        if ($value == 'staff') {
                            $staffChecked = 'checked';
                        } elseif ($value == 'department') {
                            $departmentChecked = 'checked';
                        }
                        ?>
                        <div class="col-md-6 hide">
                            <input type="radio" id="responsible_type_staff" name="responsible_type" value="staff" <?= $staffChecked ?>>
                            <label for="responsible_type_staff"><?= _l('notify_assigned_user') ?></label>
                        </div>
                        <div class="col-md-6 hide" style="padding-left: 0 !important;">
                            <input type="radio" id="responsible_type_department" name="responsible_type" value="department" <?= $departmentChecked ?>>
                            <label for="responsible_type_department">BP chịu trách nhiệm</label>
                        </div>
                        <div id="staff_responsible_container">
                            <?php $value = !empty($production_report) ? $production_report->staff_manage : '' ?>
                            <?= render_select('staff_manage', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname'], 'code'], '', $value) ?>
                        </div>
                        <div id="department_responsible_container">
                            <?php $value = !empty($production_report) ? $production_report->department_responsible : '' ?>
                            <?= render_select(
                                'department_responsible',
                                (!empty($departments_bp) ? $departments_bp : []),
                                ['departmentid', 'name'],
                                '',
                                $value
                            ) ?>
                        </div>
                    </td>
                    <td class="hide"><?= lang('Nhà cung cấp', 'suppler_id') ?></td>
                    <td class="hide">
                        <?php $value = !empty($production_report) ? $production_report->suppler_id : '' ?>
                        <?= render_select(
                            'suppler_id',
                            (!empty($suppler) ? $suppler : ''),
                            ['id', 'company', 'code'],
                            '',
                            $value
                        ) ?>
                    </td>
                </tr>
                </tbody>
                </table>
                <table id="tb-productions-orders" class="table table-hover dataTable" style="width: 100%;margin-bottom: 30px!important;">
                    <thead>
                        <tr>
                            <th class="text-center">24.Nội Dung Không Phù Hợp</th>
                            <th class="text-center">25.Nguyên Nhân</th>
                            <th class="text-center">26.Quy Trình Xử Lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="code" class="control-label">Mô Tả Sự Không Phù Hợp</label>
                                    <div>
                                        <div class="dropzone dropzone-manual">
                                            <div id="dropzoneProduction" class="dropzoneDragArea dz-default dz-message task-comment-dropzone">
                                                <span><?php echo _l('drop_files_here_to_upload'); ?></span>
                                            </div>
                                            <div class="dropzone-task-comment-previews dropzone-previews dropzone-production"></div>
                                        </div>
                                        <?php
                                        if (!empty($production_report)) {
                                            $_list_files = $this->db->get_where('tblfiles', [
                                                'rel_type' => 'production_report',
                                                'rel_id' => $production_report->id
                                            ])->result_array();
                                        }
                                        if (!empty($_list_files)) { ?>
                                            <hr class="mtop5" />
                                            <div class="row">
                                                <?php
                                                foreach ($_list_files as $key => $value) { ?>
                                                    <div class="col-md-4 row-img-<?= $value['id'] ?>">
                                                        <img src="<?= base_url($value['file_name']) ?>" style="width: 100%;height: 150px;">
                                                        <div class="text-center">
                                                            <a class="text-danger" onclick="removeFile(<?= $value['id'] ?>)"><i class="fa fa-remove"></i></a>
                                                        </div>
                                                    </div>
                                                <?php }
                                                ?>
                                            </div>
                                        <?php } ?>
                                        <div class="img-remove"></div>
                                        <!--                                                <input type="file" id="fileInput" name="image" accept="image/*">-->
                                    </div>
                                    <div class="hide">
                                        <?php $value = !empty($production_report->described) ? $production_report->described : (!empty($described) ? $described : '') ?>
                                        <textarea readonly name="described" id="described" class="form-control" rows="3"><?= $value ?></textarea>
                                        <?php $value = !empty($production_report->time_of_recording) ? _dt($production_report->time_of_recording) : _dt(date('Y-m-d H:i:s')) ?>
                                        <input type="hidden" id="time_of_recording" name="time_of_recording" class="form-control datetimepicker" value="<?= $value ?>">
                                    </div>
                                </div>
                            </td>
                            <td rowspan="1">
                                <?php
                                $dtReason = $production_report->id ?? null ? $this->recommended_list_model->getProductionReportReason($production_report->id, 'trouble') : null;
                                ?>
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="trouble_material" name="trouble[material]" class="trouble-checkbox" data-id="material" value="1" <?= !empty($production_report->material_checked) ? 'checked' : '' ?>>
                                        <label for="trouble_material">Nguyên Phụ Liệu</label>
                                    </div>
                                    <div class="mbot10 div-reason-material" style="display: none;">
                                        <textarea name="reason[material]" placeholder="<?= lang('Nguyên nhân nguyên phụ liệu') ?>" id="reason_material" class="form-control" rows="3"><?= $dtReason['material']['reason'] ?? '' ?></textarea>
                                    </div>
                                    <div class="ui-sortable trouble-material">
                                        <h3 class="bold chk-heading th font-medium font-medium-12"><u>Nguyên Phụ Liệu</u>
                                            <a href="#" onclick="add_trouble('material'); return false" class="mbot10 inline-block">
                                                <span class="new-checklist-item"><i class="fa fa-plus-circle"></i>
                                                    <?php echo _l('Thêm'); ?>
                                                </span>
                                            </a>
                                        </h3>
                                        <div class="div_material">
                                            <?php if (!empty($production_report->material)) { ?>
                                                <?php foreach ($production_report->material as $key => $value) { ?>
                                                    <div>
                                                        <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                            <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                <input type="hidden" name="trouble_item_id[material][<?= $key ?>]" value="1">
                                                                <input type="checkbox" name="checked[material][<?= $key ?>]" value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                                                <label for=""><span class="hide"><?= $value['name'] ?></span></label>
                                                                <textarea class="text_checklist" name="items[material][<?= $key ?>]" rows="1" style="height: 28px;"><?= $value['name'] ?></textarea>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="id_list[material][<?= $key ?>]" value="<?= $value['id'] ?>">
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="trouble_man" name="trouble[man]" class="trouble-checkbox" data-id="man" value="1" <?= !empty($production_report->man_checked) ? 'checked' : '' ?>>
                                        <label for="trouble_man">Nhân Lực</label>
                                    </div>
                                    <div class="mbot10 div-reason-man" style="display: none;">
                                        <textarea name="reason[man]" placeholder="<?= lang('Nguyên nhân nhân lực') ?>" id="reason_man" class="form-control" rows="3"><?= $dtReason['man']['reason'] ?? '' ?></textarea>
                                    </div>
                                    <div class="ui-sortable trouble-man">
                                        <h3 class="bold chk-heading th font-medium font-medium-12"><u>Nhân Lực</u>
                                            <a href="#" onclick="add_trouble('man'); return false" class="mbot10 inline-block">
                                                <span class="new-checklist-item"><i class="fa fa-plus-circle"></i>
                                                    <?php echo _l('Thêm'); ?>
                                                </span>
                                            </a>
                                        </h3>
                                        <div class="div_man">
                                            <?php if (!empty($production_report->man)) { ?>
                                                <?php foreach ($production_report->man as $key => $value) { ?>
                                                    <div>
                                                        <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                            <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                <input type="hidden" name="trouble_item_id[man][<?= $key ?>]" value="1">
                                                                <input type="checkbox" name="checked[man][<?= $key ?>]" value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                                                <label for=""><span class="hide"><?= $value['name'] ?></span></label>
                                                                <textarea class="text_checklist" name="items[man][<?= $key ?>]" rows="1" style="height: 28px;"><?= $value['name'] ?></textarea>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="id_list[man][<?= $key ?>]" value="<?= $value['id'] ?>">
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="trouble_machine" name="trouble[machine]" class="trouble-checkbox" data-id="machine" value="1" <?= !empty($production_report->machine_checked) ? 'checked' : '' ?>>
                                        <label for="trouble_machine">Máy Móc</label>
                                    </div>
                                    <div class="mbot10 div-reason-machine" style="display: none;">
                                        <textarea name="reason[machine]" placeholder="<?= lang('Nguyên nhân máy móc') ?>" id="reason_machine" class="form-control" rows="3"><?= $dtReason['machine']['reason'] ?? '' ?></textarea>
                                    </div>
                                    <div class="ui-sortable trouble-machine">
                                        <h3 class="bold chk-heading th font-medium font-medium-12"><u>Máy Móc</u>
                                            <a href="#" onclick="add_trouble('machine'); return false" class="mbot10 inline-block">
                                                <span class="new-checklist-item"><i class="fa fa-plus-circle"></i>
                                                    <?php echo _l('Thêm'); ?>
                                                </span>
                                            </a>
                                        </h3>
                                        <div class="div_machine">
                                            <?php if (!empty($production_report->machine)) { ?>
                                                <?php foreach ($production_report->machine as $key => $value) { ?>
                                                    <div>
                                                        <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                            <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                <input type="hidden" name="trouble_item_id[machine][<?= $key ?>]" value="1">
                                                                <input type="checkbox" name="checked[machine][<?= $key ?>]" value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                                                <label for=""><span class="hide"><?= $value['name'] ?></span></label>
                                                                <textarea class="text_checklist" name="items[machine][<?= $key ?>]" rows="1" style="height: 28px;"><?= $value['name'] ?></textarea>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="id_list[machine][<?= $key ?>]" value="<?= $value['id'] ?>">
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="trouble_method" name="trouble[method]" class="trouble-checkbox" data-id="method" value="1" <?= !empty($production_report->method_checked) ? 'checked' : '' ?>>
                                        <label for="trouble_method">Phương Pháp</label>
                                    </div>
                                    <div class="mbot10 div-reason-method" style="display: none;">
                                        <textarea name="reason[method]" placeholder="<?= lang('Nguyên nhân phương pháp') ?>" id="reason_method" class="form-control" rows="3"><?= $dtReason['method']['reason'] ?? '' ?></textarea>
                                    </div>
                                    <div class="ui-sortable trouble-method">
                                        <h3 class="bold chk-heading th font-medium font-medium-12"><u>Phương Pháp</u>
                                            <a href="#" onclick="add_trouble('method'); return false" class="mbot10 inline-block">
                                                <span class="new-checklist-item"><i class="fa fa-plus-circle"></i>
                                                    <?php echo _l('Thêm'); ?>
                                                </span>
                                            </a>
                                        </h3>
                                        <div class="div_method">
                                            <?php if (!empty($production_report->method)) { ?>
                                                <?php foreach ($production_report->method as $key => $value) { ?>
                                                    <div>
                                                        <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                            <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                <input type="hidden" name="trouble_item_id[method][<?= $key ?>]" value="1">
                                                                <input type="checkbox" name="checked[method][<?= $key ?>]" value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                                                <label for=""><span class="hide"><?= $value['name'] ?></span></label>
                                                                <textarea class="text_checklist" name="items[method][<?= $key ?>]" rows="1" style="height: 28px;"><?= $value['name'] ?></textarea>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="id_list[method][<?= $key ?>]" value="<?= $value['id'] ?>">
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="trouble_environment" name="trouble[environment]" class="trouble-checkbox" data-id="environment" value="1" <?= !empty($production_report->environment_checked) ? 'checked' : '' ?>>
                                        <label for="trouble_environment">Môi Trường</label>
                                    </div>
                                    <div class="mbot10 div-reason-environment" style="display: none;">
                                        <textarea name="reason[environment]" placeholder="<?= lang('Nguyên nhân môi trường') ?>" id="reason_environment" class="form-control" rows="3"><?= $dtReason['environment']['reason'] ?? '' ?></textarea>
                                    </div>
                                    <div class="ui-sortable trouble-environment">
                                        <h3 class="bold chk-heading th font-medium font-medium-12"><u>Môi Trường</u>
                                            <a href="#" onclick="add_trouble('environment'); return false" class="mbot10 inline-block">
                                                <span class="new-checklist-item"><i class="fa fa-plus-circle"></i>
                                                    <?php echo _l('Thêm'); ?>
                                                </span>
                                            </a>
                                        </h3>
                                        <div class="div_environment">
                                            <?php if (!empty($production_report->environment)) { ?>
                                                <?php foreach ($production_report->environment as $key => $value) { ?>
                                                    <div>
                                                        <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                            <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                <input type="hidden" name="trouble_item_id[environment][<?= $key ?>]" value="1">
                                                                <input type="checkbox" name="checked[environment][<?= $key ?>]" value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                                                <label for=""><span class="hide"><?= $value['name'] ?></span></label>
                                                                <textarea class="text_checklist" name="items[environment][<?= $key ?>]" rows="1" style="height: 28px;"><?= $value['name'] ?></textarea>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="id_list[environment][<?= $key ?>]" value="<?= $value['id'] ?>">
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td rowspan="1" style="vertical-align: top !important;">
                                <div class="div_procedure">
                                    <?php if (!empty($production_report->procedure)) { ?>
                                        <?php foreach ($production_report->procedure as $key => $value) { ?>
                                            <div>
                                                <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                    <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                        <input type="hidden" name="trouble_item_id[procedure][<?= $key ?>]" value="1">
                                                        <input type="checkbox" name="checked[procedure][<?= $key ?>]" value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                                        <label for=""><span class="hide"><?= $value['name'] ?></span></label>
                                                        <textarea class="text_checklist" name="items[procedure][<?= $key ?>]" rows="1" style="height: 28px;"><?= $value['name'] ?></textarea>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="id_list[procedure][<?= $key ?>]" value="<?= $value['id'] ?>">
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="code" class="control-label">27.Số Lượng</label>
                                    <?php $value = !empty($production_report->quantity) ? $production_report->quantity : (!empty($quantity_err) ? $quantity_err : '') ?>
                                    <input type="text" id="quantity" name="quantity" class="form-control" value="<?= $value ?>">
                                </div>
                            </td>
                            <td rowspan="2">
                                <div style="margin-bottom: 10px">28.Quyết Định</div>
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="action_now_1" name="action_now_1" value="1" <?= !empty($production_report->action_now_1) ? 'checked' : '' ?>>
                                        <label for="action_now_1">Chấp Nhận </label>
                                    </div>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="action_now_2" name="action_now_2" value="1" <?= !empty($production_report->action_now_2) ? 'checked' : '' ?>>
                                        <label for="action_now_2">Loại Bỏ</label>
                                    </div>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="action_now_3" name="action_now_3" value="1" <?= !empty($production_report->action_now_3) ? 'checked' : '' ?>>
                                        <label for="action_now_3">Làm Lại</label>
                                    </div>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="action_now_4" name="action_now_4" value="1" <?= !empty($production_report->action_now_4) ? 'checked' : '' ?>>
                                        <label for="action_now_4">Khác</label>
                                    </div>
                                </div>
                            </td>
                            <td rowspan="2">
                                <div class="ui-sortable">
                                    <div>29.Quy Trình Phòng Ngừa</div>
                                    <div class="div_fix">
                                        <?php if (!empty($production_report->fix)) { ?>
                                            <?php foreach ($production_report->fix as $key => $value) { ?>
                                                <div>
                                                    <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                        <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                            <input type="hidden" name="trouble_item_id[fix][<?= $key ?>]" value="1">
                                                            <input type="checkbox" name="checked[fix][<?= $key ?>]" value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                                            <label for=""><span class="hide"><?= $value['name'] ?></span></label>
                                                            <textarea class="text_checklist" name="items[fix][<?= $key ?>]" rows="1" style="height: 28px;"><?= $value['name'] ?></textarea>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="id_list[fix][<?= $key ?>]" value="<?= $value['id'] ?>">
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <div class="mtop10">
                                        <textarea name="note_fix" id="note_fix" placeholder="<?= lang('Khắc phục') ?>" class="form-control note_fix" rows="3"><?= !empty($production_report->note_fix) ? $production_report->note_fix : '' ?></textarea>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="code" class="control-label">30.Ghi Chú</label>
                                    <?php $value = !empty($production_report->note) ? $production_report->note : (!empty($is_note) ? $is_note : '')  ?>
                                    <textarea name="note" id="note" class="form-control" rows="3"><?= $value ?></textarea>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="hide">
        <input class="hide" name="category_recommended_id" value="<?= $category_recommended_id ?>">
        <input class="hide" name="suggest_id" value="<?= $suggest_id ?? null ?>">
        <input class="hide" name="suggest_id_detail" value="<?= $suggest_id_detail ?? null ?>">
        <input class="hide" name="object_type" value="<?= $object_type ?? null ?>">
        <input class="hide" name="object_id" value="<?= $object_id ?? null ?>">
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
<input type="hidden" name="id_quotes" id="id_quotes" value="<?= !empty($production_report) ? $production_report->id_quotes : (!empty($_GET['id_quotes']) ? $_GET['id_quotes'] : 0) ?>">
<?php echo form_close(); ?>
<?php init_tail(); ?>

<script type="text/javascript">
    Dropzone.options.expenseForm = false;
    Dropzone.autoDiscover = false;
    var expenseDropzone;

    appValidateForm($('#form-production_report'), {
        role_id: 'required',
        date: 'required',
        name: 'required',
        // category_tasks: 'required',
        // id_trouble: 'required',
        id_branch: 'required',
        recommended_list_group_id: 'required',
        // id_production_detail: 'required',
        production_stage: 'required',
        staff_handover: 'required',
        'staff_handler[]': 'required',
        'staff_assigned[]': 'required',
        staff_evaluate: 'required'
    }, ManageProductionReport);

    // function ManageProductionReport(form) {
    //     $('.add').attr('disabled', 'disabled');
    //     var data = $(form).serialize();
    //     var url = form.action;
    //     $.ajax({
    //         url: url,
    //         type: 'POST',
    //         dataType: 'JSON',
    //         data: data,
    //     }).done(function(data) {
    //         alert_float(data.alert_type, data.message);
    //         if (data.success) {
    //             if(data.idtask) {
    //                 init_task_modal(data.idtask);
    //                 $("#task-modal").addClass('show_add');
    //             }
    //             else {
    //                 window.location = admin_url + 'production_report';
    //             }
    //         }
    //         else {
    //             $(form).find('button[type="submit"]').removeAttr('disabled');
    //         }
    //     })
    //     .fail(function(err) {
    //         alert_float('danger', err.responseText);
    //         $(form).find('button[type="submit"]').removeAttr('disabled');
    //     });
    //     return false;
    // }

    function ManageProductionReport(form) {
        $(form).find('button[type="submit"]').attr('disabled', 'disabled');
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        $.each(expenseDropzone.files, function(index, value) {
            formData.append('image[]', value);
        })
        var url = form.action;
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                alert_float(data.alert_type, data.message);
                if (data.success) {
                    if (data.idtask) {
                        init_task_modal(data.idtask);
                        $("#task-modal").addClass('show_add');
                    } else {
                        window.location = admin_url + 'production_report';
                    }
                } else {
                    $(form).find('button[type="submit"]').removeAttr('disabled');
                }
            })
            .fail(function() {
                alert_float('danger', err.responseText);
                $(form).find('button[type="submit"]').removeAttr('disabled');
            });
        return false;
    }

    // init_editor('textarea[name="constitutive"]');
    // init_editor('textarea[name="note"]');
    // })

    // ajaxSelect2Not('#id_production_detail', admin_url + 'production_report/search_production_detail', $('#id_production_detail').val());
    ajaxSelect2Not('#id_production_orders', admin_url + 'production_report/search_production_orders', $('#id_production_orders').val());

    ajaxSelect2Not('#id_orders', admin_url + 'production_report/search_orders', $('#id_orders').val());

    // $('body').on('change', '#id_production_detail', function (data) {
    //     data_production_detail = data.added;
    //     $('.name_product').text(data_production_detail.items_name);
    //     $('#production_stage').html('<option></option>');
    //     $.get(admin_url + 'production_report/get_stage/' + data_production_detail.id_orders_item, function(data) {
    //         data = JSON.parse(data);
    //         $.each(data, function(index, value) {
    //             $('#production_stage').append(`<option value="${value.id}">${value.name}</option>`);
    //         })
    //         $('#production_stage').selectpicker('refresh');
    //     })
    // })

    $('body').on('change', '#id_production_orders', function(data) {
        data_production_orders = data.added;
        $('.name_product').html(data_production_orders.list_items_name);
    })

    function ajaxSelect2Not(element, url, id, types = '', dataGet = '') {
        if (id != "") {
            var DataSelect = {
                width: 'resolve',
                placeholder: 'Vui lòng chọn',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + $(element).val() + (types ? ('/' + types) : '') + dataGet,
                        dataType: "json",
                        success: function(data) {
                            callback(data.results);
                        }
                    });
                },
                ajax: {
                    url: url + dataGet,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoDontFormat,
                formatSelection: repoDontFormat,
                escapeMarkup: function(m) {
                    return m;
                }
            };


            $(element).val(id).select2(DataSelect);
        } else {
            var DataSelect = {
                width: 'resolve',
                placeholder: 'Vui lòng chọn',
                allowClear: true,
                ajax: {
                    url: url + dataGet,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoDontFormat,
                formatSelection: repoDontFormat,
                escapeMarkup: function(m) {
                    return m;
                }
            };


            $(element).select2(DataSelect);
        }
    }

    function repoDontFormat(state) {
        if (!state.id) return state.code;
        var name = '<div><b>' + state.code + '</b></div>';
        if (state.items_code) {
            name += '<div><b>Mã SP:</b> <i>' + state.items_code + '</i></div>';
        }
        if (state.items_name) {
            name += '<div><b>Tên SP:</b><i>' + state.items_name + '</i></div>';
        }
        if (state.list_items_name) {
            name += '<div><b>Sản Phẩm:</b><br/> <i>' + state.list_items_name + '</i></div>';
        }
        if (state.customer_name) {
            name += '<div><b>Khách hàng :</b> <i>' + state.customer_name + '</i></div>';
        }
        return name;
    }

    $('#id_trouble').change(function() {
        var id_trouble = $(this).val();
        //referesh checked trouble
        $('.trouble-checkbox').prop('checked', false);
        if (id_trouble) {
            $.get(admin_url + 'trouble/get_trouble/' + id_trouble, function(result) {
                result = JSON.parse(result);
                $("#salary_3p").val(0)

                if (result.salary_p3 == 1) {
                    $(".wrap_salary_3p").removeClass('hide');
                } else {
                    $(".wrap_salary_3p").addClass('hide');
                }
                $("#described").val(result.name);
                $('.div_material').html('');
                if (result.material) {
                    $.each(result.material, function(index, value) {
                        $('.div_material').append(`<div>
                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                    <input type="hidden" name="trouble_item_id[material][${index}]" value="${value.id}">
                                    <input type="checkbox" name="checked[material][${index}]" value="1">
                                    <label for=""><span class="hide">${value.name}</span></label>
                                    <textarea class="text_checklist" name="items[material][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                </div>
                            </div>
                        </div>`);
                    });

                    if (result.material.length > 0) $('#trouble_material').prop('checked', true);
                }

                $('.div_man').html('');
                if (result?.man) {
                    $.each(result.man, function(index, value) {
                        $('.div_man').append(`<div>
                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                    <input type="hidden" name="trouble_item_id[man][${index}]" value="${value.id}">
                                    <input type="checkbox" name="checked[man][]" value="1">
                                    <label for=""><span class="hide">${value.name}</span></label>
                                    <textarea class="text_checklist" name="items[man][]" rows="1" style="height: 28px;">${value.name}</textarea>
                                </div>
                            </div>
                        </div>`);
                    });

                    if (result.man.length > 0) $('#trouble_man').prop('checked', true);
                }


                $('.div_machine').html('');
                if (result?.machine) {
                    $.each(result.machine, function(index, value) {
                        $('.div_machine').append(`<div>
                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                    <input type="hidden" name="trouble_item_id[machine][${index}]" value="${value.id}">
                                    <input type="checkbox" name="checked[machine][${index}]" value="1">
                                    <label for=""><span class="hide">${value.name}</span></label>
                                    <textarea class="text_checklist" name="items[machine][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                </div>
                            </div>
                        </div>`);
                    });

                    if (result.machine.length > 0) $('#trouble_machine').prop('checked', true);
                }

                $('.div_method').html('');
                if (result?.method) {
                    $.each(result.method, function(index, value) {
                        $('.div_method').append(`<div>
                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                    <input type="hidden" name="trouble_item_id[method][${index}]" value="${value.id}">
                                    <input type="checkbox" name="checked[method][]" value="1">
                                    <label for=""><span class="hide">${value.name}</span></label>
                                    <textarea class="text_checklist" name="items[method][]" rows="1" style="height: 28px;">${value.name}</textarea>
                                </div>
                            </div>
                        </div>`);
                    });

                    if (result.method.length > 0) $('#trouble_method').prop('checked', true);
                }

                $('.div_environment').html('');
                if (result?.environment) {
                    $.each(result.environment, function(index, value) {
                        $('.div_environment').append(`<div>
                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                    <input type="hidden" name="trouble_item_id[environment][${index}]" value="${value.id}">
                                    <input type="checkbox" name="checked[environment][]" value="1">
                                    <label for=""><span class="hide">${value.name}</span></label>
                                    <textarea class="text_checklist" name="items[environment][]" rows="1" style="height: 28px;">${value.name}</textarea>
                                </div>
                            </div>
                        </div>`);
                    });
                    if (result.environment.length) $('#trouble_environment').prop('checked', true);
                }

                $('.div_procedure').html('');
                if (result?.procedure) {
                    $.each(result.procedure, function(index, value) {
                        $('.div_procedure').append(`<div>
                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                    <input type="hidden" name="trouble_item_id[procedure][${index}]" value="${value.id}">
                                    <input type="checkbox" name="checked[procedure][${index}]" value="1" checked>
                                    <label for=""><span class="hide">${value.name}</span></label>
                                    <textarea class="text_checklist" name="items[procedure][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                </div>
                            </div>
                        </div>`);
                    });

                    if (result.procedure.length) $('#trouble_procedure').prop('checked', true);
                }

                $('.div_fix').html('');
                if (result?.fix) {
                    $.each(result.fix, function(index, value) {
                        $('.div_fix').append(`<div>
                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                    <input type="hidden" name="trouble_item_id[fix][${index}]" value="${value.id}">
                                    <input type="checkbox" name="checked[fix][${index}]" value="1" checked>
                                    <label for=""><span class="hide">${value.name}</span></label>
                                    <textarea class="text_checklist" name="items[fix][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                </div>
                            </div>
                        </div>`);
                    });

                    if (result.fix.length) $('#trouble_fix').prop('checked', true);
                }

                if (result.violation_name != null) {
                    var violation_color = '#0d6efd';
                    if (result.violation_name == 'Nhắc nhở') {
                        violation_color = '#ffc107';
                    } else if (result.violation_name == 'Khiển trách') {
                        violation_color = '#fd7e14';
                    } else if (result.violation_name == 'Cảnh báo') {
                        violation_color = '#fd7e14';
                    }

                    $('#violation_point').html('<span class="inline-block label mleft5 mtop5" style="font-size: 12px;color: ' + violation_color + ';border:1px solid ' + violation_color + '">' + result.violation_name + ' (trừ ' + result.violation_point + ' điểm)</span>');
                } else {
                    $('#violation_point').html('');
                }

                eachTroubleCheckbox();
            })
        } else {
            eachTroubleCheckbox();
        }
        if (id_trouble) {
            var date = $('#date').val();
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['id'] = id_trouble;
            data['date'] = date;
            $.post(admin_url + 'trouble/countTrouble', data, function(data) {
                $('#countTrouble').val(data);
                if (data > 0) {
                    $('#countTrouble_text').html('<br>Số lần sự cố trong tháng: ' + data + ' lần');
                } else {
                    $('#countTrouble_text').html('');
                }
            })
        } else {
            $('#countTrouble').val(0);
            $('#countTrouble_text').html('');
        }
    })

    $("#task-modal").on('hidden.bs.modal', function(e) {
        if ($(this).hasClass('show_add')) {
            $("#task-modal").removeClass('show_add');
            window.location = admin_url + 'production_report';
        }
    });


    $('#id_departments').change(function() {
        var id_departments = $(this).val();
        // return;
        // $.get(admin_url + 'production_report/get_list_role/' + id_departments, function(data) {
        //     data = JSON.parse(data);
        //     $('#role_id').html(`<option></option>`);
        //     $.each(data, function(index, value) {
        //         $('#role_id').append(`<option value="${value.roleid}">${value.name}</option>`);
        //     })
        //     $('#role_id').selectpicker('refresh');
        //     $('#role_id').trigger('change');
        // })
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id_departments'] = id_departments;
        $('#kpi_list_criteria_department_violate').html(`<option></option>`);
        $('#kpi_list_criteria_department_violate').selectpicker('refresh');
        $.post(admin_url + 'production_report/get_list_category_tasks', data, function(data) {
            data = JSON.parse(data);
            category_tasks = data.category_tasks;
            $('#category_tasks').html(`<option></option>`);
            $.each(category_tasks, function(index, value) {
                $('#category_tasks').append(`<option value="${value.id}" data-subtext="${value.content}">${value.code}</option>`);
            })
            $('#category_tasks').selectpicker('refresh');

            kpi_list_criteria_department = data.kpi_list_criteria_department;
            $('#kpi_list_criteria_department').html(`<option></option>`);
            $.each(kpi_list_criteria_department, function(index, value) {
                $('#kpi_list_criteria_department').append(`<option value="${value.id}">${value.name}</option>`);
            })
            $('#kpi_list_criteria_department').selectpicker('refresh');
        })
    })

    $('#kpi_list_criteria_department').change(function() {
        var kpi_list_criteria_department_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['kpi_list_criteria_department_id'] = kpi_list_criteria_department_id;
        $('#kpi_list_criteria_department_violate').html(`<option></option>`);
        $('#kpi_list_criteria_department_violate').selectpicker('refresh');
        $.post(admin_url + 'production_report/get_list_kpi_list_criteria_department', data, function(data) {
            data = JSON.parse(data);
            kpi_list_criteria_department = data.kpi_list_criteria_department;
            $('#kpi_list_criteria_department_child').html(`<option></option>`);
            $.each(kpi_list_criteria_department, function(index, value) {
                $('#kpi_list_criteria_department_child').append(`<option value="${value.id}-${value.id_child_kpi}">${value.name} - ${value.evaluation_criteria}</option>`);
            })
            $('#kpi_list_criteria_department_child').selectpicker('refresh');
        })
    })

    $('#kpi_list_criteria_department_child').change(function() {
        var kpi_list_criteria_department_id_child = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['kpi_list_criteria_department_id_child'] = kpi_list_criteria_department_id_child;
        $.post(admin_url + 'production_report/kpi_list_criteria_department_violate', data, function(data) {
            data = JSON.parse(data);
            kpi_list_criteria_department_violate = data.kpi_list_criteria_department_violate;
            $('#kpi_list_criteria_department_violate').html(`<option></option>`);
            $.each(kpi_list_criteria_department_violate, function(index, value) {
                $('#kpi_list_criteria_department_violate').append(`<option value="${value.id}">${value.name}</option>`);
            })
            $('#kpi_list_criteria_department_violate').selectpicker('refresh');
        })
    })

    // $('#role_id').change(function () {
    //     var role_id = $('#role_id').val();
    //     var id_departments = $('#id_departments').val();
    //     var data = {};
    //     if (typeof (csrfData) !== 'undefined') {
    //         data[csrfData['token_name']] = csrfData['hash'];
    //     }
    //     data['role_id'] = role_id;
    //     data['id_departments'] = id_departments;
    //     $.post(admin_url + 'production_report/get_list_category_tasks', data, function (data) {
    //         data = JSON.parse(data);
    //         $('#category_tasks').html(`<option></option>`);
    //         $.each(data, function (index, value) {
    //             $('#category_tasks').append(`<option value="${value.id}" data-subtext="${value.content}">${value.code}</option>`);
    //         })
    //         $('#category_tasks').selectpicker('refresh');
    //     })
    // })

    // $('#role_id').change(function() {
    //     var role_id = $('#role_id').val();
    //     var id_departments = $('#id_departments').val();
    //     var data = {};
    //     if (typeof(csrfData) !== 'undefined') {
    //         data[csrfData['token_name']] = csrfData['hash'];
    //     }
    //     data['role_id'] = role_id;
    //     data['id_departments'] = id_departments;
    //     $.post(admin_url + 'production_report/getStaff', data, function(data) {
    //         data = JSON.parse(data);
    //         $('#staff_responsible').html(`<option></option>`);
    //         $.each(data, function(index, value) {
    //             $('#staff_responsible').append(`<option value="${value.staffid}" >${value.fullname}</option>`);
    //         })
    //         $('#staff_responsible').selectpicker('refresh');
    //     })
    // })
    $('#staff_responsible').change(function() {
        var role = $('#staff_responsible').find('option:selected').attr('data-role');
        $('#role_id').val(role).change();
    })
    $('#recommended_list_group_id').change(function() {
        var recommended_list_group_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_group_id;

        $.post(admin_url + 'production_report/getRecommendedListByParent', data, function(data) {
            data = JSON.parse(data);
            $('#recommended_list_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#recommended_list_id').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
            })
            $('#recommended_list_id').selectpicker('refresh');
        });
    });

    changeResponsibleType();
    $("[name='responsible_type']").on("change", function() {
        changeResponsibleType();
    });

    function changeResponsibleType() {
        var value = $("input[name='responsible_type']:checked").val();
        if (value == "staff") {
            $("#department_responsible_container").addClass("hide");
            // $("#department_responsible").prop("disabled", true);
            // $("#department_responsible").prop("required", false);
            $('#department_responsible').val('default').selectpicker('deselectAll');
            $('#department_responsible').selectpicker('refresh');

            // $("#staff_responsible").prop("disabled", false);
            $("#staff_responsible_container").removeClass("hide");
            // $("#staff_responsible").prop("required", true);
        } else if (value == "department") {
            $("#staff_responsible_container").addClass("hide");
            // $("#staff_responsible").prop("disabled", true);
            // $("#staff_responsible").prop("required", false);
            $('#staff_responsible').val('default').selectpicker('deselectAll');
            $('#staff_responsible').selectpicker('refresh');

            // $("#department_responsible").prop("disabled", false);
            $("#department_responsible_container").removeClass("hide");
            // $("#department_responsible").prop("required", true);
        }
        console.log("Giá trị mới: " + value);
    };

    var opt = {
        format: 'Y/m/d H:i:s',
        timepicker: true,
        scrollInput: false,
        lazyInit: true,
    };
    $('#date').datetimepicker(opt);

    function eachTroubleCheckbox() {
        $('.trouble-checkbox').each(function() {
            var checkboxId = $(this).data('id');
            var targetDiv = $('.trouble-' + checkboxId);
            var targetDivReason = $('.div-reason-' + checkboxId);

            if ($(this).is(':checked')) {
                targetDiv.show();
                // targetDivReason.show();
            } else {
                targetDiv.hide();
                // targetDivReason.hide();
            }

        });
    }

    $(document).ready(function() {
        eachTroubleCheckbox();
    });

    $('.trouble-checkbox').change(function() {
        var checkboxId = $(this).data('id');
        var targetDiv = $('.trouble-' + checkboxId);
        var targetDivReason = $('.div-reason-' + checkboxId);

        if ($(this).is(':checked')) {
            targetDiv.show();
            // targetDiv.find('input[type="checkbox"]').prop('checked', true);
            // targetDivReason.show();
        } else {
            targetDiv.hide();
            targetDiv.find('input[type="checkbox"]').prop('checked', false);
            // targetDivReason.hide();
        }
    });

    function add_trouble(trouble_name) {
        console.log(trouble_name);
        // $('.div_' + trouble_name).append(`<div>
        //     <div class="checklist relative ui-sortable-handle" style="height: 38px;">
        //         <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
        //             <input type="checkbox" name="checked[${trouble_name}][]" value="1">
        //             <textarea class="text_checklist" name="items[${trouble_name}][]" rows="1" style="height: 28px;"></textarea>
        //         </div>
        //     </div>
        // </div>`);

        // $('.div_' + trouble_name).html('');
        $('.div_' + trouble_name).append(`<div>
            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                    <input type="checkbox" name="checked[${trouble_name}][]" value="1" checked>
                    <label for=""><span class="hide"></span></label>
                    <textarea class="text_checklist" name="items[${trouble_name}][]" rows="1" style="height: 28px;"></textarea>
                </div>
            </div>
        </div>`);
    }


    if ($('#dropzoneProduction').length > 0) {
        expenseDropzone = new Dropzone('#form-production_report', appCreateDropzoneOptions({
            paramName: "image",
            autoProcessQueue: false,
            previewsContainer: '.dropzone-production',
            addRemoveLinks: true,
            acceptedFiles: 'image/*',
            maxFiles: 10,
            clickable: '#dropzoneProduction',
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // window.location.reload();
                }
            }
        }));
    }

    function removeFile(id) {
        $('.img-remove').append(`<input type="hidden" name="file_remove[]" value="${id}"/>`);
        $(`.row-img-${id}`).remove();
    }

    $("#staff_responsible").change(function() {
        staff_responsible = $(this).val();
        if (staff_responsible) {
            $.ajax({
                    url: site.base_url + 'admin/production_report/getKpiByStaff',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        csrf_token_name: hash,
                        staff_responsible: staff_responsible,
                    },
                })
                .done(function(data) {
                    option = '';
                    if (data.dtcategoryKpi.length > 0) {
                        $.each(data.dtcategoryKpi, function(k, v) {
                            option += `<option data-subtext="${v.type}" value="${v.id}">${v.code}-${v.name}</option>`;
                        })
                    }
                    $("#category_kpi_criteria_id").html(option);
                    $("#category_kpi_criteria_id").selectpicker("refresh");
                })
                .fail(function() {
                    console.log("error");
                });
        }
        checked = $('#violate').is(':checked');
        if (staff_responsible && checked) {
            var date = $('#date').val();
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['id'] = $('#staff_responsible').val();
            data['date'] = date;
            $.post(admin_url + 'production_report/countViolate', data, function(data) {
                // $('#countTrouble').val(data);
                data = JSON.parse(data);
                count = data.count;
                console.log(count)
                if (count > 0) {
                    $('#countViolate_text').html('<br>Số lần vi phạm trong quý: ' + count + ' lần');
                } else {
                    $('#countViolate_text').html('');
                }
            })
        } else {
            // $('#countTrouble').val(0);
            $('#countViolate_text').html('');
        }
    })

    if (!$("#violate").prop('checked')) {
        $('#violation_point').hide();
    }

    $("#violate").change(function() {
        checked = $(this).is(':checked');
        if (checked) {
            $(".show_kpi").removeClass('hide');
            $('#violation_point').show();
        } else {
            $(".show_kpi").addClass('hide');
            $('#violation_point').hide();
        }
        type_report = $('input[name="type_report"]:checked').val();
        if (type_report != 2) {
            if (checked) {
                $(".show_kpi_department").removeClass('hide');
                var date = $('#date').val();
                var data = {};
                if (typeof (csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['id'] = $('#staff_responsible').val();
                data['date'] = date;
                $.post(admin_url + 'production_report/countViolate', data, function (data) {
                    // $('#countTrouble').val(data);
                    data = JSON.parse(data);
                    count = data.count;
                    if (count > 0) {
                        $('#countViolate_text').html('<br>Số lần vi phạm trong quý: ' + count + ' lần');
                    } else {
                        $('#countViolate_text').html('');
                    }
                })
            } else {
                $(".show_kpi_department").addClass('hide');
                // $('#countTrouble').val(0);
                $('#countViolate_text').html('');
            }
        } else {
            $(".show_kpi_department").removeClass('hide');
        }
        
    })
</script>
<script>
    $(document).ready(function() {
        $('input[name="type_report"]').change(function(event) {
            var _checked = $(this).prop('checked');
            value = $(this).val();
            if (_checked) {
                var _div = $(this).closest('div.radio');
                var _name = $(_div).find('label').html();
                $('#name_report').val(_name);
            }
            if (value == 2){
                $(".show_kpi_department").removeClass('hide');
            } else {
                $(".show_kpi_department").addClass('hide')
            }


            if (value == 4) {
                $("div.wrap-category_tasks").addClass('hide');
                $("div.wrap-violation_group").removeClass('hide');
                $("select#violation_group").attr('required', true);
                $("select#category_tasks").attr('required', false);
                $("#violate").prop('checked', true).change();
            } else {
                $("div.wrap-category_tasks").removeClass('hide');
                $("div.wrap-violation_group").addClass('hide');
                $("select#violation_group").attr('required', false);
                $("select#category_tasks").attr('required', true);
                $("#violate").prop('checked', false).change();
            }
        });
        if ($('input[name="type_report"]').val() == 4) {
            $("#violate").prop('checked', true).change();
        }
    });
    ajaxSelectParams('#machines_id', 'admin/suggest_repalce/searchMachines', $("#machines_id").val(), true, true);
    ajaxSelectParamsNew('#salary_3p', 'admin/production_report/searchSalary3p', $("#salary_3p").val(), true, true);
    ajaxSelectParams('#role_id_jd', 'admin/suggest_task/searchRoles', $("#role_id_jd").val(), true, true);
    $('input[name="role_id_jd"]').change(function(event) {
        var role_id_jd = $('input[name="role_id_jd"]').val();
        $.ajax({
                url: site.base_url + 'admin/production_report/GetJdTasks',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    role_id_jd: role_id_jd,
                },
            })
            .done(function(data) {
                option = '<option></option>';
                if (data.job_detail.length > 0) {
                    $.each(data.job_detail, function(k, v) {
                        option += `<option value="${v.id}">${v.code}-${v.title}</option>`;
                    })
                }
                $("#jd_tasks").html(option);
                $("#jd_tasks").selectpicker("refresh");
            })
            .fail(function() {
                console.log("error");
            });
    });
    $('select[name="jd_tasks"]').change(function(event) {
        var jd_tasks = $('select[name="jd_tasks"]').val();
        $.ajax({
                url: site.base_url + 'admin/production_report/GetDetailJdTasks',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    jd_tasks: jd_tasks,
                },
            })
            .done(function(data) {
                option_responsibility = '<option></option>';
                if (data.responsibility.length > 0) {
                    $.each(data.responsibility, function(k, v) {
                        option_responsibility += `<option value="${v.id}">${v.name}</option>`;
                    })
                }
                $("#jd_responsibility").html(option_responsibility);
                $("#jd_responsibility").selectpicker("refresh");

                option_requirement = '<option></option>';
                if (data.requirement.length > 0) {
                    $.each(data.requirement, function(k, v) {
                        option_requirement += `<option value="${v.id}">${v.name}</option>`;
                    })
                }
                $("#jd_requirement").html(option_requirement);
                $("#jd_requirement").selectpicker("refresh");

                option_jurisdiction = '<option></option>';
                if (data.jurisdiction.length > 0) {
                    $.each(data.jurisdiction, function(k, v) {
                        option_jurisdiction += `<option value="${v.id}">${v.name}</option>`;
                    })
                }
                $("#jd_jurisdiction").html(option_jurisdiction);
                $("#jd_jurisdiction").selectpicker("refresh");

                option_competency_standard = '<option></option>';
                if (data.competency_standard.length > 0) {
                    $.each(data.competency_standard, function(k, v) {
                        option_competency_standard += `<option value="${v.id}">${v.name}</option>`;
                    })
                }
                $("#jd_competency_standard").html(option_competency_standard);
                $("#jd_competency_standard").selectpicker("refresh");

            })
            .fail(function() {
                console.log("error");
            });
    });

    function ajaxSelectParamsNew(element, url, id, params = false, clearSl2 = false) {
        if (id) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: clearSl2,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val() + '/' + $("#role_id").val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.row);
                            if (data.row) {
                                if (data.row.id === 0) {
                                    $(element).val(0);
                                }
                            }
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            params: params,
                            term: term,
                            role_id: $("#role_id").val(),
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: clearSl2,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            params: params,
                            term: term,
                            role_id: $("#role_id").val(),
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        }
    }
</script>