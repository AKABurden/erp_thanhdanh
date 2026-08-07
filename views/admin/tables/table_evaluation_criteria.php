<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('suppliers', '', 'delete');



$aColumns = [
    'tblevaluation_criteria.id',
    'tblevaluation_criteria.name',
    'tblevaluation_criteria.color',
];
$sIndexColumn = 'id';
$sTable       = 'tblevaluation_criteria';
$where        = [];
// Add blank where all filter can be stored
$views =array();
$view = $this->ci->input->post('view');
if(!empty($view)) {
    $views = explode(',',$view);
}
$filter = [];
$join = [
];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
]);
$output  = $result['output'];
$rResult = $result['rResult'];
$j=0;
foreach ($rResult as $aRow) {
    $row = [];
    $j++;
    $ktr = get_table_where('tblevaluation_criteria_children',array('id_evaluation'=>$aRow['tblevaluation_criteria.id']),'','row');
    $style='';
    if($ktr)
    {
    if(in_array($aRow['tblevaluation_criteria.id'], $views))
    {
    $style = '<span onclick="no_view('.$aRow['tblevaluation_criteria.id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_close.png').'"></span>'; 
    }else
    {
    $style = '<span onclick="view('.$aRow['tblevaluation_criteria.id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_open.png').'"></span>';
    }
    }
    $row[] = $j.$style;

    $name  = $aRow['tblevaluation_criteria.name'];
    $name = '<a href="#" onclick="edit_evaluation_criteria('.$aRow['tblevaluation_criteria.id'].',false); return false;" >' . $name . '</a>';
    
    $row[] = $name;
    $row[] ='<span style="background: '.$aRow['tblevaluation_criteria.color'].';">'.$aRow['tblevaluation_criteria.color'].'</span>';
    $_data = '<a href="#" onclick="add_evaluation_criteria_children('.$aRow['tblevaluation_criteria.id'].',false); return false;" class="btn btn-info btn-icon " title="Thêm tiêu chí con"><i class="fa fa-plus"></i></a><a href="#" onclick="edit_evaluation_criteria('.$aRow['tblevaluation_criteria.id'].',false); return false;" class="btn btn-default btn-icon "><i class="fa fa-edit"></i></a>';
    if(!$ktr)
    {
    $_data.='<a href="' . admin_url('suppliers/delete_evaluation_criteria/' . $aRow['tblevaluation_criteria.id']) . '" class="delete-remind btn btn-danger btn-icon " delete_combo ="'.$aRow['tblevaluation_criteria.id'].'" ><i class="fa fa-remove"></i></a>';
    }
    $row[]=$_data;
    $output['aaData'][] = $row;
    if(in_array($aRow['tblevaluation_criteria.id'], $views))
    {
    $get_children = get_table_where('tblevaluation_criteria_children',array('id_evaluation'=>$aRow['tblevaluation_criteria.id']));
    if($get_children)
    {
        foreach ($get_children as $key => $value) {
            $row=[];
            $row[]=$j.'.'.($key+1);
            $row[]='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#10154;<span class="label label-default" style="border: 1px solid '.$aRow['tblevaluation_criteria.color'].'">'.$value['name_children'].'</span>';
            $row[]='';
            $_data = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="edit_evaluation_criteria_children('.$value['id'].',false); return false;" class="btn btn-default btn-icon "><i class="fa fa-edit"></i></a><a href="' . admin_url('suppliers/delete_evaluation_criteria_children/' . $value['id']) . '" class="delete-remind btn btn-danger btn-icon " delete_combo ="'.$value['id'].'" ><i class="fa fa-remove"></i></a>';
            $row[]=$_data;
            $output['aaData'][] = $row;
        }
    }
    }
}
