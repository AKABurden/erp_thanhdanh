<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Entrance_ticket_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function handlingProductionReportEntranceTicketStep($id, $ticket_id, $step)
    {
        $this->db->where('entrance_ticket_id', $ticket_id);
        $this->db->where('step', $step);
        $check = $this->db->get('tbl_entrance_ticket_step')->row();

        if ($check) {
            $this->db->where('id', $check->id);
            $this->db->update('tbl_entrance_ticket_step', [
                'production_report_id' => $id
            ]);
        } else {
            $this->db->insert('tbl_entrance_ticket_step', [
                'entrance_ticket_id' => $ticket_id,
                'step' => $step,
                'production_report_id' => $id
            ]);
        }

        return true;
    }
    public function check_status($id = '')
    {
        $this->db->where('production_report_id', $id);
        $entrance_ticket_step = $this->db->get('tbl_entrance_ticket_step')->row();
        if (!empty($entrance_ticket_step)) {
            // $this->db->from('tbl_process_production_report');
            // $this->db->where('production_report_id', $id);
            // $this->db->where('staff_process', 0);
            // $this->db->limit(1);
            // $check = $this->db->get()->num_rows();

            $this->db->select('tbl_process_production_report.*');
            $this->db->where('tbl_process_production_report.staff_process', 0);
            $this->db->where('tbl_process_production_report.production_report_id', $id);
            $this->db->from('tbl_process_production_report');
            $uncompleted_count = $this->db->get()->num_rows();


            if ($uncompleted_count == 0) {
                $this->db->where('id', $entrance_ticket_step->id);
                $this->db->update('tbl_entrance_ticket_step', [
                    'status_production_report' => 1
                ]);
            }
        }
    }
}
