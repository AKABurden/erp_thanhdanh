<?php
defined('BASEPATH') or exit('No direct script access allowed');


$aColumns     = array(
    'tbluv.id',
    'tbluv.hinh',
    'tbluv.name',
    'tbluv.phone',
    'tbluv.address',
    'tbldepartments.name',
    'tblroles.name'

);
$sIndexColumn = "id";
$sTable       = 'tbluv';
$where        = array(
    //    'AND id_lead="' . $rel_id . '"'
);
$join         = array(
    'LEFT JOIN tbldepartments  ON tbldepartments.departmentid=tbluv.roomID',
    'LEFT JOIN tblroles  ON tblroles.roleid=tbluv.positionID'
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    // 'tblroles.name',
    // 'tblroles.roleid'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
// var_dump($rResult);
// die();


$j = 0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblroles.roleid') {
            $_data = $aRow['tblroles.name'];
        }
        $row[] = $_data;
    }

    $_data = '<a href="#" class="btn btn-default btn-icon" onclick="view_init_department(' . $aRow['tbluv.id'] . '); return false;"><i class="fa fa-eye"></i></a>';
    $row[] = $_data . icon_btn('uv/delete_empl/' . $aRow['tbluv.id'], 'remove', 'btn-danger delete-reminder_h');

    $output['aaData'][] = $row;
}