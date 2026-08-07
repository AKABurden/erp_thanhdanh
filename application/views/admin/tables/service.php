<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('service', '', 'delete');
$hasPermissionEdit = has_permission('service', '', 'edit');

$custom_fields = get_table_custom_fields('imports');
$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    'tbl_services.id as id',
    'tbl_services.date',
    'tbl_services.code',
    'tblsuppliers.company',
    'tbl_services.vat',
    'tbl_services.total_discount',
    'tbl_services.subtotal',
    'tblcosts.name',
    'tbl_services.status',
    'tbl_services.payment',
    'tbl_services.status_pay',
    'tbl_services.staff_id',
];
$sIndexColumn = 'id';
$sTable       = 'tbl_services';
$where        = [];

$filter = [];
$join = [
    'LEFT JOIN tblsuppliers ON tblsuppliers.id=tbl_services.suppliers_id',
    'LEFT JOIN tblstaff on tblstaff.staffid = tbl_services.staff_id',
    'LEFT JOIN tblcosts on tblcosts.id = tbl_services.type_service',
];
// if ($this->ci->input->post('filterStatus')) {
//     if(is_numeric($this->ci->input->post('filterStatus'))) {
//         if($this->ci->input->post('filterStatus') == 1) {
//             array_push($where, 'AND tbl_services.red_invoice <> 0');
//         } else if($this->ci->input->post('filterStatus') == 2) {
//             array_push($where, 'AND tbl_services.red_invoice = 0');
//         } else if($this->ci->input->post('filterStatus') == 3) {
//             array_push($where, 'AND tbl_services.status = 2');
//         } else if($this->ci->input->post('filterStatus') == 4) {
//             array_push($where, 'AND tbl_services.status = 1');
//         } else if($this->ci->input->post('filterStatus') == 5) {
//             array_push($where, 'AND tbl_services.warehouseman_id <> 0');
//         } else if($this->ci->input->post('filterStatus') == 6) {
//             array_push($where, 'AND tbl_services.warehouseman_id = 0');
//         } else if($this->ci->input->post('filterStatus') == 7) {
//             array_push($where, 'AND (tbl_services.status_pay = 2 or tblpurchase_invoice.status = 2)');
//             array_push($join,'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tbl_services.red_invoice');
//         } else if($this->ci->input->post('filterStatus') == 8) {
//             array_push($where, 'AND (tbl_services.status_pay = 1 or tblpurchase_invoice.status = 1)');
//             array_push($join,'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tbl_services.red_invoice');
//         } else if($this->ci->input->post('filterStatus') == 9) {
//             array_push($where, 'AND (tbl_services.status_pay = 0 or tblpurchase_invoice.status = 0)');
//             array_push($join,'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tbl_services.red_invoice');
//         }
//     }
// }
if ($this->ci->input->post('date')) {
    $date=explode(' - ',$this->ci->input->post('date'));
    $date_start=to_sql_date($date[0]);
    $date_end=to_sql_date($date[1]);
    array_push($where, 'AND tbl_services.date >= ' .'"'.$date_start.'"');
    array_push($where, 'AND tbl_services.date <=' .'"'.$date_end.'"');
}
if ($this->ci->input->post('suppliers')) {
    array_push($where, 'AND tbl_services.suppliers_id =' .$this->ci->input->post('suppliers'));
}
if ($this->ci->input->post('search_code')) {
    if (is_numeric($this->ci->input->post('search_code'))) {
        array_push($where, 'AND tbl_services.id = ' . $this->ci->input->post('search_code'));
    }
}
if ($this->ci->input->post('search_staff')) {
    array_push($where, 'AND  tbl_services.staff_id IN (' . implode(', ', $this->ci->input->post('search_staff')) . ')');
}
if ($this->ci->input->post('search_id_suppliers')) {
    array_push($where, 'AND tbl_services.suppliers_id IN (' . implode(', ', $this->ci->input->post('search_id_suppliers')) . ')');
}
$search_date = $this->ci->input->post('search_date');
if ($search_date) {
    $data_start = explode(' - ', $search_date);
    array_push($where, 'AND tbl_services.date_create BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
}

$suppliers_id = $this->ci->input->post('suppliers_id');
if (is_numeric($suppliers_id)) {
    array_push($where, 'AND tbl_services.suppliers_id = ' . $this->ci->input->post('suppliers_id'));
}
if (has_permission('service', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND tbl_services.staff_id = ' . get_staff_user_id());
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tbl_services.prefix',
    'tbl_services.history_status',
    'tbl_services.suppliers_id',
]);
$output  = $result['output'];
$rResult = $result['rResult'];
$j = 0;
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
foreach ($rResult as $key => $aRow) {
    //kiểm tra số lượng còn đã xuất / để xác nhận là đã xuất hay chưa
    $j++;
    $row = [];
    $row[] = $j;
    $row[] = _d($aRow['tbl_services.date']);
    $import  = $aRow['prefix'] . $aRow['tbl_services.code'];
    $isPerson = false;
    $import = '<a href="#" onclick="view_detail_service(' . $aRow['id'] . '); return false;" >' . $import . '</a>';
//    $import .= '<div class="row-options">';
//
//    $import .= '<a href="#" onclick="view_detail_service('.$aRow['id'].'); return false;" >' . _l('view') . '</a>';
//
//    if ($aRow['tbl_services.status'] < 1 && $hasPermissionEdit) {
//        $import .= ' | <a href="' . admin_url('service/detail/' . $aRow['id']) . '" >' . _l('edit') . '</a>';
//    }
//    if ($hasPermissionDelete&&($aRow['tbl_services.status_pay'] == 0)) {
//        $import .= ' | <a href="' . admin_url('service/delete/' . $aRow['id']) . '" class="text-danger delete-remind">' . _l('delete') . '</a>';
//    }
//    $import .= '</div>';

    $row[] = $import;

    $row[] = '<a href="#" onclick="int_suppliers_view(' . $aRow['suppliers_id'] . '); return false;">' . $aRow['tblsuppliers.company'] . '</a>';
    $row[] = '<div class="text-right">' . number_format($aRow['tbl_services.vat']) . '</div>';

    $row[] = '<div class="text-right">' . number_format($aRow['tbl_services.total_discount']) . '</div>';

    $row[] = '<div class="text-right">' . number_format($aRow['tbl_services.subtotal']) . '</div>';


    $row[]=$aRow['tblcosts.name'];

    if ($aRow['tbl_services.status'] == 0) {
        $type = 'warning';
        $status = _l('dont_approve');
    } elseif ($aRow['tbl_services.status'] == 1) {
        $type = 'info';
        $status = _l('ch_confirm_22');
    }
    $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tbl_services.status'] . '">' . $status . '';
    if (has_permission('service', '', 'approve')) {
        if ($aRow['tbl_services.status'] == 0) {
            $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tbl_services.status'] . ',' . $aRow['id'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
        } else {
            $status .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
        }
    }
    $status .= '</a>
                </span><br>';
    $_data = '';
    $history_status = explode('|', $aRow['history_status']);

    foreach ($history_status as $key => $value) {
        $data = explode(',', $value);
        if (is_numeric($data[0])) {
            $_data .= staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                    'data-toggle' => 'tooltip',
                    'data-title' => ' Vào lúc: ' . _dt($data[1])
                )) . get_staff_full_name($data[0]) . '<br>';
        }
    }

    $row[] = $status . $_data;
    $row[] = '<div class="text-right">' . number_format($aRow['tbl_services.payment']) . '</div>';
    $row[] = '<div class="text-center">'.format_status_pay_slip($aRow['tbl_services.status_pay']).'<div>';
    $staff = staff_profile_image($aRow['tbl_services.staff_id'], array('staff-profile-image-small mright5'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($aRow['tbl_services.staff_id'])
        )) . get_staff_full_name($aRow['tbl_services.staff_id']);
    $row[] = ($aRow['tbl_services.staff_id'] ? $staff : '');

    // Custom fields add values
    $_outputStatus = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">';
    $_outputStatus .= '<li><a href="#" onclick="view_detail_service('.$aRow['id'].'); return false;" ><i class="fa fa-eye "></i> ' . _l('view') . '</a></li>';
    if ($aRow['tbl_services.status'] < 1 && $hasPermissionEdit) {
        $_outputStatus .= '<li><a href="' . admin_url('service/detail/' . $aRow['id']) . '" ><i class="fa fa-edit "></i> ' . _l('edit') . '</a></li>';
    }
    $_outputStatus .= '<li><a href="' . admin_url('service/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> ' . _l('print_vote') . '</a></li>';
    if ($hasPermissionDelete&&($aRow['tbl_services.status_pay'] == 0)) {
        $_outputStatus .= '<li><a href="' . admin_url('service/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-trash-o width-icon-actions"></i> ' . _l('delete') . '</a></li>';
    }
    $_outputStatus .= '</ul></div>';
    $row[] = $_outputStatus;

    // $row['DT_RowClass'] = 'has-row-options';


    $row = hooks()->apply_filters('import_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
