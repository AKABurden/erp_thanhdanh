<?php

defined('BASEPATH') or exit('No direct script access allowed');

$search_date = $this->ci->input->post('search_date');

$aColumns = [
    'tblproduction_order_request.id as id',
    'tblproduction_order_request.date as date',
    'tblproduction_order_request.code as code',
    'tblproduction_order_request.note as note',
    '1',
    '"" as action',
];
$sIndexColumn = 'id';
$sTable       = 'tblproduction_order_request';
$join = [
    'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.name_table = "tblproduction_order_request"'
];

$where = [];
if (!empty($search_date)) {
    $searchDate = explode(' - ', $search_date);
    array_push($where, 'AND tblproduction_order_request.date BETWEEN "' . to_sql_date($searchDate[0]) . ' 00:00:00" and "' . to_sql_date($searchDate[1]) . ' 23:59:59"');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    '(SELECT count(tbltasks.id) FROM tbltasks  LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id  WHERE suggest_id = tblproduction_order_request.id AND tbl_category_recommended.name_table="tblproduction_order_request") as countTask',
    'tbl_category_recommended.id as category_recommended_id'
]);
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
            $_data = '<div class="text-center">' . ($rowIndex + 1) . '</div>';
        } elseif ($field == '1') {
            if (!has_permission('tasks', '', 'create')) {
                $_data = '';
            } else {
                $task = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . $aRow['category_recommended_id']) . '\')">Tạo công việc</a>';
                if (!empty($aRow['countTask'])) {
                    $data_tasks = get_table_where('tbltasks', ['suggest_id' => $aRow['id'], 'category_recommended_id' => $aRow['category_recommended_id']], '', 'result_array', '', 'tbltasks.id,tbltasks.name');
                    $__data = '';
                    $_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . $aRow['countTask'] . ' Phiếu
                        </button>';
                    foreach ($data_tasks as $kk => $vv) {
                        $__data .= '<li><a href="' . admin_url('tasks/view') . $vv['id'] . '" class="display-block main-tasks-table-href-name mbot5" onclick="init_task_modal(' . $vv['id'] . '); return false;">' . $vv['name'] . '</a>';
                    }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
                    $task .= $_data;
                    // $column[15] .= '<br/><span class="dropdown-toggle no_background label label-info mtop10">' . $aRow['countTask'] . ' phiếu công việc . </span>';
                    // '(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblinternal_proposal.id AND rel_type="internal_proposal") as countTask',

                }
                $_data = $task;
            }
        } else if ($field == 'code') {
            // $_data = '<div class="text-center">'.($_data).'</div>';
            $_data = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/production_order_request/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['code'] . '</a></div>';
        } else if ($field == 'date' || $field == 'deadline') {
            $_data = '<div class="text-center">' . _d($_data) . '</div>';
        } else if ($field == 'action') {
            $edit = '<a href="' . admin_url('production_order_request/submit/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('production_order_request') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                    <button href=\'' . base_url('admin/production_order_request/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                    <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('production_order_request') . '</a>';

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
