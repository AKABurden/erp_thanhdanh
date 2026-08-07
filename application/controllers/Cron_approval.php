<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron_approval extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
    }

    public function index()
    {
        $now = date('Y-m-d H:i:s');
        
        // Lấy danh sách email chưa gửi và đến hạn (sent = 0: Pending, 1: Sent, 2: Cancelled)
        $this->db->where('sent', 0);
        $this->db->where('scheduled_time <=', $now);
        $queue = $this->db->get('tbl_task_email_queue')->result_array();

        if (empty($queue)) {
            return;
        }

        foreach ($queue as $item) {
            $this->process_item($item);
        }
    }

    private function process_item($item)
    {
        // 1. Kiểm tra xem quy trình hiện tại đã được duyệt chưa (isCheck = 1)
        $this->db->where('tasks', $item['task_id']);
        $this->db->where('id_tasks_process', $item['detail_id']);
        $this->db->where('inspection_criteria', $item['criteria_id']);
        $this->db->where('isCheck', 1);
        $check_approved = $this->db->get('tbl_tasks_inspection_criteria_process')->row();

        if ($check_approved) {
            $this->cancel_item($item['id']);
            return;
        }

        // 2. Kiểm tra xem có báo cáo phù hợp hoàn thành chưa (tblproduction_report)
        $this->db->where('id_tasks_process_child', $item['criteria_id']);
        // $this->db->where('status_id', 4); // Nếu cần check trạng thái finished
        $report = $this->db->get('tblproduction_report')->row();
        
        if ($report) {
            $this->cancel_item($item['id']);
            return;
        }

        // 3. Gửi email (1: Nhắc nhở, 2: Cảnh báo, 3: Escalation)
        if ($item['type'] == 1 || $item['type'] == 2) {
            $this->send_to_staff($item);
        } else if ($item['type'] == 3) {
            $this->send_escalation($item);
        }
    }

    private function cancel_item($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_task_email_queue', ['sent' => 2]); // Cancelled
    }

    private function send_to_staff($item)
    {
        $staff = $this->staff_model->get($item['staff_id']);
        if (!$staff || empty($staff->email)) {
            $this->cancel_item($item['id']);
            return;
        }

        $type_text = ($item['type'] == 1) ? "NHẮC VIỆC" : "CẢNH BÁO";
        $subject = "[$type_text] Phê duyệt quy trình công việc - Task #".$item['task_id'];
        
        $criteria = get_table_where('tbl_tasks_process_child', ['id' => $item['criteria_id']], '', 'row_array');
        
        $this->db->where('id', $item['task_id']);
        $task = $this->db->get('tbltasks')->row();
        
        // Lấy quy trình bước kế tiếp từ tbltask_checklist_items
        $this->db->where('taskid', $item['task_id']);
        $this->db->where('id', $item['detail_id']);
        $next_checklist = $this->db->get('tbltask_checklist_items')->row();

        $message = "Chào " . $staff->firstname . ",<br><br>";
        $message .= "Hệ thống thông báo <b>$type_text</b> cho bước phê duyệt:<br>";
        $message .= "<b>Tiêu chí:</b> " . (!empty($criteria) ? $criteria['name'] : "N/A") . "<br>";
        $message .= "<b>Công việc:</b> " . ($task ? $task->name : "N/A") . "<br>";
        $message .= "<b>Hạn chót:</b> " . ($task && !empty($task->duedate) ? date('d/m/Y H:i', strtotime($task->duedate)) : 'N/A') . "<br>";
        $message .= "<b>Mã Task:</b> " . $item['task_id'] . "<br>";
        $message .= "<b>Link chi tiết:</b> <a href=\"" . admin_url('tasks?id=' . $item['task_id'] . '&process_id=' . $item['detail_id']) . "\">Xem tại đây</a><br><br>";
        
        if ($next_checklist) {
            $message .= "<b>Quy trình:</b> " . $next_checklist->description . "<br><br>";
        }

        $message .= "Vui lòng truy cập hệ thống để xử lý sớm nhất.";

        if ($this->send_mail($staff->email, $subject, $message)) {
            $this->mark_sent($item['id']);
        }
    }

    private function send_escalation($item)
    {
        $staff_info = $this->staff_model->get($item['staff_id']);
        $criteria = get_table_where('tbl_tasks_process_child', ['id' => $item['criteria_id']], '', 'row_array');

        $this->db->where('id', $item['task_id']);
        $task = $this->db->get('tbltasks')->row();

        // Lấy quy trình bước kế tiếp từ tbltask_checklist_items
        $this->db->where('taskid', $item['task_id']);
        $this->db->where('id', $item['detail_id']);
        $next_checklist = $this->db->get('tbltask_checklist_items')->row();

        $subject = "[ESC] Quá hạn phê duyệt quy trình công việc - Task #" . $item['task_id'];

        $staff_name = ($staff_info ? $staff_info->firstname . " " . $staff_info->lastname : "N/A");
        $criteria_name = (!empty($criteria) ? $criteria['name'] : "N/A");
        $task_name = ($task ? $task->name : "N/A");
        $due_date = ($task && !empty($task->duedate) ? date('d/m/Y H:i', strtotime($task->duedate)) : 'N/A');

        $message_body = "Quy trình sau đã <b>QUÁ HẠN</b> và được chuyển lên cấp quản lý:<br>";
        $message_body .= "<b>Nhân viên phụ trách:</b> " . $staff_name . "<br>";
        $message_body .= "<b>Tiêu chí:</b> " . $criteria_name . "<br>";
        $message_body .= "<b>Công việc:</b> " . $task_name . "<br>";
        $message_body .= "<b>Hạn chót:</b> " . $due_date . "<br>";
        $message_body .= "<b>Mã Task:</b> " . $item['task_id'] . "<br>";
        $message_body .= "<b>Link chi tiết:</b> <a href=\"" . admin_url('tasks?id=' . $item['task_id'] . '&process_id=' . $item['detail_id']) . "\">Xem tại đây</a><br><br>";

        if ($next_checklist) {
            $message_body .= "<b>Quy trình:</b> " . $next_checklist->description . "<br><br>";
        }
        $message_body .= "Vui lòng đôn đốc xử lý.";

        $sent_any = false;

        if ($staff_info && !empty($staff_info->role)) {
            $this->db->where('roleid', $staff_info->role);
            $role = $this->db->get('tblroles')->row();

            if ($role && !empty($role->roles_parent) && $role->roles_parent > 0) {
                // Tìm nhân viên có role cấp trên
                $this->db->where('role', $role->roles_parent);
                $this->db->where('active', 1);
                $managers = $this->db->get('tblstaff')->result();

                if (!empty($managers)) {
                    foreach ($managers as $manager) {
                        if (!empty($manager->email)) {
                            $full_message = "Chào " . $manager->firstname . ",<br><br>" . $message_body;
                            if ($this->send_mail($manager->email, $subject, $full_message)) {
                                $sent_any = true;
                            }
                        }
                    }
                }
            }
        }

        // Nếu không có nhân viên nào (hoặc không tìm thấy role cấp trên có nhân viên) thì gửi CEO
        if (!$sent_any) {
            $full_message = "Chào Ban quản lý,<br><br>" . $message_body;
            if ($this->send_mail(EMAIL_CEO, $subject, $full_message)) {
                $sent_any = true;
            }
        }

        if ($sent_any) {
            $this->mark_sent($item['id']);
        }
    }

    private function mark_sent($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_task_email_queue', [
            'sent' => 1,
            'date_sent' => date('Y-m-d H:i:s')
        ]);
    }

    private function send_mail($email, $subject, $message)
    {
        $this->load->library('email');
        $this->email->clear();
        $this->email->from(get_option('smtp_email'), get_option('companyname'));
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        
        $success = $this->email->send();
        
        $log_data = [
            'email'      => $email,
            'subject'       => $subject,
            'content'       => $message,
            'status'        => $success ? 'success' : 'failed',
            'error_message' => $success ? '' : $this->email->print_debugger(['headers']),
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_email_logs', $log_data);

        return $success;
    }
}
