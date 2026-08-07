<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = array(
	'tblgroup_price.id as id',
	'name_price',
//	'year',
	'tblclients.company as company',
//	'1',
);
$sIndexColumn = "id";
$sTable = 'tblgroup_price';
$where = array(//    'AND id_lead="' . $rel_id . '"'
);
$join = array(
	'LEFT JOIN tblclients ON tblclients.userid = tblgroup_price.client'
);
if ($this->ci->input->post('price_name')) {
	array_push($where, 'AND tblgroup_price.id = ' . $this->ci->input->post('price_name'));
}
if ($this->ci->input->post('client_search')) {
	array_push($where, 'AND tblgroup_price.client = ' . $this->ci->input->post('client_search'));
}
if ($this->ci->input->post('year_search')) {
	array_push($where, 'AND tblgroup_price.year = "' . $this->ci->input->post('year_search') . '"');
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblgroup_price.client'));
$output = $result['output'];
$rResult = $result['rResult'];
$j = 0;
foreach ($rResult as $key => $aRow) {
	$row = [];
	$j++;
	$row[] = '<div class="text-center"> ' . ($key + 1) . ' </div>';
	$row[] = '<a href="javascript::void(0);"  onclick="view_detail(' . $aRow['id'] . '); return false;">' . $aRow['name_price'] . '</a>';
//	$row[] = $aRow['year'];
	$row[] = '<a  target="_blank" href="'.admin_url('clients/client/' . $aRow['client']).'?view">' . $aRow['company'] . '</a>';
	$_data = '';
	if (is_admin()) {
		$_data .= '<a href="#" class="btn btn-info btn-icon" onclick="updateprice(' . $aRow['id'] . '); return false;">Cập nhật giá đơn hàng</a>';
		$_data .= '<a href="#" class="btn btn-default btn-icon" onclick="view_detail(' . $aRow['id'] . '); return false;"><i class="fa fa-eye"></i></a>';
		$_data .= icon_btn('import_price_group/print_pdf/' . $aRow['id'], 'file-pdf-o', 'btn-warning', array('target' => '_blank'));
		$row[] = '<div class="pull-right">' . $_data . icon_btn('import_price_group/delete_import/' . $aRow['id'], 'remove', 'btn-danger delete-remind') . '</div>';
	} else {
		$_data .= '<a href="#" class="btn btn-info btn-icon" onclick="updateprice(' . $aRow['id'] . '); return false;">Cập nhật giá đơn hàng</a>';
		$row[] = '<div class="pull-right">' . $_data  . '</div>';
	}
	$output['aaData'][] = $row;
}