    <?php
    defined('BASEPATH') or exit('No direct script access allowed');
    $hasPermissionDelete = has_permission('debt_suppliers', '', 'delete');

    $beginMonth =  '';
    $endMonth   =  '';
        $months_report = $this->ci->input->post('report_months');

        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth   = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->ci->input->post('report_from'));
                $to_date   = to_sql_date($this->ci->input->post('report_to'));
                if ($from_date == $to_date) {
                    $beginMonth =  $to_date;
                    $endMonth   =  $to_date;
                } else {
                    $beginMonth =  $from_date;
                    $endMonth   =  $to_date;
                }
            }
        }
$id_account = $this->ci->input->post('id_account');
$aColumns = [
        'day_vouchers as date',
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'concat(tblpay_slip.prefix,"-",tblpay_slip.code) as code',
        'tblsuppliers.company as company',
        'tblpay_slip.note as note',
        '0 as thu',
        'tblpay_slip.payment as chi',
        '0 as sub_total'
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblpay_slip';
    $where        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where, 'AND day_vouchers >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where, 'AND day_vouchers <='.'"'.$endMonth.' 23:59:59"');
    }
    array_push($where, 'AND payment_mode ='.$id_account);
    $filter = [];
    $join = [
        'LEFT JOIN tblsuppliers  ON tblsuppliers.id=tblpay_slip.id_supplierss',
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblpay_slip.staff_id'
    ];
    $group_by = '';
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        '0 as objects','tblpay_slip.type','id_old','3 as checks','4 as objects_text','tblpay_slip.id as idd','1 as types'
    ],$group_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];    


    $aColumns_other = [
        'tblother_payslips.date as date',
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'concat(tblother_payslips.prefix,"-",tblother_payslips.code) as code',
        'objects_id as company',
        'tblother_payslips.note as note',
        '0 as thu',
        'tblother_payslips.total as chi',
        '0 as sub_total'
    ];
    $sIndexColumn_other = 'id';
    $sTable_other       = 'tblother_payslips';
    $where_other        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_other, 'AND tblother_payslips.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_other, 'AND tblother_payslips.date <='.'"'.$endMonth.' 23:59:59"');
    }
    array_push($where_other, 'AND payment_modes ='.$id_account);
    $filter_other = [];
    $join_other = [
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblother_payslips.staff_id'
    ];
    $group_by_other = '';
    $result_other = data_tables_init($aColumns_other, $sIndexColumn_other, $sTable_other, $join_other, $where_other, [
        'objects as objects','3 as type','type_vouchers','vouchers_id','3 as checks','objects_text','tblother_payslips.id as idd','2 as types'
    ],$group_by_other);
    $output_other  = $result_other['output'];
    $rResult_other = $result_other['rResult'];


    $aColumns_thu = [
        'date_vouchers as date',
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'code_vouchers as code',
        'tblclients.company as company',
        'tblvouchers_coupon.note as note',
        'tblvouchers_coupon.payment as thu',
        '0 as chi',
        '0 as sub_total'
    ];
    $sIndexColumn_thu = 'id';
    $sTable_thu       = 'tblvouchers_coupon';
    $where_thu        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_thu, 'AND date_vouchers >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_thu, 'AND date_vouchers <='.'"'.$endMonth.' 23:59:59"');
    }
    array_push($where_thu, 'AND payment_mode ='.$id_account);
    $filter_thu = [];
    $join_thu = [
        'LEFT JOIN tblclients  ON tblclients.userid=tblvouchers_coupon.customer',
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblvouchers_coupon.staff'
    ];
    $group_by_thu = '';
    $result_thu = data_tables_init($aColumns_thu, $sIndexColumn_thu, $sTable_thu, $join_thu, $where_thu, [
        '1 as objects','2 as type','1 as checks','4 as objects_text','3 as types'
    ],$group_by_thu);
    $output_thu  = $result_thu['output'];
    $rResult_thu = $result_thu['rResult'];


    $aColumns_other_thu = [
        'tblother_payslips_coupon.date as date',
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'concat(tblother_payslips_coupon.prefix,"-",tblother_payslips_coupon.code) as code',
        '2 as company',
        'tblother_payslips_coupon.note as note',
        'tblother_payslips_coupon.total as thu',
        '0 as chi',
        '0 as sub_total'

    ];
    $sIndexColumn_other_thu = 'id';
    $sTable_other_thu       = 'tblother_payslips_coupon';
    $where_other_thu        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_other_thu, 'AND tblother_payslips_coupon.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_other_thu, 'AND tblother_payslips_coupon.date <='.'"'.$endMonth.' 23:59:59"');
    }
    array_push($where_other_thu, 'AND payment_modes ='.$id_account);
    $filter_other_thu = [];
    $join_other_thu = [
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblother_payslips_coupon.staff_id'
    ];
    $group_by_other_thu = '';
    $result_other_thu = data_tables_init($aColumns_other_thu, $sIndexColumn_other_thu, $sTable_other_thu, $join_other_thu, $where_other_thu, [
        'objects as objects','1 as type','type_vouchers','vouchers_id','1 as checks','objects_text','3 as types'
    ],$group_by_other_thu);
    $output_other_thu  = $result_other_thu['output'];
    $rResult_other_thu = $result_other_thu['rResult'];



    $aColumns_tu_n = [
        'tbladvance_payment.date as date',
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'tbladvance_payment.code as code',
        'tbladvance_payment.staff as company',
        'tbladvance_payment.note as note',
        'tbladvance_payment.total as thu',
        '0 as chi',
        '0 as sub_total'
    ];
    $sIndexColumn_tu_n = 'id';
    $sTable_tu_n       = 'tbladvance_payment';
    $where_tu_n        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_tu_n, 'AND date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_tu_n, 'AND date <='.'"'.$endMonth.' 23:59:59"');
    }
    array_push($where_tu_n, 'AND paymode_n ='.$id_account);
    $filter_tu_n = [];
    $join_tu_n = [
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tbladvance_payment.staff'
    ];
    $group_by_tu_n = '';
    $result_tu_n = data_tables_init($aColumns_tu_n, $sIndexColumn_tu_n, $sTable_tu_n, $join_tu_n, $where_tu_n, [
        '3 as objects','2 as type','1 as checks','4 as objects_text','4 as types'
    ],$group_by_tu_n);
    $output_tu_n  = $result_tu_n['output'];
    $rResult_tu_n = $result_tu_n['rResult'];


    $aColumns_tu_c = [
        'tbladvance_payment.date as date',
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'tbladvance_payment.code as code',
        'tbladvance_payment.staff as company',
        'tbladvance_payment.note as note',
        '0 as thu',
        'tbladvance_payment.total as chi',
        '0 as sub_total'
    ];
    $sIndexColumn_tu_c = 'id';
    $sTable_tu_c       = 'tbladvance_payment';
    $where_tu_c        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_tu_c, 'AND date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_tu_c, 'AND date <='.'"'.$endMonth.' 23:59:59"');
    }
    array_push($where_tu_c, 'AND paymode_c ='.$id_account);
    $filter_tu_c = [];
    $join_tu_c = [
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tbladvance_payment.staff'
    ];
    $group_by_tu_c = '';
    $result_tu_c = data_tables_init($aColumns_tu_c, $sIndexColumn_tu_c, $sTable_tu_c, $join_tu_c, $where_tu_c, [
        '3 as objects','2 as type','1 as checks','4 as objects_text','5 as types'
    ],$group_by_tu_c);
    $output_tu_c  = $result_tu_c['output'];
    $rResult_tu_c = $result_tu_c['rResult'];

    $aColumnsG=array(
        'date',
        'fullname',
        'code',
        'company',
        'note',
        'thu',
        'chi',
        'sub_total'
    );
    if(!empty($rResult_other_thu))
    {
        $rResult_thu=array_merge($rResult_thu,$rResult_other_thu);   
    }
    if(!empty($rResult_thu))
    {
        $rResult=array_merge($rResult,$rResult_thu);   
    }
    if(!empty($rResult_other))
    {
        $rResult=array_merge($rResult,$rResult_other);   
    }
        if(!empty($rResult_tu_n))
    {
        $rResult=array_merge($rResult,$rResult_tu_n);   
    }
    if(!empty($rResult_tu_c))
    {
        $rResult=array_merge($rResult,$rResult_tu_c);   
    }
    usort($rResult, ch_make_cmp(['date' => "asc"]));
    $output['iTotalRecords']=$output['iTotalRecords']+$output_thu['iTotalRecords']+$output_other_thu['iTotalRecords']+$output_other['iTotalRecords']+$output_tu_c['iTotalRecords']+$output_tu_n['iTotalRecords'];
    $output['iTotalDisplayRecords']=$output['iTotalDisplayRecords']+$output_thu['iTotalDisplayRecords']+$output_other_thu['iTotalDisplayRecords']+$output_other['iTotalDisplayRecords']+$output_tu_c['iTotalDisplayRecords']+$output_tu_n['iTotalDisplayRecords'];
$j=0;

$footer_data = array(
    'thu' => 0,
    'chi' => 0,
    'tong'=> 0,
);
    $opening_balance = get_table_where('tblpayment_modes',array('id'=>$id_account),'','row');
    $existing_period = getStart_coupons_v2($id_account,$beginMonth) - getStart_payslips_v2($id_account,$beginMonth)+$opening_balance->opening_balance;
    $sub_total = $existing_period;
    $footer_data['tong']=$sub_total;
    $row=array(
            'SỐ DƯ ĐẦU KỲ - '.$opening_balance->name,
            '',
            '',
            '',
            '',
            '',
            '',
            '<div class="text-right">'.number_format($existing_period).'</div>'
        );
    $row['DT_RowClass'] = 'alert-headertext bold warning';

    for ($i=0 ; $i<count($aColumns) ; $i++ ){
        $row[]="";
        }
    $output['aaData'][] = $row;
$objects[1] = '<span style="color: red;">'._l('ch_IN_client').'</span>';
$objects[2] = '<span style="color: green;">'._l('ch_IN_suppliers').'</span>';
$objects[3] = '<span style="color: blue;">'._l('ch_IN_staff').'</span>';
$objects[4] = '<span style="color: orange;">'._l('ch_IN_other').'</span>';
$type_vouchers[1]['name'] = '<span style="color: red;">'._l('ch_purchase_order_ck').'</span>';
$type_vouchers[5]['name'] = '<span style="color: green;">'._l('ch_order_ck').'</span>';
$type_vouchers[2]['name'] = '<span style="color: blue;">'._l('ch_export_different').'</span>';
$type_vouchers[8]['name'] = '<span style="color: orange;">'._l('ch_return').'</span>';
$type_vouchers[9]['name'] = '<span style="color: orange;">'._l('order_production_details').'</span>';
$type_vouchers[10]['name'] = '<span style="color: orange;">'._l('shipping_and_processing_costs').'</span>';
foreach ($rResult as $r => $aRow) {
    $row = array();
    $j++;

    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if ($aColumnsG[$i] == 'code') {
            $_data=$_data;
            if($aRow['types'] == 1)
            {
                $_data = '<a href="#" onclick="view_pay_slip('.$aRow['idd'].'); return false;" >' . $_data . '</a>';
            }
            if($aRow['types'] == 2)
            {
                $_data = '<a href="#" onclick="view_other_payslips('.$aRow['idd'].'); return false;" >' . $_data . '</a>';
            }
        }
        if ($aColumnsG[$i] == 'date') {
            $_data=_d($_data);
        }
        if ($aColumnsG[$i] == 'thu') {
            $footer_data['thu']+=$aRow['thu']; 
            $sub_total+=$aRow['thu'];
            $_data=number_format($_data);
        }
        if ($aColumnsG[$i] == 'chi') {
            $sub_total-=$aRow['chi'];
            $footer_data['chi']+=$aRow['chi'];
            $_data=number_format($_data);
        }
        if ($aColumnsG[$i] == 'sub_total') {
            $footer_data['tong']=$sub_total; 
            $_data=number_format($sub_total);
        }
        if ($aColumnsG[$i] == 'company') {
            if($aRow['objects'] == 0)
            {
                $_data='<span style="color: green;">'._l('ch_IN_suppliers').'</span>: '.$_data;
            }else
            {
                if($aRow['objects'] == 2)
                {
                    $supplier = get_table_where('tblsuppliers',array('id'=>$aRow['company']),'','row');
                    $_data = $objects[$aRow['objects']].': '.$supplier->company;
                }
                if($aRow['objects'] == 1)
                {
                    $_data='<span style="color: green;">'.$objects[$aRow['objects']].'</span>: '.$_data;
                }
                if($aRow['objects'] == 3)
                {
                    $_data = $objects[$aRow['objects']].': '.get_staff_full_name($aRow['company']);
                }
                if($aRow['objects'] == 4)
                {
                    $_data = $objects[$aRow['objects']].': '.$aRow['objects_text'];
                }
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;
$output['title_excel'] = ['Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') . ' - ' . (!empty($endMonth) ? _dt($endMonth) : ''),];