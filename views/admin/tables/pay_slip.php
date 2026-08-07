<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hasPermissionDelete = has_permission('pay_slip', '', 'delete');

$aColumns     = array(
    'tblpay_slip.day_vouchers',
    'tblpay_slip.code',
    // 'tblpay_slip.type',
    'tblpay_slip.id_old',
    'tblsuppliers.company',
    // 'tblpay_slip.total',
    'tblpay_slip.payment',
    'tblpay_slip.staff_id',
    'tblpayment_modes.name',
    'tblpay_slip.note',
);
$sIndexColumn = "id";
$sTable       = 'tblpay_slip';
$where        = array(
  
);
if ($this->ci->input->post('filterStatus')) {
    if(is_numeric($this->ci->input->post('filterStatus'))) {
        if($this->ci->input->post('filterStatus') == 1) {
            array_push($where, 'AND tblpay_slip.type = 1');
        } else if($this->ci->input->post('filterStatus') == 2) {
            array_push($where, 'AND tblpay_slip.type = 2');
        } else if($this->ci->input->post('filterStatus') == 3) {
            array_push($where, 'AND tblpay_slip.status = 1');
        } else if($this->ci->input->post('filterStatus') == 4) {
            array_push($where, 'AND tblpay_slip.status = 0');
        }else if($this->ci->input->post('filterStatus') == 5) {
            array_push($where, 'AND tblpay_slip.type = 5');
        }
    }
}
// sum note
$suppliers_id = $this->ci->input->post('suppliers_id');
if(is_numeric($suppliers_id))
{
    array_push($where, 'AND tblpay_slip.id_supplierss = '.$this->ci->input->post('suppliers_id'));
}
$start_date_search = $this->ci->input->post('start_date_search');
if(!empty($start_date_search))
{
    array_push($where, 'AND tblpay_slip.date >= "'.to_sql_date($this->ci->input->post('start_date_search')).'"');
}

$end_date_search = $this->ci->input->post('end_date_search');
if(!empty($end_date_search))
{
    array_push($where, 'AND tblpay_slip.date <= "'.to_sql_date($this->ci->input->post('end_date_search')).'"');
}
// ./sum note
$join         = array(
    'LEFT JOIN tblsuppliers ON tblsuppliers.id=tblpay_slip.id_supplierss',
    'LEFT JOIN tblpayment_modes ON tblpayment_modes.id=tblpay_slip.payment_mode',
);
    if(has_permission('pay_slip','','view_own')&&!is_admin())
    {
         array_push($where, 'AND  tblpay_slip.staff_id = '.get_staff_user_id());
    }
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    'tblpay_slip.id',
    'tblpay_slip.prefix',
    'date',
    'history_status',
    'tblpay_slip.id_supplierss'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
$j=0;
$footer_data = array(
    'total' => 0,
    'pay' => 0,
);
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblpay_slip.code') {
        $_data ='<div class="text-center">'.$aRow['prefix'].'-'.$aRow['tblpay_slip.code'].'</div>';
            $pay_slip  = $aRow['prefix'].'-'.$aRow['tblpay_slip.code'];
            $pay_slip = '<a href="#" onclick="view_pay_slip('.$aRow['id'].'); return false;" >' . $pay_slip . '</a>';
            $_data=$pay_slip;
        }
        if ($aColumns[$i] == 'tblpay_slip.type') {
            $_data = format_status_invoice($aRow['tblpay_slip.type']);
        }
        if ($aColumns[$i] == 'tblsuppliers.company') {
            $_data = '<a href="#" onclick="int_suppliers_view('.$aRow['id_supplierss'].'); return false;">' . $aRow['tblsuppliers.company'] . '</a>';
        }
        if ($aColumns[$i] == 'tblpay_slip.id_old') {
        $_data='';
        // if($aRow['tblpay_slip.type'] == 1)
        // {
        // $id_invoice = explode(',', $aRow['tblpay_slip.id_old']);
        // $count = count($id_invoice);
        // if($count == 1)
        // {
        // $invoice = get_table_where('tblpurchase_invoice',array('id'=>$id_invoice[0]),'','row');
        // $_data='<div class="text-center">'.$invoice->code_invoice.'</div>';
        // }else
        // {
        //     $_data = '<div class="dropdown" style="text-align: center;">
        //                 <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.format_status_number_invoices_ch($count).'
        //                 </button>';
        //     $__data='';
        //     foreach ($id_invoice as $key => $value) {
        //         $invoice = get_table_where('tblpurchase_invoice',array('id'=>$value),'','row');
        //         $__data.='<li><a>'.$invoice->code_invoice.'</a></li>';   
        //     }
        //         $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">'.$__data;
        //         $_data .= '</ul>';
        //         $_data .= '</div>';
        // }
        // }elseif($aRow['tblpay_slip.type'] == 2)
        // {
            $id_import = explode(',', $aRow['tblpay_slip.id_old']);
            $count = count($id_import);
            if($count == 1)
            {
            $import = get_table_where('tblpurchase_order',array('id'=>$id_import[0]),'','row');
            $imports  = $import->prefix.'-'.$import->code;
            $_data='<a href="#" onclick="view_purchase_order('.$id_import[0].'); return false;" >' . $imports . '</a>';
            }else
            {
                $_data = '<div class="dropdown" style="text-align: center;">
                            <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.format_status_number_order_ch($count).'
                            </button>';
                $__data='';
                foreach ($id_import as $key => $value) {
                    $import = get_table_where('tblpurchase_order',array('id'=>$value),'','row');
                    $__data.='<li><a href="#" onclick="view_purchase_order('.$value.'); return false;" >' . $import->prefix.'-'.$import->code . '</a></li>';   
                }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">'.$__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
            }  
        // }elseif($aRow['tblpay_slip.type'] == 5)
        // {
        //     $id_import = explode(',', $aRow['tblpay_slip.id_old']);
        //     $count = count($id_import);
        //     if($count == 1)
        //     {
        //     $import = get_table_where('tbl_outsource',array('id'=>$id_import[0]),'','row');
        //     $imports  = $import->reference_no;
        //     $_data='<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/outsource/view_outsource/').$id_import[0].'" data-toggle="modal" data-target="#myModal">' . $imports . '</a>';
        //     }else
        //     {
        //         $_data = '<div class="dropdown" style="text-align: center;">
        //                     <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.format_status_number_order_chs($count).'
        //                     </button>';
        //         $__data='';
        //         foreach ($id_import as $key => $value) {
        //             $import = get_table_where('tbl_outsource',array('id'=>$value),'','row');
        //             $__data.='<li><a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/outsource/view_outsource/').$id_import[0].'" data-toggle="modal" data-target="#myModal">' . $import->reference_no . '</a></li>';   
        //         }
        //             $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">'.$__data;
        //             $_data .= '</ul>';
        //             $_data .= '</div>';
        //     }  
        // }
        }
        if ($aColumns[$i] == 'tblpay_slip.day_vouchers') {
        $_data='<div class="text-center">'._d($aRow['tblpay_slip.day_vouchers']).'<div>';
        }
        if ($aColumns[$i] == 'tblpay_slip.total') {
        $footer_data['total']+=$aRow['tblpay_slip.total'];
        $_data='<div class="text-right">'.formatMoney($aRow['tblpay_slip.total']).'<div>';
        }
        if ($aColumns[$i] == 'tblpay_slip.payment') {
        $footer_data['pay']+=$aRow['tblpay_slip.payment'];
        $_data='<div class="text-right">'.formatMoney($aRow['tblpay_slip.payment']).'<div>';
        }
        if ($aColumns[$i] == 'tblpay_slip.status') {
            if($aRow['tblpay_slip.status']==0)
                {
                    $type='warning';
                    $status=_l('ch_status_pays_slip_no');
                }
                elseif($aRow['tblpay_slip.status']==1)
                {
                    $type='info';
                    $status=_l('ch_status_pays_slip');
                }
            $status='<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tblpay_slip.status'].'">' . $status.'';
            if(has_permission('pay_slip', '', 'approve'))
            {
                if($aRow['tblpay_slip.status']==0) {
                    $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tblpay_slip.status'] . ',' . $aRow['id'] . '); return false">
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
        if ($aColumns[$i] == 'tblpay_slip.staff_id') {
        $_data=staff_profile_image($aRow['tblpay_slip.staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: '._dt($aRow['date'])
                        )).get_staff_full_name($aRow['tblpay_slip.staff_id']).'<br>';;
        }

        $row[] = $_data;
    }
    $_outputStatus='';


    $_outputStatus = '<div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu h_right">';
    $_outputStatus .= '<li><a href="#" onclick="view_pay_slip('.$aRow['id'].'); return false;" ><i class="fa fa-eye"></i> ' . _l('view_order') . '</a></li>';
    $_outputStatus .= '<li><a href="'.admin_url('pay_slip/print_pdf/'.$aRow['id']).'" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> '._l('print_vote').'</a></li>';
    
    if ($hasPermissionDelete) {
        $_outputStatus .= '<li><a href="' . admin_url('pay_slip/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
    }

    $_outputStatus .= '</ul></div>';

    $row[] = $_outputStatus;
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;