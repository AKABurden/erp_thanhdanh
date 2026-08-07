    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('debt_suppliers', '', 'delete');

    $this->ci->db->query("SET sql_mode = ''");

    $aColumns = [
        'tbl_orders.id',
        'tbl_orders.date',
        'tbl_orders.reference_no',
        'tbl_orders.cost_delivery as total_import',
        'tbl_orders.price_other_expenses_delivery as amount_paid_import',
        '7',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tbl_orders';
    $where        = [];
    array_push($where, 'AND tbl_orders.status = "approved"');
    $filter = [];
    $join = [
     
    ];
    array_push($where, 'AND tbl_orders.transporter_id = ',$id);
    $group_by = '';
    $having = 'HAVING (total_import - amount_paid_import) > 0';
    $result = data_tables_init_having($aColumns, $sIndexColumn, $sTable, $join, $where, [

    ],'',$group_by,$having);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;
    foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tbl_orders.id') {
        $_data= '<div class="checkbox text-center"><input class="checkbox_ch" data-id="'.($aRow['total_import']-$aRow['amount_paid_import']).'" type="checkbox" value="' . $aRow['tbl_orders.id'] . '"><label></label></div>';
        }
        if ($aColumns[$i] == 'tbl_orders.date') {
        $_data =_dhau($aRow['tbl_orders.date']);
        }
        if ($aColumns[$i] == 'tbl_orders.price_other_expenses_delivery as amount_paid_import') {
            $_data = number_format($aRow['amount_paid_import']);      
        }
        if ($aColumns[$i] == '7') {
        $_data =number_format($aRow['total_import']-$aRow['amount_paid_import']);
        }
        if ($aColumns[$i] == 'tbl_orders.price_other_expenses as price_other_expenses_import') {
        $_data =number_format($aRow['price_other_expenses_import']); 
        }
        if ($aColumns[$i] == 'tbl_orders.cost_delivery as total_import') {
        $_data =number_format($aRow['total_import']);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
 
