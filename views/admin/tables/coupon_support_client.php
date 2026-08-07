<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblcoupon_support.id',
    'tblcoupon_support.code',
    'tblcoupon_support.appointment_date',
    '111',
    'tblcoupon_support.employees',
    'tblcoupon_support.note'
];

$sIndexColumn = 'id';
$sTable       = 'tblcoupon_support';

$join         = array(
    'LEFT JOIN tblclients on tblclients.userid = tblcoupon_support.customer_id',
    'LEFT JOIN tblprovince on tblprovince.provinceid = tblclients.city',
    'LEFT JOIN tblward on tblward.wardid = tblclients.ward',
);
$where         = array();
if(!empty($clientid))
{
    array_push($where, 'AND tblcoupon_support.customer_id = '.$clientid);
}
// if(is_numeric($this->ci->input->post('filterStatus'))){
//     array_push($where, 'AND FIND_IN_SET('.$this->ci->input->post('filterStatus').', tbl_set_prices.id_groups)');
// }
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblclients.ward',
    'tblcoupon_support.date_create',
    'tblclients.userid',
    'tblcoupon_support.method',
    'tblcoupon_support.status'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblcoupon_support.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if ($aColumns[$i] == 'tblcoupon_support.appointment_date') {
            $_data = _dt($aRow['tblcoupon_support.appointment_date']);
        }
        else if ($aColumns[$i] == 'tblcoupon_support.code') {
            $_data = $aRow['tblcoupon_support.code'];
            //check date
            $getDate = explode(" ", $aRow['tblcoupon_support.appointment_date']);
            $day_1 = $getDate[0];
            $day_2 = date('Y-m-d') ; //current date
            $days = (strtotime($day_1) - strtotime($day_2)) / (60 * 60 * 24);
            $err = '';
            if($days < 3 && $days > 0) {
                $err = '<br><span style="background: #ff7600; color: #fff; font-weight: 300; border-radius: 10px; padding: 0px 10px;">Sắp đến hạn</span>';
            }
            else if($days == 0) {
                $err = '<br><span style="background: #00a1ff; color: #fff; font-weight: 300; border-radius: 10px; padding: 0px 10px;">Đến hạn</span>';
            }
            else if($days < 0) {
                $err = '<br><span style="background: #ff0000; color: #fff; font-weight: 300; border-radius: 10px; padding: 0px 10px;">Quá hạn</span>';
            }
            //end
            if($aRow['status'] == 1) {
                $err = '<br><span style="background: #12ab00; color: #fff; font-weight: 300; border-radius: 10px; padding: 0px 10px;">Hoàn thành</span>';
            }
            $_data .= $err;
        }
        else if ($aColumns[$i] == '111') {
            if($aRow['method'] == 1) {
                $str = _l('method1');
            }
            else if($aRow['method'] == 2) {
                $str = _l('method2');
            }
            else if($aRow['method'] == 3) {
                $str = _l('method3');
            }
            $_data ='<span class="label label-warning">'.$str.'</span>';
        }
        else if ($aColumns[$i] == 'tblcoupon_support.employees') {
            $_data = staff_profile_image($aRow['tblcoupon_support.employees'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($aRow['tblcoupon_support.employees'])
                        )).get_staff_full_name($aRow['tblcoupon_support.employees']);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}