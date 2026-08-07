<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblcoupon_support.id',
    'tblcoupon_support.code',
    'tblclients.company',
    'tblcoupon_support.appointment_date',
    '111',
    '222',
    '333',
    'tblcoupon_support.employees',
    'tblcoupon_support.note',
    '8'
];

$sIndexColumn = 'id';
$sTable       = 'tblcoupon_support';

$join         = array(
    'LEFT JOIN tblclients on tblclients.userid = tblcoupon_support.customer_id',
    'LEFT JOIN tblprovince on tblprovince.provinceid = tblclients.city',
    'LEFT JOIN tblward on tblward.wardid = tblclients.ward',
);
$where         = array();
if(is_numeric($this->ci->input->post('filterStatus'))){
    if($this->ci->input->post('filterStatus') == 1) {
        array_push($where, 'AND tblcoupon_support.id IN (SELECT id FROM tblcoupon_support WHERE DATEDIFF(appointment_date, CURDATE()) > 0 AND DATEDIFF(appointment_date, CURDATE()) < 3 AND status is null)');
    }
    else if($this->ci->input->post('filterStatus') == 2) {
        array_push($where, 'AND tblcoupon_support.id IN (SELECT id FROM tblcoupon_support WHERE DATEDIFF(appointment_date, CURDATE()) = 0 AND status is null)');
    }
    else if($this->ci->input->post('filterStatus') == 3) {
        array_push($where, 'AND tblcoupon_support.id IN (SELECT id FROM tblcoupon_support WHERE DATEDIFF(appointment_date, CURDATE()) < 0 AND status is null)');
    }
    else if($this->ci->input->post('filterStatus') == 4) {
        array_push($where, 'AND tblcoupon_support.status = 1');
    }
}
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
            $_data = '<a onclick="edit('.$aRow['tblcoupon_support.id'].');return false;">'.$aRow['tblcoupon_support.code'].'</a>';
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
        else if ($aColumns[$i] == 'tblclients.company') {
            $_data = '<a href="'.admin_url('clients/client/'.$aRow['userid'].'?view').'" target="_blank">'.$aRow['tblclients.company'].'</a>';
        }
        else if ($aColumns[$i] == '111') {
            $checked = '';
            if($aRow['method'] == 1) {
                $checked = 'checked';
            }
            $_data ='<div class="radio radio-primary">
                        <input type="radio" class="checkbox_method" name="checkbox_method'.$r.'" value="1" '.$checked.' data-id="'.$aRow['tblcoupon_support.id'].'">
                        <label for="checkbox_method"></label>
                    </div>';
        }
        else if ($aColumns[$i] == '222') {
            $checked = '';
            if($aRow['method'] == 2) {
                $checked = 'checked';
            }
            $_data ='<div class="radio radio-primary">
                        <input type="radio" class="checkbox_method" name="checkbox_method'.$r.'" value="2" '.$checked.' data-id="'.$aRow['tblcoupon_support.id'].'">
                        <label for="checkbox_method"></label>
                    </div>';
        }
        else if ($aColumns[$i] == '333') {
            $checked = '';
            if($aRow['method'] == 3) {
                $checked = 'checked';
            }
            $_data ='<div class="radio radio-primary">
                        <input type="radio" class="checkbox_method" name="checkbox_method'.$r.'" value="3" '.$checked.' data-id="'.$aRow['tblcoupon_support.id'].'">
                        <label for="checkbox_method"></label>
                    </div>';
        }
        else if ($aColumns[$i] == 'tblcoupon_support.employees') {
            $_data = staff_profile_image($aRow['tblcoupon_support.employees'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($aRow['tblcoupon_support.employees'])
                        )).get_staff_full_name($aRow['tblcoupon_support.employees']);
        }
        else if ($aColumns[$i] == '8') {
            $_data = '';
            $_outputStatus = '<div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu h_right">';
            $_outputStatus .=       '<li><a onclick="edit('.$aRow['tblcoupon_support.id'].');return false;"><i class="fa fa-edit"></i> '._l('edit').'</a></li>';
            if($aRow['status'] != 1) {
                $_outputStatus .=   '<li><a onclick="change_status('.$aRow['tblcoupon_support.id'].');return false;"><i class="fa fa-check"></i> '._l('finished').'</a></li>';
            }
            $_outputStatus .=       '<li><a onclick="delete_coupon_support('.$aRow['tblcoupon_support.id'].');return false;" class="delete-remind"><i class="fa fa-remove"></i> '._l('delete').'</a></li>';
            $_outputStatus .=   '</ul>
                            </div>';
            $_data = $_outputStatus;
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}