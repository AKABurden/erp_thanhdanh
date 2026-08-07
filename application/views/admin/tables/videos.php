<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$aColumns     = array(
    'tblvideos.id',
    'tblvideos.name',
    'tbl_videos_groups.name',
);
$sIndexColumn = "id";
$sTable       = 'tblvideos';
$where        = array(
//    'AND id_lead="' . $rel_id . '"'
);
$join         = array(
    'LEFT JOIN tbl_videos_groups  ON tbl_videos_groups.id=tblvideos.type',
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    // 'tblroles.name',
    // 'tblroles.roleid'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
//var_dump($rResult);die();


$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblvideos.id') {
            $_data=$j;
        }
        $row[] = $_data;
    }
    if (is_admin()) {
        $_data = '<a href="#" class="btn btn-default btn-icon" onclick="new_video(' . $aRow['tblvideos.id'] . '); return false;"><i class="fa fa-eye"></i></a>';
        $row[] =$_data.icon_btn('videos/delete/'. $aRow['tblvideos.id'] , 'remove', 'btn-danger delete-remind');
    } else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}
