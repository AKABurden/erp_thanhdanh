<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roles extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->arrRoleChild = [
            'can_view',
            'can_view_own',
            'can_create',
            'can_edit',
            'can_print',
            'can_approve',
            'can_approve_warehouse',
            'can_approve_accept',
            'can_approve_qc',
            'can_approve_cancel',
            'can_import',
            'can_export',
            'can_delete',
            'can_cost',
            'can_profit',
            'can_notifications',
            'can_agree_notifications',
            'can_add_notifications',
            'can_qc',
            'can_view_price',
            'can_export_outsource',
            'can_approve_manager',
        ];
    }
    /* List all staff roles */
    public function index()
    {
        if (!has_permission('roles', '', 'view')) {
            access_denied('roles');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('roles');
        }

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

        $this->db->select('tblroles.*, tbldepartments.name as name_departments');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
        $this->db->select([
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
            'tb_other1.name_other1 as name_other1',
            'tb_other2.name_other2 as name_other2',
            'tb_other3.name_other3 as name_other3',
            'tb_other4.name_other4 as name_other4',
            'tb_other5.name_other5 as name_other5',
            'tb_other6.name_other6 as name_other6',
            'tb_other7.name_other7 as name_other7',
        ]);
        $this->db->join('tbl_board', 'tbl_board.id = tblroles.id_board', 'left');
        $this->db->join('tbl_block', 'tbl_block.id = tblroles.id_block', 'left');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $this->db->join('tbl_nest', 'tbl_nest.id = tblroles.id_nest', 'left');
        $this->db->join('tbl_group', 'tbl_group.id = tblroles.id_group', 'left');

        $this->db->join('tbl_step_salary', 'tbl_step_salary.id = tblroles.salary_id', 'left');
        $this->db->join('tbl_coefficient_salary', 'tbl_coefficient_salary.id = tblroles.coefficient_salary_id', 'left');
        $this->db->join('tbl_permission', 'tbl_permission.id = tblroles.paid_holiday_id', 'left');
        $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tblroles.kpi_category_id', 'left');
        $this->db->join('tbl_contract_labor', 'tbl_contract_labor.id = tblroles.contract_id', 'left');
        $this->db->join('tbl_type_contract', 'tbl_type_contract.id = tbl_contract_labor.type_contract_id', 'left');
        $this->db->join('tbl_detail_task', 'tbl_detail_task.role_id = tblroles.roleid', 'left');
        $this->db->join('tbl_salary_deadline', 'tbl_salary_deadline.location = tblroles.roleid', 'left');
        $this->db->join('tbl_physical_deadline', 'tbl_physical_deadline.location = tblroles.roleid', 'left');
        $this->db->join($tb_other1, 'tb_other1.role_id = tblroles.roleid', 'left');
        $this->db->join($tb_other2, 'tb_other2.role_id = tblroles.roleid', 'left');
        $this->db->join($tb_other3, 'tb_other3.role_id = tblroles.roleid', 'left');
        $this->db->join($tb_other4, 'tb_other4.role_id = tblroles.roleid', 'left');
        $this->db->join($tb_other5, 'tb_other5.role_id = tblroles.roleid', 'left');
        $this->db->join($tb_other6, 'tb_other6.role_id = tblroles.roleid', 'left');
        $this->db->join($tb_other7, 'tb_other7.role_id = tblroles.roleid', 'left');
        $data['full_categories'] = $this->db->get_where('tblroles', [
            'roles_parent' => 0,
            'active_role' => 1,
            'tblroles.type' => 0
        ])->result_array();
        // echo '<pre>';print_arrays($data);die;
        $data['title'] = _l('all_roles');
        $this->load->view('admin/roles/manage', $data);
    }

    /* Add new role or edit existing one */
    public function role($id = '')
    {
        if (!has_permission('roles', '', 'view')) {
            access_denied('roles');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('roles', '', 'create')) {
                    access_denied('roles');
                }
                if (!empty($this->input->post('code_role'))) {
                    $this->db->where('type', 0);
                    $this->db->where('code_role', $this->input->post('code_role'));
                    $ktCodeRoles = $this->db->get('tblroles')->row();
                    if (!empty($ktCodeRoles)) {
                        set_alert('danger', 'Mã vị trí đã tồn tại vui lòng nhập mã khác');
                        redirect(admin_url('roles/role'));
                    }
                }

                $id = $this->roles_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('role')));
                    redirect(admin_url('roles/role/' . $id));
                }
            } else {
                if (!has_permission('roles', '', 'edit')) {
                    access_denied('roles');
                }
                // if (!empty($this->input->post('day_evaluate'))) {
                //     $this->db->from('tbl_question_bank');
                //     $this->db->where('tbl_question_bank.role_id',$id);
                //     $dtCheckExists = $this->db->get()->row_array();
                //     if (empty($dtCheckExists)){
                //         set_alert('danger', 'Vui lòng thiết lập ngân hàng cho vị trí này trước khi cập vòng đời đánh giá (số ngày)');
                //         redirect(admin_url('roles/role/' . $id));
                //     }
                // }
                if (!empty($this->input->post('code_role'))) {
                    $this->db->where('roleid != "' . $id . '"', false, false);
                    $this->db->where('type', 0);
                    $this->db->where('code_role', $this->input->post('code_role'));
                    $ktCodeRoles = $this->db->get('tblroles')->row();
                    if (!empty($ktCodeRoles)) {
                        set_alert('danger', 'Mã vị trí đã tồn tại vui lòng nhập mã khác');
                        redirect(admin_url('roles/role/' . $id));
                    }
                }
                $success = $this->roles_model->update($this->input->post(), $id);
                if ($success) {

                    $dataPost = $this->input->post();
                    $counterOther1 = $this->input->post('counterOther1') ?? [];
                    $arrOther1 = [];
                    if (!empty($counterOther1)) {
                        foreach ($counterOther1 as $key => $value) {
                            $type = $dataPost['type_other1'][$key] ?? 1;
                            $name = $dataPost['name_other1'][$key] ?? null;
                            $coefficient_from = $dataPost['coefficient_from_other1'][$key] ?? null;
                            $coefficient_to = $dataPost['coefficient_to_other1'][$key] ?? null;
                            $arrOther1[] = [
                                'name' => $name,
                                'coefficient_from' => $coefficient_from,
                                'coefficient_to' => $coefficient_to,
                                'type' => $type,
                                'role_id' => $id,
                            ];
                        }
                    }

                    $counterOther2 = $this->input->post('counterOther2') ?? [];
                    $arrOther2 = [];
                    if (!empty($counterOther2)) {
                        foreach ($counterOther2 as $key => $value) {
                            $type = $dataPost['type_other2'][$key] ?? 2;
                            $name = $dataPost['name_other2'][$key] ?? null;
                            $coefficient_from = $dataPost['coefficient_from_other2'][$key] ?? null;
                            $coefficient_to = $dataPost['coefficient_to_other2'][$key] ?? null;
                            $arrOther2[] = [
                                'name' => $name,
                                'coefficient_from' => $coefficient_from,
                                'coefficient_to' => $coefficient_to,
                                'type' => $type,
                                'role_id' => $id,
                            ];
                        }
                    }

                    $counterOther3 = $this->input->post('counterOther3') ?? [];
                    $arrOther3 = [];
                    if (!empty($counterOther3)) {
                        foreach ($counterOther3 as $key => $value) {
                            $type = $dataPost['type_other3'][$key] ?? 3;
                            $name = $dataPost['name_other3'][$key] ?? null;
                            $coefficient_from = $dataPost['coefficient_from_other3'][$key] ?? null;
                            $coefficient_to = $dataPost['coefficient_to_other3'][$key] ?? null;
                            $arrOther3[] = [
                                'name' => $name,
                                'coefficient_from' => $coefficient_from,
                                'coefficient_to' => $coefficient_to,
                                'type' => $type,
                                'role_id' => $id,
                            ];
                        }
                    }

                    $counterOther4 = $this->input->post('counterOther4') ?? [];
                    $arrOther4 = [];
                    if (!empty($counterOther4)) {
                        foreach ($counterOther4 as $key => $value) {
                            $type = $dataPost['type_other4'][$key] ?? 4;
                            $name = $dataPost['name_other4'][$key] ?? null;
                            $coefficient_from = $dataPost['coefficient_from_other4'][$key] ?? null;
                            $coefficient_to = $dataPost['coefficient_to_other4'][$key] ?? null;
                            $arrOther4[] = [
                                'name' => $name,
                                'coefficient_from' => $coefficient_from,
                                'coefficient_to' => $coefficient_to,
                                'type' => $type,
                                'role_id' => $id,
                            ];
                        }
                    }

                    $counterOther5 = $this->input->post('counterOther5') ?? [];
                    $arrOther5 = [];
                    if (!empty($counterOther5)) {
                        foreach ($counterOther5 as $key => $value) {
                            $type = $dataPost['type_other5'][$key] ?? 5;
                            $name = $dataPost['name_other5'][$key] ?? null;
                            $coefficient_from = $dataPost['coefficient_from_other5'][$key] ?? null;
                            $coefficient_to = $dataPost['coefficient_to_other5'][$key] ?? null;
                            $arrOther5[] = [
                                'name' => $name,
                                'coefficient_from' => $coefficient_from,
                                'coefficient_to' => $coefficient_to,
                                'type' => $type,
                                'role_id' => $id,
                            ];
                        }
                    }

                    $counterOther6 = $this->input->post('counterOther6') ?? [];
                    $arrOther6 = [];
                    if (!empty($counterOther6)) {
                        foreach ($counterOther6 as $key => $value) {
                            $type = $dataPost['type_other6'][$key] ?? 6;
                            $name = $dataPost['name_other6'][$key] ?? null;
                            $coefficient_from = $dataPost['coefficient_from_other6'][$key] ?? null;
                            $coefficient_to = $dataPost['coefficient_to_other6'][$key] ?? null;
                            $arrOther6[] = [
                                'name' => $name,
                                'coefficient_from' => $coefficient_from,
                                'coefficient_to' => $coefficient_to,
                                'type' => $type,
                                'role_id' => $id,
                            ];
                        }
                    }

                    $counterOther7 = $this->input->post('counterOther7') ?? [];
                    $arrOther7 = [];
                    if (!empty($counterOther7)) {
                        foreach ($counterOther7 as $key => $value) {
                            $type = $dataPost['type_other7'][$key] ?? 7;
                            $name = $dataPost['name_other7'][$key] ?? null;
                            $coefficient_from = $dataPost['coefficient_from_other7'][$key] ?? null;
                            $coefficient_to = $dataPost['coefficient_to_other7'][$key] ?? null;
                            $arrOther7[] = [
                                'name' => $name,
                                'coefficient_from' => $coefficient_from,
                                'coefficient_to' => $coefficient_to,
                                'type' => $type,
                                'role_id' => $id,
                            ];
                        }
                    }


                    $counterOther8 = $this->input->post('counterOther8') ?? [];
                    $arrOther8 = [];
                    if (!empty($counterOther8)) {
                        foreach ($counterOther8 as $key => $value) {
                            $role_level_id = $dataPost['role_level_id'][$key] ?? null;
                            $arrOther8[] = [
                                'role_level_id' => $role_level_id,
                                'role_id' => $id,
                            ];
                        }
                    }

                    $this->db->where('type', 1);
                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_criteria_matrix_role');
                    if (!empty($arrOther1)) {
                        $this->db->insert_batch('tbl_criteria_matrix_role', $arrOther1);
                    }

                    $this->db->where('type', 2);
                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_criteria_matrix_role');
                    if (!empty($arrOther2)) {

                        $this->db->insert_batch('tbl_criteria_matrix_role', $arrOther2);
                    }

                    $this->db->where('type', 3);
                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_criteria_matrix_role');
                    if (!empty($arrOther3)) {
                        $this->db->insert_batch('tbl_criteria_matrix_role', $arrOther3);
                    }

                    $this->db->where('type', 4);
                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_criteria_matrix_role');

                    if (!empty($arrOther4)) {
                        $this->db->insert_batch('tbl_criteria_matrix_role', $arrOther4);
                    }

                    $this->db->where('type', 5);
                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_criteria_matrix_role');

                    if (!empty($arrOther5)) {
                        $this->db->insert_batch('tbl_criteria_matrix_role', $arrOther5);
                    }

                    $this->db->where('type', 6);
                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_criteria_matrix_role');
                    if (!empty($arrOther6)) {
                        $this->db->insert_batch('tbl_criteria_matrix_role', $arrOther6);
                    }

                    $this->db->where('type', 7);
                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_criteria_matrix_role');
                    if (!empty($arrOther7)) {
                        $this->db->insert_batch('tbl_criteria_matrix_role', $arrOther7);
                    }

                    $this->db->where('role_id', $id);
                    $this->db->delete('tbl_role_role_level');
                    if (!empty($arrOther8)) {
                        $this->db->insert_batch('tbl_role_role_level', $arrOther8);
                    }


                    set_alert('success', _l('updated_successfully', _l('role')));
                }
                redirect(admin_url('roles/role/' . $id));
            }
        }
        if ($id == '') {
            $data['roles_parent'] = [];
            //			$this->get_parent(0, $data['roles_parent'], 0);
            $this->db->where('active_role', 1);
            $data['roles_parent'] = $this->db->get_where('tblroles', ['roles_parent' => 0])->result_array();

            $title = _l('add_new') . ' ' .  _l('role_lowercase');

            set_alert('danger', 'Không tìm thấy vị trí');
            redirect(admin_url('roles'));
            return;
        } else {
            $data['roles_parent'] = [];
            //			$this->get_parent_remove_exists($id, 0, $data['roles_parent']);
            $this->db->where('active_role', 1);
            $data['roles_parent'] = $this->db->get_where('tblroles', ['roles_parent' => 0])->result_array();

            $data['role_staff'] = $this->roles_model->get_role_staff($id);
            $role = get_table_where('tblroles', array('roleid' => $id), '', 'row');
            //kiểm tra parent
            $check_permission_parent = get_table_where(
                'tbl_roles_parent_permission_v2',
                array('id_role' => $id, 'can_view' => 1)
            );
            $arr_id_parent_permission = array();
            foreach ($check_permission_parent as $key => $value) {
                $arr_id_parent_permission[] = get_table_where(
                    'tbl_parents_permissions_v2',
                    array('obj' => $value['obj_parent_permission']),
                    '',
                    'row'
                )->obj;
            }
            $data['role'] = $role;
            $data['arr_parent'] = $arr_id_parent_permission;
            $title = _l('edit') . ' ' . _l('vị trí') . ' ' . $role->name . ' (' . $role->code_role . ')';


            $data['list_board'] = $this->db->get('tbl_board')->result_array(); //hội  -ban
            $data['list_block'] = $this->db->get('tbl_block')->result_array(); // khối
            $data['list_room'] = $this->db->get('tbl_room')->result_array(); // phòng
            $data['list_nest'] = $this->db->get('tbl_nest')->result_array(); // tổ
            $data['list_group'] = $this->db->get('tbl_group')->result_array(); // nhóm
            $data['list_step_salary'] = $this->db->get('tbl_step_salary')->result_array(); // bật lương
            $data['list_coefficient_salary'] = $this->db->get('tbl_coefficient_salary')->result_array(); // hệ số lương
            $data['list_category_kpi'] = $this->db->get('tbl_category_kpi')->result_array(); // danh sách KPI

            $data['list_permission'] = $this->db->get('tbl_permission')->result_array(); // danh sách KPI
            $data['list_contract_labor'] = $this->db->get('tbl_contract_labor')->result_array(); // Hợp đồng thử việc
        }

        $data['dtRoleLevel'] = get_table_where('tbl_role_level');
        $data['departments'] = get_table_where('tbldepartments');
        $data['level'] = $this->roles_model->getLevel();
        $data['title'] = $title;
        $this->load->view('admin/roles/role', $data);
    }

    public function get_parent($id_parent = 0, &$array_category = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('roles_parent' => $id_parent));
            $this->db->where('active_role', 1);
            $current_level = $this->db->get('tblroles')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_category, $current_level[$key]);
                    $this->get_parent($value['roleid'], $array_category, $level + 1);
                }
            } else {
                return;
            }
        }
    }

    public function get_parent_remove_exists($id_exists = 0, $id_parent = 0, &$array_category = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('roles_parent' => $id_parent));
            $this->db->where('roleid <>', $id_exists);
            $current_level = $this->db->get('tblroles')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_category, $current_level[$key]);
                    $this->get_parent_remove_exists($id_exists, $value['roleid'], $array_category, $level + 1);
                }
            } else {
                return;
            }
        }
    }

    /* Delete role from database */
    public function delete($id)
    {
        if (!has_permission('roles', '', 'delete')) {
            access_denied('roles');
        }
        if (!$id) {
            redirect(admin_url('roles'));
        }
        $response = $this->roles_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('role_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('role')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('role_lowercase')));
        }
        redirect(admin_url('roles'));
    }

    public function c_get_parent($id_parent = 0, &$array_category = [])
    {
        $this->db->where(array('roles_parent' => $id_parent));
        $current_level = $this->db->get('tblroles')->result_array();
        if ($current_level) {
            foreach ($current_level as $key => $value) {
                $array_category[$key] = $value;
                $array_category[$key]['child'] = [];
                $this->c_get_parent($value['roleid'], $array_category[$key]['child']);
            }
        } else {
            return;
        }
    }

    public function get_level($data, $level = 0, &$levelMax = 0)
    {
        if (!empty($data['child'])) {
            $level++;
            if ($level > $levelMax) {
                $levelMax = $level;
            }
            foreach ($data['child'] as $key => $value) {
                $this->get_level($value, $level, $levelMax);
            }
        } else {
            return $levelMax;
        }
    }


    public function dt_get_parent($data, $name_parent = '', $break = 0)
    {
        $result = [];
        if ($break == 50) {
            return $result;
        }
        foreach ($data as $key => $item) {
            $break++;
            if ($item['name_parent'] == $name_parent) {
                $result[$key] = $item;
                $child = $this->dt_get_parent($data, $item['code_role'], $break);
                if (!empty($child)) {
                    $result[$key]['child'] = $child;
                }
            }
        }
        return $result;
    }

    function data_tree_cost($data, $parent_id = 0, $level = 0)
    {
        $result = [];
        foreach ($data as $item) {
            if ($item['roles_parent'] == $parent_id) {
                $item['level'] = $level;
                $item['name_parent'] = '';
                if ($parent_id > 0) {
                    $dtRole = get_table_where('tblroles', ['roleid' => $parent_id], '', 'row_array');
                    if (!empty($dtRole)) {
                        $item['name_parent'] = $dtRole['code_role'];
                    }
                }
                $result[] = $item;
                $child = $this->data_tree_cost($data, $item['roleid'], $level + 1);
                $result = array_merge($result, $child);
            }
        }
        return $result;
    }

    public function export_excel()
    {

        $this->load->library('ciqrcode');
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $rowE = cloumns_excel();
        $this->db->select('
                tblroles.*,
                tbl_type_contract.name as name_type_contract,
                tbldepartments.code as code_department,
                tbldepartments.name as name_department,
            ');
        $this->db->from('tblroles');
        $this->db->where('tblroles.active_role', 1);
        $this->db->join('tbl_type_contract', 'tbl_type_contract.id = tblroles.type_contract', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');

        $this->db->select([
            'tbl_board.name as name_board',
            'tbl_board.code as code_board',

            'tbl_block.name as name_block',
            'tbl_block.code as code_block',

            'tbl_room.name as name_room',
            'tbl_room.code as code_room',

            'tbl_nest.name as name_nest',
            'tbl_nest.code as code_nest',

            'tbl_group.name as name_group',
            'tbl_group.code as code_group',
        ]);
        $this->db->join('tbl_board', 'tbl_board.id = tblroles.id_board', 'left');
        $this->db->join('tbl_block', 'tbl_block.id = tblroles.id_block', 'left');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $this->db->join('tbl_nest', 'tbl_nest.id = tblroles.id_nest', 'left');
        $this->db->join('tbl_group', 'tbl_group.id = tblroles.id_group', 'left');

        $dtRole = $this->db->get()->result_array();
        $array_category = [];
        $array_category = $this->data_tree_cost($dtRole);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(20);
        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
        $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
            "%0" . $decimals_number . "s",
            0
        ) : '');
        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $company_vat = get_option('company_vat');
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman'
            ),
        ]);
        $objPHPExcel->getActiveSheet()->setCellValue(
            'A1',
            ('CHỨC VỤ')
        )->getStyle("A1")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 18,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(25);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:W1');
        $sttRow = 2;

        $iR = 0;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mã Vị Trí');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Vị Trí');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Chức Vụ');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Vị Trí Cha');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Cấp');
        $iR++;

        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mã Hội - Ban');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Hội - Ban');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mã Khối');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Khối');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mã Phòng');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Phòng');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mã Tổ');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Tổ');
        $iR++;

        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mã Nhóm');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Nhóm');
        $iR++;


        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mã Phòng Ban');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Tên Phòng Ban');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Email');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Bậc Lương');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Hệ Lương');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Bảng Lương');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Hệ Số Tăng Ca');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Phép');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Vật Tư-Trang Thiết Bị');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Mô Tả Công Việc');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'KPI');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Hợp Đồng Thử Việc');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Ngày Thử Việc');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Ngày Kết Thúc');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Ngày Ký HĐ');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Loại Hợp Đồng');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Ngày Hiệu Lực');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Thời Gian Tái Ký');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Thời gian xét tăng lương');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'Thời Gian Khám Sức Khỏe');
        $iR++;
        $objPHPExcel->getActiveSheet()->setCellValue($rowE[$iR] . $sttRow . '', 'QR');


        $objPHPExcel->getActiveSheet()->getStyle("$rowE[0]$sttRow:$rowE[$iR]$sttRow")->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFF00'),
            ),
        ]);
        $rowBegin = $sttRow;
        if (!empty($array_category)) {
            foreach ($array_category as $key => $value) {
                $level = $value['level'];
                $rowBegin++;
                //				$objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['name_board']);
                //				$objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name_block']);
                //				$objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_room']));
                //				$objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['name_nest']));

                $iR = 0;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['code_role']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    $value['name']
                )->getStyle("$rowE[$iR]$rowBegin")->getAlignment()->setWrapText(true);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['name_position']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['name_parent']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin",  $level);
                $iR++;

                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['code_board']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['name_board']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['code_block']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['name_block']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['code_room']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['name_room']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['code_nest']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['name_nest']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['code_group']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", ($value['name_group']));
                $iR++;


                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['code_department']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['name_department']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['email']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    ($value['salary'])
                )->getStyle("$rowE[$iR]$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['salary']));
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    $value['coefficient_salary']
                )->getStyle("$rowE[$iR]$rowBegin")->getAlignment()->setWrapText(true);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    $value['payroll']
                )->getStyle("$rowE[$iR]$rowBegin")->getAlignment()->setWrapText(true);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    $value['coefficient_overtime']
                )->getStyle("$rowE[$iR]$rowBegin")->getAlignment()->setWrapText(true);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    $value['paid_holiday']
                )->getStyle("$rowE[$iR]$rowBegin")->getAlignment()->setWrapText(true);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    $value['supplies']
                )->getStyle("$rowE[$iR]$rowBegin")->getAlignment()->setWrapText(true);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['detail_task']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['kpi']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", $value['contract']);
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    !empty($value['date_start_contract']) ? _dhau($value['date_start_contract']) : ''
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    !empty($value['date_end_contract']) ? _dhau($value['date_end_contract']) : ''
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    !empty($value['date_sign_contract']) ? _dhau($value['date_sign_contract']) : ''
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    (!empty($dtTypeContract) ? $dtTypeContract['name'] : '')
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    (!empty($value['date_effective_contract']) ? _dhau($value['date_effective_contract']) : '')
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    $value['time_sign']
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;

                //         $CI->db->join('tbl_salary_deadline', 'tbl_salary_deadline.location = tblroles.roleid', 'left');
                // $CI->db->join('tbl_physical_deadline', 'tbl_physical_deadline.location = tblroles.roleid', 'left');
                $salary_deadline = get_table_where('tbl_salary_deadline', ['location' => $value['roleid']], '', 'row_array');
                $physical_deadline = get_table_where('tbl_physical_deadline', ['location' => $value['roleid']], '', 'row_array');
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    (!empty($salary_deadline) ? $salary_deadline['time'] : '')
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;
                $objPHPExcel->getActiveSheet()->setCellValue(
                    "$rowE[$iR]$rowBegin",
                    (!empty($physical_deadline) ? $physical_deadline['time'] : '')
                )->getStyle("$rowE[$iR]$rowBegin");
                $iR++;
                $code = 'roles||' . $value['roleid'];
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/roles/';
                if (!file_exists($folder)) {
                    mkdir($folder);
                    fopen($folder . 'index.html', 'w');
                }
                if (!file_exists($folder . 'qrcode' . '/')) {
                    mkdir($folder . 'qrcode' . '/');
                    fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                }
                $params['data'] = $code;
                $params['level'] = 'H';
                $params['size'] = 40;
                $params['savename'] = $folder . 'qrcode/' . $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder . 'qrcode/' . $qr . '.png');
                if (!empty($img)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($img);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates("$rowE[$iR]$rowBegin");
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue("$rowE[$iR]$rowBegin", '');


                $objPHPExcel->getActiveSheet()->getStyle("$rowE[0]$rowBegin:$rowE[$iR]$rowBegin")->applyFromArray([
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                ]);

                $objPHPExcel->getActiveSheet()->getStyle("N$rowBegin:N$rowBegin")->applyFromArray([
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
            }
        }
        $filename = lang('chuc_vu') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function export_excel_old()
    {
        if (!has_permission('roles', '', 'export')) {
            access_denied();
        }
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $cloumns = $this->input->post('cloumns');
        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

        $style_excel['Background_header_two'] = $style_excel['Background_header'];
        $style_excel['Background_header_two']['fill']['color']['rgb'] = 'f79e83';

        $style_excel['Background_header_three'] = $style_excel['Background_header'];
        $style_excel['Background_header_three']['fill']['color']['rgb'] = '8ac78c';


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        $numberRow = 2;
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AD")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AE")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AF")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AH")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AI")->setWidth(30);


        $this->db->select('tblroles.*, tbldepartments.name as name_departments');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
        $full_categories = $this->db->get_where('tblroles', ['roles_parent' => 0])->result_array();
        $array_category = [];
        foreach ($full_categories as $key => $value) {
            $array_category[$key] = $value;
            $array_category[$key]['child'] = [];
            $this->c_get_parent($value['roleid'], $array_category[$key]['child']);
        }

        $level = 1;
        $levelMax = 0;
        foreach ($array_category as $key => $value) {
            $this->get_level($value, $level, $levelMax);
        }

        $s = 0;
        $numberRow = 1;
        $objPHPExcel->getActiveSheet()->SetCellValue(
            "$cloumns_excel[$s]$numberRow",
            'Tên chức vụ'
        )->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $s++;
        for ($i = 1; $i < $levelMax; $i++) {
            $objPHPExcel->getActiveSheet()->SetCellValue(
                "$cloumns_excel[$s]$numberRow",
                'Tên chức vụ cấp ' . $i
            )->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
            $s++;
        }

        $objPHPExcel->getActiveSheet()->SetCellValue(
            "$cloumns_excel[$s]$numberRow",
            'Phòng ban'
        )->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue(
            "$cloumns_excel[$s]$numberRow",
            'Hệ số'
        )->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $numberRow++;

        $lastS = $levelMax;
        $s = 0;
        foreach ($array_category as $key => $value) {
            $lastS = $levelMax - 1;
            $sMax = $s + $levelMax - 1;
            $objPHPExcel->getActiveSheet()->mergeCells("$cloumns_excel[0]$numberRow:$cloumns_excel[$sMax]$numberRow")->getStyle("$cloumns_excel[0]$numberRow:$cloumns_excel[$sMax]$numberRow")->applyFromArray($style_excel['BStyle_left']);
            $objPHPExcel->getActiveSheet()->SetCellValue(
                "$cloumns_excel[0]$numberRow",
                $value['name']
            )->getStyle("$cloumns_excel[0]$numberRow")->applyFromArray($style_excel['BStyle_left']);
            $lastS++;

            $objPHPExcel->getActiveSheet()->SetCellValue(
                "$cloumns_excel[$lastS]$numberRow",
                $value['name_departments']
            )->getStyle("$cloumns_excel[$lastS]$numberRow")->applyFromArray($style_excel['BStyle_center']);
            $lastS++;
            $objPHPExcel->getActiveSheet()->SetCellValue(
                "$cloumns_excel[$lastS]$numberRow",
                $value['coefficient']
            )->getStyle("$cloumns_excel[$lastS]$numberRow")->applyFromArray($style_excel['BStyle_center']);
            $lastS++;

            $numberRow++;

            if (!empty($value['child'])) {
                $data_role = [];
                $this->c_get_one_child($value['roleid'], $data_role, 0);
                foreach ($data_role as $k => $v) {
                    $lastS = $levelMax;
                    $level = $v['level'];
                    $levelFrist = $v['level'] - 1;
                    $objPHPExcel->getActiveSheet()->mergeCells("$cloumns_excel[0]$numberRow:$cloumns_excel[$levelFrist]$numberRow")->getStyle("$cloumns_excel[0]$numberRow:$cloumns_excel[$levelFrist]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                    $objPHPExcel->getActiveSheet()->SetCellValue(
                        "$cloumns_excel[0]$numberRow",
                        ''
                    )->getStyle("$cloumns_excel[0]$numberRow")->applyFromArray($style_excel['BStyle_left']);


                    $objPHPExcel->getActiveSheet()->mergeCells("$cloumns_excel[$level]$numberRow:$cloumns_excel[$sMax]$numberRow")->getStyle("$cloumns_excel[$level]$numberRow:$cloumns_excel[$sMax]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                    $objPHPExcel->getActiveSheet()->SetCellValue(
                        "$cloumns_excel[$level]$numberRow",
                        $v['name']
                    )->getStyle("$cloumns_excel[$level]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                    $objPHPExcel->getActiveSheet()->SetCellValue(
                        "$cloumns_excel[$lastS]$numberRow",
                        $v['name_departments']
                    )->getStyle("$cloumns_excel[$lastS]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                    $lastS++;
                    $objPHPExcel->getActiveSheet()->SetCellValue(
                        "$cloumns_excel[$lastS]$numberRow",
                        $v['coefficient']
                    )->getStyle("$cloumns_excel[$lastS]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                    $numberRow++;
                }
            }

            $fullMax = $levelMax + 1;
            $objPHPExcel->getActiveSheet()->mergeCells("$cloumns_excel[0]$numberRow:$cloumns_excel[$fullMax]$numberRow")->getStyle("$cloumns_excel[0]$numberRow:$cloumns_excel[$fullMax]$numberRow")->applyFromArray($style_excel['Background_header_two']);
            $numberRow++;
        }


        $filename = lang('Danh_sach_phong_ban') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function c_get_one_child($roles_parent = '', &$data_role = [], $level = 0)
    {
        $level++;
        $this->db->select('tblroles.*, tbldepartments.name as name_departments');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblroles.departments_id', 'left');
        $this->db->where(array('roles_parent' => $roles_parent));
        $current_level = $this->db->get('tblroles')->result_array();
        foreach ($current_level as $key => $value) {
            $value['level'] = $level;
            $data_role[] = $value;
            $this->c_get_one_child($value['roleid'], $data_role, $level);
        }
    }


    public function modal_excel_import()
    {
        $data['title'] = _l('Import chức vụ bằng File Excel');
        $this->load->view('admin/roles/excel_import', $data);
    }


    public function excel_import()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            // $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('W');
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AG');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 3][$col] = $value;
                }
            }

            $dataArray = [];
            $keyNameDepartments = [];
            foreach ($arraydata as $key => $value) {

                if (empty($value[0])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã vị trí</div>';
                    continue;
                }
                $code_role = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');

                if (empty($value[1])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy tên vị trí</div>';
                    continue;
                }
                $name_role = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[1])), 'UTF-8');

                if (empty($value[2])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy tên chức vụ</div>';
                    continue;
                }
                $name_position = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[2])), 'UTF-8');

                $name_parent = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[3])), 'UTF-8');

                if (!isset($value[4])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy cấp</div>';
                    continue;
                }
                $level = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[4])), 'UTF-8');

                //				if (empty($value[5])) {
                //					$errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã hội - ban</div>';
                //					continue;
                //				}
                $code_board = @mb_strtoupper(preg_replace('/\s+/',     ' ', trim($value[5])), 'UTF-8');
                $name_board = @preg_replace('/\s+/', ' ', trim($value[6]));

                //				if (empty($value[7])) {
                //					$errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã khối</div>';
                //					continue;
                //				}
                $code_block = @mb_strtoupper(preg_replace('/\s+/',     ' ', trim($value[7])), 'UTF-8');
                $name_block = @preg_replace('/\s+/', ' ', trim($value[8]));


                //				if (empty($value[9])) {
                //					$errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã phòng</div>';
                //					continue;
                //				}
                $code_room = @mb_strtoupper(preg_replace('/\s+/',     ' ', trim($value[9])), 'UTF-8');
                $name_room = @preg_replace('/\s+/', ' ', trim($value[10]));

                //				if (empty($value[11])) {
                //					$errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã tổ</div>';
                //					continue;
                //				}
                $code_nest = @mb_strtoupper(preg_replace('/\s+/',     ' ', trim($value[11])), 'UTF-8');
                $name_nest = @preg_replace('/\s+/', ' ', trim($value[12]));

                //				if (empty($value[13])) {
                //					$errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã nhóm</div>';
                //					continue;
                //				}
                $code_group = mb_strtoupper(preg_replace('/\s+/',     ' ', trim($value[13])), 'UTF-8');
                $name_group = @preg_replace('/\s+/', ' ', trim($value[14]));



                //                if (empty($value[15])) {
                //                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã phòng ban</div>';
                //                    continue;
                //                }
                $code_departments = @mb_strtoupper(preg_replace('/\s+/',     ' ', trim($value[15])), 'UTF-8');
                $name_departments = @preg_replace('/\s+/', ' ', trim($value[16]));

                if ($level > 0 && empty($name_parent)) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy tên vị trí cha</div>';
                    continue;
                }

                $email = (trim($value[17]));
                // $salary = number_unformat(trim($value[18]));
                $salary = _string($value[18]);
                $salary_id = 0;
                if (!empty($salary)) {
                    $dtStepSalary = get_table_where('tbl_step_salary', ['code' => $salary], '', 'row_array', '', 'id');
                    if (!empty($dtStepSalary)) {
                        $salary_id = $dtStepSalary['id'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã [' . $salary . '] bậc lương</div>';
                        continue;
                    }
                }

                $coefficient_salary = trim($value[19]);
                $coefficient_salary_id = 0;
                if (!empty($coefficient_salary)) {
                    $dtCoefficientSalary = get_table_where('tbl_coefficient_salary', ['code' => $coefficient_salary], '', 'row_array', '', 'id');
                    if (!empty($dtCoefficientSalary)) {
                        $coefficient_salary_id = $dtCoefficientSalary['id'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã [' . $coefficient_salary . '] hệ lương</div>';
                        continue;
                    }
                }

                $payroll = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[20])), 'UTF-8');
                $coefficient_overtime = trim($value[21]);

                $paid_holiday = trim($value[22]);
                $paid_holiday_id = 0;
                if (!empty($paid_holiday)) {
                    $dtPermission = get_table_where('tbl_permission', ['code' => $paid_holiday], '', 'row_array', '', 'id');
                    if (!empty($dtPermission)) {
                        $paid_holiday_id = $dtPermission['id'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã [' . $paid_holiday . '] phép</div>';
                        continue;
                    }
                }

                $supplies = @mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[23])), 'UTF-8');
                $detail_task = @mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[24])), 'UTF-8');
                $kpi = !empty($value[25]) ? mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[25])), 'UTF-8') : NULL;
                $kpi_category_id = 0;
                if (!empty($kpi)) {
                    $dtCategoryKpi = get_table_where('tbl_category_kpi', ['code' => $kpi], '', 'row_array', '', 'id');
                    if (!empty($dtCategoryKpi)) {
                        $kpi_category_id = $dtCategoryKpi['id'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã [' . $kpi . '] nhóm KPI</div>';
                        continue;
                    }
                }

                $contract = @mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[26])), 'UTF-8');
                $contract_id = 0;
                if (!empty($contract)) {
                    $dtContractLabor = get_table_where('tbl_contract_labor', ['code' => $contract], '', 'row_array', '', 'id');
                    if (!empty($dtContractLabor)) {
                        $contract_id = $dtContractLabor['id'];
                    } else {
                        $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã [' . $kpi . '] hợp đồng lao động</div>';
                        continue;
                    }
                }

                $date_start_contract = !empty($value[27]) ? $value[27] : NULL;
                if (!empty($date_start_contract)) {
                    if (gettype($date_start_contract) == 'double' || gettype($date_start_contract) == 'int') {
                        $date_start_contract = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_start_contract));
                    } else if (gettype($date_start_contract) == 'string') {
                        $date_start_contract = to_sql_date($date_start_contract);
                    }
                }

                $date_end_contract = @($value[28]);
                if (!empty($date_end_contract)) {
                    if (gettype($date_end_contract) == 'double' || gettype($date_end_contract) == 'int') {
                        $date_end_contract = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_end_contract));
                    } else if (gettype($date_end_contract) == 'string') {
                        $date_end_contract = to_sql_date($date_end_contract);
                    }
                }

                $date_sign_contract = @($value[29]);
                if (!empty($date_sign_contract)) {
                    if (gettype($date_sign_contract) == 'double' || gettype($date_sign_contract) == 'int') {
                        $date_sign_contract = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_sign_contract));
                    } else if (gettype($date_sign_contract) == 'string') {
                        $date_sign_contract = to_sql_date($date_sign_contract);
                    }
                }

                $type_contract = @($value[30]);
                $date_effective_contract = @trim($value[31]);
                if (!empty($date_effective_contract)) {
                    if (gettype($date_effective_contract) == 'double' || gettype($date_effective_contract) == 'int') {
                        $date_effective_contract = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_effective_contract));
                    } else if (gettype($date_effective_contract) == 'string') {
                        $date_effective_contract = to_sql_date($date_effective_contract);
                    }
                }

                $time_sign = @mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[32])), 'UTF-8');

                $dataArray[] = [
                    'code_board' => !empty($code_board) ? $code_board : NULL,
                    'name_board' => !empty($name_board) ? $name_board : NULL,
                    'code_block' => !empty($code_block) ? $code_block : NULL,
                    'name_block' => !empty($name_block) ? $name_block : NULL,
                    'code_room' => !empty($code_room) ? $code_room : NULL,
                    'name_room' => !empty($name_room) ? $name_room : NULL,
                    'code_nest' => !empty($code_nest) ? $code_nest : NULL,
                    'name_nest' => !empty($name_nest) ? $name_nest : NULL,
                    'code_group' => !empty($code_group) ? $code_group : NULL,
                    'name_group' => !empty($name_group) ? $name_group : NULL,

                    'code_role' => $code_role,
                    'name' => $name_role,
                    'name_position' => $name_position,
                    'level' => $level,
                    'code_departments' => $code_departments,
                    'name_departments' => $name_departments,
                    'name_parent' => $name_parent,
                    'email' => $email,
                    // 'salary' => $salary,
                    'salary_id' => $salary_id,
                    // 'coefficient_salary' => $coefficient_salary,
                    'coefficient_salary_id' => $coefficient_salary_id,
                    'payroll' => $payroll,
                    'coefficient_overtime' => $coefficient_overtime,
                    // 'paid_holiday' => $paid_holiday,
                    'paid_holiday_id' => $paid_holiday_id,
                    'supplies' => $supplies,
                    'detail_task' => $detail_task,
                    // 'kpi' => $kpi,
                    'kpi_category_id' => $kpi_category_id,
                    // 'contract' => $contract,
                    'contract_id' => $contract_id,
                    'date_start_contract' => $date_start_contract,
                    'date_end_contract' => $date_end_contract,
                    'date_sign_contract' => $date_sign_contract,
                    'type_contract' => $type_contract,
                    'date_effective_contract' => $date_effective_contract,
                    'time_sign' => $time_sign,
                ];
            }
            $dataArrayResult = $this->dt_get_parent($dataArray);

            $count = 0;
            if (!empty($dataArrayResult)) {
                foreach ($dataArrayResult as $key => $value) {
                    $this->insertRole($value, 0, $count);
                }
            }

            foreach ($dataArray as $key => $value) {
                $this->db->where('code_role', $value['name_parent']);
                $roles_parent = $this->db->get('tblroles')->row();
                if (!empty($roles_parent)) {
                    $this->insertRole($value, $roles_parent->roleid, $count);
                }
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Thêm mới và cập nhật thành công ' . $count . ' chức vụ',
                ]
            );
            die();
        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }

    // viet tạm
    function insertRole($value, $parent_id = 0, &$count = 0)
    {
        $child = !empty($value['child']) ? $value['child'] : [];


        $code_board = !empty($value['code_board']) ? $value['code_board'] : NULL;
        $name_board = empty($value['name_board']) ? $value['name_board'] : NULL;
        $code_block = !empty($value['code_block']) ? $value['code_block'] : NULL;
        $name_block = !empty($value['name_block']) ? $value['name_block'] : NULL;
        $code_room = !empty($value['code_room']) ? $value['code_room'] : NULL;
        $name_room = !empty($value['name_room']) ? $value['name_room'] : NULL;
        $code_nest = !empty($value['code_nest']) ? $value['code_nest'] : NULL;
        $name_nest = !empty($value['name_nest']) ? $value['name_nest'] : NULL;
        $code_group = !empty($value['code_group']) ? $value['code_group'] : NULL;
        $name_group = !empty($value['name_group']) ? $value['name_group'] : NULL;


        $code_departments = $value['code_departments'];
        $name_departments = $value['name_departments'];
        unset($value['child']);
        unset($value['code_departments']);
        unset($value['name_departments']);

        unset($value['code_board']);
        unset($value['name_board']);
        unset($value['code_block']);
        unset($value['name_block']);
        unset($value['code_room']);
        unset($value['name_room']);
        unset($value['code_nest']);
        unset($value['name_nest']);
        unset($value['code_group']);
        unset($value['name_group']);


        unset($value['name_parent']);
        unset($value['level']);
        $type_contract = !empty($value['type_contract']) ? $value['type_contract'] : 0;
        $value['roles_parent'] = $parent_id;


        $departments_id = NULL;
        if (!empty($code_departments)) {
            $this->db->where('code', $code_departments);
            $this->db->where('active_departments', 1);
            $departments = $this->db->get('tbldepartments')->row();
            if (empty($departments)) {
                $this->db->insert('tbldepartments', [
                    'code' => $code_departments,
                    'name' => $name_departments,
                ]);
                $this->db->where('code', $code_departments);
                $this->db->where('active_departments', 1);
                $departments = $this->db->get('tbldepartments')->row();
                $departments_id = $departments->departmentid;
            } else {
                $departments_id = $departments->departmentid;
            }
        }

        $id_board = NULL;
        if (!empty($code_board)) {
            $this->db->where('code', $code_board);
            $board = $this->db->get('tbl_board')->row();
            if (empty($board)) {
                $this->db->insert('tbl_board', [
                    'code' => $code_board,
                    'name' => $name_board,
                ]);
                $this->db->where('code', $code_board);
                $board = $this->db->get('tbl_board')->row();
                $id_board = $board->id;
            } else {
                $id_board = $board->id;
            }
        }

        $id_block = NULL;
        if (!empty($code_block)) {
            $this->db->where('code', $code_block);
            $block = $this->db->get('tbl_block')->row();
            if (empty($block)) {
                $this->db->insert('tbl_block', [
                    'code' => $code_block,
                    'name' => $name_block,
                ]);
                $this->db->where('code', $code_block);
                $block = $this->db->get('tbl_block')->row();
                $id_block = $block->id;
            } else {
                $id_block = $block->id;
            }
        }

        $id_room = NULL;
        if (!empty($code_room)) {
            $this->db->where('code', $code_room);
            $room = $this->db->get('tbl_room')->row();
            if (empty($room)) {
                $this->db->insert('tbl_room', [
                    'code' => $code_room,
                    'name' => $name_room,
                ]);
                $this->db->where('code', $code_room);
                $room = $this->db->get('tbl_room')->row();
                $id_room = $room->id;
            } else {
                $id_room = $room->id;
            }
        }

        $id_nest = NULL;
        if (!empty($code_nest)) {
            $this->db->where('code', $code_nest);
            $nest = $this->db->get('tbl_nest')->row();
            if (empty($nest)) {
                $this->db->insert('tbl_nest', [
                    'code' => $code_nest,
                    'name' => $name_nest,
                ]);
                $this->db->where('code', $code_nest);
                $nest = $this->db->get('tbl_nest')->row();
                $id_nest = $nest->id;
            } else {
                $id_nest = $nest->id;
            }
        }

        $id_group = NULL;
        if (!empty($code_group)) {
            $this->db->where('code', $code_group);
            $group = $this->db->get('tbl_group')->row();
            if (empty($group)) {
                $this->db->insert('tbl_group', [
                    'code' => $code_group,
                    'name' => $name_group,
                ]);
                $this->db->where('code', $code_group);
                $group = $this->db->get('tbl_group')->row();
                $id_group = $group->id;
            } else {
                $id_group = $group->id;
            }
        }


        if (!empty($type_contract)) {
            $this->db->where('name', $type_contract);
            $dtTypeContract = $this->db->get('tbl_type_contract')->row();
            if (empty($dtTypeContract)) {
                $this->db->insert('tbl_type_contract', [
                    'code' => $type_contract,
                    'name' => $type_contract,
                ]);
                $type_contract = $this->db->insert_id();
            } else {
                $type_contract = $dtTypeContract->id;
            }
        }
        $value['type_contract'] = $type_contract;
        if (!empty($departments_id)) {
            $value['departments_id'] = $departments_id;
        }

        $value['id_board'] = $id_board;
        $value['id_block'] = $id_block;
        $value['id_room'] = $id_room;
        $value['id_nest'] = $id_nest;
        $value['id_group'] = $id_group;

        $this->db->from('tblroles');
        $this->db->where('code_role', $value['code_role']);
        $dtRole = $this->db->get()->row_array();
        if (!empty($dtRole)) {
            $this->db->where('roleid', $dtRole['roleid']);
            $success = $this->db->update('tblroles', $value);
            if ($success) {
                $count++;
                $parent_id = $dtRole['roleid'];
            }
        } else {
            $this->db->insert('tblroles', $value);
            $success = $this->db->insert_id();
            if ($success) {
                $count++;
                $parent_id = $success;
            }
        }
        if (!empty($child)) {
            $child = array_values($child);
            foreach ($child as $kk => $vv) {
                $child1 = !empty($vv['child']) ? $vv['child'] : [];
                $code_departments = $vv['code_departments'];
                $name_departments = $vv['name_departments'];

                $code_board = !empty($vv['code_board']) ? $vv['code_board'] : NULL;
                $name_board = empty($vv['name_board']) ? $vv['name_board'] : NULL;
                $code_block = !empty($vv['code_block']) ? $vv['code_block'] : NULL;
                $name_block = !empty($vv['name_block']) ? $vv['name_block'] : NULL;
                $code_room = !empty($vv['code_room']) ? $vv['code_room'] : NULL;
                $name_room = !empty($vv['name_room']) ? $vv['name_room'] : NULL;
                $code_nest = !empty($vv['code_nest']) ? $vv['code_nest'] : NULL;
                $name_nest = !empty($vv['name_nest']) ? $vv['name_nest'] : NULL;
                $code_group = !empty($vv['code_group']) ? $vv['code_group'] : NULL;
                $name_group = !empty($vv['name_group']) ? $vv['name_group'] : NULL;


                unset($vv['code_board']);
                unset($vv['name_board']);
                unset($vv['code_block']);
                unset($vv['name_block']);
                unset($vv['code_room']);
                unset($vv['name_room']);
                unset($vv['code_nest']);
                unset($vv['name_nest']);
                unset($vv['code_group']);
                unset($vv['name_group']);


                unset($vv['child']);
                unset($vv['code_departments']);
                unset($vv['name_departments']);
                unset($vv['name_parent']);
                unset($vv['level']);
                $type_contract = !empty($vv['type_contract']) ? $vv['type_contract'] : 0;
                $vv['roles_parent'] = $parent_id;

                $departments_id = NULL;
                if (!empty($code_departments)) {
                    $this->db->where('code', $code_departments);
                    $this->db->where('active_departments', 1);
                    $departments = $this->db->get('tbldepartments')->row();
                    if (empty($departments)) {
                        $this->db->insert('tbldepartments', [
                            'code' => $code_departments,
                            'name' => $name_departments,
                        ]);
                        $this->db->where('code', $code_departments);
                        $this->db->where('active_departments', 1);
                        $departments = $this->db->get('tbldepartments')->row();
                        $departments_id = $departments->departmentid;
                    } else {
                        $departments_id = $departments->departmentid;
                    }
                }

                $id_board = NULL;
                if (!empty($code_board)) {
                    $this->db->where('code', $code_board);
                    $board = $this->db->get('tbl_board')->row();
                    if (empty($board)) {
                        $this->db->insert('tbl_board', [
                            'code' => $code_board,
                            'name' => $name_board,
                        ]);
                        $this->db->where('code', $code_board);
                        $board = $this->db->get('tbl_board')->row();
                        $id_board = $board->id;
                    } else {
                        $id_board = $board->id;
                    }
                }

                $id_block = NULL;
                if (!empty($code_block)) {
                    $this->db->where('code', $code_block);
                    $block = $this->db->get('tbl_block')->row();
                    if (empty($block)) {
                        $this->db->insert('tbl_block', [
                            'code' => $code_block,
                            'name' => $name_block,
                        ]);
                        $this->db->where('code', $code_block);
                        $block = $this->db->get('tbl_block')->row();
                        $id_block = $block->id;
                    } else {
                        $id_block = $block->id;
                    }
                }

                $id_room = NULL;
                if (!empty($code_room)) {
                    $this->db->where('code', $code_room);
                    $room = $this->db->get('tbl_room')->row();
                    if (empty($room)) {
                        $this->db->insert('tbl_room', [
                            'code' => $code_room,
                            'name' => $name_room,
                        ]);
                        $this->db->where('code', $code_room);
                        $room = $this->db->get('tbl_room')->row();
                        $id_room = $room->id;
                    } else {
                        $id_room = $room->id;
                    }
                }

                $id_nest = NULL;
                if (!empty($code_nest)) {
                    $this->db->where('code', $code_nest);
                    $nest = $this->db->get('tbl_nest')->row();
                    if (empty($nest)) {
                        $this->db->insert('tbl_nest', [
                            'code' => $code_nest,
                            'name' => $name_nest,
                        ]);
                        $this->db->where('code', $code_nest);
                        $nest = $this->db->get('tbl_nest')->row();
                        $id_nest = $nest->id;
                    } else {
                        $id_nest = $nest->id;
                    }
                }

                $id_group = NULL;
                if (!empty($code_group)) {
                    $this->db->where('code', $code_group);
                    $group = $this->db->get('tbl_group')->row();
                    if (empty($group)) {
                        $this->db->insert('tbl_group', [
                            'code' => $code_group,
                            'name' => $name_group,
                        ]);
                        $this->db->where('code', $code_group);
                        $group = $this->db->get('tbl_group')->row();
                        $id_group = $group->id;
                    } else {
                        $id_group = $group->id;
                    }
                }






                if (!empty($type_contract)) {
                    $this->db->where('name', $type_contract);
                    $dtTypeContract = $this->db->get('tbl_type_contract')->row();
                    if (empty($dtTypeContract)) {
                        $this->db->insert('tbl_type_contract', [
                            'code' => $type_contract,
                            'name' => $type_contract,
                        ]);
                        $type_contract = $this->db->insert_id();
                    } else {
                        $type_contract = $dtTypeContract->id;
                    }
                }
                $vv['type_contract'] = $type_contract;
                if (!empty($departments_id)) {
                    $vv['departments_id'] = $departments_id;
                }


                $vv['id_board'] = $id_board;
                $vv['id_block'] = $id_block;
                $vv['id_room'] = $id_room;
                $vv['id_nest'] = $id_nest;
                $vv['id_group'] = $id_group;

                $this->db->from('tblroles');
                $this->db->where('code_role', $vv['code_role']);
                $dtRole = $this->db->get()->row_array();
                if (!empty($dtRole)) {
                    $this->db->where('roleid', $dtRole['roleid']);
                    $success = $this->db->update('tblroles', $vv);
                    if ($success) {
                        $count++;
                        $parent_id_child = $dtRole['roleid'];
                    }
                } else {
                    $this->db->insert('tblroles', $vv);
                    $success = $this->db->insert_id();
                    if ($success) {
                        $count++;
                        $parent_id_child = $success;
                    }
                }

                if (!empty($child1)) {
                    $child1 = array_values($child1);
                    foreach ($child1 as $kkk => $vvv) {
                        $code_departments = $vvv['code_departments'];
                        $name_departments = $vvv['name_departments'];

                        $code_board = !empty($vvv['code_board']) ? $vvv['code_board'] : NULL;
                        $name_board = empty($vvv['name_board']) ? $vvv['name_board'] : NULL;
                        $code_block = !empty($vvv['code_block']) ? $vvv['code_block'] : NULL;
                        $name_block = !empty($vvv['name_block']) ? $vvv['name_block'] : NULL;
                        $code_room = !empty($vvv['code_room']) ? $vvv['code_room'] : NULL;
                        $name_room = !empty($vvv['name_room']) ? $vvv['name_room'] : NULL;
                        $code_nest = !empty($vvv['code_nest']) ? $vvv['code_nest'] : NULL;
                        $name_nest = !empty($vvv['name_nest']) ? $vvv['name_nest'] : NULL;
                        $code_group = !empty($vvv['code_group']) ? $vvv['code_group'] : NULL;
                        $name_group = !empty($vvv['name_group']) ? $vvv['name_group'] : NULL;


                        unset($vvv['code_board']);
                        unset($vvv['name_board']);
                        unset($vvv['code_block']);
                        unset($vvv['name_block']);
                        unset($vvv['code_room']);
                        unset($vvv['name_room']);
                        unset($vvv['code_nest']);
                        unset($vvv['name_nest']);
                        unset($vvv['code_group']);
                        unset($vvv['name_group']);

                        unset($vvv['child']);
                        unset($vvv['code_departments']);
                        unset($vvv['name_departments']);
                        unset($vvv['name_parent']);
                        unset($vvv['level']);
                        $type_contract = !empty($vvv['type_contract']) ? $vvv['type_contract'] : 0;
                        $vvv['roles_parent'] = $parent_id_child;

                        $departments_id = NULL;
                        if (!empty($code_departments)) {
                            $this->db->where('code', $code_departments);
                            $this->db->where('active_departments', 1);
                            $departments = $this->db->get('tbldepartments')->row();
                            if (empty($departments)) {
                                $this->db->insert('tbldepartments', [
                                    'code' => $code_departments,
                                    'name' => $name_departments,
                                ]);
                                $this->db->where('code', $code_departments);
                                $this->db->where('active_departments', 1);
                                $departments = $this->db->get('tbldepartments')->row();
                                $departments_id = $departments->departmentid;
                            } else {
                                $departments_id = $departments->departmentid;
                            }
                        }


                        $id_board = NULL;
                        if (!empty($code_board)) {
                            $this->db->where('code', $code_board);
                            $board = $this->db->get('tbl_board')->row();
                            if (empty($board)) {
                                $this->db->insert('tbl_board', [
                                    'code' => $code_board,
                                    'name' => $name_board,
                                ]);
                                $this->db->where('code', $code_board);
                                $board = $this->db->get('tbl_board')->row();
                                $id_board = $board->id;
                            } else {
                                $id_board = $board->id;
                            }
                        }

                        $id_block = NULL;
                        if (!empty($code_block)) {
                            $this->db->where('code', $code_block);
                            $block = $this->db->get('tbl_block')->row();
                            if (empty($block)) {
                                $this->db->insert('tbl_block', [
                                    'code' => $code_block,
                                    'name' => $name_block,
                                ]);
                                $this->db->where('code', $code_block);
                                $block = $this->db->get('tbl_block')->row();
                                $id_block = $block->id;
                            } else {
                                $id_block = $block->id;
                            }
                        }

                        $id_room = NULL;
                        if (!empty($code_room)) {
                            $this->db->where('code', $code_room);
                            $room = $this->db->get('tbl_room')->row();
                            if (empty($room)) {
                                $this->db->insert('tbl_room', [
                                    'code' => $code_room,
                                    'name' => $name_room,
                                ]);
                                $this->db->where('code', $code_room);
                                $room = $this->db->get('tbl_room')->row();
                                $id_room = $room->id;
                            } else {
                                $id_room = $room->id;
                            }
                        }

                        $id_nest = NULL;
                        if (!empty($code_nest)) {
                            $this->db->where('code', $code_nest);
                            $nest = $this->db->get('tbl_nest')->row();
                            if (empty($nest)) {
                                $this->db->insert('tbl_nest', [
                                    'code' => $code_nest,
                                    'name' => $name_nest,
                                ]);
                                $this->db->where('code', $code_nest);
                                $nest = $this->db->get('tbl_nest')->row();
                                $id_nest = $nest->id;
                            } else {
                                $id_nest = $nest->id;
                            }
                        }

                        $id_group = NULL;
                        if (!empty($code_group)) {
                            $this->db->where('code', $code_group);
                            $group = $this->db->get('tbl_group')->row();
                            if (empty($group)) {
                                $this->db->insert('tbl_group', [
                                    'code' => $code_group,
                                    'name' => $name_group,
                                ]);
                                $this->db->where('code', $code_group);
                                $group = $this->db->get('tbl_group')->row();
                                $id_group = $group->id;
                            } else {
                                $id_group = $group->id;
                            }
                        }



                        if (!empty($type_contract)) {
                            $this->db->where('name', $type_contract);
                            $dtTypeContract = $this->db->get('tbl_type_contract')->row();
                            if (empty($dtTypeContract)) {
                                $this->db->insert('tbl_type_contract', [
                                    'code' => $type_contract,
                                    'name' => $type_contract,
                                ]);
                                $type_contract = $this->db->insert_id();
                            } else {
                                $type_contract = $dtTypeContract->id;
                            }
                        }


                        $vvv['type_contract'] = $type_contract;
                        if (!empty($departments_id)) {
                            $vvv['departments_id'] = $departments_id;
                        }
                        $vvv['id_board'] = $id_board;
                        $vvv['id_block'] = $id_block;
                        $vvv['id_room'] = $id_room;
                        $vvv['id_nest'] = $id_nest;
                        $vvv['id_group'] = $id_group;
                        $this->db->from('tblroles');
                        $this->db->where('code_role', $vvv['code_role']);
                        $dtRole = $this->db->get()->row_array();
                        if (!empty($dtRole)) {
                            $this->db->where('roleid', $dtRole['roleid']);
                            $success = $this->db->update('tblroles', $vvv);
                            if ($success) {
                                $count++;
                            }
                        } else {
                            $this->db->insert('tblroles', $vvv);
                            $success = $this->db->insert_id();
                            if ($success) {
                                $count++;
                            }
                        }
                    }
                }
            }
        }
    }

    public function excel_import_old()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('G');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $dataArray = [];
            $keyNameDepartments = [];
            foreach ($arraydata as $key => $value) {
                if (empty($value[0])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã phòng ban</div>';
                    continue;
                }
                $code_departments = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');
                $name_departments = preg_replace('/\s+/', ' ', trim($value[1]));
                $keyNameDepartments[$code_departments] = $name_departments;


                $code_role_level_one = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[2])), 'UTF-8');
                $name_role_level_one = preg_replace('/\s+/', ' ', trim($value[3]));
                $name_role_level_two = preg_replace('/\s+/', ' ', trim($value[4]));
                $key_name_role_level_two = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[4])));


                if (empty($code_role_level_one) || empty($name_role_level_one)) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã chức vụ cấp 1</div>';
                    continue;
                }

                if (empty($dataArray[$code_departments][$code_role_level_one])) {
                    $dataArray[$code_departments][$code_role_level_one]['name'] = $name_role_level_one;
                }
                if (!empty($name_role_level_two)) {
                    $dataArray[$code_departments][$code_role_level_one]['item'][$key_name_role_level_two] = $name_role_level_two;
                }
            }
            $count_one = 0;
            $count_two = 0;
            $count_departments = 0;
            foreach ($dataArray as $code_departments => $items) {
                $this->db->where('code', $code_departments);
                $this->db->where('active_departments', 1);
                $departments = $this->db->get('tbldepartments')->row();
                if (empty($departments)) {
                    if (!empty($keyNameDepartments[$code_departments])) {
                        $this->db->insert('tbldepartments', [
                            'code' => $code_departments,
                            'name' => $keyNameDepartments[$code_departments],
                        ]);
                        $count_departments++;
                    }
                    $this->db->where('code', $code_departments);
                    $this->db->where('active_departments', 1);
                    $departments = $this->db->get('tbldepartments')->row();
                }

                if (!empty($departments)) {
                    foreach ($items as $role_one => $item) {
                        $this->db->where('code_role', $role_one);
                        $kt_role_one = $this->db->get('tblroles')->row();
                        if (!empty($kt_role_one)) {
                            $arrayInsert = [];
                            foreach ($item['item'] as $key => $value) {
                                if (empty($value)) {
                                    $arrayInsert[] = [
                                        'departments_id' => $departments->departmentid,
                                        'name' => $value,
                                        'roles_parent' => $kt_role_one->roleid
                                    ];
                                }
                            }
                            if (!empty($arrayInsert)) {
                                $this->db->insert_batch('tblroles', $arrayInsert);
                                $count_two += count($arrayInsert);
                            }
                        } else {
                            $success_role_one = $this->db->insert('tblroles', [
                                'code_role' => $role_one,
                                'departments_id' => $departments->departmentid,
                                'name' => $item['name'],
                                'roles_parent' => 0
                            ]);
                            if (!empty($success_role_one)) {
                                $count_one++;
                                $this->db->where('code_role', $role_one);
                                $kt_role_one = $this->db->get('tblroles')->row();
                                if (!empty($kt_role_one)) {
                                    $arrayInsert = [];
                                    foreach ($item['item'] as $key => $value) {
                                        if (!empty($value)) {
                                            $arrayInsert[] = [
                                                'departments_id' => $departments->departmentid,
                                                'name' => $value,
                                                'roles_parent' => $kt_role_one->roleid
                                            ];
                                        }
                                    }
                                    if (!empty($arrayInsert)) {
                                        $this->db->insert_batch('tblroles', $arrayInsert);
                                        $count_two += count($arrayInsert);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $errors .= '<div>Không tìm thấy mã phòng ban [' . $code_departments . '] trong dữ liệu</div>';
                }
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Import Thêm mới thành công ' . $count_departments . ' phòng ban và ' . $count_one . ' chức vụ cấp 1 và ' . $count_two . ' chức vụ cấp 2',
                ]
            );
            die();
        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }

    public function updateStaff($role_id = 0)
    {
        $data = [];
        if ($this->input->post()) {
            $dataPost = $this->input->post();
            $counter = !empty($dataPost['counter']) ? $dataPost['counter'] : [];
            $items = [];
            $arrId = [0];
            if (!empty($counter)) {
                foreach ($counter as $key => $value) {
                    $staff_id = $this->input->post('staff_id')[$value];
                    $items[] = [
                        'staff_id' => $staff_id
                    ];
                    $arrId[] = $staff_id;
                }
            }
            $dtRoles = get_table_where('tblroles', ['roleid' => $role_id], '', 'row_array');
            if (empty($dtRoles)) {
                $data['result'] = 0;
                $data['message'] = lang('Không tồn tại chức vụ');
                echo json_encode($data);
                die();
            }
            // if (empty($dtRoles['departments_id'])){
            //     $data['result'] = 0;
            //     $data['message'] = lang('Chức vụ chưa tồn tại phòng ban!');
            //     echo json_encode($data);die();
            // }
            $this->db->from('tblstaff');
            $this->db->where('role', $role_id);
            $this->db->where_not_in('staffid', $arrId);
            $dtStaffRolOld = $this->db->get()->result_array();

            $role_parent = get_table_where('tbl_roles_parent_permission_v2', ['id_role' => $role_id, 'can_view' => 1]);
            $role_child = get_table_where('tbl_roles_child_permission_v2', ['id_role' => $role_id]);
            $success = false;
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $this->db->where('staffid', $value['staff_id']);
                    $this->db->where('departmentid', $dtRoles['departments_id']);
                    $dtCheckExitst = $this->db->get('tblstaff_departments')->row_array();
                    if (empty($dtCheckExitst)) {
                        $this->db->insert('tblstaff_departments', [
                            'departmentid' => $dtRoles['departments_id'],
                            'staffid' => $value['staff_id'],
                        ]);
                    }

                    $this->db->where('staffid', $value['staff_id']);
                    $success = $this->db->update('tblstaff', [
                        'role' => $role_id
                    ]);

                    //update_permisssion
                    $reset_can_permission = array(
                        'can_view' => 0,
                        'can_view_own' => 0,
                        'can_create' => 0,
                        'can_edit' => 0,
                        'can_print' => 0,
                        'can_approve' => 0,
                        'can_approve_warehouse' => 0,
                        'can_approve_accept' => 0,
                        'can_approve_qc' => 0,
                        'can_approve_cancel' => 0,
                        'can_import' => 0,
                        'can_export' => 0,
                        'can_delete' => 0,
                        'can_cost' => 0,
                        'can_profit' => 0,
                        'can_notifications' => 0,
                        'can_agree_notifications' => 0,
                        'can_add_notifications' => 0,
                        'can_qc' => 0,
                        'can_view_price' => 0,
                        'can_export_outsource' => 0,
                        'can_approve_manager' => 0,
                    );
                    $this->db->where('id_staff', $value['staff_id']);
                    $this->db->update('tbl_staff_child_permission_v2', $reset_can_permission);
                    $staff_parent = get_table_where('tbl_staff_parent_permission_v2', ['id_staff' => $value['staff_id']]);
                    if (!empty($staff_parent)) {
                        $arrUpdateBatch = [];
                        foreach ($staff_parent as $kk => $vv) {
                            $arrUpdateBatch[] = [
                                'id' => $vv['id'],
                                'can_view' => 0
                            ];
                        }
                        $this->db->update_batch('tbl_staff_parent_permission_v2', $arrUpdateBatch, 'id');
                    }
                    if (!empty($role_parent)) {
                        foreach ($role_parent as $kk => $vv) {
                            $checkExists_parent = get_table_where('tbl_parents_permissions_v2', array('obj' => $vv['obj_parent_permission']), '', 'row');
                            if (!$checkExists_parent) {
                                $this->db->insert('tbl_parents_permissions_v2', ['obj' => $vv['obj_parent_permission']]);
                                $insert_id_parent = $this->db->insert_id();
                            } else {
                                $insert_id_parent = $checkExists_parent->id;
                            }
                            //thêm quyền đc xem parent
                            $obj_parent_permission = get_table_where('tbl_parents_permissions_v2', array('id' => $insert_id_parent), '', 'row');
                            $check_can_view = get_table_where('tbl_staff_parent_permission_v2', array('id_staff' => $value['staff_id'], 'obj_parent_permission' => $obj_parent_permission->obj), '', 'row');
                            if (!$check_can_view) {
                                $this->db->insert('tbl_staff_parent_permission_v2', ['id_staff' => $value['staff_id'], 'obj_parent_permission' => $obj_parent_permission->obj, 'can_view' => 1]);
                            } else {
                                $this->db->set('can_view', 1);
                                $this->db->where('id', $check_can_view->id);
                                $this->db->update('tbl_staff_parent_permission_v2');
                            }
                            if (!empty($role_child)) {
                                foreach ($role_child as $key_child => $value_child) {
                                    //kiểm tra và thêm quyền parent
                                    $checkExists_child = get_table_where('tbl_permission_v2', array('obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $value_child['obj_permission']), '', 'row');
                                    if (!$checkExists_child) {
                                        $this->db->insert('tbl_permission_v2', ['obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $value_child['obj_permission']]);
                                        $insert_id_child = $this->db->insert_id();
                                    } else {
                                        $insert_id_child = $checkExists_child->id;
                                    }

                                    //phân quyền
                                    $obj_permission = get_table_where('tbl_permission_v2', array('id' => $insert_id_child), '', 'row');
                                    $checkExists_permission = get_table_where(
                                        'tbl_staff_child_permission_v2',
                                        array(
                                            'id_staff' => $value['staff_id'],
                                            'obj_permission' => $obj_permission->obj
                                        ),
                                        '',
                                        'row'
                                    );
                                    if (!$checkExists_permission) {
                                        $this->db->insert('tbl_staff_child_permission_v2', [
                                            'id_staff' => $value['staff_id'],
                                            'obj_permission' => $obj_permission->obj
                                        ]);
                                        $insert_id_permission = $this->db->insert_id();
                                    } else {
                                        $insert_id_permission = $checkExists_permission->id;
                                    }
                                    $arrUpdateBatch = [];
                                    foreach ($this->arrRoleChild  as $key_v => $value_v) {

                                        $colum = $value_v;
                                        if (!empty($value_child[$value_v])) {
                                            $valueColum = 1;
                                        } else {
                                            $valueColum = 0;
                                        }
                                        $arrUpdateBatch[] = [
                                            'id' => $insert_id_permission,
                                            $colum => $valueColum
                                        ];
                                    }
                                    $this->db->update_batch('tbl_staff_child_permission_v2', $arrUpdateBatch, 'id');
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($dtStaffRolOld)) {
                foreach ($dtStaffRolOld as $key => $value) {
                    $this->db->where('staffid', $value['staffid']);
                    $this->db->where('departmentid', $dtRoles['departments_id']);
                    $dtCheckExitst = $this->db->get('tblstaff_departments')->row_array();
                    if (!empty($dtCheckExitst)) {
                        $this->db->where('staffdepartmentid', $dtCheckExitst['staffdepartmentid']);
                        $this->db->delete('tblstaff_departments');
                    }

                    $this->db->where('staffid', $value['staffid']);
                    $success = $this->db->update('tblstaff', [
                        'role' => 0
                    ]);
                    $reset_can_permission = array(
                        'can_view' => 0,
                        'can_view_own' => 0,
                        'can_create' => 0,
                        'can_edit' => 0,
                        'can_print' => 0,
                        'can_approve' => 0,
                        'can_approve_warehouse' => 0,
                        'can_approve_accept' => 0,
                        'can_approve_qc' => 0,
                        'can_approve_cancel' => 0,
                        'can_import' => 0,
                        'can_export' => 0,
                        'can_delete' => 0,
                        'can_cost' => 0,
                        'can_profit' => 0,
                        'can_notifications' => 0,
                        'can_agree_notifications' => 0,
                        'can_add_notifications' => 0,
                        'can_qc' => 0,
                        'can_view_price' => 0,
                        'can_export_outsource' => 0,
                        'can_approve_manager' => 0,
                    );
                    $this->db->where('id_staff', $value['staffid']);
                    $this->db->update('tbl_staff_child_permission_v2', $reset_can_permission);
                    $staff_parent = get_table_where('tbl_staff_parent_permission_v2', ['id_staff' => $value['staffid']]);
                    if (!empty($staff_parent)) {
                        foreach ($staff_parent as $kk => $vv) {
                            $this->db->where('id', $vv['id']);
                            $this->db->update('tbl_staff_parent_permission_v2', [
                                'can_view' => 0
                            ]);
                        }
                    }
                }
            }
            if ($success) {
                $data['result'] = 1;
                $data['message'] = lang('Thành công!');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Thất bại!');
            }
            echo json_encode($data);
            die();
        }
        $data['title'] = lang('Chỉnh sửa nhân viên');
        $staff = get_table_where('tblstaff', ['active' => 1]);
        if (!empty($staff)) {
            foreach ($staff as $key => $value) {
                $url = base_url('assets/images/user-placeholder.jpg');
                if (!empty($value['profile_image']) && file_exists(('uploads/staff_profile_images/' . $value['staffid'] . '/small_' . $value['profile_image']))) {
                    $url = base_url('uploads/staff_profile_images/' . $value['staffid'] . '/' . 'small' . '_' . $value['profile_image']);
                }
                $staff[$key]['image'] = $url;
            }
        }
        $dtStaffRole = get_table_where('tblstaff', ['role' => $role_id]);
        if (!empty($dtStaffRole)) {
            foreach ($dtStaffRole as $key => $value) {
                $url = base_url('assets/images/user-placeholder.jpg');
                if (!empty($value['profile_image']) && file_exists(('uploads/staff_profile_images/' . $value['staffid'] . '/small_' . $value['profile_image']))) {
                    $url = base_url('uploads/staff_profile_images/' . $value['staffid'] . '/' . 'small' . '_' . $value['profile_image']);
                }
                $dtStaffRole[$key]['image'] = $url;
            }
        }
        $data['dtStaffRole'] = $dtStaffRole;
        $data['staff'] = $staff;
        $data['id'] = $role_id;
        $this->load->view('admin/roles/update_staff', $data);
    }

    public function modal_excel_import_permission()
    {
        $data['title'] = _l('Import chức vụ phân quyền');
        $data['data_roles'] = $this->db->get_where('tblroles', ['roles_parent' => 0, 'type' => 0])->result_array();
        $this->load->view('admin/roles/modal_excel_import_permission', $data);
    }

    public function export_excel_permission_bk($id_roles)
    {
        if (!has_permission('roles', '', 'export')) {
            access_denied();
        }
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $cloumns = $this->input->post('cloumns');
        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
        $permissions = get_available_staff_permissions();

        $numberRow = 1;
        $j = 0;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'STT')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Mã chức vụ')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Tên chức vụ')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Menu')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Phân quyền')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $numberRow++;
        $_data = $this->db->get_where('tblroles', ['roleid' => $id_roles])->result_array();
        foreach ($_data as $key => $value) {
            $stt = (string)($key + 1);
            $i = 0;
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("$cloumns_excel[$i]$numberRow", $stt, PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code_role']);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
            $i++;
            foreach ($permissions as $ks => $vs) {
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vs['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                $numberRow++;
                foreach ($vs['child'] as $key_child => $value_child) {
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '>>>' . $value_child['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                    $numberRow++;
                    foreach ($value_child['permissions'] as $key_permissions => $value_permissions) {
                        $can_permission = 'can_' . $key_permissions;
                        $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $value['roleid'], 'obj_permission' => $key_child, $can_permission => 1), '', 'row');
                        if ($checkTrue_permission) {
                            $dem = $i + 1;
                            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$dem]$numberRow", $value_permissions)->getStyle("$cloumns_excel[$dem]$numberRow")->getAlignment()->setWrapText(true);
                            $numberRow++;
                        }
                    }
                }
            }
            $_data_child = $this->db->get_where('tblroles', ['roles_parent' => $value['roleid'], 'type' => 0])->result_array();
            $sttmain = $stt;
            foreach ($_data_child as $k => $v) {
                $stt = $sttmain . '.' . (string)($k + 1);
                $i = 0;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit("$cloumns_excel[$i]$numberRow", $stt, PHPExcel_Cell_DataType::TYPE_STRING);
                $i++;
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $v['code_role']);
                $i++;
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $v['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                $i++;
                foreach ($permissions as $ks => $vs) {
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vs['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                    $numberRow++;
                    foreach ($vs['child'] as $key_child => $value_child) {
                        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '>>>' . $value_child['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                        $numberRow++;
                        foreach ($value_child['permissions'] as $key_permissions => $value_permissions) {
                            $can_permission = 'can_' . $key_permissions;
                            $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $v['roleid'], 'obj_permission' => $key_child, $can_permission => 1), '', 'row');
                            if ($checkTrue_permission) {
                                $dem = $i + 1;
                                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$dem]$numberRow", $value_permissions)->getStyle("$cloumns_excel[$dem]$numberRow")->getAlignment()->setWrapText(true);
                                $numberRow++;
                            }
                        }
                    }
                }
                $_data_child_v2 = $this->db->get_where('tblroles', ['roles_parent' => $v['roleid'], 'type' => 0])->result_array();
                $sttmain_2 = $stt;
                foreach ($_data_child_v2 as $kk => $vv) {
                    $stt = $sttmain_2 . '.' . (string)($kk + 1);
                    $i = 0;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("$cloumns_excel[$i]$numberRow", $stt, PHPExcel_Cell_DataType::TYPE_STRING);
                    $i++;
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vv['code_role']);
                    $i++;
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vv['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                    $i++;
                    foreach ($permissions as $ks => $vs) {
                        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vs['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                        $numberRow++;
                        foreach ($vs['child'] as $key_child => $value_child) {
                            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '>>>' . $value_child['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                            $numberRow++;
                            foreach ($value_child['permissions'] as $key_permissions => $value_permissions) {
                                $can_permission = 'can_' . $key_permissions;
                                $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $vv['roleid'], 'obj_permission' => $key_child, $can_permission => 1), '', 'row');
                                if ($checkTrue_permission) {
                                    $dem = $i + 1;
                                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$dem]$numberRow", $value_permissions)->getStyle("$cloumns_excel[$dem]$numberRow")->getAlignment()->setWrapText(true);
                                    $numberRow++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $objPHPExcel->getActiveSheet()->getStyle('A1:E' . $numberRow)->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $filename = lang('Phan_quyen_chuc_vu_') . $_data[0]['name'] . '.xlsx';
        ob_start();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'result' => 1,
            'filename' => $filename,
            'message' => lang('success'),
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }



    public function export_excel_permission2($id_roles)
    {
        if (!has_permission('roles', '', 'export')) {
            access_denied();
        }
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $cloumns = $this->input->post('cloumns');
        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
        $permissions = get_available_staff_permissions();

        $numberRow = 1;
        $j = 0;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'STT')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Mã chức vụ')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Tên chức vụ')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Menu')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$j]$numberRow", 'Phân quyền')->getStyle("$cloumns_excel[$j]$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $j++;
        $numberRow++;
        $_data = $this->db->get_where('tblroles', ['roleid' => $id_roles])->result_array();
        foreach ($_data as $key => $value) {
            $stt = (string)($key + 1);
            $i = 0;
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("$cloumns_excel[$i]$numberRow", $stt, PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code_role']);
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
            $i++;
            foreach ($permissions as $ks => $vs) {
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vs['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                $numberRow++;
                foreach ($vs['child'] as $key_child => $value_child) {
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '>>>' . $value_child['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                    $numberRow++;
                    foreach ($value_child['permissions'] as $key_permissions => $value_permissions) {
                        $can_permission = 'can_' . $key_permissions;
                        $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $value['roleid'], 'obj_permission' => $key_child, $can_permission => 1), '', 'row');
                        if ($checkTrue_permission) {
                            $dem = $i + 1;
                            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$dem]$numberRow", $value_permissions)->getStyle("$cloumns_excel[$dem]$numberRow")->getAlignment()->setWrapText(true);
                            $numberRow++;
                        }
                    }
                }
            }
            $_data_child = $this->db->get_where('tblroles', ['roles_parent' => $value['roleid'], 'type' => 0])->result_array();
            $sttmain = $stt;
            foreach ($_data_child as $k => $v) {
                $stt = $sttmain . '.' . (string)($k + 1);
                $i = 0;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit("$cloumns_excel[$i]$numberRow", $stt, PHPExcel_Cell_DataType::TYPE_STRING);
                $i++;
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $v['code_role']);
                $i++;
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $v['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                $i++;
                foreach ($permissions as $ks => $vs) {
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vs['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                    $numberRow++;
                    foreach ($vs['child'] as $key_child => $value_child) {
                        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '>>>' . $value_child['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                        $numberRow++;
                        foreach ($value_child['permissions'] as $key_permissions => $value_permissions) {
                            $can_permission = 'can_' . $key_permissions;
                            $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $v['roleid'], 'obj_permission' => $key_child, $can_permission => 1), '', 'row');
                            if ($checkTrue_permission) {
                                $dem = $i + 1;
                                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$dem]$numberRow", $value_permissions)->getStyle("$cloumns_excel[$dem]$numberRow")->getAlignment()->setWrapText(true);
                                $numberRow++;
                            }
                        }
                    }
                }
                $_data_child_v2 = $this->db->get_where('tblroles', ['roles_parent' => $v['roleid'], 'type' => 0])->result_array();
                $sttmain_2 = $stt;
                foreach ($_data_child_v2 as $kk => $vv) {
                    $stt = $sttmain_2 . '.' . (string)($kk + 1);
                    $i = 0;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("$cloumns_excel[$i]$numberRow", $stt, PHPExcel_Cell_DataType::TYPE_STRING);
                    $i++;
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vv['code_role']);
                    $i++;
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vv['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                    $i++;
                    foreach ($permissions as $ks => $vs) {
                        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $vs['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                        $numberRow++;
                        foreach ($vs['child'] as $key_child => $value_child) {
                            $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '>>>' . $value_child['name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true);
                            $numberRow++;
                            foreach ($value_child['permissions'] as $key_permissions => $value_permissions) {
                                $can_permission = 'can_' . $key_permissions;
                                $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $vv['roleid'], 'obj_permission' => $key_child, $can_permission => 1), '', 'row');
                                if ($checkTrue_permission) {
                                    $dem = $i + 1;
                                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$dem]$numberRow", $value_permissions)->getStyle("$cloumns_excel[$dem]$numberRow")->getAlignment()->setWrapText(true);
                                    $numberRow++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $objPHPExcel->getActiveSheet()->getStyle('A1:E' . $numberRow)->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $filename = lang('Phan_quyen_chuc_vu_') . $_data[0]['name'] . '.xlsx';
        ob_start();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'result' => 1,
            'filename' => $filename,
            'message' => lang('success'),
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }



    public function export_excel_permission($id_roles = 0)
    {
        if (!has_permission('roles', '', 'export')) {
            access_denied();
        }
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $cloumns_excel = cloumns_excel();
        $style_excel = style_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

        $objPHPExcel = new PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet();

        // 1. Định nghĩa các cột quyền hàng ngang
        $permission_columns = [
            'view'     => 'Xem chung',
            'view_own' => 'Xem riêng',
            'create'   => 'Thêm',
            'edit'     => 'Sửa',
            'delete'   => 'Xóa',
            'import'   => 'Import',
            'export'   => 'Export'
        ];

        $headers = ['STT', 'Mã phòng ban', 'Tên phòng ban', 'Mã chức vụ', 'Tên chức vụ', 'Module', 'Function'];

        // Vẽ Header
        foreach ($headers as $i => $text) {
            $sheet->SetCellValue($cloumns_excel[$i] . '1', $text);
            $sheet->getStyle($cloumns_excel[$i] . '1')->applyFromArray($style_excel['Background_header_one']);
        }

        $start_perm_col = count($headers);
        $j = $start_perm_col;
        foreach ($permission_columns as $p_label) {
            $sheet->SetCellValue($cloumns_excel[$j] . '1', $p_label);
            $sheet->getStyle($cloumns_excel[$j] . '1')->applyFromArray($style_excel['Background_header_one']);
            $sheet->getColumnDimension($cloumns_excel[$j])->setWidth(12);
            $j++;
        }

        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(15);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(25);

        $permissions = get_available_staff_permissions();
        $numberRow = 2;

        // --- SỬA LOGIC LẤY DỮ LIỆU GỐC ---
        $this->db->select('tblroles.*, tbl_room.code as dept_code, tbl_room.name as dept_name');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        if (!empty($id_roles) && $id_roles != 0) {
            $this->db->where('roleid', $id_roles);
        } else {
            $this->db->where('roles_parent', 0);
        }
        $roleAll = $this->db->get_where('tblroles', ['type' => 0])->result_array();
        $stt = 1;
        foreach ($roleAll as $jKey => $main_role) {
            $all_roles = [];
            // Gọi đệ quy: Sử dụng chính ID của role hiện tại và STT là index + 1
            $this->get_roles_recursive($main_role['roleid'], $all_roles, (string)($jKey + 1), $main_role);

            foreach ($all_roles as $role) {
                $dept_code = !empty($role['dept_code']) ? $role['dept_code'] : '-';
                $dept_name = !empty($role['dept_name']) ? $role['dept_name'] : '-';

                foreach ($permissions as $vs) {
                    foreach ($vs['child'] as $key_child => $value_child) {

                        // Điền thông tin cơ bản (Lặp lại từng dòng)
                        $sheet->setCellValueExplicit("A$numberRow", $stt, PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->SetCellValue("B$numberRow", $dept_code);
                        $sheet->SetCellValue("C$numberRow", $dept_name);
                        $sheet->SetCellValue("D$numberRow", $role['code_role']);
                        $sheet->SetCellValue("E$numberRow", $role['name']);
                        $sheet->SetCellValue("F$numberRow", $vs['name']);
                        $sheet->SetCellValue("G$numberRow", $value_child['name']);

                        // Điền Y/N cho các cột quyền
                        $col_p = $start_perm_col;
                        foreach ($permission_columns as $p_key => $p_label) {
                            $cell_coord = $cloumns_excel[$col_p] . $numberRow;

                            if (isset($value_child['permissions'][$p_key])) {
                                $can_field = 'can_' . $p_key;
                                $check = get_table_where('tbl_roles_child_permission_v2', [
                                    'id_role' => $role['roleid'],
                                    'obj_permission' => $key_child,
                                    $can_field => 1
                                ], '', 'row');

                                $val = ($check) ? 'Y' : 'N';
                                $sheet->SetCellValue($cell_coord, $val);
                                $color = ($val == 'Y') ? '008000' : 'FF0000';
                                $sheet->getStyle($cell_coord)->getFont()->getColor()->setRGB($color);
                            } else {
                                $sheet->SetCellValue($cell_coord, 'N');
                                $sheet->getStyle($cell_coord)->getFont()->getColor()->setRGB('FF0000');
                            }
                            $sheet->getStyle($cell_coord)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $col_p++;
                        }
                        $numberRow++;
                        $stt++;
                    }
                }
            }
        }

        // Border toàn bộ
        $last_col = $cloumns_excel[$start_perm_col + count($permission_columns) - 1];
        $sheet->getStyle('A1:' . $last_col . ($numberRow - 1))->applyFromArray([
            'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
            'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        ]);

        // Xuất file
        $filename = lang('Bao_cao_phan_quyen_') . date('Ymd_His') . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        die(json_encode([
            'result' => 1,
            'filename' => $filename,
            'message' => lang('success'),
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        ]));
    }

    private function get_roles_recursive($parent_id, &$result, $stt_prefix, $current_role)
    {
        $current_role['stt'] = $stt_prefix;
        $result[] = $current_role;

        $this->db->select('tblroles.*, tbl_room.code as dept_code, tbl_room.name as dept_name');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $children = $this->db->get_where('tblroles', ['roles_parent' => $parent_id, 'type' => 0])->result_array();

        foreach ($children as $key => $child) {
            $this->get_roles_recursive($child['roleid'], $result, $stt_prefix . '.' . ($key + 1), $child);
        }
    }



    public function modal_import_permission()
    {
        $data['title'] = _l('Import Phân Quyền File Excel');
        $this->load->view('admin/roles/excel_import_permission', $data);
    }



    private function convert_to_key($str)
    {
        if (!$str) return '';
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '_', $str);
        return trim($str, '_');
    }



    public function excel_import_permission()
    {
        ob_end_clean();
        ini_set('memory_limit', '3500M');
        ini_set('max_execution_time', 1200);

        if (!has_permission('roles', '', 'import')) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện chức năng này.']);
            die();
        }

        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');

        if (!empty($_FILES['file']['tmp_name'])) {
            $fullfile  = $_FILES['file']['tmp_name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'message' => 'Định dạng file không hỗ trợ.']);
                die();
            }

            try {
                $objPHPExcel = PHPExcel_IOFactory::load($fullfile);
                $sheet       = $objPHPExcel->getActiveSheet();
                $highestRow  = $sheet->getHighestRow();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi đọc file: ' . $e->getMessage()]);
                die();
            }

            // --- BƯỚC 1: MAPPING HỆ THỐNG ---
            $all_permissions = get_available_staff_permissions();
            $map_permission_key = [];
            foreach ($all_permissions as $group) {
                foreach ($group['child'] as $key_system => $child) {
                    $normalized_name = $this->convert_to_key($child['name']);
                    $map_permission_key[$normalized_name] = $key_system;
                }
            }

            $perm_cols = [
                'view' => 7,
                'view_own' => 8,
                'create' => 9,
                'edit' => 10,
                'delete' => 11,
                'import' => 12,
                'export' => 13
            ];

            $count_success = 0;
            $row_errors = [];
            $list_update = []; // Lưu lại để dùng cho append_staff
            $list_role_ids = [];

            // --- BƯỚC 2: XỬ LÝ QUYỀN TRÊN CHỨC VỤ (ROLE) ---
            for ($row = 2; $row <= $highestRow; $row++) {
                $role_code = trim($sheet->getCellByColumnAndRow(3, $row)->getValue());
                $raw_func  = trim($sheet->getCellByColumnAndRow(6, $row)->getValue());

                if (empty($role_code) || empty($raw_func)) continue;

                $role = $this->db->get_where('tblroles', ['code_role' => $role_code])->row();
                if (!$role) {
                    $row_errors[] = "Dòng $row: Mã chức vụ '$role_code' không tồn tại.";
                    continue;
                }

                $excel_func_key = $this->convert_to_key($raw_func);
                $obj_permission = isset($map_permission_key[$excel_func_key]) ? $map_permission_key[$excel_func_key] : '';

                if (empty($obj_permission)) {
                    $row_errors[] = "Dòng $row: Không tìm thấy quyền tương ứng cho '$raw_func'.";
                    continue;
                }

                $data_update = [
                    'id_role'        => $role->roleid,
                    'obj_permission' => $obj_permission
                ];

                foreach ($perm_cols as $p_key => $col_index) {
                    $val = strtoupper(trim($sheet->getCellByColumnAndRow($col_index, $row)->getValue()));
                    $data_update['can_' . $p_key] = ($val == 'Y') ? 1 : 0;
                }

                // Update/Insert quyền Role
                $this->db->where(['id_role' => $role->roleid, 'obj_permission' => $obj_permission]);
                $check = $this->db->get('tbl_roles_child_permission_v2')->row();

                if ($check) {
                    $this->db->where('id', $check->id)->update('tbl_roles_child_permission_v2', $data_update);
                } else {
                    $this->db->insert('tbl_roles_child_permission_v2', $data_update);
                }

                // Lưu vào danh sách để xử lý nhân viên ở bước sau
                $list_update[$role->roleid][] = $data_update;
                $list_role_ids[] = $role->roleid;
                $count_success++;
            }

            // --- BƯỚC 3: CẬP NHẬT CHO NHÂN VIÊN (APPEND STAFF) ---
            $append_staff = $this->input->post('append_staff');
            if (!empty($append_staff) && !empty($list_role_ids)) {
                $list_role_ids = array_unique($list_role_ids);

                // Lấy danh sách nhân viên thuộc các chức vụ vừa update
                $list_staff = $this->db->where_in('role', $list_role_ids)->get('tblstaff')->result_array();

                $roleStaff = [];
                foreach ($list_staff as $staff) {
                    $roleStaff[$staff['role']][] = $staff['staffid'];
                }

                // Duyệt từng Role
                foreach ($list_update as $roleid => $permissions) {
                    if (empty($roleStaff[$roleid])) continue;

                    $staff_ids = $roleStaff[$roleid];

                    // Duyệt từng quyền (Module/Function) được import từ Excel
                    foreach ($permissions as $perm_row) {
                        $obj_p = $perm_row['obj_permission'];

                        // Chuẩn bị data (Bỏ id_role vì bảng nhân viên dùng id_staff)
                        $staff_perm_data = $perm_row;
                        unset($staff_perm_data['id_role']);

                        // QUAN TRỌNG: Duyệt qua từng nhân viên để kiểm tra và xử lý riêng biệt
                        foreach ($staff_ids as $staff_id) {
                            $this->db->where([
                                'obj_permission' => $obj_p,
                                'id_staff'       => $staff_id
                            ]);
                            $check_staff_perm = $this->db->get('tbl_staff_child_permission_v2')->row();

                            if ($check_staff_perm) {
                                // Nếu đã có dòng quyền này cho nhân viên -> Update
                                $this->db->where('id', $check_staff_perm->id);
                                $this->db->update('tbl_staff_child_permission_v2', $staff_perm_data);
                            } else {
                                // Nếu chưa có -> Insert mới
                                $staff_perm_data['id_staff'] = $staff_id;
                                $this->db->insert('tbl_staff_child_permission_v2', $staff_perm_data);
                            }
                        }
                    }
                }
            }

            echo json_encode([
                'success'    => true,
                'alert_type' => 'success',
                'message'    => "Đã xử lý xong $count_success dòng (Bao gồm đồng bộ nhân viên).",
                'errors'     => !empty($row_errors) ? implode('<br>', $row_errors) : ''
            ]);
            die();
        }
    }
}
