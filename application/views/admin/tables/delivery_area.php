<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$aColumns     = array(
    'tbldelivery_area.id',
    'tbldelivery_area.code',
    'tblprovince.name',
    '3',
    'tbldelivery_area.note',
    
);
$sIndexColumn = "id";
$sTable       = 'tbldelivery_area';
$where        = array(
//    'AND id_lead="' . $rel_id . '"'
);
$join         = array(
    // 'LEFT JOIN tblroles  ON tblroles.roleid=tbldepartment.id_role'
);

$join[] = 'LEFT JOIN tblprovince on tblprovince.provinceid = tbldelivery_area.city';
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
         if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == '3') {
            $_data='';
            $delivery_area_detail = get_table_where('delivery_area_detail',array('id_delivery_area'=>$aRow['tbldelivery_area.id']));
            foreach ($delivery_area_detail as $key => $value) {
                $name = get_table_where('tbldistrict',array('districtid'=>$value['id_district']),'','row');
                if(!empty($name))
                {
                    $_data .= $name->name.',<br>';
                }
                
            }
            $_data = trim($_data,',<br>');
        }
    $row[] = $_data;

    }
//    if (is_admin()) {
        $_data = '<a href="#" class="btn btn-default btn-icon" onclick="view_init_department(' . $aRow['tbldelivery_area.id'] . '); return false;"><i class="fa fa-eye"></i></a>';
        $row[] =$_data.icon_btn('delivery_area/delete_delivery_area/'. $aRow['tbldelivery_area.id'] , 'remove', 'btn-danger delete-reminds');
//    } else {
//        $row[] = '';
//    }
  
    $output['aaData'][] = $row;
}
