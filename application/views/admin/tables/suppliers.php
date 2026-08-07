<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('suppliers', '', 'delete');
$hasPermissionEdit = has_permission('suppliers', '', 'edit');

$custom_fields = get_table_custom_fields('suppliers');
$this->ci->db->query("SET sql_mode = ''");

$start_date_search = $this->ci->input->post('start_date_search');
$end_date_search = $this->ci->input->post('end_date_search');

$aColumns = [
    'tblsuppliers.id as id',
    'tblsuppliers_groups.code as code_group',
    'tblsuppliers_groups.name as groups',
    'tblsuppliers.code as code',
    'company',
    'abbreviation',
    'tblsupplier_classify.name as name_classify',
    'vat',
    'code_nxk',
    'tax',
    'bank_account',
    'name_account',
    'address_bank',
    'tm_ck',
    'tblcurrencies.name as name_curenci',
    'IF(tblsuppliers.default_currency > 0,CONCAT("1 ",tblcurrencies.name," - ",tblcurrencies.amount_to_vnd," VND"),"") as amount_to_vnd',
    'time_payment',
    'contract_number',
    'number_contract',
    'renewal_date',
    'address',
    'representative',
    'package_specifications',
    'CONCAT(tblcosts.code, "/",tblcosts.name) as cost_name',
    'date_begin',
    // 'phone',
    // 'address_delivery',
    'tblsuppliers.active',
    'tblsuppliers.datecreated as datecreated',
    '3'
];
$sIndexColumn = 'id';
$sTable       = 'tblsuppliers';
$where        = [];
// Add blank where all filter can be stored
$filter = [];
$join = [
    'LEFT JOIN tblsuppliers_groups ON tblsuppliers_groups.id=tblsuppliers.groups_in',
    'LEFT JOIN tblcurrencies ON tblcurrencies.id=tblsuppliers.default_currency',
    'LEFT JOIN tblsupplier_classify ON tblsupplier_classify.id=tblsuppliers.type_suppliers',
    'LEFT JOIN tblcosts ON tblcosts.id = tblsuppliers.cost_id',
];
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

if (!empty($start_date_search)){
    array_push($where,'AND tblsuppliers.datecreated >= "'.to_sql_date($start_date_search).' 00:00:00"');
}
if (!empty($end_date_search)){
    array_push($where,'AND tblsuppliers.datecreated <= "'.to_sql_date($end_date_search).' 23:59:59"');
}

if (!is_admin()) {
    $arrBranch = get_branch_staff();
    if (!empty($arrBranch)) {
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
    // $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

    // // Company
    // $company  = $aRow['company'];
    // $isPerson = false;

    // if ($company == '') {
    //     $company  = _l('no_company_view_profile');
    //     $isPerson = true;
    // }
    // $row[] = '<a href="#" onclick="int_suppliers_view(' . $aRow['id'] . ',false); return false;" >'.$aRow['code']. '</a>';
    // $company = '<a href="#" onclick="int_suppliers_view(' . $aRow['id'] . ',false); return false;" >' . $company . '</a>';
    // $row[] = $company;
    // //representative
    // $row[] = $aRow['representative'];
    // // Primary contact email
    // $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');
    // $row[] = '<div style="width: 100px">'.($aRow['vat'] ? $aRow['vat'] : '').'<div>';

    // // Primary contact phone
    // $row[] = '<div style="width: 100px">'.($aRow['phone'] ? '<a href="tel:' . $aRow['phone'] . '">' . $aRow['phone'] . '</a>' : '').'<div>';
    // //vat

    // // Toggle active/inactive customer
    // $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('') . '">
    // <input type="checkbox"' . (!$hasPermissionEdit ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'suppliers/change_suppliers_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow[db_prefix() . 'suppliers.active'] == 1 ? 'checked' : '') . '>
    // <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    // </div>';

    // // For exporting
    // $toggleActive .= '<span class="hide">' . ($aRow[db_prefix() . 'suppliers.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    // $row[] = $toggleActive;
    // $row[] = ($aRow['groups'] ? $aRow['groups']  : '');

    // $row[] = _dt($aRow['datecreated']);

    // $this->ci->db->select('tblbranch.name as name_branch');
    // $this->ci->db->from('tbl_suppliers_branch');
    // $this->ci->db->where('tbl_suppliers_branch.suppliers_id',$aRow['id']);
    // $this->ci->db->join('tblbranch','tblbranch.id = tbl_suppliers_branch.branch_id');
    // $branchSupplier = $this->ci->db->get()->result_array();
    // $branch_name = '';
    // if(!empty($branchSupplier)){
    //     foreach ($branchSupplier as $k => $v){
    //         $branch_name .= $v['name_branch'].', ';
    //     }
    // }
    // $branch_name = trim($branch_name,', ');
    // $row[] = $branch_name;
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblsuppliers_groups.code as code_group') {
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
            $_data = $aRow['code_group'] . '<br>' . $_outputStatus;
        }
        if ($aColumns[$i] == 'tblsuppliers.id as id') {

            $_data = $j;
        }
        if ($aColumns[$i] == 'tblsupplier_classify.name as name_classify') {

            $_data = '<div>'.$aRow['name_classify'].'</div>';
        }
        if ($aColumns[$i] == 'tm_ck') {
            if ($aRow['tm_ck'] == 1) {
                $_data = lang('tnh_tm');
            }
            if ($aRow['tm_ck'] == 2) {
                $_data = lang('tnh_ck');
            }
        }
        if ($aColumns[$i] == 'tblsuppliers.datecreated as datecreated') {
            $_data = _dt($aRow['datecreated']);
        }
        if ($aColumns[$i] == 'renewal_date') {
            $_data = _d($aRow['renewal_date']);
        }
        if ($aColumns[$i] == 'tblsuppliers.active') {
            $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('') . '">
            <input type="checkbox"' . (!$hasPermissionEdit ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'suppliers/change_suppliers_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['tblsuppliers.active'] == 1 ? 'checked' : '') . '>
            <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
            </div>';

            // For exporting
            $toggleActive .= '<span class="hide">' . ($aRow['tblsuppliers.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

            $_data = $toggleActive;
        }
        if ($aColumns[$i] == '3') {
            $this->ci->db->select('tblbranch.name as name_branch');
            $this->ci->db->from('tbl_suppliers_branch');
            $this->ci->db->where('tbl_suppliers_branch.suppliers_id', $aRow['id']);
            $this->ci->db->join('tblbranch', 'tblbranch.id = tbl_suppliers_branch.branch_id');
            $branchSupplier = $this->ci->db->get()->result_array();
            $branch_name = '';
            if (!empty($branchSupplier)) {
                foreach ($branchSupplier as $k => $v) {
                    $branch_name .= $v['name_branch'] . ', ';
                }
            }
            $branch_name = trim($branch_name, ', ');
            $_data = $branch_name;
        }

        if ($aColumns[$i] == 'date_begin') {
            $_data = !empty($aRow['date_begin']) ? _d($aRow['date_begin']) : '';
        }
        $row[] = $_data;
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('suppliers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
