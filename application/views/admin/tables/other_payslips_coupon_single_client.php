<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('other_payslips_coupon', '', 'delete');
$hasPermissionEdit = has_permission('other_payslips_coupon', '', 'edit');

$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    'tblother_payslips_coupon.code',
    'tblother_payslips_coupon.date',
    'tblother_payslips_coupon.objects',
    'tblother_payslips_coupon.objects_id',
    'tblother_payslips_coupon.type_vouchers',
    'tblother_payslips_coupon.vouchers_id',
    'tblpayment_modes.name',
    'tblother_payslips_coupon.status',
    'tblother_payslips_coupon.total',
    'tblother_payslips_coupon.staff_id',
    'tblother_payslips_coupon.note',
];
$sIndexColumn = 'id';
$sTable       = 'tblother_payslips_coupon';
$where        = [];
array_push($where, 'AND tblother_payslips_coupon.objects = 1 AND tblother_payslips_coupon.objects_id = '.$clientid);
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblother_payslips_coupon.date BETWEEN "' . to_sql_date($data_start[0]).' 00:00:00' . '" and "' . to_sql_date($data_start[1]).' 23:59:59' . '"');
    }
$join = [
    'LEFT JOIN tblpayment_modes ON tblpayment_modes.id=tblother_payslips_coupon.payment_modes',
    'LEFT JOIN tblcosts ON tblcosts.id=tblother_payslips_coupon.id_costs',
];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblother_payslips_coupon.prefix','tblother_payslips_coupon.objects_id','tblother_payslips_coupon.date_create',
    'tblother_payslips_coupon.objects_text',
    'tblother_payslips_coupon.history_status',
    'tblother_payslips_coupon.id',
]);
$output  = $result['output'];
$rResult = $result['rResult'];
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
        if ($aColumns[$i] == 'tblother_payslips_coupon.status') {
            if($aRow['tblother_payslips_coupon.status']==0)
                {
                    $type='warning';
                    $status=_l('Chưa thu');
                }
                elseif($aRow['tblother_payslips_coupon.status']==1)
                {
                    $type='info';
                    $status=_l('Đã thu');
                }
                $status='<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tblother_payslips_coupon.status'].'">' . $status.'';
                $status .= '</a>
                        </span><br>';
                $_data = $status;
        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.type_vouchers') {
            $_data ='';
            $type_vouchers[1]['name'] = _l('ch_purchase_order_ck');
            $type_vouchers[5]['name'] = _l('ch_order_ck');
            $type_vouchers[2]['name'] = _l('ch_export_other_ck');
            $type_vouchers[8]['name'] = _l('ch_return_ck');
            $type_vouchers[9]['name'] = _l('order_production_details');
            if(!empty($aRow['tblother_payslips_coupon.type_vouchers']))
            {
            $_data =$type_vouchers[$aRow['tblother_payslips_coupon.type_vouchers']]['name'];    
            }            

        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.code') {
            $payslips=$aRow['prefix'].'-'.$aRow['tblother_payslips_coupon.code'];
            $_data = $payslips;
        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.date') {
        $_data =_d($aRow['tblother_payslips_coupon.date']);
        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.objects') {
            if($aRow['tblother_payslips_coupon.objects'] == 1)
            {
            $text = 'KHÁCH HÀNG';
            }
            if($aRow['tblother_payslips_coupon.objects'] == 2)
            {
            $text = 'NHÀ CUNG CẤP';
            }
            if($aRow['tblother_payslips_coupon.objects'] == 3)
            {
            $text = 'NHÂN VIÊN';
            }
            if($aRow['tblother_payslips_coupon.objects'] == 4)
            {
            $text = 'KHÁC';
            }
        $_data =$text;
        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.objects_id') {
            $_data = '';
            if($aRow['tblother_payslips_coupon.objects'] == 2)
            {
                $supplier = get_table_where('tblsuppliers',array('id'=>$aRow['tblother_payslips_coupon.objects_id']),'','row');
                $_data = '<a href="#" onclick="int_suppliers_view('.$supplier->id.'); return false;" >'.$supplier->company.'</a>';
            }
            if($aRow['tblother_payslips_coupon.objects'] == 1)
            {
                $client = get_table_where('tblclients',array('userid'=>$aRow['tblother_payslips_coupon.objects_id']),'','row');
                $_data = $client->company;
            }
            if($aRow['tblother_payslips_coupon.objects'] == 3)
            {
                $_data = get_staff_full_name($aRow['tblother_payslips_coupon.objects_id']);
            }
            if($aRow['tblother_payslips_coupon.objects'] == 4)
            {
                $_data = $aRow['objects_text'];
            }

        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.total') {
            $_data = number_format($aRow['tblother_payslips_coupon.total']);
        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.vouchers_id') {
            $_data = '';

            if($aRow['tblother_payslips_coupon.objects'] == 1)
            {   
                if($aRow['tblother_payslips_coupon.type_vouchers'] == 5)
                {
                    if(!empty($aRow['tblother_payslips_coupon.objects_id'])&&($aRow['tblother_payslips_coupon.vouchers_id'] !=0))
                    { 
                    $import = get_table_where('tbl_orders',array('id'=>$aRow['tblother_payslips_coupon.vouchers_id']),'','row');
                    $_data = '<a href="http://localhost/01419F/admin/orders/view_order/'.$import->id.'" data-toggle="modal" data-target="#myModal">'.$import->reference_no.'</a>';
                    }
                }
            }
        }
        if ($aColumns[$i] == 'tblother_payslips_coupon.staff_id') {
            $_data = staff_profile_image($aRow['tblother_payslips_coupon.staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: '._dt($aRow['date_create'])
                        )).get_staff_full_name($aRow['tblother_payslips_coupon.staff_id']).'<br>';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}