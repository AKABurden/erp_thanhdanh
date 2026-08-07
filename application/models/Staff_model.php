<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff_model extends App_Model
{
    public function delete($id, $transfer_data_to)
    {
        if (!is_numeric($transfer_data_to)) {
            return false;
        }

        if ($id == $transfer_data_to) {
            return false;
        }

        hooks()->do_action('before_delete_staff_member', [
            'id'               => $id,
            'transfer_data_to' => $transfer_data_to,
        ]);

        $name           = get_staff_full_name($id);
        $transferred_to = get_staff_full_name($transfer_data_to);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('sale_agent', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'sale_agent' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'invoices', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('sale_agent', $id);
        $this->db->update(db_prefix() . 'invoices', [
            'sale_agent' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'expenses', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'notes', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'newsfeed_post_comments', [
            'userid' => $transfer_data_to,
        ]);

        $this->db->where('creator', $id);
        $this->db->update(db_prefix() . 'newsfeed_posts', [
            'creator' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'projectdiscussions', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'projects', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'creditnotes', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'credits', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'project_files', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'proposal_comments', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'proposals', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'task_comments', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->where('is_added_from_contact', 0);
        $this->db->update(db_prefix() . 'tasks', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'files', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('renewed_by_staff_id', $id);
        $this->db->update(db_prefix() . 'contract_renewals', [
            'renewed_by_staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'task_checklist_items', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('finished_from', $id);
        $this->db->update(db_prefix() . 'task_checklist_items', [
            'finished_from' => $transfer_data_to,
        ]);

        $this->db->where('admin', $id);
        $this->db->update(db_prefix() . 'ticket_replies', [
            'admin' => $transfer_data_to,
        ]);

        $this->db->where('admin', $id);
        $this->db->update(db_prefix() . 'tickets', [
            'admin' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'leads', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('assigned', $id);
        $this->db->update(db_prefix() . 'leads', [
            'assigned' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'taskstimers', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'contracts', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('assigned_from', $id);
        $this->db->where('is_assigned_from_contact', 0);
        $this->db->update(db_prefix() . 'task_assigned', [
            'assigned_from' => $transfer_data_to,
        ]);

        $this->db->where('responsible', $id);
        $this->db->update(db_prefix() . 'leads_email_integration', [
            'responsible' => $transfer_data_to,
        ]);

        $this->db->where('responsible', $id);
        $this->db->update(db_prefix() . 'web_to_lead', [
            'responsible' => $transfer_data_to,
        ]);

        $this->db->where('created_from', $id);
        $this->db->update(db_prefix() . 'subscriptions', [
            'created_from' => $transfer_data_to,
        ]);

        $this->db->where('notify_type', 'specific_staff');
        $web_to_lead = $this->db->get(db_prefix() . 'web_to_lead')->result_array();

        foreach ($web_to_lead as $form) {
            if (!empty($form['notify_ids'])) {
                $staff = unserialize($form['notify_ids']);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', $form['id']);
                            $this->db->update(db_prefix() . 'web_to_lead', [
                                'notify_ids' => $staff,
                            ]);
                        }
                    }
                }
            }
        }

        $this->db->where('id', 1);
        $leads_email_integration = $this->db->get(db_prefix() . 'leads_email_integration')->row();

        if ($leads_email_integration->notify_type == 'specific_staff') {
            if (!empty($leads_email_integration->notify_ids)) {
                $staff = unserialize($leads_email_integration->notify_ids);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', 1);
                            $this->db->update(db_prefix() . 'leads_email_integration', [
                                'notify_ids' => $staff,
                            ]);
                        }
                    }
                }
            }
        }

        $this->db->where('assigned', $id);
        $this->db->update(db_prefix() . 'tickets', [
            'assigned' => 0,
        ]);

        $this->db->where('staff', 1);
        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'dismissed_announcements');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'newsfeed_comment_likes');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'newsfeed_post_likes');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'customer_admins');

        $this->db->where('fieldto', 'staff');
        $this->db->where('relid', $id);
        $this->db->delete(db_prefix() . 'customfieldsvalues');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'events');

        $this->db->where('touserid', $id);
        $this->db->delete(db_prefix() . 'notifications');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'user_meta');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'project_members');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'project_notes');

        $this->db->where('creator', $id);
        $this->db->or_where('staff', $id);
        $this->db->delete(db_prefix() . 'reminders');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'staff_departments');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'todos');

        $this->db->where('staff', 1);
        $this->db->where('user_id', $id);
        $this->db->delete(db_prefix() . 'user_auto_login');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'staff_permissions');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'task_assigned');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'task_followers');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'pinned_projects');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'staff');
        log_activity('Staff Member Deleted [Name: ' . $name . ', Data Transferred To: ' . $transferred_to . ']');

        $this->deleteStaffFamily($id);
        $this->deleteStaffLiteracy($id);
        $this->deleteStaffReceive($id);

        $this->db->where('staff_id', $id);
        $this->db->delete('tbl_staff_salary');

        $this->db->where('staff_id', $id);
        $this->db->delete('tbl_staff_allowance');

        $this->db->where('staff_id', $id);
        $this->db->delete('tbl_staff_reduce');

        hooks()->do_action('staff_member_deleted', [
            'id'               => $id,
            'transfer_data_to' => $transfer_data_to,
        ]);

        return true;
    }

    /**
     * Get staff member/s
     * @param  mixed $id Optional - staff id
     * @param  mixed $where where in query
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $where = [])
    {
        $select_str = '*,CONCAT(firstname," ",lastname) as full_name';

        // Used to prevent multiple queries on logged in staff to check the total unread notifications in core/AdminController.php
        if (is_staff_logged_in() && $id != '' && $id == get_staff_user_id()) {
            // $select_str .= ',(SELECT COUNT(*) FROM ' . db_prefix() . 'notifications WHERE touserid=' . get_staff_user_id() . ' and isread=0) as total_unread_notifications, (SELECT COUNT(*) FROM ' . db_prefix() . 'todos WHERE finished=0 AND staffid=' . get_staff_user_id() . ') as total_unfinished_todos';
            $select_str .= ',0 as total_unread_notifications, 0 as total_unfinished_todos';
        }

        $this->db->select($select_str);
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
            $staff = $this->db->get(db_prefix() . 'staff')->row();

            if ($staff) {
                $staff->permissions = $this->get_staff_permissions($id);
            }

            return $staff;
        }
        $this->db->order_by('firstname', 'desc');

        return $this->db->get(db_prefix() . 'staff')->result_array();
    }

    /**
     * Get staff permissions
     * @param  mixed $id staff id
     * @return array
     */
    public function get_staff_permissions($id)
    {
        // Fix for version 2.3.1 tables upgrade
        if (defined('DOING_DATABASE_UPGRADE')) {
            return [];
        }

        $permissions = $this->app_object_cache->get('staff-' . $id . '-permissions');

        if (!$permissions && !is_array($permissions)) {
            $this->db->where('staff_id', $id);
            $permissions = $this->db->get('staff_permissions')->result_array();

            $this->app_object_cache->add('staff-' . $id . '-permissions', $permissions);
        }

        return $permissions;
    }

    /**
     * Add new staff member
     * @param array $data staff $_POST data
     */
    public function add($data)
    {
        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }
        // First check for all cases if the email exists.
        if (!empty($data['email'])) {
            $this->db->where('email', $data['email']);
            $email = $this->db->get(db_prefix() . 'staff')->row();
            if ($email) {
                die('Email already exists');
            }
        }
        $data['admin'] = 0;
        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            }
        }

        $send_welcome_email = true;
        $original_password  = $data['password'];
        if (!isset($data['send_welcome_email'])) {
            $send_welcome_email = false;
        } else {
            unset($data['send_welcome_email']);
        }
        $data['email_signature'] = nl2br_save_html($data['email_signature']);
        $data['password']        = app_hash_password($data['password']);
        $data['datecreated']     = date('Y-m-d H:i:s');
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $permission = [];
        if (isset($data['permission'])) {
            $permission = $data['permission'];
            unset($data['permission']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if ($data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }

        $arrFamily = [];
        if (isset($data['arrFamily'])) {
            $arrFamily = $data['arrFamily'];
            unset($data['arrFamily']);
        }

        $arrLiteracy = [];
        if (isset($data['arrLiteracy'])) {
            $arrLiteracy = $data['arrLiteracy'];
            unset($data['arrLiteracy']);
        }

        $receive = [];
        if (isset($data['receive'])) {
            $receive = $data['receive'];
            unset($data['receive']);
        }

        $arrSalary = [];
        if (isset($data['arrSalary'])) {
            $arrSalary = $data['arrSalary'];
            unset($data['arrSalary']);
        }

        $arrPc = [];
        if (isset($data['arrPc'])) {
            $arrPc = $data['arrPc'];
            unset($data['arrPc']);
        }

        $arrGt = [];
        if (isset($data['arrGt'])) {
            $arrGt = $data['arrGt'];
            unset($data['arrGt']);
        }

        unset($data['employee_manage']);
        $this->db->insert(db_prefix() . 'staff', $data);
        $staffid = $this->db->insert_id();
        if ($staffid) {
            $slug = $data['firstname'] . ' ' . $data['lastname'];

            if ($slug == ' ') {
                $slug = 'unknown-' . $staffid;
            }

            if ($send_welcome_email == true && $data['email']) {
                send_mail_template('staff_created', $data['email'], $staffid, $original_password);
            }

            $this->db->where('staffid', $staffid);
            $this->db->update(db_prefix() . 'staff', [
                'media_path_slug' => slug_it($slug),
            ]);

            if (isset($custom_fields)) {
                handle_custom_fields_post($staffid, $custom_fields);
            }
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert(db_prefix() . 'staff_departments', [
                        'staffid'      => $staffid,
                        'departmentid' => $department,
                    ]);
                }
            }

            if (!empty($arrFamily)) {
                foreach ($arrFamily as $key => $value) {
                    $arrFamily[$key]['staff_id'] = $staffid;
                }
                $this->insertBatchStaffFamily($arrFamily);
            }

            if (!empty($arrLiteracy)) {
                foreach ($arrLiteracy as $key => $value) {
                    $arrLiteracy[$key]['staff_id'] = $staffid;
                }
                $this->insertBatchStaffLiteracy($arrLiteracy);
            }

            if (!empty($arrSalary)){
                foreach ($arrSalary as $key => $value) {
                    $arrSalary[$key]['staff_id'] = $staffid;
                }
                $this->db->insert_batch('tbl_staff_salary', $arrSalary);
            }

            if (!empty($arrPc)){
                foreach ($arrPc as $key => $value) {
                    $arrPc[$key]['staff_id'] = $staffid;
                }
                $this->db->insert_batch('tbl_staff_allowance', $arrPc);
            }

            if (!empty($arrGt)){
                foreach ($arrGt as $key => $value) {
                    $arrGt[$key]['staff_id'] = $staffid;
                }
                $this->db->insert_batch('tbl_staff_reduce', $arrGt);
            }


            if (!empty($receive)) {
                $arrReceive = [];
                foreach ($receive as $key => $value) {
                    if (empty($value)) continue;
                    $arrReceive[] = [
                        'staff_id' => $staffid,
                        'receive_id' => $value,
                    ];
                }
                if (!empty($arrReceive)) {
                    $this->insertBatchStaffReceive($arrReceive);
                }
            }

            // Delete all staff permission if is admin we dont need permissions stored in database (in case admin check some permissions)
            $this->update_permissions($data['admin'] == 1 ? [] : $permission, $staffid);

            log_activity('New Staff Member Added [ID: ' . $staffid . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');

            // Get all announcements and set it to read.
            $this->db->select('announcementid');
            $this->db->from(db_prefix() . 'announcements');
            $this->db->where('showtostaff', 1);
            $announcements = $this->db->get()->result_array();
            foreach ($announcements as $announcement) {
                $this->db->insert(db_prefix() . 'dismissed_announcements', [
                    'announcementid' => $announcement['announcementid'],
                    'staff'          => 1,
                    'userid'         => $staffid,
                ]);
            }
            hooks()->do_action('staff_member_created', $staffid);

            return $staffid;
        }

        return false;
    }

    /**
     * Update staff member info
     * @param  array $data staff data
     * @param  mixed $id   staff id
     * @return boolean
     */
    public function update($data, $id)
    {
        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        $data = hooks()->apply_filters('before_update_staff_member', $data, $id);

        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            } else {
                if ($id != get_staff_user_id()) {
                    if ($id == 1) {
                        return [
                            'cant_remove_main_admin' => true,
                        ];
                    }
                } else {
                    return [
                        'cant_remove_yourself_from_admin' => true,
                    ];
                }
                $data['admin'] = 0;
            }
        }

        $affectedRows = 0;
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $permission = [];
        if (isset($data['permission'])) {
            $permission = $data['permission'];
            unset($data['permission']);
        }

        $arrFamily = [];
        if (isset($data['arrFamily'])) {
            $arrFamily = $data['arrFamily'];
            unset($data['arrFamily']);
        }

        $arrLiteracy = [];
        if (isset($data['arrLiteracy'])) {
            $arrLiteracy = $data['arrLiteracy'];
            unset($data['arrLiteracy']);
        }

        $arrSalary = [];
        if (isset($data['arrSalary'])) {
            $arrSalary = $data['arrSalary'];
            unset($data['arrSalary']);
        }

        $arrPc = [];
        if (isset($data['arrPc'])) {
            $arrPc = $data['arrPc'];
            unset($data['arrPc']);
        }

        $arrGt = [];
        if (isset($data['arrGt'])) {
            $arrGt = $data['arrGt'];
            unset($data['arrGt']);
        }

        $receive = [];
        if (isset($data['receive'])) {
            $receive = $data['receive'];
            unset($data['receive']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password']             = app_hash_password($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }


        if (isset($data['two_factor_auth_enabled'])) {
            $data['two_factor_auth_enabled'] = 1;
        } else {
            $data['two_factor_auth_enabled'] = 0;
        }

        if (isset($data['is_not_staff'])) {
            $data['is_not_staff'] = 1;
        } else {
            $data['is_not_staff'] = 0;
        }

        if (isset($data['admin']) && $data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }
        if (isset($data['is_internal_proposal'])) {
            $data['is_internal_proposal'] = 1;
        }else{
            $data['is_internal_proposal'] = 0;
        }

        $data['email_signature'] = nl2br_save_html($data['email_signature']);

        $this->load->model('departments_model');
        $staff_departments = $this->departments_model->get_staff_departments($id);
        if (sizeof($staff_departments) > 0) {
            if (!isset($data['departments'])) {
                $this->db->where('staffid', $id);
                $this->db->delete(db_prefix() . 'staff_departments');
            } else {
                foreach ($staff_departments as $staff_department) {
                    if (isset($departments)) {
                        if (!in_array($staff_department['departmentid'], $departments)) {
                            $this->db->where('staffid', $id);
                            $this->db->where('departmentid', $staff_department['departmentid']);
                            $this->db->delete(db_prefix() . 'staff_departments');
                            if ($this->db->affected_rows() > 0) {
                                $affectedRows++;
                            }
                        }
                    }
                }
            }
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->where('staffid', $id);
                    $this->db->where('departmentid', $department);
                    $_exists = $this->db->get(db_prefix() . 'staff_departments')->row();
                    if (!$_exists) {
                        $this->db->insert(db_prefix() . 'staff_departments', [
                            'staffid'      => $id,
                            'departmentid' => $department,
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }
        } else {
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert(db_prefix() . 'staff_departments', [
                        'staffid'      => $id,
                        'departmentid' => $department,
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        //handling employee
        $employee_manage = !empty($data['employee_manage']) ? $data['employee_manage'] : [];
        unset($data['employee_manage']);
        $this->site_model->deleteEmployeeManageStaff($id);
        if (!empty($employee_manage))
        {
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

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($this->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $permission), $id)) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {

            //
            $this->deleteStaffFamily($id);
            if (!empty($arrFamily)) {
                foreach ($arrFamily as $key => $value) {
                    $arrFamily[$key]['staff_id'] = $id;
                }
                $this->insertBatchStaffFamily($arrFamily);
            }

            $this->deleteStaffLiteracy($id);
            if (!empty($arrLiteracy)) {
                foreach ($arrLiteracy as $key => $value) {
                    $arrLiteracy[$key]['staff_id'] = $id;
                }
                $this->insertBatchStaffLiteracy($arrLiteracy);
            }

            $this->deleteStaffReceive($id);
            if (!empty($receive)) {
                $arrReceive = [];
                foreach ($receive as $key => $value) {
                    if (empty($value)) continue;
                    $arrReceive[] = [
                        'staff_id' => $id,
                        'receive_id' => $value,
                    ];
                }
                if (!empty($arrReceive)) {
                    $this->insertBatchStaffReceive($arrReceive);
                }
            }

            $this->db->where('staff_id',$id);
            $this->db->delete('tbl_staff_salary');
            if (!empty($arrSalary)) {
                foreach ($arrSalary as $key => $value) {
                    $arrSalary[$key]['staff_id'] = $id;
                }
                $this->db->insert_batch('tbl_staff_salary', $arrSalary);
            }

            $this->db->where('staff_id',$id);
            $this->db->delete('tbl_staff_allowance');
            if (!empty($arrPc)) {
                foreach ($arrPc as $key => $value) {
                    $arrPc[$key]['staff_id'] = $id;
                }
                $this->db->insert_batch('tbl_staff_allowance', $arrPc);
            }

            $this->db->where('staff_id',$id);
            $this->db->delete('tbl_staff_reduce');
            if (!empty($arrGt)) {
                foreach ($arrGt as $key => $value) {
                    $arrGt[$key]['staff_id'] = $id;
                }
                $this->db->insert_batch('tbl_staff_reduce', $arrGt);
            }

            hooks()->do_action('staff_member_updated', $id);
            log_activity('Staff Member Updated [ID: ' . $id . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');

            return true;
        }

        return false;
    }

    public function update_permissions($permission, $id)
    {
        //reset tất cả quyền
        $this->db->set('can_view',0);
        $this->db->where('id_staff',$id);
        $this->db->update('tbl_staff_parent_permission_v2');

        $reset_can_permission = array(
            'can_view'=>0,
            'can_view_own'=>0,
            'can_create'=>0,
            'can_edit'=>0,
            'can_print'=>0,
            'can_approve'=>0,
            'can_approve_warehouse'=>0,
            'can_approve_accept'=>0,
            'can_approve_qc'=>0,
            'can_approve_cancel'=>0,
            'can_import'=>0,
            'can_export'=>0,
            'can_delete'=>0,
            'can_cost' => 0,
            'can_profit' => 0,
            'can_notifications' => 0,
            'can_agree_notifications' => 0,
            'can_add_notifications' => 0,
            'can_qc' => 0,
            'can_view_price' => 0,
            'can_export_outsource'=>0,
            'can_approve_manager' =>0,
            'can_approve_lbc' => 0,
            'can_approve_ncnxl' => 0,
            'can_approve_gspn' => 0,
            'can_approve_dg' => 0,
            
        );
        $this->db->where('id_staff',$id);
        $this->db->update('tbl_staff_child_permission_v2', $reset_can_permission);
        //end

        foreach ($permission as $key => $value) {
//            if(isset($value['parent'])) {
                //kiểm tra và thêm quyền parent
                $checkExists_parent = get_table_where('tbl_parents_permissions_v2',array('obj'=>$key),'','row');
                if(!$checkExists_parent) {
                    $this->db->insert('tbl_parents_permissions_v2', ['obj'=>$key]);
                    $insert_id_parent = $this->db->insert_id();
                }
                else {
                    $insert_id_parent = $checkExists_parent->id;
                }

                //thêm quyền đc xem parent
                $obj_parent_permission = get_table_where('tbl_parents_permissions_v2',array('id'=>$insert_id_parent),'','row');
                $check_can_view = get_table_where('tbl_staff_parent_permission_v2',array('id_staff'=>$id,'obj_parent_permission'=>$obj_parent_permission->obj),'','row');
                if(!$check_can_view) {
                    $this->db->insert('tbl_staff_parent_permission_v2', ['id_staff'=>$id,'obj_parent_permission'=>$obj_parent_permission->obj,'can_view'=>1]);
                }
                else {
                    $this->db->set('can_view',1);
                    $this->db->where('id',$check_can_view->id);
                    $this->db->update('tbl_staff_parent_permission_v2');
                }

                //thêm quyền child
                if(isset($value['child'])) {
                    foreach ($value['child'] as $key_child => $value_child) {
                        //kiểm tra và thêm quyền parent
                        $checkExists_child = get_table_where('tbl_permission_v2',array('obj_parent_permission'=>$obj_parent_permission->obj,'obj'=>$key_child),'','row');
                        if(!$checkExists_child) {
                            $this->db->insert('tbl_permission_v2', ['obj_parent_permission'=>$obj_parent_permission->obj,'obj'=>$key_child]);
                            $insert_id_child = $this->db->insert_id();
                        }
                        else {
                            $insert_id_child = $checkExists_child->id;
                        }

                        //phân quyền
                        $obj_permission = get_table_where('tbl_permission_v2',array('id'=>$insert_id_child),'','row');
                        foreach ($value_child as $key_v => $value_v) {
                            $checkExists_permission = get_table_where('tbl_staff_child_permission_v2',array('id_staff'=>$id,'obj_permission'=>$obj_permission->obj),'','row');
                            if(!$checkExists_permission) {
                                $this->db->insert('tbl_staff_child_permission_v2',['id_staff'=>$id,'obj_permission'=>$obj_permission->obj]);
                                $insert_id_permission = $this->db->insert_id();
                            }
                            else {
                                $insert_id_permission = $checkExists_permission->id;
                            }

                            $colum = 'can_'.$key_v;
                            $this->db->set($colum,1);
                            $this->db->where('id',$insert_id_permission);
                            $this->db->update('tbl_staff_child_permission_v2');
                        }
                    }
                }
//            }
        }
        return true;
    }

    public function update_profile($data, $id)
    {
        $data = hooks()->apply_filters('before_staff_update_profile', $data, $id);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password']             = app_hash_password($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        if (isset($data['two_factor_auth_enabled'])) {
            $data['two_factor_auth_enabled'] = 1;
        } else {
            $data['two_factor_auth_enabled'] = 0;
        }

        $data['email_signature'] = nl2br_save_html($data['email_signature']);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', $data);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('staff_member_profile_updated', $id);
            log_activity('Staff Profile Updated [Staff: ' . get_staff_full_name($id) . ']');

            return true;
        }

        return false;
    }

    /**
     * Change staff passwordn
     * @param  mixed $data   password data
     * @param  mixed $userid staff id
     * @return mixed
     */
    public function change_password($data, $userid)
    {
        $data = hooks()->apply_filters('before_staff_change_password', $data, $userid);

        $member = $this->get($userid);
        // CHeck if member is active
        if ($member->active == 0) {
            return [
                [
                    'memberinactive' => true,
                ],
            ];
        }

        // Check new old password
        if (!app_hasher()->CheckPassword($data['oldpassword'], $member->password)) {
            return [
                [
                    'passwordnotmatch' => true,
                ],
            ];
        }

        $data['newpasswordr'] = app_hash_password($data['newpasswordr']);

        $this->db->where('staffid', $userid);
        $this->db->update(db_prefix() . 'staff', [
            'password'             => $data['newpasswordr'],
            'last_password_change' => date('Y-m-d H:i:s'),
        ]);
        if ($this->db->affected_rows() > 0) {
            log_activity('Staff Password Changed [' . $userid . ']');

            return true;
        }

        return false;
    }

    /**
     * Change staff status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_staff_status($id, $status)
    {
        $status = hooks()->apply_filters('before_staff_status_change', $status, $id);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', [
            'active' => $status,
        ]);

        log_activity('Staff Status Changed [StaffID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
    }

    public function get_logged_time_data($id = '', $filter_data = [])
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }
        $result['timesheets'] = [];
        $result['total']      = [];
        $result['this_month'] = [];

        $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
        $last_day_this_month  = date('Y-m-t 23:59:59');

        $result['last_month'] = [];
        $first_day_last_month = date('Y-m-01', strtotime('-1 MONTH')); // hard-coded '01' for first day
        $last_day_last_month  = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));

        $result['this_week'] = [];
        $first_day_this_week = date('Y-m-d', strtotime('monday this week'));
        $last_day_this_week  = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        $result['last_week'] = [];

        $first_day_last_week = date('Y-m-d', strtotime('monday last week'));
        $last_day_last_week  = date('Y-m-d 23:59:59', strtotime('sunday last week'));

        $this->db->select('task_id,start_time,end_time,staff_id,' . db_prefix() . 'taskstimers.hourly_rate,name,' . db_prefix() . 'taskstimers.id,rel_id,rel_type, billed');
        $this->db->where('staff_id', $id);
        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id = ' . db_prefix() . 'taskstimers.task_id', 'left');
        $timers           = $this->db->get(db_prefix() . 'taskstimers')->result_array();
        $_end_time_static = time();

        $filter_period = false;
        if (isset($filter_data['period-from']) && $filter_data['period-from'] != '' && isset($filter_data['period-to']) && $filter_data['period-to'] != '') {
            $filter_period = true;
            $from          = to_sql_date($filter_data['period-from']);
            $from          = date('Y-m-d', strtotime($from));
            $to            = to_sql_date($filter_data['period-to']);
            $to            = date('Y-m-d', strtotime($to));
        }

        foreach ($timers as $timer) {
            $start_date = strftime('%Y-%m-%d', $timer['start_time']);

            $end_time    = $timer['end_time'];
            $notFinished = false;
            if ($timer['end_time'] == null) {
                $end_time    = $_end_time_static;
                $notFinished = true;
            }

            $total = $end_time - $timer['start_time'];

            $result['total'][]     = $total;
            $timer['total']        = $total;
            $timer['end_time']     = $end_time;
            $timer['not_finished'] = $notFinished;

            if ($start_date >= $first_day_this_month && $start_date <= $last_day_this_month) {
                $result['this_month'][] = $total;
                if (isset($filter_data['this_month']) && $filter_data['this_month'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_last_month && $start_date <= $last_day_last_month) {
                $result['last_month'][] = $total;
                if (isset($filter_data['last_month']) && $filter_data['last_month'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_this_week && $start_date <= $last_day_this_week) {
                $result['this_week'][] = $total;
                if (isset($filter_data['this_week']) && $filter_data['this_week'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_last_week && $start_date <= $last_day_last_week) {
                $result['last_week'][] = $total;
                if (isset($filter_data['last_week']) && $filter_data['last_week'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }

            if ($filter_period == true) {
                if ($start_date >= $from && $start_date <= $to) {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
        }
        $result['total']      = array_sum($result['total']);
        $result['this_month'] = array_sum($result['this_month']);
        $result['last_month'] = array_sum($result['last_month']);
        $result['this_week']  = array_sum($result['this_week']);
        $result['last_week']  = array_sum($result['last_week']);

        return $result;
    }

    public function getStaff() {
        $this->db->select('tblstaff.staffid, tblstaff.firstname, tblstaff.lastname', false);
        $this->db->from('tblstaff');
        return $this->db->get()->result_array();
    }

    public function insertBatchStaffFamily($data) {
        return $this->db->insert_batch('tbl_staff_family', $data);
    }

    public function insertBatchStaffLiteracy($data) {
        return $this->db->insert_batch('tbl_staff_literacy', $data);
    }

    public function deleteStaffFamily($staff_id) {
        $this->db->where('tbl_staff_family.staff_id', $staff_id);
        return $this->db->delete('tbl_staff_family');
    }

    public function deleteStaffLiteracy($staff_id) {
        $this->db->where('tbl_staff_literacy.staff_id', $staff_id);
        return $this->db->delete('tbl_staff_literacy');
    }

    public function insertBatchStaffReceive($data) {
        return $this->db->insert_batch('tbl_staff_receive', $data);
    }

    public function deleteStaffReceive($staff_id) {
        $this->db->where('tbl_staff_receive.staff_id', $staff_id);
        return $this->db->delete('tbl_staff_receive');
    }

    public function getStaffFamily($staff_id) {
        $this->db->select('*');
        $this->db->from('tbl_staff_family');
        $this->db->where('tbl_staff_family.staff_id', $staff_id);
        return $this->db->get()->result_array();
    }

    public function getStaffLiteracy($staff_id) {
        $this->db->select('*');
        $this->db->from('tbl_staff_literacy');
        $this->db->where('tbl_staff_literacy.staff_id', $staff_id);
        return $this->db->get()->result_array();
    }

    public function getStaffReceive($staff_id) {
        $this->db->select('*');
        $this->db->from('tbl_staff_receive');
        $this->db->where('tbl_staff_receive.staff_id', $staff_id);
        return $this->db->get()->result_array();
    }

}