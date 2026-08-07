<?php

defined('BASEPATH') or exit('No direct script access allowed');


//$aColumns = [
//    'id',
//    'address_v2',
//    'name_v2',
//    'delivery_area',
//    'tblshipping_client.name',
//    'phone',
//    'address',
//    'tbldistrict.name',
//    'address_primary',
//];
$aColumns = [];
$aColumns[] = 'id';
if(empty($userid)) {
	$aColumns[] = '(SELECT company FROM tblclients WHERE tblclients.userid = tblshipping_client.client LIMIT 1) as company';
}
$aColumns[] = 'address_v2';
$aColumns[] = 'name_v2';
$aColumns[] = 'delivery_area';
$aColumns[] = 'tblshipping_client.name';
$aColumns[] = 'phone';
$aColumns[] = 'address';
$aColumns[] = 'tbldistrict.name';
$aColumns[] = 'address_primary';

$sIndexColumn = 'id';
$sTable       = db_prefix().'shipping_client';
$where = [];
if(!empty($userid)) {
	$where = ['AND client = ' . $userid];
}
$join[] = 'LEFT JOIN tbldistrict on tbldistrict.districtid = '.db_prefix().'shipping_client.district_shipping';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'tblshipping_client.client'
]);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $key => $aRow) {
    $row = [];
    $row[]    = ($key+1);
	if(empty($userid)) {
		$row[] = $aRow['company'];
	}
    $company = "";
    $company .= '<div class="row-options">';
    if (is_admin()) {
        $company .= '<a onclick="ChangeShippingClient(' . $aRow['id'] . ')">' . _l('view') . '</a>';
        $company .= ' | <a href="' . admin_url('clients/delete_shipping/' . $aRow['id']) . '" class="text-danger _delete_shipping">' . _l('delete') . '</a>';
    }

    $company .= '</div>';
    $row[]    = '<a onclick="ChangeShippingClient(' . $aRow['id'] . ')">'.$aRow['address'].'</a>'.$company;
    $row[]    = !empty($aRow['tblshipping_client.name']) ? $aRow['tblshipping_client.name'] : '';
    $row[]    = !empty($aRow['delivery_area']) ? $aRow['delivery_area'] : '';
    $row[]    = !empty($aRow['name_v2']) ? $aRow['name_v2'] : '';
    $row[]    = !empty($aRow['phone']) ? $aRow['phone'] : '';
    $row[]    = !empty($aRow['address_v2']) ? $aRow['address_v2'] : '';
    $row[]    = !empty($aRow['tbldistrict.name']) ? $aRow['tbldistrict.name'] : '';
    $row[]    = '<div class="checkbox"><input '.(empty($userid) ? 'disabled' : '').' class="check_address_primary" type="checkbox" value="' . $aRow['id'] . '" '.($aRow['address_primary'] == 1 ? 'checked' : '').'><label></label></div>';
    $output['aaData'][] = $row;
}
