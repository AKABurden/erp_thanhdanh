<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('suppliers', '', 'delete');
$hasPermissionEdit = has_permission('suppliers', '', 'edit');

$custom_fields = get_table_custom_fields('suppliers');
$this->ci->db->query("SET sql_mode = ''");
var_dump(123);die;
$aColumns = [
    '"" as code',
    'company',
    'representative',
    'email',
    db_prefix() . 'suppliers.vat as vat',
    db_prefix() . 'suppliers.phone as phone',
    db_prefix() . 'suppliers.active',
    db_prefix() . 'suppliers_groups.name as groups1',
    db_prefix() . 'suppliers.datecreated as datecreated',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'suppliers';
$where        = [];
// Add blank where all filter can be stored
$filter = [];
$join = [
    'LEFT JOIN ' . db_prefix() . 'suppliers_groups ON ' . db_prefix() . 'suppliers_groups.id=' . db_prefix() . 'suppliers.groups_in',
];

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'suppliers.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}
$join = hooks()->apply_filters('suppliers_table_sql_join', $join);

if (has_permission('suppliers', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND  tblsuppliers.addedfrom = ' . get_staff_user_id());
}
if ($this->ci->input->post('filterStatus')) {
    if (is_numeric($this->ci->input->post('filterStatus'))) {
        if ($this->ci->input->post('filterStatus') == 0) {
            array_push($where, 'AND tblsuppliers.type = ' . $this->ci->input->post('filterStatus'));
        } else if ($this->ci->input->post('filterStatus') == 1) {
            array_push($where, 'AND tblsuppliers.type = ' . $this->ci->input->post('filterStatus'));
        }
    }
}
if ($this->ci->input->post('id_supplier')) {
    array_push($where, 'AND tblsuppliers.id = ' . $this->ci->input->post('id_supplier'));
}
if ($this->ci->input->post('group')) {
    array_push($where, 'AND tblsuppliers.groups_in = ' . $this->ci->input->post('group'));
}

if(!is_admin()) {
    $arrBranch = get_branch_staff();
    if(!empty($arrBranch)) {
        $coverStrBranch = implode(",", $arrBranch);
        array_push($where, 'AND (tblsuppliers.id IN (
				SELECT tbl_suppliers_branch.suppliers_id FROM tbl_suppliers_branch WHERE tbl_suppliers_branch.branch_id IN (' . $coverStrBranch . ')
			)  
        )');
    } else {
        array_push($where, 'AND tblsuppliers.id = 0');
    }
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tblsuppliers.id'
]);
// var_dump($result);die;
$output  = $result['output'];
$rResult = $result['rResult'];
$j = 0;
foreach ($rResult as $aRow) {
    $j++;
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

    // Company
    $company  = $aRow['company'];
    $isPerson = false;

    if ($company == '') {
        $company  = _l('no_company_view_profile');
        $isPerson = true;
    }
    $row[] = '<a href="#" onclick="int_suppliers_view(' . $aRow['id'] . ',false); return false;" >'.$aRow['code']. '</a>';
    $company = '<a href="#" onclick="int_suppliers_view(' . $aRow['id'] . ',false); return false;" >' . $company . '</a>';
    $row[] = $company;
    //representative
    $row[] = $aRow['representative'];
    // Primary contact email
    $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');
    $row[] = '<div style="width: 100px">'.($aRow['vat'] ? $aRow['vat'] : '').'<div>';

    // Primary contact phone
    $row[] = '<div style="width: 100px">'.($aRow['phone'] ? '<a href="tel:' . $aRow['phone'] . '">' . $aRow['phone'] . '</a>' : '').'<div>';
    //vat

    // Toggle active/inactive customer
    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('') . '">
    <input type="checkbox"' . (!$hasPermissionEdit ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'suppliers/change_suppliers_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow[db_prefix() . 'suppliers.active'] == 1 ? 'checked' : '') . '>
    <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow[db_prefix() . 'suppliers.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row[] = $toggleActive;
    $row[] = ($aRow['groups1'] ? $aRow['groups1']  : '');

    $row[] = _dt($aRow['datecreated']);

    $this->ci->db->select('tblbranch.name as name_branch');
    $this->ci->db->from('tbl_suppliers_branch');
    $this->ci->db->where('tbl_suppliers_branch.suppliers_id',$aRow['id']);
    $this->ci->db->join('tblbranch','tblbranch.id = tbl_suppliers_branch.branch_id');
    $branchSupplier = $this->ci->db->get()->result_array();
    $branch_name = '';
    if(!empty($branchSupplier)){
        foreach ($branchSupplier as $k => $v){
            $branch_name .= $v['name_branch'].', ';
        }
    }
    $branch_name = trim($branch_name,', ');
    $row[] = $branch_name;


    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $_outputStatus = '<div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu h_right">';
    $_outputStatus .= '<li><a href="#" onclick="int_suppliers_view(' . $aRow['id'] . ',false); return false;" ><i class="fa fa-eye"></i> ' . _l('view') . '</a></li>';
    if ($hasPermissionEdit) {
        $_outputStatus .= '<li><a href="#" onclick="int_suppliers_view(' . $aRow['id'] . ',true); return false;" ><i class="fa fa-pencil"></i> ' . _l('edit') . '</a></li>';
    }
    if ($hasPermissionDelete) {
        $_outputStatus .= '<li><a href="' . admin_url('suppliers/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete') . '</a></li>';
    }
    $_outputStatus .= '</ul></div>';
    $row[] = $_outputStatus;

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('suppliers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
