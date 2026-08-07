<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPermissionView = has_permission('tasks', '', 'view');
$hasPermissionViewOwn = has_permission('tasks', '', 'view_own');
$hasPermissionEdit = has_permission('tasks', '', 'edit');
$hasPermissionDelete = has_permission('tasks', '', 'delete');
$tasksPriorities = get_tasks_priorities();
$this->is_branch = true;
$aColumns = [
    '1', // bulk actions
    'tbltasks.id as id',
    //	'tbldepartments_tasks.name as name_departments_tasks',
    // '(
    // 	SELECT 
    // 	GROUP_CONCAT(tbldepartments.name) 
    // 	FROM tbltask_department 
    // 	JOIN tbldepartments ON tbldepartments.departmentid = tbltask_department.department_id
    // 	WHERE tbltask_department.task_id = tbltasks.id
    // ) as name_departments_tasks',
    '(
		SELECT 
		GROUP_CONCAT(tbl_room.name) 
		FROM tbltask_department 
		JOIN tbl_room ON tbl_room.id = tbltask_department.department_id
		WHERE tbltask_department.task_id = tbltasks.id
	) as name_departments_tasks',
    'tblcategory_tasks.code as task_code',
    'tbltasks.name as task_name',
    '1',
    '2',
    'startdate',
    'duedate',
    'addedfrom',
    get_sql_select_task_asignees_full_names(true) . ' as assignees',
    'tbltasks.status as status',
    //	'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'tasks.id and rel_type="task" ORDER by tag_order ASC) as tags',
    '(
		SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60)
		FROM tbltaskstimers 
		WHERE tbltaskstimers.task_id = tbltasks.id
	) as _minute',
    'priority',
    'tbltasks.order_by as order_by',
    '(
		SELECT GROUP_CONCAT(
			CONCAT(
				tblproduction_report.id, 
				"|||", 
				tblproduction_report.name_report,
				"|||",
				tblproduction_report.date
			) SEPARATOR ",,,"
		) 
		FROM tblproduction_report 
		WHERE tblproduction_report.id_tasks = tbltasks.id
	) as ProductionReport'
];
$sIndexColumn = 'id';
$sTable = 'tbltasks';
$where = [];
$whereTotal = [];
$whereTotal_new = [];
$join = [];
$join[] = 'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tbltasks.category_tasks';
$join[] = 'LEFT JOIN tblbranch ON tblbranch.id = tbltasks.id_branch';
$join[] = 'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbltasks.rel_id AND tbltasks.rel_type = "internal_proposal"';
$join[] = 'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id';
$join[] = 'LEFT JOIN tbl_category_recommended new_tasks ON new_tasks.id = tbltasks.category_recommended_id';
$join[] = 'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbltasks.po_id';
$join[] = 'LEFT JOIN tbl_stages ON tbl_stages.id = tbltasks.stage_id';
//$join[] = 'LEFT JOIN tbldepartments_tasks ON tbldepartments_tasks.id = tbltasks.id_list_object';

// array_push($where, ' AND tbltasks.id >= 61403');

include_once(APPPATH . 'views/admin/tables/includes/tasks_filter.php');
array_push($where, 'AND CASE WHEN rel_type="project" AND rel_id IN (SELECT project_id FROM tblproject_settings WHERE project_id=rel_id AND name="hide_tasks_on_main_tasks_table" AND value=1) THEN rel_type != "project" ELSE 1=1 END');
array_push($whereTotal, 'AND CASE WHEN rel_type="project" AND rel_id IN (SELECT project_id FROM tblproject_settings WHERE project_id=rel_id AND name="hide_tasks_on_main_tasks_table" AND value=1) THEN rel_type != "project" ELSE 1=1 END');
$custom_fields = get_table_custom_fields('tasks');
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tasks.id AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $selectAs);
}
if (has_permission('tasks', '', 'view_own') && !is_admin()) {
    $where[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN (' . get_staff_user_id() . '))';
    $whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN (' . get_staff_user_id() . '))';
    //	$join[] = 'LEFT JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id';
    //	array_push($where, 'AND  tbltask_assigned.staffid = ' . get_staff_user_id());
    //	array_push($whereTotal, 'AND  tbltask_assigned.staffid = ' . get_staff_user_id());
}
$aColumns = hooks()->apply_filters('tasks_table_sql_columns', $aColumns);
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
if ($this->ci->input->post('category_tasks_search_search')) {
    $where[] = 'AND tbltasks.category_tasks = ' . $this->ci->input->post('category_tasks_search_search');
    $whereTotal_new[] = 'AND tbltasks.category_tasks = ' . $this->ci->input->post('category_tasks_search_search');
}

if ($this->ci->input->post('task_id_search')) {
    $where[] = 'AND tbltasks.id = ' . $this->ci->input->post('task_id_search');
    $whereTotal_new[] = 'AND tbltasks.id = ' . $this->ci->input->post('task_id_search');
}

if ($this->ci->input->post('category_recommended_id_search')) {
    $where[] = 'AND (tblinternal_proposal.category_recommended_id = ' . $this->ci->input->post('category_recommended_id_search') . ' OR tbltasks.category_recommended_id = ' . $this->ci->input->post('category_recommended_id_search') . ')';
    $whereTotal_new[] = 'AND (tblinternal_proposal.category_recommended_id = ' . $this->ci->input->post('category_recommended_id_search') . ' OR tbltasks.category_recommended_id = ' . $this->ci->input->post('category_recommended_id_search') . ')';
}
if ($this->ci->input->post('suggest_id_search')) {
    $where[] = 'AND (tblinternal_proposal.suggest_id = ' . $this->ci->input->post('suggest_id_search') . ' OR tbltasks.suggest_id = ' . $this->ci->input->post('suggest_id_search') . ')';
    $whereTotal_new[] = 'AND (tblinternal_proposal.suggest_id = ' . $this->ci->input->post('suggest_id_search') . ' OR tbltasks.suggest_id = ' . $this->ci->input->post('suggest_id_search') . ')';
}
if ($this->ci->input->post('staff_follower_search')) {
    $where[] = 'AND EXISTS (SELECT 1 FROM tbltask_followers WHERE tbltask_followers.taskid = tbltasks.id AND tbltask_followers.staffid IN (' . $this->ci->input->post('staff_follower_search') . '))';
    $whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_followers WHERE tbltask_followers.taskid = tbltasks.id AND tbltask_followers.staffid IN (' . $this->ci->input->post('staff_follower_search') . '))';
}
if ($this->ci->input->post('list_staff')) {
    $where[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltasks.addedfrom != tbltask_assigned.staffid AND tbltask_assigned.staffid IN (' . $this->ci->input->post('list_staff') . '))';
    $whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltasks.addedfrom != tbltask_assigned.staffid AND tbltask_assigned.staffid IN (' . $this->ci->input->post('list_staff') . '))';
}
if ($this->ci->input->post('list_staff_create')) {
    $where[] = 'AND tbltasks.addedfrom IN (' . $this->ci->input->post('list_staff_create') . ')';
    $whereTotal[] = 'AND tbltasks.addedfrom IN (' . $this->ci->input->post('list_staff_create') . ')';
}

if ($this->ci->input->post('list_departments')) {
    $where[] = 'AND EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . $this->ci->input->post('list_departments') . '))';
    $whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . $this->ci->input->post('list_departments') . '))';
}
if ($this->ci->input->post('date_start_search')) {
    $date_start_search = $this->ci->input->post('date_start_search');
    $where[] = 'AND DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") >= "' . to_sql_date($date_start_search) . '"';
    $whereTotal[] = 'AND DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") >= "' . to_sql_date($date_start_search) . '"';
}
if ($this->ci->input->post('date_end_search')) {
    $date_end_search = $this->ci->input->post('date_end_search');
    $where[] = 'AND DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") <= "' . to_sql_date($date_end_search) . '"';
    $whereTotal[] = 'AND DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") <= "' . to_sql_date($date_end_search) . '"';
}
if ($this->ci->input->post('date_start_end_search')) {
    $date_start_end_search = $this->ci->input->post('date_start_end_search');
    $where[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") >= "' . to_sql_date($date_start_end_search) . '"';
    $whereTotal[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") >= "' . to_sql_date($date_start_end_search) . '"';
}
if ($this->ci->input->post('date_end_end_search')) {
    $date_end_end_search = $this->ci->input->post('date_end_end_search');
    $where[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") <= "' . to_sql_date($date_end_end_search) . '"';
    $whereTotal[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") <= "' . to_sql_date($date_end_end_search) . '"';
}
if ($this->ci->input->post('procedure_tasks_search')) {
    $procedure_tasks_search = $this->ci->input->post('procedure_tasks_search');
    $where[] = 'AND EXISTS (SELECT 1
	         FROM tbltask_checklist_items 
	         WHERE tbltask_checklist_items.finished = 0 AND tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.process_id = ' . $procedure_tasks_search . ')';
    $where[] = 'AND NOT EXISTS (SELECT 1
	FROM tbltask_checklist_items 
	WHERE tbltask_checklist_items.finished = 0 AND tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.process_id < ' . $procedure_tasks_search . ')';
    // $where[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") <= "' . to_sql_date($date_end_end_search) . '"';
    // $whereTotal[] = 'AND DATE_FORMAT(tbltasks.duedate, "%Y-%m-%d") <= "' . to_sql_date($date_end_end_search) . '"';
}
if ($this->ci->input->post('type_tasks_search_')) {
    $type_tasks_search = $this->ci->input->post('type_tasks_search_');
    if ($type_tasks_search == 1) {
        $where[] = 'AND repeat_every is NULL and tbltasks.is_recurring_from is NULL';
    }
    if ($type_tasks_search == 2) {
        $where[] = 'AND (repeat_every > 0 or tbltasks.is_recurring_from > 0)';
    }
}
// $this->db->where('EXISTS (SELECT tbl_calender_appointment_service.calender_appointment_id
//         FROM tbl_calender_appointment_service 
//         WHERE tbl_calender_appointment_service.status != 1 AND tbl_calender_appointment_service.calender_appointment_id = tbl_calender_appointment.id)',false,false);
if ($this->ci->input->post('filterStatus')) {
    $filterStatus = $this->ci->input->post('filterStatus');
    if (is_numeric($filterStatus)) {
        $where[] = 'AND tbltasks.status = "' . $filterStatus . '"';
    } else {
        $filterStatus = explode('_', $filterStatus);
        $filterStatus = implode(',', $filterStatus);
        $where[] = 'AND tbltasks.status IN (' . $filterStatus . ')';
    }
} else {
    //	$where[] = 'AND tbltasks.status != "5"';
}

if (!empty($this->is_branch)) {
    if (!is_admin()) {
        $list_branch = get_list_branch_staff();
        if (!empty($list_branch)) {
            $where[] = 'AND (tbltasks.id_branch IN (' . $list_branch . '))';
        } else {
            $where[] = 'AND tbltasks.id = 0';
        }
    }
}

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [
        'rel_type',
        'rel_id',
        'recurring',
        tasks_rel_name_select_query() . ' as rel_name',
        'billed',
        '(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ' LIMIT 1) as is_assigned',
        get_sql_select_task_assignees_ids(true) . ' as assignees_ids',
        '(SELECT MAX(id) FROM ' . db_prefix() . 'taskstimers WHERE task_id=' . db_prefix() . 'tasks.id and staff_id=' . get_staff_user_id() . ' and end_time IS NULL) as not_finished_timer_by_current_staff',
        '(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ' LIMIT 1) as current_user_is_assigned',
        '(SELECT CASE WHEN addedfrom=' . get_staff_user_id() . ' AND is_added_from_contact=0 THEN 1 ELSE 0 END) as current_user_is_creator',
        '(SELECT tblcategory_tasks.time FROM tblcategory_tasks WHERE tblcategory_tasks.id = tbltasks.category_tasks LIMIT 1) as minute_limit',
        'tbltasks.category_tasks',
        'tblcategory_tasks.content as content_category',
        'tblbranch.name as name_branch',
        'tbl_category_recommended.type_kpi as type_kpi',
        'tbl_category_recommended.id as id_category_recommended',
        'tbl_category_recommended.name_table as name_table',
        'tbl_category_recommended.name as name_category_recommended',
        'tblinternal_proposal.suggest_id as suggest_id',

        'new_tasks.id as id_category_recommended_task',
        'new_tasks.type_kpi as type_kpi_task',
        'new_tasks.name_table as name_table_task',
        'new_tasks.name as name_category_recommended_task',
        'tbltasks.suggest_id as suggest_id_task',
        'tbl_productions_orders.reference_no as reference_no_po',
        'tbl_productions_orders.id as po_id',
        'tbl_stages.name as name_stage',
        'tbltasks.is_recurring_from  as is_recurring_from',
        'repeat_every'
    ]
);
$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $kr => $aRow) {
    $color = '';
    if (!empty($aRow['duedate'])) {
        if ($aRow['duedate'] < date('Y-m-d 00:0:00')) {
            $color = 'color:red;';
        }
    }

    $row = [];
    //	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    $row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
    $row[] = '<a class="row-tasks-id" data-id="' . $aRow['id'] . '" href="' . admin_url('tasks/view/' . $aRow['id']) . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['id'] . '</a>';
    $rel_id = '';
    if (!empty($aRow['rel_type']) && $aRow['rel_id']) {
        $task_rel_data = get_relation_data($aRow['rel_type'], $aRow['rel_id']);
        $task_rel_value = get_relation_values($task_rel_data, $aRow['rel_type']);
        if (!empty($task_rel_value['type'])) {
            $rel_id = _l('c_tasks_' . $task_rel_value['type']) . ': ' . (!empty($task_rel_value['full_link']) ? $task_rel_value['full_link'] : ('<a target="_blank" href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>'));
        }
        if (!empty($task_rel_data->id_production_orders)) {
            $this->ci->db->where('id', $task_rel_data->id_production_orders);
            $data_productions_orders = $this->ci->db->get('tbl_productions_orders')->row();
            if (!empty($data_productions_orders)) {
                $rel_id .= '<br/>LSX: ' . $data_productions_orders->reference_no;
            }
        }
    } else {
        if (!empty($aRow['rel_type'])) {
            $rel_id = '<i class="text-warning">' . _l('c_tasks_' . $aRow['rel_type']) . '</i>';
        }
    }
    if (!empty($aRow['name_departments_tasks']) && !empty($rel_id)) {
        $rel_id = '<br/><hr class="mtop5 mbot5"/>' . $rel_id;
    }
    $row[] = '<b>' . $aRow['name_departments_tasks'] . '</b>' . $rel_id;
    $row[] = '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['task_code'] . '</a><i><br/>' . $aRow['content_category'] . '</i>' . (!empty($aRow['name_branch']) ? '<br/><i style="font-size: 11px;">Chi nhánh: ' . $aRow['name_branch'] . '</i>' : '');
    $outputName = '';
    if ($aRow['not_finished_timer_by_current_staff']) {
        $outputName .= '<span class="pull-left text-danger"><i class="fa fa-clock-o fa-fw"></i></span>';
    }
    $repeat_every = '<span class="label inline-block customer-group-list pointer" style="border-radius: 5px;background-color: red;border:1px solid;">Công việc đột xuất</span>';
    if ($aRow['repeat_every'] > 0 || $aRow['is_recurring_from'] > 0) {
        $repeat_every = '<span class="label inline-block customer-group-list pointer" style="border-radius: 5px;background-color: #1ead1a;border:1px solid;">Công việc thường xuyên</span>';
    }
    $outputName .= '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['task_name'] . '</a>' . $repeat_every;
    if ($aRow['rel_name']) {
        $relName = task_rel_name($aRow['rel_name'], $aRow['rel_id'], $aRow['rel_type']);
        $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);
        $outputName .= '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . $relName . '</a>';
    }
    if ($aRow['recurring'] == 1) {
        $outputName .= '<br /><span class="label label-primary inline-block mtop4"> ' . _l('recurring_tasks') . '</span>';
    }
    //	$this->ci->db->select('tbldepartments.*');
    //	$this->ci->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
    //	$departments = $this->ci->db->get_where('tbltask_department', ['task_id' => $aRow['id']])->result_array();

    $this->ci->db->select('tbl_room.*');
    $this->ci->db->join('tbl_room', 'tbl_room.id = tbltask_department.department_id');
    $rooms = $this->ci->db->get_where('tbltask_department', ['task_id' => $aRow['id']])->result_array();
    if (!empty($rooms)) {
        $outputName .= '<div style="margin-left: -5px;">';
        foreach ($rooms as $k => $v) {
            $outputName .= '<span class="inline-block label mleft5 mtop5" style="font-size: 9px;color:' . (!empty($color_department[$k]) ? $color_department[$k] : 'black') . ';border:1px solid ' . (!empty($color_department[$k]) ? $color_department[$k] : '') . '">' . $v['name'] . '</span>';
        }
        $outputName .= '</div>';
    }
    $outputName .= '<div class="row-options">';
    $class = 'text-success bold';
    $style = '';
    $tooltip = '';
    if ($aRow['billed'] == 1 || !$aRow['is_assigned'] || $aRow['status'] == Tasks_model::STATUS_COMPLETE) {
        $class = 'text-dark disabled';
        $style = 'style="opacity:0.6;cursor: not-allowed;"';
        if ($aRow['status'] == Tasks_model::STATUS_COMPLETE) {
            $tooltip = ' data-toggle="tooltip" data-title="' . format_task_status($aRow['status'], false, true) . '"';
        } elseif ($aRow['billed'] == 1) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_billed_cant_start_timer') . '"';
        } elseif (!$aRow['is_assigned']) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_start_timer_only_assignee') . '"';
        }
    }
    if ($aRow['not_finished_timer_by_current_staff']) {
        $outputName .= '<a href="#" class="text-danger tasks-table-stop-timer" onclick="timer_action(this,' . $aRow['id'] . ',' . $aRow['not_finished_timer_by_current_staff'] . '); return false;">' . _l('task_stop_timer') . '</a>';
    } else {
        $outputName .= '<span' . $tooltip . ' ' . $style . '>
        <a href="#" class="' . $class . ' tasks-table-start-timer" onclick="timer_action(this,' . $aRow['id'] . '); return false;">' . _l('task_start_timer') . '</a>
        </span>';
    }
    if ($hasPermissionEdit) {
        $outputName .= '<span class="text-dark"> | </span><a href="#" onclick="edit_task(' . $aRow['id'] . '); return false">' . _l('edit') . '</a>';
    }
    if ($hasPermissionDelete) {
        $outputName .= '<span class="text-dark"> | </span><a href="' . admin_url('tasks/delete_task/' . $aRow['id']) . '" class="text-danger _delete task-delete">' . _l('delete') . '</a>';
    }


    $outputName .= '</div>';
    $row[] = $outputName;
    // 'new_tasks.type_kpi as type_kpi_task',
    // 	'new_tasks.name_table as name_table_task',
    // 	'new_tasks.name as name_category_recommended_task',
    // 	'tbltasks.suggest_id as suggest_id_task',
    if (!empty($aRow['reference_no_po'])) {
        $row[] = '<div>Lệnh sản xuất</div>';
    } else {
        $row[] = (!empty($aRow['id_category_recommended']) ? ('<div>' . $aRow['name_category_recommended'] . '</div>') : (!empty($aRow['id_category_recommended_task']) ? ('<div>' . $aRow['name_category_recommended_task'] . '</div>') : ''));
    }
    $code_Suggest = '';
    if (!empty($aRow['name_table'])) {
        $dtSuggest = get_table_where($aRow['name_table'], ['id' => $aRow['suggest_id']], '', 'row_array');
        if (!empty($dtSuggest)) {
            if (!empty($dtSuggest['reference_no'])) {
                $code_Suggest = $dtSuggest['reference_no'];
            }
            if (!empty($dtSuggest['code'])) {
                $code_Suggest = $dtSuggest['code'];
            }
            $link = '';
            $name_table = explode('tbl_', $aRow['name_table']);
            if (count($name_table) > 1) {
                $link = $name_table[1];
            } else {
                $name_table_v2 = explode('tbl', $aRow['name_table']);
                if (count($name_table_v2) > 1) {
                    $link = $name_table_v2[1];
                }
            }
            $html = '</div><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a>';
            if ($aRow['type_kpi'] == 1) {
                $html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
            }
            // $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
            $code_Suggest = $html;
        }
    }
    if (!empty($aRow['name_table_task'])) {
        $dtSuggest = get_table_where($aRow['name_table_task'], ['id' => $aRow['suggest_id_task']], '', 'row_array');
        if (!empty($dtSuggest)) {
            if (!empty($dtSuggest['reference_no'])) {
                $code_Suggest = $dtSuggest['reference_no'];
            }
            if (!empty($dtSuggest['code'])) {
                $code_Suggest = $dtSuggest['code'];
            }
            $link = '';
            $name_table = explode('tbl_', $aRow['name_table_task']);
            if (count($name_table) > 1) {
                $link = $name_table[1];
            } else {
                $name_table_v2 = explode('tbl', $aRow['name_table_task']);
                if (count($name_table_v2) > 1) {
                    $link = $name_table_v2[1];
                }
            }
            $html = '</div><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a>';
            if ($aRow['type_kpi'] == 1) {
                $html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
            }
            // $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
            $code_Suggest = $html;
        }
    }
    if (!empty($aRow['reference_no_po'])) {
        $row[] = '<div><a target="_blank" href="' . base_url('admin/manufactures/detail_productions_orders/' . $aRow['po_id']) . '">' . $aRow['reference_no_po'] . '</a></div><div>CĐ: ' . $aRow['name_stage'] . '</div>';
    } else {
        $row[] = !empty($code_Suggest) ? ('<div>' . $code_Suggest . '</div>') : '';
    }
    $row[] = _d($aRow['startdate']);
    $row[] = _dt($aRow['duedate']);

    $profile_CREATE = '';
    if (!empty($aRow['addedfrom'])) {
        $fullname_CREATE = get_staff_full_name($aRow['addedfrom']);
        $profile_CREATE = '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['addedfrom']) . '">' . staff_profile_image($aRow['addedfrom'], [
            'staff-profile-image-small',
        ]) . '</a><div class="hide">' . $fullname_CREATE . '</div></p>';
    }
    $row[] = $profile_CREATE;


    $row[] = format_members_by_ids_and_names($aRow['assignees_ids'], $aRow['assignees']);
    $canChangeStatus = ($aRow['current_user_is_creator'] != '0' || $aRow['current_user_is_assigned'] || has_permission('tasks', '', 'edit'));
    $status = get_task_status_by_id($aRow['status']);
    $outputStatus = '';
    $outputStatus .= '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $aRow['status'] . '">';
    $outputStatus .= $status['name'];
    if ($canChangeStatus) {
        $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $outputStatus .= '</a>';
        $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $aRow['id'] . '">';
        foreach ($task_statuses as $taskChangeStatus) {
            if ($aRow['status'] != $taskChangeStatus['id']) {
                $outputStatus .= '<li>
                  <a href="#" onclick="task_mark_as(' . $taskChangeStatus['id'] . ',' . $aRow['id'] . '); return false;">
                     ' . _l('task_mark_as', $taskChangeStatus['name']) . '
                  </a>
               </li>';
            }
        }
        $outputStatus .= '</ul>';
        $outputStatus .= '</div>';
    }
    $outputStatus .= '</span>';
    $row[] = $outputStatus;
    //	$row[] = render_tags($aRow['tags']);
    $resultTime = '<span class="label label-default">Chưa tính giờ</span>';
    if (empty($aRow['category_tasks'])) {
        $resultTime = '<span class="label label-warning">Chưa chọn mã công việc</span>';
    } else if (!empty($aRow['_minute'])) {
        if ($aRow['_minute'] > $aRow['minute_limit']) {
            $resultTime = '<span class="label label-danger">Chưa đạt</span>';
        } elseif ($aRow['_minute'] == $aRow['minute_limit']) {
            $resultTime = '<span class="label label-success">Đạt</span>';
        } else {
            $resultTime = '<span class="label label-info">Vượt KPI</span>';
        }
    } else {
        $resultTime = '<span class="label label-default">Chưa tính giờ</span>';
    }
    if (!empty($aRow['_minute'])) {
        $resultTime .= '<br/><span class="label label-primary lableMinus"> Tổng TG thực hiện 	' . number_format_data($aRow['_minute']) . ' (Phút)' . '</span>';
    }
    $row[] = '<div class="text-center">' . $resultTime . '</div>';
    $outputPriority = '<span style="color:' . task_priority_color($aRow['priority']) . ';" class="inline-block">' . task_priority($aRow['priority']);
    if (has_permission('tasks', '', 'edit') && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $outputPriority .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $outputPriority .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskPriority-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputPriority .= '<span data-toggle="tooltip" title="' . _l('task_single_priority') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $outputPriority .= '</a>';
        $outputPriority .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskPriority-' . $aRow['id'] . '">';
        foreach ($tasksPriorities as $priority) {
            if ($aRow['priority'] != $priority['id']) {
                $outputPriority .= '<li>
                  <a href="#" onclick="task_change_priority(' . $priority['id'] . ',' . $aRow['id'] . '); return false;">
                     ' . $priority['name'] . '
                  </a>
               </li>';
            }
        }
        $outputPriority .= '</ul>';
        $outputPriority .= '</div>';
    }
    $outputPriority .= '</span>';
    $row[] = $outputPriority;
    $arrowUpTop = '';
    $arrowUp = '';
    $arrowDownBot = '';
    $arrowDown = '';

    if (!empty($kr)) {
        //		$arrowUpTop = '<a class="btn btn-info btn-icon font-10" onclick="arrow_up_tr(this)"
        //			data-order-before="' . (!empty($rResult[0]) ? $rResult[0]['order_by'] : '') . '"
        //			data-id-before="' . (!empty($rResult[0]) ? $rResult[0]['id'] : '') . '"
        //			data-id="' . $aRow['id'] . '"
        //			data-order="' . $aRow['order_by'] . '"
        //			data-original-title="Sắp xếp lên đầu danh sách"
        //			data-toggle="tooltip"
        //			><i class="fa fa-arrow-up" aria-hidden="true"></i></a>';
        $arrowUp = '<a class="btn btn-info btn-icon font-10" onclick="arrow_up_tr(this)"
			data-order-before="' . (!empty($rResult[$kr - 1]) ? $rResult[$kr - 1]['order_by'] : '') . '"
			data-id-before="' . (!empty($rResult[$kr - 1]) ? $rResult[$kr - 1]['id'] : '') . '"
			data-id="' . $aRow['id'] . '"
			data-original-title="Sắp xếp lên"
			data-toggle="tooltip"
			data-order="' . $aRow['order_by'] . '"><i class="fa fa-caret-up" aria-hidden="true"></i> Lên</a>';
    }

    if (!empty($rResult[$kr + 1])) {
        //		$arrowDownBot = '<a class="btn btn-warning btn-icon font-10" onclick="arrow_down_tr(this)"
        //			data-order-after="' . (!empty($rResult[count($rResult) - 1]) ? $rResult[count($rResult) - 1]['order_by'] : '') . '"
        //			data-id-after="' . (!empty($rResult[count($rResult) - 1]) ? $rResult[count($rResult) - 1]['id'] : '') . '"
        //			data-id="' . $aRow['id'] . '"
        //			data-original-title="Sắp xếp xuống cuối danh sách"
        //			data-toggle="tooltip"
        //			data-order="' . $aRow['order_by'] . '"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>';
        $arrowDown = '<a class="btn btn-warning btn-icon font-10" onclick="arrow_down_tr(this)"
			data-order-after="' . (!empty($rResult[$kr + 1]) ? $rResult[$kr + 1]['order_by'] : '') . '"
			data-id-after="' . (!empty($rResult[$kr + 1]) ? $rResult[$kr + 1]['id'] : '') . '"
			data-id="' . $aRow['id'] . '"
			data-original-title="Sắp xếp xuống"
			data-toggle="tooltip"
			data-order="' . $aRow['order_by'] . '"><i class="fa fa-caret-down" aria-hidden="true"></i> Xuống</a>';
    }

    $row[] = '<div class="text-center" style="width: 180px;">' . (!empty($aRow['order_by']) ? ('<span class="label label-warning inline-block mbot5">' . $aRow['order_by'] . '</span>') : '')
        . '<div>' . $arrowUpTop . $arrowDownBot . $arrowUp . $arrowDown . '</div></div>';
    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }
    $row['DT_RowClass'] = 'has-row-options tr_' . $aRow['id'];
    if ((!empty($aRow['duedate']) && strtotime($aRow['duedate']) < strtotime(date('Y-m-d H:i:s'))) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' text-danger';
    }
    $rowReport = '<a class="btn btn-info btn-icon mbot5" href="' . admin_url('production_report/detail?id_tasks=' . $aRow['id']) . '">Tạo phiếu báo cáo</a>';
    $button_rowReport = '';
    if ($aRow['ProductionReport']) {
        $divReport = '<div class="dropdown-menu dropdown-menu-right">
							<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-39" style="">';
        $ProductionReport = explode(',,,', $aRow['ProductionReport']);
        foreach ($ProductionReport as $kPro => $vPro) {
            $vPro = explode('|||', $vPro);
            $divReport .= '<li><a class="c_modal" href="' . admin_url('production_report/modal/' . $vPro[0]) . '">' . $vPro[1] . ' - ' . _dt($vPro[2]) . '</a></li>';
        }
        $divReport .= '</ul></div>';
        $button_rowReport = '<span class="dropdown-toggle no_background label label-info mtop10" type="button" data-toggle="dropdown">Đã tạo ' . count($aRow['ProductionReport']) . ' phiếu báo cáo . ' . $divReport . '</span>';
    }
    $row[] = $rowReport . '<br/>' . $button_rowReport;

    //    $this->ci->db->select('tbltask_checklist_items.*');
    //    $this->ci->db->select('(SELECT COUNT(*) FROM tbl_tasks_inspection_criteria_process WHERE tbl_tasks_inspection_criteria_process.tasks = ' . $aRow['id'] . ' AND tbl_tasks_inspection_criteria_process.process_id = tbltask_checklist_items.id) as count_process');
    //    $this->ci->db->select('(SELECT COUNT(*) FROM tbl_tasks_inspection_criteria_process WHERE tbl_tasks_inspection_criteria_process.tasks = ' . $aRow['id'] . ' AND tbl_tasks_inspection_criteria_process.process_id = tbltask_checklist_items.id AND isCheck is null AND isCheckNot is null) as count_not_process');
    //	$this->ci->db->where('tbltask_checklist_items.taskid', $aRow['id']);
    //	$data_checklist_items = $this->ci->db->get('tbltask_checklist_items')->result_array();

    $this->ci->db->select('
        tbltask_checklist_items.*,
        COUNT(p.id) as count_process,
        COUNT(CASE 
                WHEN p.isCheck IS NULL 
                 AND p.isCheckNot IS NULL 
                THEN 1 
             END) as count_not_process
        ');
    $this->ci->db->from('tbltask_checklist_items');
    $this->ci->db->join(
        'tbl_tasks_inspection_criteria_process as p',
        'p.process_id = tbltask_checklist_items.process_id 
                AND p.tasks = ' . $this->ci->db->escape($aRow['id']),
        'left'
    );
    $this->ci->db->where('tbltask_checklist_items.taskid', $aRow['id']);
    $this->ci->db->group_by('tbltask_checklist_items.id');
    $data_checklist_items = $this->ci->db->get()->result_array();

    $htmlCheckList = '<b class="text-danger">Chưa thiết lập quy trình công việc.</b>';
    if (!empty($data_checklist_items)) {
        $htmlCheckList = '';
        $rowCheckList = '';
        $rowStaffList = '';
        foreach ($data_checklist_items as $k => $v) {
            if (!empty($v['count_process'])) {
                if (!empty($v['count_not_process'])) {
                    $statusProcess = '<span class="label label-warning">Đang thực hiện</span>';
                    $v['description'] .= ' (' . ($v['count_process'] - $v['count_not_process']) . '/ ' . $v['count_process'] . ')';
                }
            }

            $date_status = '';
            if (!empty($v['date_finished'])) {
                $date_status = explode(' ', $v['date_finished'])[0] . ' 00:00:00';
            }
            $staffs = '';
            if (!empty($v['staff_id'])) {
                $FullName = get_staff_full_name($v['staff_id']);
                $staffs = '<div>' . staff_profile_image(
                    $v['staff_id'],
                    array('staff-profile-image-small mright5 img_ch'),
                    'small',
                    array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                ) . $FullName . ' </div>';
            }
            $rowStaffList .= '<li class="initli" style="list-style-type: none;width: 110px;float: left;font-size: 12px;position: relative;text-align: center;color: #7d7d7d;z-index: 0;font-size: 9px;">
                                        ' . $staffs . '
                                    </li>';
            $imgStaff = '';
            if (!empty($v['finished_from'])) {
                $imgStaff = staff_profile_image($v['finished_from'], array('staff-profile-image-small mright5 img_ch'), 'small', array(
                    'data-toggle' => 'tooltip',
                    'data-title' => get_staff_full_name($v['finished_from'])
                ));
            }
            if ($v['finished']) {
                $activeStaff = 'active';
                $full_name = get_staff_full_name($v['finished_from']);
                $date_directorate = _d($v['date_finished']);
                // $date_directorate = '';
                $color = '';
                if ($aRow['duedate'] . ' 23:59:59' < $date_status) {
                    $color = 'color:#ff9b00e3;';
                }
                $strStaffActive = '<div><a class="mright5" data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $v['finished_from']) . '">' . staff_profile_image(
                    $v['finished_from'],
                    [
                        'staff-profile-image-small-2x mbot5',
                    ]
                ) . $full_name . '</a> <br><span>' . $date_directorate . '</div>';
                $strStaffActive .= '<a style="font-size:15px;" class="" data-toggle="tooltip" title="' . lang('delete') . '" onclick="status_checklist(' . $v['id'] . ', 0,1); return false;"><i style="' . $color . '" class="wrap-icon-check text-danger fa fa-remove"></i></a>';
                $strStaffActive .= '<div style="border: 1px solid blue;border-radius: 5px;font-size: 10px;color: blue;width:90px;margin-left:10px"><a style="color: blue" class="c_modal" href="' . admin_url('tasks/inspection_criteria/' . $aRow['id'] . '/' . $v['id'] . '/' . (empty($v['process_id']) ? 1 : $v['process_id']) . '/' . $v['finished']) . '">Xem kiểm quy trình</a></div>';

                if (!empty($v['stages'])) {
                    $delivery_records_object = get_table_where('tbl_delivery_records', array('id_create' => $aRow['id'], 'type_create' => 'tasks'), '', 'row_array');
                    if (!empty($delivery_records_object)) {
                        $strStaffActive .= '<div style="border: 1px solid blue;border-radius: 5px;padding: 5px;color: blue"><a style="color: blue" class="tnh-modal" href="' . admin_url('hand_over/view/') . $delivery_records_object['id'] . '">' . $delivery_records_object['reference_no'] . '</a></div>';
                    }
                }
                $check = get_table_where('tbl_tasks_inspection_criteria_process', ['tasks' => $aRow['id'], 'process_id' => $v['process_id'], 'id_tasks_process' => $v['id'], 'isCheckNot' => 1]);
                if (!empty($check)) {
                    foreach ($check as $kvchild => $vvchild) {
                        $production_report = get_table_where('tblproduction_report', ['id_tasks' => $aRow['id'], 'id_tasks_process' => $v['id'], 'id_tasks_process_child' => $vvchild['inspection_criteria']], '', 'row_array');
                        if (!empty($production_report)) {
                            $strStaffActive .= '<div><a class="c_modal" href="' . base_url('admin/production_report/modal/') . $production_report['id'] . '">' . $production_report['reference_no'] . '</a>,</div>';
                        }
                    }
                }
            } else {
                $color = '';
                if (!empty($aRow['duedate'])) {
                    if ($aRow['duedate'] < date('Y-m-d 00:0:00')) {
                        $color = 'color:red;';
                    }
                }
                if (!empty($v['stages'])) {
                    $strStaffActive = '<a style="font-size:15px;" data-toggle="tooltip" title="' . lang('Duyệt bàn giao') . '" class="c_modal" href="' . admin_url('tasks/hand_over/' . $v['id']) . '"><i style="' . $color . '" class="wrap-icon-check fa fa-check-circle-o"></i></a>';
                } else {
                    $CheckCreateBCKPH = taskCheckCreateBCKPH($aRow['id'], $v['process_id'], $v['id']);
                    $__htmls = '';
                    if ($CheckCreateBCKPH != 1) {
                        $__htmls = '<div><span class="label label-danger">Có sự cố</span></div>';
                    }
                    // $strStaffActive = '<a style="font-size:15px;" class="" data-toggle="tooltip" title="' . lang('Duyệt') . '"onclick="status_checklist(' . $v['id'] . ', 1,1); return false;"><i style="' . $color . '" class="wrap-icon-check fa fa-check-circle-o"></i></a>';
                    $strStaffActive = '<div class="text-center" style="font-size: 18px; cursor: pointer;"><a style="font-size:15px;" data-toggle="tooltip" title="' . lang('Duyệt tiêu chí') . '" class="c_modal" href="' . admin_url('tasks/inspection_criteria/' . $aRow['id'] . '/' . $v['id'] . '/' . $v['process_id']) . '"><i style="' . $color . '" class="wrap-icon-check fa fa-check-circle-o"></i></a></div>' . $__htmls;
                }
            }
            // $rowCheckList .= '<li class="pointer ' . ($v['finished'] ? 'active' : '') . '" onclick="status_checklist(' . $v['id'] . ', ' . ($v['finished'] ? '0' : '1') . ', this)">
            // 					' . $v['description'] . '
            // 					<p class="active_poin">' . (!empty($v['finished_from']) ? ('Được ' . get_staff_full_name($v['finished_from']) . ' hoàn thành') : '') . '</p>
            // 					' . $strStaffActive . '
            // 				</li>';
            $rowCheckList .= '<li style="' . $color . '" class="pointer ' . ($v['finished'] ? 'active' : '') . '" ">
								' . $v['description'] . '
								<div class="wrap-title-process" style="">
								' . $strStaffActive . '
                                </div>
							</li>';
        }
        $htmlCheckList = '<div class="display: table; justify-content: center;">
								<ul class="progressbar" style="display: flex;">' . $rowStaffList . '</ul>
								<ul class="progressbar" style="display: flex;">' . $rowCheckList . '</ul>
						 </div>';
        $row[] = $htmlCheckList;
    } else {
        $row[] = $htmlCheckList;
    }

    $row = hooks()->apply_filters('tasks_table_row_data', $row, $aRow);
    $output['aaData'][] = $row;
}

$data = [];
if (!empty($whereTotal)) {
    $_where = implode(' ', $whereTotal);
    $_where = trim($_where);
    if (startsWith($_where, 'AND') || startsWith($_where, 'OR')) {
        if (startsWith($_where, 'OR')) {
            $_where = substr($_where, 2);
        } else {
            $_where = substr($_where, 3);
        }
    }
}
if (!empty($whereTotal_new)) {
    $_where_new = implode(' ', $whereTotal_new);
    $_where_new = trim($_where_new);
    if (startsWith($_where_new, 'AND') || startsWith($_where_new, 'OR')) {
        if (startsWith($_where_new, 'OR')) {
            $_where_new = substr($_where_new, 2);
        } else {
            $_where_new = substr($_where_new, 3);
        }
    }
}
$list_branch = get_array_branch_staff();
if (!empty($task_statuses)) {
    foreach ($task_statuses as $key => $value) {
        if (has_permission('tasks', '', 'view_own') && !is_admin()) {
            //			$this->ci->db->join('tbltask_assigned', 'tbltask_assigned.taskid = tbltasks.id', 'left');
            //			$this->ci->db->where('AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN (' . get_staff_user_id() . '))', false, false);
        }
        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                if (!empty($list_branch)) {
                    $this->ci->db->where_in('tbltasks.id_branch', $list_branch);
                } else {
                    $this->ci->db->where('tbltasks.id_branch = 0', false, false);
                }
            }
        }

        $this->ci->db->join('tblinternal_proposal', 'tblinternal_proposal.id = tbltasks.rel_id AND tbltasks.rel_type = "internal_proposal"', 'left');
        $this->ci->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tblinternal_proposal.category_recommended_id', 'left');
        $this->ci->db->where($_where, false, false);
        if (!empty($_where_new)) {
            $this->ci->db->where($_where_new, false, false);
        }
        $this->ci->db->where('tbltasks.status', $value['id']);
        $data[$value['id']] = $this->ci->db->get('tbltasks')->num_rows();
    }
}
if (has_permission('tasks', '', 'view_own') && !is_admin()) {
    //	$this->ci->db->join('tbltask_assigned', 'tbltask_assigned.taskid = tbltasks.id', 'left');
}

if (!empty($_where_new)) {
    $this->ci->db->where($_where_new, false, false);
}
// $join[] = 'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbltasks.rel_id AND tbltasks.rel_type = "internal_proposal"';
// $join[] = 'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id';
if (!empty($this->is_branch)) {
    if (!is_admin()) {
        if (!empty($list_branch)) {
            $this->ci->db->where_in('tbltasks.id_branch', $list_branch);
        } else {
            $this->ci->db->where('tbltasks.id_branch = 0', false, false);
        }
    }
}
$this->ci->db->join('tblinternal_proposal', 'tblinternal_proposal.id = tbltasks.rel_id AND tbltasks.rel_type = "internal_proposal"', 'left');
$this->ci->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tblinternal_proposal.category_recommended_id', 'left');

$this->ci->db->where($_where, false, false);
//$this->ci->db->where('status != 5', false, false);
$data['all'] = $this->ci->db->get_where('tbltasks')->num_rows();
$data['2_3'] = 0;
if (!empty($data[2])) {
    $data['2_3'] += $data[2];
}
if (!empty($data[3])) {
    $data['2_3'] += $data[3];
}
$output['total'] = $data;
