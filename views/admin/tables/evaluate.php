<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblpurchase_order_evaluate.id',
    'CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code)',
    'tblpurchase_order.date',
    'tblsuppliers.company',
    'tblpurchase_order_evaluate.points',
    'tblpurchase_order_evaluate.staff_create',
    'tblpurchase_order_evaluate.date_create',
    'tblpurchase_order_evaluate.note'
];

$sIndexColumn = 'id';
$sTable       = 'tblpurchase_order_evaluate';

$join         = array(
    'LEFT JOIN tblpurchase_order on tblpurchase_order.id = tblpurchase_order_evaluate.id_purchase_order',
    'LEFT JOIN tblsuppliers on tblsuppliers.id = tblpurchase_order.suppliers_id'
);
$where         = array();
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblpurchase_order.id as id_purchase_order',
    'tblsuppliers.id as id_suppliers'
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tblpurchase_order_evaluate.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if ($aColumns[$i] == 'CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code)') {
            $_data = '<a onclick="view_purchase_order('.$aRow['id_purchase_order'].'); return false;">'.$aRow[$aColumns[$i]].'</a>';
        }
        else if ($aColumns[$i] == 'tblsuppliers.company') {
            $_data = '<a onclick="int_suppliers_view('.$aRow['id_suppliers'].'); return false;">'.$aRow[$aColumns[$i]].'</a>';
        }
        else if ($aColumns[$i] == 'tblpurchase_order.date') {
            $_data = _d($aRow['tblpurchase_order.date']);
        }
        else if ($aColumns[$i] == 'tblpurchase_order_evaluate.points') {
            if($aRow['tblpurchase_order_evaluate.points'] == 1) {
                $type = 'danger';
                $str = _l('rate_1');
            }
            else if($aRow['tblpurchase_order_evaluate.points'] == 2) {
                $type = 'warning';
                $str = _l('rate_2');
            }
            else if($aRow['tblpurchase_order_evaluate.points'] == 3) {
                $type = 'default';
                $str = _l('rate_3');
            }
            else if($aRow['tblpurchase_order_evaluate.points'] == 4) {
                $type = 'info';
                $str = _l('rate_4');
            }
            else if($aRow['tblpurchase_order_evaluate.points'] == 5) {
                $type = 'success';
                $str = _l('rate_5');
            }
            $_data = '<span class="inline-block label label-'.$type.'">'.$str.'</span>';
        }
        else if ($aColumns[$i] == 'tblpurchase_order_evaluate.date_create') {
            $_data = _d($aRow['tblpurchase_order_evaluate.date_create']);
        }
        else if ($aColumns[$i] == 'tblpurchase_order_evaluate.staff_create') {
            $_data = get_staff_full_name($aRow['tblpurchase_order_evaluate.staff_create']);
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
