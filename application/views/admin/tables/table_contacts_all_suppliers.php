<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('suppliers', '', 'delete');



$aColumns = [
    db_prefix().'contacts_suppliers.name as name',
    'tblsuppliers.company',
    db_prefix().'contacts_suppliers.email as email',
    db_prefix().'contacts_suppliers.phone as phone',
    db_prefix().'contacts_suppliers.birthday as birthday',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'contacts_suppliers';
$where        = [];
// Add blank where all filter can be stored
$filter = [];
$join = [
    'LEFT JOIN tblsuppliers  ON tblsuppliers.id=tblcontacts_suppliers.id_supplers'
];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
 db_prefix().'contacts_suppliers.id','tblcontacts_suppliers.id_supplers'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

    // Company
    $company  = $aRow['name'];
    $isPerson = false;

    if ($company == '') {
        $company  = _l('no_company_view_profile');
        $isPerson = true;
    }
    $company = $company;
    $company .= '<div class="row-options">';
    
    if ($hasPermissionDelete) {
        $company .= '<a href="' . admin_url('suppliers/delete_contact/' . $aRow['id']) . '" class="text-danger delete-remind">' . _l('delete') . '</a>';
    }

    $company .= '</div>';

    $row[] = $company;
    $row[] = ($aRow['tblsuppliers.company'] ? '<a href="#" onclick="int_suppliers_view('.$aRow['id_supplers'].',false); return false;" >' .$aRow['tblsuppliers.company']. '</a>' : '');
    // Primary contact email
    $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    // Primary contact phone
    $row[] = ($aRow['phone'] ? '<a href="tel:' . $aRow['phone'] . '">' . $aRow['phone'] . '</a>' : '');
    //vat

    // Toggle active/inactive customer

    $row[] = _d($aRow['birthday']);



    $row['DT_RowClass'] = 'has-row-options';



    $row = hooks()->apply_filters('suppliers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
