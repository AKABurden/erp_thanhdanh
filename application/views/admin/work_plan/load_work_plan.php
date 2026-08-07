<?php
$counter = 0;
$trOne = '';
$trTwo = '';
$trThree = '';
$trFour = '';
if (!empty($work_plan)) {
    $items = $this->work_plan_model->getWorkPlanItems($id);
    foreach ($items as $key => $value) {
        $type = $value['type'];

        $tdNumber = '<td class="text-center td-numbers"></td>';
        $tdName = '<td>
            <input type="hidden" name="items[' . $counter . '][type]" class="form-control type" value="' . $type . '">
            <input type="hidden" name="items[' . $counter . '][number]" class="form-control number" value="">
            <input type="hidden" name="items[' . $counter . '][work_plan_items_id]" class="form-control work_plan_items_id" value="' . $value['id'] . '">
            <input type="text" name="items[' . $counter . '][name]" class="form-control name" placeholder="' . lang('Tên') . '" value="' . $value['name'] . '">
        </td>';
     
        $tdDateStart = '<td><input type="text" name="items[' . $counter . '][date_start]"
        data-date-min-date="' . (!empty($limit_date_start) ? $limit_date_start : '') . '"
        data-date-end-date="' . (!empty($limit_date_end) ? $limit_date_end : '') . '" class="form-control datepicker width100" value=""></td>';

        $tdDateEnd = '<td><input type="text" name="items[' . $counter . '][date_end]" class="form-control datepicker width100" value=""></td>';
        $tdDateTasks = '<td><input type="text" name="items[' . $counter . '][date_tasks]"
        data-date-min-date="' . (!empty($limit_date_start) ? $limit_date_start : '') . '"
        data-date-end-date="' . (!empty($limit_date_end) ? $limit_date_end : '') . '" class="form-control datepicker width100" value=""></td>';
        $tdWeekOne = '<td>
                        <input type="text" name="items[' . $counter . '][week_one]" class="form-control week_one" placeholder="' . lang('Tuần 1') . '" value="' . $value['week_one'] . '">
                    </td>';
        $tdWeekTwo = '<td>
            <input type="text" name="items[' . $counter . '][week_two]" class="form-control week_two" placeholder="' . lang('Tuần 2') . '" value="' . $value['week_two'] . '">
        </td>';
        $tdWeekThree = '<td>
            <input type="text" name="items[' . $counter . '][week_three]" class="form-control week_three" placeholder="' . lang('Tuần 3') . '" value="' . $value['week_three'] . '">
        </td>';
        $tdWeekFour = '<td>
            <input type="text" name="items[' . $counter . '][week_four]" class="form-control week_four" placeholder="' . lang('Tuần 4') . '" value="' . $value['week_four'] . '">
        </td>';
        $tdPriorityLevel = '<td>
            <input type="text" name="items[' . $counter . '][priority_level]" class="form-control priority_level number-format" placeholder="' . lang('Mức độ ưu tiên') . '" value="' . $value['priority_level'] . '">
        </td>';

        $optionsProcess = '<option></option>';
        foreach ($process_work_plan as $kP => $vP) {
            $selected = $kP == $value['process'] ? 'selected' : '';
            $optionsProcess .= '<option data-content="<span style=\'color: ' . $vP['color'] . ';\'>' . $vP['name'] . '</span>" ' . $selected . ' value="' . $kP . '">' . $vP['name'] . '</option>';
        }

        $tdProcess = '<td style="max-width: 150px !important;">
            <select name="items[' . $counter . '][process]" class="form-control selectpicker process" data-live-search="true" data-none-selected-text="' . lang('Quy trình') . '">
                ' . $optionsProcess . '
            </select>
        </td>';

        $optionsStaff = '';
        $optionsManageReports = '';
        $dtWorkPlanItemsStaffs = $this->work_plan_model->getWorkPlanItemsStaffs($value['id'], 1);
        $dtWorkPlanItemsStaffsManage = $this->work_plan_model->getWorkPlanItemsStaffs($value['id'], 2);
        if (!empty($staffs)) {
            foreach ($staffs as $kS => $vS) {
                $selectedStaff = '';
                foreach ($dtWorkPlanItemsStaffs as $kWPS => $vWPS) {
                    if ($vS['staffid'] == $vWPS['staff_id']) {
                        $selectedStaff = 'selected';
                        break;
                    }
                }

                $selectedStaffManage = '';
                foreach ($dtWorkPlanItemsStaffsManage as $kWPS => $vWPS) {
                    if ($vS['staffid'] == $vWPS['staff_id']) {
                        $selectedStaffManage = 'selected';
                        break;
                    }
                }

                $optionsStaff .= '<option ' . $selectedStaff . ' value="' . $vS['staffid'] . '">' . $vS['fullname'] . '</option>';
                $optionsManageReports .= '<option ' . $selectedStaffManage . ' value="' . $vS['staffid'] . '">' . $vS['fullname'] . '</option>';
            }
        }

        $tdStaffs = '<td style="max-width: 150px !important;">
            <select name="items[' . $counter . '][staffs][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="' . lang('Người phụ trách') . '" multiple>
                ' . $optionsStaff . '
            </select>
        </td>';
        $tdManageReports = '<td style="max-width: 150px !important;">
            <select name="items[' . $counter . '][manage_reports][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="' . lang('Người giám sát - báo cáo') . '" multiple>
                ' . $optionsManageReports . '
            </select>
        </td>';
        $tdQR = '<td></td>';
        $tdActions = '<td class="text-danger text-center">
            <i onclick="removeItemWorkPlan(this)" class="fa fa-remove" style="cursor: pointer;"></i>
        </td>';

        $trItem = '<tr>
            ' . $tdNumber . '
            ' . $tdName . '
            ' . $tdDateStart . '
            ' . $tdDateEnd . '
            ' . $tdDateTasks . '
            ' . $tdWeekOne . '
            ' . $tdWeekTwo . '
            ' . $tdWeekThree . '
            ' . $tdWeekFour . '
            ' . $tdPriorityLevel . '
            ' . $tdProcess . '
            ' . $tdStaffs . '
            ' . $tdManageReports . '
            ' . $tdQR . '
            ' . $tdActions . '
        </tr>';
        $counter++;

        if ($type == 1) {
            $trOne .= $trItem;
        } else if ($type == 2) {
            $trTwo .= $trItem;
        } else if ($type == 3) {
            $trThree .= $trItem;
        } else if ($type == 4) {
            $trFour .= $trItem;
        }
    }
}
?>
<?php // $work_plan_task = get_table_where('tbl_work_plan_task', ['work_plan_id'=>$id]); $counter = 0;
?>
<?php $counter = 0; ?>

<?php foreach ($work_plan_task as $key => $value) {
    $taskRel = $this->work_plan_model->getTaskRel($value['id']);
    $category_task = get_table_where('tblcategory_tasks', ['id' => $value['category_task_id']], '', 'row_array');
    if (!empty($category_task['departments'])) {
        $department = get_table_where('tbldepartments', ['departmentid' => $category_task['departments']], '', 'row_array', '', 'name');
    }

    $staff_assigner = '';
    if (!empty($value['staff_assigner'])) {
        $fullname_CREATE = get_staff_full_name($value['staff_assigner']);
        $staff_assigner = '<span class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $value['staff_assigner']) . '">' . staff_profile_image($value['staff_assigner'], [
            'staff-profile-image-small',
        ]) . '</a><div class="hide">' . $fullname_CREATE . '</div></span>';
    }

    $staff_assigned = '';
    if (!empty($value['staff_assigned'])) {
        $arrStaff_assigned = explode(',', $value['staff_assigned']);
        foreach ($arrStaff_assigned as $staff_assigned_id) {
            $fullname_CREATE = get_staff_full_name($staff_assigned_id);
            // $staff_assigned .= '<p class="text-center" style="margin: 0;"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $staff_assigned_id) . '">' . staff_profile_image($staff_assigned_id, [
            //         'staff-profile-image-small',
            //     ]) . '</a><div class="hide">'.$fullname_CREATE.'</div></p>';
            $staff_assigned .= '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $staff_assigned_id) . '">' . staff_profile_image($staff_assigned_id, [
                'staff-profile-image-small',
                'mright5'
            ]) . '</a>';
        }
    }
    $staff_monitor = '';
    if (!empty($value['staff_monitor'])) {
        $fullname_CREATE = get_staff_full_name($value['staff_monitor']);
        $staff_monitor = '<span class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $value['staff_monitor']) . '">' . staff_profile_image($value['staff_monitor'], [
            'staff-profile-image-small',
        ]) . '</a><div class="hide">' . $fullname_CREATE . '</div></span>';
    }

    $isHaveTask = false;
    $taskRelLabel = '<span class="dropdown-toggle no_background label label-danger mtop10">Chưa tạo phiếu công việc</span>';
    if (!empty($value['arr_task_rel'])) {
        $isHaveTask = true;
        $taskRelLabel = '<span class="dropdown-toggle no_background label label-info mtop10">Có phiếu công việc</span>';
    }
?>
    <tr class="tr-group" style="background: #ddddddd1;" data-id="<?= ($key + 1) ?>">
        <td class="text-center td-numbers"></td>
        <td class="text-left">
            <input type="hidden" name="main_item[<?= ($key + 1) ?>][id]" value="<?= $value['id'] ?>">
            <?= render_select('main_item[' . ($key + 1) . '][category_task]', $arrCategoryTask, ['id', 'code'], '', $value['category_task_id'], ['data-id' => ($key + 1)], [], '', 'category_task') ?>
            <br>
            <?= $taskRelLabel ?>
        </td>
        <td class="text-left task_name"><?= (!empty($category_task['content']) ? $category_task['content'] : '') ?></td>
       
        <td class="text-left"><input name="main_item[<?= ($key + 1) ?>][date_start]" data-date-min-date="<?= !empty($limit_date_start) ? $limit_date_start : '' ?>" data-date-end-date="<?= !empty($limit_date_end) ? $limit_date_end : '' ?>" class="form-control datepicker width100" value="<?= !empty($value['date_start']) ? _dC($value['date_start']) : '' ?>"></td>
        <td class="text-left">
            <input name="main_item[<?= ($key + 1) ?>][date_end]" class="form-control datepicker width100" value="<?= !empty($value['date_end']) ? _dC($value['date_end']) : '' ?>">
        </td>
        <td class="text-left"><input name="main_item[<?= ($key + 1) ?>][date_tasks]" data-date-min-date="<?= !empty($limit_date_start) ? $limit_date_start : '' ?>" data-date-end-date="<?= !empty($limit_date_end) ? $limit_date_end : '' ?>" class="form-control datepicker width100" value="<?= !empty($value['date_tasks']) ? _dC($value['date_tasks']) : '' ?>"></td>
        <td class="text-left">
            <?= render_select('main_item[' . ($key + 1) . '][branch]', $arrBranch, ['id', 'name'], '', $value['branch_id']) ?>
        </td>
        <td class="text-left task_department">
            <?= (!empty($department['name']) ? $department['name'] : '') ?>
        </td>
        <td class="text-left">
            <input name="main_item[<?= ($key + 1) ?>][content]" type="text" class="form-control" value="<?= $value['content'] ?>">
        </td>
        <td>
            <input name="main_item[<?= ($key + 1) ?>][staff_assigner]" type="hidden" class="form-control" value="<?= $value['staff_assigner'] ?>"><?= $staff_assigner ?>
        </td>
        <td style="min-width: 111px !important;">
            <input name="main_item[<?= ($key + 1) ?>][staff_assigned]" type="hidden" class="form-control" value="<?= $value['staff_assigned'] ?>">
            <?= $staff_assigned ?>
        </td>
        <td>
            <input name="main_item[<?= ($key + 1) ?>][staff_monitor]" type="hidden" class="form-control" value="<?= $value['staff_monitor'] ?>"><?= $staff_monitor ?>
        </td>
        <td colspan="9" class="text-left"></td>
        <td class="text-danger text-center">
            <?php if (!$isHaveTask) { ?><i onclick="removeItemWorkPlan(this)" class="fa fa-remove" style="cursor: pointer;"></i> <?php } ?>
        </td>
    </tr>
    <?php $work_plan_item = get_table_where('tbl_work_plan_items', ['work_plan_id' => $id, 'work_plan_task_id' => $value['id']]); ?>
    <?php foreach ($work_plan_item as $key2 => $value2) {
        $staff_id = '';
        if (!empty($value2['staff_id'])) {
            $fullname_CREATE = get_staff_full_name($value2['staff_id']);
            $staff_id = '<span class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $value2['staff_id']) . '">' . staff_profile_image($value2['staff_id'], [
                'staff-profile-image-small',
            ]) . '</a><div class="hide">' . $fullname_CREATE . '</div></span>';
        }
        $process_details = $this->db->get_where('tblcategory_tasks_process', ['id' => $value2['process_id']])->row_array();
        $kpi_plus = !empty($process_details) ? $process_details['kpi_plus'] : 0;
        $kpi_minus = !empty($process_details) ? $process_details['kpi_minus'] : 0;
        $counter++; ?>
        <tr class="row-<?= ($key + 1) ?>">
            <td class="text-center "></td>
            <td colspan="11">
                <input type="hidden" name="items[<?= ($key + 1) ?>][<?= $counter ?>][category_tasks_process_name]" class="form-control" value="<?= $value2['category_tasks_process_name'] ?>">
                <input type="hidden" name="items[<?= ($key + 1) ?>][<?= $counter ?>][process]" class="form-control" value="<?= $value2['process_id'] ?>">
            </td>
            <td style="max-width: 150px !important;white-space: inherit!important;min-width: 120px!important">
                <?= $value2['category_tasks_process_name'] ?>
                <?php 
                $htmls = '';
                if (!empty($taskRel)) { ?>
                    <br>
                    <?php 
                    $this->db->where('taskid', $taskRel[0]['task_id']);
                    $this->db->where('process_id', $value2['process_id']);
	                $data_checklist_items = $this->db->get('tbltask_checklist_items')->row_array();
                    if($data_checklist_items['finished']){
                        $htmls = '<div style="border: 1px solid blue;border-radius: 5px;font-size: 10px;color: blue;width:90px;margin-left:10px"><a style="color: blue" class="c_modal" href="' . admin_url('tasks/inspection_criteria/' . $taskRel[0]['task_id'] . '/' . $data_checklist_items['id'] . '/' . (empty($data_checklist_items['process_id']) ? 1 : $data_checklist_items['process_id']) . '/' . $data_checklist_items['finished']) . '">Xem kiểm quy trình</a></div>';
                    }
                    echo $htmls;
                    ?>
                <?php } ?>
            </td>
            <td>
                <input name="items[<?= ($key + 1) ?>][<?= $counter ?>][staff_id]" type="hidden" class="form-control" value="<?= $value2['staff_id'] ?>"><?= $staff_id ?>
            </td>
            <td>
                <input type="text" name="items[<?= ($key + 1) ?>][<?= $counter ?>][week_one]" class="form-control week_one" placeholder="<?= lang('Tuần 1') ?>" value="<?= $value2['week_one'] ?>">
            </td>
            <td>
                <input type="text" name="items[<?= ($key + 1) ?>][<?= $counter ?>][week_two]" class="form-control week_two" placeholder="<?= lang('Tuần 2') ?>" value="<?= $value2['week_two'] ?>">
            </td>
            <td>
                <input type="text" name="items[<?= ($key + 1) ?>][<?= $counter ?>][week_three]" class="form-control week_three" placeholder="<?= lang('Tuần 3') ?>" value="<?= $value2['week_three'] ?>">
            </td>
            <td>
                <input type="text" name="items[<?= ($key + 1) ?>][<?= $counter ?>][week_four]" class="form-control week_four" placeholder="<?= lang('Tuần 4') ?>" value="<?= $value2['week_four'] ?>">
            </td>
            <td class="text-left">
                <input class="radio-pass" type="radio" id="past_<?= $counter ?>" name="items[<?= ($key + 1) ?>][<?= $counter ?>][pass_status]" value="1" data-kpi-plus="<?= $kpi_plus ?>" <?= (!empty($value2['pass_status']) && $value2['pass_status'] == 1 ? 'checked' : '') ?>>
                <label for="past_<?= $counter ?>">Đạt</label>
                <br>
                <input class="radio-fail" type="radio" id="fail_<?= $counter ?>" name="items[<?= ($key + 1) ?>][<?= $counter ?>][pass_status]" value="0" data-kpi-minus="<?= $kpi_minus ?>" <?= (isset($value2['pass_status']) && $value2['pass_status'] == 0 ? 'checked' : '') ?>>
                <label for="fail_<?= $counter ?>">Không đạt</label>
            </td>
            <td class="text-center">
                <?php
                $kpi_val = !empty($value2['kpi']) ? $value2['kpi'] : '';
                $prefix = '+';
                $absVal = '0';
                if ($kpi_val !== '') {
                    $firstChar = substr(trim($kpi_val), 0, 1);
                    if ($firstChar === '+' || $firstChar === '-') {
                        $prefix = $firstChar;
                        $absVal = substr(trim($kpi_val), 1);
                    } else {
                        $prefix = $value2['pass_status'] == 1 ? '+' : '-';
                        $absVal = trim($kpi_val);
                        $kpi_val = $prefix . $absVal;
                    }
                } else {
                    $prefix = $value2['pass_status'] == 1 ? '+' : '-';
                    $absVal = $value2['pass_status'] == 1 ? $kpi_plus : $kpi_minus;
                    $kpi_val = $prefix . $absVal;
                }
                ?>
                <div style="white-space: nowrap;">
                    <span class="kpi-prefix" style="font-weight: bold; margin-right: 5px; display: inline-block; vertical-align: middle; width: 12px; text-align: center;"><?= $prefix ?></span>
                    <input type="text" class="form-control kpi-num-input" value="<?= $absVal ?>" style="width: 70px; display: inline-block; text-align: center; vertical-align: middle;">
                    <input type="hidden" name="items[<?= ($key + 1) ?>][<?= $counter ?>][kpi]" class="kpi-input" value="<?= $kpi_val ?>">
                </div>
            </td>
            <td class="text-danger text-center">
                <div class="">
                    <select name="items[<?= ($key + 1) ?>][<?= $counter ?>][problem]" class="form-control selectpicker" style="">
                        <option value=""></option>
                        <option value="have_qt" <?= (($value2['problem'] == 'have_qt') ? 'selected' : '') ?>>Đã có QT</option>
                        <option value="not_qt" <?= (($value2['problem'] == 'not_qt') ? 'selected' : '') ?>>Chưa có QT</option>
                    </select>
                </div>
            </td>
            <td></td>
        </tr>
    <?php } ?>
<?php } ?>
<!-- <?= $trOne ?>
<tr class="tr-group" style="background: #ddddddd1;">
    <td class="text-center">
        <a onclick="addItemWorkPlan(this, 2)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
            </svg>
        </a>
    </td>
    <td class="text-left" colspan="8"><?= lang('HOÀN THÀNH Cập Nhật Full Thông Tin SP') ?></td>
    <td class="text-right" colspan="1"><a class="hide" href="<?= base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx') ?>"><?= lang('Download Mẫu...') ?></a></td>
    <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="2"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>

</tr>
<?= $trTwo ?>
<tr class="tr-group" style="background: #ddddddd1;">
    <td class="text-center">
        <a onclick="addItemWorkPlan(this, 3)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
            </svg>
        </a>
    </td>
    <td class="text-left" colspan="8"><?= lang('Qui Trình') ?></td>
    <td class="text-right" colspan="1"><a class="hide" href="<?= base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx') ?>"><?= lang('Download Mẫu...') ?></a></td>
    <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="3"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
</tr>
<?= $trThree ?>
<tr class="tr-group" style="background: #ddddddd1;">
    <td class="text-center">
        <a onclick="addItemWorkPlan(this, 4)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
            </svg>
        </a>
    </td>
    <td class="text-left" colspan="8"><?= lang('Sản Xuất - Chất Lượng') ?></td>
    <td class="text-right" colspan="1"><a class="hide" href="<?= base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx') ?>"><?= lang('Download Mẫu...') ?></a></td>
    <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="4"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
</tr>
<?= $trFour ?> -->
<script>
    $(document).ready(function() {
        mainItemIndex = <?= count($work_plan_task) ?>;
        counter = <?= $counter ?>;
        init_selectpicker();
        init_datepicker();
        totalWorkPlan();
        $('#content').val('<?= $work_plan['content'] ?>');
        $('#id').val('<?= $work_plan['id'] ?>');
    });
</script>