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
        'id',
        'name',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblpayment_modes';
    $where        = [];
    // if(!empty($beginMonth)&&!empty($endMonth))
    // {
    //     array_push($where, 'AND date_vouchers >='.'"'.$beginMonth.' 00:00:00"');  
    //     array_push($where, 'AND date_vouchers <='.'"'.$endMonth.' 23:59:59"');
    // }
    $filter = [];
    $join = [
        // 'LEFT JOIN tblclients  ON tblclients.userid=tblvouchers_coupon.customer',
        // 'LEFT JOIN tblstaff  ON tblstaff.staffid=tblvouchers_coupon.staff'
    ];
    $group_by = '';
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        'opening_balance',
    ],$group_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;
$footer_data['debt_start'] = 0; //cong no dau ki
$footer_data['pay_start'] = 0; // Chi đầu kì
$footer_data['debt'] = 0; // Công nợ phát sinh
$footer_data['pay'] = 0; // Chi phát sinh
$footer_data['debt_end'] = 0; // công nợ cuối kì
$footer_data['pay_end'] = 0; // chi cuối kì
foreach ($rResult as $aRow) {
    $row = array();
    $j++;

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i]=='2')
        {
            $getStart_coupons = getStart_payslips($aRow['id'],$beginMonth);
            $footer_data['pay_start']+= $getStart_coupons;
            $_data = number_format($getStart_coupons);
            if($_data == 0)
            {
                $_data='';
            }
        }
        if($aColumns[$i]=='1')
        {
            $getStart_payslips = getStart_coupons($aRow['id'],$beginMonth)+$aRow['opening_balance'];
            $footer_data['debt_start']+= $getStart_payslips;

            $_data = number_format($getStart_payslips);
            if($_data == 0)
            {
                $_data='';
            }
        }
        if($aColumns[$i]=='4')
        {
            $getStart_coupons_ch = getStart_payslips($aRow['id'],$beginMonth,$endMonth);
            $footer_data['pay']+= $getStart_coupons_ch;
            $_data = number_format($getStart_coupons_ch);
            if($_data == 0)
            {
                $_data='';
            }
        }
        if($aColumns[$i]=='3')
        {
            $getStart_payslips_ch = getStart_coupons($aRow['id'],$beginMonth,$endMonth);
            $footer_data['debt']+= $getStart_payslips_ch;
            $_data = number_format($getStart_payslips_ch);
            if($_data == 0)
            {
                $_data='';
            }
        }
        if($aColumns[$i]=='6')
        {
            $total = ($getStart_coupons + $getStart_coupons_ch) - ($getStart_payslips + $getStart_payslips_ch);
            if($total > 0)
            {
                $total = $total;
            }else
            {
                $total = 0;
            }
            $footer_data['pay_end']+=$total;
            $_data = number_format($total);
            if($_data == 0)
            {
                $_data='';
            }
        }
        if($aColumns[$i]=='5')
        {
            $total = ($getStart_payslips + $getStart_payslips_ch) - ($getStart_coupons + $getStart_coupons_ch);
            if($total < 0)
            {
                $total = 0;
            }
            $footer_data['debt_end']+=$total;
            $_data = number_format($total);
            if($_data == 0)
            {
                $_data='';
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
    foreach ($footer_data as $key => $total) {
        $footer_data[$key]=number_format($total);
        if($footer_data[$key] == 0)
        {
            $footer_data[$key]='';
        }
    }
    $output['sums']              = $footer_data;
    
$output['title_excel'] = [
    'Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') .' - '. (!empty($endMonth) ? _dt($endMonth) : ''),
];