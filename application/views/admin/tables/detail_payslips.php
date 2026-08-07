<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('other_payslips', '', 'delete');
$hasPermissionEdit = has_permission('other_payslips', '', 'edit');
$this->ci->db->query("SET sql_mode = ''");
$aColumns = [
    'tblother_payslips.date',
    'tblother_payslips.code',
    'tblother_payslips.objects',
    'tblother_payslips.objects_id',
    'tblother_payslips.type_vouchers',
    'tblother_payslips.vouchers_id',
    'tblpayment_modes.name',
    'tblcosts.name',
    'tblother_payslips.total',
    'tblother_payslips.staff_id',
    'tblother_payslips.note',
    ];
$sIndexColumn = 'id';
$sTable       = 'tblother_payslips';
$where        = [];
if ($this->ci->input->post('filterStatus')) {
    if (is_numeric($this->ci->input->post('filterStatus'))) {
        if ($this->ci->input->post('filterStatus') == 1) {
            array_push($where, 'AND tblother_payslips.status = 1');
        } else if ($this->ci->input->post('filterStatus') == 2) {
            array_push($where, 'AND tblother_payslips.status = 0');
        } else if ($this->ci->input->post('filterStatus') == 3) {
            array_push($where, 'AND tblother_payslips.objects = 1');
        } else if ($this->ci->input->post('filterStatus') == 4) {
            array_push($where, 'AND tblother_payslips.objects = 2');
        } else if ($this->ci->input->post('filterStatus') == 5) {
            array_push($where, 'AND tblother_payslips.objects = 3');
        } else if ($this->ci->input->post('filterStatus') == 6) {
            array_push($where, 'AND tblother_payslips.objects = 4');
        } else if ($this->ci->input->post('filterStatus') == 7) {
            array_push($where, 'AND tblother_payslips.objects = 5');
        }
    }
}

//    array_push($where,'AND tblother_payslips.type_manager = 0');

if ($this->ci->input->post('objects_idd')) {
    if (is_numeric($this->ci->input->post('objects_idd'))) {
        array_push($where, 'AND tblother_payslips.objects = ' . $this->ci->input->post('objects_idd'));
    }
}
if ($this->ci->input->post('objects_ids')) {
    if (is_numeric($this->ci->input->post('objects_ids'))) {
        array_push($where, 'AND tblother_payslips.objects_id = ' . $this->ci->input->post('objects_ids'));
    }
}
if ($this->ci->input->post('objects_texts')) {
    if ($this->ci->input->post('objects_texts')) {
        array_push($where, 'AND tblother_payslips.objects_text LIKE "%' . $this->ci->input->post('objects_texts') . '%"');
    }
}
$search_date = $this->ci->input->post('search_date');
if ($search_date) {
    $data_start = explode(' - ', $search_date);
    array_push($where, 'AND tblother_payslips.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
}
if (is_numeric($type)) {
    if ($type == 1) {
        array_push($where, 'AND tblother_payslips.type_manager = 1');
    }
    if ($type == 2) {
        array_push($where, 'AND tblother_payslips.type_manager = 0');
    }
    if ($type == 3) {
        array_push($where, 'AND tblcosts.type = 5');
    }
    if ($type == 4) {
        array_push($where, 'AND tblcosts.type = 6');
    }
}
$filter = [];
$join = [
    'LEFT JOIN tblpayment_modes ON tblpayment_modes.id=tblother_payslips.payment_modes',
    'LEFT JOIN tblcosts ON tblcosts.id=tblother_payslips.id_costs',
];
if (has_permission('other_payslips', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND  tblother_payslips.staff_id = ' . get_staff_user_id());
}
array_push($where, 'AND  tblother_payslips.is_advance = 0');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tblother_payslips.id',
    'tblother_payslips.prefix',
    'tblother_payslips.objects_id',
    'tblother_payslips.date_create',
    'tblother_payslips.objects_text',
    'tblother_payslips.history_status',
    'tblother_payslips.detai',
    'tblother_payslips.vouchers_coupon_id as vouchers_coupon_id',

]);
$output  = $result['output'];
$rResult = $result['rResult'];
$j = 0;
$footer_data = array(
    'all' => 0,
    'payment' => 0,
);

$this->ci->load->model('other_payslips_model');
foreach ($rResult as $aRow) {
    $vouchers_coupon_id = $aRow['vouchers_coupon_id'];
    $strVoucherCoupon = '';
    if ($vouchers_coupon_id) {
        $dtVoucherCoupon = $this->ci->other_payslips_model->getVouchersCouponId($vouchers_coupon_id);
        $strVoucherCoupon = $dtVoucherCoupon['code_vouchers'] ?? '';
        $strVoucherCoupon = '<div>' . $strVoucherCoupon . '</div>';
    }

    $row = array();
    $j++;
    $footer_data['all']++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblother_payslips.status') {
            if ($aRow['tblother_payslips.status'] == 0) {
                $type = 'warning';
                $status = _l('ch_status_pays_slip_no');
            } elseif ($aRow['tblother_payslips.status'] == 1) {
                $type = 'info';
                $status = _l('ch_status_pays_slip');
            }
            $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblother_payslips.status'] . '">' . $status . '';
            if (has_permission('other_payslips', '', 'approve')) {
                if ($aRow['tblother_payslips.status'] == 0) {
                    $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tblother_payslips.status'] . ',' . $aRow['id'] . '); return false">
                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
                } else {
                    $status .= '<a href="javacript:void(0)">
                <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
                }
            }
            $status .= '</a>
                    </span><br>';
            $__data = '';
            $history_status = explode('|', $aRow['history_status']);

            foreach ($history_status as $key => $value) {
                $data = explode(',', $value);
                if (is_numeric($data[0])) {
                    $__data .= staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: ' . _dt($data[1])
                    )) . get_staff_full_name($data[0]) . '<br>';
                }
            }

            $_data = $status . $__data;
        }
        if ($aColumns[$i] == 'tblother_payslips.type_vouchers') {
            $_data = '';
            $type_vouchers[1]['name'] = _l('Nhập hàng');
            $type_vouchers[5]['name'] = _l('ch_order_ck');
            $type_vouchers[2]['name'] = _l('ch_export_other_ck');
            $type_vouchers[8]['name'] = _l('ch_return_ck');
            $type_vouchers[9]['name'] = _l('order_production_details');
            $type_vouchers[65]['name'] = _l('Service_ticket');
            $type_vouchers[12]['name'] = _l('ch_suggestion');
            if (!empty($aRow['tblother_payslips.type_vouchers'])) {
                $_data = $type_vouchers[$aRow['tblother_payslips.type_vouchers']]['name'];
            }
        }
        if ($aColumns[$i] == 'tblother_payslips.code') {
            $payslips = $aRow['prefix'] . '-' . $aRow['tblother_payslips.code'];
            $payslips = '<a href="#" onclick="view_other_payslips(' . $aRow['id'] . '); return false;" >' . $payslips . '</a>';
            $_data = $payslips;
        }
        if ($aColumns[$i] == 'tblother_payslips.date') {
            $_data = _d($aRow['tblother_payslips.date']);
        }
        if ($aColumns[$i] == 'tblother_payslips.objects') {
            if ($aRow['tblother_payslips.objects'] == 1) {
                $text = _l('ch_IN_client');
            }
            if ($aRow['tblother_payslips.objects'] == 2) {
                $text = _l('ch_IN_suppliers');
            }
            if ($aRow['tblother_payslips.objects'] == 3) {
                $text = _l('ch_IN_staff');
            }
            if ($aRow['tblother_payslips.objects'] == 4) {
                $text = _l('ch_IN_other');
            }
            if ($aRow['tblother_payslips.objects'] == 5) {
                $text = _l('ch_IN_tscd');
            }
            $_data = $text;
        }
        if ($aColumns[$i] == 'tblother_payslips.objects_id') {
            $_data = '';
            if ($aRow['tblother_payslips.objects'] == 2) {
                $supplier = get_table_where('tblsuppliers', array('id' => $aRow['tblother_payslips.objects_id']), '', 'row');
                if (!empty($supplier)) {
                    $_data = '<a href="#" onclick="int_suppliers_view(' . $supplier->id . '); return false;" >' . $supplier->company . '</a>';
                } else {
                    $_data = '';
                }
            }
            if ($aRow['tblother_payslips.objects'] == 1) {
                $client = get_table_where('tblclients', array('userid' => $aRow['tblother_payslips.objects_id']), '', 'row');
                $_data = $client->company;
            }
            if ($aRow['tblother_payslips.objects'] == 3) {
                $_data = get_staff_full_name($aRow['tblother_payslips.objects_id']);
            }
            if ($aRow['tblother_payslips.objects'] == 4) {
                $_data = $aRow['objects_text'] . $strVoucherCoupon;
            }
        }
        if ($aColumns[$i] == 'tblother_payslips.total') {
            $footer_data['payment'] += $aRow['tblother_payslips.total'];
            $_data = formatNumber($aRow['tblother_payslips.total'], 0);
        }
        if ($aColumns[$i] == 'tblother_payslips.vouchers_id') {
            $_data = '';

            if ($aRow['tblother_payslips.objects'] == 2) {
                if ($aRow['tblother_payslips.type_vouchers'] == 1) {
                    if (!empty($aRow['tblother_payslips.vouchers_id'])) {
                        $_data = '';
                        $other_items = get_table_where('tblother_payslips_detail', array('other_pay' => $aRow['id']), '', 'result');
                        if (!empty($other_items)) {
                            foreach ($other_items as $key => $value) {
                                $import = get_table_where('tblimport', array('id' => $value->id_import), '', 'row');
                                if (!empty($import)) {
                                    $_data .= '<a href="#" onclick="view_import(' . $import->id . '); return false;" >' . $import->prefix . '-' . $import->code . '</a> <br>';
                                }
                            }
                        }
                    }
                    // if(!empty($aRow['tblother_payslips.objects_id'])&&($aRow['tblother_payslips.vouchers_id'] !=0))
                    // { 
                    //     $import = get_table_where('tblimport',array('id'=>$aRow['tblother_payslips.vouchers_id']),'','row');
                    //     if(!empty($import)){
                    //         $_data = '<a href="#" onclick="view_import('.$import->id.'); return false;" >'.$import->prefix.'-'.$import->code.'</a>';
                    //     } else {
                    //         $_data = '';
                    //     }
                    // }
                } else
            if ($aRow['tblother_payslips.type_vouchers'] == 8) {
                    if (!empty($aRow['tblother_payslips.vouchers_id'])) {

                        $return = get_table_where('tblreturn_suppliers', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                        $_data = '<a href="#" onclick="view_return_suppliers(' . $return->id . '); return false;" >' . $return->prefix . $return->code . '</a>';
                    }
                } else
            if ($aRow['tblother_payslips.type_vouchers'] == 5) {
                    if (!empty($aRow['tblother_payslips.vouchers_id']) && ($aRow['tblother_payslips.vouchers_id'] != -1)) {

                        $return = get_table_where('tbl_orders', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                        $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('orders/view_order/' . $aRow['tblother_payslips.vouchers_id']) . '" data-toggle="modal" data-target="#myModal">' . $return->reference_no . '</a>';
                    }
                    if (!empty($aRow['tblother_payslips.vouchers_id']) && ($aRow['tblother_payslips.vouchers_id'] == -1)) {
                        $detal = explode(',', $aRow['detai']);
                        $_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . format_status_number_order_tnh(count($detal) - 1) . '
                        </button>';
                        $__data = '';
                        foreach ($detal as $key => $value) {
                            if (!empty($value)) {
                                $import = get_table_where('tbl_orders', array('id' => explode('|', $value)[0]), '', 'row');
                                $__data .= '<li><a data-tnh="modal" class="tnh-modal" href="' . admin_url('orders/view_order/' . explode(',', $value)[0]) . '" data-toggle="modal" data-target="#myModal">' . $import->reference_no . ' (' . formatNumber(explode('|', $value)[1], 0) . ')' . '</a></li>';
                            }
                        }
                        $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                        $_data .= '</ul>';
                        $_data .= '</div>';
                    }
                    // $return = get_table_where('tbl_orders',array('id'=>$aRow['tblother_payslips.vouchers_id']),'','row');
                    // $_data = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('orders/view_order/'.$aRow['tblother_payslips.vouchers_id']).'" data-toggle="modal" data-target="#myModal">' . $return->reference_no . '</a>'; 
                } else
            if ($aRow['tblother_payslips.type_vouchers'] == 2) {
                    if (!empty($aRow['tblother_payslips.vouchers_id'])) {

                        $return = get_table_where('tblexport_different', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                        $_data = '<a href="#" onclick="view_return_suppliers(' . $return->id . '); return false;" >' . $return->prefix . $return->code . '</a>';
                    }
                }
                if ($aRow['tblother_payslips.type_vouchers'] == 65) {
                    if (!empty($aRow['tblother_payslips.vouchers_id'])) {
                        $_data = '';
                        $other_items = get_table_where('tblother_payslips_detail', array('other_pay' => $aRow['id']), '', 'result');
                        if (!empty($other_items)) {
                            foreach ($other_items as $key => $value) {
                                $import = get_table_where('tbl_services', array('id' => $value->id_service), '', 'row');
                                if (!empty($import)) {
                                    $_data .= '<a href="#" onclick="view_detail_service(' . $import->id . '); return false;" >' . $import->prefix . '-' . $import->code . '</a> <br>';
                                }
                            }
                        }
                    }
                }
            } elseif ($aRow['tblother_payslips.objects'] == 1) {
                if ($aRow['tblother_payslips.type_vouchers'] == 1) {
                    if (!empty($aRow['tblother_payslips.objects_id']) && ($aRow['tblother_payslips.vouchers_id'] != 0)) {
                        $import = get_table_where('tblpurchase_order', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                        $_data = '<a href="#" onclick="view_import(' . $import->id . '); return false;" >' . $import->prefix . '-' . $import->code . '</a>';
                    }
                } else
            if ($aRow['tblother_payslips.type_vouchers'] == 8) {
                    if (!empty($aRow['tblother_payslips.vouchers_id'])) {

                        $return = get_table_where('tblreturn_suppliers', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                        $_data = '<a href="#" onclick="view_return_suppliers(' . $return->id . '); return false;" >' . $return->prefix . $return->code . '</a>';
                    }
                } else
            if ($aRow['tblother_payslips.type_vouchers'] == 5) {
                    if (!empty($aRow['tblother_payslips.vouchers_id'])) {

                        $return = get_table_where('tbl_orders', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                        $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('orders/view_order/' . $aRow['tblother_payslips.vouchers_id']) . '" data-toggle="modal" data-target="#myModal">' . $return->reference_no . '</a>';
                    }
                } else
            if ($aRow['tblother_payslips.type_vouchers'] == 2) {
                    if (!empty($aRow['tblother_payslips.vouchers_id'])) {

                        $return = get_table_where('tblexport_different', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                        $_data = '<a href="#" onclick="view_export_different(' . $return->id . '); return false;" >' . $return->prefix . '-' . $return->code . '</a>';
                    }
                }
            }
            if ($aRow['tblother_payslips.type_vouchers'] == 9) {
                if (!empty($aRow['tblother_payslips.vouchers_id'])) {

                    $productions_orders_details = get_table_where('tbl_productions_orders_details', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                    if (!empty($productions_orders_details)) {
                        $_data = '<a target="_blank" href="' . admin_url('manufactures/detail_productions/' . $aRow['tblother_payslips.vouchers_id']) . '" >' . $productions_orders_details->reference_no . '</a>';
                    } else {
                        $_data = '';
                    }
                }
            }
            // if($aRow['tblother_payslips.type_vouchers'] == 12)
            // {
            //     if(!empty($aRow['tblother_payslips.vouchers_id']))
            //     {
            //         $suggestion = get_table_where('tblsuggestion',array('id'=>$aRow['tblother_payslips.vouchers_id']),'','row');
            //         if(!empty($suggestion)){
            //             $_data = '<a data-tnh="modal" style="" class="tnh-modal"  data-toggle="modal" data-target="#myModal" href="'.admin_url('suggestion/view_modal/'.$aRow['tblother_payslips.vouchers_id']).'" >'.$suggestion->code.'</a>';
            //         } else {
            //             $_data = '';
            //         }
            //     }  
            // }
            if ($aRow['tblother_payslips.type_vouchers'] == 12) {
                if (!empty($aRow['tblother_payslips.vouchers_id'])) {
                    $_data = '';
                    $other_items = get_table_where('tblother_payslips_detail', array('other_pay' => $aRow['id']), '', 'result');
                    if (!empty($other_items)) {
                        foreach ($other_items as $key => $value) {
                            $import = get_table_where('tblsuggestion', array('id' => $value->id_service), '', 'row');
                            if (!empty($import)) {
                                $_data .= '<a data-tnh="modal" style="" class="tnh-modal"  data-toggle="modal" data-target="#myModal" href="' . admin_url('suggestion/view_modal/' . $import->id) . '" >' . $import->code . '</a>,<br>';
                            }
                        }
                    }
                }
            }
        }
        if ($aColumns[$i] == 'tblother_payslips.staff_id') {
            $_data = staff_profile_image($aRow['tblother_payslips.staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => ' Vào lúc: ' . _dt($aRow['date_create'])
            )) . get_staff_full_name($aRow['tblother_payslips.staff_id']) . '<br>';
        }


        if ($aColumns[$i] == '1') {
            $_data = '<div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu h_right">';
            $_data .= '<li><a href="#" onclick="view_other_payslips(' . $aRow['id'] . '); return false;" ><i class="fa fa-eye"></i> ' . _l('view_order') . '</a></li>';
            if ($hasPermissionEdit && ($aRow['tblother_payslips.vouchers_id'] != -1)) {

                $_data .= '<li><a href="" onclick="edit_other_payslips(' . $aRow['id'] . '); return false;" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
            }
            $invoice = '';
            if ($aRow['tblother_payslips.objects'] == 2) {
                if (!empty($aRow['tblother_payslips.type_vouchers'])) {
                    if ($aRow['tblother_payslips.type_vouchers'] == 1) {
                        if (!empty($aRow['tblother_payslips.vouchers_id'])) {

                            $ktr_import = get_table_where('tblpurchase_order', array('id' => $aRow['tblother_payslips.vouchers_id']), '', 'row');
                            if (!empty($ktr_import)) {
                                $invoice = $ktr_import->red_invoice;
                            }
                        }
                    }
                }
            }
            if ($hasPermissionDelete && empty($invoice) && ($aRow['tblother_payslips.vouchers_id'] != -1)) {
                $_data .= '<li><a href="' . admin_url('other_payslips/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
            }
            if ($hasPermissionDelete && empty($invoice) && ($aRow['tblother_payslips.vouchers_id'] == -1)) {
                $_data .= '<li><a href="' . admin_url('other_payslips/delete_v2/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete') . '</a></li>';
            }
            $_data .= '<li><a href="' . admin_url('other_payslips/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> ' . _l('print_vote') . '</a></li>';
            $_data .= '</ul></div>';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total, 0);
}
$output['sums'] = $footer_data;
