<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns =  [
    'id',
    '(select count(rel_id) from tbltaggablesfb where tag_id = tbltagsfb.id) as count',
    'name',
    'color',
    'background_color'
];

$sIndexColumn = 'id';
$sTable       = 'tbltagsfb';
$join         = [];

$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'id'
]);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow)
{
    $row = [];
    $row[] = $aRow['id'];
    $row[] = $aRow['count'];
    $name =  '<p class="p-lable">'.$aRow['name'].'</p>';
    $name .=  '<div class="input-lable hide"><input type="text" name="name" class="form-control" value="'.$aRow['name'].'"></div>';
    $row[] = $name;

    $color      = '<p class="p-lable text-center width70px" style="background-color: '.$aRow['color'].'">'.$aRow['color'].'</p>';
    $color      .= '<div class="input-lable input-group colorpicker-input colorpicker-element hide">
                        <input type="text"  name="color" value="'.$aRow['color'].'"  class="form-control">
                        <span class="input-group-addon"><i style="background-color: '.$aRow['color'].'"></i></span>
                   </div>';
    $row[] = $color;

    $background = '<p class="p-lable text-center width70px" style="background-color: '.$aRow['background_color'].'">'.$aRow['background_color'].'</p>';
    $background .= '<div class="input-lable input-group colorpicker-input colorpicker-element hide">
                        <input type="text"  name="background_color" value="'.$aRow['background_color'].'"  class="form-control">
                        <span class="input-group-addon"><i style="background-color: '.$aRow['background_color'].'"></i></span>
                   </div>';
    $row[] = $background;

    $option = '<a class="btn btn-default btn-icon editTag p-lable" id-data="'.$aRow['id'].'" data-toggle="tooltip" data-placement="top" title="'._l('cong_edit').'">
                    <i class="fa fa-edit"></i>
                </a>';
    $option.= '<a class="btn btn-danger btn-icon p-lable deleteTr" id-data="'.$aRow['id'].'" data-toggle="tooltip" data-placement="top" title="'._l('cong_delete').'">
                    <i class="fa fa-remove"></i>
               </a>';

    $option .= '<a class="btn btn-info btn-icon input-lable UpdateTr hide" data-toggle="tooltip" data-placement="top" title="'._l('cong_save').'" id-data="'.$aRow['id'].'">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </a>';
    $option .= '<a class="btn btn-warning btn-icon input-lable hide NotUpdateTr" data-toggle="tooltip" data-placement="top" title="'._l('cong_not_save').'">
                    <i class="fa fa-repeat" aria-hidden="true"></i>
                </a>';
    $row[] = $option;
    $row['DT_RowClass'] = 'Tr-'.$aRow['id'];
    $output['aaData'][] = $row;
}
