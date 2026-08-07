<?php

use GuzzleHttp\Psr7\Response;

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Products extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('purchases_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('departments_model');
        $this->load->model('stock_model');
        $this->load->model('tools_supplies_model');
        $this->load->model('quality_control_model');
        $this->load->model('transfer_model');
        $this->load->library('ciqrcode');
        // $this->lang->load('vietnamese/form_validation_lang');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('products');
        $this->datetime_now = time();

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
        $staffid = checkTokenLoginApp($tokenAccount);
        // $staffid = 1;
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

        //permission productions orders
        $this->perViewProductionsOrders = has_permission('manufactures_productions_orders', $this->staffid, 'view');
        $this->perViewOwnProductionsOrders = has_permission('manufactures_productions_orders', $this->staffid, 'view_own');
        $this->perAddProductionsOrders = has_permission('manufactures_productions_orders', $this->staffid, 'create');
        $this->perEditProductionsOrders = has_permission('manufactures_productions_orders', $this->staffid, 'edit');
        $this->perDeleteProductionsOrders = has_permission('manufactures_productions_orders', $this->staffid, 'delete');
        $this->perApproveProductionsOrders = has_permission('manufactures_productions_orders', $this->staffid, 'approve');

        $this->perViewOPD = has_permission('manufactures_order_production_details', $this->staffid, 'view');
        $this->perViewOwnOPD = has_permission('manufactures_order_production_details', $this->staffid, 'view_own');
        $this->perApproveOPD = has_permission('manufactures_order_production_details', $this->staffid, 'approve');
        $this->perQC = has_permission('manufactures_order_production_details', $this->staffid, 'qc');

        $this->isAdmin = is_admin($this->staffid);
        $this->branchID = get_staff_user_id_branch_app($this->staffid);
        if ($this->branchID == 'main') $this->branchID = 0;
    }

    function searchProductsSelect2($id = false)
    {
        $search = '';
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }
        if (!empty($data_post['search'])) {
            $search = $data_post['search'];
        }
        $limit = get_option('select2_limit');
        $data['results'] = $this->products_model->searchProductsSelect2($search, $limit);
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }
}