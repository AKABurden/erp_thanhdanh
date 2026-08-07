<?php

defined('BASEPATH') or exit('No direct script access allowed');

$search_date = $this->ci->input->post('search_date');

$aColumns = [
    'tblstock_out_request_product.id as id',
    'tblstock_out_request_product.date as date',
    'tblstock_out_request_product.code as code',
    'tbl_productions_orders.reference_no as production_order_code',
    'tblstock_out_request_product.note as note',
    '"" as action',
];
$sIndexColumn = 'id';
$sTable       = 'tblstock_out_request_product';
$join = [];
$join[] = 'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tblstock_out_request_product.production_order_id';

$where = [];
if (!empty($search_date)) {
    $searchDate = explode(' - ', $search_date);
    array_push($where, 'AND tblstock_out_request_product.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $rowIndex => $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $field = explode('as ', $aColumns[$i]);
        if (!empty($field[1])) {
            $field = $field[1];
        } else {
            $field = $aColumns[$i];
        }
        $_data = $aRow[$field];

        if ($field == 'id') {
            $_data = '<div class="text-center">'.($rowIndex+1).'</div>';
        } else if ($field == 'code') {
            // $_data = '<div class="text-center">'.($_data).'</div>';
            $_data = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/stock_out_request_product/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['code'] . '</a></div>';

        } else if ($field == 'date' || $field == 'deadline') {
            $_data = '<div class="text-center">'._d($_data).'</div>';
        } else if ($field == 'action') {
            $edit = '<a href="'.admin_url('stock_out_request_product/submit/'.$aRow['id']).'"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('stock_out_request_product') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                    <button href=\'' . base_url('admin/stock_out_request_product/delete/'.$aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                    <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('stock_out_request_product') . '</a>';

            $_data = '<div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
