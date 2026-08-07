<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('debt_suppliers', '', 'delete');

$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    'tblimport.id as id',
    'tblimport.date as date',
    '2 as type_invoice',
    'CONCAT(tblimport.prefix,"-",tblimport.code) as code',
    'tblimport.total as total_import',
    '(COALESCE(tblpurchase_order.amount_paid,0)+ COALESCE(tblpurchase_order.price_other_expenses,0))  as amount_paid_import',
    'tblpurchase_order.price_other_expenses as price_other_expenses_import',
    '7',
];
$sIndexColumn = 'id';
$sTable       = 'tblsuppliers';
$where        = [];
// array_push($where, 'AND ((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))');
array_push($where, 'AND (tblpurchase_order.status_pay != 2)');// AND tblpurchase_order.red_invoice = 0)');

$filter = [];
$join = [
    'LEFT JOIN tblimport ON tblimport.suppliers_id=tblsuppliers.id',
    'LEFT JOIN tblpurchase_order ON tblpurchase_order.id=tblimport.id_order',
    // 'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice',
];
/*if ($this->ci->input->post('filterStatuss')) {
    if (is_numeric($this->ci->input->post('filterStatuss'))) {
        if ($this->ci->input->post('filterStatuss') == 2) {
            array_push($where, 'AND tblpurchase_order.id = -1');
        }
    }
}

if ($this->ci->input->post('filterType')) {
    if (is_numeric($this->ci->input->post('filterType'))) {
        if ($this->ci->input->post('filterType') == 2) {
            $where[] = 'AND tblpurchase_order.id IN (
                            SELECT tblpurchase_order.id 
                            FROM tblpurchase_order 
                            WHERE tblpurchase_order.amount_paid < tblpurchase_order.totalAll_suppliers
                            AND tblpurchase_order.suppliers_id = tblsuppliers.id
                            AND DATEDIFF(CURDATE(), tblpurchase_order.date) > tblsuppliers.time_payment
                            AND tblsuppliers.time_payment <> 0
                        )';
        }
    }
}*/
array_push($where, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
array_push($where, 'AND tblsuppliers.id = ', $id);
array_push($where, 'AND tblpurchase_order.totalAll_suppliers > 0 ');
$date_start = to_sql_date($this->ci->input->post('date_start'));

if (!empty($date_start)) {
    array_push($where, 'AND tblimport.date >=', '"' . $date_start . '"');
}
$date_end = to_sql_date($this->ci->input->post('date_end'));
if (!empty($date_end)) {
    array_push($where, 'AND tblimport.date <=', '"' . $date_end . '"');
}
$type_invoice = $this->ci->input->post('type_invoice');
if (!empty($type_invoice)) {
    if ($type_invoice == 1) {
        array_push($where, 'AND tblimport.id = -1');
    }
}
$group_by = '';
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tblimport.red_invoice', 'tblpurchase_order.status',
], '', $group_by);
$output  = $result['output'];
$rResult = $result['rResult'];


$aColumns_v2 = [
    'tblpurchase_invoice.id as id',
    'tblpurchase_invoice.date_invoice as date',
    '1 as type_invoice',
    'CONCAT(tblpurchase_invoice.code_invoice) as code',
    'tblpurchase_invoice.total_price_befor_vat as total_import',
    '(COALESCE(tblpurchase_invoice.amount_paid,0)) as amount_paid_import',
    'tblpurchase_invoice.price_other_expenses as price_other_expenses_import',
    '7',
];
$sIndexColumn_v2 = 'id';
$sTable_v2       = 'tblsuppliers';
$where_v2        = [];
// array_push($where, 'AND ((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))');
array_push($where_v2, 'AND (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0)');

$filter_v2 = [];
$join_v2 = [
    'LEFT JOIN tblpurchase_order ON tblpurchase_order.suppliers_id=tblsuppliers.id',
    'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice',
];
if ($this->ci->input->post('filterStatuss')) {
    if (is_numeric($this->ci->input->post('filterStatuss'))) {
        if ($this->ci->input->post('filterStatuss') == 2) {
            array_push($where_v2, 'AND tblpurchase_invoice.id = -1');
        }
    }
}
// array_push($where_v2, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
array_push($where_v2, 'AND tblsuppliers.id = ', $id);
array_push($where_v2, 'AND tblpurchase_invoice.total_price_befor_vat > 0 ');
$type_invoice = $this->ci->input->post('type_invoice');
if (!empty($type_invoice)) {
    if ($type_invoice == 2) {
        array_push($where_v2, 'AND tblpurchase_invoice.id = -1');
    }
}
if (!empty($date_start)) {
    array_push($where_v2, 'AND tblpurchase_order.date >=', '"' . $date_start . '"');
}
$date_end = to_sql_date($this->ci->input->post('date_end'));
if (!empty($date_end)) {
    array_push($where_v2, 'AND tblpurchase_order.date <=', '"' . $date_end . '"');
}
$group_by_v2 = '';
$result_v2 = data_tables_init($aColumns_v2, $sIndexColumn_v2, $sTable_v2, $join_v2, $where_v2, [
    'red_invoice', 'tblpurchase_order.status', 'tblpurchase_invoice.id_import'
], '', $group_by_v2);

$output_v2  = $result_v2['output'];
$rResult_v2 = $result_v2['rResult'];



$aColumns_outsource = [
    'tbl_outsource.id as id',
    'tbl_outsource.date as date',
    '5 as type_invoice',
    'CONCAT(tbl_outsource.reference_no) as code',
    'tbl_outsource.grand_total as total_import',
    '(COALESCE(tbl_outsource.amount_paid,0)) as amount_paid_import',
    '0 as price_other_expenses_import',
    '7',
];
$sIndexColumn_outsource = 'id';
$sTable_outsource       = 'tblsuppliers';
$where_outsource        = [];
// array_push($where, 'AND ((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tbl_outsource.status != 2 AND tblpurchase_order.status_pay = 0))');
array_push($where_outsource, 'AND (tbl_outsource.status = "approved" AND tbl_outsource.status_pay != 2)');
if ($this->ci->input->post('filterStatuss')) {
    if (is_numeric($this->ci->input->post('filterStatuss'))) {
        if ($this->ci->input->post('filterStatuss') == 1) {
            array_push($where_outsource, 'AND tbl_outsource.id = -1');
        }
    }
}
$filter_outsource = [];
$join_outsource = [
    'LEFT JOIN tbl_outsource ON tbl_outsource.supplier_id=tblsuppliers.id'
];
array_push($where_outsource, 'AND tblsuppliers.id = ', $id);
array_push($where_outsource, 'AND tbl_outsource.grand_total > 0 ');
$type_invoice = $this->ci->input->post('type_invoice');
if (!empty($type_invoice)) {
    array_push($where_outsource, 'AND tbl_outsource.id = -1');
}
if (!empty($date_start)) {
    array_push($where_outsource, 'AND tbl_outsource.date >=', '"' . $date_start . '"');
}
$date_end = to_sql_date($this->ci->input->post('date_end'));
if (!empty($date_end)) {
    array_push($where_outsource, 'AND tbl_outsource.date <=', '"' . $date_end . '"');
}
$group_by_outsource = '';
$result_outsource = data_tables_init($aColumns_outsource, $sIndexColumn_outsource, $sTable_outsource, $join_outsource, $where_outsource, [
    'tbl_outsource.status',
], '', $group_by_outsource);
$output_outsource  = $result_outsource['output'];
$rResult_outsource = $result_outsource['rResult'];


$aColumns_services = [
    'tbl_services.id as id',
    'tbl_services.date as date',
    '6 as type_invoice',
    'CONCAT(tbl_services.prefix,"",tbl_services.code) as code',
    'tbl_services.subtotal as total_import',
    '(COALESCE(tbl_services.payment,0)) as amount_paid_import',
    '(COALESCE(tbl_services.payment,0)) as price_other_expenses_import',
    '7',
];
$sIndexColumn_services = 'id';
$sTable_services       = 'tblsuppliers';
$where_services        = [];
array_push($where_services, 'AND (tbl_services.status = 1 AND tbl_services.status_pay != 2)');

$filter_services = [];
$join_services = [
    'LEFT JOIN tbl_services ON tbl_services.suppliers_id=tblsuppliers.id',
];
if ($this->ci->input->post('filterStatuss')) {
    if (is_numeric($this->ci->input->post('filterStatuss'))) {
        if ($this->ci->input->post('filterStatuss') == 2) {
            array_push($where_services, 'AND tbl_services.id = -1');
        }
        if ($this->ci->input->post('filterStatuss') == 1) {
            array_push($where_services, 'AND tbl_services.id = -1');
        }
    }
}
array_push($where_services, 'AND tblsuppliers.id = ', $id);
array_push($where_services, 'AND tbl_services.subtotal > 0 ');
$type_invoice = $this->ci->input->post('type_invoice');
if (!empty($type_invoice)) {
    array_push($where_services, 'AND tbl_services.id = -1');
}
if (!empty($date_start)) {
    array_push($where_services, 'AND tbl_services.date >=', '"' . $date_start . '"');
}
$date_end = to_sql_date($this->ci->input->post('date_end'));
if (!empty($date_end)) {
    array_push($where_services, 'AND tbl_services.date <=', '"' . $date_end . '"');
}
$group_by_services = '';
$result_services = data_tables_init($aColumns_services, $sIndexColumn_services, $sTable_services, $join_services, $where_services, [
    'tbl_services.status', '0 as total_return',
], '', $group_by_services);
$output_services  = $result_services['output'];
$rResult_services = $result_services['rResult'];
if (!empty($rResult_services)) {
    $rResult = array_merge($rResult, $rResult_services);
}
if (!empty($rResult_v2)) {
    $rResult = array_merge($rResult, $rResult_v2);
}
if (!empty($rResult_outsource)) {
    $rResult = array_merge($rResult, $rResult_outsource);
}
$output['iTotalRecords'] = $output['iTotalRecords'] + $output_v2['iTotalRecords'] + $output_outsource['iTotalRecords'] + $output_services['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output['iTotalDisplayRecords'] + $output_v2['iTotalDisplayRecords'] + $output_outsource['iTotalDisplayRecords'] + $output_services['iTotalDisplayRecords'];
$j = 0;

$aColumns_G = [
    'id',
    'date',
    'type_invoice',
    'code',
    'total_import',
    'amount_paid_import',
    'price_other_expenses_import',
    '7',
];
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns_G); $i++) {
        if (strpos($aColumns_G[$i], 'as') !== false && !isset($aRow[$aColumns_G[$i]])) {
            $_data = $aRow[strafter($aColumns_G[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns_G[$i]];
        }
        if ($aColumns[$i] == '1') {
            $_data = $aRow['id'];
        }
        if ($aColumns_G[$i] == 'code') {
            if ($aRow['type_invoice'] == 1) {
                $_data = '';

                $id_invoice = explode(',', $aRow['id_import']);
                $count = count($id_invoice);
                if ($count == 1) {
                    $invoice = get_table_where('tblpurchase_order', array('id' => $id_invoice[0]), '', 'row');
                    $_data .= '<div class="text-center">' . $invoice->prefix . '-' . $invoice->code . '</div>';
                } else {
                    $_data .= '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . format_status_number_invoices_ch($count) . '
                        </button>';
                    $__data = '';
                    foreach ($id_invoice as $key => $value) {
                        $invoice = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');
                        $__data .= '<li><a>' . $invoice->prefix . '-' . $invoice->code . '</a></li>';
                    }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
                }
            }
        }
        if ($aColumns_G[$i] == 'date') {
            $_data = _dhau($aRow['date']);
        }
        if ($aColumns_G[$i] == 'id') {
            if ($aRow['type_invoice'] == 2) {
                if (($aRow['red_invoice'] == 0) && ($aRow['status'] > 2) && !empty($type_invoice)) {
                    $_data = '<div class="checkbox text-center"><input class="checkbox_ch" data-id="' . ($aRow['total_import'] - $aRow['amount_paid_import']) . '" type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
                } else {
                    $_data = '';
                }
            } elseif ($aRow['type_invoice'] == 5) {
                if ($this->ci->input->post('filterStatuss') == 2) {
                    $_data = '<div class="checkbox text-center"><input class="checkbox_ch" data-id="' . ($aRow['total_import'] - $aRow['amount_paid_import']) . '" type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
                } else {
                    $_data = '';
                }
            } else {
                if ($aRow['type_invoice'] == 6) {
                    $_data = '';
                } else {
                    if (!empty($type_invoice)) {
                        $_data = '<div class="checkbox text-center"><input class="checkbox_ch" data-id="' . ($aRow['total_import'] - $aRow['amount_paid_import']) . '" type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
                    } else {
                        $_data = '';
                    }
                }
            }
        }
        if ($aColumns_G[$i] == 'type_invoice') {
            $_data = format_status_invoice($aRow['type_invoice']);
            $_data = format_status_invoice($aRow['type_invoice']);
            if ($aRow['type_invoice'] == 1) {
                $_data .= '<br>' . $aRow['code'];
            }
        }
        if ($aColumns_G[$i] == 'amount_paid_import') {
            $_data = number_format($aRow['amount_paid_import'] - $aRow['price_other_expenses_import']);
        }
        if ($aColumns_G[$i] == '7') {
            $_data = number_format($aRow['total_import'] - $aRow['amount_paid_import']);
        }
        if ($aColumns_G[$i] == 'price_other_expenses_import') {
            $_data = number_format($aRow['price_other_expenses_import']);
        }
        if ($aColumns_G[$i] == 'total_import') {
            $_data = number_format($aRow['total_import']);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
