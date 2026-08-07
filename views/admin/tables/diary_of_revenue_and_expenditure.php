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
        'concat(tblpay_slip.prefix,"-",tblpay_slip.code) as code',
        'day_vouchers as date',
        'tblsuppliers.company as company',
        'id_old as arr_code_orders',
        'tblpay_slip.note as note',
        '0 as thu',
        'tblpay_slip.payment as chi',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblpay_slip';
    $where        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where, 'AND day_vouchers >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where, 'AND day_vouchers <='.'"'.$endMonth.' 23:59:59"');
    }
    $filter = [];
    $join = [
        'LEFT JOIN tblsuppliers  ON tblsuppliers.id=tblpay_slip.id_supplierss',
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblpay_slip.staff_id'
    ];
    $group_by = '';
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        '0 as objects','tblpay_slip.type','id_old','3 as checks'
    ],$group_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];    


    $aColumns_other = [
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'concat(tblother_payslips.prefix,"-",tblother_payslips.code) as code',
        'tblother_payslips.date as date',
        'objects_id as company',
        '2 as arr_code_orders',
        'tblother_payslips.note as note',
        '0 as thu',
        'tblother_payslips.total as chi',
    ];
    $sIndexColumn_other = 'id';
    $sTable_other       = 'tblother_payslips';
    $where_other        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_other, 'AND tblother_payslips.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_other, 'AND tblother_payslips.date <='.'"'.$endMonth.' 23:59:59"');
    }
    $filter_other = [];
    $join_other = [
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblother_payslips.staff_id'
    ];
    $group_by_other = '';
    $result_other = data_tables_init($aColumns_other, $sIndexColumn_other, $sTable_other, $join_other, $where_other, [
        'objects as objects','3 as type','type_vouchers','vouchers_id','3 as checks'
    ],$group_by_other);
    $output_other  = $result_other['output'];
    $rResult_other = $result_other['rResult'];


    $aColumns_thu = [
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'code_vouchers as code',
        'date_vouchers as date',
        'tblclients.company as company',
        'arr_code_orders as arr_code_orders',
        'tblvouchers_coupon.note as note',
        'tblvouchers_coupon.payment as thu',
        '0 as chi',
    ];
    $sIndexColumn_thu = 'id';
    $sTable_thu       = 'tblvouchers_coupon';
    $where_thu        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_thu, 'AND date_vouchers >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_thu, 'AND date_vouchers <='.'"'.$endMonth.' 23:59:59"');
    }
    $filter_thu = [];
    $join_thu = [
        'LEFT JOIN tblclients  ON tblclients.userid=tblvouchers_coupon.customer',
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblvouchers_coupon.staff'
    ];
    $group_by_thu = '';
    $result_thu = data_tables_init($aColumns_thu, $sIndexColumn_thu, $sTable_thu, $join_thu, $where_thu, [
        '0 as objects','2 as type','1 as checks'
    ],$group_by_thu);
    $output_thu  = $result_thu['output'];
    $rResult_thu = $result_thu['rResult'];


    $aColumns_other_thu = [
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'concat(tblother_payslips_coupon.prefix,"-",tblother_payslips_coupon.code) as code',
        'tblother_payslips_coupon.date as date',
        'objects_id as company',
        '2 as arr_code_orders',
        'tblother_payslips_coupon.note as note',
        'tblother_payslips_coupon.total as thu',
        '0 as chi',

    ];
    $sIndexColumn_other_thu = 'id';
    $sTable_other_thu       = 'tblother_payslips_coupon';
    $where_other_thu        = [];
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_other_thu, 'AND tblother_payslips_coupon.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_other_thu, 'AND tblother_payslips_coupon.date <='.'"'.$endMonth.' 23:59:59"');
    }
    $filter_other_thu = [];
    $join_other_thu = [
        'LEFT JOIN tblstaff  ON tblstaff.staffid=tblother_payslips_coupon.staff_id'
    ];
    $group_by_other_thu = '';
    $result_other_thu = data_tables_init($aColumns_other_thu, $sIndexColumn_other_thu, $sTable_other_thu, $join_other_thu, $where_other_thu, [
        'objects as objects','1 as type','type_vouchers','vouchers_id','1 as checks'
    ],$group_by_other_thu);
    $output_other_thu  = $result_other_thu['output'];
    $rResult_other_thu = $result_other_thu['rResult'];
        $aColumnsG=array(
            'fullname',
            'code',
            'date',
            'company',
            'arr_code_orders',
            'note',
            'thu',
            'chi',
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
        $output['iTotalRecords']=$output['iTotalRecords']+$output_thu['iTotalRecords']+$output_other_thu['iTotalRecords']+$output_other['iTotalRecords'];
        $output['iTotalDisplayRecords']=$output['iTotalDisplayRecords']+$output_thu['iTotalDisplayRecords']+$output_other_thu['iTotalDisplayRecords']+$output_other['iTotalDisplayRecords'];
    $j=0;

$footer_data = array(
    'thu' => 0,
    'chi' => 0,
);
$objectss[1] = '<span style="color: red;">'._l('ch_IN_client').'</span>';
$objectss[2] = '<span style="color: green;">'._l('ch_IN_suppliers').'</span>';
$objectss[3] = '<span style="color: blue;">'._l('ch_IN_staff').'</span>';
$objectss[4] = '<span style="color: orange;">'._l('ch_IN_other').'</span>';
$type_voucherss[1]['name'] = '<span style="color: purple;">Đơn đặt hàng mua</span>';
$type_voucherss[5]['name'] = '<span style="color: green;">Đơn đặt hàng bán</span>';
$type_voucherss[2]['name'] = '<span style="color: blue;">Xuất kho khác</span>';
$type_voucherss[8]['name'] = '<span style="color: orange;">Trả hàng</span>';
$type_voucherss[12]['name'] = '<span style="color: orange;">Phiếu đề xuất</span>';
$objects[1] = '<span style="color: red;">'._l('ch_IN_client').'</span>';
$objects[2] = '<span style="color: green;">'._l('ch_IN_suppliers').'</span>';
$objects[3] = '<span style="color: blue;">'._l('ch_IN_staff').'</span>';
$objects[4] = '<span style="color: orange;">'._l('ch_IN_other').'</span>';

$type_vouchers[5]['name'] = '<span style="color: red;">Đơn đặt hàng bán</span>';
$type_vouchers[2]['name'] = '<span style="color: green;">Xuất kho khác</span>';
$type_vouchers[8]['name'] = '<span style="color: orange;">Trả hàng</span>';
$type_vouchers[12]['name'] = '<span style="color: orange;">Tạm ứng</span>';
foreach ($rResult as $aRow) {
    $row = array();
    $j++;

    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if($aRow['checks'] == 1){

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
        if ($aColumnsG[$i] == 'thu') {
            $footer_data['thu']+=$aRow['thu']; 
            $_data=number_format($_data);
        }
        if ($aColumnsG[$i] == 'chi') {
            $_data='';
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
                                $_data = $import->reference_no;
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
        }
        if($aRow['checks'] == 3)
        {   
            if ($aColumnsG[$i] == 'company') {
            if($aRow['objects'] == 0)
            {
                $_data='<span style="color: green;">'._l('ch_IN_suppliers').'</span>: '.$_data;
            }else
            {
                if($aRow['objects'] == 2)
                {
                    $supplier = get_table_where('tblsuppliers',array('id'=>$aRow['company']),'','row');
                    $_data = $objectss[$aRow['objects']].': '.$supplier->company;
                }
                if($aRow['objects'] == 1)
                {
                    $client = get_table_where('tblclients',array('userid'=>$aRow['company']),'','row');
                    $_data = $objectss[$aRow['objects']].': '.$client->company;
                }
                if($aRow['objects'] == 3)
                {
                    $_data = $objectss[$aRow['objects']].': '.get_staff_full_name($aRow['company']);
                }
                if($aRow['objects'] == 4)
                {
                    $_data = $objectss[$aRow['objects']].': '.$aRow['objects_text'];
                }
            }
        }
        if ($aColumnsG[$i] == 'date') {
            $_data=_d($_data);
        }
        if ($aColumnsG[$i] == 'thu') {
            $_data='';
        }
        if ($aColumnsG[$i] == 'chi') {
            $footer_data['chi']+=$aRow['chi']; 

            $_data=number_format($_data);
        }
        if ($aColumnsG[$i] == 'arr_code_orders') {
            $_data='';
            if($aRow['type'] == 1)
            {   
                $_data.=$type_voucherss[1]['name'].'<br>';
                $id_invoice = explode(',', $aRow['id_old']);
                foreach ($id_invoice as $k => $v) {
                $invoice = get_table_where('tblpurchase_invoice',array('id'=>$v),'','row');
                $_data.=$invoice->code_invoice.'<br>';   
                }
            }elseif($aRow['type'] == 2)
            {
                $_data.=$type_voucherss[1]['name'].'<br>';
                $id_import = explode(',', $aRow['id_old']);
                foreach ($id_import as $key => $value) {
                    $import = get_table_where('tblpurchase_order',array('id'=>$value),'','row');
                    $_data.=$import->prefix.'-'.$import->code . '<br>';   
                }
            }elseif($aRow['type'] == 3)
            {
                if(!empty($aRow['type_vouchers']))
                {
                $_data.=$type_voucherss[$aRow['type_vouchers']]['name'].'<br>';
                }
                if($aRow['objects'] == 2)
                {   
                    if($aRow['type_vouchers'] == 1)
                    {
                        if(!empty($aRow['company'])&&($aRow['vouchers_id'] !=0))
                        { 
                        $import = get_table_where('tblpurchase_order',array('id'=>$aRow['vouchers_id']),'','row');
                        $_data.= $import->prefix.'-'.$import->code;
                        }
                    }else
                    if($aRow['type_vouchers'] == 8)
                    {
                        if(!empty($aRow['vouchers_id']))
                        {

                            $return = get_table_where('tblreturn_suppliers',array('id'=>$aRow['vouchers_id']),'','row');
                            $_data.= $return->prefix.$return->code;
                        }  
                    }else
                    if($aRow['type_vouchers'] == 5)
                    {
                        if(!empty($aRow['vouchers_id']))
                        {

                            $return = get_table_where('tbl_orders',array('id'=>$aRow['vouchers_id']),'','row');
                            $_data.= $return->reference_no;   
                        }  
                    }else
                    if($aRow['type_vouchers'] == 2)
                    {
                        if(!empty($aRow['vouchers_id']))
                        {

                            $return = get_table_where('tblexport_different',array('id'=>$aRow['vouchers_id']),'','row');
                            $_data.= $return->prefix.$return->code;
                        }  
                    }
                }elseif($aRow['objects'] == 1)
                {   
                    if($aRow['type_vouchers'] == 1)
                    {
                        if(!empty($aRow['company'])&&($aRow['vouchers_id'] !=0))
                        { 
                        $import = get_table_where('tblpurchase_order',array('id'=>$aRow['vouchers_id']),'','row');
                        $_data.= $import->prefix.'-'.$import->code;
                        }
                    }else
                    if($aRow['type_vouchers'] == 8)
                    {
                        if(!empty($aRow['vouchers_id']))
                        {

                            $return = get_table_where('tblreturn_suppliers',array('id'=>$aRow['vouchers_id']),'','row');
                            $_data.= $return->prefix.$return->code;
                        }  
                    }else
                    if($aRow['type_vouchers'] == 5)
                    {
                        if(!empty($aRow['vouchers_id']))
                        {

                            $return = get_table_where('tbl_orders',array('id'=>$aRow['vouchers_id']),'','row');
                            $_data.= $return->reference_no;   
                        }  
                    }else
                    if($aRow['type_vouchers'] == 2)
                    {
                        if(!empty($aRow['vouchers_id']))
                        {

                            $return = get_table_where('tblexport_different',array('id'=>$aRow['vouchers_id']),'','row');
                            $_data.= $return->prefix.'-'.$return->code;
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