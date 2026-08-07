<?php

defined('BASEPATH') or exit('No direct script access allowed');

// $search_date = $this->ci->input->post('search_date');

$aColumns = [
    'tbl_work_norm_group.id as id',
    'tbl_work_norm_group.code as code',
    'tbl_work_norm_group.name as name',
    'tbl_work_norm_group.task as task',
    'tblunits.unit as unit_id',
    'tbl_work_norm_group.productivity_hour as productivity_hour',
    'tbl_work_norm_group.formula as formula',
    'tbl_work_norm_group.norm as norm',
    'tbl_work_norm_group.number_execution as number_execution',
    '"" as action',
];
$sIndexColumn = 'id';
$sTable       = 'tbl_work_norm_group';
$join = [];
$join[] = 'LEFT JOIN tblunits ON tblunits.unitid = tbl_work_norm_group.unit_id';

$where = [];
// if (!empty($search_date)) {
//     $searchDate = explode(' - ', $search_date);
//     array_push($where, 'AND tblproduction_order_request.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
// }

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $rowIndex => $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $field = explode('as ', $aColumns[$i]);
        if (!empty($field[1])) {
            $field = $field[1];
        } else {
            $field = $aColumns[$i];
        }
        $_data = $aRow[$field];

        if ($field == 'id') {
            $_data = '<div class="text-center">'.($rowIndex+1).'</div>';
        } else if ($field == 'action') {
	        $_data = '<a class="btn btn-icon btn-danger _delete_row" href="'.admin_url('work_norm_group/delete/' . $aRow['id']).'"><i class="fa fa-remove"></i></a>';

        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
