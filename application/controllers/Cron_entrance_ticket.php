<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron_entrance_ticket extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Entrance_ticket_mail_model');
    }

    public function index()
    {
        // Điều kiện:
        // status < 4 (chưa duyệt hết)
        // chưa có phiếu báo cáo (chưa có trong tbl_entrance_ticket_step hoặc chưa có status_production_report > 0)
        // 30 phút và 60 phút từ date_create
        
        $this->db->select('*');
        $this->db->from('tbl_entrance_ticket');
        $this->db->where('status <', 4);
        
        // Điều kiện chưa gửi 60 phút
        $this->db->group_start();
        $this->db->where('warning_30_sent', 0);
        $this->db->or_where('warning_60_sent', 0);
        $this->db->group_end();
        
        $tickets = $this->db->get()->result_array();
        
        if (empty($tickets)) {
            echo "No tickets to process.\n";
            return;
        }
        
        $now = time();
        $sent_count = 0;
        
        foreach ($tickets as $ticket) {
            $ticket_id = $ticket['id'];
            
            // Check pending reports: if there's any pending or missing, or whatever condition was meant by "chưa có phiếu báo cáo hay gì"
            // we will query to see if there is any report step record
            $this->db->where('entrance_ticket_id', $ticket_id);
            $steps = $this->db->get('tbl_entrance_ticket_step')->result_array();
            
            $has_report = false;
            foreach ($steps as $st) {
                if (!empty($st['production_report_id'])) {
                    $has_report = true;
                    break;
                }
            }
            
            if ($has_report) {
                continue; // Skip because it has report
            }
            
            $created_time = strtotime($ticket['date_create']);
            if (!$created_time) continue;
            
            $diff_minutes = floor(($now - $created_time) / 60);
            
            if ($diff_minutes >= 60 && $ticket['warning_60_sent'] == 0) {
                $this->Entrance_ticket_mail_model->send_warning_email($ticket_id, 60);
                $this->db->where('id', $ticket_id)->update('tbl_entrance_ticket', [
                    'warning_30_sent' => 1, // To make sure we don't send 30m anymore if 60m is triggered
                    'warning_60_sent' => 1
                ]);
                $sent_count++;
            } elseif ($diff_minutes >= 30 && $diff_minutes < 60 && $ticket['warning_30_sent'] == 0) {
                $this->Entrance_ticket_mail_model->send_warning_email($ticket_id, 30);
                $this->db->where('id', $ticket_id)->update('tbl_entrance_ticket', [
                    'warning_30_sent' => 1
                ]);
                $sent_count++;
            }
        }
        
        echo "Cron executed. Sent $sent_count warning emails.\n";
    }
}
