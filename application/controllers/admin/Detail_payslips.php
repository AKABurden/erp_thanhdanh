<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Detail_payslips extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('costs_model');
        $this->load->model('import_model');
    }
    public function index()
    {
        $data['tnh'] = true;
        if (!has_permission('other_payslips', '', 'view') && !has_permission('other_payslips', '', 'view_own')) {
            access_denied('detail_payslips');
        }
        $data['title'] = _l('ch_other_payslips');
        $type = $this->input->get('type');
        if ($type != 1 && $type != 2 && $type != 3 && $type != 4) {
            access_denied('detail_payslips');
        }
        if ($type == 1){
            $data['title'] = 'Chi Phí Hợp Lý';
        }
        if ($type == 2){
            $data['title'] = 'Chi Phí Ngoài';
        }
        if ($type == 3){
            $data['title'] = 'Chi Phí Khấu Trừ';
        }
        if ($type == 4){
            $data['title'] = 'Chi Phí Giảm Trừ';
        }
        $data['type'] = $type;
        $this->load->view('admin/detail_payslips/manage', $data);
    }
    public function table($type = '')
    {
        $this->app->get_table_data('detail_payslips', array('type' => $type));
    }
    public function count_all($type = '')
    {
        if (has_permission('other_payslips', '', 'view_own') && !is_admin()) {
            if ($type == 1) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('type_manager', 1);
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 1);
                $this->db->where('type_manager', 1);
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 2);
                $this->db->where('type_manager', 1);
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 3);
                $this->db->where('type_manager', 1);
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 4);
                $this->db->where('type_manager', 1);
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 5);
                $this->db->where('type_manager', 1);
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
            if ($type == 2) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('type_manager', 0);
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 1);
                $this->db->where('type_manager', 0);
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 2);
                $this->db->where('type_manager', 0);
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 3);
                $this->db->where('type_manager', 0);
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 4);
                $this->db->where('type_manager', 0);
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 5);
                $this->db->where('type_manager', 0);
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
            if ($type == 3) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 1);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 2);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 3);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 4);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 5);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
            if ($type == 4) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 1);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 2);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 3);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 4);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('staff_id', $staff_id);
                $this->db->where('objects', 5);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
        } else {
            if ($type == 1) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('type_manager', 1);
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('objects', 1);
                $this->db->where('type_manager', 1);
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('objects', 2);
                $this->db->where('type_manager', 1);
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('objects', 3);
                $this->db->where('type_manager', 1);
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('objects', 4);
                $this->db->where('type_manager', 1);
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('objects', 5);
                $this->db->where('type_manager', 1);
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
            if ($type == 2) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('type_manager', 0);
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('objects', 1);
                $this->db->where('type_manager', 0);
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('objects', 2);
                $this->db->where('type_manager', 0);
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('objects', 3);
                $this->db->where('type_manager', 0);
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('objects', 4);
                $this->db->where('type_manager', 0);
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('objects', 5);
                $this->db->where('type_manager', 0);
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
            if ($type == 3) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('objects', 1);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('objects', 2);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('objects', 3);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('objects', 4);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('objects', 5);
                $this->db->where('tblcosts.type', 5);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
            if ($type == 4) {
                $staff_id = get_staff_user_id();
                $this->db->select('count(*) as alls');
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $count = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_client');
                $this->db->where('objects', 1);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_client = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_suppliers');
                $this->db->where('objects', 2);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_suppliers = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_staff');
                $this->db->where('objects', 3);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_staff = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_other');
                $this->db->where('objects', 4);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_other = $this->db->get('tblother_payslips')->row();

                $this->db->select('count(*) as pay_tscd');
                $this->db->where('objects', 5);
                $this->db->where('tblcosts.type', 6);
                $this->db->join('tblcosts','tblcosts.id = tblother_payslips.id_costs','left');
                $pay_tscd = $this->db->get('tblother_payslips')->row();
            }
        }
        $data['all'] = $count->alls;
        $data['pay_client'] = $pay_client->pay_client;
        $data['pay_suppliers'] = $pay_suppliers->pay_suppliers;
        $data['pay_staff'] = $pay_staff->pay_staff;
        $data['pay_other'] = $pay_other->pay_other;
        $data['pay_tscd'] = $pay_tscd->pay_tscd;
        echo json_encode($data);
    }
}
