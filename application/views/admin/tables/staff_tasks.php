<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns         = [
    'tbltasks.id',
    'tbltasks.name',
    'tbltasks.status',
    'tbltasks.startdate',
    'tbltasks.duedate',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'tasks.id and rel_type="task" ORDER by tag_order ASC)',
    'tbltasks.priority'
];
$sIndexColumn     = 'id';
$sTable           = 'tbltask_assigned';
$additionalSelect = [
];
$join             = [
    'JOIN tbltasks ON tbltasks.id = tbltask_assigned.taskid',
];

$where    = [];
$staff_id = get_staff_user_id();
if ($this->ci->input->post('staff_id')) {
    $staff_id = $this->ci->input->post('staff_id');
}
array_push($where, ' AND tbltask_assigned.staffid = '.$staff_id);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[ $aColumns[$i] ];
        if ($aColumns[$i] == 'tbltasks.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if($aColumns[$i] == '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'tasks.id and rel_type="task" ORDER by tag_order ASC)') {
            $_data = render_tags($aRow[$aColumns[$i]]);
        }
        else if($aColumns[$i] == 'tbltasks.startdate' || $aColumns[$i] == 'tbltasks.duedate') {
            $_data = _d($aRow[$aColumns[$i]]);
        }
        else if ($aColumns[$i] == 'tbltasks.status') {
            $status = get_task_status_by_id($aRow['tbltasks.status']);
            $_data = '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '">'.$status['name'].'</span>';
        }
        else if ($aColumns[$i] == 'tbltasks.priority') {
            $_data = '<span class="inline-block label" style="color:' . task_priority_color($aRow['tbltasks.priority']) . ';">' . task_priority($aRow['tbltasks.priority']) . '</span>';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
