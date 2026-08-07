<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix().'procedure_client_detail.name as name',
    // 'leadtime',
    'orders'
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'procedure_client_detail';
$where        = [];
if(!empty($id_detail))
{
    if($id_detail == 4) {
        $aColumns[] = 'tblprocedure_client_detail.staff_id';
    }

    $where[] = 'AND id_detail = '.$id_detail;
}
$filter = [];

$join = ['JOIN '.db_prefix().'procedure_client on '.db_prefix().'procedure_client.id = '.db_prefix().'procedure_client_detail.id_detail'];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[db_prefix().'procedure_client_detail.id as id', 'type_object']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    if($aRow['type_object'] == 1)
    {
        $row['DT_RowClass'] = 'dragger';
    }
    $row[] = $aRow['name'];
    // $row[] = $aRow['leadtime'];
    $row[] = ($aRow['type_object'] == 1 ? $aRow['orders'] : '');

    if(!empty($id_detail))
    {
        if($id_detail == 4) {
            $str = '';
            if($aRow['tblprocedure_client_detail.staff_id'] && $aRow['tblprocedure_client_detail.staff_id'] != '') {
                $staff = explode(',', $aRow['tblprocedure_client_detail.staff_id']);
                foreach ($staff as $key => $value) {
                    $str .= '[<span class="bold">'.get_staff_full_name($value).'</span>] - ';
                }
                $str = trim($str, ' - ');
            }
            $row[] = $str;
        }
    }

    $options = "<input type='hidden' class='hidden_id' value='".$aRow['id']."'/>";
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', [
        'onclick' => 'editProcedure_client('.$aRow['id'].', this); return false;',
        'data-toggle' => 'tooltip',
        'title' => _l('cong_edit_title_from')
    ]);
    $row[] = $options;
    // $row[]   = ( $options .= icon_btn('#', 'remove', 'btn-danger delete-remind', ['onclick' => 'deleteProcedure_client('.$aRow['id'].', \'table-procadure_detail_'.$aRow['id'].'\', this); return false;']) );
    $output['aaData'][] = $row;
}
