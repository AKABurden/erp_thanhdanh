<?php

defined('BASEPATH') or exit('No direct script access allowed');

$search_date = $this->ci->input->post('search_date');

$aColumns = [
    'tblspending_plan.id as id',
    'tblspending_plan.code as code',
    'tblspending_plan.date as date',
    'tblspending_plan.group_plan as group_plan',
    'tblspending_plan.detail as detail',
    'CONCAT(tb_create.firstname, " ", tb_create.lastname) as create_by',
    'tblspending_plan.receiver as receiver',
    'CONCAT(tb_approve_staff.firstname, " ", tb_approve_staff.lastname) as approve_staff',
    'CONCAT(tb_spending_staff.firstname, " ", tb_spending_staff.lastname) as spending_staff',
    'tblspending_plan.price as price',
    'tblspending_plan.tax_rate as tax_rate',
    'tblspending_plan.amount as amount',
    'tblpayment_modes.name as payment_method',
    'tblcurrencies.name as currency',
    'tblspending_plan.exchange_rate as exchange_rate',
    'tblspending_plan.category_spend as category_spend',
    'tblspending_plan.expense as expense',
    'tblspending_plan.deadline as deadline',
    '"" as action',
];
$sIndexColumn = 'id';
$sTable       = 'tblspending_plan';
$join = [];
$join[] = 'LEFT JOIN tblstaff tb_create ON tb_create.staffid = tblspending_plan.create_by';
$join[] = 'LEFT JOIN tblstaff tb_approve_staff ON tb_approve_staff.staffid = tblspending_plan.approve_staff_id';
$join[] = 'LEFT JOIN tblstaff tb_spending_staff ON tb_spending_staff.staffid = tblspending_plan.spending_staff_id';
$join[] = 'LEFT JOIN tblpayment_modes ON tblpayment_modes.id = tblspending_plan.payment_method_id';
$join[] = 'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblspending_plan.currency_id';

$where = [];
if (!empty($search_date)) {
    $searchDate = explode(' - ', $search_date);
    array_push($where, 'AND tblspending_plan.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
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
            $_data = '<div class="text-center">'.($_data).'</div>';
        } else if ($field == 'date' || $field == 'deadline') {
            $_data = '<div class="text-center">'._d($_data).'</div>';
        } else if ($field == 'group_plan') {
            $_data = '<div class="text-center">'.($arrGroupPlan[$_data] ?? '').'</div>';
        } else if ($field == 'price' || $field == 'amount' || $field == 'exchange_rate') {
            $_data = '<div class="text-right">'.formatMoney($_data).'</div>';
        } else if ($field == 'tax_rate') {
            $_data = '<div class="text-center">'.$_data.'%</div>';
        } else if ($field == 'action') {
            $edit = '<a href="'.admin_url('spending_plan/submit/'.$aRow['id']).'" class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('spending_plan') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                    <button href=\'' . base_url('admin/spending_plan/delete/'.$aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                    <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('spending_plan') . '</a>';

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
