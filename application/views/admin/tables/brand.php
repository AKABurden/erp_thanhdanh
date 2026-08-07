<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbl_brand.id as id',
    'tbl_brand.code as code',
    'tbl_brand.name as name',
    'tbl_brand.classify as classify',
    'tbl_brand.certification_group as certification_group',
    'tbl_brand.applied_standard as applied_standard',
    'tbl_brand.verification_unit as verification_unit',
    'tbl_brand.date_start as date_start',
    'tbl_brand.date_end as date_end',
    'tbl_brand.product_standards as product_standards',
    'tbl_brand.percent_discount as percent_discount',
];

$sIndexColumn = 'id';
$sTable       = 'tbl_brand';

$join = [];
$where = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tbl_brand.id',
    'tbl_brand.color as color',
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
    $row[]   = $options .= icon_btn('clients/delete_brand/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $output['aaData'][] = $row;

}
