<?php
defined('BASEPATH') or exit('No direct script access allowed');
$months_report = $this->ci->input->post('report_months');
$CI = &get_instance();
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
$sIndexColumn = "userid";
$sTable       = 'tblclients';
$where        = array();

$customer_select = $this->ci->input->post('customer_select');
if ($customer_select) {
    $customer_id = explode('__', $customer_select);
    if (is_numeric($customer_id[1])) {
        array_push($where, 'AND tblclients.userid = ' . $customer_id[1]);
    }
}

$join         = array(
    'JOIN tbl_orders ON tbl_orders.customer_id = tblclients.userid',
);
array_push($where, 'AND tbl_orders.grand_total > 0 ');
if(!empty($beginMonth) && !empty($endMonth) )
{
    array_push($where, 'AND tbl_orders.date BETWEEN "' . ($beginMonth) . '" and "' . ($endMonth) . '"');
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblclients.userid',
    'tblclients.company',
));
$output       = $result['output'];
$rResult      = $result['rResult'];

$output = $output;
$output['iTotalRecords'] = $output['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output['iTotalDisplayRecords'];
$footer_data['total1'] = 0; //nợ đầu kỳ
$footer_data['total2'] = 0; //có đầu kỳ
$footer_data['total3'] = 0; //nợ phát sinh
$footer_data['total4'] = 0; //có phát sinh
foreach ($rResult as $aRow) {
    $row = array();
    $row[] = '<span class="bold">' . _l('client') . ': ' . $aRow['company'] . '</span>';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row['DT_RowClass'] = 'row-header';
    $output['aaData'][] = $row;

    $total_result = 0;
    $col1 = 0;
    $col2 = 0;
    $col3 = 0;
    $col4 = 0;
    $CI->db->select('tbl_orders.*');
    $CI->db->where('tbl_orders.customer_id', $aRow['userid']);
    $CI->db->where('tbl_orders.status', 'approved');
    $CI->db->where('tbl_orders.grand_total >', 0);
    if (!empty($beginMonth) && !empty($endMonth)) {
        $CI->db->where('tbl_orders.date >= ', $beginMonth);
        $CI->db->where('tbl_orders.date <= ', $endMonth);
    }
    $get_order = $CI->db->get('tbl_orders')->result_array();
    foreach ($get_order as $key => $value) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            if ($aColumns[$i] == '1') {
                $_data = _d($value['date']);
            } else if ($aColumns[$i] == '2') {
                $_data = _d($value['date']);
            } else if ($aColumns[$i] == '3') {
                $_data = $value['reference_no'];
            } else if ($aColumns[$i] == '4') {
                $_data = $value['note'];
            } else if ($aColumns[$i] == '5') {
                //tổng thu khác
                $total_other = get_table_where('tblother_payslips_coupon', array('objects' => 1, 'objects_id' => $value['customer_id'], 'type_vouchers' => 5, 'vouchers_id' => $value['id']));
                $sum_other = 0;
                foreach ($total_other as $key_other => $value_other) {
                    $sum_other += $value_other['total'];
                }
                //end
                $debt = $value['grand_total'];
                if ($debt > 0) {
                    $_data = number_format($debt);
                    $total_result = $total_result - $debt;
                    $col1 = $col1 + $debt;
                } else {
                    $_data = 0;
                }
            } else if ($aColumns[$i] == '6') {
                //tổng thu khác
                $total_other = get_table_where('tblother_payslips_coupon', array('objects' => 1, 'objects_id' => $value['customer_id'], 'type_vouchers' => 5, 'vouchers_id' => $value['id']));
                $sum_other = 0;
                foreach ($total_other as $key_other => $value_other) {
                    $sum_other += $value_other['total'];
                }
                //end
                $debt = $value['total_payment'] + $sum_other;
                $_data = number_format($debt);
                $total_result = $total_result + $debt;
                $col2 = $col2 + $debt;
            } else if ($aColumns[$i] == '7') {
                if ($total_result < 0) {
                    $_data = number_format(abs($total_result));
                    $col3 = abs($total_result);
                } else {
                    $_data = 0;
                    $col3 = 0;
                }
            } else if ($aColumns[$i] == '8') {
                if ($total_result > 0) {
                    $_data = number_format(abs($total_result));
                    $col4 = abs($total_result);
                } else {
                    $_data = 0;
                    $col4 = 0;
                }
            }
            $row[] = $_data;
        }
        $output['aaData'][] = $row;
    }
    $footer_data['total1'] += $col1;
    $footer_data['total2'] += $col2;
    $footer_data['total3'] += $col3;
    $footer_data['total4'] += $col4;

    $row = array();
    $row[] = '<span class="bold">' . _l('total_group') . ': ' . $aRow['company'] . '</span>';
    $row[] = '';
    $row[] = '';
    $row[] = '';
    $row[] = '<span class="bold">' . number_format($col1) . '</span>';
    $row[] = '<span class="bold">' . number_format($col2) . '</span>';
    $row[] = '<span class="bold">' . number_format($col3) . '</span>';
    $row[] = '<span class="bold">' . number_format($col4) . '</span>';
    $row['DT_RowClass'] = 'row-footer';
    $output['aaData'][] = $row;
}
$output['sums'] = $footer_data;
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;
