<?php

defined('BASEPATH') or exit('No direct script access allowed');

$procedure = get_table_where(db_prefix().'procedure_client', [
    'type' => 'orders'
] ,'', 'row');

if(!empty($procedure))
{
    $this->ci->db->select(db_prefix().'procedure_client_detail.*');
    $this->ci->db->where('id_detail', $procedure->id);
    $this->ci->db->order_by('orders', 'ASC');
    $list_procedure_detail = $this->ci->db->get(db_prefix().'procedure_client_detail')->result_array();
}

$aColumns = [
    'concat(prefix, code) as fullcode',
    'client',
    'date',
    'assigned', // nhân viên phụ trách
    'date_create', // Ngày tạo
    'create_by', // nhân viên tạo
    'total_item', // tổng số sản phẩm
    'total_cost_trans', // tổng chi phí vận chuyển
    'guest_giving', // khách hàng tặng thêm
    'grand_total', //  tổng giá trị đơn hàng
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'orders';
$where        = [];

$filter = [];

if(is_numeric($this->ci->input->post('filterStatus')))
{
    $where[] = 'AND '.db_prefix().'orders.status = "'.$this->ci->input->post('filterStatus').'"';
}




$join[] = 'LEFT JOIN '.db_prefix().'staff cby on cby.staffid = '.db_prefix().'orders.create_by';
$join[] = 'LEFT JOIN '.db_prefix().'staff ss on ss.staffid = '.db_prefix().'orders.assigned';
$join[] = 'LEFT JOIN '.db_prefix().'clients c on c.userid = '.db_prefix().'orders.client';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
    'id',
    'client',
    'cby.firstname as cbyfirstname',
    'cby.lastname as cbylastname',
    'ss.firstname as ssfirstname',
    'ss.lastname as sslastname',
    'date',
    'c.company as company',
    'tblorders.status',
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
    $options .= '    <a href="'.admin_url('orders/detail/'.$aRow['id']).'" class="">'._l('edit').'</a> |';
    if($aRow['status'] > 0 || $aRow['status'] == -1)
    {
        $options .= '    <a class="text-warning pointer" onclick="restore_step('.$aRow['id'].')">'._l('cong_restore_step').'</a>|</br>';
    }
    if($aRow['status'] == -2 || $aRow['status'] == -3)
    {
        $options .= '    <a class="text-warning pointer"  onclick="restore_orders('.$aRow['id'].', '.$aRow['status'].')">'._l('cong_restore_orders').'</a>';
    }

    $options .= '    <a class="text-danger pointer" onclick="DeleteOrders('.$aRow['id'].')">'._l('delete').'</a>';
    $options .= '</div>';


    $String_status = "";
    $Alert_status = "";
    if($aRow['status'] == '-3')
    {
        $String_status = '<b class="text-danger">'._l('cong_orders_cancel').'</b>';
        $Alert_status = 'bg-danger';
    }
    else if($aRow['status'] == '-2')
    {
        $String_status = '<b class="text-danger">'._l('cong_orders_delay').'</b>';
        $Alert_status = 'bg-warning';
    }
    else if($aRow['status'] == '-1')
    {
        $String_status = '<b class="text-danger">'._l('cong_orders_success').'</b>';
        $Alert_status = 'bg-dd';
    }

    $row['DT_RowClass'] = $Alert_status;


    $row[] = '<p class="one-control pointer"><a href="'.admin_url('orders/detail/'.$aRow['id']).'">'.$aRow['fullcode'].(!empty($String_status) ? '<br/>('.$String_status.')' : '').'</a></p>'.$options;
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

    if(!empty($procedure))
    {
        $this->ci->db->select(db_prefix().'procedure_client_detail.*,tblorders_step.id_procedure,tblorders_step.date_create,tblorders_step.id_staff,tblorders_step.active');
        $this->ci->db->where('id_detail', $procedure->id);
        $this->ci->db->order_by('orders', 'ASC');
        $this->ci->db->join('tblorders_step', 'tblorders_step.id_procedure = tblprocedure_client_detail.id and id_orders = '.$aRow['id'], 'left');
        $procedure_detail = $this->ci->db->get(db_prefix().'procedure_client_detail')->result_array();
    }
    $Row_procedure_img = '<p class="text-center mw600">';
    $Row_procedure_img .= '   <ul class="progressbar_img" style="display: flex;flex-direction: row;justify-content: center;">';

    $Row_procedure = '   <ul class="progressbar" style="display: flex;flex-direction: row;justify-content: center;">';
    foreach($procedure_detail as $kDetail => $vDetail)
    {
        //(tồn tại status trong bảng step hoặc (dữ liệu chưa có trạng thái) hoặc tồn tại trạng thái trước đó) và khác đơn hàng tạm dừng và đơn hàng bị hủy hoặc kết thúc
        $active = ((!empty($vDetail['id_procedure'])
                    || ($aRow['status'] == 0 && $kDetail == 0)
                    || !empty($procedure_detail[$kDetail-1]['id_procedure']))
                && $aRow['status']!= -2
                && $aRow['status'] !=- 3
                && $aRow['status'] !=- 1)
            ? true
            : false;

        $Row_procedure .= '<li '.(!empty($vDetail['id_procedure']) ? 'class="active"' : '').'>';
        $Row_procedure .= '     <p class="pointer li_pad10 '.(!empty($active) ? 'status_orders ' : ''). (!empty($active) ? 'CRa' : '') .'" '.(!empty($active) ? ('status-procedure="'.$vDetail['id'].'" id-data = "'.$aRow['id'].'"') : '').'> '.$vDetail['name'].(!empty($vDetail['date_create']) ? ('<br/>('._dt($vDetail['date_create']).')') : '').' </p>';
        $Row_procedure .= '</li>';

        $Row_procedure_img .='<li>'.staff_profile_image($vDetail['id_staff'], ['staff-profile-image-small'],'small',[
                            'data-toggle' => 'tooltip',
                            'data-title' => !empty($vDetail['id_staff']) ? get_staff_full_name($vDetail['id_staff']) : ''
                        ]).'</li>';
    }

    // nếu đơn hàng gặp sự cố
    if($aRow['status'] == -2)
    {
        $this->ci->db->where('id_detail', $procedure->id);
        $procedure_delay = $this->ci->db->get(db_prefix().'procedure_client_detail')->row();
    }
    $Row_procedure .= '<li class="'.(!empty($procedure_delay) ? 'active' : '').' initli">';
    $Row_procedure .= '     <p class="pointer li_pad10 '. ( ($aRow['status'] != 2 && $aRow['status'] != -3) ? 'status_orders' : ($aRow['status'] != -3 ? 'CRwa' : '') ) .'" '.($aRow['status'] != -2 ? ('status-procedure="-2" id-data = "'.$aRow['id'].'"') : '').'> '._l('cong_orders_delay').(!empty($procedure_delay->date_create) ? ('('._dt($procedure_delay->date_create).')') : '').' </p>';
    $Row_procedure .= '</li>';
    $Row_procedure_img .='<li>'.staff_profile_image( (!empty($procedure_delay->id_staff) ? $procedure_delay->id_staff : ''), [
            'staff-profile-image-small'], 'small',[
            'data-toggle' => 'tooltip',
            'data-title' => !empty($procedure_delay->id_staff) ? get_staff_full_name($procedure_delay->id_staff) : ''
        ]).'</li>';
    // nếu đơn hàng hủy
    if($aRow['status'] == -3)
    {
        $this->ci->db->where('id_detail', $procedure->id);
        $procedure_cancel = $this->ci->db->get(db_prefix().'procedure_client_detail')->row();
    }
    $Row_procedure .= '<li class="'.(!empty($procedure_cancel) ? 'active' : '').' initli">';
    $Row_procedure .= '     <p class="pointer li_pad10 '. ($aRow['status'] != -3 ? 'status_orders' : 'CRwa') .'" '.($aRow['status'] != -3 ? ('status-procedure="-3" id-data = "'.$aRow['id'].'"') : '').'> '._l('cong_orders_cancel').(!empty($procedure_cancel->date_create) ? ('('._dt($procedure_cancel->date_create).')') : '').' </p>';
    $Row_procedure .= '</li>';
    $Row_procedure_img .='<li>'.staff_profile_image((!empty($procedure_cancel->id_staff) ? $procedure_cancel->id_staff : ''), [
            'staff-profile-image-small'], 'small',[
            'data-toggle' => 'tooltip',
            'data-title' => !empty($procedure_cancel->id_staff) ? get_staff_full_name($procedure_cancel->id_staff) : ''
        ]).'</li>';


    $Row_procedure .= '<div class="clearfix"></div>';
    $Row_procedure .= '</ul>';
    
    $Row_procedure_img .= '</ul>';
    $Row_procedure_img .= '</p>';

    $row[] = $Row_procedure_img.$Row_procedure;

    $row[] = '<p class="text-center">'._dt($aRow['date_create']).'</p>';
    $fullname_CREATE = $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'];
    $profile_CREATE = '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
            'staff-profile-image-small',
        ]) . '</a></p>';
    $profile_CREATE .= '<span class="text-center"><a  href="'.admin_url('staff/member/'.$aRow['assigned']).'" target="_blank">' . $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'] . '</a></span>';
    $row[] = $profile_CREATE;

    $row[] = '<p class="text-center">'.number_format($aRow['total_item']).'</p>';
    $row[] = '<p class="text-right">'.number_format($aRow['total_cost_trans']).'</p>';
    $row[] = '<p class="text-right">'.number_format($aRow['guest_giving']).'</p>';
    $row[] = '<p class="text-right">'.number_format($aRow['grand_total']).'</p>';


    $output['aaData'][] = $row;
}
//Đếm số lượng theo trạng thái
$output['total'] = [];
foreach($list_procedure_detail as $kDetail => $vDetail){
    $this->ci->db->where('status', $vDetail['id']);
    $output['total'][$vDetail['id']] = $this->ci->db->get(db_prefix().'orders')->num_rows();
}
$this->ci->db->where('status', '-1');
$output['total']['-1'] = $this->ci->db->get(db_prefix().'orders')->num_rows();

$this->ci->db->where('status', '-2');
$output['total']['-2'] = $this->ci->db->get(db_prefix().'orders')->num_rows();

$this->ci->db->where('status', '-3');
$output['total']['-3'] = $this->ci->db->get(db_prefix().'orders')->num_rows();

$this->ci->db->where('status', '0');
$output['total']['0'] = $this->ci->db->get(db_prefix().'orders')->num_rows();
