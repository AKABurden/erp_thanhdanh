<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Puchases_gantt_order extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('puchases_gantt_order_model');
        $this->datetime_now = time();
    }
    public function index()
    {
        
        $data['title']          = _l('gantt_order');
        $sum = $this->puchases_gantt_order_model->countProductionsGantt();
        $numberPage = 10;
        $data['tnh'] = true;
        $numPages = ceil($sum/$numberPage);
        $pageCurrent = !empty($this->input->get('page')) ? $this->input->get('page') : 1;

        $start = ($pageCurrent - 1) * $numberPage;
        // $limit   = $numberPage * $pageCurrent - 1;
        $data['numPages'] = $numPages;
        $data['pageCurrent'] = $pageCurrent;
        // $data['gantt_data'] = $this->site_model->loadGanttProductions($start, $numberPage);
        $data['gantt_data'] = $this->puchases_gantt_order_model->loadGanttProductions($start, $numberPage);

        $this->db->select('tblactivity_log_v2.*');
        $this->db->where('tblactivity_log_v2.type_parent_obj', 'purchase');
        $this->db->order_by('tblactivity_log_v2.id DESC');
        $this->db->limit(10);
        $data['dataLog'] = $this->db->get('tblactivity_log_v2')->result_array();

        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.active', 1);
        $data['staff'] = $this->db->get()->result_array();
        $this->load->view('admin/gantt_order/manage', $data);
    }
    public function productions_orders_id($id='')
    {
            $search = $this->input->get('term');
            $supplier = $this->input->get('supplier');

            $this->db->select('
                    tblpurchase_order.id as id,
                    concat(tblpurchase_order.prefix,"-",tblpurchase_order.code) as text,'
            , false);
            if(!empty($supplier))
            {
                $this->db->join('tblsuppliers','tblsuppliers.id = tblpurchase_order.suppliers_id', 'left');
                $this->db->where('tblsuppliers.id', $supplier);
            }
            if (!empty($search))
            {
               
                $this->db->like('concat(tblpurchase_order.prefix,"-",tblpurchase_order.code)', $search);
               
            }else {
            if($id > 0) {
                $this->db->where('tblpurchase_order.id', $id);
                }
            }
            $this->db->group_by('tblpurchase_order.id');
            $this->db->where('tblpurchase_order.cancel',0);
            $this->db->order_by('tblpurchase_order.id', 'DESC');
            $this->db->limit(50);
            $items['results'] = $this->db->get('tblpurchase_order')->result_array();
            echo json_encode($items);die();
    }    
    public function searchsuppliers($id='')
    {
            $search = $this->input->get('term');
            $this->db->select('
                    tblsuppliers.id as id,
                    tblsuppliers.company as text,
                    concat(tblsuppliers.prefix,"-",tblsuppliers.code) as code,'
            , false);
            if (!empty($search))
            {
                $this->db->group_start();
                $this->db->like('tblsuppliers.name', $search);
                $this->db->or_like('concat(tblsuppliers.prefix,"-",tblsuppliers.code)', $search);
                $this->db->group_end();
            }else {
            if($id > 0) {
                $this->db->where('tblsuppliers.id', $id);
                }
            }
            $this->db->group_by('tblsuppliers.id');
            $this->db->join('tblpurchase_order','tblpurchase_order.suppliers_id = tblsuppliers.id', 'left');
            $this->db->where('tblpurchase_order.cancel',0);
            $this->db->order_by('tblsuppliers.id', 'DESC');
            $this->db->limit(50);
            $items['results'] = $this->db->get('tblsuppliers')->result_array();
            echo json_encode($items);die();
    }

}