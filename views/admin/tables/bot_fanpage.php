<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->db->where('type', 'lead');
$procedure_lead = $this->ci->db->get(db_prefix().'procedure_client')->row();

$this->ci->db->where('id_detail', $procedure_lead->id);
$this->ci->db->order_by('orders', 'asc');
$procedure_detail = $this->ci->db->get(db_prefix().'procedure_client_detail')->result_array();
$aColumns = [
    'id',
    'concat(prefix,code) as fullcode',
    'name',
    'status',
    'create_by',
    'date_create',
    'id_fanpage'
];
$sIndexColumn = 'id';
$sTable       = 'tblbot_fanpage';
$where        = [];
$join         = ['LEFT JOIN tblstaff on tblstaff.staffid = tblbot_fanpage.create_by'];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
    'lastname as cbylastname',
    'firstname as cbyfirstname'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);
foreach ($rResult as $aRow) {
    $row = [];
    $row[] = $aRow['id'];
    $row[] = $aRow['fullcode'];
    $row[] = $aRow['name'];
    $row[] = $aRow['status'];
    $fullname_CREATE = $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'];
    $profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
            'staff-profile-image-small',
        ]) . '</a>';
    $profile_CREATE .= '<span class="hide">' . $fullname_CREATE . '</span>';
    $row[] = $profile_CREATE;
    $row[] = _dt($aRow['date_create']);
    $row[] = $aRow['id_fanpage'];
    $option = '<a class="btn btn-icon btn-default" href="'.admin_url('bot_fanpage/setupmodal/'.$aRow['id']).'"><i class="fa fa-cog"></i></a>';
    $option .= '<button class="btn btn-icon btn-default" onclick="AddBotFanpage('.$aRow['id'].', this)"><i class="fa fa-edit"></i></button>';
    $option .= '<button class="btn btn-icon btn-danger"><i class="fa fa-remove"></i></button>';
    $row[] = $option;
    $output['aaData'][] = $row;
}
