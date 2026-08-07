<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('customers', '', 'delete');

$custom_fields = get_table_custom_fields('customers');
$this->ci->db->query("SET sql_mode = ''");
$branch = get_table_where('tblbranch');
$date_start_search = $this->ci->input->post('date_start_search') ? to_sql_date($this->ci->input->post('date_start_search')).' 00:00:00' : '';
$end_start_search = $this->ci->input->post('end_start_search') ? to_sql_date($this->ci->input->post('date_start_search')).' 23:59:59' : '';

$aColumns = [
    'tblclients.userid as userid',
//    'client_image',
	'tblclients.zcode',
    'tblclients.company as company',
    'tblclients.representative',
//    'firstname',
	'tblclients.phonenumber as phonenumber',
//    'email_client',
	'(
		SELECT GROUP_CONCAT(staffid) 
		FROM tblstaff 
		JOIN tblcustomer_admins ON tblcustomer_admins.staff_id = tblstaff.staffid
		WHERE tblcustomer_admins.customer_id = tblclients.userid
	) as staff_group',
//    'tblclients.active',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tblcustomer_groups JOIN tblcustomers_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id WHERE customer_id = tblclients.userid ORDER by name ASC) as customerGroups',
    'tblclients.datecreated as datecreated',
    'vat',
    'tblclients.address as addressClient',
    '"" as status_client'
//    'tblclients.note as note',
//    'tblclients.website as website',
//    'tblclients.date_create_company as date_create_company',
//    '(SELECT short_name FROM tblcountries WHERE country_id = tblclients.country) as country',
//    '(SELECT name FROM tblprovince WHERE provinceid = tblclients.city) as city',
//    '(SELECT name FROM tbldistrict WHERE districtid = tblclients.district) as district',

    // '(SELECT name FROM tblleads_sources WHERE id = tblclients.sources) as sources',
    
//    '(SELECT TC.company FROM tblclients TC WHERE TC.userid = tblclients.userid) as introduction',
//    'tblclients.debt_limit as debt_limit',
//    'tblclients.debt_limit_day as debt_limit_day',
    // 'tblclients.discount as discount',
];

$sIndexColumn = 'userid';
$sTable       = 'tblclients';
$where        = [];
// Add blank where all filter can be stored
$filter = [];

$join = [
    'LEFT JOIN tblstatus_client ON tblstatus_client.id=tblclients.status_clients',
    
    'LEFT JOIN tblcontacts ON tblcontacts.userid = tblclients.userid AND tblcontacts.is_primary=1',
];

if (!empty($date_start_search)) {
    array_push($where, ' AND tblclients.datecreated >= "'.$date_start_search.'"');
}

if (!empty($end_start_search)) {
    array_push($where, ' AND tblclients.datecreated <= "'.$end_start_search.'"');
}

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblclients.userid = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$join = hooks()->apply_filters('customers_table_sql_join', $join);

// Filter by custom groups
$groups   = $this->ci->clients_model->get_groups();
$groupIds = [];
foreach ($groups as $group) {
    if ($this->ci->input->post('customer_group_' . $group['id'])) {
        array_push($groupIds, $group['id']);
    }
}
if (count($groupIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT customer_id FROM tblcustomer_groups WHERE groupid IN (' . implode(', ', $groupIds) . '))');
}

$countries  = $this->ci->clients_model->get_clients_distinct_countries();
$countryIds = [];
foreach ($countries as $country) {
    if ($this->ci->input->post('country_' . $country['country_id'])) {
        array_push($countryIds, $country['country_id']);
    }
}
if (count($countryIds) > 0) {
    array_push($filter, 'AND country IN (' . implode(',', $countryIds) . ')');
}


$this->ci->load->model('invoices_model');
// Filter by invoices
$invoiceStatusIds = [];
foreach ($this->ci->invoices_model->get_statuses() as $status) {
    if ($this->ci->input->post('invoices_' . $status)) {
        array_push($invoiceStatusIds, $status);
    }
}
if (count($invoiceStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT clientid FROM tblinvoices WHERE status IN (' . implode(', ', $invoiceStatusIds) . '))');
}

// Filter by estimates
$estimateStatusIds = [];
$this->ci->load->model('estimates_model');
foreach ($this->ci->estimates_model->get_statuses() as $status) {
    if ($this->ci->input->post('estimates_' . $status)) {
        array_push($estimateStatusIds, $status);
    }
}
if (count($estimateStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT clientid FROM tblestimates WHERE status IN (' . implode(', ', $estimateStatusIds) . '))');
}

// Filter by projects
$projectStatusIds = [];
$this->ci->load->model('projects_model');
foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('projects_' . $status['id'])) {
        array_push($projectStatusIds, $status['id']);
    }
}
if (count($projectStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT clientid FROM tblprojects WHERE status IN (' . implode(', ', $projectStatusIds) . '))');
}

// Filter by proposals
$proposalStatusIds = [];
$this->ci->load->model('proposals_model');
foreach ($this->ci->proposals_model->get_statuses() as $status) {
    if ($this->ci->input->post('proposals_' . $status)) {
        array_push($proposalStatusIds, $status);
    }
}
if (count($proposalStatusIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT rel_id FROM tblproposals WHERE status IN (' . implode(', ', $proposalStatusIds) . ') AND rel_type="customer")');
}

// Filter by having contracts by type
$this->ci->load->model('contracts_model');
$contractTypesIds = [];
$contract_types   = $this->ci->contracts_model->get_contract_types();

foreach ($contract_types as $type) {
    if ($this->ci->input->post('contract_type_' . $type['id'])) {
        array_push($contractTypesIds, $type['id']);
    }
}
if (count($contractTypesIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT client FROM tblcontracts WHERE contract_type IN (' . implode(', ', $contractTypesIds) . '))');
}

// Filter by proposals
$customAdminIds = [];
foreach ($this->ci->clients_model->get_customers_admin_unique_ids() as $cadmin) {
    if ($this->ci->input->post('responsible_admin_' . $cadmin['staff_id'])) {
        array_push($customAdminIds, $cadmin['staff_id']);
    }
}

if (count($customAdminIds) > 0) {
    array_push($filter, 'AND tblclients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id IN (' . implode(', ', $customAdminIds) . '))');
}

if ($this->ci->input->post('requires_registration_confirmation')) {
    array_push($filter, 'AND tblclients.registration_confirmed=0');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if (!has_permission('customers', '', 'view')) {
    //phân quyền
    $arrIDStaff = employee_manage_staff();
    if($arrIDStaff != array()) {
        $coverStr = implode(",", $arrIDStaff);
        array_push($where, 'AND (tblclients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id IN (' . $coverStr . ')) OR tblclients.addedfrom IN ('.$coverStr.'))');
    }
    //end
}

if ($this->ci->input->post('exclude_inactive')) {
    array_push($where, 'AND (tblclients.active = 1 OR tblclients.active=0 AND registration_confirmed = 0)');
}

// if ($this->ci->input->post('filterStatus')) {
//     if(is_numeric($this->ci->input->post('filterStatus'))) {
//         array_push($where, 'AND tblclients.userid IN (SELECT customer_id FROM tblcustomer_groups WHERE groupid = '.$this->ci->input->post('filterStatus').')');
//     }
// }
if ($this->ci->input->post('filterStatus')) {
    if(is_numeric($this->ci->input->post('filterStatus'))) {
        array_push($where, 'AND tblclients.status_clients = '.$this->ci->input->post('filterStatus'));
    }
}

if ($this->ci->input->post('groups_search')) {
	if(is_numeric($this->ci->input->post('groups_search'))) {
    	array_push($where, 'AND (tblclients.userid IN (SELECT customer_id FROM tblcustomer_groups WHERE groupid = '.$this->ci->input->post('groups_search').'))');
	}
	elseif ($this->ci->input->post('groups_search') == 'not_all') {
		$where_add = 'WHERE id > 0 ';
        $arrIDStaff = employee_manage_staff();
		if (!is_admin()) {
			if ($arrIDStaff != array()) {
				$coverStr = implode(",", $arrIDStaff);
				$where_add .= ' AND (addedfrom IN (' . $coverStr . ') )';
			}
		}

		$Group_customer = "(
                SELECT 
                    tblcustomer_groups.customer_id as customer_id
                FROM tblcustomer_groups
                $where_add
                ORDER BY id DESC
            )";
//		$where[] = 'AND tblclients.userid NOT IN ' . $Group_customer;
		$where[] = 'AND NOT EXISTS (
		    SELECT tblcustomer_groups.customer_id
		    FROM tblcustomer_groups
		    WHERE tblcustomer_groups.customer_id = tblclients.userid
		)';
	}
}

if ($this->ci->input->post('customer_admins_search')) {
	$customer_admins_search = $this->ci->input->post('customer_admins_search');
	if($customer_admins_search == '-1') {
		$where[] =  'AND (
							SELECT count(customer_id)
							FROM tblcustomer_admins WHERE tblcustomer_admins.customer_id = tblclients.userid
						) = 0';
	}
	else {
		$where[] = 'AND (tblclients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id = '.$customer_admins_search.'))';
	}
}

if ($this->ci->input->post('my_customers')) {
    array_push($where, 'AND tblclients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id=' . get_staff_user_id() . ')');
}


if(!is_admin()) {
    $arrBranch = get_branch_staff();
    if(!empty($arrBranch)) {
        $coverStrBranch = implode(",", $arrBranch);
        array_push($where, 'AND (tblclients.userid IN (
				SELECT tbl_client_branch.client_id FROM tbl_client_branch WHERE tbl_client_branch.branch_id IN (' . $coverStrBranch . ')
			)  
        )');
    } else {
        array_push($where, 'AND tblclients.userid = 0');
    }
}


$aColumns = hooks()->apply_filters('customers_table_sql_columns', $aColumns);

$group_by = [];
$this->ci->db->order_by('id','desc');
$info_detail = $this->ci->db->get('tblclient_info_detail')->result_array();

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tblstatus_client.color',
    'tblstatus_client.name as status_name',
    'status_clients',
    'tblcontacts.id as contact_id',
    'lastname',
    'table_price_id',
    'id_discount_client',
    'zcode',
    'tblclients.zip as zip',
    'registration_confirmed',
    'tblclients.code_type as code_type_client',
    '(SELECT GROUP_CONCAT(color SEPARATOR ",") FROM tblcustomer_groups JOIN tblcustomers_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id WHERE customer_id = tblclients.userid ORDER by name ASC) as customerColorGroups',

],'group by tblclients.userid'.(!empty($group_by) ? (','.implode(',',$group_by)) : '') );

$output  = $result['output'];
$rResult = $result['rResult'];
//
// echo "<pre>";
// var_dump($rResult);die();
$select_status_clients =get_table_where('tblstatus_client');

$j =0;
foreach ($rResult as $key_Result => $aRow) {
    $j++;
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['userid'] . '"><label></label></div>';
    // User id
//    $row[] = $j;

//    $profileImagePath = 'uploads/clients/'.$aRow['userid'].'/small_'.$aRow['client_image'];
//    $url = base_url('download/preview_image?path='.$profileImagePath);
//    $row[] = '<img src="'.$url.'" class="staff-profile-image-small" alt="'.$aRow['company'].'">';
    // Company
    $company  = $aRow['company'];
    $isPerson = false;

    if ($company == '') {
        $company  = _l('no_company_view_profile');
        $isPerson = true;
    }

    $url = admin_url('clients/client/' . $aRow['userid']) . '?view';

    if ($isPerson && $aRow['contact_id']) {
        $url .= '?contactid=' . $aRow['contact_id'];
    }

    $company = '<a href="' . $url . '">' . $company . '</a>';

    $company .= '<div class="row-options">';
    $company .= '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '?view">' . _l('view') . '</a>';

    if ($aRow['registration_confirmed'] == 0 && is_admin()) {
        $company .= ' | <a href="' . admin_url('clients/confirm_registration/' . $aRow['userid']) . '" class="text-success bold">' . _l('confirm_registration') . '</a>';
    }
    if (!$isPerson) {
        $company .= ' | <a href="' . admin_url('clients/client/' . $aRow['userid'] . '?view&edit=1') . '">' . _l('edit') . '</a>';
    }
    if ($hasPermissionDelete) {
        $company .= ' | <a href="' . admin_url('clients/delete/' . $aRow['userid']) . '" class="text-danger _deleteRow">' . _l('delete') . '</a>';
    }

    $company .= '</div>';

    $row[]  = $aRow['zcode'].'<br>';

    $row[] = $company;
    $row[]  = $aRow['tblclients.representative'];


    // Primary contact
//    $row[] = ($aRow['contact_id'] ? '<a href="' . admin_url('clients/client/' . $aRow['userid'] . '?contactid=' . $aRow['contact_id']) . '" target="_blank">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>' : '');

    // Primary contact email
//    $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    // Primary contact phone
    $row[] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');

    // Toggle active/inactive customer


//    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
//    <input type="checkbox"' . ($aRow['registration_confirmed'] == 0 ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'clients/change_client_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['userid'] . '" data-id="' . $aRow['userid'] . '" ' . ($aRow['tblclients.active'] == 1 ? 'checked' : '') . '>
//    <label class="onoffswitch-label" for="' . $aRow['userid'] . '"></label>
//    </div>';
//
//
//    $toggleActive .= '<span class="hide">' . ($aRow['tblclients.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
//
//    $row[] = $toggleActive;


	$staff_group_ass = !empty($aRow['staff_group']) ? explode(',', $aRow['staff_group']) : [];
	$staff_ass = '';
	if (!empty($staff_group_ass)) {
		foreach ($staff_group_ass as $kg => $vg) {
			$full_name = get_staff_full_name($vg);
			$staff_ass .= '<a data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $vg) . '">' . staff_profile_image($vg, [
					'staff-profile-image-small mbot5',
				]) . ' ' . $full_name . '</a><br/>';
		}
	}
	$row[] = $staff_ass;


    // Customer groups parsing
    $groupsRow = '';
    if ($aRow['customerGroups']) {
        $groups = explode(',', $aRow['customerGroups']);
        $customerColorGroups = explode(',', $aRow['customerColorGroups']);
        foreach ($groups as $k => $group) {
            $groupsRow .= '<span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid '.$customerColorGroups[$k].'">' . $group . '</span>';
        }
    }

    $row[] = $groupsRow;

    $row[] = _dt($aRow['datecreated']);

    //all colum
    $row[] = $aRow['vat'];
    $row[] = $aRow['addressClient'];
//    $row[] = $aRow['note'];
//    $row[] = $aRow['website'];
//    $row[] = _d($aRow['date_create_company']);
//    $row[] = $aRow['country'];
//    $row[] = $aRow['city'];
//    $row[] = $aRow['district'];
//    $row[] = $aRow['sources'];
//    $row[] = $aRow['introduction'];
//    $row[] = number_format($aRow['debt_limit']);
//    $row[] = number_format($aRow['debt_limit_day']);
//    $row[] = number_format($aRow['discount']);
    //tình trạng khách hàng
   $outputStatus    = '';

   $outputStatus .= '<span class="inline-block label" style="color:' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '" task-status-table="' . $aRow['status_clients'] . '">';

   $outputStatus .= $aRow['status_name'];

   $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
   $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $aRow['userid'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
   $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
   $outputStatus .= '</a>';

   $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $aRow['userid'] . '">';
   foreach ($select_status_clients as $ChangeStatus) {
       if ($aRow['status_clients'] != $ChangeStatus['id']) {
           $outputStatus .= '<li>
             <a href="#" onclick="status_mark_as(' . $ChangeStatus['id'] . ',' . $aRow['userid'] . '); return false;">
                ' .$ChangeStatus['name'] . '
             </a>
          </li>';
       }
   }
   $outputStatus .= '</ul>';
   $outputStatus .= '</div>';

   $outputStatus .= '</span>';

   $row[] = $outputStatus;
    //end


    foreach($info_detail as $key => $value)
    {
        if($value['type_form'] == 'select multiple' || $value['type_form'] == 'checkbox') {
            $this->ci->db->select('GROUP_CONCAT(tblclient_info_detail_value.name) as name')
            ->where('id_detail', $value['id'])->where('client', $aRow['userid'])
            ->join('tblclient_info_detail_value', 'tblclient_info_detail_value.id = tblclient_value.value', 'left');
            $Data_Value = $this->ci->db->get('tblclient_value')->row();
            $row[] = !empty($Data_Value->name) ? $Data_Value->name : '';
        }
        else if($value['type_form'] == 'select' || $value['type_form'] == 'radio')
        {
            $this->ci->db->select('tblclient_info_detail_value.name as name')
            ->where('id_detail', $value['id'])->where('client', $aRow['userid'])
            ->join('tblclient_info_detail_value', 'tblclient_info_detail_value.id = tblclient_value.value', 'left');
            $Data_Value = $this->ci->db->get('tblclient_value')->row();
            $row[] = !empty($Data_Value->name) ? $Data_Value->name : '';
        }
        else
        {
            $this->ci->db->select('(tblclient_value.value) as name')
            ->where('id_detail', $value['id'])->where('client', $aRow['userid']);
            $Data_Value = $this->ci->db->get('tblclient_value')->row();
            if(!empty($Data_Value->name))
            {
                if($value['type_form'] == 'date')
                {
                    $row[] = _d($Data_Value->name);
                }
                else if($value['type_form'] == 'datetime')
                {
                    $row[] = _dt($Data_Value->name);
                }
                else
                {
                    $row[] = $Data_Value->name;
                }
            }
            else
            {
                $row[] = "";
            }
        }


    }
    //end

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    //bổ xung cột cập nhập bảng giá
    // $get_set_price = get_table_where('tbl_set_prices',array('status'=>1));
    // $select = '<select class="set_price" id="set_price_'.$key_Result.'" data-placeholder="'._l('choise_set_price').'" class="modal-select2" style="width: 130px;" data-idprice="'.$aRow['table_price_id'].'" data-customer="'.$aRow['userid'].'">
    //             <option value=""></option>';
    // foreach ($get_set_price as $key_set_price => $value_set_price) {
    //     $select .= '<option value="'.$value_set_price['id'].'">'.$value_set_price['name'].'</option>';
    // }
    // $select .= '</select>';     
    // $row[] = $select;
    // //end
    // //bổ xung cột cập nhập chiết khấu
    // $get_discount = get_table_where('tbldiscount',array('apply'=>1,'status'=>1));
    // $select = '<select class="set_discount" id="set_discount_'.$key_Result.'" data-placeholder="'._l('choise_discount').'" class="modal-select2" style="width: 130px;" data-iddiscount="'.$aRow['id_discount_client'].'" data-customer="'.$aRow['userid'].'">
    //             <option value=""></option>';
    // foreach ($get_discount as $key_discount => $value_discount) {
    //     $select .= '<option value="'.$value_discount['id'].'">'.$value_discount['name_discount'].'</option>';
    // }
    // $select .= '</select>';     
    // $row[] = $select;
    //end
    //branch
    $branch_client = get_table_where('tbl_client_branch', array('client_id' => $aRow['userid']));
    $selected = array();
    if(isset($branch_client)){
        foreach($branch_client as $value){
            array_push($selected,$value['branch_id']);
        }
    }
    $row[] =  '<div style="width: auto">'.render_select('branch[]',$branch,array('id','name'),'',$selected,array('multiple'=>true, 'data-actions-box'=>true,'data-client' => $aRow['userid'],),array(),'','',false).'</div>';
    //end
    $row['DT_RowClass'] = 'has-row-options';

    if ($aRow['registration_confirmed'] == 0) {
        $row['DT_RowClass'] .= ' alert-info requires-confirmation';
        $row['Data_Title']  = _l('customer_requires_registration_confirmation');
        $row['Data_Toggle'] = 'tooltip';
    }

    $row = hooks()->apply_filters('customers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
