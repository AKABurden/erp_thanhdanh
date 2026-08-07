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

    $aColumns = [
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'code_vouchers as code',
        'date_vouchers as date',
        'tblclients.company as company',
        'arr_code_orders as arr_code_orders',
        'tblvouchers_coupon.note as note',
        'tblvouchers_coupon.payment as payment',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblvouchers_coupon';
    $where        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where, 'AND date_vouchers >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where, 'AND date_vouchers <='.'"'.$endMonth.' 23:59:59"');
    }
    $filter = [];
    $join = [
        'LEFT JOIN tblclients  ON tblclients.userid=tblvouchers_coupon.customer',
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblvouchers_coupon.staff'
    ];
    $group_by = '';
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        '0 as objects','2 as type',
    ],$group_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];


    $aColumns_other = [
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'concat(tblother_payslips_coupon.prefix,"-",tblother_payslips_coupon.code) as code',
        'tblother_payslips_coupon.date as date',
        'objects_id as company',
        '2 as arr_code_orders',
        'tblother_payslips_coupon.note as note',
        'tblother_payslips_coupon.total as payment',
    ];
    $sIndexColumn_other = 'id';
    $sTable_other       = 'tblother_payslips_coupon';
    $where_other        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_other, 'AND tblother_payslips_coupon.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_other, 'AND tblother_payslips_coupon.date <='.'"'.$endMonth.' 23:59:59"');
    }
    $filter_other = [];
    $join_other = [
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblother_payslips_coupon.staff_id'
    ];
    $group_by_other = '';
    $result_other = data_tables_init($aColumns_other, $sIndexColumn_other, $sTable_other, $join_other, $where_other, [
        'objects as objects','1 as type','type_vouchers','vouchers_id'
    ],$group_by_other);
    $output_other  = $result_other['output'];
    $rResult_other = $result_other['rResult'];
        $aColumnsG=array(
            'fullname',
            'code',
            'date',
            'company',
            'arr_code_orders',
            'note',
            'payment',
        );
        if(!empty($rResult_other))
        {
        $rResult=array_merge($rResult,$rResult_other);   
        }
        $output['iTotalRecords']=$output['iTotalRecords']+$output_other['iTotalRecords'];
        $output['iTotalDisplayRecords']=$output['iTotalDisplayRecords']+$output_other['iTotalDisplayRecords'];
    $j=0;
$objects[1] = '<span style="color: red;">'._l('ch_IN_client').'</span>';
$objects[2] = '<span style="color: green;">'._l('ch_IN_suppliers').'</span>';
$objects[3] = '<span style="color: blue;">'._l('ch_IN_staff').'</span>';
$objects[4] = '<span style="color: orange;">'._l('ch_IN_other').'</span>';

$type_vouchers[5]['name'] = '<span style="color: red;">Đơn đặt hàng bán</span>';
$type_vouchers[2]['name'] = '<span style="color: green;">Xuất kho khác</span>';
$type_vouchers[8]['name'] = '<span style="color: orange;">Trả hàng</span>';
$footer_data = array(
    'total_amount' => 0,
);
foreach ($rResult as $aRow) {
    $row = array();
    $j++;

    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if ($aColumnsG[$i] == 'company') {
            if($aRow['objects'] == 0)
            {
                $_data='<span style="color: red;">'._l('ch_IN_client').'</span>: '.$_data;
            }else
            {
                if($aRow['objects'] == 2)
                {
                    $supplier = get_table_where('tblsuppliers',array('id'=>$aRow['company']),'','row');
                    $_data = $objects[$aRow['objects']].': '.$supplier->company;
                }
                if($aRow['objects'] == 1)
                {
                    $client = get_table_where('tblclients',array('userid'=>$aRow['company']),'','row');
                    $_data = $objects[$aRow['objects']].': '.$client->company;
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
        if ($aColumnsG[$i] == 'date') {
            $_data=_d($_data);
        }
        if ($aColumnsG[$i] == 'payment') {
            $footer_data['total_amount']+=$aRow['payment']; 
            $_data=number_format($_data);
        }
        if ($aColumnsG[$i] == 'arr_code_orders') {
            $_data='';
            if($aRow['type'] == 2)
            {
                $order = explode(',', $aRow['arr_code_orders']);
                $_data.=$type_vouchers[5]['name'].': <br>';
                foreach ($order as $k => $v) {
                    $_v = explode('|', $v);

                    $_order = get_table_where('tbl_orders',array('id'=>$_v[0]),'','row'); 
                    if(!empty($_order))
                    {
                    $_data.=$_order->reference_no.'<br>';
                    }
                }
            }else
            {
                if($aRow['objects'] == 1)
                {   
                    if($aRow['type_vouchers'] == 5)
                    {
                        if(!empty($aRow['company'])&&($aRow['vouchers_id'] !=0))
                        { 
                        $import = get_table_where('tbl_orders',array('id'=>$aRow['vouchers_id']),'','row');
                            if(!empty($aRow['type_vouchers']))
                            {
                                $_data = $type_vouchers[$aRow['type_vouchers']]['name'].': '.$import->reference_no;
                            }else
                            {
                                $_data =$import->reference_no;
                            }
                        }
                    }
                }else
                if($aRow['objects'] == 2)
                {   
                    if($aRow['type_vouchers'] == 8)
                    {
                        if(!empty($aRow['company'])&&($aRow['vouchers_id'] !=0))
                        { 
                        $import = get_table_where('tblreturn_suppliers',array('id'=>$aRow['vouchers_id']),'','row');
                            if(!empty($aRow['type_vouchers']))
                            {
                                $_data = $type_vouchers[$aRow['type_vouchers']]['name'].': '.$import->prefix.$import->code;
                            }else
                            {
                                $_data = $import->prefix.$import->code;
                            }
                        }
                    }
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