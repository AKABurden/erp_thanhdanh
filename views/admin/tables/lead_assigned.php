<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'staff',
    'created_by',
    'date_create'
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'lead_assigned';
$where        = [];
if(!empty($id_lead))
{
    $where[] = 'AND id_lead = '.$id_lead;
}
$filter = [];

$join[] = 'LEFT JOIN '.db_prefix().'staff cby on cby.staffid = '.db_prefix().'lead_assigned.created_by';
$join[] = 'LEFT JOIN '.db_prefix().'staff cbs on cbs.staffid = '.db_prefix().'lead_assigned.staff';
$join[] = 'LEFT JOIN '.db_prefix().'leads clead on clead.id = '.db_prefix().'lead_assigned.id_lead';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
    db_prefix().'lead_assigned.id',
    'id_lead',
    'cby.firstname as cbyfirstname',
    'cby.lastname as cbylastname',
    'cbs.firstname as cbsfirstname',
    'cbs.lastname as cbslastname',
    'clead.name as name_lead'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);
foreach ($rResult as $aRow) {
    $row = [];

    $fullname_Staff = $aRow['cbslastname'] . ' ' . $aRow['cbsfirstname'];
    $profile_Staff = '<a data-toggle="tooltip" data-title="' . $fullname_Staff . '" href="' . admin_url('profile/' . $aRow['staff']) . '">' . staff_profile_image($aRow['staff'], [
            'staff-profile-image-small',
        ]) . '</a>';
    $profile_Staff .= '<span> ' . $fullname_Staff . '</span>';
    $row[] = $profile_Staff;

    $fullname_CREATE = $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'];
    $profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['created_by']) . '">' . staff_profile_image($aRow['created_by'], [
            'staff-profile-image-small',
        ]) . '</a>';
    $profile_CREATE .= '<span> ' . $fullname_CREATE . '</span>';
    $row[] = $profile_CREATE;

    $row[] = _d($aRow['date_create']);

    $row[]   =  icon_btn('#', 'remove', 'btn-danger delete-remind', ['onclick' => 'deleteAssigned_lead('.$aRow['id'].'); return false;']) ;

    $output['aaData'][] = $row;
}
