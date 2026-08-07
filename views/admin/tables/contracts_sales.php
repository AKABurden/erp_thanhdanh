<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'tbl_contracts_sales.id',
	'2',
	'tblclients.company_short',
	'tbl_contracts_sales.subject',
	'tbl_contracts_sales.quote_id',
	'5',
	'tbl_contracts_sales.amount',
	'tbl_contracts_sales.date_start',
	'tbl_contracts_sales.date_end',
];
$sIndexColumn = 'id';
$sTable = 'tbl_contracts_sales';
$where = array();
$join = array(
	'LEFT JOIN tblclients ON tblclients.userid = tbl_contracts_sales.customer_id'
);
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
	'tbl_contracts_sales.prefix',
	'tbl_contracts_sales.code',
	'tbl_contracts_sales.arr_staff',
));
$output = $result['output'];
$rResult = $result['rResult'];
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		$_data = $aRow[$aColumns[$i]];
		if ($aColumns[$i] == 'tbl_contracts_sales.id') {
			$_data = ($currentall + 1) - ($currentPage + $r + 1);
		} else if ($aColumns[$i] == '2') {
			$_data = '<a href="' . admin_url('contracts_sales/detail/' . $aRow['tbl_contracts_sales.id']) . '">' . $aRow['prefix'] . $aRow['code'] . '</a>';
		}
		else if ($aColumns[$i] == 'tbl_contracts_sales.quote_id') {
			$_data = '';
			if (!empty($aRow['tbl_contracts_sales.quote_id'])) {
				$quote = get_table_where('tbl_orders', array('id' => $aRow['tbl_contracts_sales.quote_id']), '', 'row');
				if (empty($quote)) {
					$_data = '';
				} else {
					$_data = $quote->reference_no;
				}
			}
		} else if ($aColumns[$i] == '5') {
			$_data = "";
			$staff = explode(',', $aRow['arr_staff']);
			foreach ($staff as $key => $value) {
				$_data .= get_staff_full_name($value) . ',<br>';
			}
			$_data = trim($_data, ',<br>');
		} else if ($aColumns[$i] == 'tbl_contracts_sales.amount') {
			$_data = number_format($aRow['tbl_contracts_sales.amount']);
		} else if ($aColumns[$i] == 'tbl_contracts_sales.amount') {
			$_data = number_format($aRow['tbl_contracts_sales.amount']);
		} else if ($aColumns[$i] == 'tbl_contracts_sales.date_start' || $aColumns[$i] == 'tbl_contracts_sales.date_end') {
			$_data = _d($aRow[$aColumns[$i]]);
		}
		$row[] = $_data;
	}
	$_outputStatus = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">
            <li><a href="' . admin_url('contracts_sales/detail/' . $aRow['tbl_contracts_sales.id']) . '"><i class="fa fa-edit"></i> ' . _l('edit') . '</a></li>
            <li><a href="' . admin_url('contracts_sales/pdf/' . $aRow['tbl_contracts_sales.id']) . '?print=true" target="_blank"><i class="fa fa-print"></i> ' . _l('print_contracts') . '</a></li>
            <li><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/contracts_sales/sendMail/' . $aRow['tbl_contracts_sales.id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-envelope-o"></i> ' . lang('Email') . '</a></li>
            <li><a class="text-danger delete-remind" href="' . admin_url('contracts_sales/delete/' . $aRow['tbl_contracts_sales.id']) . '"><i class="fa fa-remove"></i> ' . _l('delete') . '</a></li>
        </ul>
    </div>';
	$row[] = $_outputStatus;
	$output['aaData'][] = $row;
}
