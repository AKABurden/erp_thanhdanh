<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'tblcustomers_groups.id as id',
	'tblcustomers_groups.code as code',
	'tblcustomers_groups.name as name',
	'tblcustomers_groups.classify as classify',
	'tblcustomers_groups.certification_group as certification_group',
	'tblcustomers_groups.applied_standard as applied_standard',
	'tblcustomers_groups.verification_unit as verification_unit',
	'tblcustomers_groups.date_start as date_start',
	'tblcustomers_groups.date_end as date_end',
	'tblcustomers_groups.product_standards as product_standards',
	'tblcustomers_groups.percent_discount as percent_discount',
];

$sIndexColumn = 'id';
$sTable       = 'tblcustomers_groups';

$join = [];
$where = [];
$where[] = 'AND id_parent = 0';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'tblcustomers_groups.id',
	'tblcustomers_groups.color as color',
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$i = 0;
foreach ($rResult as $aRow) {
    $i ++;
    $row = [];
	$row[] = '<div class="text-center">'.$i.'</div>';
    $row[]  = '<a href="#" onclick="edit('.$aRow['id'].')">' . $aRow['code'] . '</a>';
    $row[]  = '<a href="#" onclick="edit('.$aRow['id'].')">' . $aRow['name'] . '</a>';
    $row[]  = '<div>'.$aRow['classify'].'</div>';
    $row[]  = '<div>'.$aRow['certification_group'].'</div>';
    $row[]  = '<div>'.$aRow['applied_standard'].'</div>';
    $row[]  = '<div>'.$aRow['verification_unit'].'</div>';
    $row[]  = '<div>'.(!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '').'</div>';
    $row[]  = '<div>'.(!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '').'</div>';
    $row[]  = '<div>'.$aRow['product_standards'].'</div>';
    $row[]  = '<div class="text-center">'.$aRow['percent_discount'].'</div>';
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit('.$aRow['id'].')']);
    $row[]   = $options .= icon_btn('clients/delete_group/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $output['aaData'][] = $row;

}
