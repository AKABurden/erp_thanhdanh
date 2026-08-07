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
        '2',
        'tblpay_slip.note as note',
        'tblpay_slip.payment as payment',
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
        '0 as objects','tblpay_slip.type','id_old','" " as objects_text'
    ],$group_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];


    $aColumns_other = [
        'concat(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        'concat(tblother_payslips.prefix,"-",tblother_payslips.code) as code',
        'tblother_payslips.date as date',
        'objects_id as company',
        '2',
        'tblother_payslips.note as note',
        'tblother_payslips.total as payment',
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
        'objects as objects','3 as type','type_vouchers','vouchers_id','objects_text'
    ],$group_by_other);
    $output_other  = $result_other['output'];
    $rResult_other = $result_other['rResult'];
        $aColumnsG=array(
            'fullname',
            'code',
            'date',
            'company',
            '2',
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
$objects[5] = '<span style="color: yelow;">'._l('ch_IN_tscd').'</span>';
$type_vouchers[1]['name'] = '<span style="color: red;">'._l('ch_purchase_order_ck').'</span>';
$type_vouchers[5]['name'] = '<span style="color: green;"'._l('ch_order_ck').'</span>';
$type_vouchers[2]['name'] = '<span style="color: blue;">'._l('ch_export_other_ck').'</span>';
$type_vouchers[8]['name'] = '<span style="color: orange;">'._l('ch_return_ck').'</span>';
$type_vouchers[9]['name'] = '<span style="color: yelow;">'._l('order_production_details').'</span>';
$type_vouchers[12]['name'] = '<span style="color: yelow;">'._l('Phiếu đề xuất').'</span>';


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
        if ($aColumnsG[$i] == '2') {
            $_data='';
            if($aRow['type'] == 1)
            {   
                $_data.=$type_vouchers[1]['name'].'<br>';
                $id_invoice = explode(',', $aRow['id_old']);
                foreach ($id_invoice as $k => $v) {
                $invoice = get_table_where('tblpurchase_invoice',array('id'=>$v),'','row');
                $_data.=$invoice->code_invoice.'<br>';   
                }
            }elseif($aRow['type'] == 2)
            {
                $_data.=$type_vouchers[1]['name'].'<br>';
                $id_import = explode(',', $aRow['id_old']);
                foreach ($id_import as $key => $value) {
                    $import = get_table_where('tblpurchase_order',array('id'=>$value),'','row');
                    $_data.=$import->prefix.'-'.$import->code . '<br>';   
                }
            }elseif($aRow['type'] == 3)
            {
                if(!empty($aRow['type_vouchers']))
                {
                    $_data.=$type_vouchers[$aRow['type_vouchers']]['name'].'<br>';
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
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;