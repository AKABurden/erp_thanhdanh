<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('inventory', '', 'delete');
$hasPermissionEdit = has_permission('inventory', '', 'edit');
$hasPermissionApprove = has_permission('inventory', '', 'approve');
$hasPermissionadjusted = has_permission('adjusted', '', 'create');
$aColumns     = array(
    'tblinventory.date',
    'tblinventory.code',
    'tblinventory.staff_id',
    'tblinventory.status',
    'waid.name as waidname',
    '2',
    'tblinventory.note',
);
$sIndexColumn = "id";
$sTable       = 'tblinventory';
$where        = array();
if ($this->ci->input->post('filterStatus')) {
    if (is_numeric($this->ci->input->post('filterStatus'))) {
        if ($this->ci->input->post('filterStatus') == 1) {
            array_push($where, 'AND tblinventory.status = 1');
        } else if ($this->ci->input->post('filterStatus') == 2) {
            array_push($where, 'AND tblinventory.status = 2');
        }
    }
}
$items_search = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');
if (!empty($items_search)) {
    array_push($where, 'AND EXISTS (
        SELECT tblinventory_items.inventory_id
        FROM tblinventory_items
        WHERE tblinventory_items.inventory_id = tblinventory.id
        AND tblinventory_items.product_id = ' . $items_search . ' AND tblinventory_items.type = "'.$type_items.'"
    )');
}
$search_date = $this->ci->input->post('search_date');
    if ($search_date) {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblinventory.date BETWEEN "' . to_sql_date($data_start[0]) . ' 00:00:00" and "' . to_sql_date($data_start[1]) . ' 23:59:59"');
    }
$join         = array(
    'LEFT JOIN tblwarehouse waid on waid.id = tblinventory.warehouse_id',
);
if (has_permission('inventory', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND  tblinventory.staff_id = ' . get_staff_user_id());
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblinventory.id',
    'tblinventory.prefix',
    'date_create',
    'history_status',
    'tblinventory.not_new_by_staff',
));
$output       = $result['output'];
$rResult      = $result['rResult'];

$j = 0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $ktr_exit = get_table_where('tbladjusted', array('id_inventory' => $aRow['id']), '', 'row');
        $not_new_by_staff = explode(',', $aRow['not_new_by_staff']);
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblinventory.date') {
            if (!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblinventory.status'] == 0) {
                $_data = '<div class="text-center">' . _d($aRow['tblinventory.date']) . '</div> <br><span class="wap-new">new</span>';
                $row['DT_RowClass'] = 'alert-new';
            } else {
                $_data = '<div class="text-center">' . _d($aRow['tblinventory.date']) . '</div>';
            }
        }
        if ($aColumns[$i] == 'tblinventory.code') {
            $_data = '<div class="text-center">' . $aRow['prefix'] . $aRow['tblinventory.code'] . '</div>';
            $transfer  = $aRow['prefix'] . $aRow['tblinventory.code'];
            $transfer = '<a href="#" onclick="view_inventory(' . $aRow['id'] . '); return false;" >' . $transfer . '</a>';

            $_data = $transfer;
        }
        if ($aColumns[$i] == '2') {
            $ktr_adj = get_table_where('tbladjusted', array('id_inventory' => $aRow['id']));
            if (!empty($ktr_adj)) {
                $data = '';
                foreach ($ktr_adj as $k => $v) {
                    $data .= '<li class="hoang"><a onclick="view_adjusted(' . $v['id'] . '); return false;" >' . $v['prefix'] . $v['code'] . '</a></li>';
                }
                $_outputStatus = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . count($ktr_adj) . ' Điều chỉnh
                        </button>
                        <ul style="top:80%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">';
                $_outputStatus .= $data;
                $_outputStatus .= '</ul></div>';
                $_data = $_outputStatus;
            } else {
                $_data = '';
            }
        }

        if ($aColumns[$i] == 'tblinventory.status') {
            if ($aRow['tblinventory.status'] == 0) {
                $type = 'warning';
                $status = _l('dont_approve');
            } elseif ($aRow['tblinventory.status'] == 1) {
                $type = 'info';
                $status = _l('ch_confirm_22');
            }
            $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblinventory.status'] . '">' . $status . '';
            if ($hasPermissionApprove) {
                if ($aRow['tblinventory.status'] == 0) {
                    $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tblinventory.status'] . ',' . $aRow['id'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
                } else {
                    $status .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
                    // $status .= '<a href="javacript:void(0)"  data-loading-text=""  onclick="var_status(' . $aRow['tblinventory.status'] . ',' . $aRow['id'] . '); return false">
                    // <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
                }
            }
            $status .= '</a>
                        </span><br>';
            $__data = '';
            $history_status = explode('|', $aRow['history_status']);

            foreach ($history_status as $key => $value) {
                $data = explode(',', $value);
                if (is_numeric($data[0])) {
                    $__data .= staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: ' . _dt($data[1])
                    )) . get_staff_full_name($data[0]) . '<br>';
                }
            }

            $_data = $status . $__data;
        }
        if ($aColumns[$i] == 'tblinventory.staff_id') {
            $_data = staff_profile_image($aRow['tblinventory.staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => ' Vào lúc: ' . _dt($aRow['date_create'])
            )) . get_staff_full_name($aRow['tblinventory.staff_id']) . '<br>';
        }
        if ($aColumns[$i] == 'tblinventory.warehouseman_id') {
            $_data = '';
            if (has_permission('import', '', 'view') || has_permission('import', '', 'view_own')) {
                if ($aRow['tblinventory.status'] == 2 && !empty($aRow['tblinventory.staff_id'])) {

                    $button = _l('ch_warehouse_nd');
                    $title = _l('warehouseman_confirm');
                    $type = 'fa-square-o';
                    if ($aRow['tblinventory.warehouseman_id']) {
                        $button = _l('ch_warehouse_d');
                        $title = _l('warehouseman_confirm_cancel');
                        $type = 'fa-check-square-o';
                    }
                    if (empty($aRow['tblinventory.warehouseman_id'])) {
                        $_data = '<span class="inline-block label label-warning" task-status-table="">Số lượng không đủ</span>';
                        if (test_quantity_tranfer($aRow['id'])) {
                            $_data = '<a href="" onclick="confirm_warehous(' . $aRow['id'] . ',' . $aRow['tblinventory.warehouseman_id'] . ');return false;" class=" btn btn-info btn-icon "  data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" data-original-title="' . $title . '"><i class="fa  ' . $type . '"></i> ' . $button . '</a>' . ($aRow['tblinventory.warehouseman_id'] ? '<br>' . _l('warehouseman') . ': <span style="color: red;">' . get_staff_full_name($aRow['tblinventory.warehouseman_id']) . '</span>' : '');
                        }
                    } else {
                        $_data = '<span class="inline-block label label-success" task-status-table="">Đã duyệt kho</span>';
                    }
                }
            }
        }

        $row[] = $_data;
    }

    $_outputStatus = '<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu h_right">';
    $_outputStatus .= '<li><a href="#" onclick="view_inventory(' . $aRow['id'] . '); return false;" ><i class="fa fa-eye"></i> ' . _l('view_order') . '</a></li>';
    if (empty($ktr_exit) && ($aRow['tblinventory.status'] == 0) && $hasPermissionEdit) {
        $_outputStatus .= '<li><a href="' . admin_url('inventory/detail/' . $aRow['id']) . '" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
    }
    if ($aRow['tblinventory.status'] == 1 && empty($ktr_exit) && $hasPermissionadjusted) {
        $_outputStatus .= '<li><a onclick="create_adjusted(' . $aRow['id'] . ')"><i class="lnr lnr-sync width-icon-actions"></i> ' . _l('ch_create') . '</a></li>';
    }
    $_outputStatus .= '<li><a href="' . admin_url('inventory/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> ' . _l('print_vote') . '</a></li>';
    if ($hasPermissionDelete) {
        $_outputStatus .= '<li><a href="' . admin_url('inventory/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
    }
    $_outputStatus .= '</ul></div>';
    $row[] = $_outputStatus;
    $output['aaData'][] = $row;
}
