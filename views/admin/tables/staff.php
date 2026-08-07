<?php

defined('BASEPATH') or exit('No direct script access allowed');

$has_permission_delete = has_permission('staff', '', 'delete');

$custom_fields = get_custom_fields('staff', [
    'show_on_table' => 1,
]);
$aColumns = [
    'staffid',
    'code',
    'firstname',
    'email',
    '3',
    db_prefix() . 'roles.name',
    'last_login',
    'active',
    '1',
    'status_overtime',
    '2',
    'branch_salary',
];
$sIndexColumn = 'staffid';
$sTable = db_prefix() . 'staff';
$join = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];
$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join,
        'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $i . ' ON ' . db_prefix() . 'staff.staffid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}
// Fix for big queries. Some hosting have max_join_limit
$status_table = $this->ci->input->post('status_table');
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$where = hooks()->apply_filters('staff_table_sql_where', []);
if ($this->ci->input->post('fullname_search')) {
//	$where[] = 'AND CONCAT(COALESCE(firstname, "")," ",COALESCE(lastname, "")) like "%' . $this->ci->input->post('fullname_search') . '%"';
    $where[] = 'AND tblstaff.staffid = "' . $this->ci->input->post('fullname_search') . '"';
}

if ($this->ci->input->post('departments_search')) {
    $where[] = 'AND EXISTS (SELECT 1 FROM tblstaff_departments WHERE tblstaff_departments.staffid = tblstaff.staffid AND tblstaff_departments.departmentid IN (' . implode(',',
            $this->ci->input->post('departments_search')) . '))';
}

if ($this->ci->input->post('role_search')) {
    $where[] = 'AND tblstaff.role IN (' . implode(',', $this->ci->input->post('role_search')) . ')';
}
if ($status_table != 'all') {
    if ($status_table == 'status_work0') {
        $where[] = 'AND tblstaff.status_work = 0';
    } elseif ($status_table == 'status_work1') {
        $where[] = 'AND tblstaff.status_work = 1';
    } elseif ($status_table == 'status_work2') {
        $where[] = 'AND tblstaff.status_work = 2';
    }
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'check_salary',
    'profile_image',
    'lastname',
    'id_zalo',
    'id_branch',
    'status_work',
    'CONCAT(COALESCE(lastname, "")," ",COALESCE(firstname, "")) as fullname',
    '(SELECT GROUP_CONCAT(tblstaff_branch.id_branch) FROM tblstaff_branch WHERE tblstaff_branch.staffid = tblstaff.staffid) as id_branch'
]);

$branch = get_table_where('tblbranch');

$output = $result['output'];
$rResult = $result['rResult'];
$j = 0;
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'staffid') {
            $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['staffid'] . '"><label></label></div>';
        }
        if ($aColumns[$i] == 'last_login') {
            if ($_data != null) {
                $_data = '<span class="text-has-action" data-toggle="tooltip" data-title="' . _dt($_data) . '">' . time_ago($_data) . '</span>';
            } else {
                $_data = 'Never';
            }
        } elseif ($aColumns[$i] == '1') {

            $htmlStatusWork = '';
            if ($aRow['status_work'] == 0) {
                $htmlStatusWork = '<div class="label label-primary">' . lang('Thử việc') . '</div>';
            } elseif ($aRow['status_work'] == 1) {
                $htmlStatusWork = '<div class="label label-success">' . lang('Đang làm việc') . '</div>';
            } elseif ($aRow['status_work'] == 2) {
                $htmlStatusWork = '<div class="label label-danger">' . lang('Nghỉ việc') . '</div>';
            }
            $_data = $htmlStatusWork;


        } elseif ($aColumns[$i] == 'active') {
            $checked = '';
            if ($aRow['active'] == 1) {
                $checked = 'checked';
            }

            $_data = '<div class="onoffswitch">
                <input type="checkbox" ' . (($aRow['staffid'] == get_staff_user_id() || (is_admin($aRow['staffid']) || !has_permission('staff',
                            '',
                            'edit')) && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'staff/change_staff_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
                <label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
            </div>';

            // For exporting
            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
        } elseif ($aColumns[$i] == 'firstname') {
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . staff_profile_image($aRow['staffid'],
                    [
                        'staff-profile-image-small',
                    ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';

            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . _l('view') . '</a>';

            if (($has_permission_delete && ($has_permission_delete && !is_admin($aRow['staffid']))) || is_admin()) {
                if ($has_permission_delete && $output['iTotalRecords'] > 1 && $aRow['staffid'] != get_staff_user_id()) {
                    $_data .= ' | <a href="#" onclick="delete_staff_member(' . $aRow['staffid'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
                }
            }

            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'email') {
            $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
        } elseif ($aColumns[$i] == '3') {
            $str = '';
            $get_departments = get_table_where('tblstaff_departments', array('staffid' => $aRow['staffid']));
            foreach ($get_departments as $key => $value) {
                @$str .= get_table_where('tbldepartments', array('departmentid' => $value['departmentid']), '',
                        'row')->name . ', ';
            }
            $_data = trim($str, ', ');
        } elseif ($aColumns[$i] == 2) {
            $_data = '<select data-staff="' . $aRow['staffid'] . '" class="selectpicker staff_branch" multiple="1" data-actions-box="1" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">';
            $id_branch = explode(',', $aRow['id_branch']);
            foreach ($branch as $key => $value) {
                $selected = '';
                if (is_numeric(array_search($value['id'], $id_branch))) {
                    $selected = 'selected';
                }
                $_data .= '<option ' . $selected . ' value="' . $value['id'] . '">' . $value['name'] . '</option>';
            }
            $_data .= '</select>';
            $_data = '<div style="width: 200px;">' . $_data . '</div>';
        } elseif($aColumns[$i] == 'status_overtime'){
            $checked = '';
            if ($aRow['status_overtime'] == 1) {
                $checked = 'checked';
            }

            $_data = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="' . admin_url() . 'staff/changeStatusOvertime" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_new' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
                <label class="onoffswitch-label" for="c_new' . $aRow['staffid'] . '"></label>
            </div>';
        } elseif ($aColumns[$i] == 'branch_salary'){
            $_data = '<select data-staff="' . $aRow['staffid'] . '" data-placeholder="Chọn" data-live-search="true" id="branch_salary_' . $j . '" class="branch_salary modal-select2" style="width: 100%">
                    <option></option>';
            foreach ($branch as $key => $value) {
                $selected = '';
                if ($aRow['branch_salary'] == $value['id']) {
                    $selected = 'selected';
                }
                $_data .= '<option ' . $selected . ' value="' . $value['id'] . '">' . $value['name'] . '</option>';
            }
            $_data .= '</select>';
            $checked = '';
            if ($aRow['check_salary'] == 1) {
                $checked = 'checked';
            }
            $_data .= '<div class="form-group">
                            <label class="col-sm-12 checkbox-inline" style="margin-left:unset;margin-top: 10px">
                                <input class="check_salary" data-id ="'.$aRow['staffid'].'" name="check_salary" '.$checked.' type="checkbox" value="1">Không tính lương</label>
                        </div>';
            $j++;
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }
        $row[] = $_data;
    }

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
