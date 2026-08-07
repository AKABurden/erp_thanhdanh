<?php

defined('BASEPATH') or exit('No direct script access allowed');

$search_date = $this->ci->input->post('search_date');
$client_id = $this->ci->input->post('client_id');

$tb_customer_group = '(
        SELECT
            GROUP_CONCAT(tblcustomers_groups.name SEPARATOR ", ") as name,
            tblcustomer_groups.customer_id as client_id
        FROM tblcustomer_groups
        JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
        GROUP BY tblcustomer_groups.customer_id
    ) AS tb_customer_group';

$aColumns = [
    'tblquotation_request.id as id',
    'tblquotation_request.date as date',
    'tblquotation_request.code as code',
    'tb_customer_group.name as client_group_name',
    'tblclients.zcode as client_code',
    'tblclients.company as client_name',
    '"" as action',
];
$sIndexColumn = 'id';
$sTable       = 'tblquotation_request';
$join = [];
$join[] = 'LEFT JOIN '.$tb_customer_group.' ON tb_customer_group.client_id = tblquotation_request.client_id';
$join[] = 'LEFT JOIN tblclients ON tblclients.userid = tblquotation_request.client_id';

$where = [];
if (!empty($search_date)) {
    $searchDate = explode(' - ', $search_date);
    array_push($where, 'AND tblquotation_request.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
}

if (!empty($client_id)) {
    $client = explode('__', $client_id)[1] ?? null;
    if (!empty($client)) {
        // var_dump($client);die;
        array_push($where, 'AND tblquotation_request.client_id = ' . $client . '');
    }
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
            $_data = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotation_request/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['code'] . '</a></div>';

        } else if ($field == 'date' || $field == 'deadline') {
            $_data = '<div class="text-center">'._d($_data).'</div>';
        } else if ($field == 'action') {
            $edit = '<a href="'.admin_url('quotation_request/submit/'.$aRow['id']).'"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('quotation_request') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                    <button href=\'' . base_url('admin/quotation_request/delete/'.$aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                    <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('quotation_request') . '</a>';

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
