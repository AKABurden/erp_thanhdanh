<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Screen_work extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function screen_work_customer()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $data['title']          = _l('screen_work');


        $procedure_client = get_table_where(db_prefix().'procedure_client');
        foreach($procedure_client as $key => $value)
        {
            $this->db->where('id_detail', $value['id']);
            $this->db->order_by('orders', 'asc');
            $value['detail'] = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
            if($value['type'] == 'lead')
            {
                $data['lead'] = $value;
                foreach($data['lead']['detail'] as $Kdetail => $Vdetail)
                {
                    $this->db->select('tbladvisory_lead.*, concat(tblleads.name,"-",tblleads.prefix_lead,code_lead) as fullname, tblprocedure_advisory_lead.active');
                    $this->db->where('DATE_FORMAT(tblprocedure_advisory_lead.date_create, "%Y-%m-%d") = "'.date('Y-m-d').'"');
                    $this->db->join('tblprocedure_advisory_lead', 'tblprocedure_advisory_lead.id_advisory = tbladvisory_lead.id and status_procedure = '.$Vdetail['id']);
                    $this->db->join('tblleads', 'tblleads.id = tbladvisory_lead.lead');
                    $data['lead']['detail'][$Kdetail]['info_success'] = $this->db->get('tbladvisory_lead')->result_array();
                    if($Kdetail == 0)
                    {
                        $this->db->select('tbladvisory_lead.*, concat(tblleads.name,"-",tblleads.prefix_lead,code_lead) as fullname');
                        $this->db->where('tblprocedure_advisory_lead.date_create is null');
                        $this->db->where('DATE_FORMAT(tbladvisory_lead.date_expected, "%Y-%m-%d") <="'.date('Y-m-d').'"');
                        $this->db->join('tblprocedure_advisory_lead', 'tblprocedure_advisory_lead.id_advisory = tbladvisory_lead.id');
                        $this->db->join('tblleads', 'tblleads.id = tbladvisory_lead.lead');
                        $data['lead']['detail'][$Kdetail]['info_waiting'] = $this->db->get('tbladvisory_lead')->result_array();
                    }
                    else
                    {
                        $id_key_befor = $data['lead']['detail'][$Kdetail-1]['id'];
                        $id_key = $data['lead']['detail'][$Kdetail]['id'];
                        $this->db->select('tbladvisory_lead.*, concat(tblleads.name,"-",tblleads.prefix_lead,code_lead) as fullname');
                        $this->db->where('(
                            (tblprocedure_advisory_lead.date_create is null and tbladvisory_lead.status_first = '.$id_key.')
                            OR
                            (
                                tbladvisory_lead.status_first != '.$id_key.'
                                AND tblprocedure_advisory_lead.status_procedure = '.$id_key_befor.' and tblprocedure_advisory_lead.active = 1
                            )
                         )');
                        $this->db->where('DATE_FORMAT(tbladvisory_lead.date_expected, "%Y-%m-%d") <="'.date('Y-m-d').'"');
                        $this->db->join('tblprocedure_advisory_lead', 'tblprocedure_advisory_lead.id_advisory = tbladvisory_lead.id');
                        $this->db->join('tblleads', 'tblleads.id = tbladvisory_lead.lead');
                        $data['lead']['detail'][$Kdetail]['info_waiting'] = $this->db->get('tbladvisory_lead')->result_array();
                    }
                }
                continue;
            }
            else if($value['type'] == 'client')
            {
                $data['client'] = $value;
                foreach($data['client']['detail'] as $Kdetail => $Vdetail)
                {
                    $this->db->select('tblcare_of_clients.*, concat(tblclients.company, "-", tblclients.prefix_client,code_client) as fullname, tblprocedure_care_of.active');
                    $this->db->where('DATE_FORMAT(tblprocedure_care_of.date_create, "%Y-%m-%d") = "'.date('Y-m-d').'"');
                    $this->db->join('tblprocedure_care_of', 'tblprocedure_care_of.id_care_of = tblcare_of_clients.id and status_procedure = '.$Vdetail['id']);
                    $this->db->join('tblclients', 'tblclients.userid = tblcare_of_clients.client');
                    $data['client']['detail'][$Kdetail]['info_success'] = $this->db->get('tblcare_of_clients')->result_array();
                    if($Kdetail == 0)
                    {
                        $this->db->select('tblcare_of_clients.*, concat(tblclients.company,"-",tblclients.prefix_client,code_client) as fullname');
                        $this->db->where('tblprocedure_care_of.date_create is null');
                        $this->db->where('DATE_FORMAT(tblcare_of_clients.date_expected, "%Y-%m-%d") <="'.date('Y-m-d').'"');
                        $this->db->join('tblprocedure_care_of', 'tblprocedure_care_of.id_care_of = tblcare_of_clients.id');
                        $this->db->join('tblclients', 'tblclients.userid = tblcare_of_clients.client');
                        $data['client']['detail'][$Kdetail]['info_waiting'] = $this->db->get('tblcare_of_clients')->result_array();
                    }
                    else
                    {
                        $id_key_befor = $data['client']['detail'][$Kdetail-1]['id'];
                        $id_key = $data['client']['detail'][$Kdetail]['id'];
                        $this->db->select('tblcare_of_clients.*, concat(tblclients.company,"-",tblclients.prefix_client,code_client) as fullname');
                        $this->db->where('(
                            (tblprocedure_care_of.date_create is null and tblcare_of_clients.status_first = '.$id_key.')
                            OR
                            (
                                tblcare_of_clients.status_first != '.$id_key.'
                                AND tblprocedure_care_of.status_procedure = '.$id_key_befor.' and tblprocedure_care_of.active = 1
                            )
                         )');
                        $this->db->where('DATE_FORMAT(tblcare_of_clients.date_expected, "%Y-%m-%d") <="'.date('Y-m-d').'"');
                        $this->db->join('tblprocedure_care_of', 'tblprocedure_care_of.id_care_of = tblcare_of_clients.id');
                        $this->db->join('tblclients', 'tblclients.userid = tblcare_of_clients.client');
                        $data['client']['detail'][$Kdetail]['info_waiting'] = $this->db->get('tblcare_of_clients')->result_array();
                    }
                }
                continue;
            }
            else if($value['type'] == 'event_client')
            {
                $data['event_client'] = $value;
                continue;
            }
            else if($value['type'] == 'orders')
            {
                $data['orders'] = $value;
                continue;
            }
        }
        $this->load->view('admin/screen_work/screen_work_customer', $data);
    }
    public function save_dashboard_widgets_screen_work_customer()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');

        $post_data = $this->input->post();
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_screen_work_customer', serialize($post_data));
    }
}