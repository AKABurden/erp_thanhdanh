<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard_warehouse extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->db->select('tblactivity_log_v2.*');
        $this->db->where('tblactivity_log_v2.type_parent_obj', 'warehouse');
        $this->db->order_by('tblactivity_log_v2.id DESC');
        $this->db->limit(10);
        $data['dataLog'] = $this->db->get('tblactivity_log_v2')->result_array();

        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.active', 1);
        $data['staff'] = $this->db->get()->result_array();
        $data['title'] = _l('Dashboard');
        $this->load->view('admin/dashboard_warehouse/manage', $data);
    }
    public function count_all()
    {
        $data = $this->input->post();
        if(!empty($data['report_from'])&&!empty($data['report_from'])){
            $begin = to_sql_date($data['report_from']);
            $end   = to_sql_date($data['report_to']);
            if((strtotime($end)-strtotime($begin)) < 0){
                $data['stock'] = 0;
                $data['total'] = 0;
                $data['warehouse'] = 0;
                echo json_encode($data);die;
            }
        }
            $whereJoin_quote=array();
            $whereJoin_quote['join']=array();
            $whereJoin_quote['field']='product_quantity';
            $stock = (sum_from_table_join('tblwarehouse_items',$whereJoin_quote));

            $this->db->select('SUM(quantity_left*price) as total');
            $total = $this->db->get('tblwarehouse_product')->row()->total;

            $data['stock'] = number_format($stock);
            $data['total'] = number_format($total);
            $data['warehouse'] = number_format(count(get_table_where('tblwarehouse')));
            $data['date_update'] = "Updated "._dt(date('Y-m-d H:i:s'));
            echo json_encode($data);
    }   
    public function dashboard_report_tron()
    {
        $color = array('#FF0000','#00FF00','#0000FF','#FFFF00','#00FFFF','#FF00FF','#C0C0C0','#808080','#800000','#808000','#008000','#800080','#008080','#000080');
        $data = $this->input->post();
            $this->db->select('tbltype_items.name as name,SUM(tblwarehouse_items.product_quantity) as product_quantity');
            $this->db->join('tbltype_items','tbltype_items.type = tblwarehouse_items.type_items','left');
            $this->db->group_by('tblwarehouse_items.type_items');    
            $order = $this->db->get('tblwarehouse_items')->result_array(); 

            $whereJoin_quote=array();
            $whereJoin_quote['join']=array();
            $whereJoin_quote['field']='product_quantity';
            $total = (sum_from_table_join('tblwarehouse_items',$whereJoin_quote));
            $_order = array();
            $labels = array();
            $colors = array();
            foreach ($order as $key => $value) {
                $labels[] = $value['name'];
                $_order[] = $value['product_quantity'];
                $colors[] = $color[($key+2)];

            }
            $__data['color'] = $colors;
            $__data['data'] = $_order;
            $__data['labels'] = $labels;
            echo json_encode($__data);die;
    } 
    public function dashboard_report_pie()
    {
        $color = array('#FF0000','#00FF00','#0000FF','#FFFF00','#00FFFF','#FF00FF','#C0C0C0','#808080','#800000','#808000','#008000','#800080','#008080','#000080');
        $data = $this->input->post();
            $this->db->select('tblwarehouse.name as name,SUM(tblwarehouse_items.product_quantity) as product_quantity');
            $this->db->join('tblwarehouse','tblwarehouse.id = tblwarehouse_items.warehouse_id','left');
            $this->db->group_by('tblwarehouse_items.warehouse_id');    
            $order = $this->db->get('tblwarehouse_items')->result_array(); 

            $whereJoin_quote=array();
            $whereJoin_quote['join']=array();
            $whereJoin_quote['field']='product_quantity';
            $total = (sum_from_table_join('tblwarehouse_items',$whereJoin_quote));
            $_order = array();
            $labels = array();
            $colors = array();
            foreach ($order as $key => $value) {
                $labels[] = $value['name'];
                $_order[] = $value['product_quantity'];
                $colors[] = $color[$key];

            }
            $__data['color'] = $color;
            $__data['data'] = $_order;
            $__data['labels'] = $labels;
            echo json_encode($__data);die;
    }     
}