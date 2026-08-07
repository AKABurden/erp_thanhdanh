<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$months_report = $this->ci->input->post('report_months');
$CI =& get_instance();
if ($months_report != '') {
    $custom_date_select = '';
    if (is_numeric($months_report)) {
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

$aColumns     = array(
    '1',
    '2',
    '3',
    '4',
    '5',
    '6',
    '7',
    '8'
);
$sIndexColumn = "staffid";
$sTable       = 'tblstaff';
$where        = array();

$staff_select = $this->ci->input->post('staff_select');
if($staff_select)
{
    if(is_numeric($staff_select)) {
        array_push($where, 'AND tblstaff.staffid = '.$staff_select);
    }
}

$join         = array(
    'LEFT JOIN tbl_orders ON tbl_orders.employee_id = tblstaff.staffid',
);
array_push($where, 'AND tbl_orders.grand_total > 0 ');
if(!empty($beginMonth) && !empty($endMonth) )
{
    array_push($where, 'AND tbl_orders.date BETWEEN "' . ($beginMonth) . '" and "' . ($endMonth) . '"');
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    'tblstaff.staffid',
),'GROUP BY tbl_orders.employee_id');
$output       = $result['output'];
$rResult      = $result['rResult'];

$output = $output;
$output['iTotalRecords'] = $output['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output['iTotalDisplayRecords'];
$footer_data['total1'] = 0; //nợ đầu kỳ
$footer_data['total2'] = 0; //có đầu kỳ
$footer_data['total3'] = 0; //nợ phát sinh
$footer_data['total4'] = 0; //có phát sinh
$footer_data['total5'] = 0; //nợ cuối kỳ
$footer_data['total6'] = 0; //có cuối kỳ
foreach ($rResult as $aRow) {
    $row = array();
    $row[] = '<span class="bold">'._l('als_staff').': '.get_staff_full_name($aRow['staffid']).'</span>';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row['DT_RowClass'] = 'row-header';
    $output['aaData'][] = $row;

    $total_result1 = 0;
    $total_result2 = 0;
    $total_result3 = 0;
    $total_result4 = 0;
    $total_result5 = 0;
    $total_result6 = 0;

    $CI->db->select('tbl_orders.*, SUM(tbl_orders.grand_total) as G_total, SUM(tbl_orders.total_payment) as P_total');
    $CI->db->where('tbl_orders.employee_id', $aRow['staffid']);
    $CI->db->where('tbl_orders.status', 'approved');
    $CI->db->where('tbl_orders.grand_total >', 0);
    if(!empty($beginMonth)&&!empty($endMonth)) {
        $CI->db->where('tbl_orders.date >= ', $beginMonth);
        $CI->db->where('tbl_orders.date <= ', $endMonth);
    }
    $CI->db->group_by('tbl_orders.customer_id');
    $get_order = $CI->db->get('tbl_orders')->result_array();
    foreach ($get_order as $key => $value) {
        $col1 = 0;
        $col2 = 0;
        $col3 = 0;
        $col4 = 0;
        $col5 = 0;
        $col6 = 0;

        $row = array();
        
        for ($i = 0; $i < count($aColumns); $i++) {
            $clients = get_table_where('tblclients',array('userid'=>$value['customer_id']),'','row');

            if($aColumns[$i] == '1') {
                $_data = $clients->zcode;
            }
            else if($aColumns[$i] == '2') {
                $_data = $clients->company;
            }
            else if($aColumns[$i] == '3') {
                $amount_begin = $clients->debt_begin;
                $debt_begin = 0;
                if(!empty($beginMonth)&&!empty($endMonth)) {
                    //tổng nợ
                    $CI->db->select('tbl_orders.*');
                    $CI->db->where('tbl_orders.customer_id', $value['customer_id']);
                    $CI->db->where('tbl_orders.employee_id', $aRow['staffid']);
                    $CI->db->where('tbl_orders.status', 'approved');
                    $CI->db->where('tbl_orders.date < ', $beginMonth);
                    $get_all_order = $CI->db->get('tbl_orders')->result_array();
                    foreach ($get_all_order as $key_all_order => $value_all_order) {
                        $debt_begin += $value_all_order['grand_total'] - $value_all_order['total_payment'];
                    }
                    $debt_begin = $amount_begin + $debt_begin;

                    //tổng thu khác
                    $total_payslips = 0;
                    $CI->db->select('tblother_payslips_coupon.*');
                    $CI->db->where('tblother_payslips_coupon.objects', 1);
                    $CI->db->where('tblother_payslips_coupon.objects_id', $value['customer_id']);
                    $CI->db->where('tblother_payslips_coupon.date < ', $beginMonth);
                    $get_payslips = $CI->db->get('tblother_payslips_coupon')->result_array();
                    foreach ($get_payslips as $key_payslips => $value_payslips) {
                        $total_payslips += $value_payslips['total'];
                    }
                    $debt_begin = $debt_begin - $total_payslips;
                }
                else {
                    $debt_begin = $amount_begin;
                }

                if($debt_begin > 0) {
                    $_data = number_format($debt_begin);
                    $col1 = $debt_begin;
                    $total_result1 += $debt_begin;
                }
                else {
                    $_data = 0;
                    $col1 = 0;
                    $total_result1 += 0;
                }
            }
            else if($aColumns[$i] == '4') {
                $amount_begin = $clients->debt_begin;
                $debt_begin = 0;
                if(!empty($beginMonth)&&!empty($endMonth)) {
                    //tổng nợ
                    $CI->db->select('tbl_orders.*');
                    $CI->db->where('tbl_orders.customer_id', $value['customer_id']);
                    $CI->db->where('tbl_orders.employee_id', $aRow['staffid']);
                    $CI->db->where('tbl_orders.status', 'approved');
                    $CI->db->where('tbl_orders.date < ', $beginMonth);
                    $get_all_order = $CI->db->get('tbl_orders')->result_array();
                    foreach ($get_all_order as $key_all_order => $value_all_order) {
                        $debt_begin += $value_all_order['grand_total'] - $value_all_order['total_payment'];
                    }
                    $debt_begin = $amount_begin + $debt_begin;

                    //tổng thu khác
                    $total_payslips = 0;
                    $CI->db->select('tblother_payslips_coupon.*');
                    $CI->db->where('tblother_payslips_coupon.objects', 1);
                    $CI->db->where('tblother_payslips_coupon.objects_id', $value['customer_id']);
                    $CI->db->where('tblother_payslips_coupon.date < ', $beginMonth);
                    $get_payslips = $CI->db->get('tblother_payslips_coupon')->result_array();
                    foreach ($get_payslips as $key_payslips => $value_payslips) {
                        $total_payslips += $value_payslips['total'];
                    }
                    $debt_begin = $debt_begin - $total_payslips;
                }
                else {
                    $debt_begin = $amount_begin;
                }

                if($debt_begin < 0) {
                    $_data = number_format(abs($debt_begin));
                    $col2 = abs($debt_begin);
                    $total_result2 += abs($debt_begin);
                }
                else {
                    $_data = 0;
                    $col2 = 0;
                    $total_result2 += 0;
                }
            }
            else if($aColumns[$i] == '5') {
                $_data = number_format($value['G_total']);
                $col3 = $value['G_total'];
                $total_result3 += $value['G_total'];
            }
            else if($aColumns[$i] == '6') {
                $CI->db->select('tbl_orders.id, tbl_orders.customer_id');
                $CI->db->where('tbl_orders.customer_id', $value['customer_id']);
                $CI->db->where('tbl_orders.employee_id', $aRow['staffid']);
                $CI->db->where('tbl_orders.status', 'approved');
                if(!empty($beginMonth)&&!empty($endMonth)) {
                    $CI->db->where('tbl_orders.date >= ', $beginMonth);
                    $CI->db->where('tbl_orders.date <= ', $endMonth);
                }
                $get_order_check = $CI->db->get('tbl_orders')->result_array();
                $total_payslips = 0;
                foreach ($get_order_check as $key_check => $value_check) {
                    $CI->db->select('SUM(tblother_payslips_coupon.total) as total');
                    $CI->db->where('tblother_payslips_coupon.objects_id', $value_check['customer_id']);
                    $CI->db->where('tblother_payslips_coupon.objects', 1);
                    $CI->db->where('tblother_payslips_coupon.type_vouchers', 5);
                    $CI->db->where('tblother_payslips_coupon.vouchers_id', $value_check['id']);
                    if(!empty($beginMonth)&&!empty($endMonth)) {
                        $CI->db->where('tblother_payslips_coupon.date >= ', $beginMonth);
                        $CI->db->where('tblother_payslips_coupon.date <= ', $endMonth);
                    }
                    $get_payslips = $CI->db->get('tblother_payslips_coupon')->row();
                    $total_payslips += $value['P_total'] + $get_payslips->total;
                }
                $total_payslips = $total_payslips;
                $_data = number_format($total_payslips);
                $col4 = $total_payslips;
                $total_result4 += $total_payslips;
            }
            else if($aColumns[$i] == '7') {
                $total = $col1 + $col3 - $col2 - $col4;
                if($total > 0) {
                    $_data = number_format($total);
                    $total_result5 += $total;
                }
                else {
                    $_data = 0;
                }
            }
            else if($aColumns[$i] == '8') {
                $total = $col1 + $col3 - $col2 - $col4;
                if($total < 0) {
                    $_data = number_format($total);
                    $total_result6 += $total;
                }
                else {
                    $_data = 0;
                }
            }
            $row[] = $_data;
        }
        $output['aaData'][] = $row;
    }
    $footer_data['total1'] += $total_result1;
    $footer_data['total2'] += $total_result2;
    $footer_data['total3'] += $total_result3;
    $footer_data['total4'] += $total_result4;
    $footer_data['total5'] += $total_result5;
    $footer_data['total6'] += $total_result6;

    $row = array();
    $row[] = '<span class="bold">'._l('total_group').': '.get_staff_full_name($aRow['staffid']).'</span>';
    $row[] = '';
    $row[] = '<span class="bold">'.number_format($total_result1).'</span>';
    $row[] = '<span class="bold">'.number_format($total_result2).'</span>';
    $row[] = '<span class="bold">'.number_format($total_result3).'</span>';
    $row[] = '<span class="bold">'.number_format($total_result4).'</span>';
    $row[] = '<span class="bold">'.number_format($total_result5).'</span>';
    $row[] = '<span class="bold">'.number_format($total_result6).'</span>';
    $row['DT_RowClass'] = 'row-footer';
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;