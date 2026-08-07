<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tasks_approval_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * core logic for approving criteria
     */
    public function approve_criteria($params)
    {
        $id = $params['id'];
        $process_id = $params['process_id'];
        $detail_id = $params['detail_id'];
        $inspection_criteria_id = $params['inspection_criteria_id'];
        $type = $params['type'];
        $role_processing = isset($params['role_processing']) ? $params['role_processing'] : '';

        // 1. Kiểm tra tồn tại record
        $check_exists = get_table_where(
            'tbl_tasks_inspection_criteria_process',
            [
                'tasks' => $id,
                'id_tasks_process' => $detail_id,
                'inspection_criteria' => $inspection_criteria_id,
                'process_id' => $process_id
            ],
            '',
            'row_array'
        );

        $ins_detail = [
            'tasks' => $id,
            'id_tasks_process' => $detail_id,
            'process_id' => $process_id,
            'inspection_criteria' => $inspection_criteria_id
        ];

        if ($type == 1) {
            $ins_detail['isCheck'] = 1;
            $ins_detail['isCheckNot'] = NULL;
            $message = 'Duyệt thành công';
        } else {
            $ins_detail['isCheck'] = NULL;
            $ins_detail['isCheckNot'] = 1;
            $message = 'Không duyệt thành công';
        }

        if (!empty($check_exists)) {
            $this->db->where('id', $check_exists['id']);
            $this->db->update('tbl_tasks_inspection_criteria_process', $ins_detail);
        } else {
            $this->db->insert('tbl_tasks_inspection_criteria_process', $ins_detail);
        }

        // 2. Xóa hàng đợi gửi mail cũ
        $this->db->where('task_id', $id);
        $this->db->where('detail_id', $detail_id);
        $this->db->where('criteria_id', $inspection_criteria_id);
        $this->db->where('sent', 0);
        $this->db->update('tbl_task_email_queue', ['sent' => 2]);

        // 3. Xử lý gửi mail kế tiếp nếu duyệt, hoặc dừng mail nếu bỏ duyệt
        if ($type == 1) {
            if (!empty($role_processing)) {
                $this->send_email_next_role($id, $process_id, $detail_id, $role_processing, $inspection_criteria_id);
            }
        } else {
            // Bỏ duyệt hoặc Không duyệt -> Dừng mail các bước liên quan
            $this->stop_next_step_reminders($id, $process_id, $detail_id, $inspection_criteria_id);
            $this->stop_previous_step_reminders($id, $process_id, $detail_id, $inspection_criteria_id);
        }

        return [
            'success' => true,
            'message' => $message
        ];
    }

    /**
     * Tìm role_processing kế tiếp và gửi email
     */
    public function send_email_next_role($task_id, $process_id, $detail_id, $current_role_processing, $current_criteria_id)
    {
        $this->db->select('tbl_tasks_process_child.id, tbl_tasks_process_child.id_category_tasks_process, tblcategory_tasks_process_child.role_processing');
        $this->db->join('tblcategory_tasks_process_child', 'tblcategory_tasks_process_child.id = tbl_tasks_process_child.id_category_tasks_process', 'left');
        $this->db->where('tbl_tasks_process_child.task', $task_id);
        $this->db->where('tbl_tasks_process_child.id_category_tasks', $process_id);
        $this->db->order_by('tbl_tasks_process_child.id', 'ASC');
        $all_children = $this->db->get('tbl_tasks_process_child')->result_array();

        $this->db->select('duedate');
        $this->db->where('id', $task_id);
        $task_info = $this->db->get('tbltasks')->row();
        $task_duedate = !empty($task_info->duedate) ? $task_info->duedate : date('Y-m-d H:i:s', strtotime('+90 minutes'));

        $next_role = null;
        $next_child_id = null;
        $next_id_category_tasks_process = null;
        $found_current = false;
        foreach ($all_children as $child) {
            if ($child['id'] == $current_criteria_id) {
                $found_current = true;
                continue;
            }
            if ($found_current && !empty($child['role_processing']) && $child['role_processing'] != $current_role_processing) {
                $next_role = $child['role_processing'];
                $next_child_id = $child['id'];
                $next_id_category_tasks_process = $child['id_category_tasks_process'];
                break;
            }
        }

        if (empty($next_role)) {
            // Nếu không tìm thấy role tiếp theo trong process hiện tại, tìm checklist item tiếp theo
            $this->db->where('taskid', $task_id);
            $this->db->where('id >', $detail_id);
            $this->db->order_by('id', 'ASC');
            $next_main = $this->db->get('tbltask_checklist_items')->row();

            if ($next_main && !empty($next_main->process_id)) {
                // Tìm tiêu chí đầu tiên của process tiếp theo
                $this->db->select('tbl_tasks_process_child.id, tbl_tasks_process_child.id_category_tasks_process, tblcategory_tasks_process_child.role_processing');
                $this->db->join('tblcategory_tasks_process_child', 'tblcategory_tasks_process_child.id = tbl_tasks_process_child.id_category_tasks_process', 'left');
                $this->db->where('tbl_tasks_process_child.task', $task_id);
                $this->db->where('tbl_tasks_process_child.id_category_tasks', $next_main->process_id);
                $this->db->order_by('tbl_tasks_process_child.id', 'ASC');
                $first_child = $this->db->get('tbl_tasks_process_child')->row_array();

                if ($first_child && !empty($first_child['role_processing'])) {
                    $this->_trigger_notification_and_reminders($task_id, $next_main->process_id, $next_main->id, $first_child, $task_duedate, $current_criteria_id);
                }
            }
            return;
        }

        // Nếu tìm thấy role tiếp theo trong cùng process
        $next_child_data = [
            'role_processing' => $next_role,
            'id' => $next_child_id,
            'id_category_tasks_process' => $next_id_category_tasks_process
        ];

        $this->_trigger_notification_and_reminders($task_id, $process_id, $detail_id, $next_child_data, $task_duedate, $current_criteria_id);
    }

    /**
     * Helper to trigger notification and reminders for a specific criteria
     */
    private function _trigger_notification_and_reminders($task_id, $process_id, $detail_id, $next_child_data, $task_duedate, $current_criteria_id)
    {
        $next_role = $next_child_data['role_processing'];
        $next_child_id = $next_child_data['id'];
        $next_id_category_tasks_process = $next_child_data['id_category_tasks_process'];

        $this->db->where('roleid', $next_role);
        $role_info = $this->db->get('tblroles')->row();

        $this->db->select('staffid, email, firstname, lastname');
        $this->db->where('role', $next_role);
        $this->db->where('active', 1);
        $staff_list = $this->db->get('tblstaff')->result_array();

        if (empty($staff_list)) {
            return;
        }

        $criteria = get_table_where('tbl_tasks_process_child', ['id' => $current_criteria_id], '', 'row_array');
        $next_criteria = get_table_where('tbl_tasks_process_child', ['id' => $next_child_id], '', 'row_array');

        $kpi_name = '';
        if (!empty($next_id_category_tasks_process)) {
            $kpi_name = $this->get_kpi_name($next_id_category_tasks_process);
        }

        $extra_info = [
            'next_role_code' => !empty($role_info) ? $role_info->code_role : '',
            'next_role_name' => !empty($role_info) ? $role_info->name : '',
            'next_criteria_name' => !empty($next_criteria) ? $next_criteria['name'] : '',
            'next_kpi_name' => $kpi_name,
        ];

        foreach ($staff_list as $staff) {
            if (!empty($staff['email'])) {
                $this->send_approval_email($staff, $task_id, $criteria, 1, $extra_info, $detail_id);

                $reminders = [
                    ['type' => 1, 'scheduled_time' => date('Y-m-d H:i:s', strtotime('+30 minutes'))],
                    ['type' => 2, 'scheduled_time' => date('Y-m-d H:i:s', strtotime('+60 minutes'))],
                    ['type' => 3, 'scheduled_time' => $task_duedate],
                ];

                foreach ($reminders as $rem) {
                    $this->db->insert('tbl_task_email_queue', [
                        'task_id'        => $task_id,
                        'process_id'     => $process_id,
                        'detail_id'      => $detail_id,
                        'criteria_id'    => $next_child_id,
                        'staff_id'       => $staff['staffid'],
                        'type'           => $rem['type'],
                        'scheduled_time' => $rem['scheduled_time'],
                        'sent'           => 0,
                        'created_at'     => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }

    /**
     * Lấy tên KPI xét duyệt
     */
    public function get_kpi_name($id_category_tasks_process)
    {
        $this->db->from('tbl_kpi_list_criteria_department');
        $this->db->where('tbl_kpi_list_criteria_department.id_task_procedure', $id_category_tasks_process);
        $dtData = $this->db->get()->row_array();

        $name = '';
        $parent_id = 0;
        if (!empty($dtData)) {
            $parent_id = $dtData['parent_id'];
        }

        $counts = 0;
        while ($counts <= 3) {
            $counts++;
            if ($parent_id > 0) {
                $this->db->from('tbl_kpi_list_criteria_department');
                $this->db->where('tbl_kpi_list_criteria_department.id', $parent_id);
                $dtParent = $this->db->get()->row_array();
                if (!empty($dtParent)) {
                    $name .= '->' . $dtParent['name'];
                    $parent_id = $dtParent['parent_id'];
                } else {
                    break;
                }
            }
        }

        return $name;
    }

    /**
     * Gửi mail thông báo duyệt
     */
    public function send_approval_email($staff, $task_id, $criteria, $type, $extra_info = [], $current_detail_id = null)
    {
        $this->db->where('id', $task_id);
        $task = $this->db->get('tbltasks')->row();

        if (empty($task) || empty($staff['email'])) {
            return false;
        }

        $status_text = ($type == 1) ? 'Đã duyệt' : 'Không duyệt';
        
        $message = '<br /><span style="font-size: 12pt;">Thông báo duyệt tiêu chí kiểm tra:</span>
                    <br /><br />
                    <span style="font-size: 12pt;"><strong>Tiêu chí:</strong> ' . $criteria['name'] . '</span><br />
                    <span style="font-size: 12pt;"><strong>Trạng thái:</strong> ' . $status_text . '</span><br />
                    <span style="font-size: 12pt;"><strong>Công việc:</strong> ' . $task->name . '</span><br />
                    <span style="font-size: 12pt;"><strong>Hạn chót:</strong> ' . (!empty($task->duedate) ? date('d/m/Y H:i', strtotime($task->duedate)) : 'N/A') . '</span><br />
                    <span style="font-size: 12pt;"><strong>Ngày duyệt:</strong> ' . date('d/m/Y H:i') . '</span><br />
                    <span style="font-size: 12pt;"><strong>Link chi tiết:</strong> <a href="' . admin_url('tasks?id=' . $task_id . '&process_id=' . $current_detail_id) . '">Xem tại đây</a></span><br /><br />';

        if (!empty($extra_info)) {
            if (!empty($extra_info['next_role_code']) || !empty($extra_info['next_role_name'])) {
                $vi_tri = $extra_info['next_role_code'];
                if (!empty($extra_info['next_role_name'])) {
                    $vi_tri .= ' (' . $extra_info['next_role_name'] . ')';
                }
                $message .= '<span style="font-size: 12pt;"><strong>Vị trí được phân tiếp theo:</strong> ' . $vi_tri . '</span><br />';
            }
            if (!empty($extra_info['next_criteria_name'])) {
                $message .= '<span style="font-size: 12pt;"><strong>Quy Chuẩn Công Việc tiếp theo:</strong> ' . $extra_info['next_criteria_name'] . '</span><br />';
            }
            if (!empty($extra_info['next_kpi_name'])) {
                $message .= '<span style="font-size: 12pt;"><strong>KPI xét duyệt tiếp theo:</strong> ' . $extra_info['next_kpi_name'] . '</span><br />';
            }
            $message .= '<br />';
        }

        // Lấy quy trình bước kế tiếp từ tbltask_checklist_items
        if (!empty($current_detail_id)) {
            $this->db->where('taskid', $task_id);
            $this->db->where('id', $current_detail_id);
            $next_checklist = $this->db->get('tbltask_checklist_items')->row();
            if ($next_checklist) {
                $message .= '<span style="font-size: 12pt;"><strong>Quy trình:</strong> ' . $next_checklist->description . '</span><br /><br />';
            }
        }

        if ($type == 2 && !empty($criteria['name_role'])) {
            $message .= '<span style="font-size: 12pt;"><strong>Lý do không duyệt:</strong> Cần tạo phiếu báo cáo không phù hợp</span><br /><br />';
        }

        $message .= '<span style="font-size: 12pt;">Vui lòng theo dõi công việc để biết thêm chi tiết.</span>';

        $this->load->config('email');
        $template = new stdClass();
        $template->message = $message;
        $template->fromname = get_option('companyname');
        $template->subject = 'Thông báo duyệt tiêu chí - ' . $task->name;

        $template = parse_email_template($template);

        $this->email->initialize();
        $this->email->set_newline(config_item('newline'));
        $this->email->set_crlf(config_item('crlf'));

        $this->email->from(get_option('smtp_email'), $template->fromname);
        $this->email->to($staff['email']);

        $systemBCC = get_option('bcc_emails');
        if ($systemBCC != '') {
            $this->email->bcc($systemBCC);
        }

        $this->email->subject($template->subject);
        $this->email->message($template->message);
        
        $status = $this->email->send(true);
        $error_message = '';
        if (!$status) {
            $error_message = $this->email->print_debugger();
        }

        $this->db->insert('tbl_email_logs', [
            'task_id'       => $task_id,
            'email'         => $staff['email'],
            'subject'       => $template->subject,
            'content'       => $template->message,
            'status'        => $status ? 1 : 0,
            'error_message' => $error_message,
            'date_sent'     => date('Y-m-d H:i:s'),
        ]);

        return $status;
    }

    /**
     * Stop reminders for the next step (child or main process)
     */
    public function stop_next_step_reminders($task_id, $process_id, $detail_id, $current_criteria_id)
    {
        // Tìm bước con tiếp theo trong cùng process
        $this->db->where('task', $task_id);
        $this->db->where('id_category_tasks', $process_id);
        $this->db->where('id >', $current_criteria_id);
        $this->db->order_by('id', 'ASC');
        $next_child = $this->db->get('tbl_tasks_process_child')->row();

        if ($next_child) {
            $this->db->where('task_id', $task_id);
            $this->db->where('process_id', $process_id);
            $this->db->where('detail_id', $detail_id);
            $this->db->where('criteria_id', $next_child->id);
            $this->db->where('sent', 0);
            $this->db->update('tbl_task_email_queue', ['sent' => 2]);
        } else {
            // Nếu là bước cuối của process, tìm checklist item tiếp theo
            $this->db->where('taskid', $task_id);
            $this->db->where('id >', $detail_id);
            $this->db->order_by('id', 'ASC');
            $next_main = $this->db->get('tbltask_checklist_items')->row();

            if ($next_main) {
                // Tiêu chí đầu tiên của quy trình chính tiếp theo
                $this->db->where('task', $task_id);
                $this->db->where('id_category_tasks', $next_main->process_id);
                $this->db->order_by('id', 'ASC');
                $first_child = $this->db->get('tbl_tasks_process_child')->row();

                if ($first_child) {
                    $this->db->where('task_id', $task_id);
                    $this->db->where('detail_id', $next_main->id);
                    $this->db->where('criteria_id', $first_child->id);
                    $this->db->where('sent', 0);
                    $this->db->update('tbl_task_email_queue', ['sent' => 2]);
                }
            }
        }
    }

    /**
     * Stop reminders for the previous step
     */
    public function stop_previous_step_reminders($task_id, $process_id, $detail_id, $current_criteria_id)
    {
        // Tìm bước con trước đó trong cùng process
        $this->db->where('task', $task_id);
        $this->db->where('id_category_tasks', $process_id);
        $this->db->where('id <', $current_criteria_id);
        $this->db->order_by('id', 'DESC');
        $prev_child = $this->db->get('tbl_tasks_process_child')->row();

        if ($prev_child) {
            $this->db->where('task_id', $task_id);
            $this->db->where('process_id', $process_id);
            $this->db->where('detail_id', $detail_id);
            $this->db->where('criteria_id', $prev_child->id);
            $this->db->where('sent', 0);
            $this->db->update('tbl_task_email_queue', ['sent' => 2]);
        } else {
            // Nếu là bước đầu của process, tìm checklist item trước đó
            $this->db->where('taskid', $task_id);
            $this->db->where('id <', $detail_id);
            $this->db->order_by('id', 'DESC');
            $prev_main = $this->db->get('tbltask_checklist_items')->row();

            if ($prev_main) {
                // Tiêu chí cuối cùng của quy trình chính trước đó
                $this->db->where('task', $task_id);
                $this->db->where('id_category_tasks', $prev_main->process_id);
                $this->db->order_by('id', 'DESC');
                $last_child = $this->db->get('tbl_tasks_process_child')->row();

                if ($last_child) {
                    $this->db->where('task_id', $task_id);
                    $this->db->where('detail_id', $prev_main->id);
                    $this->db->where('criteria_id', $last_child->id);
                    $this->db->where('sent', 0);
                    $this->db->update('tbl_task_email_queue', ['sent' => 2]);
                }
            }
        }
    }

    /**
     * Kiểm tra tạo phiếu báo cáo không phù hợp
     */
    public function CheckCreateBCKPH($id = '', $process_id = '', $detail_id = '')
    {
        $this->db->select('tbl_tasks_process_child.*');
        $this->db->where('tbl_tasks_process_child.id_category_tasks', $process_id);
        $this->db->where('tbl_tasks_process_child.task', $id);
        $category_hand_over = $this->db->get('tbl_tasks_process_child')->result_array();
        $is_check = 1;
        foreach ($category_hand_over as $key => $value) {
            $check = get_table_where('tbl_tasks_inspection_criteria_process', ['tasks' => $id, 'process_id' => $process_id, 'id_tasks_process' => $detail_id, 'inspection_criteria' => $value['id']], '', 'row_array');

            $isCheckNot = '';
            if (!empty($check)) {
                if ($check['isCheckNot'] == 1) {
                    $isCheckNot = 1;
                }
            }
            if ($isCheckNot == 1) {
                $production_report = get_table_where('tblproduction_report', ['id_tasks' => $id, 'id_tasks_process' => $detail_id, 'id_tasks_process_child' => $value['id']], '', 'row_array');

                if (!empty($production_report)) {
                    $this->db->select('tbl_process_production_report.*');
                    $this->db->where('tbl_process_production_report.staff_process', 0);
                    $this->db->where('tbl_process_production_report.production_report_id', $production_report['id']);
                    $this->db->from('tbl_process_production_report');
                    $Success_process = $this->db->get()->num_rows();
                    if (!empty($Success_process)) {
                        $is_check = 2;
                    }
                } else {
                    $is_check = $value['id'];
                }
            }
        }
        return $is_check;
    }

    public function check_mail_production_report($id) {
        $production_report = get_table_where('tblproduction_report', ['id' => $id], '', 'row_array');
        if (!empty($production_report)) {
            $id_tasks_process = $production_report['id_tasks_process'];
            $id_tasks_process_child = $production_report['id_tasks_process_child'];
            if (!empty($id_tasks_process_child) && !empty($id_tasks_process)) {
                //Biết nó từ công việc qui trình tạo qua rùi kiểm tra trạng thái duyệt hết chưa
                $this->db->from('tbl_process_production_report');
                $this->db->where('production_report_id', $id);
                $this->db->where('staff_process', 0);
                $this->db->limit(1);
                $check = $this->db->get()->num_rows();
                if (empty($check)) {
                    $task_id = $production_report['id_tasks'];
                    $process = get_table_where(
                        'tbl_tasks_inspection_criteria_process',
                        [
                            'tasks' => $task_id,
                            'id_tasks_process' => $id_tasks_process,
                            'inspection_criteria' => $id_tasks_process_child
                        ],
                        '',
                        'row_array'
                    );

                    $this->db->select('tblcategory_tasks_process_child.role_processing');
                    $this->db->from('tbl_tasks_process_child');
                    $this->db->join('tblcategory_tasks_process_child', 'tblcategory_tasks_process_child.id = tbl_tasks_process_child.id_category_tasks_process', 'left');
                    $this->db->where('tbl_tasks_process_child.task', $task_id);
                    $this->db->where('tbl_tasks_process_child.id_category_tasks', $process['process_id']);
                    $process_child = $this->db->get()->row_array();

                    $role_processing = $process_child['role_processing'] ?? 0;
                    $this->tasks_approval_model->send_email_next_role($task_id, $process['process_id'], $id_tasks_process, $role_processing, $id_tasks_process_child);
                }

            }
        }
        return true;
    }
}
