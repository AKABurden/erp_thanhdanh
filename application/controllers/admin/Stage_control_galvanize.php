<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stage_control_galvanize extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('spending_plan_model');
    }

    public function index() {
        // if ($this->input->is_ajax_request()) {
        //     $data = [];
        //     $data['arrGroupPlan'] = $this->arrGroupPlan;
        //     $this->app->get_table_data('spending_plan', $data);
        // }

        $data['title'] = _l('stage_control_galvanize');
        $this->load->view('admin/stage_control_galvanize/manage', $data);
    }

    public function submit($id = '') {
        if ($this->input->post()) {
            $formData = $this->input->post();

            $result = $this->spending_plan_model->submit($formData, $id);
            if (!empty($result['submitId'])) {
                echo json_encode(['success'=>true, 'alert_type'=>'success', 'message'=>_l('Thành công')]);
            } else {
                echo json_encode(['success'=>false, 'alert_type'=>'danger', 'message'=>_l('Thất bại')]);
            }
        } else {
            $data['title'] = '';
            // $data['arrGroupPlan'] = [];
            // foreach ($this->arrGroupPlan as $groupCode => $group) {
            //     $data['arrGroupPlan'][] = ['code' => $groupCode, 'name' => $group];
            // }

            $data['arrStaff'] = [];
            $staff = get_table_where('tblstaff',array('active'=>1));
            $arrStaff = array();
            foreach ($staff as $key => $value) {
                $arrStaff[$key]['id'] = $value['staffid'];
                $arrStaff[$key]['full_name'] = $value['firstname'].' '.$value['lastname'];
            }
            $data['arrStaff'] = $arrStaff;

            $data['arrPaymentMethod'] = get_table_where('tblpayment_modes',array('active'=>1));
            $data['arrTax'] = get_table_where('tbltaxes');
            $data['arrCurrency'] = get_table_where('tblcurrencies');

            // if (!empty($id)) {
            //     $data['value'] = get_table_where('tblspending_plan', ['id'=>$id], '', 'row_array');
            //     $data['id'] = $id;
            // }

            $data['breadcrumb'] = [array('link' => base_url('admin/stage_control_galvanize/'), 'page' => lang('stage_control_galvanize')), array('link' => '#', 'page' => lang('stage_control_galvanize'))];
            $this->load->view('admin/stage_control_galvanize/submit', $data);
        }
    }

    public function searchProductionOrder($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_productions_orders.id as id, 
            tbl_productions_orders.reference_no as text
        ', false);
        $this->db->from('tbl_productions_orders');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Lệch sản xuất'), 'children' => $pod];
        if (!empty($id)){
            $dtPod = get_table_where('tbl_productions_orders',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtPod['id'], 'text' => $dtPod['reference_no']];
        }
        echo json_encode($data);
    }

    function searchProduct($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $production_order_id = $this->input->get('production_order_id') ?? -1;
        $limit = get_option('select2_limit');
            $this->db->select('
                CONCAT(tbl_products.id, "__products") as id,
                CONCAT(tbl_products.code, " (", tbl_products.name, ")") as text'
            );
            $this->db->from('tbl_productions_orders_items');
            $this->db->join('tbl_products','tbl_products.id = tbl_productions_orders_items.items_id');
            $this->db->where('tbl_productions_orders_items.productions_orders_id',$production_order_id);
            if (!empty($term))
            {
                $this->db->group_start();
                $this->db->like('tbl_products.code', $term);
                $this->db->or_like('tbl_products.name', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $data['results'] = $this->db->get()->result_array();
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }
}