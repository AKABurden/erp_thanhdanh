<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hasPermissionDelete = has_permission('pay_slip', '', 'delete');

$aColumns     = array(
    'tbldiscount.id',
    'tbldiscount.code',
    'tbldiscount.date',
    'tbldiscount.name_discount',
    'tbldiscount.type',
    'tbldiscount.type_client',
    'tbldiscount.date_start',
    'tbldiscount.staff_create',
    '1',
    'tbldiscount.status',
    '999',
);
$sIndexColumn = "id";
$sTable       = 'tbldiscount';
$where        = array(
  
);
    if ($this->ci->input->post('filterStatus')) {
        if(is_numeric($this->ci->input->post('filterStatus'))) {
            if($this->ci->input->post('filterStatus') == 1) {
                array_push($where, 'AND tbldiscount.type = 1');
            } else if($this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tbldiscount.type = 2');
            }
        }
    }
$join         = array(
    // 'LEFT JOIN tblwarehouse waid on waid.id = tbldiscount.warehouse_id',
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array('tbldiscount.prefix','date_create','history_status','tbldiscount.date_end','apply'
));
$output       = $result['output'];
$rResult      = $result['rResult'];

$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tbldiscount.id') {
        $_data ='<div class="text-center">'.$j.'</div>';
        }
        if ($aColumns[$i] == 'tbldiscount.code') {
        $_data ='<div class="text-center">'.$aRow['prefix'].'-'.$aRow['tbldiscount.code'].'</div>';
            $transfer  = $aRow['prefix'].$aRow['tbldiscount.code'];
            $transfer = '<a  >' . $transfer . '</a>';
            $transfer .= '<div class="row-options">';
            
            if($aRow['tbldiscount.type'] == 1){
            $transfer .= '<a href="#" onclick="view_discount('.$aRow['tbldiscount.id'].'); return false;" >' . _l('view') . '</a>';
            $transfer .= ' | <a href="' . admin_url('discount/trade/' . $aRow['tbldiscount.id']) . '" >' . _l('edit') . '</a>';     
            }
            if($aRow['tbldiscount.type'] == 2){
            $transfer .= '<a href="#" onclick="view_discount_payment('.$aRow['tbldiscount.id'].'); return false;" >' . _l('view') . '</a>';
            $transfer .= ' | <a href="' . admin_url('discount/payment/' . $aRow['tbldiscount.id']) . '" >' . _l('edit') . '</a>';     
            }  
            if ($hasPermissionDelete) {
                $transfer .= ' | <a href="' . admin_url('discount/delete/' . $aRow['tbldiscount.id']) . '" class="text-danger delete-remind">' . _l('delete') . '</a>';
            }   
            $transfer .= '</div>';
        $_data=$transfer;
        }
        if ($aColumns[$i] == 'tbldiscount.date') {
            $_data = _d($aRow['tbldiscount.date']);
        } 
        if ($aColumns[$i] == 'tbldiscount.date_start') {
            $_data = _d($aRow['tbldiscount.date_start']).' - '._d($aRow['date_end']);
        } 
        if ($aColumns[$i] == 'tbldiscount.type') {
            if($aRow['tbldiscount.type'] == 1)
            {
            $_data = '<span class="inline-block label label-warning text-center" >Thương mại</span>';    
            }else
            {
            $_data = '<span class="inline-block label label-info text-center" >Thanh toán</span>';   
            }
        } 
        if ($aColumns[$i] == '1') {
            if(($aRow['tbldiscount.status'] == 1)&&($aRow['apply'] == 1)){ 
            if($j%2 == 0)
            {
            $_data = '<span class="inline-block label label-warning text-center" >Chưa có đơn hàng</span>';    
            }else
            {
             
            $_data = '<a onclick="view_sales_discount('.$aRow['tbldiscount.id'].'); return false;"><span  class="inline-block label label-info text-center" >1 đơn hàng</span></a>'; 
            }  
            }else
            {
             $_data ='';   
            }
        }
        if ($aColumns[$i] == 'tbldiscount.type_client') {
            $get = explode(',', trim($aRow['tbldiscount.type_client']));
            $this->ci->db->select('name');
            $this->ci->db->where_in('id',$get);
            $type_client = $this->ci->db->get('tblcustomers_groups')->result_array();
            $_data='';
                foreach ($type_client as $key => $value) {
                  $_data.=$value['name'].',<br>';
                }
        }
        if ($aColumns[$i] == 'tbldiscount.status') {
            if($aRow['tbldiscount.status']==0)
                {
                    $type='warning';
                    $status=_l('dont_approve');
                }
                elseif($aRow['tbldiscount.status']==1)
                {
                    $type='info';
                    $status=_l('ch_confirm_22');
                }
            $status='<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tbldiscount.status'].'">' . $status.'';
            if(has_permission('pay_slip', '', 'view') && has_permission('pay_slip', '', 'view_own'))
            {
                if($aRow['tbldiscount.status']==0) {
                    $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tbldiscount.status'] . ',' . $aRow['tbldiscount.id'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
                }
                else
                {
                    $status .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
                }
            }
                $status .= '</a>
                        </span><br>';
                $__data='';
                $history_status = explode('|',$aRow['history_status']);

                foreach ($history_status as $key => $value) {
                    $data=explode(',',$value);
                    if(is_numeric($data[0]))
                    {
                    $__data.=staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                                    'data-toggle' => 'tooltip',
                                    'data-title' => ' Vào lúc: '._dt($data[1])
                                )).get_staff_full_name($data[0]).'<br>';
                    }
                }

                $_data = $status.$__data;
        }
        if ($aColumns[$i] == 'tbldiscount.staff_create') {
        $_data=staff_profile_image($aRow['tbldiscount.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: '._dt($aRow['date_create'])
                        )).get_staff_full_name($aRow['tbldiscount.staff_create']).'<br>';
        }
        if ($aColumns[$i] == '999') {
            $_data = '';
            if($aRow['tbldiscount.status'] == 1) {
                $_data = '<button class="btn btn-info apply-discount" data-id="'.$aRow['tbldiscount.id'].'">'._l('apply').' <i class="fa fa-spinner fa-spin wap-load hide"></i></button>';
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
