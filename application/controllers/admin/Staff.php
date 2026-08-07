<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->token_zalo = get_option('token_zalo');
    }

    public function api_profile($user_id = '')
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://openapi.zalo.me/v2.0/oa/getprofile?data={user_id:' . $user_id . '}&access_token=' . $this->token_zalo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

    public function api()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://openapi.zalo.me/v2.0/oa/getfollowers?data={offset:0,count:50}&access_token=" . $this->token_zalo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function set_id_zalo()
    {
        $data = $this->input->post();
        if (($data['id_zalo'] == 0) || ($data['id_zalo'] == '')) {
            $data['id_zalo'] = '';
        } else {
            $ktr = get_table_where('tblstaff', array('id_zalo' => $data['id_zalo']), '', 'row');
            if (!empty($ktr)) {
                echo json_encode(array(
                    'success' => 0,
                    'message' => _l('ch_exist_staff') . get_staff_full_name($ktr->staffid),
                    'alert_type' => 'warning'
                ));
                die;
            }
        }
        $this->db->set('id_zalo', $data['id_zalo']);
        $this->db->where('staffid', $data['id_staff']);
        $this->db->update('tblstaff');
        echo json_encode(array(
            'success' => 1,
            'message' => _l('add_slide_true'),
            'alert_type' => 'success'
        ));
        die;
    }

    public function set_id_branch()
    {
        $data = $this->input->post();
        $this->db->set('branch_salary', $data['id_branch']);
        $this->db->where('staffid', $data['id_staff']);
        $this->db->update('tblstaff');
        echo json_encode(array(
            'success' => 1,
            'message' => _l('add_slide_true'),
            'alert_type' => 'success'
        ));
        die;
    }

    /* List all staff members */
    public function index()
    {
        if (!has_permission('staff', '', 'view')) {
            //			access_denied('staff');
        }
        if ($this->input->is_ajax_request()) {
            $array = array();
            if (!empty($this->api()->data)) {
                $followers = $this->api()->data->followers;
                foreach ($followers as $key => $value) {
                    $profile = $this->api_profile($value->user_id);
                    $array[$key]['userid'] = $value->user_id;
                    $array[$key]['avatar'] = $profile->data->avatar;
                    $array[$key]['name'] = $profile->data->display_name;
                }
            }
            $this->app->get_table_data('staff', array('array' => $array));
            //            $this->app->get_table_data('staff');
        }
        $this->db->select('staffid, CONCAT(COALESCE(firstname, "")," ",COALESCE(lastname, "")) as fullname');
        $data['list_staff'] = $this->db->get_where('tblstaff')->result_array();
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['departments'] = $this->db->get('tbldepartments')->result_array();
        $data['roles'] = $this->db->get('tblroles')->result_array();
        $data['roles'] = [];
        $this->get_parent(0, $data['roles']);

        $this->db->from('tblstaff');
        $all = $this->db->count_all_results();

        $this->db->from('tblstaff');
        $this->db->where('status_work', 0);
        $status_work0 = $this->db->count_all_results();

        $this->db->from('tblstaff');
        $this->db->where('status_work', 1);
        $status_work1 = $this->db->count_all_results();

        $this->db->from('tblstaff');
        $this->db->where('status_work', 2);
        $status_work2 = $this->db->count_all_results();

        $data['all'] = $all;
        $data['status_work0'] = $status_work0;
        $data['status_work1'] = $status_work1;
        $data['status_work2'] = $status_work2;
        $data['title'] = _l('staff_members');
        $this->load->view('admin/staff/manage', $data);
    }

    /* Add new staff member or edit existing */
    public function member($id = '')
    {
        if (!has_permission('staff', '', 'view')) {
            //			access_denied('staff');
        }
        hooks()->do_action('staff_member_edit_view_profile', $id);
        $this->load->model('departments_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['birthday'] = to_sql_date($data['birthday']);
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);
            $data['password'] = $this->input->post('password', false);
            $data['salary_bhxh'] = number_unformat($this->input->post('salary_bhxh'));
            $data['salary_bhxh_new'] = number_unformat($this->input->post('salary_bhxh_new'));
            $data['allowance'] = number_unformat($this->input->post('allowance'));
            $data['coefficient_position'] = number_unformat($this->input->post('coefficient_position'));
            $data['coefficient_responsibility'] = number_unformat($this->input->post('coefficient_responsibility'));
            $data['check_bhxh'] = $this->input->post('check_bhxh') ? $this->input->post('check_bhxh') : 0;
            $data['check_union'] = $this->input->post('check_union') ? $this->input->post('check_union') : 0;
            $data['day_in'] = $this->input->post('day_in') ? to_sql_date($this->input->post('day_in')) : null;
            $data['date_range'] = !empty($data['date_range']) ? to_sql_date($data['date_range']) : NULL;
            $data['status_work'] = $data['status_work'];
            $data['total_advance'] = number_unformat($data['total_advance']);
            $data['sales'] = number_unformat($data['sales']);
            $data['phone'] = number_unformat($data['phone']);
            $data['gasonline_cars'] = number_unformat($data['gasonline_cars']);
            $data['motel'] = number_unformat($data['motel']);
            $data['concurrently'] = number_unformat($data['concurrently']);
            $data['business_fee_staff'] = number_unformat($data['business_fee_staff']);
            $data['seniority'] = number_unformat($data['seniority']);
            $arrFamily = [];
            if (!empty($data['counterFamily'])) {
                foreach ($data['counterFamily'] as $key => $value) {
                    $staff_family_id = !empty($this->input->post('staff_family_id')[$value]) ? $this->input->post('staff_family_id')[$value] : 0;
                    $relationship_family = $this->input->post('relationship_family')[$value];
                    $fullname_family = $this->input->post('fullname_family')[$value];
                    $year_birthday_family = $this->input->post('year_birthday_family')[$value];
                    $career_family = $this->input->post('career_family')[$value];
                    $address_family = $this->input->post('address_family')[$value];
                    $telephone_family = $this->input->post('telephone_family')[$value];
                    if (empty($relationship_family) || empty($fullname_family) || empty($telephone_family)) {
                        continue;
                    }
                    $arrFamily[] = [
                        'id' => $staff_family_id,
                        'relationship_family' => $relationship_family,
                        'fullname_family' => $fullname_family,
                        'year_birthday_family' => $year_birthday_family,
                        'career_family' => $career_family,
                        'address_family' => $address_family,
                        'telephone_family' => $telephone_family,
                    ];
                }
            }
            $data['arrFamily'] = $arrFamily;
            //literacy
            $counterLiteracy = $this->input->post('counterLiteracy');
            $arrLiteracy = [];
            $countLiteracy = 0;
            if (!empty($counterLiteracy)) {
                foreach ($counterLiteracy as $key => $value) {
                    $from_date_literacy = !empty($this->input->post('from_date_literacy')[$value]) ? to_sql_date($this->input->post('from_date_literacy')[$value]) : NULL;
                    $to_date_literacy = !empty($this->input->post('to_date_literacy')[$value]) ? to_sql_date($this->input->post('to_date_literacy')[$value]) : NULL;
                    $literacy = $this->input->post('literacy')[$value];
                    $training_places_literacy = $this->input->post('training_places_literacy')[$value];
                    $specialized_literacy = $this->input->post('specialized_literacy')[$value];
                    $classification_literacy = $this->input->post('classification_literacy')[$value];
                    $staff_literacy_id = !empty($this->input->post('staff_literacy_id')[$value]) ? $this->input->post('staff_literacy_id')[$value] : 0;
                    if (empty($from_date_literacy) || empty($to_date_literacy) || empty($literacy) || empty($training_places_literacy) || empty($specialized_literacy) || empty($classification_literacy)) {
                        continue;
                    }
                    $arrLiteracy[] = [
                        'id' => $staff_literacy_id,
                        'from_date_literacy' => $from_date_literacy,
                        'to_date_literacy' => $to_date_literacy,
                        'literacy' => $literacy,
                        'training_places_literacy' => $training_places_literacy,
                        'specialized_literacy' => $specialized_literacy,
                        'classification_literacy' => $classification_literacy,
                    ];
                    $countLiteracy++;
                }
            }
            $data['arrLiteracy'] = $arrLiteracy;
            //salary
            $counterSalary = $this->input->post('counterSalaryNew');
            $arrSalary = [];
            $countSalary = 0;
            if (!empty($counterSalary)) {
                foreach ($counterSalary as $key => $value) {
                    $month = !empty($this->input->post('month')[$value]) ? ($this->input->post('month')[$value]) : NULL;
                    $year = !empty($this->input->post('year')[$value]) ? ($this->input->post('year')[$value]) : NULL;
                    $salary = number_unformat($this->input->post('salary')[$value]);
                    $active = !empty($this->input->post('active_new')[$value]) ? 1 : 0;
                    $id_salary_staff = !empty($this->input->post('id_salary_staff')[$value]) ? $this->input->post('id_salary_staff')[$value] : 0;
                    if (empty($month) || empty($year) || empty($salary)) {
                        continue;
                    }
                    $arrSalary[] = [
                        'id' => $id_salary_staff,
                        'month' => $month,
                        'year' => $year,
                        'salary' => $salary,
                        'active' => $active,
                    ];
                    $countSalary++;
                }
            }
            $data['arrSalary'] = $arrSalary;

            $countPhuCap = !empty($this->input->post('counterAllowance')) ? $this->input->post('counterAllowance') : [];
            $arrPc = [];
            if (!empty($countPhuCap)) {
                foreach ($countPhuCap as $key => $value) {
                    $title = $this->input->post('title')[$value];
                    $staff_id_pc = $this->input->post('staff_id_pc')[$value];
                    $id_pc = !empty($this->input->post('id_allowance_staff')[$value]) ? $this->input->post('id_allowance_staff')[$value] : 0;
                    $amount = number_unformat($this->input->post('amount')[$value]);
                    if (empty($title)) {
                        continue;
                    }
                    $arrPc[] = [
                        'id' => !empty($id_pc) ? $id_pc : 0,
                        'category_id' => $title,
                        'amount' => $amount,
                        'date_created' => date('Y-m-d H:i:s'),
                        'staff_id' => get_staff_user_id(),
                    ];
                }
            }
            $data['arrPc'] = $arrPc;

            $countGiamTru = !empty($this->input->post('countGiamTru')) ? $this->input->post('countGiamTru') : [];
            $arrGt = [];
            if (!empty($countGiamTru)) {
                foreach ($countGiamTru as $key => $value) {
                    $title = $this->input->post('title_gt')[$value];
                    $staff_id_gt = $this->input->post('staff_id_gt')[$value];
                    $id_gt = !empty($this->input->post('id_reduce_staff')[$value]) ? $this->input->post('id_reduce_staff')[$value] : 0;
                    $amount = number_unformat($this->input->post('amount_gt')[$value]);
                    if (empty($title)) {
                        continue;
                    }
                    $arrGt[] = [
                        'id' => !empty($id_gt) ? $id_gt : 0,
                        'category_id' => $title,
                        'amount' => $amount,
                        'date_created' => date('Y-m-d H:i:s'),
                        'staff_id' => get_staff_user_id(),
                    ];
                }
            }
            $data['arrGt'] = $arrGt;

            if (isset($data['id_allowance_staff'])) unset($data['id_allowance_staff']);
            if (isset($data['title'])) unset($data['title']);
            if (isset($data['counterAllowance'])) unset($data['counterAllowance']);
            if (isset($data['staff_id_pc'])) unset($data['staff_id_pc']);
            if (isset($data['amount'])) unset($data['amount']);
            if (isset($data['id_reduce_staff'])) unset($data['id_reduce_staff']);
            if (isset($data['title_gt'])) unset($data['title_gt']);
            if (isset($data['countGiamTru'])) unset($data['countGiamTru']);
            if (isset($data['staff_id_gt'])) unset($data['staff_id_gt']);
            if (isset($data['amount_gt'])) unset($data['amount_gt']);
            if (isset($data['id_salary_staff'])) unset($data['id_salary_staff']);
            if (isset($data['counterSalaryNew'])) unset($data['counterSalaryNew']);
            if (isset($data['salary'])) unset($data['salary']);
            if (isset($data['month'])) unset($data['month']);
            if (isset($data['year'])) unset($data['year']);
            if (isset($data['active_new'])) unset($data['active_new']);
            if (isset($data['counterFamily'])) unset($data['counterFamily']);
            if (isset($data['staff_family_id'])) unset($data['staff_family_id']);
            if (isset($data['relationship_family'])) unset($data['relationship_family']);
            if (isset($data['fullname_family'])) unset($data['fullname_family']);
            if (isset($data['year_birthday_family'])) unset($data['year_birthday_family']);
            if (isset($data['career_family'])) unset($data['career_family']);
            if (isset($data['address_family'])) unset($data['address_family']);
            if (isset($data['telephone_family'])) unset($data['telephone_family']);
            if (isset($data['staff_literacy_id'])) unset($data['staff_literacy_id']);
            if (isset($data['from_date_literacy'])) unset($data['from_date_literacy']);
            if (isset($data['counterLiteracy'])) unset($data['counterLiteracy']);
            if (isset($data['to_date_literacy'])) unset($data['to_date_literacy']);
            if (isset($data['literacy'])) unset($data['literacy']);
            if (isset($data['training_places_literacy'])) unset($data['training_places_literacy']);
            if (isset($data['specialized_literacy'])) unset($data['specialized_literacy']);
            if (isset($data['classification_literacy'])) unset($data['classification_literacy']);
            if (isset($data['DataTables_Table_1_length'])) unset($data['DataTables_Table_1_length']);
            if (isset($data['DataTables_Table_2_length'])) unset($data['DataTables_Table_2_length']);
            if (isset($data['DataTables_Table_0_length'])) unset($data['DataTables_Table_0_length']);
            if (isset($data['staff_id'])) unset($data['staff_id']);
            if ($id == '') {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                if ($data['status_work'] == 2) {
                    $data['date_status_work'] = date('Y-m-d');
                }
                $data['date_dochai'] = !empty($data['date_dochai']) ? to_sql_date($data['date_dochai']) : null;
                // print_arrays($this->input->post());
                $id = $this->staff_model->add($data);
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    //add employee
                    $employee_manage = $this->input->post('employee_manage');
                    if (!empty($employee_manage)) {
                        $fields = [];
                        foreach ($employee_manage as $key => $value) {
                            $fields[] = [
                                'staff_id' => $id,
                                'employee_id' => $value,
                            ];
                        }
                        if (!empty($fields)) {
                            $this->site_model->insertBatchEmployeeManageStaff($fields);
                        }
                    }
                    //
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    redirect(admin_url('staff/member/' . $id));
                }
            } else {
                if (!has_permission('staff', '', 'edit')) {
                    access_denied('staff');
                }
                $dtStaff = get_table_where('tblstaff', ['staffid' => $id], '', 'row_array');
                if ($dtStaff['status_work'] != 2 && $data['status_work'] == 2) {
                    $data['date_status_work'] = date('Y-m-d');
                }
                handle_staff_profile_image_upload($id);
                $data['date_dochai'] = !empty($data['date_dochai']) ? to_sql_date($data['date_dochai']) : null;
                $response = $this->staff_model->update($data, $id);
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                redirect(admin_url('staff/member/' . $id));
            }
        }
        if ($id == '') {
            //new
            $staff = get_table_where('tblstaff');
            if (count($staff) >= 10006) {
                set_alert('warning', _l('Vượt quá giới hạn user cho phép'));
                redirect(admin_url('staff'));
            }
            $data['employees_v2'] = array();
            $title = _l('add_new', _l('staff_member_lowercase'));
        } else {
            //new
            $getIDRole = get_table_where('tblstaff', array('staffid' => $id), '', 'row');
            $getStaff = get_table_where('tblstaff', array('staffid' => $id), '', 'row_array');
            $arrID_child = array();
            $arrStaff = array();
            if ($getIDRole->role != 0) {
                $this->get_childs_id($getIDRole->role, $arrID_child);
                if ($arrID_child != array()) {
                    $this->db->select('tblstaff.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name');
                    $this->db->where_in('tblstaff.role', $arrID_child);
                    $this->db->where('tblstaff.active', 1);
                    $resultStaff = $this->db->get('tblstaff')->result_array();
                    if ($resultStaff) {
                        $arrStaff = $resultStaff;
                    }
                }
            }
            $data['employees_v2'] = $arrStaff;
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member'] = $member;
            $title = $member->firstname . ' ' . $member->lastname;
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
            $data['employee_manage_staff'] = $this->site_model->getEmployeeManageStaffByStaffId($member->staffid);
            $ts_filter_data = [];
            if ($this->input->get('filter')) {
                if ($this->input->get('range') != 'period') {
                    $ts_filter_data[$this->input->get('range')] = true;
                } else {
                    $ts_filter_data['period-from'] = $this->input->get('period-from');
                    $ts_filter_data['period-to'] = $this->input->get('period-to');
                }
            } else {
                $ts_filter_data['this_month'] = true;
            }
            //hoàng cem bổ xung
            //            $role = get_table_where('tblstaff', array('staffid' => $id), '', 'row');
            //kiểm tra parent
            $check_permission_parent = get_table_where('tbl_staff_parent_permission_v2', array('id_staff' => $id, 'can_view' => 1));
            $arr_id_parent_permission = array();
            foreach ($check_permission_parent as $key => $value) {
                $arr_id_parent_permission[] = get_table_where('tbl_parents_permissions_v2', array('obj' => $value['obj_parent_permission']), '', 'row')->obj;
            }
            $data['arr_parent'] = $arr_id_parent_permission;
            //end
            $data['logged_time'] = $this->staff_model->get_logged_time_data($id, $ts_filter_data);
            $data['timesheets'] = $data['logged_time']['timesheets'];
            $data['check_dahahi']['result'] = 1;
            $data['check_dahahi']['message'] = 'Chưa kết nối';
            $data['MachineBoxId']['result'] = 0;
            if (!empty($getStaff['FaceID']) && !empty($getStaff['MachineBoxId'])) {
                $MachineBoxId = dahahi_get_MachineBoxId($getStaff['EmployeeName']);
                $data['MachineBoxId'] = $MachineBoxId;
                $data['check_dahahi'] = [];
                if (!empty($MachineBoxId)) {
                    foreach ($MachineBoxId['data'] as $key => $value) {
                        $data['check_dahahi'][] = dahahi_check_getInfo($getStaff['FaceID'], $value['MachineBoxId']);
                    }
                }
            }
        }
        $data['getStaff'] = $getStaff;
        $this->load->model('currencies_model');
        //old
        // $user_id = get_staff_user_id();
        // $this->db->select("tblstaff.staffid, CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name, tblstaff.firstname, tblstaff.lastname");
        // $this->db->from('tblstaff');
        // $this->db->where("tblstaff.staffid <>", $user_id);
        // $this->db->where('tblstaff.active', 1);
        // $data['employees'] = $this->db->get()->result_array();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['roles'] = $this->roles_model->get();
        $data['user_notes'] = $this->misc_model->get_notes($id, 'staff');
        $data['departments'] = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtRoleLevel'] = get_table_where('tbl_role_level');
        $data['dtSetupShift'] = get_table_where('tbl_setup_shift');
        $data['roles'] = [];
        //		if (!empty($data['member'])) {
        //			foreach ($data['staff_departments'] as $key => $value) {
        //				$this->get_parent_role_to_departments(0, $value['departmentid'], $data['roles']);
        //			}
        //		} else {
        //			$this->get_parent(0, $data['roles']);
        //		}
        $this->get_parent(0, $data['roles']);
        $data['title'] = $title;
        $this->load->view('admin/staff/member', $data);
    }

    public function get_parent($id_parent = 0, &$array_category = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('roles_parent' => $id_parent));
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

    /* Get role permission for specific role id */
    public function role_changed($id)
    {
        if (!has_permission('staff', '', 'view')) {
            ajax_access_denied('staff');
        }
        echo json_encode($this->roles_model->get($id)->permissions);
    }

    public function save_dashboard_widgets_order()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');
        $post_data = $this->input->post();
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', serialize($post_data));
    }

    public function save_dashboard_widgets_order_client()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');
        $post_data = $this->input->post();
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order_client', serialize($post_data));
    }

    public function save_dashboard_widgets_order_suppliers()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');
        $post_data = $this->input->post();
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order_suppliers', serialize($post_data));
    }

    public function save_dashboard_widgets_visibility()
    {
        hooks()->do_action('before_save_dashboard_widgets_visibility');
        $post_data = $this->input->post();
        var_dump($post_data);
        die;
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', serialize($post_data['widgets']));
    }

    public function reset_dashboard()
    {
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', null);
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', null);
        redirect(admin_url());
    }

    public function save_hidden_table_columns()
    {
        hooks()->do_action('before_save_hidden_table_columns');
        $data = $this->input->post();
        $id = $data['id'];
        $hidden = isset($data['hidden']) ? $data['hidden'] : [];
        update_staff_meta(get_staff_user_id(), 'hidden-columns-' . $id, json_encode($hidden));
    }

    public function change_language($lang = '')
    {
        hooks()->do_action('before_staff_change_language', $lang);
        $this->db->where('staffid', get_staff_user_id());
        $this->db->update(db_prefix() . 'staff', ['default_language' => $lang]);
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url());
        }
    }

    public function timesheets()
    {
        $data['view_all'] = false;
        if (is_admin() && $this->input->get('view') == 'all') {
            $data['staff_members_with_timesheets'] = $this->db->query('SELECT DISTINCT staff_id FROM ' . db_prefix() . 'taskstimers WHERE staff_id !=' . get_staff_user_id())->result_array();
            $data['view_all'] = true;
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff_timesheets', ['view_all' => $data['view_all']]);
        }
        if ($data['view_all'] == false) {
            unset($data['view_all']);
        }
        $data['logged_time'] = $this->staff_model->get_logged_time_data(get_staff_user_id());
        $data['title'] = '';
        $this->load->view('admin/staff/timesheets', $data);
    }

    public function delete()
    {
        if (!is_admin() && is_admin($this->input->post('id'))) {
            die('Busted, you can\'t delete administrators');
        }
        if (has_permission('staff', '', 'delete')) {
            $checkTimekeeping = get_table_where('tbl_timekeeping_detail', ['staff_id' => $this->input->post('id')], '', 'row_array');
            if (!empty($checkTimekeeping)) {
                set_alert('danger', 'Nhân viên đã tồn tại chấm công không thể xóa !');
                redirect(admin_url('staff'));
            }
            $success = $this->staff_model->delete($this->input->post('id'), $this->input->post('transfer_data_to'));
            if ($success) {
                set_alert('success', _l('deleted', _l('staff_member')));
            }
        }
        redirect(admin_url('staff'));
    }

    /* When staff edit his profile */
    public function edit_profile()
    {
        if ($this->input->post()) {
            handle_staff_profile_image_upload();
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);
            $success = $this->staff_model->update_profile($data, get_staff_user_id());
            if ($success) {
                set_alert('success', _l('staff_profile_updated'));
            }
            redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $this->load->model('departments_model');
        $data['member'] = $member;
        $data['departments'] = $this->departments_model->get();
        $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
        $data['title'] = $member->firstname . ' ' . $member->lastname;
        $this->load->view('admin/staff/profile', $data);
    }

    /* Remove staff profile image / ajax */
    public function remove_staff_profile_image($id = '')
    {
        $staff_id = get_staff_user_id();
        if (is_numeric($id) && (has_permission('staff', '', 'create') || has_permission('staff', '', 'edot'))) {
            $staff_id = $id;
        }
        hooks()->do_action('before_remove_staff_profile_image');
        $member = $this->staff_model->get($staff_id);
        if (file_exists(get_upload_path_by_type('staff') . $staff_id)) {
            delete_dir(get_upload_path_by_type('staff') . $staff_id);
        }
        $this->db->where('staffid', $staff_id);
        $this->db->update(db_prefix() . 'staff', [
            'profile_image' => null,
        ]);
        if (!is_numeric($id)) {
            redirect(admin_url('staff/edit_profile/' . $staff_id));
        } else {
            redirect(admin_url('staff/member/' . $staff_id));
        }
    }

    /* When staff change his password */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $response = $this->staff_model->change_password($this->input->post(null, false), get_staff_user_id());
            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorrect'));
            } else {
                if ($response == true) {
                    set_alert('success', _l('staff_password_changed'));
                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }
            redirect(admin_url('staff/edit_profile'));
        }
    }

    /* View public profile. If id passed view profile by staff id else current user*/
    public function profile($id = '')
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }
        hooks()->do_action('staff_profile_access', $id);
        $data['logged_time'] = $this->staff_model->get_logged_time_data($id);
        $data['staff_p'] = $this->staff_model->get($id);
        if (!$data['staff_p']) {
            blank_page('Staff Member Not Found', 'danger');
        }
        $this->load->model('departments_model');
        $data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
        $data['departments'] = $this->departments_model->get();
        $data['title'] = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
        // notifications
        $type_noti = 1;
        if ($this->input->get('type_noti')) {
            $type_noti = $this->input->get('type_noti');
        }
        $total_notifications = total_rows(db_prefix() . 'notifications', [
            'touserid' => get_staff_user_id(),
            'type' => $type_noti
        ]);
        $data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());
        $this->load->view('admin/staff/myprofile', $data);
    }

    /* Change status to staff active or inactive / ajax */
    public function change_staff_status($id, $status)
    {
        if (has_permission('staff', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->staff_model->change_staff_status($id, $status);
            }
        }
    }

    /* Logged in staff notifications*/
    public function notifications()
    {
        $this->load->model('misc_model');
        if ($this->input->post()) {
            $page = $this->input->post('page');
            $type_noti = $this->input->post('type_noti');
            $offset = ($page * $this->misc_model->get_notifications_limit());
            $this->db->limit($this->misc_model->get_notifications_limit(), $offset);
            $this->db->where('touserid', get_staff_user_id());
            if (!empty($type_noti)) {
                $this->db->where('type', $type_noti);
            }
            $this->db->order_by('date', 'desc');
            $notifications = $this->db->get(db_prefix() . 'notifications')->result_array();
            $total_notifications = total_rows(db_prefix() . 'notifications', [
                'touserid' => get_staff_user_id(),
                'type' => $type_noti
            ]);
            $data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());
            $i = 0;
            foreach ($notifications as $notification) {
                if (($notification['fromcompany'] == null && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == null && $notification['fromclientid'] != 0)) {
                    if ($notification['fromuserid'] != 0) {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('staff/profile/' . $notification['fromuserid']) . '">' . staff_profile_image($notification['fromuserid'], [
                                'staff-profile-image-small',
                                'img-circle',
                                'pull-left',
                            ]) . '</a>';
                    } else {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('clients/client/' . $notification['fromclientid']) . '">
                    <img class="client-profile-image-small img-circle pull-left" src="' . contact_profile_image_url($notification['fromclientid']) . '"></a>';
                    }
                } else {
                    $notifications[$i]['profile_image'] = '';
                    $notifications[$i]['full_name'] = '';
                }
                $additional_data = '';
                if (!empty($notification['additional_data'])) {
                    $additional_data = unserialize($notification['additional_data']);
                    $x = 0;
                    foreach ($additional_data as $data) {
                        if (strpos($data, '<lang>') !== false) {
                            $lang = get_string_between($data, '<lang>', '</lang>');
                            $temp = _l($lang);
                            if (strpos($temp, 'project_status_') !== false) {
                                $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                                $temp = $status['name'];
                            }
                            $additional_data[$x] = $temp;
                        }
                        $x++;
                    }
                }
                $notifications[$i]['description'] = _l($notification['description'], $additional_data);
                $notifications[$i]['date'] = time_ago($notification['date']);
                $notifications[$i]['full_date'] = $notification['date'];
                $i++;
            } //$notifications as $notification
            $data['notifications'] = $notifications;
            echo json_encode($data);
            die;
        }
    }

    public function getData_permission()
    {
        $data = $this->input->post();
        $get_parent = get_table_where('tbl_roles_parent_permission_v2', array('id_role' => $data['id_role'], 'can_view' => 1));
        $arr_parent = array();
        foreach ($get_parent as $key => $value) {
            $arr_parent[] = $value['obj_parent_permission'];
        }
        $arr_child = array();
        $get_child = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $data['id_role']));
        foreach ($get_child as $key => $value) {
            $arr_can_child = array();
            $data_result['child'][$key]['id_child'] = $value['id'];
            $data_result['child'][$key]['permission'] = $value['obj_permission'];
            //data can
            if ($value['can_view']) {
                $arr_can_child[] = 'view';
            }
            if ($value['can_view_own']) {
                $arr_can_child[] = 'view_own';
            }
            if ($value['can_create']) {
                $arr_can_child[] = 'create';
            }
            if ($value['can_edit']) {
                $arr_can_child[] = 'edit';
            }
            if ($value['can_print']) {
                $arr_can_child[] = 'print';
            }
            if ($value['can_approve']) {
                $arr_can_child[] = 'approve';
            }
            if ($value['can_import']) {
                $arr_can_child[] = 'import';
            }
            if ($value['can_export']) {
                $arr_can_child[] = 'export';
            }
            if ($value['can_delete']) {
                $arr_can_child[] = 'delete';
            }
            if ($value['can_approve_warehouse']) {
                $arr_can_child[] = 'approve_warehouse';
            }
            if ($value['can_approve_accept']) {
                $arr_can_child[] = 'approve_accept';
            }
            if ($value['can_approve_qc']) {
                $arr_can_child[] = 'approve_qc';
            }
            if ($value['can_approve_cancel']) {
                $arr_can_child[] = 'approve_cancel';
            }
            if ($value['can_cost']) {
                $arr_can_child[] = 'cost';
            }
            if ($value['can_profit']) {
                $arr_can_child[] = 'profit';
            }
            if ($value['can_notifications']) {
                $arr_can_child[] = 'notifications';
            }
            if ($value['can_agree_notifications']) {
                $arr_can_child[] = 'agree_notifications';
            }
            if ($value['can_add_notifications']) {
                $arr_can_child[] = 'add_notifications';
            }
            if ($value['can_qc']) {
                $arr_can_child[] = 'qc';
            }
            $data_result['child'][$key]['arr_child'] = $arr_can_child;
        }
        $data_result['arr_parent'] = $arr_parent;
        echo json_encode($data_result);
        die;
    }

    public function pdf()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $result['arr'] = explode(',', $data['arrID']);
            $this->load->view('admin/staff/printBarcode', $result);
        }
    }

    public function getStaff_roles()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id_role'] != '') {
                $arrID_child = array();
                $this->get_childs_id($data['id_role'], $arrID_child);
                $arrStaff = array();
                if ($arrID_child != array()) {
                    $this->db->select('tblstaff.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname');
                    $this->db->where_in('tblstaff.role', $arrID_child);
                    $this->db->where('tblstaff.active', 1);
                    $resultStaff = $this->db->get('tblstaff')->result_array();
                    if ($resultStaff) {
                        $arrStaff = $resultStaff;
                    }
                }
                echo json_encode($arrStaff);
                die;
            } else {
                echo json_encode(array());
                die;
            }
        }
    }

    function get_childs_id($parent_id = '', &$result = array())
    {
        $this->db->where('roles_parent', $parent_id);
        $items = $this->db->get('tblroles')->result();
        foreach ($items as $value) {
            array_push($result, $value->roleid);
            $this->get_childs_id($value->roleid, $result);
        }
    }

    public function get_role_parent_departments()
    {
        $roles = [];
        $list_departments = $this->input->post('list_departments');
        if (!empty($list_departments)) {
            $this->db->where_in('departmentid', $list_departments);
            $departments = $this->db->get('tbldepartments')->result_array();
            foreach ($departments as $key => $value) {
                $this->get_parent_role_to_departments(0, $value['departmentid'], $roles);
            }
        }
        echo json_encode($roles);
        die();
    }

    public function get_parent_role_to_departments($id_parent = 0, $id_departments = NULL, &$array_category = [], $level = 0)
    {
        if (is_numeric($level)) {
            if (!empty($id_departments)) {
                $this->db->where('departments_id', $id_departments);
            }
            $this->db->where(array('roles_parent' => $id_parent));
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
                    $this->get_parent_role_to_departments($value['roleid'], $id_departments, $array_category, $level + 1);
                }
            } else {
                return;
            }
        }
    }

    public function import_staff()
    {
        if (!has_permission('staff', '', 'create')) {
            access_denied('staff');
        }
        if ($this->input->post('save')) {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load("$fullfile");
            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AO');
            $arraydata = array();
            $row_start = $this->input->post('row_start') ? $this->input->post('row_start') : 3;
            $row_end = $this->input->post('row_end') ? $this->input->post('row_end') : $highestRow;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }
            $insertedRow = 0;
            $row = 3;
            foreach ($arraydata as $key => $value) {
                $lastname = $value[0]; //họ
                $firstname = $value[1]; //tên
                $code = $value[2]; //mã nhân viên
                $email = $value[3]; // email
                $birthday = \PHPExcel_Style_NumberFormat::toFormattedString($value[4], 'YYYY-MM-DD'); //ngày sinh
                // $salary = $value[5]; //lương trên tháng
                $phonenumber = $value[5]; //Điện thoại
                $facebook = $value[6];
                $linkedin = $value[7];
                $skype = $value[8];
                $departments = $value[9]; // Phòng ban
                // $role = $value[11]; //
                $password = $value[10];
                $gender = $value[11]; //
                $birthplace = $value[12];
                $domicile = $value[13];
                $cmnd_id_passport = $value[14];
                $date_range = \PHPExcel_Style_NumberFormat::toFormattedString($value[15], 'YYYY-MM-DD');
                $issued_by = $value[16];
                $marital_status = $value[17];
                $nationality = $value[18];
                $nation = $value[19];
                $account_name = $value[20];
                $bank = $value[21];
                $branch_bank = $value[22];
                $personal_tax_code = $value[23];
                $religion = $value[24];
                $resident = $value[25];
                $current_accommodation = $value[26];
                $salary_bhxh = $value[27];
                $allowance = $value[28];
                $check_bhxh = $value[29];
                $day_in = \PHPExcel_Style_NumberFormat::toFormattedString($value[30], 'YYYY-MM-DD');
                $insurrance_book_number = $value[31];
                $number_bhty = $value[32];
                $province_code_staff = $value[33];
                $hospital_registration = $value[34];
                $salary_bhxh_new = $value[35];
                $status_work = $value[36];
                $check_union = $value[37];
                $total_advance = $value[38];
                $coefficient_position = $value[39];
                $coefficient_responsibility = $value[40];
                // $relationship_family = $value[36];
                // $fullname_family = $value[37];
                // $year_birthday_family = $value[38];
                // $career_family = $value[39];
                // $address_family = $value[40];
                // $telephone_family = $value[41];
                // $from_date_literacy = $value[42];
                // $to_date_literacy = $value[43];
                // $literacy = $value[44];
                // $training_places_literacy = $value[45];
                // $specialized_literacy = $value[46];
                // $classification_literacy = $value[47];
                // $receive = $value[48];
                $reason = "";
                if (!empty($code)) {
                    $this->db->where('code', $code);
                    //					$this->db->where('email', $email);
                    $isExistCode = $this->db->get(db_prefix() . 'staff')->row();
                    // if ($isExistCode) {
                    // 	$reason .= 'Mã nhân viên đã tồn tại tại dòng ' . $row . '<br />';
                    // 	continue;
                    // }
                } else {
                    $isExistCode = false;
                }
                if ($isExistCode) { // update
                    if (!empty($gender)) {
                        switch ($gender) {
                            case 'Nam':
                                $gender = 'male';
                                break;
                            case 'Nữ':
                                $gender = 'female';
                                break;
                            case 'Khác':
                                $gender = 'other';
                                break;
                            default:
                                $reason .= 'Giới tính không hợp lệ tại dòng ' . $row . '<br />';
                                continue;
                                $gender = '';
                                break;
                        }
                    } else {
                        $gender = '';
                    }
                    if (!empty($marital_status)) {
                        switch ($marital_status) {
                            case 'Độc thân':
                                $marital_status = 'alone';
                                break;
                            case 'Kết hôn':
                                $marital_status = 'marriage';
                                break;
                            case 'Ly hôn':
                                $marital_status = 'divorce';
                                break;
                            default:
                                $reason .= 'Tình trạng hôn nhân không hợp lệ tại dòng ' . $row . '<br />';
                                continue;
                                $marital_status = '';
                                break;
                        }
                    } else {
                        $marital_status = '';
                    }
                    if (!empty($check_bhxh)) {
                        switch ($check_bhxh) {
                            case 'Có':
                            case 'có':
                                $check_bhxh = '1';
                                break;
                            case 'Không':
                            case 'không':
                                $check_bhxh = '0';
                                break;
                            default:
                                $reason .= 'Có tham gia Bảo hiểm không hợp lệ tại dòng ' . $row . '<br />';
                                continue;
                                $check_bhxh = '';
                                break;
                        }
                    } else {
                        $check_bhxh = '';
                    }
                    if (!empty($phonenumber)) {
                        if (!is_numeric($phonenumber)) {
                            $reason .= 'Số điện thoại không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }
                    if (!empty($cmnd_id_passport)) {
                        if (!is_numeric($cmnd_id_passport)) {
                            $reason .= 'CMND/Căn cước không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }
                    if (!empty($salary_bhxh)) {
                        if (!is_numeric($salary_bhxh)) {
                            $reason .= 'Lương cơ bản không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }
                    if (!empty($allowance)) {
                        if (!is_numeric($allowance)) {
                            $reason .= 'Phụ cấp không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }
                    if (!empty($departments)) {
                        $arrDepartmentsCode = explode(';', $departments);
                        $arrDepartmentsId = [];
                        $continueFlag = false;
                        foreach ($arrDepartmentsCode as $departmentCode) {
                            $departmentsId = $this->getDepartmentByCode($departmentCode);
                            if (!empty($departmentsId)) {
                                $arrDepartmentsId[] = $departmentsId;
                            } else {
                                $reason .= 'Mã phòng ban không hợp lệ tại dòng ' . $row . '<br />';
                                $continueFlag = true;
                                break;
                            }
                        }
                        if ($continueFlag) {
                            continue;
                        }
                    }

                    if (!empty($salary_bhxh_new)) {
                        if (!is_numeric($salary_bhxh_new)) {
                            $reason .= 'Lương cơ bản BHXH không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    if ($status_work != 0 && $status_work != 1 && $status_work != 2) {
                        $reason .= 'Trạng thái làm việc không hợp lệ tại dòng ' . $row . '<br />';
                        continue;
                    }

                    if ($check_union != 0 && $check_union != 1) {
                        $reason .= 'Tham gia đoàn phí không hợp lệ tại dòng ' . $row . '<br />';
                        continue;
                    }

                    if (!empty($total_advance)) {
                        if (!is_numeric($total_advance)) {
                            $reason .= 'Tiền ứng lương không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    if (!empty($coefficient_position)) {
                        if (!is_numeric($coefficient_position)) {
                            $reason .= 'Hệ số lương chức vụ không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    if (!empty($coefficient_responsibility)) {
                        if (!is_numeric($coefficient_responsibility)) {
                            $reason .= 'Hệ số lương trách nhiệm không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    $insertData = [
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'code' => $code,
                        'email' => $email,
                        'birthday' => $birthday, //!empty(to_sql_date($birthday)) ? to_sql_date($birthday) : $birthday,
                        // 'salary' => $salary,
                        'phonenumber' => $phonenumber,
                        'facebook' => $facebook,
                        'linkedin' => $linkedin,
                        'skype' => $skype,
                        // 'departments' => $arrDepartmentsId,
                        'password' => $password,
                        'gender' => $gender,
                        'birthplace' => $birthplace,
                        'domicile' => $domicile,
                        'cmnd_id_passport' => $cmnd_id_passport,
                        'date_range' => $date_range, //!empty(to_sql_date($date_range)) ? to_sql_date($date_range) : $date_range,
                        'issued_by' => $issued_by,
                        'marital_status' => $marital_status, //
                        'nationality' => $nationality, //
                        'nation' => $nation,
                        'account_name' => $account_name,
                        'bank' => $bank,
                        'branch_bank' => $branch_bank,
                        'personal_tax_code' => $personal_tax_code,
                        'religion' => $religion,
                        'resident' => $resident,
                        'current_accommodation' => $current_accommodation,
                        'salary_bhxh' => $salary_bhxh,
                        'allowance' => $allowance,
                        'check_bhxh' => $check_bhxh,
                        'insurrance_book_number' => $insurrance_book_number,
                        'number_bhty' => $number_bhty,
                        'province_code_staff' => $province_code_staff,
                        'hospital_registration' => $hospital_registration,
                        'day_in' => $day_in, //!empty(to_sql_date($day_in)) ? to_sql_date($day_in) : $day_in,
                        'email_signature' => '',

                        'salary_bhxh_new' => $salary_bhxh_new,
                        'status_work' => $status_work,
                        'check_union' => $check_union,
                        'total_advance' => $total_advance,
                        'coefficient_position' => $coefficient_position,
                        'coefficient_responsibility' => $coefficient_responsibility,
                        // 'dont_update_departments' => true,
                        // 'receive' => $receive,
                    ];
                    foreach ($insertData as $key => $value) {
                        if (empty($value) && $value !== '0') {
                            unset($insertData[$key]);
                        }
                    }
                    $this->db->where('staffid', $isExistCode->staffid);
                    $id = $this->db->update(db_prefix() . 'staff', $insertData);
                    // if ($this->db->affected_rows() > 0) {
                    // 	$id = true;
                    // }
                    // $id = $this->staff_model->update($insertData, $isExistCode->staffid);
                } else { // insert
                    if (empty($firstname)) {
                        $reason .= 'Tên trống tại dòng ' . $row . '<br />';
                        continue;
                    }
                    if (empty($lastname)) {
                        $reason .= 'Họ trống tại dòng ' . $row . '<br />';
                        continue;
                    }
                    // if (empty($phonenumber)) {
                    // 	$reason .= 'Số điện thoại trống tại dòng ' . $row . '<br />';
                    // 	continue;
                    // }
                    if (empty($password)) {
                        $password = '';
                        // $reason .= 'Mật khẩu trống tại dòng ' . $row . '<br />';
                        // continue;
                    }
                    if (!empty($gender)) {
                        switch ($gender) {
                            case 'Nam':
                                $gender = 'male';
                                break;
                            case 'Nữ':
                                $gender = 'female';
                                break;
                            case 'Khác':
                                $gender = 'other';
                                break;
                            default:
                                $reason .= 'Giới tính không hợp lệ tại dòng ' . $row . '<br />';
                                continue;
                                $gender = '';
                                break;
                        }
                    } else {
                        $gender = '';
                    }
                    if (!empty($marital_status)) {
                        switch ($marital_status) {
                            case 'Độc thân':
                                $marital_status = 'alone';
                                break;
                            case 'Kết hôn':
                                $marital_status = 'marriage';
                                break;
                            case 'Ly hôn':
                                $marital_status = 'divorce';
                                break;
                            default:
                                $reason .= 'Tình trạng hôn nhân không hợp lệ tại dòng ' . $row . '<br />';
                                $marital_status = '';
                                continue;
                                break;
                        }
                    } else {
                        $marital_status = '';
                    }
                    if (!empty($check_bhxh)) {
                        switch ($check_bhxh) {
                            case 'Có':
                            case 'có':
                                $check_bhxh = '1';
                                break;
                            case 'Không':
                            case 'không':
                                $check_bhxh = '0';
                                break;
                            default:
                                $reason .= 'Có tham gia Bảo hiểm không hợp lệ tại dòng ' . $row . '<br />';
                                $check_bhxh = '';
                                continue;
                                break;
                        }
                    } else {
                        $check_bhxh = '';
                    }
                    if (!empty($phonenumber)) {
                        if (!is_numeric($phonenumber)) {
                            $reason .= 'Số điện thoại không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    } else {
                        $phonenumber = '';
                    }
                    if (!empty($cmnd_id_passport)) {
                        if (!is_numeric($cmnd_id_passport)) {
                            $reason .= 'CMND/Căn cước không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    } else {
                        $cmnd_id_passport = '';
                    }
                    if (!empty($salary_bhxh)) {
                        if (!is_numeric($salary_bhxh)) {
                            $reason .= 'Lương cơ bản BHXH không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    } else {
                        $salary_bhxh = '';
                    }
                    if (!empty($allowance)) {
                        if (!is_numeric($allowance)) {
                            $reason .= 'Phụ cấp không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    } else {
                        $allowance = '';
                    }
                    if (!empty($email)) {
                        $this->db->where('email', $email);
                        $isExist = $this->db->get(db_prefix() . 'staff')->row();
                        if ($isExist) {
                            $reason .= 'Email đã tồn tại tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }
                    if (!empty($departments)) {
                        $arrDepartmentsCode = explode(';', $departments);
                        $arrDepartmentsId = [];
                        $continueFlag = false;
                        foreach ($arrDepartmentsCode as $departmentCode) {
                            $departmentsId = $this->getDepartmentByCode($departmentCode);
                            if (!empty($departmentsId)) {
                                $arrDepartmentsId[] = $departmentsId;
                            } else {
                                $reason .= 'Mã phòng ban không hợp lệ tại dòng ' . $row . '<br />';
                                $continueFlag = true;
                                break;
                            }
                        }
                        if ($continueFlag) {
                            continue;
                        }
                    } else {
                        $arrDepartmentsId = [];
                    }

                    if (!empty($salary_bhxh_new)) {
                        if (!is_numeric($salary_bhxh_new)) {
                            $reason .= 'Lương cơ bản BHXH không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    if ($status_work != 0 && $status_work != 1 && $status_work != 2) {
                        $reason .= 'Trạng thái làm việc không hợp lệ tại dòng ' . $row . '<br />';
                        continue;
                    }

                    if ($check_union != 0 && $check_union != 1) {
                        $reason .= 'Tham gia đoàn phí không hợp lệ tại dòng ' . $row . '<br />';
                        continue;
                    }

                    if (!empty($total_advance)) {
                        if (!is_numeric($total_advance)) {
                            $reason .= 'Tiền ứng lương không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    if (!empty($coefficient_position)) {
                        if (!is_numeric($coefficient_position)) {
                            $reason .= 'Hệ số lương chức vụ không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    if (!empty($coefficient_responsibility)) {
                        if (!is_numeric($coefficient_responsibility)) {
                            $reason .= 'Hệ số lương trách nhiệm không hợp lệ tại dòng ' . $row . '<br />';
                            continue;
                        }
                    }

                    $insertData = [
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'code' => $code,
                        'email' => $email,
                        'birthday' => !empty(to_sql_date($birthday)) ? to_sql_date($birthday) : $birthday,
                        // 'salary' => $salary,
                        'phonenumber' => $phonenumber,
                        'facebook' => $facebook,
                        'linkedin' => $linkedin,
                        'skype' => $skype,
                        'departments' => $arrDepartmentsId,
                        'password' => $password,
                        'gender' => $gender,
                        'birthplace' => $birthplace,
                        'domicile' => $domicile,
                        'cmnd_id_passport' => $cmnd_id_passport,
                        'date_range' => !empty(to_sql_date($date_range)) ? to_sql_date($date_range) : $date_range,
                        'issued_by' => $issued_by,
                        'marital_status' => $marital_status, //
                        'nationality' => $nationality, //
                        'nation' => $nation,
                        'account_name' => $account_name,
                        'bank' => $bank,
                        'branch_bank' => $branch_bank,
                        'personal_tax_code' => $personal_tax_code,
                        'religion' => $religion,
                        'resident' => $resident,
                        'current_accommodation' => $current_accommodation,
                        'salary_bhxh' => $salary_bhxh,
                        'allowance' => $allowance,
                        'check_bhxh' => $check_bhxh,
                        'insurrance_book_number' => $insurrance_book_number,
                        'number_bhty' => $number_bhty,
                        'province_code_staff' => $province_code_staff,
                        'hospital_registration' => $hospital_registration,
                        'day_in' => !empty(to_sql_date($day_in)) ? to_sql_date($day_in) : $day_in,
                        'email_signature' => '',

                        'salary_bhxh_new' => $salary_bhxh_new,
                        'status_work' => $status_work,
                        'check_union' => $check_union,
                        'total_advance' => $total_advance,
                        'coefficient_position' => $coefficient_position,
                        'coefficient_responsibility' => $coefficient_responsibility,
                        // 'receive' => $receive,
                    ];
                    if (empty($insertData['email'])) {
                        unset($insertData['email']);
                    }
                    $id = $this->staff_model->add($insertData);
                }
                if (!empty($id)) {
                    $insertedRow++;
                }
                $row++;
            }
            echo json_encode(
                [
                    'result' => true,
                    'message' => 'Import thành công ' . $insertedRow . ' dòng.<br />' . $reason,
                ]
            );
            die;
        } else {
            $data = [];
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_staff');
            $this->load->view('admin/staff/import', $data);
        }
    }

    public function print_code($id = '')
    {
        $staffid = $this->input->get('ids');
        $arrId = explode(',', $staffid);
        $this->db->select("tblstaff.staffid as staffid,CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as name_staff, tblstaff.code as code_staff");
        $this->db->from('tblstaff');
        $this->db->where_in('tblstaff.staffid', $arrId);
        $staffs = $this->db->get()->result_array();
        ob_start();
        $data = [];
        $data['title'] = lang('In QR');
        // $data['type'] = 'P';
        $data['type'] = 'L';
        $data['img'] = '';
        $object = 'staff';
        foreach ($staffs as $key => $value) {
            if (empty($value['barcode'])) {
                $codes = $value['staffid'] . '||' . $value['code_staff'] . '||' . $value['name_staff'];
                $this->db->update(
                    'tblstaff',
                    array('barcode' => $codes),
                    array('staffid' => $value['staffid'])
                );
            } else {
                $codes = $value['barcode'];
                $detail_code = explode('||', $codes);
                $codes = $value['staffid'] . '||' . $value['code_staff'] . '||' . $value['name_staff'];
                $this->db->update(
                    'tblstaff',
                    array('barcode' => $codes),
                    array('staffid' => $value['staffid'])
                );
            }
            $data['staffs'][$key]['codes'] = $codes;
            $staff_detail = get_table_where('tblstaff', array('staffid' => $value['staffid']), '', 'row');
            $data['staffs'][$key]['staffs'] = $staff_detail;
            $data['staffs'][$key]['data'] = $value;
            $data['staffs'][$key]['object'] = $object;
        }
        $content = ob_get_contents();
        $data['hide'] = 'hide';
        $data['content'] = $content;
        ob_end_clean();
        ob_clean();
        $pdf = print_pdf_staff_dt($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function print_pdf_html()
    {
        $data = [];
        $ids = $this->input->get('ids');
        $arrId = explode(',', $ids);
        $this->db->select("
			tblstaff.*,
			tblstaff.profile_image as images,
			tblroles.name as role_name,
			tblbranch.name as branch_name
		");
        $this->db->from('tblstaff');
        $this->db->where_in('tblstaff.staffid', $arrId);
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tblbranch', 'tblbranch.id = tblstaff.id_branch', 'left');
        $data['staff'] = $this->db->get()->result_array();
        foreach ($data['staff'] as $key => $value) {
            $codes = $value['barcode'];
            $data['staff'][$key]['fullname'] = $value['firstname'] . ' ' . $value['lastname'];
            $data['staff'][$key]['codes'] = $value['staffid'] . '||' . $value['code'] . '||' . $value['firstname'] . ' ' . $value['lastname'];
            $this->db->update(
                'tblstaff',
                array('barcode' => $codes),
                array('staffid' => $value['staffid'])
            );
            if (!empty($value['images'])) {
                if (!file_exists('uploads/staff_profile_images/' . $value['staffid'] . '/thumb_' . $value['images'])) {
                    $data['staff'][$key]['images'] = base_url('assets/images/user-placeholder.jpg');
                } else {
                    $data['staff'][$key]['images'] = base_url('uploads/staff_profile_images/' . $value['staffid'] . '/thumb_' . $value['images']);
                }
            } else {
                $data['staff'][$key]['images'] = base_url('assets/images/user-placeholder.jpg');
            }
        }
        $data['title'] = 'IN';
        $this->load->view('admin/staff/pdf', $data);
    }

    public function getDepartmentByCode($code)
    {
        $code = trim($code);
        $department = get_table_where('tbldepartments', ['code' => $code], '', 'row', '', 'departmentid');
        if (!empty($department)) {
            return $department->departmentid;
        } else {
            return '';
        }
    }

    public function export_staff()
    {
        if (!has_permission('staff', '', 'export')) {
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
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(30);
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

        $objPHPExcel->getActiveSheet()->mergeCells("A1:K1")->getStyle("A1:K1")->applyFromArray($style_excel['BStyle_center']);
        $objPHPExcel->getActiveSheet()->SetCellValue("A1", 'Thông tin chung')->getStyle("A1")->applyFromArray($style_excel['Background_header_one']);

        $objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'Họ')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Tên')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Mã nhân viên')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Email')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Ngày sinh')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Điện thoại')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Facebook')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", 'LinkedIn')->getStyle("H$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", 'Skype')->getStyle("I$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("J$numberRow", "Phòng ban \n(Mã phòng ban) \n(Phân cách bằng dấu ';')")->getStyle("J$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->getStyle("J$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("K$numberRow", 'Mật khẩu')->getStyle("K$numberRow")->applyFromArray($style_excel['Background_header_one']);


        $objPHPExcel->getActiveSheet()->mergeCells("L1:AE1")->getStyle("L1:AE1")->applyFromArray($style_excel['BStyle_center']);
        $objPHPExcel->getActiveSheet()->SetCellValue("L1", 'Thông tin cá nhân')->getStyle("L1")->applyFromArray($style_excel['Background_header_two']);

        $objPHPExcel->getActiveSheet()->SetCellValue("L$numberRow", 'Giới tính')->getStyle("L$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("M$numberRow", 'Nơi sinh')->getStyle("M$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("N$numberRow", 'Quê quán')->getStyle("N$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("O$numberRow", 'CMND/Căn cước')->getStyle("O$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("P$numberRow", 'Ngày cấp')->getStyle("P$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("Q$numberRow", 'Nơi cấp')->getStyle("Q$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("R$numberRow", 'Tình trạng hôn nhân')->getStyle("R$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("S$numberRow", 'Quốc tịch')->getStyle("S$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("T$numberRow", 'Dân tộc')->getStyle("T$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("U$numberRow", 'Tên tài khoản')->getStyle("U$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("V$numberRow", 'Ngân hàng')->getStyle("V$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("W$numberRow", 'Chi nhánh ngân hàng')->getStyle("W$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("X$numberRow", 'Mã số thuế cá nhân')->getStyle("X$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("Y$numberRow", 'Tôn giáo')->getStyle("Y$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("Z$numberRow", 'Thường trú')->getStyle("Z$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AA$numberRow", 'Chổ ở hiện nay')->getStyle("AA$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AB$numberRow", 'Lương cơ bản BHXH')->getStyle("AB$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AC$numberRow", 'Phụ cấp')->getStyle("AC$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AD$numberRow", 'Có tham gia Bảo hiểm')->getStyle("AD$numberRow")->applyFromArray($style_excel['Background_header_two']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AE$numberRow", 'Ngày vào làm')->getStyle("AE$numberRow")->applyFromArray($style_excel['Background_header_two']);


        $objPHPExcel->getActiveSheet()->mergeCells("AF1:AI1")->getStyle("AF1:AI1")->applyFromArray($style_excel['BStyle_center']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AF1", 'Bảo hiểm')->getStyle("AF1")->applyFromArray($style_excel['Background_header_three']);

        $objPHPExcel->getActiveSheet()->SetCellValue("AF$numberRow", 'Số sổ bảo hiểm')->getStyle("AF$numberRow")->applyFromArray($style_excel['Background_header_three']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AG$numberRow", 'Số thẻ BHYT')->getStyle("AG$numberRow")->applyFromArray($style_excel['Background_header_three']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AH$numberRow", 'Mã tỉnh cấp')->getStyle("AH$numberRow")->applyFromArray($style_excel['Background_header_three']);
        $objPHPExcel->getActiveSheet()->SetCellValue("AI$numberRow", 'Đăng ký bệnh viên')->getStyle("AI$numberRow")->applyFromArray($style_excel['Background_header_three']);
        $numberRow++;



        if ($this->input->get('fullname_search')) {
            $this->db->where('tblstaff.staffid = "' . $this->input->get('fullname_search') . '"', false, false);
        }

        if ($this->input->get('departments_search')) {
            $this->db->where('EXISTS (SELECT 1 FROM tblstaff_departments WHERE tblstaff_departments.staffid = tblstaff.staffid AND tblstaff_departments.departmentid IN (' . implode(',', $this->input->get('departments_search')) . '))', false, false);
        }

        if ($this->input->get('role_search')) {
            $this->db->where('tblstaff.role IN (' . implode(',', $this->input->get('role_search')) . ')', false, false);
        }

        $this->db->select([
            'tblstaff.*',
            '(
				SELECT GROUP_CONCAT(tbldepartments.code SEPARATOR ";") 
				FROM tblstaff_departments
				JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
				WHERE tblstaff_departments.staffid = tblstaff.staffid
			) as code_departments'
        ]);
        $data_staff = $this->db->get('tblstaff')->result_array();
        if (!empty($data_staff)) {
            foreach ($data_staff as $key => $value) {
                $i = 0;
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['lastname'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['firstname'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['email'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", _dC($value['birthday']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['phonenumber'] . ' ')->getStyle("$cloumns_excel[$i]$numberRow")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['facebook'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['linkedin'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['skype'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code_departments'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '')->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['gender'] == 'male' ? 'Nam' : ($value['gender'] == 'female' ? 'Nữ' : ($value['gender'] == 'other' ? 'Khác' : ''))))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['birthplace'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['domicile'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['cmnd_id_passport'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", _dC($value['date_range']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['issued_by'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['marital_status'] == 'alone' ? 'Độc thân' : ($value['marital_status'] == 'marriage' ? 'Kết hôn' : ($value['marital_status'] == 'divorce' ? 'Ly hôn' : ''))))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['nationality'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['nation'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['account_name'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['bank'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['branch_bank'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['personal_tax_code'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;


                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['religion'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['resident'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['current_accommodation'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['salary_bhxh'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['allowance'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", !empty($value['check_bhxh']) ? ($value['check_bhxh'] == 1 ? 'Có' : 'Không') : '')->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", _dC($value['day_in']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['insurrance_book_number'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['number_bhty'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['province_code_staff'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['hospital_registration'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;


                $numberRow++;
            }
        }


        $filename = lang('Danh_sach_nhan_vien') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function updateActiveSalary()
    {
        $value = !empty($this->input->get('value')) ? 1 : 0;
        $id_salary_new = $this->input->get('id_salary_new');
        $staff_id = $this->input->get('staff_id');
        $data = [];
        if ($value) {
            $this->db->where('staff_id', $staff_id);
            $this->db->where('active', 1);
            $this->db->where('id !=', $id_salary_new);
            $this->db->from('tbl_staff_salary');
            $check = $this->db->count_all_results();
            if ($check) {
                $data['result'] = 0;
                $data['message'] = lang('Đã có dòng áp dụng vào lương rồi !!');
                echo json_encode($data);
                die();
            } else {
                $dtSalaryNew = get_table_where('tbl_staff_salary', ['id' => $id_salary_new], '', 'row_array');
                $this->db->where('id', $id_salary_new);
                $this->db->update('tbl_staff_salary', [
                    'active' => 1
                ]);
                $this->db->where('staffid', $staff_id);
                $this->db->update('tblstaff', [
                    'salary_bhxh' => $dtSalaryNew['salary'],
                ]);
                $data['result'] = 1;
                $data['salary'] = $dtSalaryNew['salary'];
                $data['message'] = lang('Đã áp dụng lương vào nhân viên ');
            }
        } else {
            $this->db->where('id', $id_salary_new);
            $this->db->update('tbl_staff_salary', [
                'active' => 0
            ]);
            $this->db->where('staffid', $staff_id);
            $this->db->update('tblstaff', [
                'salary_bhxh' => 0,
            ]);
            $data['result'] = 1;
            $data['salary'] = 0;
            $data['message'] = lang('Đã xóa áp dụng lương vào nhân viên ');
        }
        echo json_encode($data);
    }

    public function add_branch()
    {
        $id_branch = $this->input->post('id_branch');
        $staffid = $this->input->post('staffid');
        $this->db->where('staffid', $staffid);
        $this->db->delete('tblstaff_branch');
        if (!empty($id_branch)) {
            foreach ($id_branch as $key => $value) {
                $this->db->insert('tblstaff_branch', [
                    'staffid' => $staffid,
                    'id_branch' => $value,
                ]);
            }
        }
        echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật thành công']);
        die();
    }

    public function changeStatusOvertime($staff_id, $status_overtime)
    {
        $data = [];
        $this->db->where('staffid', $staff_id);
        $success = $this->db->update('tblstaff', [
            'status_overtime' => $status_overtime
        ]);

        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function changeStatusSalary()
    {
        $data = [];
        $id = $this->input->post('id');
        $type_singer = $this->input->post('value');
        $this->db->where('staffid', $id);
        $success = $this->db->update('tblstaff', [
            'check_salary' => $type_singer
        ]);

        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }
    public function exportExcelStaff()
    {
        $columsExcel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];

        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/thong_tin_nhan_vien.xls';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);

            $this->db->select('
            tblstaff.*,
            tbl_staff_family.relationship_family as relationship_family	,
            tbl_staff_family.fullname_family as fullname_family,
            tbl_staff_family.telephone_family as telephone_family
            ');
            $this->db->from('tblstaff');
            $this->db->join('tbl_staff_family', 'tbl_staff_family.staff_id = tblstaff.staffid', 'left');
            $this->db->order_by('tblstaff.staffid asc');
            $staff = $this->db->get()->result_array();
            $dem = 0;
            $row = 2;
            $this->load->library('ciqrcode');
            foreach ($staff as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[0] . $row, $value['code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['firstname'] . ' ' . $value['lastname'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['personal_tax_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, $value['cmnd_id_passport'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $value['issued_by'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $value['current_accommodation'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, _d($value['birthday']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, $value['phonenumber'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, $value['email'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, $value['account_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, $value['bank'] . ' ' . $value['branch_bank'], PHPExcel_Cell_DataType::TYPE_STRING);
                if (!empty($value['relationship_family'])) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, getRelationship($value['relationship_family']) . ': ' . $value['fullname_family'], PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                }
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, $value['telephone_family'], PHPExcel_Cell_DataType::TYPE_STRING);
                if (!empty($value['barcode_new'])) {
                    $code = $value['barcode_new'];
                } else {
                    $code = 'staff||' . $value['staffid'];
                    $this->db->where('staffid', $value['staffid']);
                    $this->db->update('tblstaff', ['barcode_new' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/staff/';
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
                    $objDrawing1->setWidth(80);
                    $objDrawing1->setHeight(53);
                    $objDrawing1->setOffsetX(3);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[13] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(42);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, '')->getStyle($columsExcel[13] . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                //Giới tính
                $iExcel = 13;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, ($value['gender'] == 'male' ? 'Nam' : ($value['gender'] == 'female' ? 'Nữ' : 'Khác')), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['birthplace'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['domicile'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['cmnd_id_passport'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, (_d($value['date_range'] ?? null)), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['issued_by'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, ($value['marital_status'] == 'alone' ? 'Độc thân' : ($value['marital_status'] == 'marriage' ? 'Kết hôn' : ($value['marital_status'] == 'divorce' ? 'Ly hôn' : ''))), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['nationality'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['nation'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['religion'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, $value['resident'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['salary_bhxh']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['salary_bhxh_new']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, ($value['check_bhxh'] ? 'Có' : 'Không'), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, (_d($value['day_in'] ?? null)), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, ($value['status_work'] == 2 ? 'Nghỉ việc' : ($value['status_work'] == 1 ? 'Làm việc' : 'Thử việc')), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, ($value['check_union'] ? 'Có' : 'Không'), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['total_advance']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['coefficient_position']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['coefficient_responsibility']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['sales']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['phone']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['gasonline_cars']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['motel']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['number_reduce']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['concurrently']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['business_fee_staff']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, formatMoney($value['seniority']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[++$iExcel] . $row, (_d($value['date_dochai'] ?? null)), PHPExcel_Cell_DataType::TYPE_STRING);
            }

            $objPHPExcel->getActiveSheet()->getStyle('A3:AQ' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A3:AQ' . $row)->applyFromArray([
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
            $filename = lang('thong_tin_nhan_vien') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }
    public function timekeeping($id = '')
    {
        $_data = [];
        if (empty($id)) {
            $data['result'] = 0;
            $data['message'] = 'Không tìm thấy dữ liệu';
            echo json_encode($data);
            die();
        }
        $getStaff = get_table_where('tblstaff', array('staffid' => $id), '', 'row_array');
        // $data['check_dahahi']['result'] = 1;
        // $data['check_dahahi']['message'] = 'Chưa kết nối';
        // if (!empty($getStaff['FaceID']) && !empty($getStaff['MachineBoxId'])) {
        // 	$MachineBoxId = dahahi_get_MachineBoxId($getStaff['EmployeeName']);
        // 	$data['MachineBoxId'] = $MachineBoxId;
        // 	$data['check_dahahi'] = [];
        // 	if (!empty($MachineBoxId)) {
        // 		foreach ($MachineBoxId['data'] as $key => $value) {
        // 			$data['check_dahahi'][] = dahahi_check_getInfo($getStaff['FaceID'], $value['MachineBoxId']);
        // 		}
        // 	}
        // }
        if ($this->input->post('save')) {
            $faceid = $this->input->post('faceid');
            $FacePersionId = $this->input->post('FacePersionId');
            $MachineBoxId = $this->input->post('MachineBoxId');

            // $personnel = $this->personnel_model->getPersonnelById($id);
            $images = '';
            if ($faceid == 1) {
                if (!empty($getStaff)) {
                    if (!empty($getStaff['images'])) {
                        $link_images = 'uploads/staff_profile_images/'.$getStaff['staffid'].'/thumb_' . $getStaff['profile_image'];
                        if (file_exists($link_images)) {
                            $imageData = file_get_contents($link_images);
                            $images = base64_encode($imageData);
                        } else {
                            $data['result'] = 0;
                            $data['message'] = 'Không có avatar hiện tại';
                            echo json_encode($data);
                            die();
                        }
                    } else {
                        $data['result'] = 0;
                        $data['message'] = 'Không có avatar hiện tại';
                        echo json_encode($data);
                        die();
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Không có avatar hiện tại';
                    echo json_encode($data);
                    die();
                }
            }
            if ($faceid == 2) {
                if (isset($_FILES['images']['name']) && $_FILES['images']['name'] != '') {
                    $imageData = file_get_contents($_FILES['images']['tmp_name']);
                    $images = base64_encode($imageData);
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Vui lòng up hình ảnh';
                    echo json_encode($data);
                    die();
                }
            }
            $MachineBoxId_old = !empty($getStaff['MachineBoxId']) ? explode(',', $getStaff['MachineBoxId']) : [];

            $_data = [];
            $_data['FacePersionId'] = (!empty($FacePersionId) ? $FacePersionId : $getStaff['code'].$getStaff['staffid']);
            $_data['FullName'] = $getStaff['firstname'] . ' ' . $getStaff['lastname'];
            $_data['Base64Image'] = $images;
            if (!empty($MachineBoxId)) {
                foreach ($MachineBoxId as $key => $value) {
                    $response = dahahi_editInfo($id, $_data, $value);
                }
            }
            if (empty($response)) {
                $response = '000000';
            }
            if ($response == '000000') {
                if (!empty($MachineBoxId)) {
                    $this->db->where('staffid', $id);
                    $this->db->update('tblstaff', array('EmployeeName' => $_data['FullName'], 'FaceID' => $_data['FacePersionId'], 'MachineBoxId' => implode(',', $MachineBoxId)));
                } else {
                    $this->db->where('staffid', $id);
                    $this->db->update('tblstaff', array('FaceID' => NULL, 'MachineBoxId' => NULL));
                }
                if (!empty($MachineBoxId_old)) {
                    foreach ($MachineBoxId_old as $key => $value) {
                        if (!in_array($value, $MachineBoxId)) {
                            dahahi_remove_MachineBoxId($_data['FacePersionId'], $value);
                        }
                    }
                }
                $data['result'] = 1;
                $data['id'] = $id;
                $data['message'] = 'Liên kết thành công';
                echo json_encode($data);
                die();
            } else if ($response == 'FARE0014') {
                $data['result'] = 0;
                $data['message'] = 'Thiết bị không online';
                echo json_encode($data);
                die();
            } else if ($response == 'FARE0001') {
                $data['result'] = 0;
                $data['message'] = 'Lỗi không thấy khuôn mặt trong ảnh';
                echo json_encode($data);
                die();
            } else if ($response == 'FARE0008') {
                $data['result'] = 0;
                $data['message'] = 'Mã thiết bị không tồn tại';
                echo json_encode($data);
                die();
            } else if ($response == 'FARE000FOSO') {
                $data['result'] = 0;
                $data['message'] = 'Chưa kết nói đến phần mềm chấm công';
                echo json_encode($data);
                die();
            } else {
                $data['result'] = 0;
                $data['message'] = 'Liên kết thất bại';
                echo json_encode($data);
                die();
            }
        } else {
            $data['id'] = $id;
            // $personnel = $this->personnel_model->getPersonnelById($id);
            $getStaff = get_table_where('tblstaff', array('staffid' => $id), '', 'row_array');

            $data['check_dahahi'] = [];
            $data['check_dahahi'][0]['result'] = 1;
            $data['check_dahahi'][0]['message'] = 'Chưa kết nối';
            $data['dahahi_getAllMachine'] = dahahi_getAllMachine();

            if (!is_array($data['dahahi_getAllMachine'])) {
                // $data['result'] = 0;
                // $data['message'] = $data['dahahi_getAllMachine'];
                // echo json_encode($data);
                // die();
                refererModel($data['dahahi_getAllMachine']);
            }
            if (!empty($getStaff['FaceID']) && !empty($getStaff['MachineBoxId'])) {
                $MachineBoxId = dahahi_get_MachineBoxId($getStaff['EmployeeName']);
                $data['MachineBoxId'] = $MachineBoxId;
                $data['check_dahahi'] = [];
                if (!empty($MachineBoxId)) {
                    foreach ($MachineBoxId['data'] as $key => $value) {
                        $data['check_dahahi'][] = dahahi_check_getInfo($getStaff['FaceID'], $value['MachineBoxId']);
                    }
                }
            }
            $data['personnel'] = $getStaff;
            $data['title'] = lang('Liên kết phần mềm chấm công');
            $this->load->view('admin/staff/timekeeping', $data);
        }
    }

    public function loadViewUpdateShift()
    {
        $data = [];
        $data['ids'] = $this->input->post('ids');
        $data['dtshift'] = get_table_where('tbl_setup_shift');
        $this->load->view('admin/staff/update_shift', $data);
    }

    public function updateShift()
    {
        $data = [];
        $shift_id = $this->input->post('shift_id');
        $staff_id = $this->input->post('staff_id');
        $count = 0;
        if (!empty($staff_id)) {
            $staff_id = explode(',', $staff_id);
            foreach ($staff_id as $key => $value) {
                $this->db->where('staffid', $value);
                $success = $this->db->update('tblstaff', [
                    'setup_shift_id' => $shift_id
                ]);
                if ($success) {
                    $count++;
                }
            }
        }
        if ($count) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
        die();
    }

}
