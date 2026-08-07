<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Report_Quality_Control extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('quality_control_model');
        $this->load->model('products_model');
        $this->load->model('unit_model');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('check_quality');
        $tokenAccount = '';
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['tokenAccount'])) {
                    $tokenAccount = $data_post['tokenAccount'];
                }
            }
        }
        if (empty($tokenAccount)) {
            $tokenAccount = $this->input->post('tokenAccount');
        }
        $staffid = checkTokenLoginApp($tokenAccount);
        $staff = get_table_where('tblstaff', array('staffid' => $staffid), '', 'row');
        if (!empty($staff)) {
            $this->staffid = $staffid;
        } else {
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'result' => false,
            ]);
            die;
        }

        $this->perViewQC = has_permission('quality_control', $this->staffid, 'view');
        $this->isAdmin = is_admin($this->staffid);
        $this->branchID = get_staff_user_id_branch_app($this->staffid);
    }

    public function get_reason(){

        $beginMonth = '';
        $endMonth = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['beginMonth'])) {
                        $beginMonth = to_sql_date($data_post['beginMonth']);
                    }
                    if (!empty($data_post['endMonth'])) {
                        $endMonth = to_sql_date($data_post['endMonth']);
                    }
                }
            }
        }

        $total_quanlity = 0;
        $reasons = [];

        $this->db->select('tbl_detail_errors.name as name_reason,SUM(tbl_check_quality_items_error.quantity) as quantity_reason');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items_error.id_error');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $reasons = $this->db->get()->result_array();
   
        if (!empty($reasons)) {
            foreach ($reasons as $key => $value) {
                $total_quanlity += $value['quantity_reason'];
            }

            foreach ($reasons as $key => $value) {
                $reasons[$key]['tyle'] = formatNumber(($value['quantity_reason'] * 100) / $total_quanlity);
            }
        }

        echo json_encode($reasons);
    }

    public function dashboard_report_pie_reason()
    {
        $beginMonth = '';
        $endMonth = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['beginMonth'])) {
                        $beginMonth = to_sql_date($data_post['beginMonth']);
                    }
                    if (!empty($data_post['endMonth'])) {
                        $endMonth = to_sql_date($data_post['endMonth']);
                    }
                }
            }
        }
        $total_quanlity = 0;
        $reasons = [];

        $this->db->select('tbl_detail_errors.name as name_reason,SUM(tbl_check_quality_items_error.quantity) as quantity_reason');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items_error.id_error');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $reasons = $this->db->get()->result_array();
        if (!empty($reasons)) {
            foreach ($reasons as $key => $value) {
                $total_quanlity += $value['quantity_reason'];
            }

            foreach ($reasons as $key => $value) {
                $reasons[$key]['tyle'] = formatNumber(($value['quantity_reason'] * 100) / $total_quanlity);
            }
        }


        $_order = array();
        $labels = array();
        $colors = array();
        $datas = array();
        foreach ($reasons as $key => $value) {
            $labels[] = $value['name_reason'];
            $_order[] = $value['quantity_reason'];
            $colors[] = '#' . rand_color();
            $datas[] = [
                'label' => $value['name_reason'],
                'data' => [$value['quantity_reason']],
                'backgroundColor' => '#' . rand_color(),
                'borderColor' => '#' . rand_color(),
            ];
        }
        $__data['color'] = $colors;
        $__data['data'] = $_order;
        $__data['labels'] = $labels;
        echo json_encode($__data);
        die;
    }

    public function get_product()
    {

        $beginMonth = '';
        $endMonth = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['beginMonth'])) {
                        $beginMonth = to_sql_date($data_post['beginMonth']);
                    }
                    if (!empty($data_post['endMonth'])) {
                        $endMonth = to_sql_date($data_post['endMonth']);
                    }
                }
            }
        }
        $total_quanlity = 0;
        $products = [];

        $this->db->select('SUM(tbl_check_quality_items_error.quantity) as quantity_reason,tbl_products.name');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_check_quality_items.item_id', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items.item_id');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $this->db->limit(10);
        $products = $this->db->get()->result_array();

        if (!empty($products)) {
            foreach ($products as $key => $value) {
                $total_quanlity += $value['quantity_reason'];
            }

            foreach ($products as $key => $value) {
                $products[$key]['tyle'] = formatNumber(($value['quantity_reason'] * 100) / $total_quanlity);
            }
        }


        echo json_encode($products);
    }

    function dashboard_report_pie_product()
    {
        $beginMonth = '';
        $endMonth = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['beginMonth'])) {
                        $beginMonth = to_sql_date($data_post['beginMonth']);
                    }
                    if (!empty($data_post['endMonth'])) {
                        $endMonth = to_sql_date($data_post['endMonth']);
                    }
                }
            }
        }
        $total_quanlity = 0;
        $products = [];

        $this->db->select('SUM(tbl_check_quality_items_error.quantity) as quantity_reason,tbl_products.name');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_check_quality_items.item_id', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items.item_id');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $this->db->limit(10);
        $products = $this->db->get()->result_array();

        $_order = array();
        $labels = array();
        $colors = array();
        foreach ($products as $key => $value) {
            $labels[] = $value['name'];
            $_order[] = $value['quantity_reason'];
            $colors[] = '#' . rand_color();
        }
        $__data['color'] = $colors;
        $__data['data'] = $_order;
        $__data['labels'] = $labels;
        echo json_encode($__data);
        die;
    }

    public function getOrderCheckQuality($page = 1, $limit = 10)
    {
        $data = [];
        $beginMonth = '';
        $endMonth = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['beginMonth'])) {
                        $beginMonth = to_sql_date($data_post['beginMonth']);
                    }
                    if (!empty($data_post['endMonth'])) {
                        $endMonth = to_sql_date($data_post['endMonth']);
                    }
                }
            }
        }
        $start = ($page - 1) * $limit;

        $reason = "(
            SELECT tbl_check_quality_items_error.id
            FROM tbl_check_quality_items_error
            INNER JOIN tbl_check_quality_items ON tbl_check_quality_items.id = tbl_check_quality_items_error.id_check_quality_item
            WHERE tbl_check_quality_items.order_id = tbl_orders.id
        )";

        $this->db->select("
            tbl_check_quality.id as id,
            tbl_productions_orders.reference_no as reference_no_pod,
            tbl_orders.reference_no as reference_no,
            GROUP_CONCAT(tbl_check_quality_items.id ) as id_check_quality_item,
            ,
        ", FALSE)
        ->from('tbl_check_quality')
        ->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id', 'left')
        ->join('tbl_orders', 'tbl_orders.id = tbl_check_quality_items.order_id AND object_type = "orders"', 'inner')
        ->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_check_quality_items.pod_id', 'left')
        ->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id', 'left');

        if (!empty($beginMonth) && !empty($endMonth)) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->where('tbl_check_quality_items.quantity_recycling > 0');
        $this->db->where("EXISTS $reason");
        $this->db->group_by('tbl_check_quality_items.order_id,tbl_productions_orders.id');
        $this->db->order_by('tbl_check_quality_items.order_id ASC,tbl_productions_orders.id ASC');
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        foreach ($result as $key => $value) {
            $check_quality_id = $value['id'];
            $check_quality_item_id = $value['id_check_quality_item'];
            unset($result[$key]['id_check_quality_item']);

            $this->db->select('tbl_detail_errors.name as name_reason,,SUM(tbl_check_quality_items_error.quantity) as quantity_reason');
            $this->db->from('tbl_check_quality_items_error');
            $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
            $this->db->where('tbl_check_quality_items_error.id_check_quality_item IN ('.$check_quality_item_id.')');
            $this->db->group_by('tbl_check_quality_items_error.id_error');
            $this->db->having('quantity_reason > 0');
            $reasons = $this->db->get()->result_array();
            $result[$key]['child'] = $reasons;
        }
        $data['result'] = $result;
        //next
        $this->db->select("
            tbl_check_quality.id as id,
            tbl_productions_orders.reference_no as reference_no_pod,
            tbl_orders.reference_no as reference_no,
            GROUP_CONCAT(tbl_check_quality_items.id ) as id_check_quality_item,
            ,
        ", FALSE)
        ->from('tbl_check_quality')
        ->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id', 'left')
        ->join('tbl_orders', 'tbl_orders.id = tbl_check_quality_items.order_id AND object_type = "orders"', 'inner')
        ->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_check_quality_items.pod_id', 'left')
        ->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id', 'left');
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);
        if (!empty($beginMonth) && !empty($endMonth)) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->where('tbl_check_quality_items.quantity_recycling > 0');
        $this->db->where("EXISTS $reason");
        $this->db->group_by('tbl_check_quality_items.order_id,tbl_productions_orders.id');
        $this->db->order_by('tbl_check_quality_items.order_id ASC,tbl_productions_orders.id ASC');
        $data['next'] = $this->db->get()->num_rows();
        echo json_encode($data);
    
    }
}