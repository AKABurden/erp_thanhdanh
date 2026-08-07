<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="production_report_modal" role="dialog" style="z-index: 9999999;">
    <style>
        #production_report_modal img.staff-profile-image-small {
            height: 20px;
            width: 20px;
        }
    </style>
    <div class="modal-dialog" role="document" style="min-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="title">
                        <?= !empty($title) ? $title : '' ?>
                    </span>
                </h4>
            </div>
            <?php
            $imgcheck = '<img style="width:15px;" src="' . base_url('uploads/check.png') . '" width="10" height="10">';
            $imgnocheck = '<span style="font-size: 19px;">◻️</span>';
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-xs-6 lead-information-col">
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Số phiếu: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['reference_no_report'] ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Loại phiếu: </span>
                                    <?php
                                    $type_report = $production_report['type_report'];
                                    $strTypeReport = '';
                                    if ($type_report == 1) {
                                        $strTypeReport = '<div class=""><div class="label btn-success">Báo cáo không phù hợp</div></div>';
                                    } else if ($type_report == 2) {
                                        $strTypeReport = '<div class=""><label class="label btn-primary">Báo cáo vượt</label></div>';
                                    } else if ($type_report == 3) {
                                        $strTypeReport = '<div class=""><label class="label btn-warning">Báo cáo cải tiến</label></div>';
                                    }
                                    ?>
                                    <span class="bold font-medium-xs lead-name"><?= $strTypeReport ?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Tên phiếu: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['name_report'] ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Ngày: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= _dt_new($production_report['date']) ?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Liên quan đến: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= !empty($production_report['group_rl_name']) ? ('<br/>' . $production_report['group_rl_name']) : '' ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Đơn đặt hàng: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['code_orders'] ?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Lệnh SX: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['reference_no'] ?>
                                        <span style="float: right;">Số lượng
                                            <?= number_format_data($production_report['quantity_pcs']) ?>
                                            pcs</span></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Sản phẩm: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= !empty($production_report['list_items_name']) ? ('<br/>' . $production_report['list_items_name']) : '-' ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Chi tiết liên quan: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= !empty($production_report['recommended_list_name']) ? ('<br/>' . $production_report['recommended_list_name']) : '' ?></span>
                                </div>
                                <div class="wap-content firt" style="display: flex;align-items: center">
                                    <span class="text-muted lead-field-heading no-mtop bold">Vi Phạm: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <div class="checkbox checkbox-info">
                                            <input type="checkbox" name="violate" onclick="return false;" id="violate"
                                                <?= $production_report['violate'] == 1 ? 'checked' : '' ?> value="1">
                                            <label for="violate"><?= lang('Vi phạm') ?></label>
                                        </div>
                                    </span>
                                </div>
                                <div class="wap-content second" style="display: flex;align-items: center">
                                    <span class="text-muted lead-field-heading no-mtop bold">Mục tiêu KPI phòng ban:
                                    </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= !empty($production_report['name_kpi_department']) ? ('' . $production_report['name_kpi_department']) : '' ?></span>
                                </div>
                                <div class="wap-content firt" style="display: flex;align-items: center">
                                    <span class="text-muted lead-field-heading no-mtop bold">Chi tiết mục tiêu KPI phòng
                                        ban: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= !empty($production_report['name_kpi_department_detail']) ? ('' . $production_report['name_kpi_department_detail']) : '' ?></span>
                                </div>
                                <div class="wap-content firt" style="display: flex;align-items: center">
                                    <span class="text-muted lead-field-heading no-mtop bold">Chi tiết vi phạm: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= !empty($production_report['kpi_list_criteria_department_violate']) ? ('' . $production_report['kpi_list_criteria_department_violate']) : '' ?></span>
                                </div>
                                <?php if ($production_report['countViolate'] > 0) { ?>
                                    <div class="wap-content second">
                                        <span class="text-muted lead-field-heading no-mtop bold">Số vi phạm trong quý từ lúc
                                            tạo phiếu: </span>
                                        <span
                                            class="bold font-medium-xs lead-name"><?= $production_report['countViolate'] ?></span>
                                    </div>
                                <?php } ?>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Sự cố: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['code_trouble'] ?>
                                        (<i><?= $production_report['name_trouble'] ?></i>)</span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Số lần sự cố trong tháng từ
                                        lúc tạo phiếu: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['countTrouble'] ?></span>
                                </div>
                                <div class="wap-content firt" style="display: flex;justify-content: space-between">
                                    <div>
                                        <span
                                            class="text-muted lead-field-heading no-mtop bold"><?= _l('Quản lý chịu trách nhiệm') ?>:
                                        </span>
                                        <?php
                                        $responsible = '';
                                        if (!empty($production_report['staff_manage'])) {
                                            $responsible = get_staff_full_name($production_report['staff_manage']);
                                        }
                                        ?>
                                        <span class="bold font-medium-xs lead-name"><?= $responsible ?></span>
                                    </div>
                                    <div>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy mẫu: </span>
                                        <span
                                            class="bold font-medium-xs lead-name"><?= (!empty($production_report['type_stage_1']) ? $imgcheck : $imgnocheck) ?></span>
                                        <br>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy hàng + mẫu:
                                        </span>
                                        <span
                                            class="bold font-medium-xs lead-name"><?= (!empty($production_report['type_stage_2']) ? $imgcheck : $imgnocheck) ?></span>
                                        <br>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy hàng: </span>
                                        <span
                                            class="bold font-medium-xs lead-name"><?= (!empty($production_report['type_stage_3']) ? $imgcheck : $imgnocheck) ?></span>
                                        <br>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy bù hàng: </span>
                                        <span
                                            class="bold font-medium-xs lead-name"><?= (!empty($production_report['type_stage_4']) ? $imgcheck : $imgnocheck) ?></span>
                                    </div>
                                </div>
                                <div class="wap-content second hide">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người tạo: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php $nameStaffCreate = (!empty($production_report['create_by']) ? $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
                                            ->get_where('tblstaff', ['staffid' => $production_report['create_by']])->row('fullname') : ''); ?>
                                        <?php echo staff_profile_image((!empty($production_report['create_by']) ? $production_report['create_by'] : ''), [
                                            'staff-profile-image-small'
                                        ], 'small', [
                                            'data-toggle' => 'tooltip',
                                            'data-title' => $nameStaffCreate
                                        ]) . ' ' . $nameStaffCreate ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-6 lead-information-col mbot10">
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Chi nhánh: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= !empty($production_report['id_branch']) ? get_table_where('tblbranch', ['id' => $production_report['id_branch']], '', 'row')->name : '' ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Bộ phận: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['name_departments'] ?></span>
                                </div>
                                <div class="wap-content firt" style="display: flex;align-items: center">
                                    <span class="text-muted lead-field-heading no-mtop bold">Mã công việc: </span>
                                    <span class="bold font-medium-xs lead-name" style="margin-left: 10px">
                                        <div>
                                            <label style="font-weight: 500">Lọc Mã Công Việc Theo Chức Vụ</label><br>
                                            <?= !empty($production_report['name_role']) ? $production_report['name_role'] : '-' ?>
                                        </div>
                                        <span class="font-weight: 500"><?= _l('Nhân viên') ?>: </span>
                                        <?php
                                        $responsible = '';
                                        if (!empty($production_report['staff_responsible'])) {
                                            $responsible = get_staff_full_name($production_report['staff_responsible']);
                                        } else if (!empty($production_report['department_responsible'])) {
                                            $responsible = get_table_where('tbldepartments', ['departmentid' => $production_report['department_responsible']], '', 'row_array', '', 'CONCAT(code, " (", name, ")") as department');
                                            if (!empty($responsible['department'])) {
                                                $responsible = $responsible['department'];
                                            } else {
                                                $responsible = '';
                                            }
                                        }
                                        ?>
                                        <span class=""><?= $responsible ?></span>
                                        <?php if ($production_report['type_report'] == 4) { ?>
                                            <div>
                                                <?php
                                                $dtViolationGroup = get_table_where('tbl_violation_group', ['id' => $production_report['violation_group']], '', 'row_array');
                                                ?>
                                                <label style="font-weight: 500">Nhóm Vi Phạm</label><br>
                                                <?= !empty($dtViolationGroup) ? $dtViolationGroup['code'] . ' - ' . $dtViolationGroup['name'] . ' (' . $dtViolationGroup['detail'] . ') ' : '' ?>
                                            </div>
                                        <?php } else { ?>
                                            <div>
                                                <label style="font-weight: 500">Mã Công Việc</label><br>
                                                <?= !empty($production_report['code_category_task']) ? $production_report['code_category_task'] : '-' ?>
                                                - <?= $production_report['name_category_task'] ?? '' ?>
                                            </div>
                                        <?php } ?>
                                    </span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Công đoạn phát hiện sự cố:
                                    </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['stage'] ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Chi tiết công việc: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['detail_tasks'] ?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người bàn giao: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php
                                        if (!empty($production_report['staff_handover'])) {
                                            echo staff_profile_image($production_report['staff_handover'], array('staff-profile-image-small mright5'), 'small', array(
                                                'data-toggle' => 'tooltip',
                                                'data-title' => (!empty($production_report['staff_handover']) ? $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
                                                    ->get_where('tblstaff', ['staffid' => $production_report['staff_handover']])->row('fullname') : '')

                                            ));
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người nhận bàn giao:
                                    </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php
                                        if (!empty($production_report['handler'])) {
                                            foreach ($production_report['handler'] as $key => $value) {
                                                echo staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => (!empty($value['staff_id']) ? $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
                                                        ->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname') : '')

                                                ));
                                            }
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người giám sát - báo cáo:
                                    </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php
                                        if (!empty($production_report['assigned'])) {
                                            foreach ($production_report['assigned'] as $key => $value) {
                                                echo staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => (!empty($value['staff_id']) ? $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
                                                        ->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname') : '')

                                                ));
                                            }
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">KPIs (lần): </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['quantity_kpi'] ?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Máy móc thiết bị: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['name_machine'] ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Thời gian nghỉ máy: </span>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= $production_report['downtime'] ?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Mục tiêu vị trí theo (Mô tả
                                        công việc) JD: </span>
                                    <?php
                                    $role_id_jd = get_table_where('tblroles', ['roleid' => $production_report['role_id_jd']], '', 'row_array');
                                    ?>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= (!empty($role_id_jd)) ? $role_id_jd['name'] : '' ?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Mô tả công việc JD: </span>
                                    <?php
                                    $jd_tasks = get_table_where('tbl_job_detail', ['id' => $production_report['jd_tasks']], '', 'row_array');
                                    ?>
                                    <span
                                        class="bold font-medium-xs lead-name"><?= (!empty($jd_tasks)) ? $jd_tasks['title'] . ' (' . $jd_tasks['code'] . ')' : '' ?></span>
                                </div>
                                <?php
                                $this->db->select("
                                    tbl_job_detail_child.id as id,
                                    tbl_job_detail_child.name as name,
                                    tbl_job_detail_child.type as type,
                                ");
                                $this->db->from('tbl_job_detail_child');
                                $this->db->where('tbl_job_detail_child.job_detail_id', $production_report['jd_tasks']);
                                $job_detail = $this->db->get()->result_array();
                                $responsibility = [];
                                $jurisdiction = [];
                                $requirement = [];
                                $competency_standard = [];
                                foreach ($job_detail as $key => $value) {
                                    if ($value['type'] == 1) {
                                        $responsibility[] = $value;
                                    }
                                    if ($value['type'] == 2) {
                                        $jurisdiction[] = $value;
                                    }
                                    if ($value['type'] == 3) {
                                        $requirement[] = $value;
                                    }
                                    if ($value['type'] == 4) {
                                        $competency_standard[] = $value;
                                    }
                                }
                                $this->db->from('tblproduction_report_jd');
                                $this->db->where('tblproduction_report_jd.id_production_report', $production_report['id']);
                                $production_report_jd = $this->db->get()->result_array();
                                $data_responsibility = [];
                                $data_jurisdiction = [];
                                $data_requirement = [];
                                $data_competency_standard = [];
                                foreach ($production_report_jd as $key => $value) {
                                    if ($value['type'] == 'jd_responsibility') {
                                        $data_responsibility[] = $value['id_job_detail_child'];
                                    }
                                    if ($value['type'] == 'jd_jurisdiction') {
                                        $data_jurisdiction[] = $value['id_job_detail_child'];
                                    }
                                    if ($value['type'] == 'jd_requirement') {
                                        $data_requirement[] = $value['id_job_detail_child'];
                                    }
                                    if ($value['type'] == 'jd_competency_standard') {
                                        $data_competency_standard[] = $value['id_job_detail_child'];
                                    }
                                }
                                ?>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Theo trách nhiệm: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php foreach ($responsibility as $key => $value) { ?>
                                            <?php if (in_array($value['id'], $data_responsibility)) { ?>
                                                <div>- <?= $value['name'] ?></div>
                                            <?php } ?>
                                        <?php } ?>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Theo quyền hạn: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php foreach ($jurisdiction as $key => $value) { ?>
                                            <?php if (in_array($value['id'], $data_jurisdiction)) { ?>
                                                <div>- <?= $value['name'] ?></div>
                                            <?php } ?>
                                        <?php } ?>  
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Theo yêu cầu: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php foreach ($requirement as $key => $value) { ?>
                                            <?php if (in_array($value['id'], $data_requirement)) { ?>
                                                <div>- <?= $value['name'] ?></div>
                                            <?php } ?>
                                        <?php } ?>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Theo tiêu chuẩn năng lực: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php foreach ($competency_standard as $key => $value) { ?>
                                            <?php if (in_array($value['id'], $data_competency_standard)) { ?>
                                                <div>- <?= $value['name'] ?></div>
                                            <?php } ?>
                                        <?php } ?>
                                </div>
                                <a href="<?= admin_url('production_report/detail/' . $production_report['id']) ?>"
                                    class="btn btn-info pull-right mtop10">Sửa</a>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table dataTable">
                            <thead>
                                <tr>
                                    <th>Nội Dung KPH</th>
                                    <th>Hành Động Xử Lý Lập Tức</th>
                                    <th>Quy Trình Xử Lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 500px;">
                                        <b>Mô tả sự KPH:</b>
                                        <?= !empty($production_report['described']) ? $production_report['described'] : '-' ?>
                                        <div class="clearfix"></div>
                                        <div class="row mtop10">
                                            <?php if (!empty($images)) { ?>
                                                <?php foreach ($images as $key => $image) { ?>
                                                    <div class="col-md-4">
                                                        <img src="<?= base_url($image['file_name']); ?>"
                                                            style="width:100%;height:150px;">
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <!--                                <td><b>Thời điểm ghi nhận:</b> --><? //=_dt($production_report['time_of_recording'])
                                    ?><!--</td>-->
                                    <td rowspan="3">
                                        <div><b>Chấp nhận :</b>
                                            <?= (!empty($production_report['action_now_1']) ? $imgcheck : $imgnocheck) ?>
                                        </div><br />
                                        <div><b>Loại bỏ :</b>
                                            <?= (!empty($production_report['action_now_2']) ? $imgcheck : $imgnocheck) ?>
                                        </div><br />
                                        <div><b>Làm lại :</b>
                                            <?= (!empty($production_report['action_now_3']) ? $imgcheck : $imgnocheck) ?>
                                        </div><br />
                                        <div><b>Khác :</b>
                                            <?= (!empty($production_report['action_now_4']) ? $imgcheck : $imgnocheck) ?>
                                        </div><br />
                                    </td>
                                    <td rowspan="1">
                                        <b>Nguyên nhân:</b>
                                        <hr class="mtop5 mbot5" />
                                        <?php
                                        $dtReason = $production_report['id'] ?? null ? $this->recommended_list_model->getProductionReportReason($production_report['id'], 'trouble') : null;
                                        ?>
                                        <div class="col-md-12">
                                            <?php if (!empty($production_report['material'])) { ?>
                                                <?php
                                                $is_check = false;
                                                foreach ($production_report['material'] as $key => $value) {
                                                    if (!empty($value['ischeck'])) {
                                                        $is_check = true;
                                                    }
                                                }
                                                ?>
                                                <?php if (!empty($is_check)) { ?>
                                                    <div class="mtop10"><b><u>Nguyên phụ liệu (Material)</u></b></div>
                                                    <div class="text-danger"><?= $dtReason['material']['reason'] ?? '' ?></div>
                                                    <?php foreach ($production_report['material'] as $key => $value) { ?>
                                                        <?php if (!empty($value['ischeck'])) { ?>
                                                            <div class="mleft10 mtop10">
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" id="material_<?= $key ?>"
                                                                        onclick="changeStatus(<?= $value['id'] ?>, this)" value="1"
                                                                        <?= (!empty($value['ischeck']) ? 'checked' : '') ?>>
                                                                    <label for="material_<?= $key ?>"><?= $value['name'] ?></label>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!empty($production_report['man'])) { ?>
                                                <?php
                                                $is_check = false;
                                                foreach ($production_report['man'] as $key => $value) {
                                                    if (!empty($value['ischeck'])) {
                                                        $is_check = true;
                                                    }
                                                }
                                                ?>
                                                <?php if (!empty($is_check)) { ?>
                                                    <div class="mtop10"><b><u>Nhân lực (Man)</u></b></div>
                                                    <div class="text-danger"><?= $dtReason['man']['reason'] ?? '' ?></div>
                                                    <?php foreach ($production_report['man'] as $key => $value) { ?>
                                                        <?php if (!empty($value['ischeck'])) { ?>
                                                            <div class="mleft10 mtop10">
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" id="man_<?= $key ?>"
                                                                        onclick="changeStatus(<?= $value['id'] ?>, this)" value="1"
                                                                        <?= (!empty($value['ischeck']) ? 'checked' : '') ?>>
                                                                    <label for="man_<?= $key ?>"><?= $value['name'] ?></label>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!empty($production_report['machine'])) { ?>
                                                <?php
                                                $is_check = false;
                                                foreach ($production_report['machine'] as $key => $value) {
                                                    if (!empty($value['ischeck'])) {
                                                        $is_check = true;
                                                    }
                                                }
                                                ?>
                                                <?php if (!empty($is_check)) { ?>
                                                    <div class="mtop10"><b><u>Máy móc (Machine)</u></b></div>
                                                    <div class="text-danger"><?= $dtReason['machine']['reason'] ?? '' ?></div>
                                                    <?php foreach ($production_report['machine'] as $key => $value) { ?>
                                                        <?php if (!empty($value['ischeck'])) { ?>
                                                            <div class="mleft10 mtop10">
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" id="machine_<?= $key ?>"
                                                                        onclick="changeStatus(<?= $value['id'] ?>, this)" value="1"
                                                                        <?= (!empty($value['ischeck']) ? 'checked' : '') ?>>
                                                                    <label for="machine_<?= $key ?>"><?= $value['name'] ?></label>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!empty($production_report['method'])) { ?>
                                                <?php
                                                $is_check = false;
                                                foreach ($production_report['method'] as $key => $value) {
                                                    if (!empty($value['ischeck'])) {
                                                        $is_check = true;
                                                    }
                                                }
                                                ?>
                                                <?php if (!empty($is_check)) { ?>
                                                    <div class="mtop10"><b><u>Phương pháp (Method)</u></b></div>
                                                    <div class="text-danger"><?= $dtReason['method']['reason'] ?? '' ?></div>
                                                    <?php foreach ($production_report['method'] as $key => $value) { ?>
                                                        <?php if (!empty($value['ischeck'])) { ?>
                                                            <div class="mleft10 mtop10">
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" id="method_<?= $key ?>"
                                                                        onclick="changeStatus(<?= $value['id'] ?>, this)" value="1"
                                                                        <?= (!empty($value['ischeck']) ? 'checked' : '') ?>>
                                                                    <label for="method_<?= $key ?>"><?= $value['name'] ?></label>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!empty($production_report['environment'])) { ?>
                                                <?php
                                                $is_check = false;
                                                foreach ($production_report['environment'] as $key => $value) {
                                                    if (!empty($value['ischeck'])) {
                                                        $is_check = true;
                                                    }
                                                }
                                                ?>
                                                <?php if (!empty($is_check)) { ?>
                                                    <div class="mtop10"><b><u>Môi trường (Environment)</u></b></div>
                                                    <div class="text-danger"><?= $dtReason['method']['environment'] ?? '' ?>
                                                    </div>
                                                    <?php foreach ($production_report['environment'] as $key => $value) { ?>
                                                        <?php if (!empty($value['ischeck'])) { ?>
                                                            <div class="mleft10 mtop10">
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" id="environment_<?= $key ?>"
                                                                        onclick="changeStatus(<?= $value['id'] ?>, this)" value="1"
                                                                        <?= (!empty($value['ischeck']) ? 'checked' : '') ?>>
                                                                    <label for="environment_<?= $key ?>"><?= $value['name'] ?></label>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Số lượng hàng hư :</b>
                                        <?= number_format_data($production_report['quantity']) ?></td>
                                    <td rowspan="1">
                                        <b>Quy trình xử lý :</b>
                                        <hr class="mtop5 mbot5" />
                                        <div class="col-md-12">
                                            <?php if (!empty($production_report['procedure'])) { ?>
                                                <?php foreach ($production_report['procedure'] as $key => $value) { ?>
                                                    <div class="mleft10 mtop10">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" id="procedure_<?= $key ?>"
                                                                onclick="changeStatus(<?= $value['id'] ?>, this)" value="1"
                                                                <?= (!empty($value['ischeck']) ? 'checked' : '') ?>>
                                                            <label for="procedure_<?= $key ?>"><?= $value['name'] ?></label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="white-space: break-spaces;"><b>Ghi chú:</b>
                                        <?= $production_report['note'] ?></td>
                                    <td>
                                        <b>Quy trình khắc phục, phòng ngừa :</b>
                                        <hr class="mtop5 mbot5" />
                                        <div class="col-md-12">
                                            <?php if (!empty($production_report['fix'])) { ?>
                                                <?php foreach ($production_report['fix'] as $key => $value) { ?>
                                                    <div class="mleft10 mtop10">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" id="fix_<?= $key ?>"
                                                                onclick="changeStatus(<?= $value['id'] ?>, this)" value="1"
                                                                <?= (!empty($value['ischeck']) ? 'checked' : '') ?>>
                                                            <label for="fix_<?= $key ?>"><?= $value['name'] ?></label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div>
                                            <?= !empty($production_report['note_fix']) ? $production_report['note_fix'] : '' ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-default" target="_blank"
                    href="<?= admin_url('production_report/pdf/' . $production_report['id']) ?>"><i class="fa fa-print"
                        aria-hidden="true"></i> <?php echo _l('in'); ?></a>
                <button class="btn btn-danger" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#production_report_modal').modal('show');

    function changeStatus(id, _this) {
        var status = 0;
        if ($(_this).prop('checked') == true) {
            status = 1;
        }
        $.get(admin_url + 'production_report/changeIscheck/' + id + '/' + status, function (result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
        })
    }

    $('#cproduction_report_modal').on('hidden', function () {
        if (typeof TableData != 'undefined') {
            TableData.draw('page');
        }
    });
</script>