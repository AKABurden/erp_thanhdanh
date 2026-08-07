<?php

defined('BASEPATH') or exit('No direct script access allowed');

$array_name_status = [
    _l('cong_not_send'),
    _l('cong_warting_send'),
    _l('cong_true_send'),
    _l('cong_false_send')
];
$array_color_status = [
    '#585053',
    '#c7782a',
    '#32c930',
    '#f44038'
];




$aColumns = [];
$aColumns[] = db_prefix() .'send_sms.id';
$aColumns[] = 'phone';
$aColumns[] = 'message';
$aColumns[] = 'create_by';
$aColumns[] = 'date_send';
$aColumns[] = 'status';

$sIndexColumn = 'id';
$sTable       = db_prefix().'send_sms';

$join = ['LEFT JOIN ' . db_prefix() . 'sms ON ' . db_prefix() . 'sms.id =' . db_prefix() . 'send_sms.id_sms'];
$join[] = 'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid =' . db_prefix() . 'send_sms.userid';
$join[] = 'LEFT JOIN ' . db_prefix() . 'contacts ON ' . db_prefix() . 'contacts.id =' . db_prefix() . 'send_sms.id_contact';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [
    db_prefix().'sms.datecreated',
    'brandname',
    'company',
    'concat(lastname," ",firstname) as fullname',
    db_prefix().'clients.userid',
    'id_contact']
);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[]  = $aRow[db_prefix() .'send_sms.id'];
    $row[]  = $aRow['phone'];
    $row[]  = !empty($aRow['id_contact']) ? '<p class="bg-info border-name">'.$aRow['fullname'].'</p>' : '<p class="bg-success border-name">'.$aRow['company'].'</p>';
    $row[]  = $aRow['brandname'];
    $row[]  = _dt($aRow['datecreated']);
    $row[]  = '<p style="width: 300px;">'.$aRow['message'].'</p>';

    $img    = '<a href="' . admin_url('profile/' . $aRow['create_by']) . '" data-toggle="tooltip" title="' . get_staff_full_name($aRow['create_by']) . '" class="pull-left mright5">' . staff_profile_image($aRow['create_by'], [
            'staff-profile-image-small',
        ]) . '</a>';
    $row[]  = $img;
    $row[]  = _d($aRow['date_send']);

    $string_status = '<span class="label label-default mleft5 inline-block pointer" style="border:1px solid '.$array_color_status[$aRow['status']].'">'.$array_name_status[$aRow['status']].'</span>';
    $row[]  = $string_status;

    $option = '<a class="btn btn-default btn-icon" type="button" href="'.admin_url('sms/send_sms/'.$aRow[db_prefix() .'send_sms.id']).'"><i class="fa fa-edit"></i></a>';
    $row[] = $option;

    $output['aaData'][] = $row;
}
