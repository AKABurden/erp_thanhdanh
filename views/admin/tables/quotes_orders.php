<?php

defined('BASEPATH') or exit('No direct script access allowed');

$procedure = get_table_where(db_prefix().'procedure_client', [
    'type' => 'orders'
] ,'', 'row');

$aColumns = [
    'concat(prefix, code) as fullcode',
    'client',
    'date',
    'assigned', // nhân viên phụ trách
    'date_create', // Ngày tạo
    'create_by', // nhân viên tạo
    'total_item', // tổng số sản phẩm
    'total_cost_trans', // tổng chi phí vận chuyển
    'grand_total', //  tổng giá trị đơn hàng
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'quotes_orders';
$where        = [];

$filter = [];

if(is_numeric($this->ci->input->post('filterStatus')))
{
    $where[] = 'AND '.db_prefix().'quotes_orders.status = "'.$this->ci->input->post('filterStatus').'"';
}




$join[] = 'LEFT JOIN '.db_prefix().'staff cby on cby.staffid = '.db_prefix().'quotes_orders.create_by';
$join[] = 'LEFT JOIN '.db_prefix().'staff ss on ss.staffid = '.db_prefix().'quotes_orders.assigned';
$join[] = 'JOIN '.db_prefix().'clients c on c.userid = '.db_prefix().'quotes_orders.client';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
    'id',
    'client',
    'cby.firstname as cbyfirstname',
    'cby.lastname as cbylastname',
    'ss.firstname as ssfirstname',
    'ss.lastname as sslastname',
    'date',
    'c.company as company',
    'tblquotes_orders.status',
    'concat(c.prefix_client, c.code_client) as fullcode_client'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);
foreach ($rResult as $aRow) {
    $row = [];
    $options = '<div class="row-options">';
    $options .= '    <a onclick="initOrders('.$aRow['id'].')">'._l('view').'</a> |';
    $options .= '    <a href="'.admin_url('quotes_orders/detail/'.$aRow['id']).'" class="">'._l('edit').'</a> |';
    if($aRow['status'] == 2)
    {
        $options .= '    <a class="text-warning pointer"  onclick="restore_orders('.$aRow['id'].', '.$aRow['status'].')">'._l('cong_restore_orders').'</a> |';
    }
    if($aRow['status'] == 0)
    {
        $options .= '    <a href="'.admin_url('orders/detail?convert_quotes='.$aRow['id']).'" class="text-success pointer">'._l('cong_convert_orders_to_quotes').'</a> |';
        $options .= '    <a class="text-warning pointer" onclick="CancelOrders('.$aRow['id'].')">'._l('cong_cancel').'</a> |';
    }
    $options .= '    <a class="text-danger pointer" onclick="DeleteOrders('.$aRow['id'].')">'._l('delete').'</a>';
    $options .= '</div>';


    $String_status = "";
    $Alert_status = "";
    $lable_status = '<span class="label label-default mleft5 inline-block" style="border:1px solid #0bd5fa">'._l('cong_new_create').'</span>';
    if($aRow['status'] == '2')
    {
        $String_status = '<b class="text-danger">'._l('cong_orders_cancel').'</b>';
        $Alert_status = 'bg-danger';
        $lable_status = '<span class="label label-default mleft5 inline-block" style="border:1px solid #fa0b0b">'._l('cong_have_cancel_quotes_orders').'</span>';
    }
    else if($aRow['status'] == '1')
    {
        $String_status = '<b class="text-danger">'._l('cong_have_create_orders').'</b>';
        $Alert_status = 'bg-dd';
        $lable_status = '<span class="label label-default mleft5 inline-block" style="border:1px solid #1cee09">'._l('cong_have_create_orders').'</span>';
    }

    $row['DT_RowClass'] = $Alert_status;


    $row[] = '<p class="one-control pointer"><a href="'.admin_url('quotes_orders/detail/'.$aRow['id']).'">'.$aRow['fullcode'].(!empty($String_status) ? '<br/>('.$String_status.')' : '').'</a></p>'.$options;
    $row[] = '<a class="pointer" href="'.admin_url('clients/client/'.$aRow['client']).'" target="_blank">'.$aRow['company'].(!empty($aRow['fullcode_client']) ? '<br/>('.$aRow['fullcode_client'].')' : '' ).'</a>';
    $row[] = '<p class="text-center">'._d($aRow['date']).'</p>';

    // Nhân viên hoàn thành chăm sóc
    $profile_assigned = "";
    if(!empty($aRow['assigned']))
    {
        $profile_assigned = $aRow['sslastname'] . ' ' . $aRow['ssfirstname'];
        $profile_assigned = '<p class="text-center"><a data-toggle="tooltip" data-title="' . $profile_assigned . '" href="' . admin_url('profile/' . $aRow['assigned']) . '">' . staff_profile_image($aRow['assigned'], [
                'staff-profile-image-small',
            ]) . '</a></p>';
        $profile_assigned .= '<p class="text-center"><a href="'.admin_url('staff/member/'.$aRow['assigned']).'" target="_blank">' . $aRow['sslastname'] . ' ' . $aRow['ssfirstname'] . '</a></p>';
    }
    $row[] = $profile_assigned;

    $row[] = $lable_status;

    $row[] = '<p class="text-center">'._dt($aRow['date_create']).'</p>';
    $fullname_CREATE = $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'];
    $profile_CREATE = '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
            'staff-profile-image-small',
        ]) . '</a></p>';
    $profile_CREATE .= '<span class="text-center"><a  href="'.admin_url('staff/member/'.$aRow['assigned']).'" target="_blank">' . $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'] . '</a></span>';
    $row[] = $profile_CREATE;

    $row[] = '<p class="text-center">'.number_format($aRow['total_item']).'</p>';
    $row[] = '<p class="text-right">'.number_format($aRow['total_cost_trans']).'</p>';
    $row[] = '<p class="text-right">'.number_format($aRow['grand_total']).'</p>';

    $output['aaData'][] = $row;
}
//Đếm số lượng theo trạng thái
$output['total'] = [];
$this->ci->db->where('status', '0');
$output['total']['0'] = $this->ci->db->get(db_prefix().'quotes_orders')->num_rows();

$this->ci->db->where('status', '1');
$output['total']['1'] = $this->ci->db->get(db_prefix().'quotes_orders')->num_rows();

$this->ci->db->where('status', '2');
$output['total']['2'] = $this->ci->db->get(db_prefix().'quotes_orders')->num_rows();
