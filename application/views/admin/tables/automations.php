<?php

defined('BASEPATH') or exit('No direct script access allowed');
$GetActionAutomation = GetActionAutomation(false);
$aColumns = ['id'];
$aColumns[] = 'name';
$aColumns[] = 'action';
$aColumns[] = 'note';
$aColumns[] = 'created_by';
$aColumns[] = 'status';
$aColumns[] = 'date_create';

$sIndexColumn = 'id';
$sTable       = db_prefix().'automations';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[]  = $aRow['id'];
    $row[]  = $aRow['name'];
    $row[]  = !empty($GetActionAutomation[$aRow['action']]) ? $GetActionAutomation[$aRow['action']] : '';
    $row[]  = $aRow['note'];

    $img    = '<a href="' . admin_url('profile/' . $aRow['created_by']) . '" data-toggle="tooltip" title="' . get_staff_full_name($aRow['created_by']) . '" class="pull-left mright5">' . staff_profile_image($aRow['created_by'], [
            'staff-profile-image-small',
        ]) . '</a>';
    $row[]  = $img;

    $status = "";
    if($aRow['status'] == 2)
    {
        $status ='<span class="inline-block label label-default" style="border:1px solid #32c930">
                        <span class="label label-default mleft5 inline-block pointer" style="border:1px solid #32c930">'._l('cong_active').'</span>
                        <a class="check_status" id-data="'.$aRow['id'].'" id-status="1"><i class="fa fa-check task-icon check-danger" data-toggle="tooltip" title="'._l('cong_check_to_not_active').'"></i></a>
                 </span>';
    }
    else
    {
        $status ='<span class="inline-block label label-default" style="border:1px solid #eb0d0d">
                    <span class="label label-default mleft5 inline-block pointer" style="border:1px solid #eb0d0d">'._l('cong_not_active').'</span>
                    <a class="check_status" id-data="'.$aRow['id'].'" id-status="2"><i class="fa fa-check task-icon check-success" data-toggle="tooltip" title="'._l('cong_check_to_active').'"></i></a>
                 </span>';
    }

    $row[]  = $status;
    $row[]  = _dt($aRow['date_create']);

    $option = "";
    $option .= '<a class="btn btn-default btn-icon" type="button" href="'.admin_url('automations/detail/'.$aRow['id']).'"><i class="fa fa-edit"></i></a>';
    $option .= '<a class="btn btn-danger btn-icon pointer" onclick="deleteAutomation('.$aRow['id'].')"><i class="fa fa-remove"></i></a>';
    $row[] = $option;
    $output['aaData'][] = $row;
}
