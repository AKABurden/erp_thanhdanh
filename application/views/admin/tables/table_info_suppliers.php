<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('suppliers', '', 'delete');



$aColumns = [
    'tblsuppliers_info_group.id',
    'tblsuppliers_info_group.name',
    'tblsuppliers_info_group.color',
    '1',
    '2',
];
$sIndexColumn = 'id';
$sTable       = 'tblsuppliers_info_group';
$where        = [];
// Add blank where all filter can be stored
$views =array();
$view = $this->ci->input->post('view');
$view_childrens = $this->ci->input->post('view_children');
if(!empty($view)) {
    $views = explode(',',$view);
}
if(!empty($view_childrens)) {
    $view_children = explode(',',$view_childrens);
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
    
    $ktr = get_table_where('tblsuppliers_info_detail',array('id_suppliers_info '=>$aRow['tblsuppliers_info_group.id']),'','row');
    $style='';
    if($ktr)
    {
    if(in_array($aRow['tblsuppliers_info_group.id'], $views))
    {
    $style = '<span onclick="no_view('.$aRow['tblsuppliers_info_group.id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_close.png').'"></i></span>';    
    }else
    {
    $style = '<span onclick="view('.$aRow['tblsuppliers_info_group.id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_open.png').'"></span>';
    }
    }
    $row[] = $j.$style;
    $name  = $aRow['tblsuppliers_info_group.name'];
    $name = '<a>' . $name . '</a>';

    $row[] = $name;
    $row[] ='<span style="background: '.$aRow['tblsuppliers_info_group.color'].';">'.$aRow['tblsuppliers_info_group.color'].'</span>';
    $row[] ='Tiêu đề';
    $row[] ='';
    $_data = '<a href="#" onclick="add_evaluation_criteria_children('.$aRow['tblsuppliers_info_group.id'].',false); return false;" class="btn btn-info btn-icon " title="Thêm tiêu chí con"><i class="fa fa-plus"></i></a><a href="#" onclick="edit_evaluation_criteria('.$aRow['tblsuppliers_info_group.id'].',false); return false;" class="btn btn-default btn-icon "><i class="fa fa-edit"></i></a>';
    if(!$ktr)
    {
    $_data.='<a href="' . admin_url('suppliers/delete_info_suppliers/' . $aRow['tblsuppliers_info_group.id']) . '" class="delete-remind btn btn-danger btn-icon " delete_combo ="'.$aRow['tblsuppliers_info_group.id'].'" ><i class="fa fa-remove"></i></a>';
    }
    $row[]=$_data;
    $output['aaData'][] = $row;
    if(in_array($aRow['tblsuppliers_info_group.id'], $views))
    {
    $get_children = get_table_where('tblsuppliers_info_detail',array('id_suppliers_info'=>$aRow['tblsuppliers_info_group.id']));
    if($get_children)
    {
        foreach ($get_children as $key => $value) {
            $style='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            if($value['type_form'] == 'select' || $value['type_form'] == 'select multiple'|| $value['type_form'] == 'radio'|| $value['type_form'] == 'checkbox')
            {
                $get_childrens = get_table_where('tblsuppliers_info_detail_value',array('id_info_detail'=>$value['id']));
                if(!empty($get_childrens))
                {
                if(in_array($value['id'], $view_children))
                {
                
                $style = '<span onclick="no_view_children('.$value['id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_close.png').'"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';     
                }else
                {
                $style = '<span onclick="view_children('.$value['id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_open.png').'"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                }
            }
            $row=[];
            $row[]=$j.'.'.($key+1);
            $row[]=$style.'<span class="label label-default" style="border: 1px solid '.$aRow['tblsuppliers_info_group.color'].'">'.$value['name'].'</span>';
            $row[]='';
            $row[]=$value['type_form'];
            if($value['is_required'] == 1)
            {
             $row[]='&nbsp;&nbsp;&nbsp;X';   
            }else
            {
             $row[]='';    
            }
            $_data='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            if($value['type_form'] == 'select' || $value['type_form'] == 'select multiple'|| $value['type_form'] == 'radio'|| $value['type_form'] == 'checkbox')
            {
                $_data='<a href="#" onclick="add_suppliers_info_detail_value('.$value['id'].'); return false;" class="btn btn-info btn-icon " title="Thêm tiêu chí con"><i class="fa fa-plus"></i></a>';
            }
            $_data = $_data.'<a href="#" onclick="edit_evaluation_criteria_children('.$value['id'].',false); return false;" class="btn btn-default btn-icon "><i class="fa fa-edit"></i></a><a href="' . admin_url('suppliers/delete_info_suppliers_detail/' . $value['id']) . '" class="delete-remind btn btn-danger btn-icon " delete_combo ="'.$value['id'].'" ><i class="fa fa-remove"></i></a>';
            $row[]=$_data;
            $row[]='2';
            $output['aaData'][] = $row;
            if($value['type_form'] == 'select' || $value['type_form'] == 'select multiple'|| $value['type_form'] == 'radio'|| $value['type_form'] == 'checkbox')
            {
                if(in_array($value['id'], $view_children))
                {
                if($get_childrens)
                {
                        foreach ($get_childrens as $k => $v) {
                        $row=[];
                        $row[]=$j.'.'.($key+1).'.'.($k+1);
                        $row[]='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$v['name'].'</b>';
                        $row[]='';
                        $row[]='Giá trị';
                        
                        $row[]='';    
                        $_data='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        $_data = $_data.'<a href="#" onclick="edit_suppliers_info_detail_value('.$v['id'].',false); return false;" class="btn btn-default btn-icon "><i class="fa fa-edit"></i></a><a href="' . admin_url('suppliers/delete_info_suppliers_detail_value/' . $v['id'].'/'.$value['id']) . '" class="delete-remind btn btn-danger btn-icon " delete_combo ="'.$v['id'].'" ><i class="fa fa-remove"></i></a>';
                        $row[]=$_data;
                        $row[]='2';
                        $output['aaData'][] = $row;
                        }
                }
                }
            }
        }
    }
    }
}
