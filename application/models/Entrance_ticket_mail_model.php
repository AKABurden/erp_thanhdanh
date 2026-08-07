<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Entrance_ticket_mail_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
    }

    /**
     * Gửi định dạng chung qua thư viện Email của hệ thống
     */
    private function _send_email($ticket_id, $type, $email_to, $subject, $message)
    {
        $this->load->library('email');
        $this->email->clear();
        $this->email->from(get_option('smtp_email'), get_option('companyname'));
        $this->email->to($email_to);
        $this->email->subject($subject);
        $this->email->message($message);
        
        $success = $this->email->send();
        
        $log_data = [
            'ticket_id'     => $ticket_id,
            'type'          => $type,
            'email_to'      => $email_to,
            'subject'       => $subject,
            'content'       => $message,
            'status'        => $success ? 'success' : 'failed',
            'error_message' => $success ? '' : $this->email->print_debugger(['headers']),
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_entrance_ticket_emails', $log_data);

        return $success;
    }

    /**
     * Lấy danh sách email của một/nhiều Location (ví dụ: QA, BV)
     */
    private function _get_location_emails($location_names)
    {
        if (!is_array($location_names)) $location_names = [$location_names];
        
        $this->db->select('s.email');
        $this->db->from('tbl_entrance_ticket_locations l');
        $this->db->join('tbl_entrance_ticket_location_roles lr', 'lr.location_id = l.id');
        $this->db->join('tblstaff s', 's.role = lr.role_id');
        $this->db->where_in('l.name', $location_names);
        $this->db->where('s.active', 1);
        $this->db->where('s.email !=', '');
        $this->db->where('s.email IS NOT NULL', null, false);
        
        $res = $this->db->get()->result_array();
        return array_column($res, 'email');
    }

    /**
     * Lấy email của người phụ trách
     */
    private function _get_staff_email($staff_id)
    {
        $staff = $this->staff_model->get($staff_id);
        if ($staff && !empty($staff->email)) {
            return $staff->email;
        }
        return false;
    }

    /**
     * Gửi một mảng email gộp lại để tránh gửi trùng lặp nếu 1 người có nhiều role
     */
    private function _send_batch($ticket_id, $type, $emails, $subject, $message)
    {
        $emails = array_unique(array_filter($emails));
        if (empty($emails)) return false;

        // Có thể gửi từng người hoặc bcc, ở đây ta gọi foreach gửi từng email
        // vì email template có thể cần custom tên, nhưng ta dùng form chung
        foreach ($emails as $email) {
            $this->_send_email($ticket_id, $type, $email, $subject, $message);
        }
        return true;
    }

    /**
     * Xây dựng nội dung HTML chi tiết phiếu để chèn vào mail
     */
    private function _build_ticket_info_html($ticket)
    {
        $priority_map = [
            0 => '<span style="color: #555;">Bình thường</span>',
            1 => '<span style="color: #f39c12; font-weight: bold;">Gấp</span>',
            2 => '<span style="color: #e74c3c; font-weight: bold;">Rất gấp</span>',
        ];

        $priority_text = $priority_map[$ticket['priority']] ?? 'Bình thường';
        
        // Lấy tên phương tiện (nếu vehicle_type là ID)
        $vehicle_name = $ticket['vehicle_type'];
        if (is_numeric($vehicle_name)) {
            $vt = $this->db->where('id', $vehicle_name)->get('tbl_transportation_vehicles')->row();
            if ($vt) $vehicle_name = $vt->name;
        }

        $html = "<div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #2196F3; margin: 10px 0;'>";
        $html .= "<b>Thông tin chi tiết phiếu:</b><br>";
        $html .= "<table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
        
        $fields = [
            'Ưu tiên'     => $priority_text,
            'Đối tác'     => $ticket['partner_name'],
            'Hàng hóa'    => $ticket['item_code_name'],
            'Số lượng'    => $ticket['quantity'] . " (" . $ticket['package_count'] . " kiện, " . $ticket['kg_weight'] . " kg)",
            'Phương tiện' => $vehicle_name . " (" . $ticket['license_plate'] . " - Tài xế: " . $ticket['driver_name'] . ")",
            'Ngày đi (Planned)' => _dt($ticket['planned_date_out']),
            'Ngày về (Planned)' => _dt($ticket['planned_date_return']),
            'Điểm đến'    => $ticket['destination'],
            'Ghi chú/Lý do' => $ticket['note_reason'],
        ];

        foreach ($fields as $label => $value) {
            $html .= "<tr><td style='width: 150px; padding: 4px 0;'><b>$label:</b></td><td>$value</td></tr>";
        }
        
        $html .= "</table></div>";
        return $html;
    }

    /**
     * Tạo phiếu mới -> Gửi cho Phụ trách, QA, BV
     */
    public function send_created_email($ticket_id)
    {
        $ticket = $this->db->where('id', $ticket_id)->get('tbl_entrance_ticket')->row_array();
        if (!$ticket) return false;

        $emails = [];
        $emails[] = $this->_get_staff_email($ticket['id_staff']);
        $emails = array_merge($emails, $this->_get_location_emails(['QA', 'BV']));

        $subject = "[Phiếu mới] Phiếu mang hàng ra/vào cổng: " . $ticket['reference_no'];
        
        $link = admin_url('entrance_ticket?id=' . $ticket_id);
        
        $message = "Hệ thống thông báo có phiếu mang hàng ra/vào cổng mới được tạo.<br><br>";
        $message .= "<b>Mã phiếu:</b> " . $ticket['reference_no'] . "<br>";
        $message .= "<b>Người phụ trách:</b> " . get_staff_full_name($ticket['id_staff']) . "<br>";
        
        $message .= $this->_build_ticket_info_html($ticket);

        $message .= "Vui lòng truy cập hệ thống để xem chi tiết và phê duyệt (nếu có quyền):<br>";
        $message .= "<a href='{$link}' style='display:inline-block; padding: 10px 20px; background: #2196F3; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 10px;'>Xem chi tiết và xử lý tại đây</a><br><br>";

        return $this->_send_batch($ticket_id, 'created', $emails, $subject, $message);
    }

    /**
     * Cập nhật phiếu -> Gửi cho Phụ trách, QA, BV
     */
    public function send_edited_email($ticket_id)
    {
        $ticket = $this->db->where('id', $ticket_id)->get('tbl_entrance_ticket')->row_array();
        if (!$ticket) return false;

        $emails = [];
        $emails[] = $this->_get_staff_email($ticket['id_staff']);
        $emails = array_merge($emails, $this->_get_location_emails(['QA', 'BV']));

        $subject = "[Cập nhật] Phiếu mang hàng ra/vào cổng: " . $ticket['reference_no'];
        
        $link = admin_url('entrance_ticket?id=' . $ticket_id);
        
        $message = "Hệ thống thông báo phiếu mang hàng ra/vào cổng đã được CẬP NHẬT thông tin.<br><br>";
        $message .= "<b>Mã phiếu:</b> " . $ticket['reference_no'] . "<br>";
        $message .= "<b>Người phụ trách:</b> " . get_staff_full_name($ticket['id_staff']) . "<br>";
        
        $message .= $this->_build_ticket_info_html($ticket);

        $message .= "Vui lòng truy cập hệ thống để xem chi tiết:<br>";
        $message .= "<a href='{$link}' style='display:inline-block; padding: 10px 20px; background: #2196F3; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 10px;'>Xem chi tiết phiếu tại đây</a><br><br>";

        return $this->_send_batch($ticket_id, 'edited', $emails, $subject, $message);
    }

    /**
     * Duyệt / Từ chối -> Gửi mail
     * $next_status: -1 (Từ chối), 1 (QA duyệt đi), 2 (BV ra), 3 (BV về), 4 (QA hoàn tất)
     */
    public function send_approve_reject_email($ticket_id, $next_status)
    {
        $ticket = $this->db->where('id', $ticket_id)->get('tbl_entrance_ticket')->row_array();
        if (!$ticket) return false;

        $emails = [];
        $emails[] = $this->_get_staff_email($ticket['id_staff']);

        $subject = "";
        $message = "";
        $link = admin_url('entrance_ticket?id=' . $ticket_id);

        if ($next_status == -1) {
            // Từ chối -> Báo phụ trách
            $subject = "[TỪ CHỐI] Phiếu mang hàng ra/vào cổng: " . $ticket['reference_no'];
            $message = "Phiếu mang hàng của bạn đã bị <b>TỪ CHỐI</b>.<br><br>";
            $message .= "<b>Mã phiếu:</b> " . $ticket['reference_no'] . "<br>";
            $message .= "Vui lòng kiểm tra lại với QA hoặc BV để biết thêm chi tiết.<br><br>";
            $type = 'rejected';
        } else {
            // Xác nhận
            $subject = "[Phê duyệt chuyển bước] Phiếu mang hàng ra/vào cổng: " . $ticket['reference_no'];
            $message = "Hệ thống thông báo phiếu mang hàng đã được xác nhận để chuyển sang bước tiếp theo.<br><br>";
            $message .= "<b>Mã phiếu:</b> " . $ticket['reference_no'] . "<br>";
            
            if ($next_status == 1) {
                // QA duyệt đi -> Tới BV
                $emails = array_merge($emails, $this->_get_location_emails('BV'));
                $message .= "<b>Trạng thái:</b> QA đã duyệt đi. Đang chờ Bảo Vệ (BV) xác nhận ra cổng.<br><br>";
            } elseif ($next_status == 2) {
                // BV ra -> Tới QA (nếu đi luôn) sửa lại là có thể BV về
                $emails = array_merge($emails, $this->_get_location_emails(['QA', 'BV']));
                $message .= "<b>Trạng thái:</b> Bảo Vệ (BV) đã xác nhận ra cổng.<br><br>";
            } elseif ($next_status == 3) {
                // BV về -> Tới QA hoàn tất
                $emails = array_merge($emails, $this->_get_location_emails('QA'));
                $message .= "<b>Trạng thái:</b> Bảo Vệ (BV) đã xác nhận về cổng. Đang chờ QA hoàn tất.<br><br>";
            } elseif ($next_status == 4) {
                // QA xong
                $subject = "[Hoàn thành] Phiếu mang hàng ra/vào cổng: " . $ticket['reference_no'];
                $message = "<b>Trạng thái:</b> QA đã đánh dấu phiếu HOÀN TẤT.<br><br>";
            }
            $type = 'approved';
        }

        $message .= $this->_build_ticket_info_html($ticket);

        $message .= "<a href='{$link}' style='display:inline-block; padding: 10px 20px; background: #2196F3; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 10px;'>Xem chi tiết tại đây</a><br><br>";

        return $this->_send_batch($ticket_id, $type, $emails, $subject, $message);
    }

    /**
     * Gửi mail cảnh báo treo phiếu sau 30 phút / 60 phút
     */
    public function send_warning_email($ticket_id, $warning_type = 30)
    {
        $ticket = $this->db->where('id', $ticket_id)->get('tbl_entrance_ticket')->row_array();
        if (!$ticket) return false;

        $emails = [];
        $emails[] = $this->_get_staff_email($ticket['id_staff']);
        $emails = array_merge($emails, $this->_get_location_emails(['QA', 'BV']));
        // array_push($emails, 'trinhhoang.fososoft@gmail.com');

        $subject = "[Cảnh báo trễ thẻ - " . $warning_type . " phút] Phiếu mang hàng ra/vào cổng: " . $ticket['reference_no'];
        
        $link = admin_url('entrance_ticket?id=' . $ticket_id);
        
        $message = "<b>CẢNH BÁO QUÁ HẠN XỬ LÝ:</b> Hệ thống ghi nhận phiếu mang hàng ra/vào cổng đã được tạo hơn " . $warning_type . " phút nhưng chưa hoàn tất.<br><br>";
        $message .= "<b>Mã phiếu:</b> " . $ticket['reference_no'] . "<br>";
        $message .= "<b>Người tạo:</b> " . get_staff_full_name($ticket['staff_create']) . "<br>";
        $message .= "<b>Người phụ trách:</b> " . get_staff_full_name($ticket['id_staff']) . "<br>";
        $message .= "<b>Thời gian tạo:</b> " . $ticket['date_create'] . "<br><br>";
        
        $message .= $this->_build_ticket_info_html($ticket);

        $message .= "Vui lòng truy cập hệ thống để kiểm tra và xử lý phiếu dứt điểm ngay lập tức:<br>";
        $message .= "<a href='{$link}' style='display:inline-block; padding: 10px 20px; background: #e74c3c; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 10px;'>Xem và xử lý phiếu tại đây</a><br><br>";

        return $this->_send_batch($ticket_id, 'warning_' . $warning_type, $emails, $subject, $message);
    }
}
