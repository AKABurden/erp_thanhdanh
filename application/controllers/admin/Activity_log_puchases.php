<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Activity_log_puchases extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['dataLog'] = get_table_where('tblactivity_log_v2',array('type_parent_obj'=>'purchase'),'id DESC','result_array','type_parent_obj, table_obj, id_obj');
        $data['title'] = _l('activity_log_puchases');
        $this->load->view('admin/activity_log/manage', $data);
    }

    public function getActivityLog()
    {
        $date_history = $this->input->post('date_history');
        $staff_history = $this->input->post('staff_history');
        $module_history = $this->input->post('module_history');
        $moreHistory = $this->input->post('moreHistory');
        if (!empty($module_history)) {
            $module_history = explode('|', $module_history);
        } else {
            $module_history = [0];
        }

        // print_arrays($this->input->post());

        $this->db->select('tblactivity_log_v2.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name');
        $this->db->from('tblactivity_log_v2');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblactivity_log_v2.staff_id', 'inner');
        $this->db->where_in('tblactivity_log_v2.type_parent_obj', $module_history);
        $this->db->order_by('tblactivity_log_v2.date DESC');
        $this->db->limit(30, $moreHistory);

        if (!empty($date_history)) {
            $arrDate = explode('-', $date_history);
            $startDate = to_sql_date(trim($arrDate[0]));
            $endDate = to_sql_date(trim($arrDate[1]));

            $this->db->where('DATE_FORMAT(tblactivity_log_v2.date, "%Y-%m-%d") >=', $startDate);
            $this->db->where('DATE_FORMAT(tblactivity_log_v2.date, "%Y-%m-%d") <=', $endDate);
        }

        if (!empty($staff_history)) {
            $this->db->where('tblactivity_log_v2.staff_id', $staff_history);
        }

        $activityLog = $this->db->get()->result_array();
        $html = '';
        if (!empty($activityLog)) {
            foreach ($activityLog as $key => $value) {
                $html.= '
                    <div class="feed-item">
                        <div class="activity-text">
                            '.staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small').''.$value['staff_name'].'
                        </div>
                        <div class="activity-time">
                            '.time_ago($value['date']).'<span class="activity-module">'._l($value['type_parent_obj']).'</span>
                        </div>
                        <div>
                            '.$value['content'].'
                        </div>
                    </div>
                ';
            }
        }
        echo $html;
    }
}
