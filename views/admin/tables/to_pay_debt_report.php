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
            // $this->db->where($custom_date_select);
        }

    $aColumns = [
        'concat(tblsuppliers.prefix,"-",tblsuppliers.code) as code',
        'tblsuppliers.company',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblsuppliers';
    $where        = [];
   
    $filter = [];
    $join = [
        
    ];
    $search_id_suppliers = $this->ci->input->post('search_id_suppliers');
    if(!empty($search_id_suppliers))
    {
        array_push($where, 'AND tblsuppliers.id IN('.implode(',', $search_id_suppliers).')');
    }
    $group_by = 'GROUP BY tblsuppliers.id';
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        'tblsuppliers.id'
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
        $getStart_debt_supplierts = getStart_debt_supplierts_v2($aRow['id'],$beginMonth);
        $getStart_pay_slip_ch = getStart_pay_slip_ch($aRow['id'],$beginMonth);

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i]=='1')
        {   
            $total = $getStart_pay_slip_ch - $getStart_debt_supplierts;
            if($total < 0)
            {
                $total = abs($total);
            }else
            {
                $total = 0;
            }
            $footer_data['debt_start']+= $total;

            $_data = number_format($total);
        }
        if($aColumns[$i]=='2')
        {
            $total = $getStart_pay_slip_ch - $getStart_debt_supplierts;
            if($total > 0)
            {
                $total = abs($total);
            }else
            {
                $total = 0;
            }
            $footer_data['pay_start']+=$total;
            $_data = number_format($total);
        }
        if($aColumns[$i]=='3')
        {
            $debt_supplierts = debt_supplierts_v2($aRow['id'],$beginMonth,$endMonth);
            $footer_data['debt']+=$debt_supplierts;
            $_data = number_format($debt_supplierts);
        }
        if($aColumns[$i]=='4')
        {
            $pay_slip_ch = pay_slip_ch($aRow['id'],$beginMonth,$endMonth);
            $footer_data['pay']+=$pay_slip_ch;
            $_data = number_format($pay_slip_ch);
        }
        if($aColumns[$i]=='5')
        {
            $total = $pay_slip_ch + $getStart_pay_slip_ch - ($getStart_debt_supplierts + $debt_supplierts);
            if($total < 0)
            {
                $total = abs($total);
            }else
            {
                $total = 0;
            }
            $footer_data['debt_end']+=$total;
            $_data = number_format($total);
        }
        if($aColumns[$i]=='6')
        {
            $total = $pay_slip_ch + $getStart_pay_slip_ch - ($getStart_debt_supplierts + $debt_supplierts);
            if($total < 0)
            {
                $total = 0;
            }
            $footer_data['pay_end']+=$total;
            $_data = number_format($total);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
    foreach ($footer_data as $key => $total) {
        $footer_data[$key]=number_format($total);
    }
    $output['sums']              = $footer_data;