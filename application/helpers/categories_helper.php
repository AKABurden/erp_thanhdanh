<?php
defined('BASEPATH') or exit('No direct script access allowed');

function categories($data='',$html,$parent,$level)
{
    foreach ($data as $key => $value) {
        $html.='<tr class="treegrid-'.$value['id'].' treegrid-parent-'.$parent.'">
                <td><h5 style="display: inline-block;">'.$value['category'].'</h5></td>
                <td>Cấp '.$level.'</td>
                <td>'. staff_profile_image($value['staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => get_staff_full_name($value['staff_create'])
                    )).'</td>
                <td>'._dt($value['date_create']).'</td>
                <td></td>
                <td>'.(has_permission('categories', '', 'edit')?icon_btn('#' , 'pencil', 'btn-default',array('onclick'=>"edit_category(".$value['id'].",'".$value['category']."',".$value['category_parent']."); return false;")):'').(has_permission('categories', '', 'delete')?'<a onclick="delete_category('.$value['id'].')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left"  title="">
                            <i class="fa fa-remove"></i>
                        </a>':'').'
                </td>  
            </tr>';
        $_data =get_table_where('tblcategories',array('category_parent'=>$value['id']));
        if($_data)
        {
        $html=categories($_data,$html,$value['id'],($level + 1));
        }else
        {
           continue;
        }
    }
     return $html;
}
function get_categories($data='')
{
    $html='';
    foreach ($data as $key => $value) {

        $html.='<tr class="treegrid-'.$value['id'].'">
                <td><h5 style="display: inline-block;">'.$value['category'].'</h5></td>
                <td>Cấp 1</td>
                <td>'. staff_profile_image($value['staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => get_staff_full_name($value['staff_create'])
                    )).'</td>
                <td>'._dt($value['date_create']).'</td>
                <td>
                    <div class="onoffswitch">
                        <input type="checkbox"' . (!has_permission('categories', '', 'edit') ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'categories/change_items_calculated" name="onoffswitch" class="onoffswitch-checkbox" id="c' . $value['id'] . '" data-id="' . $value['id'] . '" ' . ($value['calculated_on_sales'] == 1 ? 'checked' : '') . '>
                        <label class="onoffswitch-label" for="c' . $value['id'] . '"></label>
                    </div>
                </td>
                <td>'.(has_permission('categories', '', 'edit')?icon_btn('#' , 'pencil', 'btn-default',array('onclick'=>"edit_category(".$value['id'].",'".$value['category']."',".$value['category_parent']."); return false;")):'').(has_permission('categories', '', 'delete')?'<a onclick="delete_category('.$value['id'].')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left"  title="">
                            <i class="fa fa-remove"></i>
                        </a>':'').'
                </td>
            </tr>';
        $_data = get_table_where('tblcategories',array('category_parent'=>$value['id']));
        if($_data) {
            $html = categories($_data,$html,$value['id'],2);
        } else {
            continue;
        }
    }
    echo $html;
}
function returnget_categories($data='')
{
    $html='';
    foreach ($data as $key => $value) {

        $html.='<tr class="treegrid-'.$value['id'].'">
                <td><h5 style="display: inline-block;">'.$value['category'].'</h5></td>
                <td>Cấp 1</td>
                <td>'. staff_profile_image($value['staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => get_staff_full_name($value['staff_create'])
                    )).'</td>
                <td>'._dt($value['date_create']).'</td>
                <td></td>
            </tr>';
        $_data =get_table_where('tblcategories',array('category_parent'=>$value['id']));
        if($_data)
        {
        $html=categories($_data,$html,$value['id'],2);
        }else
        {
            continue;
        }
    }
    return $html;
}


function get_categories_roles_old($data = null)
{
	$CI       = & get_instance();
    $salary_minimum = get_option('salary_minimum');
    $html='';
    foreach ($data as $key => $value) {
        $monthly_salary = $salary_minimum * $value['coefficient'];
        $annual_salary = $monthly_salary * 12;
        $dtTypeContract = get_table_where('tbl_type_contract',['id' => $value['type_contract']],'','row_array');
        $dtStaff = get_table_where('tblstaff', ['role' => $value['roleid']],'','result_array');
        $_data = '';
        if (!empty($dtStaff)){
            foreach ($dtStaff as $kk => $vv){
                $_data.= '<a href="' . admin_url('staff/profile/' . $vv['staffid']) . '">' . staff_profile_image($vv['staffid'],
                        [
                            'staff-profile-image-small',
                        ]) . '</a>';
            }
        }

        $CI->db->select('GROUP_CONCAT(tbl_materials_equipment.machine SEPARATOR "</br>") as machine', false);
        $CI->db->from('tbl_materials_equipment');
        $CI->db->where('tbl_materials_equipment.role_id', $value['roleid']);
        $materials_equipment = $CI->db->get()->row_array();

        // '.$value['supplies'].'
        $html.='<tr class="treegrid-'.$value['roleid'].'">
                    <td></td>
                    <td class="code_board">' . $value['code_board'] . '</td>
                    <td class="code_block">' . $value['code_block'] . '</td>
                    <td class="code_room">' . $value['code_room'] . '</td>
                    <td class="code_nest">' . $value['code_nest'] . '</td>
                    <td class="code_group">' . $value['code_group'] . '</td>
                    <td>
                          <div class="code_role">'.$value['code_role'].'</div>
                    </td>
                    <td>
                        <h5 style="display: inline-block;">
                            <a href="' . admin_url('roles/role/' . $value['roleid']) . '" class="mbot10 display-block">' . $value['name']. '</a>
                            <span class="mtop10 display-block">' . _l('roles_total_users') . ' ' . total_rows(db_prefix().'staff', ['role' => $value['roleid']]) . '</span>
                        </h5>
                    </td>
                    <td class="text-left">'.$value['name_position'].'</td>
                    <td class="text-center">Cấp 1</td>
                    <td class="text-left">'.$value['name_departments'].'</td>
                    <td class="text-left" style="width: 120px">
                         '.$_data.'<br>
                         <a class="tnh-modal" href="'.base_url('admin/roles/updateStaff/'.$value['roleid'].'').'">Cập nhật nhân viên</a>
                    </td>
                    <td class="text-left">'.$value['email'].'</td>
                    <td class="text-center">'.($value['coefficient_step_salary']).'</td>
                    <td class="text-center">
                        '.($value['coefficient_coefficient_salary']).'
                    </td>
                    <td class="text-right">
                        '.formatNumber($value['salary_step_salary']).'
                    </td>
                    <td class="text-center">
                        '.formatNumber($value['coefficient_overtime']).'
                    </td>
                    <td class="text-center">
                        '.($value['day_off_permission']).'
                    </td>
                    <td class="text-left">
                        '.(!empty($materials_equipment['machine']) ? $materials_equipment['machine'] : '').'
                    </td>
                    <td class="text-left">
                        <a target="_blank" href="'.base_url('admin/kpi/view_detail_task/'.$value['id_detail_task']).'">'.$value['code_detail_task'].'</a>
                    </td>
                    <td class="text-left">
                        <a target="_blank" href="' . base_url('admin/kpi/view_category_kpi/' . $value['id_category_kpi']) . '">'.$value['name_category_kpi'].'</a>
                    </td>
                    <td class="text-left">
                        '.$value['code_contract_labor'].'
                    </td>
                     <td class="text-left">
                        '.(!empty($value['date_probation']) ? _dhau($value['date_probation']) : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['date_end']) ? _dhau($value['date_end']) : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['date_sign_contract']) ? _dhau($value['date_sign_contract']) : '').'
                    </td>  
                    <td class="text-left">
                        '.(!empty($value['name_type_contract']) ? $value['name_type_contract'] : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['date_start']) ? _dhau($value['date_start']) : '').'
                    </td>  
                    <td class="text-left">
                          '.(!empty($value['date_sign']) ? _dhau($value['date_sign']) : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['time']) ? formatNumber($value['time']) : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['time_physical_deadline']) ? formatNumber($value['time_physical_deadline']) : '').'
                    </td>
                    <td class="text-center">
                        '.icon_btn('roles/role/' . $value['roleid'], 'pencil-square-o').'
                        '.icon_btn('roles/delete/' . $value['roleid'], 'remove', 'btn-danger _delete').'
                    </td>
                   
                   
                </tr>';
//        $_data = get_table_where('tblroles',array('roles_parent'=>$value['roleid']));
		
		
		
		$CI->db->select('tblroles.*, tbldepartments.name as name_departments');
		$CI->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
		$CI->db->select([
			'tbl_board.code as code_board',
			'tbl_block.code as code_block',
			'tbl_room.code as code_room',
			'tbl_nest.code as code_nest',
			'tbl_group.code as code_group',
            'tbl_step_salary.code as code_step_salary',
			'tbl_step_salary.coefficient as coefficient_step_salary',
			'tbl_step_salary.salary as salary_step_salary',
			'tbl_coefficient_salary.code as code_coefficient_salary',
			'tbl_coefficient_salary.coefficient as coefficient_coefficient_salary',
			'tbl_permission.code as code_permission',
			'tbl_permission.day_off as day_off_permission',
			'tbl_category_kpi.code as code_category_kpi',
			'tbl_category_kpi.name as name_category_kpi',
			'tbl_category_kpi.id as id_category_kpi',
			'tbl_contract_labor.code as code_contract_labor',
            'tbl_contract_labor.date_probation as date_probation',
            'tbl_contract_labor.date_end as date_end',
            'tbl_contract_labor.date_sign_contract as date_sign_contract',
            'tbl_type_contract.name as name_type_contract',
            'tbl_contract_labor.date_start as date_start',
            'tbl_contract_labor.date_sign as date_sign',
            'tbl_detail_task.code as code_detail_task',
            'tbl_detail_task.id as id_detail_task',
            'tbl_salary_deadline.time as time',
            'tbl_physical_deadline.time as time_physical_deadline',
		]);
		$CI->db->join('tbl_board', 'tbl_board.id = tblroles.id_board', 'left');
		$CI->db->join('tbl_block', 'tbl_block.id = tblroles.id_block', 'left');
		$CI->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
		$CI->db->join('tbl_nest', 'tbl_nest.id = tblroles.id_nest', 'left');
		$CI->db->join('tbl_group', 'tbl_group.id = tblroles.id_group', 'left');

        $CI->db->join('tbl_step_salary', 'tbl_step_salary.id = tblroles.salary_id', 'left');
        $CI->db->join('tbl_coefficient_salary', 'tbl_coefficient_salary.id = tblroles.coefficient_salary_id', 'left');
        $CI->db->join('tbl_permission', 'tbl_permission.id = tblroles.paid_holiday_id', 'left');
        $CI->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tblroles.kpi_category_id', 'left');
        $CI->db->join('tbl_contract_labor', 'tbl_contract_labor.id = tblroles.contract_id', 'left');
        $CI->db->join('tbl_type_contract', 'tbl_type_contract.id = tbl_contract_labor.type_contract_id', 'left');
        $CI->db->join('tbl_detail_task', 'tbl_detail_task.role_id = tblroles.roleid', 'left');
        $CI->db->join('tbl_salary_deadline', 'tbl_salary_deadline.location = tblroles.roleid', 'left');
        $CI->db->join('tbl_physical_deadline', 'tbl_physical_deadline.location = tblroles.roleid', 'left');
		$_data = $CI->db->get_where('tblroles', ['roles_parent' => $value['roleid']])->result_array();
        if($_data) {
            $html = categories_roles($_data, $html, $value['roleid'], 2);
        } else {
            continue;
        }
    }
    echo $html;
}

function categories_roles_old($data = null, $html, $parent, $level)
{
	$CI       = & get_instance();
    $salary_minimum = get_option('salary_minimum');
    foreach ($data as $key => $value) {
        $monthly_salary = $salary_minimum * $value['coefficient'];
        $annual_salary = $monthly_salary * 12;
        $dtStaff = get_table_where('tblstaff',['role' => $value['roleid']],'','result_array');
        $_data = '';
        if (!empty($dtStaff)){
            foreach ($dtStaff as $kk => $vv){
                $_data.= '<a href="' . admin_url('staff/profile/' . $vv['staffid']) . '">' . staff_profile_image($vv['staffid'],
                        [
                            'staff-profile-image-small',
                        ]) . '</a>';
            }
        }

        $CI->db->select('GROUP_CONCAT(tbl_materials_equipment.machine SEPARATOR "</br>") as machine', false);
        $CI->db->from('tbl_materials_equipment');
        $CI->db->where('tbl_materials_equipment.role_id', $value['roleid']);
        $materials_equipment = $CI->db->get()->row_array();

        $html.='<tr class="treegrid-'.$value['roleid'].' treegrid-parent-'.$parent.'">
                    <td></td>
                    <td class="code_board">' . $value['code_board'] . '</td>
                    <td class="code_block">' . $value['code_block'] . '</td>
                    <td class="code_room">' . $value['code_room'] . '</td>
                    <td class="code_nest">' . $value['code_nest'] . '</td>
                    <td class="code_group">' . $value['code_group'] . '</td>
                    <td>
                          <div class="code_role">'.$value['code_role'].'</div>
                    </td>
                    <td>
                        <h5 style="display: inline-block;">
                            <a href="' . admin_url('roles/role/' . $value['roleid']) . '" class="mbot10 display-block">' . $value['name'] . '</a>
                            <span class="mtop10 display-block">' . _l('roles_total_users') . ' ' . total_rows(db_prefix().'staff', ['role' => $value['roleid']]) . '</span>
                        </h5>
                    </td>
                     <td class="text-left">'.$value['name_position'].'</td>
                    <td class="text-center">Cấp '.$level.'</td>
                    <td class="text-left">'.$value['name_departments'].'</td>
                    <td class="text-left" style="width: 120px">
                      '.$_data.'<br>
                         <a class="tnh-modal" href="'.base_url('admin/roles/updateStaff/'.$value['roleid'].'').'">Cập nhật nhân viên</a>
                    </td>
                    <td class="text-left">'.$value['email'].'</td>
                    <td class="text-right">'.($value['coefficient_step_salary']).'</td>
                    <td class="text-center">
                        '.($value['coefficient_coefficient_salary']).'
                    </td>
                    <td class="text-left">
                        '.formatNumber($value['salary_step_salary']).'
                    </td>
                    <td class="text-center">
                        '.formatNumber($value['coefficient_overtime']).'
                    </td>
                    <td class="text-center">
                        '.($value['day_off_permission']).'
                    </td>
                    <td class="text-left">
                        '.(!empty($materials_equipment['machine']) ? $materials_equipment['machine'] : '').'
                    </td>
                    <td class="text-left">
                        <a target="_blank" href="'.base_url('admin/kpi/view_detail_task/'.$value['id_detail_task']).'">'.$value['code_detail_task'].'</a>
                    </td>
                    <td class="text-left">
                        <a target="_blank" href="' . base_url('admin/kpi/view_category_kpi/' . $value['id_category_kpi']) . '">'.$value['name_category_kpi'].'</a>
                    </td>
                    <td class="text-left">
                        '.$value['code_contract_labor'].'
                    </td>
                     <td class="text-left">
                        '.(!empty($value['date_probation']) ? _dhau($value['date_probation']) : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['date_end']) ? _dhau($value['date_end']) : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['date_sign_contract']) ? _dhau($value['date_sign_contract']) : '').'
                    </td>  
                    <td class="text-left">
                        '.(!empty($value['name_type_contract']) ? $value['name_type_contract'] : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['date_start']) ? _dhau($value['date_start']) : '').'
                    </td>  
                     <td class="text-left">
                          '.(!empty($value['date_sign']) ? _dhau($value['date_sign']) : '').'
                    </td>
                    <td class="text-left">
                    '.(!empty($value['time']) ? formatNumber($value['time']) : '').'
                    </td>
                    <td class="text-left">
                        '.(!empty($value['time_physical_deadline']) ? formatNumber($value['time_physical_deadline']) : '').'
                    </td>
                    <td class="text-center">
                        '.icon_btn('roles/role/' . $value['roleid'], 'pencil-square-o').'
                        '.icon_btn('roles/delete/' . $value['roleid'], 'remove', 'btn-danger _delete').'
                    </td>  
                </tr>';

		$CI->db->select('tblroles.*, tbldepartments.name as name_departments');
		$CI->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
	
		$CI->db->select([
			'tbl_board.code as code_board',
			'tbl_block.code as code_block',
			'tbl_room.code as code_room',
			'tbl_nest.code as code_nest',
			'tbl_group.code as code_group'
		]);
		$CI->db->join('tbl_board', 'tbl_board.id = tblroles.id_board', 'left');
		$CI->db->join('tbl_block', 'tbl_block.id = tblroles.id_block', 'left');
		$CI->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
		$CI->db->join('tbl_nest', 'tbl_nest.id = tblroles.id_nest', 'left');
		$CI->db->join('tbl_group', 'tbl_group.id = tblroles.id_group', 'left');
		$_data = $CI->db->get_where('tblroles', ['roles_parent' => $value['roleid']])->result_array();
//        $_data = get_table_where('tblroles',array('roles_parent'=>$value['roleid']));
        if($_data) {
            $html = categories_roles($_data, $html, $value['roleid'], ($level + 1));
        } else {
            continue;
        }
    }
     return $html;
}

function get_categories_roles($data = null)
{
    $CI       = & get_instance();
    $html='';
    foreach ($data as $key => $value) {
        $dtStaff = get_table_where('tblstaff', ['role' => $value['roleid']],'','result_array');
        $_data = '';
        if (!empty($dtStaff)){
            foreach ($dtStaff as $kk => $vv){
                $_data.= '<a href="' . admin_url('staff/profile/' . $vv['staffid']) . '">' . staff_profile_image($vv['staffid'],
                        [
                            'staff-profile-image-small',
                        ]) . '</a>';
            }
        }

        $CI->db->select('GROUP_CONCAT(tbl_materials_equipment.machine SEPARATOR "</br>") as machine', false);
        $CI->db->from('tbl_materials_equipment');
        $CI->db->where('tbl_materials_equipment.role_id', $value['roleid']);
        $materials_equipment = $CI->db->get()->row_array();

        // '.$value['supplies'].'
        $html.='<tr class="treegrid-'.$value['roleid'].'">
                    <td></td>
                    <td>
                          <div class="code_role">'.$value['code_role'].'</div>
                    </td>
                    <td>
                        <h5 style="display: inline-block;">
                            <a href="' . admin_url('roles/role/' . $value['roleid']) . '" class="mbot10 display-block">' . $value['name']. '</a>
                            <span class="mtop10 hide">' . _l('roles_total_users') . ' ' . total_rows(db_prefix().'staff', ['role' => $value['roleid']]) . '</span>
                        </h5>
                    </td>
                    <td class="text-left">'.$value['name_position'].'</td>
                    <td class="text-left">'.$value['code_room'].'</td>
                    <td class="text-center"></td>
                    <td class="text-center">'.$value['email'].'</td>
                    <td class="text-left" style="width: 120px">
                         '.$_data.'<br>
                         <a class="tnh-modal" href="'.base_url('admin/roles/updateStaff/'.$value['roleid'].'').'">Cập nhật nhân viên</a>
                    </td>
                    <td class="text-right">'.(!empty($value['budget_role']) ? formatMoney($value['budget_role']) : '' ).'</td>
                    <td class="text-center">'.(!empty($value['headcount']) ? formatNumber($value['headcount']) : '' ).'</td>
                    <td class="text-center">'.(!empty($value['code_room']) ? ($value['status_room'] == 1 ? 'Active' : 'UnActive') : '').'</td>
                    <td class="text-center">'.(!empty($value['effective_from']) ? _dhau($value['effective_from']) : '').'</td>
                    <td class="text-center">'.(!empty($value['effective_to']) ? _dhau($value['effective_to']) : '').'</td>
                    <td class="text-center">'.($value['day_evaluate']).'</td>
                    <td class="text-center">'.($value['asset_link']).'</td>
                    <td class="text-center">
                       
                    </td>
                    <td class="text-right">
                        '.($value['workspace_link']).'
                    </td>
                     <td class="text-center">
                        '.$value['name_other1'].'
                    </td>
                    <td class="text-center">
                         '.$value['name_other2'].'
                    </td>
                    <td class="text-left">
                          '.$value['name_other3'].'
                    </td>
                    <td class="text-left">
                        '.$value['name_other4'].'
                    </td>
                    <td class="text-left">
                       '.$value['name_other7'].'
                    </td>
                    <td class="text-left">
                        '.$value['name_other5'].'
                    </td>
                     <td class="text-left">
                        '.$value['name_other6'].'
                    </td>
                    <td class="text-left">

                    </td>
                    <td class="text-left">
                        '.$value['coefficient_step_salary'].'
                    </td>
                    <td class="text-left">
                        '.$value['login_convention'].'
                    </td>  
                    <td class="text-left">
                          '.(!empty($value['salary_step_salary']) ? formatMoney($value['salary_step_salary']) : '').'
                    </td>
                    <td class="text-center">
                        '.icon_btn('roles/role/' . $value['roleid'], 'pencil-square-o').'
                        '.icon_btn('roles/delete/' . $value['roleid'], 'remove', 'btn-danger _delete').'
                    </td>
                </tr>';


        $tb_other1 = "(
            SELECT 
                GROUP_CONCAT(tbl_criteria_matrix_role.name) as name_other1,
                tbl_criteria_matrix_role.role_id
            FROM tbl_criteria_matrix_role
            WHERE tbl_criteria_matrix_role.type = 1
            GROUP BY tbl_criteria_matrix_role.role_id
        ) tb_other1";

        $tb_other2 = "(
            SELECT 
                GROUP_CONCAT(tbl_criteria_matrix_role.name) as name_other2,
                tbl_criteria_matrix_role.role_id
            FROM tbl_criteria_matrix_role
            WHERE tbl_criteria_matrix_role.type = 2
            GROUP BY tbl_criteria_matrix_role.role_id
        ) tb_other2";

        $tb_other3 = "(
            SELECT 
                GROUP_CONCAT(tbl_criteria_matrix_role.name) as name_other3,
                tbl_criteria_matrix_role.role_id
            FROM tbl_criteria_matrix_role
            WHERE tbl_criteria_matrix_role.type = 3
            GROUP BY tbl_criteria_matrix_role.role_id
        ) tb_other3";

        $tb_other4 = "(
            SELECT 
                GROUP_CONCAT(tbl_criteria_matrix_role.name) as name_other4,
                tbl_criteria_matrix_role.role_id
            FROM tbl_criteria_matrix_role
            WHERE tbl_criteria_matrix_role.type = 4
            GROUP BY tbl_criteria_matrix_role.role_id
        ) tb_other4";

        $tb_other5 = "(
            SELECT 
                GROUP_CONCAT(tbl_criteria_matrix_role.name) as name_other5,
                tbl_criteria_matrix_role.role_id
            FROM tbl_criteria_matrix_role
            WHERE tbl_criteria_matrix_role.type = 5
            GROUP BY tbl_criteria_matrix_role.role_id
        ) tb_other5";

        $tb_other6 = "(
            SELECT 
                GROUP_CONCAT(tbl_criteria_matrix_role.name) as name_other6,
                tbl_criteria_matrix_role.role_id
            FROM tbl_criteria_matrix_role
            WHERE tbl_criteria_matrix_role.type = 6
            GROUP BY tbl_criteria_matrix_role.role_id
        ) tb_other6";

        $tb_other7 = "(
            SELECT 
                GROUP_CONCAT(tbl_criteria_matrix_role.name) as name_other7,
                tbl_criteria_matrix_role.role_id
            FROM tbl_criteria_matrix_role
            WHERE tbl_criteria_matrix_role.type = 7
            GROUP BY tbl_criteria_matrix_role.role_id
        ) tb_other7";


        $CI->db->select('tblroles.*, tbldepartments.name as name_departments');
        $CI->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
        $CI->db->select([
            'tbl_board.code as code_board',
            'tbl_block.code as code_block',
            'tbl_room.code as code_room',
            'tbl_room.status as status_room',
            'tbl_room.effective_from as effective_from',
            'tbl_room.effective_to as effective_to',
            'tbl_nest.code as code_nest',
            'tbl_group.code as code_group',
            'tbl_step_salary.code as code_step_salary',
            'tbl_step_salary.coefficient as coefficient_step_salary',
            'tbl_step_salary.salary as salary_step_salary',
            'tbl_coefficient_salary.code as code_coefficient_salary',
            'tbl_coefficient_salary.coefficient as coefficient_coefficient_salary',
            'tbl_permission.code as code_permission',
            'tbl_permission.day_off as day_off_permission',
            'tbl_category_kpi.code as code_category_kpi',
            'tbl_category_kpi.name as name_category_kpi',
            'tbl_category_kpi.id as id_category_kpi',
            'tbl_contract_labor.code as code_contract_labor',
            'tbl_contract_labor.date_probation as date_probation',
            'tbl_contract_labor.date_end as date_end',
            'tbl_contract_labor.date_sign_contract as date_sign_contract',
            'tbl_type_contract.name as name_type_contract',
            'tbl_contract_labor.date_start as date_start',
            'tbl_contract_labor.date_sign as date_sign',
            'tbl_detail_task.code as code_detail_task',
            'tbl_detail_task.id as id_detail_task',
            'tbl_salary_deadline.time as time',
            'tbl_physical_deadline.time as time_physical_deadline',
            'tb_parent.code_role as code_role_parent',
            'tb_other1.name_other1 as name_other1',
            'tb_other2.name_other2 as name_other2',
            'tb_other3.name_other3 as name_other3',
            'tb_other4.name_other4 as name_other4',
            'tb_other5.name_other5 as name_other5',
            'tb_other6.name_other6 as name_other6',
            'tb_other7.name_other7 as name_other7',
        ]);
        $CI->db->join('tbl_board', 'tbl_board.id = tblroles.id_board', 'left');
        $CI->db->join('tbl_block', 'tbl_block.id = tblroles.id_block', 'left');
        $CI->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $CI->db->join('tbl_nest', 'tbl_nest.id = tblroles.id_nest', 'left');
        $CI->db->join('tbl_group', 'tbl_group.id = tblroles.id_group', 'left');

        $CI->db->join('tblroles tb_parent', 'tb_parent.roleid = tblroles.roles_parent', 'left');

        $CI->db->join('tbl_step_salary', 'tbl_step_salary.id = tblroles.salary_id', 'left');
        $CI->db->join('tbl_coefficient_salary', 'tbl_coefficient_salary.id = tblroles.coefficient_salary_id', 'left');
        $CI->db->join('tbl_permission', 'tbl_permission.id = tblroles.paid_holiday_id', 'left');
        $CI->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tblroles.kpi_category_id', 'left');
        $CI->db->join('tbl_contract_labor', 'tbl_contract_labor.id = tblroles.contract_id', 'left');
        $CI->db->join('tbl_type_contract', 'tbl_type_contract.id = tbl_contract_labor.type_contract_id', 'left');
        $CI->db->join('tbl_detail_task', 'tbl_detail_task.role_id = tblroles.roleid', 'left');
        $CI->db->join('tbl_salary_deadline', 'tbl_salary_deadline.location = tblroles.roleid', 'left');
        $CI->db->join('tbl_physical_deadline', 'tbl_physical_deadline.location = tblroles.roleid', 'left');

        $CI->db->join($tb_other1, 'tb_other1.role_id = tblroles.roleid', 'left');
        $CI->db->join($tb_other2, 'tb_other2.role_id = tblroles.roleid', 'left');
        $CI->db->join($tb_other3, 'tb_other3.role_id = tblroles.roleid', 'left');
        $CI->db->join($tb_other4, 'tb_other4.role_id = tblroles.roleid', 'left');
        $CI->db->join($tb_other5, 'tb_other5.role_id = tblroles.roleid', 'left');
        $CI->db->join($tb_other6, 'tb_other6.role_id = tblroles.roleid', 'left');
        $CI->db->join($tb_other7, 'tb_other7.role_id = tblroles.roleid', 'left');
        $_data = $CI->db->get_where('tblroles', ['tblroles.roles_parent' => $value['roleid']])->result_array();
        if($_data) {
            $html = categories_roles($_data, $html, $value['roleid'], 2);
        } else {
            continue;
        }
    }
    echo $html;
}

function categories_roles($data = null, $html, $parent, $level)
{
    $CI       = & get_instance();
    foreach ($data as $key => $value) {
        $dtStaff = get_table_where('tblstaff',['role' => $value['roleid']],'','result_array');
        $_data = '';
        if (!empty($dtStaff)){
            foreach ($dtStaff as $kk => $vv){
                $_data.= '<a href="' . admin_url('staff/profile/' . $vv['staffid']) . '">' . staff_profile_image($vv['staffid'],
                        [
                            'staff-profile-image-small',
                        ]) . '</a>';
            }
        }

        $CI->db->select('GROUP_CONCAT(tbl_materials_equipment.machine SEPARATOR "</br>") as machine', false);
        $CI->db->from('tbl_materials_equipment');
        $CI->db->where('tbl_materials_equipment.role_id', $value['roleid']);
        $materials_equipment = $CI->db->get()->row_array();

        $html.='<tr class="treegrid-'.$value['roleid'].' treegrid-parent-'.$parent.'">
                    <td></td>
                    <td>
                          <div class="code_role">'.$value['code_role'].'</div>
                    </td>
                    <td>
                        <h5 style="display: inline-block;">
                            <a href="' . admin_url('roles/role/' . $value['roleid']) . '" class="mbot10 display-block">' . $value['name'] . '</a>
                            <span class="mtop10 hide">' . _l('roles_total_users') . ' ' . total_rows(db_prefix().'staff', ['role' => $value['roleid']]) . '</span>
                        </h5>
                    </td>
                    <td class="text-left">'.$value['name_position'].'</td>
                    <td class="text-left">'.$value['code_room'].'</td>
                    <td class="text-center">'.$value['code_role_parent'].'</td>
                    <td class="text-left">'.$value['email'].'</td>
                    <td class="text-left" style="width: 120px">
                      '.$_data.'<br>
                         <a class="tnh-modal" href="'.base_url('admin/roles/updateStaff/'.$value['roleid'].'').'">Cập nhật nhân viên</a>
                    </td>
                    <td class="text-right">'.(!empty($value['budget_role']) ? formatMoney($value['budget_role']) : '' ).'</td>
                    <td class="text-center">'.(!empty($value['headcount']) ? formatNumber($value['headcount']) : '' ).'</td>
                    <td class="text-center">'.(!empty($value['code_room']) ? ($value['status_room'] == 1 ? 'Active' : 'UnActive') : '').'</td>
                    <td class="text-center">'.(!empty($value['effective_from']) ? _dhau($value['effective_from']) : '').'</td>
                    <td class="text-center">'.(!empty($value['effective_to']) ? _dhau($value['effective_to']) : '').'</td>
                    <td class="text-center">'.($value['day_evaluate']).'</td>
                    <td class="text-center">'.($value['asset_link']).'</td>
                      <td class="text-center">
                       
                    </td>
                    <td class="text-right">
                        '.($value['workspace_link']).'
                    </td>
                    <td class="text-center">
                        '.$value['name_other1'].'
                    </td>
                    <td class="text-center">
                         '.$value['name_other2'].'
                    </td>
                    <td class="text-left">
                          '.$value['name_other3'].'
                    </td>
                    <td class="text-left">
                        '.$value['name_other4'].'
                    </td>
                    <td class="text-left">
                       '.$value['name_other7'].'
                    </td>
                    <td class="text-left">
                        '.$value['name_other5'].'
                    </td>
                     <td class="text-left">
                        '.$value['name_other6'].'
                    </td>
                    <td class="text-left">

                    </td>
                    <td class="text-left">
                        '.$value['coefficient_step_salary'].'
                    </td>
                    <td class="text-left">
                        '.$value['login_convention'].'
                    </td>  
                    <td class="text-left">
                          '.(!empty($value['salary_step_salary']) ? formatMoney($value['salary_step_salary']) : '').'
                    </td>
                    <td class="text-center">
                        '.icon_btn('roles/role/' . $value['roleid'], 'pencil-square-o').'
                        '.icon_btn('roles/delete/' . $value['roleid'], 'remove', 'btn-danger _delete').'
                    </td>  
                </tr>';

        $CI->db->select('tblroles.*, tbldepartments.name as name_departments');
        $CI->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');

        $CI->db->select([
            'tbl_board.code as code_board',
            'tbl_block.code as code_block',
            'tbl_room.code as code_room',
            'tbl_room.status as status_room',
            'tbl_room.effective_from as effective_from',
            'tbl_room.effective_to as effective_to',
            'tbl_nest.code as code_nest',
            'tbl_group.code as code_group'
        ]);
        $CI->db->join('tbl_board', 'tbl_board.id = tblroles.id_board', 'left');
        $CI->db->join('tbl_block', 'tbl_block.id = tblroles.id_block', 'left');
        $CI->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $CI->db->join('tbl_nest', 'tbl_nest.id = tblroles.id_nest', 'left');
        $CI->db->join('tbl_group', 'tbl_group.id = tblroles.id_group', 'left');
        $_data = $CI->db->get_where('tblroles', ['roles_parent' => $value['roleid']])->result_array();
        if($_data) {
            $html = categories_roles($_data, $html, $value['roleid'], ($level + 1));
        } else {
            continue;
        }
    }
    return $html;
}