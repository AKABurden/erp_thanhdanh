<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblvouchers_coupon.code_vouchers',
    'tblvouchers_coupon.date_vouchers',
    'tblclients.company',
    'tblvouchers_coupon.arr_code_orders',
    'tblvouchers_coupon.staff',
    'tblpayment_modes.name',
    'tblvouchers_coupon.total',
    'tblvouchers_coupon.payment',
    'tblvouchers_coupon.status',
    'tblvouchers_coupon.note'
];

$sIndexColumn = 'id';
$sTable       = 'tblvouchers_coupon';

$join         = array(
    'LEFT JOIN tblclients on tblclients.userid = tblvouchers_coupon.customer',
    'LEFT JOIN tblpayment_modes on tblpayment_modes.id = tblvouchers_coupon.payment_mode',
);
$where = array();
array_push($where, 'AND tblvouchers_coupon.customer = '.$clientid);
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblvouchers_coupon.date_vouchers BETWEEN "' . to_sql_date($data_start[0]).' 00:00:00' . '" and "' . to_sql_date($data_start[1]).' 23:59:59' . '"');
    }
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblvouchers_coupon.id',
    'tblvouchers_coupon.history_status'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblvouchers_coupon.code_vouchers') {
            $_data = '<a onclick="view_vouchers_coupon('.$aRow['id'].'); return false;">'.$aRow['tblvouchers_coupon.code_vouchers'].'</a>';
            $_data .= '<div class="row-options">';
            $_data .= '<a onclick="view_vouchers_coupon('.$aRow['id'].'); return false;">'._l('view').'</a>';
            if(has_permission('vouchers_coupon', '', 'delete')) {
                $_data .= ' | <a class="text-danger" onclick="delete_vouchers_coupon('.$aRow['id'].'); return false;">'._l('delete').'</a>';
            }
            $_data .= '</div>';
        }
        else if ($aColumns[$i] == 'tblvouchers_coupon.date_vouchers') {
            $_data = _d($aRow['tblvouchers_coupon.date_vouchers']);
        }
        else if ($aColumns[$i] == 'tblvouchers_coupon.arr_code_orders') {
            $_data = '<span class="inline-block label label-warning">0 '._l('vote').'</span>';
            if($aRow['tblvouchers_coupon.arr_code_orders'] != '') {
                $_data = '<span class="inline-block label label-warning popover-menu" style="cursor: pointer;">'.count(explode(',', $aRow['tblvouchers_coupon.arr_code_orders'])).' '._l('vote');
                $_data .= '<div class="content_code_orders hide">';
                foreach (explode(',', $aRow['tblvouchers_coupon.arr_code_orders']) as $key => $value) {
                    $id_order = explode('|',$value);
                    $get_detail = get_table_where('tbl_orders',array('id'=>$id_order[0]),'','row');
                    $_data .= '<div>'.$get_detail->reference_no.' <span style="font-size: 10px;">('.number_format($id_order[1]).')</span></div>';
                }
                $_data .= '</div>';
                $_data .= '</span>';
            }
        }
        else if ($aColumns[$i] == 'tblvouchers_coupon.staff') {
            $_data = staff_profile_image($aRow['tblvouchers_coupon.staff'], array('staff-profile-image-small mright5'), 'small').get_staff_full_name($aRow['tblvouchers_coupon.staff']).'<br>';
        }
        else if ($aColumns[$i] == 'tblvouchers_coupon.total') {
            $_data = number_format($aRow['tblvouchers_coupon.total']);
        }
        else if ($aColumns[$i] == 'tblvouchers_coupon.payment') {
            $_data = number_format($aRow['tblvouchers_coupon.payment']);
        }
        else if ($aColumns[$i] == 'tblvouchers_coupon.status') {
            if($aRow['tblvouchers_coupon.status'] == 0) {
                $type = 'warning';
                $status = _l('coupon_status_dont');
            }
            else if ($aRow['tblvouchers_coupon.status'] == 1) {
                $type = 'info';
                $status = _l('coupon_status_do');
            }
            $status = '<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tblvouchers_coupon.status'].'">' . $status.'';
            $status .= '</a></span><br>';
            $_data = $status;
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
