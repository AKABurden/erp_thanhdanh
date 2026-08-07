<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$aColumns     = array(
    'tblsuppliers_price.id',
    'name_price',
    'year',
    'tblsuppliers.code',
    'tblsuppliers.company',

);
$sIndexColumn = "id";
$sTable       = 'tblsuppliers_price';
$where        = array(
//    'AND id_lead="' . $rel_id . '"'
);
$join         = array(
    'LEFT JOIN tblsuppliers  ON tblsuppliers_price.supplier_id=tblsuppliers.id'
);
if ($this->ci->input->post('price_name')) {
    array_push($where, 'AND tblsuppliers_price.id = '.$this->ci->input->post('price_name'));
}
if ($this->ci->input->post('suppliers_name')) {
    array_push($where, 'AND tblsuppliers.id = '.$this->ci->input->post('suppliers_name'));
}

if ($this->ci->input->post('materials_search')) {
    array_push($where,'AND EXISTS (
        SELECT 1
        FROM tblsuppliers_price_detail
        WHERE tblsuppliers_price_detail.supplier_price_id = tblsuppliers_price.id
        AND tblsuppliers_price_detail.product_id = ' . $this->ci->input->post('materials_search') . ' AND tblsuppliers_price_detail.product_type = "nvl"
    )');
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    // 'tblroles.name',
    // 'tblroles.roleid'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
//var_dump($rResult);die();


$j=0;
foreach ($rResult as $key => $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $stt = $key+1;
        if ($aColumns[$i] == 'name_price') {    
            $_data='<a href="javascript::void(0);"  onclick="view_supplier_detail(' . $aRow['tblsuppliers_price.id'] . '); return false;">'.$aRow['name_price'].'</a>';
        }
        if ($aColumns[$i] == 'tblsuppliers_price.id'){
            $_data = '<div class="text-center"> '.$stt.' </div>';
        }
        $row[] = $_data;
    }
    $_data = '';
    if (has_permission('import_price', '', 'edit')) {
        $_data .= '<a href="#" class="btn btn-default btn-icon" onclick="view_supplier_detail(' . $aRow['tblsuppliers_price.id'] . '); return false;"><i class="fa fa-eye"></i></a>';
    }
    $_data .= icon_btn('import_price/print_pdf/'. $aRow['tblsuppliers_price.id'] , 'file-pdf-o', 'btn-warning',array('target'=>'_blank'));
    if (has_permission('import_price', '', 'delete')) {
        $_data .= icon_btn('import_price/delete_import/'. $aRow['tblsuppliers_price.id'] , 'remove', 'btn-danger delete-remind');
    }
    $row[] = $_data;

    $output['aaData'][] = $row;
}